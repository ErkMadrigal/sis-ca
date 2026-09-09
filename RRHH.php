<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>CAROBRA Consultores Especializados</title>
		<link rel="shortcut icon" href="./img/favicon.ico">
		<script type="text/javascript" src="./js/carobra.js"></script>
		<script type="text/javascript" src="./js/autocompletar.js"></script>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<style>
			/* Anular text-align:center heredado de CAROBRA */
			#contenido,
			#contenido * {
				text-align: left;
			}

			/* ── Barra superior ── */
			#barra-top {
				display: flex;
				align-items: flex-start;
				justify-content: space-between;
				padding: 10px 14px;
				background: #fff;
				border-bottom: 1px solid #ddd;
				gap: 12px;
			}

			/* ── Typeahead ── */
			.nap-ta-wrapper {
				position: relative;
				width: 340px;
				flex-shrink: 0;
			}

			.nap-ta-input {
				width: 100%;
				box-sizing: border-box;
				border: 1px solid #d0d0d0;
				border-radius: 5px;
				padding: 7px 10px;
				font-size: 13px;
				color: #222;
				background: #fff;
				outline: none;
				transition: border .2s;
			}

			.nap-ta-input:focus {
				border-color: #2B73B6;
				box-shadow: 0 0 0 2px rgba(26,103,133,.15);
			}

			.nap-ta-dropdown {
				display: none;
				position: absolute;
				top: calc(100% + 2px);
				left: 0;
				right: 0;
				z-index: 9000;
				background: #fff;
				border: 1px solid #d0d0d0;
				border-top: none;
				border-radius: 0 0 6px 6px;
				max-height: 230px;
				overflow-y: auto;
				box-shadow: 0 6px 16px rgba(0,0,0,.1);
			}

			.nap-ta-dropdown.open { display: block; }

			.nap-ta-item {
				padding: 8px 12px;
				cursor: pointer;
				font-size: 13px;
				border-bottom: 1px solid #f0f0f0;
				display: flex;
				align-items: center;
				justify-content: space-between;
				transition: background .1s;
			}

			.nap-ta-item:last-child { border-bottom: none; }
			.nap-ta-item:hover,
			.nap-ta-item.activo { background: #e8f4f8; }

			.nap-ta-nombre { flex: 1; color: #222; }
			.nap-ta-highlight { color: #2B73B6; font-weight: 700; }

			.nap-ta-clave {
				font-size: 11px;
				color: #fff;
				background: #2B73B6;
				border-radius: 4px;
				padding: 2px 7px;
				margin-left: 8px;
				white-space: nowrap;
			}

			.nap-ta-vacio,
			.nap-ta-cargando {
				padding: 10px 12px;
				font-size: 13px;
				color: #aaa;
				text-align: center;
			}

			/* Badge seleccionado */
			.nap-badge-sel {
				display: inline-flex;
				align-items: center;
				gap: 5px;
				background: #e8f4f8;
				color: #2B73B6;
				font-size: 12px;
				padding: 4px 12px;
				border-radius: 20px;
				border: 1px solid #b0d8e8;
				font-weight: 600;
			}

			.nap-ta-x {
				cursor: pointer;
				font-weight: 700;
				margin-left: 4px;
				opacity: .5;
				font-size: 14px;
			}

			.nap-ta-x:hover { opacity: 1; color: #c0392b; }

			/* ── Botón Nuevo ── */
			#btn-nuevo-emp {
				background: #2B73B6;
				color: #fff;
				border: none;
				padding: 7px 16px;
				border-radius: 4px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				white-space: nowrap;
				transition: background .2s;
			}

			#btn-nuevo-emp:hover { background: #145670; }

			/* ── Botón Permisos (barra superior) ── */
			#btn-permisos-top {
				background: #fff;
				color: #2B73B6;
				border: 1px solid #2B73B6;
				padding: 7px 16px;
				border-radius: 4px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				white-space: nowrap;
				transition: background .2s, color .2s;
			}

			#btn-permisos-top:hover { background: #2B73B6; color: #fff; }

			/* ── Botón Boletinados Principal (barra superior) ── */
			#btn-boletinados-top {
				background: #fff;
				color: #b7791f;
				border: 1px solid #e8c98a;
				padding: 7px 16px;
				border-radius: 4px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				white-space: nowrap;
				transition: background .2s, color .2s;
			}

			#btn-boletinados-top:hover { background: #b7791f; color: #fff; }

			/* ── Panel de formulario ── */
			#panel-empleado {
				display: none;
				padding: 14px 16px;
				text-align: left;
			}

			#panel-empleado label,
			#panel-empleado .nap-label,
			#panel-empleado .toggle-wrap,
			#panel-empleado .nap-row {
				text-align: left;
			}

			/* ── Pestañas ── */
			.nap-tabs {
				display: flex;
				border-bottom: 2px solid #2B73B6;
				margin-bottom: 16px;
				overflow-x: auto;
				overflow-y: hidden;
				flex-wrap: nowrap;
				scrollbar-width: thin;
			}

			.nap-tabs::-webkit-scrollbar { height: 6px; }
			.nap-tabs::-webkit-scrollbar-track { background: #f0f4f6; border-radius: 4px; }
			.nap-tabs::-webkit-scrollbar-thumb { background: #b8d4e0; border-radius: 4px; }
			.nap-tabs::-webkit-scrollbar-thumb:hover { background: #2B73B6; }

			.nap-tab {
				padding: 7px 20px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				background: #e4eef2;
				color: #2B73B6;
				border: 1px solid #ccc;
				border-bottom: none;
				border-radius: 4px 4px 0 0;
				margin-right: 4px;
				transition: background .15s;
				user-select: none;
				flex-shrink: 0;
				white-space: nowrap;
			}

			.nap-tab.activa {
				background: #2B73B6;
				color: #fff;
				border-color: #2B73B6;
			}

			.nap-tab:hover:not(.activa) { background: #cde0e9; }

			.nap-pane { display: none; }
			.nap-pane.activo { display: block; }

			/* ── Campos del formulario ── */
			.nap-label {
				font-size: 12px;
				font-weight: 600;
				color: #444;
				margin-bottom: 3px;
				display: block;
			}

			.nap-input {
				width: 100%;
				padding: 6px 10px;
				border: 1px solid #bbb;
				border-radius: 4px;
				font-size: 13px;
				outline: none;
				background: #fafafa;
				transition: border-color .2s;
			}

			.nap-input:focus {
				border-color: #2B73B6;
				background: #fff;
				box-shadow: 0 0 0 2px rgba(26,103,133,.15);
			}

			.nap-input[readonly] {
				background: #eef3f5;
				color: #555;
				cursor: default;
			}

			.nap-row {
				display: flex;
				gap: 14px;
				margin-bottom: 14px;
				flex-wrap: wrap;
			}

			.nap-col { flex: 1; min-width: 160px; }
			.nap-col-fixed-200 { width: 200px; flex: none; }
			.nap-col-fixed-160 { width: 160px; flex: none; }

			/* Toggle activo/inactivo */
			.toggle-wrap {
				display: flex;
				align-items: center;
				gap: 10px;
				padding-top: 22px;
			}

			.toggle-label { font-size: 13px; color: #444; }

			.toggle-switch {
				position: relative;
				width: 44px;
				height: 24px;
				flex-shrink: 0;
			}

			.toggle-switch input { opacity: 0; width: 0; height: 0; }

			.toggle-slider {
				position: absolute;
				inset: 0;
				background: #ccc;
				border-radius: 24px;
				cursor: pointer;
				transition: background .25s;
			}

			.toggle-slider:before {
				content: '';
				position: absolute;
				width: 18px;
				height: 18px;
				left: 3px;
				bottom: 3px;
				background: #fff;
				border-radius: 50%;
				transition: transform .25s;
			}

			.toggle-switch input:checked + .toggle-slider { background: #2B73B6; }
			.toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

			#lbl-estado { font-size: 13px; font-weight: 600; color: #2B73B6; min-width: 50px; }

			/* Botones guardar */
			.nap-footer {
				display: flex;
				justify-content: flex-end;
				gap: 10px;
				margin-top: 18px;
				padding-top: 12px;
				border-top: 1px solid #e0e0e0;
			}

			.btn-guardar {
				background: #2B73B6;
				color: #fff;
				border: none;
				padding: 7px 22px;
				border-radius: 4px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				transition: background .2s;
			}

			.btn-guardar:hover { background: #145670; }

			.btn-cancelar {
				background: #fff;
				color: #555;
				border: 1px solid #bbb;
				padding: 7px 18px;
				border-radius: 4px;
				font-size: 13px;
				cursor: pointer;
				transition: background .2s;
			}

			.btn-cancelar:hover { background: #f5f5f5; }

			/* Placeholder de pestañas vacías */
			.pane-placeholder {
				text-align: center;
				padding: 40px 20px;
				color: #aaa;
				font-size: 13px;
			}

			.pane-placeholder svg {
				display: block;
				margin: 0 auto 10px;
				opacity: .35;
			}

			.nap-col-fixed-260 { width: 320px; flex: none; }

			.btn-regenClave,
			.btn-regenPass {
				width: 32px;
				height: 32px;
				border: 1px solid #bbb;
				border-radius: 4px;
				background: #eef3f5;
				color: #2B73B6;
				font-size: 16px;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				flex-shrink: 0;
				transition: background .15s, color .15s;
				padding: 0;
			}

			.btn-regenClave:hover,
			.btn-regenPass:hover {
				background: #2B73B6;
				color: #fff;
				border-color: #2B73B6;
			}

			.nap-password { color: #1a5c78; font-weight: 600; }

			.btn-resetpass {
				background: #2B73B6;
				color: #fff;
				border: 1px solid #2B73B6;
				border-radius: 6px;
				padding: 8px 16px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				transition: background .15s;
			}

			.btn-resetpass:hover { background: #2B73B6; }

			/* Selector de claves estilo Google */
			.clave-opciones {
				display: flex;
				flex-direction: column;
				gap: 8px;
				margin-top: 6px;
			}

			.clave-opcion {
				display: flex;
				align-items: center;
				gap: 10px;
				padding: 9px 12px;
				border: 1px solid #d0d0d0;
				border-radius: 6px;
				cursor: pointer;
				font-size: 13px;
				font-family: monospace;
				letter-spacing: .5px;
				transition: border-color .15s, background .15s;
				background: #fafafa;
			}

			.clave-opcion:hover { background: #eef6f9; border-color: #9cc6d8; }

			.clave-opcion.seleccionada {
				border-color: #2B73B6;
				background: #e8f4f8;
				font-weight: 700;
				color: #2B73B6;
			}

			.clave-opcion input[type=radio] { accent-color: #2B73B6; cursor: pointer; }

			.clave-otra-link {
				font-size: 12px;
				color: #2B73B6;
				cursor: pointer;
				text-decoration: underline;
				margin-top: 2px;
				display: inline-block;
			}

			/* Estado vacío (antes de seleccionar) */
			#estado-vacio {
				text-align: center;
				padding: 50px 20px;
				color: #aaa;
				font-size: 13px;
			}

			#estado-vacio svg { display: block; margin: 0 auto 12px; opacity: .3; }

			.nap-seccion-titulo {
				font-size: 11px;
				font-weight: 700;
				letter-spacing: .06em;
				text-transform: uppercase;
				color: #2B73B6;
				margin: 18px 0 10px;
				padding-bottom: 4px;
				border-bottom: 1px solid #e0e8ec;
			}

			.nap-seccion-titulo:first-child { margin-top: 0; }

			#dg-form::-webkit-scrollbar { width: 8px; }
			#dg-form::-webkit-scrollbar-track { background: #f0f4f6; border-radius: 4px; }
			#dg-form::-webkit-scrollbar-thumb { background: #b8d4e0; border-radius: 4px; }
			#dg-form::-webkit-scrollbar-thumb:hover { background: #2B73B6; }

			/* ── Datos bancarios ── */
			.btn-bnc-pdf, .btn-bnc-nueva {
				border: 1px solid #2B73B6;
				background: #fff;
				color: #2B73B6;
				border-radius: 6px;
				padding: 7px 14px;
				font-size: 12px;
				font-weight: 600;
				cursor: pointer;
				transition: background .15s, color .15s;
			}

			.btn-bnc-nueva { background: #2B73B6; color: #fff; }

			.btn-bnc-pdf:hover { background: #eef6f9; }
			.btn-bnc-nueva:hover { background: #145670; border-color: #145670; }

			.bnc-tabla { width: 100%; border-collapse: collapse; font-size: 12.5px; }

			.bnc-tabla thead th {
				background: #f0f6f9;
				color: #2B73B6;
				font-weight: 700;
				font-size: 11px;
				text-transform: uppercase;
				letter-spacing: .04em;
				padding: 9px 10px;
				text-align: left;
				position: sticky;
				top: 0;
				z-index: 1;
			}

			.bnc-tabla tbody td {
				padding: 9px 10px;
				border-bottom: 1px solid #eef2f4;
				color: #333;
			}

			.bnc-tabla tbody tr:hover { background: #f7fbfc; }
			.bnc-tabla tbody tr.bnc-inactiva { opacity: .55; }

			.bnc-vacio { text-align: center; color: #aaa; padding: 20px !important; }

			.bnc-pill-clabe { font-family: monospace; letter-spacing: .5px; }

			.bnc-check-activa {
				width: 16px; height: 16px;
				accent-color: #2B73B6;
				cursor: pointer;
			}

			.bnc-btn-icon {
				background: none;
				border: none;
				cursor: pointer;
				font-size: 14px;
				padding: 2px 5px;
				border-radius: 4px;
				transition: background .15s;
			}

			.bnc-btn-icon:hover { background: #f0e4e4; }

			.spinner-bnc {
				display: inline-block;
				width: 14px; height: 14px;
				border: 2px solid #ccc;
				border-top-color: #2B73B6;
				border-radius: 50%;
				animation: bnc-spin .7s linear infinite;
				vertical-align: -2px;
				margin-right: 4px;
			}

			@keyframes bnc-spin { to { transform: rotate(360deg); } }

			#pdf-drop-zone:hover { border-color: #2B73B6; }

			/* ── Estructura empleados ── */
			.est-resumen-card {
				display: flex;
				align-items: center;
				gap: 14px;
				background: linear-gradient(135deg, #f0f6fa, #e8f2f8);
				border: 1px solid #d4e6ef;
				border-radius: 10px;
				padding: 16px 18px;
				flex-wrap: wrap;
			}

			.est-resumen-item {
				display: flex;
				flex-direction: column;
				gap: 3px;
				min-width: 120px;
			}

			.est-resumen-label {
				font-size: 10px;
				font-weight: 700;
				letter-spacing: .05em;
				text-transform: uppercase;
				color: #6f93a8;
			}

			.est-resumen-valor {
				font-size: 14px;
				font-weight: 700;
				color: #14507a;
			}

			.est-resumen-flecha {
				color: #9cc0d4;
				font-size: 18px;
				font-weight: 700;
			}

			.est-chips-vacio {
				font-size: 12.5px;
				color: #aaa;
				font-style: italic;
			}

			/* ── Tabla de 3 columnas (Nivel / Gerencia / Brockers) ── */
			.est-tabla-3col {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr;
				gap: 16px;
			}

			.est-col-header {
				color: #14507a;
				font-size: 12.5px;
				font-weight: 700;
				margin-bottom: 5px;
			}

			.est-col-body {
				background: #d6e6f2;
				border: 1px solid #aecbe0;
				border-radius: 4px;
				height: 150px;
				overflow-y: auto;
				padding: 4px 0;
			}

			.est-col-body::-webkit-scrollbar { width: 8px; }
			.est-col-body::-webkit-scrollbar-thumb { background: #8fb4cf; border-radius: 4px; }
			.est-col-body::-webkit-scrollbar-track { background: #d6e6f2; }

			.est-row-item {
				padding: 4px 10px;
				font-size: 12.5px;
				color: #1a3f5c;
				cursor: pointer;
				transition: background .12s;
			}

			.est-row-item:hover { background: rgba(255,255,255,.55); }

			.est-row-item.est-row-cc {
				color: #7a5200;
				font-weight: 600;
			}

			.est-row-item.est-row-base {
				background: #2B73B6;
				color: #fff;
				font-weight: 700;
			}

			.est-row-item.est-row-base:hover { background: #1f5d96; }

			.est-row-item .est-row-badge {
				font-size: 10px;
				color: #4a7a9c;
				margin-left: 6px;
			}

			.est-row-item.est-row-base .est-row-badge {
				color: #cfe4f5;
			}

			.est-row-item .est-row-tag-base {
				font-size: 9px;
				background: rgba(255,255,255,.9);
				color: #2B73B6;
				border-radius: 8px;
				padding: 1px 6px;
				margin-left: 6px;
				font-weight: 700;
				letter-spacing: .03em;
			}

			.est-row-vacio {
				padding: 10px 10px;
				font-size: 12px;
				color: #7596ab;
				font-style: italic;
			}

			/* ── Historial de movimientos ── */
			.hist-contadores {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr;
				gap: 14px;
			}

			.hist-contador {
				border-radius: 10px;
				padding: 16px;
				text-align: center;
				border: 1px solid;
			}

			.hist-contador-num {
				font-size: 26px;
				font-weight: 700;
				line-height: 1;
			}

			.hist-contador-label {
				font-size: 11.5px;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: .04em;
				margin-top: 4px;
			}

			.hist-ingreso {
				background: #eafaf1;
				border-color: #b8e8cd;
			}
			.hist-ingreso .hist-contador-num,
			.hist-ingreso .hist-contador-label { color: #1e8e58; }

			.hist-reingreso {
				background: #eef6fb;
				border-color: #bfe0f2;
			}
			.hist-reingreso .hist-contador-num,
			.hist-reingreso .hist-contador-label { color: #2384c5; }

			.hist-baja {
				background: #fdeeee;
				border-color: #f3c4c4;
			}
			.hist-baja .hist-contador-num,
			.hist-baja .hist-contador-label { color: #c0392b; }

			/* ── Contadores usados como filtro (modal Folios de producción) ── */
			.fol-contador-filtro {
				cursor: pointer;
				user-select: none;
				transition: transform .12s, box-shadow .12s, opacity .12s;
			}
			.fol-contador-filtro:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 10px rgba(0,0,0,.08);
			}
			.fol-contadores-filtrando .fol-contador-filtro:not(.fol-activo) {
				opacity: .45;
			}
			.fol-contador-filtro.fol-activo {
				border-width: 2px;
				box-shadow: 0 0 0 3px rgba(43,115,182,.15);
			}

			.hist-timeline {
				position: relative;
				padding-left: 26px;
				max-height: 320px;
				overflow-y: auto;
			}

			.hist-timeline::before {
				content: '';
				position: absolute;
				left: 8px;
				top: 6px;
				bottom: 6px;
				width: 2px;
				background: #e0e8ec;
			}

			.hist-item {
				position: relative;
				padding: 0 0 18px 14px;
			}

			.hist-item:last-child { padding-bottom: 0; }

			.hist-item::before {
				content: '';
				position: absolute;
				left: -22px;
				top: 3px;
				width: 12px;
				height: 12px;
				border-radius: 50%;
				border: 2px solid #fff;
				box-shadow: 0 0 0 1px #ccc;
			}

			.hist-item.tipo-ingreso::before   { background: #1e8e58; box-shadow: 0 0 0 1px #1e8e58; }
			.hist-item.tipo-reingreso::before { background: #2384c5; box-shadow: 0 0 0 1px #2384c5; }
			.hist-item.tipo-baja::before      { background: #c0392b; box-shadow: 0 0 0 1px #c0392b; }

			.hist-item-tag {
				display: inline-block;
				font-size: 10.5px;
				font-weight: 700;
				text-transform: uppercase;
				letter-spacing: .03em;
				padding: 2px 9px;
				border-radius: 10px;
				margin-bottom: 3px;
			}

			.hist-item.tipo-ingreso   .hist-item-tag { background: #eafaf1; color: #1e8e58; }
			.hist-item.tipo-reingreso .hist-item-tag { background: #eef6fb; color: #2384c5; }
			.hist-item.tipo-baja      .hist-item-tag { background: #fdeeee; color: #c0392b; }

			.hist-item-fecha {
				font-size: 13.5px;
				font-weight: 700;
				color: #1a1a2e;
			}

			.hist-item-meta {
				font-size: 11px;
				color: #aaa;
				margin-top: 1px;
			}

			.hist-vacio {
				text-align: center;
				padding: 30px 10px;
				color: #aaa;
				font-size: 13px;
			}

			/* ── Movimientos de estructura (Gerencia / Brocker / SC) ── */
			.hist-estructura-1col {
				width: 100%;
			}

			.hist-estructura-header {
				color: #14507a;
				font-size: 12.5px;
				font-weight: 700;
				margin-bottom: 5px;
			}

			.hist-estructura-body {
				width: 100%;
				box-sizing: border-box;
				background: #fff;
				border: 1px solid #e0e8ec;
				border-radius: 6px;
				padding: 6px 6px 24px 6px;
				height: 280px;
				overflow-y: scroll;
			}

			.hist-estructura-body::-webkit-scrollbar { width: 8px; }
			.hist-estructura-body::-webkit-scrollbar-thumb { background: #cfe0e8; border-radius: 4px; }
			.hist-estructura-body::-webkit-scrollbar-track { background: #f7fafb; }

			.hist-estructura-bloque {
				padding: 8px 10px;
				border-radius: 6px;
				background: #f7fafb;
				border-left: 3px solid #2384c5;
				margin-bottom: 6px;
				font-size: 12px;
			}

			.hist-estructura-bloque:last-child { margin-bottom: 0; }

			.hist-estructura-nombre {
				font-weight: 700;
				color: #1a3f5c;
				margin-bottom: 2px;
			}

			.hist-estructura-rango {
				color: #777;
				font-size: 11px;
			}

			.hist-estructura-subdetalle {
				margin-top: 5px;
				padding-top: 5px;
				border-top: 1px dashed #e0e8ec;
				font-size: 10.5px;
				color: #5a7a8c;
				line-height: 1.5;
			}

			.hist-estructura-subdetalle-label {
				font-weight: 700;
				color: #14507a;
			}

			.hist-estructura-vacio {
				text-align: center;
				padding: 20px 8px;
				color: #aaa;
				font-size: 12px;
				font-style: italic;
			}

			/* ── Contactos de emergencia ── */
			.ce-lista {
				display: flex;
				flex-direction: column;
				gap: 10px;
			}

			.ce-card {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 12px;
				padding: 12px 14px;
				background: #f7fafb;
				border: 1px solid #e0e8ec;
				border-left: 4px solid #2B73B6;
				border-radius: 8px;
			}

			.ce-card-info { flex: 1; min-width: 0; }

			.ce-card-nombre {
				font-weight: 700;
				font-size: 13.5px;
				color: #1a1a2e;
			}

			.ce-card-meta {
				font-size: 12px;
				color: #777;
				margin-top: 2px;
			}

			.ce-card-parentesco {
				display: inline-block;
				font-size: 10.5px;
				font-weight: 700;
				text-transform: uppercase;
				letter-spacing: .03em;
				background: #e8f4f8;
				color: #2B73B6;
				border-radius: 10px;
				padding: 2px 9px;
				margin-left: 8px;
			}

			.ce-card-acciones {
				display: flex;
				gap: 4px;
				flex-shrink: 0;
			}

			.ce-vacio {
				text-align: center;
				padding: 30px 10px;
				color: #aaa;
				font-size: 13px;
			}

			/* ── Expediente Digital ── */
			.exd-lista {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
				gap: 14px;
			}

			.exd-card {
				display: flex;
				flex-direction: column;
				background: #f7fafb;
				border: 1px solid #e0e8ec;
				border-radius: 10px;
				overflow: hidden;
				transition: box-shadow .15s;
			}

			.exd-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }

			.exd-card.exd-card-1 { border-left: 4px solid #1e8e58; }
			.exd-card.exd-card-2 { border-left: 4px solid #c0392b; opacity: .75; }
			.exd-card.exd-card-0 { border-left: 4px solid #e0a800; }

			.exd-thumb {
				height: 120px;
				background: #e8eef2;
				display: flex;
				align-items: center;
				justify-content: center;
				cursor: pointer;
				overflow: hidden;
			}

			.exd-thumb img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}

			.exd-thumb-pdf {
				font-size: 13px;
				font-weight: 700;
				color: #b0392b;
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 6px;
			}

			.exd-info { padding: 10px 12px; flex: 1; }

			.exd-tipo {
				font-weight: 700;
				font-size: 12.5px;
				color: #1a1a2e;
			}

			.exd-nombre {
				font-size: 11px;
				color: #888;
				margin-top: 2px;
				word-break: break-all;
			}

			.exd-fecha {
				font-size: 10.5px;
				color: #aaa;
				margin-top: 4px;
			}

			.exd-badge {
				display: inline-block;
				font-size: 10px;
				font-weight: 700;
				text-transform: uppercase;
				letter-spacing: .03em;
				border-radius: 10px;
				padding: 2px 9px;
				margin-top: 6px;
			}

			.exd-badge-0 { background: #fff8e8; color: #b37f00; }
			.exd-badge-1 { background: #eafaf1; color: #1e8e58; }
			.exd-badge-2 { background: #fdeeee; color: #c0392b; }

			.exd-comentario {
				font-size: 11px;
				color: #c0392b;
				margin-top: 6px;
				font-style: italic;
			}

			.exd-acciones {
				display: flex;
				gap: 6px;
				padding: 0 12px 12px;
			}

			.exd-acciones button {
				flex: 1;
				font-size: 11px;
				font-weight: 600;
				border: none;
				border-radius: 6px;
				padding: 6px 4px;
				cursor: pointer;
			}

			.exd-btn-ver       { background: #e8eef2; color: #333; }
			.exd-btn-aprobar   { background: #1e8e58; color: #fff; }
			.exd-btn-rechazar  { background: #c0392b; color: #fff; }

			/* ── Permisos de pestañas ── */
			.perm-check-item {
				display: flex;
				align-items: center;
				gap: 7px;
				background: #f7fafb;
				border: 1px solid #e0e8ec;
				border-radius: 6px;
				padding: 7px 12px;
				font-size: 12.5px;
				color: #333;
				cursor: pointer;
			}

			.perm-check-item input {
				accent-color: #2B73B6;
				cursor: pointer;
			}

			.perm-lote-usuarios {
				display: flex;
				flex-wrap: wrap;
				gap: 6px;
				margin-top: 6px;
			}

			.perm-lote-usuario-chip {
				font-size: 11px;
				background: #e8f4f8;
				color: #2B73B6;
				border: 1px solid #b0d8e8;
				border-radius: 12px;
				padding: 2px 10px;
			}
			.pp-layout {
				display: flex;
				gap: 16px;
			}

			.pp-col-modulos { width: 260px; flex: none; }
			.pp-col-paginas { flex: 1; min-width: 0; }

			.pp-lista-modulos {
				background: #f7fafb;
				border: 1px solid #e0e8ec;
				border-radius: 6px;
				height: 320px;
				overflow-y: auto;
				padding: 4px 0;
			}

			.pp-lista-modulos::-webkit-scrollbar { width: 8px; }
			.pp-lista-modulos::-webkit-scrollbar-thumb { background: #cfe0e8; border-radius: 4px; }
			.pp-lista-modulos::-webkit-scrollbar-track { background: #f7fafb; }

			.pp-modulo-item {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 8px;
				padding: 8px 12px;
				font-size: 12.5px;
				color: #333;
				cursor: pointer;
				border-bottom: 1px solid #eef2f4;
				transition: background .12s;
			}

			.pp-modulo-item:last-child { border-bottom: none; }
			.pp-modulo-item:hover { background: #eef6f9; }

			.pp-modulo-item.pp-modulo-activo {
				background: #2B73B6;
				color: #fff;
				font-weight: 700;
			}

			.pp-modulo-badge {
				font-size: 10.5px;
				font-weight: 700;
				background: #e8f4f8;
				color: #2B73B6;
				border-radius: 10px;
				padding: 1px 8px;
				flex-shrink: 0;
			}

			.pp-modulo-item.pp-modulo-activo .pp-modulo-badge { background: rgba(255,255,255,.9); }

			.pp-modulo-vacio {
				padding: 14px 12px;
				font-size: 12px;
				color: #aaa;
				font-style: italic;
			}

			.pp-paginas-header {
				display: flex;
				align-items: center;
				justify-content: space-between;
				font-size: 12.5px;
				font-weight: 700;
				color: #14507a;
				margin-bottom: 8px;
				padding-bottom: 6px;
				border-bottom: 1px solid #e0e8ec;
			}

			.pp-modulo-toggle-all {
				font-size: 11px;
				color: #2B73B6;
				cursor: pointer;
				text-decoration: underline;
				font-weight: 600;
			}

			.pp-paginas-lista {
				background: #fff;
				border: 1px solid #e0e8ec;
				border-radius: 6px;
				height: 320px;
				overflow-y: auto;
				padding: 10px 12px;
				display: flex;
				flex-direction: column;
				gap: 6px;
			}

			.pp-paginas-lista::-webkit-scrollbar { width: 8px; }
			.pp-paginas-lista::-webkit-scrollbar-thumb { background: #cfe0e8; border-radius: 4px; }
			.pp-paginas-lista::-webkit-scrollbar-track { background: #f7fafb; }

			.pp-paginas-vacio {
				text-align: center;
				padding: 30px 10px;
				color: #aaa;
				font-size: 12.5px;
				font-style: italic;
			}
			.pp-footer {
				position: sticky;
				bottom: -1px;
				background: #fff;
				z-index: 2;
				padding-top: 14px;
				margin-top: 14px;
			}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader" style="
			display:none;
			position: fixed;
			top:0; left:0;
			width:100%; height:100%;
			background: rgba(0,0,0,0.5);
			z-index:9999;
			justify-content:center;
			align-items:center;
		">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Cargando...</div>
			</div>
		</div>

	<?php

		function formatoFecha($fecha){
			if (empty($fecha) || $fecha == '0000-00-00') return '';
			$timestamp = strtotime($fecha);
			if (!$timestamp) return '';
			return date('d/m/Y', $timestamp);
		}

		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));

		if($periodo > 600){
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"] = "p";
		}

		if ($_SESSION["autentifica"] != "p"){
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {

			$divpie       = new div("pie");
			$divopcion    = new div("opcion");
			$divcabecera  = new div("cabecera");
			$divcontenido = new div("contenido");
			$divcontenedor= new div("contenedor");
			$imgpie       = new img("imgpie","./img/pie.png");
			$divusuario   = new div("usuario",'',"absolute","97%");
			$imgcabecera  = new img("imgcabecera","./img/cabecera.png");

			$divcabecera->crear();
				$imgcabecera->crear();
				$imgcabecera->css("width","100%");
				titulo("Categorías NAP");
				$pag="PRODUCCION";
				menu();
			$divcabecera->close();

			$divpie->crear();
				$imgpie->crear();
				$imgpie->css("width","100%");
				$divusuario->crear();
					echo "<br><br>";
					$divusuario->css("top","10px");
					$divusuario->css("text-align","left");
	?>
				<div id="vinculos" style="margin-top:10px; margin-left:25px;">
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
					$divopcion->css("background","#FFFFFF");
				$divopcion->close();
				$divcontenido->crear();
				$divcontenido->css("overflow-y","auto");

				$conecta = new conector();
				$nivel   = nivel_usuario(utf8_decode($_SESSION["nombre"]));
				$query   = "SELECT clave FROM empl_usuarios WHERE nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
				$bd      = $conecta->consulta($query);
				$tb      = mysqli_fetch_array($bd);
				$clave   = $tb['clave'];

				$usuario = $_SESSION["nombre"];

				/* ── Permisos de pestañas del usuario en sesión ──
				   Se resuelve si el usuario actual pertenece al nivel
				   Super Usuario (id_nivel = 12), y qué pestañas tiene
				   asignadas en carobra_2014.permisos_pestanas. Estos
				   datos se exponen al JS para ocultar en el cliente las
				   pestañas no permitidas. El servidor (RRHH_api.php)
				   vuelve a validar todo lo relacionado a la gestión de
				   permisos, así que ocultar aquí es solo para UX. */
				$es_superusuario      = false;
				$pestanas_permitidas  = [];

				if (!empty($clave)) {
					$claveSesionEsc = mysqli_real_escape_string($conecta->conexion, $clave);

					$rsNivelSesion = $conecta->consulta(
						"SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveSesionEsc}'"
					);
					if ($rsNivelSesion) {
						while ($rowNivelSesion = mysqli_fetch_assoc($rsNivelSesion)) {
							if (intval($rowNivelSesion['id_nivel']) === 12) {
								$es_superusuario = true;
								break;
							}
						}
					}

					$rsPermSesion = $conecta->consulta(
						"SELECT pestana FROM carobra_2014.permisos_pestanas WHERE clave='{$claveSesionEsc}'"
					);
					if ($rsPermSesion) {
						while ($rowPermSesion = mysqli_fetch_assoc($rsPermSesion)) {
							$pestanas_permitidas[] = $rowPermSesion['pestana'];
						}
					}
				}

				/* El botón "Permisos" de la barra superior (modal global que
				   administra QUIÉN ve QUÉ pestañas) es un caso aparte: solo
				   lo pueden ver estas claves puntuales, sin importar nivel ni
				   pestañas asignadas. El backend (RRHH_api.php) vuelve a
				   validar esto mismo en cada acción del modal — ocultarlo
				   aquí es solo para UX. */
				$claves_gestion_permisos = ['FEROJAS', 'COYHERNAND', 'ADMINISTRA'];
				$puede_gestionar_permisos = !empty($clave) && in_array(strtoupper($clave), $claves_gestion_permisos, true);

				echo '<script>window.NAP_ES_SUPERUSUARIO = ' . ($es_superusuario ? 'true' : 'false') . ';'
				   . ' window.NAP_PESTANAS_PERMITIDAS = ' . json_encode($pestanas_permitidas) . ';'
				   . ' window.NAP_PUEDE_PERMISOS_TOP = ' . ($puede_gestionar_permisos ? 'true' : 'false') . ';</script>';
	?>

	<!-- ══════════════════════════════════════════════
	     CONTENIDO PRINCIPAL
	══════════════════════════════════════════════ -->
	<div class="container-fluid" style="padding:0; text-align:left;">

		<!-- Barra superior -->
		<div id="barra-top">
			<!-- Búsqueda typeahead -->
			<div class="nap-ta-wrapper">
				<input type="text"
					   id="inp-busqueda"
					   class="nap-ta-input"
					   placeholder="Buscar empleado..."
					   autocomplete="off">
				<div class="nap-ta-dropdown" id="dropdown-resultados"></div>
			</div>

			<!-- Botón nuevo -->
			<button id="btn-nuevo-emp" onclick="nuevoEmpleado()">+ Nuevo empleado</button>

			<!-- Boletinados Principal: acceso directo sin necesidad de buscar/crear un empleado -->
			<button id="btn-boletinados-top" onclick="abrirBoletinados()" style="display:none;">📋 Boletinados Principal</button>

			<!-- Botón Permisos (solo Super Usuario) -->
			<button id="btn-permisos-top" onclick="abrirModalPermisos()" style="display:none;">Permisos</button>
		</div>

		<!-- Nombre del empleado seleccionado, centrado en todo el ancho -->
		<div id="emp-badge-wrap" style="display:none; padding:10px 14px 0; text-align:center;">
			<span id="emp-badge-txt" style="font-weight:700; font-size:14px; color:#2B73B6;"></span>
		</div>

		<!-- Panel de edición / alta -->
		<div id="panel-empleado">

			<!-- Pestañas -->
			<div class="nap-tabs">
				<div class="nap-tab activa" data-pane="pane-datos">Usuario</div>
				<div class="nap-tab" data-pane="pane-tab2">Datos generales</div>
				<div class="nap-tab" data-pane="pane-tab3">Datos bancarios</div>
				<div class="nap-tab" data-pane="pane-tab4">Estruc empleados</div>
				<div class="nap-tab" data-pane="pane-tab5">Finanzas</div>
				<div class="nap-tab" data-pane="pane-tab6">Historial</div>
				<div class="nap-tab" data-pane="pane-tab7">Contac. Emergencia</div>
				<div class="nap-tab" data-pane="pane-tab8">Información</div>
				<div class="nap-tab" data-pane="pane-tab10">Expediente Digital</div>
				<div class="nap-tab" data-pane="pane-boletinados">Boletinados Principal</div>
				<div class="nap-tab" data-pane="pane-tab9">Permisos</div>
			</div>

			<!-- ── Pestaña 1: Datos generales ── -->
			<div class="nap-pane activo" id="pane-datos">

				<!-- ══ MODO ALTA ══ -->
				<div id="sec-nuevo">
					<div style="flex:none;padding-bottom:3px;">
						<div class="toggle-wrap" style="padding-top:0;float: inline-end;">
							<!-- <span class="toggle-label">Estado:</span> -->
							<label class="toggle-switch">
								<input type="checkbox" id="emp-activo" checked onchange="actualizarEstado()">
								<span class="toggle-slider"></span>
							</label>
							<span id="lbl-estado">Activo</span>
						</div>
					</div>
					<!-- Nombre completo: ya no se captura directo, se compone
					     a partir de Nombre(s)/Ap. paterno/Ap. materno de abajo.
					     Se deja oculto (no eliminado) porque clave/guardado
					     siguen usando este valor internamente. -->
					<div class="nap-row" style="display:none;align-items:flex-end;margin-top: 2rem;">
						<div class="nap-col">
							<label class="nap-label">Nombre completo</label>
							<input type="text" id="nvo-nombre" class="nap-input"
								   placeholder="Ej: ANGELICA ALEJANDRA GARCIA TORRES"
								   oninput="onNombreInput()">
						</div>

					</div>
					<div class="nap-row" style="margin-top: 2rem;">
						<div class="nap-col">
							<label class="nap-label">Nombre(s)</label>
							<input type="text" id="nvo-nombres" class="nap-input" placeholder="Ej: ANGELICA ALEJANDRA" oninput="onNombreSeparadoAltaInput()">
						</div>
						<div class="nap-col">
							<label class="nap-label">Apellido paterno</label>
							<input type="text" id="nvo-apellido-paterno" class="nap-input" placeholder="Ej: GARCIA" oninput="onNombreSeparadoAltaInput()">
						</div>
						<div class="nap-col">
							<label class="nap-label">Apellido materno</label>
							<input type="text" id="nvo-apellido-materno" class="nap-input" placeholder="Ej: TORRES" oninput="onNombreSeparadoAltaInput()">
						</div>
					</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Correo electrónico</label>
							<input type="email" id="nvo-email" class="nap-input" placeholder="ejemplo@correo.com">
						</div>
					</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Clave <span style="color:#aaa;font-weight:400;font-size:11px;">(elige una opción)</span></label>
							<div id="clave-opciones-wrap" class="clave-opciones">
								<span style="font-size:12px;color:#aaa;">Escribe el nombre para ver opciones de clave...</span>
							</div>
							<input type="hidden" id="nvo-clave" value="">
						</div>
					</div>
				</div>

				<!-- ══ MODO EDICIÓN ══ -->
				<div id="sec-editar" style="display:none;">
					<div class="nap-row" style="justify-content:flex-end;">
						<div class="toggle-wrap" style="padding-top:0;">
							<span class="toggle-label">Estado:</span>
							<label class="toggle-switch">
								<input type="checkbox" id="emp-activo-ed" checked onchange="actualizarEstado()">
								<span class="toggle-slider"></span>
							</label>
							<span id="lbl-estado">Activo</span>
						</div>
					</div>
					<!-- Nombre (completo, solo lectura): oculto, no eliminado;
					     la edición del nombre real vive en "Nombre separado" abajo. -->
					<div class="nap-row" style="display:none;">
						<div class="nap-col" style="flex:1 1 100%;">
							<label class="nap-label">Nombre</label>
							<input type="text" id="emp-nombre-ro" class="nap-input" readonly
								   style="background:#eef3f5;color:#555;">
						</div>
					</div>

					<div class="nap-row" style="margin-top:4px;">
						<div class="nap-col">
							<label class="nap-label">Nombre(s)</label>
							<input type="text" id="emp-nombres" class="nap-input" placeholder="Ej: ANGELICA ALEJANDRA">
						</div>
						<div class="nap-col">
							<label class="nap-label">Apellido paterno</label>
							<input type="text" id="emp-apellido-paterno" class="nap-input" placeholder="Ej: GARCIA">
						</div>
						<div class="nap-col">
							<label class="nap-label">Apellido materno</label>
							<input type="text" id="emp-apellido-materno" class="nap-input" placeholder="Ej: TORRES">
						</div>
					</div>

					<div class="nap-row" style="align-items:flex-end;margin-top:14px;">
						<div class="nap-col-fixed-160">
							<label class="nap-label">Clave</label>
							<input type="text" id="emp-clave-ro" class="nap-input" readonly
								   style="background:#eef3f5;color:#555;">
						</div>
						<div style="flex:1;"></div>
						<div style="flex:none;">
							<button type="button" id="btn-reset-pass" class="btn-resetpass" onclick="iniciarResetPassword()">
								&#8635; Resetear contraseña
							</button>
						</div>
					</div>
				</div>

				<!-- Campos ocultos globales -->
				<input type="hidden" id="emp-id"     value="">
				<input type="hidden" id="emp-nombre" value="">
				<input type="hidden" id="emp-email"  value="">
				<input type="hidden" id="emp-clave"  value="">
				<input type="hidden" id="emp-activo" value="1">

				<!-- Footer acciones (solo aplica a esta pestaña) -->
				<div class="nap-footer">
					<button class="btn-cancelar" onclick="cerrarPanel()">Cancelar</button>
					<button class="btn-guardar" onclick="guardarEmpleado()">Guardar</button>
				</div>

			</div>
			<!-- /pane-datos -->

			<!-- ── Pestaña 2: Datos generales (empleados) ── -->
			<div class="nap-pane" id="pane-tab2">

				<div id="dg-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder capturar sus datos generales.
					</div>
				</div>

				<div id="dg-form" style="display:none;">

					<!-- Staff / Espartano -->
					<div class="nap-row">
						<div class="nap-col-fixed-200">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Staff:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="dg-staff" onchange="toggleJefeInmediato()">
									<span class="toggle-slider"></span>
								</label>
								<span id="lbl-staff">No</span>
							</div>
						</div>
						<div class="nap-col-fixed-200">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Espartano:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="dg-espartano" onchange="toggleEspartano()">
									<span class="toggle-slider"></span>
								</label>
								<span id="lbl-espartano">No</span>
							</div>
						</div>
					</div>

					<!-- Jefe inmediato (solo visible si Staff = sí) -->
					<div class="nap-row" id="dg-row-jefe" style="display:none;">
						<div class="nap-col">
							<label class="nap-label">Jefe inmediato</label>
							<div class="nap-ta-wrapper" style="width:100%;">
								<input type="text" id="dg-jefe-search" class="nap-input nap-ta-input"
									   placeholder="Buscar jefe inmediato..." autocomplete="off">
								<div class="nap-ta-dropdown" id="dg-jefe-dropdown"></div>
							</div>
							<input type="hidden" id="dg-jefe-clave" value="">
						</div>
					</div>

					<!-- Fechas -->
					<div class="nap-seccion-titulo">Fechas</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Fec. Ingreso</label>
							<input type="date" id="dg-fec-ingreso" class="nap-input">
						</div>
						<div class="nap-col">
							<label class="nap-label">Fec. Reingreso</label>
							<input type="date" id="dg-fec-reingreso" class="nap-input">
						</div>
						<div class="nap-col">
							<label class="nap-label">Fec. Baja</label>
							<input type="date" id="dg-fec-baja" class="nap-input">
						</div>
					</div>

					<!-- Domicilio -->
					<div class="nap-seccion-titulo">Domicilio</div>
					<div class="nap-row">
						<div class="nap-col-fixed-160">
							<label class="nap-label">C.P.</label>
							<input type="text" id="dg-cp" class="nap-input" maxlength="5" placeholder="00000"
								   oninput="onCpInput(this)">
							<span id="dg-cp-hint" style="font-size:11px;"></span>
						</div>
						<div class="nap-col">
							<label class="nap-label">Estado</label>
							<input type="text" id="dg-estado" class="nap-input" readonly style="background:#eef3f5;color:#555;">
						</div>
						<div class="nap-col">
							<label class="nap-label">Municipio</label>
							<input type="text" id="dg-municipio" class="nap-input" readonly style="background:#eef3f5;color:#555;">
						</div>
					</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Colonia</label>
							<select id="dg-colonia" class="nap-input">
								<option value="">Escribe primero el C.P.</option>
							</select>
						</div>
					</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Calle y número</label>
							<input type="text" id="dg-calle" class="nap-input" placeholder="Calle, número exterior e interior">
						</div>
					</div>

					<!-- Contacto -->
					<div class="nap-seccion-titulo">Contacto</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Teléfono</label>
							<input type="text" id="dg-telefono" class="nap-input" maxlength="15" placeholder="Teléfono fijo">
						</div>
						<div class="nap-col">
							<label class="nap-label">Celular</label>
							<input type="text" id="dg-celular" class="nap-input" maxlength="15" placeholder="Celular">
						</div>
					</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">Email personal</label>
							<input type="email" id="dg-email-personal" class="nap-input" placeholder="correo@personal.com">
						</div>
						<div class="nap-col">
							<label class="nap-label">Email institucional</label>
							<input type="email" id="dg-email-inst" class="nap-input" placeholder="correo@carobra.com.mx">
						</div>
					</div>

					<!-- Documentos -->
					<div class="nap-seccion-titulo">Documentos</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">NSS</label>
							<input type="text" id="dg-nss" class="nap-input" maxlength="11" placeholder="00000000000">
						</div>
						<div class="nap-col">
							<label class="nap-label">RFC</label>
							<input type="text" id="dg-rfc" class="nap-input" maxlength="15" placeholder="RFC" style="text-transform:uppercase;">
						</div>
						<div class="nap-col">
							<label class="nap-label">CURP</label>
							<input type="text" id="dg-curp" class="nap-input" maxlength="18" placeholder="CURP" style="text-transform:uppercase;">
						</div>
					</div>

					<!-- NAP -->
					<div class="nap-seccion-titulo">NAP</div>
					<div class="nap-row">
						<div class="nap-col">
							<label class="nap-label">NAP</label>
							<input type="text" id="dg-nap" class="nap-input" maxlength="10" placeholder="NAP" style="text-transform:uppercase;">
						</div>
						<div class="nap-col">
							<label class="nap-label">Fec. Alta NAP</label>
							<input type="date" id="dg-fec-alta-nap" class="nap-input">
						</div>
						<div class="nap-col">
							<label class="nap-label">Reclutador</label>
							<div class="nap-ta-wrapper" style="width:100%;">
								<input type="text" id="dg-recluta-search" class="nap-input nap-ta-input"
									   placeholder="Buscar reclutador..." autocomplete="off">
								<div class="nap-ta-dropdown" id="dg-recluta-dropdown"></div>
							</div>
							<input type="hidden" id="dg-recluta-clave" value="">
						</div>
					</div>

					<input type="hidden" id="dg-clave" value="">

					<!-- Footer propio de esta pestaña -->
					<div class="nap-footer">
						<button class="btn-guardar" onclick="guardarDatosGenerales()">Guardar datos generales</button>
					</div>

				</div>
				<!-- /dg-form -->

			</div>

			<!-- ── Pestaña 3: placeholder ── -->
			<div class="nap-pane" id="pane-tab3">

				<div id="bnc-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder capturar sus cuentas bancarias.
					</div>
				</div>

				<div id="bnc-contenido" style="display:none;">


					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
						<div class="nap-seccion-titulo" style="margin:0;">Cuentas bancarias</div>
						<div style="display:flex; gap:8px;">
							<button type="button" class="btn-bnc-pdf" onclick="abrirModalPdf()">&#128196; Cargar estado de cuenta</button>
							<button type="button" class="btn-bnc-nueva" onclick="abrirFormCuenta()">+ Nueva cuenta</button>
						</div>
					</div>

					<!-- Form alta/edición de cuenta -->
					<div id="bnc-form-wrap" style="display:none; margin-bottom:16px; padding:14px; background:#f7fafb; border:1px solid #e0e8ec; border-radius:8px;">
						<div class="nap-row">
							<div class="nap-col">
								<label class="nap-label">Nombre</label>
								<input type="text" id="bnc-nombre" class="nap-input" maxlength="25" placeholder="Nombre">
							</div>
							<div class="nap-col">
								<label class="nap-label">Apellido paterno</label>
								<input type="text" id="bnc-ap" class="nap-input" maxlength="20" placeholder="Apellido paterno">
							</div>
							<div class="nap-col">
								<label class="nap-label">Apellido materno</label>
								<input type="text" id="bnc-am" class="nap-input" maxlength="20" placeholder="Apellido materno">
							</div>
						</div>
						<div class="nap-row">
							<div class="nap-col">
								<label class="nap-label">CLABE interbancaria <span style="color:#aaa;font-weight:400;font-size:11px;">(18 dígitos)</span></label>
								<input type="text" id="bnc-clabe" class="nap-input" maxlength="18" placeholder="000000000000000000"
									   style="letter-spacing:1px;font-family:monospace;" oninput="onClabeManualInput(this)">
								<span id="bnc-clabe-hint" style="font-size:11px;"></span>
							</div>
							<div class="nap-col-fixed-200">
								<label class="nap-label">Banco <span style="color:#aaa;font-weight:400;font-size:11px;" id="bnc-banco-hint">(detectado por CLABE)</span></label>
								<select id="bnc-id-banco" class="nap-input">
									<option value="">— Selecciona —</option>
								</select>
							</div>
						</div>
						<div class="nap-row" style="margin-bottom:6px;">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Cuenta activa:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="bnc-activo" checked>
									<span class="toggle-slider"></span>
								</label>
							</div>
							<span style="font-size:11px;color:#aaa;margin-left:8px;">Solo puede haber una cuenta activa a la vez; al activar esta, las demás se inactivan automáticamente.</span>
						</div>
						<input type="hidden" id="bnc-editando-id" value="">
						<div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
							<button type="button" class="btn-cancelar" onclick="cerrarFormCuenta()">Cancelar</button>
							<button type="button" class="btn-guardar" onclick="guardarCuentaBancaria()">Guardar cuenta</button>
						</div>
					</div>
					<!-- /bnc-form-wrap -->

					<!-- Tabla de cuentas -->
					<div id="bnc-tabla-wrap" style="border:1px solid #e0e8ec; border-radius:6px;">
						<table class="bnc-tabla">
							<thead>
								<tr>
									<th>Nombre cuenta</th>
									<th>Banco</th>
									<th>CLABE</th>
									<th style="text-align:center;">Activa</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="bnc-tbody">
								<tr><td colspan="5" class="bnc-vacio">Sin cuentas registradas</td></tr>
							</tbody>
						</table>
					</div>

				</div>
				<!-- /bnc-contenido -->

			</div>

			<!-- ── Pestaña 4: Estructura de empleados ── -->
			<div class="nap-pane" id="pane-tab4">

				<div id="est-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder ver su estructura.
					</div>
				</div>

				<div id="est-contenido" style="display:none;">

					<!-- Tarjeta resumen: Gerencia → Brocker → Socio -->
					<div class="est-resumen-card">
						<div class="est-resumen-item">
							<span class="est-resumen-label">Gerencia</span>
							<span class="est-resumen-valor" id="est-categoria">—</span>
						</div>
						<span class="est-resumen-flecha">→</span>
						<div class="est-resumen-item">
							<span class="est-resumen-label">Brocker</span>
							<span class="est-resumen-valor" id="est-brocker">—</span>
						</div>
						<span class="est-resumen-flecha">→</span>
						<div class="est-resumen-item">
							<span class="est-resumen-label">Socio (SC)</span>
							<span class="est-resumen-valor" id="est-socio">—</span>
						</div>
					</div>

					<!-- Tabla de 3 columnas: Nivel / Gerencia / Brockers -->
					<div class="nap-seccion-titulo" style="margin-top:20px;">Estructura asignada</div>
					<div class="est-tabla-3col">
						<div>
							<div class="est-col-header">Nivel</div>
							<div class="nap-ta-wrapper" style="width:100%;margin-bottom:6px;">
								<input type="text" id="est-add-nivel-search" class="nap-input nap-ta-input"
									   placeholder="Asignar nivel..." autocomplete="off">
								<div class="nap-ta-dropdown" id="est-add-nivel-dropdown"></div>
							</div>
							<div id="est-lista-nivel" class="est-col-body"></div>
						</div>
						<div>
							<div class="est-col-header">Gerencia</div>
							<div class="nap-ta-wrapper" style="width:100%;margin-bottom:6px;">
								<input type="text" id="est-add-gerencia-search" class="nap-input nap-ta-input"
									   placeholder="Asignar gerencia..." autocomplete="off">
								<div class="nap-ta-dropdown" id="est-add-gerencia-dropdown"></div>
							</div>
							<div id="est-lista-gerencia" class="est-col-body"></div>
						</div>
						<div>
							<div class="est-col-header">Brockers</div>
							<div class="nap-ta-wrapper" style="width:100%;margin-bottom:6px;">
								<input type="text" id="est-add-brocker-search" class="nap-input nap-ta-input"
									   placeholder="Asignar brocker..." autocomplete="off">
								<div class="nap-ta-dropdown" id="est-add-brocker-dropdown"></div>
							</div>
							<div id="est-lista-brockers" class="est-col-body"></div>
						</div>
					</div>

				</div>
				<!-- /est-contenido -->

			</div>

			<!-- ── Pestaña 5: Finanzas ── -->
			<div class="nap-pane" id="pane-tab5">

				<div id="fin-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder capturar su información financiera.
					</div>
				</div>

				<div id="fin-contenido" style="display:none;">

					<div class="nap-row" style="align-items:flex-end;">
						<div style="flex:none;padding-bottom:3px;">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Factura:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="fin-factura" onchange="toggleFinFactura()">
									<span class="toggle-slider"></span>
								</label>
								<span id="lbl-fin-factura">No</span>
							</div>
						</div>
						<div class="nap-col" id="fin-row-fec-factura" style="display:none;">
							<label class="nap-label">A partir de</label>
							<input type="date" id="fin-fec-factura" class="nap-input">
						</div>
						<div class="nap-col">
							<label class="nap-label">Régimen</label>
							<select id="fin-regimen" class="nap-input">
								<option value="">— Selecciona —</option>
								<option value="AE">Actividad Empresarial</option>
								<option value="IF">Incorporación Fiscal</option>
								<option value="PM">Persona Moral</option>
								<option value="R">RESICO</option>
							</select>
						</div>
						<div style="flex:none;padding-bottom:3px;">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Dto. NAP-Factura:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="fin-nap-factura">
									<span class="toggle-slider"></span>
								</label>
							</div>
						</div>
					</div>

					<div class="nap-row" style="align-items:flex-end; margin-top:14px;">
						<div style="flex:none;padding-bottom:3px;">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Dto. NAP-Gerencia:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="fin-nap-gerencia">
									<span class="toggle-slider"></span>
								</label>
							</div>
						</div>
						<div style="flex:none;padding-bottom:3px;">
							<div class="toggle-wrap" style="padding-top:0;">
								<span class="toggle-label">Asesoría Bernal:</span>
								<label class="toggle-switch">
									<input type="checkbox" id="fin-ase-bernal" onchange="toggleFinCostoAsesoria()">
									<span class="toggle-slider"></span>
								</label>
							</div>
						</div>
						<div class="nap-col" id="fin-row-costo-asesoria" style="display:none;">
							<label class="nap-label">Costo asesoría</label>
							<input type="number" id="fin-costo-a" class="nap-input" step="0.01" min="0" placeholder="0.00">
						</div>
					</div>

					<input type="hidden" id="fin-clave" value="">

					<div class="nap-footer">
						<button class="btn-guardar" onclick="guardarFinanzas()">Guardar finanzas</button>
					</div>

				</div>
				<!-- /fin-contenido -->

			</div>

			<!-- ── Pestaña 6: Historial de movimientos ── -->
			<div class="nap-pane" id="pane-tab6">

				<div id="hist-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder ver su historial.
					</div>
				</div>

				<div id="hist-contenido" style="display:none;">

					<!-- Línea de tiempo -->
					<div class="nap-seccion-titulo" style="margin-top:20px;">Línea de tiempo</div>
					<div id="hist-timeline" class="hist-timeline"></div>

					<!-- Movimientos de Estructura: Gerencia (con SC/Brocker integrado por bloque) -->
					<div class="nap-seccion-titulo" style="margin-top:24px;">Movimientos de estructura</div>
					<div class="hist-estructura-1col">
						<div id="hist-estructura-gerencia" class="hist-estructura-body"></div>
					</div>

				</div>
				<!-- /hist-contenido -->

			</div>

			<!-- ── Pestaña 7: Contactos de emergencia ── -->
			<div class="nap-pane" id="pane-tab7">

				<div id="ce-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder capturar sus contactos de emergencia.
					</div>
				</div>

				<div id="ce-contenido" style="display:none;">

					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
						<div class="nap-seccion-titulo" style="margin:0;">Contactos de emergencia</div>
						<button type="button" class="btn-bnc-nueva" onclick="abrirFormContacto()">+ Nuevo contacto</button>
					</div>

					<!-- Form alta/edición de contacto -->
					<div id="ce-form-wrap" style="display:none; margin-bottom:16px; padding:14px; background:#f7fafb; border:1px solid #e0e8ec; border-radius:8px;">
						<div class="nap-row">
							<div class="nap-col">
								<label class="nap-label">Nombre completo</label>
								<input type="text" id="ce-nombre" class="nap-input" maxlength="100" placeholder="Nombre completo del contacto">
							</div>
						</div>
						<div class="nap-row">
							<div class="nap-col">
								<label class="nap-label">Número de teléfono</label>
								<input type="text" id="ce-numero" class="nap-input" maxlength="15" placeholder="Teléfono">
							</div>
							<div class="nap-col">
								<label class="nap-label">Parentesco</label>
								<select id="ce-parentesco" class="nap-input">
									<option value="">— Selecciona —</option>
								</select>
							</div>
						</div>
						<input type="hidden" id="ce-editando-id" value="">
						<div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
							<button type="button" class="btn-cancelar" onclick="cerrarFormContacto()">Cancelar</button>
							<button type="button" class="btn-guardar" onclick="guardarContacto()">Guardar contacto</button>
						</div>
					</div>
					<!-- /ce-form-wrap -->

					<!-- Lista de contactos -->
					<div id="ce-lista" class="ce-lista"></div>

				</div>
				<!-- /ce-contenido -->

			</div>

			<!-- ── Pestaña 8: Información adicional ── -->
			<div class="nap-pane" id="pane-tab8">

				<div id="info-sin-empleado" style="display:block;">
					<div class="pane-placeholder">Selecciona un empleado para ver su información.</div>
				</div>

				<div id="info-contenido" style="display:none;">

					<!-- <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
						<div class="nap-seccion-titulo" style="margin:0;">Periodo de pago detectado</div>
						<span id="info-periodo-badge" style="font-size:12px;font-weight:700;font-family:monospace;background:#e8f4f8;color:#2B73B6;border:1px solid #b0d8e8;border-radius:20px;padding:3px 14px;">—</span>
					</div> -->

					<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px;">

						<div id="info-card-insuf" style="display:none;background:#fdeeee;border:1px solid #f3c4c4;border-left:4px solid #c0392b;border-radius:8px;padding:16px 18px;">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#c0392b;margin-bottom:6px;">Insuficiencia en pagos</div>
							<!-- <div id="info-insuf-motivo" style="font-size:12px;color:#888;margin-bottom:8px;"></div> -->
							<span style="font-size:22px;font-weight:700;color:#c0392b;" id="info-insuf-ajuste">$0.00</span>
							<!-- <span style="font-size:11px;color:#aaa;"> ajuste</span> -->
						</div>

						<div id="info-card-ok" style="display:none;background:#eafaf1;border:1px solid #b8e8cd;border-left:4px solid #1e8e58;border-radius:8px;padding:16px 18px;">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#1e8e58;margin-bottom:6px;">Insuficiencia</div>
							<div style="font-size:14px;font-weight:700;color:#1e8e58;margin-top:8px;">✓ Sin insuficiencia en este periodo</div>
						</div>

						<div id="info-card-facturas"
							onclick="abrirModalFacturas()"
							style="cursor:pointer; background:#fff8e8;border:1px solid #f5d97a;border-left:4px solid #e0a800;border-radius:8px;padding:16px 18px;transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#b37f00;margin-bottom:6px;">Facturas pendientes</div>
							<span style="font-size:28px;font-weight:700;color:#b37f00;" id="info-facturas-num">0</span>
							<div id="info-facturas-ok" style="display:none;font-size:13px;font-weight:700;color:#1e8e58;margin-top:6px;">✓ Sin facturas pendientes</div>
						</div>

						<!-- Adeudos / Préstamos -->
						<div id="info-card-adeudos"
							onclick="abrirModalAdeudos()"
							style="cursor:pointer; background:#d6e6f2; border:1px solid #aecbe0; border-left:4px solid #2B73B6; border-radius:8px; padding:16px 18px; transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(43,115,182,.2)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#2B73B6; margin-bottom:6px;">Préstamos pendientes</div>
							<div style="display:flex; align-items:baseline; gap:8px; margin-top:8px;">
								<span style="font-size:28px; font-weight:700; color:#2B73B6;" id="info-adeudos-num">0</span>
								<span style="font-size:12px; color:#aaa;">con pagos por cubrir</span>
							</div>
							<div id="info-adeudos-ok" style="display:none; font-size:13px; font-weight:700; color:#1e8e58; margin-top:6px;">✓ Sin adeudos pendientes</div>
						</div>

						<!-- Anticipos (Primer + Segundo, unificados) -->
						<div id="info-card-anticipos"
							onclick="abrirModalAnticipos()"
							style="cursor:pointer; background:#e6f7f5;border:1px solid #a8ded6;border-left:4px solid #0f9b8e;border-radius:8px;padding:16px 18px;transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(15,155,142,.2)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#0f7c72;margin-bottom:6px;">Anticipos pendientes</div>
							<span style="font-size:28px;font-weight:700;color:#0f7c72;" id="info-anticipos-num">0</span>
							<div id="info-anticipos-ok" style="display:none;font-size:13px;font-weight:700;color:#1e8e58;margin-top:6px;">✓ Sin folios pendientes</div>
						</div>

						<!-- Acompañamiento: A cobrar -->
						<div id="info-card-acomp-cobrar"
							onclick="abrirModalAcompanamiento('cobrar')"
							style="cursor:pointer; background:#eafaf1;border:1px solid #b8e8cd;border-left:4px solid #1e8e58;border-radius:8px;padding:16px 18px;transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(30,142,88,.2)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#1e8e58;margin-bottom:6px;">Acompañamiento a cobrar</div>
							<span style="font-size:28px;font-weight:700;color:#1e8e58;" id="info-acomp-cobrar-num">0</span>
							<div id="info-acomp-cobrar-ok" style="display:none;font-size:13px;font-weight:700;color:#1e8e58;margin-top:6px;">✓ Nada por cobrar</div>
						</div>

						<!-- Acompañamiento: A pagar -->
						<div id="info-card-acomp-pagar"
							onclick="abrirModalAcompanamiento('pagar')"
							style="cursor:pointer; background:#fdeeee;border:1px solid #f3c4c4;border-left:4px solid #c0392b;border-radius:8px;padding:16px 18px;transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(192,57,43,.2)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#c0392b;margin-bottom:6px;">Acompañamiento a pagar</div>
							<span style="font-size:28px;font-weight:700;color:#c0392b;" id="info-acomp-pagar-num">0</span>
							<div id="info-acomp-pagar-ok" style="display:none;font-size:13px;font-weight:700;color:#1e8e58;margin-top:6px;">✓ Nada por pagar</div>
						</div>

						<!-- Folios de producción: por pagar (rechazados/pagados se ven en el detalle) -->
						<div id="info-card-folios"
							onclick="abrirModalFolios()"
							style="cursor:pointer; background:#d6e6f2;border:1px solid #aecbe0;border-left:4px solid #2B73B6;border-radius:8px;padding:16px 18px;transition:box-shadow .15s;"
							onmouseenter="this.style.boxShadow='0 4px 12px rgba(43,115,182,.2)'"
							onmouseleave="this.style.boxShadow='none'">
							<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#2B73B6;margin-bottom:6px;">Folios por cobrar</div>
							<span style="font-size:28px;font-weight:700;color:#2B73B6;" id="info-folios-num">0</span>
							<div id="info-folios-ok" style="display:none;font-size:13px;font-weight:700;color:#1e8e58;margin-top:6px;">✓ Sin folios por cobrar</div>
						</div>

					</div>

					<div id="info-loading" style="text-align:center;padding:30px;color:#aaa;font-size:13px;">
						<span class="spinner-bnc"></span> Consultando...
					</div>
					<div id="info-error" style="display:none;color:#c0392b;font-size:13px;padding:16px;background:#fdf0ef;border-radius:6px;margin-top:8px;"></div>

				</div>
			</div>

			<!-- ── Pestaña 10: Expediente Digital ── -->
			<div class="nap-pane" id="pane-tab10">

				<div id="exd-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
							<path d="M14 2v6h6"/>
						</svg>
						Selecciona un empleado para ver su expediente digital.
					</div>
				</div>

				<div id="exd-contenido" style="display:none;">

					<div style="font-size:12px;color:#888;margin-bottom:14px;">
						Documentos cargados por el cliente/empleado en SISCA (imágenes o PDFs). Revisa cada uno y apruébalo o recházalo: los rechazados dejan de contar como vigentes en su expediente.
					</div>

					<div id="exd-loading" style="text-align:center;padding:30px;color:#aaa;font-size:13px;">
						<span class="spinner-bnc"></span> Consultando...
					</div>
					<div id="exd-error" style="display:none;color:#c0392b;font-size:13px;padding:16px;background:#fdf0ef;border-radius:6px;margin-top:8px;"></div>

					<div id="exd-sin-vinculo" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">
						Este empleado no tiene un expediente digital vinculado en SISCA todavía (no existe ningún expediente con esta clave).
					</div>

					<div id="exd-resumen" style="display:none;background:linear-gradient(135deg,#f0f6fa,#e8f2f8);border:1px solid #d4e6ef;border-radius:10px;padding:14px 16px;margin-bottom:14px;">
						<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
							<div>
								<div style="font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6f93a8;">Expediente en SISCA</div>
								<div id="exd-resumen-tipo" style="font-size:13.5px;font-weight:700;color:#14507a;margin-top:2px;">—</div>
							</div>
							<span id="exd-resumen-estatus" style="font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;"></span>
						</div>
						<div id="exd-resumen-conteo" style="font-size:12px;color:#5a7a8c;margin-top:8px;">—</div>
					</div>

					<div id="exd-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">
						Este empleado tiene expediente en SISCA, pero todavía no ha cargado documentos.
					</div>

					<div id="exd-lista" class="exd-lista"></div>

				</div>
			</div>
			<!-- /pane-tab10 -->

			<!-- ── Pestaña: Boletinados Principal ──
			     A diferencia de las demás pestañas de este panel, NO
			     depende de tener un empleado cargado -- es un registro
			     general, siempre disponible (ver también el botón
			     "📋 Boletinados Principal" en la barra superior, que
			     abre directo esta pestaña sin pasar por buscar/crear
			     un empleado). Sin scroll propio a propósito: se deja
			     crecer libremente y solo scrollea la página completa,
			     igual que en seg_espartanos.php. -->
			<div class="nap-pane" id="pane-boletinados">

				<div style="font-size:12px;color:#888;margin-bottom:14px;">
					Registro de personas boletinadas por Principal. Al dar de alta un nuevo empleado, el sistema compara su nombre contra esta lista y avisa si encuentra una posible coincidencia.
				</div>

				<div class="nap-row" style="margin-top:0;">
					<div class="nap-col">
						<label class="nap-label">Nombre(s)</label>
						<input type="text" id="bol-nombres" class="nap-input" placeholder="Ej: ANGELICA ALEJANDRA">
					</div>
					<div class="nap-col">
						<label class="nap-label">Apellido paterno</label>
						<input type="text" id="bol-apellido-paterno" class="nap-input" placeholder="Ej: GARCIA">
					</div>
					<div class="nap-col">
						<label class="nap-label">Apellido materno</label>
						<input type="text" id="bol-apellido-materno" class="nap-input" placeholder="Ej: TORRES">
					</div>
				</div>
				<div class="nap-row" style="justify-content:flex-end;">
					<button type="button" class="btn-guardar" onclick="guardarBoletinado()">+ Agregar a la lista</button>
				</div>

				<div class="nap-row" style="margin-top:22px;">
					<div class="nap-col" style="flex:1 1 100%;">
						<label class="nap-label">Buscar en la lista</label>
						<input type="text" id="bol-buscador" class="nap-input" placeholder="Buscar por nombre o apellido..." oninput="filtrarTablaBoletinados(this.value)">
					</div>
				</div>

				<table class="bnc-tabla" style="margin-top:10px;">
					<thead>
						<tr>
							<th>Nombre(s)</th>
							<th>Apellido paterno</th>
							<th>Apellido materno</th>
							<th>Capturado por</th>
							<th>Fecha</th>
							<th></th>
						</tr>
					</thead>
					<tbody id="bol-tbody">
						<tr><td colspan="6" class="bnc-vacio">Cargando...</td></tr>
					</tbody>
				</table>

			</div>
			<!-- /pane-boletinados -->

			<!-- ── Pestaña 9: Permisos por página (solo Super Usuario) ── -->
			<div class="nap-pane" id="pane-tab9">

				<div id="pp-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Guarda el usuario primero para poder asignar sus permisos.
					</div>
				</div>

				<div id="pp-contenido" style="display:none;">

					<div style="font-size:12px;color:#888;margin-bottom:14px;">
						Selecciona un módulo para ver y marcar las páginas permitidas para <strong id="pp-nombre-txt">este empleado</strong>. Los cambios de todos los módulos se guardan juntos al presionar "Guardar".
					</div>

					<div class="pp-layout">

						<!-- Columna izquierda: buscador + lista de módulos -->
						<div class="pp-col-modulos">
							<div class="nap-ta-wrapper" style="width:100%;margin-bottom:8px;">
								<input type="text" id="pp-modulo-buscar" class="nap-input nap-ta-input"
									placeholder="Buscar módulo..." autocomplete="off" oninput="filtrarModulosPaginas()">
							</div>
							<div id="pp-lista-modulos" class="pp-lista-modulos"></div>
						</div>

						<!-- Columna derecha: páginas del módulo seleccionado -->
						<div class="pp-col-paginas">
							<div class="pp-paginas-header">
								<span id="pp-modulo-actual-nombre">Selecciona un módulo</span>
								<span id="pp-modulo-actual-toggle" class="pp-modulo-toggle-all" style="display:none;" onclick="toggleTodasPaginasModuloActual()">seleccionar / quitar todo</span>
							</div>
							<div id="pp-paginas-lista" class="pp-paginas-lista">
								<div class="pp-paginas-vacio">Elige un módulo de la lista para ver sus páginas.</div>
							</div>
						</div>

					</div>

					<input type="hidden" id="pp-clave" value="">

					<div class="nap-footer pp-footer">
						<button class="btn-guardar" onclick="guardarPermisosPaginas()">Guardar permisos</button>
					</div>

				</div>

			</div>
			<!-- /pane-tab9 -->

			</div>
			<!-- /panel-empleado -->

		</div>
		<!-- /panel-empleado -->

		<!-- Estado vacío inicial -->
		<div id="estado-vacio">
			<svg width="56" height="56" fill="none" stroke="#bbb" stroke-width="1.4" viewBox="0 0 24 24">
				<circle cx="12" cy="8" r="4"/>
				<path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
			</svg>
			Busca un empleado o haz clic en <strong>+ Nuevo empleado</strong>
		</div>

	</div>
	<!-- /container-fluid -->

	<?php
		$divcontenido->close();
		$divcontenedor->close();
		$divpie->close();
	?>

	<script language='javascript'>
		var hc = calcula_h(125);
		var h  = $(window).outerHeight() - hc;
		$('#pie').css("top", h+5);
		hc = calcula_h(85);
		h  = $(window).outerHeight() - (hc*2);
		$('#contenedor').css("top", hc-15);
		$('#contenedor').css("height", h);
		hc = calcula_h(45);
		$('#menu').css("top", hc-20);
		$('#opcion').load('menu.php?modulo=<?php echo $pag?>');
	</script>

	<?php } ?>

	<!-- MODAL: Cargar estado de cuenta (extrae CLABE + detecta banco) -->
	<div id="modal-pdf-overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.6); z-index:99999; justify-content:center; align-items:center; padding:1.5rem; backdrop-filter:blur(4px);">
		<div style="width:100%; max-width:480px; max-height:90vh; background:#fff; border-radius:14px; display:flex; flex-direction:column; overflow:hidden;">

			<div style="padding:18px 22px 14px; border-bottom:1px solid #e8e8e8; display:flex; justify-content:space-between; align-items:center;">
				<h4 style="margin:0; font-size:15px; font-weight:700; color:#1a1a2e;">📄 Cargar estado de cuenta</h4>
				<button onclick="cerrarModalPdf()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#888;">✕</button>
			</div>

			<div style="flex:1; overflow-y:auto; padding:20px 22px; display:flex; flex-direction:column; gap:14px;">

				<div id="pdf-drop-zone" style="border:2px dashed #c0d4e0; border-radius:10px; padding:30px 16px; text-align:center; cursor:pointer; transition:border-color .15s;">
					<input type="file" id="pdf-file-input" accept="application/pdf,image/*" style="display:none;" onchange="onPdfFileSelected(this)">
					<div id="pdf-drop-texto">
						<div style="font-size:30px; margin-bottom:6px;">📄</div>
						<div style="font-size:13px; color:#555;">Haz clic para seleccionar el PDF o la foto/imagen<br>del estado de cuenta</div>
					</div>
				</div>

				<div id="pdf-procesando" style="display:none; text-align:center; padding:14px; font-size:13px; color:#888;">
					<span class="spinner-bnc"></span> <span id="pdf-procesando-texto">Leyendo documento...</span>
				</div>

				<div id="pdf-resultado" style="display:none;">
					<div style="background:#f0f6f9; border:1px solid #d8e8ee; border-radius:8px; padding:14px;">
						<div style="font-size:11px; color:#888; margin-bottom:4px;">CLABE detectada</div>
						<div id="pdf-clabe-detectada" style="font-family:monospace; font-weight:700; letter-spacing:1px; font-size:15px; color:#1a6785;"></div>
						<div style="font-size:11px; color:#888; margin-top:10px; margin-bottom:4px;">Nombre detectado</div>
						<div id="pdf-nombre-detectado" style="font-weight:600; font-size:13px; color:#222;"></div>
						<div style="font-size:11px; color:#888; margin-top:10px; margin-bottom:4px;">Banco detectado</div>
						<div id="pdf-banco-detectado" style="font-weight:600; font-size:13px; color:#222;"></div>
					</div>
					<div id="pdf-no-detectado" style="display:none; font-size:12px; color:#c0392b; background:#fdf0ef; border:1px solid #f3c8c4; border-radius:6px; padding:10px; margin-top:10px;">
						No se pudo detectar toda la información automáticamente. Verifica y completa los datos manualmente en el formulario.
					</div>
				</div>

			</div>

			<div style="padding:14px 22px; border-top:1px solid #e8e8e8; display:flex; gap:10px; justify-content:flex-end; background:#f8fafc;">
				<button onclick="cerrarModalPdf()" class="btn-cancelar">Cancelar</button>
				<button id="btn-usar-datos-pdf" onclick="usarDatosPdf()" class="btn-guardar" style="display:none;">Usar estos datos</button>
			</div>

		</div>
	</div>


	<!-- ══ MODAL: Facturas pendientes ══ -->
	<div id="modal-facturas-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:780px;max-height:88vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<div style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#fff8e8,#fff);">
			<div>
				<h4 style="margin:0;font-size:15px;font-weight:700;color:#92400e;">📄 Facturas pendientes</h4>
				<div id="modal-fac-subtitulo" style="font-size:12px;color:#aaa;margin-top:2px;"></div>
			</div>
			<button onclick="document.getElementById('modal-facturas-overlay').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;line-height:1;">✕</button>
			</div>

			<div style="flex:1;overflow-y:auto;padding:18px 22px;">
			<div id="modal-fac-loading" style="text-align:center;padding:40px;color:#aaa;font-size:13px;"><span class="spinner-bnc"></span> Cargando...</div>
			<div id="modal-fac-alerta-retenido" style="display:none;background:#fdeeee;border:1px solid #f3c4c4;border-left:4px solid #c0392b;border-radius:8px;padding:12px 16px;margin-bottom:14px;font-size:13px;font-weight:600;color:#c0392b;">
				⛔ Este empleado tiene pagos <strong>RETENIDOS</strong>
			</div>
			<table id="modal-fac-tabla" style="display:none;width:100%;border-collapse:collapse;font-size:12.5px;">
				<thead>
				<tr style="background:#fef3c7;">
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">No. Factura</th>
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">RFC</th>
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">Fec. Factura</th>
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">Fec. Subida</th>
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">PDF</th>
					<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">XML</th>
					<th style="padding:9px 10px;text-align:center;font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">Estado</th>
				</tr>
				</thead>
				<tbody id="modal-fac-tbody"></tbody>
			</table>
			<div id="modal-fac-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">Sin facturas pendientes.</div>
			</div>

			<div style="padding:12px 22px;border-top:1px solid #e8e8e8;background:#fafafa;text-align:right;">
			<button onclick="document.getElementById('modal-facturas-overlay').style.display='none'" style="background:#e0a800;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Cerrar</button>
			</div>
		</div>
	</div>
	<!-- ══ MODAL: Adeudos / Préstamos ══ -->
	<div id="modal-adeudos-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:860px;max-height:88vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<!-- Header -->
			<div style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#d6e6f2,#fff);">
				<div>
					<h4 style="margin:0;font-size:15px;font-weight:700;color:#2B73B6;">💳 Préstamos con adeudos</h4>
					<div id="modal-ade-subtitulo" style="font-size:12px;color:#aaa;margin-top:2px;"></div>
				</div>
				<button onclick="document.getElementById('modal-adeudos-overlay').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;line-height:1;">✕</button>
			</div>

			<!-- Body -->
			<div style="flex:1;overflow-y:auto;padding:18px 22px;">
				<div id="modal-ade-loading" style="text-align:center;padding:40px;color:#aaa;font-size:13px;">
					<span class="spinner-bnc"></span> Cargando...
				</div>
				<div id="modal-ade-lista" style="display:none;flex-direction:column;gap:18px;"></div>
				<div id="modal-ade-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">
					Sin préstamos con adeudos.
				</div>
			</div>

			<!-- Footer -->
			<div style="padding:12px 22px;border-top:1px solid #e8e8e8;background:#fafafa;text-align:right;">
				<button onclick="document.getElementById('modal-adeudos-overlay').style.display='none'" style="background:#2B73B6;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Cerrar</button>
			</div>

		</div>
	</div>

	<!-- ══ MODAL: Anticipos (Primer + Segundo, unificados) ══ -->
	<div id="modal-anticipos-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:720px;max-height:88vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<div style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#e6f7f5,#fff);">
				<div>
					<h4 style="margin:0;font-size:15px;font-weight:700;color:#0f7c72;">💰 Anticipos pendientes</h4>
					<div id="modal-ant-subtitulo" style="font-size:12px;color:#aaa;margin-top:2px;"></div>
				</div>
				<button onclick="document.getElementById('modal-anticipos-overlay').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;line-height:1;">✕</button>
			</div>

			<div style="flex:1;overflow-y:auto;padding:18px 22px;">
				<div id="modal-ant-loading" style="text-align:center;padding:40px;color:#aaa;font-size:13px;"><span class="spinner-bnc"></span> Cargando...</div>
				<table id="modal-ant-tabla" style="display:none;width:100%;border-collapse:collapse;font-size:12.5px;">
					<thead>
						<tr style="background:#e6f7f5;">
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#0f7c72;text-transform:uppercase;letter-spacing:.04em;">Folio</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#0f7c72;text-transform:uppercase;letter-spacing:.04em;">Tipo</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#0f7c72;text-transform:uppercase;letter-spacing:.04em;">Fec. Traspaso</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#0f7c72;text-transform:uppercase;letter-spacing:.04em;">Trabajador</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#0f7c72;text-transform:uppercase;letter-spacing:.04em;">Estatus</th>
						</tr>
					</thead>
					<tbody id="modal-ant-tbody"></tbody>
				</table>
				<div id="modal-ant-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">Sin folios pendientes de anticipo.</div>
			</div>

			<div style="padding:12px 22px;border-top:1px solid #e8e8e8;background:#fafafa;text-align:right;">
				<button onclick="document.getElementById('modal-anticipos-overlay').style.display='none'" style="background:#0f9b8e;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Cerrar</button>
			</div>
		</div>
	</div>

	<!-- ══ MODAL: Acompañamiento de naps (cobrar / pagar) ══ -->
	<div id="modal-acomp-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:680px;max-height:88vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<div id="modal-acomp-header" style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;">
				<div>
					<h4 id="modal-acomp-titulo" style="margin:0;font-size:15px;font-weight:700;"></h4>
					<div id="modal-acomp-subtitulo" style="font-size:12px;color:#aaa;margin-top:2px;"></div>
				</div>
				<button onclick="document.getElementById('modal-acomp-overlay').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;line-height:1;">✕</button>
			</div>

			<div style="flex:1;overflow-y:auto;padding:18px 22px;">
				<div id="modal-acomp-loading" style="text-align:center;padding:40px;color:#aaa;font-size:13px;"><span class="spinner-bnc"></span> Cargando...</div>

				<div id="modal-acomp-resumen" style="display:none;background:linear-gradient(135deg,#f7fafc,#eef2f4);border:1px solid #e0e8ec;border-radius:10px;padding:14px 16px;margin-bottom:14px;display:flex;justify-content:space-around;text-align:center;">
					<div>
						<div style="font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6f93a8;">Folios</div>
						<div style="font-size:22px;font-weight:700;color:#14507a;margin-top:4px;" id="modal-acomp-total-folios">0</div>
					</div>
					<div>
						<div style="font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6f93a8;">Monto total</div>
						<div style="font-size:22px;font-weight:700;color:#14507a;margin-top:4px;" id="modal-acomp-total-monto">$0.00</div>
					</div>
				</div>

				<table id="modal-acomp-tabla" style="display:none;width:100%;border-collapse:collapse;font-size:12.5px;">
					<thead>
						<tr id="modal-acomp-thead-tr">
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Folio</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Fec. Pago</th>
							<th style="padding:9px 10px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Costo</th>
						</tr>
					</thead>
					<tbody id="modal-acomp-tbody"></tbody>
				</table>
				<div id="modal-acomp-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;"></div>
			</div>

			<div style="padding:12px 22px;border-top:1px solid #e8e8e8;background:#fafafa;text-align:right;">
				<button id="modal-acomp-btn-cerrar" onclick="document.getElementById('modal-acomp-overlay').style.display='none'" style="color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Cerrar</button>
			</div>
		</div>
	</div>

	<!-- ══ MODAL: Folios de producción (todos los que le pertenecen) ══ -->
	<div id="modal-folios-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:780px;max-height:88vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<div style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#d6e6f2,#fff);">
				<div>
					<h4 style="margin:0;font-size:15px;font-weight:700;color:#2B73B6;">📂 Folios (últimos 2 meses)</h4>
					<div id="modal-fol-subtitulo" style="font-size:12px;color:#aaa;margin-top:2px;"></div>
				</div>
				<button onclick="document.getElementById('modal-folios-overlay').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;line-height:1;">✕</button>
			</div>

			<div style="flex:1;overflow-y:auto;padding:18px 22px;">
				<div id="modal-fol-loading" style="text-align:center;padding:40px;color:#aaa;font-size:13px;"><span class="spinner-bnc"></span> Cargando...</div>

				<div id="modal-fol-contadores" class="hist-contadores" style="display:none;margin-bottom:8px;grid-template-columns:1fr 1fr;">
					<div class="hist-contador hist-baja fol-contador-filtro" id="modal-fol-card-pendiente" onclick="filtrarFolios(true)">
						<div class="hist-contador-num" id="modal-fol-num-pendiente" style="color:#c0392b;">0</div>
						<div class="hist-contador-label">Pendientes de validar pago</div>
					</div>
					<div class="hist-contador hist-ingreso fol-contador-filtro" id="modal-fol-card-pagado" onclick="filtrarFolios(false)">
						<div class="hist-contador-num" id="modal-fol-num-pagado" style="color:#1e8e58;">0</div>
						<div class="hist-contador-label">Pagados</div>
					</div>
				</div>

				<div id="modal-fol-filtro-activo-wrap" style="display:none;margin-bottom:12px;font-size:12px;color:#888;">
					Mostrando solo: <strong id="modal-fol-filtro-activo-txt"></strong>
					&nbsp;·&nbsp;
					<span style="color:#2B73B6;cursor:pointer;text-decoration:underline;" onclick="filtrarFolios(null)">ver todos</span>
				</div>

				<table id="modal-fol-tabla" style="display:none;width:100%;border-collapse:collapse;font-size:12.5px;">
					<thead>
						<tr style="background:#d6e6f2;">
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#2B73B6;text-transform:uppercase;letter-spacing:.04em;">Folio</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#2B73B6;text-transform:uppercase;letter-spacing:.04em;">Estatus</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#2B73B6;text-transform:uppercase;letter-spacing:.04em;">Fec. Traspaso</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#2B73B6;text-transform:uppercase;letter-spacing:.04em;">Trabajador</th>
							<th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#2B73B6;text-transform:uppercase;letter-spacing:.04em;">Pendiente de validar pago</th>
						</tr>
					</thead>
					<tbody id="modal-fol-tbody"></tbody>
				</table>
				<div id="modal-fol-vacio" style="display:none;text-align:center;padding:40px;color:#aaa;font-size:13px;">Este empleado no tiene folios en los últimos 2 meses.</div>
			</div>

			<div style="padding:12px 22px;border-top:1px solid #e8e8e8;background:#fafafa;text-align:right;">
				<button onclick="document.getElementById('modal-folios-overlay').style.display='none'" style="background:#2B73B6;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Cerrar</button>
			</div>
		</div>
	</div>

	<!-- ══ MODAL: Ver documento del Expediente Digital ══ -->
	<div id="modal-exd-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.7);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:720px;max-height:92vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);">

			<div style="padding:16px 20px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;">
				<div>
					<h4 id="exd-modal-tipo" style="margin:0;font-size:15px;font-weight:700;color:#1a1a2e;">Documento</h4>
					<div id="exd-modal-nombre" style="font-size:11.5px;color:#aaa;margin-top:2px;word-break:break-all;"></div>
				</div>
				<button onclick="cerrarModalExpediente()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#888;">✕</button>
			</div>

			<div style="flex:1;overflow:auto;background:#f0f2f5;display:flex;align-items:center;justify-content:center;min-height:320px;">
				<img id="exd-modal-img" style="max-width:100%;max-height:70vh;display:none;">
				<iframe id="exd-modal-pdf" style="width:100%;height:70vh;border:none;display:none;"></iframe>
			</div>

			<div style="padding:12px 20px;border-top:1px solid #e8e8e8;">
				<span id="exd-modal-badge" class="exd-badge"></span>
				<div id="exd-modal-comentario" style="display:none;font-size:12px;color:#c0392b;margin-top:8px;font-style:italic;"></div>
			</div>

			<div style="padding:14px 20px;border-top:1px solid #e8e8e8;display:flex;gap:10px;justify-content:flex-end;background:#f8fafc;">
				<button onclick="cerrarModalExpediente()" class="btn-cancelar">Cerrar</button>
				<button id="exd-modal-btn-rechazar" onclick="rechazarDocumentoExpediente()" style="background:#c0392b;color:#fff;border:none;padding:8px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Rechazar</button>
				<button id="exd-modal-btn-aprobar" onclick="aprobarDocumentoExpediente()" style="background:#1e8e58;color:#fff;border:none;padding:8px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Aprobar</button>
			</div>

		</div>
	</div>

	<!-- ══ MODAL: Permisos de pestañas (solo Super Usuario) ══ -->
	<div id="modal-permisos-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:99999;justify-content:center;align-items:center;padding:1.5rem;backdrop-filter:blur(4px);">
		<div style="width:100%;max-width:880px;max-height:90vh;background:#fff;border-radius:14px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

			<div style="padding:18px 22px 14px;border-bottom:1px solid #e8e8e8;display:flex;justify-content:space-between;align-items:center;">
				<h4 style="margin:0;font-size:15px;font-weight:700;color:#1a1a2e;"> Permisos de pestañas</h4>
				<button onclick="cerrarModalPermisos()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#888;">✕</button>
			</div>

			<div style="flex:1;overflow-y:auto;padding:20px 22px;">

				<div class="nap-seccion-titulo" style="margin-top:0;">Usuarios con permisos asignados</div>
				<div style="font-size:12px;color:#888;margin-bottom:10px;">
					Quién tiene acceso a qué pestañas ahora mismo. Haz clic en editar para ajustarlo, o en la papelera para quitarle todo.
				</div>

				<input type="text" id="perm-lista-buscar" class="nap-input" placeholder="Buscar por nombre o clave..."
					   autocomplete="off" oninput="renderListaPermisos()" style="margin-bottom:8px;">

				<div style="max-height:220px;overflow-y:auto;border:1px solid #e0e8ec;border-radius:6px;margin-bottom:22px;">
					<table class="bnc-tabla">
						<thead>
							<tr>
								<th>Usuario</th>
								<th>Clave</th>
								<th>Pestañas permitidas</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="perm-lista-tbody">
							<tr><td colspan="4" class="bnc-vacio">Cargando...</td></tr>
						</tbody>
					</table>
				</div>

				<div class="nap-seccion-titulo" style="margin-top:0;">Asignar permisos por lote (nivel)</div>
				<div style="font-size:12px;color:#888;margin-bottom:12px;">
					Selecciona un nivel y las pestañas que le corresponden. Se aplicará a <strong>todos</strong> los usuarios que tengan ese nivel asignado en este momento. Después puedes seguir ajustando el acceso de una persona en particular desde la sección de abajo, sin que afecte al resto del nivel.
				</div>

				<div class="nap-row">
					<div class="nap-col-fixed-260">
						<label class="nap-label">Nivel</label>
						<select id="perm-lote-nivel" class="nap-input" onchange="onCambiaNivelLote()">
							<option value="">— Selecciona un nivel —</option>
						</select>
					</div>
				</div>

				<div id="perm-lote-usuarios-wrap" style="display:none;margin-bottom:14px;">
					<div class="nap-label" style="margin-bottom:4px;">Usuarios que tienen este nivel</div>
					<div id="perm-lote-usuarios" class="perm-lote-usuarios"></div>
				</div>

				<div class="nap-row">
					<div class="nap-col" style="flex:1 1 100%;">
						<label class="nap-label">Pestañas a otorgar</label>
						<div id="perm-lote-checks" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;"></div>
					</div>
				</div>

				<div class="nap-footer" style="justify-content:flex-start;border-top:none;padding-top:0;">
					<button class="btn-guardar" onclick="aplicarPermisosLote()">Aplicar a todos los del nivel</button>
				</div>

				<div class="nap-seccion-titulo" style="margin-top:28px;">Permisos individuales</div>
				<div style="font-size:12px;color:#888;margin-bottom:10px;">
					Busca cualquier usuario del sistema por nombre o clave para ajustar sus permisos, sin necesidad de abrir su expediente completo.
				</div>

				<div class="nap-row">
					<div class="nap-col">
						<label class="nap-label">Buscar usuario</label>
						<div class="nap-ta-wrapper" style="width:100%;">
							<input type="text" id="perm-ind-search" class="nap-input nap-ta-input"
								   placeholder="Buscar por nombre o clave..." autocomplete="off">
							<div class="nap-ta-dropdown" id="perm-ind-dropdown"></div>
						</div>
						<input type="hidden" id="perm-ind-clave" value="">
					</div>
				</div>

				<div id="perm-ind-sin-empleado" style="display:block;">
					<div class="pane-placeholder">
						<svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<line x1="3" y1="9" x2="21" y2="9"/>
							<line x1="9" y1="21" x2="9" y2="9"/>
						</svg>
						Busca un usuario arriba para ajustar sus permisos.
					</div>
				</div>

				<div id="perm-ind-contenido" style="display:none;">
					<div style="font-size:12px;color:#888;margin-bottom:8px;">
						Ajusta aquí las pestañas de <strong id="perm-ind-nombre-txt">este empleado</strong>. Esto solo cambia sus permisos, no afecta a nadie más de su nivel.
					</div>
					<div id="perm-ind-checks" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;"></div>
					<div class="nap-footer" style="justify-content:flex-start;border-top:none;padding-top:0;">
						<button class="btn-guardar" onclick="guardarPermisosIndividual()">Guardar permisos de este empleado</button>
					</div>
				</div>

			</div>

			<div style="padding:14px 22px;border-top:1px solid #e8e8e8;display:flex;gap:10px;justify-content:flex-end;background:#f8fafc;">
				<button onclick="cerrarModalPermisos()" class="btn-cancelar">Cerrar</button>
			</div>

		</div>
	</div>
	</body>

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
	<script>
		if (window.pdfjsLib) {
			pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
		}
	</script>
	<!-- OCR de respaldo: fotos/capturas del estado de cuenta (o PDFs que en
	     realidad solo traen una imagen escaneada adentro, sin texto real). -->
	<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

	<script type="text/javascript">

	const API = 'RRHH_api.php';

	async function api(payload) {
		const resp = await fetch(API, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	/* ════════════════════════════════════════
	   PERMISOS DE PESTAÑAS
	════════════════════════════════════════ */
	const PESTANAS_INFO = [
		{ key: 'usuario',              pane: 'pane-datos', label: 'Usuario' },
		{ key: 'datos_generales',      pane: 'pane-tab2',  label: 'Datos generales' },
		{ key: 'datos_bancarios',      pane: 'pane-tab3',  label: 'Datos bancarios' },
		{ key: 'estructura',           pane: 'pane-tab4',  label: 'Estruc empleados' },
		{ key: 'finanzas',             pane: 'pane-tab5',  label: 'Finanzas' },
		{ key: 'historial',            pane: 'pane-tab6',  label: 'Historial' },
		{ key: 'contactos_emergencia', pane: 'pane-tab7',  label: 'Contac. Emergencia' },
		{ key: 'info',                 pane: 'pane-tab8',  label: 'Información' },
		{ key: 'expediente_digital',   pane: 'pane-tab10', label: 'Expediente Digital' },
		{ key: 'boletinados',          pane: 'pane-boletinados', label: 'Boletinados Principal' },
		{ key: 'permisos',             pane: 'pane-tab9',  label: 'Permisos' },
	];

	/* Oculta del menú de pestañas todo lo que el usuario en sesión no
	   tenga permitido, muestra/oculta el botón "+ Nuevo empleado" (requiere
	   el mismo permiso que la pestaña Usuario) y el botón "Permisos" de la
	   barra superior (solo claves autorizadas explícitamente, ver
	   NAP_PUEDE_PERMISOS_TOP más abajo). Esto es solo UX: el backend
	   vuelve a validar todo lo sensible (ver RRHH_api.php, acciones
	   guardarPermisosPestanas, aplicarPermisosPorNivel, getTodosPermisos,
	   etc.). */
	function inicializarVisibilidadPestanas() {
		const esSuper   = !!window.NAP_ES_SUPERUSUARIO;
		const permitidas = Array.isArray(window.NAP_PESTANAS_PERMITIDAS) ? window.NAP_PESTANAS_PERMITIDAS : [];
		const puedePermisosTop = !!window.NAP_PUEDE_PERMISOS_TOP;

		// Botón "Permisos" (barra superior, abre el modal global de
		// gestión): exclusivo de las claves autorizadas, sin importar
		// nivel ni pestañas asignadas.
		document.getElementById('btn-permisos-top').style.display = puedePermisosTop ? '' : 'none';

		// Botón "+ Nuevo empleado": mismo permiso que la pestaña Usuario.
		const btnNuevoEmp = document.getElementById('btn-nuevo-emp');
		if (btnNuevoEmp) {
			const puedeVerUsuario = esSuper || permitidas.indexOf('usuario') !== -1;
			btnNuevoEmp.style.display = puedeVerUsuario ? '' : 'none';
		}

		// Botón "📋 Boletinados Principal": mismo permiso que su pestaña.
		const btnBoletinadosTop = document.getElementById('btn-boletinados-top');
		if (btnBoletinadosTop) {
			const puedeVerBoletinados = esSuper || permitidas.indexOf('boletinados') !== -1;
			btnBoletinadosTop.style.display = puedeVerBoletinados ? '' : 'none';
		}

		if (esSuper) return; /* el Super Usuario ve todas las pestañas normales */

		let huboActivaOculta = false;
		let primerPaneVisible = null;

		PESTANAS_INFO.forEach(info => {
			const permitida = permitidas.indexOf(info.key) !== -1;
			const tab  = document.querySelector('.nap-tab[data-pane="' + info.pane + '"]');
			const pane = document.getElementById(info.pane);
			if (!tab || !pane) return;

			if (!permitida) {
				tab.style.display = 'none';
				if (tab.classList.contains('activa')) huboActivaOculta = true;
			} else if (!primerPaneVisible) {
				primerPaneVisible = info.pane;
			}
		});

		if (huboActivaOculta && primerPaneVisible) {
			activarTab(primerPaneVisible);
		} else if (!primerPaneVisible) {
			/* No tiene ninguna pestaña permitida todavía */
			document.querySelectorAll('.nap-pane').forEach(p => p.classList.remove('activo'));
			document.querySelectorAll('.nap-tab').forEach(t => t.classList.remove('activa'));
		}
	}

	/* ── Modal de Permisos (botón en la barra superior) ──
	   Vive fuera del panel de empleado: no depende de tener un
	   empleado seleccionado ni de que esté guardado. */
	function abrirModalPermisos() {
		document.getElementById('modal-permisos-overlay').style.display = 'flex';
		if (document.getElementById('perm-lote-nivel').options.length <= 1) {
			cargarNivelesLote();
		}
		cargarListaPermisos();
	}

	function cerrarModalPermisos() {
		document.getElementById('modal-permisos-overlay').style.display = 'none';
	}

	/* ── Listado de "quién tiene qué" ──
	   Muestra todos los usuarios con al menos una pestaña asignada,
	   con acciones rápidas para editar (los manda a la sección de
	   permisos individuales, ya cargados) o quitarles todo de un tiro. */
	let permListaCache = [];

	async function cargarListaPermisos() {
		const tbody = document.getElementById('perm-lista-tbody');
		tbody.innerHTML = '<tr><td colspan="4" class="bnc-vacio">Cargando...</td></tr>';
		document.getElementById('perm-lista-buscar').value = '';

		try {
			const resp = await api({ action: 'getTodosPermisos' });

			if (!resp.ok) {
				permListaCache = [];
				tbody.innerHTML = '<tr><td colspan="4" class="bnc-vacio">' + (resp.msg || 'Error al cargar.') + '</td></tr>';
				return;
			}

			permListaCache = resp.datos || [];
			renderListaPermisos();

		} catch (e) {
			permListaCache = [];
			tbody.innerHTML = '<tr><td colspan="4" class="bnc-vacio">Error de comunicación con el servidor.</td></tr>';
		}
	}

	/* Filtra permListaCache por nombre/clave (client-side, ya está todo
	   cargado) y repinta la tabla. Se llama al cargar y en cada tecleo
	   del buscador. */
	function renderListaPermisos() {
		const tbody = document.getElementById('perm-lista-tbody');
		const q = (document.getElementById('perm-lista-buscar').value || '').trim().toLowerCase();

		if (permListaCache.length === 0) {
			tbody.innerHTML = '<tr><td colspan="4" class="bnc-vacio">Nadie tiene permisos asignados todavía.</td></tr>';
			return;
		}

		const datos = q
			? permListaCache.filter(u => u.nombre.toLowerCase().includes(q) || u.clave.toLowerCase().includes(q))
			: permListaCache;

		if (datos.length === 0) {
			tbody.innerHTML = '<tr><td colspan="4" class="bnc-vacio">Sin resultados para "' + q + '"</td></tr>';
			return;
		}

		tbody.innerHTML = datos.map(u => {
			const chips = (u.pestanas || []).map(k => {
				const info = PESTANAS_INFO.find(p => p.key === k);
				return '<span class="perm-lote-usuario-chip">' + (info ? info.label : k) + '</span>';
			}).join(' ');
			const badgeInactivo = (u.activo == 0)
				? '<span style="font-size:10px;background:#fdeeee;color:#c0392b;border-radius:4px;padding:2px 6px;margin-left:6px;font-weight:700;">INACTIVO</span>'
				: '';
			const nombreEsc = u.nombre.replace(/'/g, "\\'");

			return `<tr>
				<td>${resaltarTexto(u.nombre, q)}${badgeInactivo}</td>
				<td class="bnc-pill-clabe">${resaltarTexto(u.clave, q)}</td>
				<td>${chips || '<span style="color:#aaa;font-style:italic;">sin pestañas</span>'}</td>
				<td style="text-align:right;white-space:nowrap;">
					<button class="bnc-btn-icon" title="Editar" onclick="editarPermisosDesdeTabla('${u.clave}', '${nombreEsc}')">&#9998;</button>
					<button class="bnc-btn-icon" title="Quitar todos los permisos" onclick="quitarTodosPermisos('${u.clave}', '${nombreEsc}')">🗑️</button>
				</td>
			</tr>`;
		}).join('');
	}

	function editarPermisosDesdeTabla(clave, nombre) {
		cargarPermisosIndividual(clave, nombre);
		document.getElementById('perm-ind-contenido').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}

	async function quitarTodosPermisos(clave, nombre) {
		const result = await Swal.fire({
			title: '¿Quitar todos los permisos?',
			html: `<strong>${nombre}</strong> dejará de ver todas las pestañas hasta que se le asignen de nuevo.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, quitar todos',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#c0392b'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'guardarPermisosPestanas', clave, pestanas: [] });
			ocultarLoader();
			if (resp.ok) {
				await cargarListaPermisos();
				if (document.getElementById('perm-ind-clave').value === clave) {
					renderPermChecks('perm-ind-checks', []);
				}
				Swal.fire({ icon:'success', title:'Permisos quitados', timer:1500, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	function renderPermChecks(contenedorId, seleccionadas) {
		const cont = document.getElementById(contenedorId);
		cont.innerHTML = PESTANAS_INFO.map(info => {
			const checked = seleccionadas.indexOf(info.key) !== -1 ? 'checked' : '';
			return `<label class="perm-check-item">
				<input type="checkbox" class="perm-chk" value="${info.key}" ${checked}>
				${info.label}
			</label>`;
		}).join('');
	}

	function leerPermChecksSeleccionados(contenedorId) {
		return Array.from(document.querySelectorAll('#' + contenedorId + ' .perm-chk:checked')).map(c => c.value);
	}

	/* ── Carga inicial del catálogo de niveles para el selector de lote ── */
	async function cargarNivelesLote() {
		const sel = document.getElementById('perm-lote-nivel');
		try {
			const resp = await api({ action: 'getCatalogoNivel', q: '' });
			const niveles = resp.datos || [];
			sel.innerHTML = '<option value="">— Selecciona un nivel —</option>'
				+ niveles.map(n => `<option value="${n.id}">${n.nombre}</option>`).join('');
		} catch (e) {
			console.error('Error cargando catálogo de niveles:', e);
		}
		renderPermChecks('perm-lote-checks', []);
	}

	async function onCambiaNivelLote() {
		const idNivel = document.getElementById('perm-lote-nivel').value;
		const wrapUsuarios = document.getElementById('perm-lote-usuarios-wrap');
		const contUsuarios  = document.getElementById('perm-lote-usuarios');

		if (!idNivel) {
			wrapUsuarios.style.display = 'none';
			contUsuarios.innerHTML = '';
			return;
		}

		contUsuarios.innerHTML = '<span style="font-size:12px;color:#aaa;">Cargando...</span>';
		wrapUsuarios.style.display = 'block';

		try {
			const resp = await api({ action: 'getUsuariosPorNivel', id_nivel: idNivel });
			const usuarios = resp.datos || [];
			if (usuarios.length === 0) {
				contUsuarios.innerHTML = '<span style="font-size:12px;color:#aaa;">Nadie tiene este nivel asignado todavía.</span>';
			} else {
				contUsuarios.innerHTML = usuarios.map(u =>
					`<span class="perm-lote-usuario-chip">${u.nombre}</span>`
				).join('');
			}
		} catch (e) {
			contUsuarios.innerHTML = '<span style="font-size:12px;color:#c0392b;">Error al cargar usuarios.</span>';
		}
	}

	async function aplicarPermisosLote() {
		const idNivel   = document.getElementById('perm-lote-nivel').value;
		const nombreNivel = document.getElementById('perm-lote-nivel').selectedOptions[0]
			? document.getElementById('perm-lote-nivel').selectedOptions[0].textContent : '';
		const pestanas  = leerPermChecksSeleccionados('perm-lote-checks');

		if (!idNivel) { Swal.fire('Selecciona un nivel', 'Elige el nivel al que quieres asignar permisos.', 'warning'); return; }

		const result = await Swal.fire({
			title: `¿Aplicar permisos a todos los de "${nombreNivel}"?`,
			html: pestanas.length
				? `Van a poder ver: <strong>${pestanas.map(k => (PESTANAS_INFO.find(p=>p.key===k)||{}).label).join(', ')}</strong>. Esto reemplaza los permisos actuales de todos los usuarios de este nivel.`
				: 'No seleccionaste ninguna pestaña: esto les quitará el acceso a todas las pestañas a todos los usuarios de este nivel.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Sí, aplicar a todos',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'aplicarPermisosPorNivel', id_nivel: idNivel, pestanas });
			ocultarLoader();
			if (resp.ok) {
				await cargarListaPermisos();
				Swal.fire({ icon:'success', title:'Permisos aplicados', text: resp.msg || 'Listo.', timer:2000, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo aplicar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ── Permisos individuales: búsqueda propia sobre carobra.usuarios ──
	   Independiente del empleado que esté cargado en el panel de arriba:
	   permite buscar y asignar permisos a cualquier usuario del sistema
	   sin necesidad de abrir su expediente completo (datos generales,
	   bancarios, etc.). Reutiliza la misma acción "buscarAsesor" que ya
	   usa el buscador principal, porque ambas cosas leen carobra.usuarios. */
	let timerPermInd = null;
	const inpPermInd  = document.getElementById('perm-ind-search');
	const dropPermInd = document.getElementById('perm-ind-dropdown');

	if (inpPermInd) {
		inpPermInd.addEventListener('keyup', function (e) {
			if ([13, 27, 38, 40].indexOf(e.keyCode) !== -1) return;
			clearTimeout(timerPermInd);
			const q = this.value.trim();
			if (q.length < 2) { dropPermInd.classList.remove('open'); dropPermInd.innerHTML = ''; return; }

			dropPermInd.innerHTML = '<div class="nap-ta-cargando">Buscando...</div>';
			dropPermInd.classList.add('open');

			timerPermInd = setTimeout(async () => {
				try {
					const resp = await api({ action: 'buscarAsesor', q });
					const lista = resp.datos || [];
					dropPermInd.innerHTML = '';
					if (lista.length === 0) {
						dropPermInd.innerHTML = '<div class="nap-ta-vacio">Sin resultados</div>';
					} else {
						lista.forEach(u => {
							const item = document.createElement('div');
							item.className = 'nap-ta-item';
							const badgeInactivo = (u.activo == 0)
								? '<span style="font-size:10px;background:#fdeeee;color:#c0392b;border-radius:4px;padding:2px 6px;margin-left:6px;font-weight:700;">INACTIVO</span>'
								: '';
							item.innerHTML = '<span class="nap-ta-nombre">' + resaltarTexto(u.nombre, q) + badgeInactivo + '</span>'
							               + '<span class="nap-ta-clave">' + u.clave + '</span>';
							item.addEventListener('mousedown', () => {
								dropPermInd.classList.remove('open');
								inpPermInd.value = u.nombre;
								cargarPermisosIndividual(u.clave, u.nombre);
							});
							dropPermInd.appendChild(item);
						});
					}
					dropPermInd.classList.add('open');
				} catch (e) {
					dropPermInd.classList.remove('open');
				}
			}, 300);
		});

		inpPermInd.addEventListener('blur', function () {
			setTimeout(() => dropPermInd.classList.remove('open'), 200);
		});
	}

	/* ── Permisos individuales: cargar/guardar del usuario seleccionado ──
	   Se llama tanto desde la búsqueda propia de arriba como, por
	   comodidad, automáticamente cuando se carga un empleado desde el
	   buscador principal (cargarFormulario). */
	async function cargarPermisosIndividual(clave, nombre) {
		document.getElementById('perm-ind-clave').value = clave || '';
		document.getElementById('perm-ind-search').value = nombre || clave || '';
		document.getElementById('perm-ind-sin-empleado').style.display = 'none';
		document.getElementById('perm-ind-contenido').style.display    = 'block';
		document.getElementById('perm-ind-nombre-txt').textContent     = nombre || clave;

		renderPermChecks('perm-ind-checks', []);

		try {
			const resp = await api({ action: 'getPermisosPestanas', clave });
			if (resp.ok) renderPermChecks('perm-ind-checks', resp.pestanas || []);
		} catch (e) {
			console.error('Error cargando permisos individuales:', e);
		}
	}

	async function guardarPermisosIndividual() {
		const clave = document.getElementById('perm-ind-clave').value;
		if (!clave) return;

		const pestanas = leerPermChecksSeleccionados('perm-ind-checks');

		mostrarLoader();
		try {
			const resp = await api({ action: 'guardarPermisosPestanas', clave, pestanas });
			ocultarLoader();
			if (resp.ok) {
				await cargarListaPermisos();
				Swal.fire({ icon:'success', title:'Guardado', text: resp.msg || 'Permisos actualizados.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	PERMISOS POR PÁGINA (pestaña 9) — solo Super Usuario
	════════════════════════════════════════ */
	let ppModulosCache   = [];
	let ppSeleccionadas  = new Set();
	let ppModuloActualId = null;

	async function cargarCatalogoModulosPaginas() {
		if (ppModulosCache.length > 0) return ppModulosCache;
		try {
			const resp = await api({ action: 'getCatalogoModulosPaginas' });
			if (resp.ok) ppModulosCache = resp.modulos || [];
		} catch (e) { console.error('Error cargando catálogo de módulos/páginas:', e); }
		return ppModulosCache;
	}

	async function cargarPermisosPaginas(clave, nombre) {
		document.getElementById('pp-clave').value = clave || '';
		document.getElementById('pp-nombre-txt').textContent = nombre || clave || 'este empleado';
		document.getElementById('pp-modulo-buscar').value = '';
		ppModuloActualId = null;

		await cargarCatalogoModulosPaginas();

		ppSeleccionadas = new Set();
		try {
			const resp = await api({ action: 'getPermisosPaginas', clave });
			if (resp.ok) ppSeleccionadas = new Set(resp.paginas || []);
		} catch (e) {
			console.error('Error cargando permisos de páginas:', e);
		}

		renderListaModulos();
		renderPaginasModulo(null);
	}

	function filtrarModulosPaginas() {
		renderListaModulos();
	}

	function renderListaModulos() {
		const cont = document.getElementById('pp-lista-modulos');
		const q = (document.getElementById('pp-modulo-buscar').value || '').trim().toLowerCase();

		const modulos = q
			? ppModulosCache.filter(m => m.modulo.toLowerCase().includes(q))
			: ppModulosCache;

		if (modulos.length === 0) {
			cont.innerHTML = `<div class="pp-modulo-vacio">${q ? 'Sin resultados para "' + q + '"' : 'Sin módulos configurados.'}</div>`;
			return;
		}

		cont.innerHTML = modulos.map(m => {
			const totalAsignadas = (m.paginas || []).filter(p => ppSeleccionadas.has(p.id_pagina)).length;
			const activo = m.id_modulo === ppModuloActualId ? ' pp-modulo-activo' : '';
			const badge  = totalAsignadas > 0 ? `<span class="pp-modulo-badge">${totalAsignadas}</span>` : '';
			return `<div class="pp-modulo-item${activo}" onclick="seleccionarModuloPaginas(${m.id_modulo})">
				<span>${m.modulo}</span>${badge}
			</div>`;
		}).join('');
	}

	function seleccionarModuloPaginas(idModulo) {
		ppModuloActualId = idModulo;
		renderListaModulos();
		renderPaginasModulo(idModulo);
	}

	function renderPaginasModulo(idModulo) {
		const nombreEl = document.getElementById('pp-modulo-actual-nombre');
		const toggleEl = document.getElementById('pp-modulo-actual-toggle');
		const listaEl  = document.getElementById('pp-paginas-lista');

		if (!idModulo) {
			nombreEl.textContent = 'Selecciona un módulo';
			toggleEl.style.display = 'none';
			listaEl.innerHTML = '<div class="pp-paginas-vacio">Elige un módulo de la lista para ver sus páginas.</div>';
			return;
		}

		const mod = ppModulosCache.find(m => m.id_modulo === idModulo);
		if (!mod) return;

		nombreEl.textContent = mod.modulo;
		toggleEl.style.display = (mod.paginas && mod.paginas.length) ? 'inline' : 'none';

		if (!mod.paginas || mod.paginas.length === 0) {
			listaEl.innerHTML = '<div class="pp-paginas-vacio">Este módulo no tiene páginas configuradas.</div>';
			return;
		}

		listaEl.innerHTML = mod.paginas.map(p => {
			const checked = ppSeleccionadas.has(p.id_pagina) ? 'checked' : '';
			return `<label class="perm-check-item">
				<input type="checkbox" class="pp-chk" value="${p.id_pagina}" ${checked} onchange="onTogglePaginaChk(this)">
				${p.nom_pagina}
			</label>`;
		}).join('');
	}

	function onTogglePaginaChk(chk) {
		const id = parseInt(chk.value, 10);
		if (chk.checked) ppSeleccionadas.add(id);
		else ppSeleccionadas.delete(id);
		renderListaModulos(); /* refresca el badge del módulo actual */
	}

	function toggleTodasPaginasModuloActual() {
		if (!ppModuloActualId) return;
		const mod = ppModulosCache.find(m => m.id_modulo === ppModuloActualId);
		if (!mod || !mod.paginas) return;

		const marcarTodo = mod.paginas.some(p => !ppSeleccionadas.has(p.id_pagina));
		mod.paginas.forEach(p => {
			if (marcarTodo) ppSeleccionadas.add(p.id_pagina);
			else ppSeleccionadas.delete(p.id_pagina);
		});
		renderPaginasModulo(ppModuloActualId);
		renderListaModulos();
	}

	async function guardarPermisosPaginas() {
		const clave = document.getElementById('pp-clave').value;
		if (!clave) return;

		const paginas = Array.from(ppSeleccionadas);

		mostrarLoader();
		try {
			const resp = await api({ action: 'guardarPermisosPaginas', clave, paginas });
			ocultarLoader();
			if (resp.ok) {
				Swal.fire({ icon:'success', title:'Guardado', text: resp.msg || 'Permisos actualizados.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}
	/* ════════════════════════════════════════
	MODAL: Facturas pendientes
	════════════════════════════════════════ */
	let infoClaveActual = '';  // guarda la clave al cargar info

	// ← En cargarInfoEmpleado, al inicio agrega: infoClaveActual = clave;

	async function abrirModalFacturas() {
		if (!infoClaveActual) return;
		const overlay = document.getElementById('modal-facturas-overlay');
		overlay.style.display = 'flex';
		document.getElementById('modal-fac-loading').style.display  = 'block';
		document.getElementById('modal-fac-tabla').style.display    = 'none';
		document.getElementById('modal-fac-vacio').style.display    = 'none';
		document.getElementById('modal-fac-alerta-retenido').style.display = 'none';
		document.getElementById('modal-fac-subtitulo').textContent  = infoClaveActual;

		try {
			const resp = await api({ action: 'getFacturasDetalle', clave: infoClaveActual });
			document.getElementById('modal-fac-loading').style.display = 'none';

			if (!resp.ok || !resp.facturas.length) {
				document.getElementById('modal-fac-vacio').style.display = 'block'; return;
			}

			const hayRetenido = resp.facturas.some(f => f.retenido == 1);
			if (hayRetenido) document.getElementById('modal-fac-alerta-retenido').style.display = 'block';

			const tbody = document.getElementById('modal-fac-tbody');
			tbody.innerHTML = resp.facturas.map(f => `
				<tr style="border-bottom:1px solid #f0f0f0;" onmouseenter="this.style.background='#fffbeb'" onmouseleave="this.style.background=''">
					<td style="padding:9px 10px;font-weight:600;color:#1a1a2e;">${f.no_factura || '—'}</td>
					<td style="padding:9px 10px;font-size:11.5px;color:#555;">${f.rfc || '—'}</td>
					<td style="padding:9px 10px;">${f.fec_fac || '—'}</td>
					<td style="padding:9px 10px;color:#888;">${f.fec_subir || '—'}</td>
					<td style="padding:9px 10px;font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${f.nom_pdf}">${f.nom_pdf || '—'}</td>
					<td style="padding:9px 10px;font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${f.nom_xml}">${f.nom_xml || '—'}</td>
					<td style="padding:9px 10px;text-align:center;">
						${f.retenido == 1
							? '<span style="background:#fdeeee;color:#c0392b;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #f3c4c4;">RETENIDO</span>'
							: '<span style="background:#e8f4f8;color:#2B73B6;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #b0d8e8;">PENDIENTE</span>'}
					</td>
				</tr>`).join('');
			document.getElementById('modal-fac-tabla').style.display = 'table';

		} catch (e) {
			document.getElementById('modal-fac-loading').style.display = 'none';
			document.getElementById('modal-fac-vacio').textContent = 'Error al cargar las facturas.';
			document.getElementById('modal-fac-vacio').style.display = 'block';
		}
	}

	/* ════════════════════════════════════════
	MODAL: Adeudos / Préstamos
	════════════════════════════════════════ */
	const fmt = n => parseFloat(n || 0).toLocaleString('es-MX', { style:'currency', currency:'MXN' });

	async function abrirModalAdeudos() {
		if (!infoClaveActual) return;

		const overlay = document.getElementById('modal-adeudos-overlay');
		overlay.style.display = 'flex';

		const elLoading = document.getElementById('modal-ade-loading');
		const elLista   = document.getElementById('modal-ade-lista');
		const elVacio   = document.getElementById('modal-ade-vacio');

		elLoading.style.display = 'block';
		elLista.style.display   = 'none';
		elVacio.style.display   = 'none';
		elLista.innerHTML       = '';
		document.getElementById('modal-ade-subtitulo').textContent = infoClaveActual;

		try {
			const resp = await api({ action: 'getAdeudos', clave: infoClaveActual });
			elLoading.style.display = 'none';

			if (!resp.ok || !resp.prestamos.length) {
				elVacio.style.display = 'block';
				return;
			}

			elLista.innerHTML = resp.prestamos.map(p => {
				const pct        = p.total_pagos_reg > 0 ? Math.round((p.pagados / p.total_pagos_reg) * 100) : 0;
				const adeudo     = (p.pendientes * p.monto_pago).toFixed(2);
				const detenido   = p.detener_cobro == 1;

				/* ── Filas de la tabla de pagos ── */
				const filasHistorial = (p.pagos || []).map(pg => `
					<tr style="border-bottom:1px solid #aecbe0;"
						onmouseenter="this.style.background='#d6e6f2'"
						onmouseleave="this.style.background='${pg.pagado ? '' : '#d6e6f2'}'">
						<td style="padding:7px 10px;font-weight:600;color:#14507a;text-align:center;">${pg.no_pago}</td>
						<td style="padding:7px 10px;${pg.pagado ? 'color:#1e8e58;font-weight:600;' : 'color:#c0392b;font-style:italic;'}">
							${pg.pagado ? pg.fec_pago : '— pendiente —'}
						</td>
						<td style="padding:7px 10px;color:#888;font-size:11px;">${pg.periodo || '—'}</td>
						<td style="padding:7px 10px;text-align:right;font-family:monospace;font-size:12px;">${fmt(pg.monto)}</td>
						<td style="padding:7px 10px;text-align:center;">
							${pg.pagado
								? '<span style="background:#eafaf1;color:#1e8e58;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #b8e8cd;">✓ PAGADO</span>'
								: '<span style="background:#fdeeee;color:#c0392b;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #f3c4c4;">PENDIENTE</span>'}
						</td>
					</tr>`).join('');

				return `
				<div style="background:#faf8ff;border:1px solid #aecbe0;border-left:4px solid #2B73B6;border-radius:10px;padding:16px 18px;">

					<!-- Encabezado del préstamo -->
					<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
						<div>
							<div style="font-size:11px;color:#888;">
								Préstamo #${p.id_prestamos}
								&nbsp;·&nbsp; Capturado: ${p.fec_captura || '—'}
								&nbsp;·&nbsp; Periodo: ${p.periodo || '—'}
							</div>
							<div style="font-size:14px;font-weight:700;color:#2B73B6;margin-top:3px;">${p.motivo || 'Sin motivo'}</div>
						</div>
						<div style="text-align:right;">
							<div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.04em;">Monto original</div>
							<div style="font-size:20px;font-weight:700;color:#2B73B6;">${fmt(p.monto)}</div>
						</div>
					</div>

					<!-- Barra de progreso -->
					<div style="background:#aecbe0;border-radius:20px;height:10px;margin-bottom:6px;overflow:hidden;">
						<div style="background:#2B73B6;height:100%;width:${pct}%;border-radius:20px;transition:width .5s;"></div>
					</div>
					<div style="display:flex;justify-content:space-between;font-size:11px;color:#888;margin-bottom:12px;">
						<span>${p.pagados} de ${p.total_pagos_reg} pagos realizados</span>
						<span style="font-weight:700;">${pct}%</span>
					</div>

					<!-- Chips resumen -->
					<div style="display:flex;flex-wrap:wrap;gap:8px;font-size:12px;margin-bottom:14px;">
						<span style="background:#fff;border:1px solid #aecbe0;border-radius:6px;padding:4px 10px;color:#2B73B6;">
							<strong>${p.pendientes}</strong> pagos pendientes
						</span>
						<span style="background:#fdeeee;border:1px solid #f3c4c4;border-radius:6px;padding:4px 10px;color:#c0392b;font-weight:700;">
							Adeudo: ${fmt(adeudo)}
						</span>
						<span style="background:#f0f6f9;border:1px solid #d8e8ee;border-radius:6px;padding:4px 10px;color:#555;">
							Pago mensual: ${fmt(p.monto_pago)}
						</span>
						<span style="background:#eafaf1;border:1px solid #b8e8cd;border-radius:6px;padding:4px 10px;color:#1e8e58;">
							Pagado: ${fmt(p.monto_pagado)}
						</span>
						${detenido ? '<span style="background:#fff8e8;border:1px solid #f5d97a;border-radius:6px;padding:4px 10px;color:#b37f00;font-weight:700;">⏸ Cobro detenido</span>' : ''}
					</div>

					<!-- Tabla historial de pagos -->
					<div style="border:1px solid #aecbe0;border-radius:8px;overflow:hidden;">
						<div style="background:#d6e6f2;padding:7px 12px;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#2B73B6;">
							Historial de pagos
						</div>
						<div style="max-height:200px;overflow-y:auto;">
							<table style="width:100%;border-collapse:collapse;font-size:12.5px;">
								<thead>
									<tr style="background:#aecbe0;position:sticky;top:0;z-index:1;">
										<th style="padding:7px 10px;text-align:center;color:#14507a;font-weight:700;font-size:11px;">#</th>
										<th style="padding:7px 10px;text-align:left;color:#14507a;font-weight:700;font-size:11px;">Fecha de pago</th>
										<th style="padding:7px 10px;text-align:left;color:#14507a;font-weight:700;font-size:11px;">Periodo</th>
										<th style="padding:7px 10px;text-align:right;color:#14507a;font-weight:700;font-size:11px;">Monto</th>
										<th style="padding:7px 10px;text-align:center;color:#14507a;font-weight:700;font-size:11px;">Estado</th>
									</tr>
								</thead>
								<tbody>${filasHistorial}</tbody>
							</table>
						</div>
					</div>

					${p.observa ? `
					<div style="margin-top:10px;font-size:11.5px;color:#777;border-top:1px dashed #e0d9fa;padding-top:8px;">
						📝 ${p.observa}
					</div>` : ''}

				</div>`;
			}).join('');

			elLista.style.display = 'flex';

		} catch (e) {
			elLoading.style.display = 'none';
			elVacio.textContent     = 'Error al cargar los adeudos.';
			elVacio.style.display   = 'block';
		}
	}

	/* ════════════════════════════════════════
	MODAL: Anticipos (Primer + Segundo, unificados en una sola vista)
	════════════════════════════════════════ */
	function badgeTipoAnticipo(tipo) {
		const esPrimero = tipo === 'Primer anticipo';
		const color = esPrimero ? '#0f7c72' : '#2B73B6';
		const fondo = esPrimero ? '#e6f7f5' : '#d6e6f2';
		const borde = esPrimero ? '#a8ded6' : '#aecbe0';
		return `<span style="background:${fondo};color:${color};font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid ${borde};white-space:nowrap;">${tipo}</span>`;
	}

	async function abrirModalAnticipos() {
		if (!infoClaveActual) return;

		const overlay = document.getElementById('modal-anticipos-overlay');
		overlay.style.display = 'flex';
		document.getElementById('modal-ant-loading').style.display = 'block';
		document.getElementById('modal-ant-tabla').style.display   = 'none';
		document.getElementById('modal-ant-vacio').style.display   = 'none';
		document.getElementById('modal-ant-subtitulo').textContent = infoClaveActual;

		try {
			const resp = await api({ action: 'getAnticiposDetalle', clave: infoClaveActual });
			document.getElementById('modal-ant-loading').style.display = 'none';

			if (!resp.ok) {
				document.getElementById('modal-ant-vacio').textContent = 'Error al cargar los anticipos.';
				document.getElementById('modal-ant-vacio').style.display = 'block';
				return;
			}

			if (!resp.folios.length) {
				document.getElementById('modal-ant-vacio').textContent = 'Sin folios pendientes de anticipo.';
				document.getElementById('modal-ant-vacio').style.display = 'block';
				return;
			}

			const tbody = document.getElementById('modal-ant-tbody');
			tbody.innerHTML = resp.folios.map(f => `
				<tr style="border-bottom:1px solid #f0f0f0;" onmouseenter="this.style.background='#f7fafc'" onmouseleave="this.style.background=''">
					<td style="padding:9px 10px;font-weight:600;color:#1a1a2e;">${f.folio}</td>
					<td style="padding:9px 10px;">${badgeTipoAnticipo(f.tipo)}</td>
					<td style="padding:9px 10px;color:#555;">${f.fec_traspaso || '—'}</td>
					<td style="padding:9px 10px;color:#555;">${f.trabajador || '—'}</td>
					<td style="padding:9px 10px;color:#555;">${f.estatus_confirma || '—'}</td>
				</tr>`).join('');
			document.getElementById('modal-ant-tabla').style.display = 'table';

		} catch (e) {
			document.getElementById('modal-ant-loading').style.display = 'none';
			document.getElementById('modal-ant-vacio').textContent = 'Error al cargar los anticipos.';
			document.getElementById('modal-ant-vacio').style.display = 'block';
		}
	}

	/* ════════════════════════════════════════
	MODAL: Folios de producción (últimos 2 meses, sin rechazados)
	════════════════════════════════════════ */
	function badgeEstatusFolio(estatus) {
		return `<span style="background:#eef6fb;color:#2384c5;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #bfe0f2;white-space:nowrap;">${estatus}</span>`;
	}

	function badgePendientePago(pendiente) {
		if (!pendiente) return '';
		return `<span style="background:#fdeeee;color:#c0392b;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid #f3c4c4;white-space:nowrap;">Pendiente de validar pago</span>`;
	}

	/* Cache de la última consulta + filtro activo. Filtrar es 100%
	   client-side (ya se trajeron todos los folios de una vez).
	   folModalFiltro: null = ver todos, true = solo pendientes de
	   validar pago, false = solo pagados. */
	let folModalDatos  = [];
	let folModalFiltro = null;

	const FOL_FILTRO_A_TARJETA = { true: 'modal-fol-card-pendiente', false: 'modal-fol-card-pagado' };
	const FOL_FILTRO_A_TEXTO   = { true: 'Pendientes de validar pago', false: 'Pagados' };

	function filtrarFolios(pendiente) {
		/* Clic sobre la misma tarjeta ya activa = quitar el filtro */
		folModalFiltro = (folModalFiltro === pendiente) ? null : pendiente;

		const cont = document.getElementById('modal-fol-contadores');
		cont.classList.toggle('fol-contadores-filtrando', folModalFiltro !== null);
		Object.values(FOL_FILTRO_A_TARJETA).forEach(id => {
			document.getElementById(id).classList.remove('fol-activo');
		});
		if (folModalFiltro !== null) {
			document.getElementById(FOL_FILTRO_A_TARJETA[folModalFiltro]).classList.add('fol-activo');
		}

		document.getElementById('modal-fol-filtro-activo-wrap').style.display = folModalFiltro !== null ? 'block' : 'none';
		document.getElementById('modal-fol-filtro-activo-txt').textContent   = folModalFiltro !== null ? FOL_FILTRO_A_TEXTO[folModalFiltro] : '';

		renderTablaFolios();
	}

	function renderTablaFolios() {
		const datos = folModalFiltro === null
			? folModalDatos
			: folModalDatos.filter(f => !!f.pendiente_pago === folModalFiltro);

		const tbody = document.getElementById('modal-fol-tbody');
		const tabla = document.getElementById('modal-fol-tabla');
		const vacio = document.getElementById('modal-fol-vacio');

		if (!datos.length) {
			tabla.style.display = 'none';
			vacio.textContent = folModalFiltro !== null
				? `No hay folios en "${FOL_FILTRO_A_TEXTO[folModalFiltro]}".`
				: 'Este empleado no tiene folios en los últimos 2 meses.';
			vacio.style.display = 'block';
			return;
		}

		vacio.style.display = 'none';
		tbody.innerHTML = datos.map(f => `
			<tr style="border-bottom:1px solid #f0f0f0;" onmouseenter="this.style.background='#f7fafc'" onmouseleave="this.style.background=''">
				<td style="padding:9px 10px;font-weight:600;color:#1a1a2e;">${f.folio}</td>
				<td style="padding:9px 10px;">${badgeEstatusFolio(f.estatus || f.id_estatus_prod)}</td>
				<td style="padding:9px 10px;color:#555;">${f.fec_traspaso || '—'}</td>
				<td style="padding:9px 10px;color:#555;">${f.trabajador || '—'}</td>
				<td style="padding:9px 10px;">${badgePendientePago(f.pendiente_pago)}</td>
			</tr>`).join('');
		tabla.style.display = 'table';
	}

	async function abrirModalFolios() {
		if (!infoClaveActual) return;

		const overlay = document.getElementById('modal-folios-overlay');
		overlay.style.display = 'flex';
		document.getElementById('modal-fol-loading').style.display    = 'block';
		document.getElementById('modal-fol-contadores').style.display = 'none';
		document.getElementById('modal-fol-tabla').style.display      = 'none';
		document.getElementById('modal-fol-vacio').style.display      = 'none';
		document.getElementById('modal-fol-subtitulo').textContent    = infoClaveActual;

		folModalDatos  = [];
		folModalFiltro = null;
		document.getElementById('modal-fol-contadores').classList.remove('fol-contadores-filtrando');
		Object.values(FOL_FILTRO_A_TARJETA).forEach(id => document.getElementById(id).classList.remove('fol-activo'));
		document.getElementById('modal-fol-filtro-activo-wrap').style.display = 'none';

		try {
			const resp = await api({ action: 'getFoliosDetalle', clave: infoClaveActual });
			document.getElementById('modal-fol-loading').style.display = 'none';

			if (!resp.ok) {
				document.getElementById('modal-fol-vacio').textContent = 'Error al cargar los folios.';
				document.getElementById('modal-fol-vacio').style.display = 'block';
				return;
			}

			folModalDatos = resp.folios || [];

			const numPendientes = folModalDatos.filter(f => f.pendiente_pago).length;
			const numPagados    = folModalDatos.length - numPendientes;
			document.getElementById('modal-fol-num-pendiente').textContent = numPendientes;
			document.getElementById('modal-fol-num-pagado').textContent    = numPagados;
			document.getElementById('modal-fol-contadores').style.display  = 'grid';

			renderTablaFolios();

		} catch (e) {
			document.getElementById('modal-fol-loading').style.display = 'none';
			document.getElementById('modal-fol-vacio').textContent = 'Error al cargar los folios.';
			document.getElementById('modal-fol-vacio').style.display = 'block';
		}
	}

	/* ════════════════════════════════════════
	MODAL: Acompañamiento de naps (A cobrar / A pagar)
	════════════════════════════════════════ */
	const ACOMP_CONFIG = {
		cobrar: {
			titulo: '💰 Acompañamiento a cobrar',
			subtitulo: 'Folios procesados por otro asesor usando un nap de',
			colorTexto: '#1e8e58',
			colorFondoHeader: 'linear-gradient(135deg,#eafaf1,#fff)',
			colorFila: '#eafaf1',
			colorBadgeBg: '#eafaf1',
			colorBadgeBorder: '#b8e8cd',
			colorBtn: '#1e8e58',
			vacioTexto: 'Nada por cobrar por acompañamiento de naps.',
		},
		pagar: {
			titulo: '💸 Acompañamiento a pagar',
			subtitulo: 'Folios procesados usando un nap que no es de',
			colorTexto: '#c0392b',
			colorFondoHeader: 'linear-gradient(135deg,#fdeeee,#fff)',
			colorFila: '#fdeeee',
			colorBadgeBg: '#fdeeee',
			colorBadgeBorder: '#f3c4c4',
			colorBtn: '#c0392b',
			vacioTexto: 'Nada por pagar por acompañamiento de naps.',
		}
	};

	async function abrirModalAcompanamiento(tipo) {
		if (!infoClaveActual) return;
		const cfg = ACOMP_CONFIG[tipo];
		if (!cfg) return;

		const overlay = document.getElementById('modal-acomp-overlay');
		overlay.style.display = 'flex';

		document.getElementById('modal-acomp-header').style.background = cfg.colorFondoHeader;
		document.getElementById('modal-acomp-titulo').textContent = cfg.titulo;
		document.getElementById('modal-acomp-titulo').style.color = cfg.colorTexto;
		document.getElementById('modal-acomp-subtitulo').textContent = cfg.subtitulo + ' ' + infoClaveActual;
		document.getElementById('modal-acomp-btn-cerrar').style.background = cfg.colorBtn;

		document.getElementById('modal-acomp-loading').style.display = 'block';
		document.getElementById('modal-acomp-resumen').style.display = 'none';
		document.getElementById('modal-acomp-tabla').style.display   = 'none';
		document.getElementById('modal-acomp-vacio').style.display   = 'none';

		try {
			const resp = await api({ action: 'getAcompanamientoDetalle', clave: infoClaveActual });
			document.getElementById('modal-acomp-loading').style.display = 'none';

			if (!resp.ok) {
				document.getElementById('modal-acomp-vacio').textContent = 'Error al cargar la información.';
				document.getElementById('modal-acomp-vacio').style.display = 'block';
				return;
			}

			const folios = resp[tipo] || [];

			if (!folios.length) {
				document.getElementById('modal-acomp-vacio').textContent = cfg.vacioTexto;
				document.getElementById('modal-acomp-vacio').style.display = 'block';
				return;
			}

			const totalMonto = folios.reduce((acc, f) => acc + (parseFloat(f.costo) || 0), 0);
			document.getElementById('modal-acomp-total-folios').textContent = folios.length;
			document.getElementById('modal-acomp-total-monto').textContent = fmt(totalMonto);
			document.getElementById('modal-acomp-resumen').style.display = 'flex';

			const tbody = document.getElementById('modal-acomp-tbody');
			tbody.innerHTML = folios.map(f => `
				<tr style="border-bottom:1px solid #f0f0f0;" onmouseenter="this.style.background='${cfg.colorFila}'" onmouseleave="this.style.background=''">
					<td style="padding:9px 10px;font-weight:600;color:#1a1a2e;">${f.folio}</td>
					<td style="padding:9px 10px;color:#555;">${f.fec_pago || '—'}</td>
					<td style="padding:9px 10px;text-align:right;font-family:monospace;">${fmt(f.costo)}</td>
				</tr>`).join('');
			document.getElementById('modal-acomp-tabla').style.display = 'table';

		} catch (e) {
			document.getElementById('modal-acomp-loading').style.display = 'none';
			document.getElementById('modal-acomp-vacio').textContent = 'Error al cargar la información.';
			document.getElementById('modal-acomp-vacio').style.display = 'block';
		}
	}

	/* ════════════════════════════════════════
	   Generador de VARIANTES de clave (estilo Google)
	   Ej: ERICK MADRIGAL FLORES →
	       ERMADRIGFL, EMADRIGALF, EMADRIGAFL, ERIMADRIGAL
	════════════════════════════════════════ */
	function construirVariantesClave(nombreCompleto) {
		const partes = nombreCompleto.trim().toUpperCase().replace(/\s+/g, ' ').split(' ').filter(p => p.length > 0);
		const total  = partes.length;
		if (total === 0) return [];

		const variantes = new Set();

		if (total === 1) {
			variantes.add(partes[0].substring(0, 10));
		} else if (total === 2) {
			const [nom, ape] = partes;
			variantes.add((nom.substring(0,2) + ape).substring(0,10));
			variantes.add((nom.substring(0,1) + ape).substring(0,10));
			variantes.add((nom.substring(0,3) + ape).substring(0,10));
			variantes.add((ape.substring(0,2) + nom).substring(0,10));
		} else {
			const nom = partes[0], ape1 = partes[1], extra = partes[2];
			variantes.add((nom.substring(0,2) + ape1 + extra.charAt(0)).substring(0,10));
			variantes.add((nom.substring(0,1) + ape1 + extra.substring(0,2)).substring(0,10));
			variantes.add((nom.substring(0,1) + ape1 + extra.charAt(0)).substring(0,10));
			variantes.add((nom.substring(0,3) + ape1).substring(0,10));
		}

		return Array.from(variantes).slice(0, 4);
	}

	let timerNombre = null;

	/* El campo "Nombre completo" (nvo-nombre) queda oculto en el alta;
	   se compone automáticamente a partir de Nombre(s)/Ap. paterno/
	   Ap. materno cada vez que el usuario captura ahí, y dispara la
	   misma búsqueda de clave que antes disparaba el campo único. */
	function onNombreSeparadoAltaInput() {
		const nombres = document.getElementById('nvo-nombres').value.trim();
		const apPat   = document.getElementById('nvo-apellido-paterno').value.trim();
		const apMat   = document.getElementById('nvo-apellido-materno').value.trim();
		const compuesto = [nombres, apPat, apMat].filter(Boolean).join(' ');
		document.getElementById('nvo-nombre').value = compuesto;
		onNombreInput();
	}

	function onNombreInput() {
		clearTimeout(timerNombre);
		const nombre = document.getElementById('nvo-nombre').value.trim();
		const wrap   = document.getElementById('clave-opciones-wrap');

		if (!nombre) {
			wrap.innerHTML = '<span style="font-size:12px;color:#aaa;">Escribe el nombre para ver opciones de clave...</span>';
			document.getElementById('nvo-clave').value = '';
			return;
		}

		wrap.innerHTML = '<span style="font-size:12px;color:#aaa;">Buscando claves disponibles...</span>';

		timerNombre = setTimeout(async () => {
			const variantes = construirVariantesClave(nombre);
			if (variantes.length === 0) return;

			try {
				const resp = await api({ action: 'verificarClaves', claves: variantes });
				const disponibles = resp.disponibles || {};
				renderOpcionesClave(variantes, disponibles);
			} catch (e) {
				renderOpcionesClave(variantes, {});
			}
		}, 400);
	}

	function renderOpcionesClave(variantes, disponibles) {
		const wrap = document.getElementById('clave-opciones-wrap');
		wrap.innerHTML = '';

		let primeraDisponibleMarcada = false;

		variantes.forEach((clave, idx) => {
			const libre = disponibles[clave] !== false; /* true u undefined = libre */
			const div = document.createElement('label');
			div.className = 'clave-opcion' + (!libre ? '' : '');
			div.style.opacity = libre ? '1' : '.5';
			div.style.cursor  = libre ? 'pointer' : 'not-allowed';

			const checked = (libre && !primeraDisponibleMarcada) ? 'checked' : '';
			if (libre && checked) primeraDisponibleMarcada = true;

			div.innerHTML = `
				<input type="radio" name="clave-radio" value="${clave}" ${checked} ${libre ? '' : 'disabled'}
					   onchange="document.getElementById('nvo-clave').value='${clave}'; marcarSeleccionClave(this)">
				<span>${clave}</span>
				${!libre ? '<span style="font-size:10px;color:#c0392b;margin-left:auto;">no disponible</span>' : ''}
			`;
			wrap.appendChild(div);

			if (checked) {
				div.classList.add('seleccionada');
				document.getElementById('nvo-clave').value = clave;
			}
		});

		if (!primeraDisponibleMarcada) {
			const aviso = document.createElement('span');
			aviso.style.fontSize = '12px';
			aviso.style.color = '#c0392b';
			aviso.textContent = 'Todas las opciones generadas ya existen, intenta variar el nombre.';
			wrap.appendChild(aviso);
		}
	}

	function marcarSeleccionClave(radio) {
		document.querySelectorAll('.clave-opcion').forEach(el => el.classList.remove('seleccionada'));
		radio.closest('.clave-opcion').classList.add('seleccionada');
	}

	/* ════════════════════════════════════════
	   Generador de PASSWORD (random, para reset)
	   Formato: XxXxXx-_NN  Ej: VuSuAe-_19
	════════════════════════════════════════ */
	const SEPARADORES = ['-_', '._', '-*', '.*', '-#', '._'];

	function generarPwd() {
		const cons = 'bcdfghjklmnpqrstvwxyz';
		const voc  = 'aeiou';
		let chars  = '';
		for (let i = 0; i < 3; i++) {
			const c = cons[Math.floor(Math.random() * cons.length)];
			const v = voc[Math.floor(Math.random() * voc.length)];
			chars += c.toUpperCase() + v;
		}
		const sep = SEPARADORES[Math.floor(Math.random() * SEPARADORES.length)];
		const num = String(Math.floor(Math.random() * 90) + 10);
		return chars + sep + num;
	}

	/* ════════════════════════════════════════
	   Reset de contraseña (con confirmación simple)
	════════════════════════════════════════ */
	async function iniciarResetPassword() {
		const result = await Swal.fire({
			title: '¿Resetear contraseña?',
			html: `
				<p style="font-size:13px;color:#555;margin-bottom:10px;">Se generará una nueva contraseña y se enviará por correo al empleado.</p>
				<label style="font-size:12px;color:#888;display:block;text-align:left;margin-bottom:4px;">Correo en copia (opcional)</label>
				<input type="email" id="swal-email-copia" class="swal2-input" placeholder="correo@ejemplo.com" style="margin:0;width:90%;">
			`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Sí, resetear',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6',
			preConfirm: () => {
				return document.getElementById('swal-email-copia').value.trim();
			}
		});

		if (!result.isConfirmed) return;

		const emailCopia = result.value || '';

		const nuevaPwd = generarPwd();
		const id = document.getElementById('emp-id').value;

		mostrarLoader();
		try {
			const resp = await api({ action: 'guardarEmpleado', id, nombre:'', email:'', clave:'', password: nuevaPwd, activo: document.getElementById('emp-activo-ed').checked ? 1 : 0, email_copia: emailCopia });
			ocultarLoader();
			if (resp.ok) {
				Swal.fire({ icon:'success', title:'Contraseña actualizada', text:'Se envió la nueva contraseña por correo.', timer:2000, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Typeahead búsqueda de empleados
	════════════════════════════════════════ */
	let timerBusq = null;
	const inpBusq = document.getElementById('inp-busqueda');
	const dropBusq = document.getElementById('dropdown-resultados');

	function resaltarTexto(texto, q) {
		if (!q) return texto;
		const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
		return texto.replace(re, '<span class="nap-ta-highlight">$1</span>');
	}

	inpBusq.addEventListener('keyup', function (e) {
		if ([13, 27, 38, 40].indexOf(e.keyCode) !== -1) return;
		clearTimeout(timerBusq);
		const q = this.value.trim();

		if (q.length < 2) { cerrarDropdown(); return; }

		dropBusq.innerHTML = '<div class="nap-ta-cargando">Buscando...</div>';
		dropBusq.classList.add('open');

		timerBusq = setTimeout(async () => {
			try {
				const resp = await api({ action: 'buscarAsesor', q });
				renderDropdown(resp.datos || [], q);
			} catch (e) {
				cerrarDropdown();
			}
		}, 300);
	});

	inpBusq.addEventListener('keydown', function (e) {
		const items = dropBusq.querySelectorAll('.nap-ta-item');
		if (!items.length) return;
		let activo = dropBusq.querySelector('.nap-ta-item.activo');
		let idx = -1;
		items.forEach((el, i) => { if (el === activo) idx = i; });

		if (e.keyCode === 40) {
			e.preventDefault();
			if (activo) activo.classList.remove('activo');
			(items[idx + 1] || items[0]).classList.add('activo');
		} else if (e.keyCode === 38) {
			e.preventDefault();
			if (activo) activo.classList.remove('activo');
			(items[idx - 1] || items[items.length - 1]).classList.add('activo');
		} else if (e.keyCode === 13 && activo) {
			e.preventDefault();
			activo.dispatchEvent(new Event('mousedown'));
		} else if (e.keyCode === 27) {
			cerrarDropdown();
		}
	});

	inpBusq.addEventListener('blur', function () {
		setTimeout(() => cerrarDropdown(), 200);
	});

	document.addEventListener('click', function (e) {
		if (!e.target.closest('.nap-ta-wrapper')) cerrarDropdown();
	});

	function cerrarDropdown() {
		dropBusq.classList.remove('open');
		dropBusq.innerHTML = '';
	}

	function renderDropdown(lista, q) {
		dropBusq.innerHTML = '';

		if (lista.length === 0) {
			dropBusq.innerHTML = '<div class="nap-ta-vacio">Sin resultados para "' + q + '"</div>';
			dropBusq.classList.add('open');
			return;
		}

		lista.forEach(emp => {
			const item = document.createElement('div');
			item.className = 'nap-ta-item';
			const badgeInactivo = (emp.activo == 0) ? '<span style="font-size:10px;background:#fdeeee;color:#c0392b;border-radius:4px;padding:2px 6px;margin-left:6px;font-weight:700;">INACTIVO</span>' : '';
			item.innerHTML = '<span class="nap-ta-nombre">' + resaltarTexto(emp.nombre, q) + badgeInactivo + '</span>'
			               + '<span class="nap-ta-clave">' + emp.clave + '</span>';
			item.addEventListener('mousedown', () => {
				seleccionarEmpleado(emp.id);
				cerrarDropdown();
				inpBusq.value = emp.nombre;
				// badge
				document.getElementById('emp-badge-txt').textContent = emp.nombre + ' (' + emp.clave + ')';
				document.getElementById('emp-badge-wrap').style.display = 'block';
			});
			dropBusq.appendChild(item);
		});

		dropBusq.classList.add('open');
	}

	function limpiarBusqueda() {
		inpBusq.value = '';
		document.getElementById('emp-badge-wrap').style.display = 'none';
		document.getElementById('emp-badge-txt').textContent = '';
		cerrarPanel();
	}

	/* ════════════════════════════════════════
	   Seleccionar empleado
	════════════════════════════════════════ */
	async function seleccionarEmpleado(id) {
		mostrarLoader();
		try {
			const resp = await api({ action: 'getEmpleado', id });
			ocultarLoader();
			if (resp.ok) {
				cargarFormulario(resp.empleado);
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo cargar el empleado.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	function cargarFormulario(emp) {
		/* Modo edición: solo activo + password */
		document.getElementById('emp-id').value    = emp.id     || '';
		document.getElementById('emp-nombre').value = emp.nombre || '';
		document.getElementById('emp-email').value  = emp.email  || '';
		document.getElementById('emp-clave').value  = emp.clave  || '';
		document.getElementById('emp-nombre-ro').value = emp.nombre || '';
		document.getElementById('emp-clave-ro').value  = emp.clave  || '';
		document.getElementById('emp-activo-ed').checked = emp.activo == 1;
		document.getElementById('sec-nuevo').style.display  = 'none';
		document.getElementById('sec-editar').style.display = 'block';
		actualizarEstado();
		mostrarPanel();
		activarTab('pane-datos');
		cargarNombreSeparado(emp.clave);

		/* Habilitar y cargar pestaña Datos generales */
		document.getElementById('dg-sin-empleado').style.display = 'none';
		document.getElementById('dg-form').style.display = 'block';
		document.getElementById('dg-clave').value = emp.clave || '';
		cargarDatosGenerales(emp.clave);

		/* Habilitar y cargar pestaña Datos bancarios */
		document.getElementById('bnc-sin-empleado').style.display = 'none';
		document.getElementById('bnc-contenido').style.display = 'block';
		cargarCuentasBancarias(emp.clave);

		/* Habilitar y cargar pestaña Estructura de empleados */
		document.getElementById('est-sin-empleado').style.display = 'none';
		document.getElementById('est-contenido').style.display = 'block';
		cargarEstructuraEmpleado(emp.clave);

		/* Habilitar y cargar pestaña Finanzas */
		document.getElementById('fin-sin-empleado').style.display = 'none';
		document.getElementById('fin-contenido').style.display = 'block';
		cargarFinanzas(emp.clave);

		/* Habilitar y cargar pestaña Historial */
		document.getElementById('hist-sin-empleado').style.display = 'none';
		document.getElementById('hist-contenido').style.display = 'block';
		cargarHistorialMovimientos(emp.clave);

		/* Habilitar y cargar pestaña Contactos de emergencia */
		document.getElementById('ce-sin-empleado').style.display = 'none';
		document.getElementById('ce-contenido').style.display = 'block';
		cargarContactosEmergencia(emp.clave);

		document.getElementById('info-sin-empleado').style.display = 'none';
		document.getElementById('info-contenido').style.display    = 'block';
		cargarInfoEmpleado(emp.clave)

		/* Habilitar y cargar pestaña Expediente Digital */
		document.getElementById('exd-sin-empleado').style.display = 'none';
		document.getElementById('exd-contenido').style.display = 'block';
		cargarExpedienteDigital(emp.clave);

		/* Habilitar y cargar la sección individual de la pestaña Permisos
		   (la sección de lote por nivel no depende de tener un empleado
		   seleccionado, así que no se toca aquí) */
		cargarPermisosIndividual(emp.clave, emp.nombre);
		/* Habilitar y cargar pestaña Permisos por página — Super Usuario,
		   o cualquier empleado al que se le haya otorgado la pestaña
		   "Permisos" desde el modal de gestión de pestañas (misma lógica
		   que ve/oculta la pestaña en inicializarVisibilidadPestanas). */
		const permitidasSesionPP = Array.isArray(window.NAP_PESTANAS_PERMITIDAS) ? window.NAP_PESTANAS_PERMITIDAS : [];
		if (window.NAP_ES_SUPERUSUARIO || permitidasSesionPP.indexOf('permisos') !== -1) {
			document.getElementById('pp-sin-empleado').style.display = 'none';
			document.getElementById('pp-contenido').style.display = 'block';
			cargarPermisosPaginas(emp.clave, emp.nombre);
		}
	}

	/* ════════════════════════════════════════
	   Nombre separado (temporal, pestaña Usuario / edición)
	   carobra.usuarios solo tiene el nombre completo; esto vive
	   aparte en usuarios_nombre_separado, sin tocar esa tabla.
	════════════════════════════════════════ */
	async function cargarNombreSeparado(clave) {
		document.getElementById('emp-nombres').value = '';
		document.getElementById('emp-apellido-paterno').value = '';
		document.getElementById('emp-apellido-materno').value = '';

		try {
			const resp = await api({ action: 'getNombreSeparado', clave });
			if (!resp.ok) return;

			document.getElementById('emp-nombres').value          = resp.nombres          || '';
			document.getElementById('emp-apellido-paterno').value = resp.apellido_paterno || '';
			document.getElementById('emp-apellido-materno').value = resp.apellido_materno || '';
		} catch (e) {
			console.error('Error cargando nombre separado:', e);
		}
	}

	/* NOTA: ya no existe un botón/función aparte para guardar el nombre
	   separado en edición — se quitó a propósito para no confundir a
	   quien captura con dos botones de "Guardar". El único botón
	   "Guardar" del formulario de edición (ver guardarEmpleado()) ahora
	   manda también nombres/apellido_paterno/apellido_materno junto con
	   el resto, y el backend los guarda en la misma llamada. */

	/* ════════════════════════════════════════
	   Nuevo empleado
	════════════════════════════════════════ */
	function nuevoEmpleado() {
		document.getElementById('emp-id').value    = '';
		document.getElementById('nvo-nombre').value = '';
		document.getElementById('nvo-nombres').value = '';
		document.getElementById('nvo-apellido-paterno').value = '';
		document.getElementById('nvo-apellido-materno').value = '';
		document.getElementById('nvo-email').value  = '';
		document.getElementById('nvo-clave').value  = '';
		const chk = document.getElementById('emp-activo');
		if (chk) chk.checked = true;
		actualizarEstado();
		document.getElementById('sec-nuevo').style.display  = 'block';
		document.getElementById('sec-editar').style.display = 'none';
		mostrarPanel();
		activarTab('pane-datos');
		document.getElementById('nvo-nombres').focus();

		/* Pestaña Datos generales deshabilitada hasta guardar el alta */
		document.getElementById('dg-sin-empleado').style.display = 'block';
		document.getElementById('dg-form').style.display = 'none';
		document.getElementById('dg-clave').value = '';

		/* Pestaña Datos bancarios deshabilitada hasta guardar el alta */
		document.getElementById('bnc-sin-empleado').style.display = 'block';
		document.getElementById('bnc-contenido').style.display = 'none';
		bncCuentas = [];

		/* Pestaña Estructura de empleados deshabilitada hasta guardar el alta */
		document.getElementById('est-sin-empleado').style.display = 'block';
		document.getElementById('est-contenido').style.display = 'none';

		/* Pestaña Finanzas deshabilitada hasta guardar el alta */
		document.getElementById('fin-sin-empleado').style.display = 'block';
		document.getElementById('fin-contenido').style.display = 'none';

		/* Pestaña Historial deshabilitada hasta guardar el alta */
		document.getElementById('hist-sin-empleado').style.display = 'block';
		document.getElementById('hist-contenido').style.display = 'none';

		/* Pestaña Contactos de emergencia deshabilitada hasta guardar el alta */
		document.getElementById('ce-sin-empleado').style.display = 'block';
		document.getElementById('ce-contenido').style.display = 'none';

		document.getElementById('info-sin-empleado').style.display = 'block';
		document.getElementById('info-contenido').style.display = 'none';

		/* Pestaña Expediente Digital deshabilitada hasta guardar el alta */
		document.getElementById('exd-sin-empleado').style.display = 'block';
		document.getElementById('exd-contenido').style.display = 'none';
		document.getElementById('exd-lista').innerHTML = '';
		exdDocumentos = [];
		exdIdActual = null;

		/* Pestaña Permisos (sección individual): se limpia la búsqueda propia
		   también, para no dejar cargado a otro usuario mientras se da de
		   alta uno nuevo */
		document.getElementById('perm-ind-search').value = '';
		document.getElementById('perm-ind-clave').value = '';
		document.getElementById('perm-ind-sin-empleado').style.display = 'block';
		document.getElementById('perm-ind-contenido').style.display = 'none';

		/* Pestaña Permisos por página deshabilitada hasta guardar el alta */
		document.getElementById('pp-sin-empleado').style.display = 'block';
		document.getElementById('pp-contenido').style.display = 'none';
		document.getElementById('pp-clave').value = '';
	}

	/* ════════════════════════════════════════
	   Datos generales (pestaña 2) — cargar / guardar
	════════════════════════════════════════ */
	async function cargarDatosGenerales(clave) {
		try {
			const resp = await api({ action: 'getDatosGenerales', clave });
			if (!resp.ok) return;
			const d = resp.datos || {};

			document.getElementById('dg-fec-ingreso').value    = d.fec_ingreso    || '';
			document.getElementById('dg-fec-reingreso').value  = d.fec_reingreso  || '';
			document.getElementById('dg-fec-baja').value       = d.fec_baja       || '';
			document.getElementById('dg-staff').checked        = d.staff == 1;
			document.getElementById('dg-espartano').checked    = d.espartano == 1;
			document.getElementById('lbl-espartano').textContent = (d.espartano == 1) ? 'Sí' : 'No';
			document.getElementById('dg-jefe-clave').value     = d.jefe_inmediato || '';
			document.getElementById('dg-jefe-search').value    = d.jefe_nombre    || d.jefe_inmediato || '';
			document.getElementById('dg-cp').value             = d.cp             || '';
			document.getElementById('dg-calle').value          = d.calle          || '';
			document.getElementById('dg-telefono').value       = d.telefono       || '';
			document.getElementById('dg-celular').value        = d.celular        || '';
			document.getElementById('dg-email-personal').value = d.email_personal || '';
			document.getElementById('dg-email-inst').value     = d.email_inst     || '';
			document.getElementById('dg-nss').value            = d.nss            || '';
			document.getElementById('dg-rfc').value            = d.rfc            || '';
			document.getElementById('dg-curp').value           = d.curp           || '';
			document.getElementById('dg-nap').value             = d.nap            || '';
			document.getElementById('dg-fec-alta-nap').value    = d.fec_alta_nap   || '';
			document.getElementById('dg-recluta-clave').value   = d.recluta_clave  || '';
			document.getElementById('dg-recluta-search').value  = d.recluta_nombre || d.recluta_clave || '';

			toggleJefeInmediato();

			/* Si hay CP guardado, cargar Estado/Municipio/Colonias y preseleccionar la colonia guardada */
			if (d.cp && d.cp.length === 5) {
				await buscarPorCP(d.cp, d.colonia || '');
			} else {
				document.getElementById('dg-estado').value = '';
				document.getElementById('dg-municipio').value = '';
				document.getElementById('dg-colonia').innerHTML = '<option value="">Escribe primero el C.P.</option>';
			}
		} catch (e) {
			console.error('Error cargando datos generales:', e);
		}
	}

	/* ════════════════════════════════════════
	   Domicilio — autocompletar Estado/Municipio/Colonia por CP
	════════════════════════════════════════ */
	let timerCp = null;

	function onCpInput(inp) {
		inp.value = inp.value.replace(/\D/g, '').substring(0, 5);
		clearTimeout(timerCp);

		const hint = document.getElementById('dg-cp-hint');

		if (inp.value.length < 5) {
			document.getElementById('dg-estado').value = '';
			document.getElementById('dg-municipio').value = '';
			document.getElementById('dg-colonia').innerHTML = '<option value="">Escribe primero el C.P.</option>';
			hint.textContent = inp.value.length > 0 ? (inp.value.length + ' / 5 dígitos') : '';
			hint.style.color = '#c0392b';
			return;
		}

		hint.textContent = 'Buscando...';
		hint.style.color = '#888';

		timerCp = setTimeout(() => buscarPorCP(inp.value), 300);
	}

	async function buscarPorCP(cp, coloniaPreseleccionar) {
		const hint = document.getElementById('dg-cp-hint');
		const selColonia = document.getElementById('dg-colonia');

		try {
			const resp = await api({ action: 'buscarPorCP', cp });

			if (!resp.ok || !resp.colonias || resp.colonias.length === 0) {
				document.getElementById('dg-estado').value = '';
				document.getElementById('dg-municipio').value = '';
				selColonia.innerHTML = '<option value="">CP no encontrado</option>';
				hint.textContent = 'CP no encontrado en el catálogo';
				hint.style.color = '#c0392b';
				return;
			}

			document.getElementById('dg-estado').value    = resp.estado    || '';
			document.getElementById('dg-municipio').value = resp.municipio || '';

			selColonia.innerHTML = '<option value="">— Selecciona colonia —</option>';
			resp.colonias.forEach(col => {
				const opt = document.createElement('option');
				opt.value = col;
				opt.textContent = col;
				if (coloniaPreseleccionar && col === coloniaPreseleccionar) opt.selected = true;
				selColonia.appendChild(opt);
			});

			/* Si la colonia guardada no aparece en la lista (CP cambió de catálogo), agregarla al final */
			if (coloniaPreseleccionar && !resp.colonias.includes(coloniaPreseleccionar)) {
				const opt = document.createElement('option');
				opt.value = coloniaPreseleccionar;
				opt.textContent = coloniaPreseleccionar + ' (no está en catálogo actual)';
				opt.selected = true;
				selColonia.appendChild(opt);
			}

			hint.textContent = '✓ ' + resp.colonias.length + ' colonia(s) encontradas';
			hint.style.color = '#1e6e42';

		} catch (e) {
			hint.textContent = 'Error al buscar CP';
			hint.style.color = '#c0392b';
		}
	}

	function toggleJefeInmediato() {
		const activo = document.getElementById('dg-staff').checked;
		document.getElementById('lbl-staff').textContent = activo ? 'Sí' : 'No';
		document.getElementById('dg-row-jefe').style.display = activo ? 'flex' : 'none';
		if (!activo) {
			document.getElementById('dg-jefe-search').value = '';
			document.getElementById('dg-jefe-clave').value  = '';
		}
	}

	function toggleEspartano() {
		const activo = document.getElementById('dg-espartano').checked;
		document.getElementById('lbl-espartano').textContent = activo ? 'Sí' : 'No';
	}

	async function cargarInfoEmpleado(clave) {
		document.getElementById('info-loading').style.display    = 'block';
		document.getElementById('info-error').style.display      = 'none';
		document.getElementById('info-card-insuf').style.display = 'none';
		document.getElementById('info-card-ok').style.display    = 'none';
		// document.getElementById('info-periodo-badge').textContent = '—';
		document.getElementById('info-facturas-num').style.display = 'block';
		document.getElementById('info-facturas-ok').style.display  = 'none';
		document.getElementById('info-card-facturas').style.background      = '#fff8e8';
		document.getElementById('info-card-facturas').style.borderLeftColor = '#e0a800';
		document.getElementById('info-card-acomp-cobrar').style.display = 'none';
		document.getElementById('info-card-acomp-pagar').style.display  = 'none';

		try {
			infoClaveActual = clave;

			const resp = await api({ action: 'getInfoEmpleado', clave });
			document.getElementById('info-loading').style.display = 'none';

			if (!resp.ok) {
				document.getElementById('info-error').style.display  = 'block';
				document.getElementById('info-error').textContent = resp.msg || 'Error al cargar.';
				return;
			}

			// document.getElementById('info-periodo-badge').textContent = resp.periodo || '—';

			if (resp.insuficiencia) {
				const ajuste = parseFloat(resp.insuficiencia.ajuste || 0);
				// document.getElementById('info-insuf-motivo').textContent = resp.insuficiencia.motivo || '';
				document.getElementById('info-insuf-ajuste').textContent =
					ajuste.toLocaleString('es-MX', { style:'currency', currency:'MXN' });
				document.getElementById('info-card-insuf').style.display = 'block';
			} else {
				document.getElementById('info-card-ok').style.display = 'block';
			}

			const numFac = resp.facturas_pendientes || 0;
			document.getElementById('info-facturas-num').textContent = numFac;
			if (numFac > 0) {
				document.getElementById('info-facturas-num').style.display = 'block';
				document.getElementById('info-facturas-ok').style.display  = 'none';
				document.getElementById('info-card-facturas').style.background      = '#fff8e8';
				document.getElementById('info-card-facturas').style.borderLeftColor = '#e0a800';
			} else {
				document.getElementById('info-facturas-num').style.display = 'none';
				document.getElementById('info-facturas-ok').style.display  = 'block';
				document.getElementById('info-card-facturas').style.background      = '#eafaf1';
				document.getElementById('info-card-facturas').style.borderLeftColor = '#1e8e58';
			}

			const numAde = resp.adeudos_pendientes || 0;
			document.getElementById('info-adeudos-num').textContent = numAde;
			if (numAde > 0) {
				document.getElementById('info-adeudos-num').style.display = 'block';
				document.getElementById('info-adeudos-ok').style.display  = 'none';
				document.getElementById('info-card-adeudos').style.background      = '#d6e6f2';
				document.getElementById('info-card-adeudos').style.borderLeftColor = '#2B73B6';
			} else {
				document.getElementById('info-adeudos-num').style.display = 'none';
				document.getElementById('info-adeudos-ok').style.display  = 'block';
				document.getElementById('info-card-adeudos').style.background      = '#eafaf1';
				document.getElementById('info-card-adeudos').style.borderLeftColor = '#1e8e58';
			}

			const numAnt = resp.anticipos_pendientes || 0;
			document.getElementById('info-anticipos-num').textContent = numAnt;
			if (numAnt > 0) {
				document.getElementById('info-anticipos-num').style.display = 'block';
				document.getElementById('info-anticipos-ok').style.display  = 'none';
				document.getElementById('info-card-anticipos').style.background      = '#e6f7f5';
				document.getElementById('info-card-anticipos').style.borderLeftColor = '#0f9b8e';
			} else {
				document.getElementById('info-anticipos-num').style.display = 'none';
				document.getElementById('info-anticipos-ok').style.display  = 'block';
				document.getElementById('info-card-anticipos').style.background      = '#eafaf1';
				document.getElementById('info-card-anticipos').style.borderLeftColor = '#1e8e58';
			}

			/* Las dos cards se muestran siempre juntas: un empleado puede
			   tener folios por cobrar (nap suyo usado por otro) y folios
			   por pagar (él usó un nap ajeno) al mismo tiempo. */
			document.getElementById('info-card-acomp-cobrar').style.display = 'block';
			document.getElementById('info-card-acomp-pagar').style.display  = 'block';

			const numAcompCobrar = resp.acompanamiento_cobrar || 0;
			document.getElementById('info-acomp-cobrar-num').textContent = numAcompCobrar;
			document.getElementById('info-acomp-cobrar-num').style.display = numAcompCobrar > 0 ? 'block' : 'none';
			document.getElementById('info-acomp-cobrar-ok').style.display  = numAcompCobrar > 0 ? 'none' : 'block';

			const numAcompPagar = resp.acompanamiento_pagar || 0;
			document.getElementById('info-acomp-pagar-num').textContent = numAcompPagar;
			document.getElementById('info-acomp-pagar-num').style.display = numAcompPagar > 0 ? 'block' : 'none';
			document.getElementById('info-acomp-pagar-ok').style.display  = numAcompPagar > 0 ? 'none' : 'block';

			/* Folios de producción: el número de la card es solo "Por
			   cobrar" (id_estatus_prod distinto de 6 y de 1); Rechazados
			   (6) y Pagados (1) se ven en el detalle, no en la card. */
			const numFolios = resp.folios_por_cobrar || 0;
			document.getElementById('info-folios-num').textContent = numFolios;
			if (numFolios > 0) {
				document.getElementById('info-folios-num').style.display = 'block';
				document.getElementById('info-folios-ok').style.display  = 'none';
				document.getElementById('info-card-folios').style.background      = '#d6e6f2';
				document.getElementById('info-card-folios').style.borderLeftColor = '#2B73B6';
			} else {
				document.getElementById('info-folios-num').style.display = 'none';
				document.getElementById('info-folios-ok').style.display  = 'block';
				document.getElementById('info-card-folios').style.background      = '#eafaf1';
				document.getElementById('info-card-folios').style.borderLeftColor = '#1e8e58';
			}

		} catch (e) {
			document.getElementById('info-loading').style.display = 'none';
			document.getElementById('info-error').style.display   = 'block';
			document.getElementById('info-error').textContent = 'Error de comunicación con el servidor.';
		}
	}

	/* ════════════════════════════════════════
	   Expediente Digital (pestaña 10)
	   Ya NO usa la tabla carobra_2014.expediente_digital (se dio de
	   baja). Ahora se lee/escribe contra expedientes_sisca_api.php,
	   que trabaja sobre carobra_2014.expedientes / expediente_docs
	   y liga el expediente al empleado por clave_sisca = clave.
	════════════════════════════════════════ */
	const EXPEDIENTES_SISCA_API = 'expedientes_sisca_api.php';

	async function apiSisca(payload) {
		const resp = await fetch(EXPEDIENTES_SISCA_API, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	let exdClaveActual = '';
	let exdDocumentos   = [];
	let exdIdActual     = null;
	let exdInfoActual   = null;

	const EXD_ESTATUS_LABEL = { pendiente: 'Pendiente', aprobado: 'Aprobado', rechazado: 'Rechazado' };
	const EXD_ESTATUS_NUM   = { pendiente: 0, aprobado: 1, rechazado: 2 };

	/* Etiquetas legibles para los tipo_doc que vienen de la app/portal SISCA */
	const EXD_TIPO_DOC_LABEL = {
		ine_frente: 'INE (frente)',
		ine_reverso: 'INE (reverso)',
		curp: 'CURP',
		situacion_fiscal: 'Constancia fiscal',
		comprobante_domicilio: 'Comprobante domicilio',
		estado_cuenta: 'Estado de cuenta',
		ine_representante: 'INE representante legal',
		comprobante_domicilio_fiscal: 'Comprobante domicilio fiscal',
		estado_cuenta_moral: 'Estado de cuenta (moral)',
		acta_constitutiva: 'Acta constitutiva',
		rfc_empresa: 'RFC empresa',
	};

	async function cargarExpedienteDigital(clave) {
		exdClaveActual = clave;
		exdInfoActual  = null;

		document.getElementById('exd-loading').style.display     = 'block';
		document.getElementById('exd-error').style.display       = 'none';
		document.getElementById('exd-sin-vinculo').style.display = 'none';
		document.getElementById('exd-resumen').style.display      = 'none';
		document.getElementById('exd-vacio').style.display        = 'none';
		document.getElementById('exd-lista').innerHTML = '';

		try {
			const resp = await apiSisca({ action: 'expedientePorEmpleado', clave });
			document.getElementById('exd-loading').style.display = 'none';

			if (!resp.ok) {
				document.getElementById('exd-error').style.display = 'block';
				document.getElementById('exd-error').textContent = resp.msg || 'Error al cargar el expediente.';
				return;
			}

			if (!resp.exp) {
				document.getElementById('exd-sin-vinculo').style.display = 'block';
				exdDocumentos = [];
				return;
			}

			exdInfoActual = resp.exp;
			exdDocumentos = resp.docs || [];
			renderResumenExpediente();
			renderExpedienteDigital();

		} catch (e) {
			document.getElementById('exd-loading').style.display = 'none';
			document.getElementById('exd-error').style.display   = 'block';
			document.getElementById('exd-error').textContent = 'Error de comunicación con el servidor.';
		}
	}

	function renderResumenExpediente() {
		const exp = exdInfoActual;
		if (!exp) return;

		const completo = exp.estatus === 'completo';
		document.getElementById('exd-resumen').style.display = 'block';
		document.getElementById('exd-resumen-tipo').textContent =
			(exp.tipo_persona === 'moral' ? '🏢 Persona moral' : '👤 Persona física') + ' — ' + (exp.nombre || '');

		const badge = document.getElementById('exd-resumen-estatus');
		badge.textContent = completo ? '✅ Completo' : '⏳ Incompleto';
		badge.style.background = completo ? '#eafaf1' : '#fff8e6';
		badge.style.color      = completo ? '#1e8e58' : '#856404';

		document.getElementById('exd-resumen-conteo').textContent =
			`${exp.docs_aprobados || 0} aprobado(s) · ${exp.docs_pendientes || 0} pendiente(s) · ${exp.docs_rechazados || 0} rechazado(s) — ${exp.total_docs || 0} documento(s) en total`;
	}

	function renderExpedienteDigital() {
		const cont = document.getElementById('exd-lista');

		if (!exdDocumentos.length) {
			cont.innerHTML = '';
			document.getElementById('exd-vacio').style.display = 'block';
			return;
		}
		document.getElementById('exd-vacio').style.display = 'none';

		cont.innerHTML = exdDocumentos.map(d => {
			const estatusNum = EXD_ESTATUS_NUM[d.estatus] ?? 0;
			const esImagen   = /\.(jpe?g|png|gif|webp)$/i.test(d.url || '');
			const thumb      = esImagen
				? `<img src="${d.url}" alt="">`
				: `<div class="exd-thumb-pdf">📄<br>PDF</div>`;
			const comentario = (d.estatus === 'rechazado' && d.comentario)
				? `<div class="exd-comentario">Motivo: ${escaparHtml(d.comentario)}</div>`
				: '';

			return `
				<div class="exd-card exd-card-${estatusNum}" data-id="${d.id}">
					<div class="exd-thumb" onclick="verDocumentoExpediente(${d.id})">${thumb}</div>
					<div class="exd-info">
						<div class="exd-tipo">${escaparHtml(EXD_TIPO_DOC_LABEL[d.tipo_doc] || d.tipo_doc || 'Documento')}</div>
						<div class="exd-nombre">${escaparHtml(d.nombre || '')}</div>
						<div class="exd-fecha">Cargado: ${d.creado_en || '—'}</div>
						<span class="exd-badge exd-badge-${estatusNum}">${EXD_ESTATUS_LABEL[d.estatus] || '—'}</span>
						${comentario}
					</div>
					<div class="exd-acciones">
						<button class="exd-btn-ver" onclick="verDocumentoExpediente(${d.id})">Ver</button>
					</div>
				</div>`;
		}).join('');
	}

	function escaparHtml(txt) {
		const div = document.createElement('div');
		div.textContent = txt == null ? '' : String(txt);
		return div.innerHTML;
	}

	function verDocumentoExpediente(id) {
		const d = exdDocumentos.find(x => x.id == id);
		if (!d) return;

		exdIdActual = id;

		document.getElementById('exd-modal-tipo').textContent   = EXD_TIPO_DOC_LABEL[d.tipo_doc] || d.tipo_doc || 'Documento';
		document.getElementById('exd-modal-nombre').textContent = d.nombre || '';

		const imgEl = document.getElementById('exd-modal-img');
		const pdfEl = document.getElementById('exd-modal-pdf');
		const esImagen = /\.(jpe?g|png|gif|webp)$/i.test(d.url || '');

		if (esImagen) {
			imgEl.src = d.url;
			imgEl.style.display = 'block';
			pdfEl.style.display = 'none';
			pdfEl.src = '';
		} else {
			pdfEl.src = d.url;
			pdfEl.style.display = 'block';
			imgEl.style.display = 'none';
			imgEl.src = '';
		}

		const estatusNum = EXD_ESTATUS_NUM[d.estatus] ?? 0;
		const badge = document.getElementById('exd-modal-badge');
		badge.textContent = EXD_ESTATUS_LABEL[d.estatus] || '—';
		badge.className = 'exd-badge exd-badge-' + estatusNum;

		const comentarioEl = document.getElementById('exd-modal-comentario');
		if (d.estatus === 'rechazado' && d.comentario) {
			comentarioEl.style.display = 'block';
			comentarioEl.textContent = 'Motivo: ' + d.comentario;
		} else {
			comentarioEl.style.display = 'none';
		}

		document.getElementById('exd-modal-btn-aprobar').style.display  = (d.estatus === 'aprobado')  ? 'none' : '';
		document.getElementById('exd-modal-btn-rechazar').style.display = (d.estatus === 'rechazado') ? 'none' : '';

		document.getElementById('modal-exd-overlay').style.display = 'flex';
	}

	function cerrarModalExpediente() {
		document.getElementById('modal-exd-overlay').style.display = 'none';
		document.getElementById('exd-modal-img').src = '';
		document.getElementById('exd-modal-pdf').src = '';
		exdIdActual = null;
	}

	async function aprobarDocumentoExpediente() {
		if (!exdIdActual) return;

		const conf = await Swal.fire({
			icon: 'question',
			title: '¿Aprobar este documento?',
			text: 'Quedará marcado como vigente en el expediente del cliente/empleado.',
			showCancelButton: true,
			confirmButtonText: 'Sí, aprobar',
			cancelButtonText: 'Cancelar'
		});
		if (!conf.isConfirmed) return;

		try {
			const resp = await apiSisca({ action: 'revisarDoc', doc_id: exdIdActual, estatus: 'aprobado', comentario: '' });
			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo aprobar el documento.', 'error');
				return;
			}
			cerrarModalExpediente();
			await cargarExpedienteDigital(exdClaveActual);
			Swal.fire({ icon: 'success', title: 'Documento aprobado', timer: 1400, showConfirmButton: false });
		} catch (e) {
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function rechazarDocumentoExpediente() {
		if (!exdIdActual) return;

		const { value: comentario, isConfirmed } = await Swal.fire({
			icon: 'warning',
			title: '¿Rechazar este documento?',
			input: 'text',
			inputLabel: 'Motivo (opcional)',
			inputPlaceholder: 'Ej. Foto ilegible, documento vencido...',
			showCancelButton: true,
			confirmButtonText: 'Sí, rechazar',
			cancelButtonText: 'Cancelar'
		});
		if (!isConfirmed) return;

		try {
			const resp = await apiSisca({ action: 'revisarDoc', doc_id: exdIdActual, estatus: 'rechazado', comentario: comentario || '' });
			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo rechazar el documento.', 'error');
				return;
			}
			cerrarModalExpediente();
			await cargarExpedienteDigital(exdClaveActual);
			Swal.fire({ icon: 'success', title: 'Documento rechazado', timer: 1400, showConfirmButton: false });
		} catch (e) {
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function guardarDatosGenerales() {
		const clave = document.getElementById('dg-clave').value;
		if (!clave) return;

		const payload = {
			action: 'guardarDatosGenerales',
			clave,
			fec_ingreso    : document.getElementById('dg-fec-ingreso').value,
			fec_reingreso  : document.getElementById('dg-fec-reingreso').value,
			fec_baja       : document.getElementById('dg-fec-baja').value,
			staff          : document.getElementById('dg-staff').checked ? 1 : 0,
			espartano      : document.getElementById('dg-espartano').checked ? 1 : 0,
			jefe_inmediato : document.getElementById('dg-jefe-clave').value,
			cp             : document.getElementById('dg-cp').value.trim(),
			colonia        : document.getElementById('dg-colonia').value.trim(),
			calle          : document.getElementById('dg-calle').value.trim(),
			telefono       : document.getElementById('dg-telefono').value.trim(),
			celular        : document.getElementById('dg-celular').value.trim(),
			email_personal : document.getElementById('dg-email-personal').value.trim(),
			email_inst     : document.getElementById('dg-email-inst').value.trim(),
			nss            : document.getElementById('dg-nss').value.trim(),
			rfc            : document.getElementById('dg-rfc').value.trim().toUpperCase(),
			curp           : document.getElementById('dg-curp').value.trim().toUpperCase(),
			fec_alta_nap   : document.getElementById('dg-fec-alta-nap').value,
			recluta_clave  : document.getElementById('dg-recluta-clave').value,
			nap            : document.getElementById('dg-nap').value.trim().toUpperCase()
		};

		mostrarLoader();
		try {
			const resp = await api(payload);
			ocultarLoader();
			if (resp.ok) {
				Swal.fire({ icon:'success', title:'Guardado', text:'Datos generales actualizados.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ── Typeahead Jefe inmediato (reutiliza buscarAsesor) ── */
	let timerJefe = null;
	const inpJefe  = document.getElementById('dg-jefe-search');
	const dropJefe = document.getElementById('dg-jefe-dropdown');

	if (inpJefe) {
		inpJefe.addEventListener('keyup', function (e) {
			if ([13, 27, 38, 40].indexOf(e.keyCode) !== -1) return;
			clearTimeout(timerJefe);
			const q = this.value.trim();
			if (q.length < 2) { dropJefe.classList.remove('open'); dropJefe.innerHTML = ''; return; }

			dropJefe.innerHTML = '<div class="nap-ta-cargando">Buscando...</div>';
			dropJefe.classList.add('open');

			timerJefe = setTimeout(async () => {
				try {
					const resp = await api({ action: 'buscarAsesor', q });
					const lista = resp.datos || [];
					dropJefe.innerHTML = '';
					if (lista.length === 0) {
						dropJefe.innerHTML = '<div class="nap-ta-vacio">Sin resultados</div>';
					} else {
						lista.forEach(emp => {
							const item = document.createElement('div');
							item.className = 'nap-ta-item';
							item.innerHTML = '<span class="nap-ta-nombre">' + resaltarTexto(emp.nombre, q) + '</span><span class="nap-ta-clave">' + emp.clave + '</span>';
							item.addEventListener('mousedown', () => {
								inpJefe.value = emp.nombre;
								document.getElementById('dg-jefe-clave').value = emp.clave;
								dropJefe.classList.remove('open');
							});
							dropJefe.appendChild(item);
						});
					}
					dropJefe.classList.add('open');
				} catch (e) {
					dropJefe.classList.remove('open');
				}
			}, 300);
		});

		inpJefe.addEventListener('blur', function () {
			setTimeout(() => dropJefe.classList.remove('open'), 200);
		});
	}

	/* ── Typeahead Reclutador (reutiliza buscarAsesor) ── */
	let timerRecluta = null;
	const inpRecluta  = document.getElementById('dg-recluta-search');
	const dropRecluta = document.getElementById('dg-recluta-dropdown');

	if (inpRecluta) {
		inpRecluta.addEventListener('keyup', function (e) {
			if ([13, 27, 38, 40].indexOf(e.keyCode) !== -1) return;
			clearTimeout(timerRecluta);
			const q = this.value.trim();
			if (q.length < 2) { dropRecluta.classList.remove('open'); dropRecluta.innerHTML = ''; return; }

			dropRecluta.innerHTML = '<div class="nap-ta-cargando">Buscando...</div>';
			dropRecluta.classList.add('open');

			timerRecluta = setTimeout(async () => {
				try {
					const resp = await api({ action: 'buscarAsesor', q });
					const lista = resp.datos || [];
					dropRecluta.innerHTML = '';
					if (lista.length === 0) {
						dropRecluta.innerHTML = '<div class="nap-ta-vacio">Sin resultados</div>';
					} else {
						lista.forEach(emp => {
							const item = document.createElement('div');
							item.className = 'nap-ta-item';
							item.innerHTML = '<span class="nap-ta-nombre">' + resaltarTexto(emp.nombre, q) + '</span><span class="nap-ta-clave">' + emp.clave + '</span>';
							item.addEventListener('mousedown', () => {
								inpRecluta.value = emp.nombre;
								document.getElementById('dg-recluta-clave').value = emp.clave;
								dropRecluta.classList.remove('open');
							});
							dropRecluta.appendChild(item);
						});
					}
					dropRecluta.classList.add('open');
				} catch (e) {
					dropRecluta.classList.remove('open');
				}
			}, 300);
		});

		inpRecluta.addEventListener('blur', function () {
			setTimeout(() => dropRecluta.classList.remove('open'), 200);
		});
	}

	/* ════════════════════════════════════════
	   Guardar empleado
	════════════════════════════════════════ */
	async function guardarEmpleado() {
		const esNuevo = !document.getElementById('emp-id').value;
		let payload;

		if (esNuevo) {
			/* ── ALTA ── */
			const nombre = document.getElementById('nvo-nombre').value.trim();
			const email  = document.getElementById('nvo-email').value.trim();
			const clave  = document.getElementById('nvo-clave').value.trim();
			const activo = document.getElementById('emp-activo').checked ? 1 : 0;

			if (!nombre) { Swal.fire('Campo requerido', 'Escribe el nombre (nombre(s) y apellidos).', 'warning'); document.getElementById('nvo-nombres').focus(); return; }
			if (!email)  { Swal.fire('Campo requerido', 'Escribe el correo.', 'warning'); document.getElementById('nvo-email').focus(); return; }
			if (!clave)  { Swal.fire('Campo requerido', 'La clave es obligatoria.', 'warning'); return; }

			const nombres          = document.getElementById('nvo-nombres').value.trim();
			const apellidoPaterno  = document.getElementById('nvo-apellido-paterno').value.trim();
			const apellidoMaterno  = document.getElementById('nvo-apellido-materno').value.trim();

			/* Antes de registrar, comparar contra Boletinados Principal.
			   Si hay coincidencia y quien captura elige "No continuar",
			   se corta el alta aquí -- no se manda nada al servidor. */
			const puedeContinuar = await verificarBoletinadoAntesDeAlta(nombres, apellidoPaterno, apellidoMaterno);
			if (!puedeContinuar) return;

			payload = { action:'guardarEmpleado', id:'', nombre, email, clave, password:'', activo,
			            nombres, apellido_paterno: apellidoPaterno, apellido_materno: apellidoMaterno };

		} else {
			/* ── EDICIÓN: activo + nombre separado ──
			   Ya no hay un botón aparte de "Guardar nombre separado"
			   (generaba confusión con el Guardar general); este único
			   botón guarda ambas cosas de una vez. */
			const id     = document.getElementById('emp-id').value;
			const activo = document.getElementById('emp-activo-ed').checked ? 1 : 0;

			const nombresEd   = document.getElementById('emp-nombres').value.trim().toUpperCase();
			const apPatEd     = document.getElementById('emp-apellido-paterno').value.trim().toUpperCase();
			const apMatEd     = document.getElementById('emp-apellido-materno').value.trim().toUpperCase();

			payload = {
				action:'guardarEmpleado', id, nombre:'', email:'', clave:'', password:'', activo,
				nombres: nombresEd, apellido_paterno: apPatEd, apellido_materno: apMatEd
			};
		}

		mostrarLoader();
		try {
			const resp = await api(payload);
			ocultarLoader();
			if (resp.ok) {
				if (resp.id) document.getElementById('emp-id').value = resp.id;

				if (esNuevo && resp.clave) {
					/* Tras el alta, cargar directo el empleado en modo edición
					   para no obligar a buscarlo de nuevo manualmente */
					const nombreNuevo = document.getElementById('nvo-nombre').value.trim().toUpperCase();
					const emailNuevo  = document.getElementById('nvo-email').value.trim();
					const activoNuevo = document.getElementById('emp-activo').checked ? 1 : 0;

					cargarFormulario({
						id: resp.clave,
						nombre: nombreNuevo,
						email: emailNuevo,
						clave: resp.clave,
						activo: activoNuevo
					});

					inpBusq.value = nombreNuevo;
					document.getElementById('emp-badge-txt').textContent = nombreNuevo + ' (' + resp.clave + ')';
					document.getElementById('emp-badge-wrap').style.display = 'block';
				}

				Swal.fire({ icon:'success', title:'Guardado', text: resp.msg || 'Guardado correctamente.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   UI helpers
	════════════════════════════════════════ */
	function mostrarPanel() {
		document.getElementById('estado-vacio').style.display  = 'none';
		document.getElementById('panel-empleado').style.display = 'block';
	}

	function cerrarPanel() {
		document.getElementById('panel-empleado').style.display = 'none';
		document.getElementById('estado-vacio').style.display   = 'block';
		document.getElementById('inp-busqueda').value           = '';
		document.getElementById('emp-badge-wrap').style.display = 'none';
		document.getElementById('emp-badge-txt').textContent    = '';
	}

	/* ════════════════════════════════════════
	   BOLETINADOS PRINCIPAL
	   No depende de tener un empleado cargado -- se puede abrir
	   directo desde el botón de la barra superior, sin pasar por
	   buscar o crear un empleado primero.
	════════════════════════════════════════ */
	let boletinadosCache = [];
	let boletinadosCargados = false;

	function abrirBoletinados() {
		mostrarPanel();
		activarTab('pane-boletinados');
		if (!boletinadosCargados) cargarBoletinados();
	}

	async function cargarBoletinados() {
		const tbody = document.getElementById('bol-tbody');
		tbody.innerHTML = '<tr><td colspan="6" class="bnc-vacio">Cargando...</td></tr>';
		try {
			const resp = await api({ action: 'listarBoletinados' });
			if (!resp.ok) {
				tbody.innerHTML = '<tr><td colspan="6" class="bnc-vacio">' + (resp.msg || 'Error al cargar.') + '</td></tr>';
				return;
			}
			boletinadosCache   = resp.data || [];
			boletinadosCargados = true;
			renderTablaBoletinados(boletinadosCache);
		} catch (e) {
			tbody.innerHTML = '<tr><td colspan="6" class="bnc-vacio">Error de comunicación con el servidor.</td></tr>';
		}
	}

	function renderTablaBoletinados(lista) {
		const tbody = document.getElementById('bol-tbody');

		if (!lista.length) {
			tbody.innerHTML = '<tr><td colspan="6" class="bnc-vacio">Sin registros en la lista.</td></tr>';
			return;
		}

		tbody.innerHTML = lista.map(function (b) {
			return '<tr>' +
				'<td>' + (b.nombres || '') + '</td>' +
				'<td>' + (b.apellido_paterno || '') + '</td>' +
				'<td>' + (b.apellido_materno || '') + '</td>' +
				'<td>' + (b.capturado_por || '—') + '</td>' +
				'<td>' + (b.fec_captura || '') + '</td>' +
				'<td style="text-align:right;"><button class="bnc-btn-icon" title="Eliminar" onclick="eliminarBoletinadoFila(' + b.id + ')">🗑️</button></td>' +
				'</tr>';
		}).join('');
	}

	function filtrarTablaBoletinados(texto) {
		const q = (texto || '').toLowerCase().trim();
		if (!q) { renderTablaBoletinados(boletinadosCache); return; }

		const filtrados = boletinadosCache.filter(function (b) {
			return (b.nombres || '').toLowerCase().includes(q)
				|| (b.apellido_paterno || '').toLowerCase().includes(q)
				|| (b.apellido_materno || '').toLowerCase().includes(q);
		});
		renderTablaBoletinados(filtrados);
	}

	async function guardarBoletinado() {
		const nombres = document.getElementById('bol-nombres').value.trim();
		const apPat   = document.getElementById('bol-apellido-paterno').value.trim();
		const apMat   = document.getElementById('bol-apellido-materno').value.trim();

		if (!nombres || !apPat) {
			Swal.fire('Campos requeridos', 'Captura al menos Nombre(s) y Apellido paterno.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarBoletinado',
				nombres, apellido_paterno: apPat, apellido_materno: apMat
			});
			ocultarLoader();
			if (resp.ok) {
				document.getElementById('bol-nombres').value = '';
				document.getElementById('bol-apellido-paterno').value = '';
				document.getElementById('bol-apellido-materno').value = '';
				Swal.fire({ icon: 'success', title: 'Agregado', timer: 1500, showConfirmButton: false });
				await cargarBoletinados();
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function eliminarBoletinadoFila(id) {
		const result = await Swal.fire({
			title: '¿Quitar de la lista de boletinados?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, quitar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'eliminarBoletinado', id });
			ocultarLoader();
			if (resp.ok) {
				await cargarBoletinados();
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo eliminar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* Verificación de coincidencia contra Boletinados Principal, se
	   dispara justo antes de dar de alta un empleado nuevo (ver
	   guardarEmpleado()). Si hay coincidencia, deja la decisión en
	   manos de quien captura: "Continuar" sigue con el alta tal cual,
	   "No continuar" cancela el registro por completo. */
	async function verificarBoletinadoAntesDeAlta(nombres, apellidoPaterno, apellidoMaterno) {
		try {
			const resp = await api({
				action: 'verificarBoletinado',
				nombres, apellido_paterno: apellidoPaterno, apellido_materno: apellidoMaterno
			});

			if (!resp.ok || !resp.coincide) return true; /* sin coincidencia (o error de verificación): se deja continuar */

			const coincidencias = resp.coincidencias || [];
			const listaHtml = coincidencias.map(function (c) {
				return '<li>' + [c.nombres, c.apellido_paterno, c.apellido_materno].filter(Boolean).join(' ') + '</li>';
			}).join('');

			const result = await Swal.fire({
				icon: 'warning',
				title: 'Posible coincidencia con Boletinados Principal',
				html: 'El nombre capturado se parece a alguien en la lista de boletinados:<ul style="text-align:left;margin-top:8px;">' + listaHtml + '</ul>¿Quieres continuar con el registro de todas formas?',
				showCancelButton: true,
				confirmButtonText: 'Continuar de todas formas',
				cancelButtonText: 'No continuar',
				confirmButtonColor: '#c0392b',
				cancelButtonColor: '#2B73B6'
			});

			return result.isConfirmed;
		} catch (e) {
			return true; /* si falla la verificación, no se bloquea el alta por un error de red */
		}
	}

	function activarTab(paneId) {
		document.querySelectorAll('.nap-tab').forEach(t  => t.classList.remove('activa'));
		document.querySelectorAll('.nap-pane').forEach(p => p.classList.remove('activo'));
		document.querySelector(`[data-pane="${paneId}"]`).classList.add('activa');
		document.getElementById(paneId).classList.add('activo');
	}

	function actualizarEstado() {
		const editar = document.getElementById('sec-editar').style.display !== 'none';
		const chk = document.getElementById(editar ? 'emp-activo-ed' : 'emp-activo');
		document.getElementById('lbl-estado').textContent = (chk && chk.checked) ? 'Activo' : 'Inactivo';
	}

	function mostrarLoader() {
		document.getElementById('loader').style.display = 'flex';
	}

	function ocultarLoader() {
		document.getElementById('loader').style.display = 'none';
	}

	/* ════════════════════════════════════════
	   DATOS BANCARIOS — catálogo de bancos
	════════════════════════════════════════ */
	let bancosCache = [];

	async function cargarCatalogoBancos() {
		if (bancosCache.length > 0) return bancosCache;
		try {
			const resp = await api({ action: 'getBancos' });
			if (resp.ok) {
				bancosCache = resp.bancos || [];
				const sel = document.getElementById('bnc-id-banco');
				sel.innerHTML = '<option value="">— Selecciona —</option>';
				bancosCache.forEach(b => {
					const opt = document.createElement('option');
					opt.value = b.id_banco;
					opt.textContent = b.banco;
					sel.appendChild(opt);
				});
			}
		} catch (e) { console.error('Error cargando bancos:', e); }
		return bancosCache;
	}

	function detectarBancoPorClabe(clabe) {
		if (!clabe || clabe.length < 3) return null;
		const codigo = clabe.substring(0, 3);
		return bancosCache.find(b => b.codigo_banco === codigo) || null;
	}

	function onClabeManualInput(inp) {
		inp.value = inp.value.replace(/\D/g, '').substring(0, 18);
		const hint = document.getElementById('bnc-clabe-hint');
		const hintBanco = document.getElementById('bnc-banco-hint');

		if (inp.value.length < 18) {
			hint.textContent = inp.value.length + ' / 18 dígitos';
			hint.style.color = '#c0392b';
			return;
		}
		hint.textContent = '✓ 18 dígitos';
		hint.style.color = '#1e6e42';

		const banco = detectarBancoPorClabe(inp.value);
		if (banco) {
			document.getElementById('bnc-id-banco').value = banco.id_banco;
			hintBanco.textContent = '(detectado por CLABE)';
		} else {
			hintBanco.textContent = '(no detectado — selecciona manualmente)';
		}
	}

	/* ════════════════════════════════════════
	   DATOS BANCARIOS — listar / render
	════════════════════════════════════════ */
	let bncCuentas = [];

	async function cargarCuentasBancarias(clave) {
		await cargarCatalogoBancos();
		try {
			const resp = await api({ action: 'getCuentasBancarias', clave });
			if (resp.ok) {
				bncCuentas = resp.cuentas || [];
				renderTablaCuentas();
			}
		} catch (e) { console.error('Error cargando cuentas:', e); }
	}

	function renderTablaCuentas() {
		const tbody = document.getElementById('bnc-tbody');
		tbody.innerHTML = '';

		if (bncCuentas.length === 0) {
			tbody.innerHTML = '<tr><td colspan="5" class="bnc-vacio">Sin cuentas registradas</td></tr>';
			return;
		}

		bncCuentas.forEach((cta, idx) => {
			const banco = bancosCache.find(b => b.id_banco == cta.id_banco);
			const nombreBanco = banco ? banco.banco : ('Banco ' + cta.id_banco);
			const nombreCompleto = [cta.nombre_cta, cta.ap_cta, cta.am_cta].filter(Boolean).join(' ');

			const tr = document.createElement('tr');
			if (cta.activo != 1) tr.classList.add('bnc-inactiva');

			tr.innerHTML = `
				<td>${nombreCompleto}</td>
				<td>${nombreBanco}</td>
				<td class="bnc-pill-clabe">${cta.clabe}</td>
				<td style="text-align:center;">
					<input type="checkbox" class="bnc-check-activa" ${cta.activo == 1 ? 'checked' : ''}
						   onchange="activarCuenta(${idx}, this)">
				</td>
				<td style="text-align:right;">
					<button class="bnc-btn-icon" title="Editar" onclick="editarCuenta(${idx})">&#9998;</button>
					${cta.activo == 1
						? '<span class="bnc-btn-icon" title="No se puede eliminar la cuenta activa" style="opacity:.35;cursor:not-allowed;">🗑️</span>'
						: `<button class="bnc-btn-icon" title="Eliminar" onclick="eliminarCuenta(${idx})">🗑️</button>`}
				</td>
			`;
			tbody.appendChild(tr);
		});
	}

	async function activarCuenta(idx, checkbox) {
		const cta = bncCuentas[idx];
		if (!cta) return;

		/* Si la desmarcan y es la única activa, no permitir dejar todo inactivo */
		if (!checkbox.checked) {
			checkbox.checked = true;
			Swal.fire({ icon:'info', title:'Debe haber una cuenta activa', text:'Activa otra cuenta para reemplazar esta, no puedes dejar todas inactivas.', timer:2500, showConfirmButton:false });
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({ action:'activarCuentaBancaria', clave: document.getElementById('emp-id').value, clabe: cta.clabe });
			ocultarLoader();
			if (resp.ok) {
				bncCuentas.forEach(c => c.activo = (c.clabe === cta.clabe) ? 1 : 0);
				renderTablaCuentas();
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo activar.', 'error');
				renderTablaCuentas();
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function eliminarCuenta(idx) {
		const cta = bncCuentas[idx];
		if (!cta) return;

		if (cta.activo == 1) {
			Swal.fire({
				icon: 'warning',
				title: 'No se puede eliminar',
				text: 'Esta cuenta está activa. Activa otra cuenta primero y después podrás eliminar esta.',
				confirmButtonColor: '#2B73B6'
			});
			return;
		}

		const result = await Swal.fire({
			title: '¿Eliminar cuenta bancaria?',
			text: 'Esta acción no se puede deshacer.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#c0392b'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action:'eliminarCuentaBancaria', clave: document.getElementById('emp-id').value, clabe: cta.clabe });
			ocultarLoader();
			if (resp.ok) {
				await cargarCuentasBancarias(document.getElementById('emp-id').value);
				Swal.fire({ icon:'success', title:'Eliminada', timer:1500, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo eliminar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   DATOS BANCARIOS — alta/edición de cuenta
	════════════════════════════════════════ */
	function abrirFormCuenta() {
		document.getElementById('bnc-editando-id').value = '';
		document.getElementById('bnc-nombre').value = '';
		document.getElementById('bnc-ap').value = '';
		document.getElementById('bnc-am').value = '';
		document.getElementById('bnc-clabe').value = '';
		document.getElementById('bnc-id-banco').value = '';
		document.getElementById('bnc-activo').checked = bncCuentas.length === 0; /* si es la primera, activa por default */
		document.getElementById('bnc-clabe-hint').textContent = '';
		document.getElementById('bnc-banco-hint').textContent = '(detectado por CLABE)';
		document.getElementById('bnc-form-wrap').style.display = 'block';
		document.getElementById('bnc-nombre').focus();
	}

	function cerrarFormCuenta() {
		document.getElementById('bnc-form-wrap').style.display = 'none';
	}

	function editarCuenta(idx) {
		const cta = bncCuentas[idx];
		if (!cta) return;

		document.getElementById('bnc-editando-id').value = cta.clabe; /* la clabe original identifica el registro */
		document.getElementById('bnc-nombre').value = cta.nombre_cta || '';
		document.getElementById('bnc-ap').value     = cta.ap_cta     || '';
		document.getElementById('bnc-am').value     = cta.am_cta     || '';
		document.getElementById('bnc-clabe').value  = cta.clabe      || '';
		document.getElementById('bnc-id-banco').value = cta.id_banco || '';
		document.getElementById('bnc-activo').checked = cta.activo == 1;
		document.getElementById('bnc-clabe-hint').textContent = '✓ 18 dígitos';
		document.getElementById('bnc-clabe-hint').style.color = '#1e6e42';
		document.getElementById('bnc-form-wrap').style.display = 'block';
	}

	async function guardarCuentaBancaria() {
		const clave = document.getElementById('emp-id').value;
		const nombre = document.getElementById('bnc-nombre').value.trim().toUpperCase();
		const ap = document.getElementById('bnc-ap').value.trim().toUpperCase();
		const am = document.getElementById('bnc-am').value.trim().toUpperCase();
		const clabe = document.getElementById('bnc-clabe').value.trim();
		const idBanco = document.getElementById('bnc-id-banco').value;
		const activo = document.getElementById('bnc-activo').checked ? 1 : 0;
		const clabeOriginal = document.getElementById('bnc-editando-id').value;

		if (!nombre) { Swal.fire('Campo requerido', 'Escribe el nombre.', 'warning'); return; }
		if (clabe.length !== 18) { Swal.fire('CLABE inválida', 'La CLABE debe tener 18 dígitos.', 'warning'); return; }
		if (!idBanco) { Swal.fire('Campo requerido', 'Selecciona el banco.', 'warning'); return; }

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarCuentaBancaria',
				clave, nombre_cta: nombre, ap_cta: ap, am_cta: am,
				clabe, id_banco: idBanco, activo,
				clabe_original: clabeOriginal
			});
			ocultarLoader();
			if (resp.ok) {
				cerrarFormCuenta();
				await cargarCuentasBancarias(clave);
				Swal.fire({ icon:'success', title:'Guardado', text: resp.msg || 'Cuenta guardada.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   DATOS BANCARIOS — modal PDF (pdf.js)
	════════════════════════════════════════ */
	let pdfDatosExtraidos = null;

	function abrirModalPdf() {
		document.getElementById('pdf-file-input').value = '';
		document.getElementById('pdf-drop-texto').style.display = 'block';
		document.getElementById('pdf-procesando').style.display = 'none';
		document.getElementById('pdf-resultado').style.display = 'none';
		document.getElementById('pdf-no-detectado').style.display = 'none';
		document.getElementById('btn-usar-datos-pdf').style.display = 'none';
		document.getElementById('pdf-procesando-texto').textContent = 'Leyendo documento...';
		pdfDatosExtraidos = null;
		document.getElementById('modal-pdf-overlay').style.display = 'flex';
	}

	function cerrarModalPdf() {
		document.getElementById('modal-pdf-overlay').style.display = 'none';
	}

	document.getElementById('pdf-drop-zone').addEventListener('click', () => {
		document.getElementById('pdf-file-input').click();
	});

	/* Debajo de este umbral de caracteres, se considera que el PDF no
	   trae una capa de texto real aprovechable (típico: el usuario
	   subió el estado de cuenta como una foto/captura pegada dentro
	   de un PDF, sin OCR de por medio) y se cae al flujo de OCR. */
	const PDF_TEXTO_MINIMO_UTIL = 25;

	function setTextoProcesando(txt) {
		const el = document.getElementById('pdf-procesando-texto');
		if (el) el.textContent = txt;
	}

	/* Extrae el texto de la página 1 de un PDF usando su capa de texto
	   (pdf.js). Si el PDF es en realidad una imagen escaneada, esto
	   devuelve texto vacío o casi vacío. */
	async function leerTextoPdf(arrayBuffer) {
		const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;

		/* Solo la primera página: ahí vive la CLABE, nombre y domicilio */
		const page = await pdf.getPage(1);
		const content = await page.getTextContent();

		/* Reconstruir texto respetando posición Y (renglón) para no mezclar
		   columnas de tablas que pdf.js a veces devuelve fuera de orden */
		const items = content.items.map(it => ({
			str: it.str,
			x: it.transform[4],
			y: Math.round(it.transform[5])
		}));

		items.sort((a, b) => (b.y - a.y) || (a.x - b.x));

		let textoCompleto = '';
		let yActual = null;
		items.forEach(it => {
			if (yActual === null || Math.abs(it.y - yActual) > 2) {
				textoCompleto += '\n';
				yActual = it.y;
			} else {
				textoCompleto += ' ';
			}
			textoCompleto += it.str;
		});

		return textoCompleto;
	}

	/* Renderiza la página 1 de un PDF a una imagen (canvas → blob), para
	   poder mandarla a Tesseract cuando el PDF no trae texto real
	   (es una imagen escaneada metida dentro del PDF). Escala 2x para
	   que el OCR tenga mejor resolución con la que trabajar. */
	async function renderizarPaginaPdfComoImagen(arrayBuffer) {
		const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
		const page = await pdf.getPage(1);

		const viewport = page.getViewport({ scale: 2 });
		const canvas = document.createElement('canvas');
		canvas.width  = viewport.width;
		canvas.height = viewport.height;

		await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

		return new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
	}

	/* OCR con Tesseract (español + inglés) sobre una imagen (File o Blob) */
	async function leerImagenConOCR(imagen) {
		const { data: { text } } = await Tesseract.recognize(imagen, 'spa+eng');
		return text;
	}

	async function onPdfFileSelected(input) {
		const file = input.files[0];
		if (!file) return;

		document.getElementById('pdf-drop-texto').style.display = 'none';
		document.getElementById('pdf-procesando').style.display = 'block';
		document.getElementById('pdf-resultado').style.display = 'none';
		setTextoProcesando('Leyendo documento...');

		const esPdf   = file.type === 'application/pdf' || /\.pdf$/i.test(file.name || '');
		const esImagen = file.type.startsWith('image/');

		if (!esPdf && !esImagen) {
			document.getElementById('pdf-procesando').style.display = 'none';
			Swal.fire('Formato no soportado', 'Sube un PDF o una foto/imagen (JPG, PNG) del estado de cuenta.', 'warning');
			return;
		}

		try {
			let textoCompleto = '';

			if (esPdf) {
				const arrayBuffer = await file.arrayBuffer();
				textoCompleto = await leerTextoPdf(arrayBuffer);

				console.log('--- TEXTO PDF EXTRAÍDO (página 1) ---');
				console.log(textoCompleto);
				console.log('--- FIN TEXTO PDF ---');

				/* El PDF no trae texto real aprovechable: seguro es una
				   imagen escaneada pegada dentro del PDF. Se renderiza la
				   página como imagen y se manda a OCR, igual que si el
				   usuario hubiera subido directamente la foto. */
				if (textoCompleto.trim().length < PDF_TEXTO_MINIMO_UTIL) {
					console.log('Texto del PDF insuficiente, se intenta OCR sobre la página renderizada...');
					setTextoProcesando('El PDF no tiene texto legible, leyendo como imagen (puede tardar unos segundos)...');

					const imagenPagina = await renderizarPaginaPdfComoImagen(arrayBuffer);
					textoCompleto = await leerImagenConOCR(imagenPagina);

					console.log('--- TEXTO OCR (PDF renderizado como imagen) ---');
					console.log(textoCompleto);
					console.log('--- FIN TEXTO OCR ---');
				}
			} else {
				/* Imagen/foto directa (JPG, PNG, etc.) subida por el usuario */
				setTextoProcesando('Leyendo imagen con OCR (puede tardar unos segundos)...');

				textoCompleto = await leerImagenConOCR(file);

				console.log('--- TEXTO OCR (imagen) ---');
				console.log(textoCompleto);
				console.log('--- FIN TEXTO OCR ---');
			}

			procesarTextoPdf(textoCompleto);

		} catch (e) {
			console.error('Error leyendo documento:', e);
			document.getElementById('pdf-procesando').style.display = 'none';
			Swal.fire('Error', 'No se pudo leer el documento. Verifica que el archivo no esté dañado.', 'error');
		}
	}

	function limpiarNombrePdf(texto) {
		let n = texto.trim().replace(/\s+/g, ' ');
		/* Cortar en la primera palabra que sea claramente parte de domicilio o
		   encabezado, en caso de que el regex haya agarrado de más */
		const cortes = ['PRIV', 'CALLE', 'AV', 'BLVD', 'COL', 'FRACC', 'MEXICO', 'CP', 'C.P',
		                'NO.', 'PERIODO', 'FECHA', 'ESTADO', 'CUENTA', 'CLIENTE', 'SUCURSAL'];
		const palabras = n.split(' ');
		const idxCorte = palabras.findIndex(p => cortes.includes(p.toUpperCase()));
		if (idxCorte > 0) n = palabras.slice(0, idxCorte).join(' ');
		return n.trim();
	}

	/* Dígito verificador de la CLABE (ponderación 3-7-1 cíclica sobre
	   los primeros 17 dígitos, módulo 10). Sirve para descartar corridas
	   de 18 dígitos que NO son realmente la CLABE (ej. una referencia o
	   número de cuenta que también mide 18 dígitos), y es clave con OCR
	   porque valida que Tesseract no haya "leído mal" ningún dígito. */
	function validarClabe(clabe) {
		if (!/^\d{18}$/.test(clabe)) return false;

		const pesos = [3, 7, 1];
		let suma = 0;

		for (let i = 0; i < 17; i++) {
			suma += (parseInt(clabe[i], 10) * pesos[i % 3]) % 10;
		}

		const digito = (10 - (suma % 10)) % 10;

		return digito === parseInt(clabe[17], 10);
	}

	/* Busca en el texto todas las corridas de 18 dígitos (tolerando
	   espacios/guiones entre ellos, como suele dejar el OCR) y regresa
	   la primera que pase la validación del dígito verificador. */
	function extraerClabeValidada(texto) {
		const coincidencias = texto.match(/(?:\d[\s-]*){18}/g) || [];

		for (const posible of coincidencias) {
			const candidata = posible.replace(/\D/g, '');
			if (candidata.length === 18 && validarClabe(candidata)) {
				return candidata;
			}
		}

		return null;
	}

	async function procesarTextoPdf(texto) {
		await cargarCatalogoBancos();

		const lineas = texto.split('\n').map(l => l.trim()).filter(l => l.length > 0);

		/* ── CLABE ──
		   1) Patrón con la etiqueta "CLABE" seguida de los 18 dígitos
		      (con o sin espacios entre grupos) — el caso más confiable.
		   2) Si no aparece la etiqueta (típico en texto de OCR, que a
		      veces recorta o ensucia encabezados), se buscan TODAS las
		      corridas de 18 dígitos en el texto (permitiendo espacios o
		      guiones de por medio, como separadores de OCR) y se valida
		      cada una con el dígito verificador de la CLABE; se usa la
		      primera que sí cuadre. Esto evita agarrar por error otro
		      número de 18 dígitos que no sea realmente la CLABE. */
		let clabe = null;

		const matchClabeEspaciada = texto.match(/CLABE\s*[:\-]?\s*(\d{3})\s*(\d{3})\s*(\d{11})\s*(\d{1})\b/i);
		if (matchClabeEspaciada) {
			clabe = matchClabeEspaciada[1] + matchClabeEspaciada[2] + matchClabeEspaciada[3] + matchClabeEspaciada[4];
		} else {
			clabe = extraerClabeValidada(texto);
			if (!clabe) {
				/* Último respaldo: 18 dígitos consecutivos sin validar,
				   igual que antes, por si el dígito verificador no cuadra
				   por un error de OCR en un solo carácter. */
				const matchClabe18 = texto.match(/\b\d{18}\b/);
				if (matchClabe18) clabe = matchClabe18[0];
			}
		}

		/* ── NOMBRE DEL TITULAR ──
		   1) Etiquetas explícitas ("Nombre del Receptor", "Titular", "A nombre de").
		   2) Por renglón: el nombre del titular suele ser la primera línea en
		      mayúsculas (solo letras y espacios, 2-5 palabras) que aparece después
		      del bloque de datos del banco (RFC del banco) y antes de palabras de
		      domicilio (PRIV, CALLE, AV, COL, LOC, FRACC). Recorremos línea por línea
		      buscando ese patrón en vez de un regex de texto corrido (evita mezclar
		      columnas/renglones que pdf.js puede desordenar). */
		let nombre = null;

		const patronesDirectos = [
			/Nombre del Receptor\s*:?\s*([A-ZÑÁÉÍÓÚ][A-ZÑÁÉÍÓÚ\s]{6,60})/i,
			/(?:Titular|A nombre de)\s*:?\s*([A-ZÑÁÉÍÓÚ][A-ZÑÁÉÍÓÚ\s]{6,60})/i
		];
		for (const re of patronesDirectos) {
			const m = texto.match(re);
			if (m && m[1]) { nombre = limpiarNombrePdf(m[1]); break; }
		}

		if (!nombre) {
			const palabrasDomicilio = /^(PRIV|CALLE|AV\.?|AVE|BLVD|COL\.?|FRACC|LOC|CALZ)\b/i;
			const soloLetras = /^[A-ZÑÁÉÍÓÚ]{2,}(\s+[A-ZÑÁÉÍÓÚ]{2,}){1,4}$/;

			for (let i = 0; i < lineas.length - 1; i++) {
				const lineaActual = lineas[i];
				const lineaSiguiente = lineas[i + 1];
				if (soloLetras.test(lineaActual) && palabrasDomicilio.test(lineaSiguiente)) {
					nombre = lineaActual.trim();
					break;
				}
			}
		}

		/* ── BANCO ── */
		let banco = null;
		if (clabe) banco = detectarBancoPorClabe(clabe);

		if (!banco && bancosCache.length) {
			const textoUpper = texto.toUpperCase();
			banco = bancosCache.find(b => textoUpper.includes(b.banco.toUpperCase())) || null;
		}

		document.getElementById('pdf-procesando').style.display = 'none';
		document.getElementById('pdf-resultado').style.display = 'block';

		document.getElementById('pdf-clabe-detectada').textContent = clabe || 'No detectada';
		document.getElementById('pdf-nombre-detectado').textContent = nombre || 'No detectado';
		document.getElementById('pdf-banco-detectado').textContent = banco ? banco.banco : 'No detectado';

		if (!clabe || !nombre || !banco) {
			document.getElementById('pdf-no-detectado').style.display = 'block';
		}

		pdfDatosExtraidos = { clabe, nombre, banco };

		if (clabe || nombre) {
			document.getElementById('btn-usar-datos-pdf').style.display = 'inline-block';
		}
	}

	function usarDatosPdf() {
		if (!pdfDatosExtraidos) return;

		abrirFormCuenta();

		if (pdfDatosExtraidos.nombre) {
			const partes = pdfDatosExtraidos.nombre.split(' ').filter(p => p);
			document.getElementById('bnc-nombre').value = partes[0] || '';
			document.getElementById('bnc-ap').value     = partes[1] || '';
			document.getElementById('bnc-am').value     = partes.slice(2).join(' ') || '';
		}
		if (pdfDatosExtraidos.clabe) {
			document.getElementById('bnc-clabe').value = pdfDatosExtraidos.clabe;
			onClabeManualInput(document.getElementById('bnc-clabe'));
		}
		if (pdfDatosExtraidos.banco) {
			document.getElementById('bnc-id-banco').value = pdfDatosExtraidos.banco.id_banco;
		}

		cerrarModalPdf();
	}

	/* ════════════════════════════════════════
	   ESTRUCTURA DE EMPLEADOS (pestaña 4)
	════════════════════════════════════════ */
	let estClaveActual = '';

	async function cargarEstructuraEmpleado(clave) {
		estClaveActual = clave;
		const renderVacio = (id, texto) => {
			document.getElementById(id).innerHTML = `<div class="est-row-vacio">${texto}</div>`;
		};

		renderVacio('est-lista-nivel', 'Cargando...');
		renderVacio('est-lista-gerencia', 'Cargando...');
		renderVacio('est-lista-brockers', 'Cargando...');
		document.getElementById('est-categoria').textContent = '—';
		document.getElementById('est-brocker').textContent   = '—';
		document.getElementById('est-socio').textContent     = '—';

		try {
			const resp = await api({ action: 'getEstructuraEmpleado', clave });
			if (!resp.ok) return;
			const d = resp.datos || {};

			/* Niveles → filas, clic = eliminar (con confirmación) */
			if (d.niveles && d.niveles.length) {
				document.getElementById('est-lista-nivel').innerHTML =
					d.niveles.map(n =>
						`<div class="est-row-item" onclick="confirmarEliminarNivel(${n.id_nivel}, '${n.desc_nivel.replace(/'/g, "\\'")}')">${n.desc_nivel}</div>`
					).join('');
			} else {
				renderVacio('est-lista-nivel', 'Sin nivel asignado');
			}

			/* Gerencias → filas, la base (pertenece=1) resaltada en azul sólido;
			   clic en la base = eliminar, clic en otra = preguntar si marcar como base */
			if (d.gerencias && d.gerencias.length) {
				document.getElementById('est-lista-gerencia').innerHTML =
					d.gerencias.map(g => {
						const esBase = g.pertenece == 1;
						const tagBase = esBase ? '<span class="est-row-tag-base">BASE</span>' : '';
						return `<div class="est-row-item${esBase ? ' est-row-base' : ''}"
							onclick="onClicGerencia(${g.id_gerencia}, '${g.gerencia.replace(/'/g, "\\'")}', ${esBase})">${g.gerencia}${tagBase}</div>`;
					}).join('');
			} else {
				renderVacio('est-lista-gerencia', 'Sin gerencias asignadas');
			}

			/* Brockers → filas, los marcados (CC) resaltados */
			if (d.brockers && d.brockers.length) {
				document.getElementById('est-lista-brockers').innerHTML =
					d.brockers.map(b => {
						const esCC = b.includes('(CC)');
						return `<div class="est-row-item${esCC ? ' est-row-cc' : ''}">${b}</div>`;
					}).join('');
			} else {
				renderVacio('est-lista-brockers', 'Sin brockers a cargo');
			}

			document.getElementById('est-categoria').textContent = d.gerencia_directa || '—';
			document.getElementById('est-brocker').textContent   = d.brocker          || '—';
			document.getElementById('est-socio').textContent     = d.socio            || '—';

		} catch (e) {
			console.error('Error cargando estructura:', e);
			renderVacio('est-lista-nivel', 'Error al cargar');
			renderVacio('est-lista-gerencia', 'Error al cargar');
			renderVacio('est-lista-brockers', 'Error al cargar');
		}
	}

	/* ── Eliminar nivel (con confirmación) ── */
	async function confirmarEliminarNivel(idNivel, descNivel) {
		const result = await Swal.fire({
			title: '¿Eliminar nivel?',
			html: `Vas a quitar el nivel <strong>${descNivel}</strong> de este empleado.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#c0392b'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'eliminarNivel', clave: estClaveActual, id_nivel: idNivel });
			ocultarLoader();
			if (resp.ok) {
				await cargarEstructuraEmpleado(estClaveActual);
				Swal.fire({ icon:'success', title:'Nivel eliminado', timer:1300, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo eliminar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ── Clic en gerencia: si es la BASE → confirmar eliminación;
	      si NO es la base → confirmar marcar como nueva base (quita el resto) ── */
	async function onClicGerencia(idGerencia, nombreGerencia, esBase) {
		if (esBase) {
			const result = await Swal.fire({
				title: '¿Eliminar gerencia base?',
				html: `<strong>${nombreGerencia}</strong> es la gerencia base actual. ¿Quieres eliminarla?`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, eliminar',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#c0392b'
			});
			if (!result.isConfirmed) return;

			mostrarLoader();
			try {
				const resp = await api({ action: 'eliminarGerencia', clave: estClaveActual, id_gerencia: idGerencia });
				ocultarLoader();
				if (resp.ok) {
					await cargarEstructuraEmpleado(estClaveActual);
					Swal.fire({ icon:'success', title:'Gerencia eliminada', timer:1300, showConfirmButton:false });
				} else {
					Swal.fire('Error', resp.msg || 'No se pudo eliminar.', 'error');
				}
			} catch (e) {
				ocultarLoader();
				Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
			}

		} else {
			const result = await Swal.fire({
				title: '¿Asignar como gerencia base?',
				html: `<strong>${nombreGerencia}</strong> se convertirá en la gerencia base de este empleado. La gerencia base actual dejará de serlo.`,
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Sí, asignar como base',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#2B73B6'
			});
			if (!result.isConfirmed) return;

			mostrarLoader();
			try {
				const resp = await api({ action: 'marcarGerenciaBase', clave: estClaveActual, id_gerencia: idGerencia });
				ocultarLoader();
				if (resp.ok) {
					await cargarEstructuraEmpleado(estClaveActual);
					Swal.fire({ icon:'success', title:'Gerencia base actualizada', timer:1300, showConfirmButton:false });
				} else {
					Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
				}
			} catch (e) {
				ocultarLoader();
				Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
			}
		}
	}

	/* ════════════════════════════════════════
	   Typeaheads de asignación: Nivel / Gerencia / Brocker
	   Insertan automático al elegir un resultado.
	════════════════════════════════════════ */
	function setupAsignacionTypeahead(opts) {
		const inp  = document.getElementById(opts.inputId);
		const drop = document.getElementById(opts.dropdownId);
		let timer  = null;

		if (!inp) return;

		inp.addEventListener('keyup', function (e) {
			if ([13, 27, 38, 40].indexOf(e.keyCode) !== -1) return;
			clearTimeout(timer);
			const q = this.value.trim();
			if (q.length < 1) { drop.classList.remove('open'); drop.innerHTML = ''; return; }

			drop.innerHTML = '<div class="nap-ta-cargando">Buscando...</div>';
			drop.classList.add('open');

			timer = setTimeout(async () => {
				try {
					const resp = await api({ action: opts.actionCatalogo, q });
					const lista = resp.datos || [];
					drop.innerHTML = '';
					if (lista.length === 0) {
						drop.innerHTML = '<div class="nap-ta-vacio">Sin resultados</div>';
					} else {
						lista.forEach(item => {
							const div = document.createElement('div');
							div.className = 'nap-ta-item';
							div.innerHTML = `<span class="nap-ta-nombre">${resaltarTexto(item.nombre, q)}</span>`;
							div.addEventListener('mousedown', async () => {
								drop.classList.remove('open');
								inp.value = '';
								await opts.onSeleccionar(item);
							});
							drop.appendChild(div);
						});
					}
					drop.classList.add('open');
				} catch (e) {
					drop.classList.remove('open');
				}
			}, 300);
		});

		inp.addEventListener('blur', function () {
			setTimeout(() => drop.classList.remove('open'), 200);
		});
	}

	setupAsignacionTypeahead({
		inputId: 'est-add-nivel-search',
		dropdownId: 'est-add-nivel-dropdown',
		actionCatalogo: 'getCatalogoNivel',
		onSeleccionar: async (item) => {
			const clave = document.getElementById('emp-id').value;
			mostrarLoader();
			try {
				const resp = await api({ action: 'asignarNivel', clave, id_nivel: item.id });
				ocultarLoader();
				if (resp.ok) {
					await cargarEstructuraEmpleado(clave);
					Swal.fire({ icon:'success', title:'Nivel asignado', timer:1200, showConfirmButton:false });
				} else {
					Swal.fire('Error', resp.msg || 'No se pudo asignar.', 'error');
				}
			} catch (e) {
				ocultarLoader();
				Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
			}
		}
	});

	setupAsignacionTypeahead({
		inputId: 'est-add-gerencia-search',
		dropdownId: 'est-add-gerencia-dropdown',
		actionCatalogo: 'getCatalogoGerencia',
		onSeleccionar: async (item) => {
			const clave = document.getElementById('emp-id').value;
			mostrarLoader();
			try {
				const resp = await api({ action: 'asignarGerencia', clave, id_gerencia: item.id });
				ocultarLoader();
				if (resp.ok) {
					await cargarEstructuraEmpleado(clave);
					Swal.fire({ icon:'success', title:'Gerencia asignada', timer:1200, showConfirmButton:false });
				} else {
					Swal.fire('Error', resp.msg || 'No se pudo asignar.', 'error');
				}
			} catch (e) {
				ocultarLoader();
				Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
			}
		}
	});

	setupAsignacionTypeahead({
		inputId: 'est-add-brocker-search',
		dropdownId: 'est-add-brocker-dropdown',
		actionCatalogo: 'getCatalogoBrocker',
		onSeleccionar: async (item) => {
			const clave = document.getElementById('emp-id').value;
			mostrarLoader();
			try {
				const resp = await api({ action: 'asignarBrocker', clave, id_brocker: item.id });
				ocultarLoader();
				if (resp.ok) {
					await cargarEstructuraEmpleado(clave);
					Swal.fire({ icon:'success', title:'Brocker asignado', timer:1200, showConfirmButton:false });
				} else {
					Swal.fire('Error', resp.msg || 'No se pudo asignar.', 'error');
				}
			} catch (e) {
				ocultarLoader();
				Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
			}
		}
	});

	/* ════════════════════════════════════════
	   FINANZAS (pestaña 5)
	════════════════════════════════════════ */
	function toggleFinFactura() {
		const activo = document.getElementById('fin-factura').checked;
		document.getElementById('lbl-fin-factura').textContent = activo ? 'Sí' : 'No';
		document.getElementById('fin-row-fec-factura').style.display = activo ? 'block' : 'none';
		if (!activo) document.getElementById('fin-fec-factura').value = '';
	}

	function toggleFinCostoAsesoria() {
		const activo = document.getElementById('fin-ase-bernal').checked;
		document.getElementById('fin-row-costo-asesoria').style.display = activo ? 'block' : 'none';
		if (!activo) document.getElementById('fin-costo-a').value = '';
	}

	async function cargarFinanzas(clave) {
		document.getElementById('fin-clave').value = clave;
		try {
			const resp = await api({ action: 'getFinanzas', clave });
			if (!resp.ok) return;
			const d = resp.datos || {};

			document.getElementById('fin-factura').checked     = d.factura == 1;
			document.getElementById('fin-fec-factura').value   = d.fec_factura   || '';
			document.getElementById('fin-regimen').value       = d.regimen_fiscal || '';
			document.getElementById('fin-nap-factura').checked = d.nap_factura == 1;
			document.getElementById('fin-nap-gerencia').checked= d.nap_gerencia == 1;
			document.getElementById('fin-ase-bernal').checked  = d.ase_bernal == 1;
			document.getElementById('fin-costo-a').value       = d.costo_a || '';

			toggleFinFactura();
			toggleFinCostoAsesoria();
		} catch (e) {
			console.error('Error cargando finanzas:', e);
		}
	}

	async function guardarFinanzas() {
		const clave = document.getElementById('fin-clave').value;
		if (!clave) return;

		const payload = {
			action: 'guardarFinanzas',
			clave,
			factura       : document.getElementById('fin-factura').checked ? 1 : 0,
			fec_factura   : document.getElementById('fin-fec-factura').value,
			regimen_fiscal: document.getElementById('fin-regimen').value.trim().toUpperCase(),
			nap_factura   : document.getElementById('fin-nap-factura').checked ? 1 : 0,
			nap_gerencia  : document.getElementById('fin-nap-gerencia').checked ? 1 : 0,
			ase_bernal    : document.getElementById('fin-ase-bernal').checked ? 1 : 0,
			costo_a       : document.getElementById('fin-costo-a').value.trim()
		};

		mostrarLoader();
		try {
			const resp = await api(payload);
			ocultarLoader();
			if (resp.ok) {
				Swal.fire({ icon:'success', title:'Guardado', text:'Información financiera actualizada.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   HISTORIAL DE MOVIMIENTOS (pestaña 6)
	════════════════════════════════════════ */
	const HIST_ETIQUETAS = {
		ingreso:   'Ingreso',
		reingreso: 'Reingreso',
		baja:      'Baja'
	};

	async function cargarHistorialMovimientos(clave) {
		document.getElementById('hist-timeline').innerHTML = '<div class="hist-vacio">Cargando...</div>';

		try {
			const resp = await api({ action: 'getHistorialMovimientos', clave });
			if (!resp.ok) return;

			const movimientos = resp.movimientos || [];
			const timeline = document.getElementById('hist-timeline');

			if (movimientos.length === 0) {
				timeline.innerHTML = '<div class="hist-vacio">Sin movimientos registrados todavía</div>';
				return;
			}

			timeline.innerHTML = movimientos.map(m => {
				const etiqueta = HIST_ETIQUETAS[m.tipo] || m.tipo;
				const fechaTxt = m.fecha_evento || 'Sin fecha registrada';
				let meta;
				if (m.inferida) {
					meta = 'Inferido: el empleado figura inactivo en el sistema, pero nunca se capturó la fecha exacta de baja.';
				} else if (m.fecha_registro) {
					meta = `Registrado el ${m.fecha_registro}`;
				} else {
					meta = 'Dato histórico (previo a este registro de movimientos)';
				}
				return `
					<div class="hist-item tipo-${m.tipo}">
						<span class="hist-item-tag">${etiqueta}</span>
						<div class="hist-item-fecha">${fechaTxt}</div>
						<div class="hist-item-meta">${meta}</div>
					</div>
				`;
			}).join('');

		} catch (e) {
			console.error('Error cargando historial:', e);
			document.getElementById('hist-timeline').innerHTML = '<div class="hist-vacio">Error al cargar</div>';
		}

		cargarHistorialEstructura(clave);
	}

	/* ── Movimientos de estructura: Gerencia (con SC/Brocker integrado) ──
	   Cada bloque es una racha de fechas (desde-hasta) en la que el
	   empleado mantuvo la misma gerencia. Si solo hay una fecha en la
	   racha, desde y hasta son iguales. El bloque "Vigente" (gerencia
	   actual, sin historial de pagos) siempre se muestra primero. */
	async function cargarHistorialEstructura(clave) {
		const renderVacio = (id) => {
			document.getElementById(id).innerHTML = '<div class="hist-estructura-vacio">Sin movimientos</div>';
		};

		document.getElementById('hist-estructura-gerencia').innerHTML = '<div class="hist-estructura-vacio">Cargando...</div>';

		const renderBloques = (id, lista) => {
			if (!lista || lista.length === 0) { renderVacio(id); return; }

			/* "Vigente" siempre va primero; el resto conserva su orden original */
			const ordenada = [...lista].sort((a, b) => {
				const aVig = (a.desde === 'Vigente') ? 1 : 0;
				const bVig = (b.desde === 'Vigente') ? 1 : 0;
				return bVig - aVig;
			});

			document.getElementById(id).innerHTML = ordenada.map(b => {
				const rango = (b.desde === b.hasta) ? b.desde : `${b.desde} — ${b.hasta}`;
				const scTxt      = (b.sc && b.sc.length)      ? b.sc.join(', ')      : '—';
				const brockerTxt = (b.brocker && b.brocker.length) ? b.brocker.join(', ') : '—';

				return `
					<div class="hist-estructura-bloque">
						<div class="hist-estructura-nombre">${b.nombre}</div>
						<div class="hist-estructura-rango">${rango}</div>
						<div class="hist-estructura-subdetalle">
							<div><span class="hist-estructura-subdetalle-label">SC:</span> ${scTxt}</div>
							<div><span class="hist-estructura-subdetalle-label">Brocker:</span> ${brockerTxt}</div>
						</div>
					</div>
				`;
			}).join('');
		};

		try {
			const resp = await api({ action: 'getHistorialEstructura', clave });
			if (!resp.ok) {
				renderVacio('hist-estructura-gerencia');
				return;
			}

			renderBloques('hist-estructura-gerencia', resp.gerencia);

		} catch (e) {
			console.error('Error cargando historial de estructura:', e);
			renderVacio('hist-estructura-gerencia');
		}
	}

	/* ════════════════════════════════════════
	   CONTACTOS DE EMERGENCIA (pestaña 7)
	════════════════════════════════════════ */
	let ceClaveActual = '';
	let ceCatalogoParentesco = [];
	let ceContactosCache = [];

	async function cargarCatalogoParentesco() {
		if (ceCatalogoParentesco.length > 0) return ceCatalogoParentesco;
		try {
			const resp = await api({ action: 'getCatalogoParentesco' });
			if (resp.ok) {
				ceCatalogoParentesco = resp.datos || [];
				const sel = document.getElementById('ce-parentesco');
				sel.innerHTML = '<option value="">— Selecciona —</option>';
				ceCatalogoParentesco.forEach(p => {
					const opt = document.createElement('option');
					opt.value = p.id;
					opt.textContent = p.nombre;
					sel.appendChild(opt);
				});
			}
		} catch (e) { console.error('Error cargando catálogo de parentesco:', e); }
		return ceCatalogoParentesco;
	}

	async function cargarContactosEmergencia(clave) {
		ceClaveActual = clave;
		await cargarCatalogoParentesco();

		document.getElementById('ce-lista').innerHTML = '<div class="ce-vacio">Cargando...</div>';

		try {
			const resp = await api({ action: 'getContactosEmergencia', clave });
			if (!resp.ok) {
				document.getElementById('ce-lista').innerHTML = '<div class="ce-vacio">Error al cargar</div>';
				return;
			}

			ceContactosCache = resp.contactos || [];
			renderListaContactos();

		} catch (e) {
			console.error('Error cargando contactos de emergencia:', e);
			document.getElementById('ce-lista').innerHTML = '<div class="ce-vacio">Error al cargar</div>';
		}
	}

	function renderListaContactos() {
		const cont = document.getElementById('ce-lista');

		if (ceContactosCache.length === 0) {
			cont.innerHTML = '<div class="ce-vacio">Sin contactos de emergencia registrados</div>';
			return;
		}

		cont.innerHTML = ceContactosCache.map((c, idx) => `
			<div class="ce-card">
				<div class="ce-card-info">
					<span class="ce-card-nombre">${c.nombre}</span>
					<span class="ce-card-parentesco">${c.parentesco}</span>
					<div class="ce-card-meta">📞 ${c.numero}</div>
				</div>
				<div class="ce-card-acciones">
					<button class="bnc-btn-icon" title="Editar" onclick="editarContacto(${idx})">&#9998;</button>
					<button class="bnc-btn-icon" title="Eliminar" onclick="eliminarContacto(${idx})">🗑️</button>
				</div>
			</div>
		`).join('');
	}

	function abrirFormContacto() {
		document.getElementById('ce-editando-id').value = '';
		document.getElementById('ce-nombre').value = '';
		document.getElementById('ce-numero').value = '';
		document.getElementById('ce-parentesco').value = '';
		document.getElementById('ce-form-wrap').style.display = 'block';
		document.getElementById('ce-nombre').focus();
	}

	function cerrarFormContacto() {
		document.getElementById('ce-form-wrap').style.display = 'none';
	}

	function editarContacto(idx) {
		const c = ceContactosCache[idx];
		if (!c) return;

		document.getElementById('ce-editando-id').value = c.id;
		document.getElementById('ce-nombre').value = c.nombre || '';
		document.getElementById('ce-numero').value = c.numero || '';
		document.getElementById('ce-parentesco').value = c.id_parentesco || '';
		document.getElementById('ce-form-wrap').style.display = 'block';
	}

	async function guardarContacto() {
		const nombre = document.getElementById('ce-nombre').value.trim().toUpperCase();
		const numero = document.getElementById('ce-numero').value.trim();
		const idParentesco = document.getElementById('ce-parentesco').value;
		const idEditando = document.getElementById('ce-editando-id').value;

		if (!nombre) { Swal.fire('Campo requerido', 'Escribe el nombre completo.', 'warning'); return; }
		if (!numero) { Swal.fire('Campo requerido', 'Escribe el número de teléfono.', 'warning'); return; }
		if (!idParentesco) { Swal.fire('Campo requerido', 'Selecciona el parentesco.', 'warning'); return; }

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarContactoEmergencia',
				id: idEditando,
				clave: ceClaveActual,
				nombre, numero,
				id_parentesco: idParentesco
			});
			ocultarLoader();
			if (resp.ok) {
				cerrarFormContacto();
				await cargarContactosEmergencia(ceClaveActual);
				Swal.fire({ icon:'success', title:'Guardado', text: resp.msg || 'Contacto guardado.', timer:1800, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function eliminarContacto(idx) {
		const c = ceContactosCache[idx];
		if (!c) return;

		const result = await Swal.fire({
			title: '¿Eliminar contacto de emergencia?',
			html: `Vas a eliminar a <strong>${c.nombre}</strong> de los contactos de emergencia.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#c0392b'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'eliminarContactoEmergencia', id: c.id, clave: ceClaveActual });
			ocultarLoader();
			if (resp.ok) {
				await cargarContactosEmergencia(ceClaveActual);
				Swal.fire({ icon:'success', title:'Eliminado', timer:1500, showConfirmButton:false });
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo eliminar.', 'error');
			}
		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Pestañas
	════════════════════════════════════════ */
	document.addEventListener('click', function (e) {
		const tab = e.target.closest('.nap-tab');
		if (tab) activarTab(tab.dataset.pane);
	});

	/* ════════════════════════════════════════
	   Eventos del sistema (jQuery los necesita
	   carobra.js, se dejan solo los del sistema)
	════════════════════════════════════════ */
	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		document.getElementById('salir').addEventListener('click', () => {
			window.location = 'https://www.sis-ca.com.mx:7863/';
		});
		document.getElementById('email').addEventListener('click', () => {
			window.open('http://webmail.carobra.com.mx/imp/login.php');
		});

		/* Permisos de pestañas: ocultar lo que no le corresponde al usuario
		   en sesión, y si puede ver el botón "Permisos", precargar el
		   selector de niveles del modal (lote por nivel). */
		inicializarVisibilidadPestanas();
		if (window.NAP_PUEDE_PERMISOS_TOP) {
			cargarNivelesLote();
		}
	});

	</script>
</html>