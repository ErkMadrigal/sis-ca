<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=utf-8');

/* clas_carobra.php imprime HTML/comentarios al incluirse (pensado
   para páginas, no para un endpoint JSON); se atrapa con ob_ para
   que no se cuele antes del json_encode de las respuestas. */
ob_start();
require_once './funciones/clas_carobra.php';
ob_end_clean();

/* Igual que en otras APIs del sitio (ej. seg_ventas_reporteador_api.php):
   si algo truena feo (fatal error) con display_errors apagado, PHP
   corta la ejecución y el navegador recibe un body vacío -- eso se ve
   del lado del cliente como "Unexpected end of JSON input", sin
   ninguna pista de qué pasó. Este handler convierte esos fatales en
   una respuesta JSON válida con el detalle, para poder diagnosticar. */
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'ok'    => false,
            'msg'   => 'Error interno del servidor.',
            'debug' => $err['message'] . ' en ' . basename($err['file']) . ':' . $err['line'],
        ]);
    }
});

if (!isset($_SESSION['ultimoAcceso']) || !isset($_SESSION['autentifica'])) {
    echo json_encode(['ok' => false, 'msg' => 'Tu sesión ha caducado. Vuelve a iniciar sesión.']);
    exit;
}

$conecta = new conector();

$input  = json_decode(file_get_contents('php://input'), true);
$input  = is_array($input) ? $input : [];
$action = $input['action'] ?? '';

/* ════════════════════════════════════════════════════════════
   seg_movilidad_categorias_api.php

   Reporte de folios de la tabla `movilidad`, agrupados por
   categoria_cliente (Mayores / Cotizante / PPLUS Young / PPLUS
   Senior / Inactivo / sin categoría). Dos acciones:

     - listarResumen: una fila por categoría con el conteo de folios
       y el % de comisión de esa categoría.
     - listarFoliosCategoria: el detalle de folios de UNA categoría
       (se abre desde el botón "+" de cada fila del resumen).
════════════════════════════════════════════════════════════ */

/* ── % de comisión por categoría ──────────────────────────────
   PLACEHOLDER: el negocio todavía no definió la tasa real de
   comisión por categoría (pendiente de que la manden). Se deja
   hardcodeada aquí mismo para no bloquear la vista; en cuanto se
   defina la regla real, se reemplazan estos valores (o se mueven a
   una tabla de catálogo si van a cambiar seguido). */
$COMISION_POR_CATEGORIA = [
    'Mayores'      => 0,
    'Cotizante'    => 0,
    'PPLUS Young'  => 0,
    'PPLUS Senior' => 0,
    'Inactivo'     => 0,
];
/* Comisión para folios sin categoria_cliente capturada (fila en
   blanco en la tabla movilidad). */
$COMISION_SIN_CATEGORIA = 0;

/* Etiqueta a mostrar cuando categoria_cliente viene vacío/NULL --
   nunca se manda una fila con nombre en blanco a la pantalla. */
define('MOV_ETIQUETA_SIN_CATEGORIA', 'Sin categoría');

function resolverEtiquetaCategoria($categoria) {
    $categoria = trim((string) $categoria);
    return ($categoria === '') ? MOV_ETIQUETA_SIN_CATEGORIA : $categoria;
}

if ($action === 'listarResumen') {

    $query = "Select categoria_cliente, count(*) as no_folios
                from movilidad
                group by categoria_cliente
                order by no_folios desc";
    $bd = $conecta->consulta($query);

    $filas       = [];
    $totalFolios = 0;

    if ($bd) {
        while ($tb = mysqli_fetch_assoc($bd)) {
            $categoriaCruda = $tb['categoria_cliente'];
            $etiqueta       = resolverEtiquetaCategoria($categoriaCruda);
            $noFolios       = intval($tb['no_folios']);
            $totalFolios   += $noFolios;

            $pctComision = ($categoriaCruda === null || trim((string) $categoriaCruda) === '')
                ? $COMISION_SIN_CATEGORIA
                : ($COMISION_POR_CATEGORIA[$categoriaCruda] ?? 0);

            $filas[] = [
                /* 'categoria' es lo que se manda de vuelta a
                   listarFoliosCategoria para pedir el detalle -- va
                   crudo (puede ser '' o null-como-''), NO la etiqueta
                   ya traducida a "Sin categoría". */
                'categoria'     => $categoriaCruda ?? '',
                'etiqueta'      => $etiqueta,
                'no_folios'     => $noFolios,
                'pct_comision'  => $pctComision,
            ];
        }
    }

    /* % de participación sobre el total -- se calcula aquí (no viene
       de ninguna tabla) para que la vista pueda mostrar qué tan
       grande es cada categoría respecto al total de folios. */
    foreach ($filas as &$fila) {
        $fila['pct_participacion'] = ($totalFolios > 0)
            ? round(($fila['no_folios'] / $totalFolios) * 100, 1)
            : 0;
    }
    unset($fila);

    echo json_encode([
        'ok'           => true,
        'filas'        => $filas,
        'total_folios' => $totalFolios,
    ]);
    exit;
}

if ($action === 'listarFoliosCategoria') {

    /* Puede venir '' (folios sin categoria_cliente capturada) -- no
       se valida contra una lista fija de categorías porque
       categoria_cliente es texto libre en la tabla movilidad, no un
       catálogo cerrado; cualquier valor que exista ahí debe poder
       consultarse. */
    $categoria = $input['categoria'] ?? '';
    $categoria = trim((string) $categoria);

    if ($categoria === '') {
        $where = "where (categoria_cliente is null or trim(categoria_cliente) = '')";
    } else {
        $categoriaEsc = mysqli_real_escape_string($conecta->conexion, $categoria);
        $where = "where categoria_cliente = '$categoriaEsc'";
    }

    $limite = 3000;
    $query  = "Select folio, fec_captura, fec_sello, afore, movilidad, ley, salario,
                      pplus, cotiza, edad_cliente, categoria_cliente
                from movilidad
                $where
                order by fec_captura desc
                limit $limite";
    $bd = $conecta->consulta($query);

    $filas = [];
    if ($bd) {
        while ($tb = mysqli_fetch_assoc($bd)) {
            $filas[] = $tb;
        }
    }

    echo json_encode([
        'ok'        => true,
        'etiqueta'  => resolverEtiquetaCategoria($categoria),
        'filas'     => $filas,
        'limitado'  => (count($filas) >= $limite),
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);