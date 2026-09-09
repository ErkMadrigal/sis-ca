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
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<style>
			#contenido, #contenido * { text-align: left; }

			/* ── Barra superior ── */
			#barra-top-val {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 14px 18px;
				background: #fff;
				border-bottom: 1px solid #ddd;
				gap: 14px;
				flex-wrap: wrap;
			}

			.val-search-wrap {
				position: relative;
				width: 320px;
				flex-shrink: 0;
			}

			.val-search-input {
				width: 100%;
				box-sizing: border-box;
				border: 1px solid #d0d0d0;
				border-radius: 22px;
				padding: 9px 16px 9px 38px;
				font-size: 13px;
				color: #222;
				background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 12px center;
				outline: none;
				transition: border .2s, box-shadow .2s;
			}

			.val-search-input:focus {
				border-color: #2B73B6;
				box-shadow: 0 0 0 3px rgba(43,115,182,.12);
			}

			/* ── Pills de filtro ── */
			.val-filtros {
				display: flex;
				gap: 8px;
				flex-wrap: wrap;
			}

			.val-pill {
				padding: 7px 16px;
				border-radius: 20px;
				font-size: 12.5px;
				font-weight: 600;
				cursor: pointer;
				border: 1px solid #d8e2e8;
				background: #f4f7f9;
				color: #5a7385;
				transition: all .15s;
				white-space: nowrap;
			}

			.val-pill:hover { background: #e8f0f5; }

			.val-pill.activa {
				background: #2B73B6;
				color: #fff;
				border-color: #2B73B6;
			}

			.val-pill .val-pill-count {
				background: rgba(255,255,255,.25);
				border-radius: 10px;
				padding: 1px 7px;
				font-size: 11px;
				margin-left: 6px;
			}

			.val-pill:not(.activa) .val-pill-count {
				background: #dde8ee;
				color: #2B73B6;
			}

			/* ── Contenedor de tarjetas ── */
			#val-lista {
				padding: 16px 18px;
				max-height: calc(100vh - 280px);
				height: 50vh;
				overflow-y: auto;
			}

			#val-lista::-webkit-scrollbar { width: 8px; }
			#val-lista::-webkit-scrollbar-thumb { background: #c0d4e0; border-radius: 4px; }
			#val-lista::-webkit-scrollbar-track { background: #f0f4f6; }

			.val-card {
				background: #fff;
				border: 1px solid #e4eaee;
				border-radius: 12px;
				margin-bottom: 12px;
				overflow: hidden;
				transition: box-shadow .15s;
			}

			.val-card:hover { box-shadow: 0 3px 14px rgba(0,0,0,.06); }

			.val-card-head {
				display: flex;
				align-items: center;
				gap: 14px;
				padding: 14px 18px;
				cursor: pointer;
			}

			.val-avatar {
				width: 40px;
				height: 40px;
				border-radius: 50%;
				background: linear-gradient(135deg, #2B73B6, #1a5278);
				color: #fff;
				display: flex;
				align-items: center;
				justify-content: center;
				font-weight: 700;
				font-size: 14px;
				flex-shrink: 0;
			}

			.val-card-info { flex: 1; min-width: 0; }

			.val-card-nombre {
				font-size: 14px;
				font-weight: 700;
				color: #1a1a2e;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.val-card-meta {
				font-size: 12px;
				color: #888;
				margin-top: 2px;
				display: flex;
				gap: 10px;
				flex-wrap: wrap;
			}

			.val-card-meta span { display: inline-flex; align-items: center; gap: 3px; }

			.val-badges { display: flex; gap: 6px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

			.val-badge {
				font-size: 10.5px;
				font-weight: 700;
				padding: 3px 10px;
				border-radius: 20px;
				text-transform: uppercase;
				letter-spacing: .03em;
				white-space: nowrap;
			}

			/* Estatus vigentes:
			   0 = Enviado a revisión      -- GRIS
			   1 = Revisado OK             -- AMARILLO
			   2 = Captura Exitosa         -- VERDE
			   3 = Comentarios – Revisa App -- ROJO */
			.badge-enviado          { background: #f0f0f0; color: #666; }
			.badge-revisado-ok      { background: #fef3c7; color: #92400e; }
			.badge-captura-exitosa  { background: #e8f9ef; color: #1e8e58; }
			.badge-comentarios-app  { background: #fdeaea; color: #c0392b; }

			.val-badge-evidencias {
				background: #fdf3e6;
				color: #b87a16;
			}
			.val-badge-evidencias.ok { background: #e8f9ef; color: #1e8e58; }

			.val-chevron {
				color: #bbb;
				font-size: 14px;
				transition: transform .2s;
				flex-shrink: 0;
			}

			.val-card.abierta .val-chevron { transform: rotate(180deg); }

			/* ── Detalle expandido ── */
			.val-detalle {
				display: none;
				border-top: 1px solid #eef2f4;
				padding: 18px 18px 20px;
				background: #fafbfc;
				height: 50vh;
				overflow-y: auto;
			}

			.val-card.abierta .val-detalle { display: block; }

			.val-detalle-grid {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr;
				gap: 14px;
				margin-bottom: 18px;
			}

			.val-detalle-item {
				background: #fff;
				border: 1px solid #eef2f4;
				border-radius: 8px;
				padding: 10px 12px;
			}

			.val-detalle-label {
				font-size: 10.5px;
				font-weight: 700;
				text-transform: uppercase;
				letter-spacing: .04em;
				color: #95aab8;
				margin-bottom: 3px;
			}

			.val-detalle-valor {
				font-size: 13px;
				color: #1a1a2e;
				font-weight: 600;
				word-break: break-word;
			}

			/* ── Evidencias ── */
			.val-evidencias-titulo {
				font-size: 11px;
				font-weight: 700;
				text-transform: uppercase;
				letter-spacing: .05em;
				color: #2B73B6;
				margin-bottom: 10px;
				padding-bottom: 6px;
				border-bottom: 1px solid #e0e8ec;
			}

			.val-evidencias-grid {
				display: flex;
				gap: 10px;
				flex-wrap: wrap;
				margin-bottom: 18px;
			}

			.val-evidencia-item {
				width: 110px;
				border: 1px solid #e4eaee;
				border-radius: 10px;
				overflow: hidden;
				background: #fff;
				cursor: pointer;
				transition: transform .15s, box-shadow .15s;
				text-decoration: none;
				display: block;
			}

			.val-evidencia-item:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(0,0,0,.08);
			}

			.val-evidencia-thumb {
				height: 70px;
				background: #f4f7f9;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 26px;
				color: #2B73B6;
				overflow: hidden;
			}

			.val-evidencia-thumb img {
				width: 100%; height: 100%; object-fit: cover;
			}

			.val-evidencia-nombre {
				font-size: 10.5px;
				color: #555;
				padding: 6px 8px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.val-evidencias-vacio {
				font-size: 12.5px;
				color: #c0392b;
				background: #fdeeee;
				border: 1px solid #f3c4c4;
				border-radius: 8px;
				padding: 10px 14px;
				margin-bottom: 18px;
			}

			/* ── Acciones de estatus ── */
			.val-acciones {
				display: flex;
				gap: 8px;
				flex-wrap: wrap;
				padding-top: 14px;
				border-top: 1px solid #e8eef1;
			}

			.val-btn-estatus {
				border: 1px solid #d8e2e8;
				background: #fff;
				color: #5a7385;
				border-radius: 8px;
				padding: 8px 16px;
				font-size: 12.5px;
				font-weight: 600;
				cursor: pointer;
				transition: all .15s;
			}

			.val-btn-estatus:hover:not(.actual) { background: #f0f6f9; border-color: #2B73B6; color: #2B73B6; }

			.val-btn-estatus.actual {
				opacity: .4;
				cursor: default;
			}

			.val-btn-estatus[data-estatus="1"]:not(.actual) { border-color: #f3d99b; color: #92400e; }
			.val-btn-estatus[data-estatus="1"]:not(.actual):hover { background: #fffaf0; }

			.val-btn-estatus[data-estatus="2"]:not(.actual) { border-color: #a8e0bd; color: #1e8e58; }
			.val-btn-estatus[data-estatus="2"]:not(.actual):hover { background: #eafaf1; }

			.val-btn-estatus[data-estatus="3"]:not(.actual) { border-color: #f0b8b8; color: #c0392b; }
			.val-btn-estatus[data-estatus="3"]:not(.actual):hover { background: #fdeeee; }

			/* ── Estados vacío / carga ── */
			.val-estado-vacio, .val-estado-cargando {
				text-align: center;
				padding: 60px 20px;
				color: #aaa;
				font-size: 13.5px;
			}

			.val-estado-vacio svg, .val-estado-cargando svg { display: block; margin: 0 auto 14px; opacity: .35; }

			.val-spinner {
				display: inline-block;
				width: 14px; height: 14px;
				border: 2px solid #ccc;
				border-top-color: #2B73B6;
				border-radius: 50%;
				animation: val-spin .7s linear infinite;
				vertical-align: -2px;
				margin-right: 6px;
			}

			@keyframes val-spin { to { transform: rotate(360deg); } }

			/* ── Lightbox simple para imágenes ── */
			#val-lightbox {
				display: none;
				position: fixed;
				inset: 0;
				background: rgba(10,18,32,.85);
				z-index: 99999;
				justify-content: center;
				align-items: center;
				padding: 30px;
				cursor: zoom-out;
			}

			#val-lightbox img {
				max-width: 90%;
				max-height: 90%;
				border-radius: 8px;
				box-shadow: 0 10px 40px rgba(0,0,0,.4);
			}

			/* ── Comentarios: scroll contenido y bonito ──
			Antes el contenedor de comentarios no tenía scrollbar propia
			estilizada y los mensajes largos (sin espacios, ej. links) no
			hacían wrap -- con muchos comentarios o uno muy largo, la
			tarjeta se estiraba y rompía el layout de la página. Ahora el
			contenedor tiene tamaño fijo, scroll interno con su propia
			scrollbar delgada, y los mensajes envuelven el texto sin
			desbordarse. */
			.val-comentarios-lista {
				max-height: 220px;
				overflow-y: auto;
				overflow-x: hidden;
				margin-bottom: 8px;
				padding-right: 4px;
				scroll-behavior: smooth;
			}

			.val-comentarios-lista::-webkit-scrollbar { width: 6px; }
			.val-comentarios-lista::-webkit-scrollbar-thumb { background: #c7d6e0; border-radius: 6px; }
			.val-comentarios-lista::-webkit-scrollbar-thumb:hover { background: #a8bdcb; }
			.val-comentarios-lista::-webkit-scrollbar-track { background: transparent; }

			.val-comentario-item {
				padding: 8px 10px;
				border-radius: 8px;
				margin-bottom: 6px;
				border: 1px solid #eef2f4;
				overflow-wrap: anywhere;
				word-break: break-word;
			}

			.val-comentario-item p {
				overflow-wrap: anywhere;
				word-break: break-word;
			}

			/* ── Botón que abre el modal de comentarios ── */
			.val-btn-comentarios {
				border: 1px solid #d8e2e8;
				background: #f4f7f9;
				color: #2B73B6;
				border-radius: 8px;
				padding: 8px 16px;
				font-size: 12.5px;
				font-weight: 700;
				cursor: pointer;
				transition: all .15s;
			}

			.val-btn-comentarios:hover { background: #e8f0f5; border-color: #2B73B6; }

			/* ── Modal de comentarios ──
			Ojo: NO se usa el modal de Bootstrap. En este sitio ya se carga
			jQuery/Bootstrap por otro lado (clas_carobra.php) y esta misma
			página vuelve a cargar OTRA copia por CDN -- dos jQuery distintos
			pisándose hacen que $(...).modal('show') no aplique bien las
			clases/backdrop (por eso se veía pegado arriba, sin fondo oscuro).
			Se resuelve igual que el lightbox de evidencias de abajo: un
			overlay position:fixed que se muestra/oculta a mano con JS, sin
			depender de ningún plugin. */
			.val-modal-overlay {
				display: none !important;
				position: fixed !important;
				top: 0 !important;
				left: 0 !important;
				right: 0 !important;
				bottom: 0 !important;
				width: 100vw !important;
				height: 100vh !important;
				margin: 0 !important;
				transform: none !important;
				background: rgba(10,18,32,.55);
				z-index: 2147483647 !important;
				justify-content: center;
				align-items: center;
				padding: 20px;
				box-sizing: border-box;
			}

			.val-modal-overlay.abierto { display: flex !important; }

			.val-modal-box {
				background: #fff;
				border-radius: 14px;
				width: 100%;
				max-width: 480px;
				height: auto;
				max-height: 80vh;
				min-height: 320px;
				display: flex;
				flex-direction: column;
				box-shadow: 0 20px 60px rgba(0,0,0,.35);
				overflow: hidden;
			}

			.val-modal-header {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 10px;
				padding: 16px 20px;
				border-bottom: 1px solid #eef2f4;
				flex-shrink: 0;
			}

			.val-modal-header h5 {
				margin: 0;
				font-size: 15px;
				font-weight: 700;
				color: #1a1a2e;
			}

			.val-modal-close {
				background: none;
				border: none;
				font-size: 24px;
				line-height: 1;
				color: #aaa;
				cursor: pointer;
				padding: 0 2px;
			}

			.val-modal-close:hover { color: #333; }

			.val-modal-body {
				padding: 18px 20px;
				overflow: hidden;
				flex: 1;
				min-height: 0;
			}

			.val-modal-footer {
				display: flex;
				gap: 8px;
				padding: 14px 20px;
				border-top: 1px solid #eef2f4;
				flex-shrink: 0;
			}

			.val-modal-footer input {
				flex: 1;
				border: 1px solid #d0d0d0;
				border-radius: 8px;
				padding: 8px 12px;
				font-size: 13px;
				outline: none;
			}

			.val-modal-footer input:focus {
				border-color: #2B73B6;
				box-shadow: 0 0 0 3px rgba(43,115,182,.12);
			}

			.val-modal-footer button {
				background: #2B73B6;
				color: #fff;
				border: none;
				border-radius: 8px;
				padding: 8px 18px;
				font-size: 13px;
				font-weight: 700;
				cursor: pointer;
			}

			.val-modal-footer button:hover { background: #1a5278; }

			#modal-comentarios-lista {
				max-height: 340px;
			}

			.badge-en_revision { background: #f0f0f0;  color: #888;     }
			.badge-revisado    { background: #fff8e6;  color: #856404;  }
			.badge-exitosa     { background: #e8f9ef;  color: #1e8e58;  }
			.badge-comentarios { background: #fdeeee;  color: #c0392b;  }
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Cargando...</div>
			</div>
		</div>

	<?php
		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));

		if($periodo > 600){
			session_destroy(); session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"] = "p";
		}

		if ($_SESSION["autentifica"] != "p"){
			session_destroy(); session_unset();
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
				titulo("Validación de Capturas");
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
	?>

	<div class="container-fluid" style="padding:0; text-align:left;">

		<!-- Barra superior: buscador + filtros -->
		<div id="barra-top-val">
			<div class="val-search-wrap">
				<input type="text" id="val-busqueda" class="val-search-input" placeholder="Buscar folio o nombre..." autocomplete="off">
			</div>

			<div class="val-filtros">
				<div class="val-pill activa" data-filtro="0">Enviado a revisión <span class="val-pill-count" id="cnt-0">0</span></div>
				<div class="val-pill" data-filtro="1">Revisado OK <span class="val-pill-count" id="cnt-1">0</span></div>
				<div class="val-pill" data-filtro="2">Captura Exitosa <span class="val-pill-count" id="cnt-2">0</span></div>
				<div class="val-pill" data-filtro="3">Comentarios - Revisa App <span class="val-pill-count" id="cnt-3">0</span></div>
				<div class="val-pill" data-filtro="TODOS">Todos</div>
			</div>
		</div>

		<!-- Lista de folios -->
		<div id="val-lista">
			<div class="val-estado-cargando">
				<span class="val-spinner"></span> Cargando folios...
			</div>
		</div>

	</div>

	<!-- Lightbox para evidencias tipo imagen -->
	<div id="val-lightbox" onclick="cerrarLightbox()">
		<img id="val-lightbox-img" src="" alt="">
	</div>

	<!-- Modal de comentarios (compartido, se llena según el folio que se abra).
	     No es Bootstrap modal -- ver nota en el CSS de .val-modal-overlay. -->
	<div id="modal-comentarios-overlay" class="val-modal-overlay" onclick="if(event.target===this) cerrarModalComentarios()">
		<div class="val-modal-box">
			<div class="val-modal-header">
				<h5>💬 Comentarios — Folio <span id="modal-comentarios-folio"></span></h5>
				<button type="button" class="val-modal-close" onclick="cerrarModalComentarios()">&times;</button>
			</div>
			<div class="val-modal-body">
				<div id="modal-comentarios-lista" class="val-comentarios-lista">
					<p style="font-size:12px;color:#aaa;text-align:center;padding:8px;">Cargando...</p>
				</div>
			</div>
			<div class="val-modal-footer">
				<input id="modal-inp-comentario" type="text" placeholder="Escribe un comentario..." autocomplete="off">
				<button onclick="agregarComentarioModal()">Enviar</button>
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
		$('#opcion').load('menu.php?modulo=<?php echo $pag?>');
	</script>

	<?php } ?>

	</body>

	<script type="text/javascript">

	const API = 'validacion_api.php';

	async function api(payload) {
		const resp = await fetch(API, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	/* Estatus vigentes (valor numérico guardado en estatus_validacion):
	   0 = Enviado a revisión       -- GRIS
	   1 = Revisado OK              -- AMARILLO
	   2 = Captura Exitosa          -- VERDE
	   3 = Comentarios – Revisa App -- ROJO */
	const ETIQUETAS_ESTATUS = {
		'0': 'Enviado a revisión',
		'1': 'Revisado OK',
		'2': 'Captura Exitosa',
		'3': 'Comentarios - Revisa App'
	};

	const CLASES_BADGE = {
		'0': 'badge-en_revision',
		'1': 'badge-revisado',
		'2': 'badge-exitosa',
		'3': 'badge-comentarios'
	};

	let filtroActual = '0';

	let timerBusqueda = null;
	let foliosCache = [];
	let evidenciasCache = {}; // folio -> array de evidencias (cache para no recargar al reabrir)

	/* ════════════════════════════════════════
	   Inicial: cargar lista
	════════════════════════════════════════ */
	document.addEventListener('DOMContentLoaded', function () {
		cargarFolios();

		document.getElementById('val-busqueda').addEventListener('keyup', function (e) {
			clearTimeout(timerBusqueda);
			timerBusqueda = setTimeout(() => cargarFolios(), 350);
		});

		document.querySelectorAll('.val-pill').forEach(pill => {
			pill.addEventListener('click', function () {
				document.querySelectorAll('.val-pill').forEach(p => p.classList.remove('activa'));
				this.classList.add('activa');
				filtroActual = this.dataset.filtro;
				cargarFolios();
			});
		});

		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		document.getElementById('salir').addEventListener('click', () => {
			window.location = 'https://www.sis-ca.com.mx:7863/';
		});
		document.getElementById('email').addEventListener('click', () => {
			window.open('http://webmail.carobra.com.mx/imp/login.php');
		});
	});

	/* ════════════════════════════════════════
	   Cargar folios según filtro/búsqueda
	════════════════════════════════════════ */
	async function cargarFolios() {
		const q = document.getElementById('val-busqueda').value.trim();
		const lista = document.getElementById('val-lista');

		lista.innerHTML = '<div class="val-estado-cargando"><span class="val-spinner"></span> Cargando folios...</div>';

		try {
			const resp = await api({ action: 'buscarFoliosValidacion', q, filtro: filtroActual });
			if (!resp.ok) {
				lista.innerHTML = '<div class="val-estado-vacio">Error al cargar los folios.</div>';
				return;
			}

			foliosCache = resp.datos || [];
			renderLista(foliosCache);
			actualizarContadores();

		} catch (e) {
			lista.innerHTML = '<div class="val-estado-vacio">Error de comunicación con el servidor.</div>';
		}
	}

	/* ════════════════════════════════════════
	   Contadores de los pills (calculados sobre TODOS, sin filtro de pill activa)
	════════════════════════════════════════ */
	async function actualizarContadores() {
		try {
			const resp = await api({ action: 'buscarFoliosValidacion', q: '', filtro: 'TODOS' });
			if (!resp.ok) return;
			const todos = resp.datos || [];

			const cont = { '0': 0, '1': 0, '2': 0, '3': 0 };
			todos.forEach(f => { if (cont[f.estatus_validacion] !== undefined) cont[f.estatus_validacion]++; });

			document.getElementById('cnt-0').textContent = cont['0'];
			document.getElementById('cnt-1').textContent = cont['1'];
			document.getElementById('cnt-2').textContent = cont['2'];
			document.getElementById('cnt-3').textContent = cont['3'];
		} catch (e) { }
	}

	/* ════════════════════════════════════════
	   Render de la lista (tarjetas colapsadas)
	════════════════════════════════════════ */
	function renderLista(folios) {
		const lista = document.getElementById('val-lista');

		if (folios.length === 0) {
			lista.innerHTML = `
				<div class="val-estado-vacio">
					<svg width="48" height="48" fill="none" stroke="#bbb" stroke-width="1.4" viewBox="0 0 24 24">
						<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
					</svg>
					No hay folios con este filtro.
				</div>`;
			return;
		}

		lista.innerHTML = folios.map(f => {
			const iniciales = obtenerIniciales(f.trabajador);
			const badgeClase = CLASES_BADGE[f.estatus_validacion] || 'badge-enviado';
			const badgeTexto = ETIQUETAS_ESTATUS[f.estatus_validacion] || f.estatus_validacion;
			const tieneEvidencias = f.total_evidencias > 0;

			return `
				<div class="val-card" id="card-${f.folio}">
					<div class="val-card-head" onclick="toggleCard('${f.folio}')">
						<div class="val-avatar">${iniciales}</div>
						<div class="val-card-info">
							<div class="val-card-nombre">${f.trabajador || 'Sin nombre'}</div>
							<div class="val-card-meta">
								<span>📁 Folio ${f.folio}</span>
								<span>👤 ${f.nombre_comisionista || 'Sin comisionista'}</span>
								<span>🗓️ ${f.fec_traspaso || '—'}</span>
							</div>
						</div>
						<div class="val-badges">
							<span class="val-badge ${badgeClase}">${badgeTexto}</span>
							<span class="val-badge val-badge-evidencias ${tieneEvidencias ? 'ok' : ''}">
								${tieneEvidencias ? '📎 ' + f.total_evidencias : '⚠️ Sin evidencia'}
							</span>
						</div>
						<span class="val-chevron">▼</span>
					</div>
					<div class="val-detalle" id="detalle-${f.folio}">
						<div class="val-detalle-grid">
							<div class="val-detalle-item">
								<div class="val-detalle-label">NSS</div>
								<div class="val-detalle-valor">${f.nss || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">NAP</div>
								<div class="val-detalle-valor">${f.nap || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Afore cedente</div>
								<div class="val-detalle-valor">${f.afo_cedente || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Empresa</div>
								<div class="val-detalle-valor">${f.empresa || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Email</div>
								<div class="val-detalle-valor">${f.email || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Celular</div>
								<div class="val-detalle-valor">${f.celular || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">C.P.</div>
								<div class="val-detalle-valor">${f.cp || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Capturado por</div>
								<div class="val-detalle-valor">${f.clave_usuario || '—'}</div>
							</div>
							<div class="val-detalle-item">
								<div class="val-detalle-label">Fecha de captura</div>
								<div class="val-detalle-valor">${f.fecha_captura || '—'}</div>
							</div>
						</div>

						<div class="val-evidencias-titulo">Evidencias documentales</div>
						<div id="evidencias-${f.folio}">
							<div style="font-size:12.5px;color:#aaa;"><span class="val-spinner"></span> Cargando evidencias...</div>
						</div>

						<div class="val-acciones">
							<button class="val-btn-estatus ${f.estatus_validacion == 0 ? 'actual' : ''}" 
									data-estatus="0" onclick="cambiarEstatus('${f.folio}', '0')" 
									${f.estatus_validacion == 0 ? 'disabled' : ''}>
								Enviado a revisión
							</button>
							<button class="val-btn-estatus ${f.estatus_validacion == 3 ? 'actual' : ''}" 
									data-estatus="3" onclick="cambiarEstatus('${f.folio}', '3')" 
									${f.estatus_validacion == 3 ? 'disabled' : ''}
									style="border-color:#f3c4c4; color:#c0392b;">
								Comentarios – Revisa App
							</button>
							<button class="val-btn-estatus ${f.estatus_validacion == 1 ? 'actual' : ''}" 
									data-estatus="1" onclick="cambiarEstatus('${f.folio}', '1')" 
									${f.estatus_validacion == 1 ? 'disabled' : ''}
									style="border-color:#fde68a; color:#856404;">
								Revisado OK
							</button>
							<button class="val-btn-estatus ${f.estatus_validacion == 2 ? 'actual' : ''}" 
									data-estatus="2" onclick="cambiarEstatus('${f.folio}', '2')" 
									${f.estatus_validacion == 2 ? 'disabled' : ''}
									style="border-color:#a8e0bd; color:#1e8e58;">
								Captura Exitosa
							</button>
						</div>

						<div style="margin-top:14px;padding-top:14px;border-top:1px solid #eef2f4;">
							<button type="button" class="val-btn-comentarios" onclick="abrirModalComentarios('${f.folio}')">
								💬 Ver comentarios
							</button>
						</div>
					</div>
				</div>
			`;
		}).join('');
	}

	function obtenerIniciales(nombre) {
		if (!nombre) return '?';
		const partes = nombre.trim().split(/\s+/);
		return ((partes[0]?.[0] || '') + (partes[1]?.[0] || '')).toUpperCase();
	}

	/* ════════════════════════════════════════
	   Expandir/colapsar tarjeta + cargar evidencias la primera vez
	════════════════════════════════════════ */
	async function toggleCard(folio) {
		const card = document.getElementById(`card-${folio}`);
		const yaAbierta = card.classList.contains('abierta');

		// Cerrar todas las demás (acordeón)
		document.querySelectorAll('.val-card.abierta').forEach(c => {
			if (c !== card) c.classList.remove('abierta');
		});

		card.classList.toggle('abierta', !yaAbierta);

		if (!yaAbierta && !evidenciasCache[folio]) {
			await cargarEvidencias(folio);
		}
	}

	async function cargarEvidencias(folio) {
		const cont = document.getElementById(`evidencias-${folio}`);
		try {
			const resp = await api({ action: 'getEvidenciasFolio', folio });
			if (!resp.ok) {
				cont.innerHTML = '<div class="val-evidencias-vacio">Error al cargar evidencias.</div>';
				return;
			}

			const evidencias = resp.evidencias || [];
			evidenciasCache[folio] = evidencias;

			if (evidencias.length === 0) {
				cont.innerHTML = '<div class="val-evidencias-vacio">⚠️ Este folio no tiene evidencias adjuntas.</div>';
				return;
			}

			cont.innerHTML = `<div class="val-evidencias-grid">${evidencias.map(ev => renderEvidenciaItem(ev)).join('')}</div>`;

		} catch (e) {
			cont.innerHTML = '<div class="val-evidencias-vacio">Error de comunicación con el servidor.</div>';
		}
	}

	function renderEvidenciaItem(ev) {
		const esImagen = ev.tipo === 'jpg' || ev.tipo === 'jpeg' || ev.tipo === 'png';
		const thumb = esImagen
			? `<img src="${ev.url}" alt="${ev.nombre}">`
			: '📄';

		const onclickAttr = esImagen
			? `abrirLightbox('${ev.url}')`
			: `window.open('${ev.url}', '_blank')`;

		return `
			<div class="val-evidencia-item" onclick="${onclickAttr}">
				<div class="val-evidencia-thumb">${thumb}</div>
				<div class="val-evidencia-nombre">${ev.nombre}</div>
			</div>
		`;
	}

	function abrirLightbox(url) {
		document.getElementById('val-lightbox-img').src = url;
		document.getElementById('val-lightbox').style.display = 'flex';
	}

	function cerrarLightbox() {
		document.getElementById('val-lightbox').style.display = 'none';
	}

	/* ════════════════════════════════════════
	   Cambiar estatus de validación
	════════════════════════════════════════ */
	async function cambiarEstatus(folio, nuevoEstatus) {
		const result = await Swal.fire({
			title: `¿Marcar como "${ETIQUETAS_ESTATUS[nuevoEstatus]}"?`,
			text: `El folio ${folio} cambiará su estatus de validación.`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Sí, confirmar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		document.getElementById('loader').style.display = 'flex';

		try {
			const resp = await api({ action: 'cambiarEstatusValidacion', folio, estatus: nuevoEstatus });
			document.getElementById('loader').style.display = 'none';

			if (resp.ok) {
				Swal.fire({ icon:'success', title:'Estatus actualizado', timer:1300, showConfirmButton:false });
				cargarFolios();
			} else {
				Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
			}
		} catch (e) {
			document.getElementById('loader').style.display = 'none';
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}


	/* ════════════════════════════════════════
	   Modal de comentarios (un solo modal compartido; se recarga
	   según el folio que se abra desde la tarjeta)
	════════════════════════════════════════ */
	let folioComentariosActual = null;

	function abrirModalComentarios(folio) {
		folioComentariosActual = folio;
		document.getElementById('modal-comentarios-folio').textContent = folio;
		document.getElementById('modal-inp-comentario').value = '';
		document.getElementById('modal-comentarios-lista').innerHTML =
			'<p style="font-size:12px;color:#aaa;text-align:center;padding:8px;">Cargando...</p>';

		document.getElementById('modal-comentarios-overlay').classList.add('abierto');
		cargarComentariosModal(folio);
	}

	function cerrarModalComentarios() {
		document.getElementById('modal-comentarios-overlay').classList.remove('abierto');
		folioComentariosActual = null;
	}

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && document.getElementById('modal-comentarios-overlay').classList.contains('abierto')) {
			cerrarModalComentarios();
		}
	});

	async function cargarComentariosModal(folio) {
		const cont = document.getElementById('modal-comentarios-lista');

		try {
			const res = await fetch('comentarios_sisca_api.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ action: 'listar', tipo: 'traspaso', referencia: folio })
			}).then(r => r.json());

			// Si el usuario ya cambió de folio antes de que responda, ignora esta respuesta vieja
			if (folio !== folioComentariosActual) return;

			const datos = res.datos || [];

			if (datos.length === 0) {
				cont.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;padding:8px;">Sin comentarios</p>';
				return;
			}

			cont.innerHTML = datos.map(c => `
				<div class="val-comentario-item" style="background:${c.origen === 'app' ? '#f0f5ff' : '#f8fafc'};">
				<div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
					<span style="font-size:11px;font-weight:700;color:${c.origen === 'sisca' ? '#2B73B6' : '#475569'};">
					${c.nombre_autor}
					</span>
					<span style="font-size:10px;padding:1px 6px;border-radius:4px;
								background:${c.origen === 'sisca' ? '#e8f0fb' : '#f1f5f9'};
								color:${c.origen === 'sisca' ? '#2B73B6' : '#64748b'};">
					${c.origen === 'sisca' ? 'SISCA' : 'App'}
					</span>
					<span style="font-size:10px;color:#aaa;margin-left:auto;">${c.creado_en}</span>
				</div>
				<p style="font-size:12px;color:#333;margin:0;">${c.mensaje}</p>
				</div>
			`).join('');

			// deja el scroll hasta abajo (el comentario más reciente)
			cont.scrollTop = cont.scrollHeight;

		} catch (e) {
			cont.innerHTML = '<p style="font-size:12px;color:#c0392b;text-align:center;padding:8px;">Error al cargar comentarios.</p>';
		}
	}

	async function agregarComentarioModal() {
		const folio = folioComentariosActual;
		if (!folio) return;

		const input = document.getElementById('modal-inp-comentario');
		const msg   = input?.value.trim();
		if (!msg) return;

		const res = await fetch('comentarios_sisca_api.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ action: 'agregar', tipo: 'traspaso', referencia: folio, mensaje: msg })
		}).then(r => r.json());

		if (res.ok) {
			input.value = '';
			cargarComentariosModal(folio);
		}
	}

	// Enter dentro del input del modal también envía
	document.addEventListener('DOMContentLoaded', function () {
		document.getElementById('modal-inp-comentario').addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				agregarComentarioModal();
			}
		});

		/* El overlay nace metido dentro de los divs del framework del sitio
		(#contenido / #contenedor), y esos divs traen su propio position/height
		calculados por JS (calcula_h, etc.) -- eso puede romper el
		position:fixed y dejar el modal chiquito/atorado arriba en vez de
		cubrir toda la pantalla. Sacándolo como hijo directo de <body> se
		garantiza que "fixed" sea relativo a la ventana, no a ese contenedor. */
		var overlay = document.getElementById('modal-comentarios-overlay');
		if (overlay && overlay.parentElement !== document.body) {
			document.body.appendChild(overlay);
		}
	});

	</script>
</html>