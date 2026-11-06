<?php
/* ════════════════════════════════════════════════════════════
   seg_ventas_reporteador_api.php

   Backend chiquito solo para el buscador con dropdown (Socio
   Comercial / SubBrocker / Comisionista / Gerencia / NAP) del
   Reporteador. Se construye aparte (no reutiliza el helper
   autocompletar() de otras vistas) para tener control total del
   formato que espera obtenerDatosReporte() en seg_ventas_reporteador_funciones.php
   — sobre todo el caso de NAP, que se guarda como "Nombre (NAP)".
════════════════════════════════════════════════════════════ */

session_start();
header('Content-Type: application/json; charset=utf-8');

/* Blindaje de diagnóstico: si algo revienta con un error fatal en
   cualquier punto de este archivo (o de algo que incluya, como
   clas_carobra.php o seg_ventas_reporteador_funciones.php), sin esto
   el navegador recibe una respuesta VACÍA y el fetch del frontend
   truena con "Unexpected end of JSON input" -- sin pista de qué pasó
   realmente. Con este shutdown handler, cualquier error fatal se
   convierte en un JSON legible con el mensaje y la línea exacta, para
   poder diagnosticar sin adivinar. (Se puede quitar/apagar más
   adelante si se prefiere no exponer el detalle del error al cliente,
   pero mientras se depura esto es más útil que una respuesta vacía). */
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error interno del servidor.',
            'debug'   => $err['message'] . ' en ' . basename($err['file']) . ':' . $err['line'],
        ]);
    }
});

if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

/* clas_carobra.php a veces suelta HTML/comentarios sueltos (piensa que
   está armando una página completa). Aquí solo queremos sus funciones/
   clases, así que atrapamos y descartamos cualquier salida directa para
   que no contamine el JSON de la respuesta. */
ob_start();
include('./funciones/clas_carobra.php');
ob_end_clean();

require_once('./seg_ventas_reporteador_funciones.php');
$conecta = new conector();

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ── Caché del Reporteador (ver sv_cache_funciones.php) ── */
if ($action === 'estadoCache') {
    svCacheAsegurarTablas($conecta);
    $e = svCacheEstado($conecta);
    echo json_encode(['status' => 'ok', 'cache' => $e]);
    exit;
}

if ($action === 'refrescarCache') {
    $nombreC = $_SESSION['nombre'] ?? '';
    if (nivelReporteador($conecta, utf8_decode($nombreC)) != 12) {
        echo json_encode(['status' => 'error', 'message' => 'Solo un administrador puede actualizar la caché.']);
        exit;
    }
    svCacheAsegurarTablas($conecta);
    if (function_exists('fastcgi_finish_request')) {
        /* En segundo plano: se contesta ya y el refresco corre después. */
        svCacheProgramarRefresco($conecta, true);
        echo json_encode(['status' => 'ok', 'message' => 'Actualización iniciada en segundo plano.', 'async' => true]);
        exit;
    }
    $r = svCacheRefrescar($conecta);
    echo json_encode(['status' => $r['ok'] ? 'ok' : 'error', 'message' => $r['msg'], 'async' => false]);
    exit;
}

if ($action === 'generarReporte') {

    $nombre = $_SESSION['nombre'] ?? '';
    $nivel  = nivelReporteador($conecta, utf8_decode($nombre));

    $query = "Select clave from empl_usuarios where nombre='" . mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre)) . "'";
    $bd    = $conecta->consulta($query);
    $tb    = $bd ? mysqli_fetch_array($bd) : null;
    $clave = $tb['clave'] ?? '';

    $camposPedidos = filtrarCamposValidos($input['campos'] ?? [], camposVisiblesParaUsuario($conecta, $nivel, $clave, $CAMPOS_REPORTE));
    if (empty($camposPedidos)) {
        $camposPedidos = $CAMPOS_DEFAULT;
    }

    $filtroPedido = $input['filtro'] ?? '';
    $valor1Pedido = $input['valor1'] ?? '';

    /* Blindaje por nivel -- nunca hay que confiar solo en que la
       pantalla lo oculte/bloquee (mismo criterio que camposVisiblesParaNivel
       arriba). "Folio" es una búsqueda puntual y siempre está permitida,
       sin importar el nivel. Para el resto: si (nivel, filtro) tiene un
       valor fijo (ver filtroEsFijoParaNivel()/resolverValorFiltroFijo()
       en seg_ventas_reporteador_funciones.php) se ignora lo que haya
       mandado el navegador y se fuerza el valor resuelto en el servidor
       -- para Gerencia (nivel 4) eso es el nombre de SU gerencia, no su
       nombre personal. Y de plano se rechaza si intenta un tipo de
       filtro que su nivel no tiene permitido en absoluto. */
    $filtrosPermitidos = array_merge(['Folio'], filtrosPermitidosParaNivel($nivel));
    if (!in_array($filtroPedido, $filtrosPermitidos, true)) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para buscar por "' . $filtroPedido . '".']);
        exit;
    }

    if (filtroEsFijoParaNivel($nivel, $filtroPedido)) {
        $valor1Pedido = resolverValorFiltroFijo($conecta, $nivel, $nombre, $filtroPedido);
    } else {
        /* Ámbito jerárquico -- un SC/SubBrocker/Gerencia puede tener
           permitido buscar por otro tipo de filtro (ej. un SC buscando
           por Gerencia), pero solo dentro de SU PROPIA rama de la
           estructura -- nunca hay que confiar en que el buscador del
           frontend no haya ofrecido otras opciones; alguien podría
           mandar el valor a mano directo a la API. */
        $ambito = resolverAmbitoJerarquico($conecta, $nivel, $nombre);
        if (hayAmbitoJerarquico($ambito)) {
            refrescarEstrucEmpleados($conecta);
            if (!valorDentroDeAmbito($conecta, $ambito, $filtroPedido, $valor1Pedido)) {
                echo json_encode(['status' => 'error', 'message' => 'Ese valor no pertenece a tu propia estructura comercial.']);
                exit;
            }
        }
    }

    /* "Opciones adicionales" (sfp/srm0/reg_esp) son filtros contables
       -- solo admin y Finanzas pueden usarlos (ver botón engrane más
       abajo y puedeVerOpcionesAdicionales() en
       seg_ventas_reporteador_funciones.php). Nunca hay que confiar
       solo en que la pantalla los oculte: si alguien manda estos
       flags a mano sin tener el permiso, se ignoran. */
    $puedeOpciones = puedeVerOpcionesAdicionales($conecta, $nivel, $clave);

    $resultado = obtenerDatosReporte($conecta, [
        'filtro'   => $filtroPedido,
        'valor1'   => $valor1Pedido,
        'valor2'   => $input['valor2']   ?? '',
        'valor3'   => $input['valor3']   ?? '',
        'te'       => $input['te']       ?? '',
        'sfp'      => $puedeOpciones ? ($input['sfp']     ?? 0) : 0,
        'srm0'     => $puedeOpciones ? ($input['srm0']    ?? 0) : 0,
        'reg_esp'  => $puedeOpciones ? ($input['reg_esp'] ?? 0) : 0,
        'anio_ini' => $input['anio_ini'] ?? '',
        'anio_fin' => $input['anio_fin'] ?? '',
        'mes_i'    => $input['mes_i']    ?? '',
        'mes_f'    => $input['mes_f']    ?? '',
        'nivel'    => $nivel,
        'clave'    => $clave,
        'nombre'   => $nombre,
        'en_vivo'  => !empty($input['en_vivo']),
    ], 1000);

    if (!$resultado['ok']) {
        echo json_encode(['status' => 'error', 'message' => $resultado['msg']]);
        exit;
    }

    $columnas = [];
    foreach ($camposPedidos as $key) {
        $columnas[] = ['key' => $key, 'label' => $CAMPOS_REPORTE[$key]['label']];
    }

    $data = [];
    foreach ($resultado['filas'] as $fila) {
        $row = [];
        foreach ($camposPedidos as $key) {
            $valor = $fila[$key] ?? '';
            if (in_array($key, $CAMPOS_MONEDA, true) && $valor !== '') {
                $valor = '$' . number_format(floatval($valor), 2);
            }
            $row[$key] = $valor;
        }
        /* _folio va SIEMPRE, sin importar si "Folio" está entre las columnas
           que el usuario eligió mostrar -- lo necesita el frontend para el
           doble clic que abre la ficha completa del registro (ver
           abrirFichaFolio() en seg_ventas_reporteador.php), que dispara un
           segundo generarReporte con filtro=Folio usando este valor. */
        $row['_folio'] = $fila['folio'] ?? '';
        $data[] = $row;
    }

    echo json_encode([
        'status'   => 'ok',
        'columnas' => $columnas,
        'data'     => $data,
        'limitado' => $resultado['limitado'],
        'origen'   => $resultado['origen']   ?? 'vivo',
        'cache_at' => $resultado['cache_at'] ?? '',
        /* Aunque el status sea "ok", puede venir con 0 filas por una razón
           puntual ("Sin resultados para estos filtros." o, si algo tronó
           en el armado del reporte, el error de SQL) -- se manda siempre
           que exista para que la pantalla la pueda mostrar en vez de dejar
           la tabla vacía sin explicación. */
        'message'  => $resultado['msg'] ?? '',
    ]);
    exit;
}

if ($action === 'listarPeriodos') {

    /* Catálogo de periodos para buscar por "mes comercial"/"mes de pago"
       en vez de fecha exacta -- misma tabla que ya usa seg_ventas.php
       para resolver el rango real (fec_ini/fec_fin) de un periodo
       (ej. PERIODO 08 = 11/07/2026 al 07/08/2026). te=='ep' -> mes de
       pago (fechas_cierre); cualquier otro valor (incluida cadena
       vacía, el default de "Mes de comisión") -> mes comercial
       (fechas_cierre_com). $tabla queda fija a uno de estos dos
       literales, sin interpolar nada del usuario -- no hay riesgo de
       inyección aunque no se escape. */
    $te     = $input['te'] ?? '';
    $tabla  = ($te === 'ep') ? 'fechas_cierre' : 'fechas_cierre_com';

    $sql = "Select anio, mes, nom_periodo, fec_ini, fec_fin from $tabla order by anio desc, mes asc";
    $bd  = $conecta->consulta($sql);

    $data = [];
    if ($bd) {
        while ($row = mysqli_fetch_assoc($bd)) {
            $etiquetaMes = ($row['nom_periodo'] !== '' && $row['nom_periodo'] !== null)
                ? $row['nom_periodo']
                : ('Mes ' . $row['mes']);
            $data[] = [
                'anio'    => $row['anio'],
                'mes'     => $row['mes'],
                'label'   => $row['anio'] . ' - ' . $etiquetaMes . ' (' . $row['fec_ini'] . ' al ' . $row['fec_fin'] . ')',
                /* fec_ini/fec_fin aparte del label -- el frontend los usa
                   para mostrar "Rango real: ... al ..." tal cual vienen
                   en la tabla, una vez que el usuario elige periodo
                   inicial y final (ver actualizarRangoRealPeriodo() en
                   seg_ventas_reporteador.php). */
                'fec_ini' => $row['fec_ini'],
                'fec_fin' => $row['fec_fin'],
            ];
        }
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

if ($action === 'buscarFiltro') {

    $tipo    = $input['tipo']    ?? '';
    $termino = trim($input['termino'] ?? '');

    /* Blindaje por nivel también aquí -- el dropdown de búsqueda no debe
       dejar buscar valores de un tipo de filtro que el nivel del usuario
       tiene bloqueado (ver filtrosPermitidosParaNivel arriba). Esto no
       depende de sesión de "generarReporte", así que se recalcula. */
    $nombreSesion = $_SESSION['nombre'] ?? '';
    $nivelSesion  = nivelReporteador($conecta, utf8_decode($nombreSesion));
    if (!in_array($tipo, array_merge(['Folio'], filtrosPermitidosParaNivel($nivelSesion)), true)) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para buscar por "' . $tipo . '".']);
        exit;
    }

    $resultado = ['status' => 'ok', 'data' => []];

    if (mb_strlen($termino) < 1) {
        echo json_encode($resultado);
        exit;
    }

    $like = '%' . mysqli_real_escape_string($conecta->conexion, $termino) . '%';

    /* Ámbito jerárquico -- ver resolverAmbitoJerarquico() en
       seg_ventas_reporteador_funciones.php: un SC solo busca dentro de
       su propio SC, un SubBrocker dentro del suyo, una Gerencia solo
       entre sus propios comisionistas. refrescarEstrucEmpleados() se
       corre aquí porque es tabla de trabajo (truncate+insert), no
       queda garantizado que esté al día si no se refresca primero. */
    $ambito = resolverAmbitoJerarquico($conecta, $nivelSesion, $nombreSesion);
    if (hayAmbitoJerarquico($ambito)) {
        refrescarEstrucEmpleados($conecta);
    }

    if ($tipo === 'Soc. Comercial') {
        $sql = "SELECT DISTINCT sc AS etiqueta, sc AS valor
                FROM carobra_2014.estruc_empleados
                WHERE sc <> '' AND sc LIKE '$like'
                ORDER BY sc LIMIT 15";

    } elseif ($tipo === 'SubBrocker') {
        $cond = condicionAmbitoSql($conecta, $ambito);
        $sql = "SELECT DISTINCT brocker AS etiqueta, brocker AS valor
                FROM carobra_2014.estruc_empleados
                WHERE brocker <> '' AND brocker LIKE '$like' $cond
                ORDER BY brocker LIMIT 15";

    } elseif ($tipo === 'Gerencia') {
        $cond = condicionAmbitoSql($conecta, $ambito);
        $sql = "SELECT DISTINCT gerencia AS etiqueta, gerencia AS valor
                FROM carobra_2014.estruc_empleados
                WHERE gerencia <> '' AND gerencia LIKE '$like' $cond
                ORDER BY gerencia LIMIT 15";

    } elseif ($tipo === 'Comisionista') {
        if (hayAmbitoJerarquico($ambito)) {
            $cond = condicionAmbitoSql($conecta, $ambito, 'ee.');
            $sql = "SELECT DISTINCT eu.nombre AS etiqueta, eu.nombre AS valor
                    FROM empl_usuarios eu
                        INNER JOIN carobra_2014.estruc_empleados ee ON (eu.no_empleado = ee.no_empleado)
                    WHERE eu.nombre <> '' AND eu.nombre LIKE '$like' $cond
                    ORDER BY eu.nombre LIMIT 15";
        } else {
            $sql = "SELECT DISTINCT nombre AS etiqueta, nombre AS valor
                    FROM empl_usuarios
                    WHERE nombre <> '' AND nombre LIKE '$like'
                    ORDER BY nombre LIMIT 15";
        }

    } elseif ($tipo === 'NAP') {
        if (hayAmbitoJerarquico($ambito)) {
            $cond = condicionAmbitoSql($conecta, $ambito, 'ee.');
            $sql = "SELECT DISTINCT CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS etiqueta,
                           CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS valor
                    FROM carobra_2014.naps n
                        LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
                        INNER JOIN carobra_2014.estruc_empleados ee ON (n.no_empleado = ee.no_empleado)
                    WHERE (n.nap LIKE '$like' OR eu.nombre LIKE '$like') $cond
                    ORDER BY n.nap LIMIT 15";
        } else {
            $sql = "SELECT CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS etiqueta,
                           CONCAT(IFNULL(eu.nombre,'Sin dueño'), ' (', n.nap, ')') AS valor
                    FROM carobra_2014.naps n
                    LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
                    WHERE n.nap LIKE '$like' OR eu.nombre LIKE '$like'
                    ORDER BY n.nap LIMIT 15";
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de búsqueda no soportado']);
        exit;
    }

    $bd = $conecta->consulta($sql);
    if ($bd) {
        while ($row = mysqli_fetch_assoc($bd)) {
            $resultado['data'][] = ['etiqueta' => $row['etiqueta'], 'valor' => $row['valor']];
        }
    } else {
        $resultado['status'] = 'error';
        $resultado['mysql']  = mysqli_error($conecta->conexion);
    }

    echo json_encode($resultado);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Permisos de campos sensibles (Teléfono/Celular/Email/CP/Frecuencia/
   etc. -- $CAMPOS_SOLO_ADMIN) por usuario específico. Solo nivel 12
   (admin) puede leer/tocar esto -- se revalida en cada acción, nunca
   hay que confiar en que el botón de engrane esté oculto en pantalla
   para quien no es admin.
════════════════════════════════════════════════════════════ */

if (in_array($action, ['listarPermisosCampos', 'getPermisosUsuario', 'buscarUsuarioPermiso', 'otorgarPermisoCampo', 'revocarPermisoCampo'], true)) {

    $nombreSesion = $_SESSION['nombre'] ?? '';
    $nivelSesion  = nivelReporteador($conecta, utf8_decode($nombreSesion));

    if ($nivelSesion != 12) {
        echo json_encode(['status' => 'error', 'message' => 'Solo un administrador puede administrar estos permisos.']);
        exit;
    }

    if ($action === 'listarPermisosCampos') {

        $data = [];
        foreach ($CAMPOS_SOLO_ADMIN as $campo) {
            $campoEsc = mysqli_real_escape_string($conecta->conexion, $campo);
            $sql = "Select p.clave, eu.nombre
                    from carobra_2014.reporteador_campos_permisos p
                        left join empl_usuarios eu on (p.clave=eu.clave)
                    where p.campo='$campoEsc'
                    order by eu.nombre";
            $bd = $conecta->consulta($sql);
            $lista = [];
            if ($bd) {
                while ($row = mysqli_fetch_assoc($bd)) {
                    $lista[] = ['clave' => $row['clave'], 'nombre' => $row['nombre'] ?: $row['clave']];
                }
            }
            $data[$campo] = $lista;
        }

        echo json_encode(['status' => 'ok', 'data' => $data]);
        exit;
    }

    if ($action === 'getPermisosUsuario') {

        $claveConsulta = trim($input['clave'] ?? '');
        if ($claveConsulta === '') {
            echo json_encode(['status' => 'error', 'message' => 'Falta el usuario a consultar.']);
            exit;
        }

        $autorizados = obtenerCamposAutorizados($conecta, $claveConsulta);
        echo json_encode(['status' => 'ok', 'campos' => $autorizados]);
        exit;
    }

    if ($action === 'buscarUsuarioPermiso') {

        $termino = trim($input['termino'] ?? '');
        $resultado = ['status' => 'ok', 'data' => []];

        if (mb_strlen($termino) < 1) {
            echo json_encode($resultado);
            exit;
        }

        $like = '%' . mysqli_real_escape_string($conecta->conexion, $termino) . '%';
        $sql  = "SELECT clave, nombre FROM empl_usuarios
                 WHERE nombre <> '' AND (nombre LIKE '$like' OR clave LIKE '$like')
                 ORDER BY nombre LIMIT 15";

        $bd = $conecta->consulta($sql);
        if ($bd) {
            while ($row = mysqli_fetch_assoc($bd)) {
                $resultado['data'][] = ['clave' => $row['clave'], 'nombre' => $row['nombre']];
            }
        } else {
            $resultado['status'] = 'error';
            $resultado['mysql']  = mysqli_error($conecta->conexion);
        }

        echo json_encode($resultado);
        exit;
    }

    if ($action === 'otorgarPermisoCampo') {

        $campo = $input['campo'] ?? '';
        $clave = trim($input['clave'] ?? '');

        if (!in_array($campo, $CAMPOS_SOLO_ADMIN, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Campo no válido.']);
            exit;
        }
        if ($clave === '') {
            echo json_encode(['status' => 'error', 'message' => 'Falta el usuario a autorizar.']);
            exit;
        }

        $campoEsc         = mysqli_real_escape_string($conecta->conexion, $campo);
        $claveEsc         = mysqli_real_escape_string($conecta->conexion, $clave);
        $claveSesionEsc   = mysqli_real_escape_string($conecta->conexion, $nombreSesion);

        $ok = $conecta->consulta(
            "INSERT IGNORE INTO carobra_2014.reporteador_campos_permisos (campo, clave, autorizado_por)
             VALUES ('$campoEsc', '$claveEsc', '$claveSesionEsc')"
        );

        echo json_encode($ok
            ? ['status' => 'ok']
            : ['status' => 'error', 'message' => mysqli_error($conecta->conexion)]);
        exit;
    }

    if ($action === 'revocarPermisoCampo') {

        $campo = $input['campo'] ?? '';
        $clave = trim($input['clave'] ?? '');

        if (!in_array($campo, $CAMPOS_SOLO_ADMIN, true) || $clave === '') {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
            exit;
        }

        $campoEsc = mysqli_real_escape_string($conecta->conexion, $campo);
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);

        $ok = $conecta->consulta(
            "DELETE FROM carobra_2014.reporteador_campos_permisos WHERE campo='$campoEsc' and clave='$claveEsc'"
        );

        echo json_encode($ok
            ? ['status' => 'ok']
            : ['status' => 'error', 'message' => mysqli_error($conecta->conexion)]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;