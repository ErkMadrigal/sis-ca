<?php
/* ════════════════════════════════════════════════════════════
   seg_reporteador_api.php

   Backend chiquito solo para el buscador con dropdown (Socio
   Comercial / SubBrocker / Comisionista / Gerencia / NAP) del
   Reporteador. Se construye aparte (no reutiliza el helper
   autocompletar() de otras vistas) para tener control total del
   formato que espera obtenerDatosReporte() en reporteador_funciones.php
   — sobre todo el caso de NAP, que se guarda como "Nombre (NAP)".
════════════════════════════════════════════════════════════ */

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

include('./funciones/clas_carobra.php');
require_once('./reporteador_funciones.php');
$conecta = new conector();

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

if ($action === 'generarReporte') {

    $nombre = $_SESSION['nombre'] ?? '';
    $nivel  = nivel_usuario(utf8_decode($nombre));

    $query = "Select clave from empl_usuarios where nombre='" . mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre)) . "'";
    $bd    = $conecta->consulta($query);
    $tb    = $bd ? mysqli_fetch_array($bd) : null;
    $clave = $tb['clave'] ?? '';

    $camposPedidos = filtrarCamposValidos($input['campos'] ?? [], $CAMPOS_REPORTE);
    if (empty($camposPedidos)) {
        $camposPedidos = $CAMPOS_DEFAULT;
    }

    $resultado = obtenerDatosReporte($conecta, [
        'filtro'  => $input['filtro']  ?? '',
        'valor1'  => $input['valor1']  ?? '',
        'valor2'  => $input['valor2']  ?? '',
        'valor3'  => $input['valor3']  ?? '',
        'te'      => $input['te']      ?? '',
        'sfp'     => $input['sfp']     ?? 0,
        'srm0'    => $input['srm0']    ?? 0,
        'reg_esp' => $input['reg_esp'] ?? 0,
        'nivel'   => $nivel,
        'clave'   => $clave,
    ], 1000);

    if (!$resultado['ok']) {
        echo json_encode(['status' => 'error', 'message' => $resultado['msg']]);
        exit;
    }

    $columnas = [];
    foreach ($camposPedidos as $key) {
        $columnas[] = ['key' => $key, 'label' => $CAMPOS_REPORTE[$key]['label']];
    }

    $data = [];
    foreach ($resultado['filas'] as $fila) {
        $row = [];
        foreach ($camposPedidos as $key) {
            $valor = $fila[$key] ?? '';
            if (in_array($key, $CAMPOS_MONEDA, true) && $valor !== '') {
                $valor = '$' . number_format(floatval($valor), 2);
            }
            $row[$key] = $valor;
        }
        $data[] = $row;
    }

    echo json_encode([
        'status'   => 'ok',
        'columnas' => $columnas,
        'data'     => $data,
        'limitado' => $resultado['limitado'],
    ]);
    exit;
}

if ($action === 'buscarFiltro') {

    $tipo    = $input['tipo']    ?? '';
    $termino = trim($input['termino'] ?? '');

    $resultado = ['status' => 'ok', 'data' => []];

    if (mb_strlen($termino) < 1) {
        echo json_encode($resultado);
        exit;
    }

    $like = '%' . mysqli_real_escape_string($conecta->conexion, $termino) . '%';

    if ($tipo === 'Soc. Comercial') {
        $sql = "SELECT DISTINCT sc AS etiqueta, sc AS valor
                FROM carobra_2014.estruc_empleados
                WHERE sc <> '' AND sc LIKE '$like'
                ORDER BY sc LIMIT 15";

    } elseif ($tipo === 'SubBrocker') {
        $sql = "SELECT DISTINCT brocker AS etiqueta, brocker AS valor
                FROM carobra_2014.estruc_empleados
                WHERE brocker <> '' AND brocker LIKE '$like'
                ORDER BY brocker LIMIT 15";

    } elseif ($tipo === 'Gerencia') {
        $sql = "SELECT DISTINCT gerencia AS etiqueta, gerencia AS valor
                FROM carobra_2014.estruc_empleados
                WHERE gerencia <> '' AND gerencia LIKE '$like'
                ORDER BY gerencia LIMIT 15";

    } elseif ($tipo === 'Comisionista') {
        $sql = "SELECT DISTINCT nombre AS etiqueta, nombre AS valor
                FROM empl_usuarios
                WHERE nombre <> '' AND nombre LIKE '$like'
                ORDER BY nombre LIMIT 15";

    } elseif ($tipo === 'NAP') {
        $sql = "SELECT CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS etiqueta,
                       CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS valor
                FROM carobra_2014.naps n
                LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
                WHERE n.nap LIKE '$like' OR eu.nombre LIKE '$like'
                ORDER BY n.nap LIMIT 15";

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de búsqueda no soportado']);
        exit;
    }

    $bd = $conecta->consulta($sql);
    if ($bd) {
        while ($row = mysqli_fetch_assoc($bd)) {
            $resultado['data'][] = ['etiqueta' => $row['etiqueta'], 'valor' => $row['valor']];
        }
    } else {
        $resultado['status'] = 'error';
        $resultado['mysql']  = mysqli_error($conecta->conexion);
    }

    echo json_encode($resultado);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;