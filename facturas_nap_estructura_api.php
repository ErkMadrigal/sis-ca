<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

session_start();

require './vendor/autoload.php';
include("./funciones/encripta.php");

$conecta = new conector();

$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : [];

$action = $input['action'] ?? '';

/* =========================================================
   Arma el WHERE dinámico compartido entre getFacturasNapEstructura
   y descargarZip -- así el zip SIEMPRE trae exactamente los mismos
   PDFs que se ven en la tabla filtrada, nunca de más ni de menos.
   ========================================================= */
function armarWhereFacturasEstructura($conecta, $input){

    $anio        = mysqli_real_escape_string($conecta->conexion, $input['anio']     ?? '');
    $periodo     = mysqli_real_escape_string($conecta->conexion, $input['periodo']  ?? '');
    $scRaw       = trim($input['sc']       ?? '');
    $brockerRaw  = trim($input['brocker']  ?? '');
    $gerenciaRaw = trim($input['gerencia'] ?? '');
    $nameNap     = trim($input['nap']      ?? '');

    // El campo NAP (y a veces SC/Brocker/Gerencia, según cómo autocompletar()
    // los llene) puede venir como "NOMBRE (1234567)" -- se quita el "(...)"
    // final antes de armar el LIKE, si no, el sufijo nunca hace match contra
    // el nombre plano guardado en estruc_empleados y la consulta regresa 0
    // aunque el nombre en sí sí exista.
    $sc       = mysqli_real_escape_string($conecta->conexion, preg_replace('/\s*\([^)]*\)\s*$/', '', $scRaw));
    $brocker  = mysqli_real_escape_string($conecta->conexion, preg_replace('/\s*\([^)]*\)\s*$/', '', $brockerRaw));
    $gerencia = mysqli_real_escape_string($conecta->conexion, preg_replace('/\s*\([^)]*\)\s*$/', '', $gerenciaRaw));

    // El campo NAP se llena vía autocompletar() como "NOMBRE (1234567)" --
    // aquí se extrae solo el número, igual que en cumplimiento_api.php.
    preg_match('/\((\d+)\)/', $nameNap, $match);
    $nap = $match[1] ?? '';

    $errores = [];

    if(empty($anio) || empty($periodo)){
        $errores[] = 'Debes seleccionar año y periodo';
    }

    // "TODOS" es el valor de placeholder con el que arranca el campo SC
    // (igual que en seg_cumplimiento.php) -- NO es un nombre real, así que
    // no debe usarse como filtro o el LIKE nunca matchea nada.
    if($sc === 'TODOS') $sc = '';

    // nivel 12 (super usuario / admin) puede buscar todo el periodo sin
    // acotar por NAP/SC/Brocker/Gerencia, igual que en getCumplimiento.
    $nivel = $input['nivel'] ?? '';
    if($nivel != 12 && empty($nap) && empty($sc) && empty($brocker) && empty($gerencia)){
        $errores[] = 'Debes indicar NAP, SC, Brocker o Gerencia para buscar';
    }

    // TRIM en anio/periodo/nap -- por si el dato guardado trae espacios
    // de más (muy común en columnas VARCHAR capturadas manualmente), para
    // que una comparación exacta no falle en silencio y regrese vacío.
    $where = [];
    if(!empty($anio))     $where[] = "TRIM(fn.anio) = TRIM('$anio')";
    if(!empty($periodo))  $where[] = "TRIM(fn.periodo) = TRIM('$periodo')";
    if(!empty($nap))      $where[] = "TRIM(n.nap) = TRIM('$nap')";
    if(!empty($sc))       $where[] = "ee.sc LIKE '%$sc%'";
    if(!empty($brocker))  $where[] = "ee.brocker LIKE '%$brocker%'";
    if(!empty($gerencia)) $where[] = "ee.gerencia LIKE '%$gerencia%'";

    return [
        'errores'  => $errores,
        'whereSQL' => $where ? implode(" AND ", $where) : "1=1",
    ];
}

if($action === 'getFacturasNapEstructura'){

    header('Content-Type: application/json');

    $armado = armarWhereFacturasEstructura($conecta, $input);

    if(!empty($armado['errores'])){
        echo json_encode([
            'status'  => 'error',
            'message' => implode('. ', $armado['errores'])
        ]);
        exit;
    }

    $sql = "SELECT DISTINCT ee.sc, ee.brocker, ee.gerencia, e.nombre, e.clave, n.nap,
                   fn.anio, fn.periodo, fn.reg_fiscal, fn.bono, fn.ret_isr, fn.iva, fn.ret_iva,
                   fn.no_factura, fn.nom_pdf, fn.estatus_fac
            FROM naps n
                INNER JOIN empl_usuarios e   ON n.no_empleado = e.no_empleado
                INNER JOIN facturas_nap fn   ON e.clave = fn.clave
                INNER JOIN estruc_empleados ee ON ee.clave = e.clave
            WHERE {$armado['whereSQL']}
            ORDER BY ee.sc ASC, e.nombre ASC, fn.anio DESC, fn.periodo DESC";

    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al consultar facturas: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $data = [];
    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    // =========================================================
    // DIAGNÓSTICO: si no hubo resultados, se corre la misma consulta
    // por etapas (agregando un JOIN/filtro a la vez) para ver en cuál
    // paso se cae a 0 -- así no hay que adivinar si es el JOIN con
    // naps/empl_usuarios/estruc_empleados o alguno de los filtros.
    // =========================================================
    $debug = null;
    if(empty($data)){

        $anio     = mysqli_real_escape_string($conecta->conexion, $input['anio']     ?? '');
        $periodo  = mysqli_real_escape_string($conecta->conexion, $input['periodo']  ?? '');
        $wherePer = "TRIM(fn.anio) = TRIM('$anio') AND TRIM(fn.periodo) = TRIM('$periodo')";

        $pasos = [
            '1_facturas_nap_anio_periodo' =>
                "SELECT COUNT(*) AS total FROM facturas_nap fn WHERE $wherePer",
            '2_join_empl_usuarios_por_clave' =>
                "SELECT COUNT(*) AS total FROM facturas_nap fn
                    INNER JOIN empl_usuarios e ON fn.clave = e.clave
                 WHERE $wherePer",
            '3_join_naps_por_no_empleado' =>
                "SELECT COUNT(*) AS total FROM facturas_nap fn
                    INNER JOIN empl_usuarios e ON fn.clave = e.clave
                    INNER JOIN naps n ON n.no_empleado = e.no_empleado
                 WHERE $wherePer",
            '4_join_estruc_empleados_por_clave' =>
                "SELECT COUNT(*) AS total FROM facturas_nap fn
                    INNER JOIN empl_usuarios e ON fn.clave = e.clave
                    INNER JOIN naps n ON n.no_empleado = e.no_empleado
                    INNER JOIN estruc_empleados ee ON ee.clave = e.clave
                 WHERE $wherePer",
            '5_con_todos_los_filtros_enviados' =>
                "SELECT COUNT(*) AS total FROM naps n
                    INNER JOIN empl_usuarios e ON n.no_empleado = e.no_empleado
                    INNER JOIN facturas_nap fn ON e.clave = fn.clave
                    INNER JOIN estruc_empleados ee ON ee.clave = e.clave
                 WHERE {$armado['whereSQL']}",
        ];

        $debug = [];
        foreach($pasos as $nombrePaso => $sqlPaso){
            $resPaso = $conecta->consulta($sqlPaso);
            if($resPaso){
                $rowPaso = mysqli_fetch_assoc($resPaso);
                $debug[$nombrePaso] = (int)($rowPaso['total'] ?? 0);
            } else {
                $debug[$nombrePaso] = 'ERROR: ' . mysqli_error($conecta->conexion);
            }
        }
        $debug['filtros_recibidos'] = [
            'anio'     => $anio,
            'periodo'  => $periodo,
            'nap_raw'      => $input['nap']      ?? '',
            'sc_raw'       => $input['sc']       ?? '',
            'brocker_raw'  => $input['brocker']  ?? '',
            'gerencia_raw' => $input['gerencia'] ?? '',
            'whereSQL' => $armado['whereSQL'],
        ];
    }

    echo json_encode([
        'status' => 'ok',
        'debug'  => $debug,
        'data'   => $data
    ]);

    exit;
}

if($action === 'descargarZip'){

    $armado = armarWhereFacturasEstructura($conecta, $input);

    if(!empty($armado['errores'])){
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => implode('. ', $armado['errores'])
        ]);
        exit;
    }

    $sql = "SELECT DISTINCT fn.nom_pdf
            FROM naps n
                INNER JOIN empl_usuarios e   ON n.no_empleado = e.no_empleado
                INNER JOIN facturas_nap fn   ON e.clave = fn.clave
                INNER JOIN estruc_empleados ee ON ee.clave = e.clave
            WHERE {$armado['whereSQL']}
              AND fn.nom_pdf IS NOT NULL AND fn.nom_pdf <> ''";

    $res = $conecta->consulta($sql);

    if(!$res){
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al consultar PDFs: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $pdfs = [];
    while($row = mysqli_fetch_assoc($res)){
        $pdfs[] = $row['nom_pdf'];
    }

    if(empty($pdfs)){
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'No hay PDFs disponibles para descargar con estos filtros'
        ]);
        exit;
    }

    $carpetaFacturas = __DIR__ . '/facturas/';
    $tmpZip          = tempnam(sys_get_temp_dir(), 'facnap_') . '.zip';

    $zip = new ZipArchive();
    if($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true){
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'No se pudo crear el archivo ZIP'
        ]);
        exit;
    }

    $agregados = 0;
    foreach($pdfs as $nomPdf){
        $rutaPdf = $carpetaFacturas . basename($nomPdf);
        if(is_file($rutaPdf)){
            $zip->addFile($rutaPdf, basename($nomPdf));
            $agregados++;
        }
    }

    $zip->close();

    if($agregados === 0){
        @unlink($tmpZip);
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'Ninguno de los PDFs de estos filtros se encontró en el servidor'
        ]);
        exit;
    }

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="facturas_nap_' . date('Ymd_His') . '.zip"');
    header('Content-Length: ' . filesize($tmpZip));
    readfile($tmpZip);
    @unlink($tmpZip);
    exit;
}

header('Content-Type: application/json');
echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;