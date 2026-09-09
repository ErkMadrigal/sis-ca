<?php
/* ════════════════════════════════════════════════════════════
   seg_reporteador_excel.php

   Exporta a Excel el mismo reporte que arma seg_reporteador.php,
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

include('./funciones/clas_carobra.php');
require_once('./reporteador_funciones.php');

$conecta = new conector();

$usuario = $_SESSION["nombre"] ?? '';

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
$camposSeleccion    = filtrarCamposValidos($camposSeleccionGet, $CAMPOS_REPORTE);
if (empty($camposSeleccion)) {
    $camposSeleccion = $CAMPOS_DEFAULT;
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
    'nivel'    => 0,
    'clave'    => $clave,
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