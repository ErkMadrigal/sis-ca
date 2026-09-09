<?php

/* ============================================================
   SECCIÓN 1: INCLUDES Y DEPENDENCIAS
   ============================================================ */
include("./funciones/encripta.php");

if (file_exists(__DIR__ . '/vendor/autoload.php'))
    include './vendor/autoload.php';
else
{
    include './PHPExcel/IOFactory.php';
    if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory') && class_exists('PHPExcel_IOFactory'))
        class_alias('PHPExcel_IOFactory', 'PhpOffice\\PhpSpreadsheet\\IOFactory');
    if (!class_exists('PhpOffice\\PhpSpreadsheet\\Shared\\Date') && class_exists('PHPExcel_Shared_Date'))
        class_alias('PHPExcel_Shared_Date', 'PhpOffice\\PhpSpreadsheet\\Shared\\Date');
}

use PhpOffice\PhpSpreadsheet\IOFactory;

/* ============================================================
   SECCIÓN 2: CONEXIÓN Y RECEPCIÓN DEL ARCHIVO
   ============================================================ */
$conecta = new conector();

$archivo     = $_FILES['subir-archivo']['name'];
$archivo_tem = $_FILES['subir-archivo']['tmp_name'];

if (strlen($archivo) > 0)
{
    if (move_uploaded_file($archivo_tem, "./produccion/" . $archivo))
    {
        try
        {
            $archivo     = "./produccion/" . $archivo;
            $spreadsheet = IOFactory::load($archivo);
            $hoja        = $spreadsheet->getActiveSheet();

            /* ============================================================
               SECCIÓN 3: LECTURA DE ENCABEZADOS
               ============================================================ */
            $encabezados = [];
            foreach ($hoja->getRowIterator(1, 1) as $fila)
            {
                foreach ($fila->getCellIterator() as $celda)
                {
                    $valor = trim($celda->getValue());
                    if (!empty($valor))
                        $encabezados[$valor] = $celda->getColumn();
                }
            }

            // Validar que existan las columnas requeridas
            $cols = ['Folio', 'Motivo de rechazo', 'Cliente P+'];
            foreach ($cols as $columna)
            {
                if (!isset($encabezados[$columna]))
                    throw new Exception("No se encontró la columna: $columna");
            }

            /* ============================================================
               SECCIÓN 4: PROCESO FILA POR FILA
               ============================================================ */
            $numeroFilas    = $hoja->getHighestRow();
            $actualizados   = 0;
            $rechazados     = 0;
            $debug        = [];

            for ($fila = 2; $fila <= $numeroFilas; $fila++)
            {
                
                $folio          = trim($hoja->getCell($encabezados['Folio']            . $fila)->getValue());
                $clientePplus  = strtoupper(trim($hoja->getCell($encabezados['Cliente P+']        . $fila)->getValue()));
                $motivoRechazo = trim($hoja->getCell($encabezados['Motivo de rechazo'] . $fila)->getValue());
                            
                if ($motivoRechazo === '0') $motivoRechazo = '';

                // $debug[] = [
                //     "folio"         => $folio,
                //     "clientePplus"  => $clientePplus,
                //     "motivoRechazo" => $motivoRechazo,
                //     "largo_pplus"   => strlen($clientePplus),   // <- si marca más de 2 es que trae algo raro
                //     "hex_pplus"     => bin2hex($clientePplus),  // <- muestra el valor exacto en hex
                //     "regla"         => ($clientePplus === 'SI' && $motivoRechazo === '') ? "pagos" : "prod_rechazos"
                // ];

                // Saltar filas sin folio
                if (!$folio) continue;

                $folio         = addslashes($folio);
                $clientePplus  = strtoupper($clientePplus);

                /* ------------------------------------------------------------
                   REGLA 1: Cliente P+ = SI y sin Motivo de rechazo
                   → update pagos set pplus = 1
                   ------------------------------------------------------------ */
                if ($clientePplus === 'SI' && $motivoRechazo === '')
                {
                    $sql = "INSERT INTO pagos (folio, fec_pago, saldo_real, por_comision, comision, pplus) VALUES ('$folio', '', 0, 0, 0, 1) ON DUPLICATE KEY UPDATE pplus = 1";
                    
                    $conecta->consulta($sql);
                    //  $debug[] = $sql;
                    $actualizados++;
                }

                /* ------------------------------------------------------------
                   REGLA 2: Tiene Motivo de rechazo (aunque sea SI en Cliente P+)
                             O Cliente P+ = NO
                   → update prod_rechazos set pplus = 0
                   ------------------------------------------------------------ */
                elseif ($motivoRechazo !== '' || $clientePplus === 'NO')
                {
                    $sql = "UPDATE prod_rechazos SET pplus = 0 WHERE folio = '$folio'";
                    $conecta->consulta($sql);
                    // $debug[] = $sql;

                    $rechazados++;
                }
            }

            /* ============================================================
               SECCIÓN 5: RESPUESTA AL FRONTEND
               ============================================================ */
            header('Content-Type: application/json');

            $mensaje = "Archivo procesado correctamente.\n"
                    . "Registros aprobados (pplus = 1): $actualizados\n"
                    . "Registros rechazados (pplus = 0): $rechazados";

            echo json_encode([
                "ok"          => true,
                "mensaje"     => $mensaje,
                "aprobados"   => $actualizados,
                "rechazados"  => $rechazados,
                "debug" => $debug
            ]);

        }
        catch (Exception $e)
        {
            header('Content-Type: application/json');
            echo json_encode([
                "ok"      => false,
                "mensaje" => $e->getMessage()
            ]);
        }
    }
}