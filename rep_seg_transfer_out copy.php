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

    function clean($val) {
        if ($val === 'undefined' || $val === null) return '';
        return trim($val);
    }

    if (isset($_POST['transfer_out']) && $_POST['transfer_out'] == 'reporte') {
        $titulo_hoja="Reporte de Transfer Out"; 

        $textGerencia      = clean($_POST['textGerencia']      ?? '');
        $textSubBrocker    = clean($_POST['textSubBrocker']    ?? '');
        $textSocioComercial= clean($_POST['textSocioComercial']?? '');
        $textAsesor        = clean($_POST['textAsesor']        ?? '');

        $campos = '';
        $where  = "";

        if (empty($textGerencia) && empty($textSubBrocker) && empty($textSocioComercial)) {
            die("Por favor, proporciona al menos un filtro: Gerencia, Sub-Brocker o Socio Comercial.");
        }

        if ($textSocioComercial !== '') {
            $campos = "
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE av.sc           END AS 'sc',
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE av.venta_sc      END AS 'Venta SC',
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE IFNULL(ato.venta_sc,0)                                                              END AS 'Venta Salida SC',
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN ''
                     WHEN av.venta_sc IS NULL OR av.venta_sc = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_sc,0) / av.venta_sc) * 100, 2)                                                                                END AS '% Transfer out SC',

                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.brocker       END AS 'brocker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.venta_sb      END AS 'Venta Broker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE IFNULL(ato.venta_sb,0)                                                              END AS 'Venta Salida Broker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN ''
                     WHEN av.venta_sb IS NULL OR av.venta_sb = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_sb,0) / av.venta_sb) * 100, 2)                                                                                END AS '% Transfer out Broker',

                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.gerencia    END AS 'gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.venta_ger   END AS 'Venta Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE IFNULL(ato.venta_ger,0)                                                           END AS 'Venta Salida Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN ''
                     WHEN av.venta_ger IS NULL OR av.venta_ger = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_ger,0) / av.venta_ger) * 100, 2)                                                                              END AS '% Transfer out Gerencia',

                av.no_empleado,
                dbc_u.nombre                                                                       AS asesor,
                av.total_saldo_real                                                                AS 'Total Venta',
                IFNULL(ato.total_saldo_real,0)                                                     AS 'Total Venta Salida',
                CASE WHEN av.total_saldo_real IS NULL OR av.total_saldo_real = 0 THEN 0
                     ELSE ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2)  END AS '% Transfer out Asesor'
            ";
            $where = ($textSocioComercial == "TODOS") ? "" : "ee.sc = '$textSocioComercial'";

        } elseif ($textSubBrocker !== '') {
            $campos = "
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE av.sc            END AS 'sc',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.brocker        END AS 'brocker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.venta_sb       END AS 'Venta Broker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE IFNULL(ato.venta_sb,0)                                                              END AS 'Venta Salida Broker',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN ''
                     WHEN av.venta_sb IS NULL OR av.venta_sb = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_sb,0) / av.venta_sb) * 100, 2)                                                                                END AS '% Transfer out Broker',

                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.gerencia     END AS 'gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.venta_ger    END AS 'Venta Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE IFNULL(ato.venta_ger,0)                                                           END AS 'Venta Salida Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN ''
                     WHEN av.venta_ger IS NULL OR av.venta_ger = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_ger,0) / av.venta_ger) * 100, 2)                                                                              END AS '% Transfer out Gerencia',

                av.no_empleado,
                dbc_u.nombre                                                                       AS asesor,
                av.total_saldo_real                                                                AS 'Total Venta',
                IFNULL(ato.total_saldo_real,0)                                                     AS 'Total Venta Salida',
                CASE WHEN av.total_saldo_real IS NULL OR av.total_saldo_real = 0 THEN 0
                     ELSE ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2)  END AS '% Transfer out Asesor'
            ";
            $where = "ee.brocker = '$textSubBrocker'";

        } elseif ($textGerencia !== '') {
            $campos = "
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE av.sc            END AS 'sc',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.brocker        END AS 'brocker',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.gerencia     END AS 'gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.venta_ger    END AS 'Venta Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE IFNULL(ato.venta_ger,0)                                                           END AS 'Venta Salida Gerencia',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN ''
                     WHEN av.venta_ger IS NULL OR av.venta_ger = 0 THEN ''
                     ELSE ROUND((IFNULL(ato.venta_ger,0) / av.venta_ger) * 100, 2)                                                                              END AS '% Transfer out Gerencia',

                av.no_empleado,
                dbc_u.nombre                                                                       AS asesor,
                av.total_saldo_real                                                                AS 'Total Venta',
                IFNULL(ato.total_saldo_real,0)                                                     AS 'Total Venta Salida',
                CASE WHEN av.total_saldo_real IS NULL OR av.total_saldo_real = 0 THEN 0
                     ELSE ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2)  END AS '% Transfer out Asesor'
            ";
            $where = "ee.gerencia = '$textGerencia'";

        } elseif ($textAsesor !== '') {
            $campos = "
                CASE WHEN av.sc      IS NULL OR av.sc      = '' THEN '' ELSE av.sc            END AS 'sc',
                CASE WHEN av.brocker IS NULL OR av.brocker = '' THEN '' ELSE av.brocker        END AS 'brocker',
                CASE WHEN av.gerencia IS NULL OR av.gerencia = '' THEN '' ELSE av.gerencia     END AS 'gerencia',
                av.no_empleado,
                dbc_u.nombre                                                                       AS asesor,
                av.total_saldo_real                                                                AS 'Total Venta',
                IFNULL(ato.total_saldo_real,0)                                                     AS 'Total Venta Salida',
                CASE WHEN av.total_saldo_real IS NULL OR av.total_saldo_real = 0 THEN 0
                     ELSE ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2)  END AS '% Transfer out Asesor'
            ";
            $where = "TRIM(dbc_u.nombre) = TRIM('$textAsesor')";
        }

        $where_sql = ($where != "") ? "WHERE $where" : "";

        $sql = "SELECT $campos
                FROM acum_ventas av  
                    LEFT JOIN acum_tranfer_out ato ON av.no_empleado = ato.no_empleado 
                    INNER JOIN estruc_empleados ee ON av.no_empleado = ee.no_empleado 
                    INNER JOIN carobra.usuarios dbc_u ON ee.clave = dbc_u.clave
                $where_sql
                ORDER BY av.sc, av.brocker, av.gerencia";

        $excepciones = ['no_empleado', '% Transfer out Asesor', '% Transfer out Gerencia', '% Transfer out Broker', '% Transfer out SC'];
    }

    cargarDatosEnHoja($spreadsheet, $sql, $titulo_hoja, $excepciones, []);

    // ─── Helpers de color ────────────────────────────────────────────────────────

    /**
     * Devuelve el ARGB de fondo según el % transfer out.
     * Retorna null si el valor está vacío / no es numérico.
     */
    function getColorPorPorcentaje($valor): ?string {
        if ($valor === '' || $valor === null || !is_numeric($valor)) return null;
        $v = (float)$valor;
        if ($v <= 12) return 'FF92D050'; // Verde
        if ($v <= 15) return 'FFFFFF00'; // Amarillo
        return 'FFFF0000';               // Rojo
    }

    /**
     * Aplica color de fondo a un rango de celdas.
     */
    function pintarRango(Worksheet $sheet, string $rango, string $argb): void {
        $sheet->getStyle($rango)->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => $argb],
            ],
        ]);
    }

    // ─── Función principal ────────────────────────────────────────────────────────

    function cargarDatosEnHoja(Spreadsheet $spreadsheet, string $sql, string $tituloHoja, array $excepciones = [], array $num_text = [])
    {
        global $conecta;

        $bd   = $conecta->consulta($sql);
        $rows = [];
        while ($tb = $bd->fetch_assoc()) {
            $rows[] = $tb;
        }

        $filtered = array_values(array_filter($rows));
        if (empty($filtered)) return;

        $sheet = new Worksheet($spreadsheet, $tituloHoja);
        $spreadsheet->addSheet($sheet);

        $headers = array_keys($filtered[0]);
        $ncol    = count($headers);

        // ── Encabezados ──────────────────────────────────────────────────────────
        foreach ($headers as $i => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', htmlspecialchars($h));
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ncol);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF808080']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Mapa de índices de columna por nombre de header ──────────────────────
        $headerIndex = array_flip($headers); // ['sc' => 0, 'Venta SC' => 1, ...]

        // ── Grupos de coloreo: [columna_porcentaje => [columnas_a_pintar]] ───────
        $grupos = [
            '% Transfer out SC'      => ['sc', 'Venta SC', 'Venta Salida SC', '% Transfer out SC'],
            '% Transfer out Broker'  => ['brocker', 'Venta Broker', 'Venta Salida Broker', '% Transfer out Broker'],
            '% Transfer out Gerencia'=> ['gerencia', 'Venta Gerencia', 'Venta Salida Gerencia', '% Transfer out Gerencia'],
            '% Transfer out Asesor'  => ['no_empleado', 'asesor', 'Total Venta', 'Total Venta Salida', '% Transfer out Asesor'],
        ];

        // Filtramos solo los grupos cuyos headers existan en el resultado
        $gruposActivos = [];
        foreach ($grupos as $pctHeader => $colsGrupo) {
            if (isset($headerIndex[$pctHeader])) {
                $gruposActivos[$pctHeader] = array_filter(
                    $colsGrupo,
                    fn($c) => isset($headerIndex[$c])
                );
            }
        }

        // ── Escritura de datos ───────────────────────────────────────────────────
        $cols_num      = [];
        $cols_num_text = [];
        $estilo_num    = 0;
        $fila          = 2;

        foreach ($filtered as $row) {
            $colIdx = 1;
            foreach ($headers as $h) {
                $raw     = $row[$h] ?? '';
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);

                if (in_array(strtolower($h), array_map('strtolower', $num_text))) {
                    $val = htmlspecialchars((string)$raw);
                    $sheet->setCellValueExplicit($columna . $fila, $val, DataType::TYPE_STRING);
                } else {
                    $val = htmlspecialchars((string)$raw);
                    $sheet->setCellValue($columna . $fila, strval($val));
                }

                if ($estilo_num == 0 && is_numeric($val)) {
                    $tit = strtolower($h);
                    if (!in_array($tit, array_map('strtolower', $excepciones)))
                        $cols_num[]      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                    else
                        $cols_num_text[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                }
                $colIdx++;
            }

            // ── Coloreo por fila ─────────────────────────────────────────────────
            foreach ($gruposActivos as $pctHeader => $colsGrupo) {
                $pctVal = $row[$pctHeader] ?? '';

                // SC y Broker: si la columna ancla está vacía, no pintar
                $anclaVacia = false;
                if ($pctHeader === '% Transfer out SC' && isset($headerIndex['sc'])) {
                    $anclaVacia = ($row['sc'] ?? '') === '';
                }
                if ($pctHeader === '% Transfer out Broker' && isset($headerIndex['brocker'])) {
                    $anclaVacia = ($row['brocker'] ?? '') === '';
                }

                if ($anclaVacia) continue;

                $color = getColorPorPorcentaje($pctVal);
                if ($color === null) continue;

                foreach ($colsGrupo as $nombreCol) {
                    $idxCol  = $headerIndex[$nombreCol] + 1;
                    $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idxCol);
                    pintarRango($sheet, $colLetra . $fila, $color);
                }
            }

            $fila++;
            $estilo_num = 1;
        }

        // ── Ancho automático ─────────────────────────────────────────────────────
        for ($col = 1; $col <= $ncol; $col++) {
            $c = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // ── Formato numérico ─────────────────────────────────────────────────────
        foreach ($cols_num as $colLetra) {
            $sheet->getStyle($colLetra . "2:" . $colLetra . $fila)
                  ->getNumberFormat()
                  ->setFormatCode('"$"#,##0.00;[Red]\-"$"#,##0.00');
        }
        foreach ($cols_num_text as $colLetra) {
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