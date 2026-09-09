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
								height: 30px !important;
								font-size: 12px !important;
							}

			.subrayado 	{
							width: 100%;
							font-size: 1em;
							font-weight: 600;
							min-height: 25px;	
							text-align: center;
							border-bottom: 2px solid #E5E5E5;	
						}

			.fondo {	background-color: rgba(219, 236, 255, 0.4);	}

			.btn-carobra	{
								height: 30px;
								font-size: 1.5em;
								padding: 1px 8px;
								background-color: #0055A0;
								font-family: 'Goudy Bookletter 1911', serif;
							}

			.btn-carobra span	{
									color: #FEFEFE;
									font-size: 1em;
									font-weight: bold;
								}

				.btn-carobra:hover	{
										cursor:pointer;
										background-color: #143B66;
										box-shadow: 1px 1px 10px #141F78;
									}

					.panel-retenciones	{
											margin-top: 35px;
											padding: 20px 15px;
										}

					.retencion-check {
										display: flex;
										align-items: center;
										min-height: 30px;
										margin-top: 18px;
										padding: 6px 10px;
										border: 1px solid #D6E1EC;
										border-radius: 4px;
										background: #F8FBFF;
									}

					.retencion-check .form-check-input {
															position: static;
															margin: 0 8px 0 0;
															cursor: pointer;
														}

					.retencion-check .form-check-label {
															margin-bottom: 0;
															color: #143B66;
															font-size: 12px;
															font-weight: 600;
															cursor: pointer;
														}

					.retencion-check:hover {
												border-color: #8DB4D8;
												background: #EEF6FF;
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
					titulo("Generar Pagos Retenciones");
				$pag="RETENCIONES";
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
						$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
	?>
						<div class="container">
								<form id="frm-archivo" action="excel_archivo_pagos_ret.php" method="post" target="_blank">
									<input type="hidden" id="semana_pago_archivo" name="semana_pago_archivo">
									<input type="hidden" id="bonos_per_archivo" name="bonos_per_archivo" value="0">
									<input type="hidden" id="bonos_equipo_archivo" name="bonos_equipo_archivo" value="0">
								</form>
								<form id="frm-generar" action="excel_ajustes_ret.php" method="post" target="_blank">
									<input type="hidden" id="semana_pago_generar" name="semana_pago_generar">
									<input type="hidden" id="bonos_per_generar" name="bonos_per_generar" value="0">
									<input type="hidden" id="bonos_equipo_generar" name="bonos_equipo_generar" value="0">
								</form>
							<div class="row panel-retenciones">
									<div class="col-12 col-sm-4 text-left">
										<label for="sel-semana-pago" class="etiqueta mb-0 pb-0">Semana Pago</label>
										<select id="sel-semana-pago" name="sel-semana-pago" class="form-control form-control-sm custom-select carobra pt-0 pb-0 pl-2">
										<option value=""></option>
<?php
										$sql="Select * from 
											  (Select distinct case when left(semana,1)='S' then concat(SUBSTR(semana, 2,3),' ',trim(right(semana,16)))
											            else concat(left(semana,3),' ',trim(right(semana,16)))
											           end as semana
											   from clawback) c
											  where (left(semana,3) * 1) >= 295
											  order by semana";
										$bd=$conecta->consulta($sql);
										if ($bd->num_rows != 0)
										{
											while($tb=mysqli_fetch_array($bd))
												echo "<option value='" . $tb["semana"] . "'>" . $tb["semana"] . "</option>";
										}
	?>
										</select>
									</div>
									<div class="col-12 col-sm-4 text-left">
										<div class="form-check retencion-check">
											<input type="checkbox" class="form-check-input" id="chk-bonos-per" name="chk-bonos-per">
											<label class="form-check-label" for="chk-bonos-per">Incentivos individuales</label>
										</div>
									</div>
									<div class="col-12 col-sm-4 text-left">
										<div class="form-check retencion-check">
											<input type="checkbox" class="form-check-input" id="chk-bonos-equipo" name="chk-bonos-equipo">
												<label class="form-check-label" for="chk-bonos-equipo">Incentivos por equipo</label>
										</div>
									</div>
								</div>
							<div class="row mt-5 pt-5">
								<div class="col-12 col-sm-3 offset-sm-3 text-left">
									<a class="btn btn-carobra btn-sm btn-block active p-1" id="btn-archivo-pagos">
										<span>Archivo de Pagos</span>
									</a>
								</div>
								<div class="col-12 col-sm-3 text-left">
									<a class="btn btn-carobra btn-sm btn-block active p-1" id="btn-generar-pagos">
										<span>Generar pagos</span>
									</a>
								</div>
							</div>
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

				function actualiza_parametros_bonos(sufijo)
				{
					$("#bonos_per_" + sufijo).val($("#chk-bonos-per").is(":checked") ? 1 : 0);
					$("#bonos_equipo_" + sufijo).val($("#chk-bonos-equipo").is(":checked") ? 1 : 0);
				}

				$("#btn-archivo-pagos").click(function()
				{
					if($("#sel-semana-pago").val()!="")
					{
						$("#semana_pago_archivo").val($("#sel-semana-pago").val());
						actualiza_parametros_bonos("archivo");
						$("#frm-archivo").submit();
					}
					else
						alert("No se ha seleccionado la semana de pago");
				});

				$("#btn-generar-pagos").click(function()
				{
					if($("#sel-semana-pago").val()!="")
					{
							if(confirm("Se generarán los pagos de retenciones para la semana seleccionada. ¿Aceptar?"))
							{
								$("#semana_pago_generar").val($("#sel-semana-pago").val());
								actualiza_parametros_bonos("generar");
								$("#frm-generar").submit();
							}
					}
					else
						alert("No se ha seleccionado la semana de pago");
				});
			});
		</script>
</html>
