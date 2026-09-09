<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=utf-8');

ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? ($_GET['action'] ?? '');

/* ════════════════════════════════════════════════════════════
   Módulo de KPIs -- quién evalúa a quién sale de
   carobra.empleados.jefe_inmediato (el mismo campo que ya usa el
   resto del sistema, ej. el reporteador), con RRHH.evaluador_manual
   como respaldo cuando ese campo viene vacío. Los KPIs son ÚNICOS
   POR EMPLEADO (RRHH.catalogo_kpis.clave_empleado) -- ya no es un
   catálogo genérico compartido por todos.

   Además, cualquier usuario de nivel 12 (administrador, según
   nivel_usuario() del sitio) puede ver Y CALIFICAR los KPIs de
   cualquier empleado, sea o no su evaluador directo.

   Supuesto sobre la sesión: se resuelve la CURP/clave del usuario
   logueado buscando por $_SESSION['nombre'] en carobra.usuarios
   (igual que hace nivel_usuario() en otras partes del sitio). Si tu
   sesión ya guarda la clave directo (ej. $_SESSION['clave']), avísame
   y quito esta búsqueda de más.
════════════════════════════════════════════════════════════ */

function obtenerClaveUsuarioActual($conecta) {
    if (!empty($_SESSION['clave'])) return $_SESSION['clave'];

    $nombre = $_SESSION['nombre'] ?? '';
    if ($nombre === '') return null;

    /* Mismo patrón que ya usa el resto del sitio para resolver la clave
       del usuario logueado a partir de su nombre en sesión (empl_usuarios
       en carobra_2014, con utf8_decode -- $_SESSION['nombre'] viene en
       otra codificación que la columna, y sin el decode la comparación
       podía no encontrar coincidencia con nombres con acentos/ñ). Antes
       esto buscaba en carobra.usuarios sin decodificar -- funcionaba
       nada más quien ya traía $_SESSION['clave'] puesta de por sí. */
    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
    $rs  = $conecta->consulta("SELECT clave FROM carobra_2014.empl_usuarios WHERE nombre = '{$nombreEsc}' LIMIT 1");
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    return $row['clave'] ?? null;
}

/* Bug reportado: a alguien que SÍ era el jefe directo mostrado en la
   ficha ("Evaluado por: ...") le salía el formulario de calificar
   bloqueado. La comparación de claves usaba === estricto -- si la
   clave en sesión o en carobra.empleados.jefe_inmediato trae espacios
   de más o distinta capitalización (pasa con datos capturados a
   mano), dos claves "iguales" para un humano dejaban de coincidir
   para PHP. Toda comparación de "¿esta clave es la misma persona?"
   debe pasar por aquí en vez de usar === directo. */
function clavesCoinciden($a, $b) {
    if ($a === null || $b === null || $a === '' || $b === '') return false;
    return strtoupper(trim((string) $a)) === strtoupper(trim((string) $b));
}

/* ════════════════════════════════════════════════════════════
   obtenerNivelUsuarioActual()
   Usa la misma nivel_usuario() que ya usa el resto del sitio
   (seg_cumplimiento.php, seg_reporteador_api.php, etc.) para saber
   el nivel del usuario logueado. Nivel 12 = administrador, según la
   convención ya establecida en getCumplimiento_actualizado.php.
   Si la función no está disponible en este contexto por lo que sea,
   se degrada a "no es admin" en vez de tronar.
════════════════════════════════════════════════════════════ */
function obtenerNivelUsuarioActual() {
    if (!function_exists('nivel_usuario')) return null;

    $nombre = $_SESSION['nombre'] ?? '';
    if ($nombre === '') return null;

    try {
        return nivel_usuario(utf8_decode($nombre));
    } catch (\Throwable $e) {
        return null;
    }
}

function periodoActual() {
    return date('Y-m');
}

function iniciales($nombre) {
    $nombre = trim((string) $nombre);
    if ($nombre === '') return '?';
    $partes = preg_split('/\s+/', $nombre);
    $ini = mb_substr($partes[0] ?? '', 0, 1, 'UTF-8') . mb_substr($partes[1] ?? '', 0, 1, 'UTF-8');
    return mb_strtoupper($ini !== '' ? $ini : mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
}

/* ════════════════════════════════════════════════════════════
   resolverEvaluador()
   Quién evalúa a un empleado dado, con la clave como llave de
   asignación (select clave, jefe_inmediato from carobra.empleados):

     1. Si carobra.empleados.jefe_inmediato viene lleno -> ese es
        el evaluador (fuente = 'jefe_inmediato').
     2. Si viene vacío/NULL (no tiene jefe directo capturado) ->
        se usa RRHH.evaluador_manual.clave_evaluador si existe
        (fuente = 'manual').
     3. Si tampoco hay override manual -> sin evaluador (null).

   Devuelve ['clave' => ..., 'nombre' => ..., 'fuente' => ...] o null.
════════════════════════════════════════════════════════════ */
function resolverEvaluador($conecta, $claveEmpleado) {
    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $rs = $conecta->consulta(
        "SELECT e.jefe_inmediato, em.clave_evaluador AS evaluador_manual
         FROM carobra.empleados e
         LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
         WHERE e.clave = '{$claveEmpEsc}' LIMIT 1"
    );
    $row = $rs ? mysqli_fetch_assoc($rs) : null;
    if (!$row) return null;

    $claveEvaluador = null;
    $fuente = null;

    if (!empty($row['jefe_inmediato'])) {
        $claveEvaluador = $row['jefe_inmediato'];
        $fuente = 'jefe_inmediato';
    } elseif (!empty($row['evaluador_manual'])) {
        $claveEvaluador = $row['evaluador_manual'];
        $fuente = 'manual';
    } else {
        return null;
    }

    $claveEvalEsc = mysqli_real_escape_string($conecta->conexion, $claveEvaluador);
    $rsN = $conecta->consulta("SELECT nombre FROM carobra.usuarios WHERE clave = '{$claveEvalEsc}' LIMIT 1");
    $rowN = $rsN ? mysqli_fetch_assoc($rsN) : null;

    return [
        'clave'   => $claveEvaluador,
        'nombre'  => $rowN['nombre'] ?? $claveEvaluador,
        'fuente'  => $fuente,
    ];
}

/* Permiso para VER/ADMINISTRAR el catálogo de KPIs de $claveEmpleado
   (tab "KPIs por empleado", ficha, pantalla de evaluación): admin
   siempre puede; el evaluador efectivo (jefe directo/manual) también;
   y -- bug corregido -- cualquier asignador autorizado (mismo rol que
   ya puede asignar KPIs a CUALQUIER empleado) también, porque si no
   se quedaba con el buscador abierto a todos pero sin poder abrir el
   catálogo de nadie que no fuera su propio subordinado. IMPORTANTE:
   esto es solo para VER/ASIGNAR -- "calificar" (guardar resultados)
   sigue teniendo su propia regla más estricta, abajo (esEvaluadorDirecto)
   -- ya NO es lo mismo que "puede gestionar". */
function puedeGestionarEmpleado($conecta, $claveActual, $esAdminKpi, $claveEmpleado) {
    if ($esAdminKpi) return true;
    if (puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) return true;
    $evaluador = resolverEvaluador($conecta, $claveEmpleado);
    return $evaluador !== null && clavesCoinciden($evaluador['clave'], $claveActual);
}

/* CALIFICAR (guardar resultado/cumplimiento de un KPI ya asignado):
   por instrucción explícita, a partir de este cambio SOLO el
   evaluador efectivo (jefe directo, o evaluador manual si no tiene
   jefe_inmediato capturado) puede calificar -- NI SIQUIERA el
   administrador (antes sí podía calificar a cualquiera). El admin
   conserva la posibilidad de VER cualquier evaluación
   (puedeGestionarEmpleado arriba), solo ya no puede guardar
   resultados él mismo; tiene que hacerlo el jefe directo. */
function esEvaluadorDirecto($conecta, $claveActual, $claveEmpleado) {
    $evaluador = resolverEvaluador($conecta, $claveEmpleado);
    return $evaluador !== null && clavesCoinciden($evaluador['clave'], $claveActual);
}

/* ASIGNAR KPIs (crear/editar/activar-desactivar el catálogo de KPIs
   de un empleado, es decir definir QUÉ se le va a calificar y con qué
   ponderación): por instrucción explícita, esto ya NO depende de si
   eres jefe directo de ese empleado -- es un permiso de ROL, no por
   relación jefe/subordinado. Puede asignar KPIs a CUALQUIER empleado
   quien sea: (a) el administrador del módulo (ADMINISTRA), o (b) esté
   en la lista de asignadores autorizados (RRHH.kpis_asignadores,
   activo = 1) que el administrador gestiona desde la pestaña
   "Asignadores de KPIs". Puede haber varios asignadores autorizados a
   la vez (no reemplaza al anterior al agregar uno nuevo). */
function puedeAsignarKpi($conecta, $claveActual, $esAdminKpi) {
    if ($esAdminKpi) return true;
    if (!$claveActual) return false;

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);
    $rs = $conecta->consulta(
        "SELECT 1 FROM RRHH.kpis_asignadores WHERE clave_empleado = '{$claveEsc}' AND activo = 1 LIMIT 1"
    );
    return $rs && mysqli_num_rows($rs) > 0;
}

/* Enriquece una lista cruda de empleados (no_empleado, clave, nombre,
   email, categoria, jefe_inmediato, clave_evaluador, fuente_evaluador)
   con su avance de KPIs del periodo dado: total de KPIs, cuántos ya se
   calificaron, calificación acumulada, porcentajes y si el usuario
   logueado puede calificarlo. Extraído a función propia porque tanto
   getMisEvaluados (tus subordinados / todos si eres admin) como
   getTodosUsuariosKpi (TODOS los empleados, para quien puede asignar)
   necesitan exactamente el mismo cálculo por fila -- antes vivía
   duplicado sería un desastre de mantener. */
function enriquecerAvanceKpi($conecta, array $empleados, $periodo, $claveActual) {
    $periodoEsc = mysqli_real_escape_string($conecta->conexion, $periodo);
    $datos = [];

    foreach ($empleados as $emp) {
        $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $emp['clave']);

        /* Los KPIs son únicos por empleado: cada quien tiene su propio
           total/ponderación, no un catálogo compartido. */
        $totalKpisRs  = $conecta->consulta(
            "SELECT COUNT(*) AS n, COALESCE(SUM(ponderacion),0) AS suma_ponderacion
             FROM RRHH.catalogo_kpis WHERE clave_empleado = '{$claveEmpEsc}' AND activo = 1"
        );
        $totalKpisRow = $totalKpisRs ? mysqli_fetch_assoc($totalKpisRs) : ['n' => 0, 'suma_ponderacion' => 0];
        $totalKpis      = (int) $totalKpisRow['n'];
        $sumaPonderacion = (float) $totalKpisRow['suma_ponderacion'];

        /* OJO: se une contra catalogo_kpis con k.activo = 1 -- si un KPI
           se desactiva (o se reemplaza por otro) después de haber sido
           calificado, su calificación vieja ya NO debe seguir sumando
           aquí (antes se sumaba para siempre, aunque el KPI ya no
           apareciera en la ficha, y por eso la tarjeta mostraba un
           total más alto -ej. 140- que el que en realidad se veía al
           abrir la evaluación -ej. 80-). */
        $rsAv = $conecta->consulta(
            "SELECT COUNT(*) AS evaluados, COALESCE(SUM(r.calificacion),0) AS suma_calificacion
             FROM RRHH.kpis_resultados r
             INNER JOIN RRHH.catalogo_kpis k ON k.id = r.id_kpi AND k.activo = 1
             WHERE r.clave_empleado = '{$claveEmpEsc}' AND r.periodo = '{$periodoEsc}' AND r.estatus = 'evaluado'"
        );
        $rowAv = $rsAv ? mysqli_fetch_assoc($rsAv) : ['evaluados' => 0, 'suma_calificacion' => 0];

        $evaluados   = (int) $rowAv['evaluados'];
        $calificacion = round((float) $rowAv['suma_calificacion'], 2);

        /* Calculado en PHP con lo que ya trae la fila (sin query extra por
           empleado): true si el usuario logueado es el jefe_inmediato de
           este empleado, o su evaluador manual cuando no tiene
           jefe_inmediato capturado. El admin YA NO puede calificar por
           default -- solo si además resulta ser literalmente el
           jefe/evaluador de esa persona. */
        $tieneJefeInmediato = !empty($emp['jefe_inmediato']);
        $puedeCalificarFila = $tieneJefeInmediato
            ? clavesCoinciden($emp['jefe_inmediato'], $claveActual)
            : clavesCoinciden($emp['clave_evaluador'], $claveActual);

        $estatus = 'pendiente';
        if ($totalKpis === 0) {
            $estatus = 'sin_kpis';
        } elseif ($evaluados >= $totalKpis) {
            $estatus = 'completo';
        } elseif ($evaluados > 0) {
            $estatus = 'en_progreso';
        }

        $datos[] = [
            'no_empleado'        => $emp['no_empleado'],
            'clave'              => $emp['clave'],
            'nombre'             => $emp['nombre'],
            'email'              => $emp['email'],
            'categoria'          => $emp['categoria'],
            'fuente_evaluador'   => $emp['fuente_evaluador'],
            'es_uno_mismo'       => clavesCoinciden($emp['clave'], $claveActual),
            'puede_calificar'    => $puedeCalificarFila,
            'iniciales'          => iniciales($emp['nombre']),
            'total_kpis'         => $totalKpis,
            'suma_ponderacion'   => $sumaPonderacion,
            'evaluados'          => $evaluados,
            /* porcentaje_avance = cuántos de sus KPIs ya se capturaron (¿ya
               se terminó de llenar la evaluación?). porcentaje_desempeno =
               qué tan bien le fue (calificación lograda contra el máximo
               posible, es decir la suma de ponderaciones de sus KPIs) --
               este último es el que se pinta en el anillo de la tarjeta,
               para que no diga "100%" solo porque ya se capturó todo,
               cuando en realidad calificó 60 de 100. */
            'porcentaje_avance'  => $totalKpis > 0 ? round(($evaluados / $totalKpis) * 100, 1) : 0,
            'porcentaje_desempeno' => $sumaPonderacion > 0 ? round(($calificacion / $sumaPonderacion) * 100, 1) : 0,
            'calificacion'       => $calificacion,
            'estatus'            => $estatus,
        ];
    }

    return $datos;
}

function normalizarIndicador($s) {
    $s = trim((string) $s);
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
}

/* Catálogo MAESTRO de indicadores (RRHH.catalogo_indicadores): el
   nombre de un KPI ("Captura de folios", "Cumplimiento de meta
   mensual", etc.) ya no se escribe suelto cada vez que se da de alta
   para un empleado -- se busca primero aquí (sin importar mayúsculas
   ni espacios) y se reutiliza; solo se crea uno nuevo si de verdad no
   existe. Así "Captura de folios" para Erick y para Pedro terminan
   apuntando al MISMO id_indicador (cada quien con su propia
   ponderación/meta en catalogo_kpis), en vez de crear un renglón
   nuevo del catálogo por cada empleado. Regresa ['id'=>, 'nombre'=>
   (el nombre "oficial", tal cual quedó la primera vez que se creó)]. */
function resolverIndicadorMaestro($conecta, $nombre, $tipoMeta, $creadoPor) {
    $nombre = trim((string) $nombre);
    if ($nombre === '') return null;

    $norm = normalizarIndicador($nombre);
    $normEsc = mysqli_real_escape_string($conecta->conexion, $norm);

    $rs = $conecta->consulta(
        "SELECT id, nombre FROM RRHH.catalogo_indicadores
         WHERE LOWER(TRIM(nombre)) = '{$normEsc}' LIMIT 1"
    );
    $row = $rs ? mysqli_fetch_assoc($rs) : null;

    if ($row) {
        return ['id' => (int) $row['id'], 'nombre' => $row['nombre']];
    }

    $nombreEsc   = mysqli_real_escape_string($conecta->conexion, $nombre);
    $tipoMetaEsc = mysqli_real_escape_string($conecta->conexion, $tipoMeta ?: 'porcentaje');
    $creadoPorEsc = mysqli_real_escape_string($conecta->conexion, (string) $creadoPor);

    $conecta->consulta(
        "INSERT INTO RRHH.catalogo_indicadores (nombre, tipo_meta, creado_por)
         VALUES ('{$nombreEsc}', '{$tipoMetaEsc}', '{$creadoPorEsc}')"
    );
    $idNuevo = mysqli_insert_id($conecta->conexion);

    return ['id' => $idNuevo, 'nombre' => $nombre];
}

/* Administrador de ESTE módulo: antes se usaba nivel_usuario() (nivel
   12) del resto del sitio, pero eso también clasificaba como "admin"
   a gente como JAGONZALEZ que tiene ese nivel por otras razones fuera
   de KPIs -- y aquí terminaban viendo a TODOS los empleados en vez de
   solo a sus subordinados. Por instrucción explícita, el único admin
   del módulo de KPIs es la clave ADMINISTRA; cualquier otro usuario,
   sin importar su nivel en el resto del sitio, solo ve a quien le
   reporta (jefe_inmediato / evaluador_manual) -- y a sí mismo. */
$CLAVES_ADMIN_KPI = ['ADMINISTRA'];

$claveActual = obtenerClaveUsuarioActual($conecta);
$esAdminKpi  = $claveActual !== null && in_array(strtoupper(trim($claveActual)), $CLAVES_ADMIN_KPI, true);

/* ════════════════════════════════════════════════════════════
   ACTION: getMisEvaluados
   Empleados donde jefe_inmediato = clave del usuario logueado (o
   con evaluador_manual asignado a él), con su avance de evaluación
   en el periodo pedido (o el actual). Los KPIs son por empleado, así
   que el total/ponderación se calcula individualmente por cada uno.
   Si el usuario es admin (nivel 12), ve a TODOS los empleados, no
   solo a sus evaluados directos.
════════════════════════════════════════════════════════════ */
if ($action === 'getMisEvaluados') {

    if (!$claveActual) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo identificar tu usuario en el sistema.']);
        exit;
    }

    $periodo = trim($input['periodo'] ?? $_GET['periodo'] ?? periodoActual());
    $claveEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);

    if ($esAdminKpi) {
        /* Administrador: ve a todos los empleados activos que sean staff (staff = 1).
           Se trae también jefe_inmediato + evaluador_manual para poder calcular
           'puede_calificar' por fila más abajo sin una query extra por empleado --
           el admin YA NO puede calificar por default (ver esEvaluadorDirecto), así
           que solo sale en true en el caso raro de que el admin sea literalmente
           el jefe_inmediato/evaluador manual de alguien. */
        $rs = $conecta->consulta(
            "SELECT e.no_empleado, u.clave, u.nombre, u.email, e.categoria, e.jefe_inmediato,
                    em.clave_evaluador,
                    IF(e.jefe_inmediato IS NULL OR e.jefe_inmediato = '', 'manual', 'jefe_inmediato') AS fuente_evaluador
             FROM carobra.empleados e
             INNER JOIN carobra.usuarios u ON u.clave = e.clave
             LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
             WHERE u.activo = 1 AND e.staff = 1
             ORDER BY u.nombre"
        );
    } else {
        /* Asignación por clave (select clave, jefe_inmediato from carobra.empleados):
           el empleado entra en "mis evaluados" si su jefe_inmediato es el usuario
           actual, o -si no tiene jefe_inmediato capturado- si se le asignó este
           usuario manualmente en RRHH.evaluador_manual. Solo cuentan los que son
           staff (staff = 1) -- select * from empleados where staff = 1.

           OJO: ya NO se incluye "OR u.clave = claveEsc" aquí -- un jefe
           directo con subordinados debe ver SOLO a sus subordinados, no a
           sí mismo mezclado en la lista (eso es justo el bug reportado:
           Erick, que sí tiene gente a su cargo, se veía a sí mismo además
           de sus subordinados). El caso de alguien SIN nadie a su cargo
           (que sí necesita verse a sí mismo para consultar su propio
           avance) se resuelve aparte, más abajo, solo cuando esta consulta
           regresa vacía. */
        $rs = $conecta->consulta(
            "SELECT e.no_empleado, u.clave, u.nombre, u.email, e.categoria, e.jefe_inmediato,
                    em.clave_evaluador,
                    IF(e.jefe_inmediato IS NULL OR e.jefe_inmediato = '', 'manual', 'jefe_inmediato') AS fuente_evaluador
             FROM carobra.empleados e
             INNER JOIN carobra.usuarios u ON u.clave = e.clave
             LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
             WHERE u.activo = 1
               AND e.staff = 1
               AND (
                     e.jefe_inmediato = '{$claveEsc}'
                     OR (
                          (e.jefe_inmediato IS NULL OR e.jefe_inmediato = '')
                          AND em.clave_evaluador = '{$claveEsc}'
                        )
                   )
             ORDER BY u.nombre"
        );
    }

    /* Si la tabla RRHH.evaluador_manual (u otra referenciada aquí) no
       existe todavía en tu servidor, esta consulta truena -- antes se
       tragaba el error y devolvía la lista vacía sin avisar. Ahora se
       reporta explícito para no perder tiempo adivinando. */
    if ($rs === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error de base de datos: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    $empleados = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $empleados[] = $row;
        }
    }

    /* Si NO es admin y la lista de subordinados salió vacía (no tiene a
       nadie a su cargo, ni por jefe_inmediato ni por evaluador_manual),
       se incluye a sí mismo como único renglón -- así puede consultar su
       propio avance en lugar de ver la pantalla completamente vacía. Si
       SÍ tiene subordinados, esto no se ejecuta: solo ve a sus
       subordinados, nunca mezclado con su propio renglón (ver comentario
       arriba, este era justo el bug reportado). */
    if (!$esAdminKpi && empty($empleados)) {
        $rsSelf = $conecta->consulta(
            "SELECT e.no_empleado, u.clave, u.nombre, u.email, e.categoria, e.jefe_inmediato,
                    em.clave_evaluador,
                    IF(e.jefe_inmediato IS NULL OR e.jefe_inmediato = '', 'manual', 'jefe_inmediato') AS fuente_evaluador
             FROM carobra.empleados e
             INNER JOIN carobra.usuarios u ON u.clave = e.clave
             LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
             WHERE u.activo = 1 AND e.staff = 1 AND u.clave = '{$claveEsc}'"
        );
        if ($rsSelf) {
            while ($row = mysqli_fetch_assoc($rsSelf)) {
                $empleados[] = $row;
            }
        }
    }

    $datos = enriquecerAvanceKpi($conecta, $empleados, $periodo, $claveActual);

    echo json_encode([
        'ok'            => true,
        'periodo'       => $periodo,
        'es_admin'      => $esAdminKpi,
        'mi_clave'      => $claveActual,
        /* Permiso de ROL (no por empleado): ¿puede este usuario asignar
           KPIs a CUALQUIER empleado? Admin siempre, o si está en la lista
           de asignadores autorizados. Se manda una sola vez aquí (no por
           fila) porque no depende de a quién se le esté asignando. */
        'puede_asignar' => puedeAsignarKpi($conecta, $claveActual, $esAdminKpi),
        'datos'         => $datos,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getTodosUsuariosKpi
   Se le fue un pedo grande al usuario: quien tiene permiso de
   ASIGNAR KPIs (admin o asignador autorizado en
   RRHH.kpis_asignadores) puede asignar a CUALQUIER empleado, sin
   importar si es su subordinado -- pero la pestaña "Mis evaluados"
   solo muestra a tus subordinados directos (o a ti mismo si no
   tienes ninguno). Un asignador que no es jefe de nadie se quedaba
   sin forma de ver/elegir a los demás empleados de la empresa para
   asignarles KPIs. Esta pestaña nueva ("Todos los usuarios") es
   justo para eso: TODOS los empleados activos (staff=1), sin
   filtrar por jerarquía, exclusiva de quien puede asignar.
   'puede_calificar' por fila sigue siendo el mismo cálculo real
   (jefe directo/evaluador efectivo) -- ver esta lista completa no
   te da permiso de calificar a nadie que no sea tuyo.
════════════════════════════════════════════════════════════ */
if ($action === 'getTodosUsuariosKpi') {

    if (!$claveActual) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo identificar tu usuario en el sistema.']);
        exit;
    }

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'Solo quien puede asignar KPIs puede ver el listado completo de usuarios.']);
        exit;
    }

    $periodo = trim($input['periodo'] ?? $_GET['periodo'] ?? periodoActual());
    $claveActualEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);

    /* Se excluye al propio usuario: "Todos los usuarios" es para elegir
       a alguien más a quien asignarle KPIs -- si quiere ver su propio
       avance, ya existe "Mis evaluados" para eso. */
    $rs = $conecta->consulta(
        "SELECT e.no_empleado, u.clave, u.nombre, u.email, e.categoria, e.jefe_inmediato,
                em.clave_evaluador,
                IF(e.jefe_inmediato IS NULL OR e.jefe_inmediato = '', 'manual', 'jefe_inmediato') AS fuente_evaluador
         FROM carobra.empleados e
         INNER JOIN carobra.usuarios u ON u.clave = e.clave
         LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
         WHERE u.activo = 1 AND e.staff = 1 AND u.clave <> '{$claveActualEsc}'
         ORDER BY u.nombre"
    );

    if ($rs === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error de base de datos: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    $empleados = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $empleados[] = $row;
    }

    $datos = enriquecerAvanceKpi($conecta, $empleados, $periodo, $claveActual);

    echo json_encode([
        'ok'            => true,
        'periodo'       => $periodo,
        'es_admin'      => $esAdminKpi,
        'mi_clave'      => $claveActual,
        'puede_asignar' => true,
        'datos'         => $datos,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getEvaluacionEmpleado
   Catálogo de ESE empleado (los KPIs son únicos por persona) + lo
   ya capturado (si existe) para el periodo. Permite editar si quien
   pide es su evaluador efectivo (jefe directo o manual) o si es
   administrador (nivel 12).
════════════════════════════════════════════════════════════ */
if ($action === 'getEvaluacionEmpleado') {

    $claveEmpleado = trim($input['clave_empleado'] ?? $_GET['clave_empleado'] ?? '');
    $periodo       = trim($input['periodo'] ?? $_GET['periodo'] ?? periodoActual());

    if (!$claveActual || $claveEmpleado === '') {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $rsEmp = $conecta->consulta(
        "SELECT u.nombre FROM carobra.usuarios u WHERE u.clave = '{$claveEmpEsc}' LIMIT 1"
    );
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    if (!puedeGestionarEmpleado($conecta, $claveActual, $esAdminKpi, $claveEmpleado)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para evaluar a este empleado (no eres su jefe directo, su evaluador asignado ni administrador).']);
        exit;
    }

    $periodoEsc = mysqli_real_escape_string($conecta->conexion, $periodo);

    $rs = $conecta->consulta(
        "SELECT k.id AS id_kpi, k.indicador, k.descripcion, k.ponderacion, k.meta, k.tipo_meta,
                r.resultado, r.cumplimiento, r.calificacion, r.comentarios, r.estatus, r.fecha_evaluacion
         FROM RRHH.catalogo_kpis k
         LEFT JOIN RRHH.kpis_resultados r
                ON r.id_kpi = k.id AND r.clave_empleado = '{$claveEmpEsc}' AND r.periodo = '{$periodoEsc}'
         WHERE k.activo = 1 AND k.clave_empleado = '{$claveEmpEsc}'
         ORDER BY k.ponderacion DESC, k.indicador"
    );

    $kpis = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $kpis[] = $row;
        }
    }

    echo json_encode([
        'ok'              => true,
        'empleado'        => ['clave' => $claveEmpleado, 'nombre' => $rowEmp['nombre']],
        'periodo'         => $periodo,
        /* El admin puede seguir VIENDO esta pantalla (por eso no se le
           bloqueó arriba), pero ya no puede GUARDAR -- el frontend usa
           esta bandera para ocultar/deshabilitar el botón Guardar cuando
           quien abrió esto no es el jefe directo/evaluador efectivo. */
        'puede_calificar' => esEvaluadorDirecto($conecta, $claveActual, $claveEmpleado),
        'kpis'            => $kpis,
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarEvaluacion
   Guarda (upsert) el resultado de cada KPI capturado para un
   empleado/periodo. Recalcula calificacion = ponderacion*cumplimiento/100.

   Por instrucción explícita: SOLO el jefe directo/evaluador efectivo
   puede calificar -- el administrador YA NO tiene bypass aquí (antes sí
   podía calificar a cualquiera). Ver esEvaluadorDirecto().
════════════════════════════════════════════════════════════ */
if ($action === 'guardarEvaluacion') {

    $claveEmpleado = trim($input['clave_empleado'] ?? '');
    $periodo       = trim($input['periodo'] ?? periodoActual());
    $evaluaciones  = $input['evaluaciones'] ?? [];

    if (!$claveActual || $claveEmpleado === '' || !is_array($evaluaciones) || empty($evaluaciones)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    if (!esEvaluadorDirecto($conecta, $claveActual, $claveEmpleado)) {
        echo json_encode(['ok' => false, 'msg' => 'Solo el jefe directo (o evaluador asignado) de este empleado puede calificarlo.']);
        exit;
    }

    /* Trae las ponderaciones vigentes del catálogo DE ESE EMPLEADO para
       calcular la calificación en el servidor (no confiar en lo que
       mande el navegador). Al filtrar por clave_empleado también se
       evita que se cuele un id_kpi de otro empleado. */
    $rsKpis = $conecta->consulta(
        "SELECT id, ponderacion FROM RRHH.catalogo_kpis WHERE activo = 1 AND clave_empleado = '{$claveEmpEsc}'"
    );
    $ponderaciones = [];
    if ($rsKpis) {
        while ($row = mysqli_fetch_assoc($rsKpis)) {
            $ponderaciones[(int) $row['id']] = (float) $row['ponderacion'];
        }
    }

    $periodoEsc     = mysqli_real_escape_string($conecta->conexion, $periodo);
    $evaluadoPorEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);

    $guardados = 0;
    $errores   = [];

    foreach ($evaluaciones as $ev) {
        $idKpi = (int) ($ev['id_kpi'] ?? 0);
        if (!isset($ponderaciones[$idKpi])) continue; // KPI inactivo/inexistente/de otro empleado, se ignora

        $resultado     = trim((string) ($ev['resultado'] ?? ''));
        $cumplimiento  = is_numeric($ev['cumplimiento'] ?? null) ? (float) $ev['cumplimiento'] : null;
        $comentarios   = trim((string) ($ev['comentarios'] ?? ''));

        if ($cumplimiento === null) continue; // sin % de cumplimiento no se guarda esa fila

        $calificacion = round($ponderaciones[$idKpi] * $cumplimiento / 100, 2);

        $resultadoEsc    = mysqli_real_escape_string($conecta->conexion, $resultado);
        $comentariosEsc  = mysqli_real_escape_string($conecta->conexion, $comentarios);
        $cumplimientoEsc = mysqli_real_escape_string($conecta->conexion, (string) $cumplimiento);
        $calificacionEsc = mysqli_real_escape_string($conecta->conexion, (string) $calificacion);

        $ok = $conecta->consulta(
            "INSERT INTO RRHH.kpis_resultados
                (id_kpi, periodo, clave_empleado, resultado, cumplimiento, calificacion, evaluado_por, comentarios, fecha_evaluacion, estatus)
             VALUES
                ({$idKpi}, '{$periodoEsc}', '{$claveEmpEsc}', '{$resultadoEsc}', '{$cumplimientoEsc}', '{$calificacionEsc}', '{$evaluadoPorEsc}', '{$comentariosEsc}', NOW(), 'evaluado')
             ON DUPLICATE KEY UPDATE
                resultado = VALUES(resultado),
                cumplimiento = VALUES(cumplimiento),
                calificacion = VALUES(calificacion),
                evaluado_por = VALUES(evaluado_por),
                comentarios = VALUES(comentarios),
                fecha_evaluacion = VALUES(fecha_evaluacion),
                estatus = 'evaluado'"
        );

        if ($ok) {
            $guardados++;
        } else {
            $errores[] = "KPI {$idKpi}: " . mysqli_error($conecta->conexion);
        }
    }

    echo json_encode(['ok' => true, 'guardados' => $guardados, 'errores' => $errores]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoKpis
   Lista los KPIs de UN empleado específico (clave_empleado es
   obligatorio: ya no existe un catálogo global). Solo lo puede ver
   su evaluador efectivo o un administrador (nivel 12).
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoKpis') {

    $claveEmpleado = trim($input['clave_empleado'] ?? $_GET['clave_empleado'] ?? '');

    if (!$claveActual || $claveEmpleado === '') {
        echo json_encode(['ok' => false, 'msg' => 'Selecciona primero un empleado.']);
        exit;
    }

    if (!puedeGestionarEmpleado($conecta, $claveActual, $esAdminKpi, $claveEmpleado)) {
        /* Si el motivo es que el empleado no tiene NADIE asignado (ni
           jefe_inmediato ni evaluador_manual), se lo decimos al frontend
           para que pueda ofrecer asignar un evaluador ahí mismo en vez
           de dejarlo en un callejón sin salida. Si SÍ tiene evaluador
           pero es alguien más, el mensaje es distinto (no se puede
           "robar" el KPI de otro evaluador). */
        $evaluadorActual = resolverEvaluador($conecta, $claveEmpleado);
        echo json_encode([
            'ok'              => false,
            'msg'             => $evaluadorActual === null
                ? 'Este empleado no tiene jefe directo ni evaluador asignado todavía.'
                : 'No tienes permiso para administrar los KPIs de este empleado (su evaluador es ' . $evaluadorActual['nombre'] . ').',
            'sin_evaluador'   => $evaluadorActual === null,
        ]);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $rs = $conecta->consulta(
        "SELECT id, clave_empleado, indicador, descripcion, ponderacion, meta, tipo_meta, activo
         FROM RRHH.catalogo_kpis
         WHERE clave_empleado = '{$claveEmpEsc}'
         ORDER BY activo DESC, ponderacion DESC, indicador"
    );

    $datos = [];
    $sumaActivos = 0;
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            if ($row['activo'] == 1) $sumaActivos += (float) $row['ponderacion'];
            $datos[] = $row;
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos, 'suma_ponderacion_activos' => round($sumaActivos, 2)]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarKpi
   id vacío/0 -> crea uno nuevo (requiere clave_empleado); con id ->
   actualiza el existente (no se puede cambiar de dueño).

   ASIGNAR ya no depende de ser jefe directo del empleado -- es un
   permiso de rol (admin o asignador autorizado), puede asignar a
   CUALQUIER empleado. Ver puedeAsignarKpi().
════════════════════════════════════════════════════════════ */
if ($action === 'guardarKpi') {

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para asignar KPIs. Pídele al administrador que te agregue a la lista de asignadores autorizados.']);
        exit;
    }

    $id          = (int) ($input['id'] ?? 0);
    $indicador   = trim($input['indicador'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');
    $ponderacion = is_numeric($input['ponderacion'] ?? null) ? (float) $input['ponderacion'] : null;
    $meta        = trim($input['meta'] ?? '');
    $tipoMeta    = trim($input['tipo_meta'] ?? 'porcentaje');

    $tiposValidos = ['porcentaje', 'numerica', 'cumplido_no_cumplido'];
    if (!in_array($tipoMeta, $tiposValidos, true)) $tipoMeta = 'porcentaje';

    if ($indicador === '' || $ponderacion === null || $meta === '') {
        echo json_encode(['ok' => false, 'msg' => 'Faltan datos: indicador, ponderación y meta son obligatorios.']);
        exit;
    }

    /* En vez de guardar el nombre tal cual se escribió, se busca/crea
       en el catálogo MAESTRO (RRHH.catalogo_indicadores) -- así "Captura
       de folios" no se vuelve a crear cada vez que otro empleado
       también lo tiene, aunque cada quien lo escriba con distinta
       mayúscula o espacios. $indicador se reemplaza por el nombre
       "oficial" que ya exista (o el recién creado, tal cual). */
    $maestro = resolverIndicadorMaestro($conecta, $indicador, $tipoMeta, $claveActual ?? ($_SESSION['nombre'] ?? ''));
    $idIndicador = $maestro ? $maestro['id'] : null;
    if ($maestro) $indicador = $maestro['nombre'];

    $indicadorEsc     = mysqli_real_escape_string($conecta->conexion, $indicador);
    $descripcionEsc   = mysqli_real_escape_string($conecta->conexion, $descripcion);
    $metaEsc          = mysqli_real_escape_string($conecta->conexion, $meta);
    $tipoMetaEsc      = mysqli_real_escape_string($conecta->conexion, $tipoMeta);
    $ponderacionEsc   = mysqli_real_escape_string($conecta->conexion, (string) $ponderacion);
    $idIndicadorSql   = $idIndicador ? (int) $idIndicador : 'NULL';

    if ($id > 0) {
        /* Edición: el dueño (clave_empleado) sale del registro existente,
           no del navegador -- así no se puede "robar" un KPI para otro empleado. */
        $rsDueno = $conecta->consulta("SELECT clave_empleado FROM RRHH.catalogo_kpis WHERE id = {$id} LIMIT 1");
        $rowDueno = $rsDueno ? mysqli_fetch_assoc($rsDueno) : null;

        if (!$rowDueno) {
            echo json_encode(['ok' => false, 'msg' => 'KPI no encontrado.']);
            exit;
        }

        /* El permiso de asignar ya se validó arriba (rol, no por empleado). */
        $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $rowDueno['clave_empleado']);

        /* Las ponderaciones activas de un empleado no deben pasar de 100%
           (si no, la calificación total ya no representa un porcentaje
           real -- ej. 290% como pasó cuando se dieron de alta KPIs
           duplicados por error). Se excluye este mismo id de la suma
           porque se va a reemplazar su valor. */
        $rsSuma = $conecta->consulta(
            "SELECT COALESCE(SUM(ponderacion),0) AS suma FROM RRHH.catalogo_kpis
             WHERE clave_empleado = '{$claveEmpEsc}' AND activo = 1 AND id != {$id}"
        );
        $sumaActual = $rsSuma ? round((float) mysqli_fetch_assoc($rsSuma)['suma'], 2) : 0;

        /* Redondear el TOTAL a 2 decimales antes de comparar contra 100 --
           así llenar exactamente el % disponible (ej. tenías 95%, agregas
           justo 5%) nunca se rechaza por un residuo de precisión de punto
           flotante (95 + 5 podía llegar como 100.00000000000001 y antes
           se comparaba sin redondear, aunque el margen de 0.01 debería
           haber bastado, este redondeo lo hace matemáticamente imposible
           de fallar en el caso de "cabe justo"). */
        if (round($sumaActual + $ponderacion, 2) > 100) {
            $disponible = max(0, round(100 - $sumaActual, 2));
            echo json_encode(['ok' => false, 'msg' => "La suma de ponderaciones activas de este empleado no puede pasar de 100%. Ya tiene {$sumaActual}% en otros KPIs activos -- disponible: {$disponible}%."]);
            exit;
        }

        $ok = $conecta->consulta(
            "UPDATE RRHH.catalogo_kpis
             SET indicador = '{$indicadorEsc}', id_indicador = {$idIndicadorSql}, descripcion = '{$descripcionEsc}',
                 ponderacion = '{$ponderacionEsc}', meta = '{$metaEsc}', tipo_meta = '{$tipoMetaEsc}'
             WHERE id = {$id}"
        );
    } else {
        $claveEmpleado = trim($input['clave_empleado'] ?? '');

        if ($claveEmpleado === '') {
            echo json_encode(['ok' => false, 'msg' => 'Falta indicar a qué empleado pertenece este KPI.']);
            exit;
        }

        /* El permiso de asignar ya se validó arriba (rol, no por empleado):
           un asignador autorizado puede crear KPIs para CUALQUIER empleado,
           no solo para sus propios subordinados. */
        $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);
        $creadoPor   = mysqli_real_escape_string($conecta->conexion, $claveActual ?? ($_SESSION['nombre'] ?? ''));

        $rsSuma = $conecta->consulta(
            "SELECT COALESCE(SUM(ponderacion),0) AS suma FROM RRHH.catalogo_kpis
             WHERE clave_empleado = '{$claveEmpEsc}' AND activo = 1"
        );
        $sumaActual = $rsSuma ? round((float) mysqli_fetch_assoc($rsSuma)['suma'], 2) : 0;

        /* Ver comentario de la rama de edición arriba: redondear el
           TOTAL antes de comparar contra 100 para que "cabe justo"
           nunca se rechace por precisión de punto flotante. */
        if (round($sumaActual + $ponderacion, 2) > 100) {
            $disponible = max(0, round(100 - $sumaActual, 2));
            echo json_encode(['ok' => false, 'msg' => "La suma de ponderaciones activas de este empleado no puede pasar de 100%. Ya tiene {$sumaActual}% en otros KPIs activos -- disponible: {$disponible}%."]);
            exit;
        }

        $ok = $conecta->consulta(
            "INSERT INTO RRHH.catalogo_kpis (clave_empleado, id_indicador, indicador, descripcion, ponderacion, meta, tipo_meta, creado_por)
             VALUES ('{$claveEmpEsc}', {$idIndicadorSql}, '{$indicadorEsc}', '{$descripcionEsc}', '{$ponderacionEsc}', '{$metaEsc}', '{$tipoMetaEsc}', '{$creadoPor}')"
        );
    }

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: cambiarEstatusKpi
   Activa/desactiva un KPI del catálogo (no se borra, para no
   perder el histórico de evaluaciones ya capturadas con él).
   Mismo permiso que asignar (rol, no por empleado): admin o
   asignador autorizado.
════════════════════════════════════════════════════════════ */
if ($action === 'cambiarEstatusKpi') {

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para modificar KPIs.']);
        exit;
    }

    $id     = (int) ($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'KPI inválido.']);
        exit;
    }

    $rsDueno = $conecta->consulta("SELECT clave_empleado FROM RRHH.catalogo_kpis WHERE id = {$id} LIMIT 1");
    $rowDueno = $rsDueno ? mysqli_fetch_assoc($rsDueno) : null;

    if (!$rowDueno) {
        echo json_encode(['ok' => false, 'msg' => 'KPI no encontrado.']);
        exit;
    }

    $ok = $conecta->consulta("UPDATE RRHH.catalogo_kpis SET activo = {$activo} WHERE id = {$id}");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getAsignadoresKpi
   Lista de empleados actualmente autorizados para ASIGNAR KPIs
   (además del admin ADMINISTRA, que siempre puede). Solo el admin
   puede ver/gestionar esta lista -- es él quien decide quién más
   tiene este permiso.
════════════════════════════════════════════════════════════ */
if ($action === 'getAsignadoresKpi') {

    if (!$esAdminKpi) {
        echo json_encode(['ok' => false, 'msg' => 'Solo el administrador puede ver/gestionar los asignadores de KPIs.']);
        exit;
    }

    $rs = $conecta->consulta(
        "SELECT a.id, a.clave_empleado, a.asignado_por, a.asignado_en, u.nombre
         FROM RRHH.kpis_asignadores a
         LEFT JOIN carobra.usuarios u ON u.clave = a.clave_empleado
         WHERE a.activo = 1
         ORDER BY u.nombre"
    );

    if ($rs === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error de base de datos: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    $datos = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $datos[] = $row;
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: agregarAsignadorKpi
   Autoriza a un empleado a asignar KPIs a cualquier persona. No
   reemplaza a los que ya estaban -- se van sumando (varios asignadores
   autorizados a la vez). Si ya estaba pero desactivado, lo reactiva.
════════════════════════════════════════════════════════════ */
if ($action === 'agregarAsignadorKpi') {

    if (!$esAdminKpi) {
        echo json_encode(['ok' => false, 'msg' => 'Solo el administrador puede agregar asignadores de KPIs.']);
        exit;
    }

    $claveEmpleado = trim($input['clave_empleado'] ?? '');
    if ($claveEmpleado === '') {
        echo json_encode(['ok' => false, 'msg' => 'Falta indicar el empleado.']);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $rsEmp = $conecta->consulta("SELECT nombre FROM carobra.usuarios WHERE clave = '{$claveEmpEsc}' LIMIT 1");
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;
    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $asignadoPorEsc = mysqli_real_escape_string($conecta->conexion, (string) $claveActual);

    $ok = $conecta->consulta(
        "INSERT INTO RRHH.kpis_asignadores (clave_empleado, activo, asignado_por)
         VALUES ('{$claveEmpEsc}', 1, '{$asignadoPorEsc}')
         ON DUPLICATE KEY UPDATE activo = 1, asignado_por = VALUES(asignado_por), asignado_en = NOW()"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true, 'nombre' => $rowEmp['nombre']]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: quitarAsignadorKpi
   Le quita a un empleado el permiso de asignar KPIs (soft-delete:
   activo = 0, no se borra la fila -- conserva el historial de quién
   tuvo este permiso y cuándo).
════════════════════════════════════════════════════════════ */
if ($action === 'quitarAsignadorKpi') {

    if (!$esAdminKpi) {
        echo json_encode(['ok' => false, 'msg' => 'Solo el administrador puede quitar asignadores de KPIs.']);
        exit;
    }

    $claveEmpleado = trim($input['clave_empleado'] ?? '');
    if ($claveEmpleado === '') {
        echo json_encode(['ok' => false, 'msg' => 'Falta indicar el empleado.']);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $ok = $conecta->consulta(
        "UPDATE RRHH.kpis_asignadores SET activo = 0 WHERE clave_empleado = '{$claveEmpEsc}'"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarEmpleados
   Buscador en tiempo real (campo insensible a mayúsculas/minúsculas)
   por nombre, clave o no_empleado. Devuelve el evaluador efectivo de
   cada resultado (jefe_inmediato o asignación manual) para pintar el
   badge correspondiente en el dropdown. Se excluye al propio usuario
   en sesión -- este buscador es siempre para encontrar a alguien MÁS
   (ver su avance, asignarle KPIs, asignarle evaluador, agregarlo como
   asignador de KPIs, etc.), nunca a uno mismo.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarEmpleados') {

    $termino = trim($input['termino'] ?? $_GET['termino'] ?? '');

    if (mb_strlen($termino, 'UTF-8') < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    $terminoEsc = mysqli_real_escape_string($conecta->conexion, $termino);
    $claveActualEsc = $claveActual ? mysqli_real_escape_string($conecta->conexion, $claveActual) : '';

    /* Solo empleados staff (staff = 1) -- select * from empleados where staff = 1. */
    $rs = $conecta->consulta(
        "SELECT e.no_empleado, u.clave, u.nombre, e.categoria, e.jefe_inmediato
         FROM carobra.empleados e
         INNER JOIN carobra.usuarios u ON u.clave = e.clave
         WHERE u.activo = 1
           AND e.staff = 1
           AND u.clave <> '{$claveActualEsc}'
           AND (u.nombre LIKE '%{$terminoEsc}%' OR u.clave LIKE '%{$terminoEsc}%' OR e.no_empleado LIKE '%{$terminoEsc}%')
         ORDER BY u.nombre
         LIMIT 25"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $evaluador = resolverEvaluador($conecta, $row['clave']);
            $datos[] = [
                'no_empleado'      => $row['no_empleado'],
                'clave'            => $row['clave'],
                'nombre'           => $row['nombre'],
                'categoria'        => $row['categoria'],
                'iniciales'        => iniciales($row['nombre']),
                'tiene_evaluador'  => $evaluador !== null,
                'evaluador'        => $evaluador,
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarMisSubordinados
   Igual que buscarEmpleados, pero acotado: el buscador de arriba de
   "Mis evaluados" es para "ver el avance/KPIs de alguien", así que un
   jefe directo solo debe poder encontrar ahí a SUS subordinados (o a
   sí mismo si no tiene ninguno) -- no a cualquier empleado de la
   empresa. El admin sí sigue viendo/buscando a todos.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarMisSubordinados') {

    $termino = trim($input['termino'] ?? $_GET['termino'] ?? '');

    if (mb_strlen($termino, 'UTF-8') < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    if (!$claveActual) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo identificar tu usuario en el sistema.']);
        exit;
    }

    $terminoEsc = mysqli_real_escape_string($conecta->conexion, $termino);

    if ($esAdminKpi) {
        $condicionPoblacion = '1=1';
    } else {
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);

        /* Igual que en getMisEvaluados: la población son los subordinados
           (por jefe_inmediato o evaluador_manual); solo si esa población
           está completamente vacía (no tiene a nadie a su cargo) se
           amplía para incluirlo a él mismo, así puede buscarse a sí mismo
           para ver su propio avance. Esto NO depende del término de
           búsqueda -- se decide una sola vez con un chequeo de existencia
           aparte, para no confundir "no tiene subordinados" con "tiene
           subordinados pero ninguno coincide con lo que escribiste". */
        $condicionSubordinados =
            "(e.jefe_inmediato = '{$claveEsc}'
              OR (
                   (e.jefe_inmediato IS NULL OR e.jefe_inmediato = '')
                   AND em.clave_evaluador = '{$claveEsc}'
                 ))";

        $rsExiste = $conecta->consulta(
            "SELECT 1 FROM carobra.empleados e
             INNER JOIN carobra.usuarios u ON u.clave = e.clave
             LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
             WHERE u.activo = 1 AND e.staff = 1 AND {$condicionSubordinados} LIMIT 1"
        );
        $tieneSubordinados = $rsExiste && mysqli_num_rows($rsExiste) > 0;

        $condicionPoblacion = $tieneSubordinados
            ? $condicionSubordinados
            : "u.clave = '{$claveEsc}'";
    }

    $rs = $conecta->consulta(
        "SELECT e.no_empleado, u.clave, u.nombre, e.categoria, e.jefe_inmediato
         FROM carobra.empleados e
         INNER JOIN carobra.usuarios u ON u.clave = e.clave
         LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
         WHERE u.activo = 1
           AND e.staff = 1
           AND ({$condicionPoblacion})
           AND (u.nombre LIKE '%{$terminoEsc}%' OR u.clave LIKE '%{$terminoEsc}%' OR e.no_empleado LIKE '%{$terminoEsc}%')
         ORDER BY u.nombre
         LIMIT 25"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $evaluador = resolverEvaluador($conecta, $row['clave']);
            $datos[] = [
                'no_empleado'      => $row['no_empleado'],
                'clave'            => $row['clave'],
                'nombre'           => $row['nombre'],
                'categoria'        => $row['categoria'],
                'iniciales'        => iniciales($row['nombre']),
                'tiene_evaluador'  => $evaluador !== null,
                'evaluador'        => $evaluador,
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: buscarIndicadores
   Buscador en tiempo real del catálogo MAESTRO de indicadores (para
   el autocompletar de "Indicador" al dar de alta un KPI, en las 3
   pantallas donde se puede crear uno). Cualquiera que pueda dar de
   alta KPIs puede buscar aquí -- solo se listan los activos, para no
   sugerir nombres que un admin ya desactivó. Incluye cuántos
   empleados ya lo tienen asignado, para que se note cuando ya es un
   indicador "compartido" de verdad.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarIndicadores') {

    $termino = trim($input['termino'] ?? $_GET['termino'] ?? '');

    if (mb_strlen($termino, 'UTF-8') < 2) {
        echo json_encode(['ok' => true, 'datos' => []]);
        exit;
    }

    $terminoEsc = mysqli_real_escape_string($conecta->conexion, $termino);

    $rs = $conecta->consulta(
        "SELECT ci.id, ci.nombre, ci.tipo_meta,
                (SELECT COUNT(DISTINCT k.clave_empleado) FROM RRHH.catalogo_kpis k WHERE k.id_indicador = ci.id AND k.activo = 1) AS empleados_usando
         FROM RRHH.catalogo_indicadores ci
         WHERE ci.activo = 1 AND ci.nombre LIKE '%{$terminoEsc}%'
         ORDER BY empleados_usando DESC, ci.nombre
         LIMIT 15"
    );

    /* Si la consulta truena (permisos, tabla no existe en esa conexión,
       etc.) antes esto se tragaba el error y regresaba "ok:true" con
       datos vacíos -- parecía "no hay resultados" cuando en realidad
       era un error de servidor. Ahora se reporta explícito. */
    if ($rs === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error de base de datos: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    $datos = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $datos[] = [
            'id'                => (int) $row['id'],
            'nombre'            => $row['nombre'],
            'tipo_meta'         => $row['tipo_meta'],
            'empleados_usando'  => (int) $row['empleados_usando'],
        ];
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getCatalogoIndicadores
   Listado completo del catálogo maestro, con cuántos empleados tiene
   cada uno activo -- para la pestaña de administración. Mismo permiso
   que asignar KPIs (rol: admin o asignador autorizado), porque este
   catálogo es justo lo que se usa al asignar -- quien no puede
   asignar tampoco necesita administrar de dónde salen los nombres.
════════════════════════════════════════════════════════════ */
if ($action === 'getCatalogoIndicadores') {

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'Solo quien puede asignar KPIs puede ver el catálogo maestro completo.']);
        exit;
    }

    $termino = trim($input['termino'] ?? $_GET['termino'] ?? '');
    $where = "1=1";
    if ($termino !== '') {
        $terminoEsc = mysqli_real_escape_string($conecta->conexion, $termino);
        $where = "ci.nombre LIKE '%{$terminoEsc}%'";
    }

    $rs = $conecta->consulta(
        "SELECT ci.id, ci.nombre, ci.tipo_meta, ci.activo,
                (SELECT COUNT(DISTINCT k.clave_empleado) FROM RRHH.catalogo_kpis k WHERE k.id_indicador = ci.id AND k.activo = 1) AS empleados_usando
         FROM RRHH.catalogo_indicadores ci
         WHERE {$where}
         ORDER BY ci.activo DESC, empleados_usando DESC, ci.nombre"
    );

    /* Mismo caso: antes un error de SQL se traducía en "sin resultados"
       (ok:true, datos:[]) en vez de reportarse -- por eso parecía que
       "no traía nada" aunque los datos sí existieran en la tabla. */
    if ($rs === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error de base de datos: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    $datos = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $datos[] = [
            'id'                => (int) $row['id'],
            'nombre'            => $row['nombre'],
            'tipo_meta'         => $row['tipo_meta'],
            'activo'            => (int) $row['activo'] === 1,
            'empleados_usando'  => (int) $row['empleados_usando'],
        ];
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: guardarIndicadorMaestro
   Alta (id=0) o edición (id>0) de un indicador del catálogo maestro
   -- renombrar, cambiar tipo de meta o descripción. Si se renombra,
   el nuevo nombre se propaga también a los KPIs ya asignados que usan
   ese id_indicador (para que la ficha/evaluación de cada empleado
   muestre el nombre actualizado). Mismo permiso que asignar KPIs.
════════════════════════════════════════════════════════════ */
if ($action === 'guardarIndicadorMaestro') {

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'Solo quien puede asignar KPIs puede editar el catálogo maestro.']);
        exit;
    }

    $id          = (int) ($input['id'] ?? 0);
    $nombre      = trim($input['nombre'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');
    $tipoMeta    = trim($input['tipo_meta'] ?? 'porcentaje');

    $tiposValidos = ['porcentaje', 'numerica', 'cumplido_no_cumplido'];
    if (!in_array($tipoMeta, $tiposValidos, true)) $tipoMeta = 'porcentaje';

    if ($nombre === '') {
        echo json_encode(['ok' => false, 'msg' => 'El nombre es obligatorio.']);
        exit;
    }

    /* No se permite dejarlo con el mismo nombre (normalizado) que otro
       indicador ya existente -- si no, se estaría creando el duplicado
       que precisamente se quiere evitar con este catálogo. */
    $norm = normalizarIndicador($nombre);
    $normEsc = mysqli_real_escape_string($conecta->conexion, $norm);
    $rsDup = $conecta->consulta(
        "SELECT id FROM RRHH.catalogo_indicadores WHERE LOWER(TRIM(nombre)) = '{$normEsc}' AND id != {$id} LIMIT 1"
    );
    if ($rsDup && mysqli_fetch_assoc($rsDup)) {
        echo json_encode(['ok' => false, 'msg' => 'Ya existe otro indicador con ese nombre (o muy parecido) en el catálogo.']);
        exit;
    }

    $nombreEsc      = mysqli_real_escape_string($conecta->conexion, $nombre);
    $descripcionEsc = mysqli_real_escape_string($conecta->conexion, $descripcion);
    $tipoMetaEsc    = mysqli_real_escape_string($conecta->conexion, $tipoMeta);

    if ($id > 0) {
        $ok = $conecta->consulta(
            "UPDATE RRHH.catalogo_indicadores
             SET nombre = '{$nombreEsc}', descripcion = '{$descripcionEsc}', tipo_meta = '{$tipoMetaEsc}'
             WHERE id = {$id}"
        );
        if ($ok) {
            $conecta->consulta("UPDATE RRHH.catalogo_kpis SET indicador = '{$nombreEsc}' WHERE id_indicador = {$id}");
        }
    } else {
        $creadoPorEsc = mysqli_real_escape_string($conecta->conexion, $claveActual ?? ($_SESSION['nombre'] ?? ''));
        $ok = $conecta->consulta(
            "INSERT INTO RRHH.catalogo_indicadores (nombre, descripcion, tipo_meta, creado_por)
             VALUES ('{$nombreEsc}', '{$descripcionEsc}', '{$tipoMetaEsc}', '{$creadoPorEsc}')"
        );
    }

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: cambiarEstatusIndicador
   Activa/desactiva un indicador del catálogo maestro (no se borra --
   los KPIs ya asignados con él siguen funcionando igual, solo deja de
   aparecer en el buscador para KPIs nuevos). Mismo permiso que asignar KPIs.
════════════════════════════════════════════════════════════ */
if ($action === 'cambiarEstatusIndicador') {

    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'Solo quien puede asignar KPIs puede activar/desactivar el catálogo maestro.']);
        exit;
    }

    $id     = (int) ($input['id'] ?? 0);
    $activo = !empty($input['activo']) ? 1 : 0;

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Indicador inválido.']);
        exit;
    }

    $ok = $conecta->consulta("UPDATE RRHH.catalogo_indicadores SET activo = {$activo} WHERE id = {$id}");

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getFichaEmpleado
   Ficha completa de UN empleado para el modal del buscador: sus
   KPIs (únicos de él) + lo que lleva capturado en el periodo + quién
   es su evaluador efectivo. A diferencia de getEvaluacionEmpleado,
   esto NO exige que quien consulta sea el evaluador -- cualquier
   usuario con sesión puede *ver* el avance. "puede_editar" le dice
   al frontend si además puede capturar/editar (evaluador efectivo
   o administrador nivel 12).
════════════════════════════════════════════════════════════ */
if ($action === 'getFichaEmpleado') {

    $claveEmpleado = trim($input['clave_empleado'] ?? $_GET['clave_empleado'] ?? '');
    $periodo       = trim($input['periodo'] ?? $_GET['periodo'] ?? periodoActual());

    if (!$claveActual || $claveEmpleado === '') {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $claveEmpEsc = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);

    $rsEmp = $conecta->consulta(
        "SELECT u.clave, u.nombre, e.categoria, e.no_empleado
         FROM carobra.empleados e
         INNER JOIN carobra.usuarios u ON u.clave = e.clave
         WHERE u.clave = '{$claveEmpEsc}' LIMIT 1"
    );
    $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;

    if (!$rowEmp) {
        echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado.']);
        exit;
    }

    $evaluador = resolverEvaluador($conecta, $claveEmpleado);
    $periodoEsc = mysqli_real_escape_string($conecta->conexion, $periodo);

    $rs = $conecta->consulta(
        "SELECT k.id AS id_kpi, k.indicador, k.descripcion, k.ponderacion, k.meta, k.tipo_meta,
                r.resultado, r.cumplimiento, r.calificacion, r.comentarios, r.estatus, r.fecha_evaluacion
         FROM RRHH.catalogo_kpis k
         LEFT JOIN RRHH.kpis_resultados r
                ON r.id_kpi = k.id AND r.clave_empleado = '{$claveEmpEsc}' AND r.periodo = '{$periodoEsc}'
         WHERE k.activo = 1 AND k.clave_empleado = '{$claveEmpEsc}'
         ORDER BY k.ponderacion DESC, k.indicador"
    );

    $kpis = [];
    $sumaCalificacion = 0;
    $evaluados = 0;
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            if ($row['estatus'] === 'evaluado') {
                $evaluados++;
                $sumaCalificacion += (float) $row['calificacion'];
            }
            $kpis[] = $row;
        }
    }

    /* Dos permisos separados, ya no uno solo:
       - puede_calificar: llenar resultado/cumplimiento y guardar la
         evaluación -- SOLO el jefe directo/evaluador efectivo (el admin
         ya NO tiene bypass aquí).
       - puede_asignar: agregar/editar/activar KPIs del catálogo de este
         empleado (la sección "agregar KPI" inline de la ficha) -- permiso
         de rol: admin o asignador autorizado, sin importar si es jefe de
         este empleado en particular. */
    $puedeCalificar = ($evaluador !== null && clavesCoinciden($evaluador['clave'], $claveActual));
    $puedeAsignar   = puedeAsignarKpi($conecta, $claveActual, $esAdminKpi);

    echo json_encode([
        'ok'             => true,
        'empleado'       => [
            'clave'       => $rowEmp['clave'],
            'nombre'      => $rowEmp['nombre'],
            'categoria'   => $rowEmp['categoria'],
            'no_empleado' => $rowEmp['no_empleado'],
        ],
        'periodo'        => $periodo,
        'evaluador'      => $evaluador,
        'es_admin'       => $esAdminKpi,
        'puede_calificar' => $puedeCalificar,
        'puede_asignar'   => $puedeAsignar,
        'kpis'           => $kpis,
        'total_kpis'     => count($kpis),
        'evaluados'      => $evaluados,
        'calificacion'   => round($sumaCalificacion, 2),
    ]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: asignarEvaluadorManual
   Para empleados sin jefe_inmediato capturado en carobra.empleados:
   asigna a mano quién los va a evaluar (upsert en RRHH.evaluador_manual).
   Si el empleado ya tiene jefe_inmediato, ese sigue mandando -- esto
   solo se usa como respaldo para cuando no hay nadie.
════════════════════════════════════════════════════════════ */
if ($action === 'asignarEvaluadorManual') {

    $claveEmpleado  = trim($input['clave_empleado'] ?? '');
    $claveEvaluador = trim($input['clave_evaluador'] ?? '');

    if ($claveEmpleado === '' || $claveEvaluador === '') {
        echo json_encode(['ok' => false, 'msg' => 'Faltan datos: empleado y evaluador son obligatorios.']);
        exit;
    }

    if ($claveEmpleado === $claveEvaluador) {
        echo json_encode(['ok' => false, 'msg' => 'Un empleado no puede ser su propio evaluador.']);
        exit;
    }

    $claveEmpEsc  = mysqli_real_escape_string($conecta->conexion, $claveEmpleado);
    $claveEvalEsc = mysqli_real_escape_string($conecta->conexion, $claveEvaluador);

    $rsVal = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE clave = '{$claveEvalEsc}' AND activo = 1 LIMIT 1");
    if (!$rsVal || !mysqli_fetch_assoc($rsVal)) {
        echo json_encode(['ok' => false, 'msg' => 'El evaluador seleccionado no existe o está inactivo.']);
        exit;
    }

    $asignadoPorEsc = mysqli_real_escape_string($conecta->conexion, $claveActual ?? '');

    $ok = $conecta->consulta(
        "INSERT INTO RRHH.evaluador_manual (clave_empleado, clave_evaluador, asignado_por)
         VALUES ('{$claveEmpEsc}', '{$claveEvalEsc}', '{$asignadoPorEsc}')
         ON DUPLICATE KEY UPDATE
            clave_evaluador = VALUES(clave_evaluador),
            asignado_por = VALUES(asignado_por)"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al asignar: ' . mysqli_error($conecta->conexion)]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: importarKpis
   Carga masiva desde el Excel de "MEDICION DE KPI" (formato SISCA):
   el navegador ya lo parseó con SheetJS y manda uno o más GRUPOS
   (uno por hoja/equipo, cada hoja puede traer su propio periodo --
   el archivo "master" trae una hoja por jefe y no todas están en el
   mismo mes). Aquí se hace el upsert real a RRHH.catalogo_kpis (por
   clave_empleado + indicador) y a RRHH.kpis_resultados (por id_kpi +
   periodo + clave_empleado), grupo por grupo.

   Formato esperado de $input:
     grupos: [
       { periodo: 'YYYY-MM', hoja: 'DR', empleados: [ { clave, nombre, kpis: [ { indicador, ponderacion, meta, resultado } ] } ] },
       ...
     ]
   (también acepta el formato viejo de un solo grupo: {periodo, empleados}
   por compatibilidad).

   ponderacion/meta/resultado vienen como FRACCIÓN (0.5 = 50%, tal
   cual el Excel), aquí se pasan a la escala 0-100 que usa la BD.
   cumplimiento se recalcula en el servidor como resultado/meta*100
   (no se confía en la "Calificacion" que ya trae el Excel).

   Mismo permiso que dar de alta KPIs a mano: solo el evaluador
   efectivo del empleado o un administrador (nivel 12) puede
   importarle datos -- si el archivo trae gente que no te toca, esos
   renglones se reportan como omitidos, no se rechaza todo el archivo.
════════════════════════════════════════════════════════════ */
if ($action === 'importarKpis') {

    if (!$claveActual) {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo identificar tu usuario en el sistema.']);
        exit;
    }

    $grupos = $input['grupos'] ?? null;
    if (!is_array($grupos) || empty($grupos)) {
        // Compatibilidad con el formato viejo (un solo periodo/empleados sueltos).
        if (!empty($input['empleados'])) {
            $grupos = [[ 'periodo' => $input['periodo'] ?? '', 'hoja' => '', 'empleados' => $input['empleados'] ]];
        } else {
            $grupos = [];
        }
    }

    if (empty($grupos)) {
        echo json_encode(['ok' => false, 'msg' => 'El archivo no tiene empleados/KPIs reconocibles.']);
        exit;
    }

    $evaluadoPorEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);

    $resumen = [
        'empleados_procesados'   => 0,
        'kpis_creados'           => 0,
        'kpis_actualizados'      => 0,
        'evaluaciones_guardadas' => 0,
        'evaluaciones_omitidas_sin_permiso' => 0,
        'periodos_procesados'    => [],
        'empleados_omitidos'     => [],
    ];

    /* norm() = misma normalización (minúsculas + trim) para comparar
       indicadores en ambos lados (lo que trae el Excel y lo que ya está
       en catalogo_kpis), sin tener que ir preguntándole a la BD renglón
       por renglón. */
    $norm = function ($s) {
        $s = trim((string) $s);
        return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    };

    /* ── Paso 1: aplanar todo el archivo (todas las hojas/equipos) a una
       sola lista en memoria, sin tocar la BD todavía. Antes se hacía
       1-2 consultas POR EMPLEADO y otras 2-3 consultas POR CADA KPI de
       cada empleado -- con un archivo de varias hojas y cientos de
       renglones eso eran miles de idas y vueltas al servidor, por eso
       tardaba tanto. De aquí en adelante todo se resuelve en lote:
       IN (...) para leer y un solo INSERT/UPDATE de muchos renglones
       a la vez para escribir. ── */
    $itemsEmpleado = [];
    $todasClaves = [];

    foreach ($grupos as $grupo) {
        $periodo   = trim((string) ($grupo['periodo'] ?? ''));
        $hojaNom   = trim((string) ($grupo['hoja'] ?? ''));
        $empleados = $grupo['empleados'] ?? [];

        if (!preg_match('/^\d{4}-\d{2}$/', $periodo)) {
            $resumen['empleados_omitidos'][] = "Hoja '{$hojaNom}' -- no se pudo detectar su periodo (Periodo/Año), se omitió completa";
            continue;
        }

        if (!is_array($empleados) || empty($empleados)) continue;

        if (!in_array($periodo, $resumen['periodos_procesados'], true)) {
            $resumen['periodos_procesados'][] = $periodo;
        }

        foreach ($empleados as $emp) {
            $claveEmp  = trim((string) ($emp['clave'] ?? ''));
            $nombreEmp = trim((string) ($emp['nombre'] ?? ''));
            $kpisRows  = $emp['kpis'] ?? [];

            if ($claveEmp === '' || !is_array($kpisRows) || empty($kpisRows)) continue;

            $itemsEmpleado[] = [
                'clave' => $claveEmp, 'nombre' => $nombreEmp, 'hoja' => $hojaNom,
                'periodo' => $periodo, 'kpis' => $kpisRows,
            ];
            $todasClaves[$claveEmp] = true;
        }
    }

    if (empty($itemsEmpleado)) {
        echo json_encode(['ok' => true, 'resumen' => $resumen]);
        exit;
    }

    $escList = function ($claves) use ($conecta) {
        return implode(',', array_map(function ($c) use ($conecta) {
            return "'" . mysqli_real_escape_string($conecta->conexion, $c) . "'";
        }, $claves));
    };
    $clavesInSql = $escList(array_keys($todasClaves));

    /* ── Paso 2: en una sola consulta, quiénes de esas claves existen
       de verdad; y quién es el evaluador efectivo de cada una -- antes
       eran 1-2 consultas POR EMPLEADO. El mapa de evaluadores ahora se
       arma SIEMPRE (ya no solo "si no eres admin"), porque se necesita
       para decidir, más abajo, a quién sí se le pueden guardar
       resultados/calificaciones (columna "Resultado" del Excel) --
       eso sigue siendo "calificar", y calificar ya no es un privilegio
       de admin, es exclusivo del jefe directo/evaluador efectivo. */
    $usuariosSet = [];
    $rsU = $conecta->consulta("SELECT clave FROM carobra.usuarios WHERE clave IN ({$clavesInSql})");
    if ($rsU) while ($row = mysqli_fetch_assoc($rsU)) $usuariosSet[$row['clave']] = true;

    $evaluadorClaveMap = [];
    $rsEv = $conecta->consulta(
        "SELECT e.clave, e.jefe_inmediato, em.clave_evaluador AS evaluador_manual
         FROM carobra.empleados e
         LEFT JOIN RRHH.evaluador_manual em ON em.clave_empleado = e.clave
         WHERE e.clave IN ({$clavesInSql})"
    );
    if ($rsEv) {
        while ($row = mysqli_fetch_assoc($rsEv)) {
            $evaluadorClaveMap[$row['clave']] = !empty($row['jefe_inmediato'])
                ? $row['jefe_inmediato']
                : (!empty($row['evaluador_manual']) ? $row['evaluador_manual'] : null);
        }
    }

    /* Asignar (crear/actualizar el catálogo de KPIs) ya es un permiso de
       ROL, no por empleado -- se valida UNA sola vez para todo el
       archivo, no fila por fila. Si no tienes permiso de asignar, todo
       el archivo se rechaza de una (no tendría sentido dejar pasar
       algunas filas sí y otras no cuando el permiso ni siquiera depende
       de a quién le toque). */
    if (!puedeAsignarKpi($conecta, $claveActual, $esAdminKpi)) {
        echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para asignar KPIs. Pídele al administrador que te agregue a la lista de asignadores autorizados.']);
        exit;
    }

    /* ── Paso 3: valida cada empleado con los mapas de arriba (ya sin
       consultas nuevas) y arma la lista plana de renglones de KPI. ── */
    $kpiWorkItems = [];
    $clavesValidas = [];
    $puedeCalificarPorClave = []; // clave => bool -- controla el Paso 8 (guardar resultados)

    foreach ($itemsEmpleado as $item) {
        $claveEmp = $item['clave'];

        if (empty($usuariosSet[$claveEmp])) {
            $resumen['empleados_omitidos'][] = "{$claveEmp} ({$item['nombre']}) [{$item['hoja']} {$item['periodo']}] -- esa clave no existe en carobra.usuarios";
            continue;
        }

        /* El permiso de ASIGNAR ya se validó arriba para todo el archivo
           (rol, no por empleado) -- aquí solo se calcula si además se
           pueden guardar resultados/calificaciones de este empleado en
           particular (eso sí sigue siendo por relación jefe/subordinado). */
        $puedeCalificarPorClave[$claveEmp] = clavesCoinciden($evaluadorClaveMap[$claveEmp] ?? null, $claveActual);

        $resumen['empleados_procesados']++;
        $clavesValidas[$claveEmp] = true;

        foreach ($item['kpis'] as $k) {
            $indicador = trim((string) ($k['indicador'] ?? ''));
            if ($indicador === '') continue;

            $ponderacionFrac = is_numeric($k['ponderacion'] ?? null) ? (float) $k['ponderacion'] : 0;
            $metaFrac        = is_numeric($k['meta'] ?? null) ? (float) $k['meta'] : null;
            $resultadoFrac   = is_numeric($k['resultado'] ?? null) ? (float) $k['resultado'] : null;

            $kpiWorkItems[] = [
                'clave'          => $claveEmp,
                'periodo'        => $item['periodo'],
                'indicador'      => $indicador,
                'indicadorNorm'  => $norm($indicador),
                'ponderacion'    => round($ponderacionFrac * 100, 2),
                'meta'           => $metaFrac !== null ? (rtrim(rtrim(number_format($metaFrac * 100, 1), '0'), '.') . '%') : '100%',
                'metaFrac'       => $metaFrac,
                'resultadoFrac'  => $resultadoFrac,
            ];
        }
    }

    if (empty($clavesValidas)) {
        echo json_encode(['ok' => true, 'resumen' => $resumen]);
        exit;
    }

    $clavesValidasInSql = $escList(array_keys($clavesValidas));

    /* ── Paso 3.5: catálogo MAESTRO de indicadores -- se resuelve/crea
       en lote para TODO el archivo (no por empleado). Así "Captura de
       folios" en la hoja de Erick y en la de Pedro terminan apuntando
       al MISMO id_indicador (aunque cada hoja lo haya escrito con
       distinta mayúscula/espacio), en vez de crear un indicador nuevo
       del catálogo por cada quien que lo tiene. ── */
    $indicadorMaestroMap = []; // indicadorNorm => ['id'=>, 'nombre'=>]
    $normsUnicos = [];
    foreach ($kpiWorkItems as $item) $normsUnicos[$item['indicadorNorm']] = true;

    if (!empty($normsUnicos)) {
        $normsEscList = implode(',', array_map(function ($n) use ($conecta) {
            return "'" . mysqli_real_escape_string($conecta->conexion, $n) . "'";
        }, array_keys($normsUnicos)));

        $rsInd = $conecta->consulta("SELECT id, nombre FROM RRHH.catalogo_indicadores WHERE LOWER(TRIM(nombre)) IN ({$normsEscList})");
        if ($rsInd) {
            while ($row = mysqli_fetch_assoc($rsInd)) {
                $indicadorMaestroMap[$norm($row['nombre'])] = ['id' => (int) $row['id'], 'nombre' => $row['nombre']];
            }
        }

        // Los que no existían todavía se dan de alta una sola vez cada
        // uno (aunque aparezcan en decenas de renglones del archivo),
        // usando el primer texto con el que aparecieron como el nombre
        // "oficial" del catálogo.
        $faltantes = [];
        foreach ($kpiWorkItems as $item) {
            if (!isset($indicadorMaestroMap[$item['indicadorNorm']]) && !isset($faltantes[$item['indicadorNorm']])) {
                $faltantes[$item['indicadorNorm']] = $item['indicador'];
            }
        }

        if (!empty($faltantes)) {
            $creadoPorIndEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);
            foreach (array_chunk($faltantes, 300, true) as $chunk) {
                $valores = [];
                foreach ($chunk as $nombreOriginal) {
                    $n = mysqli_real_escape_string($conecta->conexion, $nombreOriginal);
                    $valores[] = "('{$n}', 'porcentaje', '{$creadoPorIndEsc}')";
                }
                $conecta->consulta("INSERT INTO RRHH.catalogo_indicadores (nombre, tipo_meta, creado_por) VALUES " . implode(',', $valores));
            }

            $rsInd2 = $conecta->consulta("SELECT id, nombre FROM RRHH.catalogo_indicadores WHERE LOWER(TRIM(nombre)) IN ({$normsEscList})");
            if ($rsInd2) {
                while ($row = mysqli_fetch_assoc($rsInd2)) {
                    $indicadorMaestroMap[$norm($row['nombre'])] = ['id' => (int) $row['id'], 'nombre' => $row['nombre']];
                }
            }
        }
    }

    /* ── Paso 4: catálogo YA existente de esos empleados, en una sola
       consulta -- decide en memoria si cada renglón del Excel es alta
       o actualización, en vez de una consulta SELECT por cada KPI. ── */
    $catalogoMap = []; // [clave][indicadorNorm] => id
    $rsCat = $conecta->consulta("SELECT id, clave_empleado, indicador FROM RRHH.catalogo_kpis WHERE clave_empleado IN ({$clavesValidasInSql})");
    if ($rsCat) {
        while ($row = mysqli_fetch_assoc($rsCat)) {
            $catalogoMap[$row['clave_empleado']][$norm($row['indicador'])] = (int) $row['id'];
        }
    }

    $insertsMap = []; // "clave|indicadorNorm" => [clave, indicador, id_indicador, ponderacion, meta]
    $updatesMap = []; // id => [indicador, id_indicador, ponderacion, meta]
    foreach ($kpiWorkItems as $item) {
        $idExistente = $catalogoMap[$item['clave']][$item['indicadorNorm']] ?? null;
        $maestro = $indicadorMaestroMap[$item['indicadorNorm']] ?? null;
        $nombreCanonico = $maestro['nombre'] ?? $item['indicador'];
        $idIndicador = $maestro['id'] ?? null;

        if ($idExistente) {
            $updatesMap[$idExistente] = [
                'indicador' => $nombreCanonico, 'id_indicador' => $idIndicador,
                'ponderacion' => $item['ponderacion'], 'meta' => $item['meta'],
            ];
        } else {
            $insertsMap[$item['clave'] . '|' . $item['indicadorNorm']] = [
                'clave' => $item['clave'], 'indicador' => $nombreCanonico, 'id_indicador' => $idIndicador,
                'ponderacion' => $item['ponderacion'], 'meta' => $item['meta'],
            ];
        }
    }

    /* ── Paso 5: alta de KPIs nuevos en bloques (un INSERT de varios
       cientos de renglones, en vez de uno por uno). ── */
    $creadoPorEsc = mysqli_real_escape_string($conecta->conexion, $claveActual);
    if (!empty($insertsMap)) {
        foreach (array_chunk(array_values($insertsMap), 300) as $chunk) {
            $valores = [];
            foreach ($chunk as $row) {
                $c  = mysqli_real_escape_string($conecta->conexion, $row['clave']);
                $i  = mysqli_real_escape_string($conecta->conexion, $row['indicador']);
                $p  = mysqli_real_escape_string($conecta->conexion, (string) $row['ponderacion']);
                $m  = mysqli_real_escape_string($conecta->conexion, $row['meta']);
                $idi = $row['id_indicador'] ? (int) $row['id_indicador'] : 'NULL';
                $valores[] = "('{$c}', {$idi}, '{$i}', '{$p}', '{$m}', 'porcentaje', '{$creadoPorEsc}')";
            }
            $conecta->consulta(
                "INSERT INTO RRHH.catalogo_kpis (clave_empleado, id_indicador, indicador, ponderacion, meta, tipo_meta, creado_por)
                 VALUES " . implode(',', $valores)
            );
            $resumen['kpis_creados'] += count($chunk);
        }
    }

    /* ── Paso 6: actualización de ponderación/meta de KPIs que ya
       existían -- un solo UPDATE por bloque con CASE, en vez de un
       UPDATE por cada renglón. También normaliza el nombre/id_indicador
       al del catálogo maestro (útil para filas viejas que aún no
       tenían id_indicador). ── */
    if (!empty($updatesMap)) {
        foreach (array_chunk($updatesMap, 300, true) as $chunk) {
            $casesPond = [];
            $casesMeta = [];
            $casesInd  = [];
            $casesIdInd = [];
            $ids = [];
            foreach ($chunk as $id => $vals) {
                $id = (int) $id;
                $ids[] = $id;
                $p = mysqli_real_escape_string($conecta->conexion, (string) $vals['ponderacion']);
                $m = mysqli_real_escape_string($conecta->conexion, $vals['meta']);
                $i = mysqli_real_escape_string($conecta->conexion, $vals['indicador']);
                $idi = $vals['id_indicador'] ? (int) $vals['id_indicador'] : 'NULL';
                $casesPond[] = "WHEN {$id} THEN '{$p}'";
                $casesMeta[] = "WHEN {$id} THEN '{$m}'";
                $casesInd[]  = "WHEN {$id} THEN '{$i}'";
                $casesIdInd[] = "WHEN {$id} THEN {$idi}";
            }
            $conecta->consulta(
                "UPDATE RRHH.catalogo_kpis
                 SET ponderacion = CASE id " . implode(' ', $casesPond) . " END,
                     meta = CASE id " . implode(' ', $casesMeta) . " END,
                     indicador = CASE id " . implode(' ', $casesInd) . " END,
                     id_indicador = CASE id " . implode(' ', $casesIdInd) . " END,
                     tipo_meta = 'porcentaje', activo = 1
                 WHERE id IN (" . implode(',', $ids) . ")"
            );
            $resumen['kpis_actualizados'] += count($chunk);
        }
    }

    /* ── Paso 7: si hubo altas nuevas, sus ids todavía no estaban en
       $catalogoMap -- se relee el catálogo de estos empleados (una
       sola consulta más) para tener el id real de cada renglón nuevo
       y poder guardar sus resultados. ── */
    if (!empty($insertsMap)) {
        $catalogoMap = [];
        $rsCat2 = $conecta->consulta("SELECT id, clave_empleado, indicador FROM RRHH.catalogo_kpis WHERE clave_empleado IN ({$clavesValidasInSql})");
        if ($rsCat2) {
            while ($row = mysqli_fetch_assoc($rsCat2)) {
                $catalogoMap[$row['clave_empleado']][$norm($row['indicador'])] = (int) $row['id'];
            }
        }
    }

    /* ── Paso 8: resultados/calificaciones, también en bloques con
       INSERT ... ON DUPLICATE KEY UPDATE (la tabla ya tiene su UNIQUE
       KEY por id_kpi+periodo+clave_empleado, así que un solo INSERT de
       varios renglones da de alta o actualiza cada uno según toque).
       calificacion = ponderacion * (resultado/meta) -- igual fórmula
       que trae el Excel pero recalculada aquí, no se confía en la
       columna "Calificacion" del archivo. Si meta viene en 0/vacía se
       asume 1 (100%) para no dividir entre cero. ── */
    $resultadosMap = []; // "idKpi|periodo" => valores (última coincidencia gana, igual que antes)
    foreach ($kpiWorkItems as $item) {
        if ($item['resultadoFrac'] === null) continue;

        /* CALIFICAR sigue siendo exclusivo del jefe directo/evaluador
           efectivo de ESE empleado -- ni un asignador autorizado ni el
           admin pueden colar resultados vía el Excel para gente que no
           evalúan ellos. Si el archivo trae "Resultado" para alguien que
           no es su evaluador, el KPI igual se crea/actualiza (eso sí se
           permitió arriba), pero el resultado se ignora. */
        if (empty($puedeCalificarPorClave[$item['clave']])) {
            $resumen['evaluaciones_omitidas_sin_permiso']++;
            continue;
        }

        $idKpi = $catalogoMap[$item['clave']][$item['indicadorNorm']] ?? null;
        if (!$idKpi) continue;

        $metaParaCalculo = ($item['metaFrac'] !== null && $item['metaFrac'] > 0) ? $item['metaFrac'] : 1;
        $cumplimiento    = round(($item['resultadoFrac'] / $metaParaCalculo) * 100, 2);
        $calificacion    = round($item['ponderacion'] * $cumplimiento / 100, 2);
        $resultadoTexto  = rtrim(rtrim(number_format($item['resultadoFrac'] * 100, 1), '0'), '.') . '%';

        $resultadosMap["{$idKpi}|{$item['periodo']}"] = [
            'idKpi' => $idKpi, 'periodo' => $item['periodo'], 'clave' => $item['clave'],
            'resultado' => $resultadoTexto, 'cumplimiento' => $cumplimiento, 'calificacion' => $calificacion,
        ];
    }

    if (!empty($resultadosMap)) {
        foreach (array_chunk(array_values($resultadosMap), 300) as $chunk) {
            $valores = [];
            foreach ($chunk as $r) {
                $c  = mysqli_real_escape_string($conecta->conexion, $r['clave']);
                $pe = mysqli_real_escape_string($conecta->conexion, $r['periodo']);
                $rt = mysqli_real_escape_string($conecta->conexion, $r['resultado']);
                $cu = mysqli_real_escape_string($conecta->conexion, (string) $r['cumplimiento']);
                $ca = mysqli_real_escape_string($conecta->conexion, (string) $r['calificacion']);
                $valores[] = "({$r['idKpi']}, '{$pe}', '{$c}', '{$rt}', '{$cu}', '{$ca}', '{$evaluadoPorEsc}', NOW(), 'evaluado')";
            }
            $conecta->consulta(
                "INSERT INTO RRHH.kpis_resultados
                    (id_kpi, periodo, clave_empleado, resultado, cumplimiento, calificacion, evaluado_por, fecha_evaluacion, estatus)
                 VALUES " . implode(',', $valores) . "
                 ON DUPLICATE KEY UPDATE
                    resultado = VALUES(resultado),
                    cumplimiento = VALUES(cumplimiento),
                    calificacion = VALUES(calificacion),
                    evaluado_por = VALUES(evaluado_por),
                    fecha_evaluacion = VALUES(fecha_evaluacion),
                    estatus = 'evaluado'"
            );
            $resumen['evaluaciones_guardadas'] += count($chunk);
        }
    }

    echo json_encode(['ok' => true, 'resumen' => $resumen]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);