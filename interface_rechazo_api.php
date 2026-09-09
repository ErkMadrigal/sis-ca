<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   Carga de rechazos del Comparador a partir de un xlsx con
   columnas "Folio", "Diagnostico TramiteClasificados en orden
   ascendente" (id) y "Descripcion Rechazo" (descripcion). El
   frontend parsea el xlsx con SheetJS y manda un arreglo
   [{folio, id, descripcion}, ...] (una fila por renglón del xlsx,
   SIN deduplicar).

   Con eso se hacen DOS cosas:

   1) CATÁLOGO (tabla rechazo_comp): se deduplica por id (se usa la
      última descripción encontrada para cada id) y se hace UPSERT
      -> si el id no existía se crea, si ya existía se actualiza su
      descripción.

   2) FOLIOS (tabla prod_rechazos_v2): por CADA fila del xlsx (sin
      deduplicar, ya que cada folio es un caso distinto) se busca
      prod_rechazos_v2.folio y se actualiza
      prod_rechazos_v2.id_rechazo_comp = <id>.

   'previsualizarRechazoComp' NO escribe nada (solo clasifica).
   'aplicarRechazoComp' ejecuta los cambios de verdad.
════════════════════════════════════════════════════════════ */

function normalizarTexto($v) {
    return trim((string) $v);
}

/* ─── Catálogo (rechazo_comp) ──────────────────────────────── */

function cargarIdsExistentesCatalogo($conecta) {
    $ids = [];
    $rs = $conecta->consulta("SELECT id FROM rechazo_comp");
    if ($rs) {
        while ($row = mysqli_fetch_assoc($rs)) {
            $ids[$row['id']] = true;
        }
    }
    return $ids;
}

function resolverFilaCatalogo($conecta, $id, $descripcion, &$idsExistentes, $aplicar) {

    $fila = [
        'id'          => $id,
        'descripcion' => $descripcion,
        'estatus'     => '',
        'motivo'      => '',
    ];

    if ($id === '') {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Fila sin id (Diagnostico TramiteClasificados).';
        return $fila;
    }

    if ($descripcion === '') {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Fila sin descripción de rechazo.';
        return $fila;
    }

    $yaExiste = isset($idsExistentes[$id]);

    if (!$aplicar) {
        $fila['estatus'] = $yaExiste ? 'actualizado' : 'nuevo';
        $fila['motivo']  = $yaExiste
            ? "Se actualizaría la descripción del id {$id}."
            : "Se crearía el id {$id} en el catálogo.";
        return $fila;
    }

    $idEsc   = mysqli_real_escape_string($conecta->conexion, $id);
    $descEsc = mysqli_real_escape_string($conecta->conexion, $descripcion);

    $ok = $conecta->consulta("
        INSERT INTO rechazo_comp (id, descripcion)
        VALUES ('{$idEsc}', '{$descEsc}')
        ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion)
    ");

    if (!$ok) {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Error al guardar: ' . mysqli_error($conecta->conexion);
        return $fila;
    }

    $fila['estatus'] = $yaExiste ? 'actualizado' : 'nuevo';
    $fila['motivo']  = $yaExiste
        ? "Descripción actualizada para el id {$id}."
        : "Id {$id} agregado al catálogo.";
    $idsExistentes[$id] = true;
    return $fila;
}

function procesarCatalogo($conecta, $filasInput, $aplicar) {
    /* Deduplica por id, quedándose con la última descripción vista
       para cada id (igual que si el usuario hubiera capturado ese
       id varias veces: gana la última). */
    $porId = [];
    foreach ($filasInput as $f) {
        $id = normalizarTexto($f['id'] ?? '');
        if ($id === '') continue;
        $porId[$id] = normalizarTexto($f['descripcion'] ?? '');
    }

    $idsExistentes = cargarIdsExistentesCatalogo($conecta);

    $resultado = [];
    $conteo = ['nuevo' => 0, 'actualizado' => 0, 'error' => 0];

    foreach ($porId as $id => $descripcion) {
        $fila = resolverFilaCatalogo($conecta, $id, $descripcion, $idsExistentes, $aplicar);
        $resultado[] = $fila;
        if (isset($conteo[$fila['estatus']])) $conteo[$fila['estatus']]++;
    }

    return [$resultado, $conteo];
}

/* ─── Folios (prod_rechazos_v2) ────────────────────────────── */

function resolverFilaFolio($conecta, $folio, $id, $aplicar) {

    $fila = [
        'folio'   => $folio,
        'id'      => $id,
        'estatus' => '',
        'motivo'  => '',
    ];

    if ($folio === '') {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Fila sin folio.';
        return $fila;
    }

    if ($id === '') {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Fila sin id de rechazo.';
        return $fila;
    }

    $folioEsc = mysqli_real_escape_string($conecta->conexion, $folio);

    $rs = $conecta->consulta("SELECT folio FROM prod_rechazos_v2 WHERE folio = '{$folioEsc}' LIMIT 1");
    $existe = $rs && mysqli_fetch_assoc($rs);

    if (!$existe) {
        $fila['estatus'] = 'no_encontrado';
        $fila['motivo']  = "Folio {$folio} no existe en prod_rechazos_v2.";
        return $fila;
    }

    if (!$aplicar) {
        $fila['estatus'] = 'actualizado';
        $fila['motivo']  = "Se actualizaría id_rechazo_comp = {$id} en folio {$folio}.";
        return $fila;
    }

    $idEsc = mysqli_real_escape_string($conecta->conexion, $id);
    $ok = $conecta->consulta("UPDATE prod_rechazos_v2 SET id_rechazo_comp = '{$idEsc}' WHERE folio = '{$folioEsc}'");

    if (!$ok) {
        $fila['estatus'] = 'error';
        $fila['motivo']  = 'Error al actualizar: ' . mysqli_error($conecta->conexion);
        return $fila;
    }

    $fila['estatus'] = 'actualizado';
    $fila['motivo']  = "id_rechazo_comp actualizado a {$id} en folio {$folio}.";
    return $fila;
}

function procesarFolios($conecta, $filasInput, $aplicar) {
    $resultado = [];
    $conteo = ['actualizado' => 0, 'no_encontrado' => 0, 'error' => 0];

    foreach ($filasInput as $f) {
        $folio = normalizarTexto($f['folio'] ?? '');
        $id    = normalizarTexto($f['id'] ?? '');

        $fila = resolverFilaFolio($conecta, $folio, $id, $aplicar);
        $resultado[] = $fila;
        if (isset($conteo[$fila['estatus']])) $conteo[$fila['estatus']]++;
    }

    return [$resultado, $conteo];
}

/* ════════════════════════════════════════════════════════════
   ACTIONS
════════════════════════════════════════════════════════════ */
if ($action === 'previsualizarRechazoComp' || $action === 'aplicarRechazoComp') {

    $aplicar = ($action === 'aplicarRechazoComp');

    $filasInput = $input['filas'] ?? [];
    if (!is_array($filasInput)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de filas inválido.']);
        exit;
    }

    [$catalogoFilas, $catalogoConteo] = procesarCatalogo($conecta, $filasInput, $aplicar);
    [$folioFilas, $folioConteo]       = procesarFolios($conecta, $filasInput, $aplicar);

    echo json_encode([
        'ok'       => true,
        'catalogo' => ['filas' => $catalogoFilas, 'conteo' => $catalogoConteo],
        'folios'   => ['filas' => $folioFilas,   'conteo' => $folioConteo],
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);