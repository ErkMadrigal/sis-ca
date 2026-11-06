<?php
/* ════════════════════════════════════════════════════════════
   sv_cache_funciones.php

   Caché precalculada del Reporteador de Ventas.

   El reporteador original arma, POR CADA consulta, una tabla
   temporal, le corre ~15 UPDATEs de enriquecimiento y luego hace el
   SELECT final (1-2 min). Aquí ese trabajo se hace UNA vez, en segundo
   plano (cron o "después de responder"), y se guarda en
   carobra_2014.sv_cache (una fila por folio x estructura). La
   consulta del usuario pasa a ser un SELECT indexado: segundos.

   - svCacheRefrescar()   -> reconstruye la caché (tabla nueva + RENAME
                             atómico: nadie ve datos a medias)
   - svCacheUsable()      -> ¿se puede responder desde caché?
   - svCacheConsultar()   -> el SELECT rápido (mismo formato de filas
                             que obtenerDatosReporte)
   - svCacheEstado()      -> para mostrar "datos al hh:mm"
   - svCacheProgramarRefresco() -> dispara el refresco DESPUÉS de
                             responder al usuario (fastcgi_finish_request)

   Requiere seg_ventas_reporteador_funciones.php (obtenerDatosReporte).
════════════════════════════════════════════════════════════ */

/* Cuánto tiempo (min) se considera "fresca" la caché; pasado esto se
   sigue usando pero se dispara un refresco en segundo plano. */
if (!defined('SV_CACHE_FRESCA_MIN'))  define('SV_CACHE_FRESCA_MIN', 10);
/* Pasado este tiempo (min) la caché ya no se usa: se va en vivo. */
if (!defined('SV_CACHE_MAXIMA_MIN'))  define('SV_CACHE_MAXIMA_MIN', 360);

/* Columnas de la caché = aliases del SELECT final de
   obtenerDatosReporte(), en el MISMO orden. */
$SV_CACHE_COLS = [
    'tipo_prod','folio','nss','trabajador','nombre','clave','no_empleado','telefono','celular','cp','email_trab',
    'saldo_estimado','saldo_real','mes','est_anticipo','monto_anticipo','monto_seg_ant','estatus','sc','brocker',
    'gerencia','nap','movimiento','fec_ant2','fec_traspaso','fec_captura','fec_anticipo','fec_pago','tipo_rechazo',
    'afore','rechazo','curp','analista','cierre_cap','observa','fec_rechazo','descrip','fec_cierre','folio_av',
    'estatus_av','fec_envio','no_oportunidad','autoriza_sin_av','transfer_out','estatus_pbi','campana','confirma',
    'saldo_pago','frecuencia','edad','fec_desc','fec_sello','movilidad','cap_movilidad','pplus','ley',
    'categoria_cliente','cotiza','nivel','categoria','tipo_venta','fec_ult_traspaso','estatus_pago',
];
$SV_CACHE_DOUBLE = ['saldo_estimado','saldo_real','monto_anticipo','monto_seg_ant','saldo_pago'];

function svCacheAsegurarTablas($conecta) {
    global $SV_CACHE_COLS, $SV_CACHE_DOUBLE;

    $conecta->consulta("CREATE TABLE IF NOT EXISTS carobra_2014.sv_cache_estado (
        id TINYINT NOT NULL PRIMARY KEY,
        refreshed_at DATETIME NULL,
        window_ini DATE NULL,
        window_fin DATE NULL,
        filas INT NULL,
        segundos INT NULL,
        estado VARCHAR(20) NULL,
        mensaje TEXT NULL,
        iniciado_at DATETIME NULL
    )");
    $conecta->consulta("INSERT IGNORE INTO carobra_2014.sv_cache_estado (id, estado) VALUES (1, 'vacio')");

    $bd = $conecta->consulta("SHOW TABLES FROM carobra_2014 LIKE 'sv_cache'");
    if ($bd && $bd->num_rows > 0) {
        /* La tabla ya existe: si le faltan columnas nuevas del catálogo (ej. nivel,
           categoria, tipo_venta...), se agregan para no tener que recrearla. */
        $existentes = [];
        $rc = $conecta->consulta("SELECT COLUMN_NAME c FROM information_schema.COLUMNS
                                  WHERE TABLE_SCHEMA='carobra_2014' AND TABLE_NAME='sv_cache'");
        while ($rc && ($x = mysqli_fetch_assoc($rc))) $existentes[strtolower($x['c'])] = true;
        $faltan = [];
        foreach ($SV_CACHE_COLS as $c) {
            if (!isset($existentes[strtolower($c)])) $faltan[] = "ADD COLUMN $c VARCHAR(255) NULL";
        }
        if ($faltan) $conecta->consulta("ALTER TABLE carobra_2014.sv_cache " . implode(', ', $faltan));
        return;
    }

    /* folio con la misma charset/collation que produccion.folio para que
       los JOIN/EXISTS contra calculo_pago/pagos no truenen por mezcla de
       collations. */
    $colFolio = "VARCHAR(60)";
    $bd = $conecta->consulta("SELECT CHARACTER_SET_NAME cs, COLLATION_NAME co
                              FROM information_schema.COLUMNS
                              WHERE TABLE_SCHEMA='carobra_2014' AND TABLE_NAME='produccion' AND COLUMN_NAME='folio'");
    $r = $bd ? mysqli_fetch_assoc($bd) : null;
    if ($r && !empty($r['cs']) && !empty($r['co'])) {
        $colFolio .= " CHARACTER SET {$r['cs']} COLLATE {$r['co']}";
    }

    $defs = [];
    foreach ($SV_CACHE_COLS as $c) {
        if ($c === 'folio')                      $defs[] = "folio $colFolio";
        elseif ($c === 'no_empleado')            $defs[] = "no_empleado BIGINT NULL";
        elseif ($c === 'edad')                   $defs[] = "edad INT NULL";
        elseif (in_array($c, $SV_CACHE_DOUBLE, true)) $defs[] = "$c DOUBLE NULL";
        elseif (in_array($c, ['observa','descrip','confirma'], true)) $defs[] = "$c TEXT NULL";
        else                                     $defs[] = "$c VARCHAR(255) NULL";
    }
    $sql = "CREATE TABLE carobra_2014.sv_cache (
                " . implode(",\n                ", $defs) . ",
                fec_traspaso_date DATE NULL,
                srm0_flag TINYINT NOT NULL DEFAULT 0,
                sfp_flag  TINYINT NOT NULL DEFAULT 1,
                KEY idx_fecha (fec_traspaso_date),
                KEY idx_folio (folio),
                KEY idx_sc (sc(100)),
                KEY idx_brocker (brocker(100)),
                KEY idx_gerencia (gerencia(100)),
                KEY idx_nap (nap),
                KEY idx_emp (no_empleado)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conecta->consulta($sql);
}

function svCacheEstado($conecta) {
    $bd = $conecta->consulta("SELECT refreshed_at, window_ini, window_fin, filas, segundos, estado, mensaje, iniciado_at,
                                     TIMESTAMPDIFF(MINUTE, refreshed_at, NOW()) AS edad_min
                              FROM carobra_2014.sv_cache_estado WHERE id=1");
    if (!$bd) return null;
    return mysqli_fetch_assoc($bd) ?: null;
}

/* Reconstruye la caché completa. Segura ante concurrencia (GET_LOCK) y
   atómica (tabla nueva + RENAME). Devuelve ['ok'=>bool,'msg'=>...]. */
function svCacheRefrescar($conecta) {

    @ignore_user_abort(true);
    @set_time_limit(0);

    $bd = $conecta->consulta("SELECT GET_LOCK('sv_cache_refresh', 0) AS l");
    $r  = $bd ? mysqli_fetch_assoc($bd) : null;
    if (!$r || intval($r['l']) !== 1) {
        return ['ok' => false, 'msg' => 'Ya hay un refresco en curso.'];
    }

    $t0 = time();
    try {
        svCacheAsegurarTablas($conecta);
        $conecta->consulta("UPDATE carobra_2014.sv_cache_estado
                            SET estado='refrescando', iniciado_at=NOW(), mensaje=NULL WHERE id=1");

        $conecta->consulta("DROP TABLE IF EXISTS carobra_2014.sv_cache_new");
        $conecta->consulta("CREATE TABLE carobra_2014.sv_cache_new LIKE carobra_2014.sv_cache");

        $anio = intval(date('Y'));
        $ini  = '01/01/' . ($anio - 1);
        $fin  = '31/12/' . $anio;

        $res = obtenerDatosReporte($conecta, [
            'filtro'      => 'Gerencia',
            'valor1'      => 'TODAS',
            'valor2'      => $ini,
            'valor3'      => $fin,
            'te'          => 'ec',
            'sfp'         => 0,
            'srm0'        => 0,
            'reg_esp'     => 2,       /* 2 = sin filtro de longitud de folio */
            'nivel'       => 12,
            'clave'       => 'cache',
            'nombre'      => 'cache',
            'cache_build' => true,
        ], 0);

        if (empty($res['ok'])) {
            throw new Exception($res['msg'] ?? 'Falló el armado de la caché.');
        }
        $err = mysqli_error($conecta->conexion);
        if ($err !== '') throw new Exception($err);

        /* Banderas para las "opciones adicionales" (sin fecha de pago / saldo real > 0) */
        $conecta->consulta("UPDATE carobra_2014.sv_cache_new c SET srm0_flag=1
                            WHERE EXISTS (SELECT 1 FROM carobra_2014.pagos pa
                                          WHERE pa.folio=c.folio AND IFNULL(pa.saldo_real,0)!=0)");
        $conecta->consulta("UPDATE carobra_2014.sv_cache_new c SET sfp_flag=0
                            WHERE EXISTS (SELECT 1 FROM carobra_2014.calculo_pago cp WHERE cp.folio=c.folio)
                              AND NOT EXISTS (SELECT 1 FROM carobra_2014.calculo_pago cp
                                              WHERE cp.folio=c.folio AND IFNULL(cp.fec_pago,'')='')");

        $bd = $conecta->consulta("SELECT COUNT(*) AS n FROM carobra_2014.sv_cache_new");
        $n  = $bd ? intval(mysqli_fetch_assoc($bd)['n']) : 0;
        if ($n === 0) throw new Exception('La caché nueva quedó vacía; se conserva la anterior.');

        /* Intercambio atómico */
        $conecta->consulta("DROP TABLE IF EXISTS carobra_2014.sv_cache_old");
        $ok = $conecta->consulta("RENAME TABLE carobra_2014.sv_cache TO carobra_2014.sv_cache_old,
                                               carobra_2014.sv_cache_new TO carobra_2014.sv_cache");
        if (!$ok) throw new Exception('No se pudo intercambiar la tabla: ' . mysqli_error($conecta->conexion));
        $conecta->consulta("DROP TABLE IF EXISTS carobra_2014.sv_cache_old");

        $seg = time() - $t0;
        $conecta->consulta("UPDATE carobra_2014.sv_cache_estado
                            SET estado='ok', refreshed_at=NOW(),
                                window_ini=STR_TO_DATE('$ini','%d/%m/%Y'), window_fin=STR_TO_DATE('$fin','%d/%m/%Y'),
                                filas=$n, segundos=$seg, mensaje=NULL WHERE id=1");
        $msg = "Caché lista: $n filas en {$seg}s.";
        $out = ['ok' => true, 'msg' => $msg, 'filas' => $n, 'segundos' => $seg];

    } catch (Exception $e) {
        $m = mysqli_real_escape_string($conecta->conexion, $e->getMessage());
        $conecta->consulta("DROP TABLE IF EXISTS carobra_2014.sv_cache_new");
        /* Si había una caché buena, se conserva: solo se anota el error. */
        $conecta->consulta("UPDATE carobra_2014.sv_cache_estado
                            SET estado=IF(refreshed_at IS NULL,'error','ok'), mensaje='$m' WHERE id=1");
        $out = ['ok' => false, 'msg' => $e->getMessage()];
    }

    $conecta->consulta("SELECT RELEASE_LOCK('sv_cache_refresh')");
    return $out;
}

/* ¿Se puede contestar esta consulta desde la caché?
   $fecIniYmd/$fecFinYmd en 'Y-m-d' (null para Folio). */
function svCacheUsable($conecta, $fecIniYmd, $fecFinYmd, $enVivo = false) {
    if ($enVivo) return false;
    $e = svCacheEstado($conecta);
    if (!$e || $e['estado'] === 'vacio' || $e['refreshed_at'] === null) return false;
    if (intval($e['edad_min']) > SV_CACHE_MAXIMA_MIN) return false;
    $bd = $conecta->consulta("SHOW TABLES FROM carobra_2014 LIKE 'sv_cache'");
    if (!$bd || $bd->num_rows === 0) return false;
    if ($fecIniYmd !== null) {
        if ($fecIniYmd < $e['window_ini'] || $fecFinYmd > $e['window_fin']) return false;
    }
    return true;
}

/* SELECT rápido contra la caché. $o:
   fec_ini/fec_fin (Y-m-d) | folio (LIKE) + cond_folio | where (fragmento
   " AND c...") | reg_esp | sfp | srm0 | limite */
function svCacheConsultar($conecta, $o, $limite) {
    global $SV_CACHE_COLS;

    $cols = 'c.' . implode(',c.', $SV_CACHE_COLS);
    $w = " WHERE 1=1";

    if (!empty($o['folio'])) {
        $f = mysqli_real_escape_string($conecta->conexion, $o['folio']);
        $w .= " AND c.folio LIKE '%$f%'" . ($o['cond_folio'] ?? '');
    } else {
        $i = mysqli_real_escape_string($conecta->conexion, $o['fec_ini']);
        $f = mysqli_real_escape_string($conecta->conexion, $o['fec_fin']);
        $w .= " AND c.fec_traspaso_date BETWEEN '$i' AND '$f'";
        if ($o['reg_esp'] == 1) $w .= " AND CHAR_LENGTH(c.folio) < 10";
        else                    $w .= " AND CHAR_LENGTH(c.folio) >= 10";
        $w .= $o['where'] ?? '';
        if ($o['sfp']  == 1) $w .= " AND c.sfp_flag=1";
        if ($o['srm0'] == 1) $w .= " AND c.srm0_flag=1";
    }

    $lim = max(1, intval($limite));
    $bd  = $conecta->consulta("SELECT $cols FROM carobra_2014.sv_cache c $w LIMIT $lim");
    if (!$bd) return null;   /* error -> que el llamador se vaya en vivo */

    $filas = [];
    while ($tb = mysqli_fetch_assoc($bd)) $filas[] = $tb;
    return $filas;
}

/* Dispara el refresco DESPUÉS de haberle contestado al usuario.
   Solo si hay fastcgi_finish_request (php-fpm); con otro SAPI el
   refresco queda a cargo del cron (sv_cache_refresh.php). */
function svCacheProgramarRefresco($conecta, $forzar = false) {
    static $programado = false;
    if ($programado) return;

    if (!$forzar) {
        $e = svCacheEstado($conecta);
        if ($e && $e['estado'] === 'refrescando' && $e['iniciado_at'] !== null) {
            /* si lleva menos de 30 min refrescando, no hacer nada */
            $bd = $conecta->consulta("SELECT TIMESTAMPDIFF(MINUTE, iniciado_at, NOW()) AS m FROM carobra_2014.sv_cache_estado WHERE id=1");
            $m  = $bd ? intval(mysqli_fetch_assoc($bd)['m']) : 0;
            if ($m < 30) return;
        }
        if ($e && $e['refreshed_at'] !== null && intval($e['edad_min']) < SV_CACHE_FRESCA_MIN) return;
    }
    if (!function_exists('fastcgi_finish_request')) return;

    $programado = true;
    register_shutdown_function(function () {
        @ignore_user_abort(true);
        @set_time_limit(0);
        /* Soltar la sesión: si no, el refresco bloquearía las demás
           pestañas/peticiones del mismo usuario mientras corre. */
        if (session_status() === PHP_SESSION_ACTIVE) { @session_write_close(); }
        @fastcgi_finish_request();
        try {
            $c = new conector();
            svCacheRefrescar($c);
        } catch (Throwable $t) { /* silencioso */ }
    });
}