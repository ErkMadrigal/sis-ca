<?php
/* ════════════════════════════════════════════════════════════
   saldos_naps_api.php  --  Puntos por NAP y semana

   Acciones:
     semanas   -> semanas de carobra_2014.fec_cierre_campana desde "Periodo 12 semana 1" (id_campana >= SN_ID_DESDE)
     consultar -> por semana (y opcionalmente SC / brocker / gerencia / NAP) regresa, por NAP:
                    no. de folios, rechazados, otro estatus, aceptados procesar, certificados,
                    puntos aproximados y categoría "imparable" (SIN mostrar saldos);
                    y por cada folio su estatus (SIN saldo).

   Reglas:
     - Se consulta por semana (más NAP y/o SC / Brocker / Gerencia); regresa una fila por NAP.
     - Folio en la semana: fec_traspaso (dd/mm/aaaa) entre fec_ini y fec_fin de la campaña (igual que el reporteador).
     - Cada folio aporta: Certificado* (ids SN_CERT) -> su saldo REAL (tabla pagos); cualquier otro estatus -> su saldo ESTIMADO.
     - Puntos aproximados = suma de los folios del NAP en la semana / 100,000, redondeo normal (.5 sube):
           450,000 -> 4.5 -> 5 | 450,001 -> 4.50001 -> 5 | 440,000 -> 4.4 -> 4
     - A la pantalla solo se envían PUNTOS y estatus; nunca montos de saldo.
     - Categoría: tabla pagos.bono_esquema_cat del esquema de bonos activo que cubre la semana.
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

/* ── Ajustes (si algún nombre difiere en tu BD, se cambia aquí) ── */
const SN_DB         = 'carobra_2014';        // BD donde viven produccion, naps, estruc_empleados, estatus, pagos, fec_cierre_campana
const SN_ID_DESDE   = 168;                   // id_campana de "Periodo 12 semana 1"
const SN_EST_ACEP   = 15;                    // ACEPTADA PROCESAR
const SN_EST_RECH   = 6;                     // Rechazado
const SN_CERT       = [1, 8, 9, 11, 12, 13, 16, 25, 27];   // Certificado, folio recuperado, sin carpeta, ".", <30 meses, >60 años, no administrado, >64 años
const SN_PTS_BASE   = 100000;                // saldo por punto

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ── Super Usuario (id_nivel 12): único que puede consultar todo el periodo sin filtro ── */
function snEsSuperUsuario($conecta) {
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
/* Permisos de datos sensibles del detalle (carobra_2014.permisos_sv, igual que el reporteador): NSS/CURP y datos de contacto.
   Super Usuario (12) ve todo. */
function snPermisosDetalle($conecta, $nivel) {
    $perm = ['nss' => ($nivel === 12), 'contacto' => ($nivel === 12)];
    if ($nivel === 12) return $perm;
    $nom = mysqli_real_escape_string($conecta->conexion, utf8_decode($_SESSION['nombre'] ?? ''));
    $rs  = mysqli_query($conecta->conexion, "SELECT clave FROM empl_usuarios WHERE nombre='{$nom}' LIMIT 1");
    $r   = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$r || $r['clave'] === '') return $perm;
    $cl  = mysqli_real_escape_string($conecta->conexion, $r['clave']);
    $rp  = mysqli_query($conecta->conexion, "SELECT permiso_contacto, permisos_nss_curp FROM carobra_2014.permisos_sv WHERE clave='{$cl}' LIMIT 1");
    if ($rp && ($f = mysqli_fetch_assoc($rp))) {
        $ok = function ($v) { $v = strtoupper(trim((string)$v)); return !in_array($v, ['', '0', 'N', 'NO'], true); };
        $perm['contacto'] = $ok($f['permiso_contacto']);
        $perm['nss']      = $ok($f['permisos_nss_curp']);
    }
    return $perm;
}
/* Edad al día del traspaso, calculada del CURP (igual que el reporteador) */
function snEdadCurp($curp, $fecDmy) {
    $curp = strtoupper(trim((string)$curp));
    if (strlen($curp) !== 18 || !preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecDmy, $m)) return '';
    $yy = intval(substr($curp, 4, 2)); $mm = intval(substr($curp, 6, 2)); $dd = intval(substr($curp, 8, 2));
    $y  = ($yy <= intval($m[3]) % 100) ? 2000 + $yy : 1900 + $yy;
    if (!checkdate($mm, $dd, $y)) return '';
    $edad = intval($m[3]) - $y;
    if (intval($m[2]) < $mm || (intval($m[2]) === $mm && intval($m[1]) < $dd)) $edad--;
    return $edad >= 0 ? $edad : '';
}

/* Nivel del usuario calculado en el servidor (no se confía en el que mande el navegador) */
$nivelUsr = function_exists('nivel_usuario') ? intval(nivel_usuario(utf8_decode($_SESSION['nombre'] ?? '')))
                                              : (snEsSuperUsuario($conecta) ? 12 : 0);

$cx = $conecta->conexion;
function snFail($m) { echo json_encode(['ok' => false, 'msg' => $m]); exit; }
function snFechaYmd($dmy) {   // dd/mm/aaaa -> aaaa-mm-dd
    return preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dmy, $m) ? "{$m[3]}-{$m[2]}-{$m[1]}" : '';
}
/* puntos = suma / 100,000 redondeado normal (.5 sube), con enteros en centavos: 4.5 -> 5, 4.50001 -> 5, 4.4 -> 4 */
function snPuntos($suma) {
    $c = (int)round($suma * 100);
    if ($c <= 0) return 0;
    $base = (int)(SN_PTS_BASE * 100);   // centavos por punto
    return intdiv($c + intdiv($base, 2), $base);
}

/* ═════════ semanas ═════════ */
if ($action === 'semanas') {
    $rs = mysqli_query($cx, "SELECT id_campana, campana, fec_ini, fec_fin FROM " . SN_DB . ".fec_cierre_campana
                             WHERE id_campana >= " . SN_ID_DESDE . " ORDER BY id_campana DESC");
    if (!$rs) snFail('No se pudo leer fec_cierre_campana: ' . mysqli_error($cx));
    $out = [];
    while ($r = mysqli_fetch_assoc($rs)) {
        $out[] = ['id' => intval($r['id_campana']), 'campana' => utf8_encode($r['campana']), 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin']];
    }
    echo json_encode(['ok' => true, 'semanas' => $out]);
    exit;
}

/* ═════════ consultar ═════════
   Entrada: id_campana (semana), nap ("NOMBRE (1234567)"), sc, brocker, gerencia (autocompletar, igual que facturas_nap_estructura).
   Salida: una fila por NAP de esa semana, SOLO en puntos (nunca se muestra saldo). */
if ($action === 'consultar') {
    $idCamp  = intval($input['id_campana'] ?? 0);
    $napRaw  = trim((string)($input['nap'] ?? ''));
    $limpia  = function ($v) { return trim(preg_replace('/\s*\([^)]*\)\s*$/', '', trim((string)$v))); };   // quita el "(...)" final del autocompletar
    $sc      = $limpia($input['sc'] ?? '');
    $brocker = $limpia($input['brocker'] ?? '');
    $gerencia= $limpia($input['gerencia'] ?? '');
    if (strtoupper($sc) === 'TODOS') $sc = '';
    /* el campo NAP llega como "NOMBRE (1234567)": se toma el número; también se acepta el número solo */
    $nap = '';
    if (preg_match('/\((\d+)\)/', $napRaw, $mm)) $nap = $mm[1];
    elseif (preg_match('/^\d+$/', $napRaw))       $nap = $napRaw;
    /* niveles con filtro fijo: SC (13) y Brocker (5) solo ven lo suyo */
    $nomSes = $limpia($_SESSION['nombre'] ?? '');
    if ($nivelUsr === 13) $sc = $nomSes;
    if ($nivelUsr === 5)  $brocker = $nomSes;
    if ($idCamp <= 0) snFail('Debes seleccionar la semana.');
    if ($nivelUsr !== 12 && $nap === '' && $sc === '' && $brocker === '' && $gerencia === '') snFail('Debes indicar NAP, SC, Brocker o Gerencia para buscar');

    /* la semana elegida (fec_cierre_campana, desde "Periodo 12 semana 1" = SN_ID_DESDE) */
    $rs = mysqli_query($cx, "SELECT id_campana, campana, fec_ini, fec_fin FROM " . SN_DB . ".fec_cierre_campana
                             WHERE id_campana = {$idCamp} AND id_campana >= " . SN_ID_DESDE . " LIMIT 1");
    if (!$rs) snFail('No se pudo leer fec_cierre_campana: ' . mysqli_error($cx));
    $semanas = [];
    if ($r = mysqli_fetch_assoc($rs)) {
        $a = snFechaYmd($r['fec_ini']); $b = snFechaYmd($r['fec_fin']);
        if ($a !== '' && $b !== '') $semanas[] = ['nombre' => utf8_encode($r['campana']), 'ini' => $a, 'fin' => $b, 'fec_ini' => $r['fec_ini'], 'fec_fin' => $r['fec_fin']];
    }
    if (!$semanas) snFail('Selecciona una semana válida (de Periodo 12 semana 1 en adelante).');
    $dMin = $semanas[0]['ini']; $dMax = $semanas[count($semanas) - 1]['fin'];

    $esc = function ($v) use ($cx) { return mysqli_real_escape_string($cx, utf8_decode($v)); };
    $cond = '';
    if ($nap !== '')      $cond .= " AND TRIM(p.nap) = '" . $esc($nap) . "'";
    if ($sc !== '')       $cond .= " AND ee.sc LIKE '%" . $esc($sc) . "%'";
    if ($brocker !== '')  $cond .= " AND ee.brocker LIKE '%" . $esc($brocker) . "%'";
    if ($gerencia !== '') $cond .= " AND ee.gerencia LIKE '%" . $esc($gerencia) . "%'";

    $D = SN_DB;
    $sql = "SELECT p.folio, p.nap, p.fec_traspaso, p.id_estatus_prod AS est, p.saldo_estimado AS se,
                   p.nss, p.fec_captura, p.fec_cierre, p.fec_ult_estatus, p.estatus_confirma,
                   IF(p.no_empleado = 0, n.no_empleado, p.no_empleado) AS emp,
                   ee.sc, ee.brocker, ee.gerencia, e.estatus AS est_nom
            FROM {$D}.produccion p
                LEFT JOIN {$D}.naps n ON (p.nap = n.nap)
                LEFT JOIN {$D}.estruc_empleados ee ON (IF(p.no_empleado = 0, n.no_empleado, p.no_empleado) = ee.no_empleado)
                LEFT JOIN {$D}.estatus e ON (e.id_estatus = p.id_estatus_prod)
            WHERE p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
              AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= '{$dMin}'
              AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') <= '{$dMax}'
              {$cond}
            ORDER BY p.nap, p.folio";
    $rs = mysqli_query($cx, $sql);
    if (!$rs) snFail('No se pudo consultar producción: ' . mysqli_error($cx));

    $folios = [];   // por folio (sin duplicados)
    while ($r = mysqli_fetch_assoc($rs)) {
        $f = $r['folio'];
        if (isset($folios[$f])) continue;
        $ymd = snFechaYmd($r['fec_traspaso']);
        $si = -1;
        foreach ($semanas as $k => $w) if ($ymd >= $w['ini'] && $ymd <= $w['fin']) { $si = $k; break; }
        if ($si < 0) continue;
        $folios[$f] = [
            'folio' => $f, 'nap' => trim($r['nap']) === '' ? '(sin NAP)' : $r['nap'], 'sem' => $si,
            'est' => intval($r['est']), 'est_nom' => utf8_encode((string)$r['est_nom']),
            'se' => floatval($r['se']),
            'nss' => trim((string)$r['nss']), 'f_cap' => $r['fec_captura'], 'f_tra' => $r['fec_traspaso'], 'f_cie' => $r['fec_cierre'],
            'f_ult' => $r['fec_ult_estatus'], 'conf' => utf8_encode((string)$r['estatus_confirma']), 'emp' => intval($r['emp']),
            'sc' => utf8_encode((string)$r['sc']), 'brocker' => utf8_encode((string)$r['brocker']), 'gerencia' => utf8_encode((string)$r['gerencia']),
        ];
    }

    /* saldo real de los folios certificados (tabla pagos, por folio; si hay varios renglones gana el último) */
    $certFolios = [];
    foreach ($folios as $f => $x) if (in_array($x['est'], SN_CERT, true)) $certFolios[] = $f;
    $real = [];
    foreach (array_chunk($certFolios, 800) as $chunk) {
        $in = "'" . implode("','", array_map(function ($f) use ($cx) { return mysqli_real_escape_string($cx, $f); }, $chunk)) . "'";
        $rp = mysqli_query($cx, "SELECT folio, saldo_real FROM {$D}.pagos WHERE folio IN ({$in})");
        if (!$rp) snFail('No se pudo leer el pago real (tabla ' . SN_DB . '.pagos): ' . mysqli_error($cx));
        while ($r = mysqli_fetch_assoc($rp)) $real[$r['folio']] = floatval($r['saldo_real']);
    }

    /* dueño del folio (prod_trabajador, por folio) y asesor (empl_usuarios, por no_empleado) */
    $perm = snPermisosDetalle($conecta, $nivelUsr);
    $trab = [];
    foreach (array_chunk(array_keys($folios), 800) as $chunk) {
        $in = "'" . implode("','", array_map(function ($f) use ($cx) { return mysqli_real_escape_string($cx, $f); }, $chunk)) . "'";
        $rt = mysqli_query($cx, "SELECT folio, trabajador, CURP FROM {$D}.prod_trabajador WHERE folio IN ({$in})");
        while ($rt && ($r = mysqli_fetch_assoc($rt))) $trab[$r['folio']] = $r;
    }
    /* movilidad (tabla movilidad, por folio): igual que el reporteador */
    $movil = [];
    /* columna de salario de la tabla movilidad: se detecta por nombre (salario / sueldo / sdi...) */
    $colSal = '';
    $rcol = mysqli_query($cx, "SHOW COLUMNS FROM {$D}.movilidad");
    while ($rcol && ($c = mysqli_fetch_assoc($rcol))) {
        if (preg_match('/salari|sueldo|^sdi$|^sal$/i', $c['Field'])) { $colSal = ", `" . str_replace('`', '', $c['Field']) . "` AS salario"; break; }
    }
    foreach (array_chunk(array_keys($folios), 800) as $chunk) {
        $in = "'" . implode("','", array_map(function ($f) use ($cx) { return mysqli_real_escape_string($cx, $f); }, $chunk)) . "'";
        $rm = mysqli_query($cx, "SELECT folio, movilidad, pplus, categoria_cliente{$colSal} FROM {$D}.movilidad WHERE folio IN ({$in})");
        if (!$rm) snFail('No se pudo leer la tabla movilidad: ' . mysqli_error($cx));
        while ($r = mysqli_fetch_assoc($rm)) $movil[$r['folio']] = [
            'movilidad' => (intval($r['movilidad']) === 1) ? 'Igual o mayor a 18 meses' : 'Menos de 18 meses',
            'pplus'     => trim((string)$r['pplus']) === '' ? '' : 'Cliente ' . trim((string)$r['pplus']),
            'cat'       => utf8_encode(trim((string)$r['categoria_cliente'])),
            'salario'   => isset($r['salario']) && $r['salario'] !== null && $r['salario'] !== '' ? $r['salario'] : '',
        ];
    }
    $asesores = [];
    $emps = array_values(array_unique(array_filter(array_map(function ($x) { return $x['emp']; }, $folios))));
    foreach (array_chunk($emps, 800) as $chunk) {
        $rt = mysqli_query($cx, "SELECT no_empleado, nombre FROM {$D}.empl_usuarios WHERE no_empleado IN (" . implode(',', array_map('intval', $chunk)) . ")");
        while ($rt && ($r = mysqli_fetch_assoc($rt))) $asesores[intval($r['no_empleado'])] = utf8_encode((string)$r['nombre']);
    }

    /* agrupar por NAP y semana. Cada folio aporta: certificado -> saldo real; cualquier otro estatus -> saldo estimado.
       (el saldo solo se usa internamente para calcular los puntos; nunca se envía a la pantalla) */
    $grupos = [];
    foreach ($folios as $f => $x) {
        $k = $x['sem'] . '|' . $x['nap'];
        if (!isset($grupos[$k])) {
            $grupos[$k] = ['sem' => $x['sem'], 'nap' => $x['nap'], 'sc' => $x['sc'], 'brocker' => $x['brocker'], 'gerencia' => $x['gerencia'],
                           'folios' => 0, 'rechazados' => 0, 'otros' => 0, 'aceptados' => 0, 'certificados' => 0, 'suma' => 0.0, 'lista' => []];
        }
        $g = &$grupos[$k];
        $g['folios']++;
        if (in_array($x['est'], SN_CERT, true)) { $g['certificados']++; $g['suma'] += ($real[$f] ?? 0); }
        else {
            $g['suma'] += $x['se'];
            if     ($x['est'] === SN_EST_ACEP) $g['aceptados']++;
            elseif ($x['est'] === SN_EST_RECH) $g['rechazados']++;
            else                               $g['otros']++;
        }
        $t = $trab[$f] ?? [];
        $g['lista'][] = [
            'folio'     => $f,
            'trabajador'=> utf8_encode(trim((string)($t['trabajador'] ?? ''))),
            'nss'       => $perm['nss'] ? $x['nss'] : '',
            'curp'      => $perm['nss'] ? strtoupper(trim((string)($t['CURP'] ?? ''))) : '',
            'edad'      => snEdadCurp($t['CURP'] ?? '', $x['f_tra']),
            'estatus'   => $x['est_nom'] !== '' ? $x['est_nom'] : 'Sin estatus',
            'confirma'  => $x['conf'],
            'f_captura' => $x['f_cap'], 'f_traspaso' => $x['f_tra'],
            'salario'   => $movil[$f]['salario'] ?? '',
            'movilidad' => $movil[$f]['movilidad'] ?? 'Sin estatus movilidad',
            'pplus'     => $movil[$f]['pplus'] ?? '',
            'cat_cliente' => $movil[$f]['cat'] ?? '',
            'asesor'    => $asesores[$x['emp']] ?? '',
        ];
        unset($g);
    }

    /* categorías "imparable": esquema de bonos activo que cubre cada semana */
    mysqli_set_charset($cx, 'utf8mb4');   // las tablas de pagos.* son utf8mb4 (acentos)
    $catsPorSem = [];
    foreach ($semanas as $k => $w) {
        $cats = []; $elite = null;
        $re = mysqli_query($cx, "SELECT id, elite_cada FROM pagos.bono_esquema WHERE activo=1 AND fec_ini <= '{$w['ini']}' AND fec_fin >= '{$w['ini']}' ORDER BY fec_ini DESC LIMIT 1");
        $esq = $re ? mysqli_fetch_assoc($re) : null;
        if ($esq) {
            $rc = mysqli_query($cx, "SELECT categoria, pts_ini, pts_fin FROM pagos.bono_esquema_cat WHERE esquema_id=" . intval($esq['id']) . " ORDER BY pts_ini");
            while ($rc && ($r = mysqli_fetch_assoc($rc))) $cats[] = ['c' => $r['categoria'], 'a' => intval($r['pts_ini']), 'b' => intval($r['pts_fin'])];
            $elite = ($esq['elite_cada'] !== null && intval($esq['elite_cada']) > 0);
        }
        $catsPorSem[$k] = [$cats, $elite];
    }
    $catDe = function ($pts, $k) use ($catsPorSem) {
        list($cats, $elite) = $catsPorSem[$k];
        if (!$cats) return '';
        if ($pts < $cats[0]['a']) return 'Sin categoría';
        foreach ($cats as $c) if ($pts >= $c['a'] && $pts <= $c['b']) return $c['c'];
        return $elite ? 'Élite +' : $cats[count($cats) - 1]['c'];
    };

    uasort($grupos, function ($a, $b) { return [$a['sem'], $a['nap']] <=> [$b['sem'], $b['nap']]; });
    $out = []; $tot = ['naps' => [], 'folios' => 0, 'puntos' => 0];
    foreach ($grupos as $g) {
        $pts = snPuntos(round($g['suma'], 2));
        $out[] = [
            'semana' => $semanas[$g['sem']]['nombre'], 'nap' => $g['nap'], 'sc' => $g['sc'], 'brocker' => $g['brocker'], 'gerencia' => $g['gerencia'],
            'folios' => $g['folios'], 'rechazados' => $g['rechazados'], 'otros' => $g['otros'],
            'aceptados' => $g['aceptados'], 'certificados' => $g['certificados'],
            'puntos' => $pts, 'categoria' => $catDe($pts, $g['sem']), 'lista' => $g['lista'],
        ];
        $tot['naps'][$g['nap']] = true; $tot['folios'] += $g['folios']; $tot['puntos'] += $pts;
    }
    $tot['naps'] = count($tot['naps']);

    echo json_encode(['ok' => true,
                      'semanas' => array_map(function ($w) { return ['nombre' => $w['nombre'], 'fec_ini' => $w['fec_ini'], 'fec_fin' => $w['fec_fin']]; }, $semanas),
                      'permisos' => $perm, 'naps' => $out, 'totales' => $tot]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);