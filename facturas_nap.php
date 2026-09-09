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

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>

		<!-- compiled function autocomplete -->
		<script src="./twitter/typeahead.bundle.js"></script>

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<style>
			table th { background: #F2F2F2; }

			.inputfile {
						    width: 0.1px;
						    height: 0.1px;
						    opacity: 0;
						    overflow: hidden;
						    position: absolute;
						    z-index: -1;
						}

			.label-afpdf, .label-afxml	{
										    max-width: 80%;
										    font-size: 10px;
										    font-weight: 700;
										    white-space: nowrap;
										    cursor: pointer;
										}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));

		$conecta= new conector();
		
		$fec_bloqueo="";
		$bd=$conecta->consulta("Select * from bloqueo_pago");
		if ($bd->num_rows != 0)
		{
			$tb=mysqli_fetch_array($bd);
			$fec_bloqueo=$tb['fecha'];
			$hora_bloqueo=$tb['hora'];
		}
		if(date("d/m/Y")==$fec_bloqueo)
		{
			if(date("G")>= $hora_bloqueo)
				$periodo=1500;
		}
$periodo=300;
     	if($periodo > 600)
     	{
     		if($fec_bloqueo=="")
     		{
     			if($_SESSION["nombre"]!="Administrador del Sistema")
     			{
		      		session_destroy();
		      		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");     				
     			}
     		}
     		else
     		{
     			if($_SESSION["nombre"]!="Administrador del Sistema")
      				mensaje("Página temporalmente bloqueada por el proceso de pagos...","contiene.php");
     		}
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
			reg_acceso($_SESSION["nombre"],"facturas");
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
				titulo("Facturas NAP");
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
//----------------------------------------------------------------------------Declaración de objetos
					$fil=  "Select u.clave from usuarios u inner join empleados e on (u.clave=e.clave) 
							where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
					$tb=$conecta->consulta($fil);
					$row = $tb->fetch_array(MYSQLI_NUM);
					$clave=$row[0];
					
					$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
					$cl=0;
					if($clave=="CAROBRA")
					{
						$cl=1;
						$nivel=3;
						$clave="EDLOAL";
						$nombre="EDMUNDO LOPEZ ALFARO";
					}
					else
					{
						if($clave=="TEMPORAL")
						{
							$cl=2;
							$nivel=3;
							$clave="MEMTZHDZ";
							$nombre="MELQUIADES MARTINEZ HERNANDEZ";
						}
						else
							$nombre=utf8_decode($_SESSION["nombre"]);
					}
?>
					<div class="container">
<?php
						if(count($_POST) == 0)
						{
?>
							<form  id="frm-envio" action="facturas_nap.php" method="post">
<?php
								if($nivel != 3)
								{
?>
									<div class='row'>
										<div class='col-5 text-left pt-0'>
											<label for='txt-asesor' class='control-label etiqueta'>Propietario NAP</label>
											<input type="text" id="txt-asesor" name="txt-asesor" class="form-control p-1" style='height: 24px;' data-n='<?php echo $nivel; ?>'>
										</div>
										<div class='col-2 pt-4 ml-0 pl-0 text-left'>
											<a class="btn btn-sm active pt-1" id="btn-buscar" style='height: 25px;'>
												<span><img src="./img/lupa.png" width="20" height="20"></span> 
											</a>
										</div>
									</div>
<?php
								}
								else
								{
?>
									<input type='hidden' id="txt-asesor" name='txt-asesor' value='<?php echo $nombre;?>'>
<?php
								}
?>
							</form>
<?php
							if($nivel==3)
							{
?>
<script type="text/javascript">
								$("#frm-envio").submit();
</script>
<?php
							}
						}
						else
						{
							$mensaje=recibe_POST("mensaje");
							$asesor=recibe_POST("txt-asesor");

							if($mensaje!="")
								mensaje(utf8_encode($mensaje));

							$fil=  "Select u.clave from usuarios u inner join empleados e on (u.clave=e.clave) 
									where nombre='" . utf8_decode($asesor) . "'";
							$tb=$conecta->consulta($fil);
							$row = $tb->fetch_array(MYSQLI_NUM);
							$clave=$row[0];

							$query="Select anio,periodo,monto,no_factura,nom_pdf,nom_xml,fec_fac,
										  CASE
											  WHEN estatus_fac=0 THEN 'Factura pendiente'
										      WHEN estatus_fac=1 THEN 'PDF pendiente'
										      WHEN estatus_fac=2 THEN 'XML pendiente'
										      WHEN estatus_fac=3 THEN 'Factura errónea'
										      WHEN estatus_fac=4 THEN 'Factura OK'
										  END as estatus_fac,fec_subir
									from facturas_nap where clave='$clave'
									 and (reg_fiscal='Personas Fisicas' or reg_fiscal='RESICO')
									order by anio,periodo desc";
							$q_rfc="Select rfc from empleados where clave='$clave'";

							$bd=$conecta->consulta($q_rfc);
							$tb=mysqli_fetch_array($bd);
							$rfc=$tb["rfc"];
?>
							<div class='row'>
								<div class='col-10 text-center pt-0'>
<?php
									if($cl==1)
									{
?>
										<h5><?php echo "Relación de facturas de Usuario de Pruebas"; ?></h5>
<?php
									}
									else
									{
										if($cl==2)
										{
?>
											<h5><?php echo "Relación de facturas de Usuario Temporal"; ?></h5>
<?php
										}
										else
										{
?>
											<h5><?php echo "Relación de facturas de " . $asesor; ?></h5>
<?php
										}
									}
?>
								</div>
							</div>
							<form id='frm-factura' action='./ajax/subir_factura_nap.php' method='post' enctype='multipart/form-data'>
								<input type='hidden' id='info' name='info'>
							</form>
							<div class="row pt-3 pl-1 pr-1">
								<div class="col-12 p-0">
									<table class='table table-sm' style='font-size: 9px;'>
										<tr style='border-bottom: 2px solid #CCCCCC;'>
											<th>Año</th>
											<th>Periodo</th>
											<th>RFC</th>
											<th>No. factura</th>
											<th>Monto</th>
											<th>Fec. Factura</th>
											<th>Estatus Factura</th>
											<th></th>
											<th></th>
										</tr>
<?php
										$i=1;
										$bd=$conecta->consulta($query);
										$color="#F2F2F2";
										while($tb=mysqli_fetch_array($bd))
										{
											$style="";
											if($tb['estatus_fac']=='Factura errónea')
												$style="color:red;";
											if($color=="#F2F2F2")
												$color="#FFFFFF";
											else
												$color="#F2F2F2";
?>
											<tr style='<?php echo "background: " . $color . ";"; ?>'>
												<td><?php echo $tb['anio']; ?></td>
												<td><?php echo $tb['periodo']; ?></td>
												<td><?php echo $rfc; ?></td>
												<td><?php echo $tb['no_factura']; ?></td>
												<td>
													<?php echo "$" . number_format($tb['monto'], 2, '.', ','); ?>
												</td>
												<td><?php echo $tb['fec_fac']; ?></td>
												<td style="<?php echo $style; ?> font-weight: bold;"><?php echo $tb['estatus_fac']; ?></td>
												<td></td>
												<td></td>
											</tr>
											<tr style='<?php echo "background: " . $color . ";"; ?> border-bottom: 2px solid #CCCCCC;'>
												<td colspan="2" class='text-right' style='font-weight: bold;'>PDF:</td>
												<td colspan="2"><?php echo $tb['nom_pdf']; ?></td>
												<td class='text-right' style='font-weight: bold;'>XML:</td>
												<td colspan="2"><?php echo $tb['nom_xml']; ?></td>
												<td>
<?php
													if($tb['estatus_fac'] != 'Factura OK' or $nivel==12)
													{
?>
														<label for="<?php echo 'af-pdf-' . $i; ?>" class='label-afpdf'>
															<span id="<?php echo 'safpdf-' . $i; ?>" class="span-afpdf">
																Subir PDF
															</span>
														</label>
														<input type="file" name="<?php echo 'af-pdf-' . $i; ?>" id="<?php echo 'af-pdf-' . $i; ?>" class="inputfile" data-clave="<?php echo $clave; ?>" data-fec="<?php echo $tb['anio'] . "|" . $tb['periodo']; ?>" data-monto="<?php echo $tb['monto']; ?>">
<?php
													}
?>
												</td>
												<td>
<?php
													if($tb['estatus_fac'] != 'Factura OK' or $nivel==12)
													{
?>
														<label for="<?php echo 'af-xml-' . $i; ?>" class='label-afxml'>
															<span id="<?php echo 'safxml-' . $i; ?>" class="span-afxml">
																Subir XML
															</span>
														</label>
														<input type="file" name="<?php echo 'af-xml-' . $i; ?>" id="<?php echo 'af-xml-' . $i; ?>" class="inputfile" data-clave="<?php echo $clave; ?>" data-fec="<?php echo $tb['anio'] . "|" . $tb['periodo']; ?>" data-monto="<?php echo $tb['monto']; ?>">
<?php
													}
?>
												</td>
											</tr>
<?php
											$i++;
										}
?>
									</table>
								</div>
							</div>
<?php
						}
?>
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
   		 	$('#men_pie').css("background","red");

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

			$("#btn-buscar").css("color","#FEFEFE");
			$("#btn-buscar").css("font-size","1.1em");
			$("#btn-buscar").css("padding","1px 5px");
			$("#btn-buscar").css("background-color","#2B73B6");

			$("#contenido").css("height","400px");
			$("#contenido").css("overflow-y","auto");

  		  	b_autocomplete($("#txt-asesor"),"prop_nap",4,$("#txt-asesor").data("n"));

  		  	$("table th, table td").css("text-align","left");

  		  	$("#btn-buscar").click(function()
  		  	{
	  			if($("#txt-asesor").val() != '')
	  				$("#frm-envio").submit();
	  			else
	  				alert("No se ha ingresado el nombre del asesor");
  		  	});

  		  	$("input[type=file]").change(function()
  		  	{
  		  		$(this).appendTo("#frm-factura");

  		  		var tipo=this.id.split("-");

  		  		var archivo = $(this).prop("files")[0];
  		  		var e_arc=$(this).val().split(".");

  		  		if(tipo[1].toUpperCase()==e_arc[1].toUpperCase())
  		  		{
  		  			let info=tipo[1] + "|" + tipo[2] + "|" + $(this).data("clave") + "|" + $(this).data("fec") + "|" + $(this).data("monto") + "|" + this.id;
  		  			$("#info").val(info);
  		  			$("#frm-factura").submit();
  		  		}
  		  		else
  		  			alert("El archivo que quieres subir no es un archivo " + tipo[1].toUpperCase());
  		  	});
		});
	</script>
</html>
