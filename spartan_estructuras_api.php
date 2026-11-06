<?php
/* ════════════════════════════════════════════════════════════
   spartan_estructuras_api.php  --  Estructuras asignadas por Espartano

   Tabla de permisos: spartan_estructura_permisos
     idPrimaria (PK), clave_spartan varchar(20), sc varchar(50), creado_en
   (antes id_gerencia; ahora guarda el SC -- el que manda en la estructura.
    Con el SC se obtienen todos los folios de la estructura.)

   Acciones:
     listar       -> espartanos (empl_usuarios) con buscador, activo, super admin y # de estructuras
     estructuras  -> catálogo de estructuras = SC distintos de estruc_empleados
     getAsignadas -> SC asignados a un espartano
     toggle       -> asigna / quita un SC a un espartano (se guarda al instante)

   Acceso: solo los usuarios hardcodeados en SP_SUPER_ADMINS.
════════════════════════════════════════════════════════════ */

session_start();
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'msg' => 'Error interno: ' . $err['message'] . ' en ' . basename($err['file']) . ':' . $err['line']]);
    }
});

if (!isset($_SESSION['ultimoAcceso']) || !isset($_SESSION['autentifica'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sesión no válida.']); exit;
}

ob_start();
include('./funciones/clas_carobra.php');
ob_end_clean();

$conecta = new conector();
$cx      = $conecta->conexion;

/* ── Ajustes ── */
const SP_DB_PERM     = 'carobra_2014';                 // BD donde vive spartan_estructura_permisos
const SP_TABLA       = 'spartan_estructura_permisos';
const SP_DB_EMP      = 'carobra_2014';                 // BD de empl_usuarios y estruc_empleados
const SP_SUPER_ADMINS = ['ANKAHEHE', 'GAGORO', 'JOCATORA', 'ADMINISTRA'];   // hardcodeados
const SP_MAX_LISTA   = 300;

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

function spFail($m) { echo json_encode(['ok' => false, 'msg' => $m]); exit; }
function spEsc($cx, $v) { return mysqli_real_escape_string($cx, utf8_decode((string)$v)); }
function spT() { return SP_DB_PERM . '.' . SP_TABLA; }

/* ── Quién es el usuario de la sesión (por nombre -> clave) ── */
$nomSes = mysqli_real_escape_string($cx, utf8_decode($_SESSION['nombre'] ?? ''));
$rsU    = mysqli_query($cx, "SELECT clave FROM " . SP_DB_EMP . ".empl_usuarios WHERE nombre='{$nomSes}' LIMIT 1");
$rowU   = $rsU ? mysqli_fetch_assoc($rsU) : null;
$claveSesion = $rowU ? strtoupper(trim($rowU['clave'])) : '';
if (!in_array($claveSesion, SP_SUPER_ADMINS, true)) spFail('No tienes permiso para esta sección.');

/* ═════════ listar ═════════ */
if ($action === 'listar') {
    $q = trim((string)($input['q'] ?? ''));
    $w = '';
    if ($q !== '') {
        $e = spEsc($cx, $q);
        $w = " WHERE (u.clave LIKE '%{$e}%' OR u.nombre LIKE '%{$e}%') ";
    }
    $rs = mysqli_query($cx, "SELECT u.clave, u.nombre, u.activo FROM " . SP_DB_EMP . ".empl_usuarios u
                             {$w} ORDER BY (u.activo = 1) DESC, u.nombre LIMIT " . SP_MAX_LISTA);
    if (!$rs) spFail('No se pudo listar: ' . mysqli_error($cx));
    $items = []; $claves = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $cl = trim($r['clave']);
        $items[] = [
            'clave'  => $cl,
            'nombre' => utf8_encode(trim((string)$r['nombre'])),
            'activo' => intval($r['activo']) === 1,
            'super'  => in_array(strtoupper($cl), SP_SUPER_ADMINS, true),
            'n'      => 0,
        ];
        $claves[] = "'" . mysqli_real_escape_string($cx, $cl) . "'";
    }
    if ($claves) {   // # de estructuras asignadas por espartano (sin JOIN para evitar choque de collations)
        $rs2 = mysqli_query($cx, "SELECT clave_spartan, COUNT(*) n FROM " . spT() . "
                                  WHERE clave_spartan IN (" . implode(',', $claves) . ") GROUP BY clave_spartan");
        $cnt = [];
        while ($rs2 && ($r = mysqli_fetch_assoc($rs2))) $cnt[strtoupper(trim($r['clave_spartan']))] = intval($r['n']);
        foreach ($items as &$it) $it['n'] = $cnt[strtoupper($it['clave'])] ?? 0;
        unset($it);
    }
    echo json_encode(['ok' => true, 'items' => $items, 'truncado' => count($items) >= SP_MAX_LISTA]);
    exit;
}

/* ═════════ estructuras (catálogo = SC distintos de estruc_empleados) ═════════ */
if ($action === 'estructuras') {
    $rs = mysqli_query($cx, "SELECT TRIM(sc) AS sc, COUNT(*) AS n FROM " . SP_DB_EMP . ".estruc_empleados
                             WHERE sc IS NOT NULL AND TRIM(sc) <> '' GROUP BY TRIM(sc) ORDER BY TRIM(sc)");
    if (!$rs) spFail('No se pudo leer el catálogo de estructuras: ' . mysqli_error($cx));
    $items = [];
    while ($r = mysqli_fetch_assoc($rs)) $items[] = ['sc' => utf8_encode($r['sc']), 'n' => intval($r['n'])];
    echo json_encode(['ok' => true, 'items' => $items]);
    exit;
}

/* ═════════ getAsignadas ═════════ */
if ($action === 'getAsignadas') {
    $cl = spEsc($cx, trim((string)($input['clave'] ?? '')));
    if ($cl === '') spFail('Falta la clave.');
    $rs = mysqli_query($cx, "SELECT sc FROM " . spT() . " WHERE clave_spartan = '{$cl}'");
    if (!$rs) spFail('No se pudo leer las asignaciones: ' . mysqli_error($cx));
    $items = [];
    while ($r = mysqli_fetch_assoc($rs)) $items[] = utf8_encode(trim($r['sc']));
    echo json_encode(['ok' => true, 'items' => $items]);
    exit;
}

/* ═════════ toggle ═════════ */
if ($action === 'toggle') {
    $clave = trim((string)($input['clave'] ?? ''));
    $sc    = trim((string)($input['sc'] ?? ''));
    $on    = !empty($input['on']);
    if ($clave === '' || $sc === '') spFail('Faltan datos.');
    if (mb_strlen($sc, 'UTF-8') > 50) spFail('El SC excede 50 caracteres.');
    $clE = spEsc($cx, $clave);
    $scE = spEsc($cx, $sc);

    $rsC = mysqli_query($cx, "SELECT 1 FROM " . SP_DB_EMP . ".empl_usuarios WHERE clave = '{$clE}' LIMIT 1");
    if (!$rsC || mysqli_num_rows($rsC) === 0) spFail('El espartano no existe.');

    if ($on) {
        $rsS = mysqli_query($cx, "SELECT 1 FROM " . SP_DB_EMP . ".estruc_empleados WHERE TRIM(sc) = '{$scE}' LIMIT 1");
        if (!$rsS || mysqli_num_rows($rsS) === 0) spFail('Esa estructura (SC) no existe en estruc_empleados.');
        $rsX = mysqli_query($cx, "SELECT 1 FROM " . spT() . " WHERE clave_spartan = '{$clE}' AND sc = '{$scE}' LIMIT 1");
        if ($rsX && mysqli_num_rows($rsX) > 0) { echo json_encode(['ok' => true]); exit; }   // ya estaba
        $ok = mysqli_query($cx, "INSERT INTO " . spT() . " (clave_spartan, sc) VALUES ('{$clE}', '{$scE}')") !== false;
    } else {
        $ok = mysqli_query($cx, "DELETE FROM " . spT() . " WHERE clave_spartan = '{$clE}' AND sc = '{$scE}'") !== false;
    }
    if (!$ok) spFail('No se pudo guardar: ' . mysqli_error($cx));
    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);