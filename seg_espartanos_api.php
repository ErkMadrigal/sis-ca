<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();

$nombreSesion = $_SESSION['nombre'] ?? '';
$nivel        = nivel_usuario(utf8_decode($nombreSesion));

/* Quien tenga acceso a esta vista (seg_espartanos.php) tiene permiso
   completo sobre todo lo de aquí -- ya no se restringe a nivel 12.
   Se deja la variable (en vez de borrar todas las validaciones) para
   no tener que tocar cada acción una por una; simplemente siempre
   pasa la revisión de requiereAdmin(). */
$esAdmin      = true;

/* Clave de quien está haciendo la acción, para dejar rastro en
   asignado_por -- mismo criterio que otras vistas (buscar la clave
   del usuario logueado por su nombre). */
$queryClave = "Select clave from carobra.usuarios where nombre='" . mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreSesion)) . "'";
$bdClave    = $conecta->consulta($queryClave);
$tbClave    = $bdClave ? mysqli_fetch_array($bdClave) : null;
$claveSesion = $tbClave['clave'] ?? '';

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   Espartanos: listado (deportenis=1 + no_prom_dep='ESPARTANO' en
   carobra.empleados), equipos con meta individual por persona y un
   LÍDER por equipo (espartanos_equipos.clave = clave del espartano
   líder), lugar final por equipo (1er/2o/3er) y premio en $ por
   lugar. Asignar equipo/meta, crear/editar equipos y editar premios
   son acciones de admin -- listar y ver equipos es de cualquiera con
   sesión válida.
════════════════════════════════════════════════════════════ */

function responderError($msg) {
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

function requiereAdmin($esAdmin) {
    if (!$esAdmin) {
        responderError('No tienes permiso para hacer esto (se requiere nivel administrador).');
    }
}

/* ¿Existe ese equipo en espartanos_equipos? (los equipos ya no son
   solo 1, 2 y 3: se pueden crear más). */
function existeEquipo($conecta, $idEquipo) {
    $idEquipo = intval($idEquipo);
    if ($idEquipo < 1) return false;
    $bd = $conecta->consulta("Select id_equipo from carobra_2014.espartanos_equipos where id_equipo=$idEquipo");
    return ($bd && mysqli_num_rows($bd) > 0);
}

if ($action === 'listarEspartanos') {

    $busqueda = trim($input['busqueda'] ?? '');
    $wBusqueda = '';
    if ($busqueda !== '') {
        $busquedaEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($busqueda));
        $wBusqueda = " and (u.nombre like '%$busquedaEsc%' or e.clave like '%$busquedaEsc%') ";
    }

    $query = "Select e.clave, u.nombre, a.id_equipo, a.meta_individual,
                      p.pago_incentivos, p.pago_asegurado
                from carobra.empleados e
                    inner join carobra.usuarios u on (e.clave = u.clave)
                    left join carobra_2014.espartanos_asignaciones a on (a.clave = e.clave)
                    left join carobra_2014.espartanos_pagos p on (p.clave = e.clave)
                where e.deportenis = 1 and e.no_prom_dep = 'ESPARTANO'
                $wBusqueda
                order by u.nombre";

    $bd = $conecta->consulta($query);
    if (!$bd) {
        responderError('Error al consultar espartanos: ' . mysqli_error($conecta->conexion));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($bd)) {
        $data[] = [
            'clave'           => $row['clave'],
            'nombre'          => utf8_encode($row['nombre']),
            'id_equipo'       => $row['id_equipo'] !== null ? intval($row['id_equipo']) : null,
            'meta_individual' => $row['meta_individual'] !== null ? floatval($row['meta_individual']) : null,
            'pago_incentivos' => intval($row['pago_incentivos'] ?? 0) === 1,
            'pago_asegurado'  => intval($row['pago_asegurado'] ?? 0) === 1,
        ];
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

if ($action === 'getEquipos') {

    /* clave = líder del equipo (espartano); su nombre sale de
       carobra.usuarios igual que en el resto de las consultas. */
    $query = "Select eq.id_equipo, eq.nombre, eq.lugar_final, eq.clave as lider_clave, ul.nombre as lider_nombre,
                      count(a.clave) as miembros,
                      ifnull(sum(a.meta_individual),0) as suma_metas,
                      pr.monto as premio
                from carobra_2014.espartanos_equipos eq
                    left join carobra_2014.espartanos_asignaciones a on (a.id_equipo = eq.id_equipo)
                    left join carobra_2014.espartanos_premios pr on (pr.lugar = eq.lugar_final)
                    left join carobra.usuarios ul on (ul.clave = eq.clave)
                group by eq.id_equipo, eq.nombre, eq.lugar_final, eq.clave, ul.nombre, pr.monto
                order by eq.id_equipo";

    $bd = $conecta->consulta($query);
    if (!$bd) {
        responderError('Error al consultar equipos: ' . mysqli_error($conecta->conexion));
    }

    $equipos = [];
    while ($row = mysqli_fetch_assoc($bd)) {
        $equipos[] = [
            'id_equipo'    => intval($row['id_equipo']),
            'nombre'       => utf8_encode($row['nombre']),
            'clave'        => $row['lider_clave'] !== null && $row['lider_clave'] !== '' ? $row['lider_clave'] : null,
            'lider_nombre' => $row['lider_nombre'] !== null ? utf8_encode($row['lider_nombre']) : null,
            'lugar_final'  => $row['lugar_final'] !== null ? intval($row['lugar_final']) : null,
            'miembros'     => intval($row['miembros']),
            'suma_metas'   => floatval($row['suma_metas']),
            'premio'       => $row['premio'] !== null ? floatval($row['premio']) : 0,
        ];
    }

    $bdPremios = $conecta->consulta("Select lugar, monto from carobra_2014.espartanos_premios order by lugar");
    $premios = [];
    if ($bdPremios) {
        while ($row = mysqli_fetch_assoc($bdPremios)) {
            $premios[intval($row['lugar'])] = floatval($row['monto']);
        }
    }

    echo json_encode(['status' => 'ok', 'equipos' => $equipos, 'premios' => $premios, 'esAdmin' => $esAdmin]);
    exit;
}

/* Crear (id_equipo = 0) o editar un equipo: nombre + líder, que debe
   ser un espartano (deportenis=1 y no_prom_dep='ESPARTANO'). */
if ($action === 'guardarEquipo') {

    requiereAdmin($esAdmin);

    $idEquipo = intval($input['id_equipo'] ?? 0);
    $nombre   = trim($input['nombre'] ?? '');
    $clave    = trim($input['clave'] ?? '');

    if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 50) {
        responderError('Escribe el nombre del equipo (máximo 50 caracteres).');
    }
    if ($clave === '') {
        responderError('Elige al líder del equipo.');
    }

    $claveEsc  = mysqli_real_escape_string($conecta->conexion, $clave);
    $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));

    $bdLider = $conecta->consulta("Select e.clave from carobra.empleados e
                                    where e.clave='$claveEsc' and e.deportenis = 1 and e.no_prom_dep = 'ESPARTANO'");
    if (!$bdLider || mysqli_num_rows($bdLider) === 0) {
        responderError('El líder debe ser un espartano.');
    }

    if ($idEquipo > 0) {

        if (!existeEquipo($conecta, $idEquipo)) {
            responderError('El equipo no existe.');
        }

        $ok = $conecta->consulta("Update carobra_2014.espartanos_equipos
                                    set nombre='$nombreEsc', clave='$claveEsc'
                                    where id_equipo=$idEquipo");
        if (!$ok) {
            responderError('No se pudo actualizar el equipo: ' . mysqli_error($conecta->conexion));
        }

        echo json_encode(['status' => 'ok', 'id_equipo' => $idEquipo]);
        exit;
    }

    /* Equipo nuevo: id siguiente al más alto (por si id_equipo no es
       AUTO_INCREMENT en la tabla). lugar_final se queda en NULL. */
    $bdMax = $conecta->consulta("Select ifnull(max(id_equipo),0) + 1 as nuevo from carobra_2014.espartanos_equipos");
    $tbMax = $bdMax ? mysqli_fetch_assoc($bdMax) : null;
    $nuevoId = intval($tbMax['nuevo'] ?? 0);
    if ($nuevoId < 1) {
        responderError('No se pudo calcular el id del nuevo equipo.');
    }

    $ok = $conecta->consulta("Insert into carobra_2014.espartanos_equipos (id_equipo, nombre, clave)
                                values ($nuevoId, '$nombreEsc', '$claveEsc')");
    if (!$ok) {
        responderError('No se pudo crear el equipo: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok', 'id_equipo' => $nuevoId]);
    exit;
}

if ($action === 'asignarEquipoMasivo') {

    requiereAdmin($esAdmin);

    $claves    = $input['claves']    ?? [];
    $idEquipo  = intval($input['id_equipo'] ?? 0);
    $meta      = floatval($input['meta_individual'] ?? 0);

    if (!is_array($claves) || empty($claves)) {
        responderError('No seleccionaste a ningún espartano.');
    }
    if (!existeEquipo($conecta, $idEquipo)) {
        responderError('Equipo inválido.');
    }
    if ($meta < 0) {
        responderError('La meta individual no puede ser negativa.');
    }

    $claveSesionEsc = mysqli_real_escape_string($conecta->conexion, $claveSesion);
    $ok = 0;
    $errores = [];

    foreach ($claves as $clave) {
        $claveEsc = mysqli_real_escape_string($conecta->conexion, trim($clave));
        if ($claveEsc === '') continue;

        $query = "Insert into carobra_2014.espartanos_asignaciones (clave, id_equipo, meta_individual, asignado_por)
                    values ('$claveEsc', $idEquipo, $meta, '$claveSesionEsc')
                    on duplicate key update id_equipo=$idEquipo, meta_individual=$meta, asignado_por='$claveSesionEsc'";

        if ($conecta->consulta($query)) {
            $ok++;
        } else {
            $errores[] = $clave . ': ' . mysqli_error($conecta->conexion);
        }
    }

    echo json_encode(['status' => 'ok', 'actualizados' => $ok, 'errores' => $errores]);
    exit;
}

if ($action === 'quitarAsignacion') {

    requiereAdmin($esAdmin);

    $clave = trim($input['clave'] ?? '');
    if ($clave === '') {
        responderError('Falta la clave del espartano.');
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $ok = $conecta->consulta("Delete from carobra_2014.espartanos_asignaciones where clave='$claveEsc'");

    if (!$ok) {
        responderError('No se pudo quitar la asignación: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'guardarLugarFinal') {

    requiereAdmin($esAdmin);

    $idEquipo   = intval($input['id_equipo'] ?? 0);
    $lugarFinal = $input['lugar_final'] ?? null; // 1, 2, 3 o null para "sin definir"

    if (!existeEquipo($conecta, $idEquipo)) {
        responderError('Equipo inválido.');
    }

    if ($lugarFinal === null || $lugarFinal === '') {
        $conecta->consulta("Update carobra_2014.espartanos_equipos set lugar_final=NULL where id_equipo=$idEquipo");
        echo json_encode(['status' => 'ok']);
        exit;
    }

    $lugarFinal = intval($lugarFinal);
    if ($lugarFinal < 1 || $lugarFinal > 3) {
        responderError('Lugar inválido (debe ser 1, 2 o 3).');
    }

    /* Un lugar solo lo puede tener un equipo a la vez -- si otro
       equipo ya tenía este lugar, se le quita antes de asignárselo
       al nuevo (si no, el UNIQUE KEY de espartanos_equipos tronaría). */
    $conecta->consulta("Update carobra_2014.espartanos_equipos set lugar_final=NULL where lugar_final=$lugarFinal and id_equipo<>$idEquipo");

    $ok = $conecta->consulta("Update carobra_2014.espartanos_equipos set lugar_final=$lugarFinal where id_equipo=$idEquipo");
    if (!$ok) {
        responderError('No se pudo guardar el lugar final: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'guardarPremios') {

    requiereAdmin($esAdmin);

    $premios = $input['premios'] ?? [];
    if (!is_array($premios) || empty($premios)) {
        responderError('Faltan los montos de premio.');
    }

    foreach ([1, 2, 3] as $lugar) {
        if (!isset($premios[$lugar]) && !isset($premios[(string)$lugar])) continue;
        $monto = floatval($premios[$lugar] ?? $premios[(string)$lugar]);
        if ($monto < 0) $monto = 0;
        $conecta->consulta("Update carobra_2014.espartanos_premios set monto=$monto where lugar=$lugar");
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Rangos de meta (bonos escalonados): aparte de meta_individual
   (un solo número "a dónde debe llegar"), hay UNA tabla compartida
   de filas [rango_inferior, rango_superior] -> bono en $ que aplica
   IGUAL para todos los espartanos (no es un juego de rangos por
   persona). CRUD fila por fila (igual que la tabla de rangos del
   screenshot: guardar/borrar por renglón, no un formulario
   completo). Solo lectura para no-admin; guardar/borrar es admin-only.
════════════════════════════════════════════════════════════ */

if ($action === 'listarRangosMeta') {

    $bd = $conecta->consulta("Select id, rango_inferior, rango_superior, bono
                                from carobra_2014.espartanos_rangos_meta
                                order by rango_inferior");

    if (!$bd) {
        responderError('Error al consultar los rangos: ' . mysqli_error($conecta->conexion));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($bd)) {
        $data[] = [
            'id'             => intval($row['id']),
            'rango_inferior' => floatval($row['rango_inferior']),
            'rango_superior' => floatval($row['rango_superior']),
            'bono'           => floatval($row['bono']),
        ];
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

if ($action === 'guardarRangoMeta') {

    requiereAdmin($esAdmin);

    $id            = intval($input['id'] ?? 0);
    $rangoInferior = floatval($input['rango_inferior'] ?? -1);
    $rangoSuperior = floatval($input['rango_superior'] ?? -1);
    $bono          = floatval($input['bono'] ?? -1);

    if ($rangoInferior < 0 || $rangoSuperior < 0 || $bono < 0) {
        responderError('Los montos no pueden ser negativos.');
    }
    if ($rangoSuperior < $rangoInferior) {
        responderError('El rango superior no puede ser menor que el rango inferior.');
    }

    if ($id > 0) {
        $ok = $conecta->consulta("Update carobra_2014.espartanos_rangos_meta
                                    set rango_inferior=$rangoInferior, rango_superior=$rangoSuperior, bono=$bono
                                    where id=$id");
        if (!$ok) {
            responderError('No se pudo actualizar el rango: ' . mysqli_error($conecta->conexion));
        }
        echo json_encode(['status' => 'ok', 'id' => $id]);
        exit;
    }

    $ok = $conecta->consulta("Insert into carobra_2014.espartanos_rangos_meta (rango_inferior, rango_superior, bono)
                                values ($rangoInferior, $rangoSuperior, $bono)");
    if (!$ok) {
        responderError('No se pudo guardar el rango: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok', 'id' => $conecta->conexion->insert_id]);
    exit;
}

if ($action === 'eliminarRangoMeta') {

    requiereAdmin($esAdmin);

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        responderError('Falta el rango a eliminar.');
    }

    $ok = $conecta->consulta("Delete from carobra_2014.espartanos_rangos_meta where id=$id");
    if (!$ok) {
        responderError('No se pudo eliminar el rango: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Pago Incentivos / Pago Asegurado: dos checks por espartano,
   independientes del equipo (a diferencia de meta_individual, no
   hace falta que ya esté asignado a un equipo para marcarlos).
   Se guardan en espartanos_pagos, upsert por clave. Admin-only.
════════════════════════════════════════════════════════════ */

if ($action === 'guardarPago') {

    requiereAdmin($esAdmin);

    $clave = trim($input['clave'] ?? '');
    $campo = $input['campo'] ?? '';
    $valor = !empty($input['valor']) ? 1 : 0;

    if ($clave === '') {
        responderError('Falta la clave del espartano.');
    }
    if (!in_array($campo, ['pago_incentivos', 'pago_asegurado'], true)) {
        responderError('Campo inválido.');
    }

    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $claveSesionEsc = mysqli_real_escape_string($conecta->conexion, $claveSesion);

    /* $campo ya viene validado contra una whitelist fija arriba, así
       que es seguro concatenarlo directo al nombre de columna en el
       UPDATE ... SET (no es un valor de usuario libre, no aplica
       escaparlo como si fuera dato). */
    $query = "Insert into carobra_2014.espartanos_pagos (clave, $campo, actualizado_por)
                values ('$claveEsc', $valor, '$claveSesionEsc')
                on duplicate key update $campo=$valor, actualizado_por='$claveSesionEsc'";

    $ok = $conecta->consulta($query);
    if (!$ok) {
        responderError('No se pudo guardar: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

/* ════════════════════════════════════════════════════════════
   Faltas y retardos: qué día faltó (o llegó tarde) cada espartano
   y en qué semana cae esa incidencia. Se guardan en la misma tabla
   espartanos_faltas; la columna `retardo` dice qué es:
   retardo = 1 -> retardo, retardo = 0 -> falta.
   Las semanas válidas para elegir salen en vivo de la tabla
   `clawback` (mismo criterio que usa el resto del sistema para
   nombrar semanas), filtradas a partir de la semana 295.
   Un mismo espartano + mismo día solo puede tener un registro
   (UNIQUE en la tabla) -- volver a guardar ese día actualiza la
   semana / el tipo / quién lo registró en vez de duplicar.
════════════════════════════════════════════════════════════ */

if ($action === 'listarSemanasFaltas') {

    $query = "Select * from
                (Select distinct case when left(semana,1)='S' then concat(SUBSTR(semana, 2,3),' ',trim(right(semana,16)))
                            else concat(left(semana,3),' ',trim(right(semana,16)))
                           end as semana
                 from clawback) c
                where (left(semana,3) * 1) >= 295
                order by semana";

    $bd = $conecta->consulta($query);
    if (!$bd) {
        responderError('Error al consultar las semanas: ' . mysqli_error($conecta->conexion));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($bd)) {
        if ($row['semana'] === null || $row['semana'] === '') continue;
        $data[] = $row['semana'];
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

if ($action === 'listarFaltas') {

    $semana = trim($input['semana'] ?? '');
    $wSemana = '';
    if ($semana !== '') {
        $semanaEsc = mysqli_real_escape_string($conecta->conexion, $semana);
        $wSemana = " and f.semana = '$semanaEsc' ";
    }

    $query = "Select f.id, f.clave, u.nombre, f.dia_falta, f.semana, f.registrado_por, f.retardo
                from carobra_2014.espartanos_faltas f
                    left join carobra.usuarios u on (u.clave = f.clave)
                where 1=1
                $wSemana
                order by f.dia_falta desc, u.nombre";

    $bd = $conecta->consulta($query);
    if (!$bd) {
        responderError('Error al consultar las faltas y retardos: ' . mysqli_error($conecta->conexion));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($bd)) {
        $data[] = [
            'id'             => intval($row['id']),
            'clave'          => $row['clave'],
            'nombre'         => $row['nombre'] !== null ? utf8_encode($row['nombre']) : $row['clave'],
            'dia_falta'      => $row['dia_falta'],
            'semana'         => $row['semana'],
            'registrado_por' => $row['registrado_por'],
            'retardo'        => intval($row['retardo'] ?? 0) === 1,
        ];
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

if ($action === 'guardarFalta') {

    requiereAdmin($esAdmin);

    $clave    = trim($input['clave']     ?? '');
    $diaFalta = trim($input['dia_falta'] ?? ''); // yyyy-mm-dd, viene de <input type="date">
    $semana   = trim($input['semana']    ?? '');
    $retardo  = !empty($input['retardo']) ? 1 : 0;   // 1 = retardo, 0 = falta

    if ($clave === '') {
        responderError('Falta elegir al espartano.');
    }
    if ($diaFalta === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $diaFalta)) {
        responderError('Falta el día de la incidencia.');
    }
    if ($semana === '') {
        responderError('Falta elegir la semana.');
    }

    $claveEsc       = mysqli_real_escape_string($conecta->conexion, $clave);
    $diaFaltaEsc    = mysqli_real_escape_string($conecta->conexion, $diaFalta);
    $semanaEsc      = mysqli_real_escape_string($conecta->conexion, $semana);
    $claveSesionEsc = mysqli_real_escape_string($conecta->conexion, $claveSesion);

    $ok = $conecta->consulta(
        "Insert into carobra_2014.espartanos_faltas (clave, dia_falta, semana, registrado_por, retardo)
            values ('$claveEsc', '$diaFaltaEsc', '$semanaEsc', '$claveSesionEsc', $retardo)
            on duplicate key update semana='$semanaEsc', registrado_por='$claveSesionEsc', retardo=$retardo"
    );

    if (!$ok) {
        responderError('No se pudo registrar la incidencia: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'eliminarFalta') {

    requiereAdmin($esAdmin);

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        responderError('Falta el registro a eliminar.');
    }

    $ok = $conecta->consulta("Delete from carobra_2014.espartanos_faltas where id=$id");
    if (!$ok) {
        responderError('No se pudo eliminar el registro: ' . mysqli_error($conecta->conexion));
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
exit;