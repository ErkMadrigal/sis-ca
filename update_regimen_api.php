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
// BUSCAR PERSONA (autocomplete)
// =========================================================
if($action === 'buscarPersona'){

    $query = mysqli_real_escape_string($conecta->conexion, trim($input['query'] ?? ''));

    if(strlen($query) < 2){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Escribe al menos 2 caracteres'
        ]);
        exit;
    }

    $sql = "SELECT erf.clave, erf.regimen, erf.cp, eu.nombre
            FROM empl_reg_fiscal erf
            INNER JOIN empl_usuarios eu ON erf.clave = eu.clave
            WHERE eu.nombre LIKE '%$query%' OR erf.clave LIKE '%$query%'
            ORDER BY eu.nombre ASC
            LIMIT 20";

    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al buscar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $data = [];

    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}

// =========================================================
// CAMBIAR REGIMEN
// =========================================================
if($action === 'cambiarRegimen'){

    $clave        = mysqli_real_escape_string($conecta->conexion, trim($input['clave'] ?? ''));
    $nuevoRegimen = mysqli_real_escape_string($conecta->conexion, trim($input['nuevo_regimen'] ?? ''));

    if(empty($clave) || empty($nuevoRegimen)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Faltan datos (clave o nuevo régimen)'
        ]);
        exit;
    }

    // Confirma que la clave exista en empl_reg_fiscal
    $resExiste = $conecta->consulta("SELECT clave FROM empl_reg_fiscal WHERE clave = '$clave' LIMIT 1");
    if(!$resExiste || mysqli_num_rows($resExiste) === 0){
        echo json_encode([
            'status'  => 'error',
            'message' => 'No se encontró esa clave en empl_reg_fiscal'
        ]);
        exit;
    }

    // =========================
    // ACTUALIZA EL REGIMEN
    // =========================
    $okUpdate = $conecta->consulta("UPDATE empl_reg_fiscal SET regimen = '$nuevoRegimen' WHERE clave = '$clave'");

    if(!$okUpdate){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al actualizar el régimen: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $facturasBloqueadas = 0;

    // =========================================================
    // Si el nuevo regimen es "Asimilados a Salarios", se borran
    // los registros de facturas_nap de esa clave, PERO solo los
    // que tengan estatus_fac = 0 (sin nada subido). Los que ya
    // tengan avance (estatus_fac != 0) NO se borran y se avisan.
    // =========================================================
    if($nuevoRegimen === 'Asimilados a Salarios'){

        $conecta->consulta("DELETE FROM facturas_nap WHERE clave = '$clave' AND estatus_fac = 0");

        $resPendientes = $conecta->consulta("SELECT COUNT(*) as pendientes FROM facturas_nap WHERE clave = '$clave' AND estatus_fac <> 0");

        if($resPendientes){
            $rowPend = mysqli_fetch_assoc($resPendientes);
            $facturasBloqueadas = (int)($rowPend['pendientes'] ?? 0);
        }
    }

    echo json_encode([
        'status'              => 'ok',
        'message'             => 'Régimen actualizado correctamente',
        'facturas_bloqueadas' => $facturasBloqueadas
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;