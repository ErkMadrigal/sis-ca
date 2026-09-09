<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

ini_set('post_max_size',       '50M');
ini_set('upload_max_filesize', '50M');
ini_set('memory_limit',        '256M');

require_once './vendor/autoload.php';
require_once './funciones/encripta.php';

$conecta = new conector();  /* conecta a carobra_2014 por default */

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   HELPER: clave única con sufijo numérico si ya existe
   Ej: AAGARCIA → AAGARCIA2 → AAGARCIA3 …
════════════════════════════════════════════════════════════ */
/* Genera la clave a partir del NOMBRE COMPLETO con la regla:
   1 palabra            -> la palabra completa
   2 palabras (Nombre Apellido) -> 2 iniciales del nombre + apellido completo
   3+ palabras (Nombre Apellido Extra) -> 2 iniciales del nombre + apellido + 1ra letra de lo siguiente
   Sin sufijos numéricos. Si colisiona, se prueban variantes con más iniciales
   del nombre (3, 4… letras) antes de rendirse. */
function construirClaveBase($nombreCompleto, $iniciales = 2) {
    $partes = preg_split('/\s+/', trim(strtoupper($nombreCompleto)));
    $partes = array_values(array_filter($partes, fn($p) => $p !== ''));
    $total  = count($partes);

    if ($total === 0) return '';

    if ($total === 1) {
        $clave = $partes[0];
    } elseif ($total === 2) {
        $clave = substr($partes[0], 0, $iniciales) . $partes[1];
    } else {
        $clave = substr($partes[0], 0, $iniciales) . $partes[1] . substr($partes[2], 0, 1);
    }

    $clave = preg_replace('/[^A-Z0-9]/', '', $clave);
    return substr($clave, 0, 10);
}

function claveUnica($nombreCompleto, $conecta, $excluirId = '') {
    for ($iniciales = 2; $iniciales <= 6; $iniciales++) {
        $clave = construirClaveBase($nombreCompleto, $iniciales);
        if ($clave === '') break;

        $esc = mysqli_real_escape_string($conecta->conexion, $clave);
        $exc = $excluirId !== '' ? " AND clave <> '" . mysqli_real_escape_string($conecta->conexion, $excluirId) . "'" : '';
        $rs  = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE clave='{$esc}'{$exc} LIMIT 1");

        if (!$rs || !mysqli_fetch_assoc($rs)) {
            return $clave; /* libre, la usamos */
        }
    }

    /* Si tras agotar variantes con iniciales sigue colisionando, regresamos
       la base de 2 iniciales tal cual (caso extremo, debe revisarse a mano) */
    return construirClaveBase($nombreCompleto, 2);
}

/* ════════════════════════════════════════════════════════════
   HELPER: genera password random tipo VuSuAe-_19
════════════════════════════════════════════════════════════ */
/* Convierte yyyy-mm-dd (input HTML date) -> dd/mm/yyyy (formato BD VARCHAR(10)) */
function formatoFechaBD($fechaIso) {
    $fechaIso = trim($fechaIso);
    if (empty($fechaIso)) return '';
    $partes = explode('-', $fechaIso);
    if (count($partes) !== 3) return '';
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}

/* Convierte dd/mm/yyyy (BD) -> yyyy-mm-dd (input HTML date) */
function formatoFechaInput($fechaBd) {
    $fechaBd = trim($fechaBd);
    if (empty($fechaBd)) return '';
    $partes = explode('/', $fechaBd);
    if (count($partes) !== 3) return '';
    return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
}

function generarPwd() {
    $cons = ['b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z'];
    $voc  = ['a','e','i','o','u'];
    $seps = ['-_','._','-*','.*','-#'];
    $pwd  = '';
    for ($i = 0; $i < 3; $i++)
        $pwd .= strtoupper($cons[array_rand($cons)]) . $voc[array_rand($voc)];
    /* VARCHAR(10): 6 letras + 2 sep + 2 dígitos = 10 exactos */
    return $pwd . $seps[array_rand($seps)] . rand(10, 99);
}

/* ════════════════════════════════════════════════════════════
   HELPER: envío de correo (PHPMailer)
════════════════════════════════════════════════════════════ */
function envia_correo($body, $from, $cliente_email, $asesor_email = null, $subjet = '', $filename = '') {
    include_once("./PHPMailer/class.phpmailer.php");
    include_once("./PHPMailer/class.smtp.php");

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

        if (!empty(trim($asesor_email))) {
            $mail->AddBCC($asesor_email);
        }

        $mail->AddBCC('altas@carobra.com');

        $mail->CharSet = 'UTF-8';
        $mail->IsHTML(true);
        $mail->Subject = $subjet;
        $mail->Body    = $body;

        if (!empty($filename) && file_exists($filename)) {
            $mail->AddAttachment($filename);
        }

        if (!$mail->Send()) {
            throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
        }

        return true;

    } catch (Exception $e) {
        error_log("envia_correo error: " . $e->getMessage());
        return false;
    }
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarAsesor
════════════════════════════════════════════════════════════ */
if ($action === 'buscarAsesor') {

    $q   = trim($input['q'] ?? '');
    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    if (strlen($q) < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    $rs    = $conecta->consulta(
        "SELECT clave, nombre, activo FROM carobra.usuarios
         WHERE (nombre LIKE '%{$esc}%' OR clave LIKE '%{$esc}%')
         ORDER BY activo DESC, nombre LIMIT 20"
    );
    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'     => utf8_encode($row['clave']),
                'nombre' => utf8_encode($row['nombre']),
                'clave'  => utf8_encode($row['clave']),
                'activo' => intval($row['activo']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getEmpleado
════════════════════════════════════════════════════════════ */
if ($action === 'getEmpleado') {

    $id = trim($input['id'] ?? '');
    if (empty($id)) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit;
    }

    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($id));
    $rs  = $conecta->consulta(
        "SELECT clave, nombre, email, activo FROM carobra.usuarios WHERE clave='{$esc}' LIMIT 1"
    );
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    if (!$row) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    echo json_encode([
        'ok'       => true,
        'empleado' => [
            'id'     => utf8_encode($row['clave']),
            'nombre' => utf8_encode($row['nombre']),
            'clave'  => utf8_encode($row['clave']),
            'email'  => utf8_encode($row['email'] ?? ''),
            'activo' => $row['activo'],
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: verificarClaves
   Recibe un array de claves candidatas y regresa cuáles ya existen
════════════════════════════════════════════════════════════ */
if ($action === 'verificarClaves') {

    $claves = $input['claves'] ?? [];
    $disponibles = [];

    if (is_array($claves)) {
        foreach ($claves as $cv) {
            $cv = strtoupper(trim($cv));
            if ($cv === '') continue;
            $esc = mysqli_real_escape_string($conecta->conexion, $cv);
            $rs  = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE clave='{$esc}' LIMIT 1");
            $existe = ($rs && mysqli_fetch_assoc($rs)) ? true : false;
            $disponibles[$cv] = !$existe; /* true = disponible, false = ya existe */
        }
    }

    echo json_encode(['ok' => true, 'disponibles' => $disponibles]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarEmpleado
════════════════════════════════════════════════════════════ */
if ($action === 'guardarEmpleado') {

    $id       = trim($input['id']       ?? '');
    $nombre   = strtoupper(trim($input['nombre']   ?? ''));
    $claveIn  = strtoupper(trim($input['clave']    ?? ''));
    $email    = trim($input['email']    ?? '');
    $password = trim($input['password'] ?? '');
    $activo   = intval($input['activo'] ?? 1);

    /* ════════════════
       UPDATE
    ════════════════ */
    if (!empty($id)) {

        $idEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($id));
        $sets  = "activo={$activo}";
        $cambioPassword = false;

        if (!empty($password)) {
            $contEsc = mysqli_real_escape_string($conecta->conexion, substr($password, 0, 10));
            $pwdEsc  = $contEsc;
            $sets   .= ", contrasena='{$contEsc}', pwd='{$pwdEsc}'";
            $cambioPassword = true;
        }

        $conecta->consulta("UPDATE carobra.usuarios SET {$sets} WHERE clave='{$idEsc}'");

        /* Si se cambió el password, enviar correo con la nueva contraseña */
        if ($cambioPassword) {
            $rsU  = $conecta->consulta("SELECT nombre, email FROM carobra.usuarios WHERE clave='{$idEsc}' LIMIT 1");
            $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;

            if ($rowU && !empty($rowU['email'])) {
                $nombreU      = utf8_encode($rowU['nombre']);
                $emailU       = $rowU['email'];
                $primerNombre = ucfirst(strtolower(explode(' ', trim($nombreU))[0]));

                $htmlCorreo = '
                <div style="font-family:Segoe UI,Arial,sans-serif;max-width:480px;margin:0 auto;background:#f4f6f8;padding:30px 0;">
                  <div style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.08);">
                    <div style="background:linear-gradient(135deg,#0f2942,#1a6785);padding:32px 28px;text-align:center;">
                      <div style="font-size:13px;letter-spacing:2px;color:#9fd4e8;font-weight:600;text-transform:uppercase;">CAROBRA</div>
                      <div style="font-size:11px;color:#cfe8f2;letter-spacing:1px;margin-top:2px;">Consultores Especializados</div>
                    </div>
                    <div style="padding:32px 28px;">
                      <h2 style="margin:0 0 6px;font-size:19px;color:#0f2942;">🔐 Contraseña actualizada</h2>
                      <p style="margin:0 0 22px;font-size:13.5px;color:#555;line-height:1.6;">
                        Hola ' . htmlspecialchars($primerNombre) . ', tu contraseña en <strong>SISCA</strong> fue actualizada. Estos son tus accesos vigentes:
                      </p>
                      <div style="background:#f0f6f9;border:1px solid #d8e8ee;border-radius:10px;padding:18px 20px;margin-bottom:22px;">
                        <table style="width:100%;font-size:13.5px;color:#1a1a2e;">
                          <tr>
                            <td style="padding:6px 0;color:#888;width:90px;">Usuario</td>
                            <td style="padding:6px 0;font-weight:700;letter-spacing:.5px;">' . htmlspecialchars($id) . '</td>
                          </tr>
                          <tr>
                            <td style="padding:6px 0;color:#888;">Contraseña</td>
                            <td style="padding:6px 0;font-weight:700;font-family:monospace;letter-spacing:1px;background:#fff;border-radius:5px;display:inline-block;padding-left:8px;padding-right:8px;">' . htmlspecialchars($password) . '</td>
                          </tr>
                        </table>
                      </div>
                      <p style="margin:0 0 22px;font-size:12.5px;color:#888;line-height:1.6;">
                        Si no solicitaste este cambio, contacta de inmediato al administrador del sistema.
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

                $emailCopia = trim($input['email_copia'] ?? '');
                $emailCopiaValida = (!empty($emailCopia) && filter_var($emailCopia, FILTER_VALIDATE_EMAIL)) ? $emailCopia : null;

                envia_correo($htmlCorreo, 'administrador.sisca@carobra.com', $emailU, $emailCopiaValida, 'Tu contraseña en el Sistema Carobra fue actualizada', '');
            }
        }

        echo json_encode(['ok' => true, 'msg' => 'Empleado actualizado.', 'id' => $id]);
        exit;
    }

    /* ════════════════
       INSERT (alta nueva)
    ════════════════ */
    if (empty($nombre)) {
        echo json_encode(['ok' => false, 'msg' => 'El nombre es requerido.']);
        exit;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok' => false, 'msg' => 'El correo no es válido.']);
        exit;
    }
    if (empty($claveIn)) {
        echo json_encode(['ok' => false, 'msg' => 'La clave es obligatoria.']);
        exit;
    }

    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
    $emailEsc  = mysqli_real_escape_string($conecta->conexion, $email);

    /* Email único */
    $rs = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE email='{$emailEsc}' LIMIT 1");
    if ($rs && mysqli_fetch_assoc($rs)) {
        echo json_encode(['ok' => false, 'msg' => 'El correo ya está registrado.']);
        exit;
    }

    /* La clave ya viene elegida por el usuario desde el selector de variantes.
       Validamos que no exista (por si cambió algo entre que se verificó y se guardó);
       si llegara a colisionar, caemos a claveUnica() como respaldo. */
    $clave = strtoupper(trim($claveIn));
    if ($clave === '') {
        $clave = claveUnica($nombre, $conecta);
    } else {
        $escCheck = mysqli_real_escape_string($conecta->conexion, $clave);
        $rsCheck  = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE clave='{$escCheck}' LIMIT 1");
        if ($rsCheck && mysqli_fetch_assoc($rsCheck)) {
            $clave = claveUnica($nombre, $conecta);
        }
    }
    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);

    /* Password random si no viene */
    if (empty($password)) $password = generarPwd();

    /* encripta() expande el texto 4x+6 chars.
       Usamos máx 6 chars del password para que quepa en VARCHAR(30).
       El password real (completo) va en pwd con bcrypt. */
    /* Ignorar encripta() — guardar password directo en contrasena, bcrypt en pwd */
    $contEsc = mysqli_real_escape_string($conecta->conexion, substr($password, 0, 10));
    $pwdEsc  = $contEsc;

    /* INSERT usuarios */
    $ok = $conecta->consulta(
        "INSERT INTO carobra.usuarios (clave, nombre, contrasena, activo, email, email_inst, pwd, afore, seg_inver, seg_educa)
         VALUES ('{$claveEsc}', '{$nombreEsc}', '{$contEsc}', {$activo}, '{$emailEsc}', '', '{$pwdEsc}', 0, 0, 0)"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al insertar usuario: ' . $conecta->conexion->error]);
        exit;
    }

    /* INSERT empleados — max + 1 en query separada */
    $rsMax   = $conecta->consulta("SELECT IFNULL(MAX(no_empleado), 0) + 1 AS nuevo FROM carobra.empleados");
    $rowMax  = $rsMax ? mysqli_fetch_assoc($rsMax) : null;
    $nuevoNo = $rowMax ? intval($rowMax['nuevo']) : 1;

    $hoy   = date('d/m/Y');
    $okEmp = $conecta->consulta(
        "INSERT INTO carobra.empleados
            (no_empleado, clave, fec_ingreso, fec_reingreso, fec_baja,
             domicilio, telefono, celular, factura, fec_factura,
             regimen_fiscal, recluta, deportenis, no_prom_dep, rfc,
             curp, nss, nap_factura, nap_gerencia, ase_bernal,
             nae, categoria, fec_nap, cen_cost, no_contpaq,
             costo_a, staff, lista_negra, jefe_inmediato)
         VALUES
            ({$nuevoNo}, '{$claveEsc}', '{$hoy}', '', '',
             '', '', '', 0, '',
             '', '', 0, '', '',
             '', '', 0, 0, 0,
             '', 0, '', '', '',
             0, 0, 0, '')"
    );

    $msgEmp = $okEmp ? '' : ' (empleados: ' . $conecta->conexion->error . ')';

    /* ── Enviar correo de bienvenida con usuario y contraseña ── */
    $primerNombre = explode(' ', trim($nombre))[0];
    $primerNombre = ucfirst(strtolower($primerNombre));

    $htmlCorreo = '
    <div style="font-family:Segoe UI,Arial,sans-serif;max-width:480px;margin:0 auto;background:#f4f6f8;padding:30px 0;">
      <div style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.08);">
        <div style="background:linear-gradient(135deg,#0f2942,#1a6785);padding:32px 28px;text-align:center;">
          <div style="font-size:13px;letter-spacing:2px;color:#9fd4e8;font-weight:600;text-transform:uppercase;">CAROBRA</div>
          <div style="font-size:11px;color:#cfe8f2;letter-spacing:1px;margin-top:2px;">Consultores Especializados</div>
        </div>
        <div style="padding:32px 28px;">
          <h2 style="margin:0 0 6px;font-size:19px;color:#0f2942;">¡Bienvenido, ' . htmlspecialchars($primerNombre) . '! 🎉</h2>
          <p style="margin:0 0 22px;font-size:13.5px;color:#555;line-height:1.6;">
            Ya eres parte del equipo. Tu usuario en <strong>SISCA</strong> ha sido activado, aquí están tus accesos:
          </p>
          <div style="background:#f0f6f9;border:1px solid #d8e8ee;border-radius:10px;padding:18px 20px;margin-bottom:22px;">
            <table style="width:100%;font-size:13.5px;color:#1a1a2e;">
              <tr>
                <td style="padding:6px 0;color:#888;width:90px;">Usuario</td>
                <td style="padding:6px 0;font-weight:700;letter-spacing:.5px;">' . htmlspecialchars($clave) . '</td>
              </tr>
              <tr>
                <td style="padding:6px 0;color:#888;">Contraseña</td>
                <td style="padding:6px 0;font-weight:700;font-family:monospace;letter-spacing:1px;background:#fff;border-radius:5px;display:inline-block;padding-left:8px;padding-right:8px;">' . htmlspecialchars($password) . '</td>
              </tr>
            </table>
          </div>
          <p style="margin:0 0 22px;font-size:12.5px;color:#888;line-height:1.6;">
            Por seguridad, puedes cambiar tu contraseña en cualquier momento dentro del sistema una vez que inicies sesión.
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

    if (!empty($email) && function_exists('envia_correo')) {
        envia_correo($htmlCorreo, 'administrador.sisca@carobra.com', $email, null, 'Tu usuario en el Sistema Carobra fue activado', '');
    }

    echo json_encode([
        'ok'    => true,
        'msg'   => 'Empleado dado de alta correctamente.' . $msgEmp,
        'id'    => $clave,
        'clave' => $clave,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getDatosGenerales
   Carga los campos de la tabla empleados para la pestaña "Datos generales"
════════════════════════════════════════════════════════════ */
if ($action === 'getDatosGenerales') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rs  = $conecta->consulta(
        "SELECT no_empleado, fec_ingreso, fec_reingreso, fec_baja, jefe_inmediato, staff,
                domicilio, telefono, celular, rfc, curp, nss, fec_nap, recluta
         FROM carobra.empleados
         WHERE clave = '{$esc}'
         LIMIT 1"
    );
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    /* email personal/institucional e email_inst viven en usuarios */
    $rsU  = $conecta->consulta("SELECT email, email_inst FROM carobra.usuarios WHERE clave='{$esc}' LIMIT 1");
    $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;

    /* domicilio: formato NUEVO = "CP|COLONIA|CALLE" (con separadores).
       Si es un registro VIEJO (anterior a este cambio) no tendrá "|" —
       en ese caso todo el contenido se manda a "Calle y número" tal cual,
       y CP/Colonia quedan vacíos para que se capturen de nuevo si aplica. */
    $cp = $colonia = $calle = '';
    if ($row && !empty($row['domicilio'])) {
        if (strpos($row['domicilio'], '|') !== false) {
            $partesDom = explode('|', $row['domicilio']);
            $cp      = $partesDom[0] ?? '';
            $colonia = isset($partesDom[1]) ? utf8_encode($partesDom[1]) : '';
            $calle   = isset($partesDom[2]) ? utf8_encode($partesDom[2]) : '';
        } else {
            /* Formato viejo: todo el campo es la dirección libre */
            $calle = utf8_encode($row['domicilio']);
        }
    }

    /* nombre del jefe inmediato (si tiene clave asignada) */
    $jefeNombre = '';
    if ($row && !empty($row['jefe_inmediato'])) {
        $jefeEsc = mysqli_real_escape_string($conecta->conexion, $row['jefe_inmediato']);
        $rsJ     = $conecta->consulta("SELECT nombre FROM carobra.usuarios WHERE clave='{$jefeEsc}' LIMIT 1");
        $rowJ    = $rsJ ? mysqli_fetch_assoc($rsJ) : null;
        if ($rowJ) $jefeNombre = utf8_encode($rowJ['nombre']);
    }

    /* nombre del reclutador (recluta guarda la clave del reclutador) */
    $reclutaNombre = '';
    if ($row && !empty($row['recluta'])) {
        $reclutaEsc = mysqli_real_escape_string($conecta->conexion, $row['recluta']);
        $rsR        = $conecta->consulta("SELECT nombre FROM carobra.usuarios WHERE clave='{$reclutaEsc}' LIMIT 1");
        $rowR       = $rsR ? mysqli_fetch_assoc($rsR) : null;
        if ($rowR) $reclutaNombre = utf8_encode($rowR['nombre']);
    }

    /* NAP vigente (tabla naps: no_empleado, nap, baja — baja=0 significa vigente) */
    $nap = '';
    if ($row && !empty($row['no_empleado'])) {
        $noEmp = intval($row['no_empleado']);
        $rsNap = $conecta->consulta(
            "SELECT nap FROM carobra_2014.naps WHERE no_empleado={$noEmp} AND baja=0 LIMIT 1"
        );
        $rowNap = $rsNap ? mysqli_fetch_assoc($rsNap) : null;
        if ($rowNap) $nap = utf8_encode($rowNap['nap']);
    }

    echo json_encode([
        'ok'    => true,
        'datos' => [
            'fec_ingreso'      => $row ? formatoFechaInput($row['fec_ingreso'] ?? '')   : '',
            'fec_reingreso'    => $row ? formatoFechaInput($row['fec_reingreso'] ?? '') : '',
            'fec_baja'         => $row ? formatoFechaInput($row['fec_baja'] ?? '')      : '',
            'staff'            => $row ? intval($row['staff'] ?? 0) : 0,
            'jefe_inmediato'   => $row['jefe_inmediato'] ?? '',
            'jefe_nombre'      => $jefeNombre,
            'cp'               => $cp,
            'colonia'          => $colonia,
            'calle'            => $calle,
            'telefono'         => $row ? utf8_encode($row['telefono'] ?? '') : '',
            'celular'          => $row ? utf8_encode($row['celular'] ?? '')  : '',
            'rfc'              => $row ? utf8_encode($row['rfc'] ?? '')      : '',
            'curp'             => $row ? utf8_encode($row['curp'] ?? '')     : '',
            'nss'              => $row ? ($row['nss'] ?? '')                 : '',
            'email_personal'   => $rowU ? utf8_encode($rowU['email'] ?? '')      : '',
            'email_inst'       => $rowU ? utf8_encode($rowU['email_inst'] ?? '') : '',
            'nap'              => $nap,
            'fec_alta_nap'     => $row ? formatoFechaInput($row['fec_nap'] ?? '') : '',
            'recluta_clave'    => $row['recluta'] ?? '',
            'recluta_nombre'   => $reclutaNombre,
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarDatosGenerales
   UPDATE a carobra.empleados (+ email/email_inst en carobra.usuarios)
════════════════════════════════════════════════════════════ */
if ($action === 'guardarDatosGenerales') {

    /* Esta tabla (carobra.empleados) trabaja en latin1; se fuerza el
       charset de la sesión por si la conexión quedó en otro estado. */
    $conecta->conexion->set_charset('latin1');

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    /* Leer las fechas ANTES del UPDATE para detectar cuál cambió
       y poder registrar el evento en historial_movimientos */
    $rsFechasViejas  = $conecta->consulta(
        "SELECT fec_ingreso, fec_reingreso, fec_baja FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1"
    );
    $rowFechasViejas = $rsFechasViejas ? mysqli_fetch_assoc($rsFechasViejas) : null;

    $fecIngreso   = formatoFechaBD($input['fec_ingreso']   ?? '');
    $fecReingreso = formatoFechaBD($input['fec_reingreso'] ?? '');
    $fecBaja      = formatoFechaBD($input['fec_baja']      ?? '');
    $staff        = intval($input['staff'] ?? 0);
    $jefe         = $staff ? strtoupper(trim($input['jefe_inmediato'] ?? '')) : '';
    $cp           = trim($input['cp']       ?? '');
    $colonia      = trim($input['colonia']  ?? '');
    $calle        = trim($input['calle']    ?? '');
    $telefono     = trim($input['telefono'] ?? '');
    $celular      = trim($input['celular']  ?? '');
    $emailPers    = trim($input['email_personal'] ?? '');
    $emailInst    = trim($input['email_inst']      ?? '');
    $nss          = trim($input['nss']  ?? '');
    $rfc          = trim($input['rfc']  ?? '');
    $curp         = trim($input['curp'] ?? '');
    $fecAltaNap   = formatoFechaBD($input['fec_alta_nap'] ?? '');
    $reclutaClave = strtoupper(trim($input['recluta_clave'] ?? ''));

    /* domicilio empaquetado como "CP|COLONIA|CALLE".
       carobra.empleados es latin1; tanto "colonia" (viene de sepomex,
       utf8mb4) como "calle" (texto libre del usuario) llegan en UTF-8
       desde el navegador, así que ambas necesitan utf8_decode(). */
    $domicilio = $cp . '|' . utf8_decode($colonia) . '|' . utf8_decode($calle);
    $domicilio = substr($domicilio, 0, 150);

    $domEsc      = mysqli_real_escape_string($conecta->conexion, $domicilio);
    $jefeEsc     = mysqli_real_escape_string($conecta->conexion, $jefe);
    $telEsc      = mysqli_real_escape_string($conecta->conexion, $telefono);
    $celEsc      = mysqli_real_escape_string($conecta->conexion, $celular);
    $nssEsc      = mysqli_real_escape_string($conecta->conexion, $nss);
    $rfcEsc      = mysqli_real_escape_string($conecta->conexion, $rfc);
    $curpEsc     = mysqli_real_escape_string($conecta->conexion, $curp);
    $reclutaEsc  = mysqli_real_escape_string($conecta->conexion, $reclutaClave);

    $okEmp = $conecta->consulta(
        "UPDATE carobra.empleados SET
            fec_ingreso     = '{$fecIngreso}',
            fec_reingreso   = '{$fecReingreso}',
            fec_baja        = '{$fecBaja}',
            staff           = {$staff},
            jefe_inmediato  = '{$jefeEsc}',
            domicilio       = '{$domEsc}',
            telefono        = '{$telEsc}',
            celular         = '{$celEsc}',
            nss             = '{$nssEsc}',
            rfc             = '{$rfcEsc}',
            curp            = '{$curpEsc}',
            fec_nap         = '{$fecAltaNap}',
            recluta         = '{$reclutaEsc}'
         WHERE clave = '{$claveEsc}'"
    );

    if (!$okEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar en empleados: ' . $conecta->conexion->error]);
        exit;
    }

    /* Registrar en historial_movimientos cualquier fecha que haya
       cambiado respecto a lo que tenía guardado antes. Solo se agrega
       un evento por tipo si la fecha nueva es distinta de la vieja y
       no está vacía (evita registrar "se borró la fecha" como evento). */
    if ($rowFechasViejas) {
        $cambios = [
            'ingreso'   => [$rowFechasViejas['fec_ingreso']   ?? '', $fecIngreso],
            'reingreso' => [$rowFechasViejas['fec_reingreso'] ?? '', $fecReingreso],
            'baja'      => [$rowFechasViejas['fec_baja']      ?? '', $fecBaja],
        ];

        foreach ($cambios as $tipo => [$fechaVieja, $fechaNueva]) {
            if ($fechaNueva !== '' && $fechaNueva !== $fechaVieja) {
                $fechaNuevaEsc = mysqli_real_escape_string($conecta->conexion, $fechaNueva);
                $conecta->consulta(
                    "INSERT INTO carobra_2014.historial_movimientos (clave, tipo, fecha_evento)
                     VALUES ('{$claveEsc}', '{$tipo}', '{$fechaNuevaEsc}')"
                );
            }
        }
    }

    /* Guardar NAP en su propia tabla (no_empleado, nap, baja).
       Se resuelve no_empleado a partir de la clave, y se actualiza el
       NAP vigente (baja=0) si ya existe, o se inserta uno nuevo. */
    $napCapturado = strtoupper(trim($input['nap'] ?? ''));

    if (!empty($napCapturado)) {
        $rsEmpNap  = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
        $rowEmpNap = $rsEmpNap ? mysqli_fetch_assoc($rsEmpNap) : null;

        if ($rowEmpNap) {
            $noEmpNap = intval($rowEmpNap['no_empleado']);
            $napEsc   = mysqli_real_escape_string($conecta->conexion, $napCapturado);

            $rsNapVig  = $conecta->consulta(
                "SELECT no_empleado FROM carobra_2014.naps WHERE no_empleado={$noEmpNap} AND baja=0 LIMIT 1"
            );

            if ($rsNapVig && mysqli_fetch_assoc($rsNapVig)) {
                $conecta->consulta(
                    "UPDATE carobra_2014.naps SET nap='{$napEsc}' WHERE no_empleado={$noEmpNap} AND baja=0"
                );
            } else {
                $conecta->consulta(
                    "INSERT INTO carobra_2014.naps (no_empleado, nap, baja) VALUES ({$noEmpNap}, '{$napEsc}', 0)"
                );
            }
        }
    }

    /* Emails van en usuarios */
    if (!empty($emailPers) || !empty($emailInst)) {
        $emailPersEsc = mysqli_real_escape_string($conecta->conexion, $emailPers);
        $emailInstEsc = mysqli_real_escape_string($conecta->conexion, $emailInst);
        $setsU = [];
        if (!empty($emailPers)) $setsU[] = "email='{$emailPersEsc}'";
        if (!empty($emailInst)) $setsU[] = "email_inst='{$emailInstEsc}'";
        if (!empty($setsU)) {
            $conecta->consulta("UPDATE carobra.usuarios SET " . implode(', ', $setsU) . " WHERE clave='{$claveEsc}'");
        }
    }

    echo json_encode(['ok' => true, 'msg' => 'Datos generales actualizados correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getBancos
   Catálogo de bancos para el selector y la detección por CLABE
════════════════════════════════════════════════════════════ */
if ($action === 'getBancos') {

    $rs     = $conecta->consulta("SELECT id_banco, banco, codigo_banco FROM carobra.bancos ORDER BY banco");
    $bancos = [];

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $bancos[] = [
                'id_banco'     => intval($row['id_banco']),
                'banco'        => utf8_encode($row['banco']),
                'codigo_banco' => $row['codigo_banco'],
            ];
        }
    }

    echo json_encode(['ok' => true, 'bancos' => $bancos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCuentasBancarias
   Lista las cuentas de usuarios_banco para un empleado.
   no_empleado en usuarios_banco se relaciona con la clave del
   usuario vía la tabla empleados (clave -> no_empleado).
════════════════════════════════════════════════════════════ */
if ($action === 'getCuentasBancarias') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rsEmp  = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => true, 'cuentas' => []]);
        exit;
    }

    $noEmpleado = intval($rowEmp['no_empleado']);

    $rs       = $conecta->consulta(
        "SELECT no_empleado, nombre_cta, ap_cta, am_cta, id_banco, clabe, activo
         FROM carobra.usuarios_banco
         WHERE no_empleado = {$noEmpleado}
         ORDER BY activo DESC, nombre_cta"
    );
    $cuentas  = [];

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $cuentas[] = [
                'nombre_cta' => utf8_encode($row['nombre_cta']),
                'ap_cta'     => utf8_encode($row['ap_cta']),
                'am_cta'     => utf8_encode($row['am_cta']),
                'id_banco'   => intval($row['id_banco']),
                'clabe'      => $row['clabe'],
                'activo'     => intval($row['activo']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'cuentas' => $cuentas]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarCuentaBancaria
   Alta o edición de una cuenta en usuarios_banco.
   Si activo=1, primero se inactivan todas las demás cuentas
   del mismo empleado (solo una activa a la vez).
════════════════════════════════════════════════════════════ */
if ($action === 'guardarCuentaBancaria') {

    $clave         = trim($input['clave'] ?? '');
    $nombreCta     = strtoupper(trim($input['nombre_cta'] ?? ''));
    $apCta         = strtoupper(trim($input['ap_cta']     ?? ''));
    $amCta         = strtoupper(trim($input['am_cta']     ?? ''));
    $clabe         = trim($input['clabe']    ?? '');
    $idBanco       = intval($input['id_banco'] ?? 0);
    $activo        = intval($input['activo']   ?? 0);
    $clabeOriginal = trim($input['clabe_original'] ?? '');

    if (empty($clave) || empty($nombreCta) || strlen($clabe) !== 18 || $idBanco <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o CLABE inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rsEmp    = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp   = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $noEmpleado = intval($rowEmp['no_empleado']);

    $nombreEsc = mysqli_real_escape_string($conecta->conexion, $nombreCta);
    $apEsc     = mysqli_real_escape_string($conecta->conexion, $apCta);
    $amEsc     = mysqli_real_escape_string($conecta->conexion, $amCta);
    $clabeEsc  = mysqli_real_escape_string($conecta->conexion, $clabe);

    /* Si esta cuenta va a quedar activa, inactivar todas las demás del empleado primero */
    if ($activo == 1) {
        $conecta->consulta("UPDATE carobra.usuarios_banco SET activo=0 WHERE no_empleado={$noEmpleado}");
    }

    if (!empty($clabeOriginal)) {
        /* ── EDICIÓN ── */
        $clabeOrigEsc = mysqli_real_escape_string($conecta->conexion, $clabeOriginal);

        $ok = $conecta->consulta(
            "UPDATE carobra.usuarios_banco SET
                nombre_cta = '{$nombreEsc}',
                ap_cta     = '{$apEsc}',
                am_cta     = '{$amEsc}',
                id_banco   = {$idBanco},
                clabe      = '{$clabeEsc}',
                activo     = {$activo}
             WHERE no_empleado = {$noEmpleado} AND clabe = '{$clabeOrigEsc}'"
        );

        if (!$ok) {
            echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . $conecta->conexion->error]);
            exit;
        }

        echo json_encode(['ok' => true, 'msg' => 'Cuenta actualizada correctamente.']);
        exit;
    }

    /* ── ALTA ── */
    /* Si es la primera cuenta del empleado, forzar activa */
    $rsCount  = $conecta->consulta("SELECT COUNT(*) as cnt FROM carobra.usuarios_banco WHERE no_empleado={$noEmpleado}");
    $rowCount = $rsCount ? mysqli_fetch_assoc($rsCount) : ['cnt' => 0];
    if (intval($rowCount['cnt']) === 0) {
        $activo = 1;
    }

    if ($activo == 1) {
        $conecta->consulta("UPDATE carobra.usuarios_banco SET activo=0 WHERE no_empleado={$noEmpleado}");
    }

    $ok = $conecta->consulta(
        "INSERT INTO carobra.usuarios_banco (no_empleado, nombre_cta, ap_cta, am_cta, id_banco, clabe, activo)
         VALUES ({$noEmpleado}, '{$nombreEsc}', '{$apEsc}', '{$amEsc}', {$idBanco}, '{$clabeEsc}', {$activo})"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al insertar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Cuenta registrada correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: activarCuentaBancaria
   Marca una cuenta como activa e inactiva el resto (mismo empleado)
════════════════════════════════════════════════════════════ */
if ($action === 'activarCuentaBancaria') {

    $clave = trim($input['clave'] ?? '');
    $clabe = trim($input['clabe'] ?? '');

    if (empty($clave) || strlen($clabe) !== 18) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rsEmp    = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp   = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $noEmpleado = intval($rowEmp['no_empleado']);
    $clabeEsc   = mysqli_real_escape_string($conecta->conexion, $clabe);

    $conecta->consulta("UPDATE carobra.usuarios_banco SET activo=0 WHERE no_empleado={$noEmpleado}");
    $ok = $conecta->consulta("UPDATE carobra.usuarios_banco SET activo=1 WHERE no_empleado={$noEmpleado} AND clabe='{$clabeEsc}'");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al activar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Cuenta activada.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: eliminarCuentaBancaria
════════════════════════════════════════════════════════════ */
if ($action === 'eliminarCuentaBancaria') {

    $clave = trim($input['clave'] ?? '');
    $clabe = trim($input['clabe'] ?? '');

    if (empty($clave) || strlen($clabe) !== 18) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rsEmp    = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp   = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $noEmpleado = intval($rowEmp['no_empleado']);
    $clabeEsc   = mysqli_real_escape_string($conecta->conexion, $clabe);

    /* No permitir eliminar la cuenta activa */
    $rsActiva  = $conecta->consulta(
        "SELECT activo FROM carobra.usuarios_banco
         WHERE no_empleado={$noEmpleado} AND clabe='{$clabeEsc}' LIMIT 1"
    );
    $rowActiva = $rsActiva ? mysqli_fetch_assoc($rsActiva) : null;

    if ($rowActiva && intval($rowActiva['activo']) === 1) {
        echo json_encode(['ok' => false, 'msg' => 'No se puede eliminar la cuenta activa. Activa otra cuenta primero.']);
        exit;
    }

    $ok = $conecta->consulta("DELETE FROM carobra.usuarios_banco WHERE no_empleado={$noEmpleado} AND clabe='{$clabeEsc}'");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Cuenta eliminada correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getEstructuraEmpleado
   Estructura organizacional: gerencia/SC del broker, niveles,
   gerencias asignadas y brockers a su cargo.
════════════════════════════════════════════════════════════ */
if ($action === 'getEstructuraEmpleado') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    /* ── 1) estruc_empleados: gerencia, brocker, sc (sin "categoria", no existe) ── */
    $gerenciaDirecta = '';
    $brocker         = '';
    $socio           = '';

    $rsEstruc  = $conecta->consulta("SELECT * FROM carobra_2014.estruc_empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEstruc = $rsEstruc ? mysqli_fetch_assoc($rsEstruc) : null;

    if ($rowEstruc) {
        $gerenciaDirecta = isset($rowEstruc['gerencia']) ? utf8_encode((string)$rowEstruc['gerencia']) : '';
        $brocker         = isset($rowEstruc['brocker'])  ? utf8_encode((string)$rowEstruc['brocker'])  : '';
        $socio           = isset($rowEstruc['sc'])       ? utf8_encode((string)$rowEstruc['sc'])       : '';
    }

    /* ── 2) niveles ── */
    $niveles = [];
    $rsNiv   = $conecta->consulta(
        "SELECT DISTINCT n.id_nivel, n.desc_nivel FROM carobra_2014.niveles n
         INNER JOIN carobra_2014.usuarios_nivel_gerencia uni ON (n.id_nivel = uni.id_nivel)
         WHERE uni.clave='{$claveEsc}'
         UNION
         SELECT n.id_nivel, n.desc_nivel FROM carobra_2014.niveles n
         INNER JOIN carobra_2014.usuarios_nivel uni ON (n.id_nivel = uni.id_nivel)
         INNER JOIN carobra_2014.usuarios u ON (uni.clave = u.clave)
         WHERE uni.clave='{$claveEsc}'
         ORDER BY desc_nivel"
    );
    if ($rsNiv) {
        while ($row = mysqli_fetch_assoc($rsNiv)) {
            if (!empty($row['desc_nivel'])) {
                $niveles[] = [
                    'id_nivel'   => intval($row['id_nivel']),
                    'desc_nivel' => utf8_encode($row['desc_nivel']),
                ];
            }
        }
    }

    /* ── 3) gerencias asignadas (id_gerencia y pertenece reales,
            pertenece=1 marca la gerencia BASE del empleado) ── */
    $gerencias = [];
    $rsGer     = $conecta->consulta(
        "SELECT DISTINCT g.id_gerencia, g.gerencia, ug.pertenece
         FROM carobra_2014.gerencias g
         INNER JOIN carobra_2014.usuarios_gerencia ug ON (g.id_gerencia = ug.id_gerencia)
         WHERE ug.clave='{$claveEsc}'
         ORDER BY g.gerencia"
    );
    if ($rsGer) {
        while ($row = mysqli_fetch_assoc($rsGer)) {
            $gerencias[] = [
                'id_gerencia' => intval($row['id_gerencia']),
                'gerencia'    => utf8_encode($row['gerencia']),
                'pertenece'   => intval($row['pertenece']),
            ];
        }
    }

    /* ── 4) brockers a su cargo ── */
    $brockers = [];
    $rsBrk    = $conecta->consulta(
        "SELECT nombre FROM (
            SELECT DISTINCT nombre FROM carobra_2014.empl_usuarios eu
            INNER JOIN carobra_2014.usuarios_brocker ub ON (eu.no_empleado = ub.id_brocker)
            INNER JOIN carobra_2014.empleados e ON (ub.no_empleado = e.no_empleado)
            WHERE e.clave='{$claveEsc}'
            UNION
            SELECT DISTINCT CONCAT(nombre, ' (CC)') FROM carobra_2014.empl_usuarios eu
            INNER JOIN (SELECT * FROM carobra_2014.control_cambios WHERE tipo='nivel') cc
               ON (eu.no_empleado = cc.gerencia)
            INNER JOIN (SELECT no_empleado FROM carobra_2014.empl_usuarios WHERE clave='{$claveEsc}') eus
               ON (cc.no_empleado = eus.no_empleado)
         ) t
         GROUP BY nombre
         ORDER BY nombre"
    );
    if ($rsBrk) {
        while ($row = mysqli_fetch_assoc($rsBrk)) {
            if (!empty($row['nombre'])) $brockers[] = utf8_encode($row['nombre']);
        }
    }

    echo json_encode([
        'ok'    => true,
        'datos' => [
            'gerencia_directa' => $gerenciaDirecta,
            'brocker'          => $brocker,
            'socio'            => $socio,
            'niveles'          => $niveles,
            'gerencias'        => $gerencias,
            'brockers'         => $brockers,
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoNivel
   Lista completa de niveles para el typeahead de asignación
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoNivel') {

    $q   = trim($input['q'] ?? '');
    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    $where = strlen($q) >= 1 ? "WHERE desc_nivel LIKE '%{$esc}%'" : '';

    $rs    = $conecta->consulta(
        "SELECT id_nivel, desc_nivel FROM carobra_2014.niveles {$where} ORDER BY desc_nivel LIMIT 20"
    );
    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'     => intval($row['id_nivel']),
                'nombre' => utf8_encode($row['desc_nivel']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoGerencia
   Lista completa de gerencias para el typeahead de asignación
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoGerencia') {

    $q   = trim($input['q'] ?? '');
    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    $where = strlen($q) >= 1 ? "WHERE gerencia LIKE '%{$esc}%'" : '';

    $rs    = $conecta->consulta(
        "SELECT DISTINCT id_gerencia, gerencia FROM carobra_2014.gerencias {$where} ORDER BY gerencia LIMIT 20"
    );
    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'     => intval($row['id_gerencia']),
                'nombre' => utf8_encode($row['gerencia']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoBrocker
   Lista de brockers disponibles para asignar
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoBrocker') {

    $q   = trim($input['q'] ?? '');
    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));

    $where = strlen($q) >= 1 ? "WHERE brocker LIKE '%{$esc}%'" : '';

    $rs    = $conecta->consulta(
        "SELECT no_empleado, brocker
         FROM (SELECT DISTINCT brocker FROM carobra_2014.estructura_gerencia WHERE brocker != '') b
         INNER JOIN carobra_2014.empl_usuarios u ON (b.brocker = u.nombre)
         {$where}
         ORDER BY brocker LIMIT 20"
    );
    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'     => intval($row['no_empleado']),
                'nombre' => utf8_encode($row['brocker']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: asignarNivel
   INSERT INTO usuarios_nivel (clave, id_nivel)
════════════════════════════════════════════════════════════ */
if ($action === 'asignarNivel') {

    $clave   = trim($input['clave']    ?? '');
    $idNivel = intval($input['id_nivel'] ?? 0);

    if (empty($clave) || $idNivel <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    /* Evitar duplicados */
    $rsChk  = $conecta->consulta(
        "SELECT clave FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}' AND id_nivel={$idNivel} LIMIT 1"
    );
    if ($rsChk && mysqli_fetch_assoc($rsChk)) {
        echo json_encode(['ok' => true, 'msg' => 'Ese nivel ya estaba asignado.']);
        exit;
    }

    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.usuarios_nivel (clave, id_nivel) VALUES ('{$claveEsc}', {$idNivel})"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al asignar nivel: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Nivel asignado correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: asignarGerencia
   INSERT INTO usuarios_gerencia (clave, id_gerencia, pertenece=1)
════════════════════════════════════════════════════════════ */
if ($action === 'asignarGerencia') {

    $clave      = trim($input['clave']       ?? '');
    $idGerencia = intval($input['id_gerencia'] ?? 0);

    if (empty($clave) || $idGerencia <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    /* Evitar duplicados */
    $rsChk  = $conecta->consulta(
        "SELECT clave FROM carobra_2014.usuarios_gerencia WHERE clave='{$claveEsc}' AND id_gerencia={$idGerencia} LIMIT 1"
    );
    if ($rsChk && mysqli_fetch_assoc($rsChk)) {
        echo json_encode(['ok' => true, 'msg' => 'Esa gerencia ya estaba asignada.']);
        exit;
    }

    /* La gerencia nueva se agrega como NO base; la base actual se conserva tal cual.
       Si no hay ninguna gerencia previa, esta primera sí se vuelve base por default. */
    $rsCount  = $conecta->consulta("SELECT COUNT(*) as cnt FROM carobra_2014.usuarios_gerencia WHERE clave='{$claveEsc}'");
    $rowCount = $rsCount ? mysqli_fetch_assoc($rsCount) : ['cnt' => 0];
    $esPrimera = intval($rowCount['cnt']) === 0;
    $pertenece = $esPrimera ? 1 : 0;

    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.usuarios_gerencia (clave, id_gerencia, pertenece) VALUES ('{$claveEsc}', {$idGerencia}, {$pertenece})"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al asignar gerencia: ' . $conecta->conexion->error]);
        exit;
    }

    $msg = $esPrimera ? 'Gerencia asignada y marcada como base (es la primera).' : 'Gerencia agregada correctamente.';
    echo json_encode(['ok' => true, 'msg' => $msg]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: eliminarNivel
   DELETE FROM usuarios_nivel WHERE clave AND id_nivel
════════════════════════════════════════════════════════════ */
if ($action === 'eliminarNivel') {

    $clave   = trim($input['clave']    ?? '');
    $idNivel = intval($input['id_nivel'] ?? 0);

    if (empty($clave) || $idNivel <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $ok = $conecta->consulta(
        "DELETE FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}' AND id_nivel={$idNivel}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar nivel: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Nivel eliminado correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: eliminarGerencia
   DELETE FROM usuarios_gerencia WHERE clave AND id_gerencia
════════════════════════════════════════════════════════════ */
if ($action === 'eliminarGerencia') {

    $clave      = trim($input['clave']       ?? '');
    $idGerencia = intval($input['id_gerencia'] ?? 0);

    if (empty($clave) || $idGerencia <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $ok = $conecta->consulta(
        "DELETE FROM carobra_2014.usuarios_gerencia WHERE clave='{$claveEsc}' AND id_gerencia={$idGerencia}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar gerencia: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Gerencia eliminada correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: marcarGerenciaBase
   Pone pertenece=1 en la gerencia elegida y pertenece=0 en el resto
   (mismo clave) — solo puede haber una gerencia base a la vez.
════════════════════════════════════════════════════════════ */
if ($action === 'marcarGerenciaBase') {

    $clave      = trim($input['clave']       ?? '');
    $idGerencia = intval($input['id_gerencia'] ?? 0);

    if (empty($clave) || $idGerencia <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $conecta->consulta("UPDATE carobra_2014.usuarios_gerencia SET pertenece=0 WHERE clave='{$claveEsc}'");

    $ok = $conecta->consulta(
        "UPDATE carobra_2014.usuarios_gerencia SET pertenece=1 WHERE clave='{$claveEsc}' AND id_gerencia={$idGerencia}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al marcar como base: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Gerencia marcada como base correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: asignarBrocker
   INSERT INTO usuarios_brocker (no_empleado, id_brocker)
   no_empleado = empleado actual (resuelto desde su clave)
   id_brocker  = no_empleado del brocker elegido en el catálogo
════════════════════════════════════════════════════════════ */
if ($action === 'asignarBrocker') {

    $clave     = trim($input['clave']      ?? '');
    $idBrocker = intval($input['id_brocker'] ?? 0);

    if (empty($clave) || $idBrocker <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rsEmp    = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp   = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $noEmpleado = intval($rowEmp['no_empleado']);

    /* Evitar duplicados */
    $rsChk  = $conecta->consulta(
        "SELECT no_empleado FROM carobra_2014.usuarios_brocker
         WHERE no_empleado={$noEmpleado} AND id_brocker={$idBrocker} LIMIT 1"
    );
    if ($rsChk && mysqli_fetch_assoc($rsChk)) {
        echo json_encode(['ok' => true, 'msg' => 'Ese brocker ya estaba asignado.']);
        exit;
    }

    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.usuarios_brocker (no_empleado, id_brocker) VALUES ({$noEmpleado}, {$idBrocker})"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al asignar brocker: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Brocker asignado correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarPorCP
   Busca en carobra_2014.sepomex: regresa estado, municipio y
   todas las colonias que correspondan a ese código postal.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarPorCP') {

    $cp = trim($input['cp'] ?? '');
    $cp = preg_replace('/\D/', '', $cp);

    if (strlen($cp) !== 5) {
        echo json_encode(['ok' => false, 'msg' => 'CP inválido.']);
        exit;
    }

    /* La tabla sepomex es utf8mb4 (a diferencia del resto del sistema que
       trabaja en latin1), así que se fuerza el charset de la sesión antes
       de esta consulta y se evita utf8_encode() para no corromper acentos. */
    $conecta->conexion->set_charset('utf8mb4');

    $cpEsc = mysqli_real_escape_string($conecta->conexion, $cp);

    $rs = $conecta->consulta(
        "SELECT d_asenta, d_mnpio, d_estado
         FROM carobra_2014.sepomex
         WHERE d_codigo = '{$cpEsc}'
         ORDER BY d_asenta"
    );

    if (!$rs || mysqli_num_rows($rs) === 0) {
        $conecta->conexion->set_charset('latin1');
        echo json_encode(['ok' => true, 'colonias' => [], 'estado' => '', 'municipio' => '']);
        exit;
    }

    $colonias  = [];
    $estado    = '';
    $municipio = '';

    while ($row = mysqli_fetch_assoc($rs)) {
        $colonias[] = $row['d_asenta'];
        if (empty($estado))    $estado    = $row['d_estado'];
        if (empty($municipio)) $municipio = $row['d_mnpio'];
    }

    /* Regresar la conexión a latin1 para no afectar al resto de acciones
       de este mismo archivo que sí dependen de ese charset. */
    $conecta->conexion->set_charset('latin1');

    echo json_encode([
        'ok'        => true,
        'colonias'  => $colonias,
        'estado'    => $estado,
        'municipio' => $municipio,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getFinanzas
   Carga los campos financieros de carobra.empleados
════════════════════════════════════════════════════════════ */
if ($action === 'getFinanzas') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rs  = $conecta->consulta(
        "SELECT factura, fec_factura, regimen_fiscal, nap_factura, nap_gerencia, ase_bernal, costo_a
         FROM carobra.empleados
         WHERE clave='{$esc}'
         LIMIT 1"
    );
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    echo json_encode([
        'ok'    => true,
        'datos' => [
            'factura'        => $row ? intval($row['factura'] ?? 0)        : 0,
            'fec_factura'    => $row ? formatoFechaInput($row['fec_factura'] ?? '') : '',
            'regimen_fiscal' => $row ? utf8_encode($row['regimen_fiscal'] ?? '')    : '',
            'nap_factura'    => $row ? intval($row['nap_factura'] ?? 0)     : 0,
            'nap_gerencia'   => $row ? intval($row['nap_gerencia'] ?? 0)    : 0,
            'ase_bernal'     => $row ? intval($row['ase_bernal'] ?? 0)      : 0,
            'costo_a'        => $row ? $row['costo_a']                      : '',
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarFinanzas
   UPDATE a carobra.empleados con los campos financieros
════════════════════════════════════════════════════════════ */
if ($action === 'guardarFinanzas') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $factura      = intval($input['factura'] ?? 0);
    $fecFactura   = $factura ? formatoFechaBD($input['fec_factura'] ?? '') : '';
    $regimen      = strtoupper(trim($input['regimen_fiscal'] ?? ''));
    $napFactura   = intval($input['nap_factura']  ?? 0);
    $napGerencia  = intval($input['nap_gerencia'] ?? 0);
    $aseBernal    = intval($input['ase_bernal']   ?? 0);
    $costoA       = $aseBernal ? trim($input['costo_a'] ?? '0') : '0';

    if ($costoA === '' || !is_numeric($costoA)) $costoA = '0';

    $regimenEsc    = mysqli_real_escape_string($conecta->conexion, $regimen);
    $fecFacturaEsc = mysqli_real_escape_string($conecta->conexion, $fecFactura);

    $ok = $conecta->consulta(
        "UPDATE carobra.empleados SET
            factura        = {$factura},
            fec_factura    = '{$fecFacturaEsc}',
            regimen_fiscal = '{$regimenEsc}',
            nap_factura    = {$napFactura},
            nap_gerencia   = {$napGerencia},
            ase_bernal     = {$aseBernal},
            costo_a        = {$costoA}
         WHERE clave = '{$claveEsc}'"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar finanzas: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Información financiera actualizada correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getHistorialMovimientos
   Regresa el historial completo de ingreso/reingreso/baja de un
   empleado, ordenado del más reciente al más antiguo, junto con
   los contadores de cuántas veces ha entrado/reingresado/dado de baja.
════════════════════════════════════════════════════════════ */
if ($action === 'getHistorialMovimientos') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rs = $conecta->consulta(
        "SELECT tipo, fecha_evento, fecha_registro
         FROM carobra_2014.historial_movimientos
         WHERE clave='{$claveEsc}'
         ORDER BY fecha_registro DESC, id DESC"
    );

    $movimientos = [];
    $contadores  = ['ingreso' => 0, 'reingreso' => 0, 'baja' => 0];

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $movimientos[] = [
                'tipo'           => $row['tipo'],
                'fecha_evento'   => $row['fecha_evento'],
                'fecha_registro' => $row['fecha_registro'],
            ];
            if (isset($contadores[$row['tipo']])) {
                $contadores[$row['tipo']]++;
            }
        }
    }

    /* La línea de tiempo SIEMPRE debe reflejar la realidad actual del
       empleado en carobra.empleados (fec_ingreso, fec_reingreso,
       fec_baja), aunque esos eventos hayan ocurrido ANTES de que
       existiera la tabla historial_movimientos (que es nueva). Por
       cada uno de los 3 tipos, si ya existe un registro de ese tipo
       en historial_movimientos NO se duplica; si no existe, se agrega
       uno sintético con la fecha que ya tiene guardada en empleados. */
    $tiposYaRegistrados = [];
    foreach ($movimientos as $m) {
        $tiposYaRegistrados[$m['tipo']] = true;
    }

    $rsEmp  = $conecta->consulta(
        "SELECT fec_ingreso, fec_reingreso, fec_baja FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1"
    );
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if ($rowEmp) {
        $fechasEmpleado = [
            'ingreso'   => $rowEmp['fec_ingreso']   ?? '',
            'reingreso' => $rowEmp['fec_reingreso'] ?? '',
            'baja'      => $rowEmp['fec_baja']      ?? '',
        ];

        foreach ($fechasEmpleado as $tipo => $fecha) {
            if (empty($tipo)) continue;
            if (empty($fecha)) continue; /* sin esa fecha capturada, no hay nada que mostrar */
            if (isset($tiposYaRegistrados[$tipo])) continue; /* ya existe en historial_movimientos, no duplicar */

            $movimientos[] = [
                'tipo'           => $tipo,
                'fecha_evento'   => $fecha,
                'fecha_registro' => null, /* evento previo a la existencia de esta tabla */
            ];
            $contadores[$tipo]++;
            $tiposYaRegistrados[$tipo] = true;
        }
    }

    /* Si el empleado está INACTIVO (usuarios.activo = 0) pero no hay
       ningún evento de tipo "baja" registrado (ni real ni con fecha en
       empleados.fec_baja), se infiere igual que está dado de baja, sin
       fecha exacta. Esto cubre el caso de empleados marcados inactivos
       manualmente sin que nunca se haya capturado cuándo. */
    if (!isset($tiposYaRegistrados['baja'])) {
        $rsUsr  = $conecta->consulta("SELECT activo FROM carobra.usuarios WHERE clave='{$claveEsc}' LIMIT 1");
        $rowUsr = $rsUsr ? mysqli_fetch_assoc($rsUsr) : null;

        if ($rowUsr && intval($rowUsr['activo']) === 0) {
            $movimientos[] = [
                'tipo'           => 'baja',
                'fecha_evento'   => null,
                'fecha_registro' => null,
                'inferida'       => true,
            ];
            $contadores['baja']++;
        }
    }

    /* Reordenar todo cronológicamente (más reciente primero). Los
       eventos sin fecha_evento (inferidos) se mandan siempre al
       principio, ya que representan el estado actual del empleado. */
    usort($movimientos, function ($a, $b) {
        if (empty($a['fecha_evento']) && empty($b['fecha_evento'])) return 0;
        if (empty($a['fecha_evento'])) return -1;
        if (empty($b['fecha_evento'])) return 1;

        $fa = DateTime::createFromFormat('d/m/Y', $a['fecha_evento']);
        $fb = DateTime::createFromFormat('d/m/Y', $b['fecha_evento']);
        if (!$fa || !$fb) return 0;
        return $fb <=> $fa; /* descendente: más reciente primero */
    });

    echo json_encode([
        'ok'          => true,
        'movimientos' => $movimientos,
        'contadores'  => $contadores,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   HELPER: agrupa una lista ordenada de pagos por rachas
   consecutivas del mismo id (misma gerencia/brocker/sc). Cada
   racha se convierte en un bloque con fecha_desde/fecha_hasta;
   si la racha es de un solo pago, desde y hasta son iguales.
════════════════════════════════════════════════════════════ */
function agruparRachas($filas, $campoId) {
    $bloques = [];
    $actual  = null;

    foreach ($filas as $fila) {
        if ($actual !== null && $actual['id'] == $fila[$campoId]) {
            $actual['hasta'] = $fila['fec_pago'];
        } else {
            if ($actual !== null) $bloques[] = $actual;
            $actual = [
                'id'    => $fila[$campoId],
                'desde' => $fila['fec_pago'],
                'hasta' => $fila['fec_pago'],
            ];
        }
    }
    if ($actual !== null) $bloques[] = $actual;

    return $bloques;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getHistorialEstructura
   Movimientos de Gerencia / Brocker / SC a través de los años,
   leídos de las tablas anuales pagos_usuarios_gerencia_AÑO,
   pagos_usuarios_brocker_AÑO y pagos_usuarios_sc_AÑO (2021-2026).
   Se agrupan rachas consecutivas del mismo id: si la gerencia no
   cambió entre dos pagos, se muestra como un solo rango de fechas;
   si cambió, se corta el bloque y empieza uno nuevo.
════════════════════════════════════════════════════════════ */
if ($action === 'getHistorialEstructura') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $anios    = ['2021', '2022', '2023', '2024', '2025', '2026'];

    /* ── Helper interno: lee y ordena por fecha (ASC) los pagos de una clave
       en un set de tablas anuales, devolviendo fec_pago + el id solicitado ── */
    $leerSerie = function ($prefijoTabla, $campoId) use ($conecta, $claveEsc, $anios) {
        $unions = [];
        foreach ($anios as $a) {
            $unions[] = "SELECT fec_pago, {$campoId} FROM carobra.{$prefijoTabla}_{$a} WHERE clave='{$claveEsc}'";
        }
        $sql = implode(' UNION ALL ', $unions);
        $rs  = $conecta->consulta($sql);

        $filas = [];
        if ($rs) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $filas[] = $row;
            }
        }

        /* Ordenar por fecha real (fec_pago viene como dd/mm/yyyy) */
        usort($filas, function ($a, $b) {
            $fa = DateTime::createFromFormat('d/m/Y', $a['fec_pago']);
            $fb = DateTime::createFromFormat('d/m/Y', $b['fec_pago']);
            if (!$fa || !$fb) return 0;
            return $fa <=> $fb;
        });

        return $filas;
    };

    /* ── GERENCIA ── */
    $filasGerencia = $leerSerie('pagos_usuarios_gerencia', 'id_gerencia');
    $bloquesGer    = agruparRachas($filasGerencia, 'id_gerencia');

    $historialGerencia = [];
    foreach ($bloquesGer as $b) {
        $idGer = intval($b['id']);
        $nombreGer = '';
        $scGer     = [];
        $brockerGer= [];

        if ($idGer > 0) {
            $rsG = $conecta->consulta("SELECT gerencia FROM carobra_2014.gerencias WHERE id_gerencia={$idGer} LIMIT 1");
            $rowG = $rsG ? mysqli_fetch_assoc($rsG) : null;
            if ($rowG) $nombreGer = utf8_encode($rowG['gerencia']);

            /* SC y Brocker que correspondían a esta gerencia específica
               (no el vigente actual, sino el histórico de ese id_gerencia) */
            $rsEstrucGer = $conecta->consulta(
                "SELECT DISTINCT sc, brocker FROM carobra_2014.estruc_empleados WHERE id_gerencia={$idGer}"
            );
            if ($rsEstrucGer) {
                while ($rowEstrucGer = mysqli_fetch_assoc($rsEstrucGer)) {
                    if (!empty($rowEstrucGer['sc']))      $scGer[]      = utf8_encode($rowEstrucGer['sc']);
                    if (!empty($rowEstrucGer['brocker']))  $brockerGer[] = utf8_encode($rowEstrucGer['brocker']);
                }
            }
        }

        $historialGerencia[] = [
            'id'      => $idGer,
            'nombre'  => $nombreGer ?: ('Gerencia ' . $idGer),
            'desde'   => $b['desde'],
            'hasta'   => $b['hasta'],
            'sc'      => array_values(array_unique($scGer)),
            'brocker' => array_values(array_unique($brockerGer)),
        ];
    }

    /* ── BROCKER ── */
    $filasBrocker = $leerSerie('pagos_usuarios_brocker', 'id_brocker');
    $bloquesBrk   = agruparRachas($filasBrocker, 'id_brocker');

    $historialBrocker = [];
    foreach ($bloquesBrk as $b) {
        $idBrk = intval($b['id']);
        $nombreBrk = '';
        if ($idBrk > 0) {
            $rsB = $conecta->consulta("SELECT nombre FROM carobra_2014.empl_usuarios WHERE no_empleado={$idBrk} LIMIT 1");
            $rowB = $rsB ? mysqli_fetch_assoc($rsB) : null;
            if ($rowB) $nombreBrk = utf8_encode($rowB['nombre']);
        }
        $historialBrocker[] = [
            'id'     => $idBrk,
            'nombre' => $nombreBrk ?: ('Empleado ' . $idBrk),
            'desde'  => $b['desde'],
            'hasta'  => $b['hasta'],
        ];
    }

    /* ── SC (Socio Comercial) ── */
    $filasSc = $leerSerie('pagos_usuarios_sc', 'id_sc');
    $bloquesSc = agruparRachas($filasSc, 'id_sc');

    $historialSc = [];
    foreach ($bloquesSc as $b) {
        $idSc = intval($b['id']);
        $nombreSc = '';
        if ($idSc > 0) {
            $rsS = $conecta->consulta("SELECT nombre FROM carobra_2014.empl_usuarios WHERE no_empleado={$idSc} LIMIT 1");
            $rowS = $rsS ? mysqli_fetch_assoc($rsS) : null;
            if ($rowS) $nombreSc = utf8_encode($rowS['nombre']);
        }
        $historialSc[] = [
            'id'     => $idSc,
            'nombre' => $nombreSc ?: ('Empleado ' . $idSc),
            'desde'  => $b['desde'],
            'hasta'  => $b['hasta'],
        ];
    }

    /* ── Fallback + "Vigente" siempre primero: si alguna de las 3 listas
       quedó vacía (sin pagos registrados en las tablas anuales), se
       muestra el valor VIGENTE actual desde estruc_empleados como un
       bloque "Vigente", sin rango de fechas. Además, para Gerencia,
       ese bloque vigente SIEMPRE se inserta al PRINCIPIO del arreglo
       (con array_unshift), para que aparezca arriba de los bloques
       históricos sin depender del orden cronológico de los pagos. ── */
    $rsEstrucVig  = $conecta->consulta("SELECT gerencia, brocker, sc FROM carobra_2014.estruc_empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEstrucVig = $rsEstrucVig ? mysqli_fetch_assoc($rsEstrucVig) : null;

    if ($rowEstrucVig) {
        if (!empty($rowEstrucVig['gerencia'])) {
            $nombreGerVig = utf8_encode($rowEstrucVig['gerencia']);

            /* Evitar duplicar si por casualidad el último bloque histórico
               ya es exactamente esta misma gerencia con rango "Vigente" */
            $yaEstaVigenteArriba = !empty($historialGerencia)
                && $historialGerencia[0]['nombre'] === $nombreGerVig
                && $historialGerencia[0]['desde'] === 'Vigente';

            if (!$yaEstaVigenteArriba) {
                array_unshift($historialGerencia, [
                    'id'      => 0,
                    'nombre'  => $nombreGerVig,
                    'desde'   => 'Vigente',
                    'hasta'   => 'Vigente',
                    'sc'      => !empty($rowEstrucVig['sc'])      ? [utf8_encode($rowEstrucVig['sc'])]      : [],
                    'brocker' => !empty($rowEstrucVig['brocker']) ? [utf8_encode($rowEstrucVig['brocker'])] : [],
                ]);
            }
        }
        if (empty($historialBrocker) && !empty($rowEstrucVig['brocker'])) {
            $historialBrocker[] = [
                'id'     => 0,
                'nombre' => utf8_encode($rowEstrucVig['brocker']),
                'desde'  => 'Vigente',
                'hasta'  => 'Vigente',
            ];
        }
        if (empty($historialSc) && !empty($rowEstrucVig['sc'])) {
            $historialSc[] = [
                'id'     => 0,
                'nombre' => utf8_encode($rowEstrucVig['sc']),
                'desde'  => 'Vigente',
                'hasta'  => 'Vigente',
            ];
        }
    }

    echo json_encode([
        'ok'       => true,
        'gerencia' => $historialGerencia,
        'brocker'  => $historialBrocker,
        'sc'       => $historialSc,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoParentesco
   Lista fija de parentescos para el select
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoParentesco') {

    $rs    = $conecta->consulta("SELECT id_parentesco, parentesco FROM carobra_2014.parentescos ORDER BY id_parentesco");
    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'id'     => intval($row['id_parentesco']),
                'nombre' => utf8_encode($row['parentesco']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getContactosEmergencia
   Lista los contactos de emergencia de un empleado
════════════════════════════════════════════════════════════ */
if ($action === 'getContactosEmergencia') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rs = $conecta->consulta(
        "SELECT ce.id, ce.nombre, ce.numero, ce.id_parentesco, p.parentesco
         FROM carobra_2014.contactos_emergencia ce
         LEFT JOIN carobra_2014.parentescos p ON ce.id_parentesco = p.id_parentesco
         WHERE ce.clave='{$claveEsc}'
         ORDER BY ce.fecha_registro ASC"
    );

    $contactos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $contactos[] = [
                'id'            => intval($row['id']),
                'nombre'        => utf8_encode($row['nombre']),
                'numero'        => $row['numero'],
                'id_parentesco' => intval($row['id_parentesco']),
                'parentesco'    => utf8_encode($row['parentesco'] ?? ''),
            ];
        }
    }

    echo json_encode(['ok' => true, 'contactos' => $contactos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarContactoEmergencia
   Alta o edición de un contacto de emergencia
════════════════════════════════════════════════════════════ */
if ($action === 'guardarContactoEmergencia') {

    $id            = trim($input['id'] ?? '');
    $clave         = trim($input['clave'] ?? '');
    $nombre        = strtoupper(trim($input['nombre'] ?? ''));
    $numero        = trim($input['numero'] ?? '');
    $idParentesco  = intval($input['id_parentesco'] ?? 0);

    if (empty($clave) || empty($nombre) || empty($numero) || $idParentesco <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos.']);
        exit;
    }

    $claveEsc  = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
    $numeroEsc = mysqli_real_escape_string($conecta->conexion, $numero);

    if (!empty($id)) {
        /* ── EDICIÓN ── */
        $idInt = intval($id);

        $ok = $conecta->consulta(
            "UPDATE carobra_2014.contactos_emergencia SET
                nombre        = '{$nombreEsc}',
                numero        = '{$numeroEsc}',
                id_parentesco = {$idParentesco}
             WHERE id = {$idInt} AND clave = '{$claveEsc}'"
        );

        if (!$ok) {
            echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . $conecta->conexion->error]);
            exit;
        }

        echo json_encode(['ok' => true, 'msg' => 'Contacto actualizado correctamente.']);
        exit;
    }

    /* ── ALTA ── */
    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.contactos_emergencia (clave, nombre, numero, id_parentesco)
         VALUES ('{$claveEsc}', '{$nombreEsc}', '{$numeroEsc}', {$idParentesco})"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al insertar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Contacto registrado correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: eliminarContactoEmergencia
════════════════════════════════════════════════════════════ */
if ($action === 'eliminarContactoEmergencia') {

    $id    = intval($input['id'] ?? 0);
    $clave = trim($input['clave'] ?? '');

    if ($id <= 0 || empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $ok = $conecta->consulta(
        "DELETE FROM carobra_2014.contactos_emergencia WHERE id={$id} AND clave='{$claveEsc}'"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Contacto eliminado correctamente.']);
    exit;
}


if ($action === 'getInfoEmpleado') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) { echo json_encode(['ok'=>false,'msg'=>'Clave inválida.']); exit; }
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    /* 1) Último periodo */
    $anio    = date('Y');
    $rsFecha = $conecta->consulta("SELECT MAX(STR_TO_DATE(fec_pago,'%d/%m/%Y')) AS fecha FROM carobra.acum_pagos_otros_{$anio} WHERE 1");
    $rowFecha = $rsFecha ? mysqli_fetch_assoc($rsFecha) : null;
    $fechaISO = $rowFecha['fecha'] ?? null;

    if (!$fechaISO) {
        echo json_encode(['ok'=>true,'periodo'=>'Sin datos','insuficiencia'=>null,'facturas_pendientes'=>0]);
        exit;
    }

    $p = explode('-', $fechaISO);
    $fechaDMY  = $p[2].'/'.$p[1].'/'.$p[0];
    $motivo    = 'INSUFICIENCIA EN PAGOS '.$fechaDMY;
    $motivoEsc = mysqli_real_escape_string($conecta->conexion, $motivo);

    /* 2) Insuficiencia */
    $rsI  = $conecta->consulta("SELECT ajuste, motivo FROM carobra_2014.insuficiencias WHERE motivo='{$motivoEsc}' AND clave='{$claveEsc}' LIMIT 1");
    $rowI = $rsI ? mysqli_fetch_assoc($rsI) : null;

    /* 3) Facturas pendientes */
    $rsF  = $conecta->consulta("SELECT COUNT(*) AS v FROM carobra_2014.facturas WHERE estatus_fac < 4 AND clave='{$claveEsc}'");
    $rowF = $rsF ? mysqli_fetch_assoc($rsF) : null;

    /* ── 4) Préstamos con pagos pendientes ── */
    $rsAdeudos  = $conecta->consulta(
        "SELECT COUNT(DISTINCT p.id_prestamos) AS v
        FROM carobra_2014.prestamos p
        WHERE p.id_prestamos IN (
            SELECT DISTINCT id_prestamos FROM carobra_2014.prestamos_pagos
            WHERE clave='{$claveEsc}' AND (fec_pago IS NULL OR fec_pago = '')
        )"
    );
    $rowAdeudos  = $rsAdeudos ? mysqli_fetch_assoc($rsAdeudos) : null;
    $numAdeudos  = $rowAdeudos ? intval($rowAdeudos['v']) : 0;


    echo json_encode([
        'ok'                  => true,
        'periodo'             => $fechaDMY,
        'insuficiencia'       => $rowI ? ['motivo'=>utf8_encode($rowI['motivo']),'ajuste'=>$rowI['ajuste']] : null,
        'facturas_pendientes' => $rowF ? intval($rowF['v']) : 0,
        'adeudos_pendientes'  => $numAdeudos,

    ]);
    exit;
}


/* ════════════════════════════════════════════════════════════
   ACTION: getFacturasDetalle
════════════════════════════════════════════════════════════ */
if ($action === 'getFacturasDetalle') {
    $clave    = trim($input['clave'] ?? '');
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rs       = $conecta->consulta(
        "SELECT no_factura, nom_pdf, nom_xml, fec_fac, fec_subir, retenido, rfc
         FROM carobra_2014.facturas
         WHERE clave='{$claveEsc}' AND estatus_fac < 4
         ORDER BY fec_fac DESC"
    );
    $facturas = [];
    if ($rs) while ($row = mysqli_fetch_assoc($rs))
        $facturas[] = [
            'no_factura' => utf8_encode($row['no_factura'] ?? ''),
            'nom_pdf'    => utf8_encode($row['nom_pdf']    ?? ''),
            'nom_xml'    => utf8_encode($row['nom_xml']    ?? ''),
            'fec_fac'    => $row['fec_fac'],
            'fec_subir'  => $row['fec_subir'],
            'retenido'   => intval($row['retenido']),
            'rfc'        => $row['rfc'],
        ];
    echo json_encode(['ok' => true, 'facturas' => $facturas]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getAdeudos
   Préstamos con pagos pendientes (sin fec_pago)
════════════════════════════════════════════════════════════ */
if ($action === 'getAdeudos') {
    $clave    = trim($input['clave'] ?? '');
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rsPrestamos = $conecta->consulta(
        "SELECT p.id_prestamos, p.fec_prestamo, p.monto, p.no_pagos, p.monto_pago,
                p.motivo, p.fec_captura, p.periodo, p.detener_cobro, p.observa
         FROM carobra_2014.prestamos p
         WHERE p.id_prestamos IN (
             SELECT DISTINCT id_prestamos FROM carobra_2014.prestamos_pagos
             WHERE clave='{$claveEsc}' AND (fec_pago IS NULL OR fec_pago = '')
         )
         ORDER BY p.fec_prestamo DESC"
    );

    $prestamos = [];
    if ($rsPrestamos) {
        while ($row = mysqli_fetch_assoc($rsPrestamos)) {
            $idP = intval($row['id_prestamos']);

            /* ── Detalle de pagos (uno por uno) ── */
            $rsPagos = $conecta->consulta(
                "SELECT no_pago, fec_pago, monto, periodo
                 FROM carobra_2014.prestamos_pagos
                 WHERE id_prestamos={$idP}
                 ORDER BY no_pago ASC"
            );
            $pagos       = [];
            $pagados     = 0;
            $pendientes  = 0;
            $montoPagado = 0;

            if ($rsPagos) {
                while ($rowP = mysqli_fetch_assoc($rsPagos)) {
                    $tieneFecha = !empty(trim($rowP['fec_pago'] ?? ''));
                    if ($tieneFecha) {
                        $pagados++;
                        $montoPagado += floatval($rowP['monto']);
                    } else {
                        $pendientes++;
                    }
                    $pagos[] = [
                        'no_pago'  => intval($rowP['no_pago']),
                        'fec_pago' => $rowP['fec_pago'],
                        'monto'    => floatval($rowP['monto']),
                        'periodo'  => $rowP['periodo'],
                        'pagado'   => $tieneFecha,
                    ];
                }
            }

            $prestamos[] = [
                'id_prestamos'    => $idP,
                'fec_prestamo'    => $row['fec_prestamo'],
                'monto'           => floatval($row['monto']),
                'no_pagos'        => intval($row['no_pagos']),
                'monto_pago'      => floatval($row['monto_pago']),
                'motivo'          => utf8_encode($row['motivo']  ?? ''),
                'observa'         => utf8_encode($row['observa'] ?? ''),
                'fec_captura'     => $row['fec_captura'],
                'periodo'         => $row['periodo'],
                'detener_cobro'   => intval($row['detener_cobro']),
                'pagados'         => $pagados,
                'pendientes'      => $pendientes,
                'total_pagos_reg' => count($pagos),
                'monto_pagado'    => $montoPagado,
                'pagos'           => $pagos,
            ];
        }
    }
    echo json_encode(['ok' => true, 'prestamos' => $prestamos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   EXPEDIENTE DIGITAL
   Documentos (imágenes/PDF) por empleado, cargados desde una app
   móvil que aún no se desarrolla. Aquí solo se revisan: se ven,
   se aprueban o se rechazan (rechazar = "se quita" del expediente).
   Fuente de verdad: carobra_2014.expediente_digital.
════════════════════════════════════════════════════════════ */

/* ACTION: getExpedienteDigital */


if ($action === 'getExpedienteDigital') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rs = $conecta->consulta(
        "SELECT id, tipo_documento, nombre_archivo, ruta_archivo, tipo_archivo,
                estatus, comentario, fecha_carga, fecha_revision, revisado_por
         FROM carobra_2014.expediente_digital
         WHERE clave='{$claveEsc}'
         ORDER BY FIELD(estatus, 0, 2, 1), fecha_carga DESC"
    );

    $documentos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $documentos[] = [
                'id'             => intval($row['id']),
                'tipo_documento' => utf8_encode($row['tipo_documento']),
                'nombre_archivo' => utf8_encode($row['nombre_archivo']),
                'ruta_archivo'   => $row['ruta_archivo'],
                'tipo_archivo'   => $row['tipo_archivo'],
                'estatus'        => intval($row['estatus']),
                'comentario'     => utf8_encode($row['comentario'] ?? ''),
                'fecha_carga'    => $row['fecha_carga'],
                'fecha_revision' => $row['fecha_revision'],
                'revisado_por'   => $row['revisado_por'],
            ];
        }
    }

    echo json_encode(['ok' => true, 'documentos' => $documentos]);
    exit;
}


/* ACTION: aprobarDocumentoExpediente */
if ($action === 'aprobarDocumentoExpediente') {

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Documento inválido.']);
        exit;
    }

    $revisorEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($_SESSION['nombre'] ?? ''));

    $ok = $conecta->consulta(
        "UPDATE carobra_2014.expediente_digital
         SET estatus = 1, comentario = '', fecha_revision = NOW(), revisado_por = '{$revisorEsc}'
         WHERE id = {$id}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al aprobar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Documento aprobado correctamente.']);
    exit;
}

/* ACTION: rechazarDocumentoExpediente
   Marca el documento como rechazado (estatus=2), con un comentario
   opcional explicando por qué "se quita". No se borra físicamente el
   registro (queda como auditoría), pero deja de contar como vigente. */
if ($action === 'rechazarDocumentoExpediente') {

    $id         = intval($input['id'] ?? 0);
    $comentario = trim($input['comentario'] ?? '');

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Documento inválido.']);
        exit;
    }

    $comentarioEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($comentario));
    $revisorEsc    = mysqli_real_escape_string($conecta->conexion, utf8_decode($_SESSION['nombre'] ?? ''));

    $ok = $conecta->consulta(
        "UPDATE carobra_2014.expediente_digital
         SET estatus = 2, comentario = '{$comentarioEsc}', fecha_revision = NOW(), revisado_por = '{$revisorEsc}'
         WHERE id = {$id}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al rechazar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Documento rechazado.']);
    exit;
}

/* ACTION: eliminarDocumentoExpediente
   Borra el registro por completo (uso: limpiar un documento rechazado
   que ya no hace falta conservar). Intenta borrar también el archivo
   físico si ruta_archivo apunta a una ruta local existente; si es una
   URL externa o ya no existe, simplemente se ignora. */
if ($action === 'eliminarDocumentoExpediente') {

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Documento inválido.']);
        exit;
    }

    $rsDoc  = $conecta->consulta("SELECT ruta_archivo FROM carobra_2014.expediente_digital WHERE id={$id} LIMIT 1");
    $rowDoc = $rsDoc ? mysqli_fetch_assoc($rsDoc) : null;

    $ok = $conecta->consulta("DELETE FROM carobra_2014.expediente_digital WHERE id={$id}");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar: ' . $conecta->conexion->error]);
        exit;
    }

    if ($rowDoc && !empty($rowDoc['ruta_archivo']) && strpos($rowDoc['ruta_archivo'], 'http') !== 0) {
        $rutaLocal = ltrim($rowDoc['ruta_archivo'], '/');
        if (file_exists($rutaLocal)) {
            @unlink($rutaLocal);
        }
    }

    echo json_encode(['ok' => true, 'msg' => 'Documento eliminado correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   PERMISOS DE PESTAÑAS
   Controla qué pestañas del panel de empleado puede VER cada
   usuario del sistema (Usuario, Datos generales, Datos bancarios,
   Estruc empleados, Finanzas, Historial, Contac. Emergencia, Info,
   Expediente Digital).

   Fuente de verdad: carobra_2014.permisos_pestanas (clave, pestana).
   El nivel (carobra_2014.usuarios_nivel) solo se usa como atajo para
   escribir permisos en LOTE a todos los usuarios de ese nivel en el
   momento en que se aplica; después cada usuario puede ajustarse de
   forma individual sin afectar a los demás de su nivel.

   Gestionar permisos (leer y escribir) está restringido a usuarios
   del nivel Super Usuario (id_nivel = 12). Esto se valida aquí en el
   servidor, no solo en el JS, porque el JS únicamente oculta la UI.
════════════════════════════════════════════════════════════ */

$PESTANAS_VALIDAS = [
    'usuario',
    'datos_generales',
    'datos_bancarios',
    'estructura',
    'finanzas',
    'historial',
    'contactos_emergencia',
    'info',
    'expediente_digital',
];

/* Resuelve si el usuario de la sesión actual ($_SESSION['nombre'])
   pertenece al nivel Super Usuario (id_nivel = 12). Se apoya en las
   mismas tablas que ya usa el resto del sistema (carobra.usuarios
   para resolver nombre->clave, carobra_2014.usuarios_nivel para el
   nivel). No confía en nada que venga del cliente. */
function esSuperUsuarioSesion($conecta) {
    $nombreSesion = $_SESSION['nombre'] ?? '';
    if (empty($nombreSesion)) return false;

    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreSesion));
    $rsU = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE nombre='{$nombreEsc}' LIMIT 1");
    $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;
    if (!$rowU || empty($rowU['clave'])) return false;

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $rowU['clave']);
    $rsN = $conecta->consulta(
        "SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}' AND id_nivel=12 LIMIT 1"
    );
    return ($rsN && mysqli_fetch_assoc($rsN)) ? true : false;
}

/* Filtra un array de pestañas dejando solo las que están en el
   catálogo válido (evita que alguien mande claves inventadas) */
function filtrarPestanasValidas($pestanas, $catalogo) {
    if (!is_array($pestanas)) return [];
    return array_values(array_intersect(array_unique($pestanas), $catalogo));
}

/* ════════════════════════════════════════════════════════════
   ACTION: getPermisosPestanas
   Regresa las pestañas permitidas para un empleado (clave) puntual.
   Usado por la sección "Permisos individuales" del empleado
   seleccionado dentro de la pestaña Permisos.
════════════════════════════════════════════════════════════ */
if ($action === 'getPermisosPestanas') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para ver esta información.']);
        exit;
    }

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rs = $conecta->consulta("SELECT pestana FROM carobra_2014.permisos_pestanas WHERE clave='{$claveEsc}'");

    $pestanas = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $pestanas[] = $row['pestana'];
        }
    }

    echo json_encode(['ok' => true, 'pestanas' => $pestanas]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarPermisosPestanas
   Reemplaza por completo las pestañas permitidas de UN empleado
   (edición individual, no toca a nadie más de su nivel).
════════════════════════════════════════════════════════════ */
if ($action === 'guardarPermisosPestanas') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para modificar permisos.']);
        exit;
    }

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $pestanas = filtrarPestanasValidas($input['pestanas'] ?? [], $PESTANAS_VALIDAS);
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $conecta->consulta("DELETE FROM carobra_2014.permisos_pestanas WHERE clave='{$claveEsc}'");

    foreach ($pestanas as $pestana) {
        $pestanaEsc = mysqli_real_escape_string($conecta->conexion, $pestana);
        $conecta->consulta(
            "INSERT INTO carobra_2014.permisos_pestanas (clave, pestana) VALUES ('{$claveEsc}', '{$pestanaEsc}')"
        );
    }

    echo json_encode(['ok' => true, 'msg' => 'Permisos de este empleado actualizados correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getUsuariosPorNivel
   Lista los usuarios que actualmente tienen asignado un nivel dado.
   Solo informativo, para que el Super Usuario vea a quién va a
   afectar antes de aplicar el lote.
════════════════════════════════════════════════════════════ */
if ($action === 'getUsuariosPorNivel') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para ver esta información.']);
        exit;
    }

    $idNivel = intval($input['id_nivel'] ?? 0);
    if ($idNivel <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Nivel inválido.']);
        exit;
    }

    $rs = $conecta->consulta(
        "SELECT u.clave, u.nombre
         FROM carobra_2014.usuarios_nivel un
         INNER JOIN carobra.usuarios u ON (u.clave = un.clave)
         WHERE un.id_nivel = {$idNivel}
         ORDER BY u.nombre"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'clave'  => utf8_encode($row['clave']),
                'nombre' => utf8_encode($row['nombre']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: aplicarPermisosPorNivel
   Escritura en LOTE: reemplaza las pestañas permitidas de TODOS
   los usuarios que en este momento tienen el nivel indicado. Es
   una fotografía puntual — no crea una regla dinámica por nivel,
   así que ajustes individuales posteriores sobre uno de esos
   usuarios no afectan al resto del nivel ni se revierten solos.
════════════════════════════════════════════════════════════ */
if ($action === 'aplicarPermisosPorNivel') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para modificar permisos.']);
        exit;
    }

    $idNivel = intval($input['id_nivel'] ?? 0);
    if ($idNivel <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Nivel inválido.']);
        exit;
    }

    $pestanas = filtrarPestanasValidas($input['pestanas'] ?? [], $PESTANAS_VALIDAS);

    $rsClaves = $conecta->consulta(
        "SELECT clave FROM carobra_2014.usuarios_nivel WHERE id_nivel={$idNivel}"
    );

    $claves = [];
    if ($rsClaves) {
        while ($row = mysqli_fetch_assoc($rsClaves)) {
            $claves[] = $row['clave'];
        }
    }

    if (empty($claves)) {
        echo json_encode(['ok' => false, 'msg' => 'Ningún usuario tiene ese nivel asignado todavía.']);
        exit;
    }

    foreach ($claves as $claveUsuario) {
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $claveUsuario);
        $conecta->consulta("DELETE FROM carobra_2014.permisos_pestanas WHERE clave='{$claveEsc}'");

        foreach ($pestanas as $pestana) {
            $pestanaEsc = mysqli_real_escape_string($conecta->conexion, $pestana);
            $conecta->consulta(
                "INSERT INTO carobra_2014.permisos_pestanas (clave, pestana) VALUES ('{$claveEsc}', '{$pestanaEsc}')"
            );
        }
    }

    $total = count($claves);
    echo json_encode([
        'ok'  => true,
        'msg' => "Permisos aplicados a {$total} usuario(s) de este nivel.",
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getTodosPermisos
   Lista todos los usuarios que tienen AL MENOS una pestaña
   permitida ahora mismo, con el detalle de cuáles son. Es el
   listado de "quién tiene qué" que se muestra en el modal de
   Permisos para poder revisar y editar de un vistazo.
════════════════════════════════════════════════════════════ */
if ($action === 'getTodosPermisos') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para ver esta información.']);
        exit;
    }

    $rs = $conecta->consulta(
        "SELECT pp.clave, u.nombre, u.activo, pp.pestana
         FROM carobra_2014.permisos_pestanas pp
         INNER JOIN carobra.usuarios u ON u.clave = pp.clave
         ORDER BY u.nombre, pp.pestana"
    );

    $usuarios = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $clave = $row['clave'];
            if (!isset($usuarios[$clave])) {
                $usuarios[$clave] = [
                    'clave'    => utf8_encode($clave),
                    'nombre'   => utf8_encode($row['nombre']),
                    'activo'   => intval($row['activo']),
                    'pestanas' => [],
                ];
            }
            $usuarios[$clave]['pestanas'][] = $row['pestana'];
        }
    }

    echo json_encode(['ok' => true, 'datos' => array_values($usuarios)]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   PERMISOS POR PÁGINA (módulos → páginas)
   Sistema independiente del de permisos de pestañas. Controla
   acceso página por página a otros módulos del sistema.
   Fuente de verdad: carobra_2014.permisos (clave_usuario, id_pagina).
   Solo gestionable por Super Usuario (id_nivel = 12).
════════════════════════════════════════════════════════════ */

/* ACTION: getCatalogoModulosPaginas */
if ($action === 'getCatalogoModulosPaginas') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para ver esta información.']);
        exit;
    }

    $modulos = [];
    $rsMod = $conecta->consulta("SELECT id_modulo, modulo FROM carobra_2014.modulos ORDER BY modulo");
    if ($rsMod) {
        while ($row = mysqli_fetch_assoc($rsMod)) {
            $modulos[intval($row['id_modulo'])] = [
                'id_modulo' => intval($row['id_modulo']),
                'modulo'    => utf8_encode($row['modulo']),
                'paginas'   => [],
            ];
        }
    }

    $rsPag = $conecta->consulta(
        "SELECT id_modulo, id_pagina, nom_pagina, pagina FROM carobra_2014.paginas ORDER BY id_modulo, nom_pagina"
    );
    if ($rsPag) {
        while ($row = mysqli_fetch_assoc($rsPag)) {
            $idMod = intval($row['id_modulo']);
            if (!isset($modulos[$idMod])) {
                $modulos[$idMod] = ['id_modulo' => $idMod, 'modulo' => 'Módulo ' . $idMod, 'paginas' => []];
            }
            $modulos[$idMod]['paginas'][] = [
                'id_pagina'  => intval($row['id_pagina']),
                'nom_pagina' => utf8_encode($row['nom_pagina']),
                'pagina'     => utf8_encode($row['pagina']),
            ];
        }
    }

    echo json_encode(['ok' => true, 'modulos' => array_values($modulos)]);
    exit;
}

/* ACTION: getPermisosPaginas */
if ($action === 'getPermisosPaginas') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para ver esta información.']);
        exit;
    }

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $rs = $conecta->consulta("SELECT id_pagina FROM carobra_2014.permisos WHERE clave_usuario='{$claveEsc}'");

    $paginas = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $paginas[] = intval($row['id_pagina']);
        }
    }

    echo json_encode(['ok' => true, 'paginas' => $paginas]);
    exit;
}

/* ACTION: guardarPermisosPaginas */
if ($action === 'guardarPermisosPaginas') {

    if (!esSuperUsuarioSesion($conecta)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para modificar permisos.']);
        exit;
    }

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $paginas = $input['paginas'] ?? [];
    if (!is_array($paginas)) $paginas = [];
    $paginas = array_values(array_unique(array_map('intval', $paginas)));

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $conecta->consulta("DELETE FROM carobra_2014.permisos WHERE clave_usuario='{$claveEsc}'");

    foreach ($paginas as $idPagina) {
        if ($idPagina <= 0) continue;
        $conecta->consulta(
            "INSERT INTO carobra_2014.permisos (clave_usuario, id_pagina) VALUES ('{$claveEsc}', {$idPagina})"
        );
    }

    echo json_encode(['ok' => true, 'msg' => 'Permisos de páginas actualizados correctamente.']);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);