<?php
/* ════════════════════════════════════════════════════════════
   reporteador_funciones.php

   Lógica COMPARTIDA entre seg_reporteador.php (vista en pantalla)
   y seg_reporteador_excel.php (exportación): arma la tabla temporal
   de trabajo, la enriquece con los mismos ~15 UPDATE que ya usa
   seg_ventas.php, y corre el SELECT final. Así la parte pesada del
   negocio vive en un solo lugar y no se duplica en dos archivos
   (si algún día cambia una regla de negocio, se cambia aquí y ya).

   ALCANCE (leer antes de tocar):
   Por ahora el Reporteador solo cubre búsquedas "estructurales" con
   rango de fechas: Socio Comercial, SubBrocker, Comisionista,
   Gerencia y NAP — el mismo modo que ya usa seg_ventas.php para
   generar listados masivos (varios registros a la vez).

   La búsqueda puntual por Folio/Oportunidad, NSS/CURP o Cliente NO
   está conectada aquí todavía: en seg_ventas.php ese modo depende de
   una función externa (tablas_sv2()) y de rastrear folios en tablas
   de respaldo de años anteriores — sirve para localizar UN registro
   puntual, no tanto para generar un reporte masivo con columnas a
   elegir. Si después se necesita, se puede agregar reutilizando esa
   misma lógica de seg_ventas.php.
════════════════════════════════════════════════════════════ */

/* ── Catálogo de campos disponibles para el reporte ──
   Cada key es una columna que el SELECT final de obtenerDatosReporte()
   ya trae resuelta/legible (no ids crudos, ya viene el texto). 'grupo'
   organiza el panel de selección de campos POR TABLA DE ORIGEN (a
   petición expresa: separar los campos por tabla, no por tema). */
$CAMPOS_REPORTE = [
    // ── carobra_2014.produccion (datos base del movimiento) ──
    'tipo_prod'         => ['label' => 'Tipo de producto',           'grupo' => 'Producción'],
    'folio'             => ['label' => 'Folio',                      'grupo' => 'Producción'],
    'no_oportunidad'    => ['label' => 'No. oportunidad',            'grupo' => 'Producción'],
    'nap'               => ['label' => 'NAP',                        'grupo' => 'Producción'],
    'nss'               => ['label' => 'NSS',                        'grupo' => 'Producción'],
    'sc'                => ['label' => 'Socio Comercial',            'grupo' => 'Producción'],
    'brocker'           => ['label' => 'SubBrocker',                 'grupo' => 'Producción'],
    'gerencia'          => ['label' => 'Gerencia',                   'grupo' => 'Producción'],
    'movimiento'        => ['label' => 'Movimiento',                 'grupo' => 'Producción'],
    'confirma'          => ['label' => 'Confirmación',               'grupo' => 'Producción'],
    'cierre_cap'        => ['label' => 'Cierre captura',             'grupo' => 'Producción'],
    'saldo_estimado'    => ['label' => 'Saldo estimado',             'grupo' => 'Producción'],
    'est_anticipo'      => ['label' => 'Estatus anticipo',           'grupo' => 'Producción'],
    'monto_anticipo'    => ['label' => 'Monto anticipo',             'grupo' => 'Producción'],
    'fec_traspaso'      => ['label' => 'Fecha traspaso',             'grupo' => 'Producción'],
    'fec_captura'       => ['label' => 'Fecha captura',              'grupo' => 'Producción'],
    'fec_anticipo'      => ['label' => 'Fecha anticipo',             'grupo' => 'Producción'],
    'fec_cierre'        => ['label' => 'Fecha cierre',               'grupo' => 'Producción'],

    // ── prod_trabajador ──
    'trabajador'        => ['label' => 'Cliente / Trabajador',       'grupo' => 'Trabajador'],
    'curp'              => ['label' => 'CURP',                       'grupo' => 'Trabajador'],
    'edad'              => ['label' => 'Edad',                       'grupo' => 'Trabajador'],
    'observa'           => ['label' => 'Observaciones',              'grupo' => 'Trabajador'],

    // ── empl_usuarios ──
    'nombre'            => ['label' => 'Comisionista',               'grupo' => 'Comisionista / Analista'],
    'analista'          => ['label' => 'Analista (captura)',         'grupo' => 'Comisionista / Analista'],

    // ── prod_rechazos / tipo_rechazo / rechazos ──
    'tipo_rechazo'      => ['label' => 'Tipo de rechazo',            'grupo' => 'Rechazos'],
    'rechazo'           => ['label' => 'Rechazo',                    'grupo' => 'Rechazos'],
    'fec_rechazo'       => ['label' => 'Fecha rechazo',              'grupo' => 'Rechazos'],
    'descrip'           => ['label' => 'Descripción (extemporáneo)', 'grupo' => 'Rechazos'],

    // ── segundo_anticipo ──
    'monto_seg_ant'     => ['label' => 'Monto segundo anticipo',     'grupo' => 'Segundo anticipo'],
    'fec_ant2'          => ['label' => 'Fecha segundo anticipo',     'grupo' => 'Segundo anticipo'],

    // ── pagos / calculo_pago / acum_det_prod / desc_incumplimiento ──
    'saldo_real'        => ['label' => 'Saldo real',                 'grupo' => 'Pagos'],
    'fec_pago'          => ['label' => 'Fecha pago',                 'grupo' => 'Pagos'],
    'saldo_pago'        => ['label' => 'Saldo pago',                 'grupo' => 'Pagos'],
    'fec_desc'          => ['label' => 'Fecha desc. incumplimiento', 'grupo' => 'Pagos'],

    // ── apovol_prod / av_pagos / av_fechas / pagos_folios_sin_av ──
    'folio_av'          => ['label' => 'Folio AV',                   'grupo' => 'Autorización de Vida (AV)'],
    'estatus_av'        => ['label' => 'Estatus AV',                 'grupo' => 'Autorización de Vida (AV)'],
    'frecuencia'        => ['label' => 'Frecuencia',                 'grupo' => 'Autorización de Vida (AV)'],
    'fec_envio'         => ['label' => 'Fecha envío AV',             'grupo' => 'Autorización de Vida (AV)'],
    'autoriza_sin_av'   => ['label' => 'Autorización sin AV',        'grupo' => 'Autorización de Vida (AV)'],

    // ── movilidad ──
    'movilidad'         => ['label' => 'Movilidad',                  'grupo' => 'Movilidad'],
    'cap_movilidad'     => ['label' => 'Fecha captura movilidad',    'grupo' => 'Movilidad'],
    'fec_sello'         => ['label' => 'Fecha sello movilidad',      'grupo' => 'Movilidad'],

    // ── estatus / afores ──
    'estatus'           => ['label' => 'Estatus',                    'grupo' => 'Estatus / Afore'],
    'estatus_pbi'       => ['label' => 'Estatus Procesar',           'grupo' => 'Estatus / Afore'],
    'afore'             => ['label' => 'Afore cedente',              'grupo' => 'Estatus / Afore'],

    // ── fechas_cierre_com / fec_cierre_campana ──
    'mes'               => ['label' => 'Mes',                        'grupo' => 'Campaña / Cierre'],
    'campana'           => ['label' => 'Campaña',                    'grupo' => 'Campaña / Cierre'],
];

/* Campos que se formatean como dinero ($#,##0.00) al desplegarse. */
$CAMPOS_MONEDA = ['saldo_estimado', 'saldo_real', 'saldo_pago', 'monto_anticipo', 'monto_seg_ant'];

/* Tipos de filtro soportados por el Reporteador (ver nota de alcance
   arriba). Se usa tanto para pintar los botones como para validar. */
$FILTROS_REPORTEADOR = ['Soc. Comercial', 'SubBrocker', 'Comisionista', 'Gerencia', 'NAP'];

/* Selección por default cuando el usuario no ha elegido nada aún
   (primera visita, sin nada guardado en localStorage). */
$CAMPOS_DEFAULT = ['folio', 'trabajador', 'nombre', 'sc', 'gerencia', 'estatus', 'saldo_real', 'fec_traspaso', 'mes'];

/* Recibe una lista de keys (ej. desde $_POST/$_GET campos_csv) y
   regresa solo las que existen en el catálogo. IMPORTANTE: preserva
   el ORDEN en que vienen en $seleccion (no el orden del catálogo),
   porque ese orden es el que el usuario armó arrastrando los campos
   en el picker, y define el orden de columnas en pantalla y en el
   Excel. También quita duplicados. */
function filtrarCamposValidos($seleccion, $catalogo) {
    if (!is_array($seleccion)) return [];
    $validos = array_keys($catalogo);
    $limpios = [];
    foreach ($seleccion as $campo) {
        $campo = trim($campo);
        if ($campo !== '' && in_array($campo, $validos, true) && !in_array($campo, $limpios, true)) {
            $limpios[] = $campo;
        }
    }
    return $limpios;
}

/* ════════════════════════════════════════════════════════════
   obtenerDatosReporte — arma la tabla temporal, la enriquece y
   corre el SELECT final. SIEMPRE regresa todas las columnas del
   catálogo (el picker de campos solo decide cuáles se PINTAN /
   exportan después, no cuáles se consultan — así no hay que rearmar
   la consulta según lo que el usuario haya elegido).

   $p espera: filtro, valor1, valor2 (fecha ini), valor3 (fecha fin),
              te, sfp, srm0, reg_esp, anio_ini, anio_fin, mes_i, mes_f,
              nivel, clave

   $limite: tope de filas para no tronar memoria/tiempo en un reporte
            sin filtrar lo suficiente. Pantalla usa uno más chico,
            Excel uno más grande (ver seg_reporteador.php / _excel.php).
════════════════════════════════════════════════════════════ */
function obtenerDatosReporte($conecta, $p, $limite = 2000) {

    global $FILTROS_REPORTEADOR;

    $filtro   = $p['filtro']   ?? '';
    $valor1   = $p['valor1']   ?? '';
    $valor2   = $p['valor2']   ?? '';
    $valor3   = $p['valor3']   ?? '';
    $te       = $p['te']       ?? '';
    $sfp      = intval($p['sfp']      ?? 0);
    $srm0     = intval($p['srm0']     ?? 0);
    $reg_esp  = intval($p['reg_esp']  ?? 0);
    $anio_ini = $p['anio_ini'] ?? '';
    $anio_fin = $p['anio_fin'] ?? '';
    $mes_i    = $p['mes_i']    ?? '';
    $mes_f    = $p['mes_f']    ?? '';
    $clave    = $p['clave']    ?? '';

    $resultado = ['ok' => true, 'msg' => '', 'tit_mes' => '', 'filas' => [], 'limitado' => false];

    if (!in_array($filtro, $FILTROS_REPORTEADOR, true)) {
        $resultado['ok']  = false;
        $resultado['msg'] = 'Este modo de búsqueda todavía no está disponible en el Reporteador (por ahora solo: '
                           . implode(', ', $FILTROS_REPORTEADOR) . ').';
        return $resultado;
    }

    $tit_mes = ($te == "ep") ? "Mes Pago" : "Mes Com.";
    $resultado['tit_mes'] = $tit_mes;

    /* ── Resolver rango de fechas (mismo criterio que seg_ventas.php) ── */
    if ($valor2 == "" && $valor3 == "") {
        $cal = ($te == "ep") ? "fechas_cierre" : "fechas_cierre_com";

        $anioIniInt = intval($anio_ini);
        $mesIniInt  = intval($mes_i);
        $query = "Select fec_ini from $cal where anio=$anioIniInt and mes=$mesIniInt";
        $bd = $conecta->consulta($query);
        $tb = $bd ? mysqli_fetch_array($bd) : null;
        $valor2 = $tb['fec_ini'] ?? '';

        $anioFinInt = intval($anio_fin);
        $mesFinInt  = intval($mes_f);
        $query = "Select fec_fin from $cal where anio=$anioFinInt and mes=$mesFinInt";
        $bd = $conecta->consulta($query);
        $tb = $bd ? mysqli_fetch_array($bd) : null;
        $valor3 = $tb['fec_fin'] ?? '';
    } else {
        $anio_ini = substr($valor2, -4);
        $anio_fin = substr($valor3, -4);
    }

    if (empty($valor2) || empty($valor3)) {
        $resultado['ok']  = false;
        $resultado['msg'] = 'No se pudo resolver el rango de fechas de la búsqueda. Verifica el calendario, el periodo y/o las fechas.';
        return $resultado;
    }

    $fec_inc = $valor2;
    $fec_fin = $valor3;

    /* ── Armar el filtro estructural (mismo criterio que seg_ventas.php) ── */
    $q_emp = 1;
    $w_nap = "";
    $qestruc = "";

    if ($filtro == "Soc. Comercial") {
        $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
        $qestruc = "(Select * from carobra_2014.estruc_empleados where sc='$valor1Esc')";
    } elseif ($filtro == "SubBrocker") {
        $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
        $qestruc = "(Select * from carobra_2014.estruc_empleados where brocker='$valor1Esc')";
    } elseif ($filtro == "Gerencia") {
        if ($valor1 != "TODAS") {
            $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
            $qestruc = "(Select * from carobra_2014.estruc_empleados where gerencia='$valor1Esc')";
        } else {
            $qestruc = "carobra_2014.estruc_empleados";
        }
    } elseif ($filtro == "NAP") {
        $partes = explode("(", $valor1);
        $nap = str_replace(")", "", $partes[1] ?? $valor1);
        $nap = trim($nap);
        $napEsc = mysqli_real_escape_string($conecta->conexion, $nap);
        $qestruc = "carobra_2014.estruc_empleados";
        $w_nap = " and p.nap='$napEsc'";
    } else { // Comisionista
        $q_emp = 0;
        $nombreEmp = ($valor1 == "Usuario de prueba") ? "MARIA MAYRA SANTOS CORTES" : $valor1;
        $nombreEmpEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreEmp));
        $query = "Select no_empleado from empl_usuarios where nombre='$nombreEmpEsc'";
        $bd = $conecta->consulta($query);
        $tb = $bd ? mysqli_fetch_array($bd) : null;
        $noEmp = intval($tb[0] ?? 0);
        $qestruc = "(Select * from carobra_2014.estruc_empleados where no_empleado=$noEmp)";
    }

    if (empty($valor1)) {
        $resultado['ok']  = false;
        $resultado['msg'] = 'Falta el valor a buscar (' . $filtro . ').';
        return $resultado;
    }

    /* ── Refrescar la tabla de apoyo estruc_empleados (misma lógica) ── */
    $conecta->consulta("Truncate estruc_empleados");
    $query = "Insert ignore into estruc_empleados
                Select distinct clave,no_empleado,sc,brocker,
                       ung.id_gerencia,gerencia
                from usuarios_nivel_gerencia ung
                    inner join estructura_gerencia eg
                            on (ung.id_gerencia=eg.id_gerencia)
                    inner join (Select distinct id_gerencia,gerencia from gerencias) g
                            on (ung.id_gerencia=g.id_gerencia)
                where pertenece=1";
    $conecta->consulta($query);

    /* ── Tabla temporal de trabajo (nombre único por consulta) ── */
    $sufijo = date("dmYGis") . '_' . mt_rand(1000, 9999);
    $qsv = "w_rep_" . preg_replace('/[^A-Za-z0-9_]/', '', $clave) . "_" . $sufijo;
    $conecta->consulta("Create TABLE $qsv LIKE sv");

    $query = "Insert into carobra_2014.$qsv
                Select p.folio,'','','','','',id_estatus_prod,
                       id_estatus_prod,estatus_confirma,cierre_cap,0,
                       0,id_tipo_mov,nss,'',nap,'','',afo_cedente,'','',
                       '',sc,brocker,id_gerencia,gerencia,
                       id_usuario_cap,'','',p.no_empleado,'','',
                       id_estatus_anticipo,monto_anticipo,0,
                       saldo_estimado,0,0,'',fec_captura,fec_pago_ant,
                       '',fec_traspaso,fec_cierre,'','',
                       fec_ult_estatus,'','','',0,''
                FROM carobra_2014.produccion p
                    inner join $qestruc ee
                            on (p.no_empleado=ee.no_empleado)";

    $w_query = " where p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                    AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
                    AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
                    $w_nap";

    $w_query .= ($reg_esp == 1) ? " and length(p.folio) < 10 " : " and length(p.folio) >= 10 ";

    if ($sfp == 1) {
        $query .= " left join carobra_2014.calculo_pago cp on (p.folio=cp.folio) ";
        $w_query .= " and ifnull(cp.fec_pago,'')=''";
        if ($srm0 == 1) {
            $query .= " left join carobra_2014.pagos pa on (p.folio=pa.folio) ";
            $w_query .= " and ifnull(pa.saldo_real,0)!=0";
        }
    } else if ($srm0 == 1) {
        $query .= " left join carobra_2014.pagos pa on (p.folio=pa.folio) ";
        $w_query .= " and ifnull(pa.saldo_real,0)!=0";
    }

    $query .= $w_query;

    if ($srm0 == 0 && $q_emp == 1) {
        $query .= " union all
                    Select p.folio,'','','','','',id_estatus_prod,
                           id_estatus_prod,estatus_confirma,cierre_cap,0,0,id_tipo_mov,p.nss,'',p.nap,
                           '','',afo_cedente,'','','',sc,brocker,id_gerencia,gerencia,id_usuario_cap,'','',
                           p.no_empleado,'','',id_estatus_anticipo,monto_anticipo,0,saldo_estimado,0,0,'',
                           fec_captura,fec_pago_ant,'',fec_traspaso,fec_cierre,'','',fec_ult_estatus,'','','',0,''
                    FROM carobra_2014.produccion p
                        inner join carobra_2014.naps n on (p.nap=n.nap)
                        inner join $qestruc ee on (n.no_empleado=ee.no_empleado)";
        $w_query = " where p.no_empleado=0 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
                        $w_nap";
        $w_query .= ($reg_esp == 1) ? " and length(p.folio) < 10 " : " and length(p.folio) >= 10 ";
        $query .= $w_query;

        $query .= " union all
                    Select p.folio,'','','','','',id_estatus_prod,
                           id_estatus_prod,estatus_confirma,cierre_cap,0,0,id_tipo_mov,p.nss,'',p.nap,
                           '','',afo_cedente,'','','',sc,brocker,id_gerencia,gerencia,id_usuario_cap,'','',
                           p.no_empleado,'','',id_estatus_anticipo,monto_anticipo,0,saldo_estimado,0,0,'',
                           fec_captura,fec_pago_ant,'',fec_traspaso,fec_cierre,'','',fec_ult_estatus,'','','',0,''
                    FROM carobra_2014.produccion p
                        inner join carobra.empleados e on (p.nap=e.nae)
                        inner join $qestruc ee on (e.no_empleado=ee.no_empleado)";
        $w_query = " where p.no_empleado=0 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
                        $w_nap";
        $w_query .= ($reg_esp == 1) ? " and length(p.folio) < 10 " : " and length(p.folio) >= 10 ";
        $query .= $w_query;
    }

    $t_pt  = "prod_trabajador";
    $t_cp  = "calculo_pago";
    $t_ap2 = "";
    if ($anio_ini == $anio_fin) {
        $t_ap = "carobra.acum_det_prod_" . $anio_fin;
    } else {
        $t_ap  = "carobra.acum_det_prod_" . $anio_ini;
        $t_ap2 = "carobra.acum_det_prod_" . $anio_fin;
    }

    $conecta->consulta($query);

    $bd = $conecta->consulta("Select 1 from carobra_2014.$qsv");
    if (!$bd || $bd->num_rows == 0) {
        $conecta->consulta("DROP TABLE $qsv");
        $resultado['ok']  = true;
        $resultado['msg'] = 'Sin resultados para estos filtros.';
        return $resultado;
    }

    /* ── Enriquecer (mismas ~15 actualizaciones que seg_ventas.php) ── */
    $updates   = [];
    $updates[] = "Update $qsv s
                    JOIN fechas_cierre_com f
                            on (str_to_date(s.fec_traspaso,'%d/%m/%Y') >= str_to_date(f.fec_ini,'%d/%m/%Y')
                                    and str_to_date(s.fec_traspaso,'%d/%m/%Y') <= str_to_date(f.fec_fin,'%d/%m/%Y'))
                    JOIN meses m on (f.mes=m.no_mes)
                  SET s.nom_mes=if(f.mes <=12,m.mes_largo,f.nom_periodo)";

    $updates[] = "Update $qsv s, fec_cierre_campana f
                  set s.campana=f.campana
                  where str_to_date(s.fec_traspaso,'%d/%m/%Y') >= str_to_date(f.fec_ini,'%d/%m/%Y')
                      and str_to_date(s.fec_traspaso,'%d/%m/%Y') <= str_to_date(f.fec_fin,'%d/%m/%Y')";

    $updates[] = "Update $qsv s, $t_pt pt
                  set s.trabajador=pt.trabajador,s.telefono=pt.telefono,
                      s.celular=pt.celular,s.cp=pt.cp,
                      s.email_trab=pt.email_trab,s.curp=pt.CURP,
                      s.no_oportunidad=pt.no_oportunidad,
                      s.observa=pt.observa
                  where s.folio=pt.folio";

    $updates[] = "Update $qsv
                  set edad=TIMESTAMPDIFF(YEAR,STR_TO_DATE(CONCAT(
                            CASE
                              WHEN SUBSTRING(curp,5,2) <= DATE_FORMAT(STR_TO_DATE(fec_traspaso,'%d/%m/%Y'), '%y')
                                THEN CONCAT('20', SUBSTRING(curp,5,2))
                              ELSE CONCAT('19', SUBSTRING(curp,5,2))
                            END,SUBSTRING(curp,7,4)),'%Y%m%d'),STR_TO_DATE(fec_traspaso,'%d/%m/%Y'))
                  where length(curp)=18 and length(fec_traspaso)=10";

    $updates[] = "Update $qsv s, empl_usuarios eu
                  set s.clave=eu.clave, s.nombre=eu.nombre
                  where s.no_empleado > 0 and s.no_empleado=eu.no_empleado";

    $updates[] = "Update $qsv s, prod_rechazos pr
                  set s.id_rechazo=pr.id_rechazo, s.id_tipo_rechazo=pr.id_tipo_rechazo
                  where s.folio=pr.folio";

    $updates[] = "Update $qsv s, segundo_anticipo sa
                  set s.monto_seg_ant=sa.monto, s.fec_pago_ant2=sa.fec_pago
                  where s.folio=sa.folio and length(s.fec_pago_ant2)=0";

    $updates[] = "Update $qsv s, pagos pa
                  set s.saldo_real=pa.saldo_real
                  where s.folio=pa.folio";

    $updates[] = "Update $qsv s, $t_cp cp
                  set s.fec_rechazo=cp.fec_pago
                  where cp.rechazo=1 and s.folio=cp.folio";

    $updates[] = "Update $qsv s, $t_cp cp
                  set s.fec_pago=cp.fec_pago
                  where cp.rechazo=0 and s.folio=cp.folio";

    $updates[] = "Update $qsv s
                    JOIN (select folio_pr, max(cast(trim(ap.folio_av) as DOUBLE)) as folio_av
                          from apovol_prod ap
                                inner join $qsv s
                                        on (ap.folio_pr=s.folio)
                          where trim(ap.folio_av) regexp '^[0-9]+$'
                          group by folio_pr) av on (s.folio=av.folio_pr)
                    JOIN av_pagos avp on (av.folio_av=avp.tramite)
                  SET s.folio_av=avp.folio, s.estatus_av=avp.estatus,
                      s.frecuencia=avp.frecuencia
                  WHERE avp.tramite regexp '^[0-9]+$'";

    $updates[] = "Update $qsv s, av_fechas avf
                  set s.fec_envio=avf.fec_envio
                  where s.folio_av !='' and s.folio_av=avf.folio";

    $updates[] = "Update carobra_2014.$qsv s
                    join carobra_2014.pagos_folios_sin_av pav
                        on (s.folio=pav.folio)
                    join carobra.usuarios u on (pav.autoriza=u.clave)
                  set s.men_autoriza=concat('Pago autorizado sin AV el ',pav.fec_autoriza,' por ',u.nombre)";

    $updates[] = "Update $qsv s, extemporaneos a
                  set s.descrip=concat('Pago extemporaneo el ',a.fec_pago)
                  where s.folio=a.folio";

    $updates[] = "Update carobra_2014.$qsv s, $t_ap ap
                  set s.saldo_pago=ap.saldo_real
                  where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
                      and s.folio=ap.folio";

    $updates[] = "Update carobra_2014.$qsv s, carobra_pagos.desc_incumplimiento d
                  set s.fec_desc=d.fec_descuento
                  where s.folio=d.folio";

    if ($t_ap2 != "") {
        $updates[] = "Update carobra_2014.$qsv s, $t_ap2 ap
                      set s.saldo_pago=ap.saldo_real
                      where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
                          and s.folio=ap.folio";
    }

    foreach ($updates as $u) {
        $conecta->consulta($u);
    }

    /* ── SELECT final (mismas columnas que seg_ventas.php, ya con
       nombres que calzan 1:1 con las keys de $CAMPOS_REPORTE) ── */
    $limiteInt = max(1, intval($limite));
    $query = "Select distinct
                   concat(if(id_tipo_mov=1,'Traspaso','Registro'),' ',if(ifnull(n.nap,'')='','NAE','NAP')) as tipo_prod,
                   s.folio,nss,trabajador,s.nombre,saldo_estimado,saldo_real,nom_mes as mes,
                   if(id_estatus_anticipo=1,'Solicitado',if(id_estatus_anticipo=2,'Pagado','')) as est_anticipo,
                   monto_anticipo,monto_seg_ant,esf.estatus,s.sc,s.brocker,s.gerencia,s.nap,
                   if(id_tipo_mov=1,'Traspaso','Registro') as movimiento,fec_pago_ant2 as fec_ant2,s.fec_traspaso,
                   s.fec_captura,fec_pago_ant as fec_anticipo,fec_pago,trec.tipo_rechazo,af.afore,r.rechazo,
                   curp,eu.nombre as analista,if(cierre_cap=0,'No','Si') as cierre_cap,observa,fec_rechazo,descrip,
                   fec_cierre,folio_av,estatus_av,fec_envio,no_oportunidad,men_autoriza as autoriza_sin_av,
                   esf.estatus as estatus_pbi,campana,
                   if(confirma='','',if(substring(confirma,-10)='Confirmado',confirma,concat('En proceso (',confirma,')'))) as confirma,
                   saldo_pago,frecuencia,edad,fec_desc,m.fec_sello,
                   case when movilidad=1 then 'Igual o mayor a 18 meses'
                        else if(ifnull(m.folio,0)=0,'Sin estatus movilidad','Menos de 18 meses')
                   end as movilidad,m.fec_captura as cap_movilidad
              from carobra_2014.$qsv s
                left join carobra_2014.naps n on (s.nap=n.nap)
                left join carobra_2014.estatus esf on (s.id_estatus_prod=esf.id_estatus)
                left join tipo_rechazo trec on (s.id_tipo_rechazo=trec.id_tipo)
                left join afores af on (s.afo_cedente=af.clave)
                left join rechazos r on (s.id_rechazo=r.id_rechazo)
                left join empl_usuarios eu on (s.id_usuario_cap=eu.no_empleado)
                left join movilidad m on (s.folio=m.folio)
              LIMIT $limiteInt";

    $bd = $conecta->consulta($query);
    $filas = [];
    if ($bd) {
        while ($tb = mysqli_fetch_assoc($bd)) {
            $filas[] = $tb;
        }
    }

    $conecta->consulta("DROP TABLE $qsv");

    $resultado['filas']    = $filas;
    $resultado['limitado'] = (count($filas) >= $limiteInt);

    return $resultado;
}