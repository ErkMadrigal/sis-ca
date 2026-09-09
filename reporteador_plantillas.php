<?php
/* ============================================================
   SECCIÓN 1: INICIO DE SESIÓN
   ============================================================ */
session_start();
?>

<!-- ============================================================
     SECCIÓN 2: HTML - CABECERA DEL DOCUMENTO
     ============================================================ -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
	<title>CAROBRA Consultores Especializados</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="./img/favicon.ico">

	<!-- JS propio -->
	<script type="text/javascript" src="./js/carobra.js"></script>

	<!-- jQuery -->
	<script src="./jquery/jquery-3.3.1.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
	<script src="./popper/popper.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>

	<!-- Autocomplete -->
	<script src="./twitter/typeahead.bundle.js"></script>

	<!-- Datepicker -->
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

	<!-- CSS propio -->
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f5f7fb;
            padding:30px;
            color:#1e293b;
        }

        /* ============================================
        BOTON NUEVO REPORTE
        ============================================ */

        #btnNuevoReporte{
            background:#2563eb;
            color:white;
            border:none;
            padding:14px 22px;
            border-radius:16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.2s;
            box-shadow:0 5px 15px rgba(37,99,235,.2);
        }

        #btnNuevoReporte:hover{
            transform:translateY(-2px);
        }

        /* ============================================
        GRID REPORTES
        ============================================ */

        .grid-reportes{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:20px;
            margin-top:25px;
        }

        /* ============================================
        CARD REPORTE
        ============================================ */

        .card-reporte{
            background:white;
            border-radius:24px;
            padding:25px;
            box-shadow:0 5px 20px rgba(0,0,0,.08);
            cursor:pointer;
            transition:.25s;
            border:1px solid #eef2ff;
        }

        .card-reporte:hover{
            transform:translateY(-5px);
            box-shadow:0 15px 35px rgba(0,0,0,.12);
        }

        .card-reporte h3{
            color:#0f172a;
            margin-bottom:10px;
            font-size:18px;
        }

        .card-reporte p{
            color:#64748b;
            font-size:14px;
        }

        /* ============================================
        MODAL FULLSCREEN CONSTRUCTOR
        ============================================ */

        #modalConstructor{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.7);
            z-index:999999;
            display:none;
            justify-content:center;
            align-items:center;
            padding:20px;
            backdrop-filter:blur(6px);
        }

        .mc-content{
            width:100%;
            max-width:1400px;
            height:90vh;
            background:white;
            border-radius:24px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            animation:modalIn .2s ease;
        }

        @keyframes modalIn{
            from{ transform:translateY(16px) scale(.98); opacity:0; }
            to{   transform:translateY(0)    scale(1);   opacity:1; }
        }

        /* ============================================
        MODAL HEADER
        ============================================ */

        .mc-header{
            padding:18px 24px;
            border-bottom:1px solid #e2e8f0;
            display:flex;
            align-items:center;
            gap:14px;
            flex-shrink:0;
            background:white;
        }

        .mc-header h2{
            font-size:18px;
            font-weight:700;
            color:#0f172a;
            flex:1;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .mc-select-config{
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:8px 12px;
            font-size:13px;
            color:#0f172a;
            background:white;
            cursor:pointer;
            min-width:200px;
            outline:none;
            transition:border-color .2s;
        }

        .mc-select-config:focus{
            border-color:#2563eb;
        }

        .mc-btn-close{
            width:34px;
            height:34px;
            border:none;
            border-radius:10px;
            background:#f1f5f9;
            color:#64748b;
            font-size:16px;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            transition:.15s;
        }

        .mc-btn-close:hover{
            background:#e2e8f0;
            color:#0f172a;
        }

        /* ============================================
        MODAL BODY — 2 COLUMNAS
        ============================================ */

        .mc-body{
            flex:1;
            display:flex;
            overflow:hidden;
        }

        /* ============================================
        LADO IZQUIERDO — CAMPOS
        ============================================ */

        .mc-campos{
            width:240px;
            flex-shrink:0;
            border-right:1px solid #e2e8f0;
            display:flex;
            flex-direction:column;
            overflow:hidden;
        }

        .mc-campos-title{
            padding:14px 16px;
            font-size:11px;
            font-weight:700;
            color:#94a3b8;
            text-transform:uppercase;
            letter-spacing:.06em;
            border-bottom:1px solid #f1f5f9;
            flex-shrink:0;
        }

        .mc-campos-list{
            flex:1;
            overflow-y:auto;
            padding:10px;
            display:flex;
            flex-direction:column;
            gap:6px;
        }

        .mc-campo-item{
            display:flex;
            align-items:center;
            gap:8px;
            padding:10px 12px;
            border-radius:10px;
            background:#f8fafc;
            border:1px solid #e2e8f0;
            cursor:grab;
            transition:.15s;
            font-size:13px;
            color:#0f172a;
            font-weight:500;
            user-select:none;
        }

        .mc-campo-item:hover{
            background:#eff6ff;
            border-color:#bfdbfe;
            color:#1d4ed8;
        }

        .mc-campo-item .drag-icon{
            color:#cbd5e1;
            font-size:12px;
            flex-shrink:0;
        }

        /* ============================================
        LADO DERECHO — CONSTRUCTOR / RESULTADOS
        ============================================ */

        .mc-derecho{
            flex:1;
            display:flex;
            flex-direction:column;
            overflow:hidden;
        }

        .mc-derecho-title{
            padding:14px 16px;
            font-size:11px;
            font-weight:700;
            color:#94a3b8;
            text-transform:uppercase;
            letter-spacing:.06em;
            border-bottom:1px solid #f1f5f9;
            flex-shrink:0;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }

        /* ============================================
        DROPZONE
        ============================================ */

        .mc-dropzone{
            flex:1;
            overflow:auto;
            padding:16px;
            transition:background .2s;
        }

        .mc-dropzone.drag-over{
            background:#eff6ff;
        }

        .mc-tabla{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }

        .mc-tabla th{
            background:#f1f5f9;
            border:1px solid #e2e8f0;
            padding:12px 14px;
            min-width:160px;
            cursor:move;
            white-space:nowrap;
        }

        .mc-tabla td{
            border:1px solid #e2e8f0;
            padding:10px 14px;
            background:white;
        }

        .mc-placeholder{
            height:100%;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap:10px;
            color:#94a3b8;
            font-size:14px;
        }

        .mc-placeholder span{
            font-size:40px;
        }

        /* ============================================
        HEADER COLUMNA
        ============================================ */

        .col-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .col-header label{
            font-size:13px;
            font-weight:600;
            color:#0f172a;
        }

        .btn-del-col{
            background:#fee2e2;
            color:#ef4444;
            border:none;
            width:24px;
            height:24px;
            border-radius:6px;
            cursor:pointer;
            font-size:12px;
            flex-shrink:0;
            transition:.15s;
        }

        .btn-del-col:hover{
            background:#ef4444;
            color:white;
        }

        /* ============================================
        MODAL FOOTER
        ============================================ */

        .mc-footer{
            padding:14px 20px;
            border-top:1px solid #e2e8f0;
            display:flex;
            gap:10px;
            justify-content:flex-end;
            flex-shrink:0;
            background:#f8fafc;
        }

        .mc-footer button{
            border:none;
            padding:11px 20px;
            border-radius:12px;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
            transition:.2s;
        }

        .mc-footer button:hover{
            transform:translateY(-1px);
        }

        #mcBtnGuardar{
            background:#e0f2fe;
            color:#0369a1;
        }

        #mcBtnGenerar{
            background:#0177cb;
            color:white;
        }

        #mcBtnExportar{
            background:#0177cb;
            color:white;
            display:none;
        }

        /* ============================================
        MODAL NUEVO REPORTE (sin cambios)
        ============================================ */

        .modal{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.6);
            z-index:999999;
            display:none;
            justify-content:center;
            align-items:center;
            padding:1.5rem;
            backdrop-filter:blur(4px);
        }

        .modal-content{
            width:100%;
            max-width:680px;
            max-height:90vh;
            background:white;
            border-radius:24px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            animation:modalIn .2s ease;
        }

        .modal-header{
            padding:24px 28px 20px;
            border-bottom:1px solid #e2e8f0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-shrink:0;
            background:white;
        }

        .modal-header h2{
            margin:0;
            font-size:20px;
            font-weight:700;
            color:#0f172a;
        }

        .btnCloseModal{
            width:34px;
            height:34px;
            border:none;
            border-radius:10px;
            background:#f1f5f9;
            color:#64748b;
            font-size:16px;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:.15s;
            flex-shrink:0;
        }

        .btnCloseModal:hover{
            background:#e2e8f0;
            color:#0f172a;
        }

        .modal-body{
            flex:1;
            overflow-y:auto;
            padding:24px 28px;
            display:flex;
            flex-direction:column;
            gap:20px;
        }

        .modal-actions{
            flex-shrink:0;
            background:#f8fafc;
            padding:16px 28px;
            border-top:1px solid #e2e8f0;
            display:flex;
            justify-content:flex-end;
            gap:10px;
        }

        .modal-actions button{
            border:none;
            padding:10px 20px;
            border-radius:10px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.15s;
        }

        .modal-actions button:first-child{
            background:white;
            border:1.5px solid #e2e8f0;
            color:#64748b;
        }

        .modal-actions button:nth-child(2){
            background:#e0f2fe;
            color:#0369a1;
        }

        .modal-actions button:last-child{
            background:#2563eb;
            color:white;
        }

        .form-group{
            margin:0;
        }

        .form-group label{
            display:block;
            margin-bottom:8px;
            font-size:11px;
            font-weight:700;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .form-group input,
        .form-group textarea{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:12px;
            padding:12px 16px;
            font-size:14px;
            color:#0f172a;
            background:#f8fafc;
            transition:border-color .2s, box-shadow .2s;
        }

        .form-group textarea{
            min-height:130px;
            resize:vertical;
            font-family:monospace;
            font-size:13px;
            line-height:1.6;
        }

        .form-group input:focus,
        .form-group textarea:focus{
            outline:none;
            border-color:#2563eb;
            background:white;
            box-shadow:0 0 0 3px rgba(37,99,235,.12);
        }

        #camposDetectados{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
            gap:10px;
        }

        .campo-detectado{
            background:#eff6ff;
            border:1px solid #bfdbfe;
            border-radius:12px;
            padding:12px 14px;
        }

        .campo-detectado h4{
            margin:0 0 8px;
            font-size:11px;
            font-weight:700;
            color:#3b82f6;
            text-transform:uppercase;
        }

        .aliasCampo{
            width:100%;
            border:1px solid #dbeafe !important;
            border-radius:8px !important;
            padding:7px 10px !important;
            font-size:13px !important;
            background:white !important;
            color:#1e40af !important;
        }

                /* ============================================
        BUSCADOR USUARIOS
        ============================================ */

        .usuario-search-wrap{
            position:relative;
        }

        .usuario-search-input{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:12px;
            padding:12px 16px;
            font-size:14px;
            color:#0f172a;
            background:#f8fafc;
            outline:none;
            transition:border-color .2s, box-shadow .2s;
        }

        .usuario-search-input:focus{
            border-color:#2563eb;
            background:white;
            box-shadow:0 0 0 3px rgba(37,99,235,.12);
        }

        .usuario-dropdown{
            position:absolute;
            top:calc(100% + 6px);
            left:0;
            right:0;
            background:white;
            border:1.5px solid #e2e8f0;
            border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,.1);
            z-index:9999;
            max-height:200px;
            overflow-y:auto;
            display:none;
        }

        .usuario-dropdown-item{
            padding:10px 14px;
            font-size:13px;
            cursor:pointer;
            color:#0f172a;
            transition:.15s;
            border-bottom:1px solid #f1f5f9;
        }

        .usuario-dropdown-item:last-child{
            border-bottom:none;
        }

        .usuario-dropdown-item:hover{
            background:#eff6ff;
            color:#1d4ed8;
        }

        .usuario-dropdown-item small{
            color:#94a3b8;
            font-size:11px;
            margin-left:6px;
        }

        .usuario-dropdown-empty{
            padding:12px 14px;
            font-size:13px;
            color:#94a3b8;
            text-align:center;
        }

        /* ============================================
        CHIPS USUARIOS SELECCIONADOS
        ============================================ */

        .usuarios-chips{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
            min-height:10px;
        }

        .usuario-chip{
            display:inline-flex;
            align-items:center;
            gap:6px;
            background:#eff6ff;
            border:1.5px solid #bfdbfe;
            border-radius:999px;
            padding:6px 12px;
            font-size:12px;
            font-weight:600;
            color:#1d4ed8;
        }

        .usuario-chip button{
            background:none;
            border:none;
            color:#93c5fd;
            cursor:pointer;
            font-size:14px;
            line-height:1;
            padding:0;
            transition:.15s;
        }

        .usuario-chip button:hover{
            color:#ef4444;
        }

        /* ============================================
        MODAL PERMISOS EXISTENTES
        ============================================ */

        #modalPermisosExistentes{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.75);
            z-index:9999999;
            display:none;
            justify-content:center;
            align-items:center;
            padding:1.5rem;
            backdrop-filter:blur(4px);
        }

        .mpe-content{
            width:100%;
            max-width:860px;
            max-height:90vh;
            background:white;
            border-radius:24px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            animation:modalIn .2s ease;
        }

        .mpe-header{
            padding:20px 24px;
            border-bottom:1px solid #e2e8f0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-shrink:0;
        }

        .mpe-header h2{
            font-size:18px;
            font-weight:700;
            color:#0f172a;
            margin:0;
        }

        .mpe-body{
            flex:1;
            overflow-y:auto;
            padding:20px 24px;
        }

        .mpe-tabla{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }

        .mpe-tabla th{
            background:#f1f5f9;
            border:1px solid #e2e8f0;
            padding:12px 14px;
            text-align:left;
            font-size:11px;
            font-weight:700;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.05em;
        }

        .mpe-tabla td{
            border:1px solid #e2e8f0;
            padding:12px 14px;
            vertical-align:middle;
        }

        .mpe-tabla tr:hover td{
            background:#f8fafc;
        }

        .btn-editar-permiso{
            background:#e0f2fe;
            color:#0369a1;
            border:none;
            padding:6px 14px;
            border-radius:8px;
            cursor:pointer;
            font-size:12px;
            font-weight:600;
            transition:.15s;
            white-space:nowrap;
        }

        .btn-editar-permiso:hover{
            background:#bae6fd;
        }

        .campos-chips-small{
            display:flex;
            flex-wrap:wrap;
            gap:4px;
        }

        .campo-chip-small{
            background:#e8f4fd;
            border:1px solid #bfdbfe;
            color:#0177cb;
            border-radius:999px;
            padding:3px 10px;
            font-size:11px;
            font-weight:600;
        }

        .mpe-footer{
            padding:14px 24px;
            border-top:1px solid #e2e8f0;
            display:flex;
            justify-content:flex-end;
            background:#f8fafc;
        }

        .mpe-footer button{
            border:none;
            padding:10px 20px;
            border-radius:10px;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
            background:white;
            border:1.5px solid #e2e8f0;
            color:#64748b;
            transition:.15s;
        }

        .mpe-footer button:hover{
            background:#f1f5f9;
        }


                /* ============================================
        MODAL FILTROS
        ============================================ */

        #modalFiltros{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.7);
            z-index:9999999;
            display:none;
            justify-content:center;
            align-items:center;
            padding:1.5rem;
            backdrop-filter:blur(6px);
        }

        .mf-content{
            width:100%;
            max-width:680px;
            max-height:90vh;
            background:white;
            border-radius:24px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            animation:modalIn .2s ease;
        }

        .mf-header{
            padding:20px 24px;
            border-bottom:1px solid #e2e8f0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-shrink:0;
        }

        .mf-header h2{
            font-size:18px;
            font-weight:700;
            color:#0f172a;
            margin:0;
        }

        .mf-body{
            flex:1;
            overflow-y:auto;
            padding:20px 24px;
            display:flex;
            flex-direction:column;
            gap:16px;
        }

        .mf-footer{
            padding:14px 24px;
            border-top:1px solid #e2e8f0;
            display:flex;
            gap:10px;
            justify-content:flex-end;
            background:#f8fafc;
            flex-shrink:0;
        }

        .mf-footer button{
            border:none;
            padding:11px 22px;
            border-radius:12px;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
            transition:.2s;
        }

        .mf-footer button:hover{ transform:translateY(-1px); }

        #mfBtnLimpiar{
            background:#f1f5f9;
            color:#64748b;
        }

        #mfBtnAplicar{
            background:#0177cb;
            color:white;
        }

        /* ============================================
        FILTRO ITEM
        ============================================ */

        .filtro-item{
            background:#f8fafc;
            border:1.5px solid #e2e8f0;
            border-radius:16px;
            padding:16px;
        }

        .filtro-item label{
            display:block;
            font-size:11px;
            font-weight:700;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.06em;
            margin-bottom:10px;
        }

        .filtro-rango{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .filtro-rango input{
            flex:1;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:9px 12px;
            font-size:13px;
            color:#0f172a;
            background:white;
            outline:none;
            transition:border-color .2s;
        }

        .filtro-rango input:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,.1);
        }

        .filtro-rango span{
            color:#94a3b8;
            font-size:13px;
            flex-shrink:0;
        }

        .filtro-sino{
            display:flex;
            gap:10px;
        }

        .filtro-sino label{
            display:flex;
            align-items:center;
            gap:6px;
            background:white;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:8px 16px;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
            color:#0f172a;
            text-transform:none;
            letter-spacing:0;
            transition:.15s;
            margin:0;
        }

        .filtro-sino input[type="radio"]{
            accent-color:#2563eb;
        }

        .filtro-sino label:has(input:checked){
            background:#eff6ff;
            border-color:#bfdbfe;
            color:#1d4ed8;
        }

        .filtro-busqueda-wrap{
            position:relative;
        }

        .filtro-busqueda-input{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:9px 12px;
            font-size:13px;
            color:#0f172a;
            background:white;
            outline:none;
            transition:border-color .2s;
        }

        .filtro-busqueda-input:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,.1);
        }

        .filtro-busqueda-dropdown{
            position:absolute;
            top:calc(100% + 4px);
            left:0;
            right:0;
            background:white;
            border:1.5px solid #e2e8f0;
            border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,.1);
            z-index:99999;
            max-height:180px;
            overflow-y:auto;
            display:none;
        }

        .filtro-busqueda-item{
            padding:9px 14px;
            font-size:13px;
            cursor:pointer;
            color:#0f172a;
            border-bottom:1px solid #f1f5f9;
            transition:.15s;
        }

        .filtro-busqueda-item:last-child{ border-bottom:none; }

        .filtro-busqueda-item:hover{
            background:#eff6ff;
            color:#1d4ed8;
        }

        .filtro-chip-selected{
            display:inline-flex;
            align-items:center;
            gap:6px;
            background:#eff6ff;
            border:1.5px solid #bfdbfe;
            border-radius:999px;
            padding:4px 10px;
            font-size:12px;
            font-weight:600;
            color:#1d4ed8;
            margin-top:8px;
            margin-right:4px;
        }

        .filtro-chip-selected button{
            background:none;
            border:none;
            color:#93c5fd;
            cursor:pointer;
            font-size:13px;
            padding:0;
            line-height:1;
            transition:.15s;
        }

        .filtro-chip-selected button:hover{ color:#ef4444; }

        .filtro-chips-wrap{
            display:flex;
            flex-wrap:wrap;
            gap:4px;
            margin-top:8px;
        }

        /* ============================================
        EDITOR CAMPOS
        ============================================ */

        .er-campo-row{
            background:#f8fafc;
            border:1.5px solid #e2e8f0;
            border-radius:14px;
            padding:14px;
            display:flex;
            flex-direction:column;
            gap:8px;
        }

        .er-campo-row:hover{
            border-color:#bfdbfe;
        }

        .er-campo-header{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .er-campo-bd{
            font-size:11px;
            font-weight:700;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.05em;
            flex:1;
        }

        .er-input{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:8px 12px;
            font-size:13px;
            color:#0f172a;
            background:white;
            outline:none;
            transition:border-color .2s;
        }

        .er-input:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,.1);
        }

        .er-select{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:8px 12px;
            font-size:13px;
            color:#0f172a;
            background:white;
            outline:none;
            cursor:pointer;
        }

        .er-textarea{
            width:100%;
            border:1.5px solid #e2e8f0;
            border-radius:10px;
            padding:8px 12px;
            font-size:12px;
            color:#0f172a;
            background:white;
            outline:none;
            font-family:monospace;
            min-height:70px;
            resize:vertical;
            line-height:1.5;
        }

        .er-textarea:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,.1);
        }


        .swal2-container{
            z-index: 99999999 !important;
        }
        /* ============================================
        BOTONES PRINCIPALES
        ============================================ */

        #btnNuevoReporte{
            background:#0177cb;
            color:white;
            border:none;
            padding:14px 22px;
            border-radius:16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.2s;
            box-shadow:0 5px 15px rgba(1,119,203,.2);
        }

        #btnNuevoReporte:hover{ transform:translateY(-2px); }

        #btnAdminPermisos{
            background:#0177cb !important;
            color:white !important;
            border:none;
            padding:14px 22px;
            border-radius:16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.2s;
            box-shadow:0 5px 15px rgba(1,119,203,.2);
            margin-left:10px;
        }

        #btnAdminRoles{
            background:#015a9a !important;
            color:white !important;
            border:none;
            padding:14px 22px;
            border-radius:16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.2s;
            box-shadow:0 5px 15px rgba(1,90,154,.2);
            margin-left:10px;
        }

        #btnEditarReportes{
            background:#01578f !important;
            color:white !important;
            border:none;
            padding:14px 22px;
            border-radius:16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:.2s;
            box-shadow:0 5px 15px rgba(1,87,143,.2);
            margin-left:10px;
        }

        /* ============================================
        BOTONES CONSTRUCTOR FOOTER
        ============================================ */

        #mcBtnGuardar{
            background:#e8f4fd;
            color:#0177cb;
        }

        #mcBtnGenerar{
            background:#0177cb;
            color:white;
        }

        #mcBtnExportar{
            background:#015a9a;
            color:white;
            display:none;
        }

        /* ============================================
        BOTONES MODAL ACTIONS
        ============================================ */

        .modal-actions button:last-child{
            background:#0177cb;
            color:white;
        }

        .modal-actions button:nth-child(2){
            background:#e8f4fd;
            color:#0177cb;
        }

        /* ============================================
        BOTONES FILTROS
        ============================================ */

        #mfBtnLimpiar{
            background:#f1f5f9;
            color:#64748b;
        }

        #mfBtnAplicar{
            background:#0177cb;
            color:white;
        }

        /* ============================================
        BOTONES TABLAS PERMISOS
        ============================================ */

        .btn-editar-permiso{
            background:#e8f4fd;
            color:#0177cb;
            border:none;
            padding:6px 14px;
            border-radius:8px;
            cursor:pointer;
            font-size:12px;
            font-weight:600;
            transition:.15s;
            white-space:nowrap;
        }

        .btn-editar-permiso:hover{
            background:#d0eaf9;
        }
    </style>


</head>

<?php
/* ============================================================
   SECCIÓN 4: PHP - INCLUDE DE CLASES
   ============================================================ */
include("./funciones/clas_carobra.php");
?>

<body>

    <?php
        /* ============================================================
        SECCIÓN 5: PHP - VALIDACIÓN DE SESIÓN Y LÓGICA PRINCIPAL
        ============================================================ */
        $fg      = $_SESSION["ultimoAcceso"];
        $fa      = date("Y-n-j H:i:s");
        $periodo = (strtotime($fa) - strtotime($fg));

        if ($periodo > 6000)
        {
            session_destroy();
            mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "http://carobra.ddns.me:8888");
        }
        else
        {
            $_SESSION["ultimoAcceso"] = $fa;
            $_SESSION["autentifica"]  = "p";
        }

        if ($_SESSION["autentifica"] != "p")
        {
            session_destroy();
            mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "index.php");
        }
        else
        {
            reg_acceso($_SESSION["nombre"], "empleados_dp");
            $_SESSION["autentifica"] = "";

            $divpie       = new div("pie");
            $divopcion    = new div("opcion");
            $divcabecera  = new div("cabecera");
            $divcontenido = new div("contenido");
            $divcontenedor= new div("contenedor");
            $imgpie       = new img("imgpie",      "./img/pie.png");
            $divusuario   = new div("usuario", '', "absolute", "97%");
            $imgcabecera  = new img("imgcabecera", "./img/cabecera.png");

            /* --- Cabecera --- */
            $divcabecera->crear();
                $imgcabecera->crear();
                $imgcabecera->css("width", "100%");
                titulo("Reporteador de Plantilla de R.H.");
                $pag = "REC_HUMANOS";
                menu();
            $divcabecera->close();

            /* --- Pie --- */
            $divpie->crear();
                $imgpie->crear();
                $imgpie->css("width", "100%");
                $divusuario->crear();
                    echo "<br><br>";
                    $divusuario->css("top",        "10px");
                    $divusuario->css("text-align", "left");
    ?>

	<!-- ============================================================
	     SECCIÓN 6: HTML - VÍNCULOS Y USUARIO
	     ============================================================ -->
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

        /* --- Contenedor principal --- */
    	$divcontenedor->crear();

		/* --- Menú de opciones --- */
		$divopcion->crear();
		$divopcion->css("background", "#FFFFFF");
		$divopcion->close();

		/* --- Contenido --- */
		$divcontenido->crear();
		$divcontenido->css("overflow-y", "auto");

		$conecta  = new conector();
		$fecha    = addslashes(trim($_POST["fecha"]));
		$mensaje  = addslashes(trim($_POST["mensaje"]));
		$dispersa = addslashes(trim($_POST["dispersa"]));
    ?>

	<!-- ============================================================
    SECCIÓN 7: HTML - FORMULARIO PRINCIPAL
    ============================================================ -->
	
    <div class="container">

    <?php $nivel = nivel_usuario(utf8_decode($_SESSION["nombre"])); ?>

        <div style="margin-bottom:20px;">
            <button id="btnNuevoReporte" style="display:none;">+ Nuevo Reporte</button>

            <button id="btnAdminPermisos"
                    style="display:none;background:#0177cb;color:white;border:none;padding:14px 22px;border-radius:16px;cursor:pointer;font-size:14px;font-weight:600;transition:.2s;box-shadow:0 5px 15px rgba(1,119,203,.2);margin-left:10px;">
                Administrar Permisos
            </button>
            <button id="btnAdminRoles"
                    style="display:none;background:#0369a1;color:white;border:none;padding:14px 22px;
                        border-radius:16px;cursor:pointer;font-size:14px;font-weight:600;
                        transition:.2s;box-shadow:0 5px 15px rgba(3,105,161,.2);margin-left:10px;">
                Administrar Roles
            </button>
            <button id="btnEditarReportes"
                    style="display:none;background:#c0392b;color:white;border:none;padding:14px 22px;
                        border-radius:16px;cursor:pointer;font-size:14px;font-weight:600;
                        transition:.2s;box-shadow:0 5px 15px rgba(220,38,38,.2);margin-left:10px;">
                Editar Reportes
            </button>
        </div>

        <h1 style="display: none;">Mis Reportes</h1>

        <div class="grid-reportes" id="gridReportes"></div>

        <!-- ============================================
        MODAL FULLSCREEN CONSTRUCTOR
        ============================================ -->

        <div id="modalConstructor">

            <div class="mc-content">

                <!-- HEADER -->
                <div class="mc-header">

                    <h2 id="mcTitulo">Reporte</h2>

                    <select class="mc-select-config" id="mcSelectConfig">
                        <option value="">— Configuraciones guardadas —</option>
                    </select>
                    <button id="mcBtnGestionConfig"
                            style="background:#f1f5f9;border:none;padding:8px 12px;border-radius:10px;
                                cursor:pointer;font-size:13px;font-weight:600;color:#64748b;
                                transition:.15s;white-space:nowrap;"
                            onclick="abrirGestionConfigs()">
                        ⚙️ Configs
                    </button>

                    <button class="mc-btn-close" onclick="cerrarConstructor()">✕</button>

                </div>

                <!-- BODY -->
                <div class="mc-body">

                    <!-- IZQUIERDO: CAMPOS -->
                    <div class="mc-campos">

                        <div class="mc-campos-title">Campos disponibles</div>

                        <div class="mc-campos-list" id="mcCamposList"></div>

                    </div>

                    <!-- DERECHO: CONSTRUCTOR / RESULTADOS -->
                    <div class="mc-derecho">

                        <div class="mc-derecho-title">
                            <span id="mcDerechoTitle">Constructor</span>
                            <button id="btnVerSQL" onclick="verSQL()" style="display:none;background:#f1f5f9;border:none;padding:5px 12px;border-radius:8px;cursor:pointer;font-size:11px;font-weight:600;color:#64748b;text-transform:none;letter-spacing:0;">
                                🔍 Ver SQL
                            </button>
                        </div>

                        <div class="mc-dropzone" id="mcDropzone">

                            <div class="mc-placeholder" id="mcPlaceholder">
                                <span>📋</span>
                                <p>Arrastra o haz clic en un campo para agregarlo</p>
                            </div>

                            <table class="mc-tabla" id="mcTabla" style="display:none;">
                                <thead>
                                    <tr id="mcThead"></tr>
                                </thead>
                                <tbody id="mcTbody"></tbody>
                            </table>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="mc-footer">

                    <button id="mcBtnGuardar">💾 Guardar Config</button>
                    <button id="mcBtnGenerar">⚡ Generar Reporte</button>
                    <button id="mcBtnExportar">📥 Exportar XLSX</button>

                </div>

            </div>

        </div>

        <!-- ============================================
        MODAL NUEVO REPORTE
        ============================================ -->


    </div>
        
                
    <?php
        $divcontenido->close();
        $divpie->close();
    ?>

    <!-- ============================================================
     SECCIÓN 8: JAVASCRIPT - LAYOUT INICIAL
     ============================================================ -->
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

        <div class="modal" id="modalNuevoReporte">

            <div class="modal-content">

                <div class="modal-header">
                    <h2>Nuevo Reporte</h2>
                    <button class="btnCloseModal" onclick="cerrarModalNuevoReporte()">✕</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nombre del reporte</label>
                        <input type="text" id="nuevoNombreReporte" placeholder="Ej: Reporte empleados">
                    </div>

                    <div class="form-group">
                        <label>Query Base</label>
                        <textarea id="nuevoQueryReporte" placeholder="SELECT nombre, curp FROM empleados"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Campos detectados</label>
                        <div id="camposDetectados"></div>
                    </div>

                </div>

                <div class="modal-actions">
                    <button onclick="cerrarModalNuevoReporte()">Cancelar</button>
                    <button onclick="detectarCampos()">Detectar Campos</button>
                    <button onclick="guardarNuevoReporte()">Guardar Reporte</button>
                </div>

            </div>

        </div>


        <!-- ============================================
        MODAL ADMIN PERMISOS
        ============================================ -->

        <div id="modalAdmin" class="modal">

            <div class="modal-content" style="max-width:820px;">

                <!-- HEADER -->
                <div class="modal-header">

                    <h2>🔐 Administrar Permisos</h2>

                    <div style="display:flex;gap:10px;align-items:center;">

                        <button onclick="abrirPermisosExistentes()"
                                style="background:#f1f5f9;border:none;padding:8px 16px;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;color:#64748b;transition:.15s;">
                            👥 Ver permisos existentes
                        </button>

                        <button class="btnCloseModal" onclick="cerrarModalAdmin()">✕</button>

                    </div>

                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- REPORTE -->
                    <div class="form-group">

                        <label>Reporte</label>

                        <select id="adminSelectReporte"
                                style="width:100%;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 16px;font-size:14px;color:#0f172a;background:#f8fafc;outline:none;">
                            <option value="">— Selecciona un reporte —</option>
                        </select>

                    </div>

                    <!-- USUARIOS BUSCADOR -->
                    <div class="form-group">

                        <label>Usuarios</label>

                        <div class="usuario-search-wrap">

                            <input type="text"
                                id="adminBuscadorUsuario"
                                class="usuario-search-input"
                                placeholder="Escribe para buscar usuarios..."
                                autocomplete="off">

                            <div class="usuario-dropdown" id="adminDropdownUsuarios"></div>

                        </div>

                        <div class="usuarios-chips" id="adminChipsUsuarios"></div>

                    </div>

                    <!-- CAMPOS -->
                    <div class="form-group">

                        <label>Campos a habilitar</label>

                        <div id="adminCampos"
                            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:4px;">

                            <p style="color:#94a3b8;font-size:13px;">
                                Selecciona un reporte primero
                            </p>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-actions">

                    <button onclick="cerrarModalAdmin()">Cancelar</button>

                    <button onclick="guardarPermisos()"
                            style="background:#2563eb;color:white;">
                        Guardar Permisos
                    </button>

                </div>

            </div>

        </div>

        <!-- ============================================
        MODAL PERMISOS EXISTENTES
        ============================================ -->

        <div id="modalPermisosExistentes">

            <div class="mpe-content">

                <div class="mpe-header">

                    <h2>👥 Usuarios con permisos</h2>

                    <button class="btnCloseModal"
                            onclick="cerrarPermisosExistentes()">✕</button>

                </div>

                <div class="mpe-body">

                    <div style="margin-bottom:16px;">

                        <select id="mpeSelectReporte"
                                style="width:100%;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 16px;font-size:14px;color:#0f172a;background:#f8fafc;outline:none;">
                            <option value="">— Selecciona un reporte —</option>
                        </select>

                    </div>

                    <div id="mpeContenido">
                        <p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">
                            Selecciona un reporte para ver los permisos
                        </p>
                    </div>

                </div>

                <div class="mpe-footer">
                    <button onclick="cerrarPermisosExistentes()">Cerrar</button>
                </div>

            </div>

        </div>

        <!-- ============================================
        MODAL FILTROS
        ============================================ -->

        <div id="modalFiltros">

            <div class="mf-content">

                <div class="mf-header">
                    <h2>🔍 Filtros del reporte</h2>
                    <button class="btnCloseModal" onclick="cerrarModalFiltros()">✕</button>
                </div>

                <div class="mf-body" id="mfBody">
                    <p style="color:#94a3b8;font-size:13px;text-align:center;">Cargando filtros...</p>
                </div>

                <div class="mf-footer">
                    <button id="mfBtnLimpiar" onclick="limpiarFiltros()">Limpiar todo</button>
                    <button id="mfBtnAplicar" onclick="aplicarFiltros()">⚡ Aplicar y generar</button>
                </div>

            </div>

        </div>


        <!-- ============================================
        MODAL ADMIN ROLES
        ============================================ -->

        <div id="modalRoles" class="modal">

            <div class="modal-content" style="max-width:900px;max-height:90vh;">

                <div class="modal-header">
                    <h2>👑 Administrar Roles</h2>
                    <button class="btnCloseModal" onclick="cerrarModalRoles()">✕</button>
                </div>

                <div class="modal-body" style="flex-direction:row;gap:0;padding:0;overflow:hidden;">

                    <!-- IZQUIERDO: LISTA DE ROLES -->
                    <div style="width:260px;flex-shrink:0;border-right:1px solid #e2e8f0;
                                display:flex;flex-direction:column;overflow:hidden;">

                        <div style="padding:16px;border-bottom:1px solid #f1f5f9;
                                    font-size:11px;font-weight:700;color:#94a3b8;
                                    text-transform:uppercase;letter-spacing:.06em;
                                    display:flex;justify-content:space-between;align-items:center;">
                            Roles
                            <button onclick="abrirCrearRol()"
                                    style="background:#2563eb;color:white;border:none;
                                        padding:5px 12px;border-radius:8px;cursor:pointer;
                                        font-size:12px;font-weight:600;">
                                + Nuevo
                            </button>
                        </div>

                        <div id="rolesLista"
                            style="flex:1;overflow-y:auto;padding:10px;display:flex;
                                    flex-direction:column;gap:6px;">
                        </div>

                    </div>

                    <!-- DERECHO: DETALLE DEL ROL -->
                    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;">

                        <div style="padding:16px;border-bottom:1px solid #f1f5f9;
                                    font-size:11px;font-weight:700;color:#94a3b8;
                                    text-transform:uppercase;letter-spacing:.06em;">
                            <span id="rolDetalleNombre">Selecciona un rol</span>
                        </div>

                        <div style="flex:1;overflow-y:auto;padding:16px;display:flex;
                                    flex-direction:column;gap:20px;" id="rolDetalle">

                            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">
                                Selecciona un rol para ver su configuración
                            </p>

                        </div>

                    </div>

                </div>

                <div class="modal-actions">
                    <button onclick="cerrarModalRoles()">Cerrar</button>
                    <button id="btnGuardarRol"
                            onclick="guardarConfigRol()"
                            style="background:#2563eb;color:white;display:none;">
                        Guardar cambios
                    </button>
                </div>

            </div>

        </div>

        <!-- ============================================
        MODAL EDITAR REPORTES
        ============================================ -->

        <div id="modalEditarReportes" class="modal">

            <div class="modal-content" style="max-width:1100px;max-height:92vh;">

                <div class="modal-header">
                    <h2>✏️ Editar Reportes</h2>
                    <button class="btnCloseModal" onclick="cerrarEditarReportes()">✕</button>
                </div>

                <div class="modal-body" style="flex-direction:row;gap:0;padding:0;overflow:hidden;">

                    <!-- IZQUIERDO: LISTA DE REPORTES -->
                    <div style="width:280px;flex-shrink:0;border-right:1px solid #e2e8f0;
                                display:flex;flex-direction:column;overflow:hidden;">

                        <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;
                                    font-size:11px;font-weight:700;color:#94a3b8;
                                    text-transform:uppercase;letter-spacing:.06em;">
                            Reportes
                        </div>

                        <div id="erListaReportes"
                            style="flex:1;overflow-y:auto;padding:10px;
                                    display:flex;flex-direction:column;gap:6px;">
                        </div>

                    </div>

                    <!-- DERECHO: EDITOR -->
                    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;">

                        <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;
                                    font-size:11px;font-weight:700;color:#94a3b8;
                                    text-transform:uppercase;letter-spacing:.06em;
                                    display:flex;justify-content:space-between;align-items:center;">
                            <span id="erTituloEditor">Selecciona un reporte</span>
                            <button id="erBtnEliminar"
                                    onclick="eliminarReporte()"
                                    style="display:none;background:#fde8e8;color:#c0392b;border:none;
                                        padding:6px 14px;border-radius:8px;cursor:pointer;
                                        font-size:12px;font-weight:600;transition:.15s;">
                                🗑 Eliminar
                            </button>
                        </div>

                        <div id="erEditor"
                            style="flex:1;overflow-y:auto;padding:20px;
                                    display:flex;flex-direction:column;gap:16px;">
                            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">
                                Selecciona un reporte para editarlo
                            </p>
                        </div>

                    </div>

                </div>

                <div class="modal-actions">
                    <button onclick="cerrarEditarReportes()">Cerrar</button>
                    <button id="erBtnGuardar"
                            onclick="guardarEdicionReporte()"
                            style="background:#2563eb;color:white;display:none;">
                        💾 Guardar cambios
                    </button>
                </div>

            </div>

        </div>

<?php } ?>

</body>

<!-- ============================================================
     SECCIÓN 9: JAVASCRIPT - EVENTOS Y FUNCIONES
     ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		/* --- Resize --- */
		$(window).resize(function ()
		{
			centra_contenedor();
		});
		$(window).resize();

		$('#men_pie').css("top", h);

		/* --- Menú --- */
		$('#tmenu td').click(function (evento)
		{
			var modulo = $(this).text().replace(/ /gi, "_");
			$("#opcion").load("menu.php?modulo=" + modulo);
		});

		/* --- Salir --- */
		$("#salir").click(function ()
		{
			window.location = "http://carobra.ddns.me";
		});

		/* --- Email --- */
		$("#email").click(function ()
		{
			window.open("http://webmail.carobra.com.mx/imp/login.php");
		});


	});

</script>

<script>

const API = location.protocol + '//' + location.host + '/afore/reporteador_plantillas_api.php';
var nivelUsuario = <?php echo intval($nivel); ?>;
var reporteActivo = null;
var todosLosUsuarios = [];
var usuariosSeleccionados = [];
var ultimosFiltros = [];
var ultimosTokens  = {};
var configActivaId = null;

var ultimoSQL            = '';
var ultimoQueryProcesado = '';

/* ============================================
INIT
============================================ */

document.addEventListener('DOMContentLoaded', function(){
    cargarMiRol().then(function(){
        cargarReportes();
    });
    initConstructor();

    /* Botón nuevo reporte */
    document.getElementById('btnNuevoReporte')
        .addEventListener('click', function(){
            document.getElementById('modalNuevoReporte').style.display = 'flex';
        });

    document.getElementById('modalNuevoReporte')
        .addEventListener('click', function(e){
            if(e.target === this) cerrarModalNuevoReporte();
        });

    /* Config select */
    document.getElementById('mcSelectConfig')
        .addEventListener('change', function(){
            if(!this.value) return;
            try{
                var cols = JSON.parse(this.value);
                resetTabla();
                cols.forEach(function(col){ agregarColumna(col.campo_bd, col.alias); });
            } catch(e){ console.error(e); }
            this.value = '';
        });

    /* Botones constructor */
    document.getElementById('mcBtnGuardar').addEventListener('click', guardarConfiguracion);
    document.getElementById('mcBtnGenerar').addEventListener('click', generarReporte);
    document.getElementById('mcBtnExportar').addEventListener('click', exportarReporte);


    document.getElementById('btnAdminPermisos')
        .addEventListener('click', abrirModalAdmin);

    document.getElementById('adminSelectReporte')
        .addEventListener('change', cargarCamposAdmin);

    document.getElementById('adminBuscadorUsuario')
        .addEventListener('input', function(){ buscarUsuarios(this.value.trim()); });

    document.getElementById('adminBuscadorUsuario')
        .addEventListener('blur', function(){
            setTimeout(function(){
                document.getElementById('adminDropdownUsuarios').style.display = 'none';
            }, 200);
        });

    document.getElementById('adminBuscadorUsuario')
        .addEventListener('focus', function(){
            if(this.value.trim().length > 0) buscarUsuarios(this.value.trim());
        });

    document.getElementById('mpeSelectReporte')
        .addEventListener('change', function(){
            cargarTablaPermisosExistentes(this.value);
        });

});

/* ESC */
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        cerrarConstructor();
        cerrarModalNuevoReporte();
        cerrarModalAdmin();
        cerrarPermisosExistentes();
        cerrarModalRoles();
        cerrarEditarReportes();
        cerrarModalFiltros();
    }
});

/* ============================================
CARGAR REPORTES
============================================ */

/* Cargar rol del usuario logueado */
var miRol = null;

async function cargarMiRol(){

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getMiRol' })
        });

        var json = await res.json();

        if(json.status === 'ok' && json.rol){

            miRol = json.rol;

            /* Mostrar botones según rol */
            if(miRol.puede_crear == 1){
                document.getElementById('btnNuevoReporte').style.display   = 'inline-block';
                document.getElementById('btnEditarReportes').style.display = 'inline-block';
            } else {
                document.getElementById('btnNuevoReporte').style.display   = 'none';
            }

            if(miRol.puede_admin == 1){
                document.getElementById('btnAdminPermisos').style.display  = 'inline-block';
                document.getElementById('btnAdminRoles').style.display     = 'inline-block';
            }

        }

    } catch(e){ console.error(e); }

}

async function cargarReportes(){

    var grid = document.getElementById('gridReportes');
    grid.innerHTML = '<p style="color:#94a3b8;font-size:14px;">Cargando reportes...</p>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getReportesPersonales' }) });
        var json = await res.json();

        if(json.status !== 'ok'){
            grid.innerHTML = '<p style="color:#ef4444;">Error: ' + (json.mysql || json.message) + '</p>';
            return;
        }

        if(json.data.length === 0){
            grid.innerHTML = '<p style="color:#94a3b8;font-size:14px;">No hay reportes aún.</p>';
            return;
        }

        /* Si solo tiene 1 reporte → mostrar configuraciones */
        if(json.data.length === 1){
            mostrarConfiguracionesReporte(json.data[0]);
            return;
        }

        /* Más de 1 → cards de reportes */
        grid.innerHTML = '';
        json.data.forEach(function(r){
            var card = document.createElement('div');
            card.className = 'card-reporte';
            card.innerHTML = '<h3>' + r.nombre + '</h3><p>Clic para construir reporte</p>';
            card.addEventListener('click', function(){ abrirConstructor(r.id, r.nombre); });
            grid.appendChild(card);
        });

    } catch(e){
        grid.innerHTML = '<p style="color:#ef4444;">Error: ' + e.message + '</p>';
    }

}


async function mostrarConfiguracionesReporte(reporte){

    var grid = document.getElementById('gridReportes');

    /* Título */
    document.querySelector('.container h1').textContent = reporte.nombre;

    /* Cargar configuraciones */
    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getConfiguraciones', idReporte:reporte.id })
        });

        var json = await res.json();

        grid.innerHTML = '';

        /* Card nueva configuración */
        var cardNueva = document.createElement('div');
        cardNueva.className = 'card-reporte';
        cardNueva.style.cssText = 'border:2px dashed #2563eb;background:#eff6ff;';
        cardNueva.innerHTML =
            '<h3 style="color:#2563eb;">+ Nuevo Reporte</h3>' +
            '<p style="color:#2563eb;">Construir desde cero</p>';
        cardNueva.addEventListener('click', function(){
            abrirConstructor(reporte.id, reporte.nombre);
        });
        grid.appendChild(cardNueva);

        /* Cards de configuraciones guardadas */
        if(json.status === 'ok' && json.data.length > 0){

            json.data.forEach(function(config){

                var card = document.createElement('div');
                card.className = 'card-reporte';
                card.innerHTML =
                    '<h3>' + config.nombre + '</h3>' +
                    '<p>Configuración guardada</p>';

                card.addEventListener('click', function(){

                    abrirConstructor(reporte.id, reporte.nombre).then(function(){

                        /* Cargar la configuración automáticamente */
                        var cols = config.configuracion;
                        if(cols && Array.isArray(cols)){
                            resetTabla();
                            cols.forEach(function(col){
                                agregarColumna(col.campo_bd, col.alias);
                            });
                        }

                    });

                });

                grid.appendChild(card);

            });

        }

    } catch(e){
        grid.innerHTML = '<p style="color:#ef4444;">Error: ' + e.message + '</p>';
    }

}

/* ============================================
CONSTRUCTOR
============================================ */

async function abrirConstructor(idReporte, nombreReporte){

    reporteActivo = idReporte;
    document.getElementById('mcTitulo').textContent = nombreReporte;
    document.getElementById('mcBtnExportar').style.display = 'none';
    document.getElementById('btnVerSQL').style.display = 'none';
    resetTabla();
    document.getElementById('modalConstructor').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    await Promise.all([
        cargarCamposMC(idReporte),
        cargarConfiguracionesMC(idReporte)
    ]);

}

function cerrarConstructor(){
    document.getElementById('modalConstructor').style.display = 'none';
    document.body.style.overflow = '';
    reporteActivo  = null;
    configActivaId = null;
    var btnGuardar = document.getElementById('mcBtnGuardar');
    btnGuardar.textContent      = '💾 Guardar Config';
    btnGuardar.style.background = '#e0f2fe';
    btnGuardar.style.color      = '#0369a1';
}

function initConstructor(){
    var dz = document.getElementById('mcDropzone');
    dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('drag-over'); });
    dz.addEventListener('dragleave', function(){ dz.classList.remove('drag-over'); });
    dz.addEventListener('drop', function(e){
        e.preventDefault();
        dz.classList.remove('drag-over');
        var campoBd = e.dataTransfer.getData('campoBd');
        var alias   = e.dataTransfer.getData('alias');
        if(campoBd) agregarColumna(campoBd, alias);
    });
}

async function cargarCamposMC(idReporte){
    var lista = document.getElementById('mcCamposList');
    lista.innerHTML = '<p style="color:#94a3b8;font-size:13px;padding:10px;">Cargando...</p>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getCamposReporte', idReporte:idReporte }) });
        var json = await res.json();

        if(json.status !== 'ok'){ lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error al cargar campos</p>'; return; }

        lista.innerHTML = '';
        json.data.sort(function(a, b){
            return a.alias_campo.localeCompare(b.alias_campo, 'es', { sensitivity:'base' });
        });

        json.data.forEach(function(campo){
            var item = document.createElement('div');
            item.className = 'mc-campo-item';
            item.draggable = true;
            item.dataset.campoBd = campo.campo_bd;
            item.dataset.alias   = campo.alias_campo;
            item.innerHTML = '<span class="drag-icon">⠿</span><span>' + campo.alias_campo + '</span>';
            item.addEventListener('dragstart', function(e){ e.dataTransfer.setData('campoBd', campo.campo_bd); e.dataTransfer.setData('alias', campo.alias_campo); });
            item.addEventListener('click', function(){ agregarColumna(campo.campo_bd, campo.alias_campo); });
            lista.appendChild(item);
        });

    } catch(e){
        lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error: ' + e.message + '</p>';
    }
}

async function cargarConfiguracionesMC(idReporte){
    var select = document.getElementById('mcSelectConfig');
    select.innerHTML = '<option value="">— Configuraciones guardadas —</option>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getConfiguraciones', idReporte:idReporte }) });
        var json = await res.json();
        if(json.status !== 'ok') return;
        json.data.forEach(function(config){
            var opt = document.createElement('option');
            opt.value = JSON.stringify(config.configuracion);
            opt.textContent = config.nombre;
            select.appendChild(opt);
        });
    } catch(e){ console.error(e); }
}

function resetTabla(){
    document.getElementById('mcThead').innerHTML = '';
    document.getElementById('mcTbody').innerHTML = '';
    document.getElementById('mcTabla').style.display = 'none';
    document.getElementById('mcPlaceholder').style.display = 'flex';
    document.getElementById('mcDerechoTitle').textContent = 'Constructor';
    document.getElementById('mcBtnExportar').style.display = 'none';
}

function agregarColumna(campoBd, alias){
    var thead = document.getElementById('mcThead');
    var tbody = document.getElementById('mcTbody');

    document.getElementById('mcPlaceholder').style.display = 'none';
    document.getElementById('mcTabla').style.display = 'table';

    if(thead.querySelector('[data-campo-bd="' + campoBd + '"]')) return;

    var th = document.createElement('th');
    th.dataset.campoBd = campoBd;
    th.draggable = true;
    th.innerHTML = '<div class="col-header"><label>' + alias + '</label><button class="btn-del-col" onclick="eliminarColumna(\'' + campoBd + '\')">✕</button></div>';

    th.addEventListener('dragstart', function(e){ e.dataTransfer.setData('reordenar', campoBd); e.stopPropagation(); });
    th.addEventListener('dragover',  function(e){ e.preventDefault(); });
    th.addEventListener('drop', function(e){
        e.preventDefault(); e.stopPropagation();
        var origen = e.dataTransfer.getData('reordenar');
        if(origen && origen !== campoBd) reordenarColumna(origen, campoBd);
    });

    thead.appendChild(th);

    var tr = tbody.querySelector('tr') || document.createElement('tr');
    if(!tbody.querySelector('tr')) tbody.appendChild(tr);

    var td = document.createElement('td');
    td.dataset.campoBd = campoBd;
    td.style.color = '#94a3b8';
    td.textContent = '—';
    tr.appendChild(td);
}

function eliminarColumna(campoBd){
    var thead = document.getElementById('mcThead');
    var tbody = document.getElementById('mcTbody');
    var th = thead.querySelector('[data-campo-bd="' + campoBd + '"]');
    var td = tbody.querySelector('[data-campo-bd="' + campoBd + '"]');
    if(th) th.remove();
    if(td) td.remove();
    if(thead.children.length === 0) resetTabla();
}

function reordenarColumna(origenBd, destinoBd){
    var thead = document.getElementById('mcThead');
    var tbody = document.getElementById('mcTbody');
    var thO = thead.querySelector('[data-campo-bd="' + origenBd  + '"]');
    var thD = thead.querySelector('[data-campo-bd="' + destinoBd + '"]');
    var tdO = tbody.querySelector('[data-campo-bd="' + origenBd  + '"]');
    var tdD = tbody.querySelector('[data-campo-bd="' + destinoBd + '"]');
    if(thO && thD) thead.insertBefore(thO, thD);
    if(tdO && tdD) tbody.querySelector('tr').insertBefore(tdO, tdD);
}

function getColumnasActivas(){
    var ths  = document.getElementById('mcThead').querySelectorAll('[data-campo-bd]');
    var cols = [];
    ths.forEach(function(th){ cols.push(th.dataset.campoBd); });
    return cols;
}

/* ============================================
GUARDAR CONFIGURACION
============================================ */

async function abrirGestionConfigs(){

    if(!reporteActivo){ Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un reporte primero' }); return; }

    try{
        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getConfiguraciones', idReporte:reporteActivo })
        });

        var json = await res.json();

        if(json.status !== 'ok' || json.data.length === 0){
            Swal.fire({ icon:'info', title:'Sin configuraciones', text:'No hay configuraciones guardadas' });
            return;
        }

        var html = '<div style="display:flex;flex-direction:column;gap:8px;max-height:400px;overflow-y:auto;">';

        json.data.forEach(function(config){
            html +=
                '<div style="display:flex;align-items:center;gap:8px;background:#f8fafc;' +
                'border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 14px;">' +
                    '<input type="text" id="configNombre_' + config.id + '" value="' + config.nombre + '" ' +
                        'style="flex:1;border:1.5px solid #e2e8f0;border-radius:8px;padding:7px 10px;' +
                        'font-size:13px;outline:none;">' +
                    '<button onclick="cargarConfigEnConstructor(' + config.id + ', ' + JSON.stringify(config.configuracion).replace(/"/g, '&quot;') + ', \'' + config.nombre + '\')" ' +
                        'style="background:#e8f4fd;color:#16a34a;border:none;padding:7px 12px;' +
                        'border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">✏️ Editar</button>' +
                    '<button onclick="renombrarConfig(' + config.id + ')" ' +
                        'style="background:#e0f2fe;color:#0369a1;border:none;padding:7px 12px;' +
                        'border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">💾 Nombre</button>' +
                    '<button onclick="eliminarConfig(' + config.id + ', this)" ' +
                        'style="background:#fee2e2;color:#dc2626;border:none;padding:7px 12px;' +
                        'border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">🗑 Borrar</button>' +
                '</div>';
        });

        html += '</div>';

        Swal.fire({
            title: '⚙️ Gestionar configuraciones',
            html:   html,
            showConfirmButton: false,
            showCloseButton:   true,
            width:             600
        });

    } catch(e){
        Swal.fire({ icon:'error', title:'Error', text:e.message });
    }

}

function cargarConfigEnConstructor(idConfig, cols, nombre){

    /* Cerrar el Swal de gestión */
    Swal.close();

    /* Marcar config activa */
    configActivaId = idConfig;

    /* Cargar columnas en el constructor */
    resetTabla();
    if(cols && Array.isArray(cols)){
        cols.forEach(function(col){ agregarColumna(col.campo_bd, col.alias); });
    }

    /* Cambiar botón guardar */
    var btnGuardar = document.getElementById('mcBtnGuardar');
    btnGuardar.textContent = '🔄 Actualizar "' + nombre + '"';
    btnGuardar.style.background = '#f0fdf4';
    btnGuardar.style.color      = '#16a34a';

}

async function renombrarConfig(idConfig){

    var input = document.getElementById('configNombre_' + idConfig);
    var nombre = input.value.trim();

    if(!nombre){ Swal.fire({ icon:'warning', title:'Oops', text:'El nombre no puede estar vacío' }); return; }

    try{
        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'renombrarConfiguracion', idConfig:idConfig, nombre:nombre })
        });

        var json = await res.json();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql }); return; }

        /* Actualizar el select del constructor */
        cargarConfiguracionesMC(reporteActivo);

        input.style.borderColor = '#16a34a';
        setTimeout(function(){ input.style.borderColor = '#e2e8f0'; }, 1500);

    } catch(e){
        Swal.fire({ icon:'error', title:'Error', text:e.message });
    }

}

async function eliminarConfig(idConfig, btn){

    var confirm = await Swal.fire({
        icon:'warning',
        title:'¿Eliminar configuración?',
        text:'Esta acción no se puede deshacer.',
        showCancelButton:true,
        confirmButtonText:'Sí, eliminar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc2626'
    });

    if(!confirm.isConfirmed) return;

    try{
        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'eliminarConfiguracion', idConfig:idConfig })
        });

        var json = await res.json();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql }); return; }

        /* Quitar del DOM */
        btn.closest('div[style]').remove();

        /* Actualizar el select del constructor */
        cargarConfiguracionesMC(reporteActivo);

    } catch(e){
        Swal.fire({ icon:'error', title:'Error', text:e.message });
    }

}


async function guardarConfiguracion(){

    if(!reporteActivo){ Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un reporte primero' }); return; }

    var columnas = getColumnasActivas();
    if(columnas.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos una columna' }); return; }

    var config = [];
    document.getElementById('mcThead').querySelectorAll('[data-campo-bd]').forEach(function(th){
        config.push({ campo_bd: th.dataset.campoBd, alias: th.querySelector('label').textContent });
    });

    /* Si hay config activa → actualizar */
    if(configActivaId){

        Swal.fire({ title:'Actualizando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

        try{
            var res  = await fetch(API, {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'actualizarConfiguracion', idConfig:configActivaId, configuracion:config })
            });

            var json = await res.json();

            Swal.close();

            if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql }); return; }

            Swal.fire({ icon:'success', title:'Configuración actualizada', timer:1200, showConfirmButton:false })
            .then(function(){
                configActivaId = null;
                var btnGuardar = document.getElementById('mcBtnGuardar');
                btnGuardar.textContent      = '💾 Guardar Config';
                btnGuardar.style.background = '#e8f4fd';
                btnGuardar.style.color      = '#0177cb';
                cargarConfiguracionesMC(reporteActivo);
                cargarReportes();
            });

            /* Resetear estado */
            configActivaId = null;
            var btnGuardar = document.getElementById('mcBtnGuardar');
            btnGuardar.textContent  = '💾 Guardar Config';
            btnGuardar.style.background = '#e0f2fe';
            btnGuardar.style.color      = '#0369a1';

            cargarConfiguracionesMC(reporteActivo);

        } catch(e){
            Swal.close();
            Swal.fire({ icon:'error', title:'Error JS', text:e.message });
        }

        return;
    }

    /* Si no hay config activa → crear nueva */
    var result = await Swal.fire({
        title:'Nombre de la configuración',
        input:'text',
        inputPlaceholder:'Ej: Reporte RH',
        showCancelButton:true,
        confirmButtonText:'Guardar',
        cancelButtonText:'Cancelar'
    });

    if(!result.isConfirmed || !result.value.trim()) return;

    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{
        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'guardarConfiguracion', idReporte:reporteActivo, nombre:result.value.trim(), configuracion:config })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.mysql || json.message }); return; }

        Swal.fire({ icon:'success', title:'Guardado', timer:1200, showConfirmButton:false })
        .then(function(){
            cargarReportes();
        });

        cargarConfiguracionesMC(reporteActivo);

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* ============================================
GENERAR REPORTE
============================================ */

async function generarReporte(){

    if(!reporteActivo){ Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un reporte primero' }); return; }

    var columnas = getColumnasActivas();
    if(columnas.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos una columna' }); return; }

    Swal.fire({ title:'Generando reporte...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'generarReporte', idReporte:reporteActivo, columnas:columnas, limite:50 }) });
        var json = await res.json();
        Swal.close();
        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.mysql || json.message }); return; }
        mostrarResultados(json.columnas, json.data);
    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

function verSQL(){
    if(!ultimoSQL){
        Swal.fire({ icon:'info', title:'Sin query', text:'Genera un reporte primero.' });
        return;
    }
    Swal.fire({
        title: '🔍 SQL ejecutado',
        html:
            '<textarea readonly style="width:100%;height:260px;font-family:monospace;' +
            'font-size:12px;border:1.5px solid #e2e8f0;border-radius:10px;padding:12px;' +
            'resize:vertical;background:#f8fafc;color:#0f172a;" ' +
            'onclick="this.select()">' + ultimoSQL.replace(/</g,'&lt;') + '</textarea>' +
            '<p style="font-size:11px;color:#94a3b8;margin-top:8px;">Clic adentro del cuadro para seleccionar todo y copiar.</p>',
        width: 700,
        showCloseButton: true,
        showConfirmButton: false
    });
}

function mostrarResultados(columnas, data){
    var tbody = document.getElementById('mcTbody');
    tbody.innerHTML = '';
    document.getElementById('mcPlaceholder').style.display = 'none';
    document.getElementById('mcTabla').style.display = 'table';
    document.getElementById('mcDerechoTitle').textContent = 'Resultados (' + data.length + ' registros)';
    document.getElementById('mcBtnExportar').style.display = 'inline-block';
    document.getElementById('btnVerSQL').style.display = 'inline-block';

    if(data.length === 0){
        var trV = document.createElement('tr');
        var tdV = document.createElement('td');
        tdV.colSpan = columnas.length;
        tdV.style.cssText = 'text-align:center;color:#94a3b8;padding:30px;';
        tdV.textContent = 'Sin resultados';
        trV.appendChild(tdV);
        tbody.appendChild(trV);
        return;
    }

    data.forEach(function(row){
        var tr = document.createElement('tr');
        columnas.forEach(function(col){
            var td = document.createElement('td');
            td.textContent = (row[col] !== null && row[col] !== undefined) ? row[col] : '—';
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
}

/* ============================================
EXPORTAR
============================================ */

async function exportarReporte(){

    var columnas = getColumnasActivas();
    if(columnas.length === 0) return;

    Swal.fire({ title:'Exportando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:    'generarReporte',
                idReporte: reporteActivo,
                columnas:  columnas,
                limite:    999999,
                filtros:   ultimosFiltros,
                tokens:    ultimosTokens
            })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.mysql || json.message }); return; }

        if(!json.data || json.data.length === 0){
            Swal.fire({ icon:'info', title:'Sin datos', text:'No hay registros para exportar' });
            return;
        }

        exportarXLSX(json.columnas, json.data, document.getElementById('mcTitulo').textContent);

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

function exportarXLSX(columnas, data, nombreReporte){

    var thead = document.getElementById('mcThead');

    var aliases = columnas.map(function(col){
        if(thead){
            var th = thead.querySelector('[data-campo-bd="' + col + '"]');
            if(th){
                var label = th.querySelector('label');
                if(label && label.textContent.trim()) return label.textContent.trim();
            }
        }
        return col.charAt(0).toUpperCase() + col.slice(1).replace(/_/g, ' ');
    });

    /* Convierte a Number SOLO si es un número "de verdad":
       - Acepta enteros y decimales, con signo opcional.
       - Si la parte entera tiene más de 1 dígito y empieza en '0',
         se asume que es un código (folio, NAP, cuenta, etc.) y se
         deja como texto para no perder los ceros a la izquierda. */
    function convertirValor(val){

        if(val === null || val === undefined || val === '') return { valor: '', esNumero: false };
        if(typeof val === 'number') return { valor: val, esNumero: true };

        var str = String(val).trim();
        if(str === '') return { valor: '', esNumero: false };

        if(!/^-?\d+(\.\d+)?$/.test(str)) return { valor: val, esNumero: false };

        var parteEntera = str.replace('-', '').split('.')[0];
        if(parteEntera.length > 1 && parteEntera[0] === '0'){
            return { valor: val, esNumero: false }; // código con ceros a la izquierda, se queda como texto
        }

        return { valor: Number(str), esNumero: true, tieneDecimales: str.indexOf('.') !== -1 };
    }

    /* Construir filas, guardando en paralelo qué celdas son numéricas
       (y si llevan decimales) para poder darles formato después. */
    var filas          = [aliases];
    var celdasNumericas = []; // [{r, c, decimales}]

    data.forEach(function(row, rIdx){
        var fila = columnas.map(function(col, cIdx){
            var res = convertirValor(row[col]);
            if(res.esNumero){
                celdasNumericas.push({ r: rIdx + 1, c: cIdx, decimales: !!res.tieneDecimales });
            }
            return res.valor;
        });
        filas.push(fila);
    });

    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet(filas);

    /* Ancho automático basado en contenido */
    var colWidths = columnas.map(function(col, i){
        var maxLen = aliases[i].length;
        data.forEach(function(row){
            var val = row[col] ? String(row[col]).length : 0;
            if(val > maxLen) maxLen = val;
        });
        return { wch: Math.min(maxLen + 4, 60) };
    });
    ws['!cols'] = colWidths;

    /* Estilo header — fila 1 en negrita */
    var range = XLSX.utils.decode_range(ws['!ref']);
    for(var c = range.s.c; c <= range.e.c; c++){
        var cellAddr = XLSX.utils.encode_cell({ r:0, c:c });
        if(!ws[cellAddr]) continue;
        ws[cellAddr].s = {
            font:    { bold:true },
            fill:    { fgColor:{ rgb:'1E293B' } },
            alignment: { horizontal:'center' }
        };
    }

    /* Formato de número para las celdas que sí son numéricas */
    celdasNumericas.forEach(function(pos){
        var addr = XLSX.utils.encode_cell({ r: pos.r, c: pos.c });
        if(!ws[addr]) return;
        ws[addr].z = pos.decimales ? '#,##0.00' : '#,##0';
    });

    var nombreHoja = (nombreReporte || 'Reporte').substring(0, 31);
    XLSX.utils.book_append_sheet(wb, ws, nombreHoja);

    var nombreArchivo = (nombreReporte || 'reporte').replace(/[^a-zA-Z0-9_\- ]/g, '') + '.xlsx';
    XLSX.writeFile(wb, nombreArchivo);

}

/* ============================================
MODAL NUEVO REPORTE
============================================ */

function cerrarModalNuevoReporte(){
    document.getElementById('modalNuevoReporte').style.display = 'none';
}

function detectarCampos(){

    var query = document.getElementById('nuevoQueryReporte').value;

    if(!query){ Swal.fire({ icon:'warning', title:'Oops', text:'Ingresa un query' }); return; }

    var match = query.match(/select([\s\S]*?)from/i);
    if(!match){ Swal.fire({ icon:'error', title:'Error', text:'No se detectó SELECT válido' }); return; }

    var campos     = match[1].replace(/\n/g,' ').trim().split(',');
    var contenedor = document.getElementById('camposDetectados');
    contenedor.innerHTML = '';

    campos.forEach(function(campo){

        campo = campo.trim();
        var alias = campo.toLowerCase().includes(' as ') ? campo.split(/ as /i)[1].trim() : campo.split('.').pop().trim();

        contenedor.innerHTML +=
            '<div class="campo-detectado">' +

                '<h4>' + alias + '</h4>' +

                '<input type="text" class="aliasCampo" data-campo="' + alias + '" value="' + alias + '">' +

                '<select class="tipoFiltro" data-campo="' + alias + '" ' +
                    'style="width:100%;margin-top:6px;border:1px solid #dbeafe;border-radius:8px;' +
                    'padding:6px 10px;font-size:12px;background:white;color:#1e40af;outline:none;"' +
                    'onchange="toggleQueryFiltro(this)">' +
                    '<option value="ninguno">Sin filtro</option>' +
                    '<option value="rango_fecha">Rango fecha</option>' +
                    '<option value="si_no">Sí / No / Todos</option>' +
                    '<option value="busqueda_bd">Búsqueda BD</option>' +
                    '<option value="texto_libre">Campo abierto</option>' +
                '</select>' +

                '<textarea class="queryFiltro" data-campo="' + alias + '" ' +
                    'placeholder="SELECT campo FROM tabla GROUP BY campo" ' +
                    'style="display:none;width:100%;margin-top:6px;border:1px solid #dbeafe;' +
                    'border-radius:8px;padding:6px 10px;font-size:12px;background:white;' +
                    'color:#1e40af;min-height:70px;resize:vertical;font-family:monospace;outline:none;">' +
                '</textarea>' +

            '</div>';

    });

    /* ── DETECTAR TOKENS ── */
    var tokensDetectados = detectarTokens(query);

    if(tokensDetectados.length > 0){

        contenedor.innerHTML +=
            '<div style="grid-column:1/-1;margin-top:10px;padding-top:10px;' +
            'border-top:2px dashed #bfdbfe;">' +
                '<div style="font-size:11px;font-weight:700;color:#3b82f6;' +
                'text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">' +
                    '⚡ Tokens detectados' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">' +
                    tokensDetectados.map(function(t){
                    return '<div class="campo-detectado" style="background:#fefce8;border-color:#fde047;">' +
                        '<h4 style="color:#ca8a04;">{{' + t + '}}</h4>' +
                        '<input type="text" class="tokenLabel" data-token="' + t + '" ' +
                            'value="' + t.replace(/_/g,' ').toLowerCase().replace(/\b\w/g, function(l){ return l.toUpperCase(); }) + '" ' +
                            'style="width:100%;border:1px solid #fde047;border-radius:8px;' +
                            'padding:7px 10px;font-size:13px;background:white;color:#854d0e;' +
                            'outline:none;margin-top:4px;" ' +
                            'placeholder="Label visible al usuario">' +
                        '<select class="tokenTipo" data-token="' + t + '" ' +
                            'style="width:100%;margin-top:6px;border:1px solid #fde047;border-radius:8px;' +
                            'padding:7px 10px;font-size:13px;background:white;color:#854d0e;outline:none;">' +
                            '<option value="texto">Texto libre</option>' +
                            '<option value="fecha">Fecha</option>' +
                            '<option value="rango_fecha">Rango de fechas</option>' +
                            '<option value="numero">Número</option>' +
                            '<option value="si_no">Sí/No/Todos</option>' +
                            '<option value="busqueda_bd">Búsqueda BD</option>' +
                        '</select>' +
                        '<input type="text" class="tokenDefault" data-token="' + t + '" ' +
                            'value="" ' +
                            'style="width:100%;border:1px solid #fde047;border-radius:8px;' +
                            'padding:7px 10px;font-size:13px;background:white;color:#854d0e;' +
                            'outline:none;margin-top:6px;" ' +
                            'placeholder="Valor por default (opcional)">' +
                    '</div>';
                }).join('') +
                '</div>' +
            '</div>';
    }

}

function toggleQueryFiltro(select){
    var container = select.closest('.campo-detectado');
    var textarea  = container.querySelector('.queryFiltro');
    textarea.style.display = select.value === 'busqueda_bd' ? 'block' : 'none';
}


async function guardarNuevoReporte(){

    var nombre = document.getElementById('nuevoNombreReporte').value.trim();
    var query  = document.getElementById('nuevoQueryReporte').value.trim();

    if(!nombre || !query){ Swal.fire({ icon:'warning', title:'Oops', text:'Completa todos los campos' }); return; }

    var campos = [];
    document.querySelectorAll('.campo-detectado').forEach(function(div){
        var input      = div.querySelector('.aliasCampo');
        var selectTipo = div.querySelector('.tipoFiltro');
        var textareaQ  = div.querySelector('.queryFiltro');
        if(!input) return; /* saltar divs de tokens */
        campos.push({
            campo_bd:     input.dataset.campo,
            alias_campo:  input.value,
            tipo_filtro:  selectTipo ? selectTipo.value : 'ninguno',
            query_filtro: (selectTipo && selectTipo.value === 'busqueda_bd') ? textareaQ.value.trim() : ''
        });
    });

    /* Recolectar tokens */
    var tokens = [];
    document.querySelectorAll('.tokenLabel').forEach(function(input){
        var tokenNombre  = input.dataset.token;
        var label        = input.value.trim() || tokenNombre;
        var tipoSelect   = document.querySelector('.tokenTipo[data-token="' + tokenNombre + '"]');
        var tipo         = tipoSelect ? tipoSelect.value : 'texto';
        var defaultInput = document.querySelector('.tokenDefault[data-token="' + tokenNombre + '"]');
        var valorDefault = defaultInput ? defaultInput.value.trim() : '';

        tokens.push({
            token:         tokenNombre,
            label:         label,
            tipo:          tipo,
            query_token:   '',
            valor_default: valorDefault
        });
    }); 

    Swal.fire({ title:'Guardando reporte...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'crearReporte', nombre:nombre, query:query, campos:campos, tokens:tokens })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql || 'Error al guardar' }); return; }

        Swal.fire({ icon:'success', title:'Reporte creado', timer:1500, showConfirmButton:false });

        cerrarModalNuevoReporte();
        cargarReportes();

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* ============================================
MODAL ADMIN PERMISOS
============================================ */

async function abrirModalAdmin(){
    usuariosSeleccionados = [];
    document.getElementById('adminChipsUsuarios').innerHTML = '';
    document.getElementById('adminBuscadorUsuario').value = '';
    document.getElementById('adminCampos').innerHTML = '<p style="color:#94a3b8;font-size:13px;">Selecciona un reporte primero</p>';
    document.getElementById('modalAdmin').style.display = 'flex';
    await Promise.all([ cargarReportesAdmin(), cargarTodosUsuarios() ]);
}

function cerrarModalAdmin(){
    document.getElementById('modalAdmin').style.display = 'none';
}

async function cargarReportesAdmin(){
    var select    = document.getElementById('adminSelectReporte');
    var selectMpe = document.getElementById('mpeSelectReporte');
    select.innerHTML    = '<option value="">— Selecciona un reporte —</option>';
    selectMpe.innerHTML = '<option value="">— Selecciona un reporte —</option>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getReportesPersonales' }) });
        var json = await res.json();
        if(json.status !== 'ok') return;
        json.data.forEach(function(r){
            var o1 = document.createElement('option'); o1.value = r.id; o1.textContent = r.nombre; select.appendChild(o1);
            var o2 = document.createElement('option'); o2.value = r.id; o2.textContent = r.nombre; selectMpe.appendChild(o2);
        });
    } catch(e){ console.error(e); }
}

async function cargarTodosUsuarios(){
    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getUsuarios' }) });
        var json = await res.json();
        if(json.status !== 'ok') return;
        todosLosUsuarios = json.data;
    } catch(e){ console.error(e); }
}

function buscarUsuarios(query){
    var dropdown = document.getElementById('adminDropdownUsuarios');
    if(query.length < 1){ dropdown.style.display = 'none'; return; }

    var q = query.toLowerCase();
    var resultados = todosLosUsuarios.filter(function(u){
        return u.nombre.toLowerCase().includes(q) || u.clave.toLowerCase().includes(q);
    }).filter(function(u){
        return !usuariosSeleccionados.find(function(s){ return s.clave === u.clave; });
    });

    if(resultados.length === 0){
        dropdown.innerHTML = '<div class="usuario-dropdown-empty">Sin resultados</div>';
        dropdown.style.display = 'block';
        return;
    }

    dropdown.innerHTML = '';
    resultados.slice(0, 8).forEach(function(u){
        var item = document.createElement('div');
        item.className = 'usuario-dropdown-item';
        item.innerHTML = u.nombre + '<small>' + u.clave + '</small>';
        item.addEventListener('mousedown', function(){ agregarUsuarioChip(u); });
        dropdown.appendChild(item);
    });

    dropdown.style.display = 'block';
}

function agregarUsuarioChip(usuario){
    if(usuariosSeleccionados.find(function(u){ return u.clave === usuario.clave; })) return;
    usuariosSeleccionados.push(usuario);

    var chips = document.getElementById('adminChipsUsuarios');
    var chip  = document.createElement('div');
    chip.className     = 'usuario-chip';
    chip.dataset.clave = usuario.clave;
    chip.innerHTML     = '<span>' + usuario.nombre + '</span><button onclick="quitarUsuarioChip(\'' + usuario.clave + '\')" title="Quitar">✕</button>';
    chips.appendChild(chip);

    document.getElementById('adminBuscadorUsuario').value = '';
    document.getElementById('adminDropdownUsuarios').style.display = 'none';
}

function quitarUsuarioChip(clave){
    usuariosSeleccionados = usuariosSeleccionados.filter(function(u){ return u.clave !== clave; });
    var chip = document.querySelector('.usuario-chip[data-clave="' + clave + '"]');
    if(chip) chip.remove();
}

async function cargarCamposAdmin(){
    var idReporte  = document.getElementById('adminSelectReporte').value;
    var contenedor = document.getElementById('adminCampos');

    if(!idReporte){ contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Selecciona un reporte primero</p>'; return; }
    contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Cargando campos...</p>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getCamposReporte', idReporte:idReporte }) });
        var json = await res.json();
        if(json.status !== 'ok'){ contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error al cargar campos</p>'; return; }

        /* Checkbox seleccionar todos */
        contenedor.innerHTML =
            '<label style="display:flex;align-items:center;gap:10px;background:#0177cb;' +
            'border-radius:12px;padding:12px 14px;cursor:pointer;font-size:13px;' +
            'font-weight:700;color:white;grid-column:1/-1;margin-bottom:4px;">' +
                '<input type="checkbox" id="chkTodosPermisos" ' +
                    'style="width:16px;height:16px;cursor:pointer;accent-color:#2563eb;" ' +
                    'onchange="toggleTodosPermisos(this)">' +
                '✅ Seleccionar todos' +
            '</label>';

        json.data.forEach(function(campo){
            contenedor.innerHTML +=
                '<label style="display:flex;align-items:center;gap:10px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 14px;cursor:pointer;font-size:13px;font-weight:500;color:#0f172a;">' +
                    '<input type="checkbox" data-id-campo="' + campo.id + '" style="width:16px;height:16px;cursor:pointer;accent-color:#2563eb;">' +
                    campo.alias_campo +
                '</label>';
        });

    } catch(e){
        contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + e.message + '</p>';
    }
}

function toggleTodosPermisos(chk){
    document.querySelectorAll('#adminCampos input[type="checkbox"][data-id-campo]')
        .forEach(function(cb){ cb.checked = chk.checked; });
}

async function guardarPermisos(){
    var idReporte = document.getElementById('adminSelectReporte').value;
    if(!idReporte){ Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un reporte' }); return; }
    if(usuariosSeleccionados.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos un usuario' }); return; }

    var checkboxes = document.querySelectorAll('#adminCampos input[type="checkbox"]');
    if(checkboxes.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'No hay campos cargados' }); return; }

    var permisos = [];
    checkboxes.forEach(function(cb){ permisos.push({ idCampo:cb.dataset.idCampo, puede_ver:cb.checked ? 1 : 0 }); });

    Swal.fire({ title:'Verificando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{
        var usuariosConPermisos = [];
        for(var i = 0; i < usuariosSeleccionados.length; i++){
            var u   = usuariosSeleccionados[i];
            var res = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'tienePermisos', idReporte:idReporte, idUsuario:u.clave }) });
            var j   = await res.json();
            if(j.status === 'ok' && j.tienePermisos) usuariosConPermisos.push(u.nombre);
        }

        Swal.close();

        if(usuariosConPermisos.length > 0){
            var confirm = await Swal.fire({
                icon:'warning', title:'Permisos existentes',
                html:'<b>' + usuariosConPermisos.join(', ') + '</b><br>ya tienen permisos.<br>¿Sobreescribir?',
                showCancelButton:true, confirmButtonText:'Sí, sobreescribir', cancelButtonText:'Cancelar', confirmButtonColor:'#2563eb'
            });
            if(!confirm.isConfirmed) return;
        }

        Swal.fire({ title:'Guardando permisos...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

        for(var j2 = 0; j2 < usuariosSeleccionados.length; j2++){
            await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'guardarPermisos', idReporte:idReporte, idUsuario:usuariosSeleccionados[j2].clave, permisos:permisos }) });
        }

        Swal.fire({ icon:'success', title:'Permisos guardados', text:usuariosSeleccionados.length + ' usuario(s) actualizados', timer:1800, showConfirmButton:false })
        .then(function(){ cerrarModalAdmin(); });
        usuariosSeleccionados = [];
        document.getElementById('adminChipsUsuarios').innerHTML = '';

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }
}

/* ============================================
MODAL PERMISOS EXISTENTES
============================================ */

async function abrirPermisosExistentes(){
    document.getElementById('modalPermisosExistentes').style.display = 'flex';
    var idReporte = document.getElementById('adminSelectReporte').value;
    if(idReporte){
        document.getElementById('mpeSelectReporte').value = idReporte;
        cargarTablaPermisosExistentes(idReporte);
    }
}

function cerrarPermisosExistentes(){
    document.getElementById('modalPermisosExistentes').style.display = 'none';
}

async function cargarTablaPermisosExistentes(idReporte){
    var contenedor = document.getElementById('mpeContenido');
    if(!idReporte){ contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">Selecciona un reporte</p>'; return; }
    contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px;">Cargando...</p>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getUsuariosConPermisos', idReporte:idReporte }) });
        var json = await res.json();

        if(json.status !== 'ok'){ contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + json.message + '</p>'; return; }
        if(json.data.length === 0){ contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">Sin usuarios con permisos</p>'; return; }

        var html = '<table class="mpe-tabla"><thead><tr><th>Usuario</th><th>Clave</th><th>Campos habilitados</th><th>Acción</th></tr></thead><tbody>';

        json.data.forEach(function(u){
            var chips = u.campos.map(function(c){ return '<span class="campo-chip-small">' + c + '</span>'; }).join('');

            html += '<tr><td style="font-weight:600;">' + u.nombre + '</td><td style="color:#64748b;">' + u.clave + '</td><td><div class="campos-chips-small">' + chips + '</div></td><td style="white-space:nowrap;"><button class="btn-editar-permiso" onclick="editarPermisoUsuario(\'' + u.clave + '\', \'' + u.nombre + '\')">✏️ Editar</button>&nbsp;<button class="btn-editar-permiso" onclick="quitarPermisosUsuario(\'' + u.clave + '\', \'' + u.nombre + '\')" style="background:#fee2e2;color:#dc2626;">🗑 Quitar</button></td></tr>';
        });

        html += '</tbody></table>';
        contenedor.innerHTML = html;

    } catch(e){
        contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + e.message + '</p>';
    }
}

async function editarPermisoUsuario(clave, nombre){
    cerrarPermisosExistentes();
    var idReporte = document.getElementById('mpeSelectReporte').value;

    usuariosSeleccionados = [];
    document.getElementById('adminChipsUsuarios').innerHTML = '';
    agregarUsuarioChip({ clave:clave, nombre:nombre });

    var contenedor = document.getElementById('adminCampos');
    contenedor.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Cargando campos...</p>';

    try{
        var res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'getPermisosUsuario', idReporte:idReporte, idUsuario:clave }) });
        var json = await res.json();
        if(json.status !== 'ok'){ contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error al cargar campos</p>'; return; }
        contenedor.innerHTML = '';
        json.data.forEach(function(campo){
            var checked = campo.puede_ver == 1 ? 'checked' : '';
            contenedor.innerHTML +=
                '<label style="display:flex;align-items:center;gap:10px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 14px;cursor:pointer;font-size:13px;font-weight:500;color:#0f172a;">' +
                    '<input type="checkbox" data-id-campo="' + campo.id + '" ' + checked + ' style="width:16px;height:16px;cursor:pointer;accent-color:#2563eb;">' +
                    campo.alias_campo +
                '</label>';
        });
    } catch(e){
        contenedor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + e.message + '</p>';
    }
}


async function quitarPermisosUsuario(clave, nombre){

    var idReporte = document.getElementById('mpeSelectReporte').value;

    var confirm = await Swal.fire({
        icon:'warning',
        title:'¿Quitar permisos?',
        html:'Se quitarán todos los permisos de <b>' + nombre + '</b> en este reporte.',
        showCancelButton:true,
        confirmButtonText:'Sí, quitar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc2626'
    });

    if(!confirm.isConfirmed) return;

    Swal.fire({ title:'Quitando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:    'quitarPermisosUsuario',
                idReporte: idReporte,
                idUsuario: clave
            })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){
            Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql });
            return;
        }

        Swal.fire({ icon:'success', title:'Permisos quitados', timer:1200, showConfirmButton:false })
            .then(function(){
                cargarTablaPermisosExistentes(idReporte);
            });

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}


async function quitarRolUsuario(clave, nombre, idRol){

    var confirm = await Swal.fire({
        icon:'warning',
        title:'¿Quitar rol?',
        html:'<b>' + nombre + '</b> perderá su rol y acceso a los reportes del mismo.',
        showCancelButton:true,
        confirmButtonText:'Sí, quitar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc2626'
    });

    if(!confirm.isConfirmed) return;

    Swal.fire({ title:'Quitando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:    'quitarRolUsuario',
                idUsuario: clave
            })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){
            Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql });
            return;
        }

        Swal.fire({ icon:'success', title:'Rol quitado', timer:1200, showConfirmButton:false })
            .then(function(){
                cargarDetalleRol({ id:idRol, nombre:document.getElementById('rolDetalleNombre').textContent });
            });

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}
/* ============================================
VARIABLES FILTROS
============================================ */

var filtrosActivos   = {};   // { campo_bd: valor }
var filtrosBusqueda  = {};   // { campo_bd: [seleccionados] }
var filtrosConfig    = [];   // config traída del API

/* ============================================
GENERAR REPORTE — abre modal filtros
============================================ */

async function generarReporte(){

    if(!reporteActivo){ Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un reporte primero' }); return; }

    var columnas = getColumnasActivas();
    if(columnas.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos una columna' }); return; }

    await abrirModalFiltros();

}

/* ============================================
ABRIR MODAL FILTROS APLICAR FILTROS Y GENERAR
============================================ */

async function abrirModalFiltros(){

    filtrosActivos  = {};
    filtrosBusqueda = {};

    document.getElementById('modalFiltros').style.display = 'flex';
    document.getElementById('mfBody').innerHTML = '<p style="color:#94a3b8;font-size:13px;text-align:center;">Cargando filtros...</p>';

    try{

        var [resFiltros, resTokens] = await Promise.all([
            fetch(API, { method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'getFiltrosReporte', idReporte:reporteActivo }) }),
            fetch(API, { method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'getTokensReporte', idReporte:reporteActivo }) })
        ]);

        var jsonFiltros = await resFiltros.json();
        var jsonTokens  = await resTokens.json();

        console.log('tokens response:', JSON.stringify(jsonTokens.data));

        if(jsonFiltros.status !== 'ok'){
            document.getElementById('mfBody').innerHTML = '<p style="color:#ef4444;">Error al cargar filtros</p>';
            return;
        }

        filtrosConfig = jsonFiltros.data;

        var columnasActivas = getColumnasActivas();

        var conFiltro = jsonFiltros.data.filter(function(f){
            return f.tipo_filtro !== 'ninguno' && columnasActivas.includes(f.campo_bd);
        });

        var tokensConf = jsonTokens.status === 'ok' ? jsonTokens.data : [];

        /* Filtrar tokens: solo mostrar si su bloque_alias está en columnas activas,
           o si no tiene bloque (token global) */
        tokensConf = tokensConf.filter(function(tok){
            if(!tok.bloque_alias) return true;
            return columnasActivas.includes(tok.bloque_alias);
        });

        if(conFiltro.length === 0 && tokensConf.length === 0){
            document.getElementById('mfBody').innerHTML =
                '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px;">' +
                'Este reporte no tiene filtros configurados.<br>Se traerán todos los registros.</p>';
            return;
        }

        var html = '';

        /* ── FILTROS DE CAMPOS ── */
        conFiltro.forEach(function(f){

            html += '<div class="filtro-item" data-campo="' + f.campo_bd + '" data-tipo="' + f.tipo_filtro + '">';
            html += '<label>' + f.alias_campo + '</label>';

            if(f.tipo_filtro === 'rango_fecha'){
                html +=
                    '<div class="filtro-rango">' +
                        '<input type="date" class="filtro-desde" data-campo="' + f.campo_bd + '">' +
                        '<span>al</span>' +
                        '<input type="date" class="filtro-hasta" data-campo="' + f.campo_bd + '">' +
                    '</div>';

            } else if(f.tipo_filtro === 'si_no'){
                html +=
                    '<div class="filtro-sino">' +
                        '<label><input type="radio" name="sino_' + f.campo_bd + '" value="" checked> Todos</label>' +
                        '<label><input type="radio" name="sino_' + f.campo_bd + '" value="SI"> Sí</label>' +
                        '<label><input type="radio" name="sino_' + f.campo_bd + '" value="NO"> No</label>' +
                    '</div>';

            } else if(f.tipo_filtro === 'busqueda_bd'){
                filtrosBusqueda[f.campo_bd] = [];
                html +=
                    '<div class="filtro-busqueda-wrap">' +
                        '<input type="text" class="filtro-busqueda-input" ' +
                            'data-campo="' + f.campo_bd + '" ' +
                            'data-query="' + encodeURIComponent(f.query_filtro) + '" ' +
                            'placeholder="Escribe para buscar...">' +
                        '<div class="filtro-busqueda-dropdown" id="drop_' + f.campo_bd + '"></div>' +
                    '</div>' +
                    '<div class="filtro-chips-wrap" id="chips_' + f.campo_bd + '"></div>';

            } else if(f.tipo_filtro === 'texto_libre'){
                html +=
                    '<input type="text" class="filtro-busqueda-input filtro-texto-libre" ' +
                        'data-campo="' + f.campo_bd + '" ' +
                        'placeholder="Ej: > 0, < 1000, LIKE \'%algo%\'">';
            }

            html += '</div>';
        });

        /* ── TOKENS DINÁMICOS ── */
        if(tokensConf.length > 0){

            if(conFiltro.length > 0){
                html += '<div style="height:1px;background:#e2e8f0;margin:4px 0;"></div>';
            }

            tokensConf.forEach(function(tok){

                html += '<div class="filtro-item" data-token="' + tok.token + '" data-tipo-token="' + tok.tipo + '">';
                html += '<label>' + tok.label + '</label>';

                if(tok.tipo === 'fecha'){
                    html += '<input type="date" class="filtro-busqueda-input token-valor" ' +
                            'data-token="' + tok.token + '" value="' + (tok.valor_default || '') + '">';

                } else if(tok.tipo === 'rango_fecha'){
                    html +=
                        '<div class="filtro-rango">' +
                            '<input type="date" class="filtro-busqueda-input token-desde" ' +
                                'data-token="' + tok.token + '_DESDE" value="' + (tok.valor_default || '') + '">' +
                            '<span>al</span>' +
                            '<input type="date" class="filtro-busqueda-input token-hasta" ' +
                                'data-token="' + tok.token + '_HASTA">' +
                        '</div>';

                } else if(tok.tipo === 'numero'){
                    html += '<input type="number" class="filtro-busqueda-input token-valor" ' +
                            'data-token="' + tok.token + '" value="' + (tok.valor_default || '') + '">';

                } else if(tok.tipo === 'si_no'){
                    html +=
                        '<div class="filtro-sino">' +
                            '<label><input type="radio" name="tok_' + tok.token + '" class="token-radio" data-token="' + tok.token + '" value="" checked> Todos</label>' +
                            '<label><input type="radio" name="tok_' + tok.token + '" class="token-radio" data-token="' + tok.token + '" value="1"> Sí</label>' +
                            '<label><input type="radio" name="tok_' + tok.token + '" class="token-radio" data-token="' + tok.token + '" value="0"> No</label>' +
                        '</div>';

                } else if(tok.tipo === 'busqueda_bd' || tok.tipo === 'seleccion'){
                    var clave = '__tok_' + tok.token;
                    filtrosBusqueda[clave] = [];
                    html +=
                        '<div class="filtro-busqueda-wrap">' +
                            '<input type="text" class="filtro-busqueda-input" ' +
                                'data-campo="' + clave + '" ' +
                                'data-query="' + encodeURIComponent(tok.query_token || '') + '" ' +
                                'data-token="' + tok.token + '" ' +
                                'placeholder="Escribe para buscar...">' +
                            '<div class="filtro-busqueda-dropdown" id="drop_' + clave + '"></div>' +
                        '</div>' +
                        '<div class="filtro-chips-wrap" id="chips_' + clave + '"></div>';

                } else {
                    html += '<input type="text" class="filtro-busqueda-input token-valor" ' +
                            'data-token="' + tok.token + '" value="' + (tok.valor_default || '') + '" ' +
                            'placeholder="' + tok.label + '">';
                }

                html += '</div>';
            });
        }

        document.getElementById('mfBody').innerHTML = html;

        /* Eventos búsqueda BD */
        document.querySelectorAll('.filtro-busqueda-input[data-query]').forEach(function(input){
            input.addEventListener('input', function(){ buscarEnBD(this); });
            input.addEventListener('blur', function(){
                var campo = this.dataset.campo;
                setTimeout(function(){
                    var drop = document.getElementById('drop_' + campo);
                    if(drop) drop.style.display = 'none';
                }, 200);
            });
        });

    } catch(e){
        document.getElementById('mfBody').innerHTML = '<p style="color:#ef4444;">Error: ' + e.message + '</p>';
    }

}


async function aplicarFiltros(){

    var columnas = getColumnasActivas();
    if(columnas.length === 0){ Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos una columna' }); return; }

    /* ── FILTROS DE CAMPOS ── */
    var filtros = [];

    document.querySelectorAll('.filtro-item[data-campo]').forEach(function(div){

        var campo = div.dataset.campo;
        var tipo  = div.dataset.tipo;

        if(tipo === 'rango_fecha'){
            var desde = div.querySelector('.filtro-desde').value;
            var hasta = div.querySelector('.filtro-hasta').value;
            if(desde || hasta) filtros.push({ campo:campo, tipo:tipo, desde:desde, hasta:hasta });

        } else if(tipo === 'si_no'){
            var checked = div.querySelector('.filtro-sino input:checked');
            if(checked && checked.value !== '') filtros.push({ campo:campo, tipo:tipo, valor:checked.value });

        } else if(tipo === 'busqueda_bd'){
            var seleccionados = filtrosBusqueda[campo] || [];
            if(seleccionados.length > 0) filtros.push({ campo:campo, tipo:tipo, valores:seleccionados });
        } else if(tipo === 'texto_libre'){

            var val = div.querySelector('.filtro-texto-libre').value.trim();
            if(val){
                filtros.push({ campo:campo, tipo:tipo, valor:val });
            }

        }

    });

    /* ── TOKENS ── */
    var tokensValores = {};

    /* Inputs simples (fecha, numero, texto) */
    document.querySelectorAll('.token-valor').forEach(function(input){
        if(input.value.trim()) tokensValores[input.dataset.token] = input.value.trim();
    });

    /* Rango de fechas — desde */
    document.querySelectorAll('.token-desde').forEach(function(input){
        if(input.value.trim()) tokensValores[input.dataset.token] = input.value.trim();
    });

    /* Rango de fechas — hasta */
    document.querySelectorAll('.token-hasta').forEach(function(input){
        if(input.value.trim()) tokensValores[input.dataset.token] = input.value.trim();
    });

    /* Radios si/no */
    document.querySelectorAll('.token-radio:checked').forEach(function(radio){
        if(radio.value !== '') tokensValores[radio.dataset.token] = radio.value;
    });

    /* Búsqueda BD */
    Object.keys(filtrosBusqueda).forEach(function(key){
        if(key.startsWith('__tok_') && filtrosBusqueda[key].length > 0){
            var tokenNombre = key.replace('__tok_', '');
            tokensValores[tokenNombre] = filtrosBusqueda[key][0];
        }
    });

    /* ← AQUÍ: Validar tokens obligatorios */
    var tokensFaltantes = [];
    document.querySelectorAll('.filtro-item[data-token]').forEach(function(div){
        var inputs = div.querySelectorAll('input');
        var tieneValor = false;
        inputs.forEach(function(inp){ if(inp.value.trim()) tieneValor = true; });
        if(!tieneValor){
            var label = div.querySelector('label');
            tokensFaltantes.push(label ? label.textContent : div.dataset.token);
        }
    });

    if(tokensFaltantes.length > 0){
        Swal.fire({
            icon:'warning',
            title:'Faltan valores',
            html:'Completa los siguientes filtros:<br><b>' + tokensFaltantes.join(', ') + '</b>'
        });
        return;
    }

    ultimosFiltros = filtros;
    ultimosTokens  = tokensValores;

    cerrarModalFiltros();

    Swal.fire({ title:'Generando reporte...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
    console.log('tokensValores:', tokensValores);

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:    'generarReporte',
                idReporte: reporteActivo,
                columnas:  columnas,
                limite:    50,
                filtros:   filtros,
                tokens:    tokensValores
            })
        });

        var json = await res.json();

        Swal.close();

        ultimoSQL            = json.sql || '';
        ultimoQueryProcesado = json.query_procesado || '';

        console.log('query_procesado:', json.query_procesado);
        console.log('sql final:', json.sql);

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.mysql || json.message }); return; }

        mostrarResultados(json.columnas, json.data);

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}


/* ============================================
CERRAR MODAL FILTROS
============================================ */

function cerrarModalFiltros(){
    document.getElementById('modalFiltros').style.display = 'none';
}

/* ============================================
BUSCAR EN BD (keyup)
============================================ */

async function buscarEnBD(input){

    var campo   = input.dataset.campo;
    var query   = decodeURIComponent(input.dataset.query);
    var termino = input.value.trim();
    var drop    = document.getElementById('drop_' + campo);

    if(termino.length < 1){ drop.style.display = 'none'; return; }

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:  'buscarFiltro',
                query:   query,
                termino: termino,
                campo:   campo
            })
        });

        var json = await res.json();

        if(json.status !== 'ok' || json.data.length === 0){
            drop.innerHTML = '<div class="filtro-busqueda-item" style="color:#94a3b8;">Sin resultados</div>';
            drop.style.display = 'block';
            return;
        }

        drop.innerHTML = '';

        json.data.forEach(function(val){

            var item = document.createElement('div');
            item.className   = 'filtro-busqueda-item';
            item.textContent = val;

            item.addEventListener('mousedown', function(){
                agregarChipFiltro(campo, val);
                input.value = '';
                drop.style.display = 'none';
            });

            drop.appendChild(item);

        });

        drop.style.display = 'block';

    } catch(e){
        console.error(e);
    }

}

/* ============================================
AGREGAR CHIP FILTRO BÚSQUEDA
============================================ */

function agregarChipFiltro(campo, valor){

    if(!filtrosBusqueda[campo]) filtrosBusqueda[campo] = [];

    /* Evitar duplicados */
    if(filtrosBusqueda[campo].includes(valor)) return;

    filtrosBusqueda[campo].push(valor);

    var wrap = document.getElementById('chips_' + campo);

    var chip = document.createElement('div');
    chip.className = 'filtro-chip-selected';
    chip.innerHTML =
        '<span>' + valor + '</span>' +
        '<button onclick="quitarChipFiltro(\'' + campo + '\', \'' + valor.replace(/'/g, "\\'") + '\', this)">✕</button>';

    wrap.appendChild(chip);

}

/* ============================================
QUITAR CHIP FILTRO
============================================ */

function quitarChipFiltro(campo, valor, btn){
    filtrosBusqueda[campo] = filtrosBusqueda[campo].filter(function(v){ return v !== valor; });
    btn.closest('.filtro-chip-selected').remove();
}

/* ============================================
LIMPIAR FILTROS
============================================ */

function limpiarFiltros(){

    /* Fechas */
    document.querySelectorAll('.filtro-desde, .filtro-hasta').forEach(function(i){ i.value = ''; });

    /* Si/No */
    document.querySelectorAll('.filtro-sino input[type="radio"]').forEach(function(r){
        if(r.value === '') r.checked = true;
    });

    /* Búsqueda */
    filtrosBusqueda = {};
    document.querySelectorAll('.filtro-chips-wrap').forEach(function(w){ w.innerHTML = ''; });
    document.querySelectorAll('.filtro-busqueda-input').forEach(function(i){ i.value = ''; });

    /* Re-inicializar arrays */
    document.querySelectorAll('.filtro-item[data-tipo="busqueda_bd"]').forEach(function(div){
        filtrosBusqueda[div.dataset.campo] = [];
    });

}



/* ============================================
VARIABLES ROLES
============================================ */

var rolActivoId = null;

/* ============================================
ABRIR MODAL ROLES
============================================ */

async function abrirModalRoles(){

    rolActivoId = null;
    document.getElementById('rolDetalle').innerHTML =
        '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">Selecciona un rol</p>';
    document.getElementById('rolDetalleNombre').textContent = 'Selecciona un rol';
    document.getElementById('btnGuardarRol').style.display  = 'none';
    document.getElementById('modalRoles').style.display     = 'flex';

    await cargarListaRoles();

}

function cerrarModalRoles(){
    document.getElementById('modalRoles').style.display = 'none';
}

/* ============================================
CARGAR LISTA DE ROLES
============================================ */

async function cargarListaRoles(){

    var lista = document.getElementById('rolesLista');
    lista.innerHTML = '<p style="color:#94a3b8;font-size:13px;padding:10px;">Cargando...</p>';

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getRoles' })
        });

        var json = await res.json();

        if(json.status !== 'ok'){
            lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error al cargar roles</p>';
            return;
        }

        lista.innerHTML = '';

        json.data.forEach(function(rol){

            var item = document.createElement('div');
            item.dataset.id = rol.id;

            item.style.cssText =
                'padding:10px 12px;border-radius:10px;background:#f8fafc;' +
                'border:1.5px solid #e2e8f0;cursor:pointer;transition:.15s;' +
                'font-size:13px;font-weight:600;color:#0f172a;';

            item.innerHTML =
                '<div>' + rol.nombre + '</div>' +
                '<div style="font-size:11px;font-weight:400;color:#94a3b8;margin-top:3px;">' +
                    (rol.puede_crear == 1 ? '✓ Crear ' : '') +
                    (rol.puede_admin == 1 ? '✓ Admin ' : '') +
                    '✓ Ver' +
                '</div>';

            item.addEventListener('click', function(){

                /* Marcar activo */
                document.querySelectorAll('#rolesLista > div').forEach(function(d){
                    d.style.background    = '#f8fafc';
                    d.style.borderColor   = '#e2e8f0';
                    d.style.color         = '#0f172a';
                });

                item.style.background  = '#eff6ff';
                item.style.borderColor = '#bfdbfe';
                item.style.color       = '#1d4ed8';

                cargarDetalleRol(rol);

            });

            lista.appendChild(item);

        });

    } catch(e){
        lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error: ' + e.message + '</p>';
    }

}

/* ============================================
CARGAR DETALLE DE UN ROL
============================================ */

async function cargarDetalleRol(rol){

    rolActivoId = rol.id;
    document.getElementById('rolDetalleNombre').textContent = rol.nombre;
    document.getElementById('btnGuardarRol').style.display  = 'inline-block';

    var detalle = document.getElementById('rolDetalle');
    detalle.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Cargando...</p>';

    try{

        /* Cargar reportes disponibles y permisos del rol */
        var [resReportes, resPermisos, resUsuarios] = await Promise.all([

            fetch(API, { method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'getReportesPersonales' }) }),

            fetch(API, { method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'getPermisosRol', idRol:rol.id }) }),

            fetch(API, { method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ action:'getUsuariosConRoles' }) })

        ]);

        var jsonReportes = await resReportes.json();
        var jsonPermisos = await resPermisos.json();
        var jsonUsuarios = await resUsuarios.json();

        var reportesConPermiso = jsonPermisos.data || [];
        var usuariosDelRol     = (jsonUsuarios.data || []).filter(function(u){ return u.idRol == rol.id; });

        var html = '';

        /* PERMISOS DEL ROL */
        html += '<div>';
        html += '<div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;' +
                'letter-spacing:.06em;margin-bottom:10px;">Reportes con acceso</div>';
        html += '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;">';

        html +=
            '<label style="display:flex;align-items:center;gap:8px;background:#0177cb;' +
            'border-radius:10px;padding:10px 12px;cursor:pointer;font-size:13px;' +
            'font-weight:700;color:white;grid-column:1/-1;">' +
                '<input type="checkbox" id="chkTodosRol" ' +
                    'style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer;" ' +
                    'onchange="toggleTodosRol(this)">' +
                '✅ Seleccionar todos' +
            '</label>';

        (jsonReportes.data || []).forEach(function(r){

            var checked = reportesConPermiso.includes(r.id) ? 'checked' : '';

            html +=
                '<label style="display:flex;align-items:center;gap:8px;background:white;' +
                'border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 12px;' +
                'cursor:pointer;font-size:13px;font-weight:500;color:#0f172a;">' +
                    '<input type="checkbox" data-reporte-id="' + r.id + '" ' + checked + ' ' +
                        'style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer;">' +
                    r.nombre +
                '</label>';

        });

        html += '</div></div>';

        /* USUARIOS CON ESTE ROL */
        html += '<div>';
        html += '<div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;' +
                'letter-spacing:.06em;margin-bottom:10px;">Usuarios con este rol</div>';

        if(usuariosDelRol.length === 0){
            html += '<p style="color:#94a3b8;font-size:13px;">Sin usuarios asignados</p>';
        } else {
            html += '<div style="display:flex;flex-wrap:wrap;gap:6px;">';
            usuariosDelRol.forEach(function(u){
                html += '<div style="display:inline-flex;align-items:center;gap:6px;background:#e8f4fd;' +
                        'border:1px solid #bfdbfe;color:#16a34a;border-radius:999px;' +
                        'padding:4px 12px;font-size:12px;font-weight:600;margin:2px;">' +
                            u.nombre +
                            '<button onclick="quitarRolUsuario(\'' + u.clave + '\', \'' + u.nombre + '\', ' + rol.id + ')" ' +
                                'style="background:none;border:none;color:#94a3b8;cursor:pointer;' +
                                'font-size:14px;padding:0;line-height:1;transition:.15s;" ' +
                                'onmouseover="this.style.color=\'#dc2626\'" ' +
                                'onmouseout="this.style.color=\'#94a3b8\'">✕</button>' +
                        '</div>';
            });
            html += '</div>';
        }

        html += '</div>';

        /* ASIGNAR USUARIOS AL ROL */
        html += '<div>';
        html += '<div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;' +
                'letter-spacing:.06em;margin-bottom:10px;">Asignar usuarios</div>';

        html += '<div style="position:relative;">' +
                    '<input type="text" id="buscarUsuarioRol" ' +
                        'placeholder="Buscar usuario para asignar..." ' +
                        'style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;' +
                        'padding:10px 14px;font-size:13px;outline:none;" ' +
                        'oninput="buscarUsuarioParaRol(this.value, ' + rol.id + ', ' +
                        JSON.stringify(jsonUsuarios.data || []).replace(/"/g, '&quot;') + ')">' +
                    '<div id="dropUsuarioRol" ' +
                        'style="position:absolute;top:calc(100% + 4px);left:0;right:0;' +
                        'background:white;border:1.5px solid #e2e8f0;border-radius:12px;' +
                        'box-shadow:0 10px 30px rgba(0,0,0,.1);z-index:9999;' +
                        'max-height:180px;overflow-y:auto;display:none;">' +
                    '</div>' +
                '</div>';

        html += '</div>';

        detalle.innerHTML = html;

    } catch(e){
        detalle.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + e.message + '</p>';
    }

}

/* ============================================
BUSCAR USUARIO PARA ASIGNAR ROL
============================================ */

function buscarUsuarioParaRol(query, idRol, todosUsuarios){

    var drop = document.getElementById('dropUsuarioRol');

    if(query.length < 1){ drop.style.display = 'none'; return; }

    var q = query.toLowerCase();
    var resultados = todosUsuarios.filter(function(u){
        return u.nombre.toLowerCase().includes(q) || u.clave.toLowerCase().includes(q);
    });

    if(resultados.length === 0){
        drop.innerHTML = '<div style="padding:10px 14px;font-size:13px;color:#94a3b8;">Sin resultados</div>';
        drop.style.display = 'block';
        return;
    }

    drop.innerHTML = '';

    resultados.slice(0, 8).forEach(function(u){

        var item = document.createElement('div');
        item.style.cssText = 'padding:10px 14px;font-size:13px;cursor:pointer;' +
                             'border-bottom:1px solid #f1f5f9;transition:.15s;color:#0f172a;';
        item.innerHTML = u.nombre +
            '<small style="color:#94a3b8;font-size:11px;margin-left:6px;">' + u.clave + '</small>' +
            (u.nombreRol ? '<small style="color:#2563eb;font-size:11px;margin-left:6px;">[' + u.nombreRol + ']</small>' : '');

        item.addEventListener('mouseover', function(){ this.style.background = '#eff6ff'; });
        item.addEventListener('mouseout',  function(){ this.style.background = 'white'; });

        item.addEventListener('mousedown', async function(){

            drop.style.display = 'none';
            document.getElementById('buscarUsuarioRol').value = '';

            /* Si ya tiene otro rol, avisar */
            if(u.nombreRol && u.idRol != idRol){

                var confirm = await Swal.fire({
                    icon:'warning',
                    title:'Usuario con rol existente',
                    html: '<b>' + u.nombre + '</b> ya tiene el rol <b>' + u.nombreRol + '</b>.<br>¿Deseas cambiarle el rol?',
                    showCancelButton:true,
                    confirmButtonText:'Sí, cambiar',
                    cancelButtonText:'Cancelar',
                    confirmButtonColor:'#2563eb'
                });

                if(!confirm.isConfirmed) return;

            }

            /* Asignar rol */
            try{

                var res  = await fetch(API, {
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({ action:'asignarRol', idUsuario:u.clave, idRol:idRol })
                });

                var json = await res.json();

                if(json.status !== 'ok'){
                    Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql });
                    return;
                }

                Swal.fire({ icon:'success', title:'Rol asignado', timer:1200, showConfirmButton:false })
                .then(function(){
                    cargarDetalleRol({ id:idRol, nombre:document.getElementById('rolDetalleNombre').textContent });
                });


            } catch(e){
                Swal.fire({ icon:'error', title:'Error JS', text:e.message });
            }

        });

        drop.appendChild(item);

    });

    drop.style.display = 'block';

}

/* ============================================
GUARDAR CONFIG DEL ROL (reportes)
============================================ */

async function guardarConfigRol(){

    if(!rolActivoId) return;

    var checkboxes = document.querySelectorAll('#rolDetalle input[type="checkbox"][data-reporte-id]');
    var reportes   = [];

    checkboxes.forEach(function(cb){
        if(cb.checked) reportes.push(parseInt(cb.dataset.reporteId));
    });

    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'guardarPermisosRol', idRol:rolActivoId, reportes:reportes })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql }); return; }

        Swal.fire({ icon:'success', title:'Guardado', timer:1200, showConfirmButton:false })
        .then(function(){ cerrarModalRoles(); cargarReportes(); });

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* ============================================
ABRIR CREAR ROL
============================================ */

async function abrirCrearRol(){

    var result = await Swal.fire({
        title:'Nuevo Rol',
        html:
            '<input id="swal-nombre" class="swal2-input" placeholder="Nombre del rol">' +
            '<div style="display:flex;gap:20px;justify-content:center;margin-top:12px;">' +
                '<label style="display:flex;align-items:center;gap:6px;font-size:14px;">' +
                    '<input type="checkbox" id="swal-crear"> Puede crear reportes' +
                '</label>' +
                '<label style="display:flex;align-items:center;gap:6px;font-size:14px;">' +
                    '<input type="checkbox" id="swal-admin"> Puede administrar' +
                '</label>' +
            '</div>',
        showCancelButton:true,
        confirmButtonText:'Crear',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#2563eb',
        preConfirm: function(){
            var nombre = document.getElementById('swal-nombre').value.trim();
            if(!nombre){ Swal.showValidationMessage('El nombre es requerido'); return false; }
            return {
                nombre:       nombre,
                puede_crear:  document.getElementById('swal-crear').checked ? 1 : 0,
                puede_admin:  document.getElementById('swal-admin').checked ? 1 : 0
            };
        }
    });

    if(!result.isConfirmed) return;

    Swal.fire({ title:'Creando rol...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'crearRol', ...result.value })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){ Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql }); return; }

        Swal.fire({ icon:'success', title:'Rol creado', timer:1200, showConfirmButton:false })
        .then(function(){ cargarListaRoles(); });


    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* Evento botón roles */
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('btnAdminRoles')
        .addEventListener('click', abrirModalRoles);
});


/* ============================================
VARIABLES EDITOR
============================================ */

var erReporteActivo = null;

/* ============================================
ABRIR MODAL EDITAR REPORTES
============================================ */

async function abrirEditarReportes(){

    erReporteActivo = null;
    document.getElementById('erEditor').innerHTML =
        '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">Selecciona un reporte para editarlo</p>';
    document.getElementById('erTituloEditor').textContent = 'Selecciona un reporte';
    document.getElementById('erBtnGuardar').style.display  = 'none';
    document.getElementById('erBtnEliminar').style.display = 'none';
    document.getElementById('modalEditarReportes').style.display = 'flex';

    await cargarListaEditarReportes();

}

function cerrarEditarReportes(){
    document.getElementById('modalEditarReportes').style.display = 'none';
}

/* ============================================
CARGAR LISTA DE REPORTES
============================================ */

async function cargarListaEditarReportes(){

    var lista = document.getElementById('erListaReportes');
    lista.innerHTML = '<p style="color:#94a3b8;font-size:13px;padding:10px;">Cargando...</p>';

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getReportesPersonales' })
        });

        var json = await res.json();

        if(json.status !== 'ok'){
            lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error</p>';
            return;
        }

        lista.innerHTML = '';

        json.data.forEach(function(r){

            var item = document.createElement('div');
            item.dataset.id = r.id;
            item.style.cssText =
                'padding:10px 12px;border-radius:10px;background:#f8fafc;' +
                'border:1.5px solid #e2e8f0;cursor:pointer;transition:.15s;';

            item.innerHTML =
                '<div style="font-size:13px;font-weight:600;color:#0f172a;">' + r.nombre + '</div>' +
                '<div style="font-size:11px;color:' + (r.activo == 1 ? '#16a34a' : '#ef4444') + ';margin-top:2px;">' +
                    (r.activo == 1 ? '● Activo' : '● Inactivo') +
                '</div>';

            item.addEventListener('click', function(){

                document.querySelectorAll('#erListaReportes > div').forEach(function(d){
                    d.style.background  = '#f8fafc';
                    d.style.borderColor = '#e2e8f0';
                });

                item.style.background  = '#eff6ff';
                item.style.borderColor = '#bfdbfe';

                cargarEditorReporte(r);

            });

            lista.appendChild(item);

        });

    } catch(e){
        lista.innerHTML = '<p style="color:#ef4444;font-size:13px;padding:10px;">Error: ' + e.message + '</p>';
    }

}

/* ============================================
CARGAR EDITOR DE UN REPORTE
============================================ */

async function cargarEditorReporte(reporte){

    erReporteActivo = reporte.id;
    document.getElementById('erTituloEditor').textContent = reporte.nombre;
    document.getElementById('erBtnGuardar').style.display  = 'inline-block';
    document.getElementById('erBtnEliminar').style.display = 'inline-block';

    var editor = document.getElementById('erEditor');
    editor.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Cargando campos...</p>';

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'getReporteCompleto', idReporte:reporte.id })
        });

        var json = await res.json();

        if(json.status !== 'ok'){
            editor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + json.message + '</p>';
            return;
        }

        var html = '';

        /* NOMBRE */
        html +=
            '<div class="form-group">' +
                '<label>Nombre del reporte</label>' +
                '<input type="text" id="erNombre" class="er-input" value="' + json.data.nombre + '">' +
            '</div>';

        /* ACTIVO */
        html +=
            '<div class="form-group">' +
                '<label>Estado</label>' +
                '<select id="erActivo" class="er-select">' +
                    '<option value="1" ' + (json.data.activo == 1 ? 'selected' : '') + '>Activo</option>' +
                    '<option value="0" ' + (json.data.activo == 0 ? 'selected' : '') + '>Inactivo</option>' +
                '</select>' +
            '</div>';

        /* QUERY */
        html +=
            '<div class="form-group">' +
                '<label>Query Base</label>' +
                '<textarea id="erQuery" class="er-textarea" style="min-height:140px;">' +
                    json.data.query_base +
                '</textarea>' +
            '</div>';

        /* CAMPOS */
        html +=
            '<div>' +
                '<div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;' +
                'letter-spacing:.06em;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">' +
                    'Campos' +
                    '<button onclick="detectarCamposEditor()" ' +
                        'style="background:#0177cb;color:white;border:none;padding:4px 12px;' +
                        'border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;">🔍 Detectar del query</button>' +
                '</div>' +
                '<div id="erCampos" style="display:flex;flex-direction:column;gap:8px;">';

        json.data.campos.forEach(function(campo){

            html +=
                '<div class="er-campo-row" data-id="' + campo.id + '">' +
                    '<div class="er-campo-header">' +
                        '<span class="er-campo-bd">' + campo.campo_bd + '</span>' +
                        '<button onclick="this.closest(\'.er-campo-row\').remove()" ' +
                            'style="background:#fde8e8;color:#c0392b;border:none;padding:3px 8px;' +
                            'border-radius:6px;cursor:pointer;font-size:11px;">✕ Quitar</button>' +
                    '</div>' +
                    '<input type="text" class="er-input er-alias" ' +
                        'placeholder="Alias" value="' + campo.alias_campo + '" ' +
                        'title="Alias del campo">' +
                    '<select class="er-select er-tipo-filtro" onchange="erToggleQuery(this)">' +
                        '<option value="ninguno"     ' + (campo.tipo_filtro === 'ninguno'     ? 'selected' : '') + '>Sin filtro</option>' +
                        '<option value="rango_fecha" ' + (campo.tipo_filtro === 'rango_fecha' ? 'selected' : '') + '>Rango fecha</option>' +
                        '<option value="si_no"       ' + (campo.tipo_filtro === 'si_no'       ? 'selected' : '') + '>Sí / No / Todos</option>' +
                        '<option value="busqueda_bd" ' + (campo.tipo_filtro === 'busqueda_bd' ? 'selected' : '') + '>Búsqueda BD</option>' +
                        '<option value="texto_libre" ' + (campo.tipo_filtro === 'texto_libre' ? 'selected' : '') + '>Campo abierto</option>' +
                    '</select>' +
                    '<textarea class="er-textarea er-query-filtro" ' +
                        'placeholder="SELECT campo FROM tabla GROUP BY campo" ' +
                        'style="display:' + (campo.tipo_filtro === 'busqueda_bd' ? 'block' : 'none') + ';">' +
                        (campo.query_filtro || '') +
                    '</textarea>' +
                '</div>';

        });

        html += '</div></div>';

        /* TOKENS */
        var tokensExistentes = await cargarTokensEditor(reporte.id);
        var tokensDetectados = detectarTokens(json.data.query_base);

        html +=
            '<div>' +
                '<div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;' +
                'letter-spacing:.06em;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">' +
                    'Tokens dinámicos' +
                    '<button onclick="agregarTokenManual()" ' +
                        'style="background:#0177cb;color:white;border:none;padding:4px 12px;' +
                        'border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;">+ Agregar</button>' +
                '</div>' +
                '<div id="erTokens" style="display:flex;flex-direction:column;gap:8px;">';

        tokensDetectados.forEach(function(t){
            var existente = tokensExistentes.find(function(te){ return te.token === t; });
            html += construirTokenRow(t, existente);
        });

        tokensExistentes.forEach(function(te){
            if(!tokensDetectados.includes(te.token)){
                html += construirTokenRow(te.token, te);
            }
        });

        if(tokensDetectados.length === 0 && tokensExistentes.length === 0){
            html += '<p style="color:#94a3b8;font-size:13px;">No hay tokens en el query. Usa {{NOMBRE_TOKEN}} en el query para agregar.</p>';
        }

        html += '</div></div>';

        editor.innerHTML = html;

    } catch(e){
        editor.innerHTML = '<p style="color:#ef4444;font-size:13px;">Error: ' + e.message + '</p>';
    }

}

function detectarCamposEditor(){

    var query = document.getElementById('erQuery').value;
    if(!query){ Swal.fire({ icon:'warning', title:'Oops', text:'El query está vacío' }); return; }

    /* Detectar campos del SELECT externo (primera línea) */
    var match = query.match(/^SELECT\s+([\s\S]+?)\s+FROM/i);
    if(!match){ Swal.fire({ icon:'error', title:'Error', text:'No se detectó SELECT válido' }); return; }

    /* Parsear campos respetando paréntesis */
    var selectRaw = match[1];
    var campos    = [];
    var depth     = 0;
    var current   = '';

    for(var i = 0; i < selectRaw.length; i++){
        var char = selectRaw[i];
        if(char === '(') depth++;
        if(char === ')') depth--;
        if(char === ',' && depth === 0){ campos.push(current.trim()); current = ''; }
        else current += char;
    }
    if(current.trim()) campos.push(current.trim());

    /* Extraer aliases */
    var camposDetectados = campos.map(function(c){
        c = c.trim();
        if(/ as /i.test(c)) return c.split(/ as /i).pop().trim();
        return c.split('.').pop().trim();
    });

    /* Ver cuáles ya existen en el editor */
    var camposExistentes = [];
    document.querySelectorAll('#erCampos .er-campo-row[data-id]').forEach(function(row){
        camposExistentes.push(row.querySelector('.er-campo-bd').textContent.trim());
    });

    /* Agregar solo los nuevos */
    var nuevos = camposDetectados.filter(function(c){ return !camposExistentes.includes(c); });

    if(nuevos.length === 0){
        Swal.fire({ icon:'info', title:'Sin cambios', text:'No hay campos nuevos para agregar' });
        return;
    }

    var contenedor = document.getElementById('erCampos');

    nuevos.forEach(function(campo){
        var div = document.createElement('div');
        div.className = 'er-campo-row';
        div.dataset.id = 'nuevo_' + campo; /* marcado como nuevo */
        div.innerHTML =
            '<div class="er-campo-header">' +
                '<span class="er-campo-bd">' + campo + '</span>' +
                '<span style="background:#dcfce7;color:#16a34a;border-radius:999px;' +
                    'padding:2px 8px;font-size:10px;font-weight:700;">NUEVO</span>' +
                '<button onclick="this.closest(\'.er-campo-row\').remove()" ' +
                    'style="background:#fee2e2;color:#dc2626;border:none;padding:3px 8px;' +
                    'border-radius:6px;cursor:pointer;font-size:11px;">✕ Quitar</button>' +
            '</div>' +
            '<input type="text" class="er-input er-alias" placeholder="Alias" value="' + campo + '" title="Alias del campo">' +
            '<select class="er-select er-tipo-filtro" onchange="erToggleQuery(this)">' +
                '<option value="ninguno">Sin filtro</option>' +
                '<option value="rango_fecha">Rango fecha</option>' +
                '<option value="si_no">Sí / No / Todos</option>' +
                '<option value="busqueda_bd">Búsqueda BD</option>' +
                '<option value="texto_libre">Campo abierto</option>' +
            '</select>' +
            '<textarea class="er-textarea er-query-filtro" ' +
                'placeholder="SELECT campo FROM tabla GROUP BY campo" ' +
                'style="display:none;">' +
            '</textarea>';
        contenedor.appendChild(div);
    });

    Swal.fire({ icon:'success', title: nuevos.length + ' campo(s) agregado(s)', timer:1200, showConfirmButton:false });

}

function construirTokenRow(tokenNombre, existente){

    var label        = existente ? existente.label        : tokenNombre;
    var tipo         = existente ? existente.tipo         : 'texto';
    var queryToken   = existente ? (existente.query_token  || '') : '';
    var valorDefault = existente ? (existente.valor_default || '') : '';

    var inputDefault = '';

    if(tipo === 'fecha'){
        inputDefault = '<input type="date" class="er-input er-token-default" ' +
            'data-token="' + tokenNombre + '" value="' + valorDefault + '">';
    } else if(tipo === 'rango_fecha'){
        inputDefault =
            '<div style="display:flex;align-items:center;gap:8px;">' +
                '<input type="date" class="er-input er-token-default" ' +
                    'data-token="' + tokenNombre + '_DESDE" value="' + valorDefault + '" style="flex:1;">' +
                '<span style="color:#94a3b8;font-size:12px;">al</span>' +
                '<input type="date" class="er-input er-token-default" ' +
                    'data-token="' + tokenNombre + '_HASTA" value="" style="flex:1;">' +
            '</div>';
    } else {
        inputDefault = '<input type="text" class="er-input er-token-default" ' +
            'placeholder="Valor por default (opcional)" value="' + valorDefault + '" ' +
            'data-token="' + tokenNombre + '">';
    }

    return '<div class="er-campo-row" data-token="' + tokenNombre + '">' +

        '<div class="er-campo-header">' +
            '<span class="er-campo-bd">{{' + tokenNombre + '}}</span>' +
            '<button onclick="this.closest(\'.er-campo-row\').remove()" ' +
                'style="background:#fee2e2;color:#dc2626;border:none;padding:3px 8px;' +
                'border-radius:6px;cursor:pointer;font-size:11px;">✕ Quitar</button>' +
        '</div>' +

        '<input type="text" class="er-input er-token-label" ' +
            'placeholder="Label visible al usuario" value="' + label + '">' +

        '<select class="er-select er-token-tipo" onchange="erToggleTokenQuery(this)">' +
            '<option value="texto"       ' + (tipo === 'texto'       ? 'selected' : '') + '>Texto libre</option>' +
            '<option value="fecha"       ' + (tipo === 'fecha'       ? 'selected' : '') + '>Fecha</option>' +
            '<option value="rango_fecha" ' + (tipo === 'rango_fecha' ? 'selected' : '') + '>Rango de fechas</option>' +
            '<option value="numero"      ' + (tipo === 'numero'      ? 'selected' : '') + '>Número</option>' +
            '<option value="si_no"       ' + (tipo === 'si_no'       ? 'selected' : '') + '>Sí/No/Todos</option>' +
            '<option value="busqueda_bd" ' + (tipo === 'busqueda_bd' ? 'selected' : '') + '>Búsqueda BD</option>' +
            '<option value="seleccion"   ' + (tipo === 'seleccion'   ? 'selected' : '') + '>Selección fija</option>' +
        '</select>' +

        '<div class="er-token-default-wrap">' + inputDefault + '</div>' +

        '<textarea class="er-textarea er-token-query" ' +
            'placeholder="SELECT valor FROM tabla (para búsqueda BD o selección)" ' +
            'style="display:' + (['busqueda_bd','seleccion'].includes(tipo) ? 'block' : 'none') + ';">' +
            queryToken +
        '</textarea>' +

    '</div>';
}
function erToggleTokenQuery(select){

    var row      = select.closest('.er-campo-row');
    var textarea = row.querySelector('.er-token-query');
    var wrap     = row.querySelector('.er-token-default-wrap');
    var tipo     = select.value;
    var token    = row.dataset.token;

    /* Mostrar/ocultar query BD */
    textarea.style.display = ['busqueda_bd','seleccion'].includes(tipo) ? 'block' : 'none';

    /* Cambiar input de default según tipo */
    if(tipo === 'fecha'){
        wrap.innerHTML = '<input type="date" class="er-input er-token-default" data-token="' + token + '" value="">';
    } else if(tipo === 'rango_fecha'){
        wrap.innerHTML =
            '<div style="display:flex;align-items:center;gap:8px;">' +
                '<input type="date" class="er-input er-token-default" data-token="' + token + '_DESDE" value="" style="flex:1;">' +
                '<span style="color:#94a3b8;font-size:12px;">al</span>' +
                '<input type="date" class="er-input er-token-default" data-token="' + token + '_HASTA" value="" style="flex:1;">' +
            '</div>';
    } else {
        wrap.innerHTML = '<input type="text" class="er-input er-token-default" ' +
            'placeholder="Valor por default (opcional)" value="" data-token="' + token + '">';
    }

}

async function agregarTokenManual(){

    var result = await Swal.fire({
        title: 'Nuevo Token',
        input: 'text',
        inputPlaceholder: 'Ej: COBRO_DESDE',
        inputAttributes: { style: 'text-transform:uppercase;' },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#2563eb',
        inputValidator: function(value){
            if(!value) return 'El nombre es requerido';
            var limpio = value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
            if(!limpio) return 'Solo mayúsculas, números y guiones bajos';
        }
    });

    if(!result.isConfirmed) return;

    var nombre = result.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    if(!nombre) return;

    var contenedor = document.getElementById('erTokens');
    var div = document.createElement('div');
    div.innerHTML = construirTokenRow(nombre, null);
    contenedor.appendChild(div.firstChild);

}

/* Toggle query filtro en editor */
function erToggleQuery(select){
    var row      = select.closest('.er-campo-row');
    var textarea = row.querySelector('.er-query-filtro');
    textarea.style.display = select.value === 'busqueda_bd' ? 'block' : 'none';
}

/* ============================================
GUARDAR EDICIÓN REPORTE
============================================ */

async function guardarEdicionReporte(){

    if(!erReporteActivo) return;

    var nombre = document.getElementById('erNombre').value.trim();
    var query  = document.getElementById('erQuery').value.trim();
    var activo = document.getElementById('erActivo').value;

    if(!nombre || !query){
        Swal.fire({ icon:'warning', title:'Oops', text:'Nombre y query son requeridos' });
        return;
    }

    /* Recolectar campos */
    var campos = [];
    document.querySelectorAll('#erCampos .er-campo-row[data-id]').forEach(function(row){
        campos.push({
            id:           row.dataset.id,
            alias_campo:  row.querySelector('.er-alias').value.trim(),
            tipo_filtro:  row.querySelector('.er-tipo-filtro').value,
            query_filtro: row.querySelector('.er-query-filtro').value.trim()
        });
    });

    /* Recolectar tokens */
    var tokens = [];
    document.querySelectorAll('#erTokens .er-campo-row[data-token]').forEach(function(row){
        var tipoSelect    = row.querySelector('.er-token-tipo');
        var tipo          = tipoSelect ? tipoSelect.value : 'texto';
        var defaultInputs = row.querySelectorAll('.er-token-default');
        var valorDefault  = '';
        if(defaultInputs.length > 0) valorDefault = defaultInputs[0].value.trim();

        tokens.push({
            token:         row.dataset.token,
            label:         row.querySelector('.er-token-label').value.trim(),
            tipo:          tipo,
            query_token:   row.querySelector('.er-token-query').value.trim(),
            valor_default: valorDefault
        });
    });


    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                action:    'editarReporte',
                idReporte: erReporteActivo,
                nombre:    nombre,
                query:     query,
                activo:    activo,
                campos:    campos,
                tokens:    tokens
            })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){
            Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql });
            return;
        }

        Swal.fire({ icon:'success', title:'Guardado', timer:1200, showConfirmButton:false })
        .then(function(){ cerrarEditarReportes(); cargarReportes(); cargarListaEditarReportes(); });

    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* ============================================
ELIMINAR REPORTE
============================================ */

async function eliminarReporte(){

    if(!erReporteActivo) return;

    var confirm = await Swal.fire({
        icon:'warning',
        title:'¿Eliminar reporte?',
        html:'Esta acción <b>no se puede deshacer</b>.<br>Se eliminarán también los campos y configuraciones.',
        showCancelButton:true,
        confirmButtonText:'Sí, eliminar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc2626'
    });

    if(!confirm.isConfirmed) return;

    Swal.fire({ title:'Eliminando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

    try{

        var res  = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ action:'eliminarReporte', idReporte:erReporteActivo })
        });

        var json = await res.json();

        Swal.close();

        if(json.status !== 'ok'){
            Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql });
            return;
        }


        Swal.fire({ icon:'success', title:'Eliminado', timer:1200, showConfirmButton:false })
        .then(function(){ cerrarEditarReportes(); cargarReportes(); });

        erReporteActivo = null;
        document.getElementById('erEditor').innerHTML =
            '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:30px;">Reporte eliminado</p>';
        document.getElementById('erBtnGuardar').style.display  = 'none';
        document.getElementById('erBtnEliminar').style.display = 'none';
        document.getElementById('erTituloEditor').textContent  = 'Selecciona un reporte';


    } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error JS', text:e.message });
    }

}

/* ============================================
DETECTAR TOKENS EN EL QUERY
============================================ */

function detectarTokens(query){
    var regex  = /\{\{([A-Z0-9_]+)\}\}/g;
    var tokens = [];
    var match;
    var vistos = {};

    while((match = regex.exec(query)) !== null){
        if(!vistos[match[1]]){
            tokens.push(match[1]);
            vistos[match[1]] = true;
        }
    }
    return tokens;
}

/* ============================================
MODAL TOKENS — dentro del editor de reporte
============================================ */

/* Agrega esto en cargarEditorReporte, después del bloque de campos */
async function cargarTokensEditor(idReporte){

    var res  = await fetch(API, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({ action:'getTokensReporte', idReporte:idReporte })
    });

    var json = await res.json();
    return json.status === 'ok' ? json.data : [];

}

/* Evento botón */
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('btnEditarReportes')
    .addEventListener('click', abrirEditarReportes);
});

document.getElementById('modalEditarReportes')
    .addEventListener('click', function(e){
        if(e.target === this) cerrarEditarReportes();
    });

document.getElementById('modalRoles')
    .addEventListener('click', function(e){
        if(e.target === this) cerrarModalRoles();
    });

document.getElementById('modalAdmin')
    .addEventListener('click', function(e){
        if(e.target === this) cerrarModalAdmin();
    });
    
</script>
</html>