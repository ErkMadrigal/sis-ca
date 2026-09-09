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

		<script src="./jquery/jquery-3.3.1.min.js"></script>
		<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
		<script src="./popper/popper.js"></script>
		<script src="./bootstrap/js/bootstrap.min.js"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

		<style>
			@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

			#contenido, #contenido * { text-align: left; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

			.kp-wrap { max-width: 1360px; margin: 18px auto 40px auto; padding: 0 16px; }

			/* ---------- Hero ---------- */
			.kp-hero {
				background: linear-gradient(120deg, #14335E 0%, #1E5B9E 55%, #2B84C7 100%);
				border-radius: 18px; padding: 26px 30px; color: #FFF;
				box-shadow: 0 10px 30px -12px rgba(20,51,94,.45);
				position: relative; overflow: hidden;
			}
			.kp-hero::before { content:""; position:absolute; right:-60px; top:-60px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.08); }
			.kp-hero::after  { content:""; position:absolute; right:80px; bottom:-90px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.06); }
			.kp-hero-titulo { font-size:22px; font-weight:800; margin:0; }
			.kp-hero-sub    { font-size:13px; color:rgba(255,255,255,.78); margin-top:4px; }

			/* ---------- Tabs ---------- */
			.kp-tabs { display:flex; gap:8px; margin: 18px 0 4px 0; }
			.kp-tab {
				padding:9px 18px; border-radius:10px; font-size:13px; font-weight:700;
				cursor:pointer; border:1px solid #E2E8F0; background:#F8FAFC; color:#64748B;
			}
			.kp-tab.activo { background:#1E5B9E; color:#FFF; border-color:#1E5B9E; }

			/* ---------- Barra filtro ---------- */
			.kp-filtro {
				background:#FFF; border-radius:16px; box-shadow:0 8px 24px -14px rgba(15,23,42,.25), 0 1px 2px rgba(15,23,42,.06);
				padding:16px 20px; margin-top:14px; display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;
			}
			.kp-campo label { font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; display:block; }
			.kp-campo select, .kp-campo input {
				height:40px; font-size:13px; border-radius:10px; border:1.5px solid #E2E8F0; padding:0 12px;
			}
			.kp-campo select:focus, .kp-campo input:focus { border-color:#2B84C7; box-shadow:0 0 0 3px rgba(43,132,199,.15); outline:none; }

			.kp-btn {
				height:40px; padding:0 18px; border:none; border-radius:10px; font-size:13px; font-weight:700;
				cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:.15s;
			}
			.kp-btn-primario { background:linear-gradient(135deg,#1E5B9E,#2B84C7); color:#FFF; box-shadow:0 8px 18px -8px rgba(30,91,158,.6); }
			.kp-btn-primario:hover { transform:translateY(-1px); }
			.kp-btn-verde { background:linear-gradient(135deg,#0E9F6E,#17B27E); color:#FFF; box-shadow:0 8px 18px -8px rgba(14,159,110,.55); }
			.kp-btn-verde:hover { transform:translateY(-1px); }

			/* ---------- Resumen ---------- */
			.kp-resumen { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:14px; margin:18px 0; }
			.kp-card { background:#FFF; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 6px 18px -12px rgba(15,23,42,.18); border:1px solid #F1F5F9; }
			.kp-card-icono { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
			.kp-card-icono.azul { background:#EAF2FC; color:#1E5B9E; }
			.kp-card-icono.verde { background:#E7F9F1; color:#0E9F6E; }
			.kp-card-icono.naranja { background:#FEF3C7; color:#92400E; }
			.kp-card-icono.morado { background:#F2ECFC; color:#7C3AED; }
			.kp-card .num { font-size:21px; font-weight:800; color:#0F172A; line-height:1.1; }
			.kp-card .lbl { font-size:10.5px; color:#94A3B8; text-transform:uppercase; letter-spacing:.5px; margin-top:2px; font-weight:700; }

			/* ---------- Tabla de evaluados ---------- */
			.kp-emp-avatar {
				width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,#1E5B9E,#2B84C7);
				color:#FFF; font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;
			}
			.kp-emp-nombre { font-size:13.5px; font-weight:700; color:#0F172A; line-height:1.2; }
			.kp-emp-cat { font-size:11px; color:#94A3B8; margin-top:2px; }

			#kpTablaEvaluados { font-size:12.5px; width:100%; border-collapse:separate; border-spacing:0; margin-bottom:0; }
			#kpTablaEvaluados thead th { background:#F8FAFC; color:#475569; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:1.5px solid #E2E8F0; padding:12px 14px; white-space:nowrap; }
			#kpTablaEvaluados tbody td { padding:11px 14px; border-bottom:1px solid #F1F5F9; color:#1E293B; vertical-align:middle; }
			#kpTablaEvaluados tbody tr { cursor:pointer; }
			#kpTablaEvaluados tbody tr:hover { background:#F8FBFF; }
			#kpTablaEvaluados .kp-emp-celda { display:flex; align-items:center; gap:10px; }

			.kp-barra-cont { width:80px; height:7px; background:#EEF2F6; border-radius:20px; overflow:hidden; display:inline-block; vertical-align:middle; }
			.kp-barra-fill { height:100%; background:#2B84C7; border-radius:20px; }
			.kp-barra-txt { font-size:11.5px; font-weight:700; color:#1E293B; margin-left:8px; }

			.kp-score-num { font-size:14px; font-weight:800; color:#0F172A; }
			.kp-score-lbl { font-size:10.5px; color:#94A3B8; }

			.kp-badge { display:inline-block; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; border-radius:20px; padding:3px 10px; }
			.kp-badge-completo { background:#E7F9F1; color:#0E9F6E; }
			.kp-badge-en_progreso { background:#FEF3C7; color:#92400E; }
			.kp-badge-pendiente { background:#F1F5F9; color:#64748B; }
			.kp-badge-sin_kpis { background:#FDECEC; color:#C0392B; }

			.kp-vacio { text-align:center; padding:60px 20px; color:#94A3B8; font-size:13.5px; }

			/* ---------- Catálogo (tabla) ---------- */
			.kp-tabla-cont { background:#FFF; border-radius:16px; box-shadow:0 6px 18px -12px rgba(15,23,42,.18); border:1px solid #F1F5F9; overflow:hidden; margin-top:16px; }
			.kp-tabla-scroll { overflow-x:auto; }
			#kpTablaCatalogo { font-size:12.5px; width:100%; border-collapse:separate; border-spacing:0; margin-bottom:0; }
			#kpTablaCatalogo thead th { background:#F8FAFC; color:#475569; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:1.5px solid #E2E8F0; padding:12px 14px; white-space:nowrap; }
			#kpTablaCatalogo tbody td { padding:11px 14px; border-bottom:1px solid #F1F5F9; color:#1E293B; vertical-align:middle; }
			#kpTablaCatalogo tbody tr:hover { background:#F8FBFF; }
			#kpTablaCatalogo tbody tr.inactivo { opacity:.45; }

			/* Regla genérica para cualquier otra tabla dentro de un
			   .kp-tabla-cont que no tenga su propio set de reglas por ID
			   (Indicadores, Asignadores) -- antes se veían sin padding
			   (pegadas) porque solo #kpTablaCatalogo/#kpTablaEvaluados
			   tenían estilo definido. */
			.kp-tabla-cont table { font-size:12.5px; width:100%; border-collapse:separate; border-spacing:0; margin-bottom:0; }
			.kp-tabla-cont table thead th { background:#F8FAFC; color:#475569; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:1.5px solid #E2E8F0; padding:12px 14px; white-space:nowrap; text-align:left; }
			.kp-tabla-cont table tbody td { padding:11px 14px; border-bottom:1px solid #F1F5F9; color:#1E293B; vertical-align:middle; }
			.kp-tabla-cont table tbody tr:last-child td { border-bottom:none; }
			.kp-tabla-cont table tbody tr:hover { background:#F8FBFF; }
			.kp-tabla-cont table tbody tr.inactivo { opacity:.45; }

			.kp-ponderacion-pill { display:inline-block; padding:3px 10px; border-radius:999px; font-weight:700; font-size:11.5px; background:#EAF2FC; color:#1E5B9E; }
			.kp-suma-ponderacion { font-size:12px; font-weight:700; padding:10px 16px; }
			.kp-suma-ok { color:#0E9F6E; }
			.kp-suma-mal { color:#C0392B; }

			.kp-icon-btn { background:none; border:none; cursor:pointer; color:#94A3B8; padding:4px; border-radius:6px; }
			.kp-icon-btn:hover { background:#F1F5F9; color:#1E5B9E; }

			/* ---------- Modal evaluación ---------- */
			.kp-modal-overlay {
				display:none !important; position:fixed !important; top:0 !important; left:0 !important; right:0 !important; bottom:0 !important;
				width:100vw !important; height:100vh !important; margin:0 !important; background:rgba(10,18,32,.55);
				z-index:2147483647 !important; justify-content:center; align-items:center; padding:20px; box-sizing:border-box;
			}
			.kp-modal-overlay.abierto { display:flex !important; }

			/* SweetAlert2 tiene que quedar SIEMPRE por encima de nuestros modales
			   (si no, se dispara pero queda oculto detrás y parece que "no pasa
			   nada" al guardar/validar). Mismo z-index tope; como Swal2 crea su
			   contenedor de nuevo cada vez que se llama (siempre al final del
			   <body>), en caso de empate gana él por orden en el DOM. */
			.swal2-container { z-index: 2147483647 !important; }

			.kp-modal-box {
				background:#fff; border-radius:16px; width:100%; max-width:820px; max-height:88vh;
				display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.35); overflow:hidden;
			}
			.kp-modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid #eef2f4; flex-shrink:0; }
			.kp-modal-header h5 { margin:0; font-size:15.5px; font-weight:800; color:#0F172A; }
			.kp-modal-header .sub { font-size:11.5px; color:#94A3B8; margin-top:2px; font-weight:600; }
			.kp-modal-close { background:none; border:none; font-size:24px; color:#aaa; cursor:pointer; line-height:1; }
			.kp-modal-close:hover { color:#333; }
			.kp-modal-body { padding:18px 22px; overflow-y:auto; flex:1; min-height:0; max-height:60vh; }
			.kp-modal-footer { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 22px; border-top:1px solid #eef2f4; flex-shrink:0; background:#FAFBFC; }

			.kp-eval-item { border:1px solid #EEF2F4; border-radius:12px; padding:14px 16px; margin-bottom:12px; }
			.kp-eval-top { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
			.kp-eval-nombre { font-size:13px; font-weight:700; color:#0F172A; }
			.kp-eval-meta { font-size:11px; color:#94A3B8; margin-top:2px; }
			.kp-eval-pond { font-size:11px; font-weight:800; color:#1E5B9E; background:#EAF2FC; padding:3px 10px; border-radius:20px; white-space:nowrap; }

			.kp-eval-campos { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:12px; }
			.kp-eval-campos label { font-size:10px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; display:block; }
			.kp-eval-campos input, .kp-eval-campos textarea {
				width:100%; border:1.5px solid #E2E8F0; border-radius:8px; padding:7px 10px; font-size:12.5px; box-sizing:border-box;
			}
			.kp-eval-campos input:focus, .kp-eval-campos textarea:focus { border-color:#2B84C7; outline:none; box-shadow:0 0 0 3px rgba(43,132,199,.12); }
			.kp-eval-calif { text-align:center; }
			.kp-eval-calif .valor { font-size:16px; font-weight:800; color:#0E9F6E; }

			.kp-total-box { display:flex; align-items:center; gap:10px; }
			.kp-total-box .num { font-size:20px; font-weight:800; color:#0F172A; }
			.kp-total-box .lbl { font-size:10.5px; color:#94A3B8; text-transform:uppercase; font-weight:700; }

			/* ---------- Buscador en tiempo real ---------- */
			.kp-buscador-wrap { position:relative; margin-top:14px; }
			.kp-buscador-caja {
				background:#FFF; border-radius:16px; box-shadow:0 8px 24px -14px rgba(15,23,42,.25), 0 1px 2px rgba(15,23,42,.06);
				padding:14px 18px; display:flex; align-items:center; gap:10px;
			}
			.kp-buscador-caja .ico { font-size:16px; color:#94A3B8; }
			.kp-buscador-caja input {
				flex:1; border:none; outline:none; font-size:13.5px; padding:6px 0; background:transparent;
			}
			.kp-buscador-dropdown {
				position:absolute; top:calc(100% + 6px); left:0; right:0; background:#FFF; border-radius:14px;
				box-shadow:0 16px 40px -12px rgba(15,23,42,.35); border:1px solid #F1F5F9; max-height:360px;
				overflow-y:auto; z-index:40; display:none;
			}
			.kp-buscador-dropdown.abierto { display:block; }
			.kp-buscador-item { display:flex; align-items:center; gap:12px; padding:10px 16px; cursor:pointer; }
			.kp-buscador-item:hover { background:#F8FBFF; }
			.kp-buscador-item + .kp-buscador-item { border-top:1px solid #F5F7FA; }
			.kp-buscador-item .kp-emp-avatar { width:36px; height:36px; font-size:12px; }
			.kp-buscador-item .info { flex:1; min-width:0; }
			.kp-buscador-item .nombre { font-size:12.5px; font-weight:700; color:#0F172A; }
			.kp-buscador-item .cat { font-size:10.5px; color:#94A3B8; }
			.kp-buscador-vacio { padding:18px; text-align:center; font-size:12.5px; color:#94A3B8; }

			.kp-mini-badge { font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; border-radius:20px; padding:2px 9px; white-space:nowrap; }
			.kp-mini-badge.ok { background:#E7F9F1; color:#0E9F6E; }
			.kp-mini-badge.manual { background:#F2ECFC; color:#7C3AED; }
			.kp-mini-badge.sin { background:#FDECEC; color:#C0392B; }

			/* ---------- Ficha de empleado (modal buscador) ---------- */
			.kp-ficha-header { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
			.kp-ficha-header .kp-emp-avatar { width:48px; height:48px; font-size:15px; }
			.kp-ficha-evaluador { font-size:11.5px; color:#64748B; margin-top:4px; }

			.kp-asignar-box { border:1.5px dashed #FDA4AF; background:#FFF5F5; border-radius:12px; padding:14px 16px; margin-bottom:16px; }
			.kp-asignar-box .titulo { font-size:12.5px; font-weight:700; color:#C0392B; margin-bottom:8px; }
			.kp-asignar-box .kp-buscador-wrap { margin-top:0; }
			.kp-asignar-box .kp-buscador-caja { box-shadow:none; border:1.5px solid #E2E8F0; padding:8px 12px; }

			.kp-eval-campos.solo-lectura input, .kp-eval-campos.solo-lectura textarea { background:#F8FAFC; color:#64748B; }

			/* ---------- Importar Excel ---------- */
			.kp-import-info {
				background:#EAF2FC; border:1px solid #CFE2F8; border-radius:12px; padding:14px 18px;
				font-size:12.5px; color:#1E5B9E; margin-top:14px; line-height:1.5;
			}
			#kpImportarArchivo { font-size:13px; padding:6px 0; }
			#kpTablaImportar { font-size:12.5px; width:100%; border-collapse:separate; border-spacing:0; }
			#kpTablaImportar thead th { background:#F8FAFC; color:#475569; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:1.5px solid #E2E8F0; padding:12px 14px; white-space:nowrap; }
			#kpTablaImportar tbody td { padding:9px 14px; border-bottom:1px solid #F1F5F9; color:#1E293B; }
			#kpTablaImportar tbody tr.kp-imp-omitido { background:#FFF5F5; color:#C0392B; }
			.kp-import-resultado-item { padding:4px 0; font-size:12.5px; }

			@media (max-width:767px) {
				.kp-filtro { flex-direction:column; align-items:stretch; }
				.kp-eval-campos { grid-template-columns:1fr; }
			}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Cargando...</div>
			</div>
		</div>

	<?php
		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo_sesion = (strtotime($fa)-strtotime($fg));

		if($periodo_sesion > 600){
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
				titulo("KPIs -- Evaluación de Desempeño");
				$pag="REC_HUMANOS";
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
				$divcontenido->css("overflow-y", "auto");
	?>

	<div class="kp-wrap">

		<div class="kp-hero">
			<p class="kp-hero-titulo">KPIs — Evaluación de Desempeño</p>
			<div class="kp-hero-sub">Evalúa a tu equipo directo por periodo, con base en el catálogo de indicadores.</div>
		</div>

		<div class="kp-buscador-wrap" id="kpBuscadorGlobalWrap">
			<div class="kp-buscador-caja">
				<span class="ico">🔎</span>
				<input type="text" id="kpBuscadorGlobal" placeholder="Buscar empleado por nombre, clave o número de empleado... (ver sus KPIs y avance)" autocomplete="off">
			</div>
			<div class="kp-buscador-dropdown" id="kpBuscadorGlobalDropdown"></div>
		</div>

		<div class="kp-tabs">
			<div class="kp-tab activo" data-tab="evaluados" onclick="cambiarTab('evaluados')">👥 Mis evaluados</div>
			<div class="kp-tab" data-tab="todos-usuarios" id="kpTabTodosUsuarios" onclick="cambiarTab('todos-usuarios')" style="display:none;">🌐 Todos los usuarios</div>
			<div class="kp-tab" data-tab="catalogo" onclick="cambiarTab('catalogo')">🔍 KPIs por empleado</div>
			<div class="kp-tab" data-tab="indicadores" id="kpTabIndicadores" onclick="cambiarTab('indicadores')" style="display:none;">🏷️ Catálogo de indicadores</div>
			<div class="kp-tab" data-tab="asignadores" id="kpTabAsignadores" onclick="cambiarTab('asignadores')" style="display:none;">🗝️ Asignadores de KPIs</div>
		</div>

		<!-- ============ TAB: Mis evaluados ============ -->
		<div id="tab-evaluados">

			<div class="kp-filtro">
				<div class="kp-campo">
					<label>Periodo</label>
					<select id="kpPeriodo" onchange="cargarEvaluados()"></select>
				</div>
				<button class="kp-btn kp-btn-primario" onclick="cargarEvaluados()">🔄 Actualizar</button>
			</div>

			<div class="kp-resumen" id="kpResumen" style="display:none;">
				<div class="kp-card">
					<div class="kp-card-icono azul">👥</div>
					<div><div class="num" id="kpNumEmpleados">0</div><div class="lbl">Colaboradores</div></div>
				</div>
				<div class="kp-card">
					<div class="kp-card-icono verde">✅</div>
					<div><div class="num" id="kpNumCompletos">0</div><div class="lbl">Evaluaciones completas</div></div>
				</div>
				<div class="kp-card">
					<div class="kp-card-icono naranja">⏳</div>
					<div><div class="num" id="kpNumPendientes">0</div><div class="lbl">Pendientes / en progreso</div></div>
				</div>
				<div class="kp-card">
					<div class="kp-card-icono morado">📊</div>
					<div><div class="num" id="kpPromedio">0</div><div class="lbl">Calificación promedio</div></div>
				</div>
			</div>

			<div class="kp-tabla-cont">
				<div class="kp-tabla-scroll">
					<table id="kpTablaEvaluados">
						<thead>
							<tr>
								<th>Empleado</th>
								<th>Categoría</th>
								<th>KPIs capturados</th>
								<th>Desempeño</th>
								<th>Calificación</th>
								<th>Estatus</th>
							</tr>
						</thead>
						<tbody id="kpGridBody">
							<tr><td colspan="6" class="kp-vacio">Cargando...</td></tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>

		<!-- ============ TAB: Todos los usuarios (solo quien puede asignar KPIs) ============
		     A diferencia de "Mis evaluados" (que solo muestra tus subordinados
		     directos, o a ti mismo si no tienes ninguno), esta pestaña lista a
		     TODOS los empleados activos de la empresa, sin filtrar por
		     jerarquía -- necesaria porque el permiso de ASIGNAR KPIs es de rol
		     (puedes asignarle a cualquiera), no de relación jefe/subordinado. -->
		<div id="tab-todos-usuarios" style="display:none;">

			<div class="kp-filtro">
				<div class="kp-campo" style="flex:1;min-width:220px;">
					<label>Buscar</label>
					<input type="text" id="kpBuscarTodosUsuarios" placeholder="Filtrar por nombre, clave o categoría..." oninput="filtrarTodosUsuarios()">
				</div>
				<button class="kp-btn kp-btn-primario" onclick="cargarTodosUsuarios()">🔄 Actualizar</button>
			</div>

			<div class="kp-tabla-cont">
				<div class="kp-tabla-scroll">
					<table id="kpTablaTodosUsuarios">
						<thead>
							<tr>
								<th>Empleado</th>
								<th>Categoría</th>
								<th>KPIs capturados</th>
								<th>Desempeño</th>
								<th>Calificación</th>
								<th>Estatus</th>
							</tr>
						</thead>
						<tbody id="kpTodosUsuariosBody">
							<tr><td colspan="6" class="kp-vacio">Cargando...</td></tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>

		<!-- ============ TAB: Catálogo (por empleado -- los KPIs son únicos de cada quien) ============ -->
		<div id="tab-catalogo" style="display:none;">

			<div class="kp-buscador-wrap" id="kpBuscadorCatalogoWrap">
				<div class="kp-buscador-caja">
					<span class="ico">🔎</span>
					<input type="text" id="kpBuscadorCatalogo" placeholder="Busca al empleado cuyos KPIs quieres administrar..." autocomplete="off">
				</div>
				<div class="kp-buscador-dropdown" id="kpBuscadorCatalogoDropdown"></div>
			</div>

			<div id="kpCatalogoVacio" class="kp-vacio" style="padding:60px 20px;">Busca un empleado arriba para ver y administrar sus KPIs.</div>

			<div id="kpCatalogoContenido" style="display:none;">

				<div class="kp-filtro" style="margin-top:14px;">
					<div style="display:flex; align-items:center; gap:12px; flex:1;">
						<div class="kp-emp-avatar" id="kpCatalogoAvatar">--</div>
						<div>
							<div style="font-size:14px;font-weight:800;color:#0F172A;" id="kpCatalogoNombre"></div>
							<div style="font-size:11.5px;color:#94A3B8;" id="kpCatalogoSub"></div>
						</div>
					</div>
					<button class="kp-btn kp-btn-verde" id="kpBtnNuevoKpi" onclick="abrirModalKpi()">➕ Nuevo KPI</button>
				</div>

				<div id="kpCatalogoAsignarBox" style="display:none;" class="kp-asignar-box">
					<div class="titulo">⚠️ Este empleado no tiene jefe directo capturado ni evaluador asignado. Asígnale uno para poder darle de alta sus KPIs:</div>
					<div class="kp-buscador-wrap" id="kpBuscadorCatalogoEvalWrap">
						<div class="kp-buscador-caja">
							<span class="ico">🔎</span>
							<input type="text" id="kpBuscadorCatalogoEval" placeholder="Buscar al futuro evaluador por nombre o clave..." autocomplete="off">
						</div>
						<div class="kp-buscador-dropdown" id="kpBuscadorCatalogoEvalDropdown"></div>
					</div>
				</div>

				<div id="kpCatalogoErrorBox" style="display:none;" class="kp-vacio"></div>

				<div class="kp-tabla-cont" id="kpCatalogoTablaWrap">
					<div class="kp-tabla-scroll">
						<table id="kpTablaCatalogo">
							<thead>
								<tr>
									<th>Indicador</th>
									<th>Meta</th>
									<th>Tipo</th>
									<th>Ponderación</th>
									<th>Estatus</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="kpCatalogoBody"></tbody>
						</table>
					</div>
					<div class="kp-suma-ponderacion" id="kpSumaPonderacion"></div>
				</div>

			</div>

		</div>

		<!-- ============ TAB: Catálogo MAESTRO de indicadores (solo admin) ============
		     Esto es lo nuevo para que "Captura de folios" no se cree una vez por
		     cada empleado que lo tiene -- aquí se ve el nombre UNA sola vez, con
		     cuántos empleados ya lo usan (cada quien con su propia ponderación,
		     eso se sigue viendo en el Catálogo de KPIs de cada quien). Renombrar
		     aquí actualiza el nombre en todos los KPIs ya asignados que lo usan;
		     desactivar solo lo quita del buscador para KPIs nuevos, no toca los
		     que ya existen. -->
		<div id="tab-indicadores" style="display:none;">

			<div class="kp-filtro">
				<div class="kp-campo" style="flex:1;">
					<label>Buscar</label>
					<input type="text" id="kpIndBuscar" placeholder="Filtrar por nombre..." oninput="filtrarCatalogoIndicadores()">
				</div>
				<button class="kp-btn kp-btn-verde" onclick="abrirModalIndicador()">➕ Nuevo indicador</button>
			</div>

			<div class="kp-tabla-cont">
				<div class="kp-tabla-scroll">
					<table id="kpTablaIndicadores">
						<thead>
							<tr>
								<th>Indicador</th>
								<th>Tipo de meta</th>
								<th>Empleados que lo tienen</th>
								<th>Estatus</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="kpIndicadoresBody"></tbody>
					</table>
				</div>
			</div>

		</div>

		<!-- ============ TAB: Asignadores de KPIs (solo admin) ============
		     Lista blanca de quién, además de ADMINISTRA, puede asignar
		     (crear/editar/activar) KPIs a CUALQUIER empleado -- ya no
		     depende de si es su jefe directo. Calificar (guardar
		     resultados) sigue siendo exclusivo del jefe directo, eso no
		     se toca aquí. -->
		<div id="tab-asignadores" style="display:none;">

			<div style="margin-bottom:14px; font-size:12.5px; color:#64748B;">
				Las personas de esta lista (además del administrador) pueden asignar KPIs a cualquier empleado, sin importar si son su jefe directo. Esto NO les da permiso para calificar -- calificar sigue siendo solo del jefe directo de cada quien.
			</div>

			<div class="kp-buscador-wrap" id="kpBuscadorAsignadorWrap">
				<div class="kp-buscador-caja">
					<span class="ico">🔎</span>
					<input type="text" id="kpBuscadorAsignador" placeholder="Busca al empleado que quieres autorizar a asignar KPIs..." autocomplete="off">
				</div>
				<div class="kp-buscador-dropdown" id="kpBuscadorAsignadorDropdown"></div>
			</div>

			<div class="kp-tabla-cont" style="margin-top:16px;">
				<div class="kp-tabla-scroll">
					<table id="kpTablaAsignadores">
						<thead>
							<tr>
								<th>Empleado</th>
								<th>Clave</th>
								<th>Autorizado por</th>
								<th>Desde</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="kpAsignadoresBody"></tbody>
					</table>
				</div>
			</div>

		</div>

	</div>

	<!-- ============ Modal: alta/edición de indicador maestro ============ -->
	<div id="kpModalIndicador" class="kp-modal-overlay" onclick="if(event.target===this) cerrarModalIndicador()">
		<div class="kp-modal-box" style="max-width:480px;">
			<div class="kp-modal-header">
				<h5 id="kpIndTitulo">Nuevo indicador</h5>
				<button type="button" class="kp-modal-close" onclick="cerrarModalIndicador()">&times;</button>
			</div>
			<div class="kp-modal-body">
				<input type="hidden" id="kpIndId">
				<div style="margin-bottom:12px;">
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Nombre</label>
					<input type="text" id="kpIndNombre" class="form-control" placeholder="Ej. Captura de folios">
				</div>
				<div style="margin-bottom:12px;">
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Descripción</label>
					<textarea id="kpIndDescripcion" class="form-control" rows="2" placeholder="Opcional"></textarea>
				</div>
				<div>
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Tipo de meta por default</label>
					<select id="kpIndTipoMeta" class="form-control">
						<option value="porcentaje">Porcentaje</option>
						<option value="numerica">Numérica</option>
						<option value="cumplido_no_cumplido">Cumplido / No cumplido</option>
					</select>
				</div>
			</div>
			<div class="kp-modal-footer" style="justify-content:flex-end;">
				<button class="kp-btn kp-btn-primario" onclick="guardarIndicadorMaestroModal()">💾 Guardar</button>
			</div>
		</div>
	</div>

	<!-- ============ Modal: evaluación de un empleado ============ -->
	<div id="kpModalEval" class="kp-modal-overlay" onclick="if(event.target===this) cerrarModalEval()">
		<div class="kp-modal-box">
			<div class="kp-modal-header">
				<div>
					<h5 id="kpEvalNombre">Evaluación</h5>
					<div class="sub" id="kpEvalSub"></div>
				</div>
				<button type="button" class="kp-modal-close" onclick="cerrarModalEval()">&times;</button>
			</div>
			<div class="kp-modal-body" id="kpEvalBody"></div>
			<div class="kp-modal-footer">
				<div class="kp-total-box">
					<div>
						<div class="num" id="kpEvalTotal">0</div>
						<div class="lbl">Calificación total</div>
					</div>
				</div>
				<button class="kp-btn kp-btn-primario" onclick="guardarEvaluacion()">💾 Guardar evaluación</button>
			</div>
		</div>
	</div>

	<!-- ============ Modal: alta/edición de KPI ============ -->
	<div id="kpModalKpi" class="kp-modal-overlay" onclick="if(event.target===this) cerrarModalKpi()">
		<div class="kp-modal-box" style="max-width:520px;">
			<div class="kp-modal-header">
				<h5 id="kpKpiTitulo">Nuevo KPI</h5>
				<button type="button" class="kp-modal-close" onclick="cerrarModalKpi()">&times;</button>
			</div>
			<div class="kp-modal-body">
				<input type="hidden" id="kpKpiId">
				<div style="margin-bottom:12px;">
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Indicador</label>
					<div style="position:relative;">
						<input type="text" id="kpKpiIndicador" class="form-control" placeholder="Ej. Cumplimiento de meta de traspasos" autocomplete="off">
						<div class="kp-buscador-dropdown" id="kpKpiIndicadorDropdown"></div>
					</div>
				</div>
				<div style="margin-bottom:12px;">
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Descripción</label>
					<textarea id="kpKpiDescripcion" class="form-control" rows="2" placeholder="Opcional"></textarea>
				</div>
				<div class="row">
					<div class="col-6" style="margin-bottom:12px;">
						<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Ponderación (%)</label>
						<input type="number" id="kpKpiPonderacion" class="form-control" min="0" max="100" step="0.01">
					</div>
					<div class="col-6" style="margin-bottom:12px;">
						<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Tipo de meta</label>
						<select id="kpKpiTipoMeta" class="form-control">
							<option value="porcentaje">Porcentaje</option>
							<option value="numerica">Numérica</option>
							<option value="cumplido_no_cumplido">Cumplido / No cumplido</option>
						</select>
					</div>
				</div>
				<div>
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;">Meta</label>
					<input type="text" id="kpKpiMeta" class="form-control" placeholder="Ej. 95%, 10 traspasos, Cumplido">
				</div>
			</div>
			<div class="kp-modal-footer" style="justify-content:flex-end;">
				<button class="kp-btn kp-btn-primario" onclick="guardarKpi()">💾 Guardar</button>
			</div>
		</div>
	</div>

	<!-- ============ Modal: ficha de empleado (buscador) ============ -->
	<div id="kpModalFicha" class="kp-modal-overlay" onclick="if(event.target===this) cerrarModalFicha()">
		<div class="kp-modal-box">
			<div class="kp-modal-header">
				<div class="kp-ficha-header">
					<div class="kp-emp-avatar" id="kpFichaAvatar">--</div>
					<div>
						<h5 id="kpFichaNombre">Ficha de empleado</h5>
						<div class="sub" id="kpFichaSub"></div>
						<div class="kp-ficha-evaluador" id="kpFichaEvaluador"></div>
					</div>
				</div>
				<button type="button" class="kp-modal-close" onclick="cerrarModalFicha()">&times;</button>
			</div>
			<div class="kp-modal-body">
				<div id="kpFichaAsignarBox" style="display:none;" class="kp-asignar-box">
					<div class="titulo">⚠️ Este empleado no tiene jefe directo capturado ni evaluador asignado. Asígnale uno manualmente:</div>
					<div class="kp-buscador-wrap" id="kpBuscadorEvalWrap">
						<div class="kp-buscador-caja">
							<span class="ico">🔎</span>
							<input type="text" id="kpBuscadorEval" placeholder="Buscar al futuro evaluador por nombre o clave..." autocomplete="off">
						</div>
						<div class="kp-buscador-dropdown" id="kpBuscadorEvalDropdown"></div>
					</div>
				</div>
				<div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
					<label style="font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;margin:0;">Periodo:</label>
					<select id="kpFichaPeriodo" style="height:34px;font-size:12.5px;border-radius:8px;border:1.5px solid #E2E8F0;padding:0 10px;"></select>
				</div>
				<div id="kpFichaBody"></div>
			</div>
			<div class="kp-modal-footer">
				<div class="kp-total-box">
					<div>
						<div class="num" id="kpFichaTotal">0</div>
						<div class="lbl">Calificación total</div>
					</div>
				</div>
				<button class="kp-btn kp-btn-primario" id="kpFichaBtnGuardar" style="display:none;" onclick="guardarFichaEvaluacion()">💾 Guardar evaluación</button>
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

	const API = 'kpis_api.php';

	async function api(payload) {
		let resp;
		try {
			resp = await fetch(API, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});
		} catch (e) {
			return { ok: false, msg: 'No se pudo conectar con el servidor (kpis_api.php). Revisa tu conexión.' };
		}

		const texto = await resp.text();
		try {
			return JSON.parse(texto);
		} catch (e) {
			/* El servidor no devolvió JSON válido -- casi siempre es un error
			   fatal de PHP (tabla/columna que no existe, etc.) en vez de la
			   respuesta esperada. Se muestra el inicio de lo que sí llegó para
			   poder diagnosticarlo, en vez de fallar en silencio. */
			console.error('Respuesta no-JSON de kpis_api.php:', texto);
			return {
				ok: false,
				msg: 'El servidor respondió algo inesperado (no fue JSON). Puede ser un error de PHP/SQL en kpis_api.php -- revisa la consola del navegador (F12) para ver el detalle: ' + texto.slice(0, 200)
			};
		}
	}

	function mostrarLoader() { document.getElementById('loader').style.display = 'flex'; }
	function ocultarLoader() { document.getElementById('loader').style.display = 'none'; }

	function escaparHtml(txt) {
		const div = document.createElement('div');
		div.textContent = txt == null ? '' : String(txt);
		return div.innerHTML;
	}

	/* ════════════════════════════════════════
	   Tabs
	════════════════════════════════════════ */
	function cambiarTab(tab) {
		document.querySelectorAll('.kp-tab').forEach(t => t.classList.toggle('activo', t.dataset.tab === tab));
		document.getElementById('tab-evaluados').style.display      = tab === 'evaluados'      ? 'block' : 'none';
		document.getElementById('tab-todos-usuarios').style.display = tab === 'todos-usuarios' ? 'block' : 'none';
		document.getElementById('tab-catalogo').style.display       = tab === 'catalogo'       ? 'block' : 'none';
		document.getElementById('tab-indicadores').style.display    = tab === 'indicadores'    ? 'block' : 'none';
		document.getElementById('tab-asignadores').style.display    = tab === 'asignadores'    ? 'block' : 'none';
		if (tab === 'todos-usuarios') cargarTodosUsuarios();
		if (tab === 'catalogo' && claveCatalogoActual) cargarCatalogo();
		if (tab === 'indicadores') cargarCatalogoIndicadores();
		if (tab === 'asignadores') cargarAsignadoresKpi();
	}

	/* ════════════════════════════════════════
	   Selector de periodo: últimos 12 meses + actual
	════════════════════════════════════════ */
	function llenarSelectorPeriodo(sel) {
		const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
		const hoy = new Date();
		sel.innerHTML = '';

		for (let i = 0; i < 12; i++) {
			const d = new Date(hoy.getFullYear(), hoy.getMonth() - i, 1);
			const yyyy = d.getFullYear();
			const mm = String(d.getMonth() + 1).padStart(2, '0');
			const valor = `${yyyy}-${mm}`;
			const opt = document.createElement('option');
			opt.value = valor;
			opt.textContent = `${meses[d.getMonth()]} ${yyyy}`;
			sel.appendChild(opt);
		}
	}

	function llenarSelectorPeriodos() {
		llenarSelectorPeriodo(document.getElementById('kpPeriodo'));
	}

	/* ════════════════════════════════════════
	   Mis evaluados (o "Todos los empleados" si eres administrador
	   nivel 12 -- puedes calificar a cualquiera, no solo a tus directos)
	════════════════════════════════════════ */
	let esAdminGlobal = false;
	let miClaveGlobal = null;
	let puedeAsignarGlobal = false; // rol: admin o asignador autorizado -- puede asignar KPIs a cualquiera

	async function cargarEvaluados() {
		const periodo = document.getElementById('kpPeriodo').value;
		const grid = document.getElementById('kpGridBody');
		grid.innerHTML = '<tr><td colspan="6" class="kp-vacio">Cargando...</td></tr>';

		try {
			const resp = await api({ action: 'getMisEvaluados', periodo });

			if (!resp.ok) {
				grid.innerHTML = `<tr><td colspan="6" class="kp-vacio">${escaparHtml(resp.msg || 'Error al cargar.')}</td></tr>`;
				document.getElementById('kpResumen').style.display = 'none';
				return;
			}

			esAdminGlobal = !!resp.es_admin;
			miClaveGlobal = resp.mi_clave || null;
			puedeAsignarGlobal = !!resp.puede_asignar;
			const tabEvaluadosLabel = document.querySelector('.kp-tab[data-tab="evaluados"]');
			if (tabEvaluadosLabel) tabEvaluadosLabel.innerHTML = esAdminGlobal ? '👥 Todos los empleados (admin)' : '👥 Mis evaluados';
			/* El catálogo de indicadores es justo lo que se usa al ASIGNAR
			   KPIs -- mismo permiso que asignar (admin o asignador
			   autorizado), ya no exclusivo del admin. */
			document.getElementById('kpTabIndicadores').style.display = puedeAsignarGlobal ? 'block' : 'none';
			/* "Todos los usuarios": quien puede asignar KPIs puede hacerlo
			   a CUALQUIER empleado, no solo a sus subordinados -- esta
			   pestaña es la única forma de ver/elegir a alguien que no
			   sea tuyo directo para darle de alta su catálogo de KPIs. */
			document.getElementById('kpTabTodosUsuarios').style.display = puedeAsignarGlobal ? 'block' : 'none';
			/* La lista de "quién puede asignar" solo la administra el admin
			   -- ser asignador autorizado no te da acceso a esta pestaña,
			   solo te deja asignar KPIs desde las pantallas normales. */
			document.getElementById('kpTabAsignadores').style.display = esAdminGlobal ? 'block' : 'none';

			renderEvaluados(resp.datos);

		} catch (e) {
			grid.innerHTML = '<tr><td colspan="6" class="kp-vacio">Error de comunicación con el servidor.</td></tr>';
		}
	}

	/* Color de la barra de desempeño según qué tan cerca está del 100%:
	   rojo = va mal, amarillo = a medias, verde = bien. Mismos cortes
	   que ya se usan en otros módulos del sitio para semáforos. */
	function colorDesempeno(pct) {
		if (pct >= 80) return '#0E9F6E'; // verde
		if (pct >= 50) return '#D97706'; // amarillo/naranja
		return '#C0392B';                // rojo
	}

	function renderEvaluados(datos) {
		const grid = document.getElementById('kpGridBody');

		if (!datos.length) {
			grid.innerHTML = `<tr><td colspan="6" class="kp-vacio">${esAdminGlobal ? 'No hay empleados activos registrados.' : 'No tienes colaboradores asignados como jefe directo.'}</td></tr>`;
			document.getElementById('kpResumen').style.display = 'none';
			return;
		}

		document.getElementById('kpResumen').style.display = 'grid';
		document.getElementById('kpNumEmpleados').textContent = datos.length;
		document.getElementById('kpNumCompletos').textContent = datos.filter(d => d.estatus === 'completo').length;
		document.getElementById('kpNumPendientes').textContent = datos.filter(d => d.estatus === 'en_progreso' || d.estatus === 'pendiente').length;

		const conCalificacion = datos.filter(d => d.evaluados > 0);
		const promedio = conCalificacion.length
			? conCalificacion.reduce((s, d) => s + (parseFloat(d.calificacion) || 0), 0) / conCalificacion.length
			: 0;
		document.getElementById('kpPromedio').textContent = promedio.toFixed(1);

		grid.innerHTML = datos.map(filaEmpleadoKpiHtml).join('');
	}

	const KP_BADGE_LABEL = { completo: 'Completo', en_progreso: 'En progreso', pendiente: 'Pendiente', sin_kpis: 'Sin KPIs asignados' };

	/* Fila de la tabla (Empleado / Categoría / KPIs capturados / Desempeño /
	   Calificación / Estatus). Compartida entre "Mis evaluados" y "Todos los
	   usuarios" -- ambas tablas tienen exactamente las mismas columnas y el
	   mismo criterio de a dónde manda el clic, así que vivía duplicado no
	   tenía caso. */
	function filaEmpleadoKpiHtml(d) {
		/* El % pinta el DESEMPEÑO (calificación lograda contra el máximo
		   posible de sus KPIs), no cuántos KPIs ya se llenaron -- si no,
		   un empleado con 1 solo KPI capturado al 60% se veía en "100%"
		   nada más porque ya no le faltaba nada por capturar. */
		const pct = d.porcentaje_desempeno ?? d.porcentaje_avance ?? 0;
		const pctBarra = Math.min(pct, 100);
		const colorBarra = colorDesempeno(pct);

		/* A partir del cambio de permisos, calificar ya NO es "admin o
		   jefe" -- es SOLO el jefe directo/evaluador efectivo (ni
		   siquiera el admin). d.puede_calificar ya viene calculado del
		   backend con esa regla. Quien no puede calificar (incluido el
		   admin viendo a alguien que no es su subordinado directo, o tu
		   propio renglón) abre la ficha de solo lectura en vez del modal
		   de evaluación -- si no, el guardarEvaluacion del modal te
		   rechazaría con "solo el jefe directo puede calificarlo". Esto
		   aplica igual en "Todos los usuarios": ver la lista completa no
		   te da permiso de calificar a nadie que no sea tuyo. */
		const onclickAttr = d.puede_calificar
			? `abrirModalEval('${d.clave}', '${escaparHtml(d.nombre)}')`
			: `abrirFicha('${d.clave}')`;

		return `
			<tr onclick="${onclickAttr}">
				<td>
					<div class="kp-emp-celda">
						<div class="kp-emp-avatar" style="width:36px;height:36px;font-size:12px;">${escaparHtml(d.iniciales)}</div>
						<div class="kp-emp-nombre">${escaparHtml(d.nombre)}${d.es_uno_mismo ? ' <span class="kp-mini-badge ok">Tú</span>' : ''}</div>
					</div>
				</td>
				<td>${escaparHtml(d.categoria || 'Sin categoría')}</td>
				<td>${d.evaluados}/${d.total_kpis}</td>
				<td>
					<span class="kp-barra-cont"><span class="kp-barra-fill" style="width:${pctBarra}%; background:${colorBarra};"></span></span>
					<span class="kp-barra-txt">${pct}%</span>
				</td>
				<td>
					<span class="kp-score-num">${(parseFloat(d.calificacion) || 0).toFixed(1)}</span>
					<span class="kp-score-lbl"> de ${(parseFloat(d.suma_ponderacion) || 100).toFixed(0)}</span>
				</td>
				<td><span class="kp-badge kp-badge-${d.estatus}">${KP_BADGE_LABEL[d.estatus] || d.estatus}</span></td>
			</tr>
		`;
	}

	/* ════════════════════════════════════════
	   Todos los usuarios (solo quien puede asignar KPIs) -- lista
	   completa de la empresa, sin filtrar por jerarquía, para poder
	   elegir a cualquiera y asignarle KPIs aunque no sea tu subordinado.
	════════════════════════════════════════ */
	let todosUsuariosCache = [];

	async function cargarTodosUsuarios() {
		const periodo = document.getElementById('kpPeriodo').value;
		const body = document.getElementById('kpTodosUsuariosBody');
		body.innerHTML = '<tr><td colspan="6" class="kp-vacio">Cargando...</td></tr>';

		try {
			const resp = await api({ action: 'getTodosUsuariosKpi', periodo });

			if (!resp.ok) {
				body.innerHTML = `<tr><td colspan="6" class="kp-vacio">${escaparHtml(resp.msg || 'Error al cargar.')}</td></tr>`;
				todosUsuariosCache = [];
				return;
			}

			todosUsuariosCache = resp.datos || [];
			filtrarTodosUsuarios();

		} catch (e) {
			body.innerHTML = '<tr><td colspan="6" class="kp-vacio">Error de comunicación con el servidor.</td></tr>';
			todosUsuariosCache = [];
		}
	}

	/* Filtro 100% client-side: ya se trajo la lista completa de una sola
	   vez, no hace falta volver a pedir al servidor por cada tecleo. */
	function filtrarTodosUsuarios() {
		const q = (document.getElementById('kpBuscarTodosUsuarios').value || '').trim().toLowerCase();
		const datos = q
			? todosUsuariosCache.filter(d =>
				(d.nombre || '').toLowerCase().includes(q) ||
				(d.clave || '').toLowerCase().includes(q) ||
				(d.categoria || '').toLowerCase().includes(q))
			: todosUsuariosCache;

		const body = document.getElementById('kpTodosUsuariosBody');

		if (!datos.length) {
			body.innerHTML = `<tr><td colspan="6" class="kp-vacio">${q ? `Sin resultados para "${escaparHtml(q)}"` : 'No hay empleados activos registrados.'}</td></tr>`;
			return;
		}

		body.innerHTML = datos.map(filaEmpleadoKpiHtml).join('');
	}

	/* ════════════════════════════════════════
	   Modal de evaluación
	════════════════════════════════════════ */
	let claveEvalActual = null;

	async function abrirModalEval(clave, nombre) {
		claveEvalActual = clave;
		document.getElementById('kpEvalNombre').textContent = '📝 Evaluando a ' + nombre;
		document.getElementById('kpEvalSub').textContent = 'Periodo: ' + document.getElementById('kpPeriodo').value;
		document.getElementById('kpModalEval').classList.add('abierto');
		await cargarFormularioEval();
	}

	async function cargarFormularioEval() {
		if (!claveEvalActual) return;

		document.getElementById('kpEvalBody').innerHTML = '<div style="text-align:center;padding:20px;color:#94A3B8;">Cargando indicadores...</div>';

		const periodo = document.getElementById('kpPeriodo').value;

		try {
			const resp = await api({ action: 'getEvaluacionEmpleado', clave_empleado: claveEvalActual, periodo });

			if (!resp.ok) {
				document.getElementById('kpEvalBody').innerHTML = `<div style="text-align:center;padding:20px;color:#C0392B;">${escaparHtml(resp.msg)}</div>`;
				return;
			}

			renderFormularioEval(resp.kpis);

		} catch (e) {
			document.getElementById('kpEvalBody').innerHTML = '<div style="text-align:center;padding:20px;color:#C0392B;">Error de comunicación con el servidor.</div>';
		}
	}

	/* Mini-formulario para dar de alta un KPI sin salir del modal de
	   evaluación -- sobre todo pensado para cuando abres a alguien que
	   todavía no tiene NINGÚN KPI: antes se quedaba muerto en "No hay
	   KPIs activos", ahora se le puede asignar ahí mismo y de inmediato
	   queda listo para capturarle el cumplimiento. */
	function htmlAgregarKpiInline() {
		return `
			<div class="kp-asignar-box" style="border-style:solid; border-color:#CFE2F8; background:#EAF2FC;">
				<div class="titulo" style="color:#1E5B9E;">➕ Agregar un KPI a este empleado</div>
				<div class="kp-eval-campos" style="grid-template-columns:2fr 1fr 1fr;">
					<div style="position:relative;">
						<label>Indicador</label>
						<input type="text" id="kpNuevoIndicador" placeholder="Ej. Cumplimiento de meta mensual" autocomplete="off">
						<div class="kp-buscador-dropdown" id="kpNuevoIndicadorDropdown"></div>
					</div>
					<div>
						<label>Ponderación (%)</label>
						<input type="number" id="kpNuevoPonderacion" min="0" max="100" step="0.01">
					</div>
					<div>
						<label>Meta</label>
						<input type="text" id="kpNuevoMeta" placeholder="Ej. 100%">
					</div>
				</div>
				<div style="margin-top:10px; text-align:right;">
					<button type="button" class="kp-btn kp-btn-primario" onclick="agregarKpiInlineEval()">➕ Agregar</button>
				</div>
			</div>
		`;
	}

	/* El mini-formulario de arriba se inserta de cero (innerHTML) cada
	   vez que se abre/recarga el modal, así que el buscador hay que
	   volver a engancharlo cada vez -- llamar justo después de meter el
	   HTML en el DOM. */
	function initBuscadorIndicadorInline() {
		iniciarBuscadorIndicador('kpNuevoIndicador', 'kpNuevoIndicadorDropdown', (ind) => {
			document.getElementById('kpNuevoIndicador').value = ind.nombre;
		});
	}

	async function agregarKpiInlineEval() {
		if (!claveEvalActual) return;

		const indicador = document.getElementById('kpNuevoIndicador').value.trim();
		const ponderacion = document.getElementById('kpNuevoPonderacion').value;
		const meta = document.getElementById('kpNuevoMeta').value.trim();

		if (!indicador || !ponderacion || !meta) {
			Swal.fire('Faltan datos', 'Indicador, ponderación y meta son obligatorios.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarKpi',
				id: 0,
				clave_empleado: claveEvalActual,
				indicador,
				descripcion: '',
				ponderacion,
				tipo_meta: 'porcentaje',
				meta
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo agregar el KPI.', 'error');
				return;
			}

			Swal.fire({ icon: 'success', title: 'KPI agregado', text: 'La lista se recargó -- si ya habías escrito algún % de cumplimiento sin guardar, captúralo de nuevo.', timer: 2200, showConfirmButton: false });
			await cargarFormularioEval();
			cargarEvaluados();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	function renderFormularioEval(kpis) {
		const body = document.getElementById('kpEvalBody');

		/* "Agregar KPI" aquí es la acción ASIGNAR (guardarKpi), que ya es
		   un permiso de rol (admin o asignador autorizado) y ya NO depende
		   de ser jefe directo -- este modal se abre justo porque SÍ eres
		   jefe directo (para calificar), pero eso no te hace asignador. Si
		   no estás en la lista de asignadores, no se ofrece la opción aquí
		   (el backend igual la rechazaría si se intentara). */
		if (!kpis.length) {
			body.innerHTML = '<div style="text-align:center;padding:14px 0 4px;color:#94A3B8;">Este empleado todavía no tiene KPIs asignados.</div>'
				+ (puedeAsignarGlobal ? htmlAgregarKpiInline() : '<div style="text-align:center;padding:4px 0 10px;color:#94A3B8;font-size:12px;">Pídele a un asignador autorizado que le dé de alta sus KPIs.</div>');
			if (puedeAsignarGlobal) initBuscadorIndicadorInline();
			return;
		}

		body.innerHTML = kpis.map(k => {
			const cumplimiento = k.cumplimiento ?? '';
			const comentarios = k.comentarios ?? '';
			const calif = k.calificacion != null ? parseFloat(k.calificacion).toFixed(2) : '0.00';

			return `
				<div class="kp-eval-item" data-id-kpi="${k.id_kpi}" data-ponderacion="${k.ponderacion}">
					<div class="kp-eval-top">
						<div>
							<div class="kp-eval-nombre">${escaparHtml(k.indicador)}</div>
							<div class="kp-eval-meta">Meta: ${escaparHtml(k.meta)}</div>
						</div>
						<span class="kp-eval-pond">${parseFloat(k.ponderacion).toFixed(1)}%</span>
					</div>
					<div class="kp-eval-campos">
						<div>
							<label>% cumplimiento</label>
							<input type="number" class="kp-inp-cumplimiento" min="0" max="200" step="0.1" value="${cumplimiento}" oninput="recalcularFila(this)">
						</div>
						<div class="kp-eval-calif">
							<label>Calificación</label>
							<div class="valor kp-out-calif">${calif}</div>
						</div>
					</div>
					<div style="margin-top:8px;">
						<label style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;">Comentarios</label>
						<textarea class="kp-inp-comentarios" rows="1" style="width:100%;border:1.5px solid #E2E8F0;border-radius:8px;padding:6px 10px;font-size:12px;">${escaparHtml(comentarios)}</textarea>
					</div>
				</div>
			`;
		}).join('');

		if (puedeAsignarGlobal) {
			body.insertAdjacentHTML('beforeend', `
				<div style="text-align:center; margin-top:6px;" id="kpTogglerAgregarKpi">
					<button type="button" class="kp-btn" style="background:#F1F5F9;color:#475569;" onclick="mostrarAgregarKpiInline()">➕ Agregar otro KPI</button>
				</div>
				<div id="kpAgregarKpiWrap" style="display:none; margin-top:12px;">${htmlAgregarKpiInline()}</div>
			`);
			initBuscadorIndicadorInline();
		}

		recalcularTotal();
	}

	function mostrarAgregarKpiInline() {
		document.getElementById('kpTogglerAgregarKpi').style.display = 'none';
		document.getElementById('kpAgregarKpiWrap').style.display = 'block';
	}

	function recalcularFila(input) {
		const item = input.closest('.kp-eval-item');
		const ponderacion = parseFloat(item.dataset.ponderacion) || 0;
		const cumplimiento = parseFloat(input.value) || 0;
		const calif = (ponderacion * cumplimiento / 100);
		item.querySelector('.kp-out-calif').textContent = calif.toFixed(2);
		recalcularTotal();
	}

	function recalcularTotal() {
		let total = 0;
		document.querySelectorAll('#kpEvalBody .kp-eval-item').forEach(item => {
			total += parseFloat(item.querySelector('.kp-out-calif').textContent) || 0;
		});
		document.getElementById('kpEvalTotal').textContent = total.toFixed(1);
	}

	function cerrarModalEval() {
		document.getElementById('kpModalEval').classList.remove('abierto');
		claveEvalActual = null;
	}

	async function guardarEvaluacion() {
		if (!claveEvalActual) return;

		const evaluaciones = [];
		document.querySelectorAll('#kpEvalBody .kp-eval-item').forEach(item => {
			const cumplimiento = item.querySelector('.kp-inp-cumplimiento').value;
			if (cumplimiento === '') return; // sin % capturado, no se manda esa fila

			evaluaciones.push({
				id_kpi: parseInt(item.dataset.idKpi, 10),
				resultado: '',
				cumplimiento: parseFloat(cumplimiento),
				comentarios: item.querySelector('.kp-inp-comentarios').value
			});
		});

		if (!evaluaciones.length) {
			Swal.fire('Nada que guardar', 'Captura al menos un % de cumplimiento.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarEvaluacion',
				clave_empleado: claveEvalActual,
				periodo: document.getElementById('kpPeriodo').value,
				evaluaciones
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
				return;
			}

			Swal.fire({ icon: 'success', title: 'Evaluación guardada', timer: 1200, showConfirmButton: false });
			cerrarModalEval();
			cargarEvaluados();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Buscador en tiempo real (genérico, reutilizable)
	════════════════════════════════════════ */
	function iniciales(nombre) {
		nombre = (nombre || '').trim();
		if (!nombre) return '?';
		const partes = nombre.split(/\s+/);
		const ini = (partes[0]?.[0] || '') + (partes[1]?.[0] || '');
		return (ini || nombre[0] || '?').toUpperCase();
	}

	function badgeEvaluador(item) {
		if (item.tiene_evaluador) {
			const fuente = item.evaluador.fuente === 'manual' ? 'manual' : 'ok';
			const texto = item.evaluador.fuente === 'manual' ? 'Evaluador manual' : 'Con jefe directo';
			return `<span class="kp-mini-badge ${fuente}">${texto}</span>`;
		}
		return '<span class="kp-mini-badge sin">Sin evaluador</span>';
	}

	function iniciarBuscador(inputId, dropdownId, onSeleccionar, action = 'buscarEmpleados') {
		const input = document.getElementById(inputId);
		const dropdown = document.getElementById(dropdownId);
		let timer = null;

		input.addEventListener('input', () => {
			clearTimeout(timer);
			const termino = input.value.trim();

			if (termino.length < 2) {
				dropdown.classList.remove('abierto');
				dropdown.innerHTML = '';
				return;
			}

			timer = setTimeout(async () => {
				dropdown.innerHTML = '<div class="kp-buscador-vacio">Buscando...</div>';
				dropdown.classList.add('abierto');

				/* 'action' puede ser un string fijo o una función que se
				   evalúa en cada búsqueda -- necesario para el buscador
				   global, cuya acción depende de un permiso que todavía no
				   se conoce en el momento en que se registra el buscador
				   (puedeAsignarGlobal se resuelve después, al cargar
				   "Mis evaluados"). */
				const actionResuelta = typeof action === 'function' ? action() : action;
				const resp = await api({ action: actionResuelta, termino });

				if (!resp.ok) {
					dropdown.innerHTML = `<div class="kp-buscador-vacio" style="color:#C0392B;">${escaparHtml(resp.msg || 'No se pudo buscar.')}</div>`;
					return;
				}

				renderDropdownBuscador(dropdown, resp.datos, onSeleccionar);
			}, 250);
		});

		document.addEventListener('click', (e) => {
			if (!input.contains(e.target) && !dropdown.contains(e.target)) {
				dropdown.classList.remove('abierto');
			}
		});
	}

	function renderDropdownBuscador(dropdown, datos, onSeleccionar) {
		if (!datos.length) {
			dropdown.innerHTML = '<div class="kp-buscador-vacio">Sin resultados.</div>';
			dropdown.classList.add('abierto');
			return;
		}

		dropdown.innerHTML = datos.map((d, idx) => `
			<div class="kp-buscador-item" data-idx="${idx}">
				<div class="kp-emp-avatar">${escaparHtml(iniciales(d.nombre))}</div>
				<div class="info">
					<div class="nombre">${escaparHtml(d.nombre)}</div>
					<div class="cat">${escaparHtml(d.categoria || 'Sin categoría')}</div>
				</div>
				${badgeEvaluador(d)}
			</div>
		`).join('');

		dropdown.querySelectorAll('.kp-buscador-item').forEach(el => {
			el.addEventListener('click', () => onSeleccionar(datos[parseInt(el.dataset.idx, 10)]));
		});

		dropdown.classList.add('abierto');
	}

	/* ════════════════════════════════════════
	   Buscador del catálogo MAESTRO de indicadores -- se usa en las 3
	   pantallas donde se puede escribir un "Indicador" nuevo (modal
	   Nuevo/Editar KPI, y los dos mini-formularios "Agregar KPI" de
	   evaluación/ficha). Sugiere nombres ya existentes mientras se
	   escribe, para que no se cree "Captura de folios" otra vez si ya
	   existe -- el backend igual dedupe si de todos modos se escribe
	   uno nuevo parecido, esto es solo para ayudar a encontrarlo antes.
	   A diferencia de iniciarBuscador() (que es para empleados), estos
	   campos son inputs normales que se pueden seguir editando después
	   de elegir una sugerencia -- no obligan a escoger una opción.
	════════════════════════════════════════ */
	function iniciarBuscadorIndicador(inputId, dropdownId, onSeleccionar) {
		const input = document.getElementById(inputId);
		const dropdown = document.getElementById(dropdownId);
		if (!input || !dropdown) return;
		let timer = null;

		input.addEventListener('input', () => {
			clearTimeout(timer);
			const termino = input.value.trim();

			if (termino.length < 2) {
				dropdown.classList.remove('abierto');
				dropdown.innerHTML = '';
				return;
			}

			timer = setTimeout(async () => {
				const resp = await api({ action: 'buscarIndicadores', termino });
				if (!resp.ok || !resp.datos || !resp.datos.length) {
					dropdown.classList.remove('abierto');
					dropdown.innerHTML = '';
					return;
				}

				dropdown.innerHTML = resp.datos.map((d, idx) => `
					<div class="kp-buscador-item" data-idx="${idx}">
						<div class="info">
							<div class="nombre">${escaparHtml(d.nombre)}</div>
						</div>
						<span class="kp-mini-badge ${d.empleados_usando > 0 ? 'ok' : 'sin'}">${d.empleados_usando > 0 ? d.empleados_usando + ' ya lo tienen' : 'nuevo'}</span>
					</div>
				`).join('');

				dropdown.querySelectorAll('.kp-buscador-item').forEach(el => {
					el.addEventListener('click', () => {
						dropdown.classList.remove('abierto');
						onSeleccionar(resp.datos[parseInt(el.dataset.idx, 10)]);
					});
				});

				dropdown.classList.add('abierto');
			}, 250);
		});

		document.addEventListener('click', (e) => {
			if (!input.contains(e.target) && !dropdown.contains(e.target)) {
				dropdown.classList.remove('abierto');
			}
		});
	}

	/* ---- instancia: buscador global de la vista ----
	   Por default acotado a 'buscarMisSubordinados' (un jefe directo
	   normal solo debe encontrar aquí a sus propios subordinados). PERO
	   si el usuario puede ASIGNAR KPIs (admin o asignador autorizado),
	   ese permiso es de rol y aplica a CUALQUIER empleado -- así que
	   aquí también debe poder buscar/ver a cualquiera, no solo a sus
	   subordinados (era el bug grande reportado: un asignador que no es
	   jefe de nadie no tenía forma de encontrar a los demás desde aquí).
	   Se resuelve con una función (no un string fijo) porque
	   puedeAsignarGlobal todavía no se conoce en este punto del arranque
	   de la página -- se llena después, al cargar "Mis evaluados". */
	iniciarBuscador('kpBuscadorGlobal', 'kpBuscadorGlobalDropdown', (empleado) => {
		document.getElementById('kpBuscadorGlobalDropdown').classList.remove('abierto');
		document.getElementById('kpBuscadorGlobal').value = empleado.nombre;
		abrirFicha(empleado.clave);
	}, () => (puedeAsignarGlobal ? 'buscarEmpleados' : 'buscarMisSubordinados'));

	/* ---- instancia: mini-buscador para asignar evaluador manual ----
	   Antes, hacer click en un resultado asignaba de inmediato -- si
	   solo estabas viendo las coincidencias del nombre, terminabas
	   asignando a alguien sin querer. Ahora primero pide confirmar. */
	iniciarBuscador('kpBuscadorEval', 'kpBuscadorEvalDropdown', (empleado) => {
		document.getElementById('kpBuscadorEvalDropdown').classList.remove('abierto');
		Swal.fire({
			icon: 'question',
			title: '¿Asignar evaluador?',
			text: `¿Asignar a ${empleado.nombre} como evaluador de este empleado?`,
			showCancelButton: true,
			confirmButtonText: 'Sí, asignar',
			cancelButtonText: 'Cancelar'
		}).then(r => {
			if (r.isConfirmed) asignarEvaluadorManual(empleado.clave, empleado.nombre);
		});
	});

	/* ════════════════════════════════════════
	   Modal: ficha de empleado (visto desde el buscador)
	════════════════════════════════════════ */
	let claveFichaActual = null;

	async function abrirFicha(clave) {
		claveFichaActual = clave;

		if (!document.getElementById('kpFichaPeriodo').options.length) {
			llenarSelectorPeriodo(document.getElementById('kpFichaPeriodo'));
			document.getElementById('kpFichaPeriodo').value = document.getElementById('kpPeriodo').value;
			document.getElementById('kpFichaPeriodo').addEventListener('change', cargarFicha);
		}

		document.getElementById('kpFichaBody').innerHTML = '<div style="text-align:center;padding:20px;color:#94A3B8;">Cargando...</div>';
		document.getElementById('kpFichaAsignarBox').style.display = 'none';
		document.getElementById('kpFichaBtnGuardar').style.display = 'none';
		document.getElementById('kpModalFicha').classList.add('abierto');

		await cargarFicha();
	}

	async function cargarFicha() {
		if (!claveFichaActual) return;

		const periodo = document.getElementById('kpFichaPeriodo').value;

		try {
			const resp = await api({ action: 'getFichaEmpleado', clave_empleado: claveFichaActual, periodo });

			if (!resp.ok) {
				document.getElementById('kpFichaBody').innerHTML = `<div style="text-align:center;padding:20px;color:#C0392B;">${escaparHtml(resp.msg)}</div>`;
				return;
			}

			renderFicha(resp);

		} catch (e) {
			document.getElementById('kpFichaBody').innerHTML = '<div style="text-align:center;padding:20px;color:#C0392B;">Error de comunicación con el servidor.</div>';
		}
	}

	function renderFicha(resp) {
		const emp = resp.empleado;
		document.getElementById('kpFichaAvatar').textContent = iniciales(emp.nombre);
		document.getElementById('kpFichaNombre').textContent = emp.nombre;
		document.getElementById('kpFichaSub').textContent = `${emp.categoria || 'Sin categoría'} · No. ${emp.no_empleado || '—'}`;

		const evalBox = document.getElementById('kpFichaEvaluador');
		const asignarBox = document.getElementById('kpFichaAsignarBox');

		if (resp.evaluador) {
			const origen = resp.evaluador.fuente === 'manual' ? 'evaluador asignado manualmente' : 'jefe directo';
			evalBox.innerHTML = `👤 Evaluado por: <strong>${escaparHtml(resp.evaluador.nombre)}</strong> (${origen})`;
			asignarBox.style.display = 'none';
		} else {
			evalBox.innerHTML = `⚠️ Sin evaluador asignado`;
			asignarBox.style.display = 'block';
		}

		/* Dos permisos separados: puede_calificar (llenar resultado/cumplimiento
		   y Guardar -- SOLO jefe directo/evaluador efectivo, ni el admin) y
		   puede_asignar (agregar KPIs nuevos aquí mismo -- rol: admin o
		   asignador autorizado, sin importar si es jefe de este empleado). */
		document.getElementById('kpFichaBtnGuardar').style.display = resp.puede_calificar ? 'inline-flex' : 'none';

		const body = document.getElementById('kpFichaBody');

		if (!resp.kpis.length) {
			body.innerHTML = '<div style="text-align:center;padding:14px 0 4px;color:#94A3B8;">Este empleado todavía no tiene KPIs asignados.</div>'
				+ (resp.puede_asignar ? htmlAgregarKpiInlineFicha() : '');
			if (resp.puede_asignar) initBuscadorIndicadorInlineFicha();
			document.getElementById('kpFichaTotal').textContent = '0';
			return;
		}

		/* Bug reportado: antes el formulario de "Agregar KPI" solo salía
		   cuando el empleado tenía CERO KPIs -- si ya tenía alguno, quien
		   puede asignar se quedaba sin forma de agregarle otro desde esta
		   ficha. Debe estar disponible siempre que resp.puede_asignar sea
		   true, sin importar cuántos KPIs ya tenga. */

		const soloLectura = !resp.puede_calificar;

		body.innerHTML = resp.kpis.map(k => {
			const cumplimiento = k.cumplimiento ?? '';
			const comentarios = k.comentarios ?? '';
			const calif = k.calificacion != null ? parseFloat(k.calificacion).toFixed(2) : '0.00';
			const disabled = soloLectura ? 'disabled' : '';

			return `
				<div class="kp-eval-item" data-id-kpi="${k.id_kpi}" data-ponderacion="${k.ponderacion}">
					<div class="kp-eval-top">
						<div>
							<div class="kp-eval-nombre">${escaparHtml(k.indicador)}</div>
							<div class="kp-eval-meta">Meta: ${escaparHtml(k.meta)}</div>
						</div>
						<span class="kp-eval-pond">${parseFloat(k.ponderacion).toFixed(1)}%</span>
					</div>
					<div class="kp-eval-campos ${soloLectura ? 'solo-lectura' : ''}">
						<div>
							<label>% cumplimiento</label>
							<input type="number" class="kp-inp-cumplimiento" min="0" max="200" step="0.1" value="${cumplimiento}" oninput="recalcularFilaFicha(this)" ${disabled}>
						</div>
						<div class="kp-eval-calif">
							<label>Calificación</label>
							<div class="valor kp-out-calif">${calif}</div>
						</div>
					</div>
					<div style="margin-top:8px;">
						<label style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;">Comentarios</label>
						<textarea class="kp-inp-comentarios" rows="1" style="width:100%;border:1.5px solid #E2E8F0;border-radius:8px;padding:6px 10px;font-size:12px;" ${disabled}>${escaparHtml(comentarios)}</textarea>
					</div>
				</div>
			`;
		}).join('') + (resp.puede_asignar ? htmlAgregarKpiInlineFicha() : '');

		if (resp.puede_asignar) initBuscadorIndicadorInlineFicha();

		document.getElementById('kpFichaTotal').textContent = resp.calificacion.toFixed(1);
	}

	function recalcularFilaFicha(input) {
		const item = input.closest('.kp-eval-item');
		const ponderacion = parseFloat(item.dataset.ponderacion) || 0;
		const cumplimiento = parseFloat(input.value) || 0;
		const calif = (ponderacion * cumplimiento / 100);
		item.querySelector('.kp-out-calif').textContent = calif.toFixed(2);

		let total = 0;
		document.querySelectorAll('#kpFichaBody .kp-eval-item').forEach(it => {
			total += parseFloat(it.querySelector('.kp-out-calif').textContent) || 0;
		});
		document.getElementById('kpFichaTotal').textContent = total.toFixed(1);
	}

	function cerrarModalFicha() {
		document.getElementById('kpModalFicha').classList.remove('abierto');
		claveFichaActual = null;
	}

	async function guardarFichaEvaluacion() {
		if (!claveFichaActual) return;

		const evaluaciones = [];
		document.querySelectorAll('#kpFichaBody .kp-eval-item').forEach(item => {
			const cumplimiento = item.querySelector('.kp-inp-cumplimiento').value;
			if (cumplimiento === '') return;

			evaluaciones.push({
				id_kpi: parseInt(item.dataset.idKpi, 10),
				resultado: '',
				cumplimiento: parseFloat(cumplimiento),
				comentarios: item.querySelector('.kp-inp-comentarios').value
			});
		});

		if (!evaluaciones.length) {
			Swal.fire('Nada que guardar', 'Captura al menos un % de cumplimiento.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarEvaluacion',
				clave_empleado: claveFichaActual,
				periodo: document.getElementById('kpFichaPeriodo').value,
				evaluaciones
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
				return;
			}

			Swal.fire({ icon: 'success', title: 'Evaluación guardada', timer: 1200, showConfirmButton: false });
			cargarFicha();
			cargarEvaluados();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function asignarEvaluadorManual(claveEvaluador, nombreEvaluador) {
		if (!claveFichaActual) return;

		mostrarLoader();
		try {
			const resp = await api({
				action: 'asignarEvaluadorManual',
				clave_empleado: claveFichaActual,
				clave_evaluador: claveEvaluador
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo asignar.', 'error');
				return;
			}

			document.getElementById('kpBuscadorEval').value = '';
			Swal.fire({ icon: 'success', title: `${nombreEvaluador} asignado como evaluador`, timer: 1400, showConfirmButton: false });
			cargarFicha();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* Mismo mini-formulario de "agregar KPI sin salir del modal" que en
	   la evaluación de "Mis evaluados", pero para la ficha abierta desde
	   el buscador (usa claveFichaActual/cargarFicha en vez de
	   claveEvalActual/cargarFormularioEval). */
	function htmlAgregarKpiInlineFicha() {
		return `
			<div class="kp-asignar-box" style="border-style:solid; border-color:#CFE2F8; background:#EAF2FC;">
				<div class="titulo" style="color:#1E5B9E;">➕ Agregar un KPI a este empleado</div>
				<div class="kp-eval-campos" style="grid-template-columns:2fr 1fr 1fr;">
					<div style="position:relative;">
						<label>Indicador</label>
						<input type="text" id="kpFichaNuevoIndicador" placeholder="Ej. Cumplimiento de meta mensual" autocomplete="off">
						<div class="kp-buscador-dropdown" id="kpFichaNuevoIndicadorDropdown"></div>
					</div>
					<div>
						<label>Ponderación (%)</label>
						<input type="number" id="kpFichaNuevoPonderacion" min="0" max="100" step="0.01">
					</div>
					<div>
						<label>Meta</label>
						<input type="text" id="kpFichaNuevoMeta" placeholder="Ej. 100%">
					</div>
				</div>
				<div style="margin-top:10px; text-align:right;">
					<button type="button" class="kp-btn kp-btn-primario" onclick="agregarKpiInlineFicha()">➕ Agregar</button>
				</div>
			</div>
		`;
	}

	function initBuscadorIndicadorInlineFicha() {
		iniciarBuscadorIndicador('kpFichaNuevoIndicador', 'kpFichaNuevoIndicadorDropdown', (ind) => {
			document.getElementById('kpFichaNuevoIndicador').value = ind.nombre;
		});
	}

	async function agregarKpiInlineFicha() {
		if (!claveFichaActual) return;

		const indicador = document.getElementById('kpFichaNuevoIndicador').value.trim();
		const ponderacion = document.getElementById('kpFichaNuevoPonderacion').value;
		const meta = document.getElementById('kpFichaNuevoMeta').value.trim();

		if (!indicador || !ponderacion || !meta) {
			Swal.fire('Faltan datos', 'Indicador, ponderación y meta son obligatorios.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api({
				action: 'guardarKpi',
				id: 0,
				clave_empleado: claveFichaActual,
				indicador,
				descripcion: '',
				ponderacion,
				tipo_meta: 'porcentaje',
				meta
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo agregar el KPI.', 'error');
				return;
			}

			Swal.fire({ icon: 'success', title: 'KPI agregado', timer: 1000, showConfirmButton: false });
			await cargarFicha();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Catálogo de KPIs -- por empleado (cada quien tiene el suyo,
	   ya no es un catálogo genérico). Primero se busca al empleado.
	════════════════════════════════════════ */
	let claveCatalogoActual = null;

	iniciarBuscador('kpBuscadorCatalogo', 'kpBuscadorCatalogoDropdown', (empleado) => {
		document.getElementById('kpBuscadorCatalogoDropdown').classList.remove('abierto');
		document.getElementById('kpBuscadorCatalogo').value = empleado.nombre;
		seleccionarEmpleadoCatalogo(empleado);
	});

	/* Mini-buscador para asignar evaluador cuando el empleado seleccionado
	   en "Administrar KPIs" no tiene jefe directo ni evaluador manual. */
	iniciarBuscador('kpBuscadorCatalogoEval', 'kpBuscadorCatalogoEvalDropdown', (empleado) => {
		document.getElementById('kpBuscadorCatalogoEvalDropdown').classList.remove('abierto');
		Swal.fire({
			icon: 'question',
			title: '¿Asignar evaluador?',
			text: `¿Asignar a ${empleado.nombre} como evaluador de este empleado?`,
			showCancelButton: true,
			confirmButtonText: 'Sí, asignar',
			cancelButtonText: 'Cancelar'
		}).then(r => {
			if (r.isConfirmed) asignarEvaluadorManualCatalogo(empleado.clave, empleado.nombre);
		});
	});

	function seleccionarEmpleadoCatalogo(empleado) {
		claveCatalogoActual = empleado.clave;

		document.getElementById('kpCatalogoVacio').style.display = 'none';
		document.getElementById('kpCatalogoContenido').style.display = 'block';
		document.getElementById('kpCatalogoAvatar').textContent = iniciales(empleado.nombre);
		document.getElementById('kpCatalogoNombre').textContent = empleado.nombre;
		document.getElementById('kpCatalogoSub').textContent = empleado.categoria || 'Sin categoría';

		cargarCatalogo();
	}

	async function cargarCatalogo() {
		if (!claveCatalogoActual) return;

		const asignarBox = document.getElementById('kpCatalogoAsignarBox');
		const errorBox = document.getElementById('kpCatalogoErrorBox');
		const tablaWrap = document.getElementById('kpCatalogoTablaWrap');

		asignarBox.style.display = 'none';
		errorBox.style.display = 'none';
		tablaWrap.style.display = 'block';
		document.getElementById('kpCatalogoBody').innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94A3B8;">Cargando...</td></tr>';

		try {
			const resp = await api({ action: 'getCatalogoKpis', clave_empleado: claveCatalogoActual });

			if (!resp.ok) {
				document.getElementById('kpBtnNuevoKpi').style.display = 'none';
				tablaWrap.style.display = 'none';

				if (resp.sin_evaluador) {
					/* Sin jefe directo ni evaluador manual: ofrecemos asignarlo
					   aquí mismo en vez de dejar al usuario en un callejón sin salida. */
					asignarBox.style.display = 'block';
				} else {
					errorBox.style.display = 'block';
					errorBox.textContent = resp.msg || 'No se pudo cargar el catálogo.';
				}
				return;
			}

			/* "Nuevo KPI" es ASIGNAR -- permiso de rol (admin o asignador
			   autorizado), ya no depende de ser jefe directo de este
			   empleado. Un jefe directo que solo puede calificar (no
			   asignar) puede seguir viendo este catálogo, nada más no ve el
			   botón de agregar/editar. */
			document.getElementById('kpBtnNuevoKpi').style.display = puedeAsignarGlobal ? 'inline-flex' : 'none';
			renderCatalogo(resp.datos, resp.suma_ponderacion_activos);

		} catch (e) {
			tablaWrap.style.display = 'none';
			errorBox.style.display = 'block';
			errorBox.textContent = 'Error de comunicación con el servidor.';
		}
	}

	async function asignarEvaluadorManualCatalogo(claveEvaluador, nombreEvaluador) {
		if (!claveCatalogoActual) return;

		mostrarLoader();
		try {
			const resp = await api({
				action: 'asignarEvaluadorManual',
				clave_empleado: claveCatalogoActual,
				clave_evaluador: claveEvaluador
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo asignar.', 'error');
				return;
			}

			document.getElementById('kpBuscadorCatalogoEval').value = '';
			Swal.fire({ icon: 'success', title: `${nombreEvaluador} asignado como evaluador`, timer: 1400, showConfirmButton: false });
			cargarCatalogo();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	const TIPO_META_LABEL = { porcentaje: 'Porcentaje', numerica: 'Numérica', cumplido_no_cumplido: 'Cumplido/No cumplido' };

	function renderCatalogo(datos, sumaPonderacion) {
		const tbody = document.getElementById('kpCatalogoBody');

		if (!datos.length) {
			tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94A3B8;">Este empleado todavía no tiene KPIs asignados.</td></tr>';
		} else {
			tbody.innerHTML = datos.map(k => `
				<tr class="${k.activo == 1 ? '' : 'inactivo'}">
					<td><strong>${escaparHtml(k.indicador)}</strong>${k.descripcion ? '<div style="font-size:11px;color:#94A3B8;margin-top:2px;">' + escaparHtml(k.descripcion) + '</div>' : ''}</td>
					<td>${escaparHtml(k.meta)}</td>
					<td>${TIPO_META_LABEL[k.tipo_meta] || k.tipo_meta}</td>
					<td><span class="kp-ponderacion-pill">${parseFloat(k.ponderacion).toFixed(1)}%</span></td>
					<td>${k.activo == 1 ? '✅ Activo' : '⛔ Inactivo'}</td>
					<td style="white-space:nowrap;">
						${puedeAsignarGlobal ? `
						<button class="kp-icon-btn" title="Editar" onclick='abrirModalKpi(${JSON.stringify(k)})'>✏️</button>
						<button class="kp-icon-btn" title="${k.activo == 1 ? 'Desactivar' : 'Activar'}" onclick="toggleEstatusKpi(${k.id}, ${k.activo == 1 ? 0 : 1})">${k.activo == 1 ? '⛔' : '♻️'}</button>
						` : ''}
					</td>
				</tr>
			`).join('');
		}

		const claseSuma = Math.abs(sumaPonderacion - 100) < 0.01 ? 'kp-suma-ok' : 'kp-suma-mal';
		document.getElementById('kpSumaPonderacion').innerHTML =
			`Suma de ponderaciones activas: <span class="${claseSuma}">${sumaPonderacion.toFixed(1)}%</span>` +
			(Math.abs(sumaPonderacion - 100) < 0.01 ? ' ✅' : ' — debería sumar 100%');
	}

	/* Autocompletar del catálogo maestro en el modal Nuevo/Editar KPI --
	   se llama una sola vez (el input es fijo en la página), a diferencia
	   de los mini-formularios de abajo que se re-crean cada vez. */
	iniciarBuscadorIndicador('kpKpiIndicador', 'kpKpiIndicadorDropdown', (ind) => {
		document.getElementById('kpKpiIndicador').value = ind.nombre;
		document.getElementById('kpKpiTipoMeta').value = ind.tipo_meta;
	});

	function abrirModalKpi(kpi) {
		if (!claveCatalogoActual) return;

		document.getElementById('kpKpiTitulo').textContent = kpi ? 'Editar KPI' : 'Nuevo KPI · ' + document.getElementById('kpCatalogoNombre').textContent;
		document.getElementById('kpKpiId').value = kpi ? kpi.id : '';
		document.getElementById('kpKpiIndicador').value = kpi ? kpi.indicador : '';
		document.getElementById('kpKpiDescripcion').value = kpi ? (kpi.descripcion || '') : '';
		document.getElementById('kpKpiPonderacion').value = kpi ? kpi.ponderacion : '';
		document.getElementById('kpKpiTipoMeta').value = kpi ? kpi.tipo_meta : 'porcentaje';
		document.getElementById('kpKpiMeta').value = kpi ? kpi.meta : '';
		document.getElementById('kpModalKpi').classList.add('abierto');
	}

	function cerrarModalKpi() {
		document.getElementById('kpModalKpi').classList.remove('abierto');
	}

	async function guardarKpi() {
		const payload = {
			action: 'guardarKpi',
			id: document.getElementById('kpKpiId').value || 0,
			clave_empleado: claveCatalogoActual,
			indicador: document.getElementById('kpKpiIndicador').value.trim(),
			descripcion: document.getElementById('kpKpiDescripcion').value.trim(),
			ponderacion: document.getElementById('kpKpiPonderacion').value,
			tipo_meta: document.getElementById('kpKpiTipoMeta').value,
			meta: document.getElementById('kpKpiMeta').value.trim()
		};

		if (!payload.indicador || !payload.ponderacion || !payload.meta) {
			Swal.fire('Faltan datos', 'Indicador, ponderación y meta son obligatorios.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api(payload);
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
				return;
			}

			cerrarModalKpi();
			cargarCatalogo();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function toggleEstatusKpi(id, activo) {
		mostrarLoader();
		try {
			const resp = await api({ action: 'cambiarEstatusKpi', id, activo });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
				return;
			}

			cargarCatalogo();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Catálogo MAESTRO de indicadores (solo admin) -- aquí se ve el
	   nombre de cada indicador UNA sola vez, con cuántos empleados ya
	   lo tienen asignado, para poder detectar y limpiar duplicados
	   parecidos que se hayan colado antes de que existiera este
	   catálogo. Renombrar aquí actualiza también el texto ya guardado
	   en los KPIs de cada empleado que lo usa.
	════════════════════════════════════════ */
	let indicadoresCache = [];

	async function cargarCatalogoIndicadores() {
		const tbody = document.getElementById('kpIndicadoresBody');
		if (!tbody) { console.error('kpIndicadoresBody no existe en el DOM -- ¿el archivo desplegado en el servidor es el más reciente?'); return; }

		try {
			const inputBuscar = document.getElementById('kpIndBuscar');
			const termino = inputBuscar ? inputBuscar.value.trim() : '';
			tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Cargando...</td></tr>';

			const resp = await api({ action: 'getCatalogoIndicadores', termino });

			if (!resp.ok) {
				console.error('getCatalogoIndicadores -> ok:false', resp);
				tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:20px;color:#C0392B;">${escaparHtml(resp.msg || 'No se pudo cargar.')}</td></tr>`;
				return;
			}

			indicadoresCache = resp.datos || [];
			renderCatalogoIndicadores(indicadoresCache);

		} catch (e) {
			console.error('cargarCatalogoIndicadores -> excepción', e);
			tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:20px;color:#C0392B;">Error de comunicación con el servidor${e && e.message ? ': ' + escaparHtml(e.message) : ''}.</td></tr>`;
		}
	}

	function filtrarCatalogoIndicadores() {
		// El filtro real ya lo hace el backend (buscarIndicadores toma
		// "termino"), pero para no pegarle a la BD en cada tecleo se
		// vuelve a pedir con un pequeño debounce.
		clearTimeout(window.__kpIndTimer);
		window.__kpIndTimer = setTimeout(cargarCatalogoIndicadores, 300);
	}

	function renderCatalogoIndicadores(datos) {
		const tbody = document.getElementById('kpIndicadoresBody');

		if (!datos.length) {
			tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Sin indicadores en el catálogo todavía.</td></tr>';
			return;
		}

		tbody.innerHTML = datos.map(d => `
			<tr class="${d.activo ? '' : 'inactivo'}">
				<td style="font-weight:700;">${escaparHtml(d.nombre)}</td>
				<td>${TIPO_META_LABEL[d.tipo_meta] || d.tipo_meta}</td>
				<td>${d.empleados_usando}</td>
				<td><span class="kp-mini-badge ${d.activo ? 'ok' : 'sin'}">${d.activo ? 'Activo' : 'Inactivo'}</span></td>
				<td style="text-align:right; white-space:nowrap;">
					<button class="kp-icon-btn" title="Editar" onclick='abrirModalIndicador(${JSON.stringify(d)})'>✏️</button>
					<button class="kp-icon-btn" title="${d.activo ? 'Desactivar' : 'Activar'}" onclick="toggleEstatusIndicador(${d.id}, ${d.activo ? 0 : 1})">${d.activo ? '🚫' : '✅'}</button>
				</td>
			</tr>
		`).join('');
	}

	function abrirModalIndicador(ind) {
		document.getElementById('kpIndTitulo').textContent = ind ? 'Editar indicador' : 'Nuevo indicador';
		document.getElementById('kpIndId').value = ind ? ind.id : '';
		document.getElementById('kpIndNombre').value = ind ? ind.nombre : '';
		document.getElementById('kpIndDescripcion').value = '';
		document.getElementById('kpIndTipoMeta').value = ind ? ind.tipo_meta : 'porcentaje';
		document.getElementById('kpModalIndicador').classList.add('abierto');
	}

	function cerrarModalIndicador() {
		document.getElementById('kpModalIndicador').classList.remove('abierto');
	}

	async function guardarIndicadorMaestroModal() {
		const payload = {
			action: 'guardarIndicadorMaestro',
			id: document.getElementById('kpIndId').value || 0,
			nombre: document.getElementById('kpIndNombre').value.trim(),
			descripcion: document.getElementById('kpIndDescripcion').value.trim(),
			tipo_meta: document.getElementById('kpIndTipoMeta').value
		};

		if (!payload.nombre) {
			Swal.fire('Falta el nombre', 'El nombre del indicador es obligatorio.', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const resp = await api(payload);
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo guardar.', 'error');
				return;
			}

			cerrarModalIndicador();
			cargarCatalogoIndicadores();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function toggleEstatusIndicador(id, activo) {
		mostrarLoader();
		try {
			const resp = await api({ action: 'cambiarEstatusIndicador', id, activo });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo actualizar.', 'error');
				return;
			}

			cargarCatalogoIndicadores();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Asignadores de KPIs (solo admin) -- lista blanca de quién más,
	   aparte de ADMINISTRA, puede asignar KPIs a cualquier empleado.
	   Buscas al empleado, lo seleccionas y con confirmación queda
	   agregado; "Quitar" lo desactiva (no lo borra, para conservar el
	   historial de quién tuvo el permiso y cuándo).
	════════════════════════════════════════ */
	async function cargarAsignadoresKpi() {
		const tbody = document.getElementById('kpAsignadoresBody');
		if (!tbody) return;

		tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Cargando...</td></tr>';

		try {
			const resp = await api({ action: 'getAsignadoresKpi' });

			if (!resp.ok) {
				tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:20px;color:#C0392B;">${escaparHtml(resp.msg || 'No se pudo cargar.')}</td></tr>`;
				return;
			}

			renderAsignadoresKpi(resp.datos || []);

		} catch (e) {
			tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#C0392B;">Error de comunicación con el servidor.</td></tr>';
		}
	}

	function renderAsignadoresKpi(datos) {
		const tbody = document.getElementById('kpAsignadoresBody');

		if (!datos.length) {
			tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Todavía no hay asignadores autorizados -- solo ADMINISTRA puede asignar KPIs.</td></tr>';
			return;
		}

		tbody.innerHTML = datos.map(d => `
			<tr>
				<td>
					<div class="kp-emp-celda">
						<div class="kp-emp-avatar" style="width:30px;height:30px;font-size:11px;">${escaparHtml(iniciales(d.nombre || d.clave_empleado))}</div>
						<div class="kp-emp-nombre">${escaparHtml(d.nombre || '(sin nombre)')}</div>
					</div>
				</td>
				<td>${escaparHtml(d.clave_empleado)}</td>
				<td>${escaparHtml(d.asignado_por || '—')}</td>
				<td>${escaparHtml(d.asignado_en || '—')}</td>
				<td style="text-align:right;">
					<button class="kp-icon-btn" title="Quitar" onclick="quitarAsignadorKpi('${d.clave_empleado}', '${escaparHtml(d.nombre || d.clave_empleado)}')">🗑️</button>
				</td>
			</tr>
		`).join('');
	}

	iniciarBuscador('kpBuscadorAsignador', 'kpBuscadorAsignadorDropdown', async (empleado) => {
		document.getElementById('kpBuscadorAsignadorDropdown').classList.remove('abierto');
		document.getElementById('kpBuscadorAsignador').value = '';

		const result = await Swal.fire({
			icon: 'question',
			title: `¿Autorizar a ${empleado.nombre}?`,
			html: `Va a poder asignar KPIs a <strong>cualquier</strong> empleado, no solo a sus subordinados. No podrá calificar a nadie que no sea su jefe directo -- eso no cambia.`,
			showCancelButton: true,
			confirmButtonText: 'Sí, autorizar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B84C7'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'agregarAsignadorKpi', clave_empleado: empleado.clave });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo agregar.', 'error');
				return;
			}

			Swal.fire({ icon: 'success', title: `${empleado.nombre} ya puede asignar KPIs`, timer: 1400, showConfirmButton: false });
			cargarAsignadoresKpi();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	});

	async function quitarAsignadorKpi(claveEmpleado, nombre) {
		const result = await Swal.fire({
			icon: 'warning',
			title: `¿Quitarle el permiso a ${nombre}?`,
			text: 'Ya no va a poder asignar KPIs a ningún empleado.',
			showCancelButton: true,
			confirmButtonText: 'Sí, quitar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#C0392B'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'quitarAsignadorKpi', clave_empleado: claveEmpleado });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo quitar.', 'error');
				return;
			}

			cargarAsignadoresKpi();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Importar Excel -- formato "MEDICION DE KPI" SISCA.

	   El archivo trae UNA HOJA POR EQUIPO (cada jefe tiene la suya, ej.
	   "DR", "AKG", "JGO"...) y cada hoja puede traer SU PROPIO periodo
	   (no todos los equipos se capturan el mismo mes). Además el libro
	   trae hojas que NO son de datos (plantilla de ejemplo, resumen de
	   pagos, glosario, calendario) -- esas se ignoran por nombre.

	   Dentro de cada hoja, los empleados vienen en bloques repetidos: una
	   fila de encabezado ("SISCA | Colaborador | Indicador | Ponderacion |
	   Meta | Resultados | Calificacion | ...") seguida de la fila del
	   empleado (clave en la columna A, nombre en la B, y ya el primer
	   indicador en C/D/E/F), y luego más renglones de indicador (columna A
	   vacía -- OJO: la columna B en esos renglones trae texto suelto de un
	   cuadro lateral del Excel que NO es del empleado, se ignora) hasta
	   llegar al renglón de totales (columna C vacía).
	════════════════════════════════════════ */
	const KP_HOJAS_EXCLUIR = ['machote', 'pagos variables', 'glosario', 'calendario variables'];

	let datosImportacion = null; // { grupos: [{ hoja, periodo, empleados }] }

	function parsearHojaKpis(hoja) {
		const filas = XLSX.utils.sheet_to_json(hoja, { header: 1, raw: true, defval: null });

		// Detectar periodo: busca las etiquetas "Periodo" y "Año" en las primeras filas.
		let mes = null, anio = null;
		for (let i = 0; i < Math.min(filas.length, 10); i++) {
			const fila = filas[i] || [];
			for (let c = 0; c < fila.length; c++) {
				const etiqueta = (fila[c] == null) ? '' : String(fila[c]).trim().toLowerCase();
				if (etiqueta === 'periodo' && typeof fila[c + 1] === 'number') mes = fila[c + 1];
				if (etiqueta === 'año' && typeof fila[c + 1] === 'number') anio = fila[c + 1];
			}
		}
		const periodo = (anio && mes) ? `${anio}-${String(mes).padStart(2, '0')}` : null;

		const empleados = [];

		for (let i = 0; i < filas.length; i++) {
			const fila = filas[i] || [];
			// Encabezado real de bloque: columna A = "SISCA" Y columna B = "Colaborador"
			// (así no confundimos con otras tablas del libro que también usan "SISCA" en A).
			const esEncabezado = fila[0] != null && String(fila[0]).trim().toUpperCase() === 'SISCA'
				&& fila[1] != null && String(fila[1]).trim().toLowerCase() === 'colaborador';
			if (!esEncabezado) continue;

			let j = i + 1;
			const filaClave = filas[j] || [];
			const clave = filaClave[0] != null ? String(filaClave[0]).trim() : '';
			const nombre = filaClave[1] != null ? String(filaClave[1]).trim() : '';

			if (!clave) { continue; }

			const kpis = [];
			while (j < filas.length) {
				const r = filas[j] || [];
				const indicador = r[2];
				if (indicador == null || String(indicador).trim() === '') break;

				kpis.push({
					indicador: String(indicador).trim(),
					ponderacion: typeof r[3] === 'number' ? r[3] : parseFloat(r[3]) || 0,
					meta: typeof r[4] === 'number' ? r[4] : (r[4] != null ? parseFloat(r[4]) : null),
					resultado: typeof r[5] === 'number' ? r[5] : (r[5] != null ? parseFloat(r[5]) : null)
				});
				j++;
			}

			if (kpis.length) empleados.push({ clave, nombre, kpis });
			i = j - 1; // el for hace i++ -> siguiente vuelta arranca justo en j
		}

		return { periodo, empleados };
	}

	function parsearWorkbookKpis(wb) {
		const grupos = [];

		wb.SheetNames.forEach(nombreHoja => {
			if (KP_HOJAS_EXCLUIR.includes(nombreHoja.trim().toLowerCase())) return;

			const { periodo, empleados } = parsearHojaKpis(wb.Sheets[nombreHoja]);
			if (empleados.length) grupos.push({ hoja: nombreHoja, periodo, empleados });
		});

		return { grupos };
	}

	async function procesarArchivoKpis() {
		const input = document.getElementById('kpImportarArchivo');
		const archivo = input.files && input.files[0];
		if (!archivo) return;

		mostrarLoader();
		try {
			const arrayBuffer = await archivo.arrayBuffer();
			const wb = XLSX.read(arrayBuffer, { type: 'array' });
			datosImportacion = parsearWorkbookKpis(wb);
			ocultarLoader();

			if (!datosImportacion.grupos.length) {
				Swal.fire('Sin datos reconocibles', 'No encontré bloques de empleados con el formato esperado (encabezado "SISCA | Colaborador...").', 'warning');
				document.getElementById('kpImportarPreview').style.display = 'none';
				document.getElementById('kpImportarVacio').style.display = 'block';
				return;
			}

			renderPreviewImportacion();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'No se pudo leer el archivo. ¿Es un .xlsx válido?', 'error');
		}
	}

	function renderPreviewImportacion() {
		document.getElementById('kpImportarVacio').style.display = 'none';
		document.getElementById('kpImportarPreview').style.display = 'block';
		document.getElementById('kpImportarResultado').style.display = 'none';

		const periodos = [...new Set(datosImportacion.grupos.map(g => g.periodo || 'sin detectar'))];
		document.getElementById('kpImpPeriodo').textContent = periodos.join(', ');

		let totalEmpleados = 0, totalKpis = 0;

		document.getElementById('kpImportarBody').innerHTML = datosImportacion.grupos.map(grupo => {
			return grupo.empleados.map(emp => {
				totalEmpleados++;
				totalKpis += emp.kpis.length;
				const sumaPond = emp.kpis.reduce((s, k) => s + (k.ponderacion || 0), 0) * 100;
				const sinPeriodo = !grupo.periodo;
				return `
					<tr class="${sinPeriodo ? 'kp-imp-omitido' : ''}">
						<td><strong>${escaparHtml(emp.clave)}</strong></td>
						<td>${escaparHtml(emp.nombre)}</td>
						<td>${escaparHtml(grupo.hoja)}</td>
						<td>${escaparHtml(grupo.periodo || 'NO DETECTADO -- se omitirá')}</td>
						<td>${emp.kpis.length}</td>
						<td>${sumaPond.toFixed(1)}%</td>
					</tr>
				`;
			}).join('');
		}).join('');

		document.getElementById('kpImpEmpleados').textContent = totalEmpleados;
		document.getElementById('kpImpKpis').textContent = totalKpis;
	}

	async function aplicarImportacionKpis() {
		if (!datosImportacion || !datosImportacion.grupos.length) return;

		mostrarLoader();
		try {
			const resp = await api({
				action: 'importarKpis',
				grupos: datosImportacion.grupos
			});
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo importar.', 'error');
				return;
			}

			const r = resp.resumen;
			const cont = document.getElementById('kpImportarResultado');
			cont.style.display = 'block';
			cont.innerHTML = `
				<div class="kp-import-info" style="background:#E7F9F1; border-color:#BEEBD6; color:#0E9F6E;">
					<div class="kp-import-resultado-item">🗓️ Periodos procesados: <strong>${escaparHtml(r.periodos_procesados.join(', ') || '--')}</strong></div>
					<div class="kp-import-resultado-item">✅ Empleados procesados: <strong>${r.empleados_procesados}</strong></div>
					<div class="kp-import-resultado-item">🆕 KPIs creados: <strong>${r.kpis_creados}</strong></div>
					<div class="kp-import-resultado-item">✏️ KPIs actualizados: <strong>${r.kpis_actualizados}</strong></div>
					<div class="kp-import-resultado-item">📊 Evaluaciones guardadas: <strong>${r.evaluaciones_guardadas}</strong></div>
					${r.empleados_omitidos.length ? `
						<div class="kp-import-resultado-item" style="color:#C0392B; margin-top:8px;">⚠️ Omitidos (${r.empleados_omitidos.length}):</div>
						${r.empleados_omitidos.map(o => `<div class="kp-import-resultado-item" style="color:#C0392B;">&nbsp;&nbsp;• ${escaparHtml(o)}</div>`).join('')}
					` : ''}
				</div>
			`;

			Swal.fire({ icon: 'success', title: 'Importación aplicada', timer: 1500, showConfirmButton: false });
			cargarEvaluados();

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	/* ════════════════════════════════════════
	   Init
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

		// Los modales viven fuera del árbol de #contenido/#contenedor del
		// framework del sitio, para que su position:fixed no se rompa
		// (mismo caso que ya resolvimos en el módulo de validación).
		['kpModalEval', 'kpModalKpi', 'kpModalFicha', 'kpModalIndicador'].forEach(id => {
			const el = document.getElementById(id);
			if (el && el.parentElement !== document.body) document.body.appendChild(el);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') return;
			if (document.getElementById('kpModalEval').classList.contains('abierto')) cerrarModalEval();
			if (document.getElementById('kpModalKpi').classList.contains('abierto')) cerrarModalKpi();
			if (document.getElementById('kpModalFicha').classList.contains('abierto')) cerrarModalFicha();
			if (document.getElementById('kpModalIndicador').classList.contains('abierto')) cerrarModalIndicador();
		});

		llenarSelectorPeriodos();
		cargarEvaluados();
	});

	</script>
</html>