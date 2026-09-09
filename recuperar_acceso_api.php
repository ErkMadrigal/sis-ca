<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* Si algo truena fatal (require que no encuentra el archivo, clase
   que no existe, etc.), por default PHP devuelve una página en
   blanco con status 500 y sin cuerpo — el navegador no ve ningún
   detalle y el fetch/$.ajax del frontend tampoco. Este shutdown
   handler convierte ESE fatal en un JSON normal (ok:false + el
   mensaje exacto de qué falló y en qué línea), para poder
   diagnosticarlo sin acceso al log del servidor. Se puede quitar el
   campo 'debug' una vez identificado y resuelto el problema real. */
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'ok'    => false,
            'msg'   => 'Error interno del servidor al recuperar el acceso.',
            'debug' => $error['message'] . ' en ' . $error['file'] . ':' . $error['line'],
        ]);
    }
});

session_start();
header('Content-Type: application/json; charset=utf-8');

/* Este archivo se pudo haber colocado en la raíz (junto a index.php)
   o dentro de alguna subcarpeta (ej. afore/) — en vez de asumir una
   ubicación fija, se busca ./funciones/clas_carobra.php subiendo
   directorios hasta encontrarlo (o hasta rendirse). Así funciona sin
   importar en qué carpeta quede este archivo. */
function localizarArchivoBase($archivoRelativo, $maxNiveles = 3) {
    $dir = __DIR__;
    for ($i = 0; $i <= $maxNiveles; $i++) {
        $candidato = $dir . '/' . $archivoRelativo;
        if (file_exists($candidato)) return $candidato;
        $dir = dirname($dir);
    }
    return null;
}

$rutaClasCarobra = localizarArchivoBase('funciones/clas_carobra.php');

if (!$rutaClasCarobra) {
    echo json_encode([
        'ok'    => false,
        'msg'   => 'Error interno del servidor al recuperar el acceso.',
        'debug' => 'No se encontró funciones/clas_carobra.php ni subiendo 3 niveles desde ' . __DIR__ . ' (revisar en qué carpeta quedó recuperar_acceso_api.php).',
    ]);
    exit;
}

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de la respuesta. */
ob_start();
require_once $rutaClasCarobra;
ob_end_clean();

$conecta = new conector();

/* ════════════════════════════════════════════════════════════
   Recuperar acceso (olvidé mi contraseña y/o clave) — el usuario
   solo da su CURP, se busca en carobra.empleados y se manda por
   correo el nombre/clave/contraseña que tiene en carobra.usuarios.

   Reutiliza el mismo mecanismo de envío de correo (PHPMailer) que
   ya usa el resto del sistema (ver envia_correo() en vacaciones_api.php).
════════════════════════════════════════════════════════════ */

function envia_correo_recuperacion($body, $cliente_email, $subject) {
    $rutaMailer = localizarArchivoBase('PHPMailer/class.phpmailer.php');
    $rutaSmtp   = localizarArchivoBase('PHPMailer/class.smtp.php');

    if (!$rutaMailer || !$rutaSmtp) {
        error_log('Error enviando correo de recuperación de acceso: no se encontró la carpeta PHPMailer subiendo desde ' . __DIR__);
        return false;
    }

    include_once($rutaMailer);
    include_once($rutaSmtp);

    try {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPSecure = 'tls';
        $mail->Username   = "administrador.sisca@carobra.com";
        $mail->Password   = "tttkscwhekfhfiqx";
        $mail->Port       = 587;

        $mail->SetFrom('administrador.sisca@carobra.com', 'Administrador SISCA');

        $mail->AddAddress($cliente_email);
        /* Copia fija siempre, igual que en el resto de correos del sistema */
        // $mail->AddBCC('altas@carobra.com');

        $mail->CharSet = 'UTF-8';
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        if (!$mail->Send()) {
            throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
        }

        return true;

    } catch (Exception $e) {
        error_log('Error enviando correo de recuperación de acceso: ' . $e->getMessage());
        return false;
    }
}

$curp = strtoupper(trim($_POST['curp'] ?? ''));

if ($curp === '') {
    echo json_encode(['ok' => false, 'msg' => 'Ingresa tu CURP.']);
    exit;
}

/* Validación de formato oficial de CURP (18 posiciones). Filtra
   teclazos/typos obvios antes de siquiera consultar la BD. Si de
   verdad no la conoce, en el modal ya se le manda a gob.mx/curp. */
if (!preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/', $curp)) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'El formato de la CURP no parece válido (deben ser 18 caracteres). Verifícala en https://www.gob.mx/curp/',
    ]);
    exit;
}

$curpEsc = mysqli_real_escape_string($conecta->conexion, $curp);

$query = "SELECT u.clave, u.nombre, u.contrasena, u.email, e.curp
          FROM carobra.usuarios u
          LEFT JOIN carobra.empleados e ON u.clave = e.clave
          WHERE e.curp = '{$curpEsc}'
          LIMIT 1";

$res = $conecta->consulta($query);
$row = $res ? mysqli_fetch_assoc($res) : null;

/* Mismo mensaje tanto si no se encontró la CURP como si se envió
   bien: así no se puede usar este formulario para adivinar/confirmar
   qué CURPs existen en el sistema. Si algo falla técnicamente al
   mandar el correo, ahí sí se avisa distinto (para que puedan
   reintentar en vez de pensar que ya les llegó). */
$msgGenerico = 'Si la CURP está registrada y tiene un correo asociado, en unos minutos te llegará un correo con tu nombre, clave y contraseña.';

if (!$row || empty($row['email'])) {
    echo json_encode(['ok' => true, 'msg' => $msgGenerico]);
    exit;
}

$nombre = utf8_encode($row['nombre']);
$clave  = $row['clave'];
$pwd    = $row['contrasena'];
$email  = $row['email'];

$primerNombre = ucfirst(strtolower(explode(' ', trim($nombre))[0] ?? ''));

$htmlCorreo = '
<div style="font-family:Segoe UI,Arial,sans-serif;max-width:480px;margin:0 auto;background:#f4f6f8;padding:30px 0;">
  <div style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#0f2942,#1a6785);padding:32px 28px;text-align:center;">
      <div style="font-size:13px;letter-spacing:2px;color:#9fd4e8;font-weight:600;text-transform:uppercase;">CAROBRA</div>
      <div style="font-size:11px;color:#cfe8f2;letter-spacing:1px;margin-top:2px;">Consultores Especializados</div>
    </div>
    <div style="padding:32px 28px;">
      <h2 style="margin:0 0 6px;font-size:19px;color:#0f2942;">🔑 Recuperación de acceso</h2>
      <p style="margin:0 0 22px;font-size:13.5px;color:#555;line-height:1.6;">
        Hola ' . htmlspecialchars($primerNombre) . ', solicitaste recuperar tus accesos en <strong>SISCA</strong>. Aquí están:
      </p>
      <div style="background:#f0f6f9;border:1px solid #d8e8ee;border-radius:10px;padding:18px 20px;margin-bottom:22px;">
        <table style="width:100%;font-size:13.5px;color:#1a1a2e;">
          <tr>
            <td style="padding:6px 0;color:#888;width:110px;">Nombre</td>
            <td style="padding:6px 0;font-weight:700;">' . htmlspecialchars($nombre) . '</td>
          </tr>
          <tr>
            <td style="padding:6px 0;color:#888;">Usuario / clave</td>
            <td style="padding:6px 0;font-weight:700;letter-spacing:.5px;">' . htmlspecialchars($clave) . '</td>
          </tr>
          <tr>
            <td style="padding:6px 0;color:#888;">Contraseña</td>
            <td style="padding:6px 0;font-weight:700;font-family:monospace;letter-spacing:1px;background:#fff;border-radius:5px;display:inline-block;padding-left:8px;padding-right:8px;">' . htmlspecialchars($pwd) . '</td>
          </tr>
        </table>
      </div>
      <p style="margin:0 0 22px;font-size:12.5px;color:#888;line-height:1.6;">
        Si no solicitaste esta recuperación, ignora este correo o contacta al administrador del sistema.
      </p>
      <div style="text-align:center;margin-bottom:6px;">
        <a href="https://www.sis-ca.com.mx:7863/" style="display:inline-block;background:#1a6785;color:#fff;text-decoration:none;padding:11px 26px;border-radius:7px;font-size:13px;font-weight:600;">
          Entrar al sistema
        </a>
      </div>
    </div>
    <div style="background:#f4f6f8;padding:18px 28px;text-align:center;border-top:1px solid #e8e8e8;">
      <p style="margin:0;font-size:11px;color:#999;">Atte. Administrador de Sistema</p>
      <p style="margin:2px 0 0;font-size:11px;color:#999;">Carobra Consultores Especializados</p>
    </div>
  </div>
</div>';

$enviado = envia_correo_recuperacion($htmlCorreo, $email, 'Recuperación de acceso — Sistema SISCA');

if (!$enviado) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'No se pudo enviar el correo por un problema técnico. Intenta de nuevo en un momento o contacta al administrador.',
    ]);
    exit;
}

echo json_encode(['ok' => true, 'msg' => $msgGenerico]);