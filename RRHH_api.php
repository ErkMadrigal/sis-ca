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

        /* Nombre separado: el mismo botón "Guardar" de edición ahora
           también guarda esto (ya no hay un botón aparte en el
           formulario, para no confundir a quien captura); si vienen
           estos campos en el payload se guardan aquí de una vez, junto
           con el resto de la edición. */
        $nombresIn = trim($input['nombres'] ?? '');
        $apPatIn   = trim($input['apellido_paterno'] ?? '');
        $apMatIn   = trim($input['apellido_materno'] ?? '');
        if ($nombresIn !== '' || $apPatIn !== '' || $apMatIn !== '') {
            guardarNombreSeparadoBD($conecta, $id, $nombresIn, $apPatIn, $apMatIn);
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

    /* Nombre separado (opcional): si el formulario de alta ya viene
       con Nombres / Apellido paterno / Apellido materno capturados
       a mano, se guardan de una vez en la tabla temporal — no se
       toca carobra.usuarios para nada. */
    $nombresIn = trim($input['nombres'] ?? '');
    $apPatIn   = trim($input['apellido_paterno'] ?? '');
    $apMatIn   = trim($input['apellido_materno'] ?? '');
    if ($nombresIn !== '' || $apPatIn !== '' || $apMatIn !== '') {
        guardarNombreSeparadoBD($conecta, $clave, $nombresIn, $apPatIn, $apMatIn);
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
   ACTION: importarEmpleadosMasivo
   Alta masiva de empleados desde un xlsx parseado en el navegador
   (mismo patrón que qualitas_api.php: el archivo se lee con SheetJS
   del lado del cliente, aquí solo llega el arreglo de filas ya
   parseadas -- $input['filas']).

   Por cada fila se repite, en el mismo orden, todo lo que hace el
   alta individual (guardarEmpleado arriba) más los datos extra que
   sí trae el xlsx y el formulario de alta normal no captura de una
   sola vez (banco, nivel, estructura/gerencia, contacto de
   emergencia). Los ÚNICOS datos obligatorios por fila son
   nombres + apellido paterno + apellido materno + correo -- clave y
   contraseña se generan solas, igual que en el alta individual; todo
   lo demás es opcional y si falta o no hace match contra su catálogo
   (banco/nivel/estructura) simplemente se omite ESE dato puntual sin
   tirar toda la fila -- el empleado se da de alta de todas formas.

   nivel/estructura (gerencia) se buscan por NOMBRE exacto (sin
   importar mayúsculas/espacios) contra los catálogos reales
   (niveles.desc_nivel / gerencias.gerencia) -- si el xlsx trae un
   nombre que no existe en el catálogo, se reporta como advertencia
   de esa fila en vez de adivinar o crear uno nuevo.
════════════════════════════════════════════════════════════ */
if ($action === 'importarEmpleadosMasivo') {

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'usuario')) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para dar de alta empleados.']);
        exit;
    }

    set_time_limit(600); // manda un correo por fila -- con muchas filas puede tardar

    $filas = $input['filas'] ?? [];
    if (!is_array($filas) || empty($filas)) {
        echo json_encode(['ok' => false, 'msg' => 'No se recibieron filas para procesar.']);
        exit;
    }

    /* ── Precargar catálogos una sola vez (nombre normalizado -> id),
       en vez de una consulta por fila -- con archivos grandes esto
       importa. ── */
    function normalizarNombreCatalogoRRHH($texto) {
        $texto = mb_strtoupper(trim((string) $texto), 'UTF-8');
        $texto = preg_replace('/\s+/', ' ', $texto);
        return $texto;
    }

    $catalogoNiveles = [];
    $rsN = $conecta->consulta("SELECT id_nivel, desc_nivel FROM carobra_2014.niveles");
    if ($rsN) while ($r = mysqli_fetch_assoc($rsN)) {
        $catalogoNiveles[normalizarNombreCatalogoRRHH(utf8_encode($r['desc_nivel']))] = intval($r['id_nivel']);
    }

    $catalogoGerencias = [];
    $rsG = $conecta->consulta("SELECT DISTINCT id_gerencia, gerencia FROM carobra_2014.gerencias");
    if ($rsG) while ($r = mysqli_fetch_assoc($rsG)) {
        $catalogoGerencias[normalizarNombreCatalogoRRHH(utf8_encode($r['gerencia']))] = intval($r['id_gerencia']);
    }

    $catalogoBancos = [];
    $rsB = $conecta->consulta("SELECT id_banco, banco FROM carobra.bancos");
    if ($rsB) while ($r = mysqli_fetch_assoc($rsB)) {
        $catalogoBancos[normalizarNombreCatalogoRRHH(utf8_encode($r['banco']))] = intval($r['id_banco']);
    }

    /* Parentesco: el xlsx no trae parentesco (solo nombre + teléfono
       del contacto), así que se usa un default razonable -- "OTRO" si
       existe en el catálogo, si no el primero que haya. Si el
       catálogo está vacío de plano, el contacto simplemente no se
       guarda (se reporta como advertencia). */
    $idParentescoDefault = 0;
    $rsP = $conecta->consulta("SELECT id_parentesco, parentesco FROM carobra_2014.parentescos ORDER BY id_parentesco");
    if ($rsP) {
        while ($r = mysqli_fetch_assoc($rsP)) {
            if ($idParentescoDefault === 0) $idParentescoDefault = intval($r['id_parentesco']);
            if (stripos((string) $r['parentesco'], 'OTRO') !== false) {
                $idParentescoDefault = intval($r['id_parentesco']);
                break;
            }
        }
    }

    /* Convierte lo que venga en la celda de fecha -- ya sea texto
       'dd/mm/yyyy', 'yyyy-mm-dd' o un serial de Excel que el parseo
       del navegador no haya normalizado -- a 'dd/mm/yyyy' (formato de
       carobra.empleados.fec_ingreso). Si no se puede leer, regresa
       hoy: un empleado siempre necesita alguna fec_ingreso, igual que
       en el alta individual (que la pone en automático). */
    function normalizarFechaIngresoRRHH($valor) {
        $valor = trim((string) $valor);
        if ($valor === '') return date('d/m/Y');

        if (preg_match('#^(\d{1,2})[/\-](\d{1,2})[/\-](\d{4})$#', $valor, $m)) {
            return sprintf('%02d/%02d/%04d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }
        if (preg_match('#^(\d{4})[/\-](\d{1,2})[/\-](\d{1,2})$#', $valor, $m)) {
            return sprintf('%02d/%02d/%04d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }
        // Serial de Excel (días desde 1899-12-30), por si el navegador lo mandó crudo
        if (is_numeric($valor)) {
            $ts = ((int) $valor - 25569) * 86400;
            $fecha = gmdate('d/m/Y', $ts);
            if ($fecha !== false) return $fecha;
        }
        return date('d/m/Y');
    }

    /* "SI"/"SÍ"/"1"/"X"/"TRUE" (sin importar mayúsculas/espacios) -> 1, cualquier otra cosa -> 0 */
    function esSiRRHH($valor) {
        $v = mb_strtoupper(trim((string) $valor), 'UTF-8');
        return in_array($v, ['SI', 'SÍ', '1', 'X', 'TRUE', 'VERDADERO'], true) ? 1 : 0;
    }

    function htmlCorreoBienvenidaMasivaRRHH($clave, $password, $primerNombre) {
        return '
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
    }

    $usuarioSesion = claveSesionActual($conecta);

    $procesados      = 0;
    $insertados      = 0;
    $omitidos        = 0;
    $omitidosDetalle = [];
    $resultados      = []; // uno por empleado insertado, con sus advertencias puntuales

    foreach ($filas as $i => $fila) {
        $procesados++;
        $numFila = $i + 2; // fila 1 = encabezados del xlsx

        $nombres  = strtoupper(trim($fila['nombres'] ?? ''));
        $apPat    = strtoupper(trim($fila['apellido_paterno'] ?? ''));
        $apMat    = strtoupper(trim($fila['apellido_materno'] ?? ''));
        $correo   = trim($fila['correo'] ?? '');

        if ($nombres === '' || $apPat === '' || $apMat === '' || $correo === '') {
            $omitidos++;
            $omitidosDetalle[] = ['fila' => $numFila, 'motivo' => 'Faltan datos mínimos (Nombre(s), Apellido Paterno, Apellido Materno o Correo).'];
            continue;
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $omitidos++;
            $omitidosDetalle[] = ['fila' => $numFila, 'motivo' => "Correo inválido: {$correo}"];
            continue;
        }

        $nombreCompleto = trim("{$nombres} {$apPat} {$apMat}");
        $correoEsc      = mysqli_real_escape_string($conecta->conexion, $correo);

        $rsDup = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE email='{$correoEsc}' LIMIT 1");
        if ($rsDup && mysqli_fetch_assoc($rsDup)) {
            $omitidos++;
            $omitidosDetalle[] = ['fila' => $numFila, 'motivo' => "El correo {$correo} ya está registrado."];
            continue;
        }

        $advertencias = [];

        $clave     = claveUnica($nombreCompleto, $conecta);
        $claveEsc  = mysqli_real_escape_string($conecta->conexion, $clave);
        $password  = generarPwd();
        $contEsc   = mysqli_real_escape_string($conecta->conexion, substr($password, 0, 10));

        $emailInst    = trim($fila['email_institucional'] ?? '');
        $emailInstEsc = mysqli_real_escape_string($conecta->conexion, $emailInst);
        $nombreEsc    = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreCompleto));

        $okUsr = $conecta->consulta(
            "INSERT INTO carobra.usuarios (clave, nombre, contrasena, activo, email, email_inst, pwd, afore, seg_inver, seg_educa)
             VALUES ('{$claveEsc}', '{$nombreEsc}', '{$contEsc}', 1, '{$correoEsc}', '{$emailInstEsc}', '{$contEsc}', 0, 0, 0)"
        );
        if (!$okUsr) {
            $omitidos++;
            $omitidosDetalle[] = ['fila' => $numFila, 'motivo' => 'Error al crear el usuario: ' . $conecta->conexion->error];
            continue;
        }

        $rsMax   = $conecta->consulta("SELECT IFNULL(MAX(no_empleado), 0) + 1 AS nuevo FROM carobra.empleados");
        $rowMax  = $rsMax ? mysqli_fetch_assoc($rsMax) : null;
        $nuevoNo = $rowMax ? intval($rowMax['nuevo']) : 1;

        $fecIngreso = normalizarFechaIngresoRRHH($fila['fecha_ingreso'] ?? '');
        $telefono   = trim($fila['telefono'] ?? '');
        $rfc        = strtoupper(trim($fila['rfc'] ?? ''));
        $curp       = strtoupper(trim($fila['curp'] ?? ''));
        $nss        = trim($fila['nss'] ?? '');
        $regimen    = strtoupper(trim($fila['regimen_fiscal'] ?? ''));
        $factura     = esSiRRHH($fila['factura'] ?? '');
        $napFactura  = esSiRRHH($fila['nap_factura'] ?? '');
        $napGerencia = esSiRRHH($fila['nap_gerencia'] ?? '');
        $aseBernal   = esSiRRHH($fila['ase_bernal'] ?? '');

        $telEsc      = mysqli_real_escape_string($conecta->conexion, $telefono);
        $rfcEsc      = mysqli_real_escape_string($conecta->conexion, $rfc);
        $curpEsc     = mysqli_real_escape_string($conecta->conexion, $curp);
        $nssEsc      = mysqli_real_escape_string($conecta->conexion, $nss);
        $regimenEsc  = mysqli_real_escape_string($conecta->conexion, $regimen);
        $fecIngresoEsc = mysqli_real_escape_string($conecta->conexion, $fecIngreso);

        $okEmp = $conecta->consulta(
            "INSERT INTO carobra.empleados
                (no_empleado, clave, fec_ingreso, fec_reingreso, fec_baja,
                 domicilio, telefono, celular, factura, fec_factura,
                 regimen_fiscal, recluta, deportenis, no_prom_dep, rfc,
                 curp, nss, nap_factura, nap_gerencia, ase_bernal,
                 nae, categoria, fec_nap, cen_cost, no_contpaq,
                 costo_a, staff, lista_negra, jefe_inmediato)
             VALUES
                ({$nuevoNo}, '{$claveEsc}', '{$fecIngresoEsc}', '', '',
                 '', '{$telEsc}', '', {$factura}, '',
                 '{$regimenEsc}', '', 0, '', '{$rfcEsc}',
                 '{$curpEsc}', '{$nssEsc}', {$napFactura}, {$napGerencia}, {$aseBernal},
                 '', 0, '', '', '',
                 0, 0, 0, '')"
        );
        if (!$okEmp) {
            $advertencias[] = 'No se pudo completar carobra.empleados: ' . $conecta->conexion->error;
        }

        /* ── Banco (opcional) ── */
        $clabe = preg_replace('/\s+/', '', (string) ($fila['clabe'] ?? ''));
        $bancoTxt = trim($fila['banco'] ?? '');
        if ($clabe !== '' || $bancoTxt !== '') {
            if (strlen($clabe) !== 18 || !ctype_digit($clabe)) {
                $advertencias[] = 'CLABE inválida (debe tener 18 dígitos) -- no se guardó la cuenta bancaria.';
            } else {
                $idBanco = $catalogoBancos[normalizarNombreCatalogoRRHH($bancoTxt)] ?? 0;
                if ($idBanco <= 0) {
                    $advertencias[] = "Banco \"{$bancoTxt}\" no encontrado en el catálogo -- no se guardó la cuenta bancaria.";
                } else {
                    $nombreCta = sugerirNombreSeparado(trim($fila['nombre_cuentahabiente'] ?? $nombreCompleto));
                    $ctaEsc = mysqli_real_escape_string($conecta->conexion, $nombreCta['nombres']);
                    $apEsc  = mysqli_real_escape_string($conecta->conexion, $nombreCta['apellido_paterno']);
                    $amEsc  = mysqli_real_escape_string($conecta->conexion, $nombreCta['apellido_materno']);
                    $clabeEsc = mysqli_real_escape_string($conecta->conexion, $clabe);

                    $okBco = $conecta->consulta(
                        "INSERT INTO carobra.usuarios_banco (no_empleado, nombre_cta, ap_cta, am_cta, id_banco, clabe, activo)
                         VALUES ({$nuevoNo}, '{$ctaEsc}', '{$apEsc}', '{$amEsc}', {$idBanco}, '{$clabeEsc}', 1)"
                    );
                    if (!$okBco) $advertencias[] = 'No se pudo guardar la cuenta bancaria: ' . $conecta->conexion->error;
                }
            }
        }

        /* ── Nivel (opcional) ── */
        $nivelTxt = trim($fila['nivel'] ?? '');
        if ($nivelTxt !== '') {
            $idNivel = $catalogoNiveles[normalizarNombreCatalogoRRHH($nivelTxt)] ?? 0;
            if ($idNivel > 0) {
                $conecta->consulta("INSERT INTO carobra_2014.usuarios_nivel (clave, id_nivel) VALUES ('{$claveEsc}', {$idNivel})");
            } else {
                $advertencias[] = "Nivel \"{$nivelTxt}\" no encontrado en el catálogo -- no se asignó.";
            }
        }

        /* ── Estructura / gerencia (opcional) ── */
        $estructuraTxt = trim($fila['estructura'] ?? '');
        if ($estructuraTxt !== '') {
            $idGerencia = $catalogoGerencias[normalizarNombreCatalogoRRHH($estructuraTxt)] ?? 0;
            if ($idGerencia > 0) {
                $conecta->consulta("INSERT INTO carobra_2014.usuarios_gerencia (clave, id_gerencia, pertenece) VALUES ('{$claveEsc}', {$idGerencia}, 1)");
            } else {
                $advertencias[] = "Estructura/gerencia \"{$estructuraTxt}\" no encontrada en el catálogo -- no se asignó.";
            }
        }

        /* ── Contacto de emergencia (opcional) ── */
        $contactoNombre   = strtoupper(trim($fila['contacto_emergencia_nombre'] ?? ''));
        $contactoTelefono = trim($fila['contacto_emergencia_telefono'] ?? '');
        if ($contactoNombre !== '' && $contactoTelefono !== '') {
            if ($idParentescoDefault <= 0) {
                $advertencias[] = 'No hay catálogo de parentescos configurado -- no se guardó el contacto de emergencia.';
            } else {
                $contNombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($contactoNombre));
                $contNumEsc    = mysqli_real_escape_string($conecta->conexion, $contactoTelefono);
                $conecta->consulta(
                    "INSERT INTO carobra_2014.contactos_emergencia (clave, nombre, numero, id_parentesco)
                     VALUES ('{$claveEsc}', '{$contNombreEsc}', '{$contNumEsc}', {$idParentescoDefault})"
                );
            }
        } elseif ($contactoNombre !== '' xor $contactoTelefono !== '') {
            $advertencias[] = 'Contacto de emergencia incompleto (falta nombre o teléfono) -- no se guardó.';
        }

        /* ── Correo de bienvenida (uno por empleado, igual que el alta individual) ── */
        $primerNombre = ucfirst(strtolower(explode(' ', $nombres)[0]));
        envia_correo(
            htmlCorreoBienvenidaMasivaRRHH($clave, $password, $primerNombre),
            'administrador.sisca@carobra.com',
            $correo,
            null,
            'Tu usuario en el Sistema Carobra fue activado',
            ''
        );

        $insertados++;
        $resultados[] = [
            'fila'          => $numFila,
            'clave'         => $clave,
            'nombre'        => $nombreCompleto,
            'email'         => $correo,
            'advertencias'  => $advertencias,
        ];
    }

    $mensaje = "Se procesaron {$procesados} fila(s): {$insertados} empleado(s) dado(s) de alta.";
    if ($omitidos > 0) $mensaje .= " {$omitidos} fila(s) omitida(s).";

    echo json_encode([
        'ok'               => true,
        'msg'              => $mensaje,
        'procesados'       => $procesados,
        'insertados'       => $insertados,
        'omitidos'         => $omitidos,
        'omitidosDetalle'  => $omitidosDetalle,
        'resultados'       => $resultados,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   BOLETINADOS PRINCIPAL

   Lista de personas boletinadas (no deseables para contratación)
   que se consulta ANTES de dar de alta un empleado nuevo, para
   avisar al capturista de una posible coincidencia. La lista en sí
   (listar/guardar/eliminar) requiere ser Super Usuario o tener la
   pestaña 'boletinados' otorgada; la verificación de coincidencia
   (verificarBoletinado) se deja abierta a cualquiera que llegue a
   este endpoint, porque se dispara automáticamente desde el flujo
   normal de alta y no expone nada más que "sí/no hay coincidencia".
════════════════════════════════════════════════════════════ */
if ($action === 'listarBoletinados') {

    $busqueda = trim($input['busqueda'] ?? '');
    $where = '';
    if ($busqueda !== '') {
        $bEsc  = mysqli_real_escape_string($conecta->conexion, $busqueda);
        $where = "WHERE nombres LIKE '%{$bEsc}%' OR apellido_paterno LIKE '%{$bEsc}%' OR apellido_materno LIKE '%{$bEsc}%'";
    }

    $rs = $conecta->consulta(
        "SELECT id, nombres, apellido_paterno, apellido_materno, observaciones, capturado_por, fec_captura
           FROM carobra_2014.boletinados_principal
           {$where}
           ORDER BY fec_captura DESC"
    );

    $lista = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $lista[] = $row;
        }
    }

    echo json_encode(['ok' => true, 'data' => $lista]);
    exit;
}

if ($action === 'guardarBoletinado') {

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'boletinados')) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para esta acción.']);
        exit;
    }

    $nombres = strtoupper(trim($input['nombres'] ?? ''));
    $apPat   = strtoupper(trim($input['apellido_paterno'] ?? ''));
    $apMat   = strtoupper(trim($input['apellido_materno'] ?? ''));
    $observ  = trim($input['observaciones'] ?? '');
    if (mb_strlen($observ) > 500) {
        $observ = mb_substr($observ, 0, 500);
    }

    if ($nombres === '' || $apPat === '') {
        echo json_encode(['ok' => false, 'msg' => 'Captura al menos Nombre(s) y Apellido paterno.']);
        exit;
    }

    $nombresEsc     = mysqli_real_escape_string($conecta->conexion, $nombres);
    $apPatEsc       = mysqli_real_escape_string($conecta->conexion, $apPat);
    $apMatEsc       = mysqli_real_escape_string($conecta->conexion, $apMat);
    $observEsc      = mysqli_real_escape_string($conecta->conexion, $observ);
    $claveSesionEsc = mysqli_real_escape_string($conecta->conexion, claveSesionActual($conecta));

    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.boletinados_principal (nombres, apellido_paterno, apellido_materno, observaciones, capturado_por)
         VALUES ('{$nombresEsc}', '{$apPatEsc}', '{$apMatEsc}', '{$observEsc}', '{$claveSesionEsc}')"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode([
        'ok'  => true,
        'msg' => 'Agregado a la lista de boletinados.',
        'id'  => mysqli_insert_id($conecta->conexion),
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: actualizarObservacionBoletinado
   Edita SOLO la observación de un registro ya existente en
   boletinados_principal (nombre/apellidos no se tocan aquí).
════════════════════════════════════════════════════════════ */
if ($action === 'actualizarObservacionBoletinado') {

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'boletinados')) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para esta acción.']);
        exit;
    }

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Registro inválido.']);
        exit;
    }

    $observ = trim($input['observaciones'] ?? '');
    if (mb_strlen($observ) > 500) {
        $observ = mb_substr($observ, 0, 500);
    }
    $observEsc = mysqli_real_escape_string($conecta->conexion, $observ);

    $ok = $conecta->consulta(
        "UPDATE carobra_2014.boletinados_principal SET observaciones = '{$observEsc}' WHERE id = {$id}"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo actualizar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Observación actualizada.']);
    exit;
}

if ($action === 'eliminarBoletinado') {

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'boletinados')) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para esta acción.']);
        exit;
    }

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Registro inválido.']);
        exit;
    }

    $ok = $conecta->consulta("DELETE FROM carobra_2014.boletinados_principal WHERE id={$id}");
    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Eliminado de la lista.']);
    exit;
}

if ($action === 'verificarBoletinado') {

    $nombresIn = strtoupper(trim($input['nombres'] ?? ''));
    $apPatIn   = strtoupper(trim($input['apellido_paterno'] ?? ''));
    $apMatIn   = strtoupper(trim($input['apellido_materno'] ?? ''));

    if ($nombresIn === '' && $apPatIn === '' && $apMatIn === '') {
        echo json_encode(['ok' => true, 'coincide' => false, 'coincidencias' => []]);
        exit;
    }

    $nombresEsc = mysqli_real_escape_string($conecta->conexion, $nombresIn);
    $apPatEsc   = mysqli_real_escape_string($conecta->conexion, $apPatIn);
    $apMatEsc   = mysqli_real_escape_string($conecta->conexion, $apMatIn);

    /* Coincidencia si: (a) ambos apellidos son iguales tal cual (señal
       fuerte, aunque el nombre de pila se haya capturado distinto -- con
       o sin segundo nombre, por ejemplo), o (b) nombre(s) + apellido
       paterno coinciden exactos. No se exige que las tres columnas
       coincidan a la vez para no dejar pasar variantes obvias. */
    $condiciones = [];
    if ($apPatEsc !== '' && $apMatEsc !== '') {
        $condiciones[] = "(UPPER(TRIM(apellido_paterno)) = '{$apPatEsc}' AND UPPER(TRIM(apellido_materno)) = '{$apMatEsc}')";
    }
    if ($nombresEsc !== '' && $apPatEsc !== '') {
        $condiciones[] = "(UPPER(TRIM(nombres)) = '{$nombresEsc}' AND UPPER(TRIM(apellido_paterno)) = '{$apPatEsc}')";
    }

    if (empty($condiciones)) {
        echo json_encode(['ok' => true, 'coincide' => false, 'coincidencias' => []]);
        exit;
    }

    $where = implode(' OR ', $condiciones);
    $rs = $conecta->consulta(
        "SELECT id, nombres, apellido_paterno, apellido_materno
           FROM carobra_2014.boletinados_principal
           WHERE {$where}"
    );

    $coincidencias = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $coincidencias[] = $row;
        }
    }

    echo json_encode([
        'ok'            => true,
        'coincide'      => count($coincidencias) > 0,
        'coincidencias' => $coincidencias,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   NOMBRE SEPARADO (temporal, mientras se corrige carobra.usuarios)

   carobra.usuarios.nombre guarda el nombre completo en un solo
   campo, lo cual complica separar Nombres / Apellido paterno /
   Apellido materno para ciertas pantallas. En lo que se resuelve
   de fondo (sin tocar usuarios, que impactaría todo el sistema),
   se guarda la versión separada en carobra.usuarios_nombre_separado.
════════════════════════════════════════════════════════════ */

/* Heurística de separación: últimas 2 palabras = apellidos, el
   resto = nombre(s). Es solo una SUGERENCIA inicial para no hacer
   capturar todo desde cero; el usuario la confirma o corrige. */
function sugerirNombreSeparado($nombreCompleto) {
    $nombreCompleto = trim((string) $nombreCompleto);
    if ($nombreCompleto === '') {
        return ['nombres' => '', 'apellido_paterno' => '', 'apellido_materno' => ''];
    }

    $partes = preg_split('/\s+/', mb_strtoupper($nombreCompleto, 'UTF-8'));
    $partes = array_values(array_filter($partes, fn($p) => $p !== ''));
    $total  = count($partes);

    if ($total === 1) {
        return ['nombres' => $partes[0], 'apellido_paterno' => '', 'apellido_materno' => ''];
    }
    if ($total === 2) {
        return ['nombres' => $partes[0], 'apellido_paterno' => $partes[1], 'apellido_materno' => ''];
    }
    if ($total === 3) {
        return ['nombres' => $partes[0], 'apellido_paterno' => $partes[1], 'apellido_materno' => $partes[2]];
    }

    $apellidoMaterno = array_pop($partes);
    $apellidoPaterno = array_pop($partes);
    $nombres = implode(' ', $partes);
    return ['nombres' => $nombres, 'apellido_paterno' => $apellidoPaterno, 'apellido_materno' => $apellidoMaterno];
}

function guardarNombreSeparadoBD($conecta, $clave, $nombres, $apellidoPaterno, $apellidoMaterno) {
    $claveEsc  = mysqli_real_escape_string($conecta->conexion, utf8_decode(strtoupper(trim($clave))));
    $nombresEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode(strtoupper(trim($nombres))));
    $apPatEsc   = mysqli_real_escape_string($conecta->conexion, utf8_decode(strtoupper(trim($apellidoPaterno))));
    $apMatEsc   = mysqli_real_escape_string($conecta->conexion, utf8_decode(strtoupper(trim($apellidoMaterno))));

    return $conecta->consulta("
        INSERT INTO carobra.usuarios_nombre_separado (clave, nombres, apellido_paterno, apellido_materno)
        VALUES ('{$claveEsc}', '{$nombresEsc}', '{$apPatEsc}', '{$apMatEsc}')
        ON DUPLICATE KEY UPDATE
            nombres = VALUES(nombres),
            apellido_paterno = VALUES(apellido_paterno),
            apellido_materno = VALUES(apellido_materno)
    ");
}

/* ════════════════════════════════════════════════════════════
   PESTAÑA TARJETAS -- carobra_2014.empleados_tarjetas (1 fila por clave)
   El nivel NO se guarda: se calcula en vivo como
   max(id_nivel) de usuarios_nivel_gerencia por clave.
════════════════════════════════════════════════════════════ */
function productosBaseTarjeta() {
    return ['Bradescard', 'Sears', 'Falabella', 'Registros'];
}

function nivelDeClaveTarjeta($conecta, $claveEsc) {
    $rs  = $conecta->consulta("SELECT MAX(id_nivel) AS id_nivel
                               FROM carobra_2014.usuarios_nivel_gerencia
                               WHERE clave = '{$claveEsc}'");
    $row = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$row || $row['id_nivel'] === null) return ['id_nivel' => null, 'nivel' => ''];
    $id  = intval($row['id_nivel']);
    $nom = '';
    $rn  = $conecta->consulta("SELECT desc_nivel FROM carobra_2014.niveles WHERE id_nivel = {$id} LIMIT 1");
    if ($rn && ($fn = mysqli_fetch_assoc($rn)) && isset($fn['desc_nivel'])) $nom = utf8_encode($fn['desc_nivel']);
    return ['id_nivel' => $id, 'nivel' => $nom];
}

if ($action === 'getTarjeta') {
    $clave = trim($input['clave'] ?? '');
    if ($clave === '') { echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']); exit; }
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rs  = $conecta->consulta("SELECT producto_base, banco, tiene_sueldo, usuario_soc, usuario_bid, fecha_mod, usuario_mod
                               FROM carobra_2014.empleados_tarjetas WHERE clave = '{$claveEsc}' LIMIT 1");
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    echo json_encode([
        'ok'        => true,
        'existe'    => (bool)$row,
        'productos' => productosBaseTarjeta(),
        'data'      => $row ? [
            'producto_base' => $row['producto_base'],
            'banco'         => $row['banco'] ?? '',
            'tiene_sueldo'  => intval($row['tiene_sueldo']) === 1,
            'usuario_soc'   => $row['usuario_soc'] ?? '',
            'usuario_bid'   => $row['usuario_bid'] ?? '',
            'fecha_mod'     => $row['fecha_mod'],
            'usuario_mod'   => $row['usuario_mod'],
        ] : null,
        'nivel'     => nivelDeClaveTarjeta($conecta, $claveEsc),
    ]);
    exit;
}

if ($action === 'guardarTarjeta') {
    $clave = trim($input['clave'] ?? '');
    if ($clave === '') { echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']); exit; }

    $producto = trim($input['producto_base'] ?? '');
    if (!in_array($producto, productosBaseTarjeta(), true)) {
        echo json_encode(['ok' => false, 'msg' => 'Selecciona un producto base válido.']); exit;
    }
    $banco = trim($input['banco'] ?? '');
    if (mb_strlen($banco) > 100) { echo json_encode(['ok' => false, 'msg' => 'El banco admite máximo 100 caracteres.']); exit; }

    $soc = trim($input['usuario_soc'] ?? '');
    $bid = trim($input['usuario_bid'] ?? '');
    if ($soc !== '' && !preg_match('/^\d{1,9}$/', $soc)) { echo json_encode(['ok' => false, 'msg' => 'Usuario Soc: solo números, máximo 9 dígitos.']); exit; }
    if ($bid !== '' && !preg_match('/^\d{1,9}$/', $bid)) { echo json_encode(['ok' => false, 'msg' => 'Usuario Bid: solo números, máximo 9 dígitos.']); exit; }

    $sueldo = !empty($input['tiene_sueldo']) ? 1 : 0;

    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $prodEsc  = mysqli_real_escape_string($conecta->conexion, $producto);
    $bancoSql = $banco === '' ? 'NULL' : "'" . mysqli_real_escape_string($conecta->conexion, $banco) . "'";
    $socSql   = $soc   === '' ? 'NULL' : "'{$soc}'";
    $bidSql   = $bid   === '' ? 'NULL' : "'{$bid}'";
    $usrEsc   = mysqli_real_escape_string($conecta->conexion, $_SESSION['nombre'] ?? '');

    $ok = $conecta->consulta("
        INSERT INTO carobra_2014.empleados_tarjetas
            (clave, producto_base, banco, tiene_sueldo, usuario_soc, usuario_bid, usuario_mod)
        VALUES ('{$claveEsc}', '{$prodEsc}', {$bancoSql}, {$sueldo}, {$socSql}, {$bidSql}, '{$usrEsc}')
        ON DUPLICATE KEY UPDATE
            producto_base = VALUES(producto_base),
            banco         = VALUES(banco),
            tiene_sueldo  = VALUES(tiene_sueldo),
            usuario_soc   = VALUES(usuario_soc),
            usuario_bid   = VALUES(usuario_bid),
            usuario_mod   = VALUES(usuario_mod)
    ");

    if (!$ok) { echo json_encode(['ok' => false, 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]); exit; }
    echo json_encode(['ok' => true, 'msg' => 'Tarjeta guardada.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getNombreSeparado
   Si ya existe una versión separada guardada, la regresa tal cual
   (sugerido=false). Si no existe todavía, regresa una sugerencia
   calculada a partir de carobra.usuarios.nombre (sugerido=true)
   para que el usuario solo la confirme o ajuste.
════════════════════════════════════════════════════════════ */
if ($action === 'getNombreSeparado') {
    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) { echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']); exit; }
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rs  = $conecta->consulta("SELECT nombres, apellido_paterno, apellido_materno FROM carobra.usuarios_nombre_separado WHERE clave='{$claveEsc}' LIMIT 1");
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    if ($row) {
        echo json_encode([
            'ok'               => true,
            'sugerido'         => false,
            'nombres'          => utf8_encode($row['nombres']),
            'apellido_paterno' => utf8_encode($row['apellido_paterno']),
            'apellido_materno' => utf8_encode($row['apellido_materno']),
        ]);
        exit;
    }

    $rsU  = $conecta->consulta("SELECT nombre FROM carobra.usuarios WHERE clave='{$claveEsc}' LIMIT 1");
    $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;
    $sugerencia = sugerirNombreSeparado($rowU ? utf8_encode($rowU['nombre']) : '');

    echo json_encode([
        'ok'               => true,
        'sugerido'         => true,
        'nombres'          => $sugerencia['nombres'],
        'apellido_paterno' => $sugerencia['apellido_paterno'],
        'apellido_materno' => $sugerencia['apellido_materno'],
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarNombreSeparado
════════════════════════════════════════════════════════════ */
if ($action === 'guardarNombreSeparado') {
    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) { echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']); exit; }

    $ok = guardarNombreSeparadoBD(
        $conecta,
        $clave,
        $input['nombres'] ?? '',
        $input['apellido_paterno'] ?? '',
        $input['apellido_materno'] ?? ''
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Nombre separado guardado.']);
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
                domicilio, telefono, celular, rfc, curp, nss, fec_nap, recluta,
                deportenis, no_prom_dep
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

    /* "Espartano" = deportenis=1 y no_prom_dep='ESPARTANO' en carobra.empleados
       (misma condición que usa seg_espartanos_api.php para armar su listado) */
    $esEspartano = 0;
    if ($row && intval($row['deportenis'] ?? 0) === 1
        && strtoupper(trim($row['no_prom_dep'] ?? '')) === 'ESPARTANO') {
        $esEspartano = 1;
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

    /* Lista negra: estado vigente en carobra.empleados.lista_negra (1 = activa).
       El comentario ES la observación vigente (carobra_2014.empl_observaciones),
       la misma que se ve/edita en la sección Observaciones de Datos generales. */
    $listaNegra = $row ? intval($row['lista_negra'] ?? 0) : 0;
    $listaNegraComentario = '';
    if ($listaNegra === 1 && $row && !empty($row['clave'])) {
        $claveLn = mysqli_real_escape_string($conecta->conexion, $row['clave']);
        $rsLn    = $conecta->consulta("SELECT observaciones FROM carobra_2014.empl_observaciones
                                       WHERE clave='{$claveLn}' LIMIT 1");
        $rowLn   = $rsLn ? mysqli_fetch_assoc($rsLn) : null;
        if ($rowLn) $listaNegraComentario = $rowLn['observaciones'] ?? '';
    }

    echo json_encode([
        'ok'    => true,
        'datos' => [
            'lista_negra'            => $listaNegra,
            'lista_negra_comentario' => $listaNegraComentario,
            'fec_ingreso'      => $row ? formatoFechaInput($row['fec_ingreso'] ?? '')   : '',
            'fec_reingreso'    => $row ? formatoFechaInput($row['fec_reingreso'] ?? '') : '',
            'fec_baja'         => $row ? formatoFechaInput($row['fec_baja'] ?? '')      : '',
            'staff'            => $row ? intval($row['staff'] ?? 0) : 0,
            'espartano'        => $esEspartano,
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
    $charsetOriginal = $conecta->conexion->character_set_name();
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
        "SELECT fec_ingreso, fec_reingreso, fec_baja, lista_negra FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1"
    );
    $rowFechasViejas = $rsFechasViejas ? mysqli_fetch_assoc($rsFechasViejas) : null;

    /* Lista negra: si pasa de 0 a 1 el comentario es OBLIGATORIO (se valida
       antes de tocar nada) y se guarda como la observación vigente del
       empleado (empl_observaciones), no en una tabla aparte. */
    $listaNegraNueva = !empty($input['lista_negra']) ? 1 : 0;
    $listaNegraVieja = $rowFechasViejas ? intval($rowFechasViejas['lista_negra'] ?? 0) : 0;
    $comentarioLn    = trim($input['lista_negra_comentario'] ?? '');
    if (mb_strlen($comentarioLn) > 500) $comentarioLn = mb_substr($comentarioLn, 0, 500);
    if ($listaNegraNueva === 1 && $listaNegraVieja === 0 && $comentarioLn === '') {
        echo json_encode(['ok' => false, 'msg' => 'Para agregar a lista negra es obligatorio capturar un comentario.']);
        exit;
    }

    $fecIngreso   = formatoFechaBD($input['fec_ingreso']   ?? '');
    $fecReingreso = formatoFechaBD($input['fec_reingreso'] ?? '');
    $fecBaja      = formatoFechaBD($input['fec_baja']      ?? '');
    $staff        = intval($input['staff'] ?? 0);
    $jefe         = $staff ? strtoupper(trim($input['jefe_inmediato'] ?? '')) : '';
    /* Espartano: controla deportenis + no_prom_dep en carobra.empleados,
       misma condición que usa el módulo de Espartanos para armar su listado. */
    $espartano    = intval($input['espartano'] ?? 0);
    $noPromDep    = $espartano ? 'ESPARTANO' : '';
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
    $noPromDepEsc = mysqli_real_escape_string($conecta->conexion, $noPromDep);

    $okEmp = $conecta->consulta(
        "UPDATE carobra.empleados SET
            fec_ingreso     = '{$fecIngreso}',
            fec_reingreso   = '{$fecReingreso}',
            fec_baja        = '{$fecBaja}',
            staff           = {$staff},
            lista_negra     = {$listaNegraNueva},
            deportenis      = {$espartano},
            no_prom_dep     = '{$noPromDepEsc}',
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

    /* Lista negra: el comentario capturado pasa a ser la observación vigente.
       Se escribe con el charset original de la conexión (el mismo que usa
       guardarObservacion) para que se lea igual en la sección Observaciones. */
    if ($listaNegraNueva === 1 && $listaNegraVieja === 0 && $comentarioLn !== '') {
        $conecta->conexion->set_charset($charsetOriginal);
        $obsEsc = mysqli_real_escape_string($conecta->conexion, $comentarioLn);
        $usrEsc = mysqli_real_escape_string($conecta->conexion, claveSesionActual($conecta));
        $conecta->consulta(
            "INSERT INTO carobra_2014.empl_observaciones (clave, observaciones, usuario_captura)
             VALUES ('{$claveEsc}', '{$obsEsc}', '{$usrEsc}')
             ON DUPLICATE KEY UPDATE
                observaciones   = VALUES(observaciones),
                usuario_captura = VALUES(usuario_captura),
                fecha_captura   = CURRENT_TIMESTAMP"
        );
        $conecta->conexion->set_charset('latin1');
    }

    /* Al guardar como Espartano se le crea su fila en espartanos_pagos
       (pago_incentivos/pago_asegurado en 0 por default) si todavía no la
       tiene. INSERT IGNORE: si ya existe no se toca lo que ya tenía
       marcado. */
    if ($espartano) {
        $claveSesionEsc = mysqli_real_escape_string($conecta->conexion, claveSesionActual($conecta));
        $conecta->consulta(
            "INSERT IGNORE INTO carobra_2014.espartanos_pagos
                (clave, pago_incentivos, pago_asegurado, actualizado_por)
             VALUES ('{$claveEsc}', 0, 0, '{$claveSesionEsc}')"
        );
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
   ACTION: getObservacion
   Trae la observación vigente de empl_observaciones (a lo más 1 por
   clave). Sin historial: la más reciente es la única que importa.

   OJO: empl_observa (sin "ciones") es la tabla VIEJA -- ahora es solo
   un respaldo de lo que había antes de este módulo, con no_empleado
   como llave. Ya no se lee ni se escribe desde aquí; su contenido
   vigente se trajo una sola vez a empl_observaciones (ver
   empl_observa_migracion.sql).
════════════════════════════════════════════════════════════ */
if ($action === 'getObservacion') {

    $clave = strtoupper(trim($input['clave'] ?? ''));
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);

    $rsActual  = $conecta->consulta(
        "SELECT id, observaciones, fecha_captura, usuario_captura
         FROM carobra_2014.empl_observaciones
         WHERE clave = '{$claveEsc}'
         LIMIT 1"
    );
    $rowActual = $rsActual ? mysqli_fetch_assoc($rsActual) : null;

    echo json_encode([
        'ok'     => true,
        'actual' => $rowActual ? [
            'id'              => intval($rowActual['id']),
            'observaciones'   => $rowActual['observaciones']   ?? '',
            'fecha_captura'   => $rowActual['fecha_captura']    ?? '',
            'usuario_captura' => $rowActual['usuario_captura']  ?? '',
        ] : null,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarObservacion
   Sin historial: actualiza directo la fila de esta clave en
   empl_observaciones (INSERT ... ON DUPLICATE KEY UPDATE). Lo
   anterior no se guarda en ningún lado -- solo importa lo vigente.
════════════════════════════════════════════════════════════ */
if ($action === 'guardarObservacion') {

    $clave = strtoupper(trim($input['clave'] ?? ''));
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $texto = trim($input['observaciones'] ?? '');
    if ($texto === '') {
        echo json_encode(['ok' => false, 'msg' => 'La observación no puede estar vacía.']);
        exit;
    }
    if (mb_strlen($texto) > 500) {
        $texto = mb_substr($texto, 0, 500);
    }

    $claveEsc      = mysqli_real_escape_string($conecta->conexion, $clave);
    $textoEsc      = mysqli_real_escape_string($conecta->conexion, $texto);
    $usuarioSesEsc = mysqli_real_escape_string($conecta->conexion, claveSesionActual($conecta));

    $ok = $conecta->consulta(
        "INSERT INTO carobra_2014.empl_observaciones (clave, observaciones, usuario_captura)
         VALUES ('{$claveEsc}', '{$textoEsc}', '{$usuarioSesEsc}')
         ON DUPLICATE KEY UPDATE
            observaciones   = VALUES(observaciones),
            usuario_captura = VALUES(usuario_captura),
            fecha_captura   = CURRENT_TIMESTAMP"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar la observación: ' . $conecta->conexion->error]);
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Observación guardada correctamente.']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getDatosBaja
   Datos para el modal "Dar de baja" de la pestaña Información:
   fec_baja y ase_bernal (carobra.empleados), activo (carobra.usuarios),
   NAP vigente (carobra_2014.naps) y observación vigente
   (carobra_2014.empl_observaciones). NAP y Asesoría Bernal son solo
   informativos en el modal.
════════════════════════════════════════════════════════════ */
if ($action === 'getDatosBaja') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $esc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $rsE  = $conecta->consulta(
        "SELECT no_empleado, fec_baja, ase_bernal FROM carobra.empleados WHERE clave='{$esc}' LIMIT 1"
    );
    $rowE = $rsE ? mysqli_fetch_assoc($rsE) : null;

    $rsU  = $conecta->consulta("SELECT activo FROM carobra.usuarios WHERE clave='{$esc}' LIMIT 1");
    $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;

    $nap = '';
    if ($rowE && !empty($rowE['no_empleado'])) {
        $noEmp = intval($rowE['no_empleado']);
        $rsNap = $conecta->consulta(
            "SELECT nap FROM carobra_2014.naps WHERE no_empleado={$noEmp} AND baja=0 LIMIT 1"
        );
        $rowNap = $rsNap ? mysqli_fetch_assoc($rsNap) : null;
        if ($rowNap) $nap = utf8_encode($rowNap['nap']);
    }

    $obsClaveEsc = mysqli_real_escape_string($conecta->conexion, strtoupper($clave));
    $rsO  = $conecta->consulta(
        "SELECT observaciones FROM carobra_2014.empl_observaciones WHERE clave='{$obsClaveEsc}' LIMIT 1"
    );
    $rowO = $rsO ? mysqli_fetch_assoc($rsO) : null;

    echo json_encode([
        'ok'    => true,
        'datos' => [
            'fec_baja'      => $rowE ? formatoFechaInput($rowE['fec_baja'] ?? '') : '',
            'activo'        => $rowU ? intval($rowU['activo']) : 0,
            'ase_bernal'    => $rowE ? intval($rowE['ase_bernal'] ?? 0) : 0,
            'nap'           => $nap,
            'observaciones' => $rowO ? ($rowO['observaciones'] ?? '') : '',
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarBaja
   Un solo guardado desde el modal "Dar de baja":
     1) empl_observaciones  -> upsert de la observación (1 por clave)
     2) carobra.empleados   -> UPDATE fec_baja
     3) carobra.usuarios    -> UPDATE activo (0 = baja, 1 = reactivar)
     4) historial_movimientos -> evento 'baja' si la fecha cambió
   Reglas: si activo=0 la fecha de baja es obligatoria; la observación
   (motivo) siempre es obligatoria.
════════════════════════════════════════════════════════════ */
if ($action === 'guardarBaja') {

    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) {
        echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']);
        exit;
    }

    $activo   = intval($input['activo'] ?? 0) === 1 ? 1 : 0;
    $fecBaja  = formatoFechaBD($input['fec_baja'] ?? '');
    $texto    = trim($input['observaciones'] ?? '');

    if ($texto === '') {
        echo json_encode(['ok' => false, 'msg' => 'La observación es obligatoria.']);
        exit;
    }
    if (mb_strlen($texto) > 500) {
        $texto = mb_substr($texto, 0, 500);
    }
    if ($activo === 0 && $fecBaja === '') {
        echo json_encode(['ok' => false, 'msg' => 'Captura la fecha de baja.']);
        exit;
    }

    $esc           = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
    $claveObsEsc   = mysqli_real_escape_string($conecta->conexion, strtoupper($clave));
    $textoEsc      = mysqli_real_escape_string($conecta->conexion, $texto);
    $fecBajaEsc    = mysqli_real_escape_string($conecta->conexion, $fecBaja);
    $usuarioSesEsc = mysqli_real_escape_string($conecta->conexion, claveSesionActual($conecta));

    /* Fecha anterior, para saber si hay que registrar el evento */
    $rsV  = $conecta->consulta("SELECT fec_baja FROM carobra.empleados WHERE clave='{$esc}' LIMIT 1");
    $rowV = $rsV ? mysqli_fetch_assoc($rsV) : null;
    if (!$rowV) {
        echo json_encode(['ok' => false, 'msg' => 'No se encontró al empleado en carobra.empleados.']);
        exit;
    }

    /* 1) Observación */
    $okObs = $conecta->consulta(
        "INSERT INTO carobra_2014.empl_observaciones (clave, observaciones, usuario_captura)
         VALUES ('{$claveObsEsc}', '{$textoEsc}', '{$usuarioSesEsc}')
         ON DUPLICATE KEY UPDATE
            observaciones   = VALUES(observaciones),
            usuario_captura = VALUES(usuario_captura),
            fecha_captura   = CURRENT_TIMESTAMP"
    );
    if (!$okObs) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar la observación: ' . $conecta->conexion->error]);
        exit;
    }

    /* 2) Fecha de baja en empleados */
    $okEmp = $conecta->consulta(
        "UPDATE carobra.empleados SET fec_baja = '{$fecBajaEsc}' WHERE clave = '{$esc}'"
    );
    if (!$okEmp) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo actualizar la fecha de baja: ' . $conecta->conexion->error]);
        exit;
    }

    /* 3) Activo en usuarios */
    $okUsr = $conecta->consulta(
        "UPDATE carobra.usuarios SET activo = {$activo} WHERE clave = '{$esc}'"
    );
    if (!$okUsr) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo actualizar el estatus del usuario: ' . $conecta->conexion->error]);
        exit;
    }

    /* 4) Historial de movimientos (solo si la fecha cambió y no quedó vacía) */
    $fechaVieja = $rowV['fec_baja'] ?? '';
    if ($fecBaja !== '' && $fecBaja !== $fechaVieja) {
        $conecta->consulta(
            "INSERT INTO carobra_2014.historial_movimientos (clave, tipo, fecha_evento)
             VALUES ('{$esc}', 'baja', '{$fecBajaEsc}')"
        );
    }

    echo json_encode([
        'ok'  => true,
        'msg' => $activo === 0 ? 'Baja registrada correctamente.' : 'Datos actualizados; el usuario permanece activo.'
    ]);
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

    /* ── 5) Anticipos (produccion, ligado por no_empleado) ──
       Los conteos de tarjeta se resuelven con la misma función que
       usan los modales de detalle, para no duplicar la lógica de
       filtros/exclusiones en dos lugares. Primer y Segundo anticipo
       se muestran unificados en la UI como un solo concepto
       "Anticipos pendientes", así que aquí se suman en un solo
       conteo (la distinción por tipo sigue viva a nivel de datos,
       ver getAnticiposDetalle más abajo, solo se dejó de separar
       en la tarjeta resumen). */
    [$anticipo1Folios, ] = resolverAnticipo1($conecta, $claveEsc);
    [$anticipo2Folios, ] = resolverAnticipo2($conecta, $claveEsc);
    $acompanamiento       = resolverAcompanamiento($conecta, $claveEsc);
    $foliosProduccion     = resolverFoliosProduccion($conecta, $claveEsc);
    $aforeMovil           = resolverAforeMovilPendientes($conecta, $claveEsc);

    echo json_encode([
        'ok'                       => true,
        'periodo'                  => $fechaDMY,
        'insuficiencia'            => $rowI ? ['motivo'=>utf8_encode($rowI['motivo']),'ajuste'=>$rowI['ajuste']] : null,
        'facturas_pendientes'      => $rowF ? intval($rowF['v']) : 0,
        'adeudos_pendientes'       => $numAdeudos,
        'anticipos_pendientes'     => count($anticipo1Folios) + count($anticipo2Folios),
        'acompanamiento_cobrar'    => count($acompanamiento['cobrar']),
        'acompanamiento_pagar'     => count($acompanamiento['pagar']),
        'es_dueno_nap'             => $acompanamiento['es_dueno_nap'],
        'folios_por_cobrar'        => count($foliosProduccion['por_cobrar']),
        'afore_movil_pendientes'   => count($aforeMovil['registros']),

    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getAforeMovilDetalle
   Detalle de los registros de Afore Móvil pendientes por cobrar
   (mismo filtro que el contador de getInfoEmpleado), para el modal
   de la card.
════════════════════════════════════════════════════════════ */
if ($action === 'getAforeMovilDetalle') {
    $clave = trim($input['clave'] ?? '');
    if (empty($clave)) { echo json_encode(['ok' => false, 'msg' => 'Clave inválida.']); exit; }
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $aforeMovil = resolverAforeMovilPendientes($conecta, $claveEsc);

    echo json_encode(['ok' => true, 'registros' => $aforeMovil['registros']]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ANTICIPOS (Info del empleado)

   Anticipo 1: folios de produccion listos para anticipo (traspaso
   ya confirmado en produccion, sin anticipo asignado, y con fecha
   de traspaso de al menos 2 meses de antigüedad).

   Anticipo 2: folios de produccion con id_estatus_prod distinto de
   6 y 1, sin anticipo asignado, que YA NO estén registrados en
   segundo_anticipo (si ya aparecen ahí, es que ya se pagaron y no
   se muestran). De ese mismo conjunto:
     - la LISTA a mostrar son los que ya están Confirmado / Posible
       Confirmado en produccion.estatus_confirma;
     - aparte, se cuentan cuántos están en T+0 y cuántos en T+1..T+9
       (agrupados como "5+5"), como referencia informativa, sin
       que eso cambie qué folios se listan.

   Ambas funciones resuelven no_empleado a partir de la clave (la
   relación vive en carobra.empleados, igual que en getCuentasBancarias
   y demás acciones de este archivo) y devuelven [] / conteos en 0 si
   el empleado no tiene no_empleado asociado (staff sin producción).
════════════════════════════════════════════════════════════ */

function resolverNoEmpleado($conecta, $claveEsc) {
    $rsEmp  = $conecta->consulta("SELECT no_empleado FROM carobra.empleados WHERE clave='{$claveEsc}' LIMIT 1");
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;
    return ($rowEmp && !empty($rowEmp['no_empleado'])) ? intval($rowEmp['no_empleado']) : 0;
}

function resolverAnticipo1($conecta, $claveEsc) {
    $folios = [];
    $noEmpleado = resolverNoEmpleado($conecta, $claveEsc);
    if (!$noEmpleado) return [$folios, 0];

    /* fec_traspaso es char(10) guardado como texto 'dd/mm/yyyy' (no es
       un DATE real), así que hay que convertirlo con STR_TO_DATE antes
       de compararlo o de ordenar por fecha; comparar el texto tal cual
       ordena mal (compara como string, no cronológicamente).

       "2 meses atrás" = solo el mes calendario que fue hace 2 meses
       (ej. si hoy es agosto 2026, solo folios con fec_traspaso en
       junio 2026), no "2 meses o más" — así no se cuelan casos viejos
       de años anteriores que se quedaron atorados en id_estatus_prod=15. */
    $mesLimite = date('Y-m', strtotime('-2 months'));

    $rs = $conecta->consulta(
        "SELECT p.folio, p.fec_traspaso, pt.trabajador
         FROM produccion p
         LEFT JOIN prod_trabajador pt ON pt.folio = p.folio
         WHERE p.no_empleado = {$noEmpleado}
           AND p.id_estatus_anticipo = 0
           AND p.id_estatus_prod = 15
           AND DATE_FORMAT(STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y'), '%Y-%m') = '{$mesLimite}'
         ORDER BY STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') ASC"
    );
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $folios[] = [
                'folio'        => $row['folio'],
                'fec_traspaso' => $row['fec_traspaso'],
                'trabajador'   => $row['trabajador'],
            ];
        }
    }
    return [$folios, count($folios)];
}

function resolverAnticipo2($conecta, $claveEsc) {
    $folios = [];
    $noEmpleado = resolverNoEmpleado($conecta, $claveEsc);
    if (!$noEmpleado) return [$folios, 0];

    /* Candidatos a anticipo 2: sin anticipo asignado, estatus de
       producción distinto de 6/1, ya Confirmado o Posible Confirmado,
       y que el folio NO esté ya en segundo_anticipo (si ya está ahí,
       ya se pagó y no se muestra). fec_traspaso es char(10) 'dd/mm/yyyy',
       se convierte con STR_TO_DATE para poder ordenar/filtrar bien.

       Igual que en anticipo 1: solo el mes calendario que fue hace
       2 meses, no "2 meses o más" — para que no se cuelen folios
       viejos de otros años que quedaron atorados sin anticipo. */
    $mesLimite = date('Y-m', strtotime('-2 months'));

    $rs = $conecta->consulta(
        "SELECT p.folio, p.fec_traspaso, p.estatus_confirma, pt.trabajador
         FROM produccion p
         LEFT JOIN prod_trabajador pt ON pt.folio = p.folio
         WHERE p.no_empleado = {$noEmpleado}
           AND p.id_estatus_anticipo = 0
           AND p.id_estatus_prod NOT IN (6, 1)
           AND p.estatus_confirma IN ('Confirmado', 'Posible Confirmado')
           AND p.folio NOT IN (SELECT folio FROM segundo_anticipo)
           AND DATE_FORMAT(STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y'), '%Y-%m') = '{$mesLimite}'
         ORDER BY STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') ASC"
    );

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $folios[] = [
                'folio'           => $row['folio'],
                'fec_traspaso'    => $row['fec_traspaso'],
                'estatus_confirma'=> $row['estatus_confirma'],
                'trabajador'      => $row['trabajador'],
            ];
        }
    }

    return [$folios, count($folios)];
}

/* ════════════════════════════════════════════════════════════
   FOLIOS DE PRODUCCIÓN (todos los que le pertenecen al empleado)

   A diferencia de los anticipos (que solo miran un subconjunto muy
   específico, acotado por fecha), aquí se trae TODO el historial de
   folios del empleado (por no_empleado, sin filtro de fecha).

   Solo se traen folios de los últimos 2 meses (por fec_traspaso) y se
   excluyen los rechazados (id_estatus_prod = 6) -- cualquier otro
   estatus de producción cuenta. "estatus" es la descripción legible
   de id_estatus_prod (catálogo carobra_2014.estatus), no solo el
   código numérico.

   "Pendiente de validar pago" se resuelve contra la tabla `pagos`:
   si el folio YA aparece ahí, ya se pagó (columna en blanco); si NO
   aparece, sigue pendiente de validar pago. Consulta de referencia:
     SELECT p.folio FROM (SELECT * FROM produccion WHERE no_empleado=?
       AND id_estatus_prod=1) p LEFT JOIN pagos pa ON p.folio=pa.folio
       WHERE pa.folio IS NULL
   (aquí se generaliza a cualquier id_estatus_prod distinto de 6, no
   solo el 1, por eso ya no se restringe a IN (1,6)).

   El conteo de 'Por cobrar' (folios sin fila en `pagos`) es el que se
   muestra en la tarjeta de Información.
════════════════════════════════════════════════════════════ */

function resolverFoliosProduccion($conecta, $claveEsc) {
    $folios = [
        'rechazado'   => [],
        'pagado'      => [],
        'por_cobrar'  => [],
    ];
    $noEmpleado = resolverNoEmpleado($conecta, $claveEsc);
    if (!$noEmpleado) return $folios;

    $rs = $conecta->consulta(
        "SELECT p.folio, p.fec_traspaso, p.id_estatus_prod, esf.estatus AS estatus_desc,
                pt.trabajador, (pa.folio IS NOT NULL) AS ya_pagado
         FROM produccion p
         LEFT JOIN prod_trabajador pt ON pt.folio = p.folio
         LEFT JOIN pagos pa ON pa.folio = p.folio
         LEFT JOIN carobra_2014.estatus esf ON esf.id_estatus = p.id_estatus_prod
         WHERE p.no_empleado = {$noEmpleado}
           AND p.id_estatus_prod <> 6
           AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
         ORDER BY STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') DESC"
    );

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $yaPagado = !empty($row['ya_pagado']);
            $bucket   = $yaPagado ? 'pagado' : 'por_cobrar';

            $folios[$bucket][] = [
                'folio'           => $row['folio'],
                'fec_traspaso'    => $row['fec_traspaso'],
                'trabajador'      => $row['trabajador'],
                'id_estatus_prod' => intval($row['id_estatus_prod']),
                'estatus'         => $row['estatus_desc'] ?: ('Estatus ' . intval($row['id_estatus_prod'])),
                'pendiente_pago'  => !$yaPagado,
            ];
        }
    }

    return $folios;
}

/* ════════════════════════════════════════════════════════════
   AFORE MÓVIL -- pendientes por cobrar

   carobra_2014.registros_afore_movil trae registros capturados desde
   la app Afore Móvil; asesor guarda el NAP del asesor que los
   levantó (igual que produccion.nap identifica al asesor en el resto
   del sistema -- NO es la clave del empleado). Se considera
   "pendiente por cobrar" un registro que:
     - tiene asesor capturado (no vacío) y es el NAP vigente de este
       empleado,
     - no tiene fec_pago todavía (fec_pago vacío = no se ha cobrado),
     - el cliente tiene menos de 60 años (edad),
     - está en estatus ACEPTADO,
     - y pasaron menos de 8 días entre fec_captura_sf y fec_envio
       (fórmula de DATEDIFF exacta que ya usa Principal para este
       cálculo -- ambas son char(10) 'dd/mm/aaaa', se arma 'aaaa-mm-dd'
       con SUBSTR para que DATEDIFF las entienda).
════════════════════════════════════════════════════════════ */

function resolverAforeMovilPendientes($conecta, $claveEsc) {
    $resultado = ['registros' => []];

    $noEmpleado = resolverNoEmpleado($conecta, $claveEsc);
    if (!$noEmpleado) return $resultado;

    $rsNap  = $conecta->consulta("SELECT nap FROM naps WHERE no_empleado = {$noEmpleado} AND baja = 0 LIMIT 1");
    $rowNap = $rsNap ? mysqli_fetch_assoc($rsNap) : null;
    if (!$rowNap || empty($rowNap['nap'])) return $resultado; // sin nap propio vigente, no puede tener registros a su nombre

    $napEsc = mysqli_real_escape_string($conecta->conexion, $rowNap['nap']);

    $rs = $conecta->consulta(
        "SELECT folio, no_oportunidad, cliente, curp, edad, nap, estatus,
                fec_captura, fec_captura_sf, fec_envio, fec_pago, monto
         FROM carobra_2014.registros_afore_movil
         WHERE asesor = '{$napEsc}'
           AND asesor <> ''
           AND (fec_pago IS NULL OR fec_pago = '')
           AND edad < 60
           AND UPPER(TRIM(estatus)) = 'ACEPTADO'
           AND DATEDIFF(
                 CONCAT(SUBSTR(fec_captura_sf,-4),'-',SUBSTR(fec_captura_sf,4,2),'-',SUBSTR(fec_captura_sf,1,2)),
                 CONCAT(SUBSTR(fec_envio,-4),'-',SUBSTR(fec_envio,4,2),'-',SUBSTR(fec_envio,1,2))
               ) < 8
         ORDER BY fec_captura DESC"
    );

    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $resultado['registros'][] = [
                'folio'           => $row['folio'],
                'no_oportunidad'  => $row['no_oportunidad'],
                'cliente'         => $row['cliente'],
                'curp'            => $row['curp'],
                'edad'            => intval($row['edad']),
                'nap'             => $row['nap'],
                'estatus'         => $row['estatus'],
                'fec_captura'     => $row['fec_captura'],
                'fec_captura_sf'  => $row['fec_captura_sf'],
                'fec_envio'       => $row['fec_envio'],
                'monto'           => floatval($row['monto']),
            ];
        }
    }

    return $resultado;
}

/* ════════════════════════════════════════════════════════════
   ACOMPAÑAMIENTO DE NAPS

   produccion.nap trae el nap con el que se hizo cada traspaso; la
   tabla naps (no_empleado, nap) dice a quién le pertenece cada nap
   realmente. Cuando el folio se procesó (produccion.no_empleado)
   con un nap que NO es del empleado que lo procesó, hay un cobro
   cruzado registrado en acompaniamiento (folio, fec_pago, costo,
   estatus): el dueño del nap COBRA, quien procesó PAGA.

   Para el empleado en sesión/seleccionado se muestran SIEMPRE las
   dos cosas a la vez, ya que puede tener folios en ambos roles al
   mismo tiempo (ser dueño de un nap que otro usó, Y haber usado él
   mismo un nap ajeno en otro folio distinto):
     - A COBRAR: folios de un nap suyo (tabla `naps`) que procesó
       alguien más.
     - A PAGAR: folios que él procesó usando un nap que no es suyo.
   `es_dueno_nap` se sigue devolviendo como dato informativo (para
   saber si el empleado tiene al menos un nap propio), pero ya NO
   se usa para ocultar ninguna de las dos listas.
   Solo se consideran acompaniamiento.estatus = 0 o 1.
════════════════════════════════════════════════════════════ */

function resolverAcompanamiento($conecta, $claveEsc) {
    $resultado = ['cobrar' => [], 'pagar' => [], 'es_dueno_nap' => false];
    $noEmpleado = resolverNoEmpleado($conecta, $claveEsc);
    if (!$noEmpleado) return $resultado;

    $rsDueno  = $conecta->consulta("SELECT COUNT(*) AS v FROM naps WHERE no_empleado = {$noEmpleado}");
    $rowDueno = $rsDueno ? mysqli_fetch_assoc($rsDueno) : null;
    $esDuenio = $rowDueno && intval($rowDueno['v']) > 0;
    $resultado['es_dueno_nap'] = $esDuenio;

    /* A COBRAR: el nap usado en el folio es de este empleado, pero
       lo procesó alguien más. Si el empleado no tiene ningún nap
       propio, la subconsulta de naps.no_empleado no regresa filas
       y esto naturalmente da 0 resultados (no hace falta el if). */
    $rsCobrar = $conecta->consulta(
        "SELECT a.folio, a.fec_pago, a.costo
         FROM acompaniamiento a
         INNER JOIN produccion p ON p.folio = a.folio
         WHERE a.estatus IN (0, 1)
           AND p.no_empleado <> {$noEmpleado}
           AND p.nap IN (SELECT nap FROM naps WHERE no_empleado = {$noEmpleado})
         ORDER BY a.fec_pago ASC"
    );
    if ($rsCobrar) {
        while ($row = mysqli_fetch_assoc($rsCobrar)) {
            $resultado['cobrar'][] = [
                'folio'    => $row['folio'],
                'fec_pago' => $row['fec_pago'],
                'costo'    => floatval($row['costo']),
            ];
        }
    }

    /* A PAGAR: este empleado procesó el folio, pero con un nap que
       no es suyo. */
    $rsPagar = $conecta->consulta(
        "SELECT a.folio, a.fec_pago, a.costo
         FROM acompaniamiento a
         INNER JOIN produccion p ON p.folio = a.folio
         WHERE a.estatus IN (0, 1)
           AND p.no_empleado = {$noEmpleado}
           AND p.nap NOT IN (SELECT nap FROM naps WHERE no_empleado = {$noEmpleado})
         ORDER BY a.fec_pago ASC"
    );
    if ($rsPagar) {
        while ($row = mysqli_fetch_assoc($rsPagar)) {
            $resultado['pagar'][] = [
                'folio'    => $row['folio'],
                'fec_pago' => $row['fec_pago'],
                'costo'    => floatval($row['costo']),
            ];
        }
    }

    return $resultado;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getAcompanamientoDetalle
════════════════════════════════════════════════════════════ */
if ($action === 'getAcompanamientoDetalle') {
    $clave    = trim($input['clave'] ?? '');
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $datos = resolverAcompanamiento($conecta, $claveEsc);

    echo json_encode([
        'ok'           => true,
        'cobrar'       => $datos['cobrar'],
        'pagar'        => $datos['pagar'],
        'es_dueno_nap' => $datos['es_dueno_nap'],
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getAnticiposDetalle
   Unifica Primer y Segundo anticipo en una sola lista para la
   UI (antes eran dos tarjetas/modales separados). Cada folio
   trae 'tipo' => 'Primer anticipo' | 'Segundo anticipo' para
   que la tabla del modal los pueda distinguir con un badge.
   La lógica de resolución de cada uno (resolverAnticipo1/2) NO
   cambió, solo se combinan aquí a nivel de presentación.
════════════════════════════════════════════════════════════ */
if ($action === 'getAnticiposDetalle') {
    $clave    = trim($input['clave'] ?? '');
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    [$folios1, ] = resolverAnticipo1($conecta, $claveEsc);
    [$folios2, ] = resolverAnticipo2($conecta, $claveEsc);

    $folios = [];
    foreach ($folios1 as $f) {
        $f['tipo'] = 'Primer anticipo';
        $f['estatus_confirma'] = $f['estatus_confirma'] ?? '';
        $folios[] = $f;
    }
    foreach ($folios2 as $f) {
        $f['tipo'] = 'Segundo anticipo';
        $folios[] = $f;
    }

    /* Orden cronológico por fecha de traspaso, igual que cada lista
       original (fec_traspaso es texto 'dd/mm/yyyy'). */
    usort($folios, function ($a, $b) {
        $fa = DateTime::createFromFormat('d/m/Y', $a['fec_traspaso']);
        $fb = DateTime::createFromFormat('d/m/Y', $b['fec_traspaso']);
        if (!$fa || !$fb) return 0;
        return $fa <=> $fb;
    });

    echo json_encode(['ok' => true, 'folios' => $folios]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getFoliosDetalle
   Folios del empleado de los últimos 2 meses, todos los estatus
   menos Rechazado (id_estatus_prod=6) -- ya vienen así de
   resolverFoliosProduccion(). Cada folio trae 'estatus' (legible),
   'id_estatus_prod' (código) y 'pendiente_pago' (true si el folio
   todavía no aparece en `pagos`). Ordenados por fecha de traspaso
   descendente (más reciente primero).
════════════════════════════════════════════════════════════ */
if ($action === 'getFoliosDetalle') {
    $clave    = trim($input['clave'] ?? '');
    $claveEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));

    $porCategoria = resolverFoliosProduccion($conecta, $claveEsc);

    $folios = array_merge($porCategoria['pagado'], $porCategoria['por_cobrar']);
    usort($folios, function ($a, $b) {
        $fa = DateTime::createFromFormat('d/m/Y', $a['fec_traspaso']);
        $fb = DateTime::createFromFormat('d/m/Y', $b['fec_traspaso']);
        if (!$fa || !$fb) return 0;
        return $fb <=> $fa; /* descendente: más reciente primero */
    });

    echo json_encode([
        'ok'     => true,
        'folios' => $folios,
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
   Se dio de baja la tabla carobra_2014.expediente_digital y todo
   lo relacionado con ella. El expediente digital ahora vive en
   carobra_2014.expedientes / expediente_docs y se consulta desde
   el frontend directamente contra expedientes_sisca_api.php
   (acciones expedientePorEmpleado / revisarDoc), no desde aquí.
════════════════════════════════════════════════════════════ */

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
    'tarjetas',
    'boletinados',
    'permisos',
];

/* Claves con acceso exclusivo al botón/modal "Permisos" de la barra
   superior (gestión GLOBAL de qué pestañas ve cada usuario). No se
   basa en nivel: es una lista puntual de personas autorizadas. */
$CLAVES_GESTION_PERMISOS = ['FEROJAS', 'COYHERNAND', 'ADMINISTRA'];

/* Resuelve la clave (carobra.usuarios) del usuario de la sesión
   actual a partir de $_SESSION['nombre']. Se cachea en estático
   porque se consulta varias veces por request. */
function claveSesionActual($conecta) {
    static $clave = null;
    if ($clave !== null) return $clave;

    $nombreSesion = $_SESSION['nombre'] ?? '';
    if (empty($nombreSesion)) { $clave = ''; return $clave; }

    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreSesion));
    $rsU = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE nombre='{$nombreEsc}' LIMIT 1");
    $rowU = $rsU ? mysqli_fetch_assoc($rsU) : null;

    $clave = ($rowU && !empty($rowU['clave'])) ? $rowU['clave'] : '';
    return $clave;
}

/* Resuelve si el usuario de la sesión actual ($_SESSION['nombre'])
   pertenece al nivel Super Usuario (id_nivel = 12). Se apoya en las
   mismas tablas que ya usa el resto del sistema (carobra.usuarios
   para resolver nombre->clave, carobra_2014.usuarios_nivel para el
   nivel). No confía en nada que venga del cliente. */
function esSuperUsuarioSesion($conecta) {
    $clave = claveSesionActual($conecta);
    if (empty($clave)) return false;

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $rsN = $conecta->consulta(
        "SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}' AND id_nivel=12 LIMIT 1"
    );
    return ($rsN && mysqli_fetch_assoc($rsN)) ? true : false;
}

/* Whitelist puntual para el botón/modal "Permisos" de la barra
   superior — reemplaza el chequeo de nivel para estas acciones
   específicas: solo estas claves pueden ver/gestionar quién ve qué
   pestañas en todo el sistema. */
function puedeGestionarPermisosPestanas($conecta) {
    global $CLAVES_GESTION_PERMISOS;
    $clave = claveSesionActual($conecta);
    if (empty($clave)) return false;
    return in_array(strtoupper($clave), $CLAVES_GESTION_PERMISOS, true);
}

/* Indica si la clave de la sesión actual tiene otorgada una pestaña
   puntual en carobra_2014.permisos_pestanas (independiente de si es
   Super Usuario). Se usa para la pestaña "Permisos" (permisos por
   página), que ahora también se puede otorgar de forma individual sin
   necesidad de subir a nivel Super Usuario. */
function tienePestanaPermitidaSesion($conecta, $pestana) {
    $clave = claveSesionActual($conecta);
    if (empty($clave)) return false;

    $claveEsc   = mysqli_real_escape_string($conecta->conexion, $clave);
    $pestanaEsc = mysqli_real_escape_string($conecta->conexion, $pestana);
    $rs = $conecta->consulta(
        "SELECT 1 FROM carobra_2014.permisos_pestanas WHERE clave='{$claveEsc}' AND pestana='{$pestanaEsc}' LIMIT 1"
    );
    return ($rs && mysqli_fetch_assoc($rs)) ? true : false;
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

    if (!puedeGestionarPermisosPestanas($conecta)) {
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

    if (!puedeGestionarPermisosPestanas($conecta)) {
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

    if (!puedeGestionarPermisosPestanas($conecta)) {
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

    if (!puedeGestionarPermisosPestanas($conecta)) {
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

    if (!puedeGestionarPermisosPestanas($conecta)) {
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

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'permisos')) {
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

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'permisos')) {
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

    if (!esSuperUsuarioSesion($conecta) && !tienePestanaPermitidaSesion($conecta, 'permisos')) {
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