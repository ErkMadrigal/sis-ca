<?php
	session_start();

	function getColor($p) 
	{
		if ($p === null) return 'verde';
		if ($p <= 12) return 'verde';
		elseif ($p <= 15) return 'amarillo';
		else return 'rojo';
	}
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
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>

		<style type="text/css">
			.t-color-r 	{
							color:red;
							font-weight: bold;
						}
			.t-color {	font-weight: bold;	}

			#t-screen td, #t-screen th {	white-space:nowrap	}
			
			.verde {
						background-color: rgba(34, 197, 94, 0.15);  /* green-500 */
						color: #166534; /* green-800 */
						/* backdrop-filter: blur(2px); */
					}

			.verde:hover { background-color: rgba(34, 197, 94, 0.25);}

			.amarillo {
						background-color: rgba(234, 179, 8, 0.15); /* yellow-500 */
						color: #854d0e; /* yellow-800 */
						/* backdrop-filter: blur(2px); */
					}

			.amarillo:hover { background-color: rgba(234, 179, 8, 0.25);	}

			.rojo {
					background-color: rgba(239, 68, 68, 0.15); /* red-500 */
					color: #991b1b; /* red-800 */
					/* backdrop-filter: blur(2px); */
				}

			.rojo:hover { background-color: rgba(239, 68, 68, 0.25);	}
			.tr_info { transition: all 0.2s ease-in-out;	}

			.tr_info:hover {
								transform: scale(1.002);
								box-shadow: 0 4px 12px rgba(0,0,0,0.08);
							}
			#getXLSX	{	cursor: pointer;	}


			.loader-box{
				width:430px;
				background:#fff;
				padding:30px;
				border-radius:10px;
				text-align:center;
				box-shadow:0 0 30px rgba(0,0,0,.35);
			}

			.loader-spinner{

				width:70px;
				height:70px;

				margin:auto;
				margin-bottom:20px;

				border:8px solid #e5e5e5;
				border-top:8px solid #007bff;

				border-radius:50%;

				animation:girar .8s linear infinite;

			}

			@keyframes girar{

				from{
					transform:rotate(0deg);
				}

				to{
					transform:rotate(360deg);
				}

			}

			.loader-progress{

				width:100%;
				height:22px;

				background:#ececec;

				border-radius:20px;

				overflow:hidden;

				margin-top:20px;

			}

			#loaderBarra{

				width:0%;

				height:100%;

				background:linear-gradient(90deg,#28a745,#5dd85d);

				transition:width .2s;

			}

			#loaderPorcentaje{

				margin-top:10px;

				font-weight:bold;

				font-size:18px;

				color:#555;

			}

			.info-sv { overflow-x: auto; }

		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
		<div id="loader" style="
			display:none;
			position:fixed;
			top:0;
			left:0;
			width:100%;
			height:100%;
			background:rgba(0,0,0,.65);
			z-index:99999;
			justify-content:center;
			align-items:center;
			">

				<div class="loader-box">

					<div class="loader-spinner"></div>

					<h4 id="loaderTitulo">
						Procesando información...
					</h4>

					<div id="loaderTexto">
						Espere un momento...
					</div>

					<div class="loader-progress">
						<div id="loaderBarra"></div>
					</div>

					<div id="loaderPorcentaje">
						0%
					</div>

				</div>

			</div>
<?php

		

		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
     	if($periodo > 600)
     	{
      		session_destroy();
			  mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
      		//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");
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
			$srm0=addslashes(trim($_POST["srm0"]));
			$nreg=addslashes(trim($_POST["nreg"]));
			$no_pag=addslashes(trim($_POST["pag"]));
			$filtro=addslashes(trim($_POST["filtro"]));
			$valor1=addslashes(trim($_POST["valor1"]));
			$valor2=addslashes(trim($_POST["valor2"]));
			$valor3=addslashes(trim($_POST["valor3"]));
			$valor4=addslashes(trim($_POST["valor4"]));
			$valor5=addslashes(trim($_POST["valor5"]));

			$periodo = addslashes(trim($_POST["periodo"]));
			$periodo = isset($_POST["periodo"])
				? addslashes(trim($_POST["periodo"]))
				: "";
			$valor1=str_replace("_"," ",$valor1);

			if(trim($filtro)=="Soc.")
				$filtro="Soc. Comercial";
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
				titulo("Seguimiento de Transfer Out");
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

				$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
				$query="Select clave from empl_usuarios 
						where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				$clave=$tb['clave'];

				$ne=0;
				$usuario=$_SESSION["nombre"];

				if($usuario=='ANAKAREN GURROLA ORTEGA')
					$nivel=12;

				if($nivel==3 or $nivel==7)
				{
					if($ne==0)
					{
						$query="Select no_empleado from empl_usuarios 
								where nombre='" . utf8_decode($usuario) . "'";
						$bd=$conecta->consulta($query);
						$tb=mysqli_fetch_array($bd);
						$ne=$tb['no_empleado'];
					}
				}
?>
				<div class="container">
					<form  id="frm-filtro" action="transfer_out.php" method="post">
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
						<input type='hidden' id='sfp' name='sfp' value=<?php echo $sfp; ?>>
						<!--<input type='hidden' id='fnv' name='fnv' value=<?php echo $fnv; ?>>-->
						<input type='hidden' id='srm0' name='srm0' value=<?php echo $srm0; ?>>
						<input type='hidden' id='n' name='n' value=<?php echo $nivel;?>>

						<input type="hidden" id="periodo" name="periodo" value="">
					</form>
					<form  id="frm-consulta" action="transfer_out.php" method="post">
						<input type='hidden' id='op' name='op' value=<?php echo $op;?>>
						<input type='hidden' id='te' name='te' value=<?php echo $tip_est;?>>
					</form>
					<div class='row mt-2 p-2 pb-0 mb-0' style="border-bottom: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC;">
						<div class='col-6 col-md-4 col-lg-2 text-right'>
							<span>Filtrar por:</span>
						</div>
						<div class='col-6 col-md-8 col-lg-10 p-0 m-0'>
							<table width="100%">
								<tr class="pt-0 pb-0 pl-0 pr-0">
									<th><a class="filtro">Soc. Comercial</a></th>
									<th><a class="filtro">SubBrocker</a></th>
									<th><a class="filtro">Gerencia</a></th>
									<th><a class="filtro">Comisionista</a></th>
								</tr>
							</table>
						</div>
<?php 
						if($op>=1)
						{

								if($filtro=="Soc. Comercial")
								{
									if($nivel != 3 and $nivel != 4 and $nivel != 5)
									{
										$btn="sc";
?>
										<div class='col-4 text-left pl-0 pr-0 pt-3'>
											<div class="autocompletar">
												<label for='txt-sc' class='etiqueta'>Socio Comercial</label>
												<input type="text" class="form-control form-control-sm" id="txt-sc" name="txt-sc" data-n='<?php echo $nivel;?>' data-ne='<?php echo $ne; ?>' autocomplete="off" value='<?php echo $valor1;?>'>
											</div>
										</div>

										<div class='col-4 text-left pl-0 pr-0 pt-3 ml-3' >
											<label for='txt-sc' class='etiqueta'>Seleccione un período</label>
											<select class="form-control form-control-sm" id="cmbPeriodo_SC" name="cmbPeriodo_SC" style="height:25px; line-height:25px;">
												<option value=""></option>
												<option value="NULL">Hoy</option>

												<?php
													$query = "SELECT fecha_to, anio, periodo FROM fec_transfer_out ORDER BY anio DESC, periodo DESC";
													$bd = $conecta->consulta($query);

													while ($tb = mysqli_fetch_array($bd)) {
														echo "<option value='".$tb['fecha_to']."'>".$tb['anio']." - ".$tb['periodo']."</option>";
													}
												?>
											</select>
										</div>
<?php
										if($nivel==13)
										{
?>
											<script type="text/javascript">
												$("#txt-sc").val("<?php echo $_SESSION['nombre']; ?>");
											</script>
<?php
										}
										else
										{
?>
											<script type="text/javascript">
												autocompletar('txt-sc',4,'sc_to',$("#txt-sc").data('n'));
											</script>
<?php
										}
									}
									else
									{
										mensaje("No tienes permisos para hacer consultas por Socio Comercial");
?>
										<script type="text/javascript">
											window.location="transfer_out.php";
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
										<div class='col-4 text-left pl-0 pr-0 pt-3'>
											<div class="autocompletar">
												<label for='txt-sb' class='etiqueta'>SubBrocker</label>
												<input type="text" class="form-control form-control-sm" id="txt-sb" name="txt-sb" data-n='<?php echo $nivel;?>' autocomplete="off" value='<?php echo $valor1;?>'>
											</div>
										</div>
										<div class='col-4 text-left pl-0 pr-0 pt-3 ml-3' >
											<label for='txt-sb' class='etiqueta'>Seleccione un período</label>
											<select class="form-control form-control-sm" id="cmbPeriodo_BR" name="cmbPeriodo_BR" style="height:25px; line-height:25px;">
												<option value=""></option>
												<option value="NULL">Hoy</option>

												<?php
													$query = "SELECT fecha_to, anio, periodo FROM fec_transfer_out ORDER BY anio DESC, periodo DESC";
													$bd = $conecta->consulta($query);

													while ($tb = mysqli_fetch_array($bd)) {
														echo "<option value='".$tb['fecha_to']."'>".$tb['anio']." - ".$tb['periodo']."</option>";
													}
												?>
											</select>
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
												autocompletar('txt-sb',4,'sb_sv',$("#txt-sb").data('n'));
											</script>
<?php
										}
									}
									else
									{
										mensaje("No tienes permisos para hacer consultas por SubBrocker");
?>
										<script type="text/javascript">
											window.location="transfer_out.php";
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
										<div class='col-4 text-left pl-0 pr-0 pt-3'>
											<div class="autocompletar">
												<label for='txt-gerencia' class='etiqueta'>Gerencia</label>
												<input type="text" class="form-control form-control-sm" id="txt-gerencia" name="txt-gerencia" data-n='<?php echo $nivel;?>' autocomplete="off" value='<?php echo $valor1;?>'>
											</div>
										</div>
										<div class='col-4 text-left pl-0 pr-0 pt-3 ml-3' >
											<label for='txt-gerencia' class='etiqueta'>Seleccione un período</label>
											<select class="form-control form-control-sm" id="cmbPeriodo_GER" name="cmbPeriodo_GER" style="height:25px; line-height:25px;">
												<option value=""></option>
												<option value="NULL">Hoy</option>

												<?php
													$query = "SELECT fecha_to, anio, periodo FROM fec_transfer_out ORDER BY anio DESC, periodo DESC";
													$bd = $conecta->consulta($query);

													while ($tb = mysqli_fetch_array($bd)) {
														echo "<option value='".$tb['fecha_to']."'>".$tb['anio']." - ".$tb['periodo']."</option>";
													}
												?>
											</select>
										</div>
<?php
										if($nivel != 4)
										{
?>
											<script type="text/javascript">
												autocompletar('txt-gerencia',4,'gerencia_to',$("#txt-gerencia").data('n'));
											</script>
<?php
										}
										else
										{
											$query="Select clave from empl_usuarios 
													where nombre='" . utf8_decode($usuario) . "'";
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
											window.location="transfer_out.php";
										</script>
<?php
									}
								}
								if($filtro=="Comisionista")
								{
									$btn="asesor";
?>
									<div class='col-4 text-left pl-0 pr-0 pt-3'>
										<div class="autocompletar">
											<label for='txt-asesor' class='etiqueta'>Comisionista</label>
											<input type="text" class="form-control form-control-sm" id="txt-asesor" name="txt-asesor" data-n='<?php echo $nivel;?>' autocomplete="off" value='<?php echo $valor1;?>'>
										</div>
									</div>
									<div class='col-4 text-left pl-0 pr-0 pt-3 ml-3' >
										<label for='txt-asesor' class='etiqueta'>Seleccione un período</label>
										<select class="form-control form-control-sm" id="cmbPeriodo_AS" name="cmbPeriodo_AS" style="height:25px; line-height:25px;">
											<option value=""></option>
											<option value="NULL">Hoy</option>

											<?php
												$query = "SELECT fecha_to, anio, periodo FROM fec_transfer_out ORDER BY anio DESC, periodo DESC";
												$bd = $conecta->consulta($query);

												while ($tb = mysqli_fetch_array($bd)) {
													echo "<option value='".$tb['fecha_to']."'>".$tb['anio']." - ".$tb['periodo']."</option>";
												}
											?>
										</select>
									</div>
<?php
									if($nivel != 3 and $nivel != 7)
									{
?>
										<script type="text/javascript">
											autocompletar('txt-asesor',4,'asesor_sv',$("#txt-asesor").data('n'));
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
?>
								<div class="col-1 pt-4">
									<img src="./img/aceptar1.png" class="btn-filtro" id="<?php echo 'btn-' . $btn; ?>">
								</div>
								<div class="col-1 pt-4">
									<img src="./img/excel.png" class="btn-filtro" id="getXLSX" width="30px">
								</div>
<?php 
						}
?>
					</div>
<?php
	
					if($op==2)
					{
						$parametro = ($periodo == "NULL" || $periodo == "")
						? "NULL"
						: "'" . $periodo . "'";

						$conecta->consulta("CALL create_acum_ventas($parametro)");

						// $conecta->consulta("CALL create_acum_ventas(NULL)");

						if (isset($conecta->conexion) && $conecta->conexion instanceof mysqli)
						{
							while ($conecta->conexion->more_results()) 
							{
								$conecta->conexion->next_result();
								if ($res = $conecta->conexion->store_result()) 
									$res->free();
							}
						}

						$conecta->consulta("CALL create_acum_transfer_out($parametro)");
						// $conecta->consulta("CALL create_acum_transfer_out(NULL)");

						$where = "";
						$campos = "";
						$thead_group=[];
						$thead = [];

						if ($filtro == 'Soc. Comercial') 
						{
							$thead_group=["Socio","Brocker","Gerencia","Comisionista"];
							$thead = ['Socio', 'Venta Acum SC', 'Venta Salida SC', '% Transfer out SC',
									  'Brocker', 'Venta Acum SB', 'Venta Salida SB', '% Transfer out SB',
									  'Gerencia', 'Venta Acum Ger', 'Venta Salida Ger', '% Transfer out Ger',
									  'Comisionista', 'Venta Acum Com.', 'Venta Salida Com.', '% Transfer out Com.']; 
							
							$campos = "CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'Socio',
									   CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.venta_acum END AS 'Venta Acum SC',
									   CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE IFNULL(sc.venta_ato,0) END AS 'Venta Salida SC',
									   CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE IFNULL(sc.por_sc,0) END AS '% Transfer out SC',

									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'Brocker',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.venta_acum END AS 'Venta Acum SB',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.venta_ato,0) END AS 'Venta Salida SB',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.por_sb,0) END AS '% Transfer out SB',

									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'Gerencia',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Acum Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Ger',

									   comi.nombre as Comisionista, comi.venta_acum as 'Venta Acum Com.', 
									   IFNULL(comi.venta_ato,0) AS 'Venta Salida Com.', comi.por_comi as '% Transfer out Com.'"; 

							if($valor1 == "TODOS")
								$where = "";
							else
								$where = "sc.sc = '$valor1'";
						}

						if ($filtro == 'SubBrocker') 
						{
							$thead_group=["","Brocker","Gerencia","Comisionista"];
							$thead = ['Socio','Brocker', 'Venta Acum SB', 'Venta Salida SB', '% Transfer out SB',
									  'Gerencia', 'Venta Acum Ger', 'Venta Salida Ger', '% Transfer out Ger',
									  'Comisionista', 'Venta Acum Com.', 'Venta Salida Com.', '% Transfer out Com.']; 

							$campos = "CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'Socio',

									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'Brocker',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.venta_acum END AS 'Venta Acum SB',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.venta_ato,0) END AS 'Venta Salida SB',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE IFNULL(sb.por_sb,0) END AS '% Transfer out SB',

									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'Gerencia',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Acum Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Ger',

									   comi.nombre as Comisionista, comi.venta_acum as 'Venta Acum Com.', 
									   IFNULL(comi.venta_ato,0) AS 'Venta Salida Com.', comi.por_comi as '% Transfer out Com.'"; 

							$where = "sb.brocker = '$valor1'";
						}

						if ($filtro == 'Gerencia') 
						{
							$thead_group=["","","Gerencia","Comisionista"];
							$thead = ['Socio','Brocker','Gerencia', 'Venta Acum Ger', 'Venta Salida Ger', '% Transfer out Ger',
									  'Comisionista', 'Venta Acum Com.', 'Venta Salida Com.', '% Transfer out Com.']; 
							
							$campos = "CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'Socio',
									   CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'Brocker',

									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'Gerencia',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.venta_acum END AS 'Venta Acum Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.venta_ato,0) END AS 'Venta Salida Ger',
									   CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE IFNULL(ger.por_ger,0) END AS '% Transfer out Ger',

									   comi.nombre as Comisionista, comi.venta_acum as 'Venta Acum Com.', 
									   IFNULL(comi.venta_ato,0) AS 'Venta Salida Com.', comi.por_comi as '% Transfer out Com.'"; 

							$where = "ger.gerencia = '$valor1'";
						}
						
						if ($filtro == 'Comisionista') 
						{
							$thead_group=["","","","Comisionista"];
							$thead = ['Socio','Brocker','Gerencia', 'Comisionista', 'Venta Acum Com.', 'Venta Salida Com.', '% Transfer out Com.']; 
							
							$campos = "CASE WHEN sc.sc IS NULL OR sc.sc = '' THEN '' ELSE sc.sc END AS 'Socio',
										CASE WHEN sb.brocker IS NULL OR sb.brocker = '' THEN '' ELSE sb.brocker END AS 'Brocker',
										CASE WHEN ger.gerencia IS NULL OR ger.gerencia = '' THEN '' ELSE ger.gerencia END AS 'Gerencia',

									   comi.nombre as Comisionista, comi.venta_acum as 'Venta Acum Com.', 
									   IFNULL(comi.venta_ato,0) AS 'Venta Salida Com.', comi.por_comi as '% Transfer out Com.'"; 

							$where = "comi.nombre = '$valor1'";
						}

						$where_sql = "";
						if ($where != "") 
							$where_sql = "WHERE $where";

						$query="Select $campos
								FROM (Select distinct av.sc, av.venta_sc as venta_acum,ifnull(ato.venta_sc,0) as venta_ato, 
											 ROUND((IFNULL(ato.venta_sc,0) / av.venta_sc) * 100, 2) as por_sc 
									  from acum_ventas av
										left join acum_tranfer_out ato on (av.sc=ato.sc)) sc
										left join (Select distinct av.sc,av.brocker, av.venta_sb as venta_acum,ifnull(ato.venta_sb,0) as venta_ato, 
														  ROUND((IFNULL(ato.venta_sb,0) / av.venta_sb) * 100, 2) as por_sb 
												   from acum_ventas av
														left join acum_tranfer_out ato on (av.brocker=ato.brocker)) sb on (sc.sc=sb.sc)
										left join (Select distinct av.sc, av.brocker, av.gerencia, av.venta_ger as venta_acum,
														  ifnull(ato.venta_ger,0) as venta_ato,
														  ROUND((IFNULL(ato.venta_ger,0) / av.venta_ger) * 100, 2) as por_ger 
												   from acum_ventas av
														left join acum_tranfer_out ato on (av.gerencia=ato.gerencia)) ger 
																on (sb.sc=ger.sc and sb.brocker=ger.brocker)
										left join (Select distinct av.gerencia, nombre, u.no_empleado,av.total_saldo_real as venta_acum,
														  ifnull(ato.total_saldo_real,0) as venta_ato, 
														  ROUND((IFNULL(ato.total_saldo_real,0) / av.total_saldo_real) * 100, 2) as por_comi 
												   from acum_ventas av
														left join acum_tranfer_out ato on (av.no_empleado=ato.no_empleado)
														inner join empl_usuarios u on (av.no_empleado=u.no_empleado)) comi on (ger.gerencia=comi.gerencia)
								$where_sql
								order by sc.sc,sb.brocker,ger.gerencia,comi.nombre";

						echo '<div class="row ml-0 mr-0 pl-0 pt-3 pr-0">
									<div class="col-12 p-0 m-0">
										<div class="info-sv">';

											echo "<table width='100%' id='t-screen' id='t_datos'";
											echo "<thead>";
												echo "<tr>";
													foreach ($thead_group as $col) 
													{
														$colspan="";
														if($col!="")
															$colspan="colspan='4'";
														echo "<th $colspan style='text-align: center'>$col</th>";
													}
												echo "</tr>";
												echo "<tr>";
													foreach ($thead as $col) 
													{
														$c= explode(" ",$col);
														if(in_array($c[count($c) - 1], ["SC", "SB", "Ger", "Com."]))
															$col=trim(str_replace($c[count($c) - 1],"",$col));

														echo "<th>$col</th>";
													}
												echo "</tr>";
											echo "</thead>";

											$bd = $conecta->consulta($query);

											while ($row = mysqli_fetch_assoc($bd)) 
											{
												$pctSC = (isset($row['% Transfer out SC']) && $row['% Transfer out SC'] !== '') ? (float)$row['% Transfer out SC'] : null;
												$pctBroker = (isset($row['% Transfer out SB']) && $row['% Transfer out SB'] !== '') ? (float)$row['% Transfer out SB'] : null;
												$pctGerencia = (isset($row['% Transfer out Ger']) && $row['% Transfer out Ger'] !== '') ? (float)$row['% Transfer out Ger'] : null;
												$pctAsesor = (isset($row['% Transfer out Com.']) && $row['% Transfer out Com.'] !== '') ? (float)$row['% Transfer out Com.'] : null;

												$colorSC = getColor($pctSC);
												$colorBroker = getColor($pctBroker);
												$colorGerencia = getColor($pctGerencia);
												$colorAsesor = getColor($pctAsesor);

												// NIVEL
												$nivel = 0;
												if ($pctSC !== null) $nivel = 1;
												if ($pctBroker !== null) $nivel = 2;
												if ($pctGerencia !== null) $nivel = 3;
												if ($pctAsesor !== null) $nivel = 4;

												echo "<tr class='tr_info'>";
													foreach ($thead as $col) 
													{
														$valorOriginal = $row[$col] ?? '';
														$esVacio = ($valorOriginal === '' || $valorOriginal === null);

														$valor = $valorOriginal;
														if (is_numeric($valor))
															$valor = number_format((float)$valor, 2);
														$claseTd = 'verde';

														// 🔹 SC
														if (!$esVacio && $nivel >= 1 && ($col == 'Socio' || $col == 'Venta Acum SC' || $col == 'Venta Salida SC' || $col == '% Transfer out SC')) 
															$claseTd = $colorSC;

														// 🔹 BROKER
														if (!$esVacio && $nivel >= 2 && ($col == 'Brocker' || $col == 'Venta Acum SB' || $col == 'Venta Salida SB' || $col == '% Transfer out SB'))
															$claseTd = $colorBroker;

														// 🔹 GERENCIA
														if (!$esVacio && $nivel >= 3 && ($col == 'Gerencia' || $col == 'Venta Acum Ger' || $col == 'Venta Salida Ger' || $col == '% Transfer out Ger')) 
															$claseTd = $colorGerencia;

														// 🔹 ASESOR
														if (!$esVacio && $nivel >= 4 && ($col == 'Comisionista' || $col == 'Venta Acum Com.' || $col == 'Venta Salida Com.' || $col == '% Transfer out Com.'))
															$claseTd = $colorAsesor;

														echo "<td class='{$claseTd}'>{$valor}</td>";
													}
												echo "</tr>";
											}

											echo "</table>";
						echo "			</div>
									</div>
								</div>";
					}
?>
				</div>
<?php
				$divcontenido->close();
			$divcontenedor->close();
			//$divpie->close();
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

  		  	$(".filtro").click(function()
  		  	{
  		  		$("#op").val(1);
  		  		$("#valor1").val('');
  		  		$("#valor2").val('');
  		  		$("#valor3").val('');
  		  		$("#valor4").val('');
  		  		$("#valor5").val('');
  		  		$("#filtro").val($(this).text());
  		  		$("#frm-filtro").submit();
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
					  case 'btn-sc':
					    	if($("#txt-sc").val()==""){
					    		alert("No se ha ingresado el Socio Comercial");
					  		}
							else
					    	{
								if($("#txt-sc").val().toUpperCase()=="TODOS"){
									console.log("Consulta para TODOS los Socios Comerciales");
									let sfp=0;
									let srm0=0;
									$("#valor1").val($("#txt-sc").val());
									$("#valor2").val($("#txt-fec-ini").val());
									$("#valor3").val($("#txt-fec-fin").val());
									$("#valor4").val($("#sel-mes-ini").val());
									$("#valor5").val($("#sel-mes-fin").val());
									$("#filtro").val($("#filtro").val().replace(" ","_"));
									$("#periodo").val($("#cmbPeriodo_SC").val());
									$("#loader").css("display","flex");
									$("#loader div:last").text("Consultando información...");
									mostrarLoader("Consultando información...");

									$("#frm-filtro").submit();
								}else{
									$.post("./ajax/validar_info.php",{ tipo: 'sc', valor1: $("#txt-sc").val(), valor4: $("#sel-est").val()}, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
												$("#valor1").val($("#txt-sc").val());
												$("#valor2").val($("#txt-fec-ini").val());
												$("#valor3").val($("#txt-fec-fin").val());
												$("#valor4").val($("#sel-mes-ini").val());
												$("#valor5").val($("#sel-mes-fin").val());
												$("#filtro").val($("#filtro").val().replace(" ","_"));
												$("#periodo").val($("#cmbPeriodo_SC").val());
												$("#loader").css("display","flex");
												$("#loader div:last").text("Consultando información...");
												mostrarLoader("Consultando información...");

												$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
								}
					    	}
					  break;
					  case 'btn-sb':
					    	if($("#txt-sb").val()=="")
					    		alert("No se ha ingresado el SubBrocker");
					    	else
					    	{
						    		$.post("./ajax/validar_info.php",{ tipo: 'sb', valor1: $("#txt-sb").val(), valor4: $("#sel-est").val() }, function(response)
									{
										if(response=="")
										{
											let sfp=0;
											let srm0=0;
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#filtro").val($("#filtro").val());
							  		  		$("#valor1").val($("#txt-sb").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#valor4").val($("#sel-mes-ini").val());
							  		  		$("#valor5").val($("#sel-mes-fin").val());
											$("#periodo").val($("#cmbPeriodo_BR").val());
											$("#loader").css("display","flex");
											$("#loader div:last").text("Consultando información...");
											mostrarLoader("Consultando información...");

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});

					    	}
					  break;
					  case 'btn-gerencia':
					    	if($("#txt-gerencia").val()=="")
					    		alert("No se ha ingresado la gerencia");
					    	else
					    	{
					    		$.post("./ajax/validar_info.php",{ tipo: 'gerencia', valor1: $("#txt-gerencia").val(), valor4: $("#sel-est").val() }, function(response)
								{
									if(response=="")
									{
										let sfp=0;
										let srm0=0;
						  		  		$("#te").val($("#sel-est").val());
						  		  		$("#filtro").val($("#filtro").val());
						  		  		$("#valor2").val($("#txt-fec-ini").val());
						  		  		$("#valor3").val($("#txt-fec-fin").val());
						  		  		$("#valor1").val($("#txt-gerencia").val());
						  		  		$("#valor4").val($("#sel-mes-ini").val());
						  		  		$("#valor5").val($("#sel-mes-fin").val());
										$("#periodo").val($("#cmbPeriodo_GER").val());
										$("#loader").css("display","flex");
										$("#loader div:last").text("Consultando información...");
										mostrarLoader("Consultando información...");

						  		  		$("#frm-filtro").submit();
									}
									else
										alert(response);
								});
					    	}
					  break;

					  case 'btn-asesor':
					    	if($("#txt-asesor").val()=="")
					    		alert("No se ha ingresado el comisionista");
					    	else
					    	{
						    		$.post("./ajax/validar_info.php",{ tipo: 'asesor', valor1: $("#txt-asesor").val(), valor4: $("#sel-est").val()}, function(response)
									{
										if(response=="")
										{
							  		  		$("#te").val($("#sel-est").val());
							  		  		$("#filtro").val($("#filtro").val());
							  		  		$("#valor1").val($("#txt-asesor").val());
							  		  		$("#valor2").val($("#txt-fec-ini").val());
							  		  		$("#valor3").val($("#txt-fec-fin").val());
							  		  		$("#valor4").val($("#sel-mes-ini").val());
							  		  		$("#valor5").val($("#sel-mes-fin").val());
											$("#periodo").val($("#cmbPeriodo_AS").val());
											$("#loader").css("display","flex");
											$("#loader div:last").text("Consultando información...");
											mostrarLoader("Consultando información...");

							  		  		$("#frm-filtro").submit();
										}
										else
											alert(response);
									});
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

			var loaderInterval=null;

			function mostrarLoader(texto){

				$("#loader").css("display","flex");

				$("#loaderTexto").text(texto);

				$("#loaderBarra").css("width","0%");

				$("#loaderPorcentaje").text("0%");

				let progreso=0;

				clearInterval(loaderInterval);

				loaderInterval=setInterval(function(){

					if(progreso<95){

						progreso++;

						$("#loaderBarra").css("width",progreso+"%");

						$("#loaderPorcentaje").text(progreso+"%");

					}

				},80);

			}

			function ocultarLoader(){

				clearInterval(loaderInterval);

				$("#loaderBarra").css("width","100%");

				$("#loaderPorcentaje").text("100%");

				setTimeout(function(){

					$("#loader").hide();

					$("#loaderBarra").css("width","0%");

					$("#loaderPorcentaje").text("0%");

				},500);

			}

        	// SUBMIT (botón generar)
			$("#getXLSX").click(function () {

				let formData = new FormData(); 
				formData.append("periodo", $("#periodo").val());

				formData.append("transfer_out", 'reporte');
				formData.append("filtro", $("#filtro").val());

				formData.append("textGerencia", $("#txt-gerencia").val());
				formData.append("textSubBrocker", $("#txt-sb").val());
				formData.append("textSocioComercial", $("#txt-sc").val());
				formData.append("textAsesor", $("#txt-asesor").val());

				mostrarLoader("Generando Excel...");

				fetch("rep_seg_transfer_out.php", {
					method: "POST",
					body: formData
				})
				.then(res => res.blob())
				.then(blob => {

					const url = window.URL.createObjectURL(blob);

					const a = document.createElement("a");

					a.href=url;

					a.download="reporte_Reporte_de_Transfer_Out.xlsx";

					document.body.appendChild(a);

					a.click();

					a.remove();

					ocultarLoader();

				})
				.catch(err => {
					console.error(err);
					ocultarLoader();
					alert("Error al generar el archivo");
				});

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
		});
	</script>
</html>
