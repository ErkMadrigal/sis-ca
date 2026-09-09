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

	<!-- Datepicker (se queda cargado por si el resto del sitio lo usa) -->
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
		HERO / LANDING
		============================================ */

		.hero-reporteador{
			max-width:720px;
			margin:20px auto 0;
			background:white;
			border-radius:24px;
			padding:44px 40px;
			text-align:center;
			box-shadow:0 5px 20px rgba(0,0,0,.08);
			border:1px solid #eef2ff;
		}

		.hero-reporteador .hero-icon{
			font-size:44px;
			margin-bottom:10px;
		}

		.hero-reporteador h1{
			font-size:24px;
			color:#0f172a;
			margin-bottom:12px;
		}

		.hero-reporteador p{
			color:#64748b;
			font-size:14px;
			line-height:1.6;
			max-width:520px;
			margin:0 auto 26px;
		}

		.btn-hero{
			background:#0177cb;
			color:white;
			border:none;
			padding:15px 26px;
			border-radius:16px;
			cursor:pointer;
			font-size:15px;
			font-weight:600;
			transition:.2s;
			box-shadow:0 5px 15px rgba(1,119,203,.25);
		}

		.btn-hero:hover{ transform:translateY(-2px); }

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

		.mc-btn-close:hover{ background:#e2e8f0; color:#0f172a; }

		.mc-body{
			flex:1;
			display:flex;
			overflow:hidden;
		}

		/* IZQUIERDO: CAMPOS */

		.mc-campos{
			width:280px;
			flex-shrink:0;
			border-right:1px solid #e2e8f0;
			display:flex;
			flex-direction:column;
			overflow:hidden;
		}

		.mc-campos-buscar{
			padding:12px;
			border-bottom:1px solid #f1f5f9;
			flex-shrink:0;
		}

		.mc-campos-buscar input{
			width:100%;
			border:1.5px solid #e2e8f0;
			border-radius:10px;
			padding:8px 12px;
			font-size:13px;
			outline:none;
			transition:border-color .2s;
		}

		.mc-campos-buscar input:focus{ border-color:#0177cb; }

		.mc-campos-list{
			flex:1;
			overflow-y:auto;
			padding:8px 10px;
		}

		.mc-campos-group-title{
			font-size:11px;
			font-weight:700;
			color:#94a3b8;
			text-transform:uppercase;
			letter-spacing:.06em;
			padding:12px 6px 6px;
		}

		.mc-campo-item{
			display:flex;
			align-items:center;
			gap:8px;
			padding:9px 12px;
			border-radius:10px;
			background:#f8fafc;
			border:1px solid #e2e8f0;
			cursor:grab;
			transition:.15s;
			font-size:13px;
			color:#0f172a;
			font-weight:500;
			user-select:none;
			margin-bottom:6px;
		}

		.mc-campo-item:hover{ background:#eff6ff; border-color:#bfdbfe; color:#0177cb; }
		.mc-campo-item.activo{ background:#e0f2fe; border-color:#0177cb; color:#0177cb; }

		.mc-campo-item .drag-icon{ color:#cbd5e1; font-size:12px; flex-shrink:0; }

		/* DERECHO: CONSTRUCTOR / RESULTADOS */

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

		.mc-derecho-title button{
			background:#f1f5f9;
			border:none;
			padding:5px 12px;
			border-radius:8px;
			cursor:pointer;
			font-size:11px;
			font-weight:600;
			color:#64748b;
			text-transform:none;
			letter-spacing:0;
		}

		.mc-derecho-title button:hover{ background:#e2e8f0; }

		.mc-dropzone{
			flex:1;
			overflow:auto;
			padding:16px;
			transition:background .2s;
		}

		.mc-dropzone.drag-over{ background:#eff6ff; }

		.mc-tabla{
			width:100%;
			border-collapse:collapse;
			font-size:13px;
		}

		.mc-tabla th{
			background:#f1f5f9;
			border:1px solid #e2e8f0;
			padding:12px 14px;
			min-width:150px;
			cursor:move;
			white-space:nowrap;
			text-align:left;
		}

		.mc-tabla td{
			border:1px solid #e2e8f0;
			padding:9px 14px;
			background:white;
			white-space:nowrap;
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
			text-align:center;
			padding:20px;
		}

		.mc-placeholder span{ font-size:40px; }

		.col-header{
			display:flex;
			align-items:center;
			justify-content:space-between;
			gap:10px;
		}

		.col-header label{ font-size:13px; font-weight:600; color:#0f172a; }

		.btn-del-col{
			background:#fee2e2;
			color:#ef4444;
			border:none;
			width:22px;
			height:22px;
			border-radius:6px;
			cursor:pointer;
			font-size:12px;
			flex-shrink:0;
			transition:.15s;
		}

		.btn-del-col:hover{ background:#ef4444; color:white; }

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

		.mc-footer button:hover{ transform:translateY(-1px); }

		#mcBtnGenerar{ background:#0177cb; color:white; }
		#mcBtnExportar{ background:#015a9a; color:white; display:none; }
		#mcBtnLimpiar{ background:#f1f5f9; color:#64748b; }

		/* ============================================
		MODAL FILTROS
		============================================ */

		#modalFiltros{
			position:fixed;
			inset:0;
			background:rgba(15,23,42,.75);
			z-index:9999999;
			display:none;
			justify-content:center;
			align-items:center;
			padding:1.5rem;
			backdrop-filter:blur(6px);
		}

		.mf-content{
			width:100%;
			max-width:640px;
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

		.mf-header h2{ font-size:18px; font-weight:700; color:#0f172a; }

		.mf-body{
			flex:1;
			overflow-y:auto;
			padding:20px 24px;
			display:flex;
			flex-direction:column;
			gap:18px;
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

		#mfBtnLimpiar{ background:#f1f5f9; color:#64748b; }
		#mfBtnAplicar{ background:#0177cb; color:white; }

		.mf-label{
			display:block;
			font-size:11px;
			font-weight:700;
			color:#64748b;
			text-transform:uppercase;
			letter-spacing:.06em;
			margin-bottom:10px;
		}

		.mf-tipos{
			display:flex;
			flex-wrap:wrap;
			gap:8px;
		}

		.mf-tipo-btn{
			background:#f8fafc;
			border:1.5px solid #e2e8f0;
			border-radius:10px;
			padding:8px 16px;
			cursor:pointer;
			font-size:13px;
			font-weight:600;
			color:#0f172a;
			transition:.15s;
		}

		.mf-tipo-btn.activo{
			background:#0177cb;
			border-color:#0177cb;
			color:white;
		}

		.ac-wrapper{ position:relative; margin-top:10px; }

		.ac-wrapper input{
			width:100%;
			border:1.5px solid #e2e8f0;
			border-radius:10px;
			padding:10px 14px;
			font-size:13px;
			color:#0f172a;
			background:#f8fafc;
			outline:none;
			transition:border-color .2s, box-shadow .2s;
		}

		.ac-wrapper input:focus{
			border-color:#0177cb;
			background:white;
			box-shadow:0 0 0 3px rgba(1,119,203,.12);
		}

		.ac-dropdown{
			position:absolute;
			top:calc(100% + 6px);
			left:0;
			right:0;
			background:white;
			border:1.5px solid #e2e8f0;
			border-radius:12px;
			box-shadow:0 10px 30px rgba(0,0,0,.12);
			z-index:99999;
			max-height:220px;
			overflow-y:auto;
			display:none;
		}

		.ac-item{
			padding:10px 14px;
			font-size:13px;
			cursor:pointer;
			color:#0f172a;
			border-bottom:1px solid #f1f5f9;
			transition:.15s;
		}

		.ac-item:last-child{ border-bottom:none; }
		.ac-item:hover{ background:#eff6ff; color:#0177cb; }

		.ac-empty{
			padding:12px 14px;
			font-size:13px;
			color:#94a3b8;
			text-align:center;
		}

		.mf-chip-wrap{ margin-top:10px; min-height:4px; }

		.mf-chip{
			display:inline-flex;
			align-items:center;
			gap:8px;
			background:#e0f2fe;
			border:1.5px solid #bae6fd;
			border-radius:999px;
			padding:6px 14px;
			font-size:13px;
			font-weight:600;
			color:#0369a1;
		}

		.mf-chip button{
			background:none;
			border:none;
			color:#0369a1;
			cursor:pointer;
			font-size:14px;
			line-height:1;
			padding:0;
		}

		.mf-fechas{
			display:flex;
			align-items:center;
			gap:10px;
		}

		.mf-fechas input, .mf-select{
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

		.mf-fechas input:focus, .mf-select:focus{ border-color:#0177cb; }

		.mf-fechas span{ color:#94a3b8; font-size:13px; flex-shrink:0; }

		.mf-checks{ display:flex; flex-direction:column; gap:8px; }

		.mf-checks label{
			display:flex;
			align-items:center;
			gap:8px;
			font-size:13px;
			color:#0f172a;
			font-weight:500;
			cursor:pointer;
		}

		.mf-checks input[type="checkbox"]{ width:15px; height:15px; accent-color:#0177cb; cursor:pointer; }

		#mfMensaje{
			font-size:13px;
			color:#94a3b8;
			text-align:center;
			padding:10px;
		}

		.mc-resumen{
			font-size:12px;
			color:#64748b;
			padding:8px 16px 0;
		}

	</style>

</head>

<?php
/* ============================================================
   SECCIÓN 4: PHP - INCLUDE DE CLASES
   ============================================================ */
include("./funciones/clas_carobra.php");
require_once('./reporteador_funciones.php');
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
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "http://carobra.ddns.me:8888");
		}
		else
		{
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"]  = "p";
		}

		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "index.php");
		}
		else
		{
			reg_acceso($_SESSION["nombre"], "reporteador_ventas");
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
				titulo("Reporteador de Ventas");
				$pag = "PAGOS_Y_FACTURAS_NAP";
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

			$conecta = new conector();
			$nombre  = $_SESSION["nombre"];
			$nivel   = nivel_usuario(utf8_decode($nombre));

			$puedeNap        = !in_array($nivel, [3, 4, 5], true);
			$puedeSC         = ($nivel != 3);
			$puedeGerencia   = ($nivel != 3);
			$puedeSubBrocker = !in_array($nivel, [3, 4], true);

			$filtrosPermitidos = [];
			if ($puedeSC)         $filtrosPermitidos[] = 'Soc. Comercial';
			if ($puedeSubBrocker) $filtrosPermitidos[] = 'SubBrocker';
			$filtrosPermitidos[] = 'Comisionista';
			if ($puedeGerencia)   $filtrosPermitidos[] = 'Gerencia';
			if ($puedeNap)        $filtrosPermitidos[] = 'NAP';
	?>

	<!-- ============================================================
	SECCIÓN 7: HTML - CONTENIDO PRINCIPAL
	============================================================ -->

	<div class="container">

		<!-- ============================================
		LANDING
		============================================ -->

		<div class="hero-reporteador">
			<div class="hero-icon">📊</div>
			<h1>Reporteador de Ventas</h1>
			<p>Arma tu propio reporte de Seguimiento de Ventas: elige los campos que necesitas,
			   filtra por Socio Comercial, SubBrocker, Comisionista, Gerencia o NAP, y exporta a Excel.</p>
			<button id="btnAbrirConstructor" class="btn-hero">+ Generar reporte</button>
		</div>

		<!-- ============================================
		MODAL FULLSCREEN CONSTRUCTOR
		============================================ -->

		<div id="modalConstructor">
			<div class="mc-content">

				<!-- HEADER -->
				<div class="mc-header">
					<h2>📊 Reporteador de Ventas</h2>
					<button class="mc-btn-close" onclick="cerrarConstructor()">✕</button>
				</div>

				<!-- BODY -->
				<div class="mc-body">

					<!-- IZQUIERDO: CAMPOS -->
					<div class="mc-campos">

						<div class="mc-campos-buscar">
							<input type="text" id="mcBuscarCampo" placeholder="Buscar campo...">
						</div>

						<div class="mc-campos-list" id="mcCamposList"></div>

					</div>

					<!-- DERECHO: CONSTRUCTOR / RESULTADOS -->
					<div class="mc-derecho">

						<div class="mc-derecho-title">
							<span id="mcDerechoTitle">Columnas del reporte</span>
							<button onclick="restaurarCamposDefault()">↺ Restaurar default</button>
						</div>

						<div class="mc-resumen" id="mcResumen"></div>

						<div class="mc-dropzone" id="mcDropzone">

							<div class="mc-placeholder" id="mcPlaceholder">
								<span>📋</span>
								<p>Arrastra o haz clic en un campo de la izquierda para agregarlo a tu reporte</p>
							</div>

							<table class="mc-tabla" id="mcTabla" style="display:none;">
								<thead><tr id="mcThead"></tr></thead>
								<tbody id="mcTbody"></tbody>
							</table>

						</div>

					</div>

				</div>

				<!-- FOOTER -->
				<div class="mc-footer">
					<button id="mcBtnLimpiar" onclick="quitarTodosCampos()">🗑 Quitar todos</button>
					<button id="mcBtnGenerar">🔍 Filtrar y generar</button>
					<button id="mcBtnExportar">📥 Exportar a Excel</button>
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
					<button class="mc-btn-close" onclick="cerrarModalFiltros()">✕</button>
				</div>

				<div class="mf-body">

					<div>
						<label class="mf-label">Buscar por</label>
						<div class="mf-tipos" id="mfTipos">
							<?php foreach ($filtrosPermitidos as $ft): ?>
								<button type="button" class="mf-tipo-btn" data-tipo="<?= htmlspecialchars($ft) ?>"><?= htmlspecialchars($ft) ?></button>
							<?php endforeach; ?>
						</div>

						<div class="ac-wrapper" id="mfBuscadorWrap" style="display:none;">
							<input type="text" id="mfBuscadorInput" placeholder="Escribe para buscar..." autocomplete="off">
							<div class="ac-dropdown" id="mfDropdown"></div>
						</div>

						<div class="mf-chip-wrap" id="mfChipWrap"></div>
					</div>

					<div>
						<label class="mf-label">Rango de fechas (fecha de traspaso)</label>
						<div class="mf-fechas">
							<input type="date" id="mfFechaDesde">
							<span>al</span>
							<input type="date" id="mfFechaHasta">
						</div>
					</div>

					<div>
						<label class="mf-label">Tipo de mes</label>
						<select class="mf-select" id="mfTipoMes">
							<option value="">Mes de comisión</option>
							<option value="ep">Mes de pago (estimado)</option>
						</select>
					</div>

					<div>
						<label class="mf-label">Opciones adicionales</label>
						<div class="mf-checks">
							<label><input type="checkbox" id="mfSfp"> Solo folios sin fecha de pago</label>
							<label><input type="checkbox" id="mfSrm0"> Solo con saldo real diferente de cero</label>
							<label><input type="checkbox" id="mfRegEsp"> Registro especial (folio corto)</label>
						</div>
					</div>

				</div>

				<div class="mf-footer">
					<button id="mfBtnLimpiar" onclick="limpiarFiltros()">Limpiar</button>
					<button id="mfBtnAplicar" onclick="aplicarFiltros()">⚡ Aplicar y generar</button>
				</div>

			</div>
		</div>

	</div>

	<?php
		$divcontenido->close();
		$divcontenedor->close();
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
		$('#opcion').load('menu.php?modulo=' + '<?php echo $pag ?? "" ?>');
	</script>

	<?php } ?>

</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		$('#tmenu td').click(function ()
		{
			var modulo = $(this).text().replace(/ /gi, "_");
			$("#opcion").load("menu.php?modulo=" + modulo);
		});

		$("#salir").click(function ()
		{
			window.location = "http://carobra.ddns.me";
		});

		$("#email").click(function ()
		{
			window.open("http://webmail.carobra.com.mx/imp/login.php");
		});
	});
</script>

<!-- ============================================================
     SECCIÓN 9: JAVASCRIPT - REPORTEADOR
     ============================================================ -->
<script>

const API_REPORTEADOR = './seg_reporteador_api.php';

var CAMPOS_REPORTE = <?php echo json_encode($CAMPOS_REPORTE); ?>;
var CAMPOS_DEFAULT = <?php echo json_encode($CAMPOS_DEFAULT); ?>;
var CAMPOS_MONEDA  = <?php echo json_encode($CAMPOS_MONEDA); ?>;

var camposActuales   = [];
var filtroTipoActivo = '';
var filtroValor       = null;   // { etiqueta, valor }
var LS_KEY = 'reporteador_campos_v4';

/* ============================================
INIT
============================================ */

document.addEventListener('DOMContentLoaded', function(){

	pintarCamposDisponibles();

	document.getElementById('btnAbrirConstructor').addEventListener('click', abrirConstructor);

	document.getElementById('mcBuscarCampo').addEventListener('input', function(){
		pintarCamposDisponibles(this.value.trim().toLowerCase());
	});

	document.getElementById('mcBtnGenerar').addEventListener('click', function(){
		if(camposActuales.length === 0){
			Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos un campo a tu reporte' });
			return;
		}
		document.getElementById('modalFiltros').style.display = 'flex';
	});

	document.getElementById('mcBtnExportar').addEventListener('click', exportarReporte);

	initDropzone();

	/* Botones de tipo de filtro */
	document.querySelectorAll('.mf-tipo-btn').forEach(function(btn){
		btn.addEventListener('click', function(){
			document.querySelectorAll('.mf-tipo-btn').forEach(function(b){ b.classList.remove('activo'); });
			btn.classList.add('activo');
			filtroTipoActivo = btn.dataset.tipo;
			filtroValor = null;
			renderChipFiltro();
			document.getElementById('mfBuscadorWrap').style.display = 'block';
			document.getElementById('mfBuscadorInput').value = '';
			document.getElementById('mfDropdown').style.display = 'none';
			document.getElementById('mfBuscadorInput').focus();
		});
	});

	/* Buscador con dropdown (debounce) */
	var timerBusqueda = null;
	document.getElementById('mfBuscadorInput').addEventListener('input', function(){
		var termino = this.value.trim();
		clearTimeout(timerBusqueda);
		if(termino.length < 1){ document.getElementById('mfDropdown').style.display = 'none'; return; }
		timerBusqueda = setTimeout(function(){ buscarFiltroValor(termino); }, 250);
	});

	document.getElementById('mfBuscadorInput').addEventListener('blur', function(){
		setTimeout(function(){ document.getElementById('mfDropdown').style.display = 'none'; }, 200);
	});

});

/* ESC cierra los modales */
document.addEventListener('keydown', function(e){
	if(e.key === 'Escape'){
		cerrarConstructor();
		cerrarModalFiltros();
	}
});

/* ============================================
CAMPOS DISPONIBLES (izquierda, agrupados por tabla)
============================================ */

function pintarCamposDisponibles(filtroTexto){

	var lista  = document.getElementById('mcCamposList');
	var grupos = {};
	var orden  = [];

	Object.keys(CAMPOS_REPORTE).forEach(function(key){
		var campo = CAMPOS_REPORTE[key];
		if(filtroTexto && campo.label.toLowerCase().indexOf(filtroTexto) === -1) return;
		if(!grupos[campo.grupo]){ grupos[campo.grupo] = []; orden.push(campo.grupo); }
		grupos[campo.grupo].push({ key:key, label:campo.label });
	});

	lista.innerHTML = '';

	if(orden.length === 0){
		lista.innerHTML = '<p style="color:#94a3b8;font-size:13px;padding:10px;text-align:center;">Sin resultados</p>';
		return;
	}

	orden.forEach(function(grupo){

		var titulo = document.createElement('div');
		titulo.className = 'mc-campos-group-title';
		titulo.textContent = grupo;
		lista.appendChild(titulo);

		grupos[grupo].forEach(function(campo){

			var item = document.createElement('div');
			item.className = 'mc-campo-item';
			item.draggable = true;
			item.dataset.campo = campo.key;
			if(camposActuales.indexOf(campo.key) !== -1) item.classList.add('activo');
			item.innerHTML = '<span class="drag-icon">⠿</span><span>' + campo.label + '</span>';

			item.addEventListener('dragstart', function(e){
				e.dataTransfer.setData('campo', campo.key);
			});

			item.addEventListener('click', function(){ agregarColumna(campo.key); });

			lista.appendChild(item);

		});

	});

}

/* ============================================
CONSTRUCTOR
============================================ */

function abrirConstructor(){

	var guardados = null;
	try{ guardados = JSON.parse(localStorage.getItem(LS_KEY) || 'null'); } catch(e){ guardados = null; }

	camposActuales = [];
	resetTabla();

	var inicial = (guardados && guardados.length) ? guardados : CAMPOS_DEFAULT;
	inicial.forEach(function(key){ if(CAMPOS_REPORTE[key]) agregarColumna(key, true); });

	document.getElementById('modalConstructor').style.display = 'flex';
	document.body.style.overflow = 'hidden';

}

function cerrarConstructor(){
	document.getElementById('modalConstructor').style.display = 'none';
	document.body.style.overflow = '';
}

function initDropzone(){
	var dz = document.getElementById('mcDropzone');
	dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('drag-over'); });
	dz.addEventListener('dragleave', function(){ dz.classList.remove('drag-over'); });
	dz.addEventListener('drop', function(e){
		e.preventDefault();
		dz.classList.remove('drag-over');
		var campo = e.dataTransfer.getData('campo');
		if(campo) agregarColumna(campo);
	});
}

function resetTabla(){
	document.getElementById('mcThead').innerHTML = '';
	document.getElementById('mcTbody').innerHTML = '';
	document.getElementById('mcTabla').style.display = 'none';
	document.getElementById('mcPlaceholder').style.display = 'flex';
	document.getElementById('mcResumen').textContent = '';
	document.getElementById('mcBtnExportar').style.display = 'none';
}

function guardarSeleccionLS(){
	try{ localStorage.setItem(LS_KEY, JSON.stringify(camposActuales)); } catch(e){}
}

function agregarColumna(campoKey, silencioso){

	if(!CAMPOS_REPORTE[campoKey]) return;
	if(camposActuales.indexOf(campoKey) !== -1) return;

	camposActuales.push(campoKey);

	document.getElementById('mcPlaceholder').style.display = 'none';
	document.getElementById('mcTabla').style.display = 'table';

	var thead = document.getElementById('mcThead');
	var tbody = document.getElementById('mcTbody');

	var th = document.createElement('th');
	th.dataset.campo = campoKey;
	th.draggable = true;
	th.innerHTML = '<div class="col-header"><label>' + CAMPOS_REPORTE[campoKey].label +
		'</label><button class="btn-del-col" onclick="eliminarColumna(\'' + campoKey + '\')">✕</button></div>';

	th.addEventListener('dragstart', function(e){ e.dataTransfer.setData('reordenar', campoKey); e.stopPropagation(); });
	th.addEventListener('dragover',  function(e){ e.preventDefault(); });
	th.addEventListener('drop', function(e){
		e.preventDefault(); e.stopPropagation();
		var origen = e.dataTransfer.getData('reordenar');
		if(origen && origen !== campoKey) reordenarColumna(origen, campoKey);
	});

	thead.appendChild(th);

	var tr = tbody.querySelector('tr') || document.createElement('tr');
	if(!tbody.querySelector('tr')) tbody.appendChild(tr);

	var td = document.createElement('td');
	td.dataset.campo = campoKey;
	td.style.color = '#94a3b8';
	td.textContent = '—';
	tr.appendChild(td);

	var itemIzq = document.querySelector('.mc-campo-item[data-campo="' + campoKey + '"]');
	if(itemIzq) itemIzq.classList.add('activo');

	if(!silencioso){
		guardarSeleccionLS();
		document.getElementById('mcResumen').textContent = camposActuales.length + ' columna(s) seleccionada(s)';
	}

}

function eliminarColumna(campoKey){

	camposActuales = camposActuales.filter(function(c){ return c !== campoKey; });

	var th = document.querySelector('#mcThead [data-campo="' + campoKey + '"]');
	var td = document.querySelector('#mcTbody [data-campo="' + campoKey + '"]');
	if(th) th.remove();
	if(td) td.remove();

	var itemIzq = document.querySelector('.mc-campo-item[data-campo="' + campoKey + '"]');
	if(itemIzq) itemIzq.classList.remove('activo');

	if(document.getElementById('mcThead').children.length === 0) resetTabla();

	guardarSeleccionLS();
	document.getElementById('mcResumen').textContent = camposActuales.length + ' columna(s) seleccionada(s)';

}

function quitarTodosCampos(){
	camposActuales.slice().forEach(function(c){ eliminarColumna(c); });
}

function restaurarCamposDefault(){
	quitarTodosCampos();
	CAMPOS_DEFAULT.forEach(function(key){ agregarColumna(key); });
}

function reordenarColumna(origenKey, destinoKey){

	var thead = document.getElementById('mcThead');
	var tbody = document.getElementById('mcTbody');

	var thO = thead.querySelector('[data-campo="' + origenKey  + '"]');
	var thD = thead.querySelector('[data-campo="' + destinoKey + '"]');
	var tdO = tbody.querySelector('[data-campo="' + origenKey  + '"]');
	var tdD = tbody.querySelector('[data-campo="' + destinoKey + '"]');

	if(thO && thD) thead.insertBefore(thO, thD);
	if(tdO && tdD) tbody.querySelector('tr').insertBefore(tdO, tdD);

	camposActuales = [];
	thead.querySelectorAll('[data-campo]').forEach(function(th){ camposActuales.push(th.dataset.campo); });

	guardarSeleccionLS();

}

/* ============================================
FILTROS
============================================ */

function renderChipFiltro(){
	var wrap = document.getElementById('mfChipWrap');
	wrap.innerHTML = '';
	if(!filtroValor) return;
	var chip = document.createElement('div');
	chip.className = 'mf-chip';
	chip.innerHTML = '<span>' + filtroValor.etiqueta + '</span><button onclick="quitarChipFiltro()">✕</button>';
	wrap.appendChild(chip);
}

function quitarChipFiltro(){
	filtroValor = null;
	renderChipFiltro();
}

async function buscarFiltroValor(termino){

	var drop = document.getElementById('mfDropdown');
	drop.innerHTML = '<div class="ac-empty">Buscando...</div>';
	drop.style.display = 'block';

	try{

		var res  = await fetch(API_REPORTEADOR, {
			method:'POST',
			headers:{'Content-Type':'application/json'},
			body:JSON.stringify({ action:'buscarFiltro', tipo:filtroTipoActivo, termino:termino })
		});

		var json = await res.json();

		if(json.status !== 'ok' || json.data.length === 0){
			drop.innerHTML = '<div class="ac-empty">Sin resultados</div>';
			return;
		}

		drop.innerHTML = '';
		json.data.forEach(function(item){
			var el = document.createElement('div');
			el.className = 'ac-item';
			el.textContent = item.etiqueta;
			el.addEventListener('mousedown', function(){
				filtroValor = { etiqueta:item.etiqueta, valor:item.valor };
				renderChipFiltro();
				document.getElementById('mfBuscadorInput').value = '';
				drop.style.display = 'none';
			});
			drop.appendChild(el);
		});

	} catch(e){
		drop.innerHTML = '<div class="ac-empty">Error de búsqueda</div>';
	}

}

function limpiarFiltros(){
	document.querySelectorAll('.mf-tipo-btn').forEach(function(b){ b.classList.remove('activo'); });
	filtroTipoActivo = '';
	filtroValor = null;
	renderChipFiltro();
	document.getElementById('mfBuscadorWrap').style.display = 'none';
	document.getElementById('mfFechaDesde').value = '';
	document.getElementById('mfFechaHasta').value = '';
	document.getElementById('mfTipoMes').value = '';
	document.getElementById('mfSfp').checked = false;
	document.getElementById('mfSrm0').checked = false;
	document.getElementById('mfRegEsp').checked = false;
}

function cerrarModalFiltros(){
	document.getElementById('modalFiltros').style.display = 'none';
}

function fechaADMY(iso){
	if(!iso) return '';
	var p = iso.split('-');
	if(p.length !== 3) return '';
	return p[2] + '/' + p[1] + '/' + p[0];
}

async function aplicarFiltros(){

	if(!filtroTipoActivo){
		Swal.fire({ icon:'warning', title:'Oops', text:'Elige por qué quieres buscar (Socio Comercial, SubBrocker, etc.)' });
		return;
	}

	if(!filtroValor){
		Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona un valor de la lista de búsqueda' });
		return;
	}

	var fDesde = document.getElementById('mfFechaDesde').value;
	var fHasta = document.getElementById('mfFechaHasta').value;

	if(!fDesde || !fHasta){
		Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona el rango de fechas' });
		return;
	}

	var payload = {
		action:  'generarReporte',
		filtro:  filtroTipoActivo,
		valor1:  filtroValor.valor,
		valor2:  fechaADMY(fDesde),
		valor3:  fechaADMY(fHasta),
		te:      document.getElementById('mfTipoMes').value,
		sfp:     document.getElementById('mfSfp').checked ? 1 : 0,
		srm0:    document.getElementById('mfSrm0').checked ? 1 : 0,
		reg_esp: document.getElementById('mfRegEsp').checked ? 1 : 0,
		campos:  camposActuales
	};

	cerrarModalFiltros();

	Swal.fire({ title:'Generando reporte...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

	try{

		var res  = await fetch(API_REPORTEADOR, {
			method:'POST',
			headers:{'Content-Type':'application/json'},
			body:JSON.stringify(payload)
		});

		var json = await res.json();

		Swal.close();

		if(json.status !== 'ok'){
			Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql || 'No se pudo generar el reporte' });
			return;
		}

		mostrarResultados(json);

	} catch(e){
		Swal.close();
		Swal.fire({ icon:'error', title:'Error', text:e.message });
	}

}

/* ============================================
RESULTADOS
============================================ */

var ultimoResultado = null;

function mostrarResultados(json){

	ultimoResultado = json;

	var tbody = document.getElementById('mcTbody');
	tbody.innerHTML = '';

	document.getElementById('mcPlaceholder').style.display = 'none';
	document.getElementById('mcTabla').style.display = 'table';
	document.getElementById('mcDerechoTitle').textContent = 'Resultados';
	document.getElementById('mcBtnExportar').style.display = 'inline-block';

	var msg = json.data.length + ' registro(s)';
	if(json.limitado) msg += ' — mostrando el tope de 1,000 en pantalla, usa "Exportar a Excel" para el reporte completo';
	document.getElementById('mcResumen').textContent = msg;

	if(json.data.length === 0){
		var trV = document.createElement('tr');
		var tdV = document.createElement('td');
		tdV.colSpan = json.columnas.length;
		tdV.style.cssText = 'text-align:center;color:#94a3b8;padding:30px;';
		tdV.textContent = 'Sin resultados para estos filtros.';
		trV.appendChild(tdV);
		tbody.appendChild(trV);
		return;
	}

	json.data.forEach(function(row){
		var tr = document.createElement('tr');
		json.columnas.forEach(function(col){
			var td = document.createElement('td');
			var val = row[col.key];
			td.textContent = (val !== null && val !== undefined && val !== '') ? val : '—';
			tr.appendChild(td);
		});
		tbody.appendChild(tr);
	});

}

function exportarReporte(){

	if(!filtroTipoActivo || !filtroValor){
		Swal.fire({ icon:'info', title:'Genera el reporte primero', text:'Usa "Filtrar y generar" antes de exportar.' });
		return;
	}

	var params = new URLSearchParams({
		filtro:      filtroTipoActivo,
		valor1:      filtroValor.valor,
		valor2:      fechaADMY(document.getElementById('mfFechaDesde').value),
		valor3:      fechaADMY(document.getElementById('mfFechaHasta').value),
		te:          document.getElementById('mfTipoMes').value,
		sfp:         document.getElementById('mfSfp').checked ? 1 : 0,
		srm0:        document.getElementById('mfSrm0').checked ? 1 : 0,
		reg_esp:     document.getElementById('mfRegEsp').checked ? 1 : 0,
		campos_csv:  camposActuales.join(',')
	});

	window.open('seg_reporteador_excel.php?' + params.toString(), '_blank');

}

</script>

</html>