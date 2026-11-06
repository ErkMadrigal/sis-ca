<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=utf-8');

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'ok'    => false,
            'msg'   => 'Error interno del servidor.',
            'debug' => $err['message'] . ' en ' . basename($err['file']) . ':' . $err['line'],
        ]);
    }
});

if (!isset($_SESSION['ultimoAcceso']) || !isset($_SESSION['autentifica'])) {
    echo json_encode(['ok' => false, 'msg' => 'Tu sesión ha caducado. Vuelve a iniciar sesión.']);
    exit;
}

$conecta = new conector();

/* El conector se conecta por default a la base de datos del sitio
   (carobra_2014); form/polizas/qualitas viven en su propia base de
   datos "qualitas", así que hay que cambiar el contexto de la
   conexión antes de correr cualquier query de este archivo. */
if (!mysqli_select_db($conecta->conexion, 'qualitas')) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'No se pudo conectar a la base de datos "qualitas": ' . mysqli_error($conecta->conexion),
    ]);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   qualitas_api.php

   Módulo Qualitas: se cargan dos xlsx por separado --
   FORMULARIO_QUALITAS (datos del cliente/vehículo, va a la tabla
   `form`) y PAGOS QUALITAS (pólizas cobradas, va a la tabla
   `polizas`) -- y se cruzan por nombre para armar la tabla
   `qualitas` (nombre + póliza + CURP).

   El parseo del xlsx se hace en el navegador con SheetJS; aquí sólo
   se reciben arreglos de filas ya parseadas en JSON.
════════════════════════════════════════════════════════════ */

/* Normaliza un nombre para poder cruzar form.nombre contra
   polizas.concepto aunque vengan con mayúsculas/acentos/espacios/
   puntuación distintos entre un archivo y otro.

   El reemplazo fijo de acentos (strtr con 'Á'=>'A', etc.) NO alcanza
   por sí solo: algunos exports de Excel/Forms guardan la tilde como
   un carácter combinado aparte (Unicode NFD, ej. "o" + "´") en vez de
   la letra ya acentuada de un solo carácter (NFC, "ó"). Byte a byte
   esas dos formas no son iguales aunque se vean idénticas, así que
   "Gónzalez" de un archivo no igualaba a "Gónzalez" del otro -- ni
   hablar de contra "Gonzalez" sin acento. Por eso primero se
   descompone todo a NFD (separa cada letra de su acento) y se
   eliminan las marcas diacríticas (\p{Mn}) -- así CUALQUIER acento,
   venga como venga, se cae. Si el servidor no tiene la extensión
   intl (clase Normalizer), se usa como respaldo el reemplazo fijo de
   siempre. */
function normalizarNombreQualitas($texto) {
    $texto = trim((string) $texto);
    $texto = mb_strtoupper($texto, 'UTF-8');

    if (class_exists('Normalizer')) {
        $descompuesto = Normalizer::normalize($texto, Normalizer::FORM_D);
        if ($descompuesto !== false) {
            $texto = preg_replace('/\p{Mn}/u', '', $descompuesto);
        }
    } else {
        $texto = strtr($texto, [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'Ñ' => 'N', 'Ç' => 'C', 'Ý' => 'Y',
        ]);
    }

    /* Quita cualquier cosa que no sea letra/número/espacio (puntos,
       comas, apóstrofes, guiones, etc.) -- "O'BRIEN", "PÉREZ," y
       "PEREZ-GOMEZ" ya no fallan el match por un signo de más. */
    $texto = preg_replace('/[^A-Z0-9 ]/', '', $texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return trim($texto);
}

/* ════════════════════════════════════════════════════════════
   ACTION: importarForm
   Recibe filas ya parseadas del FORMULARIO_QUALITAS.xlsx:
   [{ modelo, marca, descripcion, curp, nombre, datos_carobra,
      submission_id }, ...]

   submission_id (columna "Submission ID" del formulario) es la
   llave para que recargar el mismo archivo ACTUALICE la fila en vez
   de duplicarla -- requiere el UNIQUE KEY uq_form_submission_id
   (ver qualitas.sql / qualitas_migracion_dedupe.sql). Si viene vacío
   (formulario sin esa columna) simplemente se inserta sin poder
   deduplicar esa fila en particular.
════════════════════════════════════════════════════════════ */
if ($action === 'importarForm') {

    $filas = $input['filas'] ?? [];
    if (!is_array($filas)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de filas inválido.']);
        exit;
    }

    $usuario    = $_SESSION['nombre'] ?? '';
    $usuarioEsc = mysqli_real_escape_string($conecta->conexion, $usuario);
    $insertadas = 0;
    $actualizadas = 0;
    $omitidas   = 0;

    foreach ($filas as $fila) {
        if (!is_array($fila)) { $omitidas++; continue; }

        $submissionId  = trim((string) ($fila['submission_id'] ?? ''));
        $modelo        = trim((string) ($fila['modelo'] ?? ''));
        $marca         = trim((string) ($fila['marca'] ?? ''));
        $descripcion   = trim((string) ($fila['descripcion'] ?? ''));
        $curp          = trim((string) ($fila['curp'] ?? ''));
        $nombre        = trim((string) ($fila['nombre'] ?? ''));
        $datosCarobra  = trim((string) ($fila['datos_carobra'] ?? ''));

        /* Renglón totalmente vacío (típico al final del xlsx) -- se omite */
        if ($modelo === '' && $marca === '' && $descripcion === '' && $curp === '' && $nombre === '' && $datosCarobra === '') {
            $omitidas++;
            continue;
        }

        $nombreNorm = normalizarNombreQualitas($nombre);

        $modeloEsc       = mysqli_real_escape_string($conecta->conexion, $modelo);
        $marcaEsc        = mysqli_real_escape_string($conecta->conexion, $marca);
        $descripcionEsc  = mysqli_real_escape_string($conecta->conexion, $descripcion);
        $curpEsc         = mysqli_real_escape_string($conecta->conexion, $curp);
        $nombreEsc       = mysqli_real_escape_string($conecta->conexion, $nombre);
        $nombreNormEsc   = mysqli_real_escape_string($conecta->conexion, $nombreNorm);
        $datosCarobraEsc = mysqli_real_escape_string($conecta->conexion, $datosCarobra);

        /* NULL (no '') cuando no hay submission_id -- así MySQL nunca
           trata dos filas sin ID como "duplicadas" entre sí. */
        $submissionIdSql = ($submissionId === '')
            ? 'NULL'
            : "'" . mysqli_real_escape_string($conecta->conexion, $submissionId) . "'";

        $ok = $conecta->consulta(
            "INSERT INTO qualitas.form (submission_id, modelo, marca, descripcion, curp, nombre, nombre_norm, datos_carobra, usuario_carga)
             VALUES ($submissionIdSql,'$modeloEsc','$marcaEsc','$descripcionEsc','$curpEsc','$nombreEsc','$nombreNormEsc','$datosCarobraEsc','$usuarioEsc')
             ON DUPLICATE KEY UPDATE
                modelo = VALUES(modelo), marca = VALUES(marca), descripcion = VALUES(descripcion),
                curp = VALUES(curp), nombre = VALUES(nombre), nombre_norm = VALUES(nombre_norm),
                datos_carobra = VALUES(datos_carobra), usuario_carga = VALUES(usuario_carga),
                fecha_carga = CURRENT_TIMESTAMP"
        );

        if ($ok) {
            (mysqli_affected_rows($conecta->conexion) > 1) ? $actualizadas++ : $insertadas++;
        } else {
            $omitidas++;
        }
    }

    echo json_encode([
        'ok'          => true,
        'insertadas'  => $insertadas,
        'actualizadas'=> $actualizadas,
        'omitidas'    => $omitidas,
        'recibidas'   => count($filas),
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: importarPolizas
   Recibe filas ya parseadas de "PAGOS QUALITAS.xlsx":
   [{ concepto, poliza }, ...]

   (nombre_norm, poliza) es UNIQUE en la tabla -- recargar el mismo
   archivo ACTUALIZA la fila de ese nombre+póliza en vez de duplicarla
   (ver UNIQUE KEY uq_polizas_nombre_poliza).
════════════════════════════════════════════════════════════ */
if ($action === 'importarPolizas') {

    $filas = $input['filas'] ?? [];
    if (!is_array($filas)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de filas inválido.']);
        exit;
    }

    $usuario    = $_SESSION['nombre'] ?? '';
    $usuarioEsc = mysqli_real_escape_string($conecta->conexion, $usuario);
    $insertadas = 0;
    $actualizadas = 0;
    $omitidas   = 0;

    foreach ($filas as $fila) {
        if (!is_array($fila)) { $omitidas++; continue; }

        $concepto = trim((string) ($fila['concepto'] ?? ''));
        $poliza   = trim((string) ($fila['poliza'] ?? ''));

        /* Sin concepto (nombre) no hay con qué cruzar -- se omite */
        if ($concepto === '' && $poliza === '') {
            $omitidas++;
            continue;
        }

        $nombreNorm = normalizarNombreQualitas($concepto);

        $conceptoEsc   = mysqli_real_escape_string($conecta->conexion, $concepto);
        $polizaEsc     = mysqli_real_escape_string($conecta->conexion, $poliza);
        $nombreNormEsc = mysqli_real_escape_string($conecta->conexion, $nombreNorm);

        $ok = $conecta->consulta(
            "INSERT INTO qualitas.polizas (concepto, nombre_norm, poliza, usuario_carga)
             VALUES ('$conceptoEsc','$nombreNormEsc','$polizaEsc','$usuarioEsc')
             ON DUPLICATE KEY UPDATE
                concepto = VALUES(concepto), usuario_carga = VALUES(usuario_carga),
                fecha_carga = CURRENT_TIMESTAMP"
        );

        if ($ok) {
            (mysqli_affected_rows($conecta->conexion) > 1) ? $actualizadas++ : $insertadas++;
        } else {
            $omitidas++;
        }
    }

    echo json_encode([
        'ok'          => true,
        'insertadas'  => $insertadas,
        'actualizadas'=> $actualizadas,
        'omitidas'    => $omitidas,
        'recibidas'   => count($filas),
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: ejecutarCruce
   Cruza form <-> polizas por nombre_norm y (re)arma la tabla
   qualitas. Si un mismo nombre_norm tiene varios registros en
   `form` (formulario re-enviado), se usa el más reciente (MAX id).
   Es idempotente: se puede correr las veces que se quiera --
   ON DUPLICATE KEY UPDATE sobre id_poliza evita duplicar renglones.
════════════════════════════════════════════════════════════ */
if ($action === 'ejecutarCruce') {

    $sql = "INSERT INTO qualitas.qualitas (nombre, poliza, curp, id_form, id_poliza)
            SELECT p.concepto, p.poliza, f.curp, f.id, p.id
            FROM qualitas.polizas p
            INNER JOIN (
                SELECT f1.id, f1.nombre_norm, f1.curp
                FROM qualitas.form f1
                INNER JOIN (
                    SELECT nombre_norm, MAX(id) AS max_id
                    FROM qualitas.form
                    WHERE nombre_norm <> ''
                    GROUP BY nombre_norm
                ) f2 ON f2.nombre_norm = f1.nombre_norm AND f2.max_id = f1.id
            ) f ON f.nombre_norm = p.nombre_norm
            WHERE p.nombre_norm <> ''
            ON DUPLICATE KEY UPDATE
                nombre      = VALUES(nombre),
                poliza      = VALUES(poliza),
                curp        = VALUES(curp),
                id_form     = VALUES(id_form),
                fecha_cruce = CURRENT_TIMESTAMP";

    $ok = $conecta->consulta($sql);

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al ejecutar el cruce: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(array_merge(['ok' => true], obtenerResumenQualitas($conecta)));
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getResumen
   Conteos para las cards de la vista (form / polizas / qualitas /
   pólizas sin match todavía).
════════════════════════════════════════════════════════════ */
function obtenerResumenQualitas($conecta) {
    $rsForm = $conecta->consulta("SELECT COUNT(*) AS c FROM qualitas.form");
    $rsPol  = $conecta->consulta("SELECT COUNT(*) AS c FROM qualitas.polizas");
    $rsQual = $conecta->consulta("SELECT COUNT(*) AS c FROM qualitas.qualitas");
    $rsSin  = $conecta->consulta(
        "SELECT COUNT(*) AS c FROM qualitas.polizas p
         LEFT JOIN qualitas.qualitas q ON q.id_poliza = p.id
         WHERE q.id IS NULL"
    );

    return [
        'total_form'        => $rsForm ? intval(mysqli_fetch_assoc($rsForm)['c']) : 0,
        'total_polizas'     => $rsPol  ? intval(mysqli_fetch_assoc($rsPol)['c'])  : 0,
        'total_qualitas'    => $rsQual ? intval(mysqli_fetch_assoc($rsQual)['c']) : 0,
        'polizas_sin_match' => $rsSin  ? intval(mysqli_fetch_assoc($rsSin)['c'])  : 0,
    ];
}

if ($action === 'getResumen') {
    echo json_encode(array_merge(['ok' => true], obtenerResumenQualitas($conecta)));
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: listarQualitas
   Devuelve el cruce final (nombre, poliza, curp). Límite alto pero
   acotado para no tumbar la vista si algún día son millones de filas.
════════════════════════════════════════════════════════════ */
if ($action === 'listarQualitas') {

    $limite = 5000;
    $bd = $conecta->consulta(
        "SELECT nombre, poliza, curp, fecha_cruce
         FROM qualitas.qualitas
         ORDER BY fecha_cruce DESC, id DESC
         LIMIT $limite"
    );

    $filas = [];
    if ($bd) {
        while ($tb = mysqli_fetch_assoc($bd)) {
            $filas[] = $tb;
        }
    }

    echo json_encode([
        'ok'       => true,
        'filas'    => $filas,
        'limitado' => (count($filas) >= $limite),
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);