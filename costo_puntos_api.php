<?php
/* ════════════════════════════════════════════════════════════
   costo_puntos_api.php  --  pagos.costo_puntos

   Acciones:
     listar        -> todas las filas (activas e inactivas)
     guardar       -> alta (id=0) o edición (id>0)
     toggleActivo  -> activo / inactivo (el borrado es LÓGICO: "quitar" = activo 0)
     listarTabulador / guardarTabulador / toggleTabulador -> tabulador de puntos (tabla pivote edad × salario por esquema)
     listarInactivos / guardarInactivo / toggleInactivo -> tabla de clientes inactivos (puntos por rango de edad)
     listarBonos / guardarBono / toggleBono, listarBonoCampanas / guardarBonoCampana / toggleBonoCampana -> BONOS (pagos.bono_*)

   Validaciones (también en la pantalla, pero aquí es la puerta real):
     puntos        entero no negativo (en la vista se rotula "Monto" y se muestra como moneda; columna `monto` en la BD)
     fec_ini/fec_fin  fechas válidas (Y-m-d) y fec_fin >= fec_ini
     campaña       salario inicial y final numéricos >= 0 y salario_ini <= salario_fin
     costo normal  sus rangos de fechas ACTIVOS no pueden traslaparse
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

const CP_TABLA = 'pagos.costo_puntos';
/* Columna `campana` (VARCHAR): el costo por puntos normal siempre dice 'Comisión base'; una campaña especial lleva su propio nombre. */
const CP_BASE  = 'Comisión base';

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ── Solo Super Usuario (id_nivel 12) ── */
function cpEsSuperUsuario($conecta) {
    $nom = mysqli_real_escape_string($conecta->conexion, utf8_decode($_SESSION['nombre'] ?? ''));
    $rs  = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE nombre='{$nom}' LIMIT 1");
    $r   = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$r || $r['clave'] === '') return false;
    $cl  = mysqli_real_escape_string($conecta->conexion, $r['clave']);
    $rs2 = $conecta->consulta("SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$cl}'");
    while ($rs2 && ($f = mysqli_fetch_assoc($rs2))) {
        if (intval($f['id_nivel']) === 12) return true;
    }
    return false;
}
if (!cpEsSuperUsuario($conecta)) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para esta sección (se requiere Super Usuario).']); exit;
}

mysqli_set_charset($conecta->conexion, 'utf8mb4');   /* las tablas de la BD pagos son utf8mb4 (acentos en nombre de campaña) */
$usuario = mysqli_real_escape_string($conecta->conexion, $_SESSION['nombre'] ?? '');

function cpFechaOk($f) {
    if (!is_string($f) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $f)) return false;
    list($y, $m, $d) = array_map('intval', explode('-', $f));
    return checkdate($m, $d, $y);
}

/* ═════════ listar ═════════ */
if ($action === 'listar') {
    $rs = $conecta->consulta("SELECT id, campana, monto, fec_ini, fec_fin, salario_ini, salario_fin, activo
                              FROM " . CP_TABLA . "
                              ORDER BY (campana <> '" . CP_BASE . "'), campana, fec_ini, id");
    if (!$rs) { echo json_encode(['ok' => false, 'msg' => 'No se pudo leer la tabla: ' . mysqli_error($conecta->conexion)]); exit; }
    $filas = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $filas[] = [
            'id'          => intval($r['id']),
            'campana'     => ($r['campana'] === null || $r['campana'] === '') ? CP_BASE : $r['campana'],
            'puntos'      => intval($r['monto']),   /* la clave JSON sigue llamándose 'puntos' (la vista la usa así) */
            'fec_ini'     => $r['fec_ini'],
            'fec_fin'     => $r['fec_fin'],
            'salario_ini' => $r['salario_ini'] === null ? '' : $r['salario_ini'],
            'salario_fin' => $r['salario_fin'] === null ? '' : $r['salario_fin'],
            'activo'      => intval($r['activo']),
        ];
    }
    echo json_encode(['ok' => true, 'filas' => $filas]);
    exit;
}

/* ═════════ guardar ═════════ */
if ($action === 'guardar') {

    $id        = intval($input['id'] ?? 0);
    $esCampana = (($input['tipo'] ?? '') === 'campana') ? 1 : 0;   /* solo del formulario; en la BD lo que cuenta es `campana` */
    $puntosIn  = str_replace(',', '', trim((string)($input['puntos'] ?? '')));
    $fecIni    = trim((string)($input['fec_ini'] ?? ''));
    $fecFin    = trim((string)($input['fec_fin'] ?? ''));

    if (!preg_match('/^\d{1,9}$/', $puntosIn)) {
        echo json_encode(['ok' => false, 'msg' => 'El monto debe ser un número entero no negativo.']); exit;
    }
    $puntos = intval($puntosIn);

    if (!cpFechaOk($fecIni) || !cpFechaOk($fecFin)) {
        echo json_encode(['ok' => false, 'msg' => 'Captura las fechas "Del" y "Al" válidas.']); exit;
    }
    if ($fecFin < $fecIni) {
        echo json_encode(['ok' => false, 'msg' => 'La fecha "Al" no puede ser anterior a "Del".']); exit;
    }

    $salIniSql = 'NULL'; $salFinSql = 'NULL';
    if ($esCampana) {
        $si = str_replace(',', '', trim((string)($input['salario_ini'] ?? '')));
        $sf = str_replace(',', '', trim((string)($input['salario_fin'] ?? '')));
        if ($si === '' || $sf === '' || !is_numeric($si) || !is_numeric($sf) || floatval($si) < 0 || floatval($sf) < 0) {
            echo json_encode(['ok' => false, 'msg' => 'La campaña especial necesita salario inicial y final (números, no negativos).']); exit;
        }
        if (floatval($si) > floatval($sf)) {
            echo json_encode(['ok' => false, 'msg' => 'El salario inicial no puede ser mayor al final.']); exit;
        }
        $salIniSql = number_format(floatval($si), 2, '.', '');
        $salFinSql = number_format(floatval($sf), 2, '.', '');
    }

    /* Si es edición, el tipo (costo/campaña) y el estatus no cambian desde aquí */
    $activo = 1;
    if ($id > 0) {
        $rs = $conecta->consulta("SELECT campana, activo FROM " . CP_TABLA . " WHERE id={$id} LIMIT 1");
        $act = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$act) { echo json_encode(['ok' => false, 'msg' => 'El registro no existe.']); exit; }
        $esCampana = ($act['campana'] !== CP_BASE) ? 1 : 0;
        $activo    = intval($act['activo']);
    }

    /* Campaña: una campaña especial SIEMPRE lleva el nombre que se captura (ej. "Campaña Halloween");
       el costo por puntos normal se guarda siempre como "Comisión base". */
    if ($esCampana) {
        $campana = trim((string)($input['campana'] ?? ''));
        if ($campana === '') { echo json_encode(['ok' => false, 'msg' => 'Captura el nombre de la campaña especial.']); exit; }
        if (mb_strlen($campana, 'UTF-8') > 100) { echo json_encode(['ok' => false, 'msg' => 'El nombre de la campaña no puede pasar de 100 caracteres.']); exit; }
        if (mb_strtolower($campana, 'UTF-8') === mb_strtolower(CP_BASE, 'UTF-8')) { echo json_encode(['ok' => false, 'msg' => '"' . CP_BASE . '" está reservado para el costo por pago normal. Ponle otro nombre a la campaña.']); exit; }
    } else {
        $campana = CP_BASE;
    }
    $campEsc = mysqli_real_escape_string($conecta->conexion, $campana);

    /* Costo por puntos: los rangos de fechas ACTIVOS no se pueden traslapar */
    if (!$esCampana && $activo === 1) {
        $rs = $conecta->consulta("SELECT id, monto, fec_ini, fec_fin FROM " . CP_TABLA . "
                                  WHERE campana='{$campEsc}' AND activo=1 AND id<>{$id}
                                    AND fec_ini <= '{$fecFin}' AND fec_fin >= '{$fecIni}' LIMIT 1");
        if ($rs && ($c = mysqli_fetch_assoc($rs))) {
            echo json_encode(['ok' => false, 'msg' => 'Se traslapa con el costo de $' . number_format((float)$c['monto'], 2) . ' (del ' . $c['fec_ini'] . ' al ' . $c['fec_fin'] . '). Ajusta las fechas o desactiva ese registro.']); exit;
        }
    }

    if ($id > 0) {
        $ok = (mysqli_query($conecta->conexion, "UPDATE " . CP_TABLA . " SET campana='{$campEsc}', monto={$puntos}, fec_ini='{$fecIni}', fec_fin='{$fecFin}',
                                         salario_ini={$salIniSql}, salario_fin={$salFinSql}, modificado_por='{$usuario}'
                                  WHERE id={$id}") !== false);
    } else {
        $ok = (mysqli_query($conecta->conexion, "INSERT INTO " . CP_TABLA . " (campana, monto, fec_ini, fec_fin, salario_ini, salario_fin, activo, creado_por)
                                  VALUES ('{$campEsc}', {$puntos}, '{$fecIni}', '{$fecFin}', {$salIniSql}, {$salFinSql}, 1, '{$usuario}')") !== false);
        if ($ok) $id = intval(mysqli_insert_id($conecta->conexion));
    }
    if (!$ok) { echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar: ' . mysqli_error($conecta->conexion)]); exit; }
    echo json_encode(['ok' => true, 'id' => $id, 'msg' => 'Guardado.']);
    exit;
}

/* ═════════ toggleActivo ═════════ */
if ($action === 'toggleActivo') {
    $id     = intval($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;
    if ($id <= 0) { echo json_encode(['ok' => false, 'msg' => 'Registro inválido.']); exit; }

    /* Al reactivar un costo por pago, que no choque con otro activo */
    if ($activo === 1) {
        $rs = $conecta->consulta("SELECT campana, fec_ini, fec_fin FROM " . CP_TABLA . " WHERE id={$id} LIMIT 1");
        $f  = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$f) { echo json_encode(['ok' => false, 'msg' => 'El registro no existe.']); exit; }
        if ($f['campana'] === CP_BASE) {
            $rs2 = $conecta->consulta("SELECT monto, fec_ini, fec_fin FROM " . CP_TABLA . "
                                       WHERE campana='" . CP_BASE . "' AND activo=1 AND id<>{$id}
                                         AND fec_ini <= '{$f['fec_fin']}' AND fec_fin >= '{$f['fec_ini']}' LIMIT 1");
            if ($rs2 && ($c = mysqli_fetch_assoc($rs2))) {
                echo json_encode(['ok' => false, 'msg' => 'No se puede activar: se traslapa con el costo de $' . number_format((float)$c['monto'], 2) . ' (del ' . $c['fec_ini'] . ' al ' . $c['fec_fin'] . ').']); exit;
            }
        }
    }
    $ok = (mysqli_query($conecta->conexion, "UPDATE " . CP_TABLA . " SET activo={$activo}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conecta->conexion)]);
    exit;
}

/* ═════════ Tabulador: una tabla pivote por esquema (cabecera + detalle) ═════════
   costo_puntos_tabulador      -> esquema (vigencia, activo); siempre es la "Comisión base", no lleva nombre
   costo_puntos_tabulador_det  -> una fila por celda (edad × salario) de ese esquema
   Editar o desactivar afecta SIEMPRE al esquema completo. */
const CP_TAB = 'pagos.costo_puntos_tabulador';
const CP_DET = 'pagos.costo_puntos_tabulador_det';
mysqli_set_charset($conecta->conexion, 'utf8mb4');

function cpTabFail($m) { echo json_encode(['ok' => false, 'msg' => $m]); exit; }

/* Otro esquema ACTIVO que se cruce en fechas */
function cpTabSolapa($conecta, $id, $fIni, $fFin) {
    $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM " . CP_TAB . "
                              WHERE activo=1 AND id<>{$id} AND fec_ini <= '{$fFin}' AND fec_fin >= '{$fIni}' LIMIT 1");
    return $rs ? mysqli_fetch_assoc($rs) : null;
}
function cpTabMsgSolapa($c) {
    return 'Se traslapa con otro esquema activo (del ' . $c['fec_ini'] . ' al ' . $c['fec_fin'] . '). Ajusta las fechas o desactiva ese esquema.';
}

if ($action === 'listarTabulador') {
    $rs = $conecta->consulta("SELECT id, fec_ini, fec_fin, activo FROM " . CP_TAB . " ORDER BY fec_ini, id");
    if (!$rs) cpTabFail('No se pudo leer el tabulador (¿ya corriste costo_puntos_tabulador.sql?): ' . mysqli_error($conecta->conexion));
    $esq = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $esq[intval($r['id'])] = ['id' => intval($r['id']), 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin'],
                                  'activo' => intval($r['activo']), '_e' => [], '_s' => [], '_c' => []];
    }
    $rd = $conecta->consulta("SELECT tab_id, edad_ini, edad_fin, sal_ini, sal_fin, puntos FROM " . CP_DET . " ORDER BY tab_id, edad_ini, sal_ini");
    if (!$rd) cpTabFail('No se pudo leer el detalle del tabulador: ' . mysqli_error($conecta->conexion));
    while ($r = mysqli_fetch_assoc($rd)) {
        $t = intval($r['tab_id']);
        if (!isset($esq[$t])) continue;
        $ke = intval($r['edad_ini']) . '-' . intval($r['edad_fin']);
        $sf = $r['sal_fin'] === null ? null : floatval($r['sal_fin']);
        $ks = floatval($r['sal_ini']) . '|' . ($sf === null ? '' : $sf);
        $esq[$t]['_e'][$ke] = ['ini' => intval($r['edad_ini']), 'fin' => intval($r['edad_fin'])];
        $esq[$t]['_s'][$ks] = ['ini' => floatval($r['sal_ini']), 'fin' => $sf];
        $esq[$t]['_c'][$ke][$ks] = intval($r['puntos']);
    }
    $out = [];
    foreach ($esq as $e) {
        uasort($e['_e'], function ($a, $b) { return $a['ini'] <=> $b['ini']; });
        uasort($e['_s'], function ($a, $b) { return $a['ini'] <=> $b['ini']; });
        $m = [];
        foreach ($e['_e'] as $ke => $_) {
            $fila = [];
            foreach ($e['_s'] as $ks => $__) $fila[] = $e['_c'][$ke][$ks] ?? 0;
            $m[] = $fila;
        }
        $out[] = ['id' => $e['id'], 'fec_ini' => $e['fec_ini'], 'fec_fin' => $e['fec_fin'], 'activo' => $e['activo'],
                  'edades' => array_values($e['_e']), 'salarios' => array_values($e['_s']), 'puntos' => $m];
    }
    echo json_encode(['ok' => true, 'esquemas' => $out]);
    exit;
}

if ($action === 'guardarTabulador') {
    $id       = intval($input['id'] ?? 0);
    $fIni     = trim((string)($input['fec_ini'] ?? ''));
    $fFin     = trim((string)($input['fec_fin'] ?? ''));
    $edades   = $input['edades']   ?? [];
    $salarios = $input['salarios'] ?? [];
    $puntos   = $input['puntos']   ?? [];

    if (!cpFechaOk($fIni) || !cpFechaOk($fFin)) cpTabFail('Captura las fechas inicial y final válidas.');
    if ($fFin < $fIni) cpTabFail('La fecha final no puede ser anterior a la inicial.');
    if (!is_array($edades) || !is_array($salarios) || !count($edades) || !count($salarios)) cpTabFail('El esquema necesita al menos una edad y un salario.');
    if (count($puntos) !== count($edades)) cpTabFail('La matriz de puntos no coincide con las edades.');

    $E = []; $prev = -1;
    foreach ($edades as $i => $e) {
        $ini = (string)($e['ini'] ?? ''); $fin = (string)($e['fin'] ?? '');
        if (!preg_match('/^\d{1,3}$/', $ini) || !preg_match('/^\d{1,3}$/', $fin) || intval($ini) > 120 || intval($fin) > 120) cpTabFail('Edad ' . ($i + 1) . ': deben ser enteros entre 0 y 120.');
        $ini = intval($ini); $fin = intval($fin);
        if ($fin < $ini) cpTabFail('Edad ' . ($i + 1) . ': la edad final no puede ser menor a la inicial.');
        if ($ini <= $prev) cpTabFail('Edad ' . ($i + 1) . ': los rangos de edad no pueden traslaparse ni ir desordenados.');
        $prev = $fin; $E[] = [$ini, $fin];
    }

    $S = []; $pf = null; $n = count($salarios);
    foreach ($salarios as $j => $s) {
        $ini = str_replace(',', '', trim((string)($s['ini'] ?? '')));
        $fin = $s['fin'] ?? null;
        $fin = ($fin === null || trim((string)$fin) === '') ? null : str_replace(',', '', trim((string)$fin));
        $c = 'Salario ' . ($j + 1) . ': ';
        if ($ini === '' || !is_numeric($ini) || floatval($ini) < 0) cpTabFail($c . 'salario inicial inválido.');
        if ($fin === null) cpTabFail($c . 'el salario "Hasta" es obligatorio.');
        if (!is_numeric($fin) || floatval($fin) <= floatval($ini)) cpTabFail($c . 'el final debe ser mayor al inicial.');
        if ($pf !== null && floatval($ini) < $pf) cpTabFail($c . 'los rangos de salario no pueden traslaparse ni ir desordenados.');
        $pf = $fin === null ? null : floatval($fin);
        $S[] = [number_format(floatval($ini), 2, '.', ''), $fin === null ? 'NULL' : number_format(floatval($fin), 2, '.', '')];
    }
    foreach ($puntos as $i => $fila) {
        if (!is_array($fila) || count($fila) !== $n) cpTabFail('La edad ' . ($i + 1) . ' no tiene todos los salarios.');
        foreach ($fila as $j => $p) {
            if (!preg_match('/^\d{1,9}$/', trim((string)$p))) cpTabFail('Puntos inválidos en la edad ' . ($i + 1) . ', salario ' . ($j + 1) . ' (entero no negativo).');
        }
    }

    $activo = 1;
    if ($id > 0) {
        $rs = $conecta->consulta("SELECT activo FROM " . CP_TAB . " WHERE id={$id} LIMIT 1");
        $act = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$act) cpTabFail('El esquema no existe.');
        $activo = intval($act['activo']);
    }
    if ($activo === 1 && ($c = cpTabSolapa($conecta, $id, $fIni, $fFin))) cpTabFail(cpTabMsgSolapa($c));

    $cx = $conecta->conexion;
    /* Escrituras directo con mysqli_query: el valor de retorno de $conecta->consulta() no es
       confiable en INSERT/DELETE que afectan 0 filas (ej. al duplicar: el DELETE de un id nuevo
       no borra nada y se tomaba como error sin mensaje). Aquí solo false = error real. */
    $q = function ($sql) use ($cx) { return mysqli_query($cx, $sql) !== false; };
    mysqli_begin_transaction($cx);
    if ($id > 0) {
        $ok = $q("UPDATE " . CP_TAB . " SET fec_ini='{$fIni}', fec_fin='{$fFin}', modificado_por='{$usuario}' WHERE id={$id}");
    } else {
        $ok = $q("INSERT INTO " . CP_TAB . " (fec_ini, fec_fin, activo, creado_por) VALUES ('{$fIni}', '{$fFin}', 1, '{$usuario}')");
        if ($ok) $id = intval(mysqli_insert_id($cx));
    }
    if ($ok) $ok = $q("DELETE FROM " . CP_DET . " WHERE tab_id={$id}");
    if ($ok) {
        $vals = [];
        foreach ($E as $i => $e) foreach ($S as $j => $s) {
            $vals[] = "({$id}, {$e[0]}, {$e[1]}, {$s[0]}, {$s[1]}, " . intval(trim((string)$puntos[$i][$j])) . ")";
        }
        $ok = $q("INSERT INTO " . CP_DET . " (tab_id, edad_ini, edad_fin, sal_ini, sal_fin, puntos) VALUES " . implode(',', $vals));
    }
    if (!$ok) { $err = mysqli_error($cx); mysqli_rollback($cx); cpTabFail('No se pudo guardar: ' . $err); }
    mysqli_commit($cx);
    echo json_encode(['ok' => true, 'id' => $id, 'msg' => 'Esquema guardado.']);
    exit;
}

if ($action === 'toggleTabulador') {
    $id     = intval($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;
    if ($id <= 0) cpTabFail('Registro inválido.');
    if ($activo === 1) {
        $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM " . CP_TAB . " WHERE id={$id} LIMIT 1");
        $f  = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$f) cpTabFail('El esquema no existe.');
        if ($c = cpTabSolapa($conecta, $id, $f['fec_ini'], $f['fec_fin'])) cpTabFail('No se puede activar: ' . cpTabMsgSolapa($c));
    }
    $ok = (mysqli_query($conecta->conexion, "UPDATE " . CP_TAB . " SET activo={$activo}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conecta->conexion)]);
    exit;
}

/* ═════════ BONOS (pagos.bono_esquema / bono_esquema_cat / bono_campana) ═════════
   listarBonos / guardarBono / toggleBono                    -> esquema de bonos completo (categorías + Élite + + reglas)
   listarBonoCampanas / guardarBonoCampana / toggleBonoCampana -> campañas especiales de bonos (nombre, Del/Al, multiplicador)
   Editar o desactivar un esquema afecta al esquema completo. Dos esquemas ACTIVOS no se traslapan en fechas;
   tampoco dos campañas ACTIVAS. */
const BO_ESQ  = 'pagos.bono_esquema';
const BO_CAT  = 'pagos.bono_esquema_cat';
const BO_CAMP = 'pagos.bono_campana';

function boFail($m) { echo json_encode(['ok' => false, 'msg' => $m]); exit; }

/* importe -> '1234.50' o null si no es un número válido (no negativo, hasta 2 decimales) */
function boDec($v) {
    $v = str_replace(',', '', trim((string)$v));
    if ($v === '' || !preg_match('/^\d{1,10}(\.\d{1,2})?$/', $v)) return null;
    return number_format((float)$v, 2, '.', '');
}
function boSolapa($conecta, $tabla, $id, $fIni, $fFin) {
    $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM {$tabla}
                              WHERE activo=1 AND id<>{$id} AND fec_ini <= '{$fFin}' AND fec_fin >= '{$fIni}' LIMIT 1");
    return $rs ? mysqli_fetch_assoc($rs) : null;
}
function boMsgSolapa($c, $que) {
    return 'Se traslapa con ' . $que . ' activo (del ' . $c['fec_ini'] . ' al ' . $c['fec_fin'] . '). Ajusta las fechas o desactiva ese registro.';
}

if ($action === 'listarBonos') {
    $rs = $conecta->consulta("SELECT id, fec_ini, fec_fin, pct_categoria, pct_persistencia, reglas, elite_cada,
                                     elite_categoria, elite_persistencia, elite_bono, activo
                              FROM " . BO_ESQ . " ORDER BY fec_ini, id");
    if (!$rs) boFail('No se pudo leer los bonos (¿ya corriste pagos_bonos.sql?): ' . mysqli_error($conecta->conexion));
    $esq = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $tieneElite = ($r['elite_cada'] !== null && intval($r['elite_cada']) > 0);
        $esq[intval($r['id'])] = [
            'id' => intval($r['id']), 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin'],
            'pct_categoria' => intval($r['pct_categoria']), 'pct_persistencia' => intval($r['pct_persistencia']),
            'reglas' => (string)$r['reglas'],
            'elite' => $tieneElite ? ['cada' => intval($r['elite_cada']), 'categoria' => floatval($r['elite_categoria']),
                                      'persistencia' => floatval($r['elite_persistencia']), 'bono' => floatval($r['elite_bono'])] : null,
            'activo' => intval($r['activo']), 'cats' => [],
        ];
    }
    $rc = $conecta->consulta("SELECT esquema_id, categoria, pts_ini, pts_fin, monto_categoria, monto_persistencia, monto_bono
                              FROM " . BO_CAT . " ORDER BY esquema_id, orden, id");
    if (!$rc) boFail('No se pudo leer las categorías del bono: ' . mysqli_error($conecta->conexion));
    while ($r = mysqli_fetch_assoc($rc)) {
        $e = intval($r['esquema_id']);
        if (!isset($esq[$e])) continue;
        $esq[$e]['cats'][] = ['categoria' => $r['categoria'], 'pts_ini' => intval($r['pts_ini']), 'pts_fin' => intval($r['pts_fin']),
                              'monto_categoria' => floatval($r['monto_categoria']), 'monto_persistencia' => floatval($r['monto_persistencia']),
                              'monto_bono' => floatval($r['monto_bono'])];
    }
    echo json_encode(['ok' => true, 'esquemas' => array_values($esq)]);
    exit;
}

if ($action === 'guardarBono') {
    $id    = intval($input['id'] ?? 0);
    $fIni  = trim((string)($input['fec_ini'] ?? ''));
    $fFin  = trim((string)($input['fec_fin'] ?? ''));
    $pctC  = trim((string)($input['pct_categoria'] ?? '85'));
    $pctP  = trim((string)($input['pct_persistencia'] ?? '15'));
    $reg   = trim((string)($input['reglas'] ?? ''));
    $elite = is_array($input['elite'] ?? null) ? $input['elite'] : [];
    $cats  = $input['cats'] ?? [];

    if (!cpFechaOk($fIni) || !cpFechaOk($fFin)) boFail('Captura las fechas inicial y final válidas.');
    if ($fFin < $fIni) boFail('La fecha final no puede ser anterior a la inicial.');
    if (!preg_match('/^\d{1,3}$/', $pctC) || !preg_match('/^\d{1,3}$/', $pctP) || intval($pctC) > 100 || intval($pctP) > 100) boFail('Los porcentajes deben ser enteros entre 0 y 100.');
    if (mb_strlen($reg, 'UTF-8') > 2000) boFail('Las reglas no pueden pasar de 2,000 caracteres.');
    if (!is_array($cats) || !count($cats)) boFail('El esquema necesita al menos una categoría.');

    $C = []; $prev = -1;
    foreach ($cats as $i => $c) {
        $n   = $i + 1;
        $nom = trim((string)($c['categoria'] ?? ''));
        if ($nom === '' || mb_strlen($nom, 'UTF-8') > 60) boFail("Categoría {$n}: captura el nombre (máx. 60 caracteres).");
        $pi = trim((string)($c['pts_ini'] ?? '')); $pf = trim((string)($c['pts_fin'] ?? ''));
        if (!preg_match('/^\d{1,4}$/', $pi) || !preg_match('/^\d{1,4}$/', $pf)) boFail("Categoría {$n}: los puntos deben ser enteros.");
        $pi = intval($pi); $pf = intval($pf);
        if ($pf < $pi) boFail("Categoría {$n}: los puntos \"hasta\" no pueden ser menores a \"desde\".");
        if ($pi <= $prev) boFail("Categoría {$n}: los rangos de puntos no pueden traslaparse ni ir desordenados.");
        $prev = $pf;
        $mc = boDec($c['monto_categoria'] ?? ''); $mp = boDec($c['monto_persistencia'] ?? ''); $mb = boDec($c['monto_bono'] ?? '');
        if ($mc === null || $mp === null || $mb === null) boFail("Categoría {$n}: los montos deben ser importes válidos (no negativos, hasta 2 decimales).");
        $C[] = [$nom, $pi, $pf, $mc, $mp, $mb];
    }

    /* Élite +: o va completa o va vacía */
    $eCada = trim((string)($elite['cada'] ?? ''));
    $eCat  = trim((string)($elite['categoria'] ?? '')); $ePer = trim((string)($elite['persistencia'] ?? '')); $eBon = trim((string)($elite['bono'] ?? ''));
    $eSql  = 'NULL, NULL, NULL, NULL';
    if ($eCada !== '' || $eCat !== '' || $ePer !== '' || $eBon !== '') {
        if (!preg_match('/^\d{1,4}$/', $eCada) || intval($eCada) < 1) boFail('Élite +: captura cada cuántos puntos se suma el incremento (entero mayor a 0).');
        $ec = boDec($eCat); $ep = boDec($ePer); $eb = boDec($eBon);
        if ($ec === null || $ep === null || $eb === null) boFail('Élite +: los tres montos deben ser importes válidos (no negativos, hasta 2 decimales).');
        $eSql = intval($eCada) . ", {$ec}, {$ep}, {$eb}";
    }

    $activo = 1;
    if ($id > 0) {
        $rs = $conecta->consulta("SELECT activo FROM " . BO_ESQ . " WHERE id={$id} LIMIT 1");
        $act = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$act) boFail('El esquema no existe.');
        $activo = intval($act['activo']);
    }
    if ($activo === 1 && ($s = boSolapa($conecta, BO_ESQ, $id, $fIni, $fFin))) boFail(boMsgSolapa($s, 'otro esquema de bonos'));

    $cx = $conecta->conexion;
    $q  = function ($sql) use ($cx) { return mysqli_query($cx, $sql) !== false; };   /* solo false = error real */
    $regEsc = mysqli_real_escape_string($cx, $reg);
    $regSql = $reg === '' ? 'NULL' : "'{$regEsc}'";
    mysqli_begin_transaction($cx);
    if ($id > 0) {
        list($eC, $eM, $eP, $eB) = explode(', ', $eSql);
        $ok = $q("UPDATE " . BO_ESQ . " SET fec_ini='{$fIni}', fec_fin='{$fFin}', pct_categoria=" . intval($pctC) . ", pct_persistencia=" . intval($pctP) . ",
                         reglas={$regSql}, elite_cada={$eC}, elite_categoria={$eM}, elite_persistencia={$eP}, elite_bono={$eB}, modificado_por='{$usuario}'
                  WHERE id={$id}");
    } else {
        $ok = $q("INSERT INTO " . BO_ESQ . " (fec_ini, fec_fin, pct_categoria, pct_persistencia, reglas, elite_cada, elite_categoria, elite_persistencia, elite_bono, activo, creado_por)
                  VALUES ('{$fIni}', '{$fFin}', " . intval($pctC) . ", " . intval($pctP) . ", {$regSql}, {$eSql}, 1, '{$usuario}')");
        if ($ok) $id = intval(mysqli_insert_id($cx));
    }
    if ($ok) $ok = $q("DELETE FROM " . BO_CAT . " WHERE esquema_id={$id}");
    if ($ok) {
        $vals = [];
        foreach ($C as $k => $c) {
            $vals[] = "({$id}, " . ($k + 1) . ", '" . mysqli_real_escape_string($cx, $c[0]) . "', {$c[1]}, {$c[2]}, {$c[3]}, {$c[4]}, {$c[5]})";
        }
        $ok = $q("INSERT INTO " . BO_CAT . " (esquema_id, orden, categoria, pts_ini, pts_fin, monto_categoria, monto_persistencia, monto_bono) VALUES " . implode(',', $vals));
    }
    if (!$ok) { $err = mysqli_error($cx); mysqli_rollback($cx); boFail('No se pudo guardar: ' . $err); }
    mysqli_commit($cx);
    echo json_encode(['ok' => true, 'id' => $id, 'msg' => 'Esquema de bonos guardado.']);
    exit;
}

if ($action === 'toggleBono') {
    $id     = intval($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;
    if ($id <= 0) boFail('Registro inválido.');
    if ($activo === 1) {
        $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM " . BO_ESQ . " WHERE id={$id} LIMIT 1");
        $f  = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$f) boFail('El esquema no existe.');
        if ($c = boSolapa($conecta, BO_ESQ, $id, $f['fec_ini'], $f['fec_fin'])) boFail('No se puede activar: ' . boMsgSolapa($c, 'otro esquema de bonos'));
    }
    $ok = (mysqli_query($conecta->conexion, "UPDATE " . BO_ESQ . " SET activo={$activo}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conecta->conexion)]);
    exit;
}

/* ── Campañas especiales de bonos ── */
if ($action === 'listarBonoCampanas') {
    $rs = $conecta->consulta("SELECT id, campana, fec_ini, fec_fin, multiplicador, activo FROM " . BO_CAMP . " ORDER BY fec_ini, id");
    if (!$rs) boFail('No se pudo leer las campañas de bonos (¿ya corriste pagos_bonos.sql?): ' . mysqli_error($conecta->conexion));
    $filas = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $filas[] = ['id' => intval($r['id']), 'campana' => $r['campana'], 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin'],
                    'multiplicador' => floatval($r['multiplicador']), 'activo' => intval($r['activo'])];
    }
    echo json_encode(['ok' => true, 'filas' => $filas]);
    exit;
}

if ($action === 'guardarBonoCampana') {
    $id   = intval($input['id'] ?? 0);
    $camp = trim((string)($input['campana'] ?? ''));
    $fIni = trim((string)($input['fec_ini'] ?? ''));
    $fFin = trim((string)($input['fec_fin'] ?? ''));
    $mul  = str_replace(',', '', trim((string)($input['multiplicador'] ?? '')));

    if ($camp === '') boFail('Captura el nombre de la campaña.');
    if (mb_strlen($camp, 'UTF-8') > 100) boFail('El nombre de la campaña no puede pasar de 100 caracteres.');
    if (!cpFechaOk($fIni) || !cpFechaOk($fFin)) boFail('Captura las fechas "Del" y "Al" válidas.');
    if ($fFin < $fIni) boFail('La fecha "Al" no puede ser anterior a "Del".');
    if (!preg_match('/^\d{1,2}(\.\d{1,2})?$/', $mul) || floatval($mul) <= 0) boFail('El multiplicador debe ser un número mayor a 0 (ej. 2 para pagar al doble), hasta 2 decimales.');
    $mul = number_format(floatval($mul), 2, '.', '');

    $activo = 1;
    if ($id > 0) {
        $rs = $conecta->consulta("SELECT activo FROM " . BO_CAMP . " WHERE id={$id} LIMIT 1");
        $act = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$act) boFail('La campaña no existe.');
        $activo = intval($act['activo']);
    }
    if ($activo === 1 && ($s = boSolapa($conecta, BO_CAMP, $id, $fIni, $fFin))) boFail(boMsgSolapa($s, 'otra campaña de bonos'));

    $cx = $conecta->conexion;
    $campEsc = mysqli_real_escape_string($cx, $camp);
    if ($id > 0) {
        $ok = (mysqli_query($cx, "UPDATE " . BO_CAMP . " SET campana='{$campEsc}', fec_ini='{$fIni}', fec_fin='{$fFin}', multiplicador={$mul}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    } else {
        $ok = (mysqli_query($cx, "INSERT INTO " . BO_CAMP . " (campana, fec_ini, fec_fin, multiplicador, activo, creado_por) VALUES ('{$campEsc}', '{$fIni}', '{$fFin}', {$mul}, 1, '{$usuario}')") !== false);
        if ($ok) $id = intval(mysqli_insert_id($cx));
    }
    if (!$ok) boFail('No se pudo guardar: ' . mysqli_error($cx));
    echo json_encode(['ok' => true, 'id' => $id, 'msg' => 'Guardado.']);
    exit;
}

if ($action === 'toggleBonoCampana') {
    $id     = intval($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;
    if ($id <= 0) boFail('Registro inválido.');
    if ($activo === 1) {
        $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM " . BO_CAMP . " WHERE id={$id} LIMIT 1");
        $f  = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$f) boFail('La campaña no existe.');
        if ($c = boSolapa($conecta, BO_CAMP, $id, $f['fec_ini'], $f['fec_fin'])) boFail('No se puede activar: ' . boMsgSolapa($c, 'otra campaña de bonos'));
    }
    $ok = (mysqli_query($conecta->conexion, "UPDATE " . BO_CAMP . " SET activo={$activo}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conecta->conexion)]);
    exit;
}

/* ═════════ TABLA DE INACTIVOS (pagos.costo_puntos_inactivos / _det) ═════════
   Cliente inactivo (sin aportar 12 meses): un esquema = vigencia + puntos por rango de edad.
   listarInactivos / guardarInactivo / toggleInactivo. Editar o desactivar afecta al esquema completo;
   dos esquemas ACTIVOS no se traslapan en fechas. */
const CP_INA     = 'pagos.costo_puntos_inactivos';
const CP_INA_DET = 'pagos.costo_puntos_inactivos_det';

if ($action === 'listarInactivos') {
    $rs = $conecta->consulta("SELECT id, fec_ini, fec_fin, activo FROM " . CP_INA . " ORDER BY fec_ini, id");
    if (!$rs) boFail('No se pudo leer la tabla de inactivos (¿ya corriste pagos_inactivos.sql?): ' . mysqli_error($conecta->conexion));
    $esq = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $esq[intval($r['id'])] = ['id' => intval($r['id']), 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin'], 'activo' => intval($r['activo']), 'edades' => [], 'puntos' => []];
    }
    $rd = $conecta->consulta("SELECT tab_id, edad_ini, edad_fin, puntos FROM " . CP_INA_DET . " ORDER BY tab_id, edad_ini");
    if (!$rd) boFail('No se pudo leer el detalle de inactivos: ' . mysqli_error($conecta->conexion));
    while ($r = mysqli_fetch_assoc($rd)) {
        $t = intval($r['tab_id']);
        if (!isset($esq[$t])) continue;
        $esq[$t]['edades'][] = ['ini' => intval($r['edad_ini']), 'fin' => intval($r['edad_fin'])];
        $esq[$t]['puntos'][] = intval($r['puntos']);
    }
    echo json_encode(['ok' => true, 'esquemas' => array_values($esq)]);
    exit;
}

if ($action === 'guardarInactivo') {
    $id     = intval($input['id'] ?? 0);
    $fIni   = trim((string)($input['fec_ini'] ?? ''));
    $fFin   = trim((string)($input['fec_fin'] ?? ''));
    $edades = $input['edades'] ?? [];
    $puntos = $input['puntos'] ?? [];

    if (!cpFechaOk($fIni) || !cpFechaOk($fFin)) boFail('Captura las fechas inicial y final válidas.');
    if ($fFin < $fIni) boFail('La fecha final no puede ser anterior a la inicial.');
    if (!is_array($edades) || !count($edades)) boFail('El esquema necesita al menos un rango de edad.');
    if (!is_array($puntos) || count($puntos) !== count($edades)) boFail('Los puntos no coinciden con los rangos de edad.');

    $E = []; $prev = -1;
    foreach ($edades as $i => $e) {
        $n = $i + 1;
        $ini = trim((string)($e['ini'] ?? '')); $fin = trim((string)($e['fin'] ?? ''));
        if (!preg_match('/^\d{1,3}$/', $ini) || !preg_match('/^\d{1,3}$/', $fin) || intval($ini) > 120 || intval($fin) > 120) boFail("Edad {$n}: deben ser enteros entre 0 y 120.");
        $ini = intval($ini); $fin = intval($fin);
        if ($fin < $ini) boFail("Edad {$n}: la edad final no puede ser menor a la inicial.");
        if ($ini <= $prev) boFail("Edad {$n}: los rangos de edad no pueden traslaparse ni ir desordenados.");
        $p = trim((string)$puntos[$i]);
        if (!preg_match('/^\d{1,9}$/', $p)) boFail("Edad {$n}: los puntos deben ser un entero no negativo.");
        $prev = $fin; $E[] = [$ini, $fin, intval($p)];
    }

    $activo = 1;
    if ($id > 0) {
        $rs = $conecta->consulta("SELECT activo FROM " . CP_INA . " WHERE id={$id} LIMIT 1");
        $act = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$act) boFail('El esquema no existe.');
        $activo = intval($act['activo']);
    }
    if ($activo === 1 && ($s = boSolapa($conecta, CP_INA, $id, $fIni, $fFin))) boFail(boMsgSolapa($s, 'otro esquema de inactivos'));

    $cx = $conecta->conexion;
    $q  = function ($sql) use ($cx) { return mysqli_query($cx, $sql) !== false; };   /* solo false = error real */
    mysqli_begin_transaction($cx);
    if ($id > 0) {
        $ok = $q("UPDATE " . CP_INA . " SET fec_ini='{$fIni}', fec_fin='{$fFin}', modificado_por='{$usuario}' WHERE id={$id}");
    } else {
        $ok = $q("INSERT INTO " . CP_INA . " (fec_ini, fec_fin, activo, creado_por) VALUES ('{$fIni}', '{$fFin}', 1, '{$usuario}')");
        if ($ok) $id = intval(mysqli_insert_id($cx));
    }
    if ($ok) $ok = $q("DELETE FROM " . CP_INA_DET . " WHERE tab_id={$id}");
    if ($ok) {
        $vals = [];
        foreach ($E as $e) $vals[] = "({$id}, {$e[0]}, {$e[1]}, {$e[2]})";
        $ok = $q("INSERT INTO " . CP_INA_DET . " (tab_id, edad_ini, edad_fin, puntos) VALUES " . implode(',', $vals));
    }
    if (!$ok) { $err = mysqli_error($cx); mysqli_rollback($cx); boFail('No se pudo guardar: ' . $err); }
    mysqli_commit($cx);
    echo json_encode(['ok' => true, 'id' => $id, 'msg' => 'Esquema guardado.']);
    exit;
}

if ($action === 'toggleInactivo') {
    $id     = intval($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;
    if ($id <= 0) boFail('Registro inválido.');
    if ($activo === 1) {
        $rs = $conecta->consulta("SELECT fec_ini, fec_fin FROM " . CP_INA . " WHERE id={$id} LIMIT 1");
        $f  = $rs ? mysqli_fetch_assoc($rs) : null;
        if (!$f) boFail('El esquema no existe.');
        if ($c = boSolapa($conecta, CP_INA, $id, $f['fec_ini'], $f['fec_fin'])) boFail('No se puede activar: ' . boMsgSolapa($c, 'otro esquema de inactivos'));
    }
    $ok = (mysqli_query($conecta->conexion, "UPDATE " . CP_INA . " SET activo={$activo}, modificado_por='{$usuario}' WHERE id={$id}") !== false);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conecta->conexion)]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);