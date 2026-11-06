<?php
/* ════════════════════════════════════════════════════════════
   seg_ventas_reporteador_excel.php

   Exporta a Excel el mismo reporte que arma seg_ventas_reporteador.php,
   con SOLO las columnas que el usuario eligió. Se abre en pestaña
   nueva con los parámetros del formulario ya serializados (mismo
   truco que usa el botón "Excel" de seg_ventas.php).

   Usa la misma función compartida (obtenerDatosReporte) que la
   vista en pantalla, así que la lógica de negocio no se duplica.
════════════════════════════════════════════════════════════ */

session_start();

if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
    header("Location: ./index.php");
    exit;
}

/* Igual que en seg_ventas_reporteador_api.php: descartamos cualquier
   salida directa de clas_carobra.php para que no se meta antes de los
   header() de descarga ni dentro del archivo .xls. */
ob_start();
include('./funciones/clas_carobra.php');
ob_end_clean();

require_once('./seg_ventas_reporteador_funciones.php');

$conecta = new conector();

$usuario    = $_SESSION["nombre"] ?? '';
$nivelReal  = nivel_usuario(utf8_decode($usuario));

$query = "Select clave from usuarios where nombre='" . mysqli_real_escape_string($conecta->conexion, $usuario) . "'";
$bd = $conecta->consulta($query);
$tb = $bd ? mysqli_fetch_array($bd) : null;
$clave = $tb['clave'] ?? '';

/* Esta vista se abre vía GET (form serializado con .serialize()). */
$filtro   = $_GET['filtro']   ?? '';
$valor1   = $_GET['valor1']   ?? '';
$valor2   = $_GET['valor2']   ?? '';
$valor3   = $_GET['valor3']   ?? '';
$te       = $_GET['te']       ?? '';
$sfp      = $_GET['sfp']      ?? 0;
$srm0     = $_GET['srm0']     ?? 0;
$reg_esp  = $_GET['reg_esp']  ?? 0;
$anio_ini = $_GET['anio_ini'] ?? '';
$anio_fin = $_GET['anio_fin'] ?? '';
$mes_i    = $_GET['mes_i']    ?? '';
$mes_f    = $_GET['mes_f']    ?? '';

$camposSeleccionGet = isset($_GET['campos_csv']) ? array_filter(explode(',', $_GET['campos_csv'])) : [];
$camposSeleccion    = filtrarCamposValidos($camposSeleccionGet, camposVisiblesParaUsuario($conecta, $nivelReal, $clave, $CAMPOS_REPORTE));
if (empty($camposSeleccion)) {
    $camposSeleccion = $CAMPOS_DEFAULT;
}

/* Blindaje por nivel -- esta vista se abre por GET (parámetros a la
   vista, fáciles de editar a mano en la URL), así que hay que validar
   igual o más estricto que en seg_ventas_reporteador_api.php: nunca hay
   que confiar solo en que la pantalla lo oculte/bloquee. "Folio" es una
   búsqueda puntual y siempre está permitida, sin importar el nivel. */
if (!in_array($filtro, array_merge(['Folio'], filtrosPermitidosParaNivel($nivelReal)), true)) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<p style="font-family:sans-serif;">No tienes permiso para exportar este filtro.</p>';
    exit;
}
if (filtroEsFijoParaNivel($nivelReal, $filtro)) {
    $valor1 = resolverValorFiltroFijo($conecta, $nivelReal, $usuario, $filtro);
} else {
    /* Mismo blindaje de ámbito jerárquico que seg_ventas_reporteador_api.php
       -- esta vista se abre por GET, más fácil todavía de editar a mano
       en la URL, así que hay que validar igual o más estricto. */
    $ambitoExport = resolverAmbitoJerarquico($conecta, $nivelReal, $usuario);
    if (hayAmbitoJerarquico($ambitoExport)) {
        refrescarEstrucEmpleados($conecta);
        if (!valorDentroDeAmbito($conecta, $ambitoExport, $filtro, $valor1)) {
            header('Content-Type: text/html; charset=UTF-8');
            echo '<p style="font-family:sans-serif;">Ese valor no pertenece a tu propia estructura comercial.</p>';
            exit;
        }
    }
}

/* Mismo blindaje que seg_ventas_reporteador_api.php: "Opciones
   adicionales" (sfp/srm0/reg_esp) solo para admin y Finanzas -- esta
   vista se abre por GET, así que hay que revalidar aquí también. */
$puedeOpciones = puedeVerOpcionesAdicionales($conecta, $nivelReal, $clave);
if (!$puedeOpciones) {
    $sfp = 0; $srm0 = 0; $reg_esp = 0;
}

$resultado = obtenerDatosReporte($conecta, [
    'filtro'   => $filtro,
    'valor1'   => $valor1,
    'valor2'   => $valor2,
    'valor3'   => $valor3,
    'te'       => $te,
    'sfp'      => $sfp,
    'srm0'     => $srm0,
    'reg_esp'  => $reg_esp,
    'anio_ini' => $anio_ini,
    'anio_fin' => $anio_fin,
    'mes_i'    => $mes_i,
    'mes_f'    => $mes_f,
    /* FIX DE SEGURIDAD: aquí se mandaba 'nivel' => 0 fijo (bug
       preexistente) en vez de $nivelReal -- inofensivo mientras
       obtenerDatosReporte() no usaba $nivel para nada, pero ahora
       condicionFolioParaNivel() sí lo necesita para restringir la
       exportación de Folio a la propia estructura del usuario (ver
       seg_ventas_reporteador_funciones.php). Con 0 fijo, hasta un
       admin real habría quedado bloqueado por el "nivel no
       reconocido -> niega todo" de esa función. */
    'nivel'    => $nivelReal,
    'clave'    => $clave,
    'nombre'   => $usuario,
], 20000);

if (!$resultado['ok']) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<p style="font-family:sans-serif;">' . htmlspecialchars($resultado['msg']) . '</p>';
    exit;
}

$nombreArchivo = 'reporte_ventas_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Pragma: public');
header('Cache-Control: max-age=0');

echo "\xEF\xBB\xBF"; // BOM para que Excel abra bien los acentos
?>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table border="1">
    <thead>
        <tr>
            <?php foreach ($camposSeleccion as $key): ?>
                <th><?= htmlspecialchars($CAMPOS_REPORTE[$key]['label']) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($resultado['filas'])): ?>
            <tr><td colspan="<?= count($camposSeleccion) ?>">Sin resultados para estos filtros.</td></tr>
        <?php else: ?>
            <?php foreach ($resultado['filas'] as $fila): ?>
                <tr>
                    <?php foreach ($camposSeleccion as $key):
                        $valor = $fila[$key] ?? '';
                        if (in_array($key, $CAMPOS_MONEDA, true) && $valor !== '') {
                            $valor = number_format(floatval($valor), 2);
                        }
                    ?>
                        <td><?= htmlspecialchars($valor) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>