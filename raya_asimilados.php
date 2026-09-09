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
            .ec-card {
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 12px 32px rgba(24,95,165,.08);
                margin-bottom: 10px;
            }
            .ec-header {
                background: linear-gradient(135deg, #185FA5, #0C447C);
                padding: 22px 26px;
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .ec-header-icon {
                width: 42px; height: 42px; border-radius: 12px;
                background: rgba(255,255,255,.15);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
            }
            .ec-header h2 { color: #fff; font-size: 17px; font-weight: 700; margin: 0; }
            .ec-header p { color: rgba(255,255,255,.75); font-size: 12px; margin: 3px 0 0; }

            .ec-body { padding: 34px 30px; }

            .ec-fields {
                display: grid;
                grid-template-columns: 1fr 1fr 2fr;
                gap: 24px;
            }
            @media (max-width: 992px) {
                .ec-fields { grid-template-columns: 1fr 1fr; }
            }
            @media (max-width: 600px) {
                .ec-fields { grid-template-columns: 1fr; }
            }

            .ec-field label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 700;
                color: #185FA5;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 8px;
            }
            .ec-field .form-control {
                font-size: 13.5px;
                border: 1.5px solid #dce3eb;
                border-radius: 10px;
                padding: 10px 12px;
                height: auto;
                color: #1f2d3d;
                background: #fafbfc;
                transition: border-color .15s, box-shadow .15s;
            }
            .ec-field .form-control:focus {
                border-color: #185FA5;
                box-shadow: 0 0 0 3px rgba(24,95,165,.12);
                background: #fff;
                outline: none;
            }
            .ec-hint {
                font-size: 10.5px; color: #9aa5b1; margin-top: 8px; font-style: italic; line-height: 1.4;
            }

            /* =========================
            INPUT DE ARCHIVO
            ========================= */
            .archivo-wrapper {
                border: 1.5px dashed #cfe0f2;
                border-radius: 10px;
                background: #f4f7fb;
                padding: 12px 16px;
                height: auto;
                min-height: 44px;
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                transition: border-color .15s, background .15s;
            }
            .archivo-wrapper:hover { border-color: #185FA5; background: #eef4fa; }
            .archivo-wrapper svg { flex-shrink: 0; color: #185FA5; }
            .archivo-texto { font-size: 13px; color: #4b5563; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .archivo-texto b { color: #1f2d3d; }
            #archivo-xlsx { display: none; }

            .btn-cargar-grande {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: auto;
                padding: 13px 30px;
                background: linear-gradient(135deg, #185FA5, #0C447C);
                color: #fff;
                border: none;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 700;
                white-space: nowrap;
                cursor: pointer;
                transition: opacity .15s, transform .1s;
                margin-top: 26px;
            }
            @media (max-width: 768px) {
                .btn-cargar-grande { width: 100%; }
            }
            .btn-cargar-grande:hover { opacity: .92; }
            .btn-cargar-grande:active { transform: scale(.99); }
            .btn-cargar-grande:disabled { opacity: .5; cursor: not-allowed; }
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
				titulo("Carga de Lista de Raya (Asimilados)");
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
	?>

	<div class="container-fluid">
		<div class="row mt-4">
			<div class="col-md-12">

				<div class="ec-card">
					<div class="ec-header">
						<div class="ec-header-icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
								fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
								<polyline points="17 8 12 3 7 8"/>
								<line x1="12" y1="3" x2="12" y2="15"/>
							</svg>
						</div>
						<div>
							<h2>Cargar Lista de Raya — Asimilados a Salarios</h2>
							<p>Sube el .xlsx de "Lista de Raya (forma tabular)"; se busca I.S.R. Asimilados por empleado, se resuelve la clave por nombre y se guarda en el Año/Periodo elegido (reemplaza lo que ya hubiera).</p>
						</div>
					</div>

					<div class="ec-body">
					<div class="ec-fields">

						<!-- Año -->
						<div class="ec-field">
							<label>
								<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
								Año
							</label>
							<select class="form-control carobra" name="anio" id="anio">
								<option value=""></option>
								<?php for($i = date('Y'); $i >= 2026; $i--): ?>
									<option value="<?= $i ?>"><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>

						<!-- Periodo -->
						<div class="ec-field">
							<label>
								<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg>
								Periodo
							</label>
							<select name="sel-periodo" id="sel-periodo" class="form-control carobra">
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

						<!-- Archivo -->
						<div class="ec-field">
							<label>
								<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
								Archivo .xlsx
							</label>
							<label class="archivo-wrapper" for="archivo-xlsx">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
									fill="none" stroke="currentColor" stroke-width="2.5"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
									<polyline points="17 8 12 3 7 8"/>
									<line x1="12" y1="3" x2="12" y2="15"/>
								</svg>
								<span class="archivo-texto" id="archivo-texto">Da clic para elegir el archivo (o arrástralo aquí)</span>
							</label>
							<input type="file" id="archivo-xlsx" accept=".xlsx,.xls" />
							<div class="ec-hint">Debe traer las columnas "Empleado" e "I.S.R. Asimilados" (export de CONTPAQi Nóminas).</div>
						</div>

					</div>

					<button class="btn-cargar-grande" id="btn-cargar" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
							fill="none" stroke="currentColor" stroke-width="2.5"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
							<polyline points="7 10 12 15 17 10"/>
							<line x1="12" y1="15" x2="12" y2="3"/>
						</svg>
						<span id="btn-cargar-txt">Cargar Archivo</span>
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

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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

    // Muestra el nombre del archivo elegido
    document.getElementById("archivo-xlsx").addEventListener("change", function () {
        const texto = document.getElementById("archivo-texto");
        if (this.files && this.files.length > 0) {
            texto.innerHTML = `<b>${this.files[0].name}</b>`;
        } else {
            texto.textContent = "Da clic para elegir el archivo (o arrástralo aquí)";
        }
    });

    /**
     * Busca, entre todas las hojas del libro, la fila de encabezado
     * que tenga las columnas "Empleado" e "I.S.R. Asimilados"
     * (sin asumir un número de fila fijo, por si el layout cambia
     * ligeramente de un periodo a otro).
     */
    function localizarEncabezado(workbook) {
        for (const nombreHoja of workbook.SheetNames) {
            const hoja  = workbook.Sheets[nombreHoja];
            const filas = XLSX.utils.sheet_to_json(hoja, { header: 1, defval: null, raw: true });

            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                if (!Array.isArray(fila)) continue;

                const idxEmpleado = fila.findIndex(c => typeof c === "string" && c.trim().toLowerCase() === "empleado");
                const idxIsr       = fila.findIndex(c => typeof c === "string" && c.trim().toUpperCase().replace(/\s+/g, ' ').includes("I.S.R") && c.toUpperCase().includes("ASIMILADO"));

                if (idxEmpleado !== -1 && idxIsr !== -1) {
                    return { filas, filaEncabezado: i, idxEmpleado, idxIsr };
                }
            }
        }
        return null;
    }

    /**
     * Extrae los registros {nombre, isr} desde la fila siguiente al
     * encabezado hasta que dejen de verse como filas de empleado
     * (se salta subtítulos de "Reg. Pat. IMSS", separadores "====",
     * filas vacías y la fila final "Total Gral.").
     */
    function extraerRegistros({ filas, filaEncabezado, idxEmpleado, idxIsr }) {
        const registros = [];

        for (let i = filaEncabezado + 1; i < filas.length; i++) {
            const fila = filas[i];
            if (!Array.isArray(fila)) continue;

            const nombre = fila[idxEmpleado];
            const isr    = fila[idxIsr];

            const nombreValido = typeof nombre === "string" && nombre.trim() !== "" &&
                !nombre.trim().toUpperCase().startsWith("TOTAL") &&
                !nombre.trim().toUpperCase().startsWith("REG. PAT");

            const isrValido = typeof isr === "number";

            if (nombreValido && isrValido) {
                registros.push({ nombre: nombre.trim(), isr: isr });
            }
        }

        return registros;
    }

    // =========================
    // CARGAR ARCHIVO
    // =========================
    document.getElementById("btn-cargar").addEventListener("click", async function () {

        const anio        = document.getElementById("anio").value;
        const periodo     = document.getElementById("sel-periodo").value;
        const fileInput   = document.getElementById("archivo-xlsx");
        const btn         = this;
        const btnTxt      = document.getElementById("btn-cargar-txt");

        if (!anio || !periodo) {
            Swal.fire({ icon: "warning", title: "Faltan datos", text: "Selecciona año y periodo." });
            return;
        }

        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({ icon: "warning", title: "Falta archivo", text: "Elige el archivo .xlsx de la Lista de Raya." });
            return;
        }

        btn.disabled = true;
        btnTxt.textContent = "Leyendo archivo...";

        try {
            const file   = fileInput.files[0];
            const buffer = await file.arrayBuffer();
            const wb     = XLSX.read(buffer, { type: "array" });

            const encontrado = localizarEncabezado(wb);

            if (!encontrado) {
                Swal.fire({ icon: "error", title: "Formato no reconocido", text: 'No se encontraron las columnas "Empleado" e "I.S.R. Asimilados" en el archivo.' });
                return;
            }

            const registros = extraerRegistros(encontrado);

            if (registros.length === 0) {
                Swal.fire({ icon: "warning", title: "Sin datos", text: "No se encontraron filas de empleados con I.S.R. Asimilados en el archivo." });
                return;
            }

            const confirm = await Swal.fire({
                icon: "question",
                title: "¿Confirmar carga?",
                html: `Se encontraron <b>${registros.length}</b> empleados con I.S.R. Asimilados.<br>Se guardarán en Año <b>${anio}</b>, Periodo <b>${periodo}</b>.<br><br>Si ya había datos cargados para ese Año/Periodo, se reemplazan.`,
                showCancelButton: true,
                confirmButtonText: "Sí, cargar",
                cancelButtonText: "Cancelar"
            });

            if (!confirm.isConfirmed) return;

            btnTxt.textContent = "Guardando...";

            const response = await fetch("raya_asimilados_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "cargarRayaAsimilados", anio, periodo, registros })
            });

            const result = await response.json();

            if (result.status === "error") {
                Swal.fire({ icon: "error", title: "No se pudo cargar", text: result.message });
                return;
            }

            let html = `Se guardaron <b>${result.insertados}</b> registro(s).`;

            if (result.omitidos && result.omitidos.length > 0) {
                html += `<br><br>Se omitieron <b>${result.omitidos.length}</b> por no encontrar coincidencia en empl_usuarios:<br><br>` +
                    result.omitidos.map(n => `— ${n}`).join('<br>');
            }

            await Swal.fire({
                icon: (result.omitidos && result.omitidos.length > 0) ? "warning" : "success",
                title: "Carga completada",
                html: html
            });

            fileInput.value = "";
            document.getElementById("archivo-texto").textContent = "Da clic para elegir el archivo (o arrástralo aquí)";

        } catch (error) {
            Swal.fire({ icon: "error", title: "Error", text: String(error) });
        } finally {
            btn.disabled = false;
            btnTxt.textContent = "Cargar Archivo";
        }
    });

</script>
</html>