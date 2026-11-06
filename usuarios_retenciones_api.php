<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require './vendor/autoload.php';
include('./funciones/encripta.php');

$conecta = new conector();

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

$usuarioActual = $_SESSION['nombre'] ?? '';

/* ════════════════════════════════════════════════════════════
   ACTION: listarRetenciones
   Trae a todos los asignados a retenciones, con su estructura
   (gerencia/brocker/sc) resuelta en vivo contra estruc_empleados.
════════════════════════════════════════════════════════════ */
if ($action === 'listarRetenciones') {

    $sql = "SELECT
                ru.id, ru.clave, ru.activo, ru.fecha_alta, ru.usuario_alta,
                ru.fecha_baja, ru.usuario_baja,
                e.nombre, e.no_empleado, e.activo as empleado_activo,
                ee.sc, ee.brocker, ee.gerencia, ee.id_gerencia
            FROM carobra_2014.retenciones_usuarios ru
            LEFT JOIN carobra_2014.empl_usuarios   e  ON e.clave  = ru.clave
            LEFT JOIN carobra_2014.estruc_empleados ee ON ee.clave = ru.clave
            ORDER BY ru.activo DESC, e.nombre ASC";

    $res   = $conecta->consulta($sql);
    $lista = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $lista[] = [
                'id'           => $row['id'],
                'clave'        => $row['clave'],
                'nombre'       => $row['nombre'] ?? '(empleado no encontrado)',
                'activo'       => intval($row['activo']) === 1,
                'fecha_alta'   => $row['fecha_alta'],
                'usuario_alta' => $row['usuario_alta'],
                'fecha_baja'   => $row['fecha_baja'],
                'usuario_baja' => $row['usuario_baja'],
                'no_empleado'  => $row['no_empleado'],
                'sc'           => trim($row['sc']       ?? ''),
                'brocker'      => trim($row['brocker']  ?? ''),
                'gerencia'     => trim($row['gerencia'] ?? ''),
                'id_gerencia'  => $row['id_gerencia'],
                'sin_estructura' => empty($row['sc']) && empty($row['brocker']) && empty($row['gerencia']),
            ];
        }
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarEmpleado
   Typeahead para encontrar a quién asignar -- excluye a quien
   ya esté dado de alta en retenciones_usuarios, y de una vez
   muestra su estructura (gerencia/brocker/sc) para confirmar
   antes de agregarlo.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarEmpleado') {

    $q = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';

    $sql = "SELECT e.clave, e.nombre, e.no_empleado,
                   ee.sc, ee.brocker, ee.gerencia
            FROM carobra_2014.empl_usuarios e
            LEFT JOIN carobra_2014.estruc_empleados ee ON ee.clave = e.clave
            WHERE (e.nombre LIKE '$q' OR e.clave LIKE '$q')
              AND e.activo = 1
              AND e.clave NOT IN (SELECT clave FROM carobra_2014.retenciones_usuarios)
            ORDER BY e.nombre ASC
            LIMIT 15";

    $res   = $conecta->consulta($sql);
    $lista = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $lista[] = [
                'clave'       => $row['clave'],
                'nombre'      => $row['nombre'],
                'no_empleado' => $row['no_empleado'],
                'sc'          => trim($row['sc']       ?? ''),
                'brocker'     => trim($row['brocker']  ?? ''),
                'gerencia'    => trim($row['gerencia'] ?? ''),
                'sin_estructura' => empty($row['sc']) && empty($row['brocker']) && empty($row['gerencia']),
            ];
        }
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: asignarRetencion
════════════════════════════════════════════════════════════ */
if ($action === 'asignarRetencion') {

    $clave = mysqli_real_escape_string($conecta->conexion, trim($input['clave'] ?? ''));

    if (empty($clave)) {
        echo json_encode(['status' => 'error', 'msg' => 'Clave requerida']);
        exit;
    }

    /* Verificar que el empleado exista y esté activo */
    $sqlEmp = "SELECT clave, nombre FROM carobra_2014.empl_usuarios WHERE clave = '$clave' AND activo = 1 LIMIT 1";
    $resEmp = $conecta->consulta($sqlEmp);
    if (!$resEmp || mysqli_num_rows($resEmp) === 0) {
        echo json_encode(['status' => 'error', 'msg' => 'No se encontró un empleado activo con esa clave']);
        exit;
    }
    $emp = mysqli_fetch_assoc($resEmp);

    /* Verificar que no esté ya asignado */
    $sqlDup = "SELECT id FROM carobra_2014.retenciones_usuarios WHERE clave = '$clave' LIMIT 1";
    $resDup = $conecta->consulta($sqlDup);
    if ($resDup && mysqli_num_rows($resDup) > 0) {
        echo json_encode(['status' => 'error', 'msg' => $emp['nombre'] . ' ya está asignado a retenciones']);
        exit;
    }

    $usuarioEsc = mysqli_real_escape_string($conecta->conexion, $usuarioActual);

    $sql = "INSERT INTO carobra_2014.retenciones_usuarios (clave, activo, fecha_alta, usuario_alta)
            VALUES ('$clave', 1, NOW(), '$usuarioEsc')";

    $res = $conecta->consulta($sql);

    if ($res) {
        echo json_encode(['status' => 'ok', 'msg' => $emp['nombre'] . ' agregado a retenciones']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
    }
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: toggleActivo
   Activa / desactiva a un retenedor sin borrar su historial.
════════════════════════════════════════════════════════════ */
if ($action === 'toggleActivo') {

    $id     = intval($input['id'] ?? 0);
    $activo = intval($input['activo'] ?? 0) === 1 ? 1 : 0;

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
        exit;
    }

    $usuarioEsc = mysqli_real_escape_string($conecta->conexion, $usuarioActual);

    if ($activo === 1) {
        $sql = "UPDATE carobra_2014.retenciones_usuarios
                SET activo = 1, fecha_baja = NULL, usuario_baja = NULL
                WHERE id = $id";
    } else {
        $sql = "UPDATE carobra_2014.retenciones_usuarios
                SET activo = 0, fecha_baja = NOW(), usuario_baja = '$usuarioEsc'
                WHERE id = $id";
    }

    $res = $conecta->consulta($sql);

    if ($res) {
        echo json_encode(['status' => 'ok', 'msg' => $activo === 1 ? 'Reactivado' : 'Desactivado']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
    }
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: eliminarRetencion
   Borra por completo el registro (para altas por error).
════════════════════════════════════════════════════════════ */
if ($action === 'eliminarRetencion') {

    $id = intval($input['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
        exit;
    }

    $sql = "DELETE FROM carobra_2014.retenciones_usuarios WHERE id = $id";
    $res = $conecta->consulta($sql);

    if ($res) {
        echo json_encode(['status' => 'ok', 'msg' => 'Eliminado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error al eliminar: ' . mysqli_error($conecta->conexion)]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'msg' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;