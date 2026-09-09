<?php
session_start();
header('Content-Type: application/json');

ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();
$raw     = file_get_contents('php://input');
$input   = json_decode($raw, true);
$action  = $input['action'] ?? '';

/* ── Lista de expedientes ── */
if ($action === 'listarExpedientes') {
    $q      = trim($input['q']      ?? '');
    $filtro = trim($input['filtro'] ?? 'TODOS');

    $where = ["1=1"];
    if ($filtro === 'completo')   $where[] = "e.estatus = 'completo'";
    if ($filtro === 'incompleto') $where[] = "e.estatus = 'incompleto'";

    if (strlen($q) >= 2) {
        $qEsc    = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));
        $where[] = "(e.nombre LIKE '%{$qEsc}%' OR e.email LIKE '%{$qEsc}%')";
    }

    $whereSql = implode(' AND ', $where);

    $rs = $conecta->consulta("
        SELECT e.id, e.nombre, e.tipo_persona, e.email, e.celular,
               e.factura, e.rfc, e.estatus, e.creado_en, e.clave_creador,
               COUNT(ed.id) AS total_docs,
               SUM(CASE WHEN ed.estatus = 'aprobado'  THEN 1 ELSE 0 END) AS docs_aprobados,
               SUM(CASE WHEN ed.estatus = 'rechazado' THEN 1 ELSE 0 END) AS docs_rechazados,
               SUM(CASE WHEN ed.estatus = 'pendiente' THEN 1 ELSE 0 END) AS docs_pendientes
        FROM carobra_2014.expedientes e
        LEFT JOIN carobra_2014.expediente_docs ed ON ed.expediente_id = e.id
        WHERE {$whereSql}
        GROUP BY e.id
        ORDER BY e.creado_en DESC
        LIMIT 100
    ");

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'             => $row['id'],
                'nombre'         => utf8_encode($row['nombre']),
                'tipo_persona'   => $row['tipo_persona'],
                'email'          => $row['email'],
                'celular'        => $row['celular'],
                'factura'        => $row['factura'],
                'rfc'            => $row['rfc'],
                'estatus'        => $row['estatus'],
                'creado_en'      => $row['creado_en'],
                'clave_creador'  => $row['clave_creador'],
                'total_docs'     => intval($row['total_docs']),
                'docs_aprobados' => intval($row['docs_aprobados']),
                'docs_rechazados'=> intval($row['docs_rechazados']),
                'docs_pendientes'=> intval($row['docs_pendientes']),
            ];
        }
    }
    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ── Detalle de expediente ── */
if ($action === 'detalleExpediente') {
    $id = intval($input['id'] ?? 0);

    $rsExp = $conecta->consulta("
        SELECT * FROM carobra_2014.expedientes WHERE id = {$id} LIMIT 1
    ");
    $exp = $rsExp ? mysqli_fetch_assoc($rsExp) : null;

    if (!$exp) {
        echo json_encode(['ok' => false, 'msg' => 'Expediente no encontrado']);
        exit;
    }

    $rsDocs = $conecta->consulta("
        SELECT * FROM carobra_2014.expediente_docs WHERE expediente_id = {$id}
        ORDER BY creado_en ASC
    ");

    $docs = [];
    if ($rsDocs) {
        while ($row = mysqli_fetch_assoc($rsDocs)) {
            $docs[] = [
                'id'          => $row['id'],
                'tipo_doc'    => $row['tipo_doc'],
                'nombre'      => $row['nombre'],
                'url'         => 'https://www.sis-ca.com.mx:7863/afore/expedientes/' . $row['ruta'],
                'tamanio'     => $row['tamanio'],
                'estatus'     => $row['estatus'],
                'comentario'  => utf8_encode($row['comentario'] ?? ''),
                'creado_en'   => $row['creado_en'],
            ];
        }
    }

    echo json_encode([
        'ok'   => true,
        'exp'  => [
            'id'           => $exp['id'],
            'nombre'       => utf8_encode($exp['nombre']),
            'tipo_persona' => $exp['tipo_persona'],
            'email'        => $exp['email'],
            'celular'      => $exp['celular'],
            'factura'      => $exp['factura'],
            'rfc'          => $exp['rfc'],
            'estatus'      => $exp['estatus'],
            'creado_en'    => $exp['creado_en'],
            'clave_creador'=> $exp['clave_creador'],
        ],
        'docs' => $docs,
    ]);
    exit;
}

/* ── Cambiar estatus de documento ── */
if ($action === 'revisarDoc') {
    $doc_id     = intval($input['doc_id']     ?? 0);
    $estatus    = trim($input['estatus']      ?? '');
    $comentario = trim($input['comentario']   ?? '');

    $permitidos = ['aprobado', 'rechazado', 'pendiente'];
    if (!$doc_id || !in_array($estatus, $permitidos)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
        exit;
    }

    $comentarioEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($comentario));
    $estatusEsc    = mysqli_real_escape_string($conecta->conexion, $estatus);

    $ok = $conecta->consulta("
        UPDATE carobra_2014.expediente_docs
        SET estatus = '{$estatusEsc}',
            comentario = '{$comentarioEsc}',
            revisado_en = NOW()
        WHERE id = {$doc_id}
    ");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar']);
        exit;
    }

    // Actualizar estatus general del expediente
    $expRow = $conecta->consulta("
        SELECT expediente_id FROM carobra_2014.expediente_docs WHERE id = {$doc_id} LIMIT 1
    ");
    $expData = $expRow ? mysqli_fetch_assoc($expRow) : null;

    if ($expData) {
        $expId = intval($expData['expediente_id']);
        $rsStats = $conecta->consulta("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estatus = 'aprobado' THEN 1 ELSE 0 END) AS aprobados
            FROM carobra_2014.expediente_docs
            WHERE expediente_id = {$expId}
        ");
        $stats = $rsStats ? mysqli_fetch_assoc($rsStats) : null;
        if ($stats && $stats['total'] > 0 && $stats['total'] == $stats['aprobados']) {
            $conecta->consulta("UPDATE carobra_2014.expedientes SET estatus = 'completo' WHERE id = {$expId}");
        } else {
            $conecta->consulta("UPDATE carobra_2014.expedientes SET estatus = 'incompleto' WHERE id = {$expId}");
        }
    }

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'asignarClaveSisca') {
    $id          = intval($input['id']          ?? 0);
    $clave_sisca = trim($input['clave_sisca']   ?? '');

    if (!$id || !$clave_sisca) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave_sisca);
    $ok = $conecta->consulta("
        UPDATE carobra_2014.expedientes SET clave_sisca = '{$claveEsc}' WHERE id = {$id}
    ");

    echo json_encode(['ok' => $ok ? true : false]);
    exit;
}


if ($action === 'expedientePorEmpleado') {
    $no_empleado = intval($input['no_empleado'] ?? 0);
    $clave       = trim($input['clave']         ?? '');

    if (!$no_empleado && !$clave) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
        exit;
    }

    // Buscar por clave_sisca
    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $rs = $conecta->consulta("
        SELECT e.*, COUNT(ed.id) AS total_docs,
               SUM(CASE WHEN ed.estatus = 'aprobado'  THEN 1 ELSE 0 END) AS docs_aprobados,
               SUM(CASE WHEN ed.estatus = 'rechazado' THEN 1 ELSE 0 END) AS docs_rechazados,
               SUM(CASE WHEN ed.estatus = 'pendiente' THEN 1 ELSE 0 END) AS docs_pendientes
        FROM carobra_2014.expedientes e
        LEFT JOIN carobra_2014.expediente_docs ed ON ed.expediente_id = e.id
        WHERE e.clave_sisca = '{$claveEsc}'
        GROUP BY e.id
        ORDER BY e.creado_en DESC
        LIMIT 1
    ");

    $exp = $rs ? mysqli_fetch_assoc($rs) : null;

    if (!$exp) {
        echo json_encode(['ok' => true, 'exp' => null, 'docs' => []]);
        exit;
    }

    $rsDocs = $conecta->consulta("
        SELECT * FROM carobra_2014.expediente_docs
        WHERE expediente_id = {$exp['id']}
        ORDER BY creado_en ASC
    ");

    $docs = [];
    if ($rsDocs) {
        while ($row = mysqli_fetch_assoc($rsDocs)) {
            $docs[] = [
                'id'         => $row['id'],
                'tipo_doc'   => $row['tipo_doc'],
                'nombre'     => $row['nombre'],
                'url'        => 'https://www.sis-ca.com.mx:7863/afore/expedientes/' . $row['ruta'],
                'estatus'    => $row['estatus'],
                'comentario' => utf8_encode($row['comentario'] ?? ''),
                'creado_en'  => $row['creado_en'],
            ];
        }
    }

    echo json_encode([
        'ok'  => true,
        'exp' => [
            'id'             => $exp['id'],
            'nombre'         => utf8_encode($exp['nombre']),
            'tipo_persona'   => $exp['tipo_persona'],
            'estatus'        => $exp['estatus'],
            'total_docs'     => intval($exp['total_docs']),
            'docs_aprobados' => intval($exp['docs_aprobados']),
            'docs_rechazados'=> intval($exp['docs_rechazados']),
            'docs_pendientes'=> intval($exp['docs_pendientes']),
            'clave_sisca'    => $exp['clave_sisca'],
        ],
        'docs' => $docs,
    ]);
    exit;
}


/* ── Generar token QR ── */
if ($action === 'generarToken') {
    $id = intval($input['id'] ?? 0);
    if (!$id) { echo json_encode(['ok' => false, 'msg' => 'ID requerido']); exit; }

    $token  = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+72 hours'));

    $ok = $conecta->consulta("
        UPDATE carobra_2014.expedientes
        SET token_acceso = '{$token}', token_expira = '{$expira}', token_usado = 0
        WHERE id = {$id}
    ");

    echo json_encode([
        'ok'     => $ok ? true : false,
        'token'  => $token,
        'expira' => $expira,
        'url' => 'https://sis-ca.com.mx:7863/captura/#/expediente-publico/' . $token,
    ]);
    exit;
}

/* ── Editar datos expediente ── */
if ($action === 'editarExpediente') {
    $id      = intval($input['id']      ?? 0);
    $nombre  = trim($input['nombre']   ?? '');
    $email   = trim($input['email']    ?? '');
    $celular = trim($input['celular']  ?? '');
    $rfc     = trim($input['rfc']      ?? '');
    $factura = intval($input['factura'] ?? 0);

    if (!$id || !$nombre) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
        exit;
    }

    $nombreEsc  = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
    $emailEsc   = mysqli_real_escape_string($conecta->conexion, $email);
    $celularEsc = mysqli_real_escape_string($conecta->conexion, $celular);
    $rfcEsc     = mysqli_real_escape_string($conecta->conexion, $rfc);

    $ok = $conecta->consulta("
        UPDATE carobra_2014.expedientes
        SET nombre = '{$nombreEsc}', email = '{$emailEsc}',
            celular = '{$celularEsc}', rfc = '{$rfcEsc}', factura = {$factura}
        WHERE id = {$id}
    ");

    echo json_encode(['ok' => $ok ? true : false]);
    exit;
}

if ($action === 'crearExpediente') {
    $tipo    = trim($input['tipo_persona'] ?? '');
    $nombre  = trim($input['nombre']       ?? '');
    $email   = trim($input['email']        ?? '');
    $celular = trim($input['celular']      ?? '');
    $factura = intval($input['factura']    ?? 0);

    if (!$tipo || !$nombre) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
        exit;
    }

    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    $nombreEsc  = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
    $emailEsc   = mysqli_real_escape_string($conecta->conexion, $email);
    $celularEsc = mysqli_real_escape_string($conecta->conexion, $celular);
    $tipoEsc    = mysqli_real_escape_string($conecta->conexion, $tipo);
    $uuidEsc    = mysqli_real_escape_string($conecta->conexion, $uuid);
    $creador    = $_SESSION['clave'] ?? 'SISCA';

    $ok = $conecta->consulta("
        INSERT INTO carobra_2014.expedientes
            (uuid, tipo_persona, nombre, email, celular, factura, creado_por, clave_creador)
        VALUES
            ('{$uuidEsc}', '{$tipoEsc}', '{$nombreEsc}', '{$emailEsc}',
             '{$celularEsc}', {$factura}, 0, '{$creador}')
    ");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al crear expediente']);
        exit;
    }

    $id = mysqli_insert_id($conecta->conexion);
    echo json_encode(['ok' => true, 'id' => $id]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida']);