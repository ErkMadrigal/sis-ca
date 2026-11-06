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
            .ac-lista { display:none; position:absolute; left:15px; right:15px; top:100%; z-index:50; background:#fff; border:1px solid #dce3eb; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); max-height:260px; overflow-y:auto; }
            .ac-item { padding:7px 12px; font-size:12.5px; color:#1f2d3d; cursor:pointer; text-align:left; }
            .ac-item:hover { background:#eaf2fb; }
            .ac-item b { color:#185FA5; margin-right:6px; }
            .ac-partidas { font-size:11px; color:#8896a6; padding:0 4px 8px; text-align:left; line-height:1.45; }
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
				titulo("PARTIDAS DE AJUSTES");
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

						<!-- Clave / nombre (autocompletable) -->
						<div class="col-md-5 col-5 mb-3" style="text-align:left; position:relative;">
							<label for="clave">Clave o nombre</label>
							<input type="text" id="clave" class="form-control form-control-sm carobra" autocomplete="off" placeholder="Escribe clave o nombre..." />
							<div id="ac-lista" class="ac-lista"></div>
						</div>

						<!-- Fechas -->
						<div class="col-md-2 col-2 mb-3 text-left">
							<label for="fec-ini">Desde</label>
							<input type="text" id="fec-ini" class="form-control form-control-sm carobra datepicker" placeholder="dd/mm/aaaa" autocomplete="off" />
						</div>
						<div class="col-md-2 col-2 mb-3 text-left">
							<label for="fec-fin">Hasta</label>
							<input type="text" id="fec-fin" class="form-control form-control-sm carobra datepicker" placeholder="dd/mm/aaaa" autocomplete="off" />
						</div>

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
				</div>

				<hr>

				<div class="container-table">
					<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
						<h4 style="margin:0;">PARTIDAS DE AJUSTES</h4>
						<button type="button" class="btn-exportar" id="btn-exportar" disabled onclick="exportarXlsx()">&#11015; Exportar XLSX</button>
					</div>

					<div id="resumen"></div>

					<div class="tabla-facturas-wrapper">
						<table class="tabla-facturas table-striped">
							<thead>
								<tr>
									<th>Fec. pago</th>
									<th>Motivo</th>
									<th>Partida</th>
									<th>Ajuste</th>
								</tr>
							</thead>
							<tbody id="body-partidas">
								<tr>
									<td colspan="4" class="text-center text-muted" style="padding:30px;font-size:13px;">
										Captura el rango de fechas (y, si quieres, una clave o nombre) para consultar
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

	<?php } ?>

	</body>

	<script type="text/javascript">

    const AJ_API = "partidas_api.php";
    var AJ_FILAS = [];

    async function ajApi(payload) {
        const res = await fetch(AJ_API, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
        const txt = await res.text();
        try { return JSON.parse(txt); } catch (e) { return { ok: false, msg: "Respuesta inválida del servidor: " + txt.substring(0, 300) }; }
    }
    function esc(v) { return String(v == null ? "" : v).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"); }
    function money(n) { return "$" + Number(n || 0).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();

        $('#men_pie').css("top", h);
        $('#men_pie').css("background", "red");

        $('#tmenu td').click(function(evento) {
            var modulo = $(this).text().replace(/ /gi, "_");
            $("#opcion").load("menu.php?modulo=" + modulo);
        });
        $("#salir").click(function() { window.location = "https://www.sis-ca.com.mx:7863/"; });
        $("#email").click(function() { window.open("http://webmail.carobra.com.mx/imp/login.php"); });

        $(".datepicker").datepicker({ format: "dd/mm/yyyy", autoclose: true });

        ajustarAltoTabla();
        $(window).on("resize", ajustarAltoTabla);
    });

    function ajustarAltoTabla() {
        const wrapper = document.querySelector(".tabla-facturas-wrapper");
        if (!wrapper) return;
        const wrapperRect = wrapper.getBoundingClientRect();
        const contenedor  = document.getElementById("contenedor");
        let limite = window.innerHeight - 24;
        if (contenedor) limite = Math.min(limite, contenedor.getBoundingClientRect().bottom - 60);
        wrapper.style.maxHeight = Math.max(180, limite - wrapperRect.top) + "px";
        wrapper.style.overflowY = "auto";
    }

    // =========================
    // AUTOCOMPLETAR clave / nombre
    // =========================
    (function () {
        const inp = document.getElementById("clave"), lista = document.getElementById("ac-lista");
        let t = null, ult = "";
        function cerrar() { lista.style.display = "none"; lista.innerHTML = ""; }
        inp.addEventListener("input", function () {
            clearTimeout(t);
            const q = inp.value.trim();
            if (q.length < 2 || /\)\s*$/.test(q)) { cerrar(); return; }
            t = setTimeout(async () => {
                ult = q;
                const r = await ajApi({ action: "buscarClave", q });
                if (q !== ult) return;
                if (!r.ok || !r.items.length) { cerrar(); return; }
                lista.innerHTML = r.items.map(i => `<div class="ac-item" data-v="${esc(i.nombre + " (" + i.clave + ")")}"><b>${esc(i.clave)}</b> ${esc(i.nombre)}</div>`).join("");
                lista.style.display = "block";
            }, 250);
        });
        lista.addEventListener("mousedown", function (e) {
            const it = e.target.closest(".ac-item");
            if (!it) return;
            inp.value = it.dataset.v;
            cerrar();
        });
        inp.addEventListener("blur", () => setTimeout(cerrar, 150));
        inp.addEventListener("keydown", e => { if (e.key === "Enter") { cerrar(); buscar(); } });
    })();

    // =========================
    // BUSCAR
    // =========================
    async function buscar() {
        const fec_ini = document.getElementById("fec-ini").value.trim(), fec_fin = document.getElementById("fec-fin").value.trim();
        const clave = document.getElementById("clave").value.trim();
        const tbody = document.getElementById("body-partidas");

        if (!fec_ini || !fec_fin) { Swal.fire({ icon: "warning", title: "Aviso", text: "Captura el rango de fechas (desde y hasta)." }); return; }

        document.getElementById("resumen").innerHTML = "";
        document.getElementById("btn-exportar").disabled = true;
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted" style="padding:30px;font-size:13px;"><div class="spinner-border spinner-border-sm text-primary mr-2"></div>Consultando...</td></tr>`;

        const r = await ajApi({ action: "consultar", fec_ini, fec_fin, clave });
        if (!r.ok) {
            Swal.fire({ icon: "warning", title: "Aviso", text: r.msg });
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted" style="padding:30px;font-size:13px;">${esc(r.msg)}</td></tr>`;
            return;
        }
        AJ_FILAS = r.filas;

        if (!r.filas.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted" style="padding:30px;font-size:13px;">${esc(r.aviso || "Sin resultados para los filtros seleccionados")}</td></tr>`;
            ajustarAltoTabla();
            return;
        }

        document.getElementById("resumen").innerHTML =
            `<div class="sn-cards"><div class="sn-card"><span>Registros</span><b>${r.filas.length.toLocaleString("es-MX")}</b></div>` +
            `<div class="sn-card"><span>Total ajuste</span><b>${money(r.total)}</b></div></div>` +
            (r.truncado ? `<div class="sn-nota" style="color:#a06b00">Se muestran solo los primeros ${r.max.toLocaleString("es-MX")} registros. Acota más el rango de fechas o la clave.</div>` : "");

        tbody.innerHTML = r.filas.map(f => `
            <tr>
                <td>${esc(f.fec_pago)}</td>
                <td>${esc(f.motivo)}</td>
                <td class="td-persona">${esc(f.partida_nombre)}</td>
                <td class="td-puntos">${money(f.ajuste)}</td>
            </tr>`).join("");
        document.getElementById("btn-exportar").disabled = false;
        ajustarAltoTabla();
    }
    document.getElementById("buscar").addEventListener("click", buscar);

    // =========================
    // EXPORTAR XLSX
    // =========================
    function exportarXlsx() {
        if (!AJ_FILAS.length) { Swal.fire({ icon: "warning", title: "Sin datos", text: "Busca primero para poder exportar." }); return; }
        if (typeof XLSX === "undefined") { Swal.fire({ icon: "error", title: "Exportar", text: "No se pudo cargar el generador de Excel. Revisa tu conexión." }); return; }
        const cab = ["Tabla", "ID", "Fecha", "Fec. pago", "Clave", "Nombre", "Partida", "Nombre partida", "Motivo", "Ajuste", "Año", "Mes", "Socio", "Cen. costo", "No. gasto", "Estatus", "Tipo ajuste", "Afecta SB", "Responsable"];
        const filas = AJ_FILAS.map(f => [f.tabla, f.id, f.fecha, f.fec_pago, f.clave, f.nombre, f.partida, f.partida_nombre, f.motivo, f.ajuste, f.anio, f.mes, f.socio, f.cen_cost, f.no_gasto, f.estatus, f.tipo_ajuste, f.afecta_sb, f.responsable]);
        const ws = XLSX.utils.aoa_to_sheet([cab].concat(filas));
        ws["!cols"] = cab.map((c, i) => ({ wch: Math.min(45, Math.max(c.length + 2, ...filas.slice(0, 300).map(r => String(r[i] ?? "").length + 2))) }));
        for (let R = 1; R <= filas.length; R++) { const a = "J" + (R + 1); if (ws[a]) ws[a].z = "$#,##0.00"; }
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Partidas");
        const ini = document.getElementById("fec-ini").value.replace(/\//g, "-"), fin = document.getElementById("fec-fin").value.replace(/\//g, "-");
        XLSX.writeFile(wb, `partidas_${ini}_a_${fin}.xlsx`);
    }

</script>
</html>