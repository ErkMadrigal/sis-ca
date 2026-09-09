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

$sql          = "";
$titulo_hoja  = "";
$excepciones  = [];
$filtroActivo = "";

function clean($val) {
    if ($val === 'undefined' || $val === null) return '';
    return trim($val);
}

if (isset($_POST['transfer_out']) && $_POST['transfer_out'] == 'reporte') {
    $titulo_hoja = "Reporte de Transfer Out";

    $textGerencia       = clean($_POST['textGerencia']       ?? '');
    $textSubBrocker     = clean($_POST['textSubBrocker']     ?? '');
    $textSocioComercial = clean($_POST['textSocioComercial'] ?? '');
    $textAsesor         = clean($_POST['textAsesor']         ?? '');
    $periodo = clean($_POST['periodo'] ?? '');

    $campos = '';
    $where  = "";

    

    if (empty($textGerencia) && empty($textSubBrocker) && empty($textSocioComercial)) {
        die("Por favor, proporciona al menos un filtro: Gerencia, Sub-Brocker o Socio Comercial.");
    }

    if ($textSocioComercial !== '') {
        $filtroActivo = 'textSocioComercial';
        $campos = "
														
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'sc',
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.venta_acum END AS 'Venta SC',
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE IFNULL(sc.venta_ato,0) END AS 'Venta Salida SC',
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE IFNULL(sc.por_sc,0) END AS '% Transfer out SC',


            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'brocker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.venta_acum END AS 'Venta Broker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.venta_ato,0) END AS 'Venta Salida Broker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.por_sb,0) END AS '% Transfer out Broker',

            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Gerencia',


            comi.nombre as asesor, comi.venta_acum as 'Total Venta', 
            IFNULL(comi.venta_ato,0) AS 'Total Venta Salida', comi.por_comi as '% Transfer out Asesor'

        "; 
        $where = ($textSocioComercial == "TODOS") ? "" : "sc.sc = '$textSocioComercial'";

    } elseif ($textSubBrocker !== '') {
        $filtroActivo = 'textSubBrocker';
        $campos = "
														
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'sc',

            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'brocker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.venta_acum END AS 'Venta Broker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.venta_ato,0) END AS 'Venta Salida Broker',
            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.por_sb,0) END AS '% Transfer out Broker',

            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Gerencia',


            comi.nombre as asesor, comi.venta_acum as 'Total Venta', 
            IFNULL(comi.venta_ato,0) AS 'Total Venta Salida', comi.por_comi as '% Transfer out Asesor'

        "; 
        $where = "sb.brocker = '$textSubBrocker'";

    } elseif ($textGerencia !== '') {
        $filtroActivo = 'textGerencia';
        $campos = "
														
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'sc',

            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'brocker',

            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Gerencia',


            comi.nombre as asesor, comi.venta_acum as 'Total Venta', 
            IFNULL(comi.venta_ato,0) AS 'Total Venta Salida', comi.por_comi as '% Transfer out Asesor'

        "; 
        $where = "ger.gerencia = '$textGerencia'";

    } elseif ($textAsesor !== '') {
        $filtroActivo = 'textAsesor';
        $campos = "
														
            CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'sc',

            CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'brocker',

            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Gerencia',
            CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Gerencia',


            comi.nombre as asesor, comi.venta_acum as 'Total Venta', 
            IFNULL(comi.venta_ato,0) AS 'Total Venta Salida', comi.por_comi as '% Transfer out Asesor'

        "; 
        $where = "TRIM(comi.nombre) = TRIM('$textAsesor')";
    }

    $where_sql = ($where != "") ? "WHERE $where" : "";

    $parametro = ($periodo == "NULL" || $periodo == "")
    ? "NULL"
    : "'" . $periodo . "'";

    $conecta->consulta("CALL create_acum_ventas($parametro)");

    if (isset($conecta->conexion) && $conecta->conexion instanceof mysqli)
    {
        while ($conecta->conexion->more_results())
        {
            $conecta->conexion->next_result();
            if ($res = $conecta->conexion->store_result())
                $res->free();
        }
    }
    
    $conecta->consulta("CALL create_acum_transfer_out($parametro)");

    $sql = "Select $campos
								
            FROM (Select distinct av.sc, av.venta_sc as venta_acum,ifnull(ato.venta_sc,0) as venta_ato, ROUND((IFNULL(ato.venta_sc,0) / av.venta_sc) * 100, 2) as por_sc from acum_ventas av

                    left join acum_tranfer_out ato on (av.sc=ato.sc)) sc

                left join (Select distinct av.sc,av.brocker, av.venta_sb as venta_acum,ifnull(ato.venta_sb,0) as venta_ato, 
                            ROUND((IFNULL(ato.venta_sb,0) / av.venta_sb) * 100, 2) as por_sb from acum_ventas av
                    left join acum_tranfer_out ato on (av.brocker=ato.brocker)) sb on (sc.sc=sb.sc)
                left join (Select distinct av.sc, av.brocker, av.gerencia, av.venta_ger as venta_acum,ifnull(ato.venta_ger,0) as venta_ato, 
                            ROUND((IFNULL(ato.venta_ger,0) / av.venta_ger) * 100, 2) as por_ger from acum_ventas av
                    left join acum_tranfer_out ato on (av.gerencia=ato.gerencia)) ger on (sb.sc=ger.sc and sb.brocker=ger.brocker)
                left join (Select distinct av.gerencia, nombre, u.no_empleado,av.total_saldo_real as venta_acum,ifnull(ato.total_saldo_real,0) as venta_ato, 
                            ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2) as por_comi from acum_ventas av

                    left join acum_tranfer_out ato on (av.no_empleado=ato.no_empleado)

                    inner join empl_usuarios u on (av.no_empleado=u.no_empleado)) comi on (ger.gerencia=comi.gerencia)
        $where_sql
        order by sc.sc,sb.brocker,ger.gerencia,comi.nombre";

    $excepciones = [
        'no_empleado',
        '% Transfer out Asesor',
        '% Transfer out Gerencia',
        '% Transfer out Broker',
        '% Transfer out SC'
    ];
}

cargarDatosEnHoja($spreadsheet, $sql, $titulo_hoja, $excepciones, [], $filtroActivo);

// ─── Helpers ──────────────────────────────────────────────────────────────────

function getColorPorPorcentaje($valor): ?string {
    if ($valor === '' || $valor === null || !is_numeric($valor)) return '9BEEB9';
    $v = (float)$valor;
    if ($v <= 12) return '9BEEB9'; // Verde
    if ($v <= 15) return 'E3EE3F'; // Amarillo
    return 'F69393';               // Rojo
}

function pintarRango(Worksheet $sheet, string $rango, string $argb): void {
    $sheet->getStyle($rango)->applyFromArray([
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['argb' => $argb],
        ],
    ]);
}

// ─── Función principal ────────────────────────────────────────────────────────

function cargarDatosEnHoja(Spreadsheet $spreadsheet, string $sql, string $tituloHoja, array $excepciones = [], array $num_text = [], string $filtroActivo = '')
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

    $headers     = array_keys($filtered[0]);
    $ncol        = count($headers);
    $headerIndex = array_flip($headers);

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

    // ── Reglas de coloreo por grupo ──────────────────────────────────────────
    // Cada grupo define: qué % lo controla y qué columnas pinta
    $reglasColoreo = [
        '% Transfer out SC' => [
            'cols'  => ['sc', 'Venta SC', 'Venta Salida SC', '% Transfer out SC'],
            'ancla' => 'sc',
        ],
        '% Transfer out Broker' => [
            'cols'  => ['brocker', 'Venta Broker', 'Venta Salida Broker', '% Transfer out Broker'],
            'ancla' => 'brocker',
        ],
        '% Transfer out Gerencia' => [
            'cols'  => ['gerencia', 'Venta Gerencia', 'Venta Salida Gerencia', '% Transfer out Gerencia'],
            'ancla' => null,
        ],
        '% Transfer out Asesor' => [
            'cols'  => ['no_empleado', 'asesor', 'Total Venta', 'Total Venta Salida', '% Transfer out Asesor'],
            'ancla' => null,
        ],
    ];

    // Columnas extra que se pintan con el % del nivel activo
    // cuando ese nivel no tiene su propio %
    $extraPorFiltro = [
        'textSubBrocker' => ['% Transfer out Broker'  => ['sc']],
        'textGerencia'   => ['% Transfer out Gerencia' => ['sc', 'brocker']],
        'textAsesor'     => ['% Transfer out Asesor'   => ['sc', 'brocker', 'gerencia']],
    ];

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
        foreach ($reglasColoreo as $pctHeader => $regla) {

            // Si ese % no existe en este query, skip
            if (!isset($headerIndex[$pctHeader])) continue;

            // Si tiene ancla (sc o brocker) y está vacía, skip
           if ($regla['ancla'] !== null) {
                $anclaVal = ($row[$regla['ancla']] ?? '');
                if ($anclaVal === '') {
                    // Ancla vacía → pintar verde y continuar al siguiente grupo
                    foreach ($regla['cols'] as $nombreCol) {
                        if (!isset($headerIndex[$nombreCol])) continue;
                        $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headerIndex[$nombreCol] + 1);
                        pintarRango($sheet, $colLetra . $fila, '9BEEB9');
                    }
                    continue;
                }
            }

            $pctVal = $row[$pctHeader] ?? '';
            $color  = getColorPorPorcentaje($pctVal);
            if ($color === null) continue;

            // Columnas base del grupo
            $colsAPintar = $regla['cols'];

            // Sumar columnas extra si aplica para este filtro y este grupo
            if (isset($extraPorFiltro[$filtroActivo][$pctHeader])) {
                $colsAPintar = array_merge($colsAPintar, $extraPorFiltro[$filtroActivo][$pctHeader]);
            }

            foreach ($colsAPintar as $nombreCol) {
                if (!isset($headerIndex[$nombreCol])) continue;
                $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headerIndex[$nombreCol] + 1);
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