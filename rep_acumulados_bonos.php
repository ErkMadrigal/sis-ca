<?php
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
								height: 25px;
								color: #FEFEFE;
								font-size: 12px;
								font-size: 1.1em;
								padding: 1px 5px;
								background-color: #2B73B6;
								font-family: 'Goudy Bookletter 1911', serif;
							}

			.btn-carobra span	{
									color: #FEFEFE;
									font-size: 12px;
									font-weight: bold;
								}

			.btn-carobra:hover	{
									cursor:pointer;
									background-color: #0055A0;
									box-shadow: 1px 1px 10px #141F78;
								}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?php
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
				titulo("Acumulados de bonos por periodo comercial");
				$pag="SEGURIDAD";
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
				$divcontenido->css("overflow-y","auto");
//----------------------------------------------------------------------------Declaración de objetos
					$conecta= new conector();
					$anio=isset($_POST["sel-anio"]) ? trim($_POST["sel-anio"]) : "";
					$periodo=isset($_POST["sel-periodo"]) ? trim($_POST["sel-periodo"]) : "";
					$chk_factura=isset($_POST["chk-factura"]) ? 1 : 0;
					$chk_ajuste=isset($_POST["chk-ajuste"]) ? 1 : 0;
					$chk_dispersa=isset($_POST["chk-dispersa"]) ? 1 : 0;
?>
						<div class="container">
							<form  id="frm-filtro" action="gen_pagos_puntos_nap.php" method="get">
								<div class='row pb-3 mb-3'>
									<div class="col-2 text-left mr-0 pr-1 pb-0 mb-0">
										<label for='sel-anio' class='etiqueta mb-0 pb-0'>Año</label>
										<select id="sel-anio" name="sel-anio" class="form-control form-control-sm custom-select carobra pt-0 pb-0 pl-2">
											<option value=""></option>
											<?php for($i = date('Y'); $i >= 2026; $i--): ?>
												<option value="<?= $i ?>" <?= ($sel_anio==$i) ? "selected" : "" ?>><?= $i ?></option>
											<?php endfor; ?>
										</select>
									</div>
									<div class="col-3 text-left mr-0 pr-1 pb-0 mb-0">
										<label for='sel-periodo' class='etiqueta mb-0 pb-0'>Periodo</label>
										<select id="sel-periodo" name="sel-periodo" class="form-control form-control-sm custom-select carobra pt-0 pb-0 pl-2">
											<option value=""></option>
											<?php
												$sql = "Select distinct LEFT(campana,10) as periodo 
														FROM carobra_2014.fec_cierre_campana 
														WHERE STR_TO_DATE(fec_ini,'%d/%m/%Y') >= '2025-12-20'
														ORDER BY periodo";
												$resPeriodos = $conecta->consulta($sql);
												while($p = mysqli_fetch_assoc($resPeriodos)):
											?>
												<option value="<?= htmlspecialchars($p['periodo']) ?>" <?= ($sel_periodo==$p['periodo']) ? "selected" : "" ?>><?= htmlspecialchars($p['periodo']) ?></option>
											<?php endwhile; ?>
										</select>
									</div>
								</div>
								<div class='row pt-3 mt-3 justify-content-end pr-5'>
									<div class="col-2 text-left mr-0 pr-1 pb-0 mb-0 pt-5">
										<a class="btn btn-carobra btn-sm btn-block active p-1" id="btn-generar">
											<span>Generar</span>
										</a>
									</div>
								</div>
							</form>
						</div>
<?php
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

  		  	$("#btn-generar").click(function()
  		  	{
  		  		if($("#sel-anio").val()=="")
  		  		{
  		  			alert("Selecciona el año");
  		  			$("#sel-anio").focus();
  		  			return false;
  		  		}
  		  		else
  		  		{
	  		  		if($("#sel-periodo").val()=="")
	  		  		{
	  		  			alert("Selecciona el periodo");
	  		  			$("#sel-periodo").focus();
	  		  			return false;
	  		  		}
	  		  		else
	  		  		{
						pag="analisis_bonos.php?anio=" + $("#sel-anio").val() + "&periodo=" + $("#sel-periodo").val();
		  		    	window.open(pag, '_blank');
	  		  		} 
  		  		}
  		  	});
		});
	</script>
</html>
