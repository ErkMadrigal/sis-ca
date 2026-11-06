<?php
/* ════════════════════════════════════════════════════════════
   sv_cache_refresh.php -- refresca la caché del Reporteador de
   Ventas desde la línea de comandos (cron). Solo CLI.

   Cron recomendado (cada 10 minutos; ajusta la ruta y el binario de php):
       cada10min * * * *  cd /ruta/del/sitio && php sv_cache_refresh.php >> /tmp/sv_cache.log 2>&1
   (en el crontab real, cada10min se escribe como asterisco, diagonal y 10)

   Manual:   php sv_cache_refresh.php
════════════════════════════════════════════════════════════ */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Solo por línea de comandos.\n");
}

chdir(__DIR__);
ob_start();
include('./funciones/clas_carobra.php');
ob_end_clean();
require_once('./seg_ventas_reporteador_funciones.php');

$conecta = new conector();
$r = svCacheRefrescar($conecta);
echo date('Y-m-d H:i:s') . ' - ' . ($r['ok'] ? 'OK  ' : 'FAIL') . ' ' . $r['msg'] . "\n";
exit($r['ok'] ? 0 : 1);