<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* getDetalle (precache de "Detalle" para el export) trae TODOS los
   folios del periodo de un jalón -- miles de filas con varios JOIN.
   Con los límites por default de PHP (max_execution_time ~30s,
   memory_limit ~128M) el script se puede morir a medio fetch/encode
   en un query así de grande, y eso es lo que en realidad estaba
   cortando "Detalle" a la mitad -- no falta de espera antes de
   consultar. Se les quita el límite para esta petición. */
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

session_start();

header('Content-Type: application/json');

require './vendor/autoload.php';
include("./funciones/encripta.php");

$conecta = new conector();

$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : [];

$action = $input['action'] ?? '';

if($action === 'getCumplimiento'){

    $periodo  = mysqli_real_escape_string($conecta->conexion, $input['periodo'] ?? '');
    $anio     = mysqli_real_escape_string($conecta->conexion, $input['anio'] ?? '');
    $nameNap  = mysqli_real_escape_string($conecta->conexion, $input['nap'] ?? '');
    $sc       = mysqli_real_escape_string($conecta->conexion, $input['sc'] ?? '');
    $brocker  = mysqli_real_escape_string($conecta->conexion, $input['brocker'] ?? '');
    $gerencia = mysqli_real_escape_string($conecta->conexion, $input['gerencia'] ?? '');
    $nivel = mysqli_real_escape_string($conecta->conexion, $input['nivel'] ?? '');

    preg_match('/\((\d+)\)/', $nameNap, $match);
    $nap = $match[1] ?? '';

    if ($nivel != 12) {
        if (empty($nap) && empty($sc) && empty($brocker) && empty($gerencia)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Debes ingresar un NAP'
            ]);
            exit;
        }
    }

    // =========================
    // VALIDAR FILTROS
    // =========================

    if(empty($periodo) || empty($anio))
    {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes seleccionar año y periodo'
        ]);
        exit;
    }

    if(!preg_match('/^\d{4}$/', $anio))
    {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Año inválido'
        ]);
        exit;
    }

    $where = [];

    // =========================
    // FILTROS BASE
    // =========================
    $where[] = "c.periodo = '$periodo'";
    $where[] = "c.anio = '$anio'";

    if(!empty($nap))
        $where[] = "c.nap = '$nap'";

    if($sc != '' && $sc != 'TODOS')
        $where[] = "ee.sc like '%$sc%'";

    if($brocker != '')
        $where[] = "ee.brocker like '%$brocker%'";

    if($gerencia != '')
        $where[] = "ee.gerencia like '%$gerencia%'";

    $whereSQL = implode(" AND ", $where);

    $sql ="Select c.*,ifnull(er.regimen,'') as regimen, ifnull(er.cp_fiscal,'') as cp_fiscal, bono * ifnull(iva,0) as iva,
                  bono * ifnull(ret_iva,0) as ret_iva,
                  case er.regimen when 'Asimilados a Salarios' then if(bono=0,0,(((bono - limite_inferior) * tasa) + cuota_fija) * -1)
                                  else bono * ifnull(ret_isr,0)
                   end as ret_isr,
                   bono + bono * ifnull(iva,0) + bono * ifnull(ret_iva,0) + case er.regimen when 'Asimilados a Salarios'
                                                                                then if(bono=0,0,(((bono - limite_inferior) * tasa) + cuota_fija) * -1)
                                                                                else bono * ifnull(ret_isr,0)
                                                                            end as total
            from (Select t.sc,t.brocker,t.gerencia,t.nap,u.clave,u.nombre,rfc,t.avs,t.registros,t.pago,t.saldo_total,t.puntos,ec.categoria,
                               MAX(IF(t.avs >= 1 OR t.registros >= 1,IFNULL(eb.bono,0),0)) AS bono
                        FROM (SELECT c.nap,c.avs,c.registros,c.pago,ee.sc,ee.brocker,ee.gerencia,n.no_empleado,SUM(c.saldo) AS saldo_total,
                                     ROUND(SUM(c.saldo) / 100000, 2) AS puntos
                              FROM cumplimiento c
                                LEFT JOIN naps n ON (c.nap = n.nap)
                                LEFT JOIN estruc_empleados ee ON (n.no_empleado = ee.no_empleado)
                              WHERE $whereSQL and c.pago=1
                              GROUP BY c.nap,c.avs,c.registros,c.pago,ee.sc,ee.brocker,ee.gerencia,n.no_empleado) t
                            LEFT JOIN esquema_categorias ec
                                    ON (t.puntos >= ec.puntos_min AND (t.puntos <= ec.puntos_max OR ec.puntos_max IS NULL))
                            LEFT JOIN esquema_bonos eb
                                ON (ec.categoria = eb.categoria AND (eb.avs <= t.avs OR eb.registros <= t.registros))
                            LEFT JOIN empleados e ON (t.no_empleado = e.no_empleado)
                            LEFT JOIN usuarios u ON e.clave = u.clave
                        GROUP BY t.sc,t.brocker,t.gerencia,t.nap,u.clave,u.nombre,rfc,t.avs,t.registros,t.pago,t.saldo_total,t.puntos,ec.categoria) c
                left join (SELECT clave, CASE MIN(CASE regimen
                                                        WHEN 'Personas Fisicas' THEN 1
                                                        WHEN 'RESICO' THEN 2
                                                        WHEN 'Sueldos y Salarios' THEN 3
                                                        when 'Asimilados a Salarios' then 4
                                                   END)
                                            WHEN 1 THEN 'Personas Fisicas'
                                            WHEN 2 THEN 'RESICO'
                                            WHEN 3 THEN 'Sueldos y Salarios'
                                            WHEN 4 THEN 'Asimilados a Salarios'
                                         END AS regimen,
                                    MAX(cp) as cp_fiscal
                           FROM empl_reg_fiscal
                           GROUP BY clave) er on (c.clave=er.clave)
                left join regimen_fiscal rf on (er.regimen=rf.regimen)
                left join isr_tarifa_asimilados ita
                        on (ita.anio='$anio' and er.regimen='Asimilados a Salarios' and c.bono >= limite_inferior and c.bono <= limite_superior)";
    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al consultar cumplimiento: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $data = [];

    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}


if($action === 'getDetalle'){

    /* nap es OPCIONAL: si viene vacío se trae el detalle de TODOS los
       NAPs del periodo/año de un solo jalón (usado por el precache que
       arma la hoja "Detalle" del export). Antes el frontend disparaba
       una petición POR CADA NAP en paralelo (hasta 300+ al mismo
       tiempo) -- con esa carga algunas fallaban en silencio (timeout /
       límite de conexiones/sesión) y el NAP completo desaparecía de
       "Detalle" aunque sí seguía apareciendo en "Bonos por NAP" (esa
       hoja se arma con una sola consulta aparte, sin depender de estas
       peticiones). Traer todo en una sola consulta elimina esa carrera. */
    $nap     = mysqli_real_escape_string($conecta->conexion, $input['nap']     ?? '');
    $anio    = mysqli_real_escape_string($conecta->conexion, $input['anio']    ?? '');
    $periodo = mysqli_real_escape_string($conecta->conexion, $input['periodo'] ?? '');

    if(empty($anio) || empty($periodo)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes indicar año y periodo'
        ]);
        exit;
    }

    /* c.pago=1 -- MISMO filtro que usa getCumplimiento para armar "Bonos
       por NAP" (esa hoja solo suma folios con pago=1). Sin este filtro,
       "Detalle" traía TAMBIÉN los folios no pagados/pendientes del
       periodo, sumando de más y desfasando el total contra "Bonos por
       NAP". */
    $whereDet = ["c.anio = '$anio'", "c.periodo = '$periodo'", "c.pago = 1"];
    if(!empty($nap)){
        $whereDet[] = "c.nap = '$nap'";
    }
    $whereDetSQL = implode(" AND ", $whereDet);

    /* OJO: antes era INNER JOIN produccion -- si un folio de cumplimiento
       no tenía (todavía, o ya no) su fila espejo en produccion, la fila
       completa desaparecía de "Detalle" sin dejar rastro, aunque su
       saldo sí contaba en el total de "Bonos por NAP" (esa consulta no
       pasa por produccion). Con LEFT JOIN la fila siempre aparece; si de
       plano no hay captura en produccion se marca "Sin captura" igual
       que cuando el empleado capturado es 0. */
    $sql = "Select sc,brocker,gerencia,c.*, pt.trabajador,
                   if(p.no_empleado is null or p.no_empleado=0,'Sin captura','') as estatus_cap
            FROM cumplimiento c
                left join produccion p on (c.folio=p.folio)
                left join estruc_empleados ee on (p.no_empleado=ee.no_empleado)
            LEFT JOIN prod_trabajador pt ON c.folio = pt.folio
            WHERE $whereDetSQL";
    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al consultar detalle: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $data = [];

    while($row = mysqli_fetch_assoc($res))
    {
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}

if($action === 'getDetalleConteo'){

    /* Cuenta rápido (sin traer columnas) con el MISMO FROM/JOIN/WHERE
       que getDetalle. El frontend usa esto como "verdad de referencia":
       compara el número de filas que le llegaron de getDetalle contra
       este COUNT(*) real, y solo da por completa la carga cuando
       coinciden -- así ya no depende de que dos lecturas de getDetalle
       se repitan entre sí (eso se demostró que puede dar un falso
       positivo si el proceso que llena cumplimiento hace una pausa
       justo a la mitad). */
    $nap     = mysqli_real_escape_string($conecta->conexion, $input['nap']     ?? '');
    $anio    = mysqli_real_escape_string($conecta->conexion, $input['anio']    ?? '');
    $periodo = mysqli_real_escape_string($conecta->conexion, $input['periodo'] ?? '');

    if(empty($anio) || empty($periodo)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Debes indicar año y periodo'
        ]);
        exit;
    }

    $whereDet = ["c.anio = '$anio'", "c.periodo = '$periodo'", "c.pago = 1"];
    if(!empty($nap)){
        $whereDet[] = "c.nap = '$nap'";
    }
    $whereDetSQL = implode(" AND ", $whereDet);

    $sql = "SELECT COUNT(*) AS total
            FROM cumplimiento c
                left join produccion p on (c.folio=p.folio)
                left join estruc_empleados ee on (p.no_empleado=ee.no_empleado)
            LEFT JOIN prod_trabajador pt ON c.folio = pt.folio
            WHERE $whereDetSQL";
    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al contar detalle: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($res);

    echo json_encode([
        'status' => 'ok',
        'total'  => (int)($row['total'] ?? 0)
    ]);

    exit;
}

if($action === 'getNap')
{
    $query = mysqli_real_escape_string($conecta->conexion, trim($input['nap'] ?? ''));

    // si es número busca por NAP
    if(is_numeric($query))
        $where = "n.nap LIKE '%$query%'";

    else
        $where = "u.nombre LIKE '%$query%'";

    $sql = "Select n.nap, u.nombre
            FROM naps n
                LEFT JOIN estruc_empleados ee ON n.no_empleado = ee.no_empleado
                LEFT JOIN usuarios u ON ee.clave = u.clave
            WHERE $where
            LIMIT 20";

    $res = $conecta->consulta($sql);

    if(!$res){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al consultar NAP: ' . mysqli_error($conecta->conexion)
        ]);
        exit;
    }

    $data = [];

    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Acción no reconocida'
]);
exit;