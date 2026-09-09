<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    session_start();

    include("./funciones/encripta.php");
    require './vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Color;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    $conecta = new conector();
    $spreadsheet = new Spreadsheet();
    $spreadsheet->removeSheetByIndex(0);


    $sql = "";
    $titulo_hoja = "";
    $excepciones = [];


    $titulo_hoja="Clawback"; 

    
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';


    if (empty($fecha_inicio) || empty($fecha_fin)) {
        die("Por favor, proporciona ambas fechas: fecha_inicio y fecha_fin.");
    }

    if (!validateDate($fecha_inicio) || !validateDate($fecha_fin)) {
        die("Formato de fecha no válido. Por favor, utiliza el formato YYYY-MM-DD.");
    }

    if ($fecha_inicio > $fecha_fin) {
        die("La fecha de inicio no puede ser mayor que la fecha de fin.");
    }

    if($fecha_inicio != '' && $fecha_fin != ''){
        $where_sql = "WHERE 
            cl.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
            AND STR_TO_DATE(cl.fec_traspaso, '%d/%m/%Y') 
            BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }else{
        $where_sql = "";
    }

    
    $sql="SELECt 
        ifnull(ee.sc,een.sc) AS 'Socio Comercial',
        ifnull(ee.brocker,een.brocker) AS 'Broker',
        ifnull(ee.gerencia,een.gerencia) AS 'Gerencia',
        ifnull(bdc_us.nombre,bdc_usn.nombre) AS 'Comisionista',


        ANY_VALUE(cl.folio) AS 'Folio', ANY_VALUE(cl.nombre_Completo) AS 'Nombre Completo', ANY_VALUE(pd.CURP) AS 'CURP',  ANY_VALUE(cl.saldo) AS 'Saldo', 
        ANY_VALUE(cl.nap) AS 'NAP', ANY_VALUE(cl.fec_traspaso) AS 'Fec. Traspaso', 
        ANY_VALUE(pa.saldo_real) AS 'Saldo Real', ANY_VALUE(COALESCE(afr.afore, 'SIN AFORE')) AS 'Afore Receptora',
        ANY_VALUE(COALESCE(afc.afore, 'SIN AFORE')) AS 'Afore Cedente',  ANY_VALUE(es.estado) AS Estado,
        ANY_VALUE(cl.antiguedad) AS 'Antiguedad', ANY_VALUE(cl.semana) AS 'Semana', 
        ANY_VALUE(cl.mes_procesar) AS 'Mes de Procesamiento', ANY_VALUE(cl.cod_cuenta) AS 'Cod. Cuenta',

        ANY_VALUE(
            CASE 
                WHEN cl.ley IS NOT NULL AND cl.ley <> '' 
                THEN CONCAT('Ley ', cl.ley)
                ELSE ''
            END
        ) AS Ley,

        ANY_VALUE(
            CASE 
                WHEN cl.cancelacion = 0 THEN 'Baja del cliente'
                WHEN cl.cancelacion = 1 THEN 'Cliente Retenido'
                ELSE 'Sin estatus'
            END
        ) AS Cancelacion,
        ANY_VALUE(cl.fec_alta) AS 'Fec. Alta', 
        ANY_VALUE(cl.sol_traspaso) AS 'sol_traspaso', 
        ANY_VALUE(
            CASE 
                WHEN cl.pplus = 1 THEN 'SI'
                ELSE ''
            END
        ) AS 'Pplus'

        FROM clawback cl

        LEFT JOIN pagos pa 
            ON pa.folio = cl.folio

        LEFT JOIN produccion pr 
            ON pr.folio = cl.folio

        left join naps n 
            on (pr.nap=n.nap)

        LEFT JOIN estruc_empleados ee 
            ON ee.no_empleado = pr.no_empleado
        
        left join estruc_empleados een 
            on (n.no_empleado=een.no_empleado)

        LEFT JOIN prod_trabajador pd 
            ON pd.folio = cl.folio

        LEFT JOIN estados es
            ON es.no_edo = cl.estado

        LEFT JOIN afores afr
            ON afr.clave = cl.afo_receptora

        LEFT JOIN afores afc
            ON afc.clave = cl.afo_cedente

        LEFT JOIN carobra.usuarios bdc_us
            ON bdc_us.clave = ee.clave

        LEFT JOIN carobra.usuarios bdc_usn
            ON bdc_usn.clave = een.clave

        $where_sql

        ORDER BY 
        ifnull(ee.sc,een.sc),
        ifnull(ee.brocker,een.brocker),
        ifnull(ee.gerencia,een.gerencia),
        ifnull(bdc_us.nombre,bdc_usn.nombre)"
    ;
    $excepciones=['Folio', 'NAP', 'Cod. Cuenta'];

    cargarDatosEnHoja($spreadsheet,$sql,$titulo_hoja,$excepciones,[]);

    function cargarDatosEnHoja(Spreadsheet $spreadsheet, string $sql, string $tituloHoja, array $excepciones = [], array $num_text = [] ) 
    {
        global $conecta;
        $cols = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $bd = $conecta->consulta($sql);
        $rows = [];
        while ($tb = $bd->fetch_assoc()) 
        {
            $rows[] = $tb;
        }
        
        $filtered = array_values(array_filter($rows));
        if (empty($filtered)) return;

        $sheet = new Worksheet($spreadsheet, $tituloHoja);
        $spreadsheet->addSheet($sheet);

        $headers = array_keys($filtered[0]);
        

        foreach ($headers as $i => $h) 
        {
            $col = $cols[$i];
            $sheet->setCellValue($col . '1', htmlspecialchars($h));
        }

        $ncol = count($headers) - 1;
        $headerStyleArray = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF808080']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle($cols[0] . "1:" . $cols[$ncol] . "1")->applyFromArray($headerStyleArray);

        $fila = 2;
        $estilo_num = 0;
        $cols_num = [];
        $cols_num_text=[];
        
        foreach ($filtered as $row) 
        {
            $col = 1;
            foreach ($headers as $h) 
            {
                $raw = $row[$h] ?? '';
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

                if (in_array(strtolower($h), array_map('strtolower', $num_text)))
                {
                    //$val = (string)$raw;
                    $val = htmlspecialchars((string)$raw);
                    $sheet->setCellValueExplicit($columna . $fila, $val, DataType::TYPE_STRING);
                } 
                else
                {
                    $val = htmlspecialchars((string)$raw);
                    $sheet->setCellValue($columna . $fila, strval($val));
                }
                if ($estilo_num == 0 && is_numeric($val)) 
                {
                    $tit = strtolower($h);
                    if (!in_array($tit, array_map('strtolower', $excepciones))) 
                        $cols_num[] = $cols[$col - 1];
                    else
                        $cols_num_text[] = $cols[$col - 1];
                }
                $col++;
            }
            $fila++;
            $estilo_num = 1;
        }

        for ($col = 1; $col <= count($headers); $col++) 
        {
            $c = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        foreach ($cols_num as $colLetra) 
        {
            $sheet->getStyle($colLetra . "2:" . $colLetra . $fila)
                  ->getNumberFormat()
                  ->setFormatCode('"$"#,##0.00;[Red]\-"$"#,##0.00');
        }

        foreach ($cols_num_text as $colLetra) 
        {
            $sheet->getStyle($colLetra . "2:" . $colLetra . $fila)
                  ->getNumberFormat()
                  ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }
    }

    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    $spreadsheet->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="reporte.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>