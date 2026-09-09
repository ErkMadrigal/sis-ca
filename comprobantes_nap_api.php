<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

ini_set('post_max_size',       '50M');
ini_set('upload_max_filesize', '50M');
ini_set('memory_limit',        '256M');

require './vendor/autoload.php';
include('./funciones/encripta.php');

$conecta = new conector();

/* ════════════════════════════════
   HELPERS
════════════════════════════════ */

/* Extrae el número de NAP de un texto tipo "COMISIONES NAP XXXXXXXXX P0526".
   Se deja como red de seguridad: lo normal es que el JS ya mande SOLO el
   número de NAP (sin "COMISIONES NAP" ni el folio "P0526"), pero si por
   algún motivo llega el texto completo, aquí igual se rescata el número. */
function extraerNap($concepto) {
    if (preg_match('/NAP\s+([A-Za-z0-9]+)/i', $concepto, $m)) {
        return trim($m[1]);
    }
    return trim($concepto) !== '' ? trim($concepto) : null;
}

/* Si lo que llega ya es "solo el número" (alfanumérico limpio, sin
   espacios), se usa tal cual; si no, se cae al extractor de arriba. */
function resolverNumeroNap($conceptoPago) {
    $limpio = trim($conceptoPago);
    if ($limpio !== '' && preg_match('/^[A-Za-z0-9]+$/', $limpio)) {
        return $limpio;
    }
    return extraerNap($limpio);
}

/* Usuario bajo el que corre PHP/el servidor web (ej. www-data). Sirve
   para armar mensajes de error de permisos que ya traen el comando
   exacto a correr en el servidor, sin tener que adivinar. */
function usuarioPhpActual() {
    if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
        $info = @posix_getpwuid(posix_geteuid());
        if ($info && !empty($info['name'])) return $info['name'];
    }
    if (function_exists('get_current_user')) {
        $u = get_current_user();
        if (!empty($u)) return $u;
    }
    return 'www-data';
}

function formatearFecha($fecha) {
    // "06/05/2026" -> "2026-05-06"
    $partes = explode('/', $fecha);
    if (count($partes) === 3) {
        return sprintf('%04d-%02d-%02d', intval($partes[2]), intval($partes[1]), intval($partes[0]));
    }
    return null;
}

function formatearPeriodo($periodo) {
    // "Periodo_05" -> "05"
    $num = preg_replace('/\D/', '', $periodo);
    return str_pad($num, 2, '0', STR_PAD_LEFT);
}

function formatearAnio($anio) {
    // "2026" -> "26"
    return substr($anio, -2);
}

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   ACTION: keepFolder
════════════════════════════════════════════════════════════ */
if ($action === 'keepFolder') {

    $pagina         = intval($input['pagina']         ?? 0);
    $periodo        = trim($input['periodo']          ?? '00');
    $anio           = trim($input['anio']             ?? '0000');
    // El JS ya manda solo el número de NAP (sin "COMISIONES NAP" ni "P0526").
    $conceptoPago   = trim($input['conceptoPago']      ?? 'DESCONOCIDO');
    $fileName       = basename($input['fileName']      ?? 'archivo.pdf');
    $pdfBase64      = $input['pdfBase64']              ?? '';

    $claveRastreo    = trim($input['claveRastreo']    ?? '');
    $fechaCapturaRaw = trim($input['fechaCaptura']     ?? '');
    $montoRaw        = trim($input['montoOperacion']   ?? '');

    if ($pagina <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Número de página inválido']);
        exit;
    }
    if (empty($pdfBase64)) {
        echo json_encode(['status' => 'error', 'msg' => 'No se recibió contenido PDF']);
        exit;
    }

    $pdfBytes = base64_decode($pdfBase64, true);
    if ($pdfBytes === false || strlen($pdfBytes) < 4) {
        echo json_encode(['status' => 'error', 'msg' => 'Base64 inválido o PDF vacío']);
        exit;
    }
    if (substr($pdfBytes, 0, 4) !== '%PDF') {
        echo json_encode(['status' => 'error', 'msg' => 'El contenido no es un PDF válido']);
        exit;
    }

    /* Carpeta fija de destino: ya no se arma a partir de lo que mande el
       cliente, siempre es ./facturas/Pagos_nap/ */
    $destDir = __DIR__ . '/facturas/Pagos_nap/';

    if (!is_dir($destDir)) {
        if (!@mkdir($destDir, 0775, true)) {
            $usuarioPhp = usuarioPhpActual();
            echo json_encode([
                'status' => 'error',
                'msg'    => "No se pudo crear la carpeta $destDir (permisos). En el servidor corre:\n" .
                            "sudo mkdir -p " . rtrim(__DIR__, '/') . "/facturas/Pagos_nap\n" .
                            "sudo chown -R {$usuarioPhp}:{$usuarioPhp} " . rtrim(__DIR__, '/') . "/facturas\n" .
                            "sudo chmod -R 775 " . rtrim(__DIR__, '/') . "/facturas",
                'ruta'        => $destDir,
                'usuario_php' => $usuarioPhp,
            ]);
            exit;
        }
    }

    /* La carpeta puede existir de antes (creada a mano, por FTP, etc.)
       con un dueño distinto al del proceso PHP: is_dir() la ve, pero
       file_put_contents tronaría con "Permission denied". Se detecta
       aquí, ANTES de intentar escribir, para dar un mensaje claro con
       el comando exacto en vez de un warning críptico de PHP. */
    if (!is_writable($destDir)) {
        $usuarioPhp = usuarioPhpActual();
        echo json_encode([
            'status' => 'error',
            'msg'    => "El servidor (usuario '{$usuarioPhp}') no tiene permiso de escritura en $destDir. En el servidor corre:\n" .
                        "sudo chown -R {$usuarioPhp}:{$usuarioPhp} " . rtrim(__DIR__, '/') . "/facturas\n" .
                        "sudo chmod -R 775 " . rtrim(__DIR__, '/') . "/facturas",
            'ruta'        => $destDir,
            'usuario_php' => $usuarioPhp,
        ]);
        exit;
    }

    $periodo2 = formatearPeriodo($periodo);
    $anio2    = formatearAnio($anio);

    /* Nombre de archivo: solo NAP + periodo + año (ya sin "_p_{pagina}"). */
    $safeConcept = preg_replace('/[^A-Za-z0-9\-]/', '_', $conceptoPago);
    $safeConcept = trim($safeConcept, '_');
    $outputName  = $safeConcept . '_' . $periodo2 . '_' . $anio2 . '.pdf';
    $destPath    = $destDir . $outputName;
    $rutaRel     = 'facturas/Pagos_nap/' . $outputName;

    $written = @file_put_contents($destPath, $pdfBytes);
    if ($written === false) {
        $usuarioPhp = usuarioPhpActual();
        echo json_encode([
            'status' => 'error',
            'msg'    => "Error al escribir en disco (permisos). Usuario del servidor: '{$usuarioPhp}'. Corre:\n" .
                        "sudo chown -R {$usuarioPhp}:{$usuarioPhp} " . rtrim(__DIR__, '/') . "/facturas\n" .
                        "sudo chmod -R 775 " . rtrim(__DIR__, '/') . "/facturas",
            'ruta'        => $destPath,
            'usuario_php' => $usuarioPhp,
        ]);
        exit;
    }

    /* ── Resolver clave_nap (clave del dueño del NAP) ──
       Antes se guardaba el número de NAP crudo en la columna "nap".
       Ahora esa columna se llama "clave_nap" y en vez del número, se
       guarda la CLAVE del empleado dueño de ese nap, resuelta con:
           SELECT n.nap, eu.clave
           FROM naps n
           JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
       Si el nap no se encuentra (o no tiene dueño ligado), se guarda
       NULL en vez de tronar todo el registro. */
    $napNumero = resolverNumeroNap($conceptoPago);
    $claveNap  = null;

    if (!empty($napNumero)) {
        $napEsc  = mysqli_real_escape_string($conecta->conexion, $napNumero);
        $rsClave = $conecta->consulta(
            "SELECT eu.clave
             FROM naps n
             JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
             WHERE n.nap = '{$napEsc}'
             LIMIT 1"
        );
        $rowClave = $rsClave ? mysqli_fetch_assoc($rsClave) : null;
        $claveNap = $rowClave ? $rowClave['clave'] : null;
    }

    $fechaCaptura   = formatearFecha($fechaCapturaRaw);
    $montoOperacion = str_replace([',', '$'], '', $montoRaw);
    $montoOperacion = is_numeric($montoOperacion) ? $montoOperacion : 0;

    /* concepto_pago y pagina ya no se guardan en la tabla. */
    $sqlNap = "INSERT INTO datos_transferencias_nap
        (clave_nap, periodo, anio, fecha_captura, clave_rastreo, monto_operacion, archivo_origen, archivo_pdf)
        VALUES (
            " . ($claveNap ? "'" . addslashes($claveNap) . "'" : "NULL") . ",
            '" . addslashes($periodo2) . "',
            '" . addslashes($anio2) . "',
            " . ($fechaCaptura ? "'" . addslashes($fechaCaptura) . "'" : "NULL") . ",
            '" . addslashes($claveRastreo) . "',
            " . floatval($montoOperacion) . ",
            '" . addslashes($fileName) . "',
            '" . addslashes($rutaRel) . "'
        )";

    $conecta->consulta($sqlNap);

    echo json_encode([
        'status'    => 'ok',
        'msg'       => 'Página guardada correctamente',
        'file'      => $rutaRel,
        'ruta_abs'  => $destPath,
        'bytes'     => $written,
        'pagina'    => $pagina,
        'nap'       => $napNumero,
        'clave_nap' => $claveNap
    ]);
    exit;
}

/* ── Acción no reconocida ── */
echo json_encode(['status' => 'error', 'msg' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;