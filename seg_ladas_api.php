<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

set_time_limit(0);
ini_set('memory_limit', '512M');

require './vendor/autoload.php';
include('./funciones/encripta.php');

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* Si algo truena en PHP (fatal), regresar JSON legible en vez de una respuesta vacía */
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => 'Error PHP: ' . $e['message'] . ' (' . basename($e['file']) . ':' . $e['line'] . ')']);
    }
});

/* carobra.* y parte de carobra_2014 pueden venir en latin1: si el texto no es
   UTF-8 válido se convierte; si ya lo es, se deja igual (evita doble codificación). */
function utf8s($v) {
    if ($v === null) return '';
    $v = (string)$v;
    if ($v === '' || mb_check_encoding($v, 'UTF-8')) return $v;
    return mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
}

/* json_encode que NUNCA regresa vacío: sustituye bytes inválidos y avisa si aun así falla */
function responderJson($arr) {
    $json = json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    if ($json === false) {
        $json = json_encode(['status' => 'error', 'msg' => 'No se pudo codificar la respuesta: ' . json_last_error_msg()]);
    }
    echo $json;
}

/* Solo lada válida: 2 o 3 dígitos */
function ladaValida($l) { return is_string($l) && preg_match('/^\d{2,3}$/', $l); }

/* ── Catálogo de ladas: archivo JSON { "Estado": ["81","821", ...], ... } ──
   El archivo debe estar junto a este PHP y ser escribible por el usuario
   del servidor web (para agregar/quitar ladas desde la vista). */
define('LADAS_JSON', __DIR__ . '/ladas.json');

function leerCatalogo() {
    if (!is_file(LADAS_JSON)) return [];
    $j = json_decode(file_get_contents(LADAS_JSON), true);
    return is_array($j) ? $j : [];
}

function guardarCatalogo($cat) {
    ksort($cat, SORT_LOCALE_STRING);
    foreach ($cat as $e => $l) {
        $l = array_values(array_unique($l));
        usort($l, function($a, $b) { return strlen($a) - strlen($b) ?: strcmp($a, $b); });
        $cat[$e] = $l;
    }
    $json = json_encode($cat, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return @file_put_contents(LADAS_JSON, $json . "\n", LOCK_EX) !== false;
}

/* ── Estados con ladas cargadas ── */
if ($action === 'listarEstados') {
    $cat  = leerCatalogo();
    $data = [];
    foreach ($cat as $estado => $ladas) {
        $data[] = ['estado' => $estado, 'total' => count($ladas)];
    }
    usort($data, function($a, $b) { return strcmp($a['estado'], $b['estado']); });
    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

/* ── Ladas de un estado ── */
if ($action === 'getLadas') {
    $cat    = leerCatalogo();
    $estado = trim($input['estado'] ?? '');
    $ladas  = isset($cat[$estado]) ? array_values($cat[$estado]) : [];
    echo json_encode(['status' => 'ok', 'data' => $ladas]);
    exit;
}

/* ── Agregar lada al catálogo ── */
if ($action === 'agregarLada') {
    $estado = trim($input['estado'] ?? '');
    $lada   = trim($input['lada'] ?? '');
    $cat    = leerCatalogo();
    if ($estado === '' || !isset($cat[$estado]) || !ladaValida($lada)) {
        echo json_encode(['status' => 'error', 'msg' => 'Estado y lada (2 o 3 dígitos) son requeridos']);
        exit;
    }
    /* Una misma lada puede estar en varios estados (p. ej. 55 en CDMX y Edomex);
       solo se evita duplicarla dentro del mismo estado. */
    if (in_array($lada, $cat[$estado], true)) {
        echo json_encode(['status' => 'error', 'msg' => "La lada $lada ya está en $estado"]);
        exit;
    }
    $cat[$estado][] = $lada;
    echo json_encode(guardarCatalogo($cat)
        ? ['status' => 'ok', 'msg' => 'Lada agregada']
        : ['status' => 'error', 'msg' => 'No se pudo escribir ladas.json (revisa permisos de escritura)']);
    exit;
}

/* ── Quitar lada del catálogo ── */
if ($action === 'quitarLada') {
    $estado = trim($input['estado'] ?? '');
    $lada   = trim($input['lada'] ?? '');
    $cat    = leerCatalogo();
    if (!ladaValida($lada) || !isset($cat[$estado])) {
        echo json_encode(['status' => 'error', 'msg' => 'Estado o lada inválidos']);
        exit;
    }
    $cat[$estado] = array_values(array_diff($cat[$estado], [$lada]));
    echo json_encode(guardarCatalogo($cat)
        ? ['status' => 'ok', 'msg' => 'Lada eliminada']
        : ['status' => 'error', 'msg' => 'No se pudo escribir ladas.json (revisa permisos de escritura)']);
    exit;
}

/* ── Consulta principal ──
   Folios de produccion cuyo trabajador/cliente tiene celular o telefono
   que empieza con alguna de las ladas elegidas. */
if ($action === 'consultar') {

    $ladas = array_values(array_unique(array_filter((array)($input['ladas'] ?? []), 'ladaValida')));
    if (empty($ladas)) {
        echo json_encode(['status' => 'error', 'msg' => 'Selecciona al menos una lada']);
        exit;
    }

    $fi = trim($input['fecha_ini'] ?? '');
    $ff = trim($input['fecha_fin'] ?? '');
    $okFecha = function($f) { return preg_match('/^\d{4}-\d{2}-\d{2}$/', $f); };
    if (!$okFecha($fi)) $fi = date('Y') . '-01-01';
    if (!$okFecha($ff)) $ff = date('Y-m-d');

    $regex = '^(' . implode('|', $ladas) . ')';
    $limpia = function($col) {
        return "REPLACE(REPLACE(REPLACE(REPLACE(TRIM($col),' ',''),'-',''),'(',''),')','')";
    };
    $LIMITE = 50000;
    $fiE = mysqli_real_escape_string($conecta->conexion, $fi);
    $ffE = mysqli_real_escape_string($conecta->conexion, $ff);

    /* Mismo query que corre en phpMyAdmin: tablas SIN prefijo de base, así que
       se resuelven contra la base seleccionada. Se prueban las bases candidatas
       y se usa la primera que devuelva filas. */
    $sql = "SELECT
                e.no_empleado, e.clave, u.nombre, e.domicilio,
                e.celular AS emp_celular, e.telefono AS emp_telefono,
                ee.gerencia, ee.brocker, ee.sc,
                p.folio, p.fec_captura,
                pt.trabajador AS nombre_cliente,
                pt.telefono AS cliente_telefono, pt.celular AS cliente_celular,
                pg.saldo_real, pg.fec_pago
            FROM empleados e
            INNER JOIN usuarios u ON e.clave = u.clave
            LEFT JOIN estruc_empleados ee ON e.clave = ee.clave
            INNER JOIN produccion p ON e.no_empleado = p.no_empleado
            INNER JOIN prod_trabajador pt ON p.folio = pt.folio
            INNER JOIN pagos pg ON p.folio = pg.folio
            WHERE (pt.celular REGEXP '$regex' OR pt.telefono REGEXP '$regex')
              AND COALESCE(STR_TO_DATE(p.fec_captura,'%d/%m/%Y'), STR_TO_DATE(p.fec_captura,'%Y-%m-%d'))
                  BETWEEN '$fiE' AND '$ffE'
            ORDER BY e.no_empleado, COALESCE(STR_TO_DATE(p.fec_captura,'%d/%m/%Y'), STR_TO_DATE(p.fec_captura,'%Y-%m-%d'))
            LIMIT " . ($LIMITE + 1);

    $res = null; $bdUsada = ''; $intentos = [];
    foreach (['carobra', 'carobra_2014'] as $bd) {
        if (!@mysqli_query($conecta->conexion, "USE `$bd`")) { $intentos[$bd] = 'USE: ' . mysqli_error($conecta->conexion); continue; }
        $r = @mysqli_query($conecta->conexion, $sql);
        if (!$r) { $intentos[$bd] = mysqli_error($conecta->conexion); continue; }
        $intentos[$bd] = mysqli_num_rows($r) . ' filas';
        if (!$res || mysqli_num_rows($r) > 0) { $res = $r; $bdUsada = $bd; }
        if (mysqli_num_rows($r) > 0) break;
    }
    if (!$res) {
        echo json_encode(['status' => 'error', 'msg' => 'Error en consulta: ' . json_encode($intentos)]);
        exit;
    }

    $soloDig = function($v) { return preg_replace('/\D/', '', (string)$v); };
    $ladaDe  = function($num) use ($ladas) {
        /* si coinciden 2 y 3 dígitos (p.ej. 81 y 821) gana la más larga */
        $mejor = '';
        foreach ($ladas as $l) {
            if (strpos($num, $l) === 0 && strlen($l) > strlen($mejor)) $mejor = $l;
        }
        return $mejor;
    };

    $filas = [];
    $truncado = false;
    while ($row = mysqli_fetch_assoc($res)) {
        if (count($filas) >= $LIMITE) { $truncado = true; break; }

        $c = $soloDig($row['cliente_celular']);
        $t = $soloDig($row['cliente_telefono']);
        $ladaCel = $ladaDe($c);
        $ladaTel = $ladaDe($t);

        $dom = trim(implode(', ', array_filter(array_map('trim', explode('|', utf8s($row['domicilio']))))));

        $filas[] = [
            'no_empleado'      => utf8s($row['no_empleado']),
            'clave'            => utf8s($row['clave']),
            'nombre'           => utf8s($row['nombre']),
            'domicilio'        => $dom,
            'emp_celular'      => utf8s($row['emp_celular']),
            'emp_telefono'     => utf8s($row['emp_telefono']),
            'gerencia'         => utf8s($row['gerencia']),
            'brocker'          => utf8s($row['brocker']),
            'sc'               => utf8s($row['sc']),
            'folio'            => utf8s($row['folio']),
            'fec_captura'      => utf8s($row['fec_captura']),
            'nombre_cliente'   => utf8s($row['nombre_cliente']),
            'cliente_telefono' => utf8s($row['cliente_telefono']),
            'cliente_celular'  => utf8s($row['cliente_celular']),
            'lada'             => $ladaCel !== '' ? $ladaCel : $ladaTel,
            'saldo_real'       => $row['saldo_real'],
            'fec_pago'         => utf8s($row['fec_pago']),
        ];
    }

    /* Si no hubo filas, se corre la consulta por etapas para ver en qué paso se
       cae a 0 (JOIN, fechas o lada) y con qué conexión está corriendo el PHP. */
    $debug = null;
    if (empty($filas)) {
        $fiE = mysqli_real_escape_string($conecta->conexion, $fi);
        $ffE = mysqli_real_escape_string($conecta->conexion, $ff);
        $rangoP = "COALESCE(STR_TO_DATE(p.fec_captura,'%d/%m/%Y'), STR_TO_DATE(p.fec_captura,'%Y-%m-%d')) BETWEEN '$fiE' AND '$ffE'";
        $regexE = mysqli_real_escape_string($conecta->conexion, $regex);
        $ladaPt = "(pt.celular REGEXP '$regexE' OR pt.telefono REGEXP '$regexE')";

        $pasos = [
            '1_produccion_en_rango_fechas' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.produccion p WHERE $rangoP",
            '2_+_join_empleados' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.produccion p
                 INNER JOIN carobra.empleados e ON e.no_empleado = p.no_empleado WHERE $rangoP",
            '3_+_join_usuarios' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.produccion p
                 INNER JOIN carobra.empleados e ON e.no_empleado = p.no_empleado
                 INNER JOIN carobra.usuarios u ON e.clave = u.clave WHERE $rangoP",
            '4_+_join_prod_trabajador' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.produccion p
                 INNER JOIN carobra.empleados e ON e.no_empleado = p.no_empleado
                 INNER JOIN carobra.usuarios u ON e.clave = u.clave
                 INNER JOIN carobra_2014.prod_trabajador pt ON p.folio = pt.folio WHERE $rangoP",
            '5_+_join_pagos' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.produccion p
                 INNER JOIN carobra.empleados e ON e.no_empleado = p.no_empleado
                 INNER JOIN carobra.usuarios u ON e.clave = u.clave
                 INNER JOIN carobra_2014.prod_trabajador pt ON p.folio = pt.folio
                 INNER JOIN carobra_2014.pagos pg ON p.folio = pg.folio WHERE $rangoP",
            '6_prod_trabajador_con_lada_(sin_fechas_ni_joins)' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.prod_trabajador pt WHERE $ladaPt",
            '7_lada_+_produccion_en_rango' =>
                "SELECT COUNT(*) AS total FROM carobra_2014.prod_trabajador pt
                 INNER JOIN carobra_2014.produccion p ON p.folio = pt.folio
                 WHERE $ladaPt AND $rangoP",
            'produccion_rango_total' =>
                "SELECT COUNT(*) AS total, MIN(fec_captura) AS min_f, MAX(fec_captura) AS max_f FROM produccion",
            'servidor' =>
                "SELECT @@hostname AS host, @@port AS puerto, @@version AS version, DATABASE() AS db",
            'conexion' =>
                "SELECT DATABASE() AS db, CURRENT_USER() AS usuario, @@character_set_connection AS charset,
                        @@collation_connection AS collation, @@version AS version",
        ];

        $debug = ['sql_ejecutado' => $sql, 'ladas' => $ladas, 'regex' => $regex, 'pasos' => []];
        foreach ($pasos as $nombre => $q) {
            $r = $conecta->consulta($q);
            if ($r) {
                $debug['pasos'][$nombre] = mysqli_fetch_assoc($r);
            } else {
                $debug['pasos'][$nombre] = 'ERROR: ' . mysqli_error($conecta->conexion);
            }
        }
    }

    responderJson([
        'status'   => 'ok',
        'data'     => $filas,
        'debug'    => $debug ? ($debug + ['intentos' => $intentos, 'bd_usada' => $bdUsada]) : null,
        'truncado' => $truncado,
        'limite'   => $LIMITE,
        'fecha_ini'=> $fi,
        'fecha_fin'=> $ff,
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'msg' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;