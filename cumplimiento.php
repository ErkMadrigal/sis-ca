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

		<!-- compiled function datepicker -->
		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		        <style>
                /* =========================
            FILTROS
            ========================= */
            .filtros-wrapper {
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 14px;
                padding: 5px 10px !important;
                margin-bottom: 10px;
            }
            .filtros-wrapper label {
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #7b8794;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 5px;
            }
            .filtros-wrapper .form-control {
                font-size: 13px;
                border: 1px solid #dce3eb;
                border-radius: 8px;
                padding: 7px 11px;
                height: auto;
                color: #1f2d3d;
                background: #fafbfc;
                transition: border-color .15s, box-shadow .15s;
            }
            .filtros-wrapper .form-control:focus {
                border-color: #185FA5;
                box-shadow: 0 0 0 3px rgba(24,95,165,.1);
                background: #fff;
                outline: none;
            }

            /* =========================
            TABLA PRINCIPAL
            ========================= */
            .tabla-comisiones-wrapper {
                width: 100%;
                overflow-x: auto;
                overflow-y: auto;
                border: 1px solid #e8edf3;
                border-radius: 14px;
                background: #fff;
                margin-top: 16px;
            }
            .tabla-comisiones {
                width: 100%;
                min-width: 1200px;
                border-collapse: collapse;
                background: #fff;
            }
            .tabla-comisiones thead tr {
                background: #f4f7fb;
                border-bottom: 1px solid #e8edf3;
            }
            .tabla-comisiones thead th {
                color: #7b8794;
                padding: 6px 7px;
                text-align: left;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .06em;
                white-space: nowrap;
                border: none;
                position: sticky;
                top: 0;
                z-index: 2;
                background: #f4f7fb;
            }
            .tabla-comisiones tbody td {
                padding: 6px 7px;
                text-align: left;
                vertical-align: middle;
                font-size: 11px;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
            }
            .tabla-comisiones tbody tr:last-child td { border-bottom: none; }
            .tabla-comisiones tbody tr:hover { background: #f7faff; cursor: pointer; }

            .td-persona { font-weight: 500; color: #1f2d3d; white-space: nowrap; }
            .td-saldo   { font-weight: 600; color: #185FA5; font-variant-numeric: tabular-nums; }
            .td-puntos  { font-weight: 600; color: #854F0B; }
            .td-bono    { font-weight: 600; color: #0F6E56; }

            /* Badges de categoría */
            .badge-categoria {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .04em;
            }
            .bronze   { background: #FAC775; color: #412402; }
            .plata    { background: #D3D1C7; color: #2C2C2A; }
            .oro      { background: #EF9F27; color: #412402; }
            .diamante { background: #85B7EB; color: #042C53; }
            .platinum { background: #B5D4F4; color: #042C53; }

            /* =========================
            MODAL DETALLE CUMPLIMIENTO
            ========================= */
            .modal-backdrop { z-index: 9998 !important; }
            #modalDetalleCumplimiento { z-index: 999999 !important; }
            #modalDetalleCumplimiento .modal-dialog {
                max-width: 92vw;
                width: 92vw;
                margin: 20px auto;
            }
            #modalDetalleCumplimiento .modal-content {
                border-radius: 16px;
                overflow: hidden;
                border: none;
                box-shadow: 0 24px 64px rgba(0,0,0,.22);
            }
            #modalDetalleCumplimiento .modal-header {
                background: #185FA5;
                padding: 18px 24px;
                border: none;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            #modalDetalleCumplimiento .modal-title {
                color: #fff;
                font-size: 16px;
                font-weight: 600;
            }
            #modalDetalleCumplimiento .close {
                color: rgba(255,255,255,.8);
                opacity: 1;
                font-size: 22px;
                text-shadow: none;
                background: rgba(255,255,255,.12);
                border-radius: 6px;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                line-height: 1;
                transition: background .15s;
            }
            #modalDetalleCumplimiento .close:hover {
                background: rgba(255,255,255,.22);
                color: #fff;
            }
            #modalDetalleCumplimiento .modal-body {
                background: #f4f7fb;
                padding: 24px;
            }

            /* =========================
            CARDS INFO
            ========================= */
            .detalle-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e8edf3;
                height: 100%;
            }
            .detalle-card .card-body { padding: 16px 18px; }
            .detalle-title {
                font-size: 11px;
                text-transform: uppercase;
                color: #7b8794;
                margin-bottom: 6px;
                font-weight: 600;
                letter-spacing: .07em;
            }
            .detalle-value  { font-size: 15px; font-weight: 600; color: #1f2d3d; line-height: 1.4; }
            .detalle-persona { font-size: 14px; font-weight: 600; color: #1f2d3d; line-height: 1.4; word-break: break-word; }

            /* =========================
            STAT CARDS
            ========================= */
            .detalle-mini { border-radius: 12px; border: none; overflow: hidden; color: #fff; }
            .detalle-mini .card-body { padding: 18px 14px; }
            .detalle-mini-title {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .07em;
                opacity: .85;
                margin-bottom: 6px;
            }
            .detalle-mini-value { font-size: 28px; font-weight: 700; line-height: 1.1; }
            .stat-avs { background: #185FA5; }
            .stat-reg { background: #3B6D11; }
            .stat-pts { background: #854F0B; }
            .stat-cat { background: #3C3434; }

            /* =========================
            TOTALES (modal detalle)
            ========================= */
            .detalle-total-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e8edf3;
                height: 100%;
            }
            .detalle-total-card .card-body { padding: 18px; }
            .detalle-total-label {
                font-size: 11px;
                text-transform: uppercase;
                color: #7b8794;
                font-weight: 600;
                letter-spacing: .07em;
                margin-bottom: 6px;
            }
            .detalle-total-value { font-size: 26px; font-weight: 700; line-height: 1.2; }
            .tv-saldo { color: #185FA5; }
            .tv-bono  { color: #0F6E56; }

            /* =========================
            TABLA DETALLE (modal)
            ========================= */
            .detalle-table-wrapper {
                overflow-x: auto;
                border-radius: 12px;
                border: 1px solid #e8edf3;
                background: #fff;
            }
            .detalle-table {
                width: 100%;
                min-width: 900px;
                border-collapse: collapse;
                font-size: 12px;
            }
            .detalle-table thead tr { background: #f4f7fb; border-bottom: 1px solid #e8edf3; }
            .detalle-table thead th {
                color: #7b8794;
                padding: 11px 13px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .05em;
                white-space: nowrap;
            }
            .detalle-table tbody td {
                padding: 11px 13px;
                font-size: 12px;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
                white-space: nowrap;
            }
            .detalle-table tbody tr:last-child td { border-bottom: none; }
            .detalle-table tbody tr:hover { background: #f7faff; }
            .detalle-section-title { font-size: 13px; font-weight: 600; color: #1f2d3d; margin: 0 0 12px; }

            /* =========================
            BOTÓN BUSCAR
            ========================= */
            .btn-filtro-buscar {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 100%;
                padding: 8px 16px;
                background: #185FA5;
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background .15s;
                height: 31px;
            }
            .btn-filtro-buscar:hover { background: #0C447C; }

            /* =========================
            SWITCH SC / BROCKER / GERENCIA
            ========================= */
            .filtro-switch-group {
                display: inline-flex;
                background: #185FA5;
                border-radius: 10px;
                padding: 4px;
                gap: 2px;
            }
            .filtro-switch-btn {
                padding: 6px 18px;
                border: none;
                border-radius: 7px;
                font-size: 12px;
                font-weight: 600;
                color: #edeff2;
                background: transparent;
                cursor: pointer;
                transition: background .15s, color .15s;
            }
            .filtro-switch-btn.active {
                background: #fff;
                color: #185FA5;
                box-shadow: 0 1px 4px rgba(0,0,0,.1);
            }
            .filtro-switch-btn:hover:not(.active) { color: #185FA5; }

            .filtros-wrapper .row.align-items-end > [class*="col"] {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }

            /* =========================
            ACCIONES TABLA (Ver / Descargar)
            ========================= */
            .td-acciones { white-space: nowrap; text-align: center !important; }
            .btn-accion-pf {
                display: inline-flex; align-items: center; justify-content: center;
                width: 26px; height: 26px; border-radius: 7px; border: 1px solid #dce3eb;
                background: #fff; color: #185FA5; cursor: pointer; margin: 0 2px;
                transition: background .15s, border-color .15s;
            }
            .btn-accion-pf:hover { background: #eaf2fb; border-color: #185FA5; }
            .btn-accion-pf.descargar { color: #0F6E56; }
            .btn-accion-pf.descargar:hover { background: #eafaf5; border-color: #0F6E56; }

            /* =========================
            MODAL PREFACTURA
            ========================= */
            .pf-backdrop {
                display: none; position: fixed; inset: 0; z-index: 1000000;
                background: rgba(15,32,68,.5); backdrop-filter: blur(4px);
                align-items: center; justify-content: center; padding: 24px;
            }
            .pf-backdrop.open { display: flex; }
            .pf-modal {
                background: #fff; border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,.22);
                width: 100%; max-width: 820px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;
            }
            .pf-modal-header {
                background: #185FA5; padding: 16px 22px; display: flex; align-items: center; justify-content: space-between;
            }
            .pf-modal-header h3 { color: #fff; font-size: 15px; font-weight: 600; margin: 0; }
            .pf-modal-header .pf-sub { color: rgba(255,255,255,.75); font-size: 11px; margin-top: 2px; }
            .pf-modal-close {
                background: rgba(255,255,255,.15); border: none; color: #fff; width: 28px; height: 28px;
                border-radius: 6px; cursor: pointer; font-size: 16px;
            }
            .pf-modal-body { flex: 1; overflow-y: auto; padding: 0; }
            .pf-modal-footer {
                padding: 12px 22px; border-top: 1px solid #e8edf3; background: #f4f7fb;
                display: flex; justify-content: flex-end; gap: 10px;
            }

            .pf-section { padding: 18px 22px; border-bottom: 1px solid #f0f4f8; }
            .pf-section:last-child { border-bottom: none; }
            .pf-section-title {
                font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
                color: #185FA5; margin-bottom: 10px;
            }
            .pf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
            .pf-field label { display: block; font-size: 10px; font-weight: 600; color: #7b8794; text-transform: uppercase; margin-bottom: 3px; }
            .pf-field .pf-val { font-size: 13px; font-weight: 600; color: #1f2d3d; }
            .pf-warn {
                background: #fff8e6; border: 1px solid #f4d992; color: #8a6116;
                font-size: 11px; padding: 8px 12px; border-radius: 8px; margin-bottom: 12px;
            }

            /* contenedor oculto usado para descarga directa sin abrir el modal */
            #pf-hidden-render { position: fixed; left: -99999px; top: 0; width: 820px; background: #fff; }

            /* =========================
            BLOQUE ESTILO EXCEL: CONCEPTOS + TOTALES
            ========================= */
            .pf-xls-wrap { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; padding: 0 22px 20px; }

            .pf-xls-titlebar {
                background: #2f6a70; color: #fff; font-weight: 700; font-size: 13px;
                padding: 7px 12px; border: 1px solid #1f4d52;
            }

            .pf-xls-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .pf-xls-table th {
                background: #3f8288; color: #fff; font-weight: 700; font-size: 11px;
                text-align: center; padding: 8px 6px; border: 1px solid #1f4d52;
                line-height: 1.25;
            }
            .pf-xls-table td {
                border: 1px solid #c9c9c9; font-size: 12px; padding: 6px 8px;
                color: #000; background: #fff; vertical-align: middle;
            }
            .pf-xls-table tr.pf-xls-row-filled td { background: #fdf6d8; }
            .pf-xls-table tr.pf-xls-row-empty td { background: #fdf6d8; height: 22px; }

            .pf-xls-blue   { color: #1a3fa0 !important; }
            .pf-xls-right  { text-align: right; }
            .pf-xls-center { text-align: center; }

            .pf-xls-totales-wrap { display: flex; margin-top: 0; }
            .pf-xls-totales-spacer { flex: 1; }
            .pf-xls-totales { width: 380px; border-collapse: collapse; }
            .pf-xls-totales td {
                border: 1px solid #c9c9c9; font-size: 12px; padding: 6px 12px;
            }
            .pf-xls-totales tr:nth-child(odd)  td { background: #eef2f5; }
            .pf-xls-totales tr:nth-child(even) td { background: #ffffff; }
            .pf-xls-tlabel { font-weight: 700; color: #1f2d3d; text-align: right; }
            .pf-xls-tval   { text-align: right; font-weight: 600; color: #1f2d3d; }
            .pf-xls-tval.pf-xls-blue { color: #1a3fa0 !important; font-weight: 700; }
            .pf-xls-total-row td {
                background: #2f6a70 !important; color: #fff !important;
                font-weight: 700; font-size: 14px;
            }
            .pf-xls-letra { font-size: 11px; color: #555; font-style: italic; padding: 8px 4px 0; }
			/* ── Bloques de info (Emisor / Receptor / Generales) estilo Excel ── */
			.pf-xls-info-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
			.pf-xls-info-table td {
				border: 1px solid #c9c9c9; font-size: 12px; padding: 8px 10px; vertical-align: middle;
			}
			.pf-xls-info-lbl { font-weight: 700; color: #000; background: #fff; }
			.pf-xls-info-val { color: #1a3fa0; font-weight: 500; background: #fff; }
			.pf-xls-info-table tr:nth-child(even) .pf-xls-info-val,
			.pf-xls-info-table tr:nth-child(even) .pf-xls-info-lbl { background: #fdf6d8; }

			.pf-xls-info-val input {
				border: none; background: transparent; width: 100%; color: #1a3fa0;
				font-size: 12px; font-weight: 500; font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
			}
			.pf-xls-info-val input:focus { outline: 1px solid #185FA5; background: #fff; }

			.pf-editable {
				min-height: 16px; outline: none; color: #1a3fa0; font-size: 12px;
				font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; cursor: text;
			}
			.pf-editable:focus { background: #fff; box-shadow: inset 0 0 0 1px #185FA5; border-radius: 2px; }
			.pf-editable:empty::before {
				content: attr(data-placeholder); color: #b7b7b7; font-style: italic;
			}
			.pf-xls-spacer { height: 14px; }

            /* =========================
            RESPONSIVE
            ========================= */
            @media (max-width: 768px) {
                #modalDetalleCumplimiento .modal-dialog { width: 100vw; max-width: 100vw; margin: 0; }
                #modalDetalleCumplimiento .modal-content { border-radius: 0; min-height: 100vh; }
                #modalDetalleCumplimiento .modal-body { padding: 14px; }
                .detalle-value, .detalle-persona { font-size: 13px; }
                .detalle-mini-value { font-size: 22px; }
                .detalle-total-value { font-size: 20px; }
                .pf-modal { max-width: 100vw; max-height: 100vh; border-radius: 0; }
                .pf-grid { grid-template-columns: 1fr; }
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
			$_SESSION["autentifica"]  = "p";
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
				titulo("Seguimiento de puntos NAP");
				$pag="PAGOS_Y_FACTURAS_NAP";
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
				<div id="vinculos" style="margin-top: 10px; margin-left: 25px;">
					<?php include("vinculos.php");?>
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

				$conecta = new conector();
                $nombre=$_SESSION["nombre"];
                $usrPruebas = false;

                if ($nombre == "USUARIO DE PRUEBA") {
                    $usrPruebas = true;
                    $nombre = "EDMUNDO LOPEZ ALFARO";
                }

                if ($nombre == "USUARIO TEMPORAL") {
                    $usrPruebas = true;
                    $nombre = "MELQUIADES MARTINEZ HERNANDEZ";
                }

                $namePruebasUsr = $_SESSION["nombre"];

				$nivel   = nivel_usuario(utf8_decode($nombre));
				$query   = "Select clave from empl_usuarios where nombre='" . utf8_decode($nombre) . "'";
				$bd      = $conecta->consulta($query);
				$tb      = mysqli_fetch_array($bd);
				$clave   = $tb['clave'];
				$ne      = 0;
				$usuario = $nombre;
	?>

	<div class="container">
		<div class="row justify-content-center mt-0 pt-0">
			<div class="col-md-12 mt-0 pt-0">

				<!-- FILTROS -->
				<div class="filtros-wrapper">
					<div class="row align-items-end m-0 p-0">

						<!-- Año -->
						<div class="col-md-2 col-2 mb-3 text-left">
							<label for="anio">Año</label>
							<select class="form-control form-control-sm carobra" name="anio" id="anio">
								<option value=""></option>
								<?php for($i = date('Y'); $i >= 2026; $i--): ?>
									<option value="<?= $i ?>"><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>

						<!-- Periodo -->
						<div class="col-md-2 col-2 mb-3 text-left">
							<label for="sel-periodo">Periodo</label>
							<select name="sel-periodo" id="sel-periodo" class="form-control form-control-sm carobra" required>
								<option value=""></option>
								<?php
									$sqlPeriodos = "Select DISTINCT LEFT(campana,10) as periodo 
													FROM carobra_2014.fec_cierre_campana 
													WHERE STR_TO_DATE(fec_ini,'%d/%m/%Y') >= '2026-04-11'
													ORDER BY periodo";
									$resPeriodos = $conecta->consulta($sqlPeriodos);
									while($p = mysqli_fetch_assoc($resPeriodos)):
								?>
									<option value="<?= htmlspecialchars($p['periodo']) ?>"><?= htmlspecialchars($p['periodo']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>

						<!-- NAP -->
						<div class="col-md-6 col-6 mb-3" style="text-align: left;">
							<label for="nap">NAP</label>
							<input type="text" id="nap" class="form-control form-control-sm carobra"
								name="nap"
								data-n="<?= $nivel ?>"
								placeholder="Buscar NAP..." />
						</div>
						<script>autocompletar('nap', 4, 'nap_sv', $("#nap").data('n'));</script>

						<!-- Buscar -->
						<div class="col-md-2 col-2 mb-3">
							<label style="visibility:hidden">x</label>
							<button class="btn-filtro-buscar w-100" id="buscar">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
									fill="none" stroke="currentColor" stroke-width="2.5"
									stroke-linecap="round" stroke-linejoin="round">
									<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
								</svg>
								Buscar
							</button>
						</div>

					</div>

					<!-- Switch + inputs -->
					<div class="row align-items-end p-0 m-0">
						<!-- Switch -->
						<div class="col-4 mb-3 text-left">
							<div class="filtro-switch-group">
								<?php if($nivel != 3 && $nivel != 4 && $nivel != 5): ?>
									<button type="button" class="filtro-switch-btn active" data-target="grp-sc">SC</button>
								<?php endif; ?>
								<?php if($nivel != 3 && $nivel != 4): ?>
									<button type="button" class="filtro-switch-btn <?= ($nivel == 5) ? 'active' : '' ?>" data-target="grp-brocker">Brocker</button>
								<?php endif; ?>
								<?php if($nivel != 3): ?>
									<button type="button" class="filtro-switch-btn <?= ($nivel == 4) ? 'active' : '' ?>" data-target="grp-gerencia">Gerencia</button>
								<?php endif; ?>
							</div>
						</div>

						<!-- SC -->
						<?php if($nivel != 3 && $nivel != 4 && $nivel != 5): ?>
							<div class="col-md-8 mb-3 filtro-group" id="grp-sc" style="text-align: left;">
								<label>SC</label>
								<input type="text" id="txt-sc-nombre" class="form-control form-control-sm carobra"
									name="txt-sc-nombre"
									data-n="<?= $nivel ?>"
									data-fijo="<?= ($nivel == 13) ? '1' : '0' ?>"
									placeholder="Nombre SC..."
									value="<?= ($nivel == 13) ? htmlspecialchars($_SESSION['nombre']) : '' ?>" />
							</div>
							<?php if($nivel != 13): ?>
								<script>autocompletar('txt-sc-nombre', 4, 'sc_to', $("#txt-sc-nombre").data('n'));</script>
							<?php endif; ?>
						<?php endif; ?>

						<!-- Brocker -->
						<?php if($nivel != 3 && $nivel != 4): ?>
							<div class="col-md-8 mb-3 filtro-group" id="grp-brocker" <?= ($nivel != 5) ? 'style="display:none;text-align: left;"' : '' ?>>
								<label>Brocker</label>
								<input type="text" id="txt-brocker-nombre" class="form-control form-control-sm carobra"
									name="txt-brocker-nombre"
									data-n="<?= $nivel ?>"
									data-fijo="<?= ($nivel == 5) ? '1' : '0' ?>"
									placeholder="Nombre brocker..."
									value="<?= ($nivel == 5) ? htmlspecialchars($_SESSION['nombre']) : '' ?>" />
							</div>
							<?php if($nivel != 5): ?>
								<script>autocompletar('txt-brocker-nombre', 4, 'sb_sv', $("#txt-brocker-nombre").data('n'));</script>
							<?php endif; ?>
						<?php endif; ?>

						<!-- Gerencia -->
						<?php if($nivel != 3): ?>
							<div class="col-md-8 mb-3 filtro-group" id="grp-gerencia" <?= ($nivel != 4) ? 'style="display:none;text-align: left;"' : '' ?>>
								<label>Gerencia</label>
								<input type="text" id="txt-gerencia-nombre" class="form-control form-control-sm carobra"
									name="txt-gerencia-nombre"
									data-n="<?= $nivel ?>"
									data-fijo="0"
									placeholder="Nombre gerencia..." />
							</div>
							<?php if($nivel != 4): ?>
								<script>autocompletar('txt-gerencia-nombre', 4, 'gerencia_to', $("#txt-gerencia-nombre").data('n'));</script>
							<?php endif; ?>
						<?php endif; ?>

					</div>
				</div>

				<hr>

				<div class="container-table">
					<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
						<h4 style="margin:0;">ESQUEMA DE COMISIONES</h4>
						<button id="btn-exportar-xlsx" style="
							display:flex; align-items:center; gap:6px;
							padding:7px 16px;
							background:#185FA5; color:#fff;
							border:none; border-radius:8px;
							font-size:13px; font-weight:600;
							cursor:pointer;
						">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
								fill="none" stroke="currentColor" stroke-width="2.5"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
								<polyline points="7 10 12 15 17 10"/>
								<line x1="12" y1="15" x2="12" y2="3"/>
							</svg>
							Exportar XLSX
						</button>
					</div>

					<div class="tabla-comisiones-wrapper">
						<table class="tabla-comisiones table-striped">
							<thead>
								<tr>
									<th>SC</th>
									<th>Brocker</th>
									<th>Gerencia</th>
									<th>Nap</th>
									<th>Propietario</th>
									<th>RFC</th>
									<th>AVS</th>
									<th>Registros</th>
									<th>Saldo Total</th>
									<th>Puntos</th>
									<th>Categoría</th>
									<th>Comisión</th>
									<th>Regimen Fiscal</th>
									<th>IVA</th>
									<th>Ret. IVA</th>
									<th>Ret. ISR</th>
									<th>Total</th>
									<th>PreFactura</th>

								</tr>
							</thead>
							<tbody id="body-detalles">
								<tr>
									<td colspan="15" class="text-center text-muted">
										No hay resultados
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>
	</div>

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
		$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
	</script>

	<div class="modal fade" id="modalDetalleCumplimiento" tabindex="-1">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Detalle De Cumplimiento</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body" id="detalle-modal-body"></div>
			</div>
		</div>
	</div>
	<div class="pf-backdrop" id="pfBackdrop">
		<div class="pf-modal">
			<div class="pf-modal-header">
				<div>
					<h3>Prefactura — Formato de captura</h3>
					<div class="pf-sub" id="pfHeaderSub"></div>
				</div>
				<button class="pf-modal-close" id="pfClose">✕</button>
			</div>
			<div class="pf-modal-body">
				<div id="pfDoc"></div>
			</div>
			<div class="pf-modal-footer">
				<button class="btn-filtro-buscar" style="width:auto;background:#0F6E56;" id="pfBtnDescargar">⬇ Descargar PDF</button>
			</div>
		</div>
	</div>

	<div id="pf-hidden-render"></div>

	<?php } ?>

	</body>

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

	<script type="text/javascript">

    var usrPruebas = <?= json_encode($usrPruebas ?? false) ?>;
    var namePruebasUsr = <?= json_encode($namePruebasUsr ?? '') ?>;
    var nivel = <?= json_encode($nivel) ?>;

    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();

        $('#men_pie').css("top", h);
        $('#men_pie').css("background", "red");

        $('#tmenu td').click(function(evento) {
            var modulo = $(this).text().replace(/ /gi, "_");
            $("#opcion").load("menu.php?modulo=" + modulo);
        });

        $("#salir").click(function() {
            window.location = "https://www.sis-ca.com.mx:7863/";
        });

        $("#email").click(function() {
            window.open("http://webmail.carobra.com.mx/imp/login.php");
        });

        $(".datepicker").datepicker({ format: "dd/mm/yyyy" });

        $(".filtro").click(function() {
            $("#op").val(1);
            $("#valor1").val('');
            $("#valor2").val('');
            $("#valor3").val('');
            $("#valor4").val('');
            $("#valor5").val('');
            $("#filtro").val($(this).text());
            $("#frm-filtro").submit();
        });

        ajustarAltoTablaComisiones();
        $(window).on("resize", ajustarAltoTablaComisiones);
    });


    // =========================
    // ALTURA TABLA
    // =========================
    function ajustarAltoTablaComisiones() {
        const wrapper = document.querySelector(".tabla-comisiones-wrapper");
        if (!wrapper) return;

        const wrapperRect  = wrapper.getBoundingClientRect();
        const contenedor   = document.getElementById("contenedor");
        let limiteInferior = window.innerHeight - 24;

        if (contenedor) {
            const contenedorRect = contenedor.getBoundingClientRect();
            limiteInferior = Math.min(limiteInferior, contenedorRect.bottom - 16);
        }

        const altoDisponible = Math.max(180, limiteInferior - wrapperRect.top);
        wrapper.style.maxHeight = altoDisponible + "px";
        wrapper.style.overflowY = "auto";
    }

    // =========================
    // OBTENER CUMPLIMIENTO
    // =========================
    async function obtenerCumplimiento() {

        const periodo  = document.getElementById("sel-periodo").value;
        const anio     = document.getElementById("anio").value;
        const nap      = document.getElementById("nap").value;
        const sc       = document.getElementById("txt-sc-nombre")?.value ?? "";
        const brocker  = document.getElementById("txt-brocker-nombre")?.value ?? "";
        const gerencia = document.getElementById("txt-gerencia-nombre")?.value ?? "";

        const tbody = document.getElementById("body-detalles");

        tbody.innerHTML = `
            <tr>
                <td colspan="16" class="text-center text-muted" style="padding:30px;font-size:13px;">
                    <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                    Consultando...
                </td>
            </tr>
        `;
        ajustarAltoTablaComisiones();

        try {
            const response = await fetch("cumplimiento_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "getCumplimiento", periodo, anio, nap, sc, brocker, gerencia, nivel })
            });

            const result = await response.json();
            window._datosEsquema = result.data || [];

            if (result.status == "error") {
                Swal.fire({ icon: "error", title: "Error", text: result.message });
            }

            tbody.innerHTML = "";

            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="16" class="text-center text-muted" style="padding:30px;font-size:13px;">
                            Sin resultados para los filtros seleccionados
                        </td>
                    </tr>
                `;
                ajustarAltoTablaComisiones();
                return;
            }

            result.data.forEach(row => {
                let badgeClass = '';
                switch ((row.categoria || '').toUpperCase()) {
                    case 'BRONCE':   badgeClass = 'bronze';   break;
                    case 'PLATA':    badgeClass = 'plata';    break;
                    case 'ORO':      badgeClass = 'oro';      break;
                    case 'DIAMANTE': badgeClass = 'diamante'; break;
                    case 'PLATINUM': badgeClass = 'platinum'; break;
                }

                const saldo   = parseFloat(row.saldo_total || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const bono    = parseFloat(row.bono        || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const iva     = parseFloat(row.iva         || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const ret_iva = parseFloat(row.ret_iva     || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const ret_isr = parseFloat(row.ret_isr     || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const total   = parseFloat(row.total       || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
                tbody.innerHTML += `
                    <tr onclick='abrirDetalleCumplimiento(${JSON.stringify(row)})'>
                        <td class="td-persona">${row.sc       ?? ''}</td>
                        <td class="td-persona">${row.brocker  ?? ''}</td>
                        <td class="td-persona">${row.gerencia ?? ''}</td>
                        <td class="td-persona">${row.nap ?? ''}</td>
                        <td class="td-persona">${usrPruebas ? namePruebasUsr : (row.nombre ?? '')}</td>
                        <td class="td-persona">${row.rfc ?? ''}</td>
                        <td>${row.avs ?? ''}</td>
                        <td>${row.registros ?? ''}</td>
                        <td class="td-saldo">$${saldo}</td>
                        <td class="td-puntos">${row.puntos ?? 0}</td>
                        <td><span class="badge-categoria ${badgeClass}">${row.categoria ?? ''}</span></td>
                        <td class="td-bono">$${bono}</td>
                        <td>${row.regimen ?? ''}</td>
                        <td class="td-bono">$${iva}</td>
                        <td class="td-bono">$${ret_iva}</td>
                        <td class="td-bono">$${ret_isr}</td>
                        <td class="td-bono">$${total}</td>
                        <td class="td-acciones">
                            <button class="btn-accion-pf" title="Ver prefactura"
                                onclick='event.stopPropagation(); abrirPrefactura(${JSON.stringify(row)})'>👁</button>
                        </td>
                    </tr>
                `;
            });

            ajustarAltoTablaComisiones();

            // Precarga el detalle de TODOS los NAPs en paralelo
            // (usa periodo y anio ya declarados arriba, sin redeclararlos)
            window._todoElDetalle = [];

            const promesas = result.data.map(row =>
                fetch("cumplimiento_api.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "getDetalle", nap: row.nap, anio, periodo })
                })
                .then(r => r.json())
                .then(r => {
                    if (r.data) window._todoElDetalle.push(...r.data);
                })
                .catch(() => {})
            );

            await Promise.all(promesas);

        } catch (error) {
            //console.error(error);
            alert(error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="16" class="text-center text-danger" style="padding:30px;font-size:13px;">
                        Error al consultar los datos
                    </td>
                </tr>
            `;
            ajustarAltoTablaComisiones();
        }
    }

    // =========================
    // ABRIR DETALLE (modal)
    // =========================
    async function abrirDetalleCumplimiento(row) {

        $("#modalDetalleCumplimiento").modal({ backdrop: true, keyboard: true });
        $("#modalDetalleCumplimiento").modal("show");

        document.getElementById("detalle-modal-body").innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
                <div class="mt-3 text-muted" style="font-size:13px;">Cargando detalle...</div>
            </div>
        `;

        try {
            const periodo = document.querySelector("#sel-periodo").value;
            const anio    = document.querySelector("#anio").value;

            const response = await fetch("cumplimiento_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "getDetalle", nap: row.nap, anio, periodo })
            });

            const result = await response.json();
            window._ultimoDetalle = result.data || [];

            let detalleHTML = '';

            if (result.data && result.data.length > 0) {
                result.data.forEach(d => {
                    const saldoD = parseFloat(d.saldo || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    detalleHTML += `
                        <tr>
                            <td>${d.anio         ?? ''}</td>
                            <td>${d.periodo      ?? ''}</td>
                            <td>${d.fecha        ?? ''}</td>
                            <td>${d.sc      ?? ''}</td>
                            <td>${d.brocker      ?? ''}</td>
                            <td>${d.gerencia      ?? ''}</td>
                            <td>${d.folio        ?? ''}</td>
                            <td>${d.trabajador   ?? ''}</td>
                            <td class="td-saldo">$${saldoD}</td>
                            <td>${d.nap          ?? ''}</td>
                            <td>${d.avs          ?? ''}</td>
                            <td>${d.registros    ?? ''}</td>
                            <td>${d.aclaraciones ?? ''}</td>
                            <td>${d.estatus_cap  ?? ''}</td>
                        </tr>
                    `;
                });
            } else {
                detalleHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-muted" style="padding:20px;font-size:13px;">
                            Sin registros de detalle
                        </td>
                    </tr>
                `;
            }

            const saldoTotal = parseFloat(row.saldo_total || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const bonoTotal  = parseFloat(row.bono        || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById("detalle-modal-body").innerHTML = `
                <div class="container-fluid px-0">

                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <div class="card detalle-card"><div class="card-body">
                                <div class="detalle-title">SC</div>
                                <div class="detalle-value">${row.sc ?? ''}</div>
                            </div></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card detalle-card"><div class="card-body">
                                <div class="detalle-title">Brocker</div>
                                <div class="detalle-value">${row.brocker ?? ''}</div>
                            </div></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card detalle-card"><div class="card-body">
                                <div class="detalle-title">Gerencia</div>
                                <div class="detalle-value">${row.gerencia ?? ''}</div>
                            </div></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <div class="card detalle-card"><div class="card-body">
                                <div class="detalle-title">NAP</div>
                                <div class="detalle-value">${row.nap ?? ''}</div>
                            </div></div>
                        </div>
                        <div class="col-md-8 mb-2">
                            <div class="card detalle-card"><div class="card-body">
                                <div class="detalle-title">Nombre</div>
                                <div class="detalle-persona">${usrPruebas ? namePruebasUsr : (row.nombre ?? '')}</div>
                            </div></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6 col-md-3 mb-2">
                            <div class="card detalle-mini stat-avs"><div class="card-body text-center">
                                <div class="detalle-mini-title">AVS</div>
                                <div class="detalle-mini-value">${row.avs ?? 0}</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="card detalle-mini stat-reg"><div class="card-body text-center">
                                <div class="detalle-mini-title">Registros</div>
                                <div class="detalle-mini-value">${row.registros ?? 0}</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="card detalle-mini stat-pts"><div class="card-body text-center">
                                <div class="detalle-mini-title">Puntos</div>
                                <div class="detalle-mini-value">${row.puntos ?? 0}</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="card detalle-mini stat-cat"><div class="card-body text-center">
                                <div class="detalle-mini-title">Categoría</div>
                                <div class="detalle-mini-value" style="font-size:18px;">${row.categoria ?? ''}</div>
                            </div></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-2">
                            <div class="card detalle-total-card"><div class="card-body text-center">
                                <div class="detalle-total-label">Saldo total</div>
                                <div class="detalle-total-value tv-saldo">$${saldoTotal}</div>
                            </div></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="card detalle-total-card"><div class="card-body text-center">
                                <div class="detalle-total-label">Bono</div>
                                <div class="detalle-total-value tv-bono">$${bonoTotal}</div>
                            </div></div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-bottom:10px;">
                        <button onclick="exportarDetalle()" style="
                            display:flex;align-items:center;gap:6px;
                            padding:7px 16px;background:#0F6E56;color:#fff;
                            border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;
                        ">
                            <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'
                                fill='none' stroke='currentColor' stroke-width='2.5'
                                stroke-linecap='round' stroke-linejoin='round'>
                                <path d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/>
                                <polyline points='7 10 12 15 17 10'/>
                                <line x1='12' y1='15' x2='12' y2='3'/>
                            </svg>
                            Exportar Detalle
                        </button>
                    </div>

                    <p class="detalle-section-title">Detalle de registros</p>
                    <div class="detalle-table-wrapper">
                        <table class="detalle-table table-striped">
                            <thead>
                                <tr>
                                    <th>Año</th>
                                    <th>Periodo</th>
                                    <th>Fecha</th>
                                    <th>SC</th>
                                    <th>Brocker</th>
                                    <th>Gerencia</th>
                                    <th>Folio</th>
                                    <th>Trabajador</th>
                                    <th>Saldo</th>
                                    <th>NAP</th>
                                    <th>AVS</th>
                                    <th>Registros</th>
                                    <th>Aclaraciones</th>
                                    <th>Estatus captura</th>
                                </tr>
                            </thead>
                            <tbody>${detalleHTML}</tbody>
                        </table>
                    </div>

                </div>
            `;

        } catch (error) {
            console.error(error);
            document.getElementById("detalle-modal-body").innerHTML = `
                <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                    Error al cargar el detalle. Intenta de nuevo.
                </div>
            `;
        }
    }

    // =========================
    // SWITCH SC / BROCKER / GERENCIA
    // =========================
    document.querySelectorAll('.filtro-switch-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filtro-switch-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const target = this.dataset.target;

            document.querySelectorAll('.filtro-group').forEach(grp => {
                if (grp.id === target) {
                    grp.style.display = 'block';
                } else {
                    grp.style.display = 'none';
                    const input = grp.querySelector('input');
                    if (input && input.dataset.fijo !== '1') input.value = '';
                }
            });

            // obtenerCumplimiento();
        });
    });

    document.getElementById("buscar").addEventListener("click", obtenerCumplimiento);

    // =========================
    // EXPORTAR XLSX PRINCIPAL
    // =========================
    document.getElementById("btn-exportar-xlsx").addEventListener("click", function () {

        if (!window._datosEsquema || window._datosEsquema.length === 0) {
            Swal.fire({ icon: "warning", title: "Sin datos", text: "Busca primero para exportar." });
            return;
        }

        const wb       = XLSX.utils.book_new();
        const fmtMoney = '$#,##0.00';

        // Hoja 1: Esquema de Comisiones
        const headers1 = ["SC","Brocker","Gerencia","NAP","Propietario","RFC","AVS","Registros","Saldo Total","Puntos","Categoría","Comisión","Regimen Fiscal","IVA","Ret. IVA","Ret. ISR","Total"];
        const rows1    = [headers1];

        window._datosEsquema.forEach(row => {
            rows1.push([
                row.sc              ?? '',
                row.brocker         ?? '',
                row.gerencia        ?? '',
                row.nap ?? '',
                usrPruebas ? namePruebasUsr : (row.nombre ?? ''),
                row.rfc        ?? '',
                parseInt(row.avs)           || 0,
                parseInt(row.registros)     || 0,
                parseFloat(row.saldo_total) || 0,
                parseFloat(row.puntos)      || 0,
                row.categoria       ?? '',
                parseFloat(row.bono)        || 0,
                row.regimen         ?? '',
                parseFloat(row.iva)         || 0,
                parseFloat(row.ret_iva)     || 0,
                parseFloat(row.ret_isr)     || 0,
                parseFloat(row.total)       || 0,
            ]);
        });

        const ws1    = XLSX.utils.aoa_to_sheet(rows1);
        ws1["!cols"] = [
            {wch:20},{wch:22},{wch:22},{wch:10},{wch:25},
            {wch:8},{wch:11},{wch:16},{wch:10},{wch:12},{wch:14},{wch:18},{wch:12},{wch:12},{wch:12},{wch:14}
        ];

        const range1 = XLSX.utils.decode_range(ws1["!ref"]);
        for (let R = 1; R <= range1.e.r; R++) {
            ["H","K","M","N","O","P"].forEach(col => {
                const addr = col + (R + 1);
                if (ws1[addr]) ws1[addr].z = fmtMoney;
            });
        }

        XLSX.utils.book_append_sheet(wb, ws1, "Bonos por NAP");

        // Hoja 2: Detalle de Registros (todos los NAPs)
        if (window._todoElDetalle && window._todoElDetalle.length > 0) {

            const headers2 = ["Año","Periodo","Fecha","SC","Brocker","Gerencia","Folio","Trabajador","Saldo","NAP","AVS","Registros","Aclaraciones"];
            const rows2    = [headers2];

            window._todoElDetalle.forEach(d => {
                rows2.push([
                    d.anio                   ?? '',
                    d.periodo                ?? '',
                    d.fecha                  ?? '',
                    d.sc                  ?? '',
                    d.brocker                  ?? '',
                    d.gerencia                  ?? '',
                    String(d.folio           ?? ''),
                    d.trabajador             ?? '',
                    parseFloat(d.saldo       || 0),
                    String(d.nap             ?? ''),
                    parseInt(d.avs)          || 0,
                    parseInt(d.registros)    || 0,
                    parseInt(d.aclaraciones) || 0,
                ]);
            });

            const ws2    = XLSX.utils.aoa_to_sheet(rows2);
            ws2["!cols"] = [
                {wch:8},{wch:14},{wch:14},{wch:14},{wch:14},{wch:14},{wch:16},
                {wch:16},{wch:14},{wch:8},{wch:11},{wch:14},{wch:30}
            ];

            const range2 = XLSX.utils.decode_range(ws2["!ref"]);
            for (let R = 1; R <= range2.e.r; R++) {
                const addr = "E" + (R + 1);
                if (ws2[addr]) ws2[addr].z = fmtMoney;
            }

            XLSX.utils.book_append_sheet(wb, ws2, "Detalle");

        } else {
            const ws2 = XLSX.utils.aoa_to_sheet([["Sin detalle disponible"]]);
            XLSX.utils.book_append_sheet(wb, ws2, "Detalle");
        }

        const anio    = document.getElementById("anio").value        || "SIN_AÑO";
        const periodo = document.getElementById("sel-periodo").value  || "SIN_PERIODO";
        XLSX.writeFile(wb, `Comisiones_${anio}_${periodo.replace(" ", "_")}.xlsx`);
    });

    // =========================
    // EXPORTAR DETALLE (modal)
    // =========================
    function exportarDetalle() {
        if (!window._ultimoDetalle || window._ultimoDetalle.length === 0) {
            Swal.fire({ icon: "warning", title: "Sin datos", text: "No hay detalle para exportar." });
            return;
        }

        const wb       = XLSX.utils.book_new();
        const fmtMoney = '$#,##0.00';

        const headers = ["Año","Periodo","Fecha","SC","Brocker","Gerencia","Folio","Trabajador","Saldo","NAP","AVS","Registros","Aclaraciones"];
        const rows    = [headers];

        window._ultimoDetalle.forEach(d => {
            rows.push([
                d.anio                   ?? '',
                d.periodo                ?? '',
                d.fecha                  ?? '',
                d.sc                  ?? '',
                d.brocker                  ?? '',
                d.gerencia                  ?? '',
                String(d.folio           ?? ''),
                d.trabajador             ?? '',
                parseFloat(d.saldo       || 0),
                String(d.nap             ?? ''),
                parseInt(d.avs)          || 0,
                parseInt(d.registros)    || 0,
                parseInt(d.aclaraciones) || 0,
            ]);
        });

        const ws    = XLSX.utils.aoa_to_sheet(rows);
        ws["!cols"] = [{wch:8},{wch:14},{wch:14},{wch:14},{wch:14},{wch:14},{wch:16},{wch:16},{wch:14},{wch:8},{wch:11},{wch:14},{wch:30}];

        const range = XLSX.utils.decode_range(ws["!ref"]);
        for (let R = 1; R <= range.e.r; R++) {
            const addr = "E" + (R + 1);
            if (ws[addr]) ws[addr].z = fmtMoney;
        }

        XLSX.utils.book_append_sheet(wb, ws, "Detalle de Registros");

        const nap     = window._ultimoDetalle[0]?.nap     ?? 'NAP';
        const periodo = window._ultimoDetalle[0]?.periodo ?? '';
        const anio    = window._ultimoDetalle[0]?.anio    ?? '';
        XLSX.writeFile(wb, `Detalle_${nap}_${anio}_${periodo.replace(" ", "_")}.xlsx`);
    }

    /* ════════════════════════════════
       PREFACTURA POR NAP
       ════════════════════════════════ */

    // ── Config (ajusta a tus datos reales) ──
    const pfBackdrop = document.getElementById("pfBackdrop");

    const PREFACTURA_RECEPTOR = {
        nombre:  "DARACA ASESORES EN PENSIONES",   
        rfc:     "DAP1909217J6",
        regimen: "601 - General de Ley Personas Morales",
        cp:      "45030",
        usoCfdi: "G03 - Gastos en general"
    };

    const PREFACTURA_COMPROBANTE = {
        tipo:        "I - Ingreso",
        formaPago:   "03 - Transferencia electrónica de fondos",
        metodoPago:  "PUE - Pago en una sola exhibición",
        moneda:      "MXN - Peso Mexicano",
        tipoCambio:  "N/A",
        exportacion: "01 - No aplica"
    };

    const REGIMEN_SAT_MAP = {
        "Personas Fisicas":      "612 - Personas Físicas con Actividades Empresariales y Profesionales",
        "RESICO":                "626 - Régimen Simplificado de Confianza",
        "Sueldos y Salarios":    "605 - Sueldos y Salarios e Ingresos Asimilados a Salarios",
        "Asimilados a Salarios": "605 - Sueldos y Salarios e Ingresos Asimilados a Salarios"
    };

    // ── Helpers ──
    function pfMoney(n) {
        return "$" + Number(n || 0).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function pfNumeroALetra(num) {
        const UNIDADES = ["", "UNO","DOS","TRES","CUATRO","CINCO","SEIS","SIETE","OCHO","NUEVE","DIEZ",
            "ONCE","DOCE","TRECE","CATORCE","QUINCE","DIECISÉIS","DIECISIETE","DIECIOCHO","DIECINUEVE","VEINTE"];
        const DECENAS = ["","","VEINTI","TREINTA","CUARENTA","CINCUENTA","SESENTA","SETENTA","OCHENTA","NOVENTA"];
        const CENTENAS = ["","CIENTO","DOSCIENTOS","TRESCIENTOS","CUATROCIENTOS","QUINIENTOS","SEISCIENTOS","SETECIENTOS","OCHOCIENTOS","NOVECIENTOS"];
        function trescientos(n) {
            if (n === 0) return "";
            if (n === 100) return "CIEN";
            let s = "";
            if (n >= 100) { s += CENTENAS[Math.floor(n / 100)] + " "; n %= 100; }
            if (n <= 20) s += UNIDADES[n];
            else if (n < 30) s += "VEINTI" + UNIDADES[n - 20];
            else { s += DECENAS[Math.floor(n / 10)]; if (n % 10 !== 0) s += " Y " + UNIDADES[n % 10]; }
            return s.trim();
        }
        const entero = Math.floor(Math.abs(num));
        const centavos = Math.round((Math.abs(num) - entero) * 100);
        let letra = "";
        if (entero === 0) letra = "CERO";
        else {
            const millones = Math.floor(entero / 1000000);
            const miles = Math.floor((entero % 1000000) / 1000);
            const resto = entero % 1000;
            if (millones > 0) letra += (millones === 1 ? "UN MILLÓN " : trescientos(millones) + " MILLONES ");
            if (miles > 0) letra += (miles === 1 ? "MIL " : trescientos(miles) + " MIL ");
            if (resto > 0) letra += trescientos(resto);
        }
        return `${letra.trim()} PESOS ${String(centavos).padStart(2,"0")}/100 M.N.`;
    }

    /**
     * Bloque CONCEPTOS + TOTALES estilo Excel (cuadriculado, teal, amarillo).
     */
    function pfConceptosTotalesHTML(conceptos, totales, filasVacias = 6) {

        const filasConcepto = conceptos.map(c => `
            <tr class="pf-xls-row-filled">
                <td class="pf-xls-center pf-xls-blue">${Number(c.cantidad).toFixed(2)}</td>
                <td class="pf-xls-center pf-xls-blue">${c.claveUnidad ?? ''}</td>
                <td class="pf-xls-center pf-xls-blue">${c.claveProdServ ?? ''}</td>
                <td class="pf-xls-blue">${c.descripcion ?? ''}</td>
                <td class="pf-xls-right pf-xls-blue">${Number(c.valorUnitario).toFixed(2)}</td>
                <td class="pf-xls-right pf-xls-blue">${Number(c.descuento || 0).toFixed(2)}</td>
                <td class="pf-xls-center pf-xls-blue">${c.objetoImp ?? ''}</td>
                <td class="pf-xls-right">${Number(c.importe).toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
                <td class="pf-xls-right">${Number(c.baseGravable).toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
                <td class="pf-xls-center pf-xls-blue">${Math.round((c.tasaIva || 0) * 100)}%</td>
                <td class="pf-xls-right">${Number(c.ivaTrasladado).toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
            </tr>`).join("");

        const vacias = Array.from({ length: filasVacias }).map(() => `
            <tr class="pf-xls-row-empty">
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>`).join("");

        return `
        <div class="pf-xls-wrap">
            <div class="pf-xls-titlebar">CONCEPTOS</div>
            <table class="pf-xls-table">
                <colgroup>
                    <col style="width:7%"><col style="width:8%"><col style="width:9%">
                    <col style="width:23%"><col style="width:9%"><col style="width:8%">
                    <col style="width:7%"><col style="width:9%"><col style="width:9%">
                    <col style="width:5%"><col style="width:9%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Cantidad</th><th>Clave Unidad<br>SAT</th><th>Clave Prod/<br>Serv SAT</th>
                        <th>Descripción</th><th>Valor<br>Unitario</th><th>Descuento</th>
                        <th>Objeto<br>Imp.</th><th>Importe</th><th>Base<br>Gravable</th>
                        <th>Tasa<br>IVA</th><th>IVA<br>Trasladado</th>
                    </tr>
                </thead>
                <tbody>${filasConcepto}${vacias}</tbody>
            </table>

            <div style="height:14px;"></div>

            <div class="pf-xls-titlebar">TOTALES</div>
            <div class="pf-xls-totales-wrap">
                <div class="pf-xls-totales-spacer"></div>
                <table class="pf-xls-totales">
                    <tr>
                        <td class="pf-xls-tlabel">Subtotal</td>
                        <td class="pf-xls-tval">${pfMoney(totales.subtotal)}</td>
                    </tr>
                    <tr>
                        <td class="pf-xls-tlabel">( - ) Descuentos</td>
                        <td class="pf-xls-tval">${pfMoney(totales.descuentos || 0)}</td>
                    </tr>
                    <tr>
                        <td class="pf-xls-tlabel">( + ) IVA Trasladado (${Math.round((totales.tasaIva ?? 0.16) * 100)}%)</td>
                        <td class="pf-xls-tval">${pfMoney(totales.ivaTrasladado)}</td>
                    </tr>
                    <tr>
                        <td class="pf-xls-tlabel">( - ) ${totales.ivaRetenidoLabel || 'IVA Retenido'}</td>
                        <td class="pf-xls-tval">${pfMoney(totales.ivaRetenido)}</td>
                    </tr>
                    <tr>
                        <td class="pf-xls-tlabel">( - ) ISR Retenido</td>
                        <td class="pf-xls-tval pf-xls-blue">${pfMoney(totales.isrRetenido)}</td>
                    </tr>
                    <tr class="pf-xls-total-row">
                        <td class="pf-xls-tlabel" style="color:#fff!important;">TOTAL</td>
                        <td class="pf-xls-tval" style="color:#fff!important;">${pfMoney(totales.total)}</td>
                    </tr>
                </table>
            </div>
            <div class="pf-xls-letra">Importe con letra: ${pfNumeroALetra(totales.total)}</div>
        </div>`;
    }


	/**
	 * Bloque de info tipo "Emisor / Receptor / Generales" con cuadrícula Excel.
	 * filas: array de pares [ {label, value}, {label, value}|null ]
	 *   - si el segundo elemento es null, la fila usa colspan completo (como "Uso del CFDI")
	 *   - value puede ser texto plano o un <input> ya armado (para campos capturables)
	 */
	function pfInfoBlockHTML(titulo, filas) {
		const rowsHTML = filas.map(par => {
			const [a, b] = par;
			if (!b) {
				return `<tr>
					<td class="pf-xls-info-lbl">${a.label}</td>
					<td class="pf-xls-info-val" colspan="3">${a.value}</td>
				</tr>`;
			}
			return `<tr>
				<td class="pf-xls-info-lbl">${a.label}</td>
				<td class="pf-xls-info-val">${a.value}</td>
				<td class="pf-xls-info-lbl">${b.label}</td>
				<td class="pf-xls-info-val">${b.value}</td>
			</tr>`;
		}).join("");

		return `
		<div class="pf-xls-wrap">
			<div class="pf-xls-titlebar">${titulo}</div>
			<table class="pf-xls-info-table">
				<colgroup><col style="width:15%"><col style="width:35%"><col style="width:15%"><col style="width:35%"></colgroup>
				<tbody>${rowsHTML}</tbody>
			</table>
		</div>
		<div class="pf-xls-spacer"></div>`;
	}

    /**
     * Arma el documento completo de la prefactura para una fila (row) de la tabla.
     */
    function pfBuildHTML(row) {
		const periodo = document.getElementById("sel-periodo").value || "----";
		const anio    = document.getElementById("anio").value || "----";

		const regimenTxt = (row.regimen || "").trim();
		const regimenSat = REGIMEN_SAT_MAP[regimenTxt] || regimenTxt || "(sin régimen capturado)";
		const esNomina   = regimenTxt === "Sueldos y Salarios" || regimenTxt === "Asimilados a Salarios";

		const bono    = parseFloat(row.bono    || 0);
		const iva     = parseFloat(row.iva     || 0);
		const retIva  = parseFloat(row.ret_iva || 0);
		const retIsr  = parseFloat(row.ret_isr || 0);
		const total   = parseFloat(row.total   || 0);
		const tasaIva = bono ? Math.abs(iva / bono) : 0;

		const warnHTML = esNomina
			? `<div class="pf-warn" style="margin:0 22px 12px;">⚠ Régimen "${regimenTxt}": normalmente este pago se cubre vía CFDI de Nómina emitido por CAROBRA, no por el trabajador. Verifica antes de usar esta prefactura para timbrado.</div>`
			: '';

		// ── DATOS DEL EMISOR (NAP) ──
		const emisorHTML = pfInfoBlockHTML("DATOS DEL EMISOR (NAP)", [
			[
				{ label: "Razón social", value: usrPruebas ? namePruebasUsr : (row.nombre ?? '') },
				{ label: "RFC", value: row.rfc ?? '' }
			],
			[
				{ label: "Régimen fiscal", value: regimenSat },
                { label: "Lugar exp.<br>(C.P.)", value: `<div class="pf-editable" contenteditable="true" id="pfEmisorCp" data-placeholder="C.P. fiscal">${row.cp_fiscal ?? ''}</div>` }

			],
			// [
			// 	{ label: "NAP", value: row.nap ?? '' },
			// 	null
			// ]
		]);

		// ── DATOS DEL RECEPTOR ──
		const receptorHTML = pfInfoBlockHTML("DATOS DEL RECEPTOR", [
			[
				{ label: "Nombre / Razón social", value: PREFACTURA_RECEPTOR.nombre },
				{ label: "RFC", value: PREFACTURA_RECEPTOR.rfc }
			],
			[
				{ label: "Régimen fiscal receptor", value: PREFACTURA_RECEPTOR.regimen },
				{ label: "C.P.<br>domicilio fiscal", value: PREFACTURA_RECEPTOR.cp }
			],
			[
				{ label: "Uso del CFDI", value: PREFACTURA_RECEPTOR.usoCfdi },
				null
			]
		]);

		// ── DATOS GENERALES DEL COMPROBANTE ──
		const generalesHTML = pfInfoBlockHTML("DATOS GENERALES DEL COMPROBANTE", [
			[
				{ label: "Tipo de comprobante", value: PREFACTURA_COMPROBANTE.tipo },
				{ label: "Serie / Folio", value: `<div class="pf-editable" contenteditable="true" id="pfFolio" data-placeholder="Serie-Folio"></div>` }

			],
			[
				{ label: "Forma de pago", value: PREFACTURA_COMPROBANTE.formaPago },
				{ label: "Método de pago", value: PREFACTURA_COMPROBANTE.metodoPago }
			],
			[
				{ label: "Moneda", value: PREFACTURA_COMPROBANTE.moneda },
				{ label: "Tipo de cambio", value: PREFACTURA_COMPROBANTE.tipoCambio }
			],
			[
				{ label: "Exportación", value: PREFACTURA_COMPROBANTE.exportacion },
				{ label: "Fecha", value: `<div class="pf-editable" contenteditable="true" id="pfFecha">${new Date().toLocaleDateString('es-MX')}</div>` }


			]
		]);

		// ── CONCEPTOS + TOTALES ──
		let periodoLabel = periodo.split(" ")[1];
		const conceptosHTML = pfConceptosTotalesHTML(
			[{
				cantidad: 1, claveUnidad: "E48", claveProdServ: "80141601",
				descripcion: `COMISIONES CUMPLIMIENTO PERIODO ${periodoLabel} - ${anio}`,
				valorUnitario: bono, descuento: 0, objetoImp: "02",
				importe: bono, baseGravable: bono, tasaIva: tasaIva, ivaTrasladado: iva
			}],
			{
				subtotal: bono, descuentos: 0, tasaIva: tasaIva,
				ivaTrasladado: iva, ivaRetenido: retIva, ivaRetenidoLabel: "IVA Retenido",
				isrRetenido: retIsr, total: total
			}
		);

		return `
			<div style="padding:18px 22px 0;">
				<div style="font-family:'Syne', sans-serif, 'Segoe UI'; font-weight:700; font-size:16px; color:#1f2d3d;">
					PREFACTURA — FORMATO DE CAPTURA
				</div>
				<div style="font-size:11px; color:#7b8794; margin:2px 0 14px;">
					Captura previa al timbrado — CFDI 4.0. Campos en azul = capturar. Campos en negro = calculados automáticamente.
				</div>
			</div>
			${warnHTML}
			${emisorHTML}
			${receptorHTML}
			${generalesHTML}
			${conceptosHTML}`;
	}

    // ── Abrir modal (Ver) ──
    function abrirPrefactura(row) {
        document.getElementById("pfHeaderSub").textContent = `${row.nombre ?? ''} · NAP ${row.nap ?? ''}`;
        document.getElementById("pfDoc").innerHTML = pfBuildHTML(row);
        document.getElementById("pfBtnDescargar").onclick = () => pfGenerarPDF(document.getElementById("pfDoc"), row);
        pfBackdrop.classList.add("open");
    }
    document.getElementById("pfClose").addEventListener("click", () => pfBackdrop.classList.remove("open"));
    pfBackdrop.addEventListener("click", e => { if (e.target === pfBackdrop) pfBackdrop.classList.remove("open"); });

    // ── Descarga directa (sin abrir modal) ──
    async function descargarPrefacturaDirecta(row) {
        const hidden = document.getElementById("pf-hidden-render");
        hidden.innerHTML = pfBuildHTML(row);
        await pfGenerarPDF(hidden, row);
        hidden.innerHTML = "";
    }

    // ── Genera el PDF con html2canvas + jsPDF ──
    async function pfGenerarPDF(elemento, row) {
		const canvas = await html2canvas(elemento, { scale: 2, backgroundColor: "#ffffff" });
		const imgData = canvas.toDataURL("image/png");

		const { jsPDF } = window.jspdf;
		const pdf = new jsPDF("p", "mm", "letter");

		const pageWidth  = pdf.internal.pageSize.getWidth();
		const pageHeight = pdf.internal.pageSize.getHeight();
		const margin     = 6; // mm

		const maxWidth  = pageWidth  - margin * 2;
		const maxHeight = pageHeight - margin * 2;

		// Relación de aspecto real del contenido capturado
		const ratio = canvas.height / canvas.width;

		// Ajusta primero al ancho disponible…
		let renderWidth  = maxWidth;
		let renderHeight = renderWidth * ratio;

		// …y si aun así no cabe de alto, ajusta al alto disponible
		if (renderHeight > maxHeight) {
			renderHeight = maxHeight;
			renderWidth  = renderHeight / ratio;
		}

		const x = (pageWidth - renderWidth) / 2;
		const y = margin;

		pdf.addImage(imgData, "PNG", x, y, renderWidth, renderHeight);

		const periodo = (document.getElementById("sel-periodo").value || "SP").replace(/\s+/g, "_");
		const anio    = document.getElementById("anio").value || "SA";
		pdf.save(`Prefactura_NAP_${row.nap ?? 'SN'}_${periodo}_${anio}.pdf`);
	}

    function parseMonto(str) {
        return parseFloat((str || "0").replace(/[$,\s]/g, "")) || 0;
    }

</script>
</html>