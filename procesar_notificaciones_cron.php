<?php
/**
 * procesar_notificaciones_cron.php
 *
 * Revisa notificaciones con fecha_programada <= AHORA que no se han
 * enviado, y las manda por push (reutilizando enviar_push() de
 * carobra-api/send_push.php, igual que validacion_api.php).
 *
 * Configúralo en el Programador de Tareas de Windows:
 *   Programa:    C:\xampp\php\php.exe
 *   Argumentos:  C:\xampp\htdocs\CAROBRA\procesar_notificaciones_cron.php
 *   Repetir:     cada 1 minuto, indefinidamente
 *
 * (ajusta la ruta real de tu proyecto)
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/funciones/clas_carobra.php';
require_once __DIR__ . '/../carobra-api/send_push.php';

$conecta = new conector();

$rs = $conecta->consulta(
    "SELECT * FROM carobra_2014.notificaciones
     WHERE enviado = 0 AND estatus = 'programada' AND fecha_programada <= NOW()"
);

if (!$rs || mysqli_num_rows($rs) === 0) {
    echo "[" . date('Y-m-d H:i:s') . "] Sin notificaciones pendientes.\n";
    exit;
}

while ($notif = mysqli_fetch_assoc($rs)) {
    $id = intval($notif['id']);
    echo "[" . date('Y-m-d H:i:s') . "] Procesando notificación #{$id} — {$notif['titulo']}\n";

    $conecta->consulta("UPDATE carobra_2014.notificaciones SET estatus = 'enviando' WHERE id = {$id}");

    $rsDetalle = $conecta->consulta(
        "SELECT * FROM carobra_2014.notificaciones_detalle WHERE id_notificacion = {$id} AND estatus = 'pendiente'"
    );

    $enviados = 0;
    $fallidos = 0;

    if ($rsDetalle) {
        while ($d = mysqli_fetch_assoc($rsDetalle)) {
            $idDetalle = intval($d['id']);

            if (empty($d['token'])) {
                $conecta->consulta("UPDATE carobra_2014.notificaciones_detalle SET estatus = 'sin_token' WHERE id = {$idDetalle}");
                continue;
            }

            $datosExtra = ['id_notificacion' => $id];
            if (!empty($notif['adjunto_ruta'])) {
                $datosExtra['adjunto'] = 'https://www.sis-ca.com.mx:7863/afore/' . $notif['adjunto_ruta'];
            }

            try {
                $resultado = enviar_push($d['token'], $notif['titulo'], $notif['mensaje'], $datosExtra);

                $respEsc = mysqli_real_escape_string($conecta->conexion, json_encode($resultado));
                $conecta->consulta(
                    "UPDATE carobra_2014.notificaciones_detalle
                     SET estatus = 'enviado', respuesta = '{$respEsc}', enviado_en = NOW()
                     WHERE id = {$idDetalle}"
                );
                $enviados++;

            } catch (\Throwable $e) {
                $errEsc = mysqli_real_escape_string($conecta->conexion, $e->getMessage());
                $conecta->consulta(
                    "UPDATE carobra_2014.notificaciones_detalle
                     SET estatus = 'fallido', respuesta = '{$errEsc}'
                     WHERE id = {$idDetalle}"
                );
                $fallidos++;
            }
        }
    }

    $conecta->consulta(
        "UPDATE carobra_2014.notificaciones
         SET enviado = 1, enviado_en = NOW(), total_enviados = {$enviados}, total_fallidos = {$fallidos}, estatus = 'completada'
         WHERE id = {$id}"
    );

    echo "  -> {$enviados} enviados, {$fallidos} fallidos\n";
}