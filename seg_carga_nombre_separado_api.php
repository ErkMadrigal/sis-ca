<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* Los ~18 mil registros llegan en LOTES desde el frontend (no todo
   de un jalón), así que cada request individual es rápida. Aun así
   se sube un poco el límite de tiempo por si un lote coincide con
   un momento lento del servidor. */
set_time_limit(120);

session_start();
header('Content-Type: application/json');

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   Carga masiva de carobra.usuarios_nombre_separado desde un xlsx
   con columnas Clave / Nombre(s) / Apellido paterno / Apellido
   materno. Pensada para ~18 mil+ filas de golpe: el frontend NO
   manda todo en una sola petición, lo trocea en LOTES (por default
   500 filas) y aquí cada lote se aplica con UN SOLO INSERT
   multi-fila, en vez de una consulta por renglón. Con 18,000
   consultas individuales esto se hubiera tardado una eternidad (o
   reventado por timeout); así son ~36 consultas en vez de 18,000.

   REGLA: si la clave YA EXISTE, no se toca (no se sobreescribe);
   si NO existe, se inserta. Por eso es INSERT IGNORE (sin
   ON DUPLICATE KEY UPDATE): un choque de llave duplicada se ignora
   en silencio en vez de actualizar.

   Esto también es lo que arregla el problema real de "se me
   perdieron 6 mil filas": con un INSERT ... ON DUPLICATE KEY UPDATE
   normal, si UNA sola fila del lote de 500 tenía un dato inválido
   (por ejemplo un nombre más largo que la columna, o algún carácter
   raro que MySQL rechaza en modo estricto), MySQL aborta el
   INSERT COMPLETO y las 500 filas de ese lote se pierden juntas —
   si eso pasó en ~12 de los 37 lotes, ahí están los ~6,000
   faltantes. INSERT IGNORE convierte esos errores por fila en
   advertencias: esa fila puntual se salta, pero el resto del lote
   sí se inserta. Además aquí se recorta defensivamente cada valor
   al tamaño de su columna antes de mandarlo, para minimizar que
   eso vuelva a pasar.
════════════════════════════════════════════════════════════ */

function normalizarTexto($v) {
    return trim((string) $v);
}

if ($action === 'cargarLoteNombreSeparado') {

    $filas = $input['filas'] ?? [];
    if (!is_array($filas) || !count($filas)) {
        echo json_encode(['ok' => false, 'msg' => 'Lote vacío o inválido.']);
        exit;
    }

    /* Límite defensivo: aunque el frontend ya trocea en lotes de
       ~500, si alguien manda un lote gigante por error, no se arma
       un INSERT descomunal de un jalón. */
    if (count($filas) > 2000) {
        echo json_encode(['ok' => false, 'msg' => 'Lote demasiado grande (máx. 2000 filas por lote).']);
        exit;
    }

    $valores  = [];
    $sinClave = 0;

    foreach ($filas as $f) {
        $clave = strtoupper(normalizarTexto($f['clave'] ?? ''));
        if ($clave === '') { $sinClave++; continue; }

        $nombres = strtoupper(normalizarTexto($f['nombres'] ?? ''));
        $apPat   = strtoupper(normalizarTexto($f['apellido_paterno'] ?? ''));
        $apMat   = strtoupper(normalizarTexto($f['apellido_materno'] ?? ''));

        /* Recorte defensivo al tamaño real de cada columna
           (clave VARCHAR(20), los demás VARCHAR(100)) para que un
           dato demasiado largo no tumbe la fila (ni, peor, el lote
           completo bajo modo estricto). */
        $clave   = substr($clave, 0, 20);
        $nombres = substr($nombres, 0, 100);
        $apPat   = substr($apPat, 0, 100);
        $apMat   = substr($apMat, 0, 100);

        $claveEsc   = mysqli_real_escape_string($conecta->conexion, utf8_decode($clave));
        $nombresEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombres));
        $apPatEsc   = mysqli_real_escape_string($conecta->conexion, utf8_decode($apPat));
        $apMatEsc   = mysqli_real_escape_string($conecta->conexion, utf8_decode($apMat));

        $valores[] = "('{$claveEsc}', '{$nombresEsc}', '{$apPatEsc}', '{$apMatEsc}')";
    }

    if (!count($valores)) {
        echo json_encode([
            'ok'         => true,
            'insertadas' => 0,
            'omitidas'   => 0,
            'sin_clave'  => $sinClave,
            'recibidas'  => count($filas),
        ]);
        exit;
    }

    /* INSERT IGNORE multi-fila: si la clave ya existe, esa fila se
       ignora tal cual (no se modifica el registro existente); si no
       existe, se inserta. Una fila con datos problemáticos también
       se ignora sin tumbar las demás del mismo lote. */
    $sql = "INSERT IGNORE INTO carobra.usuarios_nombre_separado (clave, nombres, apellido_paterno, apellido_materno)
            VALUES " . implode(',', $valores);

    $ok = $conecta->consulta($sql);

    if (!$ok) {
        echo json_encode([
            'ok'  => false,
            'msg' => 'Error al guardar el lote: ' . mysqli_error($conecta->conexion),
        ]);
        exit;
    }

    /* affected_rows con INSERT IGNORE = cuántas filas realmente se
       insertaron de nuevo (las que chocaron por clave duplicada, u
       otro motivo, NO se cuentan aquí). */
    $insertadas = mysqli_affected_rows($conecta->conexion);
    $omitidas   = count($valores) - $insertadas;

    echo json_encode([
        'ok'         => true,
        'insertadas' => $insertadas,
        'omitidas'   => $omitidas,
        'sin_clave'  => $sinClave,
        'recibidas'  => count($filas),
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);