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

	<!-- Autocomplete -->
	<script src="./twitter/typeahead.bundle.js"></script>

	<!-- Datepicker -->
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

	<!-- CSS propio -->
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

	<!-- ============================================================
	     SECCIÓN 3: CSS LOCAL
	     ============================================================ -->
	<style type="text/css">
		.carobra {
			height: 25px !important;
			font-size: 12px !important;
		}

		.btn-carobra {
			height: auto;
			color: #FEFEFE;
			font-size: 12px;
			font-size: 1.1em;
			padding: 1px 5px;
			background-color: #2B73B6;
			font-family: 'Goudy Bookletter 1911', serif;
		}

		.btn-carobra span {
			color: #FEFEFE;
			font-size: 1.2em;
			font-weight: bold;
		}

		.btn-carobra:hover {
			cursor: pointer;
			background-color: #0055A0;
			box-shadow: 1px 1px 10px #141F78;
		}

		#btn-excel { cursor: pointer; }
	</style>
</head>

<?php
/* ============================================================
   SECCIÓN 4: PHP - INCLUDE DE CLASES
   ============================================================ */
include("./funciones/clas_carobra.php");
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
        mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "http://carobra.ddns.me:8888");
    }
    else
    {
        $_SESSION["ultimoAcceso"] = $fa;
        $_SESSION["autentifica"]  = "p";
    }

    if ($_SESSION["autentifica"] != "p")
    {
        session_destroy();
        mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "index.php");
    }
    else
    {
        reg_acceso($_SESSION["nombre"], "empleados_dp");
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
            titulo("Interfaces Pplus");
            $pag = "INTERFACES";
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

		$conecta  = new conector();
		$fecha    = addslashes(trim($_POST["fecha"]));
		$mensaje  = addslashes(trim($_POST["mensaje"]));
		$dispersa = addslashes(trim($_POST["dispersa"]));
?>

	<!-- ============================================================
	     SECCIÓN 7: HTML - FORMULARIO PRINCIPAL
	     ============================================================ -->
	<div class="container">
        <input type="hidden" id="mensaje" name="mensaje" value="<?= $mensaje; ?>">

        <div class="row pt-4 justify-content-center align-items-center text-center" style="margin-top: 10rem;">
            <div class="col-md-4">
                <input type="file" id="subir-archivo" name="subir-archivo"
                    accept=".xlsx, .xls" style="display: none;"
                    data-op="inter_transfer"
                    onchange="mostrarNombreArchivo(this)">

                <a class="btn btn-carobra btn-lg btn-block active p-1" id="btn-subir">
                    <span>Subir archivo con información</span>
                </a>
            </div>
        </div>
    </div>

<?php
		$divcontenido->close();
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

	$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
</script>

<?php } ?>

</body>

<!-- ============================================================
     SECCIÓN 9: JAVASCRIPT - EVENTOS Y FUNCIONES
     ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		/* --- Resize --- */
		$(window).resize(function ()
		{
			centra_contenedor();
		});
		$(window).resize();

		$('#men_pie').css("top", h);

		/* --- Menú --- */
		$('#tmenu td').click(function (evento)
		{
			var modulo = $(this).text().replace(/ /gi, "_");
			$("#opcion").load("menu.php?modulo=" + modulo);
		});

		/* --- Salir --- */
		$("#salir").click(function ()
		{
			window.location = "http://carobra.ddns.me";
		});

		/* --- Email --- */
		$("#email").click(function ()
		{
			window.open("http://webmail.carobra.com.mx/imp/login.php");
		});

		/* --- Subir archivo --- */
		$("#btn-subir").click(function ()
		{
			document.getElementById('subir-archivo').click();
		});

		// $("#btn-excel").click(function()
		// {
		// 	pag="rep_transfer_out.php?";
		//     window.open(pag, '_blank');
		// });

		// $("#getXLSX").click(function() {

		// 	$("#loader").css("display", "flex"); // mostrar loader

		// 	fetch("rep_transfer_out.php")
		// 		.then(response => response.blob())
		// 		.then(blob => {
		// 			const url = window.URL.createObjectURL(blob);
		// 			const a = document.createElement("a");
		// 			a.href = url;
		// 			a.download = "reporte_clawback.xlsx";
		// 			document.body.appendChild(a);
		// 			a.click();
		// 			a.remove();
		// 			$("#exampleModal").modal("hide");
		// 			$("#loader").hide(); // ocultar loader
		// 		})
		// 		.catch(error => {
		// 			console.error(error);
		// 			$("#loader").hide();
		// 			alert("Error al generar el archivo");
		// 		});

		// });

	});

	/* --- Confirmación y envío del archivo --- */
	function mostrarNombreArchivo(input)
	{
		if (input.files.length > 0)
		{
			let mensaje = "Archivo seleccionado: " + input.files[0].name + "\n\n ¿Subirlo a SISCA?";
			if (confirm(mensaje))
			{
				document.getElementById('op').value = input.dataset.op;
				document.getElementById("frm-envia").submit();
			}
		}
	}

    function mostrarNombreArchivo(input)
    {
        if (input.files.length > 0)
        {
            let nombreArchivo = input.files[0].name;

            Swal.fire({
                title: '¿Subir archivo?',
                text: "Archivo seleccionado: " + nombreArchivo,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, subir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#2B73B6'
            })
            .then(result =>
            {
                if (result.isConfirmed)
                {
                    Swal.fire({
                        title: 'Subiendo archivo...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    let formData = new FormData();
                    formData.append("subir-archivo", input.files[0]);


                    fetch("pplus.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data =>
                    {
                        if (data.ok)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Listo!',
                                html: `<b>Aprobados:</b> ${data.aprobados}<br><b>Rechazados:</b> ${data.rechazados}`,
                                confirmButtonColor: '#2B73B6'
                            }).then(() => location.reload());
                            
                        }
                        else
                        {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.mensaje,
                                confirmButtonColor: '#2B73B6'
                            });
                        }
                    })
                    .catch(error =>
                    {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: error,
                            confirmButtonColor: '#2B73B6'
                        });
                    });
                }
            });
        }
    }
</script>

</html>