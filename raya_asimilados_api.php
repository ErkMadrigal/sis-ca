<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

require './vendor/autoload.php';
include("./funciones/encripta.php");

$conecta = new conector();

$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : [];

$action = $input['action'] ?? '';

// =========================================================
// CARGAR LISTA DE RAYA (Asimilados) -> raya_asimilados
// El archivo ya se parseó en el navegador; aquí solo llega
// un arreglo de {nombre, isr} + el año/periodo elegidos.
// =========================================================
if($action === 'cargarRayaAsimilados'){

    $anio      = mysqli_real_escape_string($conecta->conexion, trim($input['anio'] ?? ''));
    $periodo   = mysqli_real_escape_string($conecta->conexion, trim($input['periodo'] ?? ''));
    $registros = is_array($input['registros'] ?? null) ? $input['registros'] : [];

    // =========================
    // VALIDAR FILTROS
    // =========================
    if(empty($anio) || empty($periodo)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes seleccionar año y periodo'
        ]);
        exit;
    }

    if(!preg_match('/^\d{4}$/', $anio)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Año inválido'
        ]);
        exit;
    }

    if(empty($registros)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'No hay registros en el archivo para cargar'
        ]);
        exit;
    }

    // =========================================================
    // RESUELVE CADA NOMBRE -> CLAVE en empl_usuarios
    // Los que no encuentran coincidencia se omiten y se reportan.
    // =========================================================
    $paraInsertar = [];
    $omitidos     = [];

    foreach($registros as $reg){
        $nombreOriginal = trim($reg['nombre'] ?? '');
        $isr            = floatval($reg['isr'] ?? 0);

        if(empty($nombreOriginal)){
            continue;
        }

        $nombreEsc = mysqli_real_escape_string($conecta->conexion, $nombreOriginal);
        $resClave  = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE UPPER(TRIM(nombre)) = UPPER('$nombreEsc') LIMIT 1");

        if($resClave && mysqli_num_rows($resClave) > 0){
            $filaClave      = mysqli_fetch_assoc($resClave);
            $paraInsertar[] = ['clave' => $filaClave['clave'], 'isr' => $isr];
        } else {
            $omitidos[] = $nombreOriginal;
        }
    }

    if(empty($paraInsertar)){
        echo json_encode([
            'status'   => 'error',
            'message'  => 'Ningún nombre del archivo coincidió con empl_usuarios. No se guardó nada.',
            'omitidos' => $omitidos
        ]);
        exit;
    }

    // =========================================================
    // REEMPLAZA lo que ya hubiera para ese Año/Periodo, para que
    // una recarga siempre deje la versión buena (sin duplicados).
    // =========================================================
    $conecta->consulta("DELETE FROM raya_asimilados WHERE anio = '$anio' AND periodo = '$periodo'");

    $insertados = 0;

    foreach($paraInsertar as $fila){
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $fila['clave']);
        $isrVal   = $fila['isr'];

        $okInsert = $conecta->consulta(
            "INSERT INTO raya_asimilados (clave, anio, periodo, isr_asimilados) VALUES ('$claveEsc', '$anio', '$periodo', '$isrVal')"
        );

        if($okInsert){
            $insertados++;
        }
    }

    echo json_encode([
        'status'     => 'ok',
        'message'    => 'Carga completada',
        'insertados' => $insertados,
        'omitidos'   => $omitidos
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;