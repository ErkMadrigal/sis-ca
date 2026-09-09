<?php 

$where = [];
$params = [];
$campos = "";

// Sanitizar entradas
$sc       = isset($_POST['sc']) ? trim($_POST['sc']) : '';
$brocker  = isset($_POST['brocker']) ? trim($_POST['brocker']) : '';
$gerencia = isset($_POST['gerencia']) ? trim($_POST['gerencia']) : '';

// ==========================
// ARMADO DE CAMPOS DINÁMICO
// ==========================

if ($sc !== '') {

    $campos = "ato.sc, 
               ato.venta_sc AS 'Acumulado transfer SC', 
               av.venta_sc AS 'Venta Acumulada SC',

               ato.brocker, 
               ato.venta_sb AS 'Acumulado transfer Broker', 
               av.venta_sb AS 'Venta Acumulada Broker',

               ato.gerencia, 
               ato.venta_ger AS 'Acumulado transfer Gerencia', 
               av.venta_ger AS 'Venta Acumulada Gerencia',

               ato.no_empleado, 
               ato.asesor,

               ato.total_saldo_real AS 'Total Acumulado transfer Out', 
               av.total_saldo_real AS 'Total Acumulado Venta'";

    $where[] = "ato.sc = ?";
    $params[] = $sc;
}

if ($brocker !== '') {

    $campos = "ato.sc,

               ato.brocker, 
               ato.venta_sb AS 'Acumulado transfer Broker', 
               av.venta_sb AS 'Venta Acumulada Broker',

               ato.gerencia, 
               ato.venta_ger AS 'Acumulado transfer Gerencia', 
               av.venta_ger AS 'Venta Acumulada Gerencia',

               ato.no_empleado, 
               ato.asesor,

               ato.total_saldo_real AS 'Total Acumulado transfer Out', 
               av.total_saldo_real AS 'Total Acumulado Venta'";

    $where[] = "ato.brocker = ?";
    $params[] = $brocker;
}

if ($gerencia !== '') {

    $campos = "ato.sc, 
               ato.brocker,

               ato.gerencia, 
               ato.venta_ger AS 'Acumulado transfer Gerencia', 
               av.venta_ger AS 'Venta Acumulada Gerencia',

               ato.no_empleado, 
               ato.asesor,

               ato.total_saldo_real AS 'Total Acumulado transfer Out', 
               av.total_saldo_real AS 'Total Acumulado Venta'";

    $where[] = "ato.gerencia = ?";
    $params[] = $gerencia;
}

// ==========================
// SI NO HAY FILTROS → TODO
// ==========================
if ($campos === "") {
    $campos = "ato.sc, 
               ato.venta_sc AS 'Acumulado transfer SC', 
               av.venta_sc AS 'Venta Acumulada SC',

               ato.brocker, 
               ato.venta_sb AS 'Acumulado transfer Broker', 
               av.venta_sb AS 'Venta Acumulada Broker',

               ato.gerencia, 
               ato.venta_ger AS 'Acumulado transfer Gerencia', 
               av.venta_ger AS 'Venta Acumulada Gerencia',

               ato.no_empleado, 
               ato.asesor,

               ato.total_saldo_real AS 'Total Acumulado transfer Out', 
               av.total_saldo_real AS 'Total Acumulado Venta'";
}

// ==========================
// ARMAR WHERE FINAL
// ==========================
$where_sql = "";
if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

// ==========================
// QUERY FINAL
// ==========================
$query = "SELECT $campos
          FROM acum_tranfer_out ato 
          LEFT JOIN acum_ventas av 
            ON av.no_empleado = ato.no_empleado
          $where_sql";

?>