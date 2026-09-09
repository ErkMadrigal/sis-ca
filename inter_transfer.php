<?
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
		<title>CAROBRA Consultores Especializados</title>
		<link rel="shortcut icon" href="./img/favicon.ico">
		<script type="text/javascript" src="./js/carobra.js"></script>

		<script src="./jquery/jquery-3.3.1.min.js"></script>
<!--		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>	-->

		<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
		<script src="./popper/popper.js"></script>
		<script src="./bootstrap/js/bootstrap.min.js"></script>

		<!-- compiled function autocomplete -->
		<script src="./twitter/typeahead.bundle.js"></script>

		<!-- compiled function datepicker -->
		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<!-- SweetAlert2: misma versión/CDN que ya usa el resto del sitio (ej. seg_kpis.php) -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

		<style type="text/css">
			.carobra	{
							height: 25px !important;
							font-size: 12px !important;
						}

			.btn-carobra	{
								height: auto;
								color: #FEFEFE;
								font-size: 12px;
								font-size: 1.1em;
								padding: 1px 5px;
								background-color: #2B73B6;
								font-family: 'Goudy Bookletter 1911', serif;
							}

			.btn-carobra span	{
									color: #FEFEFE;
									font-size: 1.2em;
									font-weight: bold;
								}

			.btn-carobra:hover	{
									cursor:pointer;
									background-color: #0055A0;
									box-shadow: 1px 1px 10px #141F78;
								}

			#btn-excel	{	cursor: pointer;	}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
     	if($periodo > 6000)
     	{
      		session_destroy();
      		mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","http://carobra.ddns.me:8888");
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
			reg_acceso($_SESSION["nombre"],"empleados_dp");
//---------------------------------------------------------------------------Recepción de parametros
			$_SESSION["autentifica"]="";
//--------------------------------------------------------------------------------------------------
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
				titulo("Interfaces transfer out");
				$pag="INTERFACES";
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
<?
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
				$divcontenido->css("overflow-y","auto");
?>
					<div class="container">
						<!-- La subida ya no hace POST tradicional a esta misma página --
							 se manda por fetch/FormData directo a inter_transfer_api.php
							 (ver JS abajo), así que este <form> ya no necesita action ni
							 method: solo envuelve el <input type="file"> y el botón. -->
						<form id="frm-envia">
							<div class="row pt-4 justify-content-center align-items-center text-center" style="margin-top: 10rem;">
								<div class="col-md-4">
									<input type="file" id="subir-archivo" name="subir-archivo"
										accept=".xlsx, .xls" style="display: none;"
										data-op="inter_transfer"
										onchange="confirmarYSubirArchivo(this)">
									<a class="btn btn-carobra btn-lg btn-block active p-1" id="btn-subir">
										<span>Subir archivo con información</span>
									</a>
								</div>
								<div class="col-md-2 d-flex justify-content-center align-items-center">
									<img src="./img/excel.png" class="btn-filtro" id="btn-excel" width="30px">
								</div>

							</div>
						</form>
					</div>
<?
				$divcontenido->close();
			$divpie->close();
?>

		<div id="loader" style="
			display:none;
			position: fixed;
			top:0;
			left:0;
			width:100%;
			height:100%;
			background: rgba(0,0,0,0.5);
			z-index:9999;
			justify-content:center;
			align-items:center;
		">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div id="loader-texto">Generando Excel...</div>
			</div>
		</div>

		<!-- Button trigger modal -->
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" id="btn-modal" style="display:none;">
		Launch demo modal
		</button>

		<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Generar Excel</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="formExcel" class="needs-validation" novalidate>
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label>Fecha de inicio</label>
										<input type="date" class="form-control" id="fecha_inicio" required>
										<div class="invalid-feedback">Selecciona la fecha de inicio</div>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label>Fecha de fin</label>
										<input type="date" class="form-control" id="fecha_fin" required>
										<div class="invalid-feedback">Selecciona la fecha de fin</div>
									</div>

								</div>

							</div>

						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary" id="getXLSX">Generar Excel</button>
					</div>
				</div>
			</div>
		</div>


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
<?php
		}
?>
    </body>

	<script type="text/javascript">
		jQuery(document).ready(function()
	   	{
   		   	$(window).resize(function ()
   	    	{
   	    		centra_contenedor();
	   	    });
   		   	$(window).resize();

   		 	$('#men_pie').css("top",h);

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="http://carobra.ddns.me";
  		  	});

  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

  		  	$("#btn-subir").click(function()
  		  	{

  		  			document.getElementById('subir-archivo').click();
  		  	});

			// $("#btn-excel").click(function()
        	// {
	  		// 	pag="rep_transfer_out.php?";
	  		//     window.open(pag, '_blank');
        	// });

		});

		/* Loader compartido por subida de archivo y generación de Excel --
		   antes cada uno mostraba/ocultaba $('#loader') a mano y el texto
		   quedaba fijo en "Generando Excel...", que no aplicaba mientras
		   se subía un archivo. Ahora acepta el texto a mostrar. */
		function mostrarLoader(texto) {
			document.getElementById('loader-texto').textContent = texto || 'Cargando...';
			$('#loader').css('display', 'flex');
		}
		function ocultarLoader() {
			$('#loader').hide();
		}

		/* Antes: confirm() nativo del navegador + document.getElementById('frm-envia').submit()
		   (POST tradicional a esta misma página, que ni siquiera procesaba el archivo --
		   el procesamiento real vive en inter_transfer_api.php). Ahora se confirma con
		   SweetAlert2 (igual que el resto del sitio) y se sube por fetch/FormData
		   directo a inter_transfer_api.php, sin recargar la página. */
		function confirmarYSubirArchivo(input) {
			if (!input.files.length) return;

			const archivo = input.files[0];

			Swal.fire({
				icon: 'question',
				title: 'Subir archivo',
				html: `Archivo seleccionado: <strong>${archivo.name}</strong><br>¿Subirlo a SISCA?`,
				showCancelButton: true,
				confirmButtonText: 'Sí, subir',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#2B73B6',
			}).then((resultado) => {
				if (resultado.isConfirmed) {
					subirArchivoInterfaces(input, archivo);
				} else {
					input.value = ''; // permite volver a elegir el mismo archivo si cancela
				}
			});
		}

		function subirArchivoInterfaces(input, archivo) {
			const formData = new FormData();
			formData.append('subir-archivo', archivo);
			formData.append('op', input.dataset.op);

			mostrarLoader('Subiendo archivo...');

			fetch('inter_transfer_api.php', {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.then(resp => {
				ocultarLoader();
				input.value = '';

				Swal.fire({
					icon: resp.success ? 'success' : 'error',
					title: resp.success ? 'Listo' : 'Error',
					html: (resp.mensaje || 'Ocurrió un error inesperado.').replace(/\n/g, '<br>'),
				});
			})
			.catch(err => {
				console.error(err);
				ocultarLoader();
				input.value = '';
				Swal.fire('Error', 'No se pudo conectar con el servidor. Intenta de nuevo.', 'error');
			});
		}

		let btn_excel = document.getElementById("btn-excel");
		let btn_modal = document.getElementById("btn-modal");

		btn_excel.addEventListener("click", function () {
			btn_modal.click(); // abre modal
		});

		const form = document.getElementById("formExcel");
		const fecha_inicio = document.getElementById("fecha_inicio");
		const fecha_fin = document.getElementById("fecha_fin");

		// Helpers
		const setValid = (input) => {
			input.classList.remove("is-invalid");
			input.classList.add("is-valid");
		};

		const setInvalid = (input, message = null) => {
			input.classList.remove("is-valid");
			input.classList.add("is-invalid");

			if (message) {
				const feedback = input.nextElementSibling;
				if (feedback) feedback.textContent = message;
			}
		};

		// Validar campo individual
		const validarCampo = (input) => {
			if (!input.value) {
				setInvalid(input);
				return false;
			} else {
				setValid(input);
				return true;
			}
		};

		// Validar rango de fechas
		const validarRangoFechas = () => {

			if (!fecha_inicio.value || !fecha_fin.value) return true;

			const inicio = new Date(fecha_inicio.value);
			const fin = new Date(fecha_fin.value);

			if (fin < inicio) {
				setInvalid(
					fecha_fin,
					"La fecha fin no puede ser menor a la fecha inicio"
				);
				return false;
			}

			// restaurar mensaje original
			fecha_fin.nextElementSibling.textContent = "Selecciona la fecha de fin";

			setValid(fecha_fin);
			return true;
		};

		// Eventos en tiempo real (INPUT)
		[fecha_inicio, fecha_fin].forEach(input => {
			input.addEventListener("input", () => {
				validarCampo(input);
				validarRangoFechas();
			});
		});

		fecha_inicio.addEventListener("change", () => {
			validarCampo(fecha_inicio);
			validarRangoFechas();
		});

		fecha_fin.addEventListener("change", () => {
			validarCampo(fecha_fin);
			validarRangoFechas();
		});


		// SUBMIT (botón generar)
		$("#getXLSX").click(function () {

			let formData = new FormData();

			formData.append("fecha_inicio", fecha_inicio.value);
			formData.append("fecha_fin", fecha_fin.value);

			let valido = true;

			[fecha_inicio, fecha_fin].forEach(input => {
				if (!validarCampo(input)) valido = false;
			});

			if (!validarRangoFechas()) valido = false;

			if (!valido) return;

			mostrarLoader('Generando Excel...');

			fetch("rep_transfer_out.php", {
				method: "POST",
				body: formData
			})
			.then(res => res.blob())
			.then(blob => {

				const url = window.URL.createObjectURL(blob);
				const a = document.createElement("a");
				a.href = url;
				a.download = "reporte_clawback.xlsx";
				document.body.appendChild(a);
				a.click();
				a.remove();

				$("#exampleModal").modal("hide");
				ocultarLoader();

				resetForm();

			})
			.catch(err => {
				console.error(err);
				ocultarLoader();
				Swal.fire('Error', 'Error al generar el archivo.', 'error');
			});

		});

		// Reset limpio
		function resetForm() {
			form.reset();

			[fecha_inicio, fecha_fin].forEach(input => {
				input.classList.remove("is-valid", "is-invalid");
			});
		}

// Reset al cerrar modal
$('#exampleModal').on('hidden.bs.modal', resetForm);
	</script>
</html>