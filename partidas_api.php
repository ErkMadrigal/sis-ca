<?php
/* ════════════════════════════════════════════════════════════
   partidas_api.php  --  Consulta de PARTIDAS de ajustes (solo visualizar)

   Acciones:
     buscarClave -> autocompletar de clave/nombre (empl_usuarios)
     consultar   -> junta TODAS las tablas ajustes_* y regresa solo las partidas de PARTIDAS_VER,
                    filtradas por rango de fechas (obligatorio) y, opcional, por clave o nombre.

   Notas:
     - Todas las tablas ajustes_* comparten estructura (id_ajuste, responsable, clave, fecha, fec_pago, socio, id_gerencia,
       anio, mes, ajuste, motivo, estatus, cen_cost, partida, tipo_ajuste, afecta_sb, no_gasto, afecta_op...).
     - Fechas guardadas como texto dd/mm/aaaa (char(10)). El rango se aplica sobre AJ_CAMPO_FECHA.
     - Si agregas otra tabla ajustes_*, solo añádela a AJ_TABLAS.
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
const AJ_DB          = '';                 // BD de las tablas ajustes_* (vacío = la BD por defecto de la conexión; ej. 'carobra_2014')
const AJ_TABLAS      = ['ajustes_corporativo', 'ajustes_hipotecario', 'ajustes_prestamos', 'ajustes_previsional', 'ajustes_seguros', 'ajustes_tarjeta'];
const AJ_CAMPO_FECHA = 'fecha';            // 'fecha' (fecha del ajuste) o 'fec_pago'
const AJ_MAX_FILAS   = 20000;
const PARTIDAS_VER   = [
    'PART1329' => 'DESCUENTO CURSO CENEVAL',
    'PART1330' => 'DESCUENTOS EXAMEN CENEVAL',
    'PART1318' => 'DESCUENTOS PRESTAMOS',
    'PART1332' => 'DSCTO BIOMETRICO',
    'PART1321' => 'DSCTO LICENCIA NAP',
    'PART1331' => 'DSCTO TABLET Y BIOMETRICO',
    'PART1153' => 'DSCTO TABLETS',
    'PART1323' => 'DSCTO PRESTAMO ASESORIA BERNAL',
    'PART1223' => 'DSCTO POR APORTACIONES A COOPERATIVA',
];

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

function ajFail($m) { echo json_encode(['ok' => false, 'msg' => $m]); exit; }

/* ── Solo Super Usuario (id_nivel 12) ── */
function ajEsSuperUsuario($cx) {
    $nom = mysqli_real_escape_string($cx, utf8_decode($_SESSION['nombre'] ?? ''));
    $rs  = mysqli_query($cx, "SELECT clave FROM empl_usuarios WHERE nombre='{$nom}' LIMIT 1");
    $r   = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$r || $r['clave'] === '') return false;
    $cl  = mysqli_real_escape_string($cx, $r['clave']);
    $rs2 = mysqli_query($cx, "SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$cl}'");
    while ($rs2 && ($f = mysqli_fetch_assoc($rs2))) if (intval($f['id_nivel']) === 12) return true;
    return false;
}
if (!ajEsSuperUsuario($cx)) ajFail('No tienes permiso para esta sección (se requiere Super Usuario).');

function ajFechaYmd($dmy) {   // dd/mm/aaaa -> aaaa-mm-dd (o '' si no es válida)
    if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dmy, $m) || !checkdate(intval($m[2]), intval($m[1]), intval($m[3]))) return '';
    return "{$m[3]}-{$m[2]}-{$m[1]}";
}

/* ═════════ buscarClave (autocompletar) ═════════ */
if ($action === 'buscarClave') {
    $q = trim((string)($input['q'] ?? ''));
    if (mb_strlen($q, 'UTF-8') < 2) { echo json_encode(['ok' => true, 'items' => []]); exit; }
    $e  = mysqli_real_escape_string($cx, utf8_decode($q));
    $rs = mysqli_query($cx, "SELECT clave, nombre FROM " . AJ_DB_PFX() . "empl_usuarios
                             WHERE clave LIKE '{$e}%' OR nombre LIKE '%{$e}%' ORDER BY nombre LIMIT 15");
    if (!$rs) ajFail('No se pudo buscar: ' . mysqli_error($cx));
    $items = [];
    while ($r = mysqli_fetch_assoc($rs)) $items[] = ['clave' => trim($r['clave']), 'nombre' => utf8_encode(trim((string)$r['nombre']))];
    echo json_encode(['ok' => true, 'items' => $items]);
    exit;
}
function AJ_DB_PFX() { return AJ_DB === '' ? '' : AJ_DB . '.'; }

/* ═════════ consultar ═════════ */
if ($action === 'consultar') {
    $fIni = ajFechaYmd(trim((string)($input['fec_ini'] ?? '')));
    $fFin = ajFechaYmd(trim((string)($input['fec_fin'] ?? '')));
    $valor = trim((string)($input['clave'] ?? ''));
    if ($fIni === '' || $fFin === '') ajFail('Captura el rango de fechas (dd/mm/aaaa).');
    if ($fFin < $fIni) ajFail('La fecha final no puede ser anterior a la inicial.');

    /* clave: "NOMBRE (CLAVE)" del autocompletar, una clave escrita, o parte del nombre */
    $cond = '';
    if ($valor !== '') {
        if (preg_match('/\(([^)]+)\)\s*$/', $valor, $m)) {
            $cond = " AND a.clave = '" . mysqli_real_escape_string($cx, trim($m[1])) . "'";
        } else {
            $e  = mysqli_real_escape_string($cx, utf8_decode($valor));
            $rs = mysqli_query($cx, "SELECT clave FROM " . AJ_DB_PFX() . "empl_usuarios WHERE clave = '{$e}' OR nombre LIKE '%{$e}%' LIMIT 200");
            $claves = [];
            while ($rs && ($r = mysqli_fetch_assoc($rs))) $claves[] = "'" . mysqli_real_escape_string($cx, trim($r['clave'])) . "'";
            if (!$claves) { echo json_encode(['ok' => true, 'filas' => [], 'total' => 0, 'truncado' => false, 'aviso' => 'No se encontró ninguna clave o nombre que coincida.']); exit; }
            $cond = " AND a.clave IN (" . implode(',', array_unique($claves)) . ")";
        }
    }

    $parts = "'" . implode("','", array_keys(PARTIDAS_VER)) . "'";
    $campo = AJ_CAMPO_FECHA === 'fec_pago' ? 'fec_pago' : 'fecha';
    $selects = [];
    foreach (AJ_TABLAS as $t) {
        $selects[] = "SELECT '{$t}' AS tabla, a.id_ajuste, a.responsable, a.clave, a.fecha, a.fec_pago, a.socio, a.anio, a.mes,
                             a.ajuste, a.motivo, a.estatus, a.cen_cost, a.partida, a.tipo_ajuste, a.afecta_sb, a.no_gasto
                      FROM " . AJ_DB_PFX() . "{$t} a
                      WHERE a.partida IN ({$parts})
                        AND a.{$campo} REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                        AND STR_TO_DATE(a.{$campo}, '%d/%m/%Y') >= '{$fIni}'
                        AND STR_TO_DATE(a.{$campo}, '%d/%m/%Y') <= '{$fFin}'
                        {$cond}";
    }
    $sql = "SELECT x.*, u.nombre AS nombre_clave FROM (" . implode(" UNION ALL ", $selects) . ") x
            LEFT JOIN " . AJ_DB_PFX() . "empl_usuarios u ON (u.clave = x.clave)
            ORDER BY STR_TO_DATE(x." . $campo . ", '%d/%m/%Y') DESC, x.clave, x.partida
            LIMIT " . (AJ_MAX_FILAS + 1);
    $rs = mysqli_query($cx, $sql);
    if (!$rs) ajFail('No se pudo consultar los ajustes: ' . mysqli_error($cx));

    $filas = []; $total = 0.0; $truncado = false;
    while ($r = mysqli_fetch_assoc($rs)) {
        if (count($filas) >= AJ_MAX_FILAS) { $truncado = true; break; }
        $p = trim($r['partida']);
        $total += floatval($r['ajuste']);
        $filas[] = [
            'tabla'     => str_replace('ajustes_', '', $r['tabla']),
            'id'        => intval($r['id_ajuste']),
            'fecha'     => trim($r['fecha']),
            'fec_pago'  => trim($r['fec_pago']),
            'clave'     => trim($r['clave']),
            'nombre'    => utf8_encode(trim((string)$r['nombre_clave'])),
            'partida'   => $p,
            'partida_nombre' => PARTIDAS_VER[$p] ?? '',
            'motivo'    => utf8_encode(trim((string)$r['motivo'])),
            'ajuste'    => floatval($r['ajuste']),
            'anio'      => intval($r['anio']),
            'mes'       => intval($r['mes']),
            'socio'     => trim($r['socio']),
            'cen_cost'  => trim($r['cen_cost']),
            'no_gasto'  => trim($r['no_gasto']),
            'estatus'   => intval($r['estatus']),
            'tipo_ajuste' => intval($r['tipo_ajuste']),
            'afecta_sb' => intval($r['afecta_sb']),
            'responsable' => utf8_encode(trim((string)$r['responsable'])),
        ];
    }
    echo json_encode(['ok' => true, 'filas' => $filas, 'total' => round($total, 2), 'truncado' => $truncado,
                      'max' => AJ_MAX_FILAS, 'campo_fecha' => $campo]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);