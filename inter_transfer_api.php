<?php

session_start();
ob_start();   
header('Content-Type: application/json; charset=utf-8');

/* Ajusta estos includes a las rutas reales de tu proyecto */
require_once __DIR__ . "/funciones/clas_carobra.php";
require_once __DIR__ . "/vendor/autoload.php"; // autoload de PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

function responder($ok, $mensaje, $extra = []) {
	ob_clean();   
	echo json_encode(array_merge([
		'success' => $ok,
		'mensaje' => $mensaje,
	], $extra), JSON_UNESCAPED_UNICODE);
	exit;
}

/* Misma validación de sesión que usa el resto del sistema.
   Ajusta esto si tu chequeo real de sesión es distinto. */
if (!isset($_SESSION["nombre"])) {
	responder(false, 'Tu sesión ha caducado. Vuelve a iniciar sesión.');
}

$conecta = new conector();

$archivo      = $_FILES['subir-archivo']['name']     ?? '';
$archivo_tem  = $_FILES['subir-archivo']['tmp_name'] ?? '';
$error_subida = $_FILES['subir-archivo']['error']    ?? UPLOAD_ERR_NO_FILE;

/* ── 1. Revisar el código de error del upload antes que nada ── */
if ($error_subida !== UPLOAD_ERR_OK) {
	$mapaErrores = [
		UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor (upload_max_filesize).',
		UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido por el formulario.',
		UPLOAD_ERR_PARTIAL    => 'El archivo se subió solo parcialmente. Intenta de nuevo.',
		UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
		UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
		UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el servidor.',
		UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.',
	];
	responder(false, $mapaErrores[$error_subida] ?? ('Error desconocido al subir el archivo (código ' . $error_subida . ').'));
}

if (strlen($archivo) === 0) {
	responder(false, 'No se seleccionó ningún archivo.');
}

$destino = __DIR__ . "/produccion/" . basename($archivo);

/* ── 2. Si move_uploaded_file falla, reportarlo ── */
if (!move_uploaded_file($archivo_tem, $destino)) {
	responder(false, 'No se pudo guardar el archivo en el servidor. Revisa permisos de la carpeta ./produccion/ o si ya existe un archivo con ese nombre bloqueado.');
}

try {
	ini_set('memory_limit', '512M');
	set_time_limit(120);

	$archivo = $destino;

	$spreadsheet = IOFactory::load($archivo);
	$hoja        = $spreadsheet->getActiveSheet();

	function txt($valor) {
		return trim((string) $valor);
	}

	function limpiarTexto($texto) {
		$texto = strtoupper(trim((string) $texto));

		$tmp = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
		if ($tmp !== false) {
			$texto = $tmp;
		}

		$texto = str_replace(['_', '-', '.'], ' ', $texto);
		$texto = preg_replace('/\s+/', ' ', $texto);
		$texto = trim($texto);

		return $texto;
	}

	function leerFecha($hoja, $columna, $fila) {
		$celda = $hoja->getCell($columna . $fila);
		$valor = $celda->getValue();

		if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($celda)) {
			try {
				$fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);
				return $fechaObj->format('d/m/Y');
			} catch (\Throwable $e) {
				return '';
			}
		}

		$texto = trim((string) $valor);
		if ($texto === '') return '';

		if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $texto, $m)) {
			return sprintf('%02d/%02d/%04d', (int) $m[1], (int) $m[2], (int) $m[3]);
		}

		return '';
	}

	// Extrae el número de semana del inicio de "297S.22 Ago al 28 Ago" -> 297
	function numeroSemana($semanaTexto) {
		if (preg_match('/^(\d+)/', trim((string) $semanaTexto), $m)) {
			return (int) $m[1];
		}
		return -1; // no se pudo leer -> se trata como la más vieja posible
	}

	/* Inserta un lote; si el INSERT completo truena, reintenta fila por fila
	para aislar cuál(es) folio(s) están mal. Distingue "0 filas afectadas
	porque ya estaba igual" (NO es error) de un error real de MySQL. */
	function insertarLote($conecta, array $lote, array $folios, $updateCompleto, array &$erroresSQL) {
		if (empty($lote)) return 0;

		$columnas = "(folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad, semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo, saldo, fec_alta, sol_traspaso, num_formulario, fec_tramite, telefono, email_trab)";

		$sql = "INSERT INTO clawback $columnas VALUES " . implode(',', $lote) . " ON DUPLICATE KEY UPDATE " . $updateCompleto;

		$resultado = $conecta->consulta($sql);
		$errorReal = mysqli_error($conecta->conexion);

		if ($resultado || $errorReal === '') {
			return count($lote); // entró bien (insertado, actualizado, o sin cambios reales)
		}

		if (count($lote) === 1) {
			$erroresSQL[] = ['folio' => $folios[0], 'mensaje' => $errorReal];
			return 0;
		}

		$insertadosOk = 0;
		foreach ($lote as $i => $fila) {
			$sqlFila = "INSERT INTO clawback $columnas VALUES $fila ON DUPLICATE KEY UPDATE " . $updateCompleto;
			$res = $conecta->consulta($sqlFila);
			$err = mysqli_error($conecta->conexion);

			if ($res || $err === '') {
				$insertadosOk++;
			} else {
				$erroresSQL[] = ['folio' => $folios[$i], 'mensaje' => $err];
			}
		}
		return $insertadosOk;
	}

	$encabezados = [];
	$numeroFilas = $hoja->getHighestRow();

	foreach ($hoja->getRowIterator(1, 1) as $fila) {
		foreach ($fila->getCellIterator() as $celda) {
			$valorOriginal = trim((string) $celda->getValue());
			if ($valorOriginal === '') continue;
			$clave = limpiarTexto($valorOriginal);
			$encabezados[$clave] = $celda->getColumn();
		}
	}

	$cols = [
		'semana..', 'Mes_Procesar', 'NAP', 'Num_Formulario', 'cod_cuenta', 'Correo',
		'Fecha Traspaso', 'ley', 'Afo Receptora', 'Entidad', 'Afore cedente',
		'Rango Antiguedad', 'TEL_MOVIL', 'Cancelación', 'P+', 'Nombre',
		'Apellido Paterno', 'Apellido Materno', 'Saldo', 'Fecha_alta',
		'FOLIO_SOL_TRASPASO', 'FECHA_TRAMITE',
	];

	$colEncontrada = [];
	$faltantes     = [];

	foreach ($cols as $columna) {
		$clave = limpiarTexto($columna);
		if (isset($encabezados[$clave])) {
			$colEncontrada[$columna] = $encabezados[$clave];
		} else {
			$faltantes[] = $columna;
		}
	}

	if (!empty($faltantes)) {
		throw new Exception(
			"No se encontraron estas columnas en el Excel: " . implode(', ', $faltantes) .
			". Encabezados detectados en el archivo: " . implode(', ', array_keys($encabezados))
		);
	}

	$bd_estados = $conecta->consulta("SELECT no_edo, estado FROM estados");
	$bd_afores  = $conecta->consulta("SELECT clave, afore FROM afores");

	$estadosMap = [];
	foreach ($bd_estados as $row) {
		$estado = txt($row['estado']);
		if (strtoupper($estado) === 'MEXICO') {
			$estado = 'ESTADO DE MEXICO';
		}
		$estadosMap[limpiarTexto($estado)] = $row['no_edo'];
	}

	$aforeMap = [];
	foreach ($bd_afores as $row) {
		$aforeMap[limpiarTexto($row['afore'])] = $row['clave'];
	}

	$filasPorFolio    = []; // folio => ['semana_num' => int, 'sql' => '(...)']
	$tamanoLote       = 200;
	$procesados       = 0;
	$omitidos         = 0;
	$insertados       = 0;
	$duplicadosSemana = 0;
	$erroresSQL       = [];

	$updateCompleto =
		"nap=VALUES(nap), fec_traspaso=VALUES(fec_traspaso), " .
		"afo_receptora=VALUES(afo_receptora), afo_cedente=VALUES(afo_cedente), " .
		"estado=VALUES(estado), antiguedad=VALUES(antiguedad), " .
		"semana=VALUES(semana), mes_procesar=VALUES(mes_procesar), " .
		"cod_cuenta=VALUES(cod_cuenta), ley=VALUES(ley), " .
		"cancelacion=VALUES(cancelacion), pplus=VALUES(pplus), " .
		"nombre_Completo=VALUES(nombre_Completo), saldo=VALUES(saldo), " .
		"fec_alta=VALUES(fec_alta), sol_traspaso=VALUES(sol_traspaso), " .
		"num_formulario=VALUES(num_formulario), fec_tramite=VALUES(fec_tramite), " .
		"telefono=VALUES(telefono), email_trab=VALUES(email_trab)";

	for ($fila = 2; $fila <= $numeroFilas; $fila++) {

		$semana       = txt($hoja->getCell($colEncontrada['semana..'] . $fila)->getValue());
		$semanaNum    = numeroSemana($semana);
		$mes_procesar = trim((string) $hoja->getCell($colEncontrada['Mes_Procesar'] . $fila)->getFormattedValue());
		$cod_cuenta   = txt($hoja->getCell($colEncontrada['cod_cuenta'] . $fila)->getValue());
		$ley          = txt($hoja->getCell($colEncontrada['ley'] . $fila)->getValue());
		$cancelacion  = txt($hoja->getCell($colEncontrada['Cancelación'] . $fila)->getValue());
		$p            = trim((string) $hoja->getCell($colEncontrada['P+'] . $fila)->getValue());
		$p            = ($p === '' ? 0 : (int) $p);

		$correo = txt($hoja->getCell($colEncontrada['Correo'] . $fila)->getValue());

		$nombre           = txt($hoja->getCell($colEncontrada['Nombre'] . $fila)->getValue());
		$apellido_paterno = txt($hoja->getCell($colEncontrada['Apellido Paterno'] . $fila)->getValue());
		$apellido_materno = txt($hoja->getCell($colEncontrada['Apellido Materno'] . $fila)->getValue());

		$nombre_completo = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
		$Saldo           = txt($hoja->getCell($colEncontrada['Saldo'] . $fila)->getValue());
		$Fecha_alta      = leerFecha($hoja, $colEncontrada['Fecha_alta'], $fila);
		$Fecha_tramite   = leerFecha($hoja, $colEncontrada['FECHA_TRAMITE'], $fila);

		$FOLIO_SOL_TRASPASO = txt($hoja->getCell($colEncontrada['FOLIO_SOL_TRASPASO'] . $fila)->getValue());
		$TEL_MOVIL          = txt($hoja->getCell($colEncontrada['TEL_MOVIL'] . $fila)->getValue());
		$num_formulario     = txt($hoja->getCell($colEncontrada['Num_Formulario'] . $fila)->getValue());

		if ($num_formulario == '') continue;
		$num_formulario = (int) $num_formulario;

		$NAP      = "";
		$valorNAP = trim((string) $hoja->getCell($colEncontrada['NAP'] . $fila)->getValue());
		if ($valorNAP !== '' && strlen($valorNAP) >= 7 && strlen($valorNAP) <= 10) {
			$NAP = str_pad($valorNAP, 10, "0", STR_PAD_LEFT);
		}

		if ($nombre === '' || $apellido_paterno === '' || $cod_cuenta === '' || $valorNAP === '') {
			$omitidos++;
			continue;
		}

		if (strlen((string) $num_formulario) > 10) {
			$omitidos++;
			continue;
		}

		$fecha_Traspaso = leerFecha($hoja, $colEncontrada['Fecha Traspaso'], $fila);

		$Afo_Receptora  = limpiarTexto(preg_replace('/^\d+\s*/', '', txt($hoja->getCell($colEncontrada['Afo Receptora'] . $fila)->getValue())));
		$idAfoReceptora = $aforeMap[$Afo_Receptora] ?? 0;

		$Entidad   = limpiarTexto(preg_replace('/^\d+\s*/', '', txt($hoja->getCell($colEncontrada['Entidad'] . $fila)->getValue())));
		$idEntidad = $estadosMap[$Entidad] ?? 0;

		$Afore_cedente_txt = limpiarTexto(preg_replace('/^\d+\s*/', '', txt($hoja->getCell($colEncontrada['Afore cedente'] . $fila)->getValue())));
		$idAforeCedente    = $aforeMap[$Afore_cedente_txt] ?? 0;

		$Rango_Antiguedad = txt($hoja->getCell($colEncontrada['Rango Antiguedad'] . $fila)->getValue());
		$Rango_Antiguedad = addslashes(preg_replace('/^\d+\.\s*/', '', $Rango_Antiguedad));

		$nombre_completo    = addslashes($nombre_completo);
		$Saldo              = addslashes($Saldo);
		$Fecha_alta         = addslashes($Fecha_alta);
		$Fecha_tramite      = addslashes($Fecha_tramite);
		$FOLIO_SOL_TRASPASO = addslashes($FOLIO_SOL_TRASPASO);
		$semana             = addslashes($semana);
		$mes_procesar       = addslashes($mes_procesar);
		$cod_cuenta         = addslashes($cod_cuenta);
		$ley                = addslashes($ley);
		$cancelacion        = addslashes($cancelacion);
		$NAP                = addslashes($NAP);
		$fecha_Traspaso     = addslashes($fecha_Traspaso);

		if ($correo != '' || $TEL_MOVIL != '') {
			$correo    = addslashes($correo);
			$TEL_MOVIL = addslashes($TEL_MOVIL);
		}

		$procesados++;

		$filaSql = "('$num_formulario', '$NAP', '$fecha_Traspaso', $idAfoReceptora, $idAforeCedente, $idEntidad, '$Rango_Antiguedad', '$semana', '$mes_procesar', '$cod_cuenta', '$ley', '$cancelacion', '$p', '$nombre_completo', '$Saldo', '$Fecha_alta', '$FOLIO_SOL_TRASPASO', '$num_formulario', '$Fecha_tramite', '$TEL_MOVIL', '$correo')";

		if (!isset($filasPorFolio[$num_formulario]) || $semanaNum >= $filasPorFolio[$num_formulario]['semana_num']) {
			if (isset($filasPorFolio[$num_formulario])) {
				$duplicadosSemana++;
			}
			$filasPorFolio[$num_formulario] = ['semana_num' => $semanaNum, 'sql' => $filaSql];
		} else {
			$duplicadosSemana++;
		}
	}

	/* Ya con un solo folio "ganador" por cada Num_Formulario (el de la
	semana más reciente), se arman los lotes reales para insertar. */
	$lote      = [];
	$folioLote = [];

	foreach ($filasPorFolio as $folio => $info) {
		$lote[]      = $info['sql'];
		$folioLote[] = $folio;

		if (count($lote) >= $tamanoLote) {
			$insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
			$lote      = [];
			$folioLote = [];
		}
	}

	if (!empty($lote)) {
		$insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
	}

	if (file_exists($archivo)) {
		unlink($archivo);
	}

	$mensaje = "El archivo se subió correctamente.\nSe procesaron $procesados registro(s), de los cuales $insertados se guardaron en la base de datos.";
	if ($omitidos > 0) {
		$mensaje .= "\nSe ignoraron $omitidos fila(s) que no parecían datos reales.";
	}
	if ($duplicadosSemana > 0) {
		$mensaje .= "\nSe encontraron $duplicadosSemana fila(s) con folio repetido en el archivo; se conservó la versión de la semana más reciente.";
	}
	if (!empty($erroresSQL)) {
		$mensaje .= "\n\nOJO: " . count($erroresSQL) . " folio(s) NO se pudieron guardar:";
		foreach (array_slice($erroresSQL, 0, 20) as $e) {
			$mensaje .= "\n  Folio {$e['folio']}: {$e['mensaje']}";
		}
	}

	responder(true, $mensaje, [
		'procesados'       => $procesados,
		'insertados'       => $insertados,
		'omitidos'         => $omitidos,
		'duplicadosSemana' => $duplicadosSemana,
		'errores'          => $erroresSQL,
	]);

} catch (\Throwable $e) {
	if (isset($archivo) && file_exists($archivo)) {
		unlink($archivo);
	}
	responder(false, $e->getMessage() . " (línea " . $e->getLine() . " de " . basename($e->getFile()) . ")");
}