<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();
$conecta = new conector();

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   ACTION: buscarFoliosValidacion
   Lista folios capturados desde la app (con id_estatus_prod != 6)
   filtrando por estatus_validacion y/o folio/nombre.
════════════════════════════════════════════════════════════ */
if ($action === 'buscarFoliosValidacion') {

    $q      = trim($input['q']      ?? '');
    $filtro = trim($input['filtro'] ?? '0'); // '0' | '1' | '2' | '3' | 'TODOS'

    $where = ["p.id_estatus_prod != 6"];

    if ($filtro !== 'TODOS') {
        $filtroInt = intval($filtro);
        $where[] = "p.estatus_validacion = {$filtroInt}";
    } else {
        $where[] = "p.estatus_validacion IS NOT NULL";
    }

    if (strlen($q) >= 2) {
        $qEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($q));
        $where[] = "(p.folio LIKE '%{$qEsc}%' OR pt.trabajador LIKE '%{$qEsc}%')";
    }

    $whereSql = implode(' AND ', $where);

    $rs = $conecta->consulta(
        "SELECT
            p.folio, p.no_empleado, p.afo_cedente, p.estatus_validacion,
            p.nss, p.nap, p.fec_traspaso,
            pt.trabajador, pt.empresa, pt.email_trab, pt.celular, pt.cp,
            eu.nombre AS nombre_comisionista,
            (SELECT COUNT(*) FROM carobra_2014.evidencias e WHERE e.folio = p.folio) AS total_evidencias,
            ca.fecha_captura, ca.clave_usuario
         FROM carobra_2014.produccion p
         INNER JOIN carobra_2014.prod_trabajador pt ON pt.folio = p.folio
         LEFT JOIN carobra_2014.empl_usuarios eu ON eu.no_empleado = p.no_empleado
         LEFT JOIN carobra_2014.capturas_app ca ON ca.folio = p.folio
         WHERE {$whereSql}
         ORDER BY ca.fecha_captura DESC
         LIMIT 100"
    );

    $datos = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $datos[] = [
                'folio'               => $row['folio'],
                'trabajador'          => utf8_encode($row['trabajador'] ?? ''),
                'empresa'             => utf8_encode($row['empresa'] ?? ''),
                'email'               => $row['email_trab'] ?? '',
                'celular'             => $row['celular'] ?? '',
                'cp'                  => $row['cp'] ?? '',
                'nss'                 => $row['nss'] ?? '',
                'nap'                 => $row['nap'] ?? '',
                'fec_traspaso'        => $row['fec_traspaso'] ?? '',
                'no_empleado'         => $row['no_empleado'],
                'nombre_comisionista' => utf8_encode($row['nombre_comisionista'] ?? ''),
                'afo_cedente'         => $row['afo_cedente'],
                'estatus_validacion'  => $row['estatus_validacion'],
                'total_evidencias'    => intval($row['total_evidencias']),
                'fecha_captura'       => $row['fecha_captura'],
                'clave_usuario'       => $row['clave_usuario'],
            ];
        }
    }

    echo json_encode(['ok' => true, 'datos' => $datos]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: getEvidenciasFolio
   Lista las evidencias (PDF/foto) subidas para un folio.
════════════════════════════════════════════════════════════ */
if ($action === 'getEvidenciasFolio') {

    $folio = trim($input['folio'] ?? '');
    if (empty($folio)) {
        echo json_encode(['ok' => false, 'msg' => 'Folio inválido.']);
        exit;
    }

    $folioEsc = mysqli_real_escape_string($conecta->conexion, $folio);

    $rs = $conecta->consulta(
        "SELECT id, nombre, tipo, ruta, tamanio, creado_en
         FROM carobra_2014.evidencias
         WHERE folio = '{$folioEsc}'
         ORDER BY creado_en DESC"
    );

    $evidencias = [];
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $evidencias[] = [
                'id'        => intval($row['id']),
                'nombre'    => $row['nombre'],
                'tipo'      => $row['tipo'],
                'url' => 'https://www.sis-ca.com.mx:7863/afore/' . $row['ruta'],
                'tamanio'   => intval($row['tamanio']),
                'creado_en' => $row['creado_en'],
            ];
        }
    }

    echo json_encode(['ok' => true, 'evidencias' => $evidencias]);
    exit;
}

/* ════════════════════════════════════════════════════════════
   ACTION: cambiarEstatusValidacion
   Actualiza el estatus_validacion en produccion para un folio.

   Estatus vigentes (se conservan los valores numéricos 0/1/2 ya
   guardados en la BD para no reinterpretar historial existente;
   "Comentarios – Revisa App" es un valor nuevo, 3, agregado al final):
     0 = Enviado a revisión   (antes "En revisión")      -- GRIS
     1 = Revisado OK          (antes "Revisado")          -- AMARILLO
     2 = Captura Exitosa      (antes "Evidencias OK")     -- VERDE
     3 = Comentarios – Revisa App (nuevo)                 -- ROJO
════════════════════════════════════════════════════════════ */
if ($action === 'cambiarEstatusValidacion') {

    $folio  = trim($input['folio']   ?? '');
    $nuevo  = trim($input['estatus'] ?? '');

    $permitidos = ['0', '1', '2', '3'];

    if (empty($folio) || !in_array($nuevo, $permitidos)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos inválidos.']);
        exit;
    }

    $folioEsc  = mysqli_real_escape_string($conecta->conexion, $folio);
    $nuevoInt  = intval($nuevo);

    $ok = $conecta->consulta(
        "UPDATE carobra_2014.produccion SET estatus_validacion = {$nuevoInt} WHERE folio = '{$folioEsc}'"
    );

    if (!$ok) {
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar: ' . $conecta->conexion->error]);
        exit;
    }

    // ─── Notificación push al comisionista del folio ──────────────────
    $etiquetas = [
        '0' => 'Enviado a revisión',
        '1' => 'Revisado OK ✓',
        '2' => 'Captura Exitosa ✅',
        '3' => 'Comentarios - Revisa App ⚠️',
    ];

    // Obtener no_empleado del comisionista desde capturas_app
    $cap = $conecta->consulta(
        "SELECT no_empleado FROM carobra_2014.capturas_app WHERE folio = '{$folioEsc}' LIMIT 1"
    );
    $cap_row = $cap ? mysqli_fetch_assoc($cap) : null;

    if ($cap_row && !empty($cap_row['no_empleado'])) {
        $noEmp = intval($cap_row['no_empleado']);

        // Buscar token push del comisionista
        $rsToken = $conecta->consulta(
            "SELECT token FROM carobra_2014.push_tokens WHERE no_empleado = {$noEmp} LIMIT 1"
        );
        $rowToken = $rsToken ? mysqli_fetch_assoc($rsToken) : null;

        if ($rowToken && !empty($rowToken['token'])) {
            require_once __DIR__ . '/carobra-api/send_push.php';

            $titulo  = 'Carobra HelpiA';
            $mensaje = "Folio {$folio}: " . ($etiquetas[$nuevo] ?? 'Estatus actualizado');

            enviar_push($rowToken['token'], $titulo, $mensaje, [
                'folio'   => $folio,
                'estatus' => $nuevo,
            ]);
        }
    }

    echo json_encode(['ok' => true, 'msg' => 'Estatus actualizado correctamente.']);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);