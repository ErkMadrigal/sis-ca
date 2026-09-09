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
		<script type="text/javascript" src="./js/autocompletar.js"></script>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

		<!-- compiled function datepicker -->
		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
<!--		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/> -->
		<link rel="stylesheet" href="./css/seg_ventas.css?v=<?php echo filemtime('./css/seg_ventas.css'); ?>">

		<style type="text/css">
			#vis-info	{
							z-index: 10;
							display: none;
							width: 980px; 
							height: 560px;
							margin-top: 3px;
							background: white;
							margin-left: 200px; 
							position: absolute;
							border-radius: 20px;
							background: #F9F9F9;
							border: 1px solid #848484;

						}
			#t-screen td, #t-screen th {	white-space:nowrap	}	
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
//   		if($_SESSION["nombre"]=="Administrador del Sistema" or $_SESSION["nombre"]=="FERNANDO ROJAS BRAVO")
//    		$periodo=10;
//    	else
//			$periodo=1000;
     	if($periodo > 600)
     	{
      		session_destroy();
			  mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
      		//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");
      		//mensaje("Pagina temporalmente bloqueada por proceso de pagos","http://carobra.ddns.me:8888/afore/contiene.php");
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
			//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888/");
		}
		else
		{
			reg_acceso($_SESSION["nombre"],"seg_ventas");
//---------------------------------------------------------------------------Recepción de parametros
			$_SESSION["autentifica"]="";
			$te=addslashes(trim($_POST["te"]));
			$op=addslashes(trim($_POST["op"]));
			$sfp=addslashes(trim($_POST["sfp"]));
			//$fnv=addslashes(trim($_POST["fnv"]));
			$srm0=addslashes(trim($_POST["srm0"]));
			$nreg=addslashes(trim($_POST["nreg"]));
			$no_pag=addslashes(trim($_POST["pag"]));
			$mes_i=addslashes(trim($_POST["mes_i"]));
			$mes_f=addslashes(trim($_POST["mes_f"]));
			$filtro=addslashes(trim($_POST["filtro"]));
			$valor1=addslashes(trim($_POST["valor1"]));
			$valor2=addslashes(trim($_POST["valor2"]));
			$valor3=addslashes(trim($_POST["valor3"]));
			$valor4=addslashes(trim($_POST["valor4"]));
				$valor5=addslashes(trim($_POST["valor5"]));
				$reg_esp=addslashes(trim($_POST["reg_esp"]));
				$estructura_zenzat=addslashes(trim($_POST["estructura_zenzat"]));
				$anio_ini=addslashes(trim($_POST["anio_ini"]));
				$anio_fin=addslashes(trim($_POST["anio_fin"]));

			$valor1=str_replace("_"," ",$valor1);

			if(trim($filtro)=="Soc.")
				$filtro="Soc. Comercial";

			$cheked_sfp="";
			if($sfp==1)
				$cheked_sfp="checked";

			$cheked_srm0="";
			if($srm0==1)
				$cheked_srm0="checked";

			$cheked_reg="";
			if($reg_esp==1)
				$cheked_reg="checked";

			$tp=$valor4;
			$cheked_ft="";
			$cheked_fc="";
			if($tp=='ft')
				$cheked_ft="CHECKED";
			else
			{
				if($tp=='fc')
					$cheked_fc="CHECKED";
			}
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
				titulo("Seguimiento de Ventas");
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

					if(bloqueo_cierre()=="S")
						mensaje("Consulta bloqueada temporalmente por el proceso de cierre de pagos","contiene.php");

					$bd=$conecta->consulta("Select * from bloqueo_seguimientos");
					if ($bd->num_rows == 0)
					{
						$anticipo=0;
						$incentivo=0;
						$estatus=0;

							$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
							$query="Select clave from empl_usuarios where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
							$bd=$conecta->consulta($query);
							$tb=mysqli_fetch_array($bd);
							$clave=$tb['clave'];
							if($clave!="BEAJODOCA" and $clave!="administra")
								$estructura_zenzat=0;
						if($clave=="CAROBRA")
						{
							$nivel=3;
							$ne=3136;
							$clave="MAMAYSANCO";
							$usuario="Usuario de prueba";
						}
						else
						{
							if($clave=="NOVGAVI")
							{
								$nivel=13;
								$clave="FARAMIREZC";
								$usuario="FERNANDO ARISTEO RAMIREZ CABRERA";
							}
							else
							{
								if($clave=="SEBASMARMA")
								{
									$nivel=13;
									$clave="FLOMAMALA";
									$usuario="FLOR MARIA MARTINEZ LARA";
								}
								else
								{
									$bd=$conecta->consulta("Select * from permisos_sv where clave='$clave'");
									if ($bd->num_rows !=0)
									{
										$tb=mysqli_fetch_array($bd);
										$nivel=$tb["nivel"];
									}

									$ne=0;
									$usuario=$_SESSION["nombre"];
								}
							}
						}
//-------------------------------------------------------------------------------- Bloqueo por pagos
						$bloqueo=0;
						$fec_bloqueo="";
						if($clave != "FEROJAS" and $clave != "administra")
						{
							$bd=$conecta->consulta("Select * from bloqueo_sv");
							if ($bd->num_rows != 0)
							{
								$tb=$bd->fetch_array(MYSQLI_NUM);
								$fec_bloqueo=$tb[0];
								$hora_ini=$tb[1];
								$hora_fin=$tb[2];
								if(date("d/m/Y")==$fec_bloqueo)
								{
									if(date("G")>= $hora_ini and date("G")<= $hora_fin)
										$bloqueo=1;									
								}
							}										
						}
//--------------------------------------------------------------------------------------------------
						if($nivel==3 or $nivel==7)
						{
							if($ne==0)
							{
								$query="Select no_empleado from empl_usuarios where nombre='" . utf8_decode($usuario) . "'";
								$bd=$conecta->consulta($query);
								$tb=mysqli_fetch_array($bd);
								$ne=$tb['no_empleado'];
							}
						}

						$sql="Select DATE_FORMAT(MAX(STR_TO_DATE(fec_captura, '%d/%m/%Y')), '%d/%m/%Y') AS fec_act
							  FROM movilidad";
						$bd=$conecta->consulta($sql);
						$tb = mysqli_fetch_array($bd);
						$act_movilidad=$tb["fec_act"];
?>
						<div class="container">
							<form  id="frm-filtro" action="seg_ventas.php" method="post">
								<input type='hidden' id='op' name='op' value=<?php echo $op;?>>
								<input type='hidden' id='pag' name='pag' value=<?php echo $no_pag;?>>
								<input type='hidden' id='te' name='te' value=<?php echo $te;?>>
								<input type='hidden' id='nreg' name='nreg' value=<?php echo $nreg;?>>
								<input type='hidden' id='filtro' name='filtro' value=<?php echo $filtro;?>>
								<input type='hidden' id='valor1' name='valor1' value=<?php echo str_replace(" ","_",$valor1);?>>
								<input type='hidden' id='valor2' name='valor2' value=<?php echo $valor2;?>>
								<input type='hidden' id='valor3' name='valor3' value=<?php echo $valor3;?>>
								<input type='hidden' id='valor4' name='valor4' value=<?php echo $valor4;?>>
								<input type='hidden' id='valor5' name='valor5' value=<?php echo $valor5;?>>
								<input type='hidden' id='estructura_zenzat' name='estructura_zenzat' value=<?php echo $estructura_zenzat;?>>
								<input type='hidden' id='anio_ini' name='anio_ini' value=<?php echo $anio_ini;?>>
								<input type='hidden' id='anio_fin' name='anio_fin' value=<?php echo $anio_fin;?>>
								<input type='hidden' id='sfp' name='sfp' value=<?php echo $sfp; ?>>
								<input type='hidden' id='reg_esp' name='reg_esp' value=<?php echo $reg_esp; ?>>
								<input type='hidden' id='srm0' name='srm0' value=<?php echo $srm0; ?>>
								<input type='hidden' id='mes_i' name='mes_i' value=<?php echo $mes_i;?>>
								<input type='hidden' id='mes_f' name='mes_f' value=<?php echo $mes_f;?>>
								<input type='hidden' id='n' name='n' value=<?php echo $nivel;?>>
							</form>
							<form  id="frm-consulta" action="seg_ventas.php" method="post">
								<input type='hidden' id='op' name='op' value=<?php echo $op;?>>
								<input type='hidden' id='te' name='te' value=<?php echo $tip_est;?>>
							</form>
							<div class='row mt-0 pt-0 pb-2'>
								<div class='col-12 text-right'>
									<span>Ultima actualización de estatus movilidad: <b><?php echo $act_movilidad; ?></b></span>
								</div>
							</div>
							<div class='row'>
								<div class='col-6 col-md-4 col-lg-2 text-right'>
									<span>Calendario:</span>
								</div>
								<div class='col-md-4 col-lg-3 text-right'>
									<select id="sel-est" name="sel-est" class="form-control form-control-sm custom-select pt-0 pb-0 pl-2">
										<option value=''></option>
										<option value='ec' <?php echo ($te=='ec') ? 'SELECTED' : ''; ?>>Calendario Comercial</option>
										<option value='ep' <?php echo ($te=='ep') ? 'SELECTED' : ''; ?>>Calendario Anual</option>
									</select>
								</div>
								<div class='col-7 text-right'>
									<div class="row">
<?php
										if($nivel==12)
										{
?>
											<div class='col-4 text-right'>
												<label class='chk-eti'>
													<input type="checkbox" name="chk-sfp" id="chk-sfp" <?php echo $cheked_sfp; ?>> Sin fecha de pago
												</label>
											</div>
											<div class='col-3 pr-0 text-right'>
												<label class='chk-eti'>
													<input type="checkbox" name="chk-srm0" id="chk-srm0" <?php echo $cheked_srm0; ?>> Saldo real > 0
												</label>
											</div>
<?php
										}
?>
										<div class='col-5 pr-2 text-right'>
											<label class='chk-eti'>
												<input type="checkbox" name="chk-reg" id="chk-reg" <?php echo $cheked_reg; ?>> Registros especiales
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class='row justify-content-center mt-2 p-2 pb-0 mb-0' style="border-bottom: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC;">
								<div class='col-6 col-md-4 col-lg-2 text-right'>
									<span>Filtrar por:</span>
								</div>
								<div class='col-6 col-md-8 col-lg-10 p-0 m-0'>
									<table width="100%">
										<tr class="pt-0 pb-0 pl-0 pr-0">
											<th class="p-0"><a class="filtro">Folio/Oport.</a></th>
											<th><a class="filtro">NSS/CURP</a></th>
											<th><a class="filtro">Cliente</a></th>
											<th><a class="filtro">NAP</a></th>
											<th><a class="filtro">Soc. Comercial</a></th>
											<th><a class="filtro">SubBrocker</a></th>
											<th><a class="filtro">Comisionista</a></th>
											<th><a class="filtro">Gerencia</a></th>
										</tr>
									</table>
								</div>
<?php
								if($op>=1)
								{
									if($filtro=="Folio/Oport.")
									{
?>
										<div class='col-4 col-sm-3 col-md-3 text-right pr-1 pt-3'>
											<span>Folio/No. oportunidad:</span>
										</div>
										<div class='col-5 col-sm-4 col-md-3 col-lg-2 text-left pl-1 pt-3'>
											<div class="autocompletar">
												<input type="text" class="form-control form-control-sm" id="txt-folio" name="txt-folio" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' value='<?php echo $valor1;?>'>
											</div>
										</div>
										<div class='col-3 col-sm-2 col-md-1 pl-0 pt-3'>
											<img src="./img/aceptar2.png" class="btn-filtro" id='btn-folio'>
										</div>
										<div class='col-3'></div>
	<script type="text/javascript">
										autocompletar('txt-folio',3,'folio_sv',$("#txt-folio").data('n') + "|" + $("#txt-folio").data('ne'));
	</script>
<?php
									}
									if($filtro=="NSS/CURP")
									{
?>
										<div class='col-4 col-sm-3 col-md-2 text-right pr-1 pt-3'>
											<span>NSS/CURP:</span>
										</div>
										<div class='col-6 col-sm-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
											<div class="autocompletar">
												<input type="text" class="form-control form-control-sm" id="txt-nss" name="txt-nss" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
											</div>
										</div>
										<div class='col-3 col-sm-2 col-md-1 pl-0 pt-3'>
											<img src="./img/aceptar2.png" class="btn-filtro" id='btn-nss'>
										</div>
										<div class='col-3'></div>
<script type="text/javascript">
										autocompletar('txt-nss',4,'nss_sv',$("#txt-nss").data('n') + "|" + $("#txt-nss").data('ne'));
</script>
<?php
									}
									if($filtro=="Cliente")
									{
?>
										<div class='col-4 col-sm-3 col-md-2 text-right pr-1 pt-3'>
											<span>Cliente:</span>
										</div>
										<div class='col-8 col-sm-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
											<div class="autocompletar">
												<input type="text" class="form-control form-control-sm" id="txt-cliente" name="txt-cliente" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
											</div>
										</div>
										<div class='col-3 col-sm-2 col-md-1 pl-0 pt-3'>
											<img src="./img/aceptar2.png" class="btn-filtro" id='btn-cliente'>
										</div>
<script type="text/javascript">
										autocompletar('txt-cliente',4,'cliente_sv',$("#txt-cliente").data('n') + "|" + $("#txt-cliente").data('ne'));
</script>
<?php
									}
									if($filtro=="Soc. Comercial" or $filtro=="SubBrocker" or $filtro=="Comisionista" or $filtro=="Gerencia" or $filtro=="NAP")
									{

										if($filtro=="NAP")
										{
											if($nivel != 3 and $nivel != 4 and $nivel != 5)
											{
												$btn="nap";
?>
												<div class='col-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
													<div class="autocompletar">
														<label for='txt-nap' class='etiqueta'>Número de Agente Promotor</label>
														<input type="text" class="form-control form-control-sm" id="txt-nap" name="txt-nap" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
													</div>
												</div>
<script type="text/javascript">
												autocompletar('txt-nap',4,'nap_sv',$("#txt-nap").data('n') + "|ep");
</script>
<?php
											}
										}

										if($filtro=="Soc. Comercial")
										{
											if($nivel != 3 and $nivel != 4 and $nivel != 5)
											{
												$btn="sc";
?>
												<div class='col-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
													<div class="autocompletar">
														<label for='txt-sc' class='etiqueta'>Socio Comercial</label>
														<input type="text" class="form-control form-control-sm" id="txt-sc" name="txt-sc" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
													</div>
												</div>
<?php
												if($nivel==13)
												{
?>
<script type="text/javascript">
													$("#txt-sc").val("<?php echo $usuario; ?>");
</script>
<?php
												}
												else
												{
?>
<script type="text/javascript">
													autocompletar('txt-sc',4,'sc_sv',$("#txt-sc").data('n') + "|ep");
</script>
<?php
												}
											}
											else
											{
												mensaje("No tienes permisos para hacer consultas por Socio Comercial");
?>
<script type="text/javascript">
													window.location="seg_ventas.php";
</script>
<?php
											}
										}

										if($filtro=="SubBrocker")
										{
											if($nivel != 3 and $nivel != 4)
											{
												$btn="sb";
?>
												<div class='col-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
													<div class="autocompletar">
														<label for='txt-sb' class='etiqueta'>SubBrocker</label>
														<input type="text" class="form-control form-control-sm" id="txt-sb" name="txt-sb" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
													</div>
												</div>
<?php
												if($nivel==5)
												{
?>
<script type="text/javascript">
													$("#txt-sb").val("<?php echo $_SESSION['nombre']; ?>");
</script>
<?php
												}
												else
												{
?>
<script type="text/javascript">
													autocompletar('txt-sb',4,'sb_sv',$("#txt-sb").data('n') + "|ep");
</script>
<?php
												}
											}
											else
											{
												mensaje("No tienes permisos para hacer consultas por SubBrocker");
?>
<script type="text/javascript">
												window.location="seg_ventas.php";
</script>
<?php
											}
										}
										if($filtro=="Comisionista")
										{
											$btn="asesor";
?>
											<div class='col-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
												<div class="autocompletar">
													<label for='txt-asesor' class='etiqueta'>Comisionista</label>
													<input type="text" class="form-control form-control-sm" id="txt-asesor" name="txt-asesor" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
												</div>
											</div>
<?php
											if($nivel != 3 and $nivel != 7)
											{
?>
<script type="text/javascript">
												autocompletar('txt-asesor',4,'asesor_sv',$("#txt-asesor").data('n') + "|ep");
</script>
<?php
											}
											else
											{
?>
<script type="text/javascript">
												$("#txt-asesor").val("<?php echo $usuario; ?>");
</script>
<?php
											}
										}
										if($filtro=="Gerencia")
										{
											if($nivel != 3)
											{
												$btn="gerencia";
?>
												<div class='col-6 col-md-5 col-lg-4 text-left pl-1 pt-3'>
													<div class="autocompletar">
														<label for='txt-gerencia' class='etiqueta'>Gerencia</label>
														<input type="text" class="form-control form-control-sm" id="txt-gerencia" name="txt-gerencia" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
													</div>
												</div>
<?php
												if($nivel != 4)
												{
?>
<script type="text/javascript">
													autocompletar('txt-gerencia',3,'gerencia_sv',$("#txt-gerencia").data('n') + "|ep");
</script>
<?php
												}
												else
												{
													$query="Select clave from empl_usuarios where nombre='" . utf8_decode($usuario) . "'";
													$bd=$conecta->consulta($query);
													$tb=mysqli_fetch_array($bd);
													$clave=$tb['clave'];
													$query="Select gerencia 
															from (Select distinct id_gerencia from usuarios_nivel_gerencia 
																  where clave='$clave' and pertenece=1) ung
																inner join (Select distinct id_gerencia, gerencia 
																			from gerencias) g on (ung.id_gerencia=g.id_gerencia)";
													$bd=$conecta->consulta($query);
													$tb=mysqli_fetch_array($bd);
													$gerencia=$tb['gerencia'];
?>
<script type="text/javascript">
													$("#txt-gerencia").val("<?php echo $gerencia; ?>");
</script>
<?php
												}
											}
											else
											{
												mensaje("No tienes permisos para hacer consultas por Gerencia");
?>
<script type="text/javascript">
													window.location="seg_ventas.php";
</script>
<?php
											}
										}
?>
										<div class='col-6 text-left pl-1 pt-3'>
											<div class="row">
												<div class='col-2 pl-3 pr-0' style='display: none;'>
													<label class='chk-eti'>
														<input type="radio" name="chk-tf" id="chk-ft" <?php echo $cheked_ft; ?>> F. Trasp.
													</label>
													<br>
													<label class='chk-eti'>
														<input type="radio" name="chk-tf" id="chk-fc" <?php echo $cheked_fc; ?>> F. Cierre
													</label>
												</div>
												<div class='col-7 text-left pl-0 pr-3 pt-0'>
													<div class="row">
														<div class='col-4 pl-2 pr-0 text-right'><span class='etiqueta'>Rango inicio:</span></div>
														<div class='col-8 pl-2 pr-4'>
															<div class="row">
																<div class='col-5 pl-2 pr-1'>
																	<select id="sel-anio-ini" name="sel-anio-ini" class="form-control form-control-sm custom-select pt-0 pb-0 pl-2">
																		<option value=0></option>
<?php
																		if($nivel==12)
																		{
																			$i=date("Y") - 1;
																			while($i <= date("Y"))
																			{
	?>
																				<option value=<?php echo $i; ?> <?php echo ($anio_ini==$i) ? 'SELECTED' : ''; ?>><?php echo $i; ?></option>
	<?php
																				$i++;
																			}
																		}
																		else 
																		{
																			$fecha=date("Y-m-j");
																			$nuevafecha = strtotime('-12 months', strtotime($fecha));
																			$nuevafecha = date('Y-m-j' , $nuevafecha);

																			$fecha = strtotime($nuevafecha);
																			$i = date("Y", $fecha);
																			while($i <= date("Y"))
																			{
	?>
																				<option value=<?php echo $i; ?> <?php echo ($anio_ini==$i) ? 'SELECTED' : ''; ?>><?php echo $i; ?></option>
	<?php
																				$i++;
																			}
																		}
?>
																	</select>
																</div>
																<div class='col-7 pl-1 pr-0'>
																	<select id="sel-mes-ini" name="sel-mes-ini" class="form-control form-control-sm custom-select pt-0 pb-0 pl-2">
																		<option value=0></option>
<?php
																		if($mes_i != "")
																		{
																			if($te=="ec")
																			{
																				$query="Select mes,nom_periodo from fechas_cierre_com where anio=$anio_ini order by mes";
																				$bd=$conecta->consulta($query);
																				if ($bd->num_rows != 0)
																				{
																					$cad="0>";
																					while($tb=mysqli_fetch_array($bd))
																					{
																						if($tb["nom_periodo"]=="")
																							$mes=mes($tb["mes"]);
																						else
																							$mes=$tb["nom_periodo"];
?>																						
																						<option value=<?php echo $tb["mes"]; ?> <?php echo ($mes_i==$tb["mes"]) ? 'SELECTED' : ''; ?>><?php echo $mes; ?></option>
<?php
																					}
																				}				
																			}
																			else
																			{
																				$i=1;
																				if($anio==date("Y"))
																					$mes=date("m");
																				else 
																					$mes=12;

																				$cad="0>";
																				while($i <= $mes)
																				{
?>
																					<option value=<?php echo $i; ?> <?php echo ($mes_i==$i) ? 'SELECTED' : ''; ?>><?php echo mes($i); ?></option>
<?php
																					$i++;
																				}
																			}
																		}
?>
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div class="row pt-1">
														<div class='col-4 pl-2 pr-0 text-right'><span class='etiqueta'>Rango final:</span></div>
														<div class='col-8 pl-2 pr-4'>
															<div class="row">
																<div class='col-5 pl-2 pr-1'>
																	<select id="sel-anio-fin" name="sel-anio-fin" class="form-control form-control-sm custom-select pt-0 pb-0 pl-2">
																		<option value=0></option>
<?php
																		if($nivel==12)
																		{
																			$i=date("Y") - 1;
																			while($i <= date("Y"))
																			{
?>
																				<option value=<?php echo $i; ?> <?php echo ($anio_fin==$i) ? 'SELECTED' : ''; ?>><?php echo $i; ?></option>
<?php
																				$i++;
																			}
																		}
																		else 
																		{
																			$fecha=date("Y-m-j");
																			$nuevafecha = strtotime('-12 months', strtotime($fecha));
																			$nuevafecha = date('Y-m-j' , $nuevafecha);

																			$fecha = strtotime($nuevafecha);
																			$i = date("Y", $fecha);
																			while($i <= date("Y"))
																			{
?>
																				<option value=<?php echo $i; ?> <?php echo ($anio_fin==$i) ? 'SELECTED' : ''; ?>><?php echo $i; ?></option>
<?php
																				$i++;
																			}
																		}

?>
																	</select>
																</div>
																<div class='col-7 pl-1 pr-0'>
																	<select id="sel-mes-fin" name="sel-mes-fin" class="form-control form-control-sm custom-select pt-0 pb-0 pl-2">
																		<option value=0></option>
<?php
																		if($mes_f != "")
																		{
																			if($te=="ec")
																			{
																				$query="Select mes,nom_periodo from fechas_cierre_com where anio=$anio_fin order by mes";
																				$bd=$conecta->consulta($query);
																				if ($bd->num_rows != 0)
																				{
																					$cad="0>";
																					while($tb=mysqli_fetch_array($bd))
																					{
																						if($tb["nom_periodo"]=="")
																							$mes=mes($tb["mes"]);
																						else
																							$mes=$tb["nom_periodo"];
?>																						
																						<option value=<?php echo $tb["mes"]; ?> <?php echo ($mes_f==$tb["mes"]) ? 'SELECTED' : ''; ?>><?php echo $mes; ?></option>
<?php
																					}
																				}				
																			}
																			else
																			{
																				$i=1;
																				if($anio==date("Y"))
																					$mes=date("m");
																				else 
																					$mes=12;

																				$cad="0>";
																				while($i <= $mes)
																				{
?>
																					<option value=<?php echo $i; ?> <?php echo ($mes_f==$i) ? 'SELECTED' : ''; ?>><?php echo mes($i); ?></option>
<?php
																					$i++;
																				}
																			}
																		}
?>
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class='col-5 pl-0 ml-0'>
													<div class="row">
														<div class='col-6 pl-3 pr-0'>
															<label for='txt-fec-ini' class='etiqueta'>Fecha inicial</label>
															<input type="text" class="form-control form-control-sm datepicker"  id="txt-fec-ini" name="txt-fec-ini" value='<?php echo $valor2;?>'>
														</div>
														<div class='col-6 pl-2 pr-2'>
															<label for='txt-fec-fin' class='etiqueta'>Fecha final</label>
															<input type="text" class="form-control form-control-sm datepicker" id="txt-fec-fin" name="txt-fec-fin" value='<?php echo $valor3;?>'>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class='col-2 pl-0 pt-3 mt-3'>
											<div class="row">
												<div class="col-4">
													<img src="./img/aceptar2.png" class="btn-filtro" id="<?php echo 'btn-' . $btn; ?>">
												</div>
												<div class="col-4">
													<img src="./img/excel.png" class="btn-filtro" id="btn-excel" width="30px">
												</div>
												<div class="col-4">
<?php 
													if ($clave=="administra" or $clave=='LUFERCONRA' or $clave=='BEAJODOCA' or $clave=='LACEARMA' or $clave=='SABAA') 
													{
?>
														<img src="./img/power_bi.png" class="btn-filtro" id="btn-powerbi" width="30px">
<?php
													}
?>		
												</div>
											</div>
										</div>
<?php
									}
								}
?>
							</div>
<?php
							if($op==2)
							{
								$qnreg="";
								if($te=="ep")
								{
									$calen="fechas_cierre";
									$estruc="estructura_gerencia";
									$tit_mes="Mes Pago";
								}
								else
								{
									$calen="fechas_cierre_com";
									$estruc="estructura_gerencia";
									$tit_mes="Mes Com.";
								}
								$fec_ant_sell= date ( 'Y-m-j' ,strtotime ( '-912 day' , strtotime ( date("Y-n-j"))));

								$dat_ss="";
									$dat_oficina="";
									if($estruc=="estructura_comercial")
									{
										$dat_ss="ss,";
										$dat_oficina="oficina,";
									}
									if($estructura_zenzat==1)
									{
										$filtro="Gerencia";
										$valor1="TODAS";
										$nivel=12;
									}
									if($filtro=='Folio/Oport.' or $filtro=='NSS/CURP' or $filtro=='Cliente')
								{
									if($filtro=='NSS/CURP')
									{
										//$valor1="";
										$d=explode("/", $valor1);
										$curp=trim($d[1]);
										$where_filtro=" where CURP='$curp'";
									}
									else
									{
										if($filtro=='Cliente')
										{
											$valor1="";
											$where_filtro=" where trabajador='" . utf8_decode($valor1) . "'";
										}
										else
											$where_filtro=" where no_oportunidad='$valor1'";
									}


									$i=1;
									while($i <= 8)
									{
										switch ($i) 
										{
											case 1: $anio_pr=""; break;
											case 2: $anio_pr="_2022"; break;
											case 3: $anio_pr="_2021"; break;
											case 4: $anio_pr="_2020"; break;
											case 5: $anio_pr="_2019"; break;
											case 6: $anio_pr="_2018"; break;
											case 7: $anio_pr="_2017"; break;
											case 8: $anio_pr=""; break;
										}
										if($i==1)
											$bd_pr="carobra_2014.";
										else
										{
											if($i < 8)
												$bd_pr="carobra_respaldos.";
											else
												$bd_pr="carobra_respaldos.respaldo_";
										}
										$query="Select folio from " . $bd_pr . "prod_trabajador" . $anio_pr . $where_filtro;
										$bd=$conecta->consulta($query);
										if ($bd->num_rows != 0)
										{
											$tb=mysqli_fetch_array($bd);
											$valor1=$tb["folio"];
											$i=8;
										}
										$i++;
									}
									if($valor1!='')
									{
										$tablas=explode("|",tablas_sv2($filtro,$valor1));

										$t_prod=$tablas[0];
										$t_aut=$tablas[1];
										$t_cp=$tablas[2];
										$t_pt=$tablas[3];
										$t_ap=$tablas[4];

										$qestruc="";
									}
									else
										mensaje("Información no encontrada","seg_ventas.php");
								}
								else
								{
									if($valor2=="" and $valor3=="")
									{
										if($te=="ep")
											$cal="fechas_cierre";
										else
											$cal="fechas_cierre_com";
										$query="Select fec_ini from $cal 
												where anio=$anio_ini and mes=$mes_i";
										$bd=$conecta->consulta($query);
										$tb=mysqli_fetch_array($bd);
										$valor2=$tb['fec_ini'];

										$query="Select fec_fin from $cal where anio=$anio_fin and mes=$mes_f";
										$bd=$conecta->consulta($query);
										$tb=mysqli_fetch_array($bd);
										$valor3=$tb['fec_fin'];
									}
									else
									{
										$anio_ini=substr($valor2,-4);
										$anio_fin=substr($valor3,-4);
									}
									$fec_inc=$valor2;
									$fec_fin=$valor3;
									$fec_inc_u=substr($valor2,-4) . "-" . substr($valor2,3,2) . "-" . substr($valor2,0,2);
									$fec_fin_u=substr($valor3,-4) . "-" . substr($valor3,3,2) . "-" . substr($valor3,0,2);

									$query="Select datediff('$fec_fin_u','$fec_inc_u') as d";
									$bd=$conecta->consulta($query);
									$tb=mysqli_fetch_array($bd);
									$b_dias=$tb['d'];

									if($b_dias >= 20)
									{
										if($bloqueo==1)
											mensaje("Consultas tempralmente fuera de servicio por proceso de pagos","seg_ventas.php");
									}
										$q_emp=1;
										$w_nap="";
										if($estructura_zenzat==1)
										{
											$qestruc="(Select e.clave,brocker,g.id_gerencia,gerencia
											 	       from feram.empleados e
															inner join feram.gerencias g on (e.id_gerencia=g.id_gerencia)
															left join feram.brockers b on (g.id_brocker=b.id_brocker))";
										}
										else
										{
										if($filtro=="Soc. Comercial")
											$qestruc="(Select * from carobra_2014.estruc_empleados 
													  where sc='$valor1')";
										else
										{
										if($filtro=="SubBrocker")
											$qestruc="(Select * from carobra_2014.estruc_empleados 
													  where brocker='$valor1')";
										else
										{													
											if($filtro=="Gerencia")
											{
												if($valor1 != "TODAS")
												   $qestruc="(Select * 
															  from carobra_2014.estruc_empleados
														      where gerencia='$valor1')";
												else
												{
													if($bloqueo==1)
														mensaje("Consultas tempralmente fuera de servicio por proceso de pagos","seg_ventas.php");
													if($nivel==12 or $nivel==6)
													{
														$qestruc="carobra_2014.estruc_empleados";
													}
												}
											}
											else
											{
												if($filtro=="NAP")
												{
													$p=explode("(", $valor1);
													$nap=str_replace(")","",$p[1]);

													$qestruc="carobra_2014.estruc_empleados";
													$w_nap=" and p.nap='$nap'";
												}
												else
												{
													$q_emp=0;
													if($valor1=="Usuario de prueba")
														$valor1="MARIA MAYRA SANTOS CORTES";
													$query="Select no_empleado from empl_usuarios 
															where nombre='$valor1'";
													$bd=$conecta->consulta($query);
													$tb=mysqli_fetch_array($bd);
													$w_qprod=" and pg.no_empleado=" . $tb[0] . " ";
													$qestruc="(Select * 
															   from carobra_2014.estruc_empleados
															   where no_empleado=" . $tb[0] . ")";
													}
												}
											}
										}
										}
										$tem_cp="";
									$q_pagos="";
									$tem_pagos="";
									if($sfp==1)
									{
										$q_pagos=" and ifnull(cp.fec_pago,'')=''";
										$tem_cp=" left join $t_cp cp on (pg.folio=cp.folio) ";
										if($srm0==1)
										{
											$tem_pagos=" left join pagos pa on (pg.folio=pa.folio) ";
											$q_pagos.=" and ifnull(pa.saldo_real,0)!=0";
										}
									}
									else
									{
										if($srm0==1)
										{
											$tem_pagos=" left join pagos pa on (pg.folio=pa.folio) ";
											$q_pagos=" and ifnull(pa.saldo_real,0)!=0";
										}
									}
								}
								$conecta->consulta("Truncate estruc_empleados");
								$query="Insert ignore into estruc_empleados
										Select distinct clave,no_empleado,sc,brocker,
											   ung.id_gerencia,gerencia
										from usuarios_nivel_gerencia ung
											inner join estructura_gerencia eg 
													on (ung.id_gerencia=eg.id_gerencia)
											inner join (Select distinct id_gerencia,gerencia from gerencias) g 
													on (ung.id_gerencia=g.id_gerencia)
										where pertenece=1";										
								$conecta->consulta($query);

								$n_aleatorio=date("d_m_Y_G_i");
								$qsv="w_sv_" . $clave . "_" . $n_aleatorio;
									$conecta->consulta("Create TABLE $qsv LIKE sv2");
									$t_ap2="";
									if($estructura_zenzat==1)
									{
											$query="Insert into carobra_2014.$qsv
													Select p.folio,'','','','','',id_estatus_prod,
														   id_estatus_prod,ifnull(confirma,0),cierre_cap,0,
														   0,id_tipo_mov,nss,'',nap,'','',afo_cedente,'','',
														   '','Zenzat',ifnull(brocker,''),id_gerencia,gerencia,
														   id_usuario_cap,'','',ifnull(eu_z.no_empleado,0),'','',
														   ifnull(pi.id_estatus_anticipo,0),ifnull(pi.monto_anticipo,0),0,
														   saldo_estimado,0,0,'',fec_captura,ifnull(pi.fec_pago_ant,''),
														   '',fec_traspaso,fec_cierre,'',ifnull(pi.fec_pago,''),
														   fec_ult_estatus,'','','',0,''
											 		FROM carobra_2014.produccion p
											 			inner join feram.prod_fr pi on (p.folio=pi.folio)
														inner join $qestruc ee 
																on (pi.clave=ee.clave)
														left join empl_usuarios eu_z on (pi.clave=eu_z.clave)";
										$w_query=" where p.no_empleado=19125 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' 
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
										$query.=$w_query;
										if($q_emp==1)
										{
											$query.=" union all
													  Select p.folio,'','','','','',id_estatus_prod,
													  		 id_estatus_prod,ifnull(confirma,0),
													  		 cierre_cap,0,0,id_tipo_mov,p.nss,'',p.nap,
													  		 '','',afo_cedente,'','','','Zenzat',
													  		 ifnull(brocker,''),id_gerencia,gerencia,id_usuario_cap,'','',
													  		 0,'','',ifnull(pi.id_estatus_anticipo,0),
													  		 ifnull(pi.monto_anticipo,0),0,saldo_estimado,0,0,'',
													  		 fec_captura,ifnull(pi.fec_pago_ant,''),'',fec_traspaso,
													  		 fec_cierre,'',ifnull(pi.fec_pago,''),fec_ult_estatus,'','','',0,''
													  FROM carobra_2014.produccion p
													  	left join feram.prod_fr pi on (p.folio=pi.folio)
													  	inner join carobra_2014.naps n on (p.nap=n.nap)
													  	inner join carobra.empleados ec on (n.no_empleado=ec.no_empleado)
													  	inner join $qestruc ee on (ec.clave=ee.clave)
										 			  where ifnull(pi.folio,'') = ''";
											$w_query=" 	and p.no_empleado=19125 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' 
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";

											$query.=$w_query;
										}
										$t_pt="prod_trabajador";
										$t_cp="calculo_pago";

										if($anio_ini==$anio_fin)
											$t_ap="carobra.acum_det_prod_" . $anio_fin;
										else
										{
											$t_ap="carobra.acum_det_prod_" . $anio_ini;
											$t_ap2="carobra.acum_det_prod_" . $anio_fin;
										}
									}
									else
									{
									if($qestruc=="")
									{
										$query="Insert into carobra_2014.$qsv
											Select p.folio,'','','','','',id_estatus_prod,
												   id_estatus_prod,estatus_confirma,cierre_cap,0,
												   0,id_tipo_mov,p.nss,'',nap,'','',afo_cedente,'',
												   '','',sc,brocker,id_gerencia,gerencia,
												   id_usuario_cap,'','',p.no_empleado,'','',
												   id_estatus_anticipo,monto_anticipo,0,
												   saldo_estimado,0,0,'',fec_captura,fec_pago_ant,
												   '',fec_traspaso,fec_cierre,'','',
												   fec_ult_estatus,'','','',0,''
									 		FROM (Select * from $t_prod 
									 			  where folio='$valor1' and no_empleado > 0) p 
									 			inner join carobra_2014.estruc_empleados ee 
									 				on (p.no_empleado=ee.no_empleado)
									 		union all
											Select p.folio,'','','','','',id_estatus_prod,
												   id_estatus_prod,estatus_confirma,cierre_cap,0,
												   0,id_tipo_mov,p.nss,'',p.nap,'','',afo_cedente,
												   '','','',sc,brocker,id_gerencia,gerencia,
												   id_usuario_cap,'','',p.no_empleado,'','',
												   id_estatus_anticipo,monto_anticipo,0,
												   saldo_estimado,0,0,'',fec_captura,fec_pago_ant,
												   '',fec_traspaso,fec_cierre,'','',
												   fec_ult_estatus,'','','',0,''
									 		FROM (Select * from $t_prod 
									 			  where folio='$valor1' and no_empleado = 0) p 
									 			inner join carobra_2014.naps n on (p.nap=n.nap)
									 			inner join carobra_2014.estruc_empleados ee 
									 				on (n.no_empleado=ee.no_empleado)
									 		union all
											Select p.folio,'','','','','',id_estatus_prod,
												   id_estatus_prod,estatus_confirma,cierre_cap,0,
												   0,id_tipo_mov,p.nss,'',nap,'','',afo_cedente,'',
												   '','',sc,brocker,id_gerencia,gerencia,
												   id_usuario_cap,'','',p.no_empleado,'','',
												   id_estatus_anticipo,monto_anticipo,0,
												   saldo_estimado,0,0,'',fec_captura,fec_pago_ant,
												   '',fec_traspaso,fec_cierre,'','',
												   fec_ult_estatus,'','','',0,''
									 		FROM (Select * from $t_prod 
									 			  where folio='$valor1' and no_empleado = 0) p 
									 			inner join carobra.empleados e on (p.nap=e.nae)
									 			inner join carobra_2014.estruc_empleados ee 
									 				on (e.no_empleado=ee.no_empleado)";
//echo $query . "<br><br>";
								}
								else
								{
									$query="Insert into carobra_2014.$qsv
											Select p.folio,'','','','','',id_estatus_prod,
												   id_estatus_prod,estatus_confirma,cierre_cap,0,
												   0,id_tipo_mov,nss,'',nap,'','',afo_cedente,'','',
												   '',sc,brocker,id_gerencia,gerencia,
												   id_usuario_cap,'','',p.no_empleado,'','',
												   id_estatus_anticipo,monto_anticipo,0,
												   saldo_estimado,0,0,'',fec_captura,fec_pago_ant,
												   '',fec_traspaso,fec_cierre,'','',
												   fec_ult_estatus,'','','',0,''
									 		FROM carobra_2014.produccion p
												inner join $qestruc ee 
														on (p.no_empleado=ee.no_empleado)";
									$w_query=" where p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' 
													AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
													AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
													$w_nap";
									if($reg_esp==1)
										$w_query.=" and length(p.folio) < 10 ";
									else
										$w_query.=" and length(p.folio) >= 10 ";
									if($sfp==1)
									{
										$query.=" left join carobra_2014.calculo_pago cp 
														on (p.folio=cp.folio) ";
										$w_query.=" and ifnull(cp.fec_pago,'')=''";
										if($srm0==1)
										{
											$query.=" left join carobra_2014.pagos pa 
															on (p.folio=pa.folio) ";
											$w_query.=" and ifnull(pa.saldo_real,0)!=0";
										}
									}
									else
									{
										if($srm0==1)
										{
											$query.=" left join carobra_2014.pagos pa 
															on (p.folio=pa.folio) ";
											$w_query.=" and ifnull(pa.saldo_real,0)!=0";
										}
									}
									$query.=$w_query;
									if($srm0==0 and $q_emp==1)
									{
										$query.=" union all
												  Select p.folio,'','','','','',id_estatus_prod,
												  		 id_estatus_prod,estatus_confirma,
												  		 cierre_cap,0,0,id_tipo_mov,p.nss,'',p.nap,
												  		 '','',afo_cedente,'','','',sc,brocker,
												  		 id_gerencia,gerencia,id_usuario_cap,'','',
												  		 p.no_empleado,'','',id_estatus_anticipo,
												  		 monto_anticipo,0,saldo_estimado,0,0,'',
												  		 fec_captura,fec_pago_ant,'',fec_traspaso,
												  		 fec_cierre,'','',fec_ult_estatus,'','','',0,''
												  FROM carobra_2014.produccion p
												  	inner join carobra_2014.naps n on (p.nap=n.nap)
												  	inner join $qestruc ee 
												  			on (n.no_empleado=ee.no_empleado)"; 
										$w_query=" where p.no_empleado=0 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' 
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
														$w_nap";
										if($reg_esp==1)
											$w_query.=" and length(p.folio) < 10 ";
										else
											$w_query.=" and length(p.folio) >= 10 ";
										$query.=$w_query;

										$query.=" union all
												  Select p.folio,'','','','','',id_estatus_prod,
												  		 id_estatus_prod,estatus_confirma,
												  		 cierre_cap,0,0,id_tipo_mov,p.nss,'',p.nap,
												  		 '','',afo_cedente,'','','',sc,brocker,
												  		 id_gerencia,gerencia,id_usuario_cap,'','',
												  		 p.no_empleado,'','',id_estatus_anticipo,
												  		 monto_anticipo,0,saldo_estimado,0,0,'',
												  		 fec_captura,fec_pago_ant,'',fec_traspaso,
												  		 fec_cierre,'','',fec_ult_estatus,'','','',0,''
												  FROM carobra_2014.produccion p
												  	inner join carobra.empleados e on (p.nap=e.nae)
												  	inner join $qestruc ee 
												  			on (e.no_empleado=ee.no_empleado)"; 
										$w_query=" where p.no_empleado=0 and p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' 
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
														AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
														$w_nap";
										if($reg_esp==1)
											$w_query.=" and length(p.folio) < 10 ";
										else
											$w_query.=" and length(p.folio) >= 10 ";
										$query.=$w_query;
									}
									$t_pt="prod_trabajador";
									$t_cp="calculo_pago";
									
									if($anio_ini==$anio_fin)
										$t_ap="carobra.acum_det_prod_" . $anio_fin;
									else
									{
										$t_ap="carobra.acum_det_prod_" . $anio_ini;
											$t_ap2="carobra.acum_det_prod_" . $anio_fin;
										}
									}
									}
									$conecta->consulta($query);
								$bd=$conecta->consulta("Select 1 from carobra_2014.$qsv");
								if ($bd->num_rows != 0)
								{
									$m_query="";
//echo $query;
									//$bd=$conecta->consulta("Select * from $qsv");
									//if ($bd->num_rows != 0)
									//{
									//	$conecta->consulta("Delete from $qsv");
									$query="Update $qsv s
												JOIN fechas_cierre_com f
														on (str_to_date(s.fec_traspaso,'%d/%m/%Y') >= str_to_date(f.fec_ini,'%d/%m/%Y')
																and str_to_date(s.fec_traspaso,'%d/%m/%Y') <= str_to_date(f.fec_fin,'%d/%m/%Y'))
												JOIN meses m on (f.mes=m.no_mes)
											SET s.nom_mes=if(f.mes <=12,m.mes_largo,f.nom_periodo)";
									$m_query.=$query;

									$query="Update $qsv s, fec_cierre_campana f
											set s.campana=f.campana
											where str_to_date(s.fec_traspaso,'%d/%m/%Y') >= str_to_date(f.fec_ini,'%d/%m/%Y')
												and str_to_date(s.fec_traspaso,'%d/%m/%Y') <= str_to_date(f.fec_fin,'%d/%m/%Y')";
									$m_query.=";" . $query;

									$query="Update $qsv s, $t_pt pt
											set s.trabajador=pt.trabajador,s.telefono=pt.telefono,
												s.celular=pt.celular,s.cp=pt.cp,
												s.email_trab=pt.email_trab,s.curp=pt.CURP,
												s.no_oportunidad=pt.no_oportunidad,
												s.observa=pt.observa
											where s.folio=pt.folio";
									$m_query.=";" . $query;

									$query="Update $qsv
											set edad=TIMESTAMPDIFF(YEAR,STR_TO_DATE(CONCAT(
												        CASE
												          WHEN SUBSTRING(curp,5,2) <= DATE_FORMAT(STR_TO_DATE(fec_traspaso,'%d/%m/%Y'), '%y')
												            THEN CONCAT('20', SUBSTRING(curp,5,2))
												          ELSE CONCAT('19', SUBSTRING(curp,5,2))
												        END,SUBSTRING(curp,7,4)),'%Y%m%d'),STR_TO_DATE(fec_traspaso,'%d/%m/%Y'))
											where length(curp)=18 and length(fec_traspaso)=10";
									$m_query.=";" . $query;

										$query="Update $qsv s, empl_usuarios eu
												set s.clave=eu.clave, s.nombre=eu.nombre
												where s.no_empleado > 0 and s.no_empleado=eu.no_empleado";
										$m_query.=";" . $query;

									$query="Update $qsv s, prod_rechazos pr
											set s.id_rechazo=pr.id_rechazo, s.id_tipo_rechazo=pr.id_tipo_rechazo 
											where s.folio=pr.folio";
									$m_query.=";" . $query;

										if($estructura_zenzat!=1)
										{
											$query="Update $qsv s, segundo_anticipo sa
													set s.monto_seg_ant=sa.monto, s.fec_pago_ant2=sa.fec_pago
													where s.folio=sa.folio and length(s.fec_pago_ant2)=0";
											$m_query.=";" . $query;
										}
//echo $query;
									$query="Update $qsv s, pagos pa
											set s.saldo_real=pa.saldo_real
											where s.folio=pa.folio";
									$m_query.=";" . $query;

									$query="Update $qsv s, $t_cp cp
											set s.fec_rechazo=cp.fec_pago
											where cp.rechazo=1 and s.folio=cp.folio";
									$m_query.=";" . $query;

										if($estructura_zenzat!=1)
										{
											$query="Update $qsv s, $t_cp cp
													set s.fec_pago=cp.fec_pago
													where cp.rechazo=0 and s.folio=cp.folio";
											$m_query.=";" . $query;
										}

										if($estructura_zenzat==1)
										{
											$query="Update carobra_2014.$qsv s
															join feram.prod_fr pi
																on (CONVERT(s.folio USING utf8mb3) COLLATE utf8mb3_general_ci = CONVERT(pi.folio USING utf8mb3) COLLATE utf8mb3_general_ci) 
															join carobra.empleados e on (pi.analista=e.clave) 
													set s.id_usuario_cap=e.no_empleado";
											$m_query.=";" . $query;
										}

									$query="Update $qsv s
												JOIN (select folio_pr, max(cast(trim(ap.folio_av) as DOUBLE)) as folio_av
												      from apovol_prod ap
												      		inner join $qsv s 
												      				on (ap.folio_pr=s.folio)
												      where trim(ap.folio_av) regexp '^[0-9]+$'
												      group by folio_pr) av on (s.folio=av.folio_pr)
												JOIN av_pagos avp on (av.folio_av=avp.tramite)
											SET s.folio_av=avp.folio, s.estatus_av=avp.estatus,
												s.frecuencia=avp.frecuencia
											WHERE avp.tramite regexp '^[0-9]+$'";
									$m_query.=";" . $query;

									$query="Update $qsv s, av_fechas avf
											set s.fec_envio=avf.fec_envio
											where s.folio_av !='' and s.folio_av=avf.folio";
									$m_query.=";" . $query;
/*
									$query="Update carobra_2014.$qsv s 
												join carobra_2014.pagos_folios_sin_av pav 
													on (s.folio=pav.folio)
												join carobra.usuarios u on (pav.autoriza=u.clave)
											set s.men_autoriza=concat('Pago autorizado sin AV el ',pav.fec_autoriza,' por ',u.nombre)";
									$m_query.=";" . $query;
*/

									$query="Update carobra_2014.$qsv s 
												join carobra_2014.clawback c on (s.folio=c.folio)
											set s.transfer_out='Baja del cliente'
											where cancelacion=0";
									$m_query.=";" . $query;

/*
									$query="Update $qsv s, extemporaneos a
											set s.descrip=concat('Pago extemporaneo el ',a.fec_pago)
											where s.folio=a.folio";
									$m_query.=";" . $query;
*/
									$query="Update carobra_2014.$qsv s, $t_ap ap
											set s.saldo_pago=ap.saldo_real
											where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
												and s.folio=ap.folio";
									$m_query.=";" . $query;

									$query="Update carobra_2014.$qsv s, carobra_pagos.desc_incumplimiento d
											set s.fec_desc=d.fec_descuento
											where s.folio=d.folio";
									$m_query.=";" . $query;

									if($t_ap2 != "")
									{
										$query="Update carobra_2014.$qsv s, $t_ap2 ap
												set s.saldo_pago=ap.saldo_real
												where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
													and s.folio=ap.folio";
										$m_query.=";" . $query;
									}

									$query="Select distinct 
												   concat(if(id_tipo_mov=1,'Traspaso','Registro'),' ',if(ifnull(n.nap,'')='','NAE','NAP')) as tipo_prod,
												   s.folio,nss,trabajador,s.nombre,'' as m30,saldo_estimado,saldo_real,nom_mes as mes,
												   if(id_estatus_anticipo=1,'Solicitado',if(id_estatus_anticipo=2,'Pagado','')) as est_anticipo,
												   monto_anticipo,monto_seg_ant as monto,esf.estatus,s.sc,s.brocker,s.gerencia,s.nap,
												   if(id_tipo_mov=1,'Traspaso','Registro') as movimiento,fec_pago_ant2 as fec_ant2,s.fec_traspaso,
												   m.fec_captura,fec_pago_ant as fec_anticipo,fec_pago,trec.tipo_rechazo,af.afore,r.rechazo,s.id_tipo_mov,
												   curp,eu.nombre as analista,if(cierre_cap=0,'No','Si') as cierre_cap,observa,fec_rechazo,descrip,
												   fec_cierre,folio_av,estatus_av,fec_envio,no_oportunidad,transfer_out,esf.estatus as estatus_pbi,campana,
												   if(confirma='','',if(substring(confirma,-10)='Confirmado',confirma,concat('En proceso (',confirma,')'))) as confirma,
												   saldo_pago,frecuencia,edad,fec_desc,m.fec_sello,
												   case when movilidad=1 then 'Igual o mayor a 18 meses'
												   		else if(ifnull(m.folio,0)=0,'Sin estatus movilidad','Menos de 18 meses')
												   end as movilidad,m.fec_captura as cap_movilidad,if(m.pplus='','',concat('Cliente ',m.pplus)) as pplus,
												   m.ley,m.categoria_cliente,
												   case when m.cotiza=1 then 'SI'
												   		when m.cotiza=0 then 'NO'
												   		else ''
												   end as cotiza
											from carobra_2014.$qsv s
												left join carobra_2014.naps n on (s.nap=n.nap)
												left join carobra_2014.estatus esf on (s.id_estatus_prod=esf.id_estatus)
												left join tipo_rechazo trec on (s.id_tipo_rechazo=trec.id_tipo) 
												left join afores af on (s.afo_cedente=af.clave) 
												left join rechazos r on (s.id_rechazo=r.id_rechazo)
												left join empl_usuarios eu on (s.id_usuario_cap=eu.no_empleado)
												left join movilidad m on (s.folio=m.folio)";
//echo $query;
									if($qnreg != "")
									{
										$nreg=0;
										$bd=$conecta->consulta($qnreg);
										$tb=mysqli_fetch_array($bd);
										$nreg=$tb['n'];
									}
								}
								else
									$m_query=$query='';
?>
								<div class="row ml-0 mr-0 pl-0 pt-3 pr-0">
									<div class="col-12 p-0 m-0">
										<div class="info-sv">
											<table width="100%" id="t-screen">
												<thead>
													<tr>
														<th >Tipo producto</th>
														<th >Folio</th>
														<th >NSS</th>
														<th >Cliente</th>
														<th >Comisionista</th>
														<th >Menor 30m</th>
														<th ><?php echo $tit_mes; ?></th>
														<th >Saldo Estimado</th>
														<th >Saldo Real</th>
														<th >Estatus Anticipo</th>
														<th >Anticipo</th>
														<th >Segundo ant.</th>
														<th >Estatus</th>
													</tr>
												</thead>
												<tbody>
<?php
//$m_query=$query='';
//echo $m_query;
													//$bd=$conecta->multi_consulta($m_query,$query);

													$m_sql = explode(";", $m_query);
											    	foreach ($m_sql as $sql) 
											    	{
											        	$sql = trim($sql);
											        	if ($sql != '') 
											        		$conecta->consulta($sql);
											        }
													$bd=$conecta->consulta($query);
													while($tb=mysqli_fetch_array($bd))
													{
														$estatus_sv=strtoupper($tb['estatus']);
														if($estatus_sv=="EN VALIDACION" or $estatus_sv=="CAPTURADO EN SISTEMA")
															$class="sv-gris";
														else
														{
															if($estatus_sv=="ENVIADA A CERTIFICAR" or $estatus_sv=="ACEPTADA OPERACIONES" or $estatus_sv=="ACEPTADA PROCESAR" or $estatus_sv=="RECIBIDA OPERACIONES")
																$class="sv-amarillo";
															else
															{
																if($estatus_sv=="RECUPERABLE" or $estatus_sv=="RECHAZADO" or $estatus_sv=="CERTIFICADO MAS DE UNA VEZ EN CAROBRA" or $estatus_sv=="DESCUENTO INCUMPLIMIENTO")
																	$class="sv-rojo";
																else
																{
																	if($estatus_sv=="CERTIFICADO" or $estatus_sv=="CERTIFICADO MAYOR A 64 AÑOS")
																		$class="sv-verde";
																	else
																		$class="sv-gris";
																}
															}
														}

														if($tb['est_anticipo']=="")
														{
															$ant_ne=0;
															$f=explode("/",$tb['fec_traspaso']);
															if($f[2] > 2022)
																$ant_ne=1;
															else
															{
																if($f[2]==2022)
																{
																	if($f[1]==12)
																	{
																		if($f[0]>=17)
																			$ant_ne=1;
																	}
																}
															}
															if((($tb['estatus']=="ACEPTADA OPERACIONES" or $tb['estatus']=="ACEPTADA PROCESAR" or $tb['estatus']=="ENVIADA A CERTIFICAR") or (($tb['estatus']=="EN VALIDACION" or $tb['estatus']=="CIERRE") and $ant_ne==1)) and $nivel==12 and $tb['cierre_cap']=='Si')
															{
																if($tb['saldo_estimado']>0 and $tb['id_tipo_mov']==1 and $tb['nombre']!='' and $tb["observa"] != 'Folio con informacion duplicada')
																	$estatus_a="<a class='solicita' style='color:red; font-weight: bold;'>Solicitar</a>";
															}
														}
														else
															$estatus_a=$tb['est_anticipo'];
														$info=$tb['sc'] . "|" . $tb['brocker'] . "|" . $tb['gerencia'] . "|" . $tb['movimiento'] . "|" . $tb['nap'] . "|" . $tb['fec_ant2'] . "|" . $tb['fec_traspaso'] . "|" . $tb['fec_captura'] . "|" . $tb['fec_anticipo'] . "|" . $tb['fec_pago'] . "|" . $tb['tipo_rechazo'] . "|" . $tb['afore'] . "|" . $tb['rechazo'] . "|" . $tb['curp'] . "|" . $tb['analista'] . "|" . $tb["cierre_cap"] . "|" . $tb["observa"];
														
														if($estruc=="estructura_comercial")
															$info.="|" . $tb["oficina"] . "|" . $tb["ss"];
														
														$info.="|" . $tb["fec_rechazo"] . "|" . $tb["descrip"] . "|" . $tb["fec_cierre"] . "|" . $tb["folio_av"] . "|" . $tb["estatus_av"] . "|" . $tb["fec_envio"] . "|" . $tb["no_oportunidad"] . "|" . $tb["campana"] . "|" . $tb['transfer_out'];
														$info.="|" . $tb['estatus_pbi'] . "|" . $tb["confirma"] . "|$" . number_format($tb["saldo_pago"], 2, '.', ',') . "|" . $tb["frecuencia"] . "|" . $tb["edad"] . "|" . $tb["fec_desc"] . "|" . $tb["fec_sello"] . "|" . $tb["movilidad"] . "|" . $tb["cap_movilidad"] . "|" . $tb["pplus"] . "|" . $tb["ley"] . "|" . $tb["categoria_cliente"] . "|" . $tb["cotiza"];

//echo $info;
?>
														<tr class="tr_info" data-info='<?php echo $info; ?>'>
															<td class="<?php echo $class; ?>"><?php echo $tb['tipo_prod']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['folio']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['nss']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['trabajador']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['nombre']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['m30']; ?></td>
															<td class="<?php echo $class; ?>"><?php echo $tb['mes']; ?></td>
															<td class="<?php echo $class; ?>">
																<?php echo '$' . number_format($tb['saldo_estimado'], 2, '.', ','); ?>
															</td>
															<td class="<?php echo $class; ?>">
																<?php echo '$' . number_format($tb['saldo_real'], 2, '.', ','); ?>
															</td>
															<td class="<?php echo $class; ?>"><?php echo $estatus_a; ?></td>
															<td class="<?php echo $class; ?>">
																<?php echo '$' . number_format($tb['monto_anticipo'], 2, '.', ','); ?>
															</td>
															<td class="<?php echo $class; ?>">
																<?php echo '$' . number_format($tb['monto'], 2, '.', ','); ?>
															</td>
															<td class="<?php echo $class; ?>"><?php echo $tb['estatus']; ?></td>
														</tr>
<?php
													}
?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
<?php
								$conecta->consulta("DROP TABLE $qsv");
								if($nreg>0)
								{
									$tpag=intval($nreg/100);
									if(($nreg%100) > 0)
										$tpag++;
									if($no_pag==0)
										$no_pag++;
?>
									<div class="row pt-3 pr-2">
										<div class="col-12 text-right">
<?php
											if($no_pag>1)
											{
?>
												<a id='pag-inicio' class='paginacion p-2'><<</a><a id='ant-pag' class='paginacion p-2' data-pag="<?php echo $no_pag; ?>"><</a>
<?php
											}
?>
											<span><?php echo "Página $no_pag de $tpag"; ?></span>
<?php
											if($no_pag<$tpag)
											{
?>
												<a id='sig-pag' class='paginacion p-2' data-pag="<?php echo $no_pag; ?>">></a><a id='pag-final' class='paginacion p-2'data-pag="<?php echo $tpag; ?>">>></a>
<?php
											}
?>
										</div>
									</div>
<?php
								}
							}
?>
						</div>
<?php
					}
					else
						mensaje("Se está corriendo el proceso de pagos... Espera unos minutos y vuelve a intentarlo por favor","contiene.php");
				$divcontenido->close();
			$divcontenedor->close();
			//$divpie->close();
?>
			<div id='vis-info'>
				<table width="100%">
					<tr>
						<th id="tit_info" width="90%">
					        <div class="tit-info-contenido">
					          <span id="tit_info_cliente"></span>
					          <span id="tit_info_categoria"></span>
					        </div>
						</th>
						<th id="tit_close" width="10%">
							<img src="./img/cerrar1.png" id="cerrar" width="17px">
						</th>
					</tr>
				</table>
				<table width="100%">
						<tr>
							<th class='tit' style="text-align: right;">Estatus SF:</th>
							<td colspan="2" id="t_estatus" class='dato' style="text-align: center; background: #F4F4F4;"></td>
							<th class='tit' style="text-align: right;">Estatus Procesar:</th>
							<td id="t_estatus_pbi" class='dato' style="text-align: center; background: #F4F4F4;"></td>
							<th class='tit' style="text-align: right;">Confirmación:</th>
							<td id="t_confirma" class='dato' style="text-align: center; background: #F4F4F4;"></td>
							<th colspan="1" class='tit' style="text-align: right;">Cierre captura:</th>
							<td id="t_cierre_cap" class='dato' style="text-align: center; background: #F4F4F4;"></td>
						</tr>
						<tr>
							<th nowrap id="tit_tip_rechazo" class="tit" width="20%" style="text-align: right;">Tipo de rechazo:</th>
							<td colspan="2" id="tip_rechazo" class="dato"></td>
							<th id="tit_rechazo" class="tit" width="10%">Rechazo:</th>
							<td colspan='3' id="t_rechazo" class="dato"></td>
						</tr>
						<hr>
				</table>
				<table width="100%" style="background-color: #F1F1F1;">
					<tr>
						<td width="20%" style='border-right: 1px solid #676767;'>
							<table>
								<tr>
									<th width="50%" class="tit">Movimiento:</th>
									<td width="50%" id="t_mov" class="dato" style="text-align: left;"></td>
								</tr>
								<tr>
									<th class="tit">NSS:</th>
									<td id="t_nss" class="dato" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">CURP:</th>
									<td id="t_curp" class="dato" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">NAP:</th>
									<td id="t_nap" class="dato" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">Oportunidad:</th>
									<td id="t_opor" class="dato" width="30%" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr style="border-bottom: 1px solid #676767;">
									<th class="tit">Afore:</th>
									<td id="t_afore" class="dato" style="text-align: left;"></td>
								</tr>
								<tr>
									<th class="tit">Folio AV:</th>
									<td id="folio_av" class="dato" width="30%" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">Estatus AV:</th>
									<td id="estatus_av" class="dato" width="30%" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">Fec envio:</th>
									<td id="fec_envio_av" class="dato" width="30%" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th class="tit">Frecuencia:</th>
									<td id="frecuencia" class="dato" width="30%" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
							</table>
						</td>
						<td width="80%">
							<table width="100%">
								<tr>
									<td width="70%">
										<table width="100%">
											<tr>
												<th width="50%" class="tit">Oficina</th>
												<th width="50%" class="tit">Socio Comercial</th>
											</tr>
											<tr>
												<td id="t_oficina" class="dato" style="text-align: left;"></td>
												<td id="t_sc" class="dato" style="text-align: left;"></td>
											</tr>
											<tr>
												<th width="50%" class="tit">Socio-SubBrocker</th>
												<th width="50%" class="tit">Brocker</th>
											</tr>

											<tr>
												<td id="t_ss" class="dato" style="text-align: left;"></td>
												<td id="t_sb" class="dato" style="text-align: left;"></td>
											</tr>
											<tr>
												<th width="50%" class="tit">Gerencia</th>
												<th width="50%" class="tit">Comisionista</th>
											</tr>
											<tr>
												<td id="t_gerencia" class="dato" style="text-align: left;"></td>
												<td id="t_comisionista" class="dato" style="text-align: left; background: #F4F4F4;"></td>
											</tr>
											<tr>
												<th width="50%" class="tit">Analista</th>
												<th width="50%" class="tit">Observaciones</th>
											</tr>
											<tr>
												<td id="t_analista" class="dato" style="text-align: left;"></td>
												<td id="t_observa" class="dato"></td>
											</tr>
										</table>
									</td>
									<td width="30%" style="background: #E4E4E4; border: 10px solid transparent; border-radius: 20px; vertical-align: top;">
										<table width="100%">
											<tr>
												<th class="tit">Campaña</th>
												<td colspan="3" id="t_campana" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<th colspan="3" class="tit">Ult. traspaso</th>
												<td id="t_ult_traspaso" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<th class="tit">Movilidad</th>
												<td colspan="3" id="t_movilidad" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<th class="tit">Ley</th>
												<td id="t_ley" class="dato" style="text-align: right;"></td>
												<th class="tit">Cotiza:</th>
												<td id="t_cotiza" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<th colspan="3" class="tit">Actualizado al: </th>
												<td id="t_act_movilidad" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<td colspan="4" id="t_pplus" class="dato" style="text-align: right;"></td>
											</tr>
											<tr>
												<td colspan="4" id="t_transfer_out" class="dato" style="text-align: center;"></td>
											</tr>
										</table>
									</td>
								</tr>								
								<tr>
									<td colspan="2">
										<table>
											<tr>
												<th width="20%" class="tit" style="text-align: right;">Comentario Principal</th>
												<td width="80%" id="t_comenta" class="dato" style="text-align: left; background: #F4F4F4;"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>

					</tr>
				</table>
				<table width="100%">
					<tr>
						<td width="100%">
							<table width="100%">
								<tr style='border-top: 1px solid #676767;'>
									<td width="2%"></td>
									<th width="20%" class="tit">Estatus anticipo</th>
									<th width="12%" class="tit">Anticipo</th>
									<th width="12%" class="tit">Segundo ant.</th>
									<th width="13%" class="tit">Saldo estimado</th>
									<th width="13%" class="tit">Saldo Real</th>
									<th width="13%" class="tit">Saldo Pago</th>
									<th width="13%" class="tit"><?php echo $tit_mes; ?></th>
									<td width="2%"></td>
								</tr>
								<tr style='border-bottom: 1px solid #676767;'>
									<td></td>
									<td id="t_est_anticipo" class="dato" style="text-align: left;"></td>
									<td id="t_anticipo" class="dato moneda" style="text-align: left;"></td>
									<td id="t_anticipo_mtto" class="dato moneda" style="text-align: left;"></td>
									<th id="t_saldo_estimado" class="dato moneda" style="text-align: left;"></th>
									<th id="t_saldo_real" class="dato moneda" style="text-align: left;"></th>
									<th id="t_saldo_pago" class="dato moneda" style="text-align: left;"></th>
									<td id="t_mes" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding: 0 10px;">
							<table width="100%">
								<tr>
									<th width="12%" class="tit">Captura</th>
									<th width="13%" class="tit">Anticipo</th>
									<th width="13%" class="tit">Segundo Ant.</th>
									<th width="13%" class="tit">Traspaso</th>
									<th width="12%" class="tit">Cierre</th>
									<th width="12%" class="tit">Rechazo</th>
									<th width="12%" class="tit">Pago</th>
									<th width="13%" class="tit">Desc. incumpl.</th>
								</tr>
								<tr>
									<td id="t_fec_captura" class="dato" style="text-align: left;"></td>
									<td id="t_fec_anticipo" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td id="t_fec_anticipo2" class="dato" style="text-align: left;"></td>
									<td id="t_fec_traspaso" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td id="t_fec_cierre" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td id="t_fec_rechazo" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td id="t_fec_pago" class="dato" style="text-align: left; background: #F4F4F4;"></td>
									<td id="t_fec_desc" class="dato" style="text-align: left; background: #F4F4F4;"></td>
								</tr>
								<tr>
									<th colspan="7" id="t_autoriza_pago" class="dato" style="text-align: center; padding-top: 12px;"></th>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div id='sol-anticipo'>
				<h5>El anticipo para el folio <span id="s_folio"></span> es de:</h5>
				<h1 id="s_anticipo" data-inf=''></h1><br>
				<h5>Detalles:</h5>
				<span id="s_detalles" style='font-size: 0.8em;'></span><br>
				<br>
				<a id='sol_solicitar' class='sol' style='padding-right: 20px;'>Solicitar</a><a id='sol_cerrar' class='sol'>Cerrar</a>
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
   		 	$('#men_pie').css("background","red");

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="https://www.sis-ca.com.mx:7863/";
  		  	});
  		  	
  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

			$(".datepicker").datepicker(
			{
				format: "dd/mm/yyyy"
			});

/*
			$("#txt-fec-ini").focusout(function()
			{
				alert("HHHHHH");
			});
*/

			$(".info_sv a").css("color","red");
			$(".tr_info").css("cursor","pointer");
			$(".info_sv a").css("font-weight","bold");

  		  	$(".filtro").click(function()
  		  	{
  		  		let pasa=1;

  		  		if($("#sel-est").val()!='')
  		  		{
  		  			let sfp=0;
  		  			let srm0=0;
					let reg_esp=0;
	  		  		
	  		  		$("#op").val(1);
	  		  		$("#valor1").val('');
	  		  		$("#valor2").val('');
	  		  		$("#valor3").val('');
	  		  		$("#filtro").val($(this).text());
	  		  		$("#te").val($("#sel-est").val());
					
					if ( $("#chk-sfp").length > 0 ) 
					{
						if($("#chk-sfp").prop("checked"))
							sfp=1;
					}
					$("#sfp").val(sfp);

					if ( $("#chk-srm0").length > 0 ) 
					{
						if($("#chk-srm0").prop("checked"))
							srm0=1;
					}
					$("#srm0").val(srm0);

					if ( $("#chk-reg").length > 0 ) 
					{
						if($("#chk-reg").prop("checked"))
							reg_esp=1;
					}
					$("#reg_esp").val(reg_esp);

	  		  		$("#frm-filtro").submit();
	  		  	}
	  		  	else
	  		  		alert("Se debe seleccionar primero el tipo de calendario");
  		  	});

			$("#btn-folio, #btn-nss, #btn-cliente, #btn-sc, #btn-sb, #btn-asesor, #btn-gerencia").hover(function()
			{
				if($(this).attr("src")=="./img/aceptar2.png")
					$(this).attr("src","./img/aceptar1.png");
				else
					$(this).attr("src","./img/aceptar2.png");
			});

				$('body').on('click', '.btn-filtro',function()
				{
					let pasa=0;
					$("#estructura_zenzat").val(0);
					if($("#txt-fec-ini").length)
				{
					if($("#txt-fec-ini").val() != "")
					{
						if($("#txt-fec-fin").val() != "")
						{
							if($("#n").val()==12)
								pasa=1;
							else 
							{
								const f = new Date();
								let a=f.getFullYear()-1;
								let m=f.getMonth()+1;

								let fec_t=f.getDate().toString().padStart(2, '0') + "/" + m.toString().padStart(2, '0') + "/" + a;
								if(compara_fecha(fec_t,$("#txt-fec-ini").val())==false)
									pasa=2;
								if(pasa != 2)
								{
									let ff=$("#txt-fec-fin").val().split("/");
									const f = new Date(ff[2],ff[1] * 1,ff[0] * 1);
									let a=f.getFullYear();
									let m=f.getMonth()-3;

									if(m < 0)
									{
										m=12 + m;
										a=a-1;
									}


									fec_t=f.getDate().toString().padStart(2, '0') + "/" + m.toString().padStart(2, '0') + "/" + a;
									if(compara_fecha(fec_t,$("#txt-fec-ini").val())==false)
										pasa=3;
									else
										pasa=1;
								}
							}
						}
					}
					if(pasa==0)
					{
						if($("#sel-mes-ini").val() != 0)
						{
							if($("#sel-mes-fin").val() != 0)
								pasa=1;
						}
					}
				}
				else
					pasa=1;

				if(pasa==1)
				{
					let mensaje='';
					$("#op").val(2);
					$("#pag").val(0);
	  		  		$("#nreg").val(0);
					switch (this.id) 
					{
					  case 'btn-folio':
					    	if($("#txt-folio").val()=="")
					    		alert("No se ha ingresado el folio");
					    	else
					    	{
					    		$.post("./ajax/validar_info.php",{ tipo: 'folio', valor1: $("#txt-folio").val(), valor4: $("#sel-est").val()}, function(response)
								{
									if(response=="")
									{
										let sfp=0;
										let srm0=0;
										let reg_esp=0;
						  		  		$("#te").val($("#sel-est").val());
						  		  		$("#filtro").val($("#filtro").val());
						  		  		$("#valor1").val($("#txt-folio").val());
										if ( $("#chk-sfp").length > 0 ) 
										{
											if($("#chk-sfp").prop("checked"))
												sfp=1;
										}
										$("#sfp").val(sfp);

										if ( $("#chk-srm0").length > 0 ) 
										{
											if($("#chk-srm0").prop("checked"))
												srm0=1;
										}
										$("#srm0").val(srm0);

										if ( $("#chk-reg").length > 0 ) 
										{
											if($("#chk-reg").prop("checked"))
												reg_esp=1;
										}
												$("#reg_esp").val(reg_esp);

									  		  		$("#frm-filtro").submit();
											}
									else
										alert(response);
								});
					    	}
					  break;
					  case 'btn-nss':
					    	if($("#txt-nss").val()=="")
					    		alert("No se ha ingresado el NSS");
					    	else
					    	{
					    		$.post("./ajax/validar_info.php",{ tipo: 'nss', valor1: $("#txt-nss").val(), valor4: $("#sel-est").val()}, function(response)
								{
									if(response=="")
									{
										let sfp=0;
										let srm0=0;
										let reg_esp=0;
						  		  		$("#te").val($("#sel-est").val());
						  		  		$("#filtro").val($("#filtro").val());
						  		  		$("#valor1").val($("#txt-nss").val());
										if ( $("#chk-sfp").length > 0 ) 
										{
											if($("#chk-sfp").prop("checked"))
												sfp=1;
										}
										$("#sfp").val(sfp);

										if ( $("#chk-srm0").length > 0 ) 
										{
											if($("#chk-srm0").prop("checked"))
												srm0=1;
										}
										$("#srm0").val(srm0);

										if ( $("#chk-reg").length > 0 ) 
										{
											if($("#chk-reg").prop("checked"))
												reg_esp=1;
												}
											$("#reg_esp").val(reg_esp);
									  		  		$("#frm-filtro").submit();
									}
									else
										alert(response);
								});
					    	}
					  break;
					  case 'btn-cliente':
					    	if($("#txt-cliente").val()=="")
					    		alert("No se ha ingresado el Cliente");
					    	else
					    	{
					    		$.post("./ajax/validar_info.php",{ tipo: 'cliente', valor1: $("#txt-cliente").val(), valor4: $("#sel-est").val()}, function(response)
								{
									if(response=="")
									{
										let sfp=0;
										let srm0=0;
										let reg_esp=0;
						  		  		$("#te").val($("#sel-est").val());
						  		  		$("#filtro").val($("#filtro").val());
						  		  		$("#valor1").val($("#txt-cliente").val());
										if ( $("#chk-sfp").length > 0 ) 
										{
											if($("#chk-sfp").prop("checked"))
												sfp=1;
										}
										$("#sfp").val(sfp);

										if ( $("#chk-srm0").length > 0 ) 
										{
											if($("#chk-srm0").prop("checked"))
												srm0=1;
										}
										$("#srm0").val(srm0);

										if ( $("#chk-reg").length > 0 ) 
										{
											if($("#chk-reg").prop("checked"))
												reg_esp=1;
												}
											$("#reg_esp").val(reg_esp);
									  		  		$("#frm-filtro").submit();
									}
									else
										alert(response);
								});
					    	}
					  break;
					  case 'btn-oficina':
					    	if($("#txt-oficina").val()=="")
					    		alert("No se ha ingresado la oficina");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
						    		$.post("./ajax/validar_info.php",{ tipo: 'oficina', valor1: $("#txt-oficina").val(), valor4: $("#sel-est").val()}, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#valor1").val($("#txt-oficina").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
							  		  		$("#filtro").val($("#filtro").val().replace(" ","_"));
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
											}
											$("#reg_esp").val(reg_esp);

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					  case 'btn-nap':
					    	if($("#txt-nap").val()=="")
					    		alert("No se ha ingresado el NAP");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
					    		$.post("./ajax/validar_info.php",{ tipo: 'nap', valor1: $("#txt-nap").val(), valor4: $("#sel-est").val()}, function(response)
								{
									if(response=="")
									{
										let sfp=0;
										let srm0=0;
										let reg_esp=0;
						  		  		$("#te").val($("#sel-est").val());
						  		  		$("#anio_ini").val($("#sel-anio-ini").val());
						  		  		$("#anio_fin").val($("#sel-anio-fin").val());
						  		  		$("#valor1").val($("#txt-nap").val());
						  		  		$("#valor2").val($("#txt-fec-ini").val());
						  		  		$("#valor3").val($("#txt-fec-fin").val());
						  		  		$("#mes_i").val($("#sel-mes-ini").val());
						  		  		$("#mes_f").val($("#sel-mes-fin").val());
						  		  		$("#filtro").val($("#filtro").val().replace(" ","_"));
										if ( $("#chk-sfp").length > 0 ) 
										{
											if($("#chk-sfp").prop("checked"))
												sfp=1;
										}
										$("#sfp").val(sfp);

										if ( $("#chk-srm0").length > 0 ) 
										{
											if($("#chk-srm0").prop("checked"))
												srm0=1;
										}
										$("#srm0").val(srm0);

										if ( $("#chk-reg").length > 0 ) 
										{
											if($("#chk-reg").prop("checked"))
												reg_esp=1;
										}
										$("#reg_esp").val(reg_esp);

						  		  		$("#frm-filtro").submit();
									}
									else
										alert(response);
								});
					    	}
					  break;







					  case 'btn-sc':
					    	if($("#txt-sc").val()=="")
					    		alert("No se ha ingresado el Socio Comercial");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
							    		$.post("./ajax/validar_info.php",{ tipo: 'sc', valor1: $("#txt-sc").val(), valor4: $("#sel-est").val()}, function(response)
										{
											if(response=="")
											{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#valor1").val($("#txt-sc").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
							  		  		$("#filtro").val($("#filtro").val().replace(" ","_"));
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
												}
												$("#reg_esp").val(reg_esp);

												if(("<?php echo $clave; ?>"=="BEAJODOCA" || "<?php echo $clave; ?>"=="administra") && $("#txt-sc").val().trim().toUpperCase()=="FERNANDO ARISTEO RAMIREZ CABRERA")
												{
													if(confirm("Deseas consultar la informacion con la estructura de ZENZAT?"))
														$("#estructura_zenzat").val(1);
												}
								  		  		$("#frm-filtro").submit();
											}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					  case 'btn-ssb':
					    	if($("#txt-ssb").val()=="")
					    		alert("No se ha ingresado el Socio-Subrocker");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
						    		$.post("./ajax/validar_info.php",{ tipo: 'ssb', valor1: $("#txt-ssb").val(), valor4: $("#sel-est").val()}, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#valor1").val($("#txt-ssb").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
							  		  		$("#filtro").val($("#filtro").val().replace(" ","_"));
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
											}
											$("#reg_esp").val(reg_esp);

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					  case 'btn-sb':
					    	if($("#txt-sb").val()=="")
					    		alert("No se ha ingresado el SubBrocker");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
						    		$.post("./ajax/validar_info.php",{ tipo: 'sb', valor1: $("#txt-sb").val(), valor4: $("#sel-est").val() }, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#filtro").val($("#filtro").val());
							  		  		$("#valor1").val($("#txt-sb").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
											}
											$("#reg_esp").val(reg_esp);

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					  case 'btn-gerencia':
					    	if($("#txt-gerencia").val()=="")
					    		alert("No se ha ingresado la gerencia");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
						    		$.post("./ajax/validar_info.php",{ tipo: 'gerencia', valor1: $("#txt-gerencia").val(), valor4: $("#sel-est").val() }, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#filtro").val($("#filtro").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#valor1").val($("#txt-gerencia").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
											}
											$("#reg_esp").val(reg_esp);

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					  case 'btn-asesor':
					    	if($("#txt-asesor").val()=="")
					    		alert("No se ha ingresado el comisionista");
					    	else
					    	{
			    				$("#valor4").val("");
								if($("#chk-ft").is(':checked'))
									$("#valor4").val('ft');
								else
								{
									if($("#chk-fc").is(':checked'))
										$("#valor4").val('fc');
								}
								//if($("#valor4").val()!="")
								//{
						    		$.post("./ajax/validar_info.php",{ tipo: 'asesor', valor1: $("#txt-asesor").val(), valor4: $("#sel-est").val()}, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
											let reg_esp=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#anio_ini").val($("#sel-anio-ini").val());
							  		  		$("#anio_fin").val($("#sel-anio-fin").val());
							  		  		$("#filtro").val($("#filtro").val());
							  		  		$("#valor1").val($("#txt-asesor").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#mes_i").val($("#sel-mes-ini").val());
							  		  		$("#mes_f").val($("#sel-mes-fin").val());
											if ( $("#chk-sfp").length > 0 ) 
											{
												if($("#chk-sfp").prop("checked"))
													sfp=1;
											}
											$("#sfp").val(sfp);

											if ( $("#chk-srm0").length > 0 ) 
											{
												if($("#chk-srm0").prop("checked"))
													srm0=1;
											}
											$("#srm0").val(srm0);

											if ( $("#chk-reg").length > 0 ) 
											{
												if($("#chk-reg").prop("checked"))
													reg_esp=1;
											}
											$("#reg_esp").val(reg_esp);

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								//}
								//else
								//	alert("No se ha seleccionado el tipo de fecha a consultar");
					    	}
					  break;
					}
				}
				else
				{
					if(pasa==2)
						alert("Solo puedes consultar información a máximo un año atrás de la fecha actual");
					else
					{
						if(pasa==3)
							alert("Solo puedes consultar información con un rango máximo de tres meses");
						else
							alert("No se han ingresado una o más fechas a consultar o no se ha ingresado un rango de meses");
					}
				}
			});

			$('body').on('click', '.solicita',function()
			{
				let obj=$(this).parent();
				let tr=obj.parent();
				let folio=tr.find("td").eq(1).html();

				if($("#n").val()==12)
				{
					$.post("./ajax/obtener_dato.php",{ id: 'sol_anticipo', filtro1: folio }, function(response)
					{
						datos=response.split("|");

						if(datos[0] == 0)
						{
							if(datos[1]==1)
							{
								if(datos[2]==3)
								{
									mensaje= "A D V E R T E N C I A...\n No es seguro que el pago de tu anticipo se realice hoy. Tu solicitud esta fuera de tiempo y podr\u00edas recibir el dep\u00f3sito hasta la pr\u00f3xima semana...";
									alert(mensaje);
								}
								if(datos[3]==0)
								{
									sal=datos[4].replace("$",'').replace(",","");
									if(sal > 10000)
									{
										if(datos[5] != '')
										{
											men_det="Edad del trabajador: " + datos[8];

											if(datos[8] >= 60)
												men_det+=", campaña especial mayores a 60";

											$("#s_folio").text(folio);
											$("#s_folio").data("estatus",datos[2] + ", " + datos[1]);
											$("#s_anticipo").text(datos[6]);
											$("#s_anticipo").data("inf",response);
											if(datos[7] =="Si")
												men_det+=", se generará segundo anticipo";
											
											$("#s_detalles").text(men_det);

						        			$("#sol-anticipo").css("display","block");
						        		}
						        		else
						        			alert("Este anticipo no podrá generarse. No se encontró la tabla de comisión del asesor");
					        		}
					        		else
					        			alert("Este anticipo no podrá generarse por tener saldo menor o igual a $10,000.00");	
				        		}
				        		else
				        			alert("Este anticipo no podrá generarse por tener el trabajador más de 60 años");
			        		}
			        		else
			        			alert("Este anticipo no podrá generarse por que el folio tiene movilidad menor a 18 meses o movilidad sin estatus");
		        		}
		        		else
		        			alert("Este anticipo no podrá generarse por que el NAP revasó el número de folios permitidos en el mes comercial");
					});
				}
				else 
					alert("No tienes los permisos necesarios para generar el anticipo de este folio");
			});

			$(".tr_info").dblclick(function()
			{
				let aut_pago='';
				let datos=$(this).data("info").split("|");
        		let folio=$(this).find("td").eq(1).html();
        		let cliente=$(this).find("td").eq(3).html();
        		$("#tit_info_cliente").text(cliente + " (" + folio + ")");
        		$("#t_estatus").text($(this).find("td").eq(12).html());
        		if(datos[12] != "")
        		{
        			$("#t_rechazo").text(datos[12]);
        			$("#tip_rechazo").text(datos[10]);
					$("#tit_rechazo").css("display","block");
        			$("#tit_tip_rechazo").css("display","block");
        		}
        		else
        		{
        			$("#t_rechazo").text('');
        			$("#tip_rechazo").text('');
					$("#tit_rechazo").css("display","none");
        			$("#tit_tip_rechazo").css("display","none");
        		}
        		$("#t_mov").text(datos[3]);
        		$("#t_nss").text($(this).find("td").eq(2).html());
        		$("#t_nap").text(datos[4]);
        		//$("#t_m30").text($(this).find("td").eq(5).html());
        		$("#t_afore").text(datos[11]);
        		$("#t_fec_anticipo2").text(datos[5]);
        		$("#t_fec_traspaso").text(datos[6]);
        		$("#t_fec_captura").text(datos[7]);
        		$("#t_fec_anticipo").text(datos[8]);
        		$("#t_fec_pago").text(datos[9]);
        		$("#t_sc").text(datos[0]);

				if($("#sel-est").val() == "ecq")
				{
					$("#t_fec_rechazo").text(datos[19]);
					$("#t_oficina").text(datos[17]);
	        		$("#t_ss").text(datos[18]);
	        		$("#t_observa").text(datos[16] + " " + datos[20]);
	        		$("#t_fec_cierre").text(datos[21]);

	        		$("#folio_av").text(datos[22]);
	        		$("#estatus_av").text(datos[23]);
	        		$("#fec_envio_av").text(datos[24]);
	        		$("#t_opor").text(datos[25]);
	        		$("#t_campana").text(datos[26]);

	        		$("#t_ult_traspaso").text(datos[34]);
	        		$("#t_movilidad").text(datos[35]);
	        		$("#t_act_movilidad").text(datos[36]);
	        		$("#t_pplus").text(datos[37]);
	        		$("#tit_info_categoria").text(datos[39]);
	        		$("#t_ley").text(datos[38]);
	        		$("#t_cotiza").text(datos[40]);
	        		$("#t_comenta").text("");
	        		transfer_out=datos[27];
	        		$("#t_estatus_pbi").text(datos[28]);
	        		$("#t_confirma").text(datos[29]);
	        		$("#t_saldo_pago").text(datos[30]);
	        		$("#t_curp").text(datos[13] + " (" + datos[32] + ")");
	        		$("#t_fec_desc").text(datos[33]);
	        	}
	        	else
	        	{
	        		$("#t_fec_rechazo").text(datos[17]);
	        		$("#t_observa").text(datos[16] + " " + datos[18]);
	        		$("#t_fec_cierre").text(datos[19]);

	        		$("#folio_av").text(datos[20]);
	        		$("#estatus_av").text(datos[21]);
	        		$("#fec_envio_av").text(datos[22]);
	        		$("#t_opor").text(datos[23]);
	        		$("#t_campana").text(datos[24]);
	        		$("#t_ult_traspaso").text(datos[32]);
	        		$("#t_movilidad").text(datos[33]);
	        		$("#t_act_movilidad").text(datos[34]);
	        		$("#t_pplus").text(datos[35]);
	        		$("#tit_info_categoria").text(datos[37]);
	        		$("#t_ley").text(datos[36]);
	        		$("#t_cotiza").text(datos[38]);
	        		$("#t_comenta").text("");
	        		transfer_out=datos[25];
	        		$("#t_estatus_pbi").text(datos[26]);
	        		$("#t_confirma").text(datos[27]);
	        		$("#t_saldo_pago").text(datos[28]);
	        		$("#t_curp").text(datos[13] + " (" + datos[30] + ")");
	        		$("#t_fec_desc").text(datos[31]);
	        	}
        		$("#t_sb").text(datos[1]);
        		$("#t_gerencia").text(datos[2]);
        		$("#t_comisionista").text($(this).find("td").eq(4).html());
        		if($(this).find("td").eq(9).html().length > 30)
        			$("#t_est_anticipo").text("Sin solicitar");
        		else
        			$("#t_est_anticipo").text($(this).find("td").eq(9).html());
        		$("#t_anticipo").text($(this).find("td").eq(10).html());
        		$("#t_anticipo_mtto").text($(this).find("td").eq(11).html());
        		$("#t_saldo_estimado").text($(this).find("td").eq(7).html());
        		$("#t_saldo_real").text($(this).find("td").eq(8).html());
        		$("#t_mes").text($(this).find("td").eq(6).html());
        		$("#t_analista").text(datos[14]);
        		$("#t_cierre_cap").text(datos[15]);
        		$("#t_transfer_out").html("<b>" + transfer_out + "</b>");
/*
        		if(aut_pago=="")
					$("#vis-info").css("height","520px");
        		else
        			$("#vis-info").css("height","540px");
        		$("#t_autoriza_pago").text(aut_pago);
*/
				$("#vis-info").css("display","block");
			});

			$("#cerrar").hover(function()
        	{
        		if($(this).prop("src").substr(-5,1)==1)
        			$(this).prop("src","./img/cerrar2.png");
        		else
        			$(this).prop("src","./img/cerrar1.png");
        	});

			$("#cerrar").click(function()
        	{
        		$("#vis-info").css("display","none");
        	});

        	$("#sol_cerrar").click(function()
        	{
        		$("#sol-anticipo").css("display","none");
        	});

        	$("#sol_solicitar").click(function()
        	{
				let cad=($("#s_anticipo").data("inf"));
    			let folio=$("#s_folio").text();

				$.post("./ajax/obtener_dato.php",{ id: 'sol_pago_anticipo', filtro1: folio, filtro2: cad }, function(response)
				{
					alert(response);
					$("#op").val(2);
					$("#pag").val($("#sig-pag").data("pag"));
	  		  		$("#frm-filtro").submit();
				});
        	});

        	$("#pag-inicio").click(function()
        	{
				$("#op").val(2);
				$("#pag").val(1);
  		  		$("#frm-filtro").submit();
        	});

        	$("#ant-pag").click(function()
        	{
        		let pag=($(this).data("pag") * 1) -1;
				$("#op").val(2);
				$("#pag").val(pag);
  		  		$("#frm-filtro").submit();
        	});

        	$("#sig-pag").click(function()
        	{
        		let pag=($(this).data("pag") * 1) + 1;
				$("#op").val(2);
				$("#pag").val(pag);
  		  		$("#frm-filtro").submit();
        	});

        	$("#pag-final").click(function()
        	{
        		let pag=($(this).data("pag") * 1);
				$("#op").val(2);
				$("#pag").val(pag);
  		  		$("#frm-filtro").submit();
        	});

	        	$("#btn-excel").click(function()
	        	{
	        		let datos_excel=$("#frm-filtro").serializeArray();
	        		let archivo_excel="seg_ventas_excel.php";
	        		if($("#estructura_zenzat").val()==1)
	        		{
	        			archivo_excel="seg_ventas_fr_excel.php";
	        			for(let i=0;i<datos_excel.length;i++)
	        			{
	        				if(datos_excel[i].name=="filtro")
	        					datos_excel[i].value="Gerencia";
	        				if(datos_excel[i].name=="valor1")
	        					datos_excel[i].value="TODAS";
	        			}
	        		}
		  			pag=archivo_excel + "?" + $.param(datos_excel);
		  		    window.open(pag, '_blank');
	        	});

        	$("#btn-powerbi").click(function()
        	{
        		pag="seg_ventas_powerbi_2.php?" + $("#frm-filtro").serialize();
	  		    window.open(pag, '_blank');
        	});

        	$("#sel-anio-ini, #sel-anio-fin").change(function()
        	{
        		d=this.id.split("-");
        		tanio=d[2];

			  	$.post("./ajax/facilitador.php",{ op: 36, anio: $(this).val(), cal: $("#sel-est").val(), n: $("#n").val() }, function(response)
				{
					if(response != "")
					{
						let i=0;

						 $('#sel-mes-' + tanio + ' option').remove();
						info=response.split("|");
						while(i < info.length)
						{
							datos=info[i].split(">");
							$('#sel-mes-' + tanio).append("<option value=" + datos[0] + ">" + datos[1] + "</option>");
							i++;
						}
					}
				});
        	});

			$("#sel-mes-ini").change(function()
			{				
				if($("#n").val()!=12)
				{
					if($("#sel-anio-ini").val() != 0)
					{
						let a_s=$("#sel-anio-ini").val();

						const f = new Date();
						let a=f.getFullYear()-1;
						let m=f.getMonth()-10;

						if(m < 0)
							m=12+m;

						if(a==a_s)
						{
							if($("#sel-mes-ini").val() < m)
							{
								alert("El mes y año inicial no pueden exceder al período de un año del mes y año actual");
								$("#sel-mes-ini").val(0);
							}
						}
					}
					else 
					{
						alert("Antes de seleccionar el mes inicial del rango de la consulta, selecciona el año de inicio");
						$("#sel-mes-ini").val(0);
					}
				}
			});

			$("#sel-mes-fin").change(function()
			{				
				if($("#n").val()!=12)
				{
					if($("#sel-anio-fin").val() != 0)
					{
						if($("#sel-mes-ini").val() != 0)
						{
							if($("#sel-anio-ini").val()==$("#sel-anio-fin").val())
							{
								if(($("#sel-mes-fin").val() - $("#sel-mes-ini").val()) >= 3)
								{
									alert("Solo se pueden generar reportes de tres meses máximo");
									$("#sel-mes-fin").val(0);
								}
							}
							else
							{
								let m=(13-$("#sel-mes-ini").val()) + ($("#sel-mes-fin").val() * 1);
								if(m > 3)
								{
									alert("Solo se pueden generar reportes de tres meses máximo");
									$("#sel-mes-fin").val(0);
								}
							}
						}
						else 
						{
							alert("Antes de seleccionar el mes final del rango de la consulta, selecciona el mes inicial");
							$("#sel-mes-fin").val("");
						}
					}
					else 
					{
						alert("Antes de seleccionar el mes final del rango de la consulta, selecciona el año final");
						$("#sel-mes-fin").val("");
					}
				}
			});
/*
        	$("#txt-fec-ini, #txt-fec-fin").change(function()
        	{
        		if($(this).val() != "")
        		{
	        		$.post("./ajax/facilitador.php",{ op: 49, fecha: $(this).val() }, function(response)
					{
						alert(response);
					});	

        		}
        	});
*/
		});
	</script>
</html>
