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
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<style>
			#contenido, #contenido * { text-align: left; }

			/* #contenedor trae una altura fija (calculada por JS más abajo);
			   sin esto el contenido que no cabe se corta en vez de poder
			   bajar con scroll de la página. */
			#contenedor { overflow-y:auto !important; }

			#loader {
				display:none; position:fixed; top:0; left:0; width:100%; height:100%;
				background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;
			}

				/* ── "cpt-" = Costo por puntos — mismo estilo que estruc_otros_masivo (.eom-*): tarjetas blancas, azul #14507a,
				   tamaños explícitos con !important para que carobra.css / seg_ventas.css no los agrande ni los desacomode ── */
				#contenido .cpt-wrap { background:transparent; padding:0; margin:0; border:0; }
				#contenido .cpt-top { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin:0 0 12px; }
				#contenido .cpt-seccion { background:#fff; border:1px solid #e0e8ec; border-radius:12px; padding:20px 22px; margin:0 0 18px; box-shadow:0 1px 3px rgba(15,23,42,.05); }
				#contenido .cpt-cab { display:flex; align-items:center; justify-content:space-between; margin:0 0 10px; }
				#contenido .cpt-seccion-titulo { font-size:16px !important; font-weight:700; color:#14507a; margin:0 0 10px !important; line-height:1.3; }
				#contenido .cpt-cab .cpt-seccion-titulo { margin:0 !important; }
				#contenido .cpt-nota { font-size:12px !important; color:#8896a6; margin-top:10px; line-height:1.5; }
				#contenido .cpt-vacio { color:#8896a6; font-size:12.5px; padding:12px 4px; background:#f6fafd; border:1px dashed #9cc3dd; border-radius:8px; text-align:center !important; }

				#contenido .cpt-btn {
					display:inline-flex; align-items:center; justify-content:center; box-sizing:border-box;
					background:#14507a; border:none; color:#fff; border-radius:9px; margin:0 !important;
					height:34px !important; min-width:40px; padding:0 16px !important; font-size:12.5px !important; font-weight:700; line-height:1 !important; cursor:pointer;
				}
				#contenido .cpt-btn:hover { background:#0f3f61; }
				#contenido .cpt-btn.guardar { background:#14507a; color:#fff; }
				#contenido .cpt-btn.chico { height:28px !important; font-size:12px !important; padding:0 10px !important; min-width:0; }
				#contenido .cpt-btn.quitar { background:#fdecea; color:#a52a1f; }
				#contenido .cpt-btn.quitar:hover { background:#f9d9d6; }
				#contenido .cpt-btn.ok { background:#e3f4ea; color:#1e8449; }
				#contenido .cpt-btn.ok:hover { background:#d2ecdc; }

				/* formulario de captura */
				#contenido .cpt-form { background:#f6fafd; border:1px solid #dde8ef; border-radius:10px; padding:14px 16px 8px; margin:0 0 12px; }
				#contenido .cpt-form-fila { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; margin:0 0 8px; }
				#contenido .cpt-campo { display:flex; flex-direction:column; min-width:0; }
				#contenido .cpt-campo label { display:block; width:auto; font-size:12px !important; font-weight:600; color:#5a6b7b; margin:0 0 3px 2px !important; padding:0; text-align:left !important; line-height:1.3; }
				#contenido .cpt-campo.cpt-btns { width:auto !important; }
				#contenido .cpt-editando { font-size:12px; color:#9a6a00; background:#fff6dd; border:1px solid #f0d58a; border-radius:6px; padding:4px 8px; display:inline-block; margin-bottom:6px; }
				#contenido .cpt-input {
					display:block; box-sizing:border-box; width:100% !important; height:34px !important; margin:0 !important; padding:0 8px !important;
					border:1px solid #c9d4dc !important; border-radius:8px !important; background:#fff !important; color:#34495e !important;
					font-size:13px !important; line-height:normal !important; outline:none; text-align:center !important; box-shadow:none;
				}
				#contenido .cpt-input:focus { border-color:#14507a !important; box-shadow:0 0 0 3px rgba(20,80,122,.12); }

				/* tablas de registros */
				#contenido .cpt-tabla { width:100%; border-collapse:collapse; background:#fff; font-size:12.5px !important; margin:0; }
				#contenido .cpt-tabla th { background:#14507a; color:#fff; padding:8px 10px; border:0; font-weight:700; font-size:11.5px !important; text-transform:uppercase; letter-spacing:.03em; text-align:center !important; }
				#contenido .cpt-tabla td { padding:7px 10px; border:0; border-bottom:1px solid #eef2f5; vertical-align:middle; text-align:center !important; color:#34495e; }
				#contenido .cpt-tabla tr.inactiva td { background:#f3f5f7; color:#98a5b1; }
				#contenido .cpt-tabla tr.editando td { background:#fff6dd; }
				#contenido .cpt-acc { white-space:nowrap; width:1%; }
				#contenido .cpt-estatus { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px !important; font-weight:700; white-space:nowrap; }
				#contenido .cpt-estatus.on { background:#e3f4ea; color:#1e8449; }
				#contenido .cpt-estatus.off { background:#eef1f3; color:#7a8a99; }
				#contenido .cpt-badge { font-size:12px; color:#7a8a99; margin-left:8px; }

				/* tabulador: editor pivote */
				#contenido .te-wrap { overflow-x:auto; }
				#contenido .te-tabla { width:100%; border-collapse:collapse; background:#fff; table-layout:fixed; font-size:12.5px !important; }
				#contenido .te-tabla th { background:#14507a; color:#fff; padding:8px 4px; border:1px solid #0f3f61; text-align:center !important; font-weight:700; font-size:12px !important; }
				#contenido .te-tabla td { padding:6px 4px; border:1px solid #e0e8ec; text-align:center !important; }
				#contenido .te-tabla th:first-child, #contenido .te-tabla td:first-child { width:230px; text-align:left !important; white-space:nowrap; }
				#contenido .te-tabla th.te-ctl, #contenido .te-tabla td.te-ctl { width:64px; background:transparent; border:0; }
				#contenido .te-rng { display:flex; align-items:center; gap:4px; }
				#contenido .te-inp { box-sizing:border-box; height:28px !important; border:1px solid #c9d4dc !important; border-radius:6px !important; padding:0 2px !important; margin:0 !important; text-align:center !important; font-size:12.5px !important; background:#fff; color:#34495e; min-width:0; }
				#contenido .te-rng .te-inp { flex:1 1 0; width:0 !important; }
				#contenido .te-mini { border:0; background:#fdecea; color:#a52a1f; border-radius:6px; cursor:pointer; padding:2px 8px; font-size:13px !important; line-height:1.2; flex:0 0 auto; }
				#contenido .te-mini.ok { background:#e3f4ea; color:#1e8449; }

				/* switch Comisión base / Bonos */
				#contenido .cpt-switch { display:inline-flex; background:#e3eaf0; border-radius:999px; padding:4px; }
				#contenido .cpt-switch button { border:0; background:transparent; color:#5a6b7b; font-size:13px !important; font-weight:700; padding:8px 24px; border-radius:999px; cursor:pointer; line-height:1.2; }
				#contenido .cpt-switch button.on { background:#14507a; color:#fff; box-shadow:0 1px 3px rgba(15,23,42,.25); }
				#contenido #bTabla { min-width:780px; }
				#contenido #bTabla th:first-child, #contenido #bTabla td:first-child { width:auto; white-space:normal; }
				#contenido .cpt-ta { display:block; width:100%; min-height:110px; box-sizing:border-box; border:1px solid #c9d4dc; border-radius:8px; padding:8px 10px; font-size:13px !important; color:#34495e; text-align:left !important; resize:vertical; font-family:inherit; }

				/* vista del bono (como la imagen de referencia) */
				#contenido .bv-kicker { font-size:13px; font-weight:700; letter-spacing:.12em; color:#2e6fae; text-transform:uppercase; margin:0 0 4px; }
				#contenido .bv-tit { font-size:30px; font-weight:800; color:#1a2f4e; margin:0 0 14px; line-height:1.15; }
				#contenido .bv-camp { display:inline-block; background:#fff1de; color:#9a5200; border:1px solid #f3c98b; border-radius:8px; padding:6px 12px; font-size:13px; margin:0 0 12px; }
				#contenido .bv-wrap { display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; }
				#bvTabla { flex:1 1 560px; border-collapse:separate; border-spacing:0; background:#fff; font-size:15px; border:1px solid #c3c9d2; box-shadow:0 2px 4px rgba(26,47,78,.15); }
				#bvTabla th { background:#1a2f4e; color:#fff; padding:12px 10px; text-align:right !important; font-weight:700; font-size:15px; }
				#bvTabla td { padding:10px; border-top:1px solid #d3dae5; border-left:1px solid #e3e8ef; text-align:right !important; color:#2b3a52; background:#fbfcfe; }
				#bvTabla th:first-child, #bvTabla td:first-child { text-align:left !important; }
				#bvTabla th.c, #bvTabla td.c { text-align:center !important; }
				#bvTabla tr:nth-child(even) td { background:#eef2f6; }
				#bvTabla tr.bv-elite td { background:#dde7f0; font-weight:600; }
				#contenido .bv-reglas { flex:0 0 300px; background:#1a2f4e; color:#fff; border-radius:12px; padding:22px; }
				#contenido .bv-reglas h4 { color:#8fb5e3; font-size:18px; font-weight:700; margin:0 0 12px; }
				#contenido .bv-reglas p { margin:0 0 10px; font-size:14.5px; line-height:1.45; color:#fff; }

				/* modal del editor de esquemas */
				#esqModal, #bonoModal, #bonoVerModal, #inaModal { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(20,30,50,.55); z-index:9999; align-items:center; justify-content:center; padding:20px; }
				#esqModal.abierto, #bonoModal.abierto, #bonoVerModal.abierto, #inaModal.abierto { display:flex; }
				#contenido #inaTabla { table-layout:fixed; min-width:0; }
				#contenido #inaTabla th:first-child, #contenido #inaTabla td:first-child { width:110px; text-align:left !important; }
				.esq-card { background:#fff; border-radius:12px; width:100%; max-width:1100px; max-height:92vh; overflow:auto; padding:44px 28px 24px; position:relative; box-shadow:0 12px 40px rgba(0,0,0,.35); text-align:left; }
				.esq-card .tab-x { position:absolute; top:10px; right:16px; border:0; background:none; font-size:26px; cursor:pointer; color:#5a6a82; line-height:1; }
				#contenido .te-th { display:flex; flex-direction:column; gap:3px; align-items:stretch; }
				#contenido .te-th-top { display:flex; justify-content:space-between; align-items:center; color:#fff; font-size:12px; font-weight:700; margin-bottom:2px; }
				#contenido .te-th label { display:block; font-size:11px !important; color:#cfe0ee; font-weight:600; margin:0 !important; padding:0; text-align:left !important; }
				#contenido .te-th .te-inp { width:100% !important; }

				/* modal tabulador (pantalla completa, solo lectura) */
				#tabModal { display:none; position:fixed; inset:0; background:rgba(20,30,50,.55); z-index:9999; align-items:center; justify-content:center; padding:20px; }
				#tabModal.abierto { display:flex; }
				.tab-card { background:#f5f6fa; border-radius:12px; width:100%; max-width:1100px; max-height:90vh; overflow:auto; padding:46px 28px 28px; position:relative; box-shadow:0 12px 40px rgba(0,0,0,.35); text-align:left; }
				.tab-x { position:absolute; top:10px; right:16px; border:0; background:none; font-size:26px; cursor:pointer; color:#5a6a82; line-height:1; }
				#tabTabla { width:100%; border-collapse:separate; border-spacing:0; background:#fff; table-layout:fixed; font-family:"Bahnschrift","Barlow Semi Condensed","Segoe UI",Calibri,Arial,sans-serif; font-size:18px; box-shadow:0 2px 4px rgba(26,47,78,.18); border-right:1px solid #c3c9d2; border-bottom:1px solid #c3c9d2; }
				#tabTabla th { background:#1a2f4e; color:#fff; padding:16px 12px; text-align:center !important; border-left:1px solid #2b4268; font-weight:700; font-size:19px; }
				#tabTabla td { padding:14px 12px; text-align:center !important; border-left:1px solid #c3c9d2; border-top:1px solid #c3c9d2; color:#2b3a52; background:#fbfcfe; }
				#tabTabla th:first-child, #tabTabla td:first-child { width:190px; text-align:left !important; white-space:nowrap; }
				#tabTabla tr:nth-child(even) td { background:#eef2f6; }
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body onload="calcula_body();">

	<div id="loader"><img src="./img/loading.gif" onerror="this.style.display='none'"></div>

<?php

		if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"]))
		{
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
		}
		else
		{
			$fg = $_SESSION["ultimoAcceso"];
			$fa = date("Y-n-j H:i:s");
			$periodo = (strtotime($fa)-strtotime($fg));
			if($periodo > 600)
			{
				session_destroy();
				mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
			}
			else
			{
				$_SESSION["ultimoAcceso"] = $fa;
				$_SESSION["autentifica"] = "p";
			}

			if ($_SESSION["autentifica"] != "p")
			{
				session_destroy();
				mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
			}
			else
			{
				reg_acceso($_SESSION["nombre"],"costo_puntos");
				$_SESSION["autentifica"]="";

				/* Solo Super Usuario (id_nivel 12). El API vuelve a validarlo. */
				$conecta = new conector();
				$esSuper = false;
				$bdC = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE nombre='" . utf8_decode($_SESSION["nombre"]) . "'");
				$tbC = $bdC ? mysqli_fetch_array($bdC) : null;
				if (!empty($tbC['clave'])) {
					$claveEsc = mysqli_real_escape_string($conecta->conexion, $tbC['clave']);
					$rsN = $conecta->consulta("SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}'");
					while ($rsN && ($rn = mysqli_fetch_assoc($rsN))) {
						if (intval($rn['id_nivel']) === 12) { $esSuper = true; break; }
					}
				}

				$divpie=new div("pie");
				$divopcion=new div("opcion");
				$divcabecera=new div("cabecera");
				$divcontenido=new div("contenido");
				$divcontenedor=new div("contenedor");
				$imgpie=new img("imgpie","./img/pie.png");
				$divusuario= new div("usuario",'',"absolute","97%");
				$imgcabecera=new img("imgcabecera","./img/cabecera.png");

				$divcabecera->crear();
					$imgcabecera->crear();
					$imgcabecera->css("width","100%");
					titulo("Configuración de Pagos");
					$pag="PAGO_LEADS_VTA";
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

					if (!$esSuper) {
?>
	<div class="container-fluid" style="padding:16px 18px; text-align:left;">
		<div class="cpt-wrap" style="text-align:center;color:#a33;">
			<div style="font-size:32px; margin-bottom:10px;">🔒</div>
			<div style="font-size:15px; font-weight:700; margin-bottom:6px;">No tienes permiso para esta sección</div>
			<div style="font-size:12.5px; color:#8896a6;">Se requiere Super Usuario.</div>
		</div>
	</div>
<?php
					} else {
?>
	<div class="container-fluid" style="padding:16px 18px; text-align:left;">
		<div class="cpt-wrap">

			<div class="cpt-top">
				<div class="cpt-switch">
					<button type="button" class="on" id="swBase" onclick="modoSet('base')">Comisión base</button>
					<button type="button" id="swBonos" onclick="modoSet('bonos')">Bonos</button>
				</div>
				<div>
					<button type="button" class="cpt-btn" id="btnVerTab" onclick="tabAbrir()" title="Ver la tabla de tabuladores">📊 Ver tabulador</button>
					<button type="button" class="cpt-btn" id="btnVerBono" style="display:none" onclick="bonoVer()" title="Ver el esquema de bonos vigente">📊 Ver bono</button>
				</div>
			</div>

			<div id="vistaBase">

			<div class="cpt-seccion">
				<h3 class="cpt-seccion-titulo">Pago por punto</h3>
				<div id="frm_n" class="cpt-form"></div>
				<div id="lst_n"></div>
				<div class="cpt-nota">Los pagos por punto activos no pueden traslaparse en fechas. Para darlos de baja usa "Desactivar" (borrado lógico): se conservan y se pueden activar de nuevo.</div>
			</div>

			<div class="cpt-seccion">
				<h3 class="cpt-seccion-titulo">Campañas especiales</h3>
				<div id="frm_c" class="cpt-form"></div>
				<div id="lst_c"></div>
			</div>

			<div class="cpt-seccion">
				<div class="cpt-cab"><h3 class="cpt-seccion-titulo">Tabulador de puntos</h3><button type="button" class="cpt-btn chico" onclick="esqNuevo()">+ Nuevo esquema</button></div>
				<div id="lst_t"></div>
				<div class="cpt-nota">Cada esquema es una tabla completa (edades × salarios) con su vigencia. Al modificarlo se edita la tabla entera y "Desactivar" apaga el esquema completo. Dos esquemas activos no pueden traslaparse en fechas.</div>
			</div>

			<div class="cpt-seccion">
				<div class="cpt-cab"><h3 class="cpt-seccion-titulo">Tabla de inactivos</h3><button type="button" class="cpt-btn chico" onclick="inaNuevo()">+ Nuevo esquema</button></div>
				<div id="lst_i"></div>
				<div class="cpt-nota">Cliente inactivo (sin aportar 12 meses): puntos por rango de edad con su vigencia. Al modificarlo se edita el esquema completo y "Desactivar" lo apaga. Dos esquemas activos no pueden traslaparse en fechas.</div>
			</div>

			</div><!-- /vistaBase -->

			<div id="vistaBonos" style="display:none">

				<div class="cpt-seccion">
					<div class="cpt-cab"><h3 class="cpt-seccion-titulo">Esquema de bonos</h3><button type="button" class="cpt-btn chico" onclick="bonoNuevo()">+ Nuevo esquema</button></div>
					<div id="lst_b"></div>
					<div class="cpt-nota">Cada esquema es la tabla completa de categorías (puntos y montos, más Élite +) con su vigencia. Al modificarlo se edita todo el esquema y "Desactivar" apaga el esquema completo. Dos esquemas activos no pueden traslaparse en fechas.</div>
				</div>

				<div class="cpt-seccion">
					<h3 class="cpt-seccion-titulo">Campañas especiales de bonos</h3>
					<div id="frm_bc" class="cpt-form"></div>
					<div id="lst_bc"></div>
					<div class="cpt-nota">Durante las fechas de la campaña el bono se paga multiplicado (ej. "Semana Embrujada" ×2 = se paga al doble). Dos campañas activas no pueden traslaparse en fechas.</div>
				</div>

			</div><!-- /vistaBonos -->

		</div>

		<div id="bonoModal" onclick="if(event.target===this)bonoCancelar()">
			<div class="esq-card">
				<button type="button" class="tab-x" onclick="bonoCancelar()" title="Cerrar">&times;</button>
				<div id="frm_b"></div>
			</div>
		</div>

		<div id="bonoVerModal" onclick="if(event.target===this)bonoVerCerrar()">
			<div class="esq-card" style="background:#f5f6fa;max-width:1200px">
				<button type="button" class="tab-x" onclick="bonoVerCerrar()" title="Cerrar">&times;</button>
				<div id="bonoVerCont"></div>
			</div>
		</div>

		<div id="inaModal" onclick="if(event.target===this)inaCancelar()">
			<div class="esq-card" style="max-width:900px">
				<button type="button" class="tab-x" onclick="inaCancelar()" title="Cerrar">&times;</button>
				<div id="frm_i"></div>
			</div>
		</div>

		<div id="esqModal" onclick="if(event.target===this)esqCancelar()">
			<div class="esq-card">
				<button type="button" class="tab-x" onclick="esqCancelar()" title="Cerrar">&times;</button>
				<div id="frm_t"></div>
			</div>
		</div>

		<div id="tabModal" onclick="if(event.target===this)tabCerrar()">
			<div class="tab-card">
				<button type="button" class="tab-x" onclick="tabCerrar()" title="Cerrar">&times;</button>
				<table id="tabTabla"></table>
			</div>
		</div>
	</div>
<?php
					}
					$divcontenido->close();
					$divcontenedor->close();
					$divpie->close();
?>
	<script language='javascript'>
		var hc=calcula_h(125);
		var h=$(window).outerHeight() - hc;
		$('#pie').css("top",h+5);
		hc=calcula_h(85);
		h=$(window).outerHeight() - (hc*2);
		$('#contenedor').css("top",hc-15);
		$('#contenedor').css("height",h);
		hc=calcula_h(45);
		$('#menu').css("top",hc-20);
		$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
	</script>
<?php } } ?>

	</body>

	<script type="text/javascript">
	const CPT_API = 'costo_puntos_api.php';

	async function cptApi(payload){
		var res = await fetch(CPT_API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
		var txt = await res.text();
		try { return JSON.parse(txt); } catch(e) { return { ok:false, msg:'Respuesta inválida del servidor: ' + txt.substring(0, 300) }; }
	}

	/* ══════════ Costo por puntos y campañas: formulario arriba, tabla abajo ══════════ */
	var CPT = { cpt: [], tab: [], ina: [] };    // tab = esquemas del tabulador, cada uno con su matriz {edades, salarios, puntos}
	var EDIT = { n:0, c:0 };           // id que se está modificando en cada formulario (0 = alta)
	var TE = null;                     // esquema del tabulador que se está capturando / editando

	var BASE = 'Comisión base';     // el costo por pago normal es siempre 'Comisión base'; una campaña especial lleva su propio nombre
	var C_NOM = { k:'campana', l:'Campaña', t:'text', w:260 };
	var C_DEL = { k:'fec_ini', l:'Del', t:'date', w:160 }, C_AL = { k:'fec_fin', l:'Al', t:'date', w:160 }, C_PTS = { k:'puntos', l:'Monto', t:'money', w:150 };
	var GRUPOS = {
		n: { tipo:'base', aG:'guardar', aT:'toggleActivo', cols:[ C_DEL, C_AL, C_PTS ], form:[ ['fec_ini','fec_fin','puntos'] ] },
		c: { tipo:'campana', aG:'guardar', aT:'toggleActivo',
		     cols:[ C_NOM, C_DEL, C_AL, { k:'salario_ini', l:'Salario inicial', t:'dec', w:150 }, { k:'salario_fin', l:'Salario final', t:'dec', w:150 }, C_PTS ],
		     form:[ ['campana'], ['fec_ini','fec_fin','salario_ini','salario_fin','puntos'] ] }
	};

	function esc(v){ return String(v == null ? '' : v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
	function mostrarLoaderCpt(){ var l = document.getElementById('loader'); if(l) l.style.display = 'flex'; }
	function ocultarLoaderCpt(){ var l = document.getElementById('loader'); if(l) l.style.display = 'none'; }
	function fmtF(s){ return s ? String(s).split('-').reverse().join('/') : ''; }
	function fmtN(n){ return (n === '' || n === null || n === undefined) ? '' : Number(n).toLocaleString('es-MX'); }
	function fmtMoney(n){ return (n === '' || n === null || n === undefined) ? '' : '$' + Number(n).toLocaleString('es-MX', { minimumFractionDigits:2, maximumFractionDigits:2 }); }
	function hoyISO(){ var d = new Date(), p = function(n){ return (n < 10 ? '0' : '') + n; }; return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate()); }
	function colDe(g, k){ return GRUPOS[g].cols.filter(function(c){ return c.k === k; })[0]; }
	function el(g, k){ return document.getElementById('f_' + g + '_' + k); }
	function grpFilas(g){ return CPT.cpt.filter(function(f){ return (f.campana !== BASE) === (GRUPOS[g].tipo === 'campana'); }); }
	function cptBuscar(id){ return CPT.cpt.filter(function(f){ return f.id === id; })[0]; }

	async function cptCargar(){
		mostrarLoaderCpt();
		var r1 = await cptApi({ action:'listar' });
		var r2 = await cptApi({ action:'listarTabulador' });
		var r3 = await cptApi({ action:'listarInactivos' });
		ocultarLoaderCpt();
		if(!r1.ok) Swal.fire('Error', r1.msg || 'No se pudo cargar.', 'error');
		if(!r2.ok) Swal.fire('Tabulador', r2.msg || 'No se pudo cargar el tabulador.', 'warning');
		CPT.cpt = r1.filas || [];
		CPT.tab = r2.esquemas || [];
		if(!r3.ok) Swal.fire('Tabla de inactivos', r3.msg || 'No se pudo cargar la tabla de inactivos.', 'warning');
		CPT.ina = r3.esquemas || [];
		pintarLista('n'); pintarLista('c'); pintarEsquemas(); pintarInactivos();
	}

	function pintarForm(g){
		var G = GRUPOS[g], cont = document.getElementById('frm_' + g), h = '';
		if(!cont) return;
		G.form.forEach(function(fila, i){
			h += '<div class="cpt-form-fila">';
			fila.forEach(function(k){
				var c = colDe(g, k), tipo = c.t === 'date' ? 'date' : 'text';
				var extra = (c.t === 'int' || c.t === 'money') ? ' inputmode="numeric" maxlength="9"' : (c.t === 'dec' ? ' inputmode="decimal"' : (c.t === 'text' ? ' maxlength="100" autocomplete="off" placeholder="Ej. Campaña Halloween"' : ''));
				var base = (g === 'c' && k === 'campana') ? c.w : Math.round(c.w * 0.7), mn = (c.t === 'date') ? 130 : (c.t === 'text' ? 200 : 95);
				h += '<div class="cpt-campo" style="flex:1 1 ' + base + 'px;min-width:' + mn + 'px"><label>' + esc(c.l) + '</label>' +
				     '<input class="cpt-input" id="f_' + g + '_' + k + '" type="' + tipo + '"' + extra +
				     ' oninput="cptFiltro(\'' + g + '\',\'' + k + '\',this)"></div>';
			});
			if(i === G.form.length - 1){
				h += '<div class="cpt-campo cpt-btns"><label>&nbsp;</label><div>' +
				     '<button type="button" class="cpt-btn" id="btn_' + g + '" title="Agregar" onclick="cptEnviar(\'' + g + '\')">+</button> ' +
				     '<button type="button" class="cpt-btn chico" id="can_' + g + '" style="display:none" onclick="cptCancelar(\'' + g + '\')">Cancelar</button></div></div>';
			}
			h += '</div>';
		});
		h += '<div class="cpt-editando" id="ed_' + g + '" style="display:none"></div>';
		cont.innerHTML = h;
		cont.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); cptEnviar(g); } });
	}
	function cptFiltro(g, k, input){
		var c = colDe(g, k), v = input.value;
		if(c.t === 'int' || c.t === 'money'){ v = v.replace(/\D/g, ''); if(input.value !== v) input.value = v; }
		if(c.t === 'dec'){ v = v.replace(/[^0-9.]/g, ''); if(input.value !== v) input.value = v; }
	}

	function pintarLista(g){
		var G = GRUPOS[g], filas = grpFilas(g), cont = document.getElementById('lst_' + g);
		if(!cont) return;
		if(!filas.length){ cont.innerHTML = '<div class="cpt-vacio">Aún no hay registros.</div>'; return; }
		var h = '<table class="cpt-tabla"><thead><tr>';
		G.cols.forEach(function(c){ h += '<th>' + esc(c.l) + '</th>'; });
		h += '<th>Estatus</th><th>Acciones</th></tr></thead><tbody>';
		filas.forEach(function(f){
			h += '<tr class="' + (f.activo ? '' : 'inactiva') + (EDIT[g] === f.id ? ' editando' : '') + '">';
			G.cols.forEach(function(c){
				var v = f[c.k];
				if(c.t === 'date') v = fmtF(v);
				else if(c.t === 'dec' || c.t === 'money') v = fmtMoney(v);
				else if(c.t === 'int') v = fmtN(v);
				h += '<td>' + esc(v) + '</td>';
			});
			h += '<td><span class="cpt-estatus ' + (f.activo ? 'on' : 'off') + '">' + (f.activo ? 'Activo' : 'Inactivo') + '</span></td>';
			h += '<td class="cpt-acc"><button class="cpt-btn chico" onclick="cptModificar(\'' + g + '\',' + f.id + ')">Modificar</button> ' +
			     '<button class="cpt-btn chico ' + (f.activo ? 'quitar' : 'ok') + '" onclick="cptToggle(\'' + g + '\',' + f.id + ',' + (f.activo ? 0 : 1) + ')">' + (f.activo ? 'Desactivar' : 'Activar') + '</button></td>';
			h += '</tr>';
		});
		cont.innerHTML = h + '</tbody></table>';
	}

	function cptModoEdicion(g, f){
		EDIT[g] = f ? f.id : 0;
		document.getElementById('btn_' + g).textContent = f ? 'Guardar' : '+';
		document.getElementById('btn_' + g).title = f ? 'Guardar cambios' : 'Agregar';
		document.getElementById('can_' + g).style.display = f ? '' : 'none';
		var ed = document.getElementById('ed_' + g);
		ed.style.display = f ? '' : 'none';
		ed.textContent = f ? 'Modificando un registro existente. Guarda los cambios o cancela.' : '';
		pintarLista(g);
	}
	function cptModificar(g, id){
		var f = cptBuscar(id), G = GRUPOS[g];
		if(!f) return;
		G.cols.forEach(function(c){ var x = el(g, c.k); if(x) x.value = (f[c.k] === null || f[c.k] === undefined) ? '' : f[c.k]; });
		cptModoEdicion(g, f);
		el(g, G.form[0][0]).scrollIntoView({ behavior:'smooth', block:'center' });
	}
	function cptCancelar(g){
		GRUPOS[g].cols.forEach(function(c){ var x = el(g, c.k); if(x) x.value = ''; });
		cptModoEdicion(g, null);
	}

	function cptValidar(g, f){
		if(!f.fec_ini || !f.fec_fin) return 'Captura las fechas inicial y final.';
		if(f.fec_fin < f.fec_ini) return 'La fecha final no puede ser anterior a la inicial.';
		if(g === 'c'){
			if(!f.campana) return 'Captura el nombre de la campaña especial.';
			if(f.campana.toLowerCase() === BASE.toLowerCase()) return '"Comisión base" está reservado para el costo por pago normal. Ponle otro nombre a la campaña.';
			var si = parseFloat(f.salario_ini), sf = parseFloat(f.salario_fin);
			if(isNaN(si) || isNaN(sf) || si < 0 || sf < 0) return 'La campaña especial necesita salario inicial y final.';
			if(si > sf) return 'El salario inicial no puede ser mayor al final.';
		}
		if(!/^\d+$/.test(String(f.puntos))) return 'El monto debe ser un número entero no negativo.';
		return '';
	}

	async function cptEnviar(g){
		var G = GRUPOS[g], f = {}, p = { action:G.aG, id:EDIT[g], tipo:G.tipo };
		G.cols.forEach(function(c){ var x = el(g, c.k); if(!x) return; f[c.k] = x.value.trim(); p[c.k] = f[c.k]; });
		var err = cptValidar(g, f);
		if(err){ Swal.fire('Revisa los datos', err, 'warning'); return; }
		mostrarLoaderCpt();
		var r = await cptApi(p);
		ocultarLoaderCpt();
		if(!r.ok){ Swal.fire('No se guardó', r.msg || '', 'error'); return; }
		await cptCargar();
		cptCancelar(g);
		Swal.fire({ icon:'success', title:'Guardado', timer:1000, showConfirmButton:false });
	}

	async function cptToggle(g, id, nuevo){
		var f = cptBuscar(id);
		if(!f) return;
		var r = await cptApi({ action:GRUPOS[g].aT, id:id, activo:nuevo });
		if(!r.ok){ Swal.fire('No se pudo cambiar', r.msg || '', 'error'); return; }
		f.activo = nuevo;
		pintarLista(g);
	}

	/* ══════════ Tabulador: una tabla pivote por esquema (salarios = columnas, edades = renglones) ══════════ */
	function tabMoney(n){ n = Number(n); return (n % 1000 === 0 && n !== 0) ? '$' + (n / 1000) + 'k' : '$' + n.toLocaleString('es-MX'); }
	function tabEtiqSal(ini, fin){
		if(fin === null || fin === '') return tabMoney(ini) + ' o más';
		if(Number(ini) === 0) return 'Hasta ' + tabMoney(fin);
		return tabMoney(ini) + '–' + tabMoney(fin);
	}
	function esqBuscar(id){ return CPT.tab.filter(function(e){ return e.id === id; })[0]; }
	function esqClon(e){ return JSON.parse(JSON.stringify(e)); }

	/* lista de esquemas */
	function pintarEsquemas(){
		var cont = document.getElementById('lst_t');
		if(!cont) return;
		if(!CPT.tab.length){ cont.innerHTML = '<div class="cpt-vacio">Aún no hay esquemas. Pulsa "+ Nuevo esquema".</div>'; return; }
		var h = '<table class="cpt-tabla"><thead><tr><th>Fecha inicial</th><th>Fecha final</th><th>Edades</th><th>Salarios</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>';
		CPT.tab.forEach(function(e){
			h += '<tr class="' + (e.activo ? '' : 'inactiva') + (TE && TE.id === e.id ? ' editando' : '') + '"><td>' + fmtF(e.fec_ini) + '</td><td>' + fmtF(e.fec_fin) + '</td>' +
			     '<td>' + e.edades.length + '</td><td>' + e.salarios.length + '</td>' +
			     '<td><span class="cpt-estatus ' + (e.activo ? 'on' : 'off') + '">' + (e.activo ? 'Activo' : 'Inactivo') + '</span></td>' +
			     '<td class="cpt-acc"><button class="cpt-btn chico" onclick="tabAbrir(' + e.id + ')">Ver</button> ' +
			     '<button class="cpt-btn chico" onclick="esqModificar(' + e.id + ')">Modificar</button> ' +
			     '<button class="cpt-btn chico" onclick="esqDuplicar(' + e.id + ')">Duplicar</button> ' +
			     '<button class="cpt-btn chico ' + (e.activo ? 'quitar' : 'ok') + '" onclick="esqToggle(' + e.id + ',' + (e.activo ? 0 : 1) + ')">' + (e.activo ? 'Desactivar' : 'Activar') + '</button></td></tr>';
		});
		cont.innerHTML = h + '</tbody></table>';
	}
	async function esqToggle(id, nuevo){
		var r = await cptApi({ action:'toggleTabulador', id:id, activo:nuevo });
		if(!r.ok){ Swal.fire('No se pudo cambiar', r.msg || '', 'error'); return; }
		var e = esqBuscar(id); if(e) e.activo = nuevo;
		pintarEsquemas();
	}

	/* editor (pivote editable) */
	function esqNuevo(){
		TE = { id:0, fec_ini:'', fec_fin:'', edades:[{ ini:18, fin:29 }], salarios:[{ ini:0, fin:10000 }], puntos:[[0]] };
		pintarEditor(); pintarEsquemas();
	}
	function esqModificar(id){ var e = esqBuscar(id); if(!e) return; TE = esqClon(e); pintarEditor(); pintarEsquemas(); }
	function esqDuplicar(id){
		var e = esqBuscar(id); if(!e) return;
		TE = esqClon(e); TE.id = 0;
		pintarEditor(); pintarEsquemas();
	}
	function esqCancelar(){ TE = null; pintarEditor(); pintarEsquemas(); }

	function teInp(cls, v, extra){ return '<input class="te-inp ' + cls + '" value="' + esc(v) + '" ' + (extra || '') + '>'; }

	function pintarEditor(){
		var host = document.getElementById('frm_t'), d = TE;
		if(!host) return;
		var modal = document.getElementById('esqModal');
		if(!d){ host.innerHTML = ''; modal.classList.remove('abierto'); return; }
		modal.classList.add('abierto');
		var h = '<div class="cpt-seccion-titulo" style="margin:0 0 12px !important">' + (d.id ? 'Modificar esquema completo' : 'Nuevo esquema') + '</div>';
		h += '<div class="cpt-form-fila">' +
		     '<div class="cpt-campo" style="width:160px"><label>Fecha inicial</label><input class="cpt-input" type="date" id="te_fi" value="' + esc(d.fec_ini) + '"></div>' +
		     '<div class="cpt-campo" style="width:160px"><label>Fecha final</label><input class="cpt-input" type="date" id="te_ff" value="' + esc(d.fec_fin) + '"></div></div>';
		h += '<div class="te-wrap"><table class="te-tabla" id="teTabla"><tr><th>Edad \\ Salario</th>';
		d.salarios.forEach(function(s, j){
			h += '<th><div class="te-th"><div class="te-th-top"><span>Salario ' + (j + 1) + '</span>' +
			     (d.salarios.length > 1 ? '<button type="button" class="te-mini" title="Quitar este salario (afecta todas las edades)" onclick="teQuitarCol(' + j + ')">×</button>' : '') + '</div>' +
			     '<label>Desde</label>' + teInp('t-si', s.ini, 'data-j="' + j + '" inputmode="decimal"') +
			     '<label>Hasta</label>' + teInp('t-sf', s.fin === null ? '' : s.fin, 'data-j="' + j + '" inputmode="decimal" required') + '</div></th>';
		});
		h += '<th class="te-ctl"><button type="button" class="te-mini ok" title="Agregar salario (afecta todas las edades)" onclick="teAgregarCol()">+</button></th></tr>';
		d.edades.forEach(function(e, i){
			h += '<tr><td>' + teInp('t-ei', e.ini, 'data-i="' + i + '" inputmode="numeric" style="width:48px"') + ' – ' + teInp('t-ef', e.fin, 'data-i="' + i + '" inputmode="numeric" style="width:48px"') + ' años' +
			     (d.edades.length > 1 ? ' <button type="button" class="te-mini" title="Quitar esta edad (afecta todos los salarios)" onclick="teQuitarFila(' + i + ')">×</button>' : '') + '</td>';
			d.salarios.forEach(function(s, j){ h += '<td>' + teInp('t-p', d.puntos[i][j], 'data-i="' + i + '" data-j="' + j + '" inputmode="numeric" style="width:70px"') + '</td>'; });
			h += '<td class="te-ctl"></td></tr>';
		});
		h += '<tr><td class="te-ctl" style="text-align:left !important"><button type="button" class="te-mini ok" title="Agregar edad (afecta todos los salarios)" onclick="teAgregarFila()">+ edad</button></td></tr></table></div>';
		h += '<div class="cpt-nota">Cada salario aplica a todas las edades y cada edad a todos los salarios. El salario inicial se incluye y el final no. "Desde" y "Hasta" son obligatorios en todos los salarios.</div>';
		h += '<div style="margin-top:12px;display:flex;gap:10px"><button type="button" class="cpt-btn" onclick="esqGuardar()">Guardar esquema</button><button type="button" class="cpt-btn" onclick="esqCancelar()">Cancelar</button></div>';
		host.innerHTML = h;
	}
	function teLeer(){
		var q = function(c){ return document.querySelectorAll('#frm_t .' + c); };
		TE.fec_ini = document.getElementById('te_fi').value;
		TE.fec_fin = document.getElementById('te_ff').value;
		q('t-ei').forEach(function(x){ TE.edades[x.dataset.i].ini = x.value.trim(); });
		q('t-ef').forEach(function(x){ TE.edades[x.dataset.i].fin = x.value.trim(); });
		q('t-si').forEach(function(x){ TE.salarios[x.dataset.j].ini = x.value.trim(); });
		q('t-sf').forEach(function(x){ TE.salarios[x.dataset.j].fin = x.value.trim() === '' ? null : x.value.trim(); });
		q('t-p').forEach(function(x){ TE.puntos[x.dataset.i][x.dataset.j] = x.value.trim(); });
	}
	function teAgregarFila(){
		teLeer();
		var u = TE.edades[TE.edades.length - 1], ini = u ? (parseInt(u.fin, 10) || 0) + 1 : 18;
		TE.edades.push({ ini:ini, fin:ini + 9 });
		TE.puntos.push(TE.salarios.map(function(){ return 0; }));
		pintarEditor();
	}
	function teQuitarFila(i){ teLeer(); TE.edades.splice(i, 1); TE.puntos.splice(i, 1); pintarEditor(); }
	function teAgregarCol(){
		teLeer();
		var u = TE.salarios[TE.salarios.length - 1];
		if(u && (u.fin === null || String(u.fin).trim() === '')) u.fin = Number(u.ini) + 10000;
		var ini = u ? Number(u.fin) : 0;
		TE.salarios.push({ ini:ini, fin:ini + 10000 });
		TE.puntos.forEach(function(f){ f.push(0); });
		pintarEditor();
	}
	function teQuitarCol(j){ teLeer(); TE.salarios.splice(j, 1); TE.puntos.forEach(function(f){ f.splice(j, 1); }); pintarEditor(); }

	function esqValidar(d){
		var i, j, prev = -1, pf = null, n = d.salarios.length;
		if(!d.fec_ini || !d.fec_fin) return 'Captura las fechas inicial y final.';
		if(d.fec_fin < d.fec_ini) return 'La fecha final no puede ser anterior a la inicial.';
		for(i = 0; i < d.edades.length; i++){
			var a = parseInt(d.edades[i].ini, 10), b = parseInt(d.edades[i].fin, 10);
			if(isNaN(a) || isNaN(b)) return 'Edad ' + (i + 1) + ': captura edades numéricas.';
			if(b < a) return 'Edad ' + (i + 1) + ': la edad final no puede ser menor a la inicial.';
			if(a <= prev) return 'Edad ' + (i + 1) + ': los rangos de edad no pueden traslaparse ni ir desordenados.';
			prev = b;
		}
		for(j = 0; j < n; j++){
			var si = Number(d.salarios[j].ini), sf = d.salarios[j].fin === null ? null : Number(d.salarios[j].fin);
			if(String(d.salarios[j].ini) === '' || isNaN(si) || si < 0) return 'Salario ' + (j + 1) + ': salario inicial inválido.';
			if(sf === null) return 'Salario ' + (j + 1) + ': el salario "Hasta" es obligatorio.';
			if(isNaN(sf) || sf <= si) return 'Salario ' + (j + 1) + ': el final debe ser mayor al inicial.';
			if(pf !== null && si < pf) return 'Salario ' + (j + 1) + ': los rangos de salario no pueden traslaparse ni ir desordenados.';
			pf = sf;
		}
		for(i = 0; i < d.puntos.length; i++) for(j = 0; j < n; j++){
			if(!/^\d{1,9}$/.test(String(d.puntos[i][j]))) return 'Puntos inválidos en la edad ' + (i + 1) + ', salario ' + (j + 1) + ' (entero no negativo).';
		}
		return '';
	}
	async function esqGuardar(){
		teLeer();
		var err = esqValidar(TE);
		if(err){ Swal.fire('Revisa los datos', err, 'warning'); return; }
		mostrarLoaderCpt();
		var r = await cptApi({ action:'guardarTabulador', id:TE.id, fec_ini:TE.fec_ini, fec_fin:TE.fec_fin, edades:TE.edades, salarios:TE.salarios, puntos:TE.puntos });
		ocultarLoaderCpt();
		if(!r.ok){ Swal.fire('No se guardó', r.msg || '', 'error'); return; }
		TE = null;
		await cptCargar();
		pintarEditor();
		Swal.fire({ icon:'success', title:'Esquema guardado', timer:1100, showConfirmButton:false });
	}

	/* ══════════ Switch: Comisión base / Bonos ══════════ */
	var MODO = 'base', BO_CARGADO = false;
	var BO = { esq: [], camp: [] };     // esquemas de bonos (con categorías) y campañas especiales de bonos
	var BE = null;                      // esquema de bonos que se está capturando / editando
	var BC_EDIT = 0;                    // id de la campaña de bonos en edición (0 = alta)

	function modoSet(m){
		MODO = m;
		document.getElementById('vistaBase').style.display  = (m === 'base')  ? '' : 'none';
		document.getElementById('vistaBonos').style.display = (m === 'bonos') ? '' : 'none';
		document.getElementById('swBase').className  = (m === 'base')  ? 'on' : '';
		document.getElementById('swBonos').className = (m === 'bonos') ? 'on' : '';
		document.getElementById('btnVerTab').style.display  = (m === 'base')  ? '' : 'none';
		document.getElementById('btnVerBono').style.display = (m === 'bonos') ? '' : 'none';
		if(m === 'bonos' && !BO_CARGADO) bonoCargar();
	}

	/* $555 sin decimales si es entero; con 2 decimales si no */
	function fmtM0(n){
		if(n === '' || n === null || n === undefined) return '';
		n = Number(n);
		return '$' + n.toLocaleString('es-MX', { minimumFractionDigits: (n % 1 === 0 ? 0 : 2), maximumFractionDigits: 2 });
	}
	function fmtMult(n){ return '×' + Number(n); }

	async function bonoCargar(){
		mostrarLoaderCpt();
		var r1 = await cptApi({ action:'listarBonos' });
		var r2 = await cptApi({ action:'listarBonoCampanas' });
		ocultarLoaderCpt();
		if(!r1.ok) Swal.fire('Bonos', r1.msg || 'No se pudieron cargar los bonos.', 'warning');
		else if(!r2.ok) Swal.fire('Campañas de bonos', r2.msg || 'No se pudieron cargar las campañas.', 'warning');
		BO.esq  = r1.esquemas || [];
		BO.camp = r2.filas || [];
		BO_CARGADO = !!(r1.ok && r2.ok);
		bonoPintarLista(); bcPintarLista();
	}

	/* ── lista de esquemas de bonos ── */
	function bonoBuscar(id){ return BO.esq.filter(function(e){ return e.id === id; })[0]; }
	function bonoClon(e){ return JSON.parse(JSON.stringify(e)); }
	function bonoVigente(){
		var hoy = hoyISO(), act = BO.esq.filter(function(e){ return e.activo === 1; }), i;
		for(i = 0; i < act.length; i++) if(act[i].fec_ini <= hoy && act[i].fec_fin >= hoy) return act[i];
		return act[0] || null;
	}
	function bonoPintarLista(){
		var cont = document.getElementById('lst_b');
		if(!cont) return;
		if(!BO.esq.length){ cont.innerHTML = '<div class="cpt-vacio">Aún no hay esquemas de bonos. Pulsa "+ Nuevo esquema".</div>'; return; }
		var h = '<table class="cpt-tabla"><thead><tr><th>Del</th><th>Al</th><th>Categorías</th><th>Élite +</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>';
		BO.esq.forEach(function(e){
			h += '<tr class="' + (e.activo ? '' : 'inactiva') + (BE && BE.id === e.id ? ' editando' : '') + '"><td>' + fmtF(e.fec_ini) + '</td><td>' + fmtF(e.fec_fin) + '</td>' +
			     '<td>' + e.cats.length + '</td><td>' + (e.elite ? 'Sí' : 'No') + '</td>' +
			     '<td><span class="cpt-estatus ' + (e.activo ? 'on' : 'off') + '">' + (e.activo ? 'Activo' : 'Inactivo') + '</span></td>' +
			     '<td class="cpt-acc"><button class="cpt-btn chico" onclick="bonoVer(' + e.id + ')">Ver</button> ' +
			     '<button class="cpt-btn chico" onclick="bonoModificar(' + e.id + ')">Modificar</button> ' +
			     '<button class="cpt-btn chico" onclick="bonoDuplicar(' + e.id + ')">Duplicar</button> ' +
			     '<button class="cpt-btn chico ' + (e.activo ? 'quitar' : 'ok') + '" onclick="bonoToggle(' + e.id + ',' + (e.activo ? 0 : 1) + ')">' + (e.activo ? 'Desactivar' : 'Activar') + '</button></td></tr>';
		});
		cont.innerHTML = h + '</tbody></table>';
	}
	async function bonoToggle(id, nuevo){
		var r = await cptApi({ action:'toggleBono', id:id, activo:nuevo });
		if(!r.ok){ Swal.fire('No se pudo cambiar', r.msg || '', 'error'); return; }
		var e = bonoBuscar(id); if(e) e.activo = nuevo;
		bonoPintarLista();
	}

	/* ── editor del esquema (modal) ── */
	function bonoPlantilla(){
		var c = function(n, a, b, x, y, z){ return { categoria:n, pts_ini:a, pts_fin:b, monto_categoria:x, monto_persistencia:y, monto_bono:z }; };
		return {
			id:0, fec_ini:'', fec_fin:'', pct_categoria:85, pct_persistencia:15,
			reglas:'',
			elite:{ cada:5, categoria:850, persistencia:150, bono:1000 },
			cats:[ c('Impulso',3,9,555,95,650), c('Máster',10,14,1190,210,1400), c('Persistente',15,19,1870,330,2200), c('Avanzado',20,24,2640,460,3100),
			       c('Referente',25,29,3490,610,4100), c('Admirable',30,34,4500,800,5300), c('Brillante',35,39,5570,980,6550),
			       c('Legendario',40,44,6800,1200,8000), c('Élite',45,49,8100,1400,9500) ]
		};
	}
	function bonoNuevo(){ BE = bonoPlantilla(); bonoPintarEditor(); bonoPintarLista(); }
	function bonoModificar(id){ var e = bonoBuscar(id); if(!e) return; BE = bonoClon(e); bonoPintarEditor(); bonoPintarLista(); }
	function bonoDuplicar(id){ var e = bonoBuscar(id); if(!e) return; BE = bonoClon(e); BE.id = 0; BE.fec_ini = ''; BE.fec_fin = ''; bonoPintarEditor(); bonoPintarLista(); }
	function bonoCancelar(){ BE = null; bonoPintarEditor(); bonoPintarLista(); }

	function boInp(cls, v, extra){ return '<input class="te-inp ' + cls + '" style="width:100%" value="' + esc(v) + '" ' + (extra || '') + '>'; }

	function bonoPintarEditor(){
		var host = document.getElementById('frm_b'), modal = document.getElementById('bonoModal'), d = BE;
		if(!host) return;
		if(!d){ host.innerHTML = ''; modal.classList.remove('abierto'); return; }
		modal.classList.add('abierto');
		var el_ = d.elite || { cada:'', categoria:'', persistencia:'', bono:'' };
		var h = '<div class="cpt-seccion-titulo" style="margin:0 0 12px !important">' + (d.id ? 'Modificar esquema de bonos' : 'Nuevo esquema de bonos') + '</div>';
		h += '<div class="cpt-form-fila">' +
		     '<div class="cpt-campo" style="width:160px"><label>Del</label><input class="cpt-input" type="date" id="b_fi" value="' + esc(d.fec_ini) + '"></div>' +
		     '<div class="cpt-campo" style="width:160px"><label>Al</label><input class="cpt-input" type="date" id="b_ff" value="' + esc(d.fec_fin) + '"></div>' +
		     '<div class="cpt-campo" style="width:130px"><label>% Categoría</label><input class="cpt-input" id="b_pc" inputmode="numeric" maxlength="3" value="' + esc(d.pct_categoria) + '"></div>' +
		     '<div class="cpt-campo" style="width:130px"><label>% Persistencia</label><input class="cpt-input" id="b_pp" inputmode="numeric" maxlength="3" value="' + esc(d.pct_persistencia) + '"></div></div>';

		h += '<div class="te-wrap"><table class="te-tabla" id="bTabla"><tr><th style="width:auto !important">Categoría</th><th style="width:90px">Puntos desde</th><th style="width:90px">Puntos hasta</th><th style="width:120px">$ Categoría</th><th style="width:120px">$ Persistencia</th><th style="width:120px">$ Bono semanal</th></tr>';
		d.cats.forEach(function(c, i){
			h += '<tr><td>' + boInp('b-cat', c.categoria, 'data-i="' + i + '" maxlength="60" readonly style="background:#f3f5f7"') + '</td>' +
			     '<td>' + boInp('b-pi', c.pts_ini, 'data-i="' + i + '" inputmode="numeric"') + '</td>' +
			     '<td>' + boInp('b-pf', c.pts_fin, 'data-i="' + i + '" inputmode="numeric"') + '</td>' +
			     '<td>' + boInp('b-mc', c.monto_categoria, 'data-i="' + i + '" inputmode="decimal"') + '</td>' +
			     '<td>' + boInp('b-mp', c.monto_persistencia, 'data-i="' + i + '" inputmode="decimal"') + '</td>' +
			     '<td>' + boInp('b-mb', c.monto_bono, 'data-i="' + i + '" inputmode="decimal"') + '</td>' +
			     '</tr>';
		});
		h += '</table></div>';

		h += '<div class="cpt-seccion-titulo" style="margin:16px 0 6px !important;font-size:14px !important">Élite + (sin tope)</div>' +
		     '<div class="cpt-form-fila">' +
		     '<div class="cpt-campo" style="width:150px"><label>Por cada (puntos)</label><input class="cpt-input" id="b_ecada" inputmode="numeric" maxlength="4" value="' + esc(el_.cada) + '"></div>' +
		     '<div class="cpt-campo" style="width:150px"><label>+ $ Categoría</label><input class="cpt-input" id="b_ecat" inputmode="decimal" value="' + esc(el_.categoria) + '"></div>' +
		     '<div class="cpt-campo" style="width:150px"><label>+ $ Persistencia</label><input class="cpt-input" id="b_eper" inputmode="decimal" value="' + esc(el_.persistencia) + '"></div>' +
		     '<div class="cpt-campo" style="width:150px"><label>+ $ Bono semanal</label><input class="cpt-input" id="b_ebon" inputmode="decimal" value="' + esc(el_.bono) + '"></div></div>' +
		     '<div class="cpt-nota" style="margin-top:0">Déjalo todo vacío si este esquema no tiene fila "Élite +".</div>';

		h += '<div style="margin-top:14px;display:flex;gap:10px"><button type="button" class="cpt-btn" onclick="bonoGuardar()">Guardar esquema</button><button type="button" class="cpt-btn" onclick="bonoCancelar()">Cancelar</button></div>';
		host.innerHTML = h;
	}
	function bonoLeer(){
		var q = function(c){ return document.querySelectorAll('#frm_b .' + c); };
		BE.fec_ini = document.getElementById('b_fi').value;
		BE.fec_fin = document.getElementById('b_ff').value;
		BE.pct_categoria = document.getElementById('b_pc').value.trim();
		BE.pct_persistencia = document.getElementById('b_pp').value.trim();
		var ec = document.getElementById('b_ecada').value.trim(), e1 = document.getElementById('b_ecat').value.trim(),
		    e2 = document.getElementById('b_eper').value.trim(), e3 = document.getElementById('b_ebon').value.trim();
		BE.elite = (ec === '' && e1 === '' && e2 === '' && e3 === '') ? null : { cada:ec, categoria:e1, persistencia:e2, bono:e3 };
		q('b-cat').forEach(function(x){ BE.cats[x.dataset.i].categoria = x.value.trim(); });
		q('b-pi').forEach(function(x){ BE.cats[x.dataset.i].pts_ini = x.value.trim(); });
		q('b-pf').forEach(function(x){ BE.cats[x.dataset.i].pts_fin = x.value.trim(); });
		q('b-mc').forEach(function(x){ BE.cats[x.dataset.i].monto_categoria = x.value.trim(); });
		q('b-mp').forEach(function(x){ BE.cats[x.dataset.i].monto_persistencia = x.value.trim(); });
		q('b-mb').forEach(function(x){ BE.cats[x.dataset.i].monto_bono = x.value.trim(); });
	}

	function boMonto(v){ var s = String(v === null || v === undefined ? '' : v).replace(/,/g, '').trim(); return /^\d{1,10}(\.\d{1,2})?$/.test(s); }
	function bonoValidar(d){
		var i, prev = -1;
		if(!d.fec_ini || !d.fec_fin) return 'Captura las fechas "Del" y "Al".';
		if(d.fec_fin < d.fec_ini) return 'La fecha final no puede ser anterior a la inicial.';
		if(!/^\d{1,3}$/.test(String(d.pct_categoria)) || !/^\d{1,3}$/.test(String(d.pct_persistencia)) || +d.pct_categoria > 100 || +d.pct_persistencia > 100) return 'Los porcentajes deben ser enteros entre 0 y 100.';
		if(!d.cats.length) return 'El esquema necesita al menos una categoría.';
		for(i = 0; i < d.cats.length; i++){
			var c = d.cats[i], n = 'Categoría ' + (i + 1) + ': ';
			if(!c.categoria) return n + 'captura el nombre.';
			if(!/^\d{1,4}$/.test(String(c.pts_ini)) || !/^\d{1,4}$/.test(String(c.pts_fin))) return n + 'los puntos deben ser enteros.';
			var a = parseInt(c.pts_ini, 10), b = parseInt(c.pts_fin, 10);
			if(b < a) return n + 'los puntos "hasta" no pueden ser menores a "desde".';
			if(a <= prev) return n + 'los rangos de puntos no pueden traslaparse ni ir desordenados.';
			prev = b;
			if(!boMonto(c.monto_categoria) || !boMonto(c.monto_persistencia) || !boMonto(c.monto_bono)) return n + 'los montos deben ser importes válidos (no negativos, hasta 2 decimales).';
		}
		if(d.elite){
			if(!/^\d{1,4}$/.test(String(d.elite.cada)) || +d.elite.cada < 1) return 'Élite +: captura cada cuántos puntos se suma el incremento (entero mayor a 0).';
			if(!boMonto(d.elite.categoria) || !boMonto(d.elite.persistencia) || !boMonto(d.elite.bono)) return 'Élite +: los tres montos deben ser importes válidos.';
		}
		return '';
	}
	async function bonoGuardar(){
		bonoLeer();
		var err = bonoValidar(BE);
		if(err){ Swal.fire('Revisa los datos', err, 'warning'); return; }
		mostrarLoaderCpt();
		var r = await cptApi({ action:'guardarBono', id:BE.id, fec_ini:BE.fec_ini, fec_fin:BE.fec_fin, pct_categoria:BE.pct_categoria, pct_persistencia:BE.pct_persistencia,
		                        reglas:'', elite:BE.elite || {}, cats:BE.cats });
		ocultarLoaderCpt();
		if(!r.ok){ Swal.fire('No se guardó', r.msg || '', 'error'); return; }
		BE = null;
		await bonoCargar();
		bonoPintarEditor();
		Swal.fire({ icon:'success', title:'Esquema guardado', timer:1100, showConfirmButton:false });
	}

	/* ── vista del bono (como la imagen de referencia) ── */
	function bonoVer(id){
		var e = id ? bonoBuscar(id) : bonoVigente(), cont = document.getElementById('bonoVerCont'), h = '';
		if(!e){
			cont.innerHTML = '<div class="cpt-vacio">No hay un esquema de bonos activo.</div>';
		} else {
			var hoy = hoyISO();
			var vig = BO.camp.filter(function(c){ return c.activo === 1 && c.fec_ini <= hoy && c.fec_fin >= hoy; });
			h += '<div class="bv-kicker">Comisionista · Bono de cumplimiento</div>' +
			     '<div class="bv-tit">Bono semanal desde ' + (e.cats.length ? e.cats[0].pts_ini : '') + ' puntos</div>' +
			     '<div class="cpt-nota" style="margin:-8px 0 14px">Vigencia: ' + fmtF(e.fec_ini) + ' al ' + fmtF(e.fec_fin) + (e.activo ? '' : ' · <b>INACTIVO</b>') + '</div>';
			vig.forEach(function(c){ h += '<div class="bv-camp">Campaña vigente: <b>' + esc(c.campana) + '</b> ' + fmtMult(c.multiplicador) + ' (el bono se paga ' + fmtMult(c.multiplicador) + ')</div>'; });
			h += '<div class="bv-wrap"><table id="bvTabla"><thead><tr><th>Categoría</th><th class="c">Puntos por contactos</th><th>Categoría (' + e.pct_categoria + '%)</th><th>Persistencia (' + e.pct_persistencia + '%)</th><th>Bono semanal</th></tr></thead><tbody>';
			e.cats.forEach(function(c){
				var n = String(c.categoria);
				h += '<tr><td><b>' + esc(n.charAt(0)) + '</b>' + esc(n.substring(1)) + '</td><td class="c">' + c.pts_ini + '–' + c.pts_fin + '</td>' +
				     '<td>' + fmtM0(c.monto_categoria) + '</td><td>' + fmtM0(c.monto_persistencia) + '</td><td>' + fmtM0(c.monto_bono) + '</td></tr>';
			});
			if(e.elite){
				h += '<tr class="bv-elite"><td>Élite +</td><td class="c">+' + e.elite.cada + ' puntos</td><td>+' + fmtM0(e.elite.categoria) + '</td><td>+' + fmtM0(e.elite.persistencia) + '</td><td>+' + fmtM0(e.elite.bono) + '</td></tr>';
			}
			h += '</tbody></table>';
			h += '</div>';
			cont.innerHTML = h;
		}
		document.getElementById('bonoVerModal').classList.add('abierto');
	}
	function bonoVerCerrar(){ document.getElementById('bonoVerModal').classList.remove('abierto'); }

	/* ── campañas especiales de bonos (nombre, Del/Al, multiplicador) ── */
	function bcPintarForm(){
		var cont = document.getElementById('frm_bc');
		if(!cont) return;
		cont.innerHTML =
			'<div class="cpt-form-fila">' +
			'<div class="cpt-campo" style="width:260px"><label>Campaña</label><input class="cpt-input" id="bc_camp" maxlength="100" autocomplete="off" placeholder="Ej. Semana Embrujada"></div>' +
			'<div class="cpt-campo" style="width:160px"><label>Del</label><input class="cpt-input" type="date" id="bc_ini"></div>' +
			'<div class="cpt-campo" style="width:160px"><label>Al</label><input class="cpt-input" type="date" id="bc_fin"></div>' +
			'<div class="cpt-campo" style="width:130px"><label>Multiplicador</label><input class="cpt-input" id="bc_mul" inputmode="decimal" maxlength="5" placeholder="Ej. 2"></div>' +
			'<div class="cpt-campo cpt-btns"><label>&nbsp;</label><div><button type="button" class="cpt-btn" id="btn_bc" title="Agregar" onclick="bcEnviar()">+</button> ' +
			'<button type="button" class="cpt-btn chico" id="can_bc" style="display:none" onclick="bcCancelar()">Cancelar</button></div></div></div>' +
			'<div class="cpt-editando" id="ed_bc" style="display:none"></div>';
		cont.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); bcEnviar(); } });
		document.getElementById('bc_mul').addEventListener('input', function(){ var v = this.value.replace(/[^0-9.]/g, ''); if(this.value !== v) this.value = v; });
	}
	function bcPintarLista(){
		var cont = document.getElementById('lst_bc');
		if(!cont) return;
		if(!BO.camp.length){ cont.innerHTML = '<div class="cpt-vacio">Aún no hay campañas especiales de bonos.</div>'; return; }
		var h = '<table class="cpt-tabla"><thead><tr><th>Campaña</th><th>Del</th><th>Al</th><th>Multiplicador</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>';
		BO.camp.forEach(function(c){
			h += '<tr class="' + (c.activo ? '' : 'inactiva') + (BC_EDIT === c.id ? ' editando' : '') + '"><td>' + esc(c.campana) + '</td><td>' + fmtF(c.fec_ini) + '</td><td>' + fmtF(c.fec_fin) + '</td><td>' + fmtMult(c.multiplicador) + '</td>' +
			     '<td><span class="cpt-estatus ' + (c.activo ? 'on' : 'off') + '">' + (c.activo ? 'Activo' : 'Inactivo') + '</span></td>' +
			     '<td class="cpt-acc"><button class="cpt-btn chico" onclick="bcModificar(' + c.id + ')">Modificar</button> ' +
			     '<button class="cpt-btn chico ' + (c.activo ? 'quitar' : 'ok') + '" onclick="bcToggle(' + c.id + ',' + (c.activo ? 0 : 1) + ')">' + (c.activo ? 'Desactivar' : 'Activar') + '</button></td></tr>';
		});
		cont.innerHTML = h + '</tbody></table>';
	}
	function bcModo(c){
		BC_EDIT = c ? c.id : 0;
		document.getElementById('btn_bc').textContent = c ? 'Guardar' : '+';
		document.getElementById('btn_bc').title = c ? 'Guardar cambios' : 'Agregar';
		document.getElementById('can_bc').style.display = c ? '' : 'none';
		var ed = document.getElementById('ed_bc');
		ed.style.display = c ? '' : 'none';
		ed.textContent = c ? 'Modificando una campaña existente. Guarda los cambios o cancela.' : '';
		bcPintarLista();
	}
	function bcModificar(id){
		var c = BO.camp.filter(function(x){ return x.id === id; })[0];
		if(!c) return;
		document.getElementById('bc_camp').value = c.campana;
		document.getElementById('bc_ini').value = c.fec_ini;
		document.getElementById('bc_fin').value = c.fec_fin;
		document.getElementById('bc_mul').value = Number(c.multiplicador);
		bcModo(c);
		document.getElementById('bc_camp').scrollIntoView({ behavior:'smooth', block:'center' });
	}
	function bcCancelar(){
		['bc_camp','bc_ini','bc_fin','bc_mul'].forEach(function(k){ document.getElementById(k).value = ''; });
		bcModo(null);
	}
	async function bcEnviar(){
		var f = { campana: document.getElementById('bc_camp').value.trim(), fec_ini: document.getElementById('bc_ini').value,
		          fec_fin: document.getElementById('bc_fin').value, multiplicador: document.getElementById('bc_mul').value.trim() };
		var err = '';
		if(!f.campana) err = 'Captura el nombre de la campaña.';
		else if(!f.fec_ini || !f.fec_fin) err = 'Captura las fechas "Del" y "Al".';
		else if(f.fec_fin < f.fec_ini) err = 'La fecha "Al" no puede ser anterior a "Del".';
		else if(!/^\d{1,2}(\.\d{1,2})?$/.test(f.multiplicador) || parseFloat(f.multiplicador) <= 0) err = 'El multiplicador debe ser un número mayor a 0 (ej. 2 para pagar al doble).';
		if(err){ Swal.fire('Revisa los datos', err, 'warning'); return; }
		mostrarLoaderCpt();
		var r = await cptApi({ action:'guardarBonoCampana', id:BC_EDIT, campana:f.campana, fec_ini:f.fec_ini, fec_fin:f.fec_fin, multiplicador:f.multiplicador });
		ocultarLoaderCpt();
		if(!r.ok){ Swal.fire('No se guardó', r.msg || '', 'error'); return; }
		await bonoCargar();
		bcCancelar();
		Swal.fire({ icon:'success', title:'Guardado', timer:1000, showConfirmButton:false });
	}
	async function bcToggle(id, nuevo){
		var r = await cptApi({ action:'toggleBonoCampana', id:id, activo:nuevo });
		if(!r.ok){ Swal.fire('No se pudo cambiar', r.msg || '', 'error'); return; }
		var c = BO.camp.filter(function(x){ return x.id === id; })[0]; if(c) c.activo = nuevo;
		bcPintarLista();
	}


	/* ══════════ Tabla de inactivos: puntos por rango de edad (un esquema = vigencia + una fila "Puntos") ══════════ */
	var IE = null;      // esquema de inactivos que se está capturando / editando
	function inaBuscar(id){ return CPT.ina.filter(function(e){ return e.id === id; })[0]; }
	function inaClon(e){ return JSON.parse(JSON.stringify(e)); }

	function pintarInactivos(){
		var cont = document.getElementById('lst_i');
		if(!cont) return;
		if(!CPT.ina.length){ cont.innerHTML = '<div class="cpt-vacio">Aún no hay esquemas de inactivos. Pulsa "+ Nuevo esquema".</div>'; return; }
		var h = '<table class="cpt-tabla"><thead><tr><th>Del</th><th>Al</th><th>Rangos de edad</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>';
		CPT.ina.forEach(function(e){
			h += '<tr class="' + (e.activo ? '' : 'inactiva') + (IE && IE.id === e.id ? ' editando' : '') + '"><td>' + fmtF(e.fec_ini) + '</td><td>' + fmtF(e.fec_fin) + '</td><td>' + e.edades.length + '</td>' +
			     '<td><span class="cpt-estatus ' + (e.activo ? 'on' : 'off') + '">' + (e.activo ? 'Activo' : 'Inactivo') + '</span></td>' +
			     '<td class="cpt-acc"><button class="cpt-btn chico" onclick="inaVer(' + e.id + ')">Ver</button> ' +
			     '<button class="cpt-btn chico" onclick="inaModificar(' + e.id + ')">Modificar</button> ' +
			     '<button class="cpt-btn chico" onclick="inaDuplicar(' + e.id + ')">Duplicar</button> ' +
			     '<button class="cpt-btn chico ' + (e.activo ? 'quitar' : 'ok') + '" onclick="inaToggle(' + e.id + ',' + (e.activo ? 0 : 1) + ')">' + (e.activo ? 'Desactivar' : 'Activar') + '</button></td></tr>';
		});
		cont.innerHTML = h + '</tbody></table>';
	}
	async function inaToggle(id, nuevo){
		var r = await cptApi({ action:'toggleInactivo', id:id, activo:nuevo });
		if(!r.ok){ Swal.fire('No se pudo cambiar', r.msg || '', 'error'); return; }
		var e = inaBuscar(id); if(e) e.activo = nuevo;
		pintarInactivos();
	}
	function inaNuevo(){ IE = { id:0, fec_ini:'', fec_fin:'', edades:[{ ini:18, fin:29 }], puntos:[0] }; inaPintarEditor(); pintarInactivos(); }
	function inaModificar(id){ var e = inaBuscar(id); if(!e) return; IE = inaClon(e); inaPintarEditor(); pintarInactivos(); }
	function inaDuplicar(id){ var e = inaBuscar(id); if(!e) return; IE = inaClon(e); IE.id = 0; inaPintarEditor(); pintarInactivos(); }
	function inaCancelar(){ IE = null; inaPintarEditor(); pintarInactivos(); }

	function inaPintarEditor(){
		var host = document.getElementById('frm_i'), modal = document.getElementById('inaModal'), d = IE;
		if(!host) return;
		if(!d){ host.innerHTML = ''; modal.classList.remove('abierto'); return; }
		modal.classList.add('abierto');
		var h = '<div class="cpt-seccion-titulo" style="margin:0 0 12px !important">' + (d.id ? 'Modificar tabla de inactivos' : 'Nueva tabla de inactivos') + '</div>';
		h += '<div class="cpt-nota" style="margin:-6px 0 12px">Cliente inactivo (sin aportar 12 meses)</div>';
		h += '<div class="cpt-form-fila">' +
		     '<div class="cpt-campo" style="width:160px"><label>Del</label><input class="cpt-input" type="date" id="in_fi" value="' + esc(d.fec_ini) + '"></div>' +
		     '<div class="cpt-campo" style="width:160px"><label>Al</label><input class="cpt-input" type="date" id="in_ff" value="' + esc(d.fec_fin) + '"></div></div>';
		h += '<div class="te-wrap"><table class="te-tabla" id="inaTabla"><tr><th>Edad</th>';
		d.edades.forEach(function(e, i){
			h += '<th><div class="te-th"><div class="te-th-top"><span>Rango ' + (i + 1) + '</span>' +
			     (d.edades.length > 1 ? '<button type="button" class="te-mini" title="Quitar este rango" onclick="inaQuitar(' + i + ')">×</button>' : '') + '</div>' +
			     '<label>Desde</label>' + teInp('i-ei', e.ini, 'data-i="' + i + '" inputmode="numeric" maxlength="3"') +
			     '<label>Hasta</label>' + teInp('i-ef', e.fin, 'data-i="' + i + '" inputmode="numeric" maxlength="3"') + '</div></th>';
		});
		h += '<th class="te-ctl"><button type="button" class="te-mini ok" title="Agregar rango de edad" onclick="inaAgregar()">+</button></th></tr>';
		h += '<tr><td>Puntos</td>';
		d.edades.forEach(function(e, i){ h += '<td>' + teInp('i-p', d.puntos[i], 'data-i="' + i + '" inputmode="numeric" maxlength="9" style="width:80px"') + '</td>'; });
		h += '<td class="te-ctl"></td></tr></table></div>';
		h += '<div class="cpt-nota">Cada rango de edad lleva sus puntos. Los rangos no pueden traslaparse.</div>';
		h += '<div style="margin-top:12px;display:flex;gap:10px"><button type="button" class="cpt-btn" onclick="inaGuardar()">Guardar esquema</button><button type="button" class="cpt-btn" onclick="inaCancelar()">Cancelar</button></div>';
		host.innerHTML = h;
	}
	function inaLeer(){
		var q = function(c){ return document.querySelectorAll('#frm_i .' + c); };
		IE.fec_ini = document.getElementById('in_fi').value;
		IE.fec_fin = document.getElementById('in_ff').value;
		q('i-ei').forEach(function(x){ IE.edades[x.dataset.i].ini = x.value.trim(); });
		q('i-ef').forEach(function(x){ IE.edades[x.dataset.i].fin = x.value.trim(); });
		q('i-p').forEach(function(x){ IE.puntos[x.dataset.i] = x.value.trim(); });
	}
	function inaAgregar(){
		inaLeer();
		var u = IE.edades[IE.edades.length - 1], ini = u ? (parseInt(u.fin, 10) || 0) + 1 : 18;
		IE.edades.push({ ini:ini, fin:ini + 9 });
		IE.puntos.push(0);
		inaPintarEditor();
	}
	function inaQuitar(i){ inaLeer(); IE.edades.splice(i, 1); IE.puntos.splice(i, 1); inaPintarEditor(); }
	function inaValidar(d){
		var i, prev = -1;
		if(!d.fec_ini || !d.fec_fin) return 'Captura las fechas inicial y final.';
		if(d.fec_fin < d.fec_ini) return 'La fecha final no puede ser anterior a la inicial.';
		if(!d.edades.length) return 'El esquema necesita al menos un rango de edad.';
		for(i = 0; i < d.edades.length; i++){
			var a = parseInt(d.edades[i].ini, 10), b = parseInt(d.edades[i].fin, 10);
			if(isNaN(a) || isNaN(b)) return 'Rango ' + (i + 1) + ': captura edades numéricas.';
			if(b < a) return 'Rango ' + (i + 1) + ': la edad final no puede ser menor a la inicial.';
			if(a <= prev) return 'Rango ' + (i + 1) + ': los rangos de edad no pueden traslaparse ni ir desordenados.';
			prev = b;
			if(!/^\d{1,9}$/.test(String(d.puntos[i]))) return 'Rango ' + (i + 1) + ': los puntos deben ser un entero no negativo.';
		}
		return '';
	}
	async function inaGuardar(){
		inaLeer();
		var err = inaValidar(IE);
		if(err){ Swal.fire('Revisa los datos', err, 'warning'); return; }
		mostrarLoaderCpt();
		var r = await cptApi({ action:'guardarInactivo', id:IE.id, fec_ini:IE.fec_ini, fec_fin:IE.fec_fin, edades:IE.edades, puntos:IE.puntos });
		ocultarLoaderCpt();
		if(!r.ok){ Swal.fire('No se guardó', r.msg || '', 'error'); return; }
		IE = null;
		await cptCargar();
		inaPintarEditor();
		Swal.fire({ icon:'success', title:'Esquema guardado', timer:1100, showConfirmButton:false });
	}
	/* "Ver": reutiliza el modal grande del tabulador */
	function inaVer(id){
		var e = inaBuscar(id), tabla = document.getElementById('tabTabla');
		if(!e) return;
		tabla.innerHTML = '<tr><th>Edad</th>' + e.edades.map(function(a){ return '<th>' + a.ini + '–' + a.fin + '</th>'; }).join('') + '</tr>' +
		                  '<tr><td>Puntos</td>' + e.puntos.map(function(p){ return '<td>' + p + '</td>'; }).join('') + '</tr>';
		document.getElementById('tabModal').classList.add('abierto');
	}

	/* modal: solo la tabla */
	function tabEsquemaVigente(){
		var hoy = hoyISO(), act = CPT.tab.filter(function(e){ return e.activo === 1; }), i;
		for(i = 0; i < act.length; i++) if(act[i].fec_ini <= hoy && act[i].fec_fin >= hoy) return act[i];
		return act[0] || null;
	}
	function tabAbrir(id){
		tabPintar(id ? esqBuscar(id) : tabEsquemaVigente());
		document.getElementById('tabModal').classList.add('abierto');
	}
	function tabPintar(e){
		var tabla = document.getElementById('tabTabla');
		if(!e){ tabla.innerHTML = '<tr><td style="text-align:center !important">No hay un esquema activo.</td></tr>'; return; }
		var h = '<tr><th>Edad \\ Salario</th>' + e.salarios.map(function(s){ return '<th>' + esc(tabEtiqSal(s.ini, s.fin)) + '</th>'; }).join('') + '</tr>';
		e.edades.forEach(function(a, i){
			h += '<tr><td>' + a.ini + '–' + a.fin + ' años</td>' + e.salarios.map(function(s, j){ return '<td>' + e.puntos[i][j] + '</td>'; }).join('') + '</tr>';
		});
		tabla.innerHTML = h;
	}
	function tabCerrar(){ document.getElementById('tabModal').classList.remove('abierto'); }
	document.addEventListener('keydown', function(e){ if(e.key === 'Escape'){ tabCerrar(); if(TE) esqCancelar(); if(IE) inaCancelar(); if(BE) bonoCancelar(); bonoVerCerrar(); } });

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		var elSalir = document.getElementById('salir');
		if(elSalir) elSalir.addEventListener('click', function(){ window.location = 'https://www.sis-ca.com.mx:7863/'; });

		var elEmail = document.getElementById('email');
		if(elEmail) elEmail.addEventListener('click', function(){ window.open('http://webmail.carobra.com.mx/imp/login.php'); });

		if(document.getElementById('frm_n')){
			['n','c'].forEach(pintarForm);
			bcPintarForm();
			cptCargar();
		}
	});
	</script>
</html>