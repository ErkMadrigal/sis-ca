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
			reg_acceso($_SESSION["nombre"],"emple_usuario");
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
				titulo("Empledos Usuario");
				$pag="REC_HUMANOS";
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

						$query="Select clave from empl_usuarios where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
						$bd=$conecta->consulta($query);
						$tb=mysqli_fetch_array($bd);
						$clav=$tb['clave'];

					if($nivel==14 or $nivel==12 or $nivel==1 or $nivel==6)
					{					
?>
						<div class="container">
							<input type="hidden" id="nivel" name='nivel' value=<?php echo $nivel; ?>>
							<input type="hidden" id="nom_usu" name='nom_usu' value=<?php echo str_replace(" ","_",$_SESSION["nombre"]); ?>>
							<form  id="frm-filtro" action="emple_usuario.php" method="post">
								<input type="hidden" id="no-ct" name='no-ct'>
								<div class='row'>
									<div class="col-12 col-sm-5 text-left mr-0 pr-1">
										<label for='txt-empleado-u' class="etiqueta mb-0 pb-0">Empleado</label>
										<input type="text" class="form-control form-control carobra" id="txt-empleado-u" name="txt-empleado-u" placeholder="Nombre empleado">
									</div>
									<div class='col-1 text-left ml-0 pl-0 mr-0' style='padding-top: 18px !important;'>
										<a class="btn btn-carobra btn-sm active pt-1" id="btn-buscar" style='height: 25px;'>
											<span><img src="./img/lupa.png" width="15" height="15"></span> 
										</a>
									</div>
									
								</div>
							</form>
							<div class='row pt-1 pr-3'>
								<div class="col-12 p-0"><hr style="background: #CCCCCC; box-shadow: 2px 2px 5px #6E6E6E;"></div>
							</div>
<?php
							if(count($_POST) != 0)
							{
								$empleado=addslashes(trim($_POST["txt-empleado-u"]));
								$nombr=explode("(",$empleado);
								$empleado_u=$nombr[0];
								$nombre=$clave=$contrasena="";
								$no_empleado=$activo=0;

								if($empleado_u!="")
								{
									$query="Select e.clave,e.no_empleado,u.nombre,u.contrasena,
												   u.activo,celular 
											from empleados e
										    	inner join usuarios u on (e.clave=u.clave)
											where u.nombre='$empleado_u'";

									$bd=$conecta->consulta($query);
									$tb=mysqli_fetch_array($bd);
									$nombre=$tb["nombre"];
									$clave=$tb["clave"];
									$contrasena=$tb["contrasena"];
									$no_empleado=$tb["no_empleado"];
									$activo=$tb["activo"];
									$cel=$tb["celular"];
								}

								if($activo==1)
									$estatus="Activo";
								else
									$estatus="Inactivo";
?>
								<form  id="frm-envio" action="emple_usuario.php" method="post" autocomplete="off">
									<div class="row">
										<br><br><br>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0 '>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>Clave: </label>
											<input type="hidden" name="txt-empleado-u" id="txt-empleado-u" value="<?php echo $nombre; ?>">
										</div>
										<div class="col-12 col-sm-4 text-left mr-0 pr-1 pb-0 mb-0">
											<label><?php echo $clave; ?></label>
										</div>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0'>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>No_empleado: </label>
										</div>
										<div class="col-12 col-sm-4 text-left mr-0 pr-1 pb-0 mb-0">
											<label><?php echo $no_empleado; ?></label>
										</div>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0'>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>Nombre: </label>
										</div>
										<div class="col-12 col-sm-6 text-left mr-0 pr-1 pb-0 mb-0">
											<label><?php echo $nombre; ?></label>
										</div>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0'>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>Estatus: </label>
										</div>
										<div class="col-12 col-sm-4 text-left mr-0 pr-1 pb-0 mb-0">
											<label><?php echo $estatus; ?></label>
										</div>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0'>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>Celular: </label>
										</div>
										<div class="col-12 col-sm-4 text-left mr-0 pr-1 pb-0 mb-0">
											<label><?php echo $cel; ?></label>
										</div>
									</div>
									<div class='row pt-1 pr-3 pb-0 mb-0'>
<?php 
									if ($clav=="administra") 
									{
?>
										<div class="col-12 col-sm-5 text-right mr-0 pr-1 pb-0 mb-0">
											<label>Contraseña: </label>
										</div>
										<div class="col-12 col-sm-4 text-left mr-0 pr-1 pb-0 mb-0">
											<label> <?php echo $contrasena; ?></label>
										</div>
<?php 
									}
?>	
									</div>
								</form>
<?php
							}
?>
						</div>
<?php
					}
					else
						mensaje("No tienes permisos para consultar esta página","contiene.php");
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

  		  	b_autocomplete($("#txt-empleado-u"),"empleados_usu",2,$("#nivel").val());

        	$("#btn-buscar").click(function()
        	{	
        		$("#frm-filtro").submit();
        	});
        	
		});
	</script>
</html>