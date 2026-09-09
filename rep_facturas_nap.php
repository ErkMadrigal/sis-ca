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
            .td-bono     { font-weight: 600; color: #0F6E56; font-variant-numeric: tabular-nums; }
            .td-impuesto { font-weight: 600; color: #854F0B; font-variant-numeric: tabular-nums; }
            .td-monto    { font-weight: 700; color: #185FA5; font-variant-numeric: tabular-nums; }
            .td-folio    { font-weight: 600; color: #1f2d3d; }

            /* Badges de estatus de factura (sin subir / pdf / xml / error / completo) */
            .badge-estatus {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .04em;
                white-space: nowrap;
            }
            .estatus-0 { background: #E4E7EB; color: #4B5563; } /* sin subir */
            .estatus-1 { background: #BFDBFE; color: #0C4A83; } /* solo pdf */
            .estatus-2 { background: #FCE7B2; color: #7A4E0A; } /* solo xml */
            .estatus-3 { background: #F8C6C6; color: #7A1414; } /* con error */
            .estatus-4 { background: #BEEAD9; color: #0B5C42; } /* completo */

            /* Badges de estatus de NAP (naps.baja + estatus_naps_cumpl.estatus) y Ase. Bernal */
            .badge-nap {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .04em;
                white-space: nowrap;
            }
            .np-activo, .np-si   { background: #d7f5e3; color: #0b6b3a; }
            .np-inactivo, .np-no { background: #f5d7d7; color: #8a1f1f; }
            .np-inhabilitado     { background: #fdecc8; color: #8a5a12; }

            /* =========================
            BOTÓN BUSCAR / REFRESCAR
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
            No. FACTURA + ICONO PDF
            ========================= */
            .td-folio-cell { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
            .pdf-badge-btn {
                display: inline-flex; align-items: center; justify-content: center;
                border: none; background: transparent; cursor: pointer; padding: 0;
                border-radius: 7px; transition: transform .12s;
            }
            .pdf-badge-btn:hover { transform: scale(1.08); }
            .pdf-badge-btn.disabled { opacity: .3; cursor: not-allowed; pointer-events: none; }
            .pdf-icon-wrap {
                width: 24px; height: 24px; border-radius: 6px;
                background: linear-gradient(135deg, #F2695C, #D8362A);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 1px 3px rgba(216,54,42,.4);
            }
            .pdf-badge-btn.disabled .pdf-icon-wrap {
                background: #c9ced4; box-shadow: none;
            }

            /* =========================
            MODAL PREVIEW PDF
            ========================= */
            .pdf-backdrop {
                display: none; position: fixed; inset: 0; z-index: 1000000;
                background: rgba(15,32,68,.55); backdrop-filter: blur(4px);
                align-items: center; justify-content: center; padding: 24px;
            }
            .pdf-backdrop.open { display: flex; }
            .pdf-modal {
                background: #fff; border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,.22);
                width: 100%; max-width: 900px; height: 88vh;
                display: flex; flex-direction: column; overflow: hidden;
            }
            .pdf-modal-header {
                background: #185FA5; padding: 16px 22px;
                display: flex; align-items: center; justify-content: space-between;
            }
            .pdf-modal-header h3 { color: #fff; font-size: 15px; font-weight: 600; margin: 0; }
            .pdf-modal-header .pdf-sub { color: rgba(255,255,255,.75); font-size: 11px; margin-top: 2px; }
            .pdf-modal-close {
                background: rgba(255,255,255,.15); border: none; color: #fff; width: 28px; height: 28px;
                border-radius: 6px; cursor: pointer; font-size: 16px;
            }
            .pdf-modal-body { flex: 1; background: #525659; }
            .pdf-modal-body iframe { width: 100%; height: 100%; border: none; }
            .pdf-modal-footer {
                padding: 12px 22px; border-top: 1px solid #e8edf3; background: #f4f7fb;
                display: flex; justify-content: flex-end; gap: 10px;
            }

            /* =========================
            RESPONSIVE
            ========================= */
            @media (max-width: 768px) {
                .pdf-modal { max-width: 100vw; height: 100vh; border-radius: 0; }
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
				titulo("SEGUIMIENTO FACTURAS NAP");
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
				$nombre  = $_SESSION["nombre"];
				$nivel   = nivel_usuario(utf8_decode($nombre));

				// Años y periodos disponibles, tomados directo de facturas_nap
				$resAnios    = $conecta->consulta("SELECT DISTINCT anio FROM facturas_nap ORDER BY anio DESC");
				$resPeriodos = $conecta->consulta("SELECT DISTINCT periodo FROM facturas_nap ORDER BY periodo DESC");
	?>

	<div class="container">
		<div class="row justify-content-center mt-0 pt-0">
			<div class="col-md-12 mt-0 pt-0">

				<!-- FILTROS -->
				<div class="filtros-wrapper">
					<div class="row align-items-end m-0 p-0">

						<!-- Año -->
						<div class="col-md-2 col-6 mb-3 text-left">
							<label for="anio">Año</label>
							<select class="form-control form-control-sm carobra" name="anio" id="anio">
								<option value="">Todos</option>
								<?php while($a = mysqli_fetch_assoc($resAnios)): ?>
									<option value="<?= htmlspecialchars($a['anio']) ?>"><?= htmlspecialchars($a['anio']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>

						<!-- Periodo -->
						<div class="col-md-2 col-6 mb-3 text-left">
							<label for="sel-periodo">Periodo</label>
							<select name="sel-periodo" id="sel-periodo" class="form-control form-control-sm carobra">
								<option value="">Todos</option>
								<?php while($p = mysqli_fetch_assoc($resPeriodos)): ?>
									<option value="<?= htmlspecialchars($p['periodo']) ?>"><?= htmlspecialchars($p['periodo']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>

						<!-- Clave / Nombre -->
						<div class="col-md-4 col-12 mb-3" style="text-align: left;">
							<label for="clave-nombre">Clave / Nombre</label>
							<input type="text" id="clave-nombre" class="form-control form-control-sm carobra"
								name="clave-nombre" autocomplete="off"
								placeholder="Buscar por clave o nombre... (en tiempo real)" />
						</div>

						<!-- Estatus -->
						<div class="col-md-2 col-6 mb-3 text-left">
							<label for="sel-estatus">Estatus</label>
							<select name="sel-estatus" id="sel-estatus" class="form-control form-control-sm carobra">
								<option value="">Todos</option>
								<option value="0">Sin subir</option>
								<option value="1">Solo PDF</option>
								<option value="2">Solo XML</option>
								<option value="3">Con error</option>
								<option value="4">Completo</option>
							</select>
						</div>

						<!-- Refrescar -->
						<div class="col-md-2 col-6 mb-3">
							<label style="visibility:hidden">x</label>
							<button class="btn-filtro-buscar w-100" id="buscar" type="button">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
									fill="none" stroke="currentColor" stroke-width="2.5"
									stroke-linecap="round" stroke-linejoin="round">
									<polyline points="23 4 23 10 17 10"/>
									<polyline points="1 20 1 14 7 14"/>
									<path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
								</svg>
								Refrescar
							</button>
						</div>

					</div>
				</div>

				<hr>

				<div class="container-table">
					<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
						<h4 style="margin:0;">FACTURAS NAP</h4>
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

					<div class="tabla-facturas-wrapper">
						<table class="tabla-facturas table-striped">
							<thead>
								<tr>
									<th>SC</th>
									<th>Brocker</th>
									<th>Gerencia</th>
                                    <th>Clave</th>
                                    <th>Ase. Bernal</th>
                                    <th>Estatus NAP</th>
									<th>Nombre</th>
									<th>Año</th>
									<th>Periodo</th>
									<th>Regimen Fiscal</th>
									<th>Bono</th>
									<th>IVA</th>
									<th>Ret. IVA</th>
									<th>Monto</th>
									<th>Estatus</th>
									<th>No. Factura</th>
								</tr>
							</thead>
							<tbody id="body-facturas">
								<tr>
									<td colspan="16" class="text-center text-muted" style="padding:30px;font-size:13px;">
										Usa los filtros (año, periodo, clave/nombre o estatus) para consultar
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

	<!-- MODAL PREVIEW PDF -->
	<div class="pdf-backdrop" id="pdfBackdrop">
		<div class="pdf-modal">
			<div class="pdf-modal-header">
				<div>
					<h3>Vista previa de factura</h3>
					<div class="pdf-sub" id="pdfPreviewSub"></div>
				</div>
				<button class="pdf-modal-close" id="pdfClose" type="button">✕</button>
			</div>
			<div class="pdf-modal-body">
				<iframe id="pdfFrame" src="" title="Vista previa PDF"></iframe>
			</div>
			<div class="pdf-modal-footer">
				<a id="pdfDescargarLink" class="btn-filtro-buscar" style="width:auto;background:#0F6E56;" download>⬇ Descargar PDF</a>
			</div>
		</div>
	</div>

	<?php } ?>

	</body>

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

	<script type="text/javascript">

    // Carpeta donde se guardan los PDF (mismo servidor que este archivo)
    const CARPETA_FACTURAS = "./facturas/";

    var nivel = <?= json_encode($nivel) ?>;

    const ESTATUS_LABEL = {
        0: "Sin subir",
        1: "Solo PDF",
        2: "Solo XML",
        3: "Con error",
        4: "Completo"
    };

    // =========================
    // BADGES: Ase. Bernal / Estatus NAP
    // =========================
    function aseBernalBadgeHTML(val) {
        const v = (val || '').trim();
        const cls = (v === 'Sí' || v === 'Si') ? 'np-si' : 'np-no';
        return `<span class="badge-nap ${cls}">${v || 'No'}</span>`;
    }

    function estatusNapBadgeHTML(desc) {
        const d = (desc || '').trim();
        let cls = 'np-activo';
        if (d === 'Inactivo') cls = 'np-inactivo';
        else if (d === 'Inhabilitado') cls = 'np-inhabilitado';
        return `<span class="badge-nap ${cls}">${d || '—'}</span>`;
    }

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

        ajustarAltoTablaFacturas();
        $(window).on("resize", ajustarAltoTablaFacturas);

        // No se consulta nada hasta que el usuario use algún filtro
    });

    // =========================
    // ALTURA TABLA
    // =========================
    function ajustarAltoTablaFacturas() {
        const wrapper = document.querySelector(".tabla-facturas-wrapper");
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
    // OBTENER FACTURAS
    // =========================
    async function obtenerFacturas() {

        const anio        = document.getElementById("anio").value;
        const periodo     = document.getElementById("sel-periodo").value;
        const claveNombre = document.getElementById("clave-nombre").value;
        const estatus     = document.getElementById("sel-estatus").value;

        const tbody = document.getElementById("body-facturas");

        tbody.innerHTML = `
            <tr>
                <td colspan="16" class="text-center text-muted" style="padding:30px;font-size:13px;">
                    <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                    Consultando...
                </td>
            </tr>
        `;
        ajustarAltoTablaFacturas();

        try {
            const response = await fetch("rep_facturas_nap_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "getFacturasNap", anio, periodo, clave_nombre: claveNombre, estatus, nivel })
            });

            const result = await response.json();
            window._datosFacturas = result.data || [];

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
                ajustarAltoTablaFacturas();
                return;
            }

            result.data.forEach(row => {

                const est       = parseInt(row.estatus_fac ?? 0);
                const estClase  = `estatus-${est}`;
                const estLabel  = ESTATUS_LABEL[est] ?? "Desconocido";

                const bono    = parseFloat(row.bono    || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const iva     = parseFloat(row.iva     || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const retIva  = parseFloat(row.ret_iva || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const monto   = parseFloat(row.monto   || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const tienePdf = !!row.nom_pdf;
                const nomPdfAttr = (row.nom_pdf ?? '').replace(/"/g, '&quot;');

                tbody.innerHTML += `
                    <tr>
                        <td class="td-persona">${row.sc       ?? ''}</td>
                        <td class="td-persona">${row.brocker  ?? ''}</td>
                        <td class="td-persona">${row.gerencia ?? ''}</td>
                        <td class="td-persona">${row.clave ?? ''}</td>
                        <td>${aseBernalBadgeHTML(row.ase_bernal)}</td>
                        <td>${estatusNapBadgeHTML(row.estatus_descripcion)}</td>
                        <td class="td-persona">${row.nombre ?? ''}</td>
                        <td>${row.anio    ?? ''}</td>
                        <td>${row.periodo ?? ''}</td>
                        <td>${row.reg_fiscal ?? ''}</td>
                        <td class="td-bono">$${bono}</td>
                        <td class="td-impuesto">$${iva}</td>
                        <td class="td-impuesto">$${retIva}</td>
                        <td class="td-monto">$${monto}</td>
                        <td><span class="badge-estatus ${estClase}">${estLabel}</span></td>
                        <td class="td-folio-cell">
                            <span class="td-folio">${row.no_factura ?? ''}</span>
                            <button type="button" class="pdf-badge-btn ${tienePdf ? '' : 'disabled'}"
                                data-nom-pdf="${nomPdfAttr}"
                                title="${tienePdf ? 'Ver PDF' : 'Sin PDF'}">
                                <span class="pdf-icon-wrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14">
                                        <path d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="#fff"/>
                                        <path d="M13 2v5h5" fill="#D8362A"/>
                                    </svg>
                                </span>
                            </button>
                        </td>
                    </tr>
                `;
            });

            ajustarAltoTablaFacturas();

        } catch (error) {
            alert(error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="16" class="text-center text-danger" style="padding:30px;font-size:13px;">
                        Error al consultar los datos
                    </td>
                </tr>
            `;
            ajustarAltoTablaFacturas();
        }
    }

    // =========================
    // BUSQUEDA EN TIEMPO REAL
    // =========================
    let debounceFacturas = null;
    function buscarConDebounce() {
        clearTimeout(debounceFacturas);
        debounceFacturas = setTimeout(obtenerFacturas, 300);
    }

    document.getElementById("clave-nombre").addEventListener("input", buscarConDebounce);
    document.getElementById("sel-estatus").addEventListener("change", obtenerFacturas);
    document.getElementById("anio").addEventListener("change", obtenerFacturas);
    document.getElementById("sel-periodo").addEventListener("change", obtenerFacturas);
    document.getElementById("buscar").addEventListener("click", obtenerFacturas);

    // =========================
    // PREVIEW PDF (delegado sobre el tbody)
    // =========================
    const pdfBackdrop = document.getElementById("pdfBackdrop");

    function abrirPreviewPdf(nomPdf) {
        if (!nomPdf) return;
        const url = CARPETA_FACTURAS + encodeURIComponent(nomPdf);
        document.getElementById("pdfFrame").src = url;
        document.getElementById("pdfDescargarLink").href = url;
        document.getElementById("pdfPreviewSub").textContent = nomPdf;
        pdfBackdrop.classList.add("open");
    }

    function cerrarPreviewPdf() {
        pdfBackdrop.classList.remove("open");
        document.getElementById("pdfFrame").src = "";
    }

    document.getElementById("pdfClose").addEventListener("click", cerrarPreviewPdf);
    pdfBackdrop.addEventListener("click", e => { if (e.target === pdfBackdrop) cerrarPreviewPdf(); });

    document.getElementById("body-facturas").addEventListener("click", function (e) {
        const btn = e.target.closest(".pdf-badge-btn");
        if (!btn || btn.classList.contains("disabled")) return;
        abrirPreviewPdf(btn.dataset.nomPdf);
    });

    // =========================
    // EXPORTAR XLSX
    // =========================
    document.getElementById("btn-exportar-xlsx").addEventListener("click", function () {

        if (!window._datosFacturas || window._datosFacturas.length === 0) {
            Swal.fire({ icon: "warning", title: "Sin datos", text: "No hay datos para exportar." });
            return;
        }

        const wb       = XLSX.utils.book_new();
        const fmtMoney = '$#,##0.00';

        const headers = ["SC","Brocker","Gerencia","Clave","Ase. Bernal","Estatus NAP","Nombre","Año","Periodo","Regimen Fiscal","Bono","IVA","Ret. IVA","Monto","Estatus","No. Factura"];
        const rows    = [headers];

        window._datosFacturas.forEach(row => {
            const est = parseInt(row.estatus_fac ?? 0);
            rows.push([
                row.sc          ?? '',
                row.brocker     ?? '',
                row.gerencia    ?? '',
                row.clave       ?? '',
                row.ase_bernal  ?? '',
                row.estatus_descripcion ?? '',
                row.nombre      ?? '',
                row.anio        ?? '',
                row.periodo     ?? '',
                row.reg_fiscal  ?? '',
                parseFloat(row.bono)    || 0,
                parseFloat(row.iva)     || 0,
                parseFloat(row.ret_iva) || 0,
                parseFloat(row.monto)   || 0,
                ESTATUS_LABEL[est] ?? '',
                row.no_factura  ?? '',
            ]);
        });

        const ws    = XLSX.utils.aoa_to_sheet(rows);
        ws["!cols"] = [
            {wch:20},{wch:20},{wch:20},{wch:10},{wch:8},{wch:14},{wch:28},{wch:8},{wch:12},{wch:18},
            {wch:12},{wch:12},{wch:12},{wch:12},{wch:14},{wch:14}
        ];

        const range = XLSX.utils.decode_range(ws["!ref"]);
            for (let R = 1; R <= range.e.r; R++) {
                ["K","L","M","N"].forEach(col => {
                    const addr = col + (R + 1);
                    if (ws[addr]) ws[addr].z = fmtMoney;
                });
            }

        XLSX.utils.book_append_sheet(wb, ws, "Facturas NAP");

        const anio    = document.getElementById("anio").value        || "TODOS";
        const periodo = document.getElementById("sel-periodo").value  || "TODOS";
        XLSX.writeFile(wb, `Facturas_NAP_${anio}_${periodo.replace(/ /g,"_")}.xlsx`);
    });

</script>
</html>