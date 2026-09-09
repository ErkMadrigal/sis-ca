<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();
require_once __DIR__ . '/../carobra-api/send_push.php';
$conecta = new conector();

/**
 * NOTA: 'crearNotificacion' recibe multipart/form-data (por el adjunto),
 * NO json -- por eso lee de $_POST directo, a diferencia de las demás
 * acciones que sí leen el body JSON como en validacion_api.php.
 */
$esMultipart = isset($_POST['action']) && $_POST['action'] === 'crearNotificacion';

if (!$esMultipart) {
    $raw    = file_get_contents('php://input');
    $input  = json_decode($raw, true);
    $action = $input['action'] ?? '';
} else {
    $action = $_POST['action'];
}

/* ════════════════════════════════════════════════════════════
   ACTION: crearNotificacion (multipart -- soporta adjunto)
════════════════════════════════════════════════════════════ */
if ($action === 'crearNotificacion') {

    $titulo  = trim($_POST['titulo']  ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $tipo    = trim($_POST['tipo_destinatario'] ?? '');
    $claveDestino = trim($_POST['clave_destino'] ?? '');
    $valorEstructura   = trim($_POST['valor_estructura'] ?? ''); // el sc/broker/gerencia elegido
    $fechaProgramada   = trim($_POST['fecha_programada'] ?? ''); // 'Y-m-d H:i:s', vacío = ahora
    $esEnvioInmediato  = ($fechaProgramada === '');

    $tiposValidos = ['todos', 'socio', 'gerente', 'broker', 'personal'];

    if ($titulo === '' || $mensaje === '' || !in_array($tipo, $tiposValidos, true)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o inválidos.']);
        exit;
    }
    if ($tipo === 'personal' && empty($claveDestino)) {
        echo json_encode(['ok' => false, 'msg' => 'Selecciona un usuario para envío personal.']);
        exit;
    }
    if (in_array($tipo, ['socio', 'gerente', 'broker'], true) && $valorEstructura === '') {
        echo json_encode(['ok' => false, 'msg' => 'Selecciona el socio/gerente/broker específico.']);
        exit;
    }

    $fechaProgramada = $fechaProgramada ?: date('Y-m-d H:i:s');

    // ── Adjunto (opcional) ──────────────────────────────────────────
    $adjuntoRuta = null;
    $adjuntoNombre = null;

    if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
        $dirDestino = __DIR__ . '/uploads/notificaciones/';
        if (!is_dir($dirDestino)) mkdir($dirDestino, 0755, true);

        $extPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['adjunto']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extPermitidas, true)) {
            echo json_encode(['ok' => false, 'msg' => 'Tipo de archivo no permitido. Usa PDF, JPG o PNG.']);
            exit;
        }

        $nombreUnico = uniqid('notif_') . '.' . $ext;
        $rutaDestino = $dirDestino . $nombreUnico;

        if (move_uploaded_file($_FILES['adjunto']['tmp_name'], $rutaDestino)) {
            $adjuntoRuta   = 'uploads/notificaciones/' . $nombreUnico;
            $adjuntoNombre = $_FILES['adjunto']['name'];
        }
    }

    $tituloEsc  = mysqli_real_escape_string($conecta->conexion, $titulo);
    $mensajeEsc = mysqli_real_escape_string($conecta->conexion, $mensaje);
    $claveUsuario = $_SESSION['clave'] ?? ($_SESSION['nombre'] ?? '');
    $claveUsuarioEsc = mysqli_real_escape_string($conecta->conexion, $claveUsuario);

    $claveDestinoSql = ($tipo === 'personal') ? "'" . mysqli_real_escape_string($conecta->conexion, $claveDestino) . "'" : 'NULL';
    $adjuntoRutaSql  = $adjuntoRuta   ? "'" . mysqli_real_escape_string($conecta->conexion, $adjuntoRuta)   . "'" : 'NULL';
    $adjuntoNombreSql = $adjuntoNombre ? "'" . mysqli_real_escape_string($conecta->conexion, $adjuntoNombre) . "'" : 'NULL';

    $valorEstructuraSql = $valorEstructura !== '' ? "'" . mysqli_real_escape_string($conecta->conexion, $valorEstructura) . "'" : 'NULL';

    $conecta->consulta(
        "INSERT INTO carobra_2014.notificaciones
            (titulo, mensaje, tipo_destinatario, clave_destino, valor_estructura, adjunto_ruta, adjunto_nombre, fecha_programada, estatus, creado_por, creado_en)
         VALUES
            ('{$tituloEsc}', '{$mensajeEsc}', '{$tipo}', {$claveDestinoSql}, {$valorEstructuraSql}, {$adjuntoRutaSql}, {$adjuntoNombreSql}, '{$fechaProgramada}', 'programada', '{$claveUsuarioEsc}', NOW())"
    );

    $idNotificacion = mysqli_insert_id($conecta->conexion);

    // ── Resolver destinatarios AHORA (aunque el envío sea futuro) ────
    $destinatarios = resolverDestinatarios($conecta, $tipo, $tipo === 'personal' ? $claveDestino : null, $valorEstructura);

    foreach ($destinatarios as $d) {
        $tokenSql = $d['token'] ? "'" . mysqli_real_escape_string($conecta->conexion, $d['token']) . "'" : 'NULL';
        $estatusIni = $d['token'] ? 'pendiente' : 'sin_token';
        $conecta->consulta(
            "INSERT INTO carobra_2014.notificaciones_detalle (id_notificacion, no_empleado, token, estatus)
             VALUES ({$idNotificacion}, {$d['no_empleado']}, {$tokenSql}, '{$estatusIni}')"
        );
    }

    $conecta->consulta(
        "UPDATE carobra_2014.notificaciones SET total_destinatarios = " . count($destinatarios) . " WHERE id = {$idNotificacion}"
    );

    // Si es "enviar ahora", procesa YA -- sin esperar al cron
    $enviados = 0;
    $fallidos = 0;
    if ($esEnvioInmediato) {
        list($enviados, $fallidos) = procesarEnvioNotificacion($conecta, $idNotificacion);
    }

    echo json_encode([
        'ok' => true,
        'msg' => $esEnvioInmediato
            ? "Notificación enviada -- {$enviados} enviados, {$fallidos} fallidos."
            : 'Notificación programada correctamente.',
        'id_notificacion' => $idNotificacion,
        'total_destinatarios' => count($destinatarios),
        'total_enviados' => $enviados,
        'total_fallidos' => $fallidos,
    ]);
    exit;
}

/**
 * Procesa el envío real de una notificación -- usada tanto por
 * "enviar ahora" (aquí mismo) como por el cron, que hace exactamente
 * lo mismo pero para las programadas. Regresa [enviados, fallidos].
 */
function procesarEnvioNotificacion($conecta, $idNotificacion) {
    $idNotificacion = intval($idNotificacion);

    $notif = $conecta->consulta("SELECT * FROM carobra_2014.notificaciones WHERE id = {$idNotificacion}");
    $notif = $notif ? mysqli_fetch_assoc($notif) : null;
    if (!$notif) return [0, 0];

    $conecta->consulta("UPDATE carobra_2014.notificaciones SET estatus = 'enviando' WHERE id = {$idNotificacion}");

    $rsDetalle = $conecta->consulta(
        "SELECT * FROM carobra_2014.notificaciones_detalle WHERE id_notificacion = {$idNotificacion} AND estatus = 'pendiente'"
    );

    $enviados = 0;
    $fallidos = 0;

    if ($rsDetalle) {
        while ($d = mysqli_fetch_assoc($rsDetalle)) {
            $idDetalle = intval($d['id']);

            if (empty($d['token'])) {
                $conecta->consulta("UPDATE carobra_2014.notificaciones_detalle SET estatus = 'sin_token' WHERE id = {$idDetalle}");
                continue;
            }

            $datosExtra = ['id_notificacion' => $idNotificacion];
            if (!empty($notif['adjunto_ruta'])) {
                $datosExtra['adjunto'] = 'https://www.sis-ca.com.mx:7863/afore/' . $notif['adjunto_ruta'];
            }

            try {
                $resultado = enviar_push($d['token'], $notif['titulo'], $notif['mensaje'], $datosExtra);
                $respEsc = mysqli_real_escape_string($conecta->conexion, json_encode($resultado));
                $conecta->consulta(
                    "UPDATE carobra_2014.notificaciones_detalle
                     SET estatus = 'enviado', respuesta = '{$respEsc}', enviado_en = NOW()
                     WHERE id = {$idDetalle}"
                );
                $enviados++;
            } catch (\Throwable $e) {
                $errEsc = mysqli_real_escape_string($conecta->conexion, $e->getMessage());
                $conecta->consulta(
                    "UPDATE carobra_2014.notificaciones_detalle
                     SET estatus = 'fallido', respuesta = '{$errEsc}'
                     WHERE id = {$idDetalle}"
                );
                $fallidos++;
            }
        }
    }

    $conecta->consulta(
        "UPDATE carobra_2014.notificaciones
         SET enviado = 1, enviado_en = NOW(), total_enviados = {$enviados}, total_fallidos = {$fallidos}, estatus = 'completada'
         WHERE id = {$idNotificacion}"
    );

    return [$enviados, $fallidos];
}

/**
 * Resuelve la lista de destinatarios (no_empleado + token) según el tipo.
 * 👈 AJUSTA la columna real donde distingues socio/gerente/broker --
 * estoy asumiendo empl_usuarios.tipo con esos 3 valores.
 */
function resolverDestinatarios($conecta, $tipo, $claveDestino = null, $valorEstructura = null) {
    $lista = [];

    if ($tipo === 'personal') {
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $claveDestino);
        $rs = $conecta->consulta(
            "SELECT no_empleado, token FROM carobra_2014.push_tokens WHERE clave = '{$claveEsc}' LIMIT 1"
        );
        if ($rs && $row = mysqli_fetch_assoc($rs)) {
            $lista[] = ['no_empleado' => intval($row['no_empleado']), 'token' => $row['token']];
        }
        return $lista;
    }

    if ($tipo === 'todos') {
        // Todo el universo con token registrado -- directo de push_tokens,
        // sin depender de empl_usuarios (evita problemas de columna/join)
        $rs = $conecta->consulta(
            "SELECT no_empleado, token FROM carobra_2014.push_tokens WHERE token IS NOT NULL AND token != ''"
        );
        if ($rs) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $lista[] = ['no_empleado' => intval($row['no_empleado']), 'token' => $row['token']];
            }
        }
        return $lista;
    }

    // socio | gerente | broker -- filtra por estruc_empleados, trae TODA la
    // estructura de ese socio/gerente/broker específico (columna 'clave')
    $columnaEstructura = ['socio' => 'sc', 'gerente' => 'gerencia', 'broker' => 'brocker'][$tipo] ?? null;
    if (!$columnaEstructura || $valorEstructura === null || $valorEstructura === '') {
        return $lista;
    }

    $valorEsc = mysqli_real_escape_string($conecta->conexion, $valorEstructura);

    // Trae las claves de TODA la estructura que cae bajo ese valor
    $rsClaves = $conecta->consulta(
        "SELECT DISTINCT clave FROM carobra_2014.estruc_empleados WHERE {$columnaEstructura} = '{$valorEsc}'"
    );

    $claves = [];
    if ($rsClaves) {
        while ($row = mysqli_fetch_assoc($rsClaves)) {
            $claves[] = mysqli_real_escape_string($conecta->conexion, $row['clave']);
        }
    }
    if (empty($claves)) return $lista;

    // ✅ push_tokens.clave (varchar) es el campo real de cruce con
    // estruc_empleados.clave -- NO no_empleado.
    $clavesInSql = "'" . implode("','", $claves) . "'";
    $rs = $conecta->consulta(
        "SELECT no_empleado, clave, token
         FROM carobra_2014.push_tokens
         WHERE clave IN ({$clavesInSql})"
    );
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $lista[] = ['no_empleado' => intval($row['no_empleado']), 'token' => $row['token']];
        }
    }
    return $lista;
}

/* ════════════════════════════════════════════════════════════
   ACTION: listarNotificaciones
════════════════════════════════════════════════════════════ */
if ($action === 'listarNotificaciones') {

    $estatus = trim($input['estatus'] ?? '');
    $where = "1=1";
    if ($estatus !== '') {
        $estatusEsc = mysqli_real_escape_string($conecta->conexion, $estatus);
        $where .= " AND estatus = '{$estatusEsc}'";
    }

    $rs = $conecta->consulta(
        "SELECT * FROM carobra_2014.notificaciones WHERE {$where} ORDER BY fecha_programada DESC LIMIT 100"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = $row;
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: cancelarNotificacion
════════════════════════════════════════════════════════════ */
if ($action === 'cancelarNotificacion') {

    $id = intval($input['id'] ?? 0);
    if (!$id) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit;
    }

    $rs = $conecta->consulta("SELECT enviado FROM carobra_2014.notificaciones WHERE id = {$id}");
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    if (!$row) {
        echo json_encode(['ok' => false, 'msg' => 'No encontrada.']);
        exit;
    }
    if ($row['enviado'] == 1) {
        echo json_encode(['ok' => false, 'msg' => 'Ya fue enviada, no se puede cancelar.']);
        exit;
    }

    $conecta->consulta("UPDATE carobra_2014.notificaciones SET estatus = 'cancelada' WHERE id = {$id}");
    echo json_encode(['ok' => true, 'msg' => 'Notificación cancelada.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarUsuariosPersonal (typeahead)
════════════════════════════════════════════════════════════ */
if ($action === 'buscarUsuariosPersonal') {

    $q = trim($input['q'] ?? '');
    if (mb_strlen($q) < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    $qEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    // Solo sugiere gente que YA tiene token registrado -- si no tiene
    // token, no hay a dónde mandarle la notificación de todos modos.
    $rs = $conecta->consulta(
        "SELECT pt.clave, eu.nombre
         FROM carobra_2014.push_tokens pt
         LEFT JOIN carobra_2014.empl_usuarios eu ON pt.clave = eu.clave
         WHERE eu.nombre LIKE '%{$qEsc}%'
         LIMIT 15"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'clave'  => $row['clave'],
                'nombre' => utf8_encode($row['nombre'] ?? ''),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarEstructura (typeahead en tiempo real)
   $campo = 'sc' | 'brocker' | 'gerencia'
   Devuelve valores ÚNICOS que coincidan, con el conteo de gente
   que cae en esa estructura -- así el admin ve de una vez a cuántos
   les va a llegar antes de confirmar.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarEstructura') {

    $campo = trim($input['campo'] ?? '');
    $q     = trim($input['q']     ?? '');

    $camposValidos = ['sc' => 'sc', 'broker' => 'brocker', 'gerencia' => 'gerencia'];
    if (!isset($camposValidos[$campo])) {
        echo json_encode(['ok' => false, 'msg' => 'Campo inválido.']);
        exit;
    }
    $columnaReal = $camposValidos[$campo];

    if (mb_strlen($q) < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    $qEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    $rs = $conecta->consulta(
        "SELECT {$columnaReal} AS valor, COUNT(*) AS total
         FROM carobra_2014.estruc_empleados
         WHERE {$columnaReal} LIKE '%{$qEsc}%' AND {$columnaReal} IS NOT NULL AND {$columnaReal} != ''
         GROUP BY {$columnaReal}
         ORDER BY {$columnaReal} ASC
         LIMIT 20"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'valor' => utf8_encode($row['valor']),
                'total' => intval($row['total']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);