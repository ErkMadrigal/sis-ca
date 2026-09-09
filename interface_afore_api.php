<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* Archivos grandes (decenas de miles de filas) ya no hacen una consulta
   por fila (ver comentario grande más abajo), pero por seguridad se
   sube el límite de tiempo de ejecución de todos modos. */
set_time_limit(180);

session_start();
header('Content-Type: application/json');

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   Actualización masiva de "Afore cedente" (produccion.afo_cedente)
   a partir de un xlsx con columnas CURP y "Nombre de la cuenta:
   Afore cedente". Flujo: el frontend parsea el xlsx con SheetJS y
   manda un arreglo [{curp, afore_texto}, ...]; aquí se resuelve
   cada fila y se arma un reporte. 'previsualizarAfoCedente' NO
   escribe nada en la BD (solo valida); 'aplicarAfoCedente' sí
   ejecuta los cambios, solo para las filas que quedaron en
   estatus 'ok' o 'en_espera'.

   ── POR QUÉ SE REESCRIBIÓ A "POR LOTES" ──
   La primera versión hacía, por cada fila del xlsx:
     1 SELECT para buscar el último folio de esa CURP
     + (si aplicar) 1 UPDATE o 1 INSERT más
   Con un archivo de pocos cientos de filas no se nota, pero con
   40,000 filas eso son hasta 80,000 idas y vueltas a MySQL en una
   sola petición -- garantizado que truena por max_execution_time
   (o el timeout del proxy) antes de terminar.

   Ahora el flujo es:
     1. En memoria (sin BD): se resuelven omitidas/pendientes/
        catálogo no reconocido, y se junta la lista de CURPs que sí
        necesitan ir a la BD.
     2. Un SELECT por cada lote de 500 CURPs (JOIN prod_trabajador +
        produccion), no por cada CURP. El "quedarse con el folio de
        fec_traspaso más reciente" se calcula en PHP, no con una
        subconsulta por fila.
     3. Si se está aplicando de verdad: UN UPDATE por lote de 500
        folios (con CASE ... WHEN) y UN INSERT multi-row por lote de
        500 CURPs para la tabla de espera, en vez de uno por fila.

   Con 40,000 filas esto baja de ~80,000 queries a menos de 200,
   sin cambiar en nada el resultado ni el formato de respuesta que
   ya consume el frontend (mismo 'filas' + 'conteo').
════════════════════════════════════════════════════════════ */

/* Tamaño de lote para IN(...), UPDATE...CASE e INSERT multi-row. */
define('AFC_LOTE_TAMANIO', 500);

/* Valores que en el xlsx significan "sin afore capturada" -> se
   omiten por completo, no se toca produccion.afo_cedente ni la
   tabla de espera. PRESTADORA DE SERVICIOS se trata igual que
   NO ASIGNADO/"-"/vacío: se omite, no se hace nada con esa fila. */
$AFORES_OMITIR = ['NO ASIGNADO', '-', 'PRESTADORA DE SERVICIOS'];

/* Alias: el nombre que trae el xlsx no siempre coincide tal cual
   con afores.afore. Aquí se traduce ANTES de buscar en el catálogo.
   Clave = nombre normalizado tal como viene en el xlsx.
   Valor = nombre normalizado tal como está en afores.afore. */
$ALIAS_AFORE = [
    'CITIBANAMEX' => 'BANAMEX',
];

/* Nombres que sí se reconocen como "afore real" pero que todavía no
   tienen clave definida/asignada en el catálogo `afores`. Se marcan
   como 'pendiente' (no es un error de dedo, es un pendiente real) y
   no se actualizan hasta que se defina su clave. Actualmente vacío:
   PRESTADORA DE SERVICIOS se movió a $AFORES_OMITIR arriba. */
$AFORES_PENDIENTES = [];

/* Quita acentos, colapsa espacios y pasa a mayúsculas para poder
   comparar nombres de afore sin que un acento o un espacio doble
   tire el match. */
function normalizarAforeTexto($texto) {
    $texto = trim((string) $texto);
    $texto = mb_strtoupper($texto, 'UTF-8');
    $texto = strtr($texto, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü' => 'U',
    ]);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return trim($texto);
}

function normalizarCurp($curp) {
    $curp = trim((string) $curp);
    return mb_strtoupper($curp, 'UTF-8');
}

/* Carga una sola vez el catálogo de afores (clave, afore) y lo
   regresa como mapa [nombre_normalizado => clave]. */
function cargarCatalogoAfores($conecta) {
    $mapa = [];
    $rs = $conecta->consulta("SELECT clave, afore FROM afores");
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $nombreNorm = normalizarAforeTexto($row['afore']);
            if ($nombreNorm !== '') {
                $mapa[$nombreNorm] = $row['clave'];
            }
        }
    }
    return $mapa;
}

/* Convierte 'd/m/Y' a timestamp para poder comparar fechas en PHP
   (equivalente a STR_TO_DATE(fec_traspaso,'%d/%m/%Y') pero calculado
   en memoria una sola vez, no en cada fila vía SQL). Fechas vacías o
   con formato raro regresan 0 (quedan como las "más viejas"). */
function parseFechaDMY($fecha) {
    $fecha = trim((string) $fecha);
    if ($fecha === '') return 0;
    $partes = explode('/', $fecha);
    if (count($partes) !== 3) return 0;
    [$d, $m, $y] = $partes;
    if (!ctype_digit($d) || !ctype_digit($m) || !ctype_digit($y)) return 0;
    $ts = mktime(0, 0, 0, (int) $m, (int) $d, (int) $y);
    return $ts === false ? 0 : $ts;
}

/* Busca, por LOTES de AFC_LOTE_TAMANIO CURPs, el último registro de
   producción (el de fec_traspaso más reciente) de cada una. Regresa
   un mapa [curpNormalizada => ['folio'=>, 'fec_traspaso'=>, 'afo_cedente'=>]].
   Las CURPs que no aparezcan en el mapa simplemente no tienen ningún
   folio en producción. */
function buscarUltimosRegistrosPorLote($conecta, array $curps) {
    $resultado = [];
    if (empty($curps)) return $resultado;

    foreach (array_chunk($curps, AFC_LOTE_TAMANIO) as $lote) {

        $listaEsc = implode(',', array_map(
            fn($c) => "'" . mysqli_real_escape_string($conecta->conexion, $c) . "'",
            $lote
        ));

        $rs = $conecta->consulta(
            "SELECT pt.curp AS curp, p.folio, p.fec_traspaso, p.afo_cedente
             FROM prod_trabajador pt
             INNER JOIN produccion p ON p.folio = pt.folio
             WHERE pt.curp IN ({$listaEsc})"
        );

        if (!$rs) continue;

        while ($row = mysqli_fetch_assoc($rs)) {
            $curp    = $row['curp'];
            $tsNuevo = parseFechaDMY($row['fec_traspaso']);

            if (!isset($resultado[$curp]) || $tsNuevo > $resultado[$curp]['_ts']) {
                $row['_ts']        = $tsNuevo;
                $resultado[$curp]  = $row;
            }
        }
    }

    return $resultado;
}

/* Aplica, por LOTES, los UPDATE de produccion.afo_cedente usando
   UPDATE ... SET afo_cedente = CASE folio WHEN ... END WHERE folio IN (...)
   en vez de un UPDATE por folio. $paraActualizar es [folio => clave].
   Regresa [folio => mensaje_error] solo para los lotes que fallaron. */
function aplicarActualizacionesProduccionPorLote($conecta, array $paraActualizar) {
    $errores = [];
    if (empty($paraActualizar)) return $errores;

    foreach (array_chunk(array_keys($paraActualizar), AFC_LOTE_TAMANIO) as $loteFolios) {

        $casos = '';
        $listaFolios = [];

        foreach ($loteFolios as $folio) {
            $folioEsc = mysqli_real_escape_string($conecta->conexion, $folio);
            $claveEsc = mysqli_real_escape_string($conecta->conexion, $paraActualizar[$folio]);
            $casos .= "WHEN '{$folioEsc}' THEN '{$claveEsc}' ";
            $listaFolios[] = "'{$folioEsc}'";
        }

        $listaFoliosSql = implode(',', $listaFolios);

        $ok = $conecta->consulta(
            "UPDATE produccion
             SET afo_cedente = CASE folio {$casos} END
             WHERE folio IN ({$listaFoliosSql})"
        );

        if (!$ok) {
            $msg = mysqli_error($conecta->conexion);
            foreach ($loteFolios as $folio) $errores[$folio] = $msg;
        }
    }

    return $errores;
}

/* Aplica, por LOTES, los INSERT ... ON DUPLICATE KEY UPDATE hacia la
   tabla de espera curp_afore_cedentes, en vez de un INSERT por CURP.
   $paraTablaEspera es [curp => clave]. Asume que `curp` es único/PK
   en esa tabla (si no lo es, hay que agregarle un índice único para
   que el ON DUPLICATE KEY funcione como upsert real).
   Regresa [curp => mensaje_error] solo para los lotes que fallaron. */
function aplicarTablaEsperaPorLote($conecta, array $paraTablaEspera) {
    $errores = [];
    if (empty($paraTablaEspera)) return $errores;

    foreach (array_chunk(array_keys($paraTablaEspera), AFC_LOTE_TAMANIO) as $loteCurps) {

        $valores = [];
        foreach ($loteCurps as $curp) {
            $curpEsc  = mysqli_real_escape_string($conecta->conexion, $curp);
            $claveEsc = mysqli_real_escape_string($conecta->conexion, $paraTablaEspera[$curp]);
            $valores[] = "('{$curpEsc}', '{$claveEsc}')";
        }

        $valoresSql = implode(',', $valores);

        $ok = $conecta->consulta(
            "INSERT INTO curp_afore_cedentes (curp, clave_afore_cedente)
             VALUES {$valoresSql}
             ON DUPLICATE KEY UPDATE clave_afore_cedente = VALUES(clave_afore_cedente)"
        );

        if (!$ok) {
            $msg = mysqli_error($conecta->conexion);
            foreach ($loteCurps as $curp) $errores[$curp] = $msg;
        }
    }

    return $errores;
}

/* Orquesta todo el proceso por lotes. Misma firma/salida que antes
   ([$filas, $conteo]), así que el resto del archivo (las dos
   acciones de abajo) no cambia nada. */
function procesarFilasAfoCedente($conecta, $filasInput, $aplicar, $AFORES_OMITIR, $ALIAS_AFORE, $AFORES_PENDIENTES) {

    $catalogoAfores = cargarCatalogoAfores($conecta);
    $conteo = ['ok' => 0, 'omitida' => 0, 'pendiente' => 0, 'error' => 0, 'ya_asignada' => 0, 'en_espera' => 0];

    /* ── Paso 1: resolver en memoria lo que no necesita BD, y juntar
       las CURPs que sí hay que buscar. ── */
    $filas = [];
    $curpsPorConsultar = [];

    foreach ($filasInput as $i => $filaInput) {
        $curpCrudo  = $filaInput['curp'] ?? '';
        $aforeCrudo = $filaInput['afore_texto'] ?? ($filaInput['afore'] ?? '');

        $curpNorm  = normalizarCurp($curpCrudo);
        $aforeNorm = normalizarAforeTexto($aforeCrudo);

        $fila = [
            'curp'         => $curpNorm,
            'afore_texto'  => trim((string) $aforeCrudo),
            'estatus'      => '',
            'motivo'       => '',
            'folio'        => null,
            'clave'        => null,
            'afore_match'  => null,
        ];

        if (empty($curpNorm)) {
            $fila['estatus'] = 'error';
            $fila['motivo']  = 'Fila sin CURP.';
            $filas[$i] = $fila;
            continue;
        }

        /* Vacío o valores tipo "NO ASIGNADO" / "-" -> se omite, no se toca
           nada, sin importar folio/afore ya asignada/etc. */
        if ($aforeNorm === '' || in_array($aforeNorm, $AFORES_OMITIR, true)) {
            $fila['estatus'] = 'omitida';
            $fila['motivo']  = 'Sin afore cedente capturada en el xlsx (' . ($aforeCrudo !== '' ? $aforeCrudo : 'vacío') . '); no se modifica.';
            $filas[$i] = $fila;
            continue;
        }

        /* Reconocida pero todavía sin clave asignada en el catálogo */
        if (in_array($aforeNorm, $AFORES_PENDIENTES, true)) {
            $fila['estatus'] = 'pendiente';
            $fila['motivo']  = 'Afore reconocida ("' . $aforeCrudo . '") pero aún no tiene clave definida. Se deja pendiente.';
            $filas[$i] = $fila;
            continue;
        }

        /* Alias: traduce el nombre del xlsx al nombre real del catálogo */
        $aforeBuscado = $ALIAS_AFORE[$aforeNorm] ?? $aforeNorm;

        if (!isset($catalogoAfores[$aforeBuscado])) {
            $fila['estatus'] = 'error';
            $fila['motivo']  = 'Afore no reconocida en el catálogo: "' . $aforeCrudo . '".';
            $filas[$i] = $fila;
            continue;
        }

        $fila['clave']       = $catalogoAfores[$aforeBuscado];
        $fila['afore_match'] = $aforeBuscado;
        $filas[$i] = $fila; // estatus queda '' -> se termina de resolver en el paso 3

        $curpsPorConsultar[$curpNorm] = true;
    }

    /* ── Paso 2: UN SELECT por lote de 500 CURPs, no uno por fila. ── */
    $ultimoPorCurp = buscarUltimosRegistrosPorLote($conecta, array_keys($curpsPorConsultar));

    /* ── Paso 3: terminar de resolver cada fila con lo ya cargado en
       memoria, y juntar lo que hay que escribir (también por lotes). ── */
    $paraActualizarProduccion = []; // folio => clave
    $paraTablaEspera          = []; // curp  => clave

    foreach ($filas as $i => &$fila) {
        if ($fila['estatus'] !== '') continue; // ya resuelta en el paso 1

        $curpNorm     = $fila['curp'];
        $clave        = $fila['clave'];
        $aforeBuscado = $fila['afore_match'];
        $ultimo       = $ultimoPorCurp[$curpNorm] ?? null;

        /* La CURP no tiene ningún folio en producción -> a la tabla de espera */
        if (!$ultimo) {
            $fila['estatus'] = 'en_espera';
            $fila['motivo']  = $aplicar
                ? "CURP sin folio en producción; se guardó afo_cedente = {$clave} ({$aforeBuscado}) en curp_afore_cedentes."
                : "Esta CURP no tiene folio en producción todavía. Se guardaría afo_cedente = {$clave} ({$aforeBuscado}) en curp_afore_cedentes para cuando tenga un traspaso.";
            $paraTablaEspera[$curpNorm] = $clave;
            continue;
        }

        $folio = $ultimo['folio'];
        $fecha = $ultimo['fec_traspaso'];
        $fila['folio'] = $folio;

        /* El último folio de esa CURP YA trae afore cedente -> no se toca */
        if (!empty($ultimo['afo_cedente'])) {
            $fila['estatus'] = 'ya_asignada';
            $fila['motivo']  = "El último folio ({$folio}, traspaso {$fecha}) ya tiene afo_cedente = {$ultimo['afo_cedente']}; no se modifica.";
            continue;
        }

        $fila['estatus'] = 'ok';
        $fila['motivo']  = $aplicar
            ? "afo_cedente actualizado a {$clave} ({$aforeBuscado}) en folio {$folio} (traspaso {$fecha})."
            : "Se actualizaría afo_cedente = {$clave} ({$aforeBuscado}) en folio {$folio} (traspaso {$fecha}).";
        $paraActualizarProduccion[$folio] = $clave;
    }
    unset($fila);

    /* ── Paso 4: si se está aplicando de verdad, ejecutar los cambios
       por lotes. Si algún lote falla, esas filas se re-marcan como
       'error' en vez de quedar reportadas como éxito sin haberse
       escrito de verdad. ── */
    if ($aplicar) {
        $erroresUpdate = aplicarActualizacionesProduccionPorLote($conecta, $paraActualizarProduccion);
        $erroresEspera = aplicarTablaEsperaPorLote($conecta, $paraTablaEspera);

        if ($erroresUpdate || $erroresEspera) {
            foreach ($filas as $i => &$fila) {
                if ($fila['estatus'] === 'ok' && isset($erroresUpdate[$fila['folio']])) {
                    $fila['estatus'] = 'error';
                    $fila['motivo']  = 'Error al actualizar: ' . $erroresUpdate[$fila['folio']];
                }
                if ($fila['estatus'] === 'en_espera' && isset($erroresEspera[$fila['curp']])) {
                    $fila['estatus'] = 'error';
                    $fila['motivo']  = 'Error al registrar en curp_afore_cedentes: ' . $erroresEspera[$fila['curp']];
                }
            }
            unset($fila);
        }
    }

    foreach ($filas as $fila) {
        if (isset($conteo[$fila['estatus']])) $conteo[$fila['estatus']]++;
    }

    return [array_values($filas), $conteo];
}

/* ════════════════════════════════════════════════════════════
   ACTION: previsualizarAfoCedente
   Solo valida (folio existe, afore reconocida) sin escribir nada.
════════════════════════════════════════════════════════════ */
if ($action === 'previsualizarAfoCedente') {

    $filasInput = $input['filas'] ?? [];
    if (!is_array($filasInput)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de filas inválido.']);
        exit;
    }

    [$resultado, $conteo] = procesarFilasAfoCedente(
        $conecta, $filasInput, false, $AFORES_OMITIR, $ALIAS_AFORE, $AFORES_PENDIENTES
    );

    echo json_encode(['ok' => true, 'filas' => $resultado, 'conteo' => $conteo]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: aplicarAfoCedente
   Vuelve a resolver cada fila (no se confía en lo que mandó el
   navegador en la vista previa) y ejecuta el UPDATE/INSERT en las
   filas que resuelvan en estatus 'ok' o 'en_espera'.
════════════════════════════════════════════════════════════ */
if ($action === 'aplicarAfoCedente') {

    $filasInput = $input['filas'] ?? [];
    if (!is_array($filasInput)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de filas inválido.']);
        exit;
    }

    [$resultado, $conteo] = procesarFilasAfoCedente(
        $conecta, $filasInput, true, $AFORES_OMITIR, $ALIAS_AFORE, $AFORES_PENDIENTES
    );

    echo json_encode(['ok' => true, 'filas' => $resultado, 'conteo' => $conteo]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);