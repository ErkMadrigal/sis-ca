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
// ASIGNAR SC / BROCKER / GERENCIA
// =========================================================
if($action === 'asignarEstructura'){

    $sc         = mysqli_real_escape_string($conecta->conexion, trim($input['sc'] ?? ''));
    $brocker    = mysqli_real_escape_string($conecta->conexion, trim($input['brocker'] ?? ''));
    $idGerencia = mysqli_real_escape_string($conecta->conexion, trim($input['id_gerencia'] ?? ''));

    // =========================================================
    // FLEXIBILIDAD DE NIVELES: SC, Brocker y Gerencia son cada
    // uno opcional de forma independiente. Se permite cualquier
    // combinación (sc->br->ger, br->ger, ger sola, sc->ger,
    // sc->br, etc.), siempre y cuando al menos uno venga lleno.
    // =========================================================
    if(empty($sc) && empty($brocker) && empty($idGerencia)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes llenar al menos uno: SC, Brocker o Gerencia'
        ]);
        exit;
    }

    if(!empty($idGerencia) && !preg_match('/^\d+$/', $idGerencia)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gerencia inválida'
        ]);
        exit;
    }

    // Sentinel: sin gerencia = 0 (la columna id_gerencia es NOT NULL)
    $idGerencia = empty($idGerencia) ? '0' : $idGerencia;

    // =========================================================
    // VALIDACIONES DE ROLES CRUZADOS
    // =========================================================

    // 1) Un Brocker que YA está asignado a un SC real (no vacío) distinto de sí mismo
    //    no puede usarse como SC (si no tiene sc "de alguien más", sí puede)
    if(!empty($sc)){
        $resScComoBrocker = $conecta->consulta("SELECT sc FROM estructura_comercial WHERE brocker = '$sc' AND sc <> '$sc' AND sc <> '' LIMIT 1");
        if($resScComoBrocker && mysqli_num_rows($resScComoBrocker) > 0){
            $fila = mysqli_fetch_assoc($resScComoBrocker);
            echo json_encode([
                'status'  => 'error',
                'message' => "\"$sc\" ya está asignado como Brocker del SC \"" . $fila['sc'] . "\", no puede usarse como SC"
            ]);
            exit;
        }
    }

    // 2) Una Gerencia que YA está asignada a un Brocker no puede
    //    reutilizarse como nombre de Brocker (si no tiene brocker, sí puede)
    if(!empty($brocker)){
        $resGerNombre = $conecta->consulta("SELECT id_gerencia FROM estruc_empleados WHERE gerencia = '$brocker' LIMIT 1");
        if($resGerNombre && mysqli_num_rows($resGerNombre) > 0){
            $filaGN = mysqli_fetch_assoc($resGerNombre);
            $idGN   = $filaGN['id_gerencia'];
            $resGerUsada = $conecta->consulta("SELECT sc, brocker FROM estructura_comercial WHERE id_gerencia = '$idGN' AND id_gerencia <> '0' LIMIT 1");
            if($resGerUsada && mysqli_num_rows($resGerUsada) > 0){
                $filaGU = mysqli_fetch_assoc($resGerUsada);
                echo json_encode([
                    'status'  => 'error',
                    'message' => "\"$brocker\" ya está asignada como Gerencia del brocker \"" . $filaGU['brocker'] . "\", no puede usarse como Brocker"
                ]);
                exit;
            }
        }
    }

    // =========================================================
    // BROCKER: un SC puede tener muchos brockers, y un brocker puede
    // tener muchas gerencias (hasta N filas), así que el brocker SÍ
    // se puede repetir. Lo único que no puede es "saltar" a otro SC
    // real distinto del que ya tiene, ni repetirse sin gerencia nueva.
    // =========================================================
    if(!empty($brocker)){
        $resBrockerSC = $conecta->consulta("SELECT sc FROM estructura_comercial WHERE brocker = '$brocker' AND sc <> '' AND sc <> '$sc' LIMIT 1");
        if($resBrockerSC && mysqli_num_rows($resBrockerSC) > 0){
            $filaBrockerSC = mysqli_fetch_assoc($resBrockerSC);
            echo json_encode([
                'status'  => 'error',
                'message' => "Ese brocker ya está asignado al SC \"" . $filaBrockerSC['sc'] . "\", no puede tener otro SC distinto"
            ]);
            exit;
        }

        // Evita duplicar la misma fila "sin gerencia" (sc+brocker repetido, ambos sin gerencia)
        if($idGerencia === '0'){
            $resDupSinGer = $conecta->consulta("SELECT 1 FROM estructura_comercial WHERE brocker = '$brocker' AND id_gerencia = 0 LIMIT 1");
            if($resDupSinGer && mysqli_num_rows($resDupSinGer) > 0){
                echo json_encode([
                    'status'  => 'error',
                    'message' => "Ese brocker ya tiene una fila sin gerencia asignada. Agrega una gerencia distinta."
                ]);
                exit;
            }
        }
    }

    // =========================================================
    // EXCLUSIVIDAD: una gerencia solo puede estar asignada una vez
    // en TODA la tabla estructura_comercial (el brocker sí se repite)
    // =========================================================
    if($idGerencia !== '0'){
        $resGerencia = $conecta->consulta("SELECT sc, brocker FROM estructura_comercial WHERE id_gerencia = '$idGerencia' LIMIT 1");
        if($resGerencia && mysqli_num_rows($resGerencia) > 0){
            $filaGer = mysqli_fetch_assoc($resGerencia);
            echo json_encode([
                'status'  => 'error',
                'message' => "Esa gerencia ya está asignada al brocker \"" . $filaGer['brocker'] . "\" (SC: " . $filaGer['sc'] . ")"
            ]);
            exit;
        }
    }

    // =========================
    // INSERTAR
    // =========================
    $okInsert = $conecta->consulta("INSERT INTO estructura_comercial (sc, brocker, id_gerencia) VALUES ('$sc','$brocker','$idGerencia')");

    if(!$okInsert){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al asignar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Estructura comercial asignada correctamente'
    ]);

    exit;
}

// =========================================================
// ELIMINAR SC COMPLETO: borra TODAS las filas de ese SC
// (libera todos sus Brockers y Gerencias para reasignar)
// =========================================================
if($action === 'eliminarSC'){

    $sc = mysqli_real_escape_string($conecta->conexion, trim($input['sc'] ?? ''));

    if(empty($sc)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Falta el SC a eliminar'
        ]);
        exit;
    }

    $okDelete = $conecta->consulta("DELETE FROM estructura_comercial WHERE sc = '$sc'");

    if(!$okDelete){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al eliminar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $afectadas = mysqli_affected_rows($conecta->conexion);

    echo json_encode([
        'status'  => 'ok',
        'message' => "Se eliminó el SC \"$sc\" y $afectadas fila(s) asociadas."
    ]);

    exit;
}

// =========================================================
// ELIMINAR BROCKER: borra la(s) fila(s) de ese Brocker
// (libera su Gerencia asignada, deja el SC intacto)
// =========================================================
if($action === 'eliminarBrocker'){

    $brocker = mysqli_real_escape_string($conecta->conexion, trim($input['brocker'] ?? ''));

    if(empty($brocker)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Falta el Brocker a eliminar'
        ]);
        exit;
    }

    $okDelete = $conecta->consulta("DELETE FROM estructura_comercial WHERE brocker = '$brocker'");

    if(!$okDelete){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al eliminar: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $afectadas = mysqli_affected_rows($conecta->conexion);

    echo json_encode([
        'status'  => 'ok',
        'message' => "Se eliminó el Brocker \"$brocker\" ($afectadas fila(s)). El SC no se modificó."
    ]);

    exit;
}

// =========================================================
// ELIMINAR (LIBERAR) GERENCIA: como una gerencia solo puede
// estar en una fila a la vez, es directo: solo se limpia
// id_gerencia de esa fila (deja SC y Brocker intactos). Si la
// fila queda totalmente vacía, se borra para no dejar basura.
// =========================================================
if($action === 'eliminarGerencia'){

    $idGerencia = mysqli_real_escape_string($conecta->conexion, trim($input['id_gerencia'] ?? ''));

    if(empty($idGerencia) || !preg_match('/^\d+$/', $idGerencia)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gerencia inválida'
        ]);
        exit;
    }

    $okUpdate = $conecta->consulta("UPDATE estructura_comercial SET id_gerencia = 0 WHERE id_gerencia = '$idGerencia'");

    if(!$okUpdate){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al liberar la gerencia: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    // Limpieza: si tras liberar la gerencia la fila quedó sin SC ni Brocker, se borra
    $conecta->consulta("DELETE FROM estructura_comercial WHERE id_gerencia = 0 AND (sc = '' OR sc IS NULL) AND (brocker = '' OR brocker IS NULL)");

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Se liberó la gerencia correctamente.'
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;