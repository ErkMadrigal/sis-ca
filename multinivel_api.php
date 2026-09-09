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

// Socios que hoy ya tienen una tabla de gerencias conocida.
// "AFORE"      -> tabla `empl_usuarios` (clave, nombre): la "gerencia"
//                 es el EMPLEADO responsable de ese socio.
// "DEPORTENIS" -> tabla `empleados_dp` (clave, promotor): la "gerencia"
//                 es un COORDINADOR (empleado cuya clave aparece como
//                 "coordinador" de algún otro registro de esa tabla).
// Cuando se agreguen más socios con su propia tabla, sumarlos aquí.
$SOCIOS_CON_GERENCIA = ['AFORE', 'DPORTENIS'];

// =========================================================
// ASIGNAR: liga un Socio con un Área de Negocio y, si el socio
// tiene tabla de gerencias conocida (hoy solo "AFORE"), también
// con una Gerencia puntual. Un mismo Socio+Área puede repetirse
// varias veces con gerencias distintas (una fila por gerencia).
// =========================================================
if($action === 'asignarMultinivel'){

    $claveSocio   = mysqli_real_escape_string($conecta->conexion, trim($input['clave_socio'] ?? ''));
    $claveNegocio = mysqli_real_escape_string($conecta->conexion, trim($input['clave_negocio'] ?? ''));
    $idGerencia   = mysqli_real_escape_string($conecta->conexion, trim($input['id_gerencia'] ?? ''));

    if(empty($claveSocio) || empty($claveNegocio)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes seleccionar un Socio y un Área de Negocio'
        ]);
        exit;
    }

    // id_gerencia ya no es siempre numérico: para AFORES es la CLAVE del
    // empleado (empl_usuarios.clave, alfanumérica, ej. "AAGARCIA"). Solo
    // se valida un formato razonable de clave; el escapado de arriba ya
    // cubre la seguridad contra inyección SQL.
    if(!empty($idGerencia) && !preg_match('/^[A-Za-z0-9_]{1,50}$/', $idGerencia)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gerencia inválida'
        ]);
        exit;
    }

    // Si el SOCIO sí tiene tabla de gerencias conocida, la gerencia es obligatoria
    // (es la razón de ser del nivel extra). Si no, se ignora cualquier cosa que
    // llegue y se guarda con el sentinel 0 ("sin gerencia").
    if(in_array($claveSocio, $SOCIOS_CON_GERENCIA, true)){
        if(empty($idGerencia)){
            echo json_encode([
                'status'  => 'error',
                'message' => 'Para este Socio también debes elegir una Gerencia'
            ]);
            exit;
        }
    } else {
        $idGerencia = '';
    }

    // Sentinel: sin gerencia = 0 (la columna id_gerencia es NOT NULL)
    $idGerencia = empty($idGerencia) ? '0' : $idGerencia;

    // =========================================================
    // Evita duplicar exactamente el mismo trío socio + área + gerencia
    // =========================================================
    $resDup = $conecta->consulta("SELECT id FROM multinivel WHERE clave_socio = '$claveSocio' AND clave_negocio = '$claveNegocio' AND id_gerencia = '$idGerencia' LIMIT 1");
    if($resDup && mysqli_num_rows($resDup) > 0){
        echo json_encode([
            'status'  => 'error',
            'message' => $idGerencia === '0'
                ? 'Ese Socio ya tiene asignada esa Área de Negocio'
                : 'Ese Socio ya tiene asignada esa Área de Negocio con esa Gerencia'
        ]);
        exit;
    }

    $okInsert = $conecta->consulta("INSERT INTO multinivel (clave_socio, clave_negocio, id_gerencia) VALUES ('$claveSocio','$claveNegocio','$idGerencia')");

    if(!$okInsert){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al asignar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Asignación guardada correctamente'
    ]);

    exit;
}

// =========================================================
// ELIMINAR: borra exactamente un par/trío Socio + Área (+ Gerencia)
// (relación plana, sin jerarquía, así que siempre es directo)
// =========================================================
if($action === 'eliminarMultinivel'){

    $claveSocio   = mysqli_real_escape_string($conecta->conexion, trim($input['clave_socio'] ?? ''));
    $claveNegocio = mysqli_real_escape_string($conecta->conexion, trim($input['clave_negocio'] ?? ''));
    $idGerencia   = mysqli_real_escape_string($conecta->conexion, trim($input['id_gerencia'] ?? '0'));

    if(empty($claveSocio) || empty($claveNegocio)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Faltan datos para eliminar la asignación'
        ]);
        exit;
    }

    // id_gerencia puede ser alfanumérico (clave de empleado para AFORES);
    // si viene algo con formato inválido, se cae al sentinel "sin gerencia".
    if(!preg_match('/^[A-Za-z0-9_]{1,50}$/', $idGerencia)){
        $idGerencia = '0';
    }

    $okDelete = $conecta->consulta("DELETE FROM multinivel WHERE clave_socio = '$claveSocio' AND clave_negocio = '$claveNegocio' AND id_gerencia = '$idGerencia'");

    if(!$okDelete){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al eliminar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Asignación eliminada correctamente'
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;