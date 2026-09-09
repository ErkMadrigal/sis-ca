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

if ($action === 'listar') {
    $tipo       = $input['tipo']       ?? '';
    $referencia = $input['referencia'] ?? '';

    $tipoEsc = mysqli_real_escape_string($conecta->conexion, $tipo);
    $refEsc  = mysqli_real_escape_string($conecta->conexion, $referencia);

    $rs = $conecta->consulta("
        SELECT id, tipo, referencia, clave_autor, nombre_autor,
               origen, mensaje, creado_en
        FROM carobra_2014.comentarios
        WHERE tipo = '{$tipoEsc}' AND referencia = '{$refEsc}'
        ORDER BY creado_en ASC
    ");

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'           => $row['id'],
                'clave_autor'  => $row['clave_autor'],
                'nombre_autor' => utf8_encode($row['nombre_autor']),
                'origen'       => $row['origen'],
                'mensaje'      => utf8_encode($row['mensaje']),
                'creado_en'    => $row['creado_en'],
            ];
        }
    }
    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

if ($action === 'agregar') {
    $tipo       = trim($input['tipo']       ?? '');
    $referencia = trim($input['referencia'] ?? '');
    $mensaje    = trim($input['mensaje']    ?? '');
    $clave      = $_SESSION['clave']        ?? 'SISCA';
    $nombre     = $_SESSION['nombre']       ?? 'Analista SISCA';

    if (!$tipo || !$referencia || !$mensaje) {
        echo json_encode(['ok' => false, 'msg' => 'Datos requeridos']);
        exit;
    }

    $tipoEsc   = mysqli_real_escape_string($conecta->conexion, $tipo);
    $refEsc    = mysqli_real_escape_string($conecta->conexion, $referencia);
    $msgEsc    = mysqli_real_escape_string($conecta->conexion, utf8_decode($mensaje));
    $claveEsc  = mysqli_real_escape_string($conecta->conexion, $clave);
    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));

    // Insertar comentario
    $ok = $conecta->consulta("
        INSERT INTO carobra_2014.comentarios
            (tipo, referencia, clave_autor, nombre_autor, origen, mensaje)
        VALUES
            ('{$tipoEsc}', '{$refEsc}', '{$claveEsc}', '{$nombreEsc}', 'sisca', '{$msgEsc}')
    ");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar']);
        exit;
    }

    // Buscar al capturista del folio
    $rsComp = $conecta->consulta("
        SELECT ca.id_usuario_cap AS no_empleado, pt.token, pt.plataforma
        FROM carobra_2014.capturas_app ca
        LEFT JOIN carobra_2014.push_tokens pt ON pt.no_empleado = ca.id_usuario_cap
        WHERE ca.folio = '{$refEsc}'
        LIMIT 1
    ");
    $comp = $rsComp ? mysqli_fetch_assoc($rsComp) : null;

    if ($comp && $comp['no_empleado']) {
        $titulo    = "💬 Nuevo comentario de SISCA";
        $cuerpoMsg = "Folio {$referencia}: " . mb_substr($mensaje, 0, 80);
        $tituloEsc = mysqli_real_escape_string($conecta->conexion, $titulo);
        $cuerpoEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($cuerpoMsg));

        $conecta->consulta("
            INSERT INTO carobra_2014.notificaciones 
                (titulo, mensaje, tipo_destinatario, fecha_programada, estatus, creado_por, referencia, tipo_ref)
            VALUES 
                ('{$tituloEsc}', '{$cuerpoEsc}', 'personal', NOW(), 'completada', '{$claveEsc}', '{$refEsc}', 'traspaso')
        ");
        $id_notif  = mysqli_insert_id($conecta->conexion);
        $noEmpDest = intval($comp['no_empleado']);
        $tokenU    = mysqli_real_escape_string($conecta->conexion, $comp['token'] ?? '');

        $conecta->consulta("
            INSERT INTO carobra_2014.notificaciones_detalle
                (id_notificacion, no_empleado, token, estatus, enviado_en, leida)
            VALUES
                ({$id_notif}, {$noEmpDest}, '{$tokenU}', 'enviado', NOW(), 0)
        ");

        if (!empty($comp['token'])) {
            enviarPushSisca($comp['token'], $titulo, $cuerpoMsg);
        }
    }

    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida']);

// Función push
function enviarPushSisca($token, $titulo, $cuerpo) {
    $credFile = '/var/www/html/carobra-api/storage/firebase-credentials.json';
    if (!file_exists($credFile)) return;

    $credentials = json_decode(file_get_contents($credFile), true);
    $projectId   = $credentials['project_id'];

    $now    = time();
    $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
    $claim  = rtrim(strtr(base64_encode(json_encode([
        'iss'   => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ])), '+/', '-_'), '=');

    $sig = '';
    openssl_sign("{$header}.{$claim}", $sig, $credentials['private_key'], 'SHA256');
    $jwt = "{$header}.{$claim}." . rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]),
    ]);
    $resp         = json_decode(curl_exec($ch), true);
    $access_token = $resp['access_token'] ?? null;
    curl_close($ch);

    if (!$access_token) return;

    $ch = curl_init("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'message' => [
                'token'        => $token,
                'notification' => ['title' => $titulo, 'body' => $cuerpo],
                'data'         => ['tipo' => 'comentario'],
            ]
        ]),
    ]);
    curl_exec($ch);
    curl_close($ch);
}