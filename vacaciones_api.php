<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    header('Content-Type: application/json');

    require './vendor/autoload.php';
    include("./funciones/encripta.php");

    $conecta = new conector();

    $input = json_decode(file_get_contents('php://input'), true);

    $action = $input['action'] ?? '';

    /* ════════════════════════════════════════════════════════════
       Calcula el estatus general a partir de estatus_jefe + estatus_rrhh
       - cualquiera = 2 (rechazado)  -> 2
       - ambos = 1 (aprobado)        -> 1
       - cualquier otro caso         -> 0 (pendiente)
    ════════════════════════════════════════════════════════════ */
    function calcularEstatusGeneral($estatusJefe, $estatusRrhh) {
        if ($estatusJefe == 2 || $estatusRrhh == 2) return 2;
        if ($estatusJefe == 1 && $estatusRrhh == 1) return 1;
        return 0;
    }

    /* ════════════════════════════════════════════════════════════
       Verifica que la clave dada tenga permiso de RRHH en
       vista_vacaciones (vista_RRHH = 1). Se reutiliza en todas las
       acciones exclusivas de RRHH para no confiar solo en el JS.
    ════════════════════════════════════════════════════════════ */
    function tienePermisoRRHH($conecta, $clave) {
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
        $rsPerm   = $conecta->consulta("SELECT vista_RRHH FROM vista_vacaciones WHERE clave='$claveEsc' LIMIT 1");
        $rowPerm  = $rsPerm ? mysqli_fetch_assoc($rsPerm) : null;
        return ($rowPerm && intval($rowPerm['vista_RRHH']) === 1);
    }

    if($action === 'getVacaciones'){
        $clave = $input['clave'] ?? '';

        $sql = "SELECT e.clave,
                    STR_TO_DATE(
                        COALESCE(NULLIF(e.fec_reingreso, ''), e.fec_ingreso),
                        '%d/%m/%Y'
                    ) AS fec_ingreso
                FROM carobra.empleados e
                WHERE e.clave = '$clave'
                LIMIT 1";

        $res = $conecta->consulta($sql);

        if (!$res) {
            echo json_encode(['status' => 'error', 'msg' => 'No encontrado']);
            exit;
        }

        $row = mysqli_fetch_assoc($res);

        // ── Nueva función, calcula todo por periodo ──────────────────
        $vac = calcularVacacionesConUsos($row['fec_ingreso'], $conecta, $clave);

        // ── Historial para la tabla del modal (incluye id para poder
        //    editar cada registro puntual desde RRHH) ─────────────────
        $sql_hist = "SELECT id, fecha_inicio, fecha_final, dias_tomados, periodo
                        FROM solicitudes_vacaciones
                        WHERE clave = '$clave' AND estatus = 1
                        AND YEAR(fecha_inicio) >= 2025
                        ORDER BY fecha_inicio DESC";

        $res_hist = $conecta->consulta($sql_hist);
        $historial = [];
        while ($row_h = mysqli_fetch_assoc($res_hist)) {
            $historial[] = $row_h;
        }

        echo json_encode([
            'status'    => 'ok',
            'vac'       => [
                'disponibles_real' => $vac['disponibles'],
                'proximos_real'    => $vac['proximos'],
                'caducados'        => $vac['caducados'],
                'usados'           => $vac['usados'],
                'total'            => $vac['total'],
                'detalle'          => $vac['detalle'],
            ],
            'historial' => $historial,
        ]);
        exit;
    }

    /* ════════════════════════════════════════════════════════════
       getSolicitudesSubordinado — para el tab "Mi Equipo": un líder
       consulta el resumen de vacaciones + TODAS las solicitudes
       (tomadas y postuladas, cualquier estatus) de un subordinado
       directo. Permiso: quien pregunta debe ser el jefe_inmediato de
       ese empleado, o tener permiso de RRHH (mismo criterio que el
       resto de las acciones RRHH de este archivo).
    ════════════════════════════════════════════════════════════ */
    if ($action === 'getSolicitudesSubordinado') {

        $clave      = trim($input['clave'] ?? '');
        $clave_jefe = trim($input['clave_jefe'] ?? '');

        if ($clave === '' || $clave_jefe === '') {
            echo json_encode(['status'=>'error','msg'=>'Datos incompletos']);
            exit;
        }

        $claveEsc     = mysqli_real_escape_string($conecta->conexion, $clave);
        $claveJefeEsc = mysqli_real_escape_string($conecta->conexion, $clave_jefe);

        $esRRHH = tienePermisoRRHH($conecta, $clave_jefe);

        if (!$esRRHH) {
            $rsJefe  = $conecta->consulta("SELECT jefe_inmediato FROM carobra.empleados WHERE clave='$claveEsc' LIMIT 1");
            $rowJefe = $rsJefe ? mysqli_fetch_assoc($rsJefe) : null;
            $esSuJefe = ($rowJefe && strtoupper(trim($rowJefe['jefe_inmediato'])) === strtoupper(trim($clave_jefe)));

            if (!$esSuJefe) {
                echo json_encode(['status'=>'error','msg'=>'No tienes permiso para ver este historial']);
                exit;
            }
        }

        $sqlEmp = "SELECT e.clave,
                        STR_TO_DATE(
                            COALESCE(NULLIF(e.fec_reingreso, ''), e.fec_ingreso),
                            '%d/%m/%Y'
                        ) AS fec_ingreso
                    FROM carobra.empleados e
                    WHERE e.clave = '$claveEsc'
                    LIMIT 1";

        $resEmp = $conecta->consulta($sqlEmp);
        $rowEmp = $resEmp ? mysqli_fetch_assoc($resEmp) : null;

        if (!$rowEmp) {
            echo json_encode(['status'=>'error','msg'=>'Empleado no encontrado']);
            exit;
        }

        $vac = calcularVacacionesConUsos($rowEmp['fec_ingreso'], $conecta, $clave);

        // A diferencia de getVacaciones (que solo trae lo YA aprobado
        // para armar el historial de "días tomados"), aquí traemos
        // TODAS las solicitudes sin filtrar por estatus -- el líder
        // quiere ver también lo pendiente/rechazado, no solo lo ya
        // confirmado.
        $sqlSol = "SELECT sv.fecha_captura, sv.fecha_inicio, sv.fecha_final, sv.dias_tomados,
                        sv.periodo, sv.estatus_jefe, sv.estatus_rrhh,
                        sv.comentario_jefe, sv.comentario_rrhh
                    FROM solicitudes_vacaciones sv
                    WHERE sv.clave = '$claveEsc'
                    AND YEAR(sv.fecha_inicio) >= 2025
                    ORDER BY sv.fecha_captura DESC";

        $resSol = $conecta->consulta($sqlSol);
        $solicitudes = [];
        while ($row_s = mysqli_fetch_assoc($resSol)) {
            $solicitudes[] = $row_s;
        }

        echo json_encode([
            'status' => 'ok',
            'vac'    => [
                'disponibles_real' => $vac['disponibles'],
                'proximos_real'    => $vac['proximos'],
                'caducados'        => $vac['caducados'],
                'usados'           => $vac['usados'],
            ],
            'solicitudes' => $solicitudes,
        ]);
        exit;
    }

    if($action === 'setVacaciones'){
        $clave  = $input['clave'] ?? '';
        $fechaInicio  = $input['fechaInicio'] ?? '';
        $fechaFinal  = $input['fechaFinal'] ?? '';
        $diasTomados  = $input['diasTomados'] ?? '';
        $periodo  = $input['periodo'] ?? '';
        $jefe_directo = $input['jefe_directo'] ?? '';
        $estatus = 0;

        //DATOS
        $empleadoData = getUsr($clave);
        $jefeData = getUsr($jefe_directo);

        //VALIDACIÓN
        if(!$empleadoData || !$jefeData){
            echo json_encode([
                'status'=>'error',
                'msg'=>'Error obteniendo usuario o jefe'
            ]);
            exit;
        }

        $clave_esc        = mysqli_real_escape_string($conecta->conexion, $clave);
        $fechaInicio_esc  = mysqli_real_escape_string($conecta->conexion, $fechaInicio);
        $fechaFinal_esc   = mysqli_real_escape_string($conecta->conexion, $fechaFinal);
        $diasTomados_esc  = intval($diasTomados);
        $periodo_esc      = mysqli_real_escape_string($conecta->conexion, $periodo);
        $jefe_directo_esc = mysqli_real_escape_string($conecta->conexion, $jefe_directo);

        //INSERT — pendiente en jefe Y en RRHH
        $query = "INSERT INTO solicitudes_vacaciones(
                    clave, fecha_inicio, fecha_final, dias_tomados, periodo, jefe_directo, fecha_captura,
                    estatus, estatus_jefe, estatus_rrhh
                )
                VALUES (
                    '$clave_esc', '$fechaInicio_esc', '$fechaFinal_esc', $diasTomados_esc, '$periodo_esc', '$jefe_directo_esc', NOW(),
                    $estatus, 0, 0
                )";

        $res = $conecta->consulta($query);

        //ID
        $id = mysqli_insert_id($conecta->conexion);

        $url = "https://sis-ca.com.mx:7863/afore/vacaciones_auth.php?clv=$clave&uuid=$id";


        $empleado = $empleadoData['nombre'];
        $nom_jefe = $jefeData['nombre'];
        $email_jefe = $jefeData['email'];

        //MAIL — el jefe directo recibe el correo; altas@carobra.com siempre en BCC (ya dentro de envia_correo)
        $mail = generarCorreoVacaciones(
            $fechaInicio,
            $fechaFinal,
            $diasTomados,
            $url,
            $empleado,
            $nom_jefe
        );

        envia_correo($mail['body'], "SISCA", $email_jefe, null, $mail['subject'], null);

        //RESPUESTA
        echo json_encode([
            'status'=>'ok',
            'mgs' => 'Solicitud de vacaciones enviada'
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       authVacaciones — resuelve la parte del JEFE
    ════════════════════════════════════════════════════════════ */
    if($action === 'authVacaciones'){
        $comentario  = $input['comentario'] ?? '';
        $id          = (int)($input['id'] ?? 0);
        $clave       = $input['clave'] ?? '';
        $estatus     = (int)($input['estatus'] ?? 0); // 1 = aprueba, 2 = rechaza
        $jefe_directo = $input['jefe_directo'] ?? '';

        if($id <= 0){
            echo json_encode(['status'=>'error','msg'=>'ID inválido']);
            exit;
        }

        $comentario   = mysqli_real_escape_string($conecta->conexion, $comentario);
        $clave        = mysqli_real_escape_string($conecta->conexion, $clave);
        $jefe_directo = mysqli_real_escape_string($conecta->conexion, $jefe_directo);

        // 1. Datos de la solicitud actual
        $queryDatos = "SELECT fecha_inicio, fecha_final, dias_tomados, estatus_rrhh
                    FROM solicitudes_vacaciones
                    WHERE id = $id LIMIT 1";

        $resDatos = $conecta->consulta($queryDatos);
        $datosSolicitud = mysqli_fetch_assoc($resDatos);

        if(!$datosSolicitud){
            echo json_encode(['status'=>'error','msg'=>'Solicitud no encontrada']);
            exit;
        }

        $fechaInicio    = $datosSolicitud['fecha_inicio'];
        $fechaFinal     = $datosSolicitud['fecha_final'];
        $diasTomados    = $datosSolicitud['dias_tomados'];
        $estatusRrhhAct = (int)$datosSolicitud['estatus_rrhh'];

        // 2. Datos de usuarios
        $empleadoData = getUsr($clave);
        $jefeData     = getUsr($jefe_directo);

        if(!$empleadoData || !$jefeData){
            echo json_encode(['status'=>'error','msg'=>'Error obteniendo usuario o jefe']);
            exit;
        }

        $empleado       = $empleadoData['nombre'];
        $nom_jefe       = $jefeData['nombre'];
        $email_empleado = $empleadoData['email'];

        // 3. UPDATE — solo la parte del jefe; recalcular estatus general
        $estatusGeneral = calcularEstatusGeneral($estatus, $estatusRrhhAct);

        $query = "UPDATE solicitudes_vacaciones
                SET comentario_jefe  = '$comentario',
                    fecha_resol_jefe = NOW(),
                    estatus_jefe     = $estatus,
                    estatus          = $estatusGeneral,
                    descripcion      = '$comentario',
                    fecha_resolucion = NOW()
                WHERE id = $id
                AND clave = '$clave'
                AND jefe_directo = '$jefe_directo'";

        $res = $conecta->consulta($query);

        if(!$res){
            echo json_encode(['status'=>'error','msg'=>'Error al actualizar']);
            exit;
        }

        // 4. Correo al empleado notificando la parte del jefe
        $url = "https://sis-ca.com.mx:7863/afore/vacaciones.php";

        $mail = generarCorreoRespuestaVacaciones(
            $fechaInicio, $fechaFinal, $diasTomados, $estatus,
            $empleado, $nom_jefe, $comentario, $url, 'jefe', $estatusGeneral
        );

        envia_correo($mail['body'], "SISCA", $email_empleado, null, $mail['subject'], null);

        // 5. Respuesta
        $msg = ($estatus == 1) ? 'Aprobado por tu parte (falta RRHH si no ha resuelto)' : 'Vacaciones rechazadas';
        if ($estatusGeneral == 1) $msg = 'Vacaciones aprobadas (ambas partes)';

        echo json_encode(['status'=>'ok','mgs' => $msg]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       authVacacionesRRHH — resuelve la parte de RRHH
    ════════════════════════════════════════════════════════════ */
    if($action === 'authVacacionesRRHH'){
        $comentario   = $input['comentario'] ?? '';
        $id           = (int)($input['id'] ?? 0);
        $clave        = $input['clave'] ?? '';
        $estatus      = (int)($input['estatus'] ?? 0); // 1 = aprueba, 2 = rechaza
        $clave_rrhh   = $input['clave_rrhh'] ?? '';

        if($id <= 0){
            echo json_encode(['status'=>'error','msg'=>'ID inválido']);
            exit;
        }

        // Verificar que quien resuelve sí tiene permiso RRHH
        $claveRrhhEsc = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        $comentario = mysqli_real_escape_string($conecta->conexion, $comentario);
        $clave      = mysqli_real_escape_string($conecta->conexion, $clave);

        // 1. Datos de la solicitud
        $queryDatos = "SELECT fecha_inicio, fecha_final, dias_tomados, estatus_jefe, jefe_directo
                    FROM solicitudes_vacaciones
                    WHERE id = $id LIMIT 1";

        $resDatos = $conecta->consulta($queryDatos);
        $datosSolicitud = mysqli_fetch_assoc($resDatos);

        if(!$datosSolicitud){
            echo json_encode(['status'=>'error','msg'=>'Solicitud no encontrada']);
            exit;
        }

        $fechaInicio    = $datosSolicitud['fecha_inicio'];
        $fechaFinal     = $datosSolicitud['fecha_final'];
        $diasTomados    = $datosSolicitud['dias_tomados'];
        $estatusJefeAct = (int)$datosSolicitud['estatus_jefe'];
        $jefe_directo   = $datosSolicitud['jefe_directo'];

        // 2. Datos de usuarios
        $empleadoData = getUsr($clave);
        $jefeData     = getUsr($jefe_directo);

        if(!$empleadoData){
            echo json_encode(['status'=>'error','msg'=>'Error obteniendo usuario']);
            exit;
        }

        $empleado       = $empleadoData['nombre'];
        $nom_jefe       = $jefeData['nombre'] ?? '';
        $email_empleado = $empleadoData['email'];

        // 3. UPDATE — solo la parte de RRHH; recalcular estatus general
        $estatusGeneral = calcularEstatusGeneral($estatusJefeAct, $estatus);

        $query = "UPDATE solicitudes_vacaciones
                SET comentario_rrhh  = '$comentario',
                    fecha_resol_rrhh = NOW(),
                    estatus_rrhh     = $estatus,
                    clave_rrhh       = '$claveRrhhEsc',
                    estatus          = $estatusGeneral
                WHERE id = $id
                AND clave = '$clave'";

        $res = $conecta->consulta($query);

        if(!$res){
            echo json_encode(['status'=>'error','msg'=>'Error al actualizar']);
            exit;
        }

        // 4. Correo al empleado notificando la parte de RRHH
        $url = "https://sis-ca.com.mx:7863/afore/vacaciones.php";

        $mail = generarCorreoRespuestaVacaciones(
            $fechaInicio, $fechaFinal, $diasTomados, $estatus,
            $empleado, $nom_jefe, $comentario, $url, 'rrhh', $estatusGeneral
        );

        envia_correo($mail['body'], "SISCA", $email_empleado, null, $mail['subject'], null);

        // 5. Respuesta
        $msg = ($estatus == 1) ? 'Aprobado por RRHH (falta jefe si no ha resuelto)' : 'Vacaciones rechazadas';
        if ($estatusGeneral == 1) $msg = 'Vacaciones aprobadas (ambas partes)';

        echo json_encode(['status'=>'ok','mgs' => $msg]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       getPendientesRRHH — lista las solicitudes pendientes de
       resolución por RRHH (para pintar el tab correspondiente)
    ════════════════════════════════════════════════════════════ */
    if($action === 'getPendientesRRHH'){
        $clave_rrhh = $input['clave_rrhh'] ?? '';

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'Sin permiso']);
            exit;
        }

        $query = "SELECT sv.id, sv.clave, u.nombre, sv.fecha_inicio, sv.fecha_final, sv.dias_tomados,
                         sv.periodo, sv.estatus_jefe, sv.estatus_rrhh, sv.comentario_jefe, sv.comentario_rrhh,
                         sv.jefe_directo, sv.masiva
                  FROM solicitudes_vacaciones sv
                  LEFT JOIN usuarios u ON sv.clave = u.clave
                  WHERE sv.estatus_rrhh = 0 AND sv.masiva = 0
                  ORDER BY sv.fecha_captura ASC";

        $res = $conecta->consulta($query);
        $pendientes = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $pendientes[] = $row;
        }

        echo json_encode(['status'=>'ok','pendientes'=>$pendientes]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       getReporteVacaciones — reporte general (RRHH) de TODAS las
       solicitudes de vacaciones, con filtro opcional por rango de
       fechas (por TRASLAPE con desde/hasta, no por contención total)
       y por nombre/clave. Sin filtros, trae las más recientes (con un
       LIMIT de seguridad para no tirar miles de filas de golpe).
    ════════════════════════════════════════════════════════════ */
    if ($action === 'getReporteVacaciones') {

        $clave_rrhh = $input['clave_rrhh'] ?? '';

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        $fechaDesde = trim($input['fechaDesde'] ?? '');
        $fechaHasta = trim($input['fechaHasta'] ?? '');
        $nombre     = trim($input['nombre'] ?? '');

        $condiciones = [];

        // Filtro por rango: se busca TRASLAPE entre el rango del filtro
        // (desde/hasta) y el rango de la solicitud (fecha_inicio/fecha_final),
        // no que la solicitud quepa completa adentro -- si no, una solicitud
        // del 8 al 22 no aparecería al filtrar 7-11 ni 21-25, a pesar de
        // caer dentro de esos rangos. Traslape clásico: la solicitud empezó
        // antes de que termine el filtro (fecha_inicio <= hasta) Y terminó
        // después de que empiece el filtro (fecha_final >= desde).
        if ($fechaDesde !== '') {
            $fechaDesdeEsc = mysqli_real_escape_string($conecta->conexion, $fechaDesde);
            $condiciones[] = "sv.fecha_final >= '$fechaDesdeEsc'";
        }
        if ($fechaHasta !== '') {
            $fechaHastaEsc = mysqli_real_escape_string($conecta->conexion, $fechaHasta);
            $condiciones[] = "sv.fecha_inicio <= '$fechaHastaEsc'";
        }
        if ($nombre !== '') {
            $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre));
            $condiciones[] = "(u.nombre LIKE '%$nombreEsc%' OR sv.clave LIKE '%$nombreEsc%')";
        }

        $where = count($condiciones) ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

        // Límite de seguridad: sin filtros esto podría ser un histórico
        // enorme; se traen las 3000 más recientes y se avisa en el
        // frontend para que afinen el filtro si necesitan ver más.
        $limite = 3000;

        $query = "SELECT sv.clave, u.nombre, sv.fecha_inicio, sv.fecha_final, sv.dias_tomados,
                         sv.periodo, sv.fecha_captura, sv.estatus
                  FROM solicitudes_vacaciones sv
                  JOIN usuarios u ON sv.clave = u.clave
                  $where
                  ORDER BY sv.fecha_captura DESC
                  LIMIT $limite";

        $res = $conecta->consulta($query);

        if (!$res) {
            echo json_encode(['status'=>'error','msg'=>'Error al consultar: ' . mysqli_error($conecta->conexion)]);
            exit;
        }

        $solicitudes = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $solicitudes[] = $row;
        }

        echo json_encode([
            'status'      => 'ok',
            'solicitudes' => $solicitudes,
            'limitado'    => (count($solicitudes) >= $limite),
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       vacacionesMasivas — RRHH aplica un rango de fechas a TODOS
       los empleados activos, ya aprobado de ambos lados.
    ════════════════════════════════════════════════════════════ */
    if($action === 'vacacionesMasivas'){
        $fechaInicio = $input['fechaInicio'] ?? '';
        $fechaFinal  = $input['fechaFinal']  ?? '';
        $diasTomados = intval($input['diasTomados'] ?? 0);
        $clave_rrhh  = $input['clave_rrhh'] ?? '';

        if (empty($fechaInicio) || empty($fechaFinal) || $diasTomados <= 0) {
            echo json_encode(['status'=>'error','msg'=>'Datos incompletos']);
            exit;
        }

        // Verificar permiso RRHH
        $claveRrhhEsc = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        $fechaInicioEsc = mysqli_real_escape_string($conecta->conexion, $fechaInicio);
        $fechaFinalEsc  = mysqli_real_escape_string($conecta->conexion, $fechaFinal);
        $periodo        = date('Y', strtotime($fechaInicio));

        // Todos los empleados activos
        $rsEmp = $conecta->consulta(
            "SELECT e.clave, e.jefe_inmediato, u.nombre, u.email
             FROM carobra.empleados e
             INNER JOIN usuarios u ON e.clave = u.clave
             WHERE u.activo = 1"
        );

        $totalInsertados = 0;
        $errores = [];

        while ($emp = mysqli_fetch_assoc($rsEmp)) {
            $claveEmp = mysqli_real_escape_string($conecta->conexion, $emp['clave']);
            $jefeEmp  = mysqli_real_escape_string($conecta->conexion, $emp['jefe_inmediato'] ?? '');

            $query = "INSERT INTO solicitudes_vacaciones(
                        clave, fecha_inicio, fecha_final, dias_tomados, periodo, jefe_directo, fecha_captura,
                        estatus, estatus_jefe, estatus_rrhh, comentario_jefe, comentario_rrhh,
                        fecha_resol_jefe, fecha_resol_rrhh, clave_rrhh, masiva
                    )
                    VALUES (
                        '$claveEmp', '$fechaInicioEsc', '$fechaFinalEsc', $diasTomados, '$periodo', '$jefeEmp', NOW(),
                        1, 1, 1, 'Vacaciones masivas (RRHH)', 'Vacaciones masivas (RRHH)',
                        NOW(), NOW(), '$claveRrhhEsc', 1
                    )";

            $ok = $conecta->consulta($query);

            if ($ok) {
                $totalInsertados++;

                // Notificar al empleado
                if (!empty($emp['email'])) {
                    $mail = generarCorreoVacacionesMasivas($fechaInicio, $fechaFinal, $diasTomados, $emp['nombre']);
                    envia_correo($mail['body'], "SISCA", $emp['email'], null, $mail['subject'], null);
                }
            } else {
                $errores[] = $emp['clave'];
            }
        }

        echo json_encode([
            'status' => 'ok',
            'mgs'    => "Vacaciones masivas aplicadas a $totalInsertados empleados.",
            'total'  => $totalInsertados,
            'errores'=> $errores
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       insertarVacacionesDirecto — RRHH da de alta, YA APROBADAS,
       las vacaciones de UN empleado puntual, sin pasar por el flujo
       de solicitud/aprobación de jefe+RRHH. Uso típico: el empleado
       ya tomó los días en la práctica y solo falta dejarlo
       registrado en el sistema.
    ════════════════════════════════════════════════════════════ */
    if ($action === 'insertarVacacionesDirecto') {

        $clave       = $input['clave']       ?? '';
        $fechaInicio = $input['fechaInicio'] ?? '';
        $fechaFinal  = $input['fechaFinal']  ?? '';
        $diasTomados = intval($input['diasTomados'] ?? 0);
        $clave_rrhh  = $input['clave_rrhh']  ?? '';

        if (empty($clave) || empty($fechaInicio) || empty($fechaFinal) || $diasTomados <= 0) {
            echo json_encode(['status'=>'error','msg'=>'Datos incompletos']);
            exit;
        }

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        // Datos del empleado (nombre/email para notificar, y su jefe
        // inmediato solo para dejar el registro completo/consistente
        // con el resto de solicitudes, aunque aquí no se le pregunta nada).
        $empleadoData = getUsr($clave);
        if (!$empleadoData) {
            echo json_encode(['status'=>'error','msg'=>'Empleado no encontrado']);
            exit;
        }

        $claveEsc       = mysqli_real_escape_string($conecta->conexion, $clave);
        $claveRrhhEsc   = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);
        $fechaInicioEsc = mysqli_real_escape_string($conecta->conexion, $fechaInicio);
        $fechaFinalEsc  = mysqli_real_escape_string($conecta->conexion, $fechaFinal);
        $periodo        = date('Y', strtotime($fechaInicio));

        $rsEmp  = $conecta->consulta("SELECT jefe_inmediato FROM carobra.empleados WHERE clave='$claveEsc' LIMIT 1");
        $rowEmp = $rsEmp ? mysqli_fetch_assoc($rsEmp) : null;
        $jefeEmpEsc = mysqli_real_escape_string($conecta->conexion, $rowEmp['jefe_inmediato'] ?? '');

        $comentarioAuto = 'Registro directo por RRHH';

        $query = "INSERT INTO solicitudes_vacaciones(
                    clave, fecha_inicio, fecha_final, dias_tomados, periodo, jefe_directo, fecha_captura,
                    estatus, estatus_jefe, estatus_rrhh, comentario_jefe, comentario_rrhh,
                    fecha_resol_jefe, fecha_resol_rrhh, clave_rrhh, masiva
                )
                VALUES (
                    '$claveEsc', '$fechaInicioEsc', '$fechaFinalEsc', $diasTomados, '$periodo', '$jefeEmpEsc', NOW(),
                    1, 1, 1, '$comentarioAuto', '$comentarioAuto',
                    NOW(), NOW(), '$claveRrhhEsc', 0
                )";

        $res = $conecta->consulta($query);

        if (!$res) {
            echo json_encode(['status'=>'error','msg'=>'Error al registrar: ' . mysqli_error($conecta->conexion)]);
            exit;
        }

        // Registro directo por RRHH: no se manda correo (a diferencia del
        // flujo normal de solicitud/aprobación, aquí RRHH ya está dejando
        // constancia de algo que el empleado sabe que ya tomó).

        echo json_encode([
            'status' => 'ok',
            'mgs'    => 'Vacaciones registradas directamente para ' . $empleadoData['nombre'] . '.'
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       editarVacacionDirecto — RRHH corrige fechas/días de una
       solicitud YA registrada (por ejemplo, el sistema tenía 4 días
       pero en realidad fueron 5). Solo toca fecha_inicio, fecha_final,
       dias_tomados y el periodo derivado; no cambia el estatus.
    ════════════════════════════════════════════════════════════ */
    if ($action === 'editarVacacionDirecto') {

        $id          = (int)($input['id'] ?? 0);
        $fechaInicio = $input['fechaInicio'] ?? '';
        $fechaFinal  = $input['fechaFinal']  ?? '';
        $diasTomados = intval($input['diasTomados'] ?? 0);
        $clave_rrhh  = $input['clave_rrhh']  ?? '';

        if ($id <= 0 || empty($fechaInicio) || empty($fechaFinal) || $diasTomados <= 0) {
            echo json_encode(['status'=>'error','msg'=>'Datos incompletos']);
            exit;
        }

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        // Solicitud existente (para poder notificar al empleado dueño)
        $rsSol  = $conecta->consulta("SELECT clave FROM solicitudes_vacaciones WHERE id=$id LIMIT 1");
        $rowSol = $rsSol ? mysqli_fetch_assoc($rsSol) : null;

        if (!$rowSol) {
            echo json_encode(['status'=>'error','msg'=>'Solicitud no encontrada']);
            exit;
        }

        $empleadoData = getUsr($rowSol['clave']);

        $claveRrhhEsc   = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);
        $fechaInicioEsc = mysqli_real_escape_string($conecta->conexion, $fechaInicio);
        $fechaFinalEsc  = mysqli_real_escape_string($conecta->conexion, $fechaFinal);
        $periodo        = date('Y', strtotime($fechaInicio));
        $notaAjuste     = 'Ajustado por RRHH el ' . date('d/m/Y H:i');

        $query = "UPDATE solicitudes_vacaciones SET
                    fecha_inicio    = '$fechaInicioEsc',
                    fecha_final     = '$fechaFinalEsc',
                    dias_tomados    = $diasTomados,
                    periodo         = '$periodo',
                    comentario_rrhh = '$notaAjuste',
                    clave_rrhh      = '$claveRrhhEsc'
                  WHERE id = $id";

        $res = $conecta->consulta($query);

        if (!$res) {
            echo json_encode(['status'=>'error','msg'=>'Error al actualizar: ' . mysqli_error($conecta->conexion)]);
            exit;
        }

        // Ajuste/edición directa por RRHH: no se manda correo.

        echo json_encode([
            'status' => 'ok',
            'mgs'    => 'Solicitud actualizada correctamente.'
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       eliminarVacacionDirecto — RRHH borra por completo un registro
       de vacaciones (por ejemplo, se capturó por error). Es
       definitivo: no se marca como rechazado, se elimina la fila.
    ════════════════════════════════════════════════════════════ */
    if ($action === 'eliminarVacacionDirecto') {

        $id         = (int)($input['id'] ?? 0);
        $clave_rrhh = $input['clave_rrhh'] ?? '';

        if ($id <= 0) {
            echo json_encode(['status'=>'error','msg'=>'Registro inválido']);
            exit;
        }

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        // Datos de la solicitud (para poder notificar al empleado dueño
        // antes de que el registro deje de existir).
        $rsSol  = $conecta->consulta("SELECT clave, fecha_inicio, fecha_final, dias_tomados FROM solicitudes_vacaciones WHERE id=$id LIMIT 1");
        $rowSol = $rsSol ? mysqli_fetch_assoc($rsSol) : null;

        if (!$rowSol) {
            echo json_encode(['status'=>'error','msg'=>'Solicitud no encontrada']);
            exit;
        }

        $empleadoData = getUsr($rowSol['clave']);

        $res = $conecta->consulta("DELETE FROM solicitudes_vacaciones WHERE id=$id");

        if (!$res) {
            echo json_encode(['status'=>'error','msg'=>'Error al eliminar: ' . mysqli_error($conecta->conexion)]);
            exit;
        }

        // Notificar al empleado dueño del registro eliminado, si tiene correo
        if ($empleadoData && !empty($empleadoData['email'])) {
            $mail = generarCorreoVacacionesEliminadas($rowSol['fecha_inicio'], $rowSol['fecha_final'], $rowSol['dias_tomados'], $empleadoData['nombre']);
            envia_correo($mail['body'], "SISCA", $empleadoData['email'], null, $mail['subject'], null);
        }

        echo json_encode([
            'status' => 'ok',
            'mgs'    => 'Registro de vacaciones eliminado correctamente.'
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       aprobarTodasPendientesRRHH — limpieza de acumulado: aprueba
       de golpe todas las solicitudes que llevan estatus_rrhh = 0
       (las que se generaron antes de tener doble aprobación, o
       cualquier rezago). De ahí en adelante solo entran solicitudes
       nuevas a la lista de pendientes.
    ════════════════════════════════════════════════════════════ */
    if ($action === 'aprobarTodasPendientesRRHH') {

        $clave_rrhh = $input['clave_rrhh'] ?? '';

        $claveRrhhEsc = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        // Traer las pendientes para poder recalcular su estatus general
        $rsPend = $conecta->consulta(
            "SELECT id, clave, estatus_jefe FROM solicitudes_vacaciones
             WHERE estatus_rrhh = 0 AND masiva = 0"
        );

        $total = 0;
        $comentarioAuto = 'Aprobación masiva inicial';

        while ($row = mysqli_fetch_assoc($rsPend)) {
            $estatusGeneral = calcularEstatusGeneral((int)$row['estatus_jefe'], 1);

            $conecta->consulta(
                "UPDATE solicitudes_vacaciones SET
                    estatus_rrhh     = 1,
                    comentario_rrhh  = '$comentarioAuto',
                    fecha_resol_rrhh = NOW(),
                    clave_rrhh       = '$claveRrhhEsc',
                    estatus          = $estatusGeneral
                 WHERE id = {$row['id']}"
            );

            $total++;
        }

        echo json_encode([
            'status' => 'ok',
            'mgs'    => "Se aprobaron $total solicitudes pendientes de RRHH.",
            'total'  => $total
        ]);
        exit;
    }


    /* ════════════════════════════════════════════════════════════
       aprobarTodasSaltandoJefe — igual que aprobarTodasPendientesRRHH,
       pero además fuerza estatus_jefe = 1 (se "salta"/omite la
       aprobación del jefe directo) cuando este todavía no ha resuelto,
       dejando el estatus general en aprobado (1) directamente.
       Por seguridad, si el jefe directo YA rechazó explícitamente su
       parte (estatus_jefe = 2), esa solicitud NO se toca: esta acción
       es para saltarse al jefe cuando no ha respondido, no para
       revertir un rechazo ya dado.
    ════════════════════════════════════════════════════════════ */
    if ($action === 'aprobarTodasSaltandoJefe') {

        $clave_rrhh = $input['clave_rrhh'] ?? '';

        if (!tienePermisoRRHH($conecta, $clave_rrhh)) {
            echo json_encode(['status'=>'error','msg'=>'No tienes permiso de RRHH para esta acción']);
            exit;
        }

        $claveRrhhEsc = mysqli_real_escape_string($conecta->conexion, $clave_rrhh);

        $rsPend = $conecta->consulta(
            "SELECT id, clave, estatus_jefe FROM solicitudes_vacaciones
             WHERE estatus_rrhh = 0 AND masiva = 0"
        );

        $comentarioAutoRrhh = 'Aprobación masiva RRHH (jefe directo omitido)';
        $comentarioAutoJefe = 'Aprobado automáticamente por RRHH (se omitió al jefe directo)';

        $totalAprobadas = 0;
        $totalOmitidas   = 0;

        while ($row = mysqli_fetch_assoc($rsPend)) {

            $estatusJefeAct = (int)($row['estatus_jefe'] ?? 0);

            if ($estatusJefeAct === 2) {
                // El jefe ya rechazó su parte: no se revierte con esta acción.
                $totalOmitidas++;
                continue;
            }

            if ($estatusJefeAct === 1) {
                // El jefe ya había aprobado su parte; solo falta RRHH.
                $conecta->consulta(
                    "UPDATE solicitudes_vacaciones SET
                        estatus_rrhh     = 1,
                        comentario_rrhh  = '$comentarioAutoRrhh',
                        fecha_resol_rrhh = NOW(),
                        clave_rrhh       = '$claveRrhhEsc',
                        estatus          = 1
                     WHERE id = {$row['id']}"
                );
            } else {
                // El jefe no ha resuelto: se aprueba también en su nombre.
                $conecta->consulta(
                    "UPDATE solicitudes_vacaciones SET
                        estatus_rrhh     = 1,
                        comentario_rrhh  = '$comentarioAutoRrhh',
                        fecha_resol_rrhh = NOW(),
                        clave_rrhh       = '$claveRrhhEsc',
                        estatus_jefe     = 1,
                        comentario_jefe  = '$comentarioAutoJefe',
                        fecha_resol_jefe = NOW(),
                        estatus          = 1
                     WHERE id = {$row['id']}"
                );
            }

            $totalAprobadas++;
        }

        $msg = "Se aprobaron $totalAprobadas solicitudes (RRHH y jefe directo, cuando aplicaba).";
        if ($totalOmitidas > 0) {
            $msg .= " $totalOmitidas quedaron sin tocar porque el jefe directo ya las había rechazado.";
        }

        echo json_encode([
            'status'    => 'ok',
            'mgs'       => $msg,
            'total'     => $totalAprobadas,
            'omitidas'  => $totalOmitidas
        ]);
        exit;
    }


    if ($action === 'login') {

        $usr = trim($input['usr'] ?? '');
        $pwd = trim($input['pass'] ?? '');

        if (empty($usr) || empty($pwd)) {
            echo json_encode(['status' => 'error', 'msg' => 'Completa los campos']);
            exit;
        }

        $usr_clean = mysqli_real_escape_string($conecta->conexion, utf8_encode($usr));

        $query = "SELECT nombre FROM usuarios
                WHERE clave = '$usr_clean'
                AND SHA1(contrasena) = SHA1('$pwd')
                AND activo = 1
                LIMIT 1";

        $bd = $conecta->consulta($query);

        if ($bd && $bd->num_rows > 0) {
            $row = $bd->fetch_assoc();

            // ✅ SIN session_start() aquí, ya está arriba
            $_SESSION["nombre"]       = $row['nombre'];
            $_SESSION["ultimoAcceso"] = date("Y-n-j H:i:s");
            $_SESSION["autentifica"]  = "p";

            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Usuario o contraseña incorrectos']);
        }

        exit;
    }
    function getUsr($clave){
        global $conecta;

        $clave = mysqli_real_escape_string($conecta->conexion, $clave);

        $query = "SELECT clave, nombre, email
                    FROM usuarios
                WHERE clave = '$clave'
                LIMIT 1";

        $res = $conecta->consulta($query);

        if(!$res){
            return null;
        }

        return mysqli_fetch_assoc($res);
    }

    function diasPorAnio($anios) {
        if ($anios == 1) return 12;
        if ($anios == 2) return 14;
        if ($anios == 3) return 16;
        if ($anios == 4) return 18;
        if ($anios == 5) return 20;
        if ($anios >= 6 && $anios <= 10) return 22;
        if ($anios >= 11 && $anios <= 15) return 24;
        if ($anios >= 16 && $anios <= 20) return 26;
        if ($anios >= 21 && $anios <= 25) return 28;
        if ($anios >= 26) return 30;
        return 0;
    }

    function calcularVacacionesDetalle($fechaIngreso) {

        if (empty($fechaIngreso) || $fechaIngreso == '0000-00-00') {
            return [
                'total' => 0,
                'disponibles' => 0,
                'proximos' => 0,
                'caducados' => 0,
                'detalle' => []
            ];
        }

        $hoy = new DateTime();

        // FIX AQUÍ
        if (strpos($fechaIngreso, '/') !== false) {
            $ingreso = DateTime::createFromFormat('d/m/Y', $fechaIngreso);
        } else {
            $ingreso = new DateTime($fechaIngreso);
        }

        // VALIDACIÓN EXTRA
        if (!$ingreso) {
            return [
                'total' => 0,
                'disponibles' => 0,
                'proximos' => 0,
                'caducados' => 0,
                'detalle' => []
            ];
        }

        $anios = $ingreso->diff($hoy)->y;

        $diasDisponibles = 0;
        $diasProximosVencer = 0;
        $diasCaducados = 0;

        $detalle = [];

        for ($i = 1; $i <= $anios; $i++) {

            $fechaAniversario = clone $ingreso;
            $fechaAniversario->modify("+$i year");

            $fechaVencimiento = clone $fechaAniversario;
            $fechaVencimiento->modify("+18 months");

            $dias = diasPorAnio($i);

            if ($fechaVencimiento < $hoy) {

                $diasCaducados += $dias;

                $detalle[] = [
                    'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
                    'vence' => $fechaVencimiento->format('d/m/Y'),
                    'dias' => $dias,
                    'tipo' => 'caducado'
                ];

                continue;
            }

            $tresMeses = clone $hoy;
            $tresMeses->modify("+3 months");

            if ($fechaVencimiento <= $tresMeses) {

                $diasProximosVencer += $dias;

                $detalle[] = [
                    'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
                    'vence' => $fechaVencimiento->format('d/m/Y'),
                    'dias' => $dias,
                    'tipo' => 'proximo'
                ];

            } else {

                $diasDisponibles += $dias;

                $detalle[] = [
                    'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
                    'vence' => $fechaVencimiento->format('d/m/Y'),
                    'dias' => $dias,
                    'tipo' => 'vigente'
                ];
            }
        }

        return [
            'total' => $diasDisponibles + $diasProximosVencer,
            'disponibles' => $diasDisponibles,
            'proximos' => $diasProximosVencer,
            'caducados' => $diasCaducados,
            'detalle' => $detalle
        ];
    }


    function generarFechas($inicio, $fin){

        // Festivos fijos México
        $festivos = [
            '01-01', // Año Nuevo
            '05-01', // Día del Trabajo
            '09-16', // Independencia
            '12-25', // Navidad
        ];

        $fechas = [];
        $current = new DateTime($inicio);
        $end = new DateTime($fin);

        while ($current <= $end) {

            $diaSemana = (int)$current->format('N'); // 1=Lun, 7=Dom
            $mmdd      = $current->format('m-d');

            if ($diaSemana < 6 && !in_array($mmdd, $festivos)) {
                $fechas[] = $current->format('d/m/Y');
            }

            $current->modify('+1 day');
        }

        return $fechas;
    }

    function envia_correo($body, $from, $cliente_email, $asesor_email=null, $subjet, $filename)
    {
        include_once("./PHPMailer/class.phpmailer.php");
        include_once("./PHPMailer/class.smtp.php");

        try {
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth = true;
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPSecure = 'tls';
            $mail->Username = "administrador.sisca@carobra.com";
            $mail->Password = "tttkscwhekfhfiqx";
            $mail->Port = 587;

            $mail->SetFrom('administrador.sisca@carobra.com', 'Administrador SISCA');

            // destinatario principal
            $mail->AddAddress($cliente_email);
            // copia opcional
            if (!empty(trim($asesor_email))) {
                $mail->AddBCC($asesor_email);
            }

            // COPIA FIJA SIEMPRE
            $mail->AddBCC('altas@carobra.com');

            $mail->CharSet = 'UTF-8';

            $mail->IsHTML(true);
            $mail->Subject = $subjet;
            $mail->Body = $body;

            if (!empty($filename) && file_exists($filename)) {
                $mail->AddAttachment($filename);
            }

            if (!$mail->Send()) {
                throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
            }

            return true;

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    function generarCorreoVacaciones($fechaInicio, $fechaFinal, $diasTomados, $url, $nombre_colaborador, $nombre_jefe){


        // generar fechas
        $fechas = generarFechas($fechaInicio, $fechaFinal);

        // lista HTML
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        // asunto
        $subject = "Solicitud de vacaciones – $nombre_colaborador";

        // cuerpo
        $body = "
            <html>
            <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_jefe</strong>,</p>

            <p>
            Te informamos que el colaborador <strong>$nombre_colaborador</strong>
            ha solicitado <strong>$diasTomados días</strong> de vacaciones a través del sistema <strong>SISCA</strong>.
            </p>

            <p><strong>Desglose de los días solicitados:</strong></p>

            <ul>
                $lista_fechas
            </ul>

            <p>
            Esta solicitud requiere tu aprobación <strong>y</strong> la de Recursos Humanos para
            quedar confirmada. Por favor, revisa esta solicitud y registra tu autorización o
            rechazo directamente en SISCA:
            </p>

            <p>
            <a href='$url' style='background:#2563eb; color:#fff; padding:10px 15px; border-radius:5px; text-decoration:none;'>
                Revisar solicitud
            </a>
            </p>

            <br>

            <p>Quedamos atentos a cualquier duda.</p>

            <p>Saludos cordiales,<br>
            <strong>Sistema SISCA</strong></p>

            </body>
            </html>
        ";

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       Correo de respuesta — ahora distingue quién resolvió
       (jefe o RRHH) y si falta la otra parte o ya quedó completo.
    ════════════════════════════════════════════════════════════ */
    function generarCorreoRespuestaVacaciones(
        $fechaInicio,
        $fechaFinal,
        $diasTomados,
        $estatus,            // 1 = aprobado por esa parte, 2 = rechazado por esa parte
        $nombre_colaborador,
        $nombre_resuelve,    // nombre del jefe o de quien de RRHH resolvió
        $comentario = '',
        $url = '',
        $quien = 'jefe',     // 'jefe' | 'rrhh'
        $estatusGeneral = 0  // 0=pendiente, 1=aprobado total, 2=rechazado total
    ){

        $origen = ($quien === 'rrhh') ? 'Recursos Humanos' : 'tu líder';

        // Para la línea principal del correo ya no queremos la etiqueta
        // "Recursos Humanos" / "tu líder" -- solo "aprobada/rechazada por
        // [nombre]". Si por algún motivo no llega el nombre, se cae de
        // regreso a $origen para no dejar la frase coja.
        $porQuien = !empty($nombre_resuelve) ? $nombre_resuelve : $origen;

        // texto de estatus de ESTA parte
        $estatusTxt = ($estatus == 1) ? 'APROBADA' : 'RECHAZADA';
        $color = ($estatus == 1) ? '#16a34a' : '#dc2626';

        // Mensaje según si ya quedó completo o falta la otra parte
        if ($estatusGeneral == 2) {
            $resumenGeneral = "<p style='color:#dc2626;'><strong>Tu solicitud fue RECHAZADA.</strong> No se requieren más aprobaciones.</p>";
        } elseif ($estatusGeneral == 1) {
            $resumenGeneral = "<p style='color:#16a34a;'><strong>¡Tu solicitud quedó completamente APROBADA!</strong> Ya tienes el visto bueno de tu líder y de Recursos Humanos.</p>";
        } else {
            $resumenGeneral = "<p style='color:#d97706;'><strong>Aún falta la aprobación de la otra parte</strong> (líder o Recursos Humanos) para que tu solicitud quede confirmada.</p>";
        }

        $fechas = generarFechas($fechaInicio, $fechaFinal);
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        $subject = "Actualización de tu solicitud de vacaciones";

        $bloqueComentario = '';
        if (!empty(trim($comentario))) {
            $bloqueComentario = "
                <p><strong>Comentario de $origen:</strong></p>
                <div style='background:#f8fafc;border-left:4px solid $color;padding:10px;border-radius:6px;'>
                    $comentario
                </div>
            ";
        }

        $bloqueBoton = '';
        if (!empty($url)) {
            $bloqueBoton = "
                <p>
                    <a href='$url' style='background:#2563eb;color:#fff;padding:10px 15px;border-radius:6px;text-decoration:none;'>
                        Ver en SISCA
                    </a>
                </p>
            ";
        }

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_colaborador</strong>,</p>

            <p>
                Tu solicitud de vacaciones ingresada en <strong>SISCA</strong> ha sido
                <strong style='color:$color;'>$estatusTxt</strong> por <strong>$porQuien</strong>.
            </p>

            $resumenGeneral

            <p><strong>Detalles de tu solicitud:</strong></p>

            <p><strong>Días solicitados:</strong> $diasTomados</p>

            <p><strong>Desglose de fechas:</strong></p>
            <ul>
                $lista_fechas
            </ul>

            $bloqueComentario

            $bloqueBoton

            <p>
                En caso de haber sido rechazada, te invitamos a revisar los comentarios
                y, si es necesario, realizar una nueva solicitud en el sistema.
            </p>

            <p>
                Para cualquier duda o aclaración, puedes contactar directamente a tu líder
                o al área de Recursos Humanos.
            </p>

            <br>

            <p>Saludos,<br>
            <strong>Sistema SISCA</strong></p>

        </body>
        </html>
        ";

        return [
            'subject' => $subject,
            'body'    => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       Correo para vacaciones masivas (ya aprobadas de ambos lados)
    ════════════════════════════════════════════════════════════ */
    function generarCorreoVacacionesMasivas($fechaInicio, $fechaFinal, $diasTomados, $nombre_colaborador){

        $fechas = generarFechas($fechaInicio, $fechaFinal);
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        $subject = "Periodo de vacaciones asignado por la empresa";

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_colaborador</strong>,</p>

            <p>
                Recursos Humanos ha registrado un periodo de <strong>vacaciones generales</strong>
                para todo el personal en el sistema <strong>SISCA</strong>. Este periodo ya cuenta
                con aprobación completa, no requiere ninguna acción de tu parte.
            </p>

            <p><strong>Días asignados:</strong> $diasTomados</p>

            <p><strong>Desglose de fechas:</strong></p>
            <ul>
                $lista_fechas
            </ul>

            <br>

            <p>Saludos,<br>
            <strong>Sistema SISCA</strong></p>

        </body>
        </html>
        ";

        return [
            'subject' => $subject,
            'body'    => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       Correo para registro DIRECTO de un solo empleado (RRHH marcó
       "este empleado tomó estos días", ya aprobado de ambos lados).
    ════════════════════════════════════════════════════════════ */
    function generarCorreoVacacionesDirecto($fechaInicio, $fechaFinal, $diasTomados, $nombre_colaborador){

        $fechas = generarFechas($fechaInicio, $fechaFinal);
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        $subject = "Vacaciones registradas en SISCA";

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_colaborador</strong>,</p>

            <p>
                Recursos Humanos registró directamente en <strong>SISCA</strong> un periodo de
                vacaciones a tu nombre. Este registro ya cuenta con aprobación completa,
                no requiere ninguna acción de tu parte.
            </p>

            <p><strong>Días registrados:</strong> $diasTomados</p>

            <p><strong>Desglose de fechas:</strong></p>
            <ul>
                $lista_fechas
            </ul>

            <p>
                Si algo de esta información no es correcta, contacta directamente al área
                de Recursos Humanos para corregirlo.
            </p>

            <br>

            <p>Saludos,<br>
            <strong>Sistema SISCA</strong></p>

        </body>
        </html>
        ";

        return [
            'subject' => $subject,
            'body'    => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       Correo cuando RRHH EDITA una solicitud ya registrada (cambia
       fechas y/o número de días de un registro existente).
    ════════════════════════════════════════════════════════════ */
    function generarCorreoVacacionesEditado($fechaInicio, $fechaFinal, $diasTomados, $nombre_colaborador){

        $fechas = generarFechas($fechaInicio, $fechaFinal);
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        $subject = "Se actualizó un registro de tus vacaciones";

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_colaborador</strong>,</p>

            <p>
                Recursos Humanos actualizó un registro de vacaciones existente en
                <strong>SISCA</strong>. Estos son los datos vigentes después del ajuste:
            </p>

            <p><strong>Días:</strong> $diasTomados</p>

            <p><strong>Desglose de fechas:</strong></p>
            <ul>
                $lista_fechas
            </ul>

            <p>
                Si algo de esta información no es correcta, contacta directamente al área
                de Recursos Humanos para corregirlo.
            </p>

            <br>

            <p>Saludos,<br>
            <strong>Sistema SISCA</strong></p>

        </body>
        </html>
        ";

        return [
            'subject' => $subject,
            'body'    => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       Correo cuando RRHH ELIMINA por completo un registro de
       vacaciones (por ejemplo, se había capturado por error).
    ════════════════════════════════════════════════════════════ */
    function generarCorreoVacacionesEliminadas($fechaInicio, $fechaFinal, $diasTomados, $nombre_colaborador){

        $fechas = generarFechas($fechaInicio, $fechaFinal);
        $lista_fechas = '';
        foreach($fechas as $f){
            $lista_fechas .= "<li>$f</li>";
        }

        $subject = "Se eliminó un registro de tus vacaciones";

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>

            <p>Hola <strong>$nombre_colaborador</strong>,</p>

            <p>
                Recursos Humanos eliminó por completo el siguiente registro de vacaciones
                en <strong>SISCA</strong>. Estos días ya no aparecerán en tu historial:
            </p>

            <p><strong>Días eliminados:</strong> $diasTomados</p>

            <p><strong>Desglose de fechas:</strong></p>
            <ul>
                $lista_fechas
            </ul>

            <p>
                Si algo de esta información no es correcta, contacta directamente al área
                de Recursos Humanos para aclararlo.
            </p>

            <br>

            <p>Saludos,<br>
            <strong>Sistema SISCA</strong></p>

        </body>
        </html>
        ";

        return [
            'subject' => $subject,
            'body'    => $body
        ];
    }


    /* ════════════════════════════════════════════════════════════
       calcularVacacionesConUsos — función autoritativa: construye
       los periodos de vacaciones desde 2025, aplica la ventana de
       vencimiento de 18 meses, clasifica cada periodo (vigente,
       proximo, caducado) y consume en FIFO los usos ya aprobados
       (estatus=1) cruzando periodos.
    ════════════════════════════════════════════════════════════ */
    function calcularVacacionesConUsos($fechaIngreso, $conecta, $clave) {

        if (empty($fechaIngreso) || $fechaIngreso == '0000-00-00') {
            return [
                'disponibles' => 0, 'proximos' => 0,
                'caducados'   => 0, 'total'    => 0,
                'usados'      => 0, 'detalle'  => []
            ];
        }

        $hoy = new DateTime();

        // ── Fix formato fecha ────────────────────────────────────────────
        if (strpos($fechaIngreso, '/') !== false) {
            $ingreso = DateTime::createFromFormat('d/m/Y', $fechaIngreso);
        } else {
            $ingreso = new DateTime($fechaIngreso);
        }

        if (!$ingreso) {
            return [
                'disponibles' => 0, 'proximos' => 0,
                'caducados'   => 0, 'total'    => 0,
                'usados'      => 0, 'detalle'  => []
            ];
        }

        $anios = $ingreso->diff($hoy)->y;

        // ── Usos aprobados (estatus general = 1) desde 2025 ──────────────
        $query = "SELECT fecha_inicio, dias_tomados
                FROM solicitudes_vacaciones
                WHERE clave = '$clave' AND estatus = 1
                AND YEAR(fecha_inicio) >= 2025
                ORDER BY fecha_inicio ASC";

        $res  = $conecta->consulta($query);
        $usos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $usos[] = [
                'fecha_inicio' => $row['fecha_inicio'],
                'dias'         => (int)$row['dias_tomados'],
                'restante'     => (int)$row['dias_tomados'],
            ];
        }

        $totalUsados = 0;
        foreach ($usos as $u) $totalUsados += $u['dias'];

        // ── Construir periodos ───────────────────────────────────────────
        $periodos = [];

        for ($i = 1; $i <= $anios; $i++) {

            $fechaAniversario = (clone $ingreso)->modify("+$i year");

            // ── Ignorar periodos anteriores a 2025 ──
            if ((int)$fechaAniversario->format('Y') < 2025) {
                continue;
            }

            $siguienteAniversario = (clone $ingreso)->modify("+" . ($i + 1) . " year");
            $fechaVencimiento     = (clone $fechaAniversario)->modify("+18 months");
            $diasPeriodo          = diasPorAnio($i);
            $tresMeses            = (clone $hoy)->modify("+3 months");

            if ($fechaVencimiento < $hoy) {
                $tipo = 'caducado';
            } elseif ($fechaVencimiento <= $tresMeses) {
                $tipo = 'proximo';
            } else {
                $tipo = 'vigente';
            }

            $periodos[] = [
                'anio'                  => $i,
                'periodo_inicio'        => $fechaAniversario->format('d/m/Y'),
                'aniversario'           => $fechaAniversario,
                'siguiente_aniversario' => $siguienteAniversario,
                'vence'                 => $fechaVencimiento->format('d/m/Y'),
                'vence_dt'              => clone $fechaVencimiento,
                'dias_brutos'           => $diasPeriodo,
                'tipo'                  => $tipo,
            ];
        }

        // ── FIFO cross-periodo ───────────────────────────────────────────
        /* BUG FIX: antes solo se armaban los periodos NO caducados
           (tipo !== 'caducado') como candidatos para consumir uso. Eso
           hacía que, si un periodo ya venció HOY, quedara fuera del
           reparto aunque los días se hayan tomado ANTES de que
           venciera — el uso entonces "se recorría" y se descontaba por
           error del siguiente periodo (todavía vigente), dejando el
           periodo vencido completo como caducado (sin descontar lo ya
           usado) y el periodo vigente con menos días de la cuenta. El
           consumo FIFO debe repartirse entre TODOS los periodos en
           orden cronológico (haya vencido o no ese periodo a día de
           hoy); lo que sobre en cada uno, tras restar lo consumido, es
           lo que se clasifica como disponible/próximo/caducado más
           abajo. */
        $idxDisponibles = array_keys($periodos);

        $consumoPorPeriodo = array_fill(0, count($periodos), 0);

        /* Días usados que no alcanzan a cubrirse con NINGÚN periodo ya
           generado (típico: RRHH registra un día directo para alguien
           que aún no cumple su primer año, o que ya agotó hasta el
           último periodo existente). Es un "adelanto": se resta de los
           días disponibles más abajo, pudiendo dejarlos en negativo,
           como recordatorio de que ese día ya se tomó y se debe cargar
           contra el siguiente periodo que se genere (en cuanto exista,
           este mismo FIFO ya lo absorbe solo, porque el uso sigue en
           la BD con su fecha original). */
        $diasNoCubiertos = 0;

        foreach ($usos as $uso) {
            $diasPorAsignar = $uso['dias'];
            $usoFecha       = new DateTime($uso['fecha_inicio']);

            foreach ($idxDisponibles as $idx) {
                if ($diasPorAsignar <= 0) break;

                /* AJUSTE: un uso solo puede descontarse de un periodo
                   que TODAVÍA estaba vigente el día en que se tomó. Si
                   el periodo ya venció ANTES de la fecha del uso, esos
                   días caducados ya se perdieron para siempre -- un uso
                   posterior (ej. tomado ya en 2026) no puede
                   "recuperarlos" y descontarse de un periodo 2025 que
                   para esa fecha ya estaba vencido. Sin este candado, el
                   FIFO cronológico global terminaba restando usos nuevos
                   del periodo viejo ya caducado en vez del periodo
                   vigente que le correspondía, y de paso "revivía" días
                   que ya habían caducado. */
                if ($periodos[$idx]['vence_dt'] < $usoFecha) continue;

                $disponibleEnPeriodo = $periodos[$idx]['dias_brutos'] - $consumoPorPeriodo[$idx];

                if ($disponibleEnPeriodo <= 0) continue;

                $tomarDeEste = min($diasPorAsignar, $disponibleEnPeriodo);
                $consumoPorPeriodo[$idx] += $tomarDeEste;
                $diasPorAsignar          -= $tomarDeEste;
            }

            $diasNoCubiertos += $diasPorAsignar;
        }

        // ── Calcular totales ─────────────────────────────────────────────
        $diasDisponibles = 0;
        $diasProximos    = 0;
        $diasCaducados   = 0;
        $detalle         = [];

        foreach ($periodos as $idx => $p) {
            $usadosEnPeriodo = $consumoPorPeriodo[$idx];
            $diasRestantes   = max(0, $p['dias_brutos'] - $usadosEnPeriodo);

            switch ($p['tipo']) {
                case 'caducado': $diasCaducados   += $diasRestantes; break;
                case 'proximo':  $diasProximos    += $diasRestantes; break;
                default:         $diasDisponibles += $diasRestantes; break;
            }

            $detalle[] = [
                'anio'           => $p['anio'],
                'periodo_inicio' => $p['periodo_inicio'],
                'vence'          => $p['vence'],
                'dias'           => $diasRestantes,
                'usados'         => $usadosEnPeriodo,
                'tipo'           => $p['tipo'],
            ];
        }

        // El adelanto sin periodo se descuenta de disponibles (puede
        // quedar en negativo: significa que se debe ese día).
        $diasDisponibles -= $diasNoCubiertos;

        return [
            'disponibles' => $diasDisponibles,
            'proximos'    => $diasProximos,
            'caducados'   => $diasCaducados,
            'total'       => $diasDisponibles + $diasProximos,
            'usados'      => $totalUsados,
            'detalle'     => $detalle,
        ];
    }