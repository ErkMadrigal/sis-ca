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

if($action === 'getFacturasNap'){

    $anio        = mysqli_real_escape_string($conecta->conexion, $input['anio']         ?? '');
    $periodo     = mysqli_real_escape_string($conecta->conexion, $input['periodo']      ?? '');
    $claveNombre = mysqli_real_escape_string($conecta->conexion, trim($input['clave_nombre'] ?? ''));
    $estatus     = mysqli_real_escape_string($conecta->conexion, $input['estatus']      ?? '');
    $nivel       = mysqli_real_escape_string($conecta->conexion, $input['nivel']        ?? '');

    // =========================
    // VALIDAR FILTROS
    // =========================
    if($anio !== '' && !preg_match('/^\d{4}$/', $anio)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Año inválido'
        ]);
        exit;
    }

    if($estatus !== '' && !preg_match('/^[0-4]$/', $estatus)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Estatus inválido'
        ]);
        exit;
    }

    $where = [];

    // =========================
    // FILTROS (todos opcionales, busqueda en tiempo real)
    // =========================
    if($anio !== '')
        $where[] = "fn.anio = '$anio'";

    if($periodo !== '')
        $where[] = "fn.periodo = '$periodo'";

    if($claveNombre !== '')
        $where[] = "(fn.clave LIKE '%$claveNombre%' OR e.nombre LIKE '%$claveNombre%')";

    if($estatus !== '')
        $where[] = "fn.estatus_fac = '$estatus'";

    $whereSQL = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    // =========================================================
    // estatus_descripcion (Activo/Inactivo/Inhabilitado):
    // se resuelve via estruc_empleados.no_empleado -> naps.no_empleado,
    // tomando un solo registro de naps por no_empleado (el activo si
    // existe, si no el más reciente) para no duplicar filas de facturas.
    // =========================================================
    $sql = "SELECT fn.clave, e.nombre, ee.sc, ee.brocker, ee.gerencia,
                   fn.anio, fn.periodo, fn.reg_fiscal, fn.bono, fn.iva, fn.ret_iva,
                   fn.monto, fn.estatus_fac, fn.no_factura, fn.nom_pdf,
                   CASE WHEN IFNULL(em.ase_bernal, 0) = 0 THEN 'No' ELSE 'Sí' END AS ase_bernal,
                   CASE
                        WHEN nn.baja = 1 THEN 'Inactivo'
                        WHEN nn.baja = 0 AND nn.estatus = 0 THEN 'Inhabilitado'
                        ELSE 'Activo'
                   END AS estatus_descripcion
            FROM facturas_nap fn
                JOIN empl_usuarios e ON fn.clave = e.clave
                JOIN carobra.empleados em ON fn.clave = em.clave
                JOIN estruc_empleados ee ON fn.clave = ee.clave
                LEFT JOIN (
                    SELECT n1.no_empleado, n1.nap, n1.baja, enc.estatus
                    FROM naps n1
                    LEFT JOIN estatus_naps_cumpl enc ON n1.nap = enc.nap
                    WHERE n1.nap = (
                        SELECT n2.nap FROM naps n2
                        WHERE n2.no_empleado = n1.no_empleado
                        ORDER BY n2.baja ASC, n2.nap DESC
                        LIMIT 1
                    )
                ) nn ON nn.no_empleado = ee.no_empleado
            $whereSQL
            ORDER BY fn.anio DESC, fn.periodo DESC, e.nombre ASC";

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

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;