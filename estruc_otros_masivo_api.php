<?php
/* ════════════════════════════════════════════════════════════
   estruc_otros_masivo_api.php
   Actualización masiva de carobra_2014.estruc_empleados_otros (área BRADESCARD)
   desde un xlsx: NOMBRE ASESOR | nivel | GERENCIA (A-Z) | ESTATUS

   Acciones:  validar  (simula, no escribe)   |   aplicar (valida de nuevo y escribe)

   Reglas (solo cuentan filas ACTIVAS, estatus=1):
   - 1 solo líder activo por gerencia (un líder puede estar en N gerencias).
   - 1 solo coordinador activo por gerencia.
   - Un coordinador o promotor solo puede estar activo en UNA gerencia; si es
     coordinador y promotor a la vez, ambos deben ser de la MISMA gerencia.
   - Promotores: N por gerencia.
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
$conecta->conexion->set_charset('latin1');   /* estas tablas viven en latin1 (ver utf8_decode/utf8_encode abajo) */

const EOM_AREA   = 'BRADESCARD';
const EOM_TABLA  = 'carobra_2014.estruc_empleados_otros';
/* Niveles HARDCODEADOS (no hay catálogo): */
const EOM_NIVELES = ['lider', 'coordinador', 'promotor'];

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ── Solo Super Usuario (id_nivel 12) ── */
function eomEsSuperUsuario($conecta) {
    $nom = mysqli_real_escape_string($conecta->conexion, utf8_decode($_SESSION['nombre'] ?? ''));
    $rs  = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE nombre='{$nom}' LIMIT 1");
    $r   = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$r || $r['clave'] === '') return false;
    if (strtoupper(trim($r['clave'])) === 'DIPEREZ') return true;   /* acceso total fijo */
    $cl  = mysqli_real_escape_string($conecta->conexion, $r['clave']);
    $rs2 = $conecta->consulta("SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$cl}'");
    while ($rs2 && ($f = mysqli_fetch_assoc($rs2))) {
        if (intval($f['id_nivel']) === 12) return true;
    }
    return false;
}
if (!eomEsSuperUsuario($conecta)) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para esta sección (se requiere Super Usuario).']); exit;
}

/* ── Helpers ── */
function eomNorm($s) {
    $s = mb_strtoupper(trim((string)$s), 'UTF-8');
    $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
    $s = preg_replace('/[^A-Z0-9 ]/', ' ', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
}
function eomNivel($s) {
    $n = mb_strtolower(trim((string)$s), 'UTF-8');
    $n = strtr($n, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
    return in_array($n, EOM_NIVELES, true) ? $n : '';
}
function eomEstatus($s) {
    $e = mb_strtoupper(trim((string)$s), 'UTF-8');
    if (in_array($e, ['ACTIVO', 'ACTIVA', '1', 'SI', 'SÍ'], true)) return 1;
    if (in_array($e, ['BAJA', 'INACTIVO', 'INACTIVA', '0', 'NO'], true)) return 0;
    return null;
}

/* La columna estatus se crea con estruc_empleados_otros_estatus.sql */
$rsCol = $conecta->consulta("SHOW COLUMNS FROM " . EOM_TABLA . " LIKE 'estatus'");
if (!$rsCol || mysqli_num_rows($rsCol) === 0) {
    echo json_encode(['ok' => false, 'msg' => "Falta la columna estatus. Corre: ALTER TABLE carobra_2014.estruc_empleados_otros ADD COLUMN estatus TINYINT(1) NOT NULL DEFAULT 1;"]);
    exit;
}

function eomCargarUsuarios($conecta) {
    $porNombre = []; $porClave = [];
    $rs = $conecta->consulta("SELECT clave, nombre, activo FROM carobra_2014.empl_usuarios");
    while ($rs && ($r = mysqli_fetch_assoc($rs))) {
        $cl = strtoupper(trim($r['clave']));
        $nm = utf8_encode($r['nombre']);
        $porClave[$cl] = $nm;
        $porNombre[eomNorm($nm)][] = ['clave' => $cl, 'activo' => intval($r['activo'])];
    }
    return [$porNombre, $porClave];
}

function eomCargarEstado($conecta) {
    $est = [];
    $rs = $conecta->consulta("SELECT clave, nivel, gerencia, estatus FROM " . EOM_TABLA . " WHERE area='" . EOM_AREA . "'");
    while ($rs && ($r = mysqli_fetch_assoc($rs))) {
        $est[] = [
            'clave'    => strtoupper(trim($r['clave'])),
            'nivel'    => (($nv = mb_strtolower(trim(utf8_encode($r['nivel'])), 'UTF-8')) === 'gerente' ? 'lider' : $nv),  /* en BD el líder se guarda como 'gerente' */
            'gerencia' => mb_strtoupper(trim(utf8_encode($r['gerencia'])), 'UTF-8'),
            'estatus'  => intval($r['estatus']),
            'nivel_raw'    => $r['nivel'],
            'gerencia_raw' => $r['gerencia'],
            'db'       => true,
        ];
    }
    return $est;
}

function eomIdx($est, $clave, $nivel, $ger) {
    foreach ($est as $i => $e) {
        if ($e['clave'] === $clave && $e['nivel'] === $nivel && $e['gerencia'] === $ger) return $i;
    }
    return -1;
}

/* Corre todas las reglas sobre la lista de filas. $desactivar = poner en 0 las
   filas activas actuales de los asesores del archivo que NO vienen en el archivo. */
function eomEvaluar($conecta, $rows, $desactivar) {

    list($porNombre, $porClave) = eomCargarUsuarios($conecta);
    $estado = eomCargarEstado($conecta);

    /* 1) Normalizar y resolver clave de cada fila */
    $items = [];
    foreach ($rows as $i => $r) {
        $it = [
            'fila'      => $i + 1,
            'nombre'    => trim((string)($r['nombre'] ?? '')),
            'clave'     => strtoupper(trim((string)($r['clave'] ?? ''))),
            'nivel_in'  => trim((string)($r['nivel'] ?? '')),
            'ger_in'    => trim((string)($r['gerencia'] ?? '')),
            'est_in'    => trim((string)($r['estatus'] ?? '')),
            'accion'    => '', 'motivo' => '',
        ];
        $it['nivel']    = eomNivel($it['nivel_in']);
        $it['gerencia'] = mb_strtoupper($it['ger_in'], 'UTF-8');
        $it['estatus']  = eomEstatus($it['est_in']);

        if ($it['clave'] !== '') {
            if (!isset($porClave[$it['clave']])) { $it['motivo'] = 'La clave no existe.'; }
            elseif ($it['nombre'] === '') $it['nombre'] = $porClave[$it['clave']];
        } elseif ($it['nombre'] === '') {
            $it['motivo'] = 'Falta el nombre del asesor.';
        } else {
            $cand = $porNombre[eomNorm($it['nombre'])] ?? [];
            if (count($cand) > 1) {
                $act = array_values(array_filter($cand, function ($c) { return $c['activo'] === 1; }));
                if (count($act) === 1) $cand = $act;
            }
            if (count($cand) === 0)      $it['motivo'] = 'Asesor no encontrado por nombre.';
            elseif (count($cand) > 1)    $it['motivo'] = 'Nombre ambiguo: coincide con varias claves (' . implode(', ', array_column($cand, 'clave')) . ').';
            else                         $it['clave']  = $cand[0]['clave'];
        }
        /* Nunca se acepta una fila sin clave (un registro "en blanco" bloquearía/ensuciaría la gerencia) */
        if ($it['motivo'] === '' && $it['clave'] === '') $it['motivo'] = 'El asesor no tiene clave.';

        if ($it['motivo'] === '') {
            if ($it['nivel'] === '')                                   $it['motivo'] = 'Nivel inválido ("' . $it['nivel_in'] . '"). Solo: lider, coordinador, promotor.';
            elseif ($it['gerencia'] === '' || !preg_match('/^[A-Z]{1,3}$/', $it['gerencia'])) $it['motivo'] = 'Gerencia inválida ("' . $it['ger_in'] . '"). Debe ser una letra A-Z.';
            elseif ($it['estatus'] === null)                           $it['motivo'] = 'Estatus inválido ("' . $it['est_in'] . '"). Usa ACTIVO o BAJA.';
        }
        if ($it['motivo'] !== '') $it['accion'] = 'omitido';
        $items[] = $it;
    }

    /* 2) Desactivar filas anteriores de los asesores del archivo (en la simulación;
          el UPDATE real se hace en aplicar) */
    $aDesactivar = [];
    if ($desactivar) {
        $enArchivo = [];
        foreach ($items as $it) {
            if ($it['accion'] === 'omitido') continue;
            $enArchivo[$it['clave']][$it['nivel'] . '|' . $it['gerencia']] = true;
        }
        foreach ($estado as $k => $e) {
            if ($e['estatus'] === 1 && isset($enArchivo[$e['clave']]) && !isset($enArchivo[$e['clave']][$e['nivel'] . '|' . $e['gerencia']])) {
                $estado[$k]['estatus'] = 0;
                $aDesactivar[] = $e;
            }
        }
    }

    /* 3) Aplicar reglas fila por fila, en orden */
    foreach ($items as $k => $it) {
        if ($it['accion'] === 'omitido') continue;
        $c = $it['clave']; $n = $it['nivel']; $g = $it['gerencia'];
        $ix = eomIdx($estado, $c, $n, $g);

        if ($it['estatus'] === 1) {
            $motivo = '';
            foreach ($estado as $j => $e) {
                if ($j === $ix || $e['estatus'] !== 1) continue;

                if ($n === 'lider' && $e['nivel'] === 'lider' && $e['gerencia'] === $g && $e['clave'] !== $c) {
                    $motivo = 'Ya hay un líder (gerente) activo en la gerencia ' . $g . ' ('
                            . ($e['clave'] === '' ? 'registro sin clave' : ($porClave[$e['clave']] ?? $e['clave'])) . ').'; break;
                }
                if ($n === 'coordinador' && $e['nivel'] === 'coordinador' && $e['gerencia'] === $g && $e['clave'] !== $c) {
                    $motivo = 'Ya hay un coordinador activo en la gerencia ' . $g . ' (' . ($porClave[$e['clave']] ?? $e['clave']) . ').'; break;
                }
                /* Solo el líder puede estar en varias gerencias. Un coordinador o promotor solo en UNA
                   (si es coordinador y promotor a la vez, en la MISMA). */
                if (($n === 'coordinador' || $n === 'promotor') && $e['clave'] === $c
                    && ($e['nivel'] === 'coordinador' || $e['nivel'] === 'promotor') && $e['gerencia'] !== $g) {
                    $motivo = 'Ya está activo como ' . $e['nivel'] . ' en la gerencia ' . $e['gerencia']
                            . '; un coordinador/promotor solo puede estar en una gerencia (y si es ambos, la misma).'; break;
                }
            }
            if ($motivo !== '') {
                $items[$k]['accion'] = 'omitido';
                $items[$k]['motivo'] = $motivo;
                continue;
            }
        }

        if ($ix >= 0) {
            $items[$k]['accion'] = ($estado[$ix]['estatus'] === $it['estatus']) ? 'sin_cambio' : 'actualizar';
            $estado[$ix]['estatus'] = $it['estatus'];
        } else {
            $items[$k]['accion'] = 'insertar';
            $estado[] = ['clave' => $c, 'nivel' => $n, 'gerencia' => $g, 'estatus' => $it['estatus'],
                         'nivel_raw' => $n, 'gerencia_raw' => $g, 'db' => false];
        }
    }

    $resumen = ['insertar' => 0, 'actualizar' => 0, 'sin_cambio' => 0, 'omitido' => 0, 'desactivar' => count($aDesactivar)];
    foreach ($items as $it) $resumen[$it['accion']]++;

    $otros = [];
    foreach ($aDesactivar as $e) {
        $otros[] = ['clave' => $e['clave'], 'nombre' => $porClave[$e['clave']] ?? '', 'nivel' => $e['nivel'], 'gerencia' => $e['gerencia']];
    }

    return [$items, $resumen, $otros, $aDesactivar];
}

/* ── Entrada ── */
$rows = $input['rows'] ?? [];
if (!is_array($rows) || count($rows) === 0) { echo json_encode(['ok' => false, 'msg' => 'No hay filas para procesar.']); exit; }
if (count($rows) > 2000) { echo json_encode(['ok' => false, 'msg' => 'Máximo 2,000 filas por archivo.']); exit; }
$desactivar = !empty($input['desactivar_anteriores']);

if ($action === 'validar') {
    list($items, $resumen, $otros) = eomEvaluar($conecta, $rows, $desactivar);
    echo json_encode(['ok' => true, 'rows' => $items, 'resumen' => $resumen, 'otros' => $otros]);
    exit;
}

if ($action === 'aplicar') {
    list($items, $resumen, $otros, $aDesactivar) = eomEvaluar($conecta, $rows, $desactivar);

    $conecta->consulta("START TRANSACTION");
    $err = '';
    $hoy = date('d/m/Y');

    foreach ($aDesactivar as $e) {
        $c = mysqli_real_escape_string($conecta->conexion, $e['clave']);
        $n = mysqli_real_escape_string($conecta->conexion, $e['nivel_raw']);
        $g = mysqli_real_escape_string($conecta->conexion, $e['gerencia_raw']);
        if (!$conecta->consulta("UPDATE " . EOM_TABLA . " SET estatus=0 WHERE area='" . EOM_AREA . "' AND clave='{$c}' AND nivel='{$n}' AND gerencia='{$g}'")) {
            $err = mysqli_error($conecta->conexion); break;
        }
    }

    if ($err === '') {
        foreach ($items as $it) {
            if ($it['accion'] !== 'insertar' && $it['accion'] !== 'actualizar') continue;
            $c = mysqli_real_escape_string($conecta->conexion, $it['clave']);
            $esLider = ($it['nivel'] === 'lider');
            /* Internamente el líder se guarda como 'gerente' */
            $n = mysqli_real_escape_string($conecta->conexion, $esLider ? 'gerente' : $it['nivel']);
            $nWhere = $esLider ? "nivel IN ('gerente','lider')" : "nivel='{$n}'";
            $g = mysqli_real_escape_string($conecta->conexion, $it['gerencia']);
            $s = intval($it['estatus']);
            /* Guardia final: nunca dos líderes (gerentes) activos en la misma gerencia, aunque
               la simulación no lo haya visto (se consulta la BD en este momento). */
            if ($esLider && $s === 1) {
                $gU = mysqli_real_escape_string($conecta->conexion, mb_strtoupper(trim($it['gerencia']), 'UTF-8'));
                $rsG = mysqli_query($conecta->conexion, "SELECT clave FROM " . EOM_TABLA
                     . " WHERE area='" . EOM_AREA . "' AND UPPER(TRIM(gerencia))='{$gU}'"
                     . " AND LOWER(TRIM(nivel)) IN ('gerente','lider') AND estatus=1 AND TRIM(IFNULL(clave,''))<>'{$c}' LIMIT 1");
                if ($rsG && mysqli_num_rows($rsG) > 0) {
                    $rg = mysqli_fetch_assoc($rsG);
                    $err = 'La gerencia ' . $it['gerencia'] . ' ya tiene un gerente activo ('
                         . (trim($rg['clave']) === '' ? 'registro sin clave' : $rg['clave']) . '); solo se permite uno.';
                    break;
                }
            }
            if ($it['accion'] === 'actualizar') {
                $sql = "UPDATE " . EOM_TABLA . " SET estatus={$s} WHERE area='" . EOM_AREA . "' AND clave='{$c}' AND {$nWhere} AND gerencia='{$g}'";
            } else {
                $sql = "INSERT INTO " . EOM_TABLA . " (area, gerencia, clave, nivel, fec_alta, estatus)
                        VALUES ('" . EOM_AREA . "', '{$g}', '{$c}', '{$n}', '{$hoy}', {$s})";
            }
            if (!$conecta->consulta($sql)) { $err = mysqli_error($conecta->conexion); break; }
        }
    }

    if ($err !== '') {
        $conecta->consulta("ROLLBACK");
        echo json_encode(['ok' => false, 'msg' => 'No se aplicó nada (se revirtió): ' . $err]);
        exit;
    }
    $conecta->consulta("COMMIT");
    echo json_encode(['ok' => true, 'rows' => $items, 'resumen' => $resumen, 'otros' => $otros]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);