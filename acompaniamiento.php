<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CAROBRA Consultores Especializados</title>
		<link rel="shortcut icon" href="./img/favicon.ico">
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="./jquery/ui/css/ui-lightness/jquery-ui-1.8.16.custom.css"/>
		<script type="text/javascript" src="./js/jquery.formatCurrency-1.4.0.js"></script>
		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="./js/carobra.js"></script>
	</head>
	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg)); 
    	$periodo=3000;
     	if($periodo > 1200)
     	{
      		//session_destroy();
      		//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
      		//mensaje("Esta pagina ha sido bloqueada por obsoleta","contiene.php");
			  mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
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
			reg_acceso($_SESSION["nombre"],"acompaniamiento");

			$_SESSION["autentifica"]="";

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
				titulo("Reparar tablas");
				$pag="PRODUCCION";
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
					$conecta= new conector();
					$txtfolio= new textbox("txt-folio");
					$imgaceptar=new img("imgaceptar","./img/aceptar1.png","./img/aceptar2.png","boton_tabla");
//--------------------------------------------------------------------------------------------------
					if (!$_POST)
					{
						$fec_bloqueo="";
						$bd=$conecta->consulta("Select * from bloqueo_pago");
						if ($bd->num_rows != 0)
						{
							$tb=$bd->fetch_array(MYSQLI_NUM);
							$fec_bloqueo=$tb[0];
							$hora_bloqueo=$tb[1];
						}
						$estatus=0;
						if(date("d/m/Y")==$fec_bloqueo)
						{
							if(date("G")>= $hora_bloqueo)
								$estatus=3;
						}
?>
						<br><br><br><br><br><br><br><br>
						<div id="info"></div>
						<form  id="frm-envio" action="acompaniamiento.php" method="post">
							<input type='hidden' id='bloqueo' name='bloqueo' value=<?php echo $estatus; ?>>
							<table width="100%">
								<tr>
									<td style='width: 10%; text-align: left; padding-left: 15px;'>Folio</td>
									<td style='width: 20%; text-align: left; padding-left: 15px;'>Estatus</td>
									<td style='width: 30%; text-align: left;'>Asesor</td>
									<td style='width: 22%; text-align: left;'>NAP</td>
									<td style='width: 13%; text-align: left;'>Costo</td>
									<td style='width: 5%;'></td>
								</tr>
								<tr>
									<td style='padding-left: 15px;'>
<?php 
										$txtfolio->crear($folio);
										$txtfolio->autocompletar("Select folio from produccion where folio like '$%'",4);
?>
									</td>
									<td style='padding-left: 15px;text-align: left;'><span id="estatus"></span></td>
									<td style="text-align: left;"><span id="asesor"></span></td>
									<td style="text-align: left;"><span id="nap"></span></td>
									<td style='padding-right: 15px;'>
										<select id="sel-costo" name="sel-costo" style="width: 100%; border: 1px solid #A8A8A8;">
											<option value=0></option>
											<option value=50>$50.00</option>
											<option value=100>$100.00</option>
											<option value=200>$200.00</option>
											<option value=300>$300.00</option>
											<option value=400>$400.00</option>
											<option value=500>$500.00</option>
										</select>
									</td>
									<td colspan="5" style="text-align: center">
										<?php $imgaceptar->crear(); ?>
									</td>
								</tr>
							</table>
						</form>
<?php
					}
					else 
					{
						echo "En construcción";
					}
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
   			 	$('#menu').css("top",hc);
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

   		 	$('#men_pie').css("top",h + 80);

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  			$("#btn_acepta").click(function()
  			{
  				$("#frm-envio").submit();
  			});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="https://www.sis-ca.com.mx:7863/";
  		  	});
  		  	
  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});
  			
  			$("#txt-folio").autocomplete
			({
				source:'autocompleta.php?c=txt-folio',minLength:3,
				select: function( event, ui )
				{
					$("#txt-folio").val(ui.item.value);
					$("#info").load("./ajax/traer_info.php?folio=" + ui.item.value, function()
					{
						var datos=$("#info").text().split("|");
						$("#estatus").text(datos[0]);
						$("#asesor").text(datos[1]);
						$("#nap").text(datos[2]);
						$("#info").html("");
						$("#sel-costo").focus();
					});
					return false;
				}
			});

			$("#txt-folio").focusout(function()
			{
				$("#info").load("./ajax/traer_info.php?folio=" + $(this).val(), function()
				{
					var datos=$("#info").text().split("|");
					$("#estatus").text(datos[0]);
					$("#asesor").text(datos[1]);
					$("#nap").text(datos[2]);
					$("#info").html("");
					$("#sel-costo").focus();
				});
				return false;
			});

			$("#imgaceptar").click(function()
			{
				var p=1;
				var estatus=$("#bloqueo").val();
				if(estatus==3)
				{
					p=0;
					var men=  "A D V E R T E N C I A... \n No es seguro que el pago de tu acompa\u00F1amiento se realice hoy. El registro esta fuera de tiempo y podr\u00edas recibir el dep\u00f3sito hasta la pr\u00f3xima semana...";
					if(confirm(men)===true)
						p=1;
				}
				if(p==1)
				{
					$.ajax(
					{
						type: "POST",
						url: "./ajax/guarda_acom.php",
						data: $("#frm-envio").serialize(),
						success: function(response)
						{
							alert(response);
							location.href="acompaniamiento.php";
						}
					});
					return false;
				}
			});
		});
	</script>
</html>