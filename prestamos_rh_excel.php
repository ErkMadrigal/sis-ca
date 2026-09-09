<?php
    include("./funciones/encripta.php");
    require './vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Color;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    $conecta = new conector();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setTitle('Prestamos');

    $sheet->setCellValue('A1', 'SC');
    $sheet->setCellValue('B1', 'Brocker');
    $sheet->setCellValue('C1', 'Gerencia');
    $sheet->setCellValue('D1', 'Asesor');
    $sheet->setCellValue('E1', 'ID préstamo');
    $sheet->setCellValue('F1', 'Fec. prestamo');
    $sheet->setCellValue('G1', 'Período');
    $sheet->setCellValue('H1', 'Motivo');
    $sheet->setCellValue('I1', 'Monto');
    $sheet->setCellValue('J1', 'No. pagos');
    $sheet->setCellValue('K1', 'Monto de pago');
    $sheet->setCellValue('L1', 'Pagos realizados');
    $sheet->setCellValue('M1', 'Pagado');
    $sheet->setCellValue('N1', 'Pendiente');
    $sheet->setCellValue('O1', 'Estatus');
    $sheet->setCellValue('P1', 'Observaciones');


    $headerStyleArray = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF808080'],
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    $reg_sombreado = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFEAEAEA'],
        ],
    ];

    $sheet->getStyle('A1:P1')->applyFromArray($headerStyleArray);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);
    $sheet->getColumnDimension('J')->setAutoSize(true);
    $sheet->getColumnDimension('K')->setAutoSize(true);
    $sheet->getColumnDimension('L')->setAutoSize(true);
    $sheet->getColumnDimension('M')->setAutoSize(true);
    $sheet->getColumnDimension('N')->setAutoSize(true);
    $sheet->getColumnDimension('O')->setAutoSize(true);
    $sheet->getColumnDimension('P')->setAutoSize(true);

    $query="with
                insuficiencias as (Select clave,ajuste from insuficiencias where fec_pago='' and fecha!=''),
                prestamos as (Select clave_empleado,ee.sc,ee.brocker,ee.gerencia,eu.nombre AS asesor,p.id_prestamos,
                                     p.fec_prestamo,p.periodo,p.monto,p.no_pagos,motivo,p.monto_pago,
                                     COUNT(CASE WHEN pp.fec_pago IS NOT NULL AND pp.fec_pago != '' 
                                                    THEN 1 END) AS semanas_abonadas,
                                     SUM(CASE WHEN pp.monto != 0 THEN pp.monto ELSE 0 END) AS total_abonado,
                                     if(COUNT(CASE WHEN pp.fec_pago IS NOT NULL AND pp.fec_pago != '' THEN 1 END)=p.no_pagos,'PAGADO','PENDIENTE') AS estatus
                              FROM prestamos p
                                    INNER JOIN estruc_empleados ee ON (p.clave_empleado = ee.clave)
                                    INNER JOIN empl_usuarios eu ON p.clave_empleado = eu.clave
                                    INNER JOIN prestamos_pagos pp ON p.id_prestamos = pp.id_prestamos
                              GROUP BY clave_empleado,ee.sc, ee.brocker, ee.gerencia, eu.nombre, p.id_prestamos,
                                       p.fec_prestamo, p.periodo,p.monto,p.no_pagos,motivo,p.monto_pago)
            Select p.*, 
                   if(ifnull(i.clave,'')='','',concat('Cuenta con insuficiencia de $',FORMAT(ajuste, 2))) as insuf
            from prestamos p
                left join insuficiencias i on (p.clave_empleado=i.clave)";

    $bdc = $conecta->consulta($query);
    if ($bdc->num_rows !== 0) 
    {
        $p=0;
        $i=0;
        $rowIndex = 2;
        while ($row = $bdc->fetch_assoc()) 
        {
            $sheet->setCellValue('A' . $rowIndex, $row['sc']);
            $sheet->setCellValue('B' . $rowIndex, $row['brocker']);
            $sheet->setCellValue('C' . $rowIndex, $row['gerencia']);
            $sheet->setCellValue('D' . $rowIndex, $row['asesor']);
            $sheet->setCellValue('E' . $rowIndex, $row['id_prestamos']);
            $sheet->setCellValue('F' . $rowIndex, $row['fec_prestamo']);
            $sheet->setCellValue('G' . $rowIndex, $row['periodo']);
            $sheet->setCellValue('H' . $rowIndex, $row['motivo']);
            $sheet->setCellValue('I' . $rowIndex, $row['monto']);
            $sheet->setCellValue('J' . $rowIndex, $row['no_pagos']);
            $sheet->setCellValue('K' . $rowIndex, $row['monto_pago']);
            $sheet->setCellValue('L' . $rowIndex, $row['semanas_abonadas'].'/'.$row['no_pagos'].'.');
            $sheet->setCellValue('M' . $rowIndex, $row['total_abonado']);
            $sheet->setCellValue('N' . $rowIndex, $row['monto'] - $row['total_abonado']);
            $sheet->setCellValue('O' . $rowIndex, $row['estatus']);
            $sheet->setCellValue('P' . $rowIndex, $row['insuf']);

            if ($p==1)
            {
                $p=0;
                $i=$rowIndex;
                $sheet->getStyle('A' . $i . ':P' . $i)->applyFromArray($reg_sombreado);
            }
            else
                $p=1;

            $rowIndex++;
        }

        $currencyFormat = [
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ];
        $sheet->getStyle('I2:I' . ($rowIndex - 1))->applyFromArray($currencyFormat);
        $sheet->getStyle('K2:K' . ($rowIndex - 1))->applyFromArray($currencyFormat);
        $sheet->getStyle('M2:M' . ($rowIndex - 1))->applyFromArray($currencyFormat);
        $sheet->getStyle('N2:N' . ($rowIndex - 1))->applyFromArray($currencyFormat);
    }

    $detalleSheet = $spreadsheet->createSheet();
    $detalleSheet->setTitle('Detalle_prestamos');
    
    $detalleSheet->setCellValue('A1', 'SC');
    $detalleSheet->setCellValue('B1', 'Brocker');
    $detalleSheet->setCellValue('C1', 'Gerencia');
    $detalleSheet->setCellValue('D1', 'Asesor');
    $detalleSheet->setCellValue('E1', 'No. pago');
    $detalleSheet->setCellValue('F1', 'Fec. pago');
    $detalleSheet->setCellValue('G1', 'Motivo');
    $detalleSheet->setCellValue('H1', 'Monto');

    $detalleSheet->getStyle('A1:H1')->applyFromArray($headerStyleArray);

    $detalleSheet->getColumnDimension('A')->setAutoSize(true);
    $detalleSheet->getColumnDimension('B')->setAutoSize(true);
    $detalleSheet->getColumnDimension('C')->setAutoSize(true);
    $detalleSheet->getColumnDimension('D')->setAutoSize(true);
    $detalleSheet->getColumnDimension('E')->setAutoSize(true);
    $detalleSheet->getColumnDimension('F')->setAutoSize(true);
    $detalleSheet->getColumnDimension('G')->setAutoSize(true);
    $detalleSheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->getStyle('A1:H1')->applyFromArray($headerStyleArray);


    $q_detalle="Select ee.sc,ee.brocker,ee.gerencia,eu.nombre AS asesor,pp.no_pago,pp.fec_pago,motivo,pp.monto
                FROM prestamos p
                    INNER JOIN estruc_empleados ee ON p.clave_empleado = ee.clave
                    INNER JOIN empl_usuarios eu ON p.clave_empleado = eu.clave
                    INNER JOIN prestamos_pagos pp ON p.id_prestamos = pp.id_prestamos
                where pp.monto > 0
                order by ee.sc, ee.brocker, ee.gerencia, eu.nombre, pp.no_pago, pp.fec_pago, pp.monto";

    $bd = $conecta->consulta($q_detalle);
    if ($bd->num_rows !== 0) 
    {
        $p=0;
        $asesor="";
        $rowIndex = 2;
        while ($row = $bd->fetch_assoc()) 
        {
            if($asesor != $row['asesor'])
            {
                if($asesor != "")
                {
                    $j=$rowIndex-1;
                    if($p==1)
                        $detalleSheet->getStyle('A' . $i . ':H' . $j)->applyFromArray($reg_sombreado);
                }
                $i=$rowIndex;
                $asesor= $row['asesor'];
                if($p==0)
                    $p=1;
                else
                    $p=0;
            }
            $detalleSheet->setCellValue('A' . $rowIndex, $row['sc']);
            $detalleSheet->setCellValue('B' . $rowIndex, $row['brocker']);
            $detalleSheet->setCellValue('C' . $rowIndex, $row['gerencia']);
            $detalleSheet->setCellValue('D' . $rowIndex, $row['asesor']);
            $detalleSheet->setCellValue('E' . $rowIndex, $row['no_pago']);
            $detalleSheet->setCellValue('F' . $rowIndex, $row['fec_pago']);
            $detalleSheet->setCellValue('G' . $rowIndex, $row['motivo']);
            $detalleSheet->setCellValue('H' . $rowIndex, $row['monto']);

            $rowIndex++;
        }

        if($p==1)
        {
            $j=$rowIndex-1;
                $detalleSheet->getStyle('A' . $i . ':H' . $j)->applyFromArray($reg_sombreado);
        }

        $currencyFormat = [
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ];

        $detalleSheet->getStyle('H2:H' . ($rowIndex - 1))->applyFromArray($currencyFormat);
    }

    $spreadsheet->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="prestamos.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>