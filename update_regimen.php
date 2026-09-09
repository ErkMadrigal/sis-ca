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
            .filtros-wrapper {
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 14px;
                padding: 22px 24px !important;
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

            .panel-titulo { font-size: 16px; font-weight: 700; color: #1f2d3d; margin: 0 0 4px; }
            .panel-sub    { font-size: 12px; color: #7b8794; margin-bottom: 20px; }

            /* =========================
            AUTOCOMPLETE
            ========================= */
            .ac-wrapper { position: relative; }
            .ac-dropdown {
                display: none;
                position: absolute;
                top: calc(100% + 4px);
                left: 0; right: 0;
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 10px;
                box-shadow: 0 12px 28px rgba(0,0,0,.12);
                max-height: 260px;
                overflow-y: auto;
                z-index: 50;
            }
            .ac-dropdown.open { display: block; }
            .ac-dropdown { text-align: left; }
            .ac-item {
                padding: 9px 14px;
                cursor: pointer;
                font-size: 13px;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
                text-align: left;
            }
            .ac-item:last-child { border-bottom: none; }
            .ac-item:hover, .ac-item.active { background: #eaf2fb; }
            .ac-item .ac-nombre { font-weight: 600; text-align: left; }
            .ac-item .ac-meta { font-size: 11px; color: #7b8794; margin-top: 2px; text-align: left; }
            .ac-empty { padding: 12px 14px; font-size: 12px; color: #9aa5b1; text-align: left; }

            /* =========================
            PANEL PERSONA SELECCIONADA
            ========================= */
            .persona-card {
                background: #f4f7fb;
                border: 1px solid #e8edf3;
                border-radius: 12px;
                padding: 16px 18px;
                margin-top: 16px;
                display: none;
            }
            .persona-card.show { display: block; }
            .persona-card .p-nombre { font-size: 15px; font-weight: 700; color: #1f2d3d; }
            .persona-card .p-fila { display: flex; gap: 24px; margin-top: 10px; flex-wrap: wrap; }
            .persona-card .p-item { min-width: 120px; }
            .persona-card .p-label {
                font-size: 10px; font-weight: 700; color: #7b8794; text-transform: uppercase;
                letter-spacing: .06em; margin-bottom: 3px;
            }
            .persona-card .p-value { font-size: 13px; font-weight: 600; color: #1f2d3d; }
            .badge-regimen-actual {
                display: inline-block; padding: 3px 10px; border-radius: 20px;
                font-size: 11px; font-weight: 700; background: #BFDBFE; color: #0C4A83;
                text-transform: uppercase; letter-spacing: .03em;
            }

            .btn-actualizar-regimen {
                display: flex; align-items: center; justify-content: center; gap: 8px;
                width: 100%; padding: 11px 20px; background: #185FA5; color: #fff;
                border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
                cursor: pointer; transition: background .15s, opacity .15s; height: 44px;
                margin-top: 18px;
            }
            .btn-actualizar-regimen:hover { background: #0C447C; }
            .btn-actualizar-regimen:disabled { opacity: .5; cursor: not-allowed; }
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
				titulo("Cambio de Régimen Fiscal");
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

				// Regimenes disponibles, tomados directo de empl_reg_fiscal
				$resRegimenes = $conecta->consulta("SELECT DISTINCT regimen FROM empl_reg_fiscal WHERE regimen IS NOT NULL AND regimen <> '' ORDER BY regimen");
	?>

	<div class="container">
		<div class="row justify-content-center mt-4">
			<div class="col-md-7">

				<div class="filtros-wrapper">
					<div class="panel-sub">Busca a la persona por clave o nombre, revisa su régimen actual y asigna el nuevo.</div>

					<!-- Buscador autocompletable -->
					<div class="ac-wrapper">
						<label for="buscar-persona">Persona</label>
						<input type="text" id="buscar-persona" class="form-control form-control-sm carobra"
							autocomplete="off" placeholder="Buscar por clave o nombre..." />
						<div class="ac-dropdown" id="ac-dropdown"></div>
					</div>

					<!-- Card de la persona seleccionada -->
					<div class="persona-card" id="persona-card">
						<div class="p-nombre" id="p-nombre"></div>
						<div class="p-fila">
							<div class="p-item">
								<div class="p-label">Clave</div>
								<div class="p-value" id="p-clave"></div>
							</div>
							<div class="p-item">
								<div class="p-label">Régimen actual</div>
								<div class="p-value"><span class="badge-regimen-actual" id="p-regimen"></span></div>
							</div>
							<div class="p-item">
								<div class="p-label">CP fiscal</div>
								<div class="p-value" id="p-cp"></div>
							</div>
						</div>

						<div class="row align-items-end mt-3">
							<div class="col-md-8 col-12 mb-2">
								<label for="nuevo-regimen">Nuevo régimen</label>
								<select id="nuevo-regimen" class="form-control form-control-sm carobra">
									<option value="">Selecciona...</option>
									<?php while($r = mysqli_fetch_assoc($resRegimenes)): ?>
										<option value="<?= htmlspecialchars($r['regimen']) ?>"><?= htmlspecialchars($r['regimen']) ?></option>
									<?php endwhile; ?>
								</select>
							</div>
						</div>

						<button class="btn-actualizar-regimen" id="btn-actualizar" type="button">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
								fill="none" stroke="currentColor" stroke-width="2.5"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 6L9 17l-5-5"/>
							</svg>
							Actualizar Régimen
						</button>
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
    });

    // =========================
    // AUTOCOMPLETE PERSONA
    // =========================
    const inputBuscar = document.getElementById("buscar-persona");
    const dropdown     = document.getElementById("ac-dropdown");
    const personaCard  = document.getElementById("persona-card");
    const btnActualizar = document.getElementById("btn-actualizar");

    let personaSeleccionada = null;
    let debounceAc = null;
    let acIndex = -1;

    inputBuscar.addEventListener("input", function () {
        personaSeleccionada = null;
        personaCard.classList.remove("show");

        const texto = this.value.trim();
        clearTimeout(debounceAc);

        if (texto.length < 2) {
            dropdown.classList.remove("open");
            dropdown.innerHTML = "";
            return;
        }

        debounceAc = setTimeout(() => buscarPersonas(texto), 250);
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".ac-wrapper")) {
            dropdown.classList.remove("open");
        }
    });

    async function buscarPersonas(texto) {
        try {
            const response = await fetch("update_regimen_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "buscarPersona", query: texto })
            });

            const result = await response.json();
            acIndex = -1;

            if (result.status === "error") {
                dropdown.innerHTML = `<div class="ac-empty">${result.message}</div>`;
                dropdown.classList.add("open");
                return;
            }

            if (!result.data || result.data.length === 0) {
                dropdown.innerHTML = `<div class="ac-empty">Sin coincidencias</div>`;
                dropdown.classList.add("open");
                return;
            }

            dropdown.innerHTML = result.data.map((p, i) => `
                <div class="ac-item" data-index="${i}">
                    <div class="ac-nombre">${p.nombre ?? ''} (${p.regimen ?? 'Sin régimen'})</div>
                </div>
            `).join("");

            dropdown.querySelectorAll(".ac-item").forEach(item => {
                item.addEventListener("click", function () {
                    const p = result.data[parseInt(this.dataset.index)];
                    seleccionarPersona(p);
                });
            });

            dropdown.classList.add("open");

        } catch (error) {
            dropdown.innerHTML = `<div class="ac-empty">Error al buscar</div>`;
            dropdown.classList.add("open");
        }
    }

    function seleccionarPersona(p) {
        personaSeleccionada = p;
        inputBuscar.value = `${p.nombre ?? ''} (${p.clave ?? ''})`;
        dropdown.classList.remove("open");

        document.getElementById("p-nombre").textContent  = p.nombre ?? '';
        document.getElementById("p-clave").textContent   = p.clave ?? '';
        document.getElementById("p-regimen").textContent = p.regimen || 'Sin régimen';
        document.getElementById("p-cp").textContent      = p.cp ?? '—';

        document.getElementById("nuevo-regimen").value = "";
        personaCard.classList.add("show");
    }

    // =========================
    // ACTUALIZAR REGIMEN
    // =========================
    btnActualizar.addEventListener("click", async function () {

        if (!personaSeleccionada) {
            Swal.fire({ icon: "warning", title: "Falta persona", text: "Busca y selecciona a la persona primero." });
            return;
        }

        const nuevoRegimen = document.getElementById("nuevo-regimen").value;

        if (!nuevoRegimen) {
            Swal.fire({ icon: "warning", title: "Falta régimen", text: "Selecciona el nuevo régimen." });
            return;
        }

        if (nuevoRegimen === personaSeleccionada.regimen) {
            Swal.fire({ icon: "info", title: "Sin cambios", text: "Esa persona ya tiene ese régimen." });
            return;
        }

        const confirm = await Swal.fire({
            icon: "question",
            title: "¿Confirmar cambio?",
            html: `Vas a cambiar el régimen de <b>${personaSeleccionada.nombre}</b><br>de <b>${personaSeleccionada.regimen || 'Sin régimen'}</b> a <b>${nuevoRegimen}</b>.`,
            showCancelButton: true,
            confirmButtonText: "Sí, actualizar",
            cancelButtonText: "Cancelar"
        });

        if (!confirm.isConfirmed) return;

        btnActualizar.disabled = true;

        try {
            const response = await fetch("update_regimen_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "cambiarRegimen", clave: personaSeleccionada.clave, nuevo_regimen: nuevoRegimen })
            });

            const result = await response.json();

            if (result.status === "error") {
                Swal.fire({ icon: "error", title: "Error", text: result.message });
                return;
            }

            if (result.facturas_bloqueadas > 0) {
                await Swal.fire({
                    icon: "warning",
                    title: "Régimen actualizado",
                    html: `El régimen se cambió correctamente.<br><br>⚠ Ya hay ${result.facturas_bloqueadas} factura(s) cargada(s) para esta persona y no se pudieron borrar.`
                });
            } else {
                await Swal.fire({ icon: "success", title: "Régimen actualizado", text: "El cambio se guardó correctamente." });
            }

            // Refresca la página para dejar todo limpio y listo para la siguiente búsqueda
            location.reload();
            return;

        } catch (error) {
            Swal.fire({ icon: "error", title: "Error", text: String(error) });
        } finally {
            btnActualizar.disabled = false;
        }
    });

</script>
</html>