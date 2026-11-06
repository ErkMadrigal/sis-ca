<?php
// ============================================================================
// inter_transfer_api.php
//
// CAMBIO DE REGLA (lo que pidió la "zorra"):
//   - Se cargan TODOS los renglones del Excel, sin importar que el
//     Num_Formulario venga repetido 2, 3, 5, 7, 8... veces.
//   - Solo se hace UPDATE de un renglón existente en clawback cuando
//     coinciden EXACTAMENTE los 3 valores: semana.. + Num_Formulario +
//     FOLIO_SOL_TRASPASO. Si cualquiera de los 3 es distinto, se INSERTA
//     como renglón nuevo.
//   - Se eliminó por completo la lógica anterior de "me quedo solo con la
//     semana más reciente por folio" (numeroSemana() como criterio de
//     descarte, $filasPorFolio, $colisionesDetalle). ESO YA NO EXISTE.
//
// REQUISITO DE BASE DE DATOS (hazlo UNA sola vez, antes de subir nada):
//   La unique key de `clawback` tiene que vivir sobre las 3 columnas a la
//   vez: semana + folio (=Num_Formulario) + sol_traspaso. Mientras no sea
//   así, ninguna lógica de PHP puede saltarse un UNIQUE KEY del motor.
//
//     -- si ya tenías uk_folio_sol (folio, sol_traspaso) de un cambio previo:
//     ALTER TABLE clawback DROP INDEX uk_folio_sol;
//     ALTER TABLE clawback
//       ADD UNIQUE KEY uk_semana_folio_sol (semana, folio, sol_traspaso);
//
//   Con esa unique key en su lugar, el INSERT ... ON DUPLICATE KEY UPDATE
//   de abajo hace exactamente la regla pedida: si ya existe un renglón con
//   la misma terna (semana, folio, sol_traspaso) lo actualiza; si cualquiera
//   de los 3 valores es distinto, lo inserta como renglón nuevo -- aunque
//   el folio se repita muchas veces con semana o sol_traspaso distintos.
// ============================================================================

ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');

ob_start();
require_once __DIR__ . "/funciones/clas_carobra.php";
ob_end_clean();

require_once __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

function responder($ok, $msg, $extra = []) {
    $payload = array_merge(['ok' => $ok, 'msg' => $msg], $extra);
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
    echo $json;
    exit;
}

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'ok'  => false,
            'msg' => 'Error fatal: ' . $error['message'] . ' (línea ' . $error['line'] . ' de ' . basename($error['file']) . ')',
        ]);
    }
});

if (!isset($_SESSION["nombre"])) {
    responder(false, 'Tu sesión ha caducado, vuelve a iniciar sesión.');
}

$conecta = new conector();

if (!isset($_FILES['subir-archivo']) || $_FILES['subir-archivo']['error'] !== UPLOAD_ERR_OK) {
    $codigo = $_FILES['subir-archivo']['error'] ?? UPLOAD_ERR_NO_FILE;
    $mapaErrores = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor.',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido por el formulario.',
        UPLOAD_ERR_PARTIAL    => 'El archivo se subió solo parcialmente.',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta una carpeta temporal en el servidor.',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco.',
        UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.',
    ];
    responder(false, $mapaErrores[$codigo] ?? 'Error desconocido al subir el archivo.');
}

$nombreArchivo = basename($_FILES['subir-archivo']['name']);
$archivo = __DIR__ . "/produccion/" . $nombreArchivo;

if (!move_uploaded_file($_FILES['subir-archivo']['tmp_name'], $archivo)) {
    responder(false, 'No se pudo mover el archivo subido al servidor.');
}

try {
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $spreadsheet = IOFactory::load($archivo);
    $hoja = $spreadsheet->getActiveSheet();
    $numeroFilas = $hoja->getHighestDataRow();

    function txt($valor) {
        return trim((string) $valor);
    }

    function limpiarTexto($valor) {
        $valor = strtoupper(trim((string) $valor));
        $valor = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
        $valor = preg_replace('/[^A-Z0-9]+/', ' ', $valor);
        $valor = preg_replace('/\s+/', ' ', $valor);
        return trim($valor);
    }

    // OJO: recibe la celda completa (el objeto Cell que regresa
    // getCellByColumnAndRow), NO solo el valor -- isDateTime() necesita la
    // celda real para leer su formato numérico, armar un Cell "falso" con
    // null como worksheet revienta el constructor.
    function leerFecha($celda) {
        if ($celda === null) return null;
        $valor = $celda->getValue();
        if ($valor === null || $valor === '') return null;

        if (is_numeric($valor) && ExcelDate::isDateTime($celda)) {
            return ExcelDate::excelToDateTimeObject($valor)->format('Y-m-d');
        }
        $ts = strtotime((string) $valor);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    // ------------------------------------------------------------------
    // Inserta un lote completo de una sola vez; si falla, reintenta
    // renglón por renglón para aislar cuál es el que truena.
    // ------------------------------------------------------------------
    function insertarLote($conecta, array $lote, array $folios, $updateCompleto, array &$erroresSQL) {
        if (empty($lote)) return 0;

        $insertados = 0;
        $sql = "INSERT INTO clawback
            (folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad,
             semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo,
             saldo, fec_alta, sol_traspaso, num_formulario, fec_tramite, telefono, email_trab)
            VALUES " . implode(",", $lote) . "
            ON DUPLICATE KEY UPDATE $updateCompleto";

        $resultado = $conecta->consulta($sql);
        $errorTexto = mysqli_error($conecta->conexion);

        if ($resultado !== false && $errorTexto === '') {
            $insertados += count($lote);
            return $insertados;
        }

        // Algo falló en el lote completo: reintentamos renglón por renglón
        // para saber exactamente cuál folio/sol_traspaso truena.
        foreach ($lote as $i => $tupla) {
            $sqlFila = "INSERT INTO clawback
                (folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad,
                 semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo,
                 saldo, fec_alta, sol_traspaso, num_formulario, fec_tramite, telefono, email_trab)
                VALUES $tupla
                ON DUPLICATE KEY UPDATE $updateCompleto";

            $resFila = $conecta->consulta($sqlFila);
            $errFila = mysqli_error($conecta->conexion);

            if ($resFila !== false && $errFila === '') {
                $insertados++;
            } else {
                $erroresSQL[] = [
                    'folio'        => $folios[$i]['folio'] ?? null,
                    'sol_traspaso' => $folios[$i]['sol_traspaso'] ?? null,
                    'semana'       => $folios[$i]['semana'] ?? null,
                    'error'        => $errFila !== '' ? $errFila : 'Error desconocido al insertar/actualizar.',
                ];
            }
        }

        return $insertados;
    }

    // ------------------------------------------------------------------
    // Detección de columnas
    // ------------------------------------------------------------------
    $cols = [
        'semana..', 'Mes_Procesar', 'NAP', 'Num_Formulario', 'cod_cuenta', 'Correo',
        'Fecha Traspaso', 'ley', 'Afo Receptora', 'Entidad', 'Afore cedente',
        'Rango Antiguedad', 'TEL_MOVIL', 'Cancelación', 'P+', 'Nombre',
        'Apellido Paterno', 'Apellido Materno', 'Saldo', 'Fecha_alta',
        'FOLIO_SOL_TRASPASO', 'FECHA_TRAMITE',
    ];

    $indiceColumnas = [];
    $encabezados = $hoja->rangeToArray('A1:' . $hoja->getHighestDataColumn() . '1')[0];
    foreach ($encabezados as $idx => $encabezado) {
        $indiceColumnas[limpiarTexto($encabezado)] = $idx + 1;
    }

    $faltantes = [];
    $col = [];
    foreach ($cols as $nombreCol) {
        $clave = limpiarTexto($nombreCol);
        if (!isset($indiceColumnas[$clave])) {
            $faltantes[] = $nombreCol;
        } else {
            $col[$nombreCol] = $indiceColumnas[$clave];
        }
    }
    if (!empty($faltantes)) {
        throw new Exception('Faltan columnas en el archivo: ' . implode(', ', $faltantes));
    }

    // ------------------------------------------------------------------
    // Catálogos
    // ------------------------------------------------------------------
    $estadosMap = [];
    $resEstados = $conecta->consulta("SELECT id, nombre FROM estados");
    while ($row = mysqli_fetch_assoc($resEstados)) {
        $estadosMap[limpiarTexto($row['nombre'])] = $row['id'];
    }

    $aforeMap = [];
    $resAfores = $conecta->consulta("SELECT id, nombre FROM afores");
    while ($row = mysqli_fetch_assoc($resAfores)) {
        $aforeMap[limpiarTexto($row['nombre'])] = $row['id'];
    }

    $conecta->consulta("START TRANSACTION");

    $updateCompleto = "nap=VALUES(nap), fec_traspaso=VALUES(fec_traspaso), afo_receptora=VALUES(afo_receptora),
        afo_cedente=VALUES(afo_cedente), estado=VALUES(estado), antiguedad=VALUES(antiguedad),
        semana=VALUES(semana), mes_procesar=VALUES(mes_procesar), cod_cuenta=VALUES(cod_cuenta),
        ley=VALUES(ley), cancelacion=VALUES(cancelacion), pplus=VALUES(pplus),
        nombre_Completo=VALUES(nombre_Completo), saldo=VALUES(saldo), fec_alta=VALUES(fec_alta),
        sol_traspaso=VALUES(sol_traspaso), num_formulario=VALUES(num_formulario),
        fec_tramite=VALUES(fec_tramite), telefono=VALUES(telefono), email_trab=VALUES(email_trab)";

    $omitidosDetalle = [];
    $erroresSQL = [];
    $sinCatalogar = [];

    // Lote a insertar: YA NO se filtra por folio. Cada renglón válido del
    // Excel entra al lote tal cual venga, aunque el Num_Formulario se
    // repita muchas veces. La decisión insert-vs-update la hace MySQL solo,
    // vía la unique key (semana, num_formulario, sol_traspaso) -- ver nota
    // al inicio del archivo.
    $lote = [];
    $folioLote = [];
    $tamanoLote = 200;
    $procesados = 0;
    $insertados = 0;

    for ($fila = 2; $fila <= $numeroFilas; $fila++) {
        $num_formulario = txt($hoja->getCellByColumnAndRow($col['Num_Formulario'], $fila)->getValue());
        $NAP            = txt($hoja->getCellByColumnAndRow($col['NAP'], $fila)->getValue());
        $nombre         = txt($hoja->getCellByColumnAndRow($col['Nombre'], $fila)->getValue());
        $apPaterno      = txt($hoja->getCellByColumnAndRow($col['Apellido Paterno'], $fila)->getValue());
        $apMaterno      = txt($hoja->getCellByColumnAndRow($col['Apellido Materno'], $fila)->getValue());
        $cod_cuenta     = txt($hoja->getCellByColumnAndRow($col['cod_cuenta'], $fila)->getValue());
        $FOLIO_SOL_TRASPASO = txt($hoja->getCellByColumnAndRow($col['FOLIO_SOL_TRASPASO'], $fila)->getValue());
        $semana         = txt($hoja->getCellByColumnAndRow($col['semana..'], $fila)->getValue());

        if ($num_formulario === '') {
            $omitidosDetalle[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => 'Num_Formulario vacío'];
            continue;
        }
        if ($nombre === '' || $apPaterno === '' || $cod_cuenta === '' || $NAP === '') {
            $omitidosDetalle[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => 'Nombre/Apellido Paterno/cod_cuenta/NAP vacío'];
            continue;
        }
        if (strlen($num_formulario) > 10) {
            $omitidosDetalle[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => 'Num_Formulario con más de 10 dígitos'];
            continue;
        }

        $mes_procesar   = txt($hoja->getCellByColumnAndRow($col['Mes_Procesar'], $fila)->getValue());
        $correo         = txt($hoja->getCellByColumnAndRow($col['Correo'], $fila)->getValue());
        $fecha_Traspaso = leerFecha($hoja->getCellByColumnAndRow($col['Fecha Traspaso'], $fila));
        $ley            = substr(txt($hoja->getCellByColumnAndRow($col['ley'], $fila)->getValue()), 0, 20);
        $afoReceptoraTx = txt($hoja->getCellByColumnAndRow($col['Afo Receptora'], $fila)->getValue());
        $entidadTx      = txt($hoja->getCellByColumnAndRow($col['Entidad'], $fila)->getValue());
        $aforeCedenteTx = txt($hoja->getCellByColumnAndRow($col['Afore cedente'], $fila)->getValue());
        $Rango_Antiguedad = txt($hoja->getCellByColumnAndRow($col['Rango Antiguedad'], $fila)->getValue());
        $TEL_MOVIL      = txt($hoja->getCellByColumnAndRow($col['TEL_MOVIL'], $fila)->getValue());
        $cancelacion    = txt($hoja->getCellByColumnAndRow($col['Cancelación'], $fila)->getValue());
        $p              = txt($hoja->getCellByColumnAndRow($col['P+'], $fila)->getValue());
        $Saldo          = txt($hoja->getCellByColumnAndRow($col['Saldo'], $fila)->getValue());
        $Fecha_alta     = leerFecha($hoja->getCellByColumnAndRow($col['Fecha_alta'], $fila));
        $Fecha_tramite  = leerFecha($hoja->getCellByColumnAndRow($col['FECHA_TRAMITE'], $fila));

        $nombre_completo = trim($nombre . ' ' . $apPaterno . ' ' . $apMaterno);

        $afoReceptoraLimpia = preg_replace('/^\d+\s*/', '', $afoReceptoraTx);
        $aforeCedenteLimpia = preg_replace('/^\d+\s*/', '', $aforeCedenteTx);

        // afo_receptora / afo_cedente / estado son columnas NOT NULL en
        // clawback. Si el catálogo no reconoce el texto del Excel, YA NO
        // se omite el renglón -- se inserta de todos modos usando 0 como
        // id "sin catalogar" (así no rompe el NOT NULL), y se anota en
        // $sinCatalogar solo para que se vea en el resumen, sin bloquear
        // el insert.
        $idAfoReceptora = $aforeMap[limpiarTexto($afoReceptoraLimpia)] ?? 0;
        $idAforeCedente = $aforeMap[limpiarTexto($aforeCedenteLimpia)] ?? 0;
        $idEntidad      = $estadosMap[limpiarTexto($entidadTx)] ?? 0;

        if ($idAfoReceptora === 0) {
            $sinCatalogar[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => "Afo Receptora no reconocida: \"$afoReceptoraTx\""];
        }
        if ($idAforeCedente === 0) {
            $sinCatalogar[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => "Afore cedente no reconocida: \"$aforeCedenteTx\""];
        }
        if ($idEntidad === 0) {
            $sinCatalogar[] = ['fila' => $fila, 'folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'motivo' => "Entidad no reconocida: \"$entidadTx\""];
        }

        $num_formulario_e     = mysqli_real_escape_string($conecta->conexion, $num_formulario);
        $NAP_e                = mysqli_real_escape_string($conecta->conexion, $NAP);
        $fecha_Traspaso_e     = mysqli_real_escape_string($conecta->conexion, $fecha_Traspaso ?? '');
        $Rango_Antiguedad_e   = mysqli_real_escape_string($conecta->conexion, $Rango_Antiguedad);
        $semana_e             = mysqli_real_escape_string($conecta->conexion, $semana);
        $mes_procesar_e       = mysqli_real_escape_string($conecta->conexion, $mes_procesar);
        $cod_cuenta_e         = mysqli_real_escape_string($conecta->conexion, $cod_cuenta);
        $ley_e                = mysqli_real_escape_string($conecta->conexion, $ley);
        $cancelacion_e        = mysqli_real_escape_string($conecta->conexion, $cancelacion);
        $p_e                  = mysqli_real_escape_string($conecta->conexion, $p);
        $nombre_completo_e    = mysqli_real_escape_string($conecta->conexion, $nombre_completo);
        $Saldo_e              = mysqli_real_escape_string($conecta->conexion, $Saldo);
        $Fecha_alta_e         = mysqli_real_escape_string($conecta->conexion, $Fecha_alta ?? '');
        $FOLIO_SOL_TRASPASO_e = mysqli_real_escape_string($conecta->conexion, $FOLIO_SOL_TRASPASO);
        $Fecha_tramite_e      = mysqli_real_escape_string($conecta->conexion, $Fecha_tramite ?? '');
        $TEL_MOVIL_e          = mysqli_real_escape_string($conecta->conexion, $TEL_MOVIL);
        $correo_e             = mysqli_real_escape_string($conecta->conexion, $correo);

        $filaSql = "('$num_formulario_e', '$NAP_e', '$fecha_Traspaso_e', $idAfoReceptora, $idAforeCedente, $idEntidad,
            '$Rango_Antiguedad_e', '$semana_e', '$mes_procesar_e', '$cod_cuenta_e', '$ley_e', '$cancelacion_e', '$p_e',
            '$nombre_completo_e', '$Saldo_e', '$Fecha_alta_e', '$FOLIO_SOL_TRASPASO_e', '$num_formulario_e',
            '$Fecha_tramite_e', '$TEL_MOVIL_e', '$correo_e')";

        $procesados++;
        $lote[] = $filaSql;
        $folioLote[] = ['folio' => $num_formulario, 'sol_traspaso' => $FOLIO_SOL_TRASPASO, 'semana' => $semana];

        if (count($lote) >= $tamanoLote) {
            $insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
            $lote = [];
            $folioLote = [];
        }
    }

    if (!empty($lote)) {
        $insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
    }

    $conecta->consulta("COMMIT");

    $mensaje = "Listo. Procesados: $procesados | Guardados: $insertados | Omitidos: " . count($omitidosDetalle) . " | Errores SQL: " . count($erroresSQL);
    if (!empty($sinCatalogar)) {
        $mensaje .= " | Sin catalogar (se insertaron con 0): " . count($sinCatalogar);
    }

    responder(true, $mensaje, [
        'procesados'       => $procesados,
        'insertados'       => $insertados,
        'omitidos'         => count($omitidosDetalle),
        'omitidosDetalle'  => $omitidosDetalle,
        'errores'          => $erroresSQL,
        'sinCatalogar'     => count($sinCatalogar),
        'sinCatalogarDetalle' => $sinCatalogar,
    ]);

} catch (\Throwable $e) {
    $conecta->consulta("ROLLBACK");
    responder(false, $e->getMessage() . " (línea " . $e->getLine() . " de " . basename($e->getFile()) . ")");
}