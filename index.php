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
		<style>
			.link-recuperar {
				display: inline-block;
				font-size: 12.5px;
				color: #1a6785;
				text-decoration: underline;
				cursor: pointer;
				margin-top: 4px;
			}
			.link-recuperar:hover { color: #0f2942; text-decoration: underline; }

			#modal-recuperar .modal-body p.ayuda-curp {
				font-size: 12px;
				color: #777;
				margin-top: 6px;
				margin-bottom: 0;
			}
			#modal-recuperar .modal-body p.ayuda-curp a { color: #1a6785; font-weight: 600; }

			#recuperar-feedback {
				display: none;
				font-size: 13px;
				padding: 10px 12px;
				border-radius: 6px;
				margin-top: 14px;
			}
			#recuperar-feedback.ok  { display:block; background:#eafaf1; color:#1e8e58; border:1px solid #b8e8cd; }
			#recuperar-feedback.err { display:block; background:#fdeeee; color:#c0392b; border:1px solid #f3c4c4; }
		</style>
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

				/* ════════════════════════════════════════
				   Olvidé mi contraseña y/o clave (por CURP)
				════════════════════════════════════════ */
				$('#modal-recuperar').on('show.bs.modal', function () {
					$('#txt-curp-recuperar').val('');
					$('#recuperar-feedback').removeClass('ok err').hide().text('');
					$('#btn-recuperar-enviar').prop('disabled', false).text('Enviar a mi correo');
				});

				$('#modal-recuperar').on('shown.bs.modal', function () {
					$('#txt-curp-recuperar').focus();
				});

				$('#txt-curp-recuperar').on('keydown', function(e){
					if (e.key === 'Enter') { e.preventDefault(); $('#btn-recuperar-enviar').click(); }
				});

				$('#btn-recuperar-enviar').click(function()
				{
					var curp = $('#txt-curp-recuperar').val().trim().toUpperCase();

					if (curp.length === 0) {
						$('#recuperar-feedback').removeClass('ok').addClass('err').show().text('Ingresa tu CURP.');
						$('#txt-curp-recuperar').focus();
						return;
					}

					var $btn = $(this);
					$btn.prop('disabled', true).text('Enviando...');
					$('#recuperar-feedback').removeClass('ok err').hide().text('');

					$.ajax({
						type: 'POST',
						url: './afore/recuperar_acceso_api.php',
						data: { curp: curp },
						dataType: 'json',
						success: function(resp) {
							$btn.prop('disabled', false).text('Enviar a mi correo');
							if (resp.ok) {
								$('#recuperar-feedback').removeClass('err').addClass('ok').show().text(resp.msg);
								$('#txt-curp-recuperar').val('');
							} else {
								$('#recuperar-feedback').removeClass('ok').addClass('err').show().text(resp.msg || 'No se pudo procesar la solicitud.');
							}
						},
						error: function() {
							$btn.prop('disabled', false).text('Enviar a mi correo');
							$('#recuperar-feedback').removeClass('ok').addClass('err').show().text('Error de comunicación con el servidor. Intenta de nuevo.');
						}
					});
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
							<div class="col-xs-12 text-center" style="margin-top: 6px; margin-bottom: 4px;">
								<span class="link-recuperar" data-toggle="modal" data-target="#modal-recuperar">
									¿Olvidaste tu contraseña y/o clave?
								</span>
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

		<!-- MODAL: Olvidé mi contraseña y/o clave -->
		<div class="modal fade" id="modal-recuperar" tabindex="-1" role="dialog" aria-labelledby="tituloModalRecuperar">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="tituloModalRecuperar">🔑 Olvidé mi contraseña y/o clave</h4>
					</div>
					<div class="modal-body">
						<p style="font-size:13px; color:#555;">
							Escribe tu <strong>CURP</strong> y te mandamos por correo tu nombre, tu clave de usuario y tu contraseña actual.
						</p>

						<div class="form-group">
							<label for="txt-curp-recuperar" class="control-label">CURP</label>
							<input type="text" class="form-control" id="txt-curp-recuperar" maxlength="18"
								   placeholder="Ej. GATA880822MJCRRN08" style="text-transform:uppercase; letter-spacing:.5px;">
							<p class="ayuda-curp">
								¿No te la sabes o no la tienes a la mano?
								<a href="https://www.gob.mx/curp/" target="_blank" rel="noopener">Consúltala aquí en gob.mx</a>
							</p>
						</div>

						<div id="recuperar-feedback"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary" id="btn-recuperar-enviar">Enviar a mi correo</button>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>