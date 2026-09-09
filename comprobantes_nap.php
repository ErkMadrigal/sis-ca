<?php
/* ============================================================
   SECCIÓN 1: INICIO DE SESIÓN
   ============================================================ */
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
	<title>CAROBRA Consultores Especializados</title>

	<link rel="shortcut icon" href="./img/favicon.ico">
	<script type="text/javascript" src="./js/carobra.js"></script>
	<script src="./jquery/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
	<script src="./popper/popper.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>
	<script src="./twitter/typeahead.bundle.js"></script>
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue-900: #0f2044; --blue-800: #163060; --blue-700: #1a3a75;
            --blue-600: #1e4799; --blue-500: #2563c4; --blue-400: #3b82e8;
            --blue-100: #e8f0fd; --blue-50:  #f0f5ff;
            --white: #ffffff;
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b;
            --gray-900: #0f172a;
            --success: #10b981; --warning: #f59e0b; --error: #ef4444;
            --shadow-sm: 0 1px 3px rgba(15,32,68,.06), 0 1px 2px rgba(15,32,68,.04);
            --shadow:    0 4px 16px rgba(15,32,68,.08), 0 1px 4px rgba(15,32,68,.04);
            --shadow-xl: 0 24px 64px rgba(15,32,68,.16), 0 8px 24px rgba(15,32,68,.08);
            --radius-sm: 8px; --radius: 12px; --radius-lg: 18px; --radius-xl: 24px;
        }

        /* ── Topbar de acciones ── */
        .nap-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
            padding: 16px 20px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .nap-topbar h1 { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 17px; color: var(--gray-900); margin: 0; }
        .nap-topbar p  { font-size: 11px; color: var(--gray-400); margin: 2px 0 0; }
        .nap-actions   { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        /* ── Search ── */
        .search-wrap { position: relative; display: flex; align-items: center; }
        .search-wrap input {
            width: 240px; background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius); padding: 7px 14px 7px 34px;
            font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--gray-700); transition: all .2s; box-shadow: var(--shadow-sm);
        }
        .search-wrap input:focus { outline: none; border-color: var(--blue-400); box-shadow: 0 0 0 3px rgba(59,130,232,.12); }
        .search-wrap input::placeholder { color: var(--gray-400); }
        .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 13px; color: var(--gray-400); pointer-events: none; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: var(--radius);
            font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all .15s;
            border: none; white-space: nowrap;
        }
        .btn-primary { background: linear-gradient(135deg, var(--blue-600), var(--blue-500)); color: white; box-shadow: 0 2px 8px rgba(30,71,153,.3); }
        .btn-primary:hover:not(:disabled) { background: linear-gradient(135deg, var(--blue-700), var(--blue-600)); transform: translateY(-1px); }
        .btn-primary:disabled { opacity: .45; cursor: not-allowed; transform: none; }
        .btn-ghost { background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); }
        .btn-ghost:hover { border-color: var(--gray-300); color: var(--gray-800); }

        /* ── Stats ── */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .stat-card {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg); padding: 16px 18px;
            box-shadow: var(--shadow-sm); display: flex; align-items: center;
            gap: 12px; transition: box-shadow .2s, transform .2s;
        }
        .stat-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.blue  { background: var(--blue-50); }
        .stat-icon.green { background: #ecfdf5; }
        .stat-icon.amber { background: #fffbeb; }
        .stat-icon.red   { background: #fef2f2; }
        .stat-val   { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 700; color: var(--gray-900); line-height: 1; }
        .stat-label { font-size: 11px; color: var(--gray-500); margin-top: 3px; font-weight: 500; }

        /* ── Work grid ── */
        .work-grid { display: grid; grid-template-columns: 380px 1fr; gap: 18px; }

        /* ── Card ── */
        .card {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-xl); box-shadow: var(--shadow-sm);
            display: flex; flex-direction: column; overflow: hidden;
        }
        .card-header { padding: 16px 20px 12px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; }
        .card-title  { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 14px; color: var(--gray-900); }
        .card-sub    { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .card-body   { padding: 16px 20px; display: flex; flex-direction: column; gap: 12px; flex: 1; }

        /* ── Dropzone ── */
        .dropzone {
            border: 2px dashed var(--gray-200); border-radius: var(--radius-lg);
            padding: 28px 16px; text-align: center; cursor: pointer;
            transition: all .2s; position: relative; background: var(--gray-50);
        }
        .dropzone:hover, .dropzone.drag-over { border-color: var(--blue-400); background: var(--blue-50); }
        .dropzone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .dz-visual {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, var(--blue-100), var(--blue-50));
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin: 0 auto 10px;
            border: 1px solid var(--blue-200); transition: all .2s;
        }
        .dropzone:hover .dz-visual, .dropzone.drag-over .dz-visual { transform: scale(1.07); background: linear-gradient(135deg, var(--blue-200), var(--blue-100)); }
        .dz-title { font-weight: 700; font-size: 13px; color: var(--gray-700); margin-bottom: 3px; }
        .dz-hint  { font-size: 12px; color: var(--gray-400); }
        .dz-types {
            display: inline-flex; align-items: center; gap: 5px; margin-top: 9px;
            padding: 3px 11px; background: var(--white); border: 1px solid var(--gray-200);
            border-radius: 999px; font-size: 11px; color: var(--gray-500); box-shadow: var(--shadow-sm);
        }

        /* ── Queue ── */
        .queue-header { display: flex; align-items: center; justify-content: space-between; }
        .queue-label  { font-size: 12px; font-weight: 600; color: var(--gray-600); }
        .badge-count  { background: var(--blue-100); color: var(--blue-600); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
        .queue-list   { display: flex; flex-direction: column; gap: 6px; max-height: 180px; overflow-y: auto; }
        .queue-list::-webkit-scrollbar { width: 3px; }
        .queue-list::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 99px; }
        .qi {
            display: flex; align-items: center; gap: 9px;
            background: var(--gray-50); border: 1px solid var(--gray-200);
            border-radius: var(--radius); padding: 8px 10px;
            animation: slideUp .2s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: none; } }
        .qi-thumb { width: 28px; height: 28px; border-radius: 7px; background: linear-gradient(135deg, var(--blue-100), var(--blue-50)); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; border: 1px solid var(--blue-200); }
        .qi-name  { font-size: 11px; font-weight: 600; color: var(--gray-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
        .qi-size  { font-size: 10px; color: var(--gray-400); margin-top: 1px; }
        .qi-status { margin-left: auto; flex-shrink: 0; font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 999px; }
        .qi-status.pending { background: var(--gray-100); color: var(--gray-500); }
        .qi-status.working { background: #fffbeb; color: #b45309; }
        .qi-status.done    { background: #ecfdf5; color: #059669; }
        .qi-status.error   { background: #fef2f2; color: #dc2626; }
        .empty-queue { text-align: center; padding: 16px; font-size: 12px; color: var(--gray-400); }

        /* ── Progress ── */
        .progress-section { display: none; }
        .progress-section.visible { display: block; }
        .prog-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .prog-label  { font-size: 11px; font-weight: 600; color: var(--gray-600); }
        .prog-pct    { font-size: 11px; font-weight: 700; color: var(--blue-600); }
        .prog-track  { height: 5px; background: var(--gray-100); border-radius: 999px; overflow: hidden; }
        .prog-fill   { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--blue-600), var(--blue-400)); transition: width .25s ease; width: 0%; }

        /* ── Results ── */
        .results-card  { display: flex; flex-direction: column; }
        .results-body  { flex: 1; overflow: hidden; display: flex; flex-direction: column; }
        .table-wrap    { flex: 1; overflow-y: auto; max-height: 400px; }
        .table-wrap::-webkit-scrollbar { width: 4px; }
        .table-wrap::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 99px; }

        /* Reseteamos los estilos de tabla de Bootstrap/Carobra para esta tabla */
        .nap-table { width: 100% !important; border-collapse: collapse !important; background: none !important; }
        .nap-table thead tr { background: var(--gray-50) !important; border-bottom: 1px solid var(--gray-200) !important; position: sticky; top: 0; }
        .nap-table th { padding: 9px 14px !important; text-align: left !important; font-size: 10px !important; font-weight: 700 !important; letter-spacing: .06em !important; text-transform: uppercase !important; color: var(--gray-500) !important; background: var(--gray-50) !important; border: none !important; }
        .nap-table tbody tr { border-bottom: 1px solid var(--gray-100) !important; transition: background .1s; }
        .nap-table tbody tr:last-child { border-bottom: none !important; }
        .nap-table tbody tr:hover { background: var(--blue-50) !important; }
        .nap-table td { padding: 9px 14px !important; font-size: 12px !important; color: var(--gray-700) !important; vertical-align: middle !important; border: none !important; }
        .td-pg      { color: var(--gray-400) !important; font-size: 11px !important; width: 50px; font-weight: 500; }
        .td-concept { font-weight: 600 !important; color: var(--blue-700) !important; }
        .td-file    { font-size: 11px !important; color: var(--gray-400) !important; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .td-status  { text-align: right !important; white-space: nowrap; }

        .badge     { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; padding: 2px 9px; border-radius: 999px; }
        .badge-ok  { background: #ecfdf5; color: #059669; }
        .badge-err { background: #fef2f2; color: #dc2626; }
        .badge-wait{ background: var(--gray-100); color: var(--gray-400); }

        /* ── Empty ── */
        .empty-results { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; padding: 40px 24px; text-align: center; }
        .er-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--gray-100); display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .empty-results h3 { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700; color: var(--gray-800); }
        .empty-results p  { font-size: 12px; color: var(--gray-400); max-width: 240px; line-height: 1.6; }

        /* ── Toast ── */
        #nap-toast {
            position: fixed; bottom: 24px; right: 24px;
            background: var(--gray-900); color: white;
            padding: 11px 16px; border-radius: var(--radius);
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            box-shadow: var(--shadow-xl);
            transform: translateY(12px); opacity: 0;
            transition: all .25s; pointer-events: none;
            max-width: 320px; z-index: 9999;
        }
        #nap-toast.show { transform: none; opacity: 1; }

        /* Responsive */
        @media (max-width: 900px) {
            .work-grid  { grid-template-columns: 1fr; }
            .stats-row  { grid-template-columns: repeat(2, 1fr); }
            .nap-topbar { flex-direction: column; align-items: flex-start; }
            .search-wrap input { width: 100%; }
        }


        /* ── Botón Ver empleados ── */
        .btn-nap-info {
            background: var(--white);
            color: var(--blue-600);
            border: 1px solid var(--blue-200);
            box-shadow: var(--shadow-sm);
        }
        .btn-nap-info:hover:not(:disabled) {
            background: var(--blue-50);
            border-color: var(--blue-400);
        }
        .btn-nap-info:disabled { opacity: .4; cursor: not-allowed; }

        /* ── Backdrop ── */
        .nap-modal-backdrop {
            display: none;
            position: fixed; inset: 0; z-index: 10000;
            background: rgba(15,32,68,.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .nap-modal-backdrop.open { display: flex; animation: napFadeIn .18s ease; }
        @keyframes napFadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ── Modal box ── */
        .nap-modal {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 760px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: napSlideUp .2s ease;
        }
        @keyframes napSlideUp {
            from { transform: translateY(14px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }

        /* ── Header ── */
        .nap-modal-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            padding: 20px 24px 14px;
            border-bottom: 1px solid var(--gray-100);
            flex-shrink: 0;
        }
        .nap-modal-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 16px; color: var(--gray-900); }
        .nap-modal-sub   { font-size: 11px; color: var(--gray-400); margin-top: 3px; }
        .nap-modal-close {
            width: 28px; height: 28px; border-radius: 8px;
            border: 1px solid var(--gray-200); background: var(--gray-50);
            color: var(--gray-500); font-size: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-left: 12px;
            transition: background .15s, color .15s;
        }
        .nap-modal-close:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

        /* ── Loading spinner ── */
        .nap-modal-loading {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; padding: 56px; font-size: 13px; color: var(--gray-500);
        }
        .nap-spinner {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid var(--gray-200);
            border-top-color: var(--blue-500);
            animation: napSpin .7s linear infinite; flex-shrink: 0;
        }
        @keyframes napSpin { to { transform: rotate(360deg); } }

        /* ── Body / scroll ── */
        .nap-modal-body { flex: 1; overflow-y: auto; }
        .nap-modal-body::-webkit-scrollbar { width: 4px; }
        .nap-modal-body::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 99px; }

        /* ── Tabla agrupada ── */
        .nap-info-table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed;
        }

        .nap-info-table thead tr {
            background: var(--gray-50) !important;
            border-bottom: 2px solid var(--gray-200) !important;
            position: sticky; top: 0; z-index: 2;
        }
        .nap-info-table th {
            padding: 10px 16px !important;
            font-size: 10px !important; font-weight: 700 !important;
            letter-spacing: .07em !important; text-transform: uppercase !important;
            color: var(--gray-500) !important;
            background: var(--gray-50) !important;
            border: none !important; text-align: left !important;
        }
        /* Anchos de columnas */
        .nap-info-table colgroup col:nth-child(1) { width: 34%; }
        .nap-info-table colgroup col:nth-child(2) { width: 10%; }
        .nap-info-table colgroup col:nth-child(3) { width: 10%; }
        .nap-info-table colgroup col:nth-child(4) { width: 46%; }

        .nap-info-table td {
            padding: 9px 16px !important;
            font-size: 12px !important;
            color: var(--gray-700) !important;
            border: none !important;
            border-bottom: 1px solid var(--gray-100) !important;
            vertical-align: middle !important;
        }

        /* Columna nombre — borde izquierdo de acento */
        .nap-td-nombre {
            vertical-align: top !important;
            padding-top: 12px !important;
            border-left: 3px solid var(--blue-400) !important;
            background: var(--blue-50) !important;
        }

        .nap-emp-name {
            font-weight: 700 !important; font-size: 12px !important;
            color: var(--gray-900) !important; line-height: 1.3;
        }
        .nap-emp-nap {
            font-family: monospace; font-size: 11px !important;
            color: var(--blue-600) !important; margin-top: 3px;
            background: var(--blue-100); padding: 1px 6px;
            border-radius: 4px; display: inline-block;
        }
        .nap-emp-noenc {
            font-size: 10px !important; color: #dc2626 !important; margin-top: 3px;
        }

        /* Periodo badge */
        .nap-td-periodo { text-align: center !important; }
        .nap-periodo-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--blue-100); color: var(--blue-700);
            font-size: 12px; font-weight: 700;
            border: 1px solid var(--blue-200);
        }

        /* Año */
        .nap-td-anio {
            font-family: monospace !important;
            font-size: 12px !important;
            color: var(--gray-600) !important;
        }

        /* Archivo */
        .nap-td-file {
            font-size: 11px !important;
            color: var(--gray-500) !important;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .nap-file-icon { margin-right: 5px; }

        /* Fila separadora entre empleados */
        .nap-row-sep td {
            padding: 0 !important; height: 6px !important;
            background: var(--gray-100) !important;
            border: none !important;
        }

        /* Filas de empleado no encontrado */
        .nap-row-notfound .nap-td-nombre {
            border-left-color: #fca5a5 !important;
            background: #fef2f2 !important;
        }
        .nap-row-notfound td { background: #fff9f9 !important; }

        /* ── Footer ── */
        .nap-modal-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 24px;
            border-top: 1px solid var(--gray-100);
            flex-shrink: 0; gap: 12px;
            background: var(--gray-50);
        }
        .nap-modal-stats { font-size: 12px; color: var(--gray-500); }
        .nap-modal-stats b { color: var(--gray-800); }


        /* ── Prefactura ── */
        .pf-section { padding: 20px 24px; border-bottom: 1px solid var(--gray-100); }
        .pf-section:last-child { border-bottom: none; }
        .pf-section-title {
            font-family: 'Syne', sans-serif; font-weight: 700; font-size: 12px;
            letter-spacing: .06em; text-transform: uppercase; color: var(--blue-600);
            margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
        }
        .pf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; }
        .pf-field label {
            display: block; font-size: 10px; font-weight: 600; color: var(--gray-400);
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px;
        }
        .pf-field .pf-static {
            font-size: 13px; font-weight: 600; color: var(--gray-800);
            padding: 7px 0;
        }
        .pf-field input {
            width: 100%; border: 1px solid var(--blue-200); background: var(--blue-50);
            border-radius: var(--radius-sm); padding: 7px 10px; font-size: 13px;
            color: var(--gray-800); font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .pf-field input:focus { outline: none; border-color: var(--blue-400); background: var(--white); }
        .pf-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .pf-table th {
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            color: var(--gray-500); background: var(--gray-50); padding: 7px 9px; text-align: left;
            border: 1px solid var(--gray-200);
        }
        .pf-table td {
            font-size: 12px; color: var(--gray-700); padding: 7px 9px;
            border: 1px solid var(--gray-200);
        }
        .pf-totals { margin-top: 14px; margin-left: auto; width: 320px; }
        .pf-totals div {
            display: flex; justify-content: space-between; padding: 5px 0;
            font-size: 12px; color: var(--gray-600);
        }
        .pf-totals .pf-total-final {
            border-top: 2px solid var(--blue-600); margin-top: 6px; padding-top: 8px;
            font-weight: 700; font-size: 14px; color: var(--blue-700);
        }
        .pf-letra { font-size: 11px; color: var(--gray-500); font-style: italic; margin-top: 10px; }
        #pfDoc { background: var(--white); }
    </style>
</head>

<?php
include("./funciones/clas_carobra.php");
?>

<body>

<?php
    $fg      = $_SESSION["ultimoAcceso"];
    $fa      = date("Y-n-j H:i:s");
    $periodo = (strtotime($fa) - strtotime($fg));

    if ($periodo > 6000) {
        session_destroy();
        mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "http://carobra.ddns.me:8888");
    } else {
        $_SESSION["ultimoAcceso"] = $fa;
        $_SESSION["autentifica"]  = "p";
    }

    if ($_SESSION["autentifica"] != "p") {
        session_destroy();
        mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "index.php");
    } else {
        reg_acceso($_SESSION["nombre"], "empleados_dp");
        $_SESSION["autentifica"] = "";

        $divpie        = new div("pie");
        $divopcion     = new div("opcion");
        $divcabecera   = new div("cabecera");
        $divcontenido  = new div("contenido");
        $divcontenedor = new div("contenedor");
        $imgpie        = new img("imgpie",      "./img/pie.png");
        $divusuario    = new div("usuario", '', "absolute", "97%");
        $imgcabecera   = new img("imgcabecera", "./img/cabecera.png");

        $divcabecera->crear();
            $imgcabecera->crear();
            $imgcabecera->css("width", "100%");
            titulo("Subir comprobante de pagos");
            $pag = "PAGOS_Y_FACTURAS_NAP";
            menu();
        $divcabecera->close();

        $divpie->crear();
            $imgpie->crear();
            $imgpie->css("width", "100%");
            $divusuario->crear();
                echo "<br><br>";
                $divusuario->css("top",        "10px");
                $divusuario->css("text-align", "left");
?>

	<div id="vinculos" style="margin-top: 10px; margin-left: 25px;">
		<?php include("vinculos.php"); ?>
	</div>

<?php
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: " . $_SESSION["nombre"] . "</spam>";
        echo "<div style='width:100%; text-align:right; margin-top:-25px;'>";
        echo "<spam id='email'>Email</spam>";
        echo "&nbsp;&nbsp;&nbsp;<spam id='salir'>Salir</spam>";
        echo "</div>";
        $divusuario->close();
        $divpie->close();

        $divcontenedor->crear();
        $divopcion->crear();
        $divopcion->css("background", "#FFFFFF");
        $divopcion->close();

        $divcontenido->crear();
        $divcontenido->css("overflow-y", "auto");

        $conecta  = new conector();
        $fecha    = addslashes(trim($_POST["fecha"]    ?? ''));
        $mensaje  = addslashes(trim($_POST["mensaje"]  ?? ''));
        $dispersa = addslashes(trim($_POST["dispersa"] ?? ''));

        $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"]));
?>

    <!-- ============================================================
         SECCIÓN 7: CONTENIDO PRINCIPAL — EXTRACTOR NAP
         ============================================================ -->
    <div class="container" style="padding: 20px;">

        <!-- Topbar de acciones -->
        <div class="nap-topbar">
            <div>
                <h1>Extractor de Comprobantes NAP</h1>
                <p>Carga PDFs y extrae conceptos de pago automáticamente · carpeta: facturas/Pagos_nap/</p>
            </div>
            <div class="nap-actions">
                <button class="btn btn-ghost" id="btnClear">🗑 Limpiar</button>
                <button class="btn btn-ghost" id="btnPrefactura" disabled>📑 Prefactura</button>
                <button class="btn btn-primary" id="btnProcess" disabled>⚡ Procesar</button>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row">

            <div class="stat-card">
                <div class="stat-icon blue">💰</div>
                <div class="stat-info">
                    <div class="stat-val" id="sMonto">$0.00</div>
                    <div class="stat-label">Monto capturado</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">📄</div>
                <div class="stat-info">
                    <div class="stat-val" id="sPages">0</div>
                    <div class="stat-label">Páginas leídas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber">📁</div>
                <div class="stat-info">
                    <div class="stat-val" id="sFiles">0</div>
                    <div class="stat-label">Archivos cargados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div class="stat-info">
                    <div class="stat-val" id="sDone">0</div>
                    <div class="stat-label">Guardados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">❌</div>
                <div class="stat-info">
                    <div class="stat-val" id="sErr">0</div>
                    <div class="stat-label">Errores</div>
                </div>
            </div>
        </div>

        <!-- Work grid -->
        <div class="work-grid">

            <!-- Upload card -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Cargar documentos</div>
                        <div class="card-sub">Soporta múltiples archivos · hasta 200 páginas</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">

						<!-- Periodo -->
						<div class="col-md-6 col-6 mb-3">
							<label for="sel-periodo">Periodo</label>
							<select name="sel-periodo" id="sel-periodo" class="form-control form-control-sm carobra" required style="border-radius: 0.6rem;">
								<option value="">Periodo</option>
								<option value="Periodo_01">Periodo 01</option>
								<option value="Periodo_02">Periodo 02</option>
								<option value="Periodo_03">Periodo 03</option>
								<option value="Periodo_04">Periodo 04</option>
								<option value="Periodo_05">Periodo 05</option>
								<option value="Periodo_06">Periodo 06</option>
								<option value="Periodo_07">Periodo 07</option>
								<option value="Periodo_08">Periodo 08</option>
								<option value="Periodo_09">Periodo 09</option>
								<option value="Periodo_10">Periodo 10</option>
								<option value="Periodo_11">Periodo 11</option>
								<option value="Periodo_12">Periodo 12</option>
								<option value="Periodo_13">Periodo 13</option>
							</select>
						</div>



						<!-- Año -->
						<div class="col-md-6 col-6 mb-3">
							<label for="anio">Año</label>
							<select class="form-control form-control-sm carobra" name="anio" id="anio" style="border-radius: 0.6rem;">
								<option value="">Año</option>
								<?php for($i = date('Y'); $i >= 2026; $i--): ?>
									<option value="<?= $i ?>"><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>

                    </div>
                    <div class="dropzone" id="dropzone">
                        <input type="file" id="fileInput" accept=".pdf" multiple/>
                        <div class="dz-visual">📄</div>
                        <div class="dz-title">Arrastra tus PDFs aquí</div>
                        <div class="dz-hint">o haz clic para explorar archivos</div>
                        <div class="dz-types">📎 PDF · Múltiples archivos · Sin límite</div>
                    </div>
                    <div class="queue-header">
                        <span class="queue-label">Cola de archivos</span>
                        <span class="badge-count" id="queueCount">0</span>
                    </div>
                    <div class="queue-list" id="queueList">
                        <div class="empty-queue">Sin archivos — arrastra un PDF para comenzar</div>
                    </div>
                    <div class="progress-section" id="progressSection">
                        <div class="prog-header">
                            <span class="prog-label" id="progLabel">Procesando…</span>
                            <span class="prog-pct" id="progPct">0%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" id="progFill"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results card -->
            <div class="card results-card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Resultados de extracción</div>
                        <div class="card-sub" id="resultsCount">Sin resultados</div>
                    </div>
                </div>
                <div class="results-body" id="resultsBody">
                    <div class="empty-results">
                        <div class="er-icon">📋</div>
                        <h3>Sin datos aún</h3>
                        <p>Carga uno o más PDFs y presiona <strong>Procesar</strong> para extraer los conceptos de pago.</p>
                    </div>
                </div>
            </div>

        </div><!-- /work-grid -->

    </div><!-- /container -->

<?php
        $divcontenido->close();
        $divpie->close();
?>

    <script language='javascript'>
        var hc = calcula_h(125);
        var h  = $(window).outerHeight() - hc;
        $('#pie').css("top", h + 5);

        hc = calcula_h(85);
        h  = $(window).outerHeight() - (hc * 2);
        $('#contenedor').css("top",    hc - 15);
        $('#contenedor').css("height", h);

        hc = calcula_h(45);
        $('#menu').css("top", hc - 20);

        $('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
    </script>

<?php } ?>



<div id="nap-toast"></div>

<!-- ═══════ MODAL PREFACTURA ═══════ -->
<div class="nap-modal-backdrop" id="pfBackdrop">
    <div class="nap-modal" style="max-width: 820px;">
        <div class="nap-modal-header">
            <div>
                <div class="nap-modal-title">Prefactura — Formato de captura</div>
                <div class="nap-modal-sub">Captura previa al timbrado · CFDI 4.0</div>
            </div>
            <button class="nap-modal-close" id="pfClose">✕</button>
        </div>

        <div class="nap-modal-body">
            <div id="pfDoc">

                <div class="pf-section">
                    <div class="pf-section-title">📌 Datos del emisor (NAP)</div>
                    <div class="pf-grid">
                        <div class="pf-field">
                            <label>Razón social</label>
                            <input type="text" id="pfEmisorNombre" placeholder="Nombre del comisionista">
                        </div>
                        <div class="pf-field">
                            <label>RFC</label>
                            <input type="text" id="pfEmisorRfc" placeholder="RFC">
                        </div>
                        <div class="pf-field">
                            <label>Régimen fiscal</label>
                            <input type="text" id="pfEmisorRegimen" value="612 - Personas Físicas con Actividades Empresariales y Profesionales">
                        </div>
                        <div class="pf-field">
                            <label>Lugar exp. (C.P.)</label>
                            <input type="text" id="pfEmisorCp" placeholder="C.P.">
                        </div>
                    </div>
                </div>

                <div class="pf-section">
                    <div class="pf-section-title">🏢 Datos del receptor</div>
                    <div class="pf-grid">
                        <div class="pf-field"><label>Nombre / Razón social</label>
                            <div class="pf-static" id="pfRecNombre"></div></div>
                        <div class="pf-field"><label>RFC</label>
                            <div class="pf-static" id="pfRecRfc"></div></div>
                        <div class="pf-field"><label>Régimen fiscal receptor</label>
                            <div class="pf-static" id="pfRecRegimen"></div></div>
                        <div class="pf-field"><label>C.P. domicilio fiscal</label>
                            <div class="pf-static" id="pfRecCp"></div></div>
                        <div class="pf-field"><label>Uso del CFDI</label>
                            <div class="pf-static" id="pfRecUso"></div></div>
                    </div>
                </div>

                <div class="pf-section">
                    <div class="pf-section-title">🧾 Datos generales del comprobante</div>
                    <div class="pf-grid">
                        <div class="pf-field"><label>Tipo de comprobante</label>
                            <div class="pf-static" id="pfCompTipo"></div></div>
                        <div class="pf-field"><label>Serie / Folio</label>
                            <input type="text" id="pfFolio" placeholder="Serie-Folio"></div>
                        <div class="pf-field"><label>Forma de pago</label>
                            <div class="pf-static" id="pfCompForma"></div></div>
                        <div class="pf-field"><label>Método de pago</label>
                            <div class="pf-static" id="pfCompMetodo"></div></div>
                        <div class="pf-field"><label>Moneda</label>
                            <div class="pf-static" id="pfCompMoneda"></div></div>
                        <div class="pf-field"><label>Tipo de cambio</label>
                            <div class="pf-static" id="pfCompCambio"></div></div>
                        <div class="pf-field"><label>Exportación</label>
                            <div class="pf-static" id="pfCompExport"></div></div>
                        <div class="pf-field"><label>Fecha</label>
                            <input type="date" id="pfFecha"></div>
                    </div>
                </div>

                <div class="pf-section">
                    <div class="pf-section-title">📋 Conceptos</div>
                    <table class="pf-table">
                        <thead>
                            <tr>
                                <th>Cant.</th><th>Clave Unidad</th><th>Clave Prod/Serv</th>
                                <th>Descripción</th><th>Valor Unitario</th><th>Importe</th>
                            </tr>
                        </thead>
                        <tbody id="pfConceptosBody"></tbody>
                    </table>

                    <div class="pf-totals" id="pfTotales"></div>
                    <div class="pf-letra" id="pfLetra"></div>
                </div>

            </div>
        </div>

        <div class="nap-modal-footer">
            <div class="nap-modal-stats">Generado a partir de <b id="pfCountRegistros">0</b> páginas capturadas</div>
            <div style="display:flex; gap:10px;">
                <button class="btn btn-ghost" id="pfClose2">Cerrar</button>
                <button class="btn btn-primary" id="pfDownload">⬇ Vista previa PDF</button>
            </div>
        </div>
    </div>
</div>

</body>

<!-- ============================================================
     SCRIPTS — al final del body para no bloquear
     ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();
        $('#men_pie').css("top", h);
        $('#tmenu td').click(function() {
            var modulo = $(this).text().replace(/ /gi, "_");
            $("#opcion").load("menu.php?modulo=" + modulo);
        });
        $("#salir").click(function() { window.location = "http://carobra.ddns.me"; });
        $("#email").click(function() { window.open("http://webmail.carobra.com.mx/imp/login.php"); });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
        /* ════════════════════════════════════════════════════════════
    EXTRACTOR NAP — JS completo y ordenado
    Reemplaza TODO el contenido del <script> final en tu PHP
    ════════════════════════════════════════════════════════════ */

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

    const NAP_API = "./comprobantes_nap_api.php";

    const PREFACTURA_CONFIG = {
        receptor: {
            nombre:  "DARACA ASESORES EN PENSIONES",
            rfc:     "DAP1909217J6",
            regimen: "601 - General de Ley Personas Morales",
            cp:      "45030",
            usoCfdi: "G03 - Gastos en general"
        },
        comprobante: {
            tipo:        "I - Ingreso",
            formaPago:   "03 - Transferencia electrónica de fondos",
            metodoPago:  "PUE - Pago en una sola exhibición",
            moneda:      "MXN - Peso Mexicano",
            tipoCambio:  "N/A",
            exportacion: "01 - No aplica"
        }
    };

    /* ════════════════════════════════
    STATE
    ════════════════════════════════ */
    let napFiles   = [];
    let napResults = [];
    let stPages = 0, stDone = 0, stErrors = 0;

    /* ════════════════════════════════
    DOM — declarados TODOS juntos aquí arriba
    ════════════════════════════════ */
    const napDropzone    = document.getElementById("dropzone");
    const napFileInput   = document.getElementById("fileInput");
    const napQueueList   = document.getElementById("queueList");
    const napQueueCount  = document.getElementById("queueCount");
    const napBtnProcess  = document.getElementById("btnProcess");
    const napBtnClear    = document.getElementById("btnClear");
    const napProgSec     = document.getElementById("progressSection");
    const napProgFill    = document.getElementById("progFill");
    const napProgLabel   = document.getElementById("progLabel");
    const napProgPct     = document.getElementById("progPct");
    const napResultBody  = document.getElementById("resultsBody");
    const napResultCount = document.getElementById("resultsCount");
    const napToast       = document.getElementById("nap-toast");

    const selPeriodo = document.getElementById("sel-periodo");
    const selAnio    = document.getElementById("anio");

    /* ════════════════════════════════
    FILE HANDLING
    ════════════════════════════════ */
    napFileInput.addEventListener("change", e => napAddFiles([...e.target.files]));

    napDropzone.addEventListener("dragover",  e => { e.preventDefault(); napDropzone.classList.add("drag-over"); });
    napDropzone.addEventListener("dragleave", ()  => napDropzone.classList.remove("drag-over"));
    napDropzone.addEventListener("drop", e => {
        e.preventDefault();
        napDropzone.classList.remove("drag-over");
        napAddFiles([...e.dataTransfer.files].filter(f => f.type === "application/pdf"));
    });

    function napAddFiles(newFiles) {
        newFiles.forEach(f => {
            if (!napFiles.find(x => x.file.name === f.name && x.file.size === f.size))
                napFiles.push({ file: f, status: "pending" });
        });
        napRenderQueue();
        napBtnProcess.disabled = napFiles.length === 0;
        napUpdateStats();
    }

    function napRenderQueue() {
        napQueueCount.textContent = napFiles.length;
        if (!napFiles.length) {
            napQueueList.innerHTML = `<div class="empty-queue">Sin archivos — arrastra un PDF para comenzar</div>`;
            return;
        }
        const labels = { pending: "Pendiente", working: "Leyendo…", done: "Listo", error: "Error" };
        napQueueList.innerHTML = napFiles.map(f => `
            <div class="qi">
                <div class="qi-thumb">📄</div>
                <div style="flex:1;min-width:0">
                    <div class="qi-name">${f.file.name}</div>
                    <div class="qi-size">${(f.file.size / 1024).toFixed(0)} KB</div>
                </div>
                <span class="qi-status ${f.status}">${labels[f.status]}</span>
            </div>`).join("");
    }


    /* ════════════════════════════════
    PROCESS
    ════════════════════════════════ */
    napBtnProcess.addEventListener("click", napProcessAll);

    async function napProcessAll() {
        if (!napFiles.length) return;

        if (!selPeriodo.value || !selAnio.value) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Selecciona un periodo y año antes de procesar.",
            });
            return;
        }

        napBtnProcess.disabled = true;
        napProgSec.classList.add("visible");
        napResults = [];
        stPages = stDone = stErrors = 0;
        napUpdateStats();
        napRenderTable();

        const pending = napFiles.filter(f => f.status !== "done");
        let processed = 0;

        for (const entry of pending) {
            entry.status = "working";
            napRenderQueue();
            napProgLabel.textContent = `Leyendo: ${entry.file.name}`;

            try {
                const fileBytes = await entry.file.arrayBuffer();
                const textPages = await napExtractText(fileBytes);
                entry.status = "done";

                for (let i = 0; i < textPages.length; i++) {
                    const pageNum = i + 1;
                    const text = textPages[i];

                    /* Concepto de pago completo, ej: "COMISIONES NAP 2403034735 P0526" */
                    const matchConcepto = text.match(/Concepto de pago:\s*([\w\s]+?)(?=\s*(?:Referencia|Importe IVA|Tipo operaci|Folio|Banco Ve|Para presentar))/i);
                    const conceptoCompleto = matchConcepto ? matchConcepto[1].trim() : "";

                    /* Nos quedamos SOLO con el número de NAP (sin "COMISIONES NAP"
                       ni el folio "P0526" al final). Si por algún motivo no
                       aparece la palabra "NAP", se cae al concepto completo como
                       respaldo para no perder el dato. */
                    const matchNap = conceptoCompleto.match(/NAP\s+([A-Za-z0-9]+)/i);
                    const concepto = matchNap ? matchNap[1].trim() : (conceptoCompleto || "DESCONOCIDO");

                    const matchFecha = text.match(/Fecha de captura:\s*([\d\/]+)/i);
                    const fechaCaptura = matchFecha ? matchFecha[1].trim() : "";

                    const matchClave = text.match(/Clave de rastreo:\s*([A-Za-z0-9]+)/i);
                    const claveRastreo = matchClave ? matchClave[1].trim() : "";

                    const matchMonto = text.match(/Monto operaci[oó]n:\s*\$?\s*([\d,]+\.\d{2})/i);
                    const montoOperacion = matchMonto ? matchMonto[1].replace(/,/g, "") : "0";

                    stPages++;
                    napUpdateStats();

                    const pageBase64 = await napExtractPageB64(fileBytes, pageNum);

                    const saved = await napSaveToServer({
                        action: "keepFolder",
                        anio: selAnio.value,
                        periodo: selPeriodo.value,
                        pagina: pageNum,
                        conceptoPago: concepto,
                        fechaCaptura: fechaCaptura,
                        claveRastreo: claveRastreo,
                        montoOperacion: montoOperacion,
                        fileName: entry.file.name,
                        pdfBase64: pageBase64
                    });

                    if (saved.status === "ok") {
                        stDone++;
                    } else {
                        stErrors++;
                    }

                    napUpdateStats();

                    napResults.push({
                        pagina: pageNum,
                        conceptoPago: concepto,
                        originalFile: entry.file.name,
                        serverFile: saved.file || "No generado",
                        serverPath: saved.file || "",
                        saved: saved.status === "ok"
                    });

                    napRenderTable();
                }

            } catch (err) {
                console.error(err);
                entry.status = "error";
                stErrors++;
                napUpdateStats();
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Error al leer: " + entry.file.name,
                });
            }

            processed++;
            const pct = Math.round(processed / pending.length * 100);
            napProgFill.style.width = pct + "%";
            napProgPct.textContent = pct + "%";
            napRenderQueue();
        }

        napProgLabel.textContent = "✓ Procesamiento completado";
        Swal.fire({
            title: "Procesamiento completado",
            text: `${stDone} páginas guardadas en facturas/Pagos_nap/`,
            icon: "success"
        });
        napBtnProcess.disabled = false;
    }

    /* ════════════════════════════════
    EXTRAER TEXTO — pdf.js
    ════════════════════════════════ */
    async function napExtractText(arrayBuffer) {
        const pdf   = await pdfjsLib.getDocument({ data: arrayBuffer.slice(0) }).promise;
        const pages = [];
        for (let i = 1; i <= pdf.numPages; i++) {
            const page    = await pdf.getPage(i);
            const content = await page.getTextContent();
            pages.push(content.items.map(it => it.str).join(" "));
        }
        return pages;
    }

    /* ════════════════════════════════
    EXTRAER PÁGINA COMO PDF — pdf-lib
    ════════════════════════════════ */
    async function napExtractPageB64(arrayBuffer, pageNum) {
        const srcDoc = await PDFLib.PDFDocument.load(arrayBuffer.slice(0));
        const newDoc = await PDFLib.PDFDocument.create();
        const [page] = await newDoc.copyPages(srcDoc, [pageNum - 1]);
        newDoc.addPage(page);
        const bytes  = await newDoc.save();
        return btoa(Array.from(new Uint8Array(bytes)).map(b => String.fromCharCode(b)).join(""));
    }

    /* ════════════════════════════════
    GUARDAR EN SERVIDOR
    ════════════════════════════════ */
    async function napSaveToServer(datos) {
        try {
            const res = await fetch(NAP_API, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });

            return await res.json();

        } catch (e) {
            return {
                status: "error",
                msg: e.message
            };
        }
    }

    /* ════════════════════════════════
    RENDER TABLE (resultados)
    ════════════════════════════════ */
    function napRenderTable() {
        const data = [...napResults];

        if (!data.length) {
            napResultCount.textContent = "Sin resultados";

            napResultBody.innerHTML = `
                <div class="empty-results">
                    <div class="er-icon">📋</div>
                    <h3>Sin datos aún</h3>
                    <p>Carga uno o más PDFs y presiona <strong>Procesar</strong>.</p>
                </div>`;
            return;
        }
        napResultCount.textContent = `${data.length} registro${data.length !== 1 ? "s" : ""}`;
        napResultBody.innerHTML = `
            <div class="table-wrap">
                <table class="nap-table">
                    <thead>
                        <tr>
                            <th>Pág.</th>
                            <th>NAP</th>
                            <th>Archivo origen</th>
                            <th style="text-align:right">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(r => console.log(r) || `
                            <tr>
                                <td class="td-pg">#${r.pagina}</td>
                                <td class="td-concept">${r.conceptoPago}</td>
                                <td class="td-file">
                                    <a href="#"
                                        onclick="verPdf('./${r.serverPath}'); return false;">
                                        <img src="./img/logo_pdf.jpg" width="40" height="40" alt="PDF" class="nap-file-icon"/>
                                    </a>
                                </td>
                                <td class="td-status">
                                    ${r.saved === null
                                        ? `<span class="badge badge-wait">En espera</span>`
                                        : r.saved
                                            ? `<span class="badge badge-ok">✓ Guardado</span>`
                                            : `<span class="badge badge-err">✗ Error</span>`}
                                </td>
                            </tr>`).join("")}
                    </tbody>
                </table>
            </div>`;
    }

    /* ════════════════════════════════
    STATS
    ════════════════════════════════ */
    function napUpdateStats() {
        document.getElementById("sPages").textContent = stPages;
        document.getElementById("sFiles").textContent = napFiles.length;
        document.getElementById("sDone").textContent  = stDone;
        document.getElementById("sErr").textContent   = stErrors;
    }

    /* ════════════════════════════════
    CLEAR
    ════════════════════════════════ */
    napBtnClear.addEventListener("click", () => {
        napFiles = []; napResults = [];
        stPages = stDone = stErrors = 0;
        napFileInput.value = "";
        napProgSec.classList.remove("visible");
        napProgFill.style.width = "0%";
        napProgPct.textContent  = "0%";
        napBtnProcess.disabled  = true;
        napRenderQueue();
        napRenderTable();
        napUpdateStats();
    });

    /* ════════════════════════════════
    TOAST
    ════════════════════════════════ */
    function napShowToast(msg) {
        napToast.textContent = msg;
        napToast.classList.add("show");
        setTimeout(() => napToast.classList.remove("show"), 3500);
    }


    function verPdf(url) {
    Swal.fire({
        width: '90%',
        html: `
            <iframe
                src="${url}"
                style="width:100%;height:700px;border:none">
            </iframe>
        `,
        showConfirmButton: false
    });
}



</script>

</html>