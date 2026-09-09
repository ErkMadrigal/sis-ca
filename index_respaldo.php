<?php session_start();
	  session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0); 

	$_SESSION["nombre"]='';

	include("./funciones/clas_carobra.php");
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>CAROBRA | Inicio</title>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">

		<!-- links jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> 

		<link rel="stylesheet" href="./afore/css/index.css">
		<script src="./afore/js/carobra.js"></script> 
		<script>
			jQuery(document).ready(function()
			{
				$("#txt-usuario").focus();

				$("#btn-change").click(function()
				{
					if($("#txt-usuario").val().length > 0)
					{
						if($("#txt-pwd").val().length > 0)
						{
							$.ajax(
							{
								type: "POST",
								url: "./afore/ajax/valida_pwd.php",
								data: $("#frm-acceso").serialize() + "&n=1",
								success: function(response)
								{
									var datos=response.split("|");
									if(datos[1] != "N")
									{
										$("#cont-ingreso").html(datos[1]);
										$("#cont-ingreso").css("margin-top","15px");
										$("#cont-ingreso").css("margin-bottom","15px");
										$("#txt-new-pwd").focus();
									}
									else
									{
										alert("Usuario o contraseña incorrecta. vuelve a intentarlo");
										$("#txt-usuario").val('');
										$("#txt-pwd").val('');
										$("#txt-usuario").focus();
									}
								}
							});
							return false;
						}
						else
							alert("No se ha ingresado la contraseña");	
					}
					else
						alert("No se ha ingresado el usuario");
				});

				$('body').on('focus', '#txt-confirm-pwd',function()
				{
					if($("#txt-new-pwd").val().length > 0)
						valida_psw($("#txt-new-pwd").val());
				});

				$("#btn-enter").click(function()
				{
					if($("#txt-usuario").val().length > 0)
					{
						if($("#txt-pwd").val().length > 0)
						{
							//if($("#sel-unegocio").val() != 0)
							//{
								$.ajax(
								{
									type: "POST",
									url: "./afore/ajax/valida_pwd.php",
									data: $("#frm-acceso").serialize() + "&n=3",
									success: function(response)
									{
										var datos=response.split("|");
										if(datos[1] == "Y")
											//alert("Temporalmente fuera de servicio");
											window.location=datos[2];
										else
										{
											alert("Contraseña incorrecta. vuelve a intentarlo");
											$("#txt-pwd").val('');
											$("#txt-pwd").focus();
										}
									}
								});
								return false;
							//}
							//else
							//	alert("No se ha seleccionado la unidad de negocio");
						}
						else
							alert("No se ha ingresado la contraseña");	
					}
					else
						alert("No se ha ingresado el usuario");
				});

				$('body').on('click', '#btn-cancela',function()
				{
					window.location='index.php';
				});

				$('body').on('click', '#btn-act-change',function()
				{
					var nue=$("#txt-new-pwd").val();
					var conf=$("#txt-confirm-pwd").val();
					if(nue==conf && nue.length>0)
					{
						$.ajax(
						{
							type: "POST",
							url: "./afore/ajax/valida_pwd.php",
							data: $("#frm-contra").serialize() + "&n=2",
							success: function(response)
							{
								var datos=response.split("|");
								alert(datos[1]);
								window.location="index.php";
							}
						});
						return false;
					}
					else
					{
						alert("La confirmación de la nueva contraseña no coincide o no se ha ingresado la nueva contraseña. Intenta de nuevo");
						$("#txt-new-pwd").val('');
						$("#txt-confirm-pwd").val('');
						$("#txt-new-pwd").focus();
					}

				});				
			});
		</script>
	</head>
<!--	<body style="overflow-y: hidden;">   -->
		<body>
		<div class='container-fluid text-prm'>
			<div class='row' style="padding-top:5px;">
				<div class="col-xs-7 col-sm-5 col-md-5 col-lg-4"><img src="./img/logo_carobra.png" class="img-responsive" /></div>
				<div class="col-sm-7 col-md-7 col-lg-8 hidden-xs hidden-sm text-right" style='padding-top: 30px; padding-right: 30px;'><h2 class='text-prm'>SISCA</h2></div>
				<div class="col-sm-7 hidden-xs hidden-md hidden-lg text-right" style='padding-top: 30px; padding-right: 30px;'><h3 class='text-prm'>SISCA</h3></div>
				<div class="col-xs-5 hidden-sm hidden-md hidden-lg text-right" style='padding-top: 10px; padding-right: 30px;'><h5 class='text-prm'>SISCA</h5></div>
			</div>
			<div class='row'>
				<div id='fondo-inicio' class="col-xs-12">
					<div class='row' style='padding:10px 10px 25px 10px;'>
						<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4 col-sm-offset-5 col-md-offset-6 col-lg-offset-7 caja-prm" id='cont-ingreso' style="margin-top: 15px; margin-bottom: 15px;">

							<div class='row titulo-prm'>
								<h3>Iniciar sesión</h3>
							</div>
<!--
							<div class='row justify-content-center'>
								<br><br><br>
								<h3>Sitio temporalmente fuera de servicio</h3>
								<br><br><br>
							</div>
-->
							<form  id="frm-acceso">
								<div class="form-group">
									<div class="col-xs-12 form-group-sm">
										<label for='txt-usuario' class='control-label'>Usuario</label>
										<input type="text" class="form-control" id="txt-usuario" name="txt-usuario" style="margin-bottom: 10px;">
									</div>
									<div class="col-xs-12 form-group-sm" >
										<label for='txt-pwd' class='control-label'>Contraseña</label>
										<input type="password" class="form-control" id="txt-pwd" name="txt-pwd" style="margin-bottom: 20px;">
									</div>
								</div>
							</form>
							<div class="col-xs-12 col-sm-6" style='margin-bottom: 10px; margin-top: 20px;'>
								<button class="btn btn-primary active btn-sm btn-block" id="btn-change">
									<span class="glyphicon glyphicon-repeat"></span> Cambiar contraseña
								</button>
							</div>
							<div class="col-xs-12 col-sm-6" style='margin-top: 20px;'>
								<button class="btn btn-primary active btn-sm btn-block" id="btn-enter">
									<span class="glyphicon glyphicon-off"></span> Ingresar
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='row' style="padding-top:20px;">
				<div class="col-sm-5">
					<div class="col-sm-3"><img src="./img/BRADESCAR.png" class="img-responsive" style="height:85px; padding-bottom: 5px;" /></div>
					<div class="col-sm-3"><img src="./img/BANCO FALLABELLA.png" class="img-responsive" style="height:85px; padding-bottom: 5px;" /></div>
					<div class="col-sm-3"><img src="./img/dportenis.png" class="img-responsive" style="height:65px; padding-bottom: 5px;" /></div>
					<div class="col-sm-3"><img src="./img/skandia.png" class="img-responsive" style="height:65px; padding-bottom: 5px;" /></div>
				</div>
				<div class="col-sm-3">
					<div class="col-sm-4"><img src="./img/qualitas.png" class="img-responsive" style="height:75px; padding-bottom: 5px;" /></div>
					<div class="col-sm-4"><img src="./img/INFINITY.png" class="img-responsive" style="height:85px; padding-bottom: 5px;" /></div>
					<div class="col-sm-4"><img src="./img/PLAN IDEAL.png" class="img-responsive" style="height:85px; padding-bottom: 5px;" /></div>
				</div>	
				<div class="col-sm-3">
					<div class="col-sm-6"><img src="./img/CAROBRA 40.png" class="img-responsive" style="height:60px; padding-bottom: 0px; padding-top: 5px;" /></div>
					<div class="col-sm-6"><img src="./img/PATRIMONIAL.png" class="img-responsive" style="height:60px; padding-bottom: 0px; padding-top: 5px;" /></div>
				</div>
				<div class="col-sm-1"><img src="./img/creditaria.png" class="img-responsive" style="height:60px; padding-bottom: 5px;" /></div>

<!--
				<div class="col-sm-2"><img src="./img/slovo.jpeg" class="img-responsive" style="height:65px; padding-bottom: 10px;" /></div>
				<div class="col-sm-2"><img src="./img/indaad.jpg" class="img-responsive" style="height:65px; padding-bottom: 10px;" /></div>
-->
				
			</div>
		</div>
	</body>
</html>