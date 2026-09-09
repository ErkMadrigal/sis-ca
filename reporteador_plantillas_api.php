<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

require './vendor/autoload.php';
include("./funciones/encripta.php");

$conecta = new conector();

$input = json_decode(
    file_get_contents('php://input'),
    true
);

$action = $input['action'] ?? '';

/* ============================================
GET REPORTES
============================================ */

if($action === 'getReportesPersonales'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$idUsuario){
        echo json_encode(['status'=>'ok','data'=>[]]);
        exit;
    }

    /* superAdministrador o administrador → ve todos */
    if($miRol && (intval($miRol['puede_crear']) === 1 || intval($miRol['puede_admin']) === 1)){
        $sql = "SELECT * FROM catalogo_reportes WHERE activo = 1 ORDER BY nombre ASC";

    /* RH/finanzas/otros → por rol + permisos individuales */
    } else {

        $idUsuarioEsc = mysqli_real_escape_string($conecta->conexion, $idUsuario);

        $sql = "SELECT DISTINCT cr.*
                FROM catalogo_reportes cr
                WHERE cr.activo = 1
                AND (
                    cr.id IN (
                        SELECT prr.idReporte
                        FROM permisos_reporte_rol prr
                        INNER JOIN usuario_rol ur ON ur.idRol = prr.idRol
                        WHERE ur.idUsuario = '$idUsuarioEsc'
                    )
                    OR
                    cr.id IN (
                        SELECT cc.idReporte
                        FROM permisos_campos pc
                        INNER JOIN catalogo_campos cc ON cc.id = pc.idCampo
                        WHERE pc.idUsuario = '$idUsuarioEsc'
                        AND pc.puede_ver = 1
                    )
                )
                ORDER BY cr.nombre ASC";
    }

    $res  = $conecta->consulta($sql);
    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}


/* ============================================
GET CAMPOS
============================================ */

if($action === 'getCamposReporte'){

    $idReporte = intval($input['idReporte']);
    $idUsuario = obtenerClaveUsuario();

    $sql = "SELECT cc.id, cc.campo_bd, cc.alias_campo
            FROM catalogo_campos cc
            LEFT JOIN permisos_campos pc
                ON pc.idCampo = cc.id
                AND pc.idUsuario = '$idUsuario'
            WHERE cc.idReporte = '$idReporte'
            AND (pc.puede_ver = 1 OR pc.puede_ver IS NULL)
            ORDER BY cc.id ASC";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion),'sql'=>$sql]); exit; }

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GET CONFIGURACIONES
============================================ */

if($action === 'getConfiguraciones'){

    $idReporte = intval($input['idReporte']);
    $idUsuario = obtenerClaveUsuario();

    $sql = "SELECT * FROM reportes_guardados
            WHERE idUsuario = '$idUsuario'
            AND idReporte = '$idReporte'
            ORDER BY id DESC";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $data = [];
    while($row = mysqli_fetch_assoc($res)){
        $row['configuracion'] = json_decode($row['configuracion'], true);
        $data[] = $row;
    }

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GUARDAR CONFIGURACION
============================================ */

if($action === 'guardarConfiguracion'){

    $idReporte = intval($input['idReporte']);
    $idUsuario = obtenerClaveUsuario();
    $nombre    = trim($input['nombre']);

    if(empty($nombre)){ echo json_encode(['status'=>'error','message'=>'Nombre requerido']); exit; }

    $configuracion = json_encode($input['configuracion'], JSON_UNESCAPED_UNICODE);
    $nombre        = mysqli_real_escape_string($conecta->conexion, $nombre);
    $configuracion = mysqli_real_escape_string($conecta->conexion, $configuracion);

    $sql = "INSERT INTO reportes_guardados (idUsuario, idReporte, nombre, configuracion)
            VALUES ('$idUsuario', '$idReporte', '$nombre', '$configuracion')";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    echo json_encode(['status'=>'ok','message'=>'Configuracion guardada']);
    exit;
}

/* ============================================
GENERAR REPORTE
============================================ */

if($action === 'generarReporte'){

    $idReporte = intval($input['idReporte']);
    $columnas  = $input['columnas'];
    $idUsuario = obtenerClaveUsuario();
    $filtros   = $input['filtros'] ?? [];

    $limite = intval($input['limite'] ?? 50);
    if($limite > 999999) $limite = 999999;

    /* Validar columnas */
    $columnasSeguras = [];

    foreach($columnas as $col){
        $col = mysqli_real_escape_string($conecta->conexion, $col);
        $sqlCampo = "SELECT cc.campo_bd
                     FROM catalogo_campos cc
                     LEFT JOIN permisos_campos pc
                         ON pc.idCampo = cc.id
                         AND pc.idUsuario = '$idUsuario'
                     WHERE cc.idReporte = '$idReporte'
                     AND cc.campo_bd = '$col'
                     AND (pc.puede_ver = 1 OR pc.puede_ver IS NULL)
                     LIMIT 1";
        $resCampo = $conecta->consulta($sqlCampo);
        if(mysqli_num_rows($resCampo) > 0) $columnasSeguras[] = $col;
    }

    if(count($columnasSeguras) === 0){
        echo json_encode(['status'=>'error','message'=>'Sin columnas validas']);
        exit;
    }

    /* Get query base */
    $sqlReporte = "SELECT query_base FROM catalogo_reportes WHERE id = '$idReporte' LIMIT 1";
    $resReporte = $conecta->consulta($sqlReporte);

    if(!$resReporte){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    $rowReporte = mysqli_fetch_assoc($resReporte);
    $queryBase  = $rowReporte['query_base'];

    /* ============================================
    ELIMINAR CAMPOS Y JOINS OPCIONALES
    ============================================ */

    // 1. Procesar campos opcionales /*CAMPO:alias*/ ... /*FINCAMPO:alias*/
    preg_match_all(
        '/\/\*CAMPO:(\w+)\*\/([\s\S]*?)\/\*FINCAMPO:\1\*\//i',
        $queryBase,
        $campos,
        PREG_SET_ORDER
    );

    foreach($campos as $campo){
        $alias        = $campo[1];
        $campoCompleto = $campo[0];

        if(!in_array($alias, $columnasSeguras)){
            // Quitar campo completo del SELECT interno (incluye la coma del final)
            $queryBase = str_replace($campoCompleto, '', $queryBase);
            // Quitar del SELECT externo
            $queryBase = preg_replace('/,\s*\b' . preg_quote($alias, '/') . '\b/i', '', $queryBase);
            $queryBase = preg_replace('/\b' . preg_quote($alias, '/') . '\b\s*,/i', '', $queryBase);
        } else {
            $queryBase = str_replace('/*CAMPO:'    . $alias . '*/', '', $queryBase);
            $queryBase = str_replace('/*FINCAMPO:' . $alias . '*/', '', $queryBase);
        }
    }

    // 2. Procesar JOINs opcionales /*JOIN:alias*/ ... /*FINJOIN:alias*/
    preg_match_all(
        '/\/\*JOIN:(\w+)\*\/([\s\S]*?)\/\*FINJOIN:\1\*\//i',
        $queryBase,
        $joins,
        PREG_SET_ORDER
    );

    foreach($joins as $join){
        $alias       = $join[1];
        $joinCompleto = $join[0];

        if(!in_array($alias, $columnasSeguras)){
            $queryBase = str_replace($joinCompleto, '', $queryBase);
        } else {
            $queryBase = str_replace('/*JOIN:'    . $alias . '*/', '', $queryBase);
            $queryBase = str_replace('/*FINJOIN:' . $alias . '*/', '', $queryBase);
        }
    }

    // 3. Limpiar coma huérfana antes de ) base
    $queryBase = preg_replace('/,\s*\n\s*\)/s', "\n)", $queryBase);
    $queryBase = preg_replace('/,(\s*FROM\s)/i', '$1', $queryBase);

    /* ============================================
    REEMPLAZAR TOKENS ANTES DEL BUILD
    ============================================ */

    $tokensInput = $input['tokens'] ?? [];

    foreach($tokensInput as $tokenNombre => $valor){
        $tokenNombre = preg_replace('/[^A-Z0-9_]/', '', strtoupper($tokenNombre));
        $valor       = mysqli_real_escape_string($conecta->conexion, $valor);
        $queryBase   = str_replace('{{' . $tokenNombre . '}}', $valor, $queryBase);
    }

    /* Limpiar tokens sin reemplazar */
    $queryBase = preg_replace('/\{\{[A-Za-z0-9_]+\}\}/i', '', $queryBase);

    if(strpos($queryBase, ">= ''") !== false || strpos($queryBase, "<= ''") !== false){
        echo json_encode(['status'=>'error','message'=>'Faltan valores en los filtros de fecha']);
        exit;
    }

    /* ============================================
    BUILD QUERY
    ============================================ */

    $tieneSubquery  = stripos($queryBase, 'select', 1) !== false;
    $tieneMultiJoin = substr_count(strtolower($queryBase), 'join') > 1;

    if($tieneSubquery || $tieneMultiJoin){
        $queryFinal = "SELECT " . implode(', ', $columnasSeguras) . "
                    FROM ( $queryBase ) reporte_base
                    LIMIT $limite";
    } else {
        $queryFinal = reconstruirQuery($queryBase, $columnasSeguras, $limite);
        if(!$queryFinal){
            $queryFinal = "SELECT " . implode(', ', $columnasSeguras) . "
                        FROM ( $queryBase ) reporte_base
                        LIMIT $limite";
        }
    }

    /* ============================================
    APLICAR FILTROS (WHERE dinámico)
    ============================================ */

    $wheres = [];

    foreach($filtros as $filtro){

        $campo = mysqli_real_escape_string($conecta->conexion, $filtro['campo']);
        $tipo  = $filtro['tipo'];

        if($tipo === 'rango_fecha'){

            $desde = mysqli_real_escape_string($conecta->conexion, $filtro['desde'] ?? '');
            $hasta = mysqli_real_escape_string($conecta->conexion, $filtro['hasta'] ?? '');

            if($desde || $hasta){
                $convertir = "COALESCE(
                    STR_TO_DATE(`$campo`, '%Y-%m-%d'),
                    STR_TO_DATE(`$campo`, '%d/%m/%Y'),
                    STR_TO_DATE(`$campo`, '%d-%m-%Y'),
                    STR_TO_DATE(`$campo`, '%Y/%m/%d'),
                    STR_TO_DATE(`$campo`, '%m/%d/%Y'),
                    STR_TO_DATE(`$campo`, '%m-%d-%Y')
                )";
                $desdeDate = $desde ? "STR_TO_DATE('$desde', '%Y-%m-%d')" : null;
                $hastaDate = $hasta ? "STR_TO_DATE('$hasta', '%Y-%m-%d')" : null;

                if($desde && $hasta){
                    $wheres[] = "$convertir BETWEEN $desdeDate AND $hastaDate";
                } elseif($desde){
                    $wheres[] = "$convertir >= $desdeDate";
                } elseif($hasta){
                    $wheres[] = "$convertir <= $hastaDate";
                }
            }

        } elseif($tipo === 'si_no'){
            $valor    = mysqli_real_escape_string($conecta->conexion, $filtro['valor']);
            $wheres[] = "`$campo` = '$valor'";

        } elseif($tipo === 'busqueda_bd'){
            $valores = $filtro['valores'] ?? [];
            if(count($valores) > 0){
                $ins = array_map(function($v) use ($conecta){
                    return "'" . mysqli_real_escape_string($conecta->conexion, $v) . "'";
                }, $valores);
                $wheres[] = "`$campo` IN (" . implode(',', $ins) . ")";
            }

        } elseif($tipo === 'texto_libre'){
            $valor = $filtro['valor'];
            if(preg_match('/^[\w\s\'\"\.\,\%\>\<\=\!\-\+\/\*\(\)]+$/u', $valor)){
                $wheres[] = "`$campo` $valor";
            }
        }
    }

    /* ============================================
    INYECTAR WHERE
    ============================================ */

    if(count($wheres) > 0){
        $whereStr   = implode(' AND ', $wheres);
        $queryFinal = "SELECT " . implode(', ', $columnasSeguras) . "
                    FROM ( $queryBase ) reporte_filtrado
                    WHERE $whereStr
                    LIMIT $limite";
    }

    /* Ejecutar */
    $resFinal = $conecta->consulta($queryFinal);

    if(!$resFinal){
        echo json_encode([
            'status' => 'error',
            'mysql'  => mysqli_error($conecta->conexion),
            'sql'    => $queryFinal
        ]);
        exit;
    }

    $data = [];
    while($row = mysqli_fetch_assoc($resFinal)) $data[] = $row;

    echo json_encode([
        'status'          => 'ok',
        'columnas'        => $columnasSeguras,
        'data'            => $data,
        'sql'             => $queryFinal,
        'query_procesado' => $queryBase   // ← quitar en producción
    ]);
    exit;
}

/* ============================================
CREAR REPORTE
============================================ */

if($action === 'crearReporte'){

    $nombre = mysqli_real_escape_string($conecta->conexion, trim($input['nombre']));
    $query  = mysqli_real_escape_string($conecta->conexion, trim($input['query']));
    $campos = $input['campos'];

    $sql = "INSERT INTO catalogo_reportes (nombre, query_base, activo) VALUES ('$nombre', '$query', 1)";
    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $idReporte = mysqli_insert_id($conecta->conexion);

    foreach($campos as $campo){

        $campo_bd     = mysqli_real_escape_string($conecta->conexion, $campo['campo_bd']);
        $alias_campo  = mysqli_real_escape_string($conecta->conexion, $campo['alias_campo']);
        $tipo_filtro  = mysqli_real_escape_string($conecta->conexion, $campo['tipo_filtro']  ?? 'ninguno');
        $query_filtro = mysqli_real_escape_string($conecta->conexion, $campo['query_filtro'] ?? '');

        $sqlCampo = "INSERT INTO catalogo_campos
                        (idReporte, campo_bd, alias_campo, visible_default, tipo_filtro, query_filtro)
                     VALUES
                        ('$idReporte', '$campo_bd', '$alias_campo', 1, '$tipo_filtro', '$query_filtro')";

        $conecta->consulta($sqlCampo);

    }

    /* Guardar tokens */
    if(!empty($input['tokens'])){
        foreach($input['tokens'] as $token){
            $tokenNombre  = mysqli_real_escape_string($conecta->conexion, preg_replace('/[^A-Z0-9_]/', '', strtoupper(trim($token['token']))));
            $label        = mysqli_real_escape_string($conecta->conexion, trim($token['label']));
            $tipo         = 'texto';
            $valorDefault = mysqli_real_escape_string($conecta->conexion, trim($token['valor_default'] ?? ''));

            if(empty($tokenNombre) || empty($label)) continue;

            $conecta->consulta("INSERT INTO catalogo_tokens
                (idReporte, token, label, tipo, query_token, valor_default)
                VALUES ('$idReporte', '$tokenNombre', '$label', '$tipo', '', '$valorDefault')");
        }
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
GET FILTROS DEL REPORTE
============================================ */

if($action === 'getFiltrosReporte'){

    $idReporte = intval($input['idReporte']);

    $sql = "SELECT campo_bd, alias_campo, tipo_filtro, query_filtro
            FROM catalogo_campos
            WHERE idReporte = '$idReporte'
            AND tipo_filtro != 'ninguno'
            AND tipo_filtro IS NOT NULL
            ORDER BY id ASC";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
BUSCAR FILTRO EN BD (keyup)
============================================ */


if($action === 'buscarFiltro'){
 
    $queryFiltro = trim($input['query']);
    $termino     = trim($input['termino'] ?? '');
 
    if(empty($queryFiltro) || $termino === ''){
        echo json_encode(['status'=>'ok','data'=>[]]);
        exit;
    }
 
    $res = $conecta->consulta($queryFiltro);
 
    if(!$res){
        echo json_encode([
            'status' => 'error',
            'mysql'  => mysqli_error($conecta->conexion),
            'sql'    => $queryFiltro
        ]);
        exit;
    }
 
    $data          = [];
    $filasRevisadas = 0;
 
    while($row = mysqli_fetch_array($res)){
 
        $filasRevisadas++;
        if($filasRevisadas > 5000) break; // válvula de seguridad
 
        $valor = $row[0];
        if($valor === null || $valor === '') continue;
 
        if(mb_stripos((string) $valor, $termino, 0, 'UTF-8') !== false){
            $data[] = $valor;
            if(count($data) >= 10) break;
        }
    }
 
    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GET USUARIOS (solo admin nivel 12)
============================================ */

if($action === 'getUsuarios'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));

    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $sql = "SELECT clave, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC";
    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GET PERMISOS DE UN USUARIO
============================================ */

if($action === 'getPermisosUsuario'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $idReporte = intval($input['idReporte']);
    $idUsuario = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);

    $sql = "SELECT cc.id, cc.campo_bd, cc.alias_campo,
                   COALESCE(pc.puede_ver, 0) AS puede_ver
            FROM catalogo_campos cc
            LEFT JOIN permisos_campos pc
                ON pc.idCampo = cc.id
                AND pc.idUsuario = '$idUsuario'
            WHERE cc.idReporte = '$idReporte'
            ORDER BY cc.id ASC";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GUARDAR PERMISOS
============================================ */

if($action === 'guardarPermisos'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $idReporte = intval($input['idReporte']);
    $idUsuario = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);
    $permisos  = $input['permisos'];

    foreach($permisos as $permiso){

        $idCampo  = intval($permiso['idCampo']);
        $puedeVer = $permiso['puede_ver'] ? 1 : 0;

        $sqlCheck = "SELECT id FROM permisos_campos WHERE idCampo = '$idCampo' AND idUsuario = '$idUsuario' LIMIT 1";
        $resCheck = $conecta->consulta($sqlCheck);

        if(mysqli_num_rows($resCheck) > 0){
            $sqlPerm = "UPDATE permisos_campos SET puede_ver = '$puedeVer' WHERE idCampo = '$idCampo' AND idUsuario = '$idUsuario'";
        } else {
            $sqlPerm = "INSERT INTO permisos_campos (idCampo, idUsuario, puede_ver) VALUES ('$idCampo', '$idUsuario', '$puedeVer')";
        }

        $conecta->consulta($sqlPerm);

    }

    echo json_encode(['status'=>'ok','message'=>'Permisos guardados']);
    exit;
}

/* ============================================
VERIFICAR SI USUARIO YA TIENE PERMISOS
============================================ */

if($action === 'tienePermisos'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $idReporte = intval($input['idReporte']);
    $idUsuario = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);

    $sql = "SELECT COUNT(*) as total
            FROM permisos_campos pc
            INNER JOIN catalogo_campos cc ON cc.id = pc.idCampo
            WHERE cc.idReporte = '$idReporte'
            AND pc.idUsuario = '$idUsuario'";

    $res = $conecta->consulta($sql);
    $row = mysqli_fetch_assoc($res);

    echo json_encode(['status'=>'ok','tienePermisos'=>intval($row['total']) > 0]);
    exit;
}

/* ============================================
GET USUARIOS CON PERMISOS EN UN REPORTE
============================================ */

if($action === 'getUsuariosConPermisos'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $idReporte = intval($input['idReporte']);

    $sql = "SELECT DISTINCT pc.idUsuario, u.nombre
            FROM permisos_campos pc
            INNER JOIN catalogo_campos cc ON cc.id = pc.idCampo
            INNER JOIN usuarios u ON u.clave = pc.idUsuario
            WHERE cc.idReporte = '$idReporte'
            AND pc.puede_ver = 1
            ORDER BY u.nombre ASC";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    $data = [];

    while($row = mysqli_fetch_assoc($res)){

        $idu = mysqli_real_escape_string($conecta->conexion, $row['idUsuario']);

        $sqlCampos = "SELECT cc.alias_campo
                      FROM permisos_campos pc
                      INNER JOIN catalogo_campos cc ON cc.id = pc.idCampo
                      WHERE cc.idReporte = '$idReporte'
                      AND pc.idUsuario = '$idu'
                      AND pc.puede_ver = 1
                      ORDER BY cc.id ASC";

        $resCampos = $conecta->consulta($sqlCampos);
        $campos    = [];

        while($campo = mysqli_fetch_assoc($resCampos)) $campos[] = $campo['alias_campo'];

        $data[] = ['clave'=>$row['idUsuario'],'nombre'=>$row['nombre'],'campos'=>$campos];

    }

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}


/* ============================================
GET ROL DEL USUARIO LOGUEADO
============================================ */

if($action === 'getMiRol'){

    $idUsuario = obtenerClaveUsuario();

    $sql = "SELECT r.id, r.nombre, r.puede_crear, r.puede_admin, r.puede_ver
            FROM usuario_rol ur
            INNER JOIN roles r ON r.id = ur.idRol
            WHERE ur.idUsuario = '$idUsuario'
            AND r.activo = 1
            LIMIT 1";

    $res = $conecta->consulta($sql);

    if(!$res || mysqli_num_rows($res) === 0){
        echo json_encode(['status'=>'ok','rol'=>null]);
        exit;
    }

    $rol = mysqli_fetch_assoc($res);

    echo json_encode(['status'=>'ok','rol'=>$rol]);
    exit;
}

/* ============================================
GET TODOS LOS ROLES
============================================ */

if($action === 'getRoles'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_admin']) !== 1){

        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $sql = "SELECT * FROM roles WHERE activo = 1 ORDER BY id ASC";
    $res = $conecta->consulta($sql);

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}


/* ============================================
CREAR ROL
============================================ */

if($action === 'crearRol'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || !$miRol['puede_admin']){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $nombre      = mysqli_real_escape_string($conecta->conexion, trim($input['nombre']));
    $puedeCrear  = $input['puede_crear'] ? 1 : 0;
    $puedeAdmin  = $input['puede_admin'] ? 1 : 0;
    $puedeVer    = 1;

    if(empty($nombre)){
        echo json_encode(['status'=>'error','message'=>'Nombre requerido']);
        exit;
    }

    $sql = "INSERT INTO roles (nombre, puede_crear, puede_admin, puede_ver)
            VALUES ('$nombre', '$puedeCrear', '$puedeAdmin', '$puedeVer')";

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    echo json_encode(['status'=>'ok','id'=>mysqli_insert_id($conecta->conexion)]);
    exit;
}

/* ============================================
ASIGNAR ROL A USUARIO
============================================ */

if($action === 'asignarRol'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || !$miRol['puede_admin']){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idUsuarioDestino = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);
    $idRol            = intval($input['idRol']);

    /* Verificar si ya tiene rol */
    $sqlCheck = "SELECT id FROM usuario_rol WHERE idUsuario = '$idUsuarioDestino' LIMIT 1";
    $resCheck = $conecta->consulta($sqlCheck);

    if(mysqli_num_rows($resCheck) > 0){
        $sql = "UPDATE usuario_rol SET idRol = '$idRol' WHERE idUsuario = '$idUsuarioDestino'";
    } else {
        $sql = "INSERT INTO usuario_rol (idUsuario, idRol) VALUES ('$idUsuarioDestino', '$idRol')";
    }

    $res = $conecta->consulta($sql);

    if(!$res){ echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]); exit; }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
GET PERMISOS DE REPORTE POR ROL
============================================ */

if($action === 'getPermisosRol'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || !$miRol['puede_admin']){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idRol = intval($input['idRol']);

    $sql = "SELECT idReporte FROM permisos_reporte_rol WHERE idRol = '$idRol'";
    $res = $conecta->consulta($sql);

    $data = [];
    while($row = mysqli_fetch_assoc($res)) $data[] = intval($row['idReporte']);

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}

/* ============================================
GUARDAR PERMISOS DE REPORTE POR ROL
============================================ */

if($action === 'guardarPermisosRol'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || !$miRol['puede_admin']){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idRol    = intval($input['idRol']);
    $reportes = $input['reportes'] ?? [];

    /* Borrar permisos anteriores */
    $conecta->consulta("DELETE FROM permisos_reporte_rol WHERE idRol = '$idRol'");

    /* Insertar nuevos */
    foreach($reportes as $idReporte){
        $idReporte = intval($idReporte);
        $conecta->consulta("INSERT INTO permisos_reporte_rol (idRol, idReporte) VALUES ('$idRol', '$idReporte')");
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
GET USUARIOS CON SUS ROLES
============================================ */

if($action === 'getUsuariosConRoles'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || !$miRol['puede_admin']){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $sql = "SELECT u.clave, u.nombre,
                   r.id AS idRol, r.nombre AS nombreRol
            FROM usuarios u
            LEFT JOIN usuario_rol ur ON ur.idUsuario = u.clave
            LEFT JOIN roles r ON r.id = ur.idRol
            WHERE u.activo = 1
            ORDER BY u.nombre ASC";

    $res  = $conecta->consulta($sql);
    $data = [];

    while($row = mysqli_fetch_assoc($res)) $data[] = $row;

    echo json_encode(['status'=>'ok','data'=>$data]);
    exit;
}


/* ============================================
GET REPORTE COMPLETO (con campos)
============================================ */

if($action === 'getReporteCompleto'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_crear']) !== 1){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idReporte = intval($input['idReporte']);

    $sqlR = "SELECT * FROM catalogo_reportes WHERE id = '$idReporte' LIMIT 1";
    $resR = $conecta->consulta($sqlR);

    if(!$resR || mysqli_num_rows($resR) === 0){
        echo json_encode(['status'=>'error','message'=>'Reporte no encontrado']);
        exit;
    }

    $reporte = mysqli_fetch_assoc($resR);

    $sqlC = "SELECT id, campo_bd, alias_campo, tipo_filtro, query_filtro
             FROM catalogo_campos
             WHERE idReporte = '$idReporte'
             ORDER BY id ASC";

    $resC = $conecta->consulta($sqlC);
    $campos = [];

    while($row = mysqli_fetch_assoc($resC)) $campos[] = $row;

    $reporte['campos'] = $campos;

    echo json_encode(['status'=>'ok','data'=>$reporte]);
    exit;
}

/* ============================================
EDITAR REPORTE
============================================ */

if($action === 'editarReporte'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_crear']) !== 1){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idReporte = intval($input['idReporte']);
    $nombre    = mysqli_real_escape_string($conecta->conexion, trim($input['nombre']));
    $query     = mysqli_real_escape_string($conecta->conexion, trim($input['query']));
    $activo    = intval($input['activo']);
    $campos    = $input['campos'];

    /* Actualizar reporte */
    $sqlR = "UPDATE catalogo_reportes
         SET nombre = '$nombre', query_base = '$query', activo = '$activo'
         WHERE id = '$idReporte'";

    $conecta->consulta($sqlR);

    /* Verificar con affected_rows en lugar de checkear el resultado */
    if(mysqli_errno($conecta->conexion) !== 0){
        echo json_encode([
            'status' => 'error',
            'mysql'  => mysqli_error($conecta->conexion),
            'sql'    => $sqlR
        ]);
        exit;
    }

    /* Borrar campos que ya no están en la lista */
    $idsQuedan = [];
    foreach($campos as $campo){
        if(strpos($campo['id'], 'nuevo_') !== 0){
            $idsQuedan[] = intval($campo['id']);
        }
    }

    if(!empty($idsQuedan)){
        $idsStr = implode(',', $idsQuedan);
        /* Borrar permisos de campos eliminados */
        $conecta->consulta("DELETE FROM permisos_campos
                            WHERE idCampo NOT IN ($idsStr)
                            AND idCampo IN (
                                SELECT id FROM catalogo_campos WHERE idReporte = '$idReporte'
                            )");
        /* Borrar campos eliminados */
        $conecta->consulta("DELETE FROM catalogo_campos
                            WHERE idReporte = '$idReporte'
                            AND id NOT IN ($idsStr)");
    } else {
        /* Si no quedó ningún campo existente, borrar todos */
        $conecta->consulta("DELETE FROM permisos_campos
                            WHERE idCampo IN (
                                SELECT id FROM catalogo_campos WHERE idReporte = '$idReporte'
                            )");
        $conecta->consulta("DELETE FROM catalogo_campos WHERE idReporte = '$idReporte'");
    }

    foreach($campos as $campo){

        $idCampo     = intval($campo['id']);
        $alias       = mysqli_real_escape_string($conecta->conexion, $campo['alias_campo']);
        $tipoFiltro  = mysqli_real_escape_string($conecta->conexion, $campo['tipo_filtro']);
        $queryFiltro = mysqli_real_escape_string($conecta->conexion, $campo['query_filtro']);

        $sqlC = "UPDATE catalogo_campos
                SET alias_campo = '$alias',
                    tipo_filtro = '$tipoFiltro',
                    query_filtro = '$queryFiltro'
                WHERE id = '$idCampo'
                AND idReporte = '$idReporte'";

        $conecta->consulta($sqlC);

        if(mysqli_errno($conecta->conexion) !== 0){
            echo json_encode([
                'status' => 'error',
                'mysql'  => mysqli_error($conecta->conexion),
                'sql'    => $sqlC
            ]);
            exit;
        }

    }

    /* Insertar campos nuevos */
    foreach($campos as $campo){

        /* Si el id empieza con "nuevo_" es un campo nuevo */
        if(strpos($campo['id'], 'nuevo_') === 0){

            $campo_bd     = mysqli_real_escape_string($conecta->conexion, str_replace('nuevo_', '', $campo['id']));
            $alias        = mysqli_real_escape_string($conecta->conexion, $campo['alias_campo']);
            $tipoFiltro   = mysqli_real_escape_string($conecta->conexion, $campo['tipo_filtro']);
            $queryFiltro  = mysqli_real_escape_string($conecta->conexion, $campo['query_filtro']);

            $conecta->consulta("INSERT INTO catalogo_campos
                (idReporte, campo_bd, alias_campo, visible_default, tipo_filtro, query_filtro)
                VALUES ('$idReporte', '$campo_bd', '$alias', 1, '$tipoFiltro', '$queryFiltro')");

        } else {

            /* Actualizar existente */
            $idCampo     = intval($campo['id']);
            $alias       = mysqli_real_escape_string($conecta->conexion, $campo['alias_campo']);
            $tipoFiltro  = mysqli_real_escape_string($conecta->conexion, $campo['tipo_filtro']);
            $queryFiltro = mysqli_real_escape_string($conecta->conexion, $campo['query_filtro']);

            $sqlC = "UPDATE catalogo_campos
                    SET alias_campo = '$alias',
                        tipo_filtro = '$tipoFiltro',
                        query_filtro = '$queryFiltro'
                    WHERE id = '$idCampo'
                    AND idReporte = '$idReporte'";

            $conecta->consulta($sqlC);

        }

    }
    /* Guardar tokens */
    if(!empty($input['tokens'])){

        $conecta->consulta("DELETE FROM catalogo_tokens WHERE idReporte = '$idReporte'");

        foreach($input['tokens'] as $token){

            $tokenNombre  = mysqli_real_escape_string($conecta->conexion, preg_replace('/[^A-Z0-9_]/', '', strtoupper(trim($token['token']))));
            $label        = mysqli_real_escape_string($conecta->conexion, trim($token['label']));
            $tipo         = mysqli_real_escape_string($conecta->conexion, trim($token['tipo']));
            $queryToken   = mysqli_real_escape_string($conecta->conexion, trim($token['query_token'] ?? ''));
            $valorDefault = mysqli_real_escape_string($conecta->conexion, trim($token['valor_default'] ?? ''));

            if(empty($tokenNombre) || empty($label)) continue;

            $conecta->consulta("INSERT INTO catalogo_tokens
                (idReporte, token, label, tipo, query_token, valor_default)
                VALUES ('$idReporte', '$tokenNombre', '$label', '$tipo', '$queryToken', '$valorDefault')");
        }
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
ELIMINAR REPORTE
============================================ */

if($action === 'eliminarReporte'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_crear']) !== 1){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idReporte = intval($input['idReporte']);

    /* Borrar en cascada */
    $conecta->consulta("DELETE FROM reportes_guardados     WHERE idReporte = '$idReporte'");
    $conecta->consulta("DELETE FROM permisos_reporte_rol   WHERE idReporte = '$idReporte'");
    $conecta->consulta("DELETE FROM permisos_campos
                        WHERE idCampo IN (
                            SELECT id FROM catalogo_campos WHERE idReporte = '$idReporte'
                        )");
    $conecta->consulta("DELETE FROM catalogo_campos        WHERE idReporte = '$idReporte'");

    $res = $conecta->consulta("DELETE FROM catalogo_reportes WHERE id = '$idReporte'");

    if(!$res){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}


/* ============================================
QUITAR PERMISOS INDIVIDUALES DE USUARIO
============================================ */

if($action === 'quitarPermisosUsuario'){

    $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
    if($nivel != 12){ echo json_encode(['status'=>'error','message'=>'Sin permisos']); exit; }

    $idReporte = intval($input['idReporte']);
    $idUsuario = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);

    $sql = "DELETE pc FROM permisos_campos pc
            INNER JOIN catalogo_campos cc ON cc.id = pc.idCampo
            WHERE cc.idReporte = '$idReporte'
            AND pc.idUsuario = '$idUsuario'";

    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
QUITAR ROL A USUARIO
============================================ */

if($action === 'quitarRolUsuario'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_admin']) !== 1){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idUsuarioDestino = mysqli_real_escape_string($conecta->conexion, $input['idUsuario']);

    $sql = "DELETE FROM usuario_rol WHERE idUsuario = '$idUsuarioDestino'";
    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}


/* ============================================
GET TOKENS CON SU BLOQUE ASOCIADO
============================================ */
if($action === 'getTokensReporte'){

    $idReporte = intval($input['idReporte']);

    $sql = "SELECT * FROM catalogo_tokens
            WHERE idReporte = '$idReporte'
            ORDER BY id ASC";

    $res  = $conecta->consulta($sql);
    $tokens = [];
    while($row = mysqli_fetch_assoc($res)) $tokens[] = $row;

    /* Cargar query para detectar a qué JOIN pertenece cada token */
    $sqlQ = "SELECT query_base FROM catalogo_reportes WHERE id = '$idReporte' LIMIT 1";
    $resQ = $conecta->consulta($sqlQ);
    $rowQ = mysqli_fetch_assoc($resQ);
    $queryBase = $rowQ['query_base'] ?? '';

    /* Mapear token → alias del JOIN que lo contiene */
    preg_match_all(
        '/\/\*JOIN:(\w+)\*\/([\s\S]*?)\/\*FINJOIN:\1\*\//i',
        $queryBase,
        $bloques,
        PREG_SET_ORDER
    );

    $tokenBloque = [];

    foreach($bloques as $bloque){
        $alias           = $bloque[1];
        $contenidoBloque = $bloque[2];
        preg_match_all('/\{\{([A-Z0-9_]+)\}\}/i', $contenidoBloque, $toks);
        foreach($toks[1] as $tok){
            $tokenBloque[strtoupper($tok)] = $alias;
        }
    }

    /* Agregar bloque_alias a cada token */
    foreach($tokens as &$token){
        $nombre = strtoupper($token['token']);
        $token['bloque_alias'] = $tokenBloque[$nombre] ?? null;
    }

    echo json_encode(['status'=>'ok','data'=>$tokens]);
    exit;
}

/* ============================================
GUARDAR TOKENS DE UN REPORTE
============================================ */

if($action === 'guardarTokens'){

    $idUsuario = obtenerClaveUsuario();
    $miRol     = getRolUsuario($idUsuario, $conecta);

    if(!$miRol || intval($miRol['puede_crear']) !== 1){
        echo json_encode(['status'=>'error','message'=>'Sin permisos']);
        exit;
    }

    $idReporte = intval($input['idReporte']);
    $tokens    = $input['tokens'] ?? [];

    /* Borrar tokens anteriores */
    $conecta->consulta("DELETE FROM catalogo_tokens WHERE idReporte = '$idReporte'");

    /* Insertar nuevos */
    foreach($tokens as $token){

        $tokenNombre  = mysqli_real_escape_string($conecta->conexion, trim($token['token']));
        $label        = mysqli_real_escape_string($conecta->conexion, trim($token['label']));
        $tipo         = mysqli_real_escape_string($conecta->conexion, trim($token['tipo']));
        $queryToken   = mysqli_real_escape_string($conecta->conexion, trim($token['query_token'] ?? ''));
        $valorDefault = mysqli_real_escape_string($conecta->conexion, trim($token['valor_default'] ?? ''));

        if(empty($tokenNombre) || empty($label)) continue;

        $sql = "INSERT INTO catalogo_tokens
                    (idReporte, token, label, tipo, query_token, valor_default)
                VALUES
                    ('$idReporte', '$tokenNombre', '$label', '$tipo', '$queryToken', '$valorDefault')";

        $conecta->consulta($sql);
    }

    echo json_encode(['status'=>'ok']);
    exit;
}


/* ============================================
RENOMBRAR CONFIGURACION
============================================ */

if($action === 'renombrarConfiguracion'){

    $idUsuario = obtenerClaveUsuario();
    $idConfig  = intval($input['idConfig']);
    $nombre    = mysqli_real_escape_string($conecta->conexion, trim($input['nombre']));

    if(empty($nombre)){
        echo json_encode(['status'=>'error','message'=>'Nombre requerido']);
        exit;
    }

    $sql = "UPDATE reportes_guardados
            SET nombre = '$nombre'
            WHERE id = '$idConfig'
            AND idUsuario = '$idUsuario'";

    $conecta->consulta($sql);

    if(mysqli_errno($conecta->conexion) !== 0){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
ELIMINAR CONFIGURACION
============================================ */

if($action === 'eliminarConfiguracion'){

    $idUsuario = obtenerClaveUsuario();
    $idConfig  = intval($input['idConfig']);

    $sql = "DELETE FROM reportes_guardados
            WHERE id = '$idConfig'
            AND idUsuario = '$idUsuario'";

    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
ACTUALIZAR CONFIGURACION (columnas)
============================================ */

if($action === 'actualizarConfiguracion'){

    $idUsuario     = obtenerClaveUsuario();
    $idConfig      = intval($input['idConfig']);
    $configuracion = json_encode($input['configuracion'], JSON_UNESCAPED_UNICODE);
    $configuracion = mysqli_real_escape_string($conecta->conexion, $configuracion);

    $sql = "UPDATE reportes_guardados
            SET configuracion = '$configuracion'
            WHERE id = '$idConfig'
            AND idUsuario = '$idUsuario'";

    $conecta->consulta($sql);

    if(mysqli_errno($conecta->conexion) !== 0){
        echo json_encode(['status'=>'error','mysql'=>mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['status'=>'ok']);
    exit;
}

/* ============================================
DEFAULT
============================================ */

echo json_encode(['status'=>'error','message'=>'Accion invalida']);
exit;

/* ============================================
FUNCIONES HELPERS
============================================ */
function obtenerClaveUsuario(){

    global $conecta;

    if(empty($_SESSION["nombre"])) return null;

    $nombre = utf8_decode($_SESSION["nombre"]);
    $nombre = mysqli_real_escape_string($conecta->conexion, $nombre);

    $sql = "SELECT clave FROM empl_usuarios WHERE nombre = '$nombre' LIMIT 1";
    $res = $conecta->consulta($sql);

    if(!$res || mysqli_num_rows($res) === 0) return null;

    $row = mysqli_fetch_assoc($res);
    return $row['clave'];

}

function getAliasTablaPrincipal($queryBase){
    preg_match('/FROM\s+\w+\s+(\w+)/i', $queryBase, $match);
    return $match ? trim($match[1]) : null;
}

function mapearColumnasAliases($queryBase){

    preg_match('/SELECT\s+([\s\S]+?)\s+FROM\s+/i', $queryBase, $matchSelect);
    if(!$matchSelect) return [];

    $selectRaw = $matchSelect[1];
    $campos    = [];
    $depth     = 0;
    $current   = '';

    for($i = 0; $i < strlen($selectRaw); $i++){
        $char = $selectRaw[$i];
        if($char === '(') $depth++;
        if($char === ')') $depth--;
        if($char === ',' && $depth === 0){ $campos[] = trim($current); $current = ''; }
        else $current .= $char;
    }

    if(trim($current) !== '') $campos[] = trim($current);

    $mapa = [];

    foreach($campos as $campo){
        $campo = trim($campo);
        if(preg_match('/^(\w+)\.(\w+)\s+AS\s+(\w+)$/i', $campo, $m)){
            $mapa[$m[3]] = $m[1];
        } elseif(preg_match('/^(\w+)\.(\w+)$/', $campo, $m)){
            $mapa[$m[2]] = $m[1];
        }
    }

    return $mapa;
}

function reconstruirQuery($queryBase, $columnasUsadas, $limite){

    $aliasPrincipal = getAliasTablaPrincipal($queryBase);
    $mapa           = mapearColumnasAliases($queryBase);
    $necesitaJoins  = false;

    foreach($columnasUsadas as $col){
        $aliasTabla = $mapa[$col] ?? null;
        if($aliasTabla && $aliasTabla !== $aliasPrincipal){ $necesitaJoins = true; break; }
    }

    $mapa_expresiones = mapearExpresiones($queryBase);
    $selectParts      = [];

    foreach($columnasUsadas as $col){
        $selectParts[] = $mapa_expresiones[$col] ?? $col;
    }

    $selectFinal = implode(', ', $selectParts);

    preg_match('/FROM\s+([\s\S]+?)(?:WHERE|GROUP\s+BY|ORDER\s+BY|HAVING|LIMIT|$)/i', $queryBase, $matchFrom);
    $fromCompleto = trim($matchFrom[1]);

    preg_match('/^(.+?)(?:\s+(?:LEFT|RIGHT|INNER|OUTER|CROSS)?\s*JOIN|\s*$)/is', $fromCompleto, $matchMain);
    $soloFrom = trim($matchMain[1]);

    return $necesitaJoins
        ? "SELECT $selectFinal\nFROM $fromCompleto\nLIMIT $limite"
        : "SELECT $selectFinal\nFROM $soloFrom\nLIMIT $limite";
}

function mapearExpresiones($queryBase){

    preg_match('/SELECT\s+([\s\S]+?)\s+FROM\s+/i', $queryBase, $matchSelect);
    if(!$matchSelect) return [];

    $selectRaw = $matchSelect[1];
    $campos    = [];
    $depth     = 0;
    $current   = '';

    for($i = 0; $i < strlen($selectRaw); $i++){
        $char = $selectRaw[$i];
        if($char === '(') $depth++;
        if($char === ')') $depth--;
        if($char === ',' && $depth === 0){ $campos[] = trim($current); $current = ''; }
        else $current .= $char;
    }

    if(trim($current) !== '') $campos[] = trim($current);

    $mapa = [];

    foreach($campos as $campo){
        $campo = trim($campo);
        if(preg_match('/^(\w+)\.(\w+)\s+AS\s+(\w+)$/i', $campo, $m)){
            $mapa[$m[3]] = $campo;
        } elseif(preg_match('/^(\w+)\.(\w+)$/', $campo, $m)){
            $mapa[$m[2]] = $campo;
        }
    }

    return $mapa;
}

/* ============================================
HELPER: GET ROL DE UN USUARIO
============================================ */

function getRolUsuario($idUsuario, $conecta){

    $idUsuario = mysqli_real_escape_string($conecta->conexion, $idUsuario);

    $sql = "SELECT r.id, r.nombre, r.puede_crear, r.puede_admin, r.puede_ver
            FROM usuario_rol ur
            INNER JOIN roles r ON r.id = ur.idRol
            WHERE ur.idUsuario = '$idUsuario'
            AND r.activo = 1
            LIMIT 1";

    $res = $conecta->consulta($sql);

    if(!$res || mysqli_num_rows($res) === 0) return null;

    return mysqli_fetch_assoc($res);

}