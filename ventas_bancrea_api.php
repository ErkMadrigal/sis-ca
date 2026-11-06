<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

ini_set('post_max_size',       '50M');
ini_set('upload_max_filesize', '50M');
ini_set('memory_limit',        '256M');

require './vendor/autoload.php';
include('./funciones/encripta.php');

$conecta = new conector();

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   Niveles de asesores_externos: 3 = asesor, 4 = gerencia, 13 = sc
════════════════════════════════════════════════════════════ */
function nivelPorRol($rol){
    if($rol === 'gerente') return 4;
    if($rol === 'sc')      return 13;
    return 3; // asesor (default)
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarAsesor
════════════════════════════════════════════════════════════ */
if ($action === 'buscarAsesor') {

    $q  = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';

    /* Internos */
    $sql1 = "SELECT clave, nombre, 0 as externo, NULL as clabe
             FROM carobra_2014.empl_usuarios
             WHERE nombre LIKE '$q' AND activo = 1
             ORDER BY nombre LIMIT 15";

    /* Externos -- SOLO nivel 3 (asesor). Los registros viejos, creados
       antes de que existiera esta columna, pueden tener nivel NULL/0 --
       se tratan como asesor por compatibilidad. */
    $sql2 = "SELECT CONCAT('EXT_', id) as clave, nombre, 1 as externo, clabe
             FROM bancrea.asesores_externos
             WHERE nombre LIKE '$q' AND (nivel = 3 OR nivel IS NULL OR nivel = 0)
             ORDER BY nombre LIMIT 10";

    $lista = [];

    $res1 = $conecta->consulta($sql1);
    while ($row = mysqli_fetch_assoc($res1)) {
        $lista[] = [
            'clave'    => $row['clave'],
            'nombre'   => $row['nombre'],
            'externo'  => false,
            'clabe'    => null
        ];
    }

    $res2 = $conecta->consulta($sql2);
    while ($row = mysqli_fetch_assoc($res2)) {
        $lista[] = [
            'clave'   => $row['clave'],
            'nombre'  => $row['nombre'],
            'externo' => true,
            'clabe'   => $row['clabe']
        ];
    }

    /* Ordenar mezclados por nombre */
    usort($lista, function($a, $b){
        return strcmp($a['nombre'], $b['nombre']);
    });

    echo json_encode(['status' => 'ok', 'data' => array_slice($lista, 0, 20)]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarParaRol
   Busca en asesores_externos (nivel según rol) + estruc_empleados
════════════════════════════════════════════════════════════ */
if ($action === 'buscarParaRol') {

    $q      = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';
    $rol    = $input['rol'] ?? 'gerente'; // 'gerente' o 'sc'
    $campo  = ($rol === 'sc') ? 'sc' : 'gerencia';
    $nivel  = nivelPorRol($rol); // 4 = gerencia, 13 = sc

    /* Internos desde estruc_empleados */
    $sql1 = "SELECT DISTINCT `$campo` as nombre, NULL as id, 0 as externo
             FROM carobra_2014.estruc_empleados
             WHERE `$campo` LIKE '$q'
             AND `$campo` != ''
             ORDER BY `$campo` ASC
             LIMIT 10";

    /* Externos desde asesores_externos -- SOLO el nivel de este rol */
    $sql2 = "SELECT nombre, id, 1 as externo
             FROM bancrea.asesores_externos
             WHERE nombre LIKE '$q' AND nivel = $nivel
             ORDER BY nombre ASC
             LIMIT 10";

    $lista = [];

    $res1 = $conecta->consulta($sql1);
    while ($row = mysqli_fetch_assoc($res1)) {
        $lista[] = [
            'nombre'  => $row['nombre'],
            'id'      => null,
            'externo' => false
        ];
    }

    $res2 = $conecta->consulta($sql2);
    while ($row = mysqli_fetch_assoc($res2)) {
        $lista[] = [
            'nombre'  => $row['nombre'],
            'id'      => $row['id'],
            'externo' => true
        ];
    }

    /* Ordenar por nombre */
    usort($lista, function($a, $b){ return strcmp($a['nombre'], $b['nombre']); });

    echo json_encode(['status' => 'ok', 'data' => array_slice($lista, 0, 15)]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Helper global: resuelve un valor guardado en ventas.asesor /
   .gerente / .sc (puede ser EXT_id, clave de empl_usuarios, o
   -- en registros viejos -- el nombre plano) a algo mostrable.
════════════════════════════════════════════════════════════ */
function resolverPersona($conecta, $valor) {
    $valor = trim($valor ?? '');
    if ($valor === '') {
        return ['clave' => '', 'nombre' => '', 'externo' => false];
    }

    if (strpos($valor, 'EXT_') === 0) {
        $id = intval(substr($valor, 4));
        $sql = "SELECT nombre FROM bancrea.asesores_externos WHERE id = $id LIMIT 1";
        $res = $conecta->consulta($sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return ['clave' => $valor, 'nombre' => $row['nombre'], 'externo' => true];
        }
        return ['clave' => $valor, 'nombre' => $valor, 'externo' => true];
    }

    $esc = mysqli_real_escape_string($conecta->conexion, $valor);
    $sql = "SELECT clave, nombre FROM carobra_2014.empl_usuarios
            WHERE clave = '$esc' AND activo = 1 LIMIT 1";
    $res = $conecta->consulta($sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        return ['clave' => $row['clave'], 'nombre' => $row['nombre'], 'externo' => false];
    }

    /* No es EXT_ ni matchea una clave -- en registros viejos gerente/sc
       se guardaron directo como nombre plano; se muestra tal cual */
    return ['clave' => $valor, 'nombre' => $valor, 'externo' => false];
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarFolio
   Si el folio ya existe en bancrea.ventas, regresa todos sus
   datos para precargar el formulario. Si fec_pago ya tiene valor,
   el comprobante queda bloqueado (no se puede modificar).
════════════════════════════════════════════════════════════ */
if ($action === 'buscarFolio') {

    $folio = mysqli_real_escape_string($conecta->conexion, trim($input['folio'] ?? ''));

    if (empty($folio)) {
        echo json_encode(['status' => 'ok', 'existe' => false]);
        exit;
    }

    $sql = "SELECT folio, fec_pago, fec_captura, cliente, saldo,
                   asesor, porcen_ase, comision_ase,
                   gerente, porcen_ger, comision_ger,
                   sc, porcen_sc, comision_sc
            FROM bancrea.ventas
            WHERE folio = '$folio' LIMIT 1";

    $res = $conecta->consulta($sql);

    if (!$res || mysqli_num_rows($res) === 0) {
        echo json_encode(['status' => 'ok', 'existe' => false]);
        exit;
    }

    $row = mysqli_fetch_assoc($res);

    $fecPago    = trim($row['fec_pago'] ?? '');
    $bloqueado  = !empty($fecPago) && $fecPago !== '0000-00-00';

    $asesorInfo  = resolverPersona($conecta, $row['asesor']);
    $gerenteInfo = resolverPersona($conecta, $row['gerente']);
    $scInfo      = resolverPersona($conecta, $row['sc']);

    echo json_encode([
        'status'      => 'ok',
        'existe'      => true,
        'bloqueado'   => $bloqueado,
        'fec_pago'    => $fecPago,
        'data'        => [
            'folio'        => $row['folio'],
            'fec_captura'  => $row['fec_captura'],
            'cliente'      => $row['cliente'],
            'saldo'        => $row['saldo'],
            'asesor_clave'   => $asesorInfo['clave'],
            'asesor_nombre'  => $asesorInfo['nombre'],
            'asesor_externo' => $asesorInfo['externo'],
            'porcen_ase'   => $row['porcen_ase'],
            'comision_ase' => $row['comision_ase'],
            'gerente_clave'  => $gerenteInfo['clave'],
            'gerente_nombre' => $gerenteInfo['nombre'],
            'porcen_ger'   => $row['porcen_ger'],
            'comision_ger' => $row['comision_ger'],
            'sc_clave'     => $scInfo['clave'],
            'sc_nombre'    => $scInfo['nombre'],
            'porcen_sc'    => $row['porcen_sc'],
            'comision_sc'  => $row['comision_sc'],
        ]
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Helper global: resuelve clave corta desde nombre completo
════════════════════════════════════════════════════════════ */
function buscarClaveEmpleado($conecta, $nombre) {
    if (empty(trim($nombre))) return '';
    $n = mysqli_real_escape_string($conecta->conexion, trim($nombre));

    $sql = "SELECT clave FROM carobra_2014.empl_usuarios
            WHERE nombre = '$n' AND activo = 1 LIMIT 1";
    $res = $conecta->consulta($sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $r = mysqli_fetch_assoc($res);
        return trim($r['clave']);
    }

    $sql2 = "SELECT clave FROM carobra_2014.empl_usuarios
             WHERE TRIM(nombre) = '$n' AND activo = 1 LIMIT 1";
    $res2 = $conecta->consulta($sql2);
    if ($res2 && mysqli_num_rows($res2) > 0) {
        $r2 = mysqli_fetch_assoc($res2);
        return trim($r2['clave']);
    }

    return '';
}

/* ════════════════════════════════════════════════════════════
   ACTION: getEstructura
════════════════════════════════════════════════════════════ */
if ($action === 'getEstructura') {

    $clave = mysqli_real_escape_string($conecta->conexion, trim($input['clave'] ?? ''));

    if (empty($clave)) {
        echo json_encode(['status' => 'error', 'msg' => 'Clave requerida']);
        exit;
    }

    $sql = "SELECT ee.clave, ee.gerencia, ee.brocker, ee.sc
            FROM carobra_2014.estruc_empleados ee
            WHERE ee.clave = '$clave' LIMIT 1";

    $res = $conecta->consulta($sql);

    if (!$res || mysqli_num_rows($res) === 0) {
        echo json_encode(['status' => 'ok', 'sin_estructura' => true]);
        exit;
    }

    $row        = mysqli_fetch_assoc($res);
    $nombre_ger = trim($row['gerencia'] ?? '');
    $nombre_brk = trim($row['brocker']  ?? '');
    $nombre_sc  = trim($row['sc']       ?? '');

    $clave_ger = buscarClaveEmpleado($conecta, $nombre_ger);
    $clave_brk = buscarClaveEmpleado($conecta, $nombre_brk);
    $clave_sc  = buscarClaveEmpleado($conecta, $nombre_sc);

    $sc_final  = ($clave_sc !== '') ? $clave_sc : $clave_brk;
    $sc_origen = ($clave_sc !== '') ? 'sc'       : 'brocker';

    /* Detectar si gerente existe en empl_usuarios */
    $ger_encontrado = ($clave_ger !== '');

    /* Detectar si SC/broker existe */
    $sc_encontrado = ($sc_final !== '');

    echo json_encode([
        'status'         => 'ok',
        'sin_estructura' => false,
        'clave'          => $row['clave'],
        'gerencia'       => $clave_ger,
        'nombre_ger'     => $nombre_ger,
        'ger_encontrado' => $ger_encontrado,
        'brocker'        => $clave_brk,
        'nombre_brk'     => $nombre_brk,
        'sc_raw'         => $clave_sc,
        'nombre_sc'      => $nombre_sc,
        'sc_final'       => $sc_final,
        'sc_origen'      => $sc_origen,
        'sc_encontrado'  => $sc_encontrado,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: insertarComprobante
════════════════════════════════════════════════════════════ */
if ($action === 'insertarComprobante') {

    $folio        = addslashes(trim($input['folio']        ?? ''));
    $fec_captura  = addslashes(trim($input['fec_captura']  ?? ''));
    $cliente      = addslashes(trim($input['cliente']      ?? ''));
    $saldo        = floatval($input['saldo']        ?? 0);
    $asesor       = addslashes(trim($input['asesor']       ?? ''));
    $porcen_ase   = floatval($input['porcen_ase']   ?? 0);
    $comision_ase = floatval($input['comision_ase'] ?? 0);
    $gerente      = addslashes(trim($input['gerente']      ?? ''));
    $porcen_ger   = floatval($input['porcen_ger']   ?? 0);
    $comision_ger = floatval($input['comision_ger'] ?? 0);
    $sc           = addslashes(trim($input['sc']           ?? ''));
    $porcen_sc    = floatval($input['porcen_sc']    ?? 0);
    $comision_sc  = floatval($input['comision_sc']  ?? 0);

    /* validaciones */
    if (empty($folio)) {
        echo json_encode(['status' => 'error', 'msg' => 'Folio requerido']);
        exit;
    }
    if (strlen($folio) < 10 || strlen($folio) > 15) {
        echo json_encode(['status' => 'error', 'msg' => 'El folio debe tener entre 10 y 15 caracteres']);
        exit;
    }
    if (empty($cliente)) {
        echo json_encode(['status' => 'error', 'msg' => 'Cliente requerido']);
        exit;
    }
    if ($saldo <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Saldo debe ser mayor a 0']);
        exit;
    }
    if (empty($asesor)) {
        echo json_encode(['status' => 'error', 'msg' => 'Asesor requerido']);
        exit;
    }
    if (empty($fec_captura)) {
        $fec_captura = date('Y-m-d');
    }


    /* Resolver clave de gerente si es interno (no empieza con EXT_) */
    if (!empty($gerente) && strpos($gerente, 'EXT_') !== 0) {
        $nombreGer = mysqli_real_escape_string($conecta->conexion, $gerente);
        $sqlGer    = "SELECT clave FROM carobra_2014.empl_usuarios
                    WHERE nombre = '$nombreGer' AND activo = 1 LIMIT 1";
        $resGer    = $conecta->consulta($sqlGer);
        if ($resGer && mysqli_num_rows($resGer) > 0) {
            $rowGer  = mysqli_fetch_assoc($resGer);
            $gerente = mysqli_real_escape_string($conecta->conexion, $rowGer['clave']);
        }
        /* Si no encuentra clave, deja el nombre tal cual */
    }

    /* Resolver clave de SC si es interno */
    if (!empty($sc) && strpos($sc, 'EXT_') !== 0) {
        $nombreSc = mysqli_real_escape_string($conecta->conexion, $sc);
        $sqlSc    = "SELECT clave FROM carobra_2014.empl_usuarios
                    WHERE nombre = '$nombreSc' AND activo = 1 LIMIT 1";
        $resSc    = $conecta->consulta($sqlSc);
        if ($resSc && mysqli_num_rows($resSc) > 0) {
            $rowSc = mysqli_fetch_assoc($resSc);
            $sc    = mysqli_real_escape_string($conecta->conexion, $rowSc['clave']);
        }
    }

    /* ¿Ya existe este folio? -- si ya tiene fec_pago, se bloquea;
       si no, se actualiza en vez de duplicar el INSERT. */
    $sqlExiste = "SELECT folio, fec_pago FROM bancrea.ventas WHERE folio = '$folio' LIMIT 1";
    $resExiste = $conecta->consulta($sqlExiste);

    if ($resExiste && mysqli_num_rows($resExiste) > 0) {

        $rowExiste = mysqli_fetch_assoc($resExiste);
        $fecPagoExiste = trim($rowExiste['fec_pago'] ?? '');

        if (!empty($fecPagoExiste) && $fecPagoExiste !== '0000-00-00') {
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Este folio ya tiene fecha de pago (' . $fecPagoExiste . ') y no se puede modificar.'
            ]);
            exit;
        }

        $sql = "UPDATE bancrea.ventas SET
                    fec_captura  = '$fec_captura',
                    cliente      = '$cliente',
                    saldo        = $saldo,
                    asesor       = '$asesor',
                    porcen_ase   = $porcen_ase,
                    comision_ase = $comision_ase,
                    gerente      = '$gerente',
                    porcen_ger   = $porcen_ger,
                    comision_ger = $comision_ger,
                    sc           = '$sc',
                    porcen_sc    = $porcen_sc,
                    comision_sc  = $comision_sc
                WHERE folio = '$folio'";

        $res = $conecta->consulta($sql);

        if ($res) {
            echo json_encode(['status' => 'ok', 'msg' => 'Comprobante actualizado correctamente', 'actualizado' => true]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
        }
        exit;
    }

    $sql = "INSERT INTO bancrea.ventas
                (folio, fec_pago, fec_captura, cliente, saldo,
                 asesor,  porcen_ase,  comision_ase,
                 gerente, porcen_ger,  comision_ger,
                 sc,      porcen_sc,   comision_sc)
            VALUES
                ('$folio', NULL, '$fec_captura', '$cliente', $saldo,
                 '$asesor',  $porcen_ase,  $comision_ase,
                 '$gerente', $porcen_ger,  $comision_ger,
                 '$sc',      $porcen_sc,   $comision_sc)";

    $res = $conecta->consulta($sql);

    if ($res) {
        $nuevo_id = mysqli_insert_id($conecta->conexion);
        echo json_encode(['status' => 'ok', 'msg' => 'Comprobante guardado correctamente', 'id' => $nuevo_id]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
    }
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: setVentas (placeholder)
════════════════════════════════════════════════════════════ */
if ($action === 'setVentas') {
    echo json_encode(['status' => 'error', 'msg' => 'setVentas aún no implementado']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getBancos
════════════════════════════════════════════════════════════ */
if ($action === 'getBancos') {

    $sql = "SELECT id_banco, banco, codigo_banco
            FROM carobra.bancos
            ORDER BY banco ASC";

    $res   = $conecta->consulta($sql);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = [
            'id_banco'     => $row['id_banco'],
            'banco'        => $row['banco'],
            'codigo_banco' => $row['codigo_banco'] ?? ''
        ];
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: detectarBancoPorClabe
   Recibe clabe, extrae dígitos 4-6, busca en bancos
════════════════════════════════════════════════════════════ */
if ($action === 'detectarBancoPorClabe') {

    $clabe = trim($input['clabe'] ?? '');

    if (strlen($clabe) < 6) {
        echo json_encode(['status' => 'ok', 'banco' => null]);
        exit;
    }

    $codigo = substr($clabe, 3, 3); // dígitos 4-6
    $codigo = mysqli_real_escape_string($conecta->conexion, $codigo);

    $sql = "SELECT id_banco, banco, codigo_banco
            FROM carobra.bancos
            WHERE codigo_banco = '$codigo'
            LIMIT 1";

    $res = $conecta->consulta($sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        echo json_encode(['status' => 'ok', 'banco' => $row]);
    } else {
        echo json_encode(['status' => 'ok', 'banco' => null]);
    }
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarGerencia
   Typeahead sensitivo sobre estruc_empleados.gerencia
════════════════════════════════════════════════════════════ */
if ($action === 'buscarGerencia') {

    $q = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';

    $sql = "SELECT DISTINCT gerencia
            FROM carobra_2014.estruc_empleados
            WHERE gerencia LIKE '$q'
            AND gerencia != ''
            ORDER BY gerencia ASC
            LIMIT 15";

    $res   = $conecta->consulta($sql);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = $row['gerencia'];
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarSC
   Typeahead sensitivo sobre estruc_empleados.sc
════════════════════════════════════════════════════════════ */
if ($action === 'buscarSC') {

    $q = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';

    $sql = "SELECT DISTINCT sc
            FROM carobra_2014.estruc_empleados
            WHERE sc LIKE '$q'
            AND sc != ''
            ORDER BY sc ASC
            LIMIT 15";

    $res   = $conecta->consulta($sql);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = $row['sc'];
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarAsesorExterno
   Busca en bancrea.asesores_externos -- SOLO nivel 3 (asesor).
   Este action solo se usa desde el flujo de asesor (typeahead
   principal y modal en rol "asesor"); gerente/SC usan
   buscarParaRol, que ya filtra por su propio nivel.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarAsesorExterno') {

    $q = '%' . mysqli_real_escape_string($conecta->conexion, trim($input['q'] ?? '')) . '%';

    $sql = "SELECT ae.id, ae.nombre, ae.clabe, ae.id_banco, ae.gerencia, ae.sc,
                   b.banco, b.codigo_banco
            FROM bancrea.asesores_externos ae
            LEFT JOIN carobra.bancos b ON b.id_banco = ae.id_banco
            WHERE ae.nombre LIKE '$q' AND (ae.nivel = 3 OR ae.nivel IS NULL OR ae.nivel = 0)
            ORDER BY ae.nombre ASC
            LIMIT 15";

    $res   = $conecta->consulta($sql);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = [
            'id'           => $row['id'],
            'nombre'       => $row['nombre'],
            'clabe'        => $row['clabe'],
            'id_banco'     => $row['id_banco'],
            'banco'        => $row['banco']        ?? '',
            'codigo_banco' => $row['codigo_banco'] ?? '',
            'gerencia'     => $row['gerencia']     ?? '',
            'sc'           => $row['sc']           ?? '',
            'externo'      => true
        ];
    }

    echo json_encode(['status' => 'ok', 'data' => $lista]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarAsesorExterno
   El nivel (3 asesor / 4 gerencia / 13 sc) se determina según el
   rol con el que se abrió el modal ("rol" en el payload) -- así
   cada externo queda clasificado y las búsquedas por rol solo
   traen a los que de verdad aplican para ese rol.
════════════════════════════════════════════════════════════ */
if ($action === 'guardarAsesorExterno') {

    $nombre   = mysqli_real_escape_string($conecta->conexion, trim($input['nombre']   ?? ''));
    $clabe    = mysqli_real_escape_string($conecta->conexion, trim($input['clabe']    ?? ''));
    $id_banco = intval($input['id_banco'] ?? 0);
    $gerencia = mysqli_real_escape_string($conecta->conexion, trim($input['gerencia'] ?? ''));
    $sc       = mysqli_real_escape_string($conecta->conexion, trim($input['sc']       ?? ''));
    $rol      = $input['rol'] ?? 'asesor';
    $nivel    = nivelPorRol($rol);

    /* Validaciones */
    /* CLABE solo requerida si se proporcionó — puede estar vacía para gerente/SC */
    if (!empty($clabe)){
        if (strlen($clabe) !== 18) {
            echo json_encode(['status' => 'error', 'msg' => 'La CLABE debe tener exactamente 18 dígitos']);
            exit;
        }
        if (!ctype_digit($clabe)) {
            echo json_encode(['status' => 'error', 'msg' => 'La CLABE solo debe contener números']);
            exit;
        }
        /* Verificar CLABE duplicada solo si se proporcionó */
        $sql_check = "SELECT id FROM bancrea.asesores_externos WHERE clabe = '$clabe' LIMIT 1";
        $res_check = $conecta->consulta($sql_check);
        if ($res_check && mysqli_num_rows($res_check) > 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Ya existe un asesor externo con esa CLABE']);
            exit;
        }
    }
    if (empty($nombre)) {
        echo json_encode(['status' => 'error', 'msg' => 'Nombre requerido']);
        exit;
    }

    /* CLABE obligatoria solo para asesor (rol/nivel 3) */
    if ($nivel === 3) {
        if (empty($clabe)) {
            echo json_encode(['status' => 'error', 'msg' => 'CLABE requerida']);
            exit;
        }
        if (strlen($clabe) !== 18) {
            echo json_encode(['status' => 'error', 'msg' => 'La CLABE debe tener exactamente 18 dígitos']);
            exit;
        }
        if (!ctype_digit($clabe)) {
            echo json_encode(['status' => 'error', 'msg' => 'La CLABE solo debe contener números']);
            exit;
        }
    }

    $id_banco_sql = $id_banco > 0 ? $id_banco : 'NULL';

    $sql = "INSERT INTO bancrea.asesores_externos
                (nombre, clabe, id_banco, gerencia, sc, nivel)
            VALUES
                ('$nombre', '$clabe', $id_banco_sql, '$gerencia', '$sc', $nivel)";

    $res = $conecta->consulta($sql);

    if ($res) {
        $nuevo_id = mysqli_insert_id($conecta->conexion);

        /* Traer datos completos para retornar al front */
        $sql_get = "SELECT ae.id, ae.nombre, ae.clabe, ae.id_banco, ae.gerencia, ae.sc, ae.nivel,
                           b.banco, b.codigo_banco
                    FROM bancrea.asesores_externos ae
                    LEFT JOIN carobra.bancos b ON b.id_banco = ae.id_banco
                    WHERE ae.id = $nuevo_id";

        $res_get = $conecta->consulta($sql_get);
        $row_get = mysqli_fetch_assoc($res_get);

        echo json_encode([
            'status' => 'ok',
            'msg'    => 'Asesor externo guardado',
            'data'   => [
                'id'           => $row_get['id'],
                'nombre'       => $row_get['nombre'],
                'clabe'        => $row_get['clabe'],
                'id_banco'     => $row_get['id_banco'],
                'banco'        => $row_get['banco']        ?? '',
                'codigo_banco' => $row_get['codigo_banco'] ?? '',
                'gerencia'     => $row_get['gerencia']     ?? '',
                'sc'           => $row_get['sc']           ?? '',
                'nivel'        => $row_get['nivel'],
                'externo'      => true
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Error al guardar: ' . mysqli_error($conecta->conexion)
        ]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'msg' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;