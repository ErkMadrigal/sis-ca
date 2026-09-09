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
      		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");
    	}
    	else
    	{
    		$_SESSION["ultimoAcceso"] = $fa;
    		$_SESSION["autentifica"] = "p";
   		}

		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
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
				titulo("Interface AV5");
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
//----------------------------------------------------------------------------Declaración de objetos
					$conecta= new conector();

					$fecha=addslashes(trim($_POST["fecha"]));
					$mensaje=addslashes(trim($_POST["mensaje"]));
					$dispersa=addslashes(trim($_POST["dispersa"]));
?>
					<div class="container">
						<input type="hidden" id="mensaje" name='mensaje' value="<?= $mensaje; ?>">
						<form  id="frm-envia" action="interfaces2.php" method="post" enctype="multipart/form-data">
							<input type="hidden" id="op" name='op'>
							<div class='row mt-4 pt-4'>
								<div class="col-4"></div>
								<div class="col-4">
									<input type="file" id="subir-archivo" name="subir-archivo" accept=".xlsx, .xls" style="display: none;" data-op="inter_av5" onchange="mostrarNombreArchivo(this)">
									<a class="btn btn-carobra btn-lg btn-block active p-1" id="btn-subir">
										<span>Subir archivo con información</span>
									</a>
								</div>
							</div>
						</form>
<?
						if ($_POST)
						{
							if($mensaje != "")
							{
?>
							<script type="text/javascript">
								alert(document.getElementById('mensaje').value);
							</script>
<?
							}
						}
?>
					</div>
<?
				$divcontenido->close();
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
  		  		if(confirm("Antes de subir la interface AV5, debes asegurarte de haber subido antes la interface AV1000, de lo contrario no se ligarán los folios de AVS con los de afore\n\n¿Continuar?"))
  		  			document.getElementById('subir-archivo').click();
  		  	});

		});

		function mostrarNombreArchivo(input) 
		{
            if (input.files.length > 0) 
            {
                let mensaje="Archivo seleccionado: " + input.files[0].name + "\n\n ¿Subirlo a SISCA?";
                if(confirm(mensaje))
                {
                	document.getElementById('op').value=input.dataset.op;
                	document.getElementById("frm-envia").submit();
                }
            }
        }
	</script>
</html>