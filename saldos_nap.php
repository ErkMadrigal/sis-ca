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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

		<style>
            /* =========================
            FILTROS  (mismo estilo que facturas_nap_estructura.php)
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
            .filtros-wrapper .row.align-items-end > [class*="col"] {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }

            /* =========================
            TABLA PRINCIPAL
            ========================= */
            .tabla-facturas-wrapper {
                width: 100%;
                overflow-x: auto;
                overflow-y: auto;
                border: 1px solid #e8edf3;
                border-radius: 14px;
                background: #fff;
                margin-top: 16px;
            }
            .tabla-facturas {
                width: 100%;
                min-width: 1150px;
                border-collapse: collapse;
                background: #fff;
            }
            .tabla-facturas thead tr {
                background: #f4f7fb;
                border-bottom: 1px solid #e8edf3;
            }
            .tabla-facturas thead th {
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
            .tabla-facturas tbody td {
                padding: 6px 7px;
                text-align: left;
                vertical-align: middle;
                font-size: 11px;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
            }
            .tabla-facturas tbody tr:last-child td { border-bottom: none; }
            .tabla-facturas tbody tr:hover { background: #f7faff; }

            .td-persona { font-weight: 500; color: #1f2d3d; white-space: nowrap; }
            .td-puntos   { font-weight: 700; color: #0F6E56; font-variant-numeric: tabular-nums; font-size: 13px !important; }
            .td-folio    { font-weight: 600; color: #1f2d3d; }

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
            .btn-ver-folios {
                border: none; background: #e8f0fa; color: #185FA5; border-radius: 6px;
                font-size: 11px; font-weight: 600; padding: 3px 10px; cursor: pointer; white-space: nowrap;
            }
            .btn-ver-folios:hover { background: #d3e3f5; }

            /* =========================
            SWITCH SC / BROCKER / GERENCIA
            ========================= */
            .filtro-switch-group {
                display: inline-flex;
                border: 1px solid #dce3eb;
                border-radius: 8px;
                overflow: hidden;
            }
            .filtro-switch-btn {
                border: none;
                background: #fafbfc;
                color: #7b8794;
                font-size: 12px;
                font-weight: 600;
                padding: 7px 16px;
                cursor: pointer;
                transition: background .15s, color .15s;
            }
            .filtro-switch-btn + .filtro-switch-btn { border-left: 1px solid #dce3eb; }
            .filtro-switch-btn.active { background: #185FA5; color: #fff; }

            /* =========================
            RESUMEN + FOLIOS
            ========================= */
            .sn-cards { display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0 0; }
            .sn-card { background: #fff; border: 1px solid #e8edf3; border-radius: 12px; padding: 8px 16px; min-width: 150px; text-align: left; }
            .sn-card span { display: block; font-size: 10.5px; color: #7b8794; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
            .sn-card b { display: block; font-size: 18px; color: #185FA5; margin-top: 1px; }
            .sn-card small { color: #8896a6; font-size: 11px; }
            .td-detalle { background: #f7faff !important; }
            .sn-folios { display: flex; flex-wrap: wrap; gap: 6px; padding: 4px 2px; }
            .sn-folio { background: #fff; border: 1px solid #dce3eb; border-radius: 6px; padding: 3px 9px; font-size: 11px; color: #1f2d3d; }
            .sn-folio b { color: #185FA5; margin-right: 4px; }
            /* MODAL DETALLE */
            .det-backdrop { display:none; position:fixed; inset:0; z-index:1000000; background:rgba(15,32,68,.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:24px; }
            .det-backdrop.open { display:flex; }
            .det-modal { background:#fff; border-radius:16px; box-shadow:0 24px 64px rgba(0,0,0,.22); width:100%; max-width:1280px; height:86vh; display:flex; flex-direction:column; overflow:hidden; }
            .det-header { background:#185FA5; padding:14px 22px; display:flex; align-items:center; justify-content:space-between; }
            .det-header h3 { color:#fff; font-size:15px; font-weight:600; margin:0; }
            .det-sub { color:rgba(255,255,255,.8); font-size:11.5px; margin-top:2px; }
            .det-close { background:rgba(255,255,255,.15); border:none; color:#fff; width:28px; height:28px; border-radius:6px; cursor:pointer; font-size:16px; }
            .det-toolbar { padding:10px 22px; border-bottom:1px solid #e8edf3; background:#f9fbfd; }
            .det-toolbar { display:flex; gap:12px; align-items:center; }
            .det-toolbar input { max-width:380px; }
            .btn-exportar { border:none; background:#0F6E56; color:#fff; border-radius:8px; font-size:12.5px; font-weight:600; padding:7px 16px; cursor:pointer; white-space:nowrap; }
            .btn-exportar:hover { background:#0B5642; }
            .btn-exportar:disabled { background:#c9ced4; cursor:not-allowed; }
            .det-body { flex:1; overflow:auto; }
            .tabla-det { width:100%; border-collapse:collapse; min-width:1100px; }
            .tabla-det thead th { position:sticky; top:0; z-index:2; background:#f4f7fb; color:#7b8794; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; padding:8px 10px; text-align:left; white-space:nowrap; border-bottom:1px solid #e8edf3; }
            .tabla-det tbody td { padding:7px 10px; font-size:12px; color:#1f2d3d; text-align:left; border-bottom:1px solid #f0f4f8; vertical-align:middle; }
            .tabla-det tbody tr:hover { background:#f7faff; }
            @media (max-width:768px) { .det-modal { max-width:100vw; height:100vh; border-radius:0; } }
            .sn-nota { font-size: 11.5px; color: #8896a6; margin: 8px 4px 0; line-height: 1.5; text-align: left; }
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
		$sesion_login = false;
		if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			exit;
		}
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
				titulo("PUNTOS POR NAP");
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

				$conecta = new conector();
				$nombre  = $_SESSION["nombre"];
				$nivel   = nivel_usuario(utf8_decode($nombre));
	?>

	<div class="container">
		<div class="row justify-content-center mt-0 pt-0">
			<div class="col-md-12 mt-0 pt-0">

				<!-- FILTROS -->
				<div class="filtros-wrapper">
					<div class="row align-items-end m-0 p-0">

						<!-- Semana (fec_cierre_campana, desde Periodo 12 semana 1) -->
						<div class="col-md-3 col-3 mb-3 text-left">
							<label for="sel-semana">Semana</label>
							<select name="sel-semana" id="sel-semana" class="form-control form-control-sm carobra"></select>
						</div>

						<!-- NAP -->
						<div class="col-md-5 col-5 mb-3" style="text-align: left;">
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
							<button class="btn-filtro-buscar w-100" id="buscar" type="button">
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
						<h4 style="margin:0;">PUNTOS POR NAP Y SEMANA</h4>
						<button type="button" class="btn-exportar" id="btn-exportar" disabled onclick="exportarResultado()">&#11015; Exportar XLSX</button>
					</div>

					<div class="tabla-facturas-wrapper">
						<table class="tabla-facturas table-striped">
							<thead>
								<tr>
									<th>Semana</th>
									<th>SC</th>
									<th>Brocker</th>
									<th>Gerencia</th>
									<th>NAP</th>
									<th>Folios</th>
									<th>Rechazados</th>
									<th>Otro estatus</th>
									<th>Aceptados procesar</th>
									<th>Certificados</th>
									<th>Puntos aprox.</th>
									<th>Categoría</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="body-puntos-nap">
								<tr>
									<td colspan="13" class="text-center text-muted" style="padding:30px;font-size:13px;">
										Busca por NAP, SC, Brocker o Gerencia (con la semana) para consultar
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="sn-nota">Cada folio suma según su estatus y los puntos aproximados salen de dividir esa suma entre 100,000 (redondeo normal: 4.5 → 5; 4.4 → 4). Categoría según el esquema de bonos vigente de cada semana.</div>
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

	<!-- MODAL DETALLE DE FOLIOS -->
	<div class="det-backdrop" id="detBackdrop">
		<div class="det-modal">
			<div class="det-header">
				<div>
					<h3>Detalle de folios</h3>
					<div class="det-sub" id="detSub"></div>
				</div>
				<button class="det-close" id="detClose" type="button">✕</button>
			</div>
			<div class="det-body">
				<table class="tabla-det">
					<thead id="detHead"></thead>
					<tbody id="detBody"></tbody>
				</table>
			</div>
		</div>
	</div>

	<?php } ?>

	</body>

	<script type="text/javascript">

    const SN_API = "saldos_naps_api.php";
    var SN_RESULT = null;

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

        ajustarAltoTabla();
        $(window).on("resize", ajustarAltoTabla);

        cargarSemanas();
    });

    async function snApi(payload) {
        const res = await fetch(SN_API, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
        const txt = await res.text();
        try { return JSON.parse(txt); } catch (e) { return { ok: false, msg: "Respuesta inválida del servidor: " + txt.substring(0, 300) }; }
    }
    function esc(v) { return String(v == null ? "" : v).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"); }
    function fmtN(n) { return Number(n || 0).toLocaleString("es-MX"); }

    // =========================
    // SEMANA (fec_cierre_campana, desde Periodo 12 semana 1)
    // =========================
    async function cargarSemanas() {
        const r = await snApi({ action: "semanas" });
        if (!r.ok) { Swal.fire({ icon: "error", title: "Semanas", text: r.msg || "No se pudieron cargar las semanas." }); return; }
        document.getElementById("sel-semana").innerHTML =
            r.semanas.map(s => `<option value="${s.id}">${esc(s.campana)} (${esc(s.fec_ini)} – ${esc(s.fec_fin)})</option>`).join("");
    }

    // =========================
    // SWITCH SC / BROCKER / GERENCIA
    // =========================
    document.querySelectorAll(".filtro-switch-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".filtro-switch-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            document.querySelectorAll(".filtro-group").forEach(g => g.style.display = "none");
            const target = document.getElementById(this.dataset.target);
            if (target) target.style.display = "";
        });
    });

    // =========================
    // ALTURA TABLA
    // =========================
    function ajustarAltoTabla() {
        const wrapper = document.querySelector(".tabla-facturas-wrapper");
        if (!wrapper) return;

        const wrapperRect  = wrapper.getBoundingClientRect();
        const contenedor   = document.getElementById("contenedor");
        let limiteInferior = window.innerHeight - 24;

        if (contenedor) {
            const contenedorRect = contenedor.getBoundingClientRect();
            limiteInferior = Math.min(limiteInferior, contenedorRect.bottom - 60);
        }

        const altoDisponible = Math.max(180, limiteInferior - wrapperRect.top);
        wrapper.style.maxHeight = altoDisponible + "px";
        wrapper.style.overflowY = "auto";
    }

    // =========================
    // FILTROS ACTIVOS (para mandar al backend)
    // =========================
    function leerFiltrosActuales() {
        return {
            id_campana: document.getElementById("sel-semana").value,
            nap:      document.getElementById("nap").value,
            sc:       document.getElementById("txt-sc-nombre")?.value       ?? "",
            brocker:  document.getElementById("txt-brocker-nombre")?.value  ?? "",
            gerencia: document.getElementById("txt-gerencia-nombre")?.value ?? "",
        };
    }

    // =========================
    // BUSCAR
    // =========================
    async function obtenerPuntos() {

        const filtros = leerFiltrosActuales();
        const tbody   = document.getElementById("body-puntos-nap");

        if (!filtros.id_campana) {
            Swal.fire({ icon: "warning", title: "Aviso", text: "Debes seleccionar la semana" });
            return;
        }

        tbody.innerHTML = `
            <tr>
                <td colspan="13" class="text-center text-muted" style="padding:30px;font-size:13px;">
                    <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                    Consultando...
                </td>
            </tr>`;
        ajustarAltoTabla();

        try {
            const r = await snApi({ action: "consultar", ...filtros });

            if (!r.ok) {
                Swal.fire({ icon: "warning", title: "Aviso", text: r.msg });
                tbody.innerHTML = `<tr><td colspan="13" class="text-center text-muted" style="padding:30px;font-size:13px;">${esc(r.msg)}</td></tr>`;
                ajustarAltoTabla();
                return;
            }

            SN_RESULT = r;
            document.getElementById("btn-exportar").disabled = !r.naps.length;

            if (!r.naps.length) {
                tbody.innerHTML = `<tr><td colspan="13" class="text-center text-muted" style="padding:30px;font-size:13px;">Sin resultados para los filtros seleccionados</td></tr>`;
                ajustarAltoTabla();
                return;
            }

            let h = "";
            r.naps.forEach((n, i) => {
                h += `
                    <tr>
                        <td>${esc(n.semana)}</td>
                        <td class="td-persona">${esc(n.sc)}</td>
                        <td class="td-persona">${esc(n.brocker)}</td>
                        <td class="td-persona">${esc(n.gerencia)}</td>
                        <td><b>${esc(n.nap)}</b></td>
                        <td>${n.folios}</td>
                        <td>${n.rechazados}</td>
                        <td>${n.otros}</td>
                        <td>${n.aceptados}</td>
                        <td>${n.certificados}</td>
                        <td class="td-puntos">${n.puntos}</td>
                        <td>${esc(n.categoria)}</td>
                        <td><button type="button" class="btn-ver-folios" onclick="abrirDetalle(${i})">Detalle</button></td>
                    </tr>`;
            });
            tbody.innerHTML = h;
            ajustarAltoTabla();

        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="13" class="text-center text-danger" style="padding:30px;font-size:13px;">Error al consultar los datos</td></tr>`;
            ajustarAltoTabla();
        }
    }

    // =========================
    // MODAL DETALLE DE FOLIOS (sin montos)
    // =========================
    var DET_LISTA = [];
    function columnasDetalle(p) {
        return [
            ["Folio", "folio"], ["Trabajador", "trabajador"], ["NSS", "nss", p.nss], ["CURP", "curp", p.nss], ["Edad", "edad"], ["Salario", "salario"],
            ["Estatus", "estatus"], ["Estatus confirma", "confirma"], ["F. captura", "f_captura"], ["F. traspaso", "f_traspaso"],
            ["Movilidad", "movilidad"], ["PPlus", "pplus"], ["Categoría cliente", "cat_cliente"], ["Asesor", "asesor"]
        ].filter(c => c[2] === undefined || c[2]);
    }
    function descargarXlsx(hojas, nombre) {
        if (typeof XLSX === "undefined") { Swal.fire({ icon: "error", title: "Exportar", text: "No se pudo cargar el generador de Excel. Revisa tu conexión e intenta de nuevo." }); return; }
        const wb = XLSX.utils.book_new();
        hojas.forEach(h => {
            const ws = XLSX.utils.aoa_to_sheet([h.cabecera].concat(h.filas));
            ws["!cols"] = h.cabecera.map((c, i) => ({ wch: Math.min(40, Math.max(String(c).length + 2, ...h.filas.slice(0, 200).map(f => String(f[i] ?? "").length + 2))) }));
            XLSX.utils.book_append_sheet(wb, ws, h.nombre);
        });
        XLSX.writeFile(wb, nombre + ".xlsx");
    }
    function limpiaNombre(s) { return String(s).replace(/[^A-Za-z0-9_-]+/g, "_").replace(/^_+|_+$/g, ""); }

    // Exporta todo el resultado: hoja 1 = puntos por NAP, hoja 2 = detalle de todos los folios
    function exportarResultado() {
        if (!SN_RESULT || !SN_RESULT.naps.length) { Swal.fire({ icon: "warning", title: "Sin datos", text: "Busca primero para poder exportar." }); return; }
        const r = SN_RESULT, cols = columnasDetalle(r.permisos || {});
        const hoja1 = {
            nombre: "Puntos por NAP",
            cabecera: ["Semana", "SC", "Brocker", "Gerencia", "NAP", "Folios", "Rechazados", "Otro estatus", "Aceptados procesar", "Certificados", "Puntos aprox.", "Categoría"],
            filas: r.naps.map(n => [n.semana, n.sc, n.brocker, n.gerencia, n.nap, n.folios, n.rechazados, n.otros, n.aceptados, n.certificados, n.puntos, n.categoria])
        };
        const filas2 = [];
        r.naps.forEach(n => n.lista.forEach(f => filas2.push([n.semana, n.nap, n.sc, n.brocker, n.gerencia].concat(cols.map(c => f[c[1]] ?? "")))));
        const hoja2 = { nombre: "Folios", cabecera: ["Semana", "NAP", "SC", "Brocker", "Gerencia"].concat(cols.map(c => c[0])), filas: filas2 };
        descargarXlsx([hoja1, hoja2], "puntos_nap_" + limpiaNombre(r.semanas[0].nombre));
    }

    function abrirDetalle(i) {
        const n = SN_RESULT.naps[i], p = SN_RESULT.permisos || {};
        DET_LISTA = n.lista;
        document.getElementById("detSub").textContent = `NAP ${n.nap} · ${n.semana} · ${n.lista.length} folio(s)`;
        const cols = columnasDetalle(p);
        DET_COLS = cols;
        document.getElementById("detHead").innerHTML = "<tr>" + cols.map(c => `<th>${c[0]}</th>`).join("") + "</tr>";
        pintarDetalle();
        document.getElementById("detBackdrop").classList.add("open");
    }
    var DET_COLS = [];
    function fmtSalario(v) { return (v === "" || v === null || isNaN(Number(v))) ? (v ?? "") : "$" + Number(v).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function pintarDetalle() {
        const filas = DET_LISTA;
        document.getElementById("detBody").innerHTML = filas.length
            ? filas.map(f => "<tr>" + DET_COLS.map(c => `<td class="${c[1] === 'folio' ? 'td-folio' : ''}">${esc(c[1] === 'salario' ? fmtSalario(f[c[1]]) : f[c[1]])}</td>`).join("") + "</tr>").join("")
            : `<tr><td colspan="${DET_COLS.length}" class="text-center text-muted" style="padding:24px">Sin coincidencias</td></tr>`;
    }
    function cerrarDetalle() { document.getElementById("detBackdrop").classList.remove("open"); }
    document.getElementById("detClose").addEventListener("click", cerrarDetalle);
    document.getElementById("detBackdrop").addEventListener("click", e => { if (e.target.id === "detBackdrop") cerrarDetalle(); });
    document.addEventListener("keydown", e => { if (e.key === "Escape") cerrarDetalle(); });

    document.getElementById("buscar").addEventListener("click", obtenerPuntos);

</script>
</html>