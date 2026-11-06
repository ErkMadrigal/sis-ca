<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>CAROBRA Consultores Especializados</title>
		<link rel="shortcut icon" href="./img/favicon.ico">
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="./jquery/ui/css/ui-lightness/jquery-ui-1.8.16.custom.css"/>
		<script type="text/javascript" src="./js/jquery.formatCurrency-1.4.0.js"></script>
		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="./js/carobra.js"></script>
		<style type="text/css">
			.inputfile {
						    width: 0.1px;
						    height: 0.1px;
						    opacity: 0;
						    overflow: hidden;
						    position: absolute;
						    z-index: -1;
						}
			#btn-guardar-new	{
									color: white;
									background: #1D43A4;
									padding: 10px;
									border-radius: 10px;
								}
			#cap-cero,#subir-hoja-captura	{	cursor: pointer;	}

			#cap-cero:hover,#subir-hoja-captura:hover	{	font-weight: bold;	}

			#div-captura	{
								z-index: 10;
								width: 800px; 
								height: 250px;
								display: none;
								margin-top: 180px;
								background: white;
								margin-left: 370px; 
								position: absolute;
								border-radius: 20px;
								background: #F9F9F9;
								border: 1px solid #A7A5A5;
							}

			#vis-av		{
							z-index: 20;
							width: 550px; 
							height: 200px;
							display: none;
							margin-top: 250px;
							background: white;
							margin-left: 740px; 
							position: absolute;
							border-radius: 20px;
							background: #F9F9F9;
							border: 1px solid #A7A5A5;
						}

			#div-captura label	{	font-size: 11px;	}

			.i-cap-new	{
							width: 100%;
							height:22px !important;
							background-color: white !important;
							border: 1px solid #AFAFAF !important;
						}

			#tit_info	{
							margin: 0px;
							color: white;
							padding-right: 0px;
							font-size: 12px;
							font-weight: bold;
							text-align: center;
							background: #1D43A4;
							border-top-left-radius: 20px;
						}

			#tit_close	{
							margin: 0px;
							cursor: pointer;
							text-align: right; 
							background: #1D43A4;
							padding: 10px 10px 8px 10px;
							border-top-right-radius: 15px;
						}

			#inter-pdf {	cursor:pointer;	}

			.input-detalle	{
								width: 100%;
								font-size: 0.9em !important;
								padding: 3px;
								border: 1px solid #888888 !important;
							}

			.btn-carobra	{
								height: 25px;
								color: #FEFEFE;
								font-size: 1.1em;
								padding: 3px 8px;
								background-color: #2B73B6;
								border: 1px solid white;
								border-radius: 5px;
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

			.etiqueta	{
							padding-right: 5px;	
							font-weight: bold;	
						}

			.info	{
						padding: 3px;
						border-radius: 5px;
						border: 1px solid #CACACA;	
					}

			.advertencia 	{
								color: white;
								background: red;
							}
			.input-cap  {	
							height: 25px;
							padding-left: 5px;
							border-radius: 5px;
							margin-bottom: 7px;
						}

#detalle_produc	{
					position:absolute;
					left:20%;
					top:-5%;
					width:60%;
					height:105%;
					display:none;
					overflow: auto;
					text-align:center;
					margin: 0 auto;
					border-color:#9E9E9E;
					border-style:solid;
					border-width:1px;
					background: #FFFFFF;
					border-radius: 10px;
					box-shadow: 10px 10px -2px -2px #1C4383;
					color:#1D4384;
					font-size:12px;
					font-family:Verdana,Helvetica,sans-serif;
					overflow: hidden;
				}
.hoja-cap {	padding-right: 20px;	}

		</style>
	</head>
	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));

     	if($periodo > 2800)
     	{
      		session_destroy();
			mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");
      		//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");
      		//mensaje("Página temporalmente bloqueda por actualizaciones","contiene.php");
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
			//mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888");
		}
		else
		{
			reg_acceso($_SESSION["nombre"],"cap_prod_ne");
			$_SESSION["autentifica"]="";

			$info=addslashes(trim($_POST["info"]));
			$mensaje=addslashes(trim($_POST["mensaje"]));
			$fecha=addslashes(trim($_POST["fecha_cap"]));
			$nom_ger=addslashes(trim($_POST["cmbgerencia"]));
			$folio=addslashes(trim($_POST["folio"]));
			$cfolio=addslashes(trim($_POST["cfolio"]));
			$txtfolio=addslashes(trim($_POST["txtfolio"]));
			$cmbcurp=addslashes(trim($_POST["cmbcurp"]));
			$txtopor=addslashes(trim($_POST["txtopor"]));
			$ne=addslashes(trim($_POST["ne"]));

			if($cfolio != "")
				$txtfolio=$cfolio;
			
			if($mensaje != "")
				mensaje($mensaje);

			$bd= new conector();

			if($cmbcurp != "")
			{
				$fil="Select p.folio,viable
					  from produccion p 
							inner join (Select * from prod_trabajador where curp='$cmbcurp') pt 
									on (p.folio=pt.folio)
					  order by p.folio desc";
				$tb=$bd->consulta($fil);
				if ($tb->num_rows != 0)
				{
					$txtfolio="";
					while($row = $tb->fetch_array(MYSQLI_NUM))
					{
						if($txtfolio=="")
							$txtfolio=$row[0];
						if($row[1]==2)
						{
							$txtfolio=$row[0];
							break;
						}
					}
				}
				else
					mensaje("No hay ningún folio viable de pago para la CURP $cmbcurp. No se puede realizar por el momento la captura","cap_prod_ne.php");
			}
			else
			{
				if($txtopor != "")
				{
					$fil="Select p.folio
						  from produccion p 
								inner join (Select * from prod_trabajador where no_oportunidad='$txtopor') pt on (p.folio=pt.folio)";
					$tb=$bd->consulta($fil);
					if ($tb->num_rows != 0)
					{
						$row = $tb->fetch_array(MYSQLI_NUM);
						$txtfolio=$row[0];					
					}
					else
						mensaje("No hay ningún folio viable de pago para el número de oportunidad $txtopor. No se puede realizar por el momento la captura","cap_prod_ne.php");
				}
				else 
				{
					if($txtfolio != "")
					{
						$tb=$bd->consulta("Select folio from produccion where folio='$txtfolio'");
						if ($tb->num_rows == 0)
							mensaje("El folio $txtfolio no es viable de pago. No se puede realizar por el momento la captura","cap_prod_ne.php");
					}
				}				
			}


			if(strlen($nom_ger)> 5)
			{
				$fil="Select distinct id_gerencia from gerencias where gerencia='$nom_ger'";
				$tb=$bd->consulta($fil);
				$row = $tb->fetch_array(MYSQLI_NUM);
				$ger=$row[0];
			}
			else
			{
				$ger=$nom_ger;
				$fil="Select distinct gerencia from gerencias where id_gerencia='$ger'";
				$tb=$bd->consulta($fil);
				$row = $tb->fetch_array(MYSQLI_NUM);
				$nom_ger=$row[0];
			}

			$tb=$bd->consulta($fil);
			$row = $tb->fetch_array(MYSQLI_NUM);
			$ger=$row[0];

			$divpie=new div("pie");
			$divopcion=new div("opcion");
			$divcabecera=new div("cabecera");
			$divcontenido=new div("contenido");
			$fec_cap= new textbox("fecha_cap");
			$textfolio= new textbox("txtfolio");
			$textopor= new textbox("txtopor");
			$divcontenedor=new div("contenedor");
			$gerencia= new textbox("cmbgerencia");
			$fcurp= new textbox("cmbcurp");
			$tcontenido= new table('','','100%');
			$imgpie=new img("imgpie","./img/pie.png");
			$fenvio= new form("frmenvio","cap_prod_ne.php");
			$contiene= new textbox("contiene",'','hidden');
			$encabeza_pie= new div("encabeza","encabeza_pie");
			$divusuario= new div("usuario",'',"absolute","97%");
			$imgcabecera=new img("imgcabecera","./img/cabecera.png");

			$divcabecera->crear();
				$imgcabecera->crear();
				$imgcabecera->css("width","100%");
				titulo("Captura");
				$pag="PRODUCCION";
				menu();
			$divcabecera->close();
			
			$divpie->crear();
				$imgpie->crear();
				$imgpie->css("width","100%");
				$divusuario->crear();
					echo "<br><br>";
					$divusuario->css("top","30px");
					$divusuario->css("text-align","left");
?>
				<div id="vinculos" style="margin-top: 10px; margin-left: 25px;">
					<?php include("vinculos.php");?>
				</div>
				<form id='frm-envio-pdf' action='./ajax/subir_pdf.php' method='post' enctype='multipart/form-data'>
						<input type='hidden' id='pase' name='pase' value='N'>
				</form>
<?php			
				$sql="Select 1 from produccion p
						left join acompaniamiento a on (p.folio=a.folio)
					  where p.folio='$txtfolio' and cierre_cap=0 and ifnull(a.estatus,0)=0";

				$nom_aut_acomp='';
				$tb=$bd->consulta($sql);
				if ($tb->num_rows != 0)
					$nom_aut_acomp=$_SESSION["nombre"];
?>				
				<input type="hidden" id='txt-nom-usuario' name='txt-nom-usuario' value="<?php echo $nom_aut_acomp; ?>">
<?php
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: " . $_SESSION["nombre"] . "</spam>";
					echo "<div style='width:100%; text-align:right; margin-top:-15px;'>";
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
					$fec_bloqueo="";
					$cap_bloqueada="N";
					$base=$bd->consulta("Select * from bloqueo_pago");
					if ($base->num_rows != 0)
					{
						$tb=$base->fetch_array(MYSQLI_NUM);
						$fec_bloqueo=$tb[0];
						$hora_bloqueo=$tb[1];
					}
					if(date("d/m/Y")==$fec_bloqueo)
					{
						if(date("G")>= $hora_bloqueo)
						{
//							if($_SESSION["nombre"] != "Administrador del Sistema")
//								mensaje("Captura bloqueada por proceso de pagos","contiene.php");
							$cap_bloqueada="S";
						}
					}
					$encabeza_pie->crear();
						echo "<br>";

						$fenvio->crear();
							tex_h("op",1);
							tex_h("cad_hoja_cap","");
							$contiene->crear();
							echo "<div id='valida' style='display:none;'></div>";
							$tcontenido->crear();
								if($cap_bloqueada=="S")
								{
									$tcontenido->tr();
										$tcontenido->td("El proceso de pagos está activo. La captura se hará en temporales","",'advertencia','',1,8,'right');
									$tcontenido->close_tr();									
								}
								else
								{
									$tcontenido->tr();
										$tcontenido->td("","",'','',1,8,'right');
									$tcontenido->close_tr();									
								}

								$sql="Select max(str_to_date(fec_fin,'%d/%m/%Y')) as fec_tope from fec_cierre_campana cc
										join (Select distinct anio,periodo from cumplimiento 
											  where str_to_date(fecha,'%d/%m/%Y')=(Select max(str_to_date(fecha,'%d/%m/%Y')) from cumplimiento)) c 
											on (left(cc.campana,10)=c.periodo)";
								$bdf=$bd->consulta($sql);
								$tbf=mysqli_fetch_array($bdf);
									$fec_tope=$tbf['fec_tope'];

								$tcontenido->tr();
									$tcontenido->td("","",'','',1,7,'right');
								$tcontenido->close_tr();
								$tcontenido->tr();
									$tcontenido->td("Folio:","5%",'','',1,1,'right');
										$filfolio="Select *
													from (Select folio from produccion p
															inner join naps n on (p.nap=n.nap)
														  where STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') > '$fec_tope'
														  union all
														  Select '8020268579' as folio) f
													where folio like '%$%' 
													limit 0,10";
/*
Select folio from produccion p
														inner join naps n on (p.nap=n.nap)
												   where cierre_cap=0 and folio like '%$%' 
												   AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= '$fec_tope' 
												   limit 0,10
*/
										$_SESSION["txtfolio"]=$filfolio;
									$tcontenido->td($textfolio,'12%','','',1,1,'','',$txtfolio);
										$textfolio->autocompletar($filfolio,3);
										$textfolio->css("width","100%");

									$tcontenido->td("No. oportunidad:","12%",'','',1,1,'right');
										$filopor="Select no_oportunidad from prod_trabajador pt 
													inner join produccion p on (p.folio=pt.folio) 
													inner join naps n on (p.nap=n.nap)
												  where no_oportunidad like '%$%'
												  AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) 
												  limit 0,10";
										$_SESSION["txtopor"]=$filopor;
									$tcontenido->td($textopor,'12%','','',1,1,'','',$txtopor);
										$textopor->autocompletar($filopor,3);
										$textopor->css("width","100%");

										$html='<label for="file-pdf"><img src="./img/pdf.png" width="30px" id="inter-pdf"></label><input type="file" name="file-pdf" id="file-pdf" class="inputfile">';

									$tcontenido->td($html,'7%','','',1,1,'','');
									$niv= nivel_usuario(utf8_decode($_SESSION["nombre"]));
									$bdd=$bd->consulta("Select clave from empl_usuarios 
															where nombre='" . utf8_decode($_SESSION["nombre"]) . "'");
									$tbb=mysqli_fetch_array($bdd);
									$clave=$tbb['clave'];

									$fil= "Select date_sub('" . substr($fecha,6,4) . "-" . substr($fecha,3,2) . "-" . substr($fecha,0,2);
									if($niv==12)
									{
										$fechap='01/01/2018';
										$filg="Select distinct gerencia from gerencias where gerencia like '%$%'";
									}
									else
									{
										$fil.= "', interval 7 day)";
										$tb2=$bd->consulta($fil);
										$row2 = $tb2->fetch_array(MYSQLI_NUM);
										$fechap= substr($row2[0],8,2) . "/" . substr($row2[0],5,2) . "/" . substr($row2[0],0,4);

										$filg="Select distinct g.gerencia 
											   from (Select distinct id_gerencia,gerencia 
											   		 from gerencias 
											   		 where gerencia like '%$%') g 
													inner join (Select distinct id_gerencia
																from usuarios_nivel_gerencia ung
																	inner join (Select clave from empl_usuarios 
																				where nombre='" . $_SESSION["nombre"] . "') eu
																			on(ung.clave=eu.clave)) ung on (g.id_gerencia=ung.id_gerencia)";
									}
									$fil_curp="Select CURP 
											   from prod_trabajador pt 
											   		inner join produccion p on (pt.folio=p.folio)
											   		inner join naps n on (p.nap=n.nap)
											   where cierre_cap=0 and CURP like '%$%' 
											   AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
											   limit 0,10";
									$tcontenido->td("CURP:","4%",'','',1,1,'right');
										$_SESSION["cmbcurp"]=$fil_curp;
										$tcontenido->td($fcurp,'20%','','',1,1,'left','',$cmbcurp);
										$fcurp->autocompletar($fil_curp,4);
										$fcurp->css("width","100%");

										if(($clave=='administra' or $clave=='LUFERCONRA' or $clave=='COYHERNAND' or $clave=='JAGONZALEZ' or $clave='VANEGUADUR') and $cap_bloqueada=="N")
											$tcontenido->td("<span id='cap-cero'>Captura desde cero</apan>","20%",'','',1,1,'right');
										else
											$tcontenido->td("<span>Captura desde cero</apan>","12%",'','',1,1,'right');
								$tcontenido->close_tr();
							$tcontenido->close();
						$fenvio->close();
					if($txtfolio != "")
					{
						$query="Select p.nap 
								from (Select folio,nap from produccion where folio='$txtfolio') p
									inner join naps n on (p.nap=n.nap)
								where baja=1";
						$bdn=$bd->consulta($query);
						if ($bdn->num_rows != 0)
						{
							$bd->consulta("Update produccion set id_estatus_prod=6 where folio='$txtfolio'");

							$query="Update prod_trabajador 
									set observa=concat(observa,' El NAP de este folio está inhabilitado')
									where folio='$txtfolio'";
							$bd->consulta($query);

							$bdn=$bd->consulta("Select * from prod_rechazos where folio='$txtfolio'");
							if ($bdn->num_rows == 0)
								$bd->consulta("Insert into prod_rechazos values('$txtfolio','" . date("d/m/Y") . "',2,2806,0)");

							mensaje("Folio con NAP inhabilitado... No puede ser capturado","cap_prod_ne.php");
						}
						$query="Select nap,fec_traspaso from produccion where folio='$txtfolio'";
						$bdd=$bd->consulta($query);

						$tb=mysqli_fetch_array($bdd);
	 					$onap=$tb["nap"];
	 					$ofec_traspaso=$tb["fec_traspaso"];

	 					$od=explode("/",$ofec_traspaso);
	 					$ofec_unix=$od[2] . "-" . $od[1] . "-" . $od[0];
						$query="Select mes, anio from fechas_cierre_com
							    where datediff('$ofec_unix',concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
									and datediff('$ofec_unix',concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0";
						$bdd=$bd->consulta($query);
						$tb=mysqli_fetch_array($bdd);
						$omes=$tb["mes"];
						$oanio=$tb["anio"];

	 					$query="Select v,folxnap
								from (Select nap,count(*) as v 
										from produccion p
											inner join fechas_cierre_com fcc
													on (str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date(fec_ini,'%d/%m/%Y') 
															and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date(fec_fin,'%d/%m/%Y'))
											left join movilidad m on (p.folio=m.folio)
										where nap='$onap' and  anio=$oanio and mes=$oanio and id_estatus_prod != 6 and ifnull(movilidad,0)=1
										group by nap) p
									inner join (Select nap,
												  case when n.mes in (9,10,11) and n.anio=2026 then
												  			case when sc='VALEAL CONSULTORES' or sc='JORGE GUERRERO SOTO' 
												  						or brocker='CRISTIHAN ISRAEL NUNO GONZALEZ' then 45
												  				 else folxnap
												  			end 
												  	   else fcc.folxnap
												  end as folxnap
											from (Select * from fechas_cierre_com fcc where anio=$oanio and mes=$oanio) fcc
												inner join (Select $oanio as anio,$oanio as mes,sc,brocker,nap from naps n
																inner join estruc_empleados ee on (n.no_empleado=ee.no_empleado)
															where baja=0 and nap='$onap') n on (fcc.anio=n.anio and fcc.mes=n.mes)) n on (p.nap=n.nap)";
						$bdd=$bd->consulta($query);
						$tb=mysqli_fetch_array($bdd);
						$nfol_nap=$tb["v"];
						$folios_per=$tb["folxnap"];

						if($nfol_nap > ($folios_per - 5))
								mensaje("El NAP $onap tiene $nfol_nap folios ingresados en el mes comercial, está cerca del límite de los $folios_per permitidos para que no genere cobro de comisión");

						if($nfol_nap > $folios_per)
							mensaje("El NAP $onap tiene ya más de $folios_per folios ingresados en el mes comercial, es altamente probable que no se pague comisión");












						$nap= new textbox("nap","naps");
						$selmov = new select("mov");
						$selafore = new select("afo_cedente");
						$textdatos= new textbox('folio');
						$selacompania = new select("monto_acomp","acompania");
						$textempleado=new textbox("empleado","empleado");
						$divcont_datos = new div("cont_datos");
						$tdatos= new table('','','100%');
						$textsaldos= new textbox("saldo","moneda");
						$textenvio= new textbox("folio",'','hidden');
						$fecha_mov= new textbox('fecha_mov','fecha_movi');
						$imgguarda=new img("imgguarda","./img/guardar1.png","./img/guardar2.png",'boton_tabla');
						
						$nom=array("Folio","NSS","Comisionista","Tipo mov","fecha mov","NAP","Acomp","Saldo estimado","Afore cedente");
						$tam=array("8%","8%","21%","9%","7%","8%","13%","7%","11%");
						$tdatos->identificador("encabeza");

						$encabeza_pie->close();
						$tdatos->css("font-size","9px",'th');

						$fenvio->identificador("fdatos");
						$fenvio->crear();
							tex_h("fecha_cap",$fecha);
							tex_h("cmbgerencia",$ger);
							tex_h("txtfolio",$txtfolio);
							tex_h("contiene");
							tex_h("dfolio");
							tex_h("dnss");
							tex_h("dempleado");
							tex_h("dtipo_mov");
							tex_h("dfec_mov");
							tex_h("dnap");
							tex_h("dsaldo");
							tex_h("dafo_cedente");
							tex_h("dacompania");
							tex_h("estatus_av");
							tex_h("op");
							tex_h("cap_bloqueada",$cap_bloqueada);
							tex_h("hoja_cap");
						$fenvio->close();
						$fil= "Select id_tipo,movimiento from tipo_mov where id_tipo < 3";
						$selmov->query($fil);
						$selafore->query("Select * from afores");

						$qprod="produccion";
						$qprod_trab="prod_trabajador";

						if($cap_bloqueada=="S")
						{
							$query="Select folio from cap_prod_tem where folio='$txtfolio'";
							$bdd=$bd->consulta($query);
							if ($bdd->num_rows != 0)
							{
								$qprod="cap_prod_tem";
								$qprod_trab="cap_prod_trabajador_tem";
							}
						}

						$fil="Select distinct p.folio,p.nss,nombre,id_tipo_mov,fec_traspaso,p.nap,
									 saldo_estimado,ifnull(trabajador,'0'),cierre_cap,afo_cedente,
									 ifnull(costo,0) as costo,p.no_empleado, gerencia,
									 g.id_gerencia,trabajador, no_oportunidad, pt.curp
							  from (Select * from $qprod where folio='$txtfolio') p 
							  	left join gerencias g on (p.nap=g.NAP) 
							  	left join $qprod_trab pt on (p.folio=pt.folio) 
							  	left join empleados e on (p.no_empleado=e.no_empleado) 
							  	left join usuarios u on (e.clave=u.clave) 
							  	left join acompaniamiento acom on (p.folio=acom.folio)
							  where p.folio not in (select folio from autoriza 
							  						where aut_cambio_ger=1)
							  union
							  Select distinct p.folio,p.nss,nombre,id_tipo_mov,fec_traspaso,p.nap,saldo_estimado,
							  		 ifnull(trabajador,'0'),cierre_cap,afo_cedente,ifnull(costo,0) as costo,p.no_empleado, gerencia,
							  		 g.id_gerencia,trabajador, no_oportunidad, pt.curp
							  from (Select * from $qprod where folio='$txtfolio') p 
							  	inner join autoriza a on (p.folio=a.folio) 
							  	inner join gerencias g on (a.id_gerencia=g.id_gerencia) 
							  	left join $qprod_trab pt on (p.folio=pt.folio) 
							  	left join empleados e on (p.no_empleado=e.no_empleado) 
							  	left join usuarios u on (e.clave=u.clave) 
							  	left join acompaniamiento acom on (p.folio=acom.folio)
							  where aut_cambio_ger=1";
						$tb=$bd->consulta($fil);						
						if ($tb->num_rows > 0)
						{
								$tdatos->identificador("t_informa");		
								$tdatos->crear();
									$i=0;
    								while ($row = $tb->fetch_array(MYSQLI_NUM))
	   								{
	   									$cierre_cap=$row[8];
	   									tex_h("detalle-nss",$row[1]);
	   									tex_h("sc-saldo",$row[6]);
	   									tex_h("sc-fec_mov",$row[4]);
	   									tex_h("sc-nap",$row[5]);
?>
<script type="text/javascript">
										$("#estatus_av").val(<?php echo $cierre_cap; ?>);
</script>
<?php
	   									if($i==0)
	   									{
											if($txtfolio !="")
											{
												$ger=$row[13];
												$gerencia=$row[12];
?>
<script type="text/javascript">
												$("#cmbgerencia").val("<?php echo $gerencia; ?>");
												$('#fdatos #cmbgerencia').val("<?php echo $gerencia; ?>");
</script>
<?php
											}
											if($row[3]==2)
												$filauto="Select distinct nombre from usuarios u
															inner join (Select clave 
																		from usuarios_nivel_gerencia
																		where pertenece=1 
																			and (id_nivel=3 or id_nivel=7)
																			and id_gerencia !=493) ung 
																	on (u.clave=ung.clave)
														  where nombre like '%$%' limit 0,5";
											else
												$filauto="Select distinct nombre from usuarios u
															inner join (Select clave 
																		from usuarios_nivel_gerencia
																		where pertenece=1 
																			and (id_nivel=3 or id_nivel=7)
																			and id_gerencia != 124
																			and id_gerencia !=493) ung 
																	on (u.clave=ung.clave)
														  where nombre like '%$%' limit 0,5";

											$filnap="Select distinct nap from naps n
														inner join (Select no_empleado from usuarios_nivel_gerencia where pertenece=1
																		and id_gerencia=$ger) ung on (n.no_empleado=ung.no_empleado)
													 where baja=0 and nap like '%$%'";
										}
	   									$query="Select nap from naps 
	   											where no_empleado=" . $row[11] . " and baja=0";
	   									$bd_nap=$bd->consulta($query);
										if ($bd_nap->num_rows > 0)
											$fol_p=1;
										else
											$fol_p=0;
	   									tex_h("fol_propio" . $i,$fol_p);

										$tdatos->tr();
											$tdatos->td("&nbsp;","",'','',1,6,'right');
											$tdatos->td("<span id='subir-hoja-captura'>Subir hoja de captura</span><input type='file' name='file-hoja-captura' id='file-hoja-captura' class='inputfile'>","100%",'hoja-cap','',1,2,'right');
										$tdatos->close_tr();
										$tdatos->tr();
											$tdatos->td("&nbsp;","",'','',1,6,'right');
										$tdatos->close_tr();
										$tdatos->tr();
											$tdatos->td("&nbsp;","",'','',1,6,'right');
										$tdatos->close_tr();
	   									$tdatos->tr();
	   										$tdatos->td("No. oportunidad:",'14%','','' . $i,1,1,'right');
	   										$tdatos->td($row[15],'12%','','' . $i,1,1,'left');
	   										$tdatos->td("CURP:",'10%','','' . $i,1,3,'right');
	   										$tdatos->td($row[16],'10%','','' . $i,1,1,'left');
	   									$tdatos->close_tr();
	   									$tdatos->tr();
	   										$tdatos->td("",'18%','','' . $i,1,1,'right');
	   									$tdatos->close_tr();
										$tdatos->tr();
											$tdatos->td("",'18%','','' . $i,1,1,'right');
											$tdatos->td("Folio:",'12%','etiqueta','' . $i,1,1,'right');
											$tdatos->td($row[0],'10%','info','folio' . $i,1,1,'');

											$tdatos->td("NSS:",'10%','etiqueta','' . $i,1,1,'right');
											$tdatos->td($row[1],'10%','info',$row[1] . $i,1,1,'');

											$tdatos->td("Tipo mov:",'10%','etiqueta','' . $i,1,1,'right');
											$selmov->identificador("tipo_mov" . $i);
											$tdatos->td($selmov,'12%','info','',1,1,'','',$row[3]);
												$selmov->css("width","100%");

											$tdatos->td("",'18%','','' . $i,1,1,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,8,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'18%','','' . $i,1,1,'right');

											$tdatos->td("Comisionista:",'12%','etiqueta','' . $i,1,1,'right');

											//$textempleado->identificador("empleado" . $i);
											//$_SESSION["empleado" . $i]=$filauto;

											$textempleado->identificador("empleado");
											$_SESSION["empleado"]=$filauto;

											$tdatos->td($textempleado,'','info','',1,5,'','',utf8_encode($row[2]));
												$textempleado->autocompletar($filauto,3);
												$textempleado->css("width","100%");
												$textempleado->css("border","none");

											$tdatos->td("",'18%','','' . $i,1,1,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,8,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'18%','','' . $i,1,1,'right');

											$tdatos->td("Cliente:",'12%','etiqueta','' . $i,1,1,'right');

											$tdatos->td($row[14],'10%','info',$row[14] . $i,1,5,'left');

											$tdatos->td("",'18%','','' . $i,1,1,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,8,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'18%','','' . $i,1,1,'right');

											$tdatos->td("Fecha mov:",'12%','etiqueta','' . $i,1,1,'right');

											$fecha_mov->identificador('fecha_mov' . $i);
											$tdatos->td($fecha_mov,'','','',1,1,'','',$row[4]);
											$fecha_mov->css("width",'100%');
											$fecha_mov->css("text-align",'left');

											$tdatos->td("NAP:",'10%','etiqueta','' . $i,1,1,'right');
											$nap->identificador("nap" . $i);
											$tdatos->td($nap,"8%",'','',1,1,'','',$row[5]);
											$nap->autocompletar($filnap,3);
											$nap->css("width",'100%');
											$nap->css("text-align",'left');

											$tdatos->td("Acompañamiento:",'10%','etiqueta','' . $i,1,1,'');
											$selacompania->identificador("monto_acomp" . $i);
											$selacompania->lista(0,"$0.00");
											$selacompania->lista(50,"$50.00");
											$selacompania->lista(100,"$100.00");
											$selacompania->lista(150,"$150.00");
											$selacompania->lista(200,"$200.00");
											$selacompania->lista(250,"$250.00");
											$selacompania->lista(300,"$300.00");
											$selacompania->lista(350,"$350.00");
											$selacompania->lista(400,"$400.00");
											$selacompania->lista(450,"$450.00");
											$selacompania->lista(500,"$500.00");
											$tdatos->td($selacompania,'13%','info','',1,1,'','',$row[10]);
												$selacompania->css("width","100%");

											$tdatos->td("",'18%','','' . $i,1,1,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,8,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'18%','','' . $i,1,1,'right');

											$tdatos->td("Saldo estimado:",'12%','etiqueta','' . $i,1,1,'right');
											$tdatos->td($row[6],'','moneda info',$row[6] . $i,1,1,'right');

											$tdatos->td("Afore cedente:",'12%','etiqueta','' . $i,1,1,'right');
											$selafore->identificador("afo_cedente" . $i);
											$tdatos->td($selafore,'','info','',1,2,'','',$row[9]);
											$selafore->css("width","100%");
										$tdatos->close_tr();
										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,8,'right');
										$tdatos->close_tr();

										$tdatos->tr();
											$tdatos->td("",'','','' . $i,1,3,'right');

											if($row[7]=='0')
												$color="#ff0000";
											else
												$color="#1d4384";
											$tdatos->td("<a class='detalle' id='detalle" . $i . "' style='color:$color;'>detalle</a>",'right');
											if($row[8]=='0')
											{
												$imgguarda->identificador("guarda" . $i);
												$imgguarda->alt("Actualizar registro");
												$tdatos->td($imgguarda,'','','',1,1,'left');
												$imgguarda->css("width","20px");
											}
											else 
											{
												$tdatos->td('','');
											}
											$tdatos->td("",'18%','','' . $i,1,3,'right');
										$tdatos->close_tr();
										$i++;
    								}
    							$tdatos->close();
    							//$tdatos->css("margin-top","2px");
    							$tdatos->css("font-size","11px",'td,select,input[type=text]');
						}
					}
					if(strlen($folio) > 0)
					{
						$cad_hc=addslashes(trim($_POST["cad_hc"]));


						//$divdetalle=new div("detalle_produc");
						//$tdetalle= new table('tdetalle','','100%');
						//$imgcerrar= new img("cerrar","./img/cerrar1.png","./img/cerrar2.png");
						$imgguarda_det= new img("guardar_new","./img/bguardar1.png","./img/bguardar2.png",'boton');

						$_SESSION["dfolio_av"]="Select concat(folio,' / ',tramite) from av_general 
											   where concat(folio,' / ',tramite)  like '%$%' limit 0,5";

						$tpt="prod_trabajador";
						$tapovol="apovol_prod";
						if($cap_bloqueada=="S")
						{
							$query="Select folio from cap_prod_trabajador_tem where folio='$folio'";
							$bdd=$bd->consulta($query);
							if ($bdd->num_rows != 0)
								$tpt="cap_prod_trabajador_tem";

							$query="Select * from cap_apovol_prod_tem where folio_pr='$folio'";
							$bdd=$bd->consulta($query);
							if ($bdd->num_rows != 0)
								$tapovol="cap_apovol_prod_tem";
						}
						$fil="Select trabajador,empresa,telefono,celular,observa,pt.curp,email_trab,
									 sal_mes,fec_sello,vigencia, cp,
									 concat(avg.folio,' / ',tramite) as folio_av 
							  from $tpt pt
							  	left join $tapovol ap on (pt.folio=ap.folio_pr)
							  	left join av_general avg on (ap.folio_av=avg.tramite)
							  where pt.folio='$folio'";
						$tb=$bd->consulta($fil);
						$row = $tb->fetch_array(MYSQLI_NUM);

						if($cad_hc!="")
						{
							$info_hc=explode("|", $cad_hc);
							$empresa=$info_hc[0];
							$email=$info_hc[1];
							$cel=$info_hc[2];
							$cp=$info_hc[3];
							$apovol=$info_hc[4];
						}
						else
						{
							$empresa=$row[1];
							$email=$row[6];
							$cel=$row[3];
							$cp=$row[10];
							$apovol=$row[11];
						}
?>
						<input type="hidden" id="dfec_sello" name="dfec_sello" value="">
						<input type="hidden" id='dsal_mes' name='dsal_mes' value=0>
						<input type="hidden" id='dtelefono' name='dtelefono' value="">
						<input type="hidden" id="dobserva" name="dobserva">
						<div id="detalle_produc" style='display: inline-block; padding: 2px 5px;'>
							<table width="102%" style="background-color: #2384C5; color: white; padding: 0; margin-left: -5px; margin-right: -5px; margin-top: -5px;">
								<tr>
									<td style='width: 85%; font-size: 1.7em; text-align: left; padding-left: 5px;'><?php echo $folio; ?></td>
									<td style='width: 15%; text-align: right; vertical-align: top; padding-top: 5px; padding-right: 5px;'>
										<img id="cerrar" src="./img/cerrar1.png" style="height: 15px; cursor: pointer;">
									</td>
								</tr>
							</table>
							<form  id="fdetalle" action="cap_prod_ne.php" method="post">
<?php
								tex_h("op");
								tex_h("contiene");
								tex_h("nombre");
								tex_h("empresa");
								tex_h("telefono");
								tex_h("celular");
								tex_h("observa");
								tex_h("CURP");
								tex_h("email_trab");
								tex_h("dfolio");
								tex_h("sal_mes");
								tex_h("fec_sello");
								tex_h("vigencia");
								tex_h("cp");
								tex_h("extra");
								tex_h("folio_av");
								tex_h("av_validada",0);
								tex_h("ancla");
								tex_h("cap_bloqueada",$cap_bloqueada);
?>
							</form>
							<table width="100%" style="margin-top: -17px;">
								<tr>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
									<td style='width: 10%;'></td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Subir SINDO histórico</td>
									<td>
										<label for="si-pdf">
											<img src="./img/pdf.png" class="subir-pdf" style="height: 30px; cursor: pointer;">
										</label>
										<input type="file" name="si-pdf" id="si-pdf" class="inputfile">
									</td>
									<td></td>
									<td colspan="4" style='text-align: right;'>Subir Semanas Cotizadas</td>
									<td>
										<label for="sc-pdf">
											<img src="./img/pdf.png" class="subir-pdf" style="height: 30px; cursor: pointer;">
										</label>
										<input type="file" name="sc-pdf" id="sc-pdf" class="inputfile">
									</td>
								</tr>
								<tr>
									<th colspan="10" style='text-align: center; padding-top: 8px; padding-bottom: 8px; font-size: 1.4em;'>Datos del Cliente</th>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Nombre:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='dnombre' id='dnombre' value="<?php echo utf8_encode($row[0]); ?>" >
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>CURP:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='dCURP' id='dCURP' value="<?php echo utf8_encode($row[5]); ?>" >
									</td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Empresa:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='dempresa' id='dempresa' value="<?php echo utf8_encode($empresa); ?>" >
									</td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Email:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='demail_trab' id='demail_trab' value="<?php echo utf8_encode($email); ?>" >
									</td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Celular:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='dcelular' id='dcelular' value="<?php echo utf8_encode($cel); ?>" >
									</td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Anclar email y celular:</td>
									<td>
										<select name="sel-ancla" id="sel-ancla" class='input-detalle input-cap'>
											<option value=-1></option>
											<option value=0>No</option>
											<option value=1 <?= ($row[9]==1) ? 'SELECTED' : '';?>>Si</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="3" style='text-align: right;'>CP:</td>
									<td colspan="6">
										<input type="text" class='input-detalle input-cap' name='dcp' id='dcp' value="<?php echo $cp; ?>" >
									</td>
								</tr>
								<tr><br></tr>
								<tr>
									<td colspan="3" style='text-align: right;'>Folio AV:</td>
									<td colspan="2"  style="padding-top: 5px;">
										<input type="text" class='input-detalle input-cap' name='dfolio_av' id='dfolio_av' value="<?php echo $apovol; ?>" >
									</td>
									<td colspan="2">
										<button class='btn-carobra' id="valida_AV">Validar AV</button>
									</td>
									<td colspan="2">
										<button class='btn-carobra' id="guardar_new" style='width: 100%;'>Guardar</button>
									</td>
								</tr>
							</table>
						</div>
<?php
				}
				else 
					$encabeza_pie->close();
			$divcontenido->close();
			$divcontenido->css("overflow","auto");
		$divpie->close();
	$divcontenedor->close();
?>
	<div id='vis-av'>	
		<table width="100%">
			<tr>
				<th id="tit_info" width="95%">Detalle Aportación Voluntria</th>
				<th id="tit_close" width="5%"><img src="./img/cerrar1.png" id="cerrar-av" width="17px"></th>
			</tr>
		</table>
		<div style="padding: 20px;">
			<table width="100%">
				<tr>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
					<td style="width: 10%;"></td>
				</tr>
				<tr>
					<th style='text-align: right; padding: 5px;'>Folio:</th>
					<td colspan="5" style='padding: 5px;'><span id="folio-av"></span></td>
					<th colspan="2" style='text-align: right;padding: 5px;'>No Trámite:</th>
					<td colspan="2" style='text-align: right; padding: 5px;'><span id="tramite-av"></span></td>
				</tr>
				<tr>
					<th style='text-align: right; padding: 5px;'>Cliente:</th>
					<td colspan="5" style='padding: 5px;'><span id="cliente-av"></span></td>
					<th colspan="2" style='text-align: right;padding: 5px;'>NSS:</th>
					<td colspan="2" style='text-align: right; padding: 5px;'><span id="nss-av"></span></td>
				</tr>
				<tr>
					<th style='text-align: right; padding: 5px;'>CURP:</th>
					<td colspan="5" style='padding: 5px;'><span id="curp-av"></span></td>
					<th colspan="2" style='text-align: right;padding: 5px;'>NAP:</th>
					<td colspan="2" style='text-align: right; padding: 5px;'><span id="nap-av"></span></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td colspan="2"><button class='btn-carobra' id="btn-cancela-av">Cancelar</button></td>
					<td colspan="2"><button class='btn-carobra' id="btn-valida-av">Validar</button></td>
					<td colspan="3"></td>
				</tr>
			</table>
		</div>
	</div>

	<div id='div-captura'>
		<table width="100%">
			<tr>
				<th id="tit_info" width="95%">Captura desde cero</th>
				<th id="tit_close" width="5%"><img src="./img/cerrar1.png" id="cerrar-s" width="17px"></th>
			</tr>
		</table>
		<div style="padding: 10px;">
			<form  id="frm-cap-new" action="facilitador.php" method="post">
				<table width="100%">
					<tr>
						<td style="width: 6%;"></td>
						<td style="width: 10%;"></td>
						<td style="width: 14%;"></td>
						<td style="width: 8%;"></td>
						<td style="width: 7%;"></td>
						<td style="width: 13%;"></td>
						<td style="width: 7%;"></td>
						<td style="width: 12%;"></td>
						<td style="width: 12%;"></td>
						<td style="width: 11%;"></td>
					</tr>
					<tr>
						<th  colspan="2" style="text-align: left;">Folio</th>
						<th  colspan="5" style="text-align: left;">Nombre</th>
						<th  colspan="3" style='text-align: left;'>CURP</th>
					</tr>
					<tr>
						<td colspan="2"><input type="text" class="i-cap-new" id="cfolio" name="cfolio"></td>
						<td colspan="5"><input type="text" class="i-cap-new" id="cnombre" name="cnombre"></td>
						<td colspan="3"><input type="text" class="i-cap-new" id="ccurp" name="ccurp"></td>
					</tr>
					<tr>
						<th colspan="2" style="text-align: left;">NSS</th>
						<th style="text-align: left;">Fec traspaso</th>
						<th colspan="2" style="text-align: left;">Movimiento</th>
						<th style="text-align: left;">NAP</th>
						<th style="width: 20%; text-align: left;">Saldo</th>
						<th colspan="3" style='text-align: left;'>Estatus</th>
					</tr>
					<tr>
						<td colspan="2"><input type="text" class="i-cap-new" id="cnss" name="cnss"></td>
						<td><input type="text" class="i-cap-new" id="cfec_traspaso" name="cfec_traspaso"></td>
						<td colspan="2">
							<select class="i-cap-new" id="cmov" name="cmov">
								<option value=0></option>
								<option value=1>Traspaso</option>
								<option value=2>Afiliación</option>
								<option value=3>Recertificación</option>
							</select>
						</td>
						<td><input type="text" class="i-cap-new" id="cnap" name="cnap"></td>
						<td><input type="text" class="i-cap-new moneda" id="csaldo" name="csaldo"></td>
						<td colspan="3">
							<select class="i-cap-new" id="cestatus" name="cestatus">
								<option value=0></option>
								<option value=1>Certificado</option>
								<option value=3>Aceptada Operaciones</option>
								<option value=4>Capturado en Sistema</option>
								<option value=5>Enviada a Certificar</option>
								<option value=6>Rechazado</option>
								<option value=15>Aceptada procesar</option>
								<option value=20>En validación</option>
								<option value=22>Recibida operaciones</option>
							</select>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<table width="100%">
			<tr>
				<td style="width: 80%; text-align: center; font-size: 14px; color: #1D43A4; padding-top: 20px;">
					Después de capturar los datos esenciales, continuar con la captura complementaria
				</td>
				<td style="width: 20%; padding-top: 25px; padding-right: 10px; text-align: right;">
					<a id='btn-guardar-new' style='width: 100%;'>Guardar</a>
				</td>
			</tr>
		</table>
	</div>

	<form id='frm-envio-sc' action='./ajax/sc_pdf.php' method='post' enctype='multipart/form-data'>
		<input type='hidden' id="cad" name='cad'>
		<input type='hidden' id="info" name='info'>
	</form>

	<form id='frm-envio-si' action='./ajax/si_pdf.php' method='post' enctype='multipart/form-data'>
		<input type='hidden' id="cad" name='cad'>
		<input type='hidden' id="info" name='info'>
	</form>

<script language='javascript'>
				var hc=calcula_h(125);
   			   	var h=$(window).outerHeight() - hc;

   			 	$('#pie').css("top",h+10);

   		 		hc=calcula_h(85);
	   		 	h=$(window).outerHeight() - (hc*2);
		   		$('#contenedor').css("top",hc-10);
   			 	$('#contenedor').css("height",h);
   			 	hc=calcula_h(45);
   			 	$('#menu').css("top",hc);
   				var f=new Date();
   				var dia=(24*60*60*1000)* 7;
   				var fecha= new Date(f.getTime() - dia);
   				var fec=fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear();
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

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="https://www.sis-ca.com.mx:7863/";
  		  	});
  		  	
  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

			$("#contenido").css("left","16%");
			$("#contenido").css("width","85%");


			$("#cmbgerencia").autocomplete
			({
				source:'autocompleta.php?c=cmbgerencia',minLength:4,
				select: function( event, ui ) 
				{
					$("#cmbgerencia").val(ui.item.value);
	  		  		$("#contiene").val($('#frmenvio').attr("action"));
	  		  		$('#frmenvio').attr("action","conector.php");
	  		  		$('#frmenvio').submit();
	  		  	}
  		  	});

			$("#txtfolio").autocomplete
			({
				source:'autocompleta.php?c=txtfolio',minLength:4,
				select: function( event, ui ) 
				{
					$("#txtfolio").val(ui.item.value);
	  		  		$("#contiene").val($('#frmenvio').attr("action"));
	  		  		$('#frmenvio').attr("action","conector.php");
	  		  		$('#frmenvio').submit();
	  		  	}
  		  	});

			$("#cmbcurp").autocomplete
			({
				source:'autocompleta.php?c=cmbcurp',minLength:4,
				select: function( event, ui ) 
				{
					$("#cmbcurp").val(ui.item.value);
	  		  		$("#contiene").val($('#frmenvio').attr("action"));
	  		  		$('#frmenvio').attr("action","conector.php");
	  		  		('#frmenvio').submit();
	  		  	}
  		  	});

			$("#txtopor").autocomplete
			({
				source:'autocompleta.php?c=txtopor',minLength:4,
				select: function( event, ui ) 
				{
					$("#txtopor").val(ui.item.value);
	  		  		$("#contiene").val($('#frmenvio').attr("action"));
	  		  		$('#frmenvio').attr("action","conector.php");
	  		  		$('#frmenvio').submit();
	  		  	}
  		  	});

			$("#dfolio_av").autocomplete
			({
				source:'autocompleta.php?c=dfolio_av',minLength:4
  		  	});

			$(".tipo_fecha").datepicker(
			{
				minDate:'<?php echo $fechap?>',
				//minDate:'01/09/2013',
			});

			$("#cfec_traspaso").datepicker();

			$(".naps").prop("disabled",true);

			if($("#txt-nom-usuario").val()=="MELANIE ARLETT DURAN MALDONADO")
				$(".acompania").prop("disabled",false);
			else
				$(".acompania").prop("disabled",true);

			$("#folio").focus();
			
			$(".moneda").formatCurrency();

			$("#saldo, #csaldo").focusout(function()
			{
				$(this).formatCurrency();
			});

			$("#folio").focusout(function()
			{
				if($("#folio").val() != "")
				{
					if($("#folio").val().length > 9)
						$("#tipo_mov").val(1);
					else
					{
						if($("#folio").val().length < 9)
							$("#tipo_mov").val(2);
						else
							$("#tipo_mov").val(3);
					}
				}
			});

			$("#nss").focusout(function()
			{
				if($(this).val().length != 11 && $(this).val().length != 0)
				{
					alert("Error en el NSS");
					$(this).val("");
					$(this).focus();
				}
			});
			$(".detalle").mouseenter(function(evento)
			{
				if(colorToHex($(this).css("color"))=="#ff0000")
					$(this).css("color","#1d4384");
				else
					$(this).css("color","#ff0000");
			});

			$(".detalle").mouseleave(function(evento)
			{
				if(colorToHex($(this).css("color"))=="#ff0000")
					$(this).css("color","#1d4384");
				else
					$(this).css("color","#ff0000");
			});

			$("#empleado").autocomplete
			({
				source:'autocompleta.php?c=empleado',minLength:4,
				select: function( event, ui ) 
				{
					let id=this.id;
					var n='0';
					$("#" + this.id).val(ui.item.value);

				   	$.post("./ajax/obtener_dato.php",{ id: 'empl_nap', filtro1: ui.item.value, filtro2: $("#fecha_mov" + n).val() + "|" + $("#nap" + n).val() }, function(response)
				   	{
				   		var datos=response.split("|");
				   		let pasa=1;
				   		if(datos.length >=4)
				   		{
				   			if(datos[3]=="negativo")
				   			{
				   				let men="Este comisionista cuenta con insuficiencias de pago, es probable que ya no esté activo en Carobra. ¿Continuar de todos modos?";
				   				if(confirm(men)==false)
				   				{
				   					pasa=0;
				   					$("#" + id).val('');
				   				}
				   			}
				   		}
				   		if(pasa==1)
				   		{
					   		if(datos[0] !="")
					   		{
						   		var nap=datos[0];
						   		var nf=datos[1];
						   	}
						   	else
						   		var nap='';
					   		$("#nap" + n).prop("disabled",true);
					   		$("#monto_acomp" + n).prop("disabled",true);
//alert(nap + "|" + nf);
					   		if(nap !="")
					   		{
					   			if(n=='')
					   				$("#nap" + n).val(nap);
					   			$("#fol_propio" + n).val(1);
					   			if(nap != $("#nap" + n).val())
					   			{
					   				//if(datos[2]>20)
					   				if(datos[2]>200)
					   				{
					   					alert("Este NAP ha tenido 20 o más acompañamientos en el mes. No se habilitará el campo de acompañamiento");
					   					$("#monto_acomp" + n).val(0);
					   				}
					   				else
					   				{
						   				$("#monto_acomp" + n).prop("disabled",false);
						   				$("#monto_acomp" + n).focus();
						   			}
					   			}
					   			else
					   				$("#monto_acomp" + n).val(0);
					   		}
					   		else
					   		{
					   			$("#monto_acomp" + n).prop("disabled",false);
					   			$("#fol_propio" + n).val(0);
					   			if(n=='')
					   			{
					   				$("#nap" + n).prop("disabled",false);
					   				$("#nap" + n).focus();
					   			}
					   			else
					   				$("#monto_acomp" + n).focus();
					   		}
					   		if(nf==1)
					   		{
					   			$("#monto_acomp" + n).prop("disabled",false);
					   			if(n=='')
					   			{
					   				$("#nap" + n).prop("disabled",false);
					   				$("#nap" + n).focus();
					   			}
					   			else
									$("#monto_acomp" + n).focus();
					   		}
					   	}
					});
					return false;
				}
			});

			$('a').click(function(evento)
			{
				var p=0;
				if(this.id != "btn-guardar-new")
				{					
					var n=this.id.substring(7);
					if($("#empleado").val() != "")
					{
						$.get( "conector.php", { op: 10, ger: $("#cmbgerencia").val(), nombre: $("#empleado").val().replace(/ /gi, '_')}, function(response)
						{
							if(response=='0')
							{
								alert("Ejecutivo inexistente o no pertenece a esta gerencia")
								$("#empleado").val("");
								$("#empleado").focus();
							}
							else
							{
								$("#fdatos #dfolio").val($("#folio" + n).text());
								$("#dnss").val($("#nss" + n).text());
								$("#dempleado").val(response);
								$("#dtipo_mov").val($("#tipo_mov" + n).val());
								$("#dfec_mov").val($("#fecha_mov" + n).val());
								$("#dnap").val($("#nap" + n).val());
								$("#dafo_cedente").val($("#afo_cedente" + n).val());
								$("#dsaldo").val($("#saldo" + n).val());
								$("#dacompania").val($("#monto_acomp" + n).val());
								$("#hoja_cap").val($("#cad_hoja_cap").val());
								$("#fdatos #op").val(2);
			  			  		$("#fdatos #contiene").val($('#fdatos').attr("action"));
			  			  		$('#fdatos').attr("action","conector.php");
			  		  			$('#fdatos').submit();
							}
						});
					}
					else
						alert("No se ha ingresado el comisionista");
				}
			});
			
			$('#guardar_new').click(function(evento)
			{
				let envia=0;
				let extra=$("#estatus_av").val() + "|" + $("#detalle-nss").val() + "|" + $("#empleado0").val() + "|" + $("#dnombre").val();

				$("#nombre").val($("#dnombre").val());
				$("#empresa").val($("#dempresa").val());
				$("#telefono").val($("#dtelefono").val());
				$("#celular").val($("#dcelular").val());
				$("#observa").val($("#dobserva").text());
				$("#CURP").val($("#dCURP").val());
				$("#email_trab").val($("#demail_trab").val());
				$("#fdetalle #dfolio").val($("#txtfolio").val());
				$("#sal_mes").val($("#dsal_mes").val());
				$("#fec_sello").val($("#dfec_sello").val());
				$("#ancla").val($("#sel-ancla").val());
				$("#cp").val($("#dcp").val());
				$("#extra").val(extra);

				if($("#dfolio_av").val() != "")
				{
					if($("#av_validada").val()==1)
					{
						envia=1;
						$("#folio_av").val($("#dfolio_av").val());
					}
				}
				else
					envia=1;

				if(envia==1)
				{
					$("#fdetalle #op").val(3);
	  		  		$("#fdetalle #contiene").val("<?php echo encripta("cap_prod_ne.php"); ?>");
	  		  		$('#fdetalle').attr("action","conector.php");
	  		  		$('#fdetalle').submit();
	  		  	}
	  		  	else
	  		  		alert("La apovol ingresada no ha sido validada. La información no se guardará hasta que se valide o se elimine de la captura");
			});

			$('#cerrar').click(function(evento)
			{
				$("#detalle_produc").css("display","none");
			});

			$('.boton_tabla').click(function(evento)
			{
				var p=0;
				var n=this.id.substring(6);

				if($("#empleado").val() != "")
				{
					if($("#monto_acomp" + n).val()>0 || $("#fol_propio" + n).val()==1)
					{
						$.get( "conector.php", { op: 10, ger: $("#cmbgerencia").val(), nombre: $("#empleado").val().replace(/ /gi, '_')}, function(response)
						{
							if(response=='0')
							{
								alert("Ejecutivo inexistente o no pertenece a esta gerencia.......")
								$("#empleado" + n).val("");
								$("#empleado" + n).focus();
							}
							else
							{
								$("#dfolio").val($("#folio" + n).text());
								$("#dnss").val($("#nss" + n).text());
								$("#dempleado").val(response);
								$("#dtipo_mov").val($("#tipo_mov" + n).val());
								$("#dfec_mov").val($("#fecha_mov" + n).val());
								$("#dnap").val($("#nap" + n).val());
								$("#dafo_cedente").val($("#afo_cedente" + n).val());
								$("#dsaldo").val($("#saldo" + n).val());
								$("#dacompania").val($("#monto_acomp" + n).val());
								$("#fdatos #op").val(5);
			  			  		$("#fdatos #contiene").val($('#fdatos').attr("action"));
			  			  		$('#fdatos').attr("action","conector.php");
			  		  			$('#fdatos').submit();
							}
						});
					}
				}
				else
					alert("No se ha ingresado el comisionista");
			});
			
	  		$('#fecha_cap').change(function()
	  		{
	  			$("#cmbgerencia").val("");
	  			$("#cont_datos").css("display","none");
	  			$("#reg_alta").css("display","none");
	  		});


			$(".naps, #nap").autocomplete
			({
				source:'autocompleta.php?c=' + this.id,minLength:4,
				select: function( event, ui ) 
				{
					var n=this.id.replace('empleado', '');
					$("#" + this.id).val(ui.item.value);
				   	$.get("conector.php",{ op: 1, ger: $("#cmbgerencia").val(), nap: ui.item.value, fol: $("#folio").val(), fec_traspaso: $("#fecha_mov").val() }, function(response)
				   	{

						if(response.length >= 10)
						{
							alert(response);
							$("#nap").val("");
							$("#nap").focus();
						}
						else
						{
							if(response.length > 0)
							{
			   					alert("Este NAP ha tenido 20 o más acompañamientos en el mes. No se habilitará el campo de acompañamiento");
			   					$("#monto_acomp" + n).val(0);
							}
						}
					});
					return false;
				}
			});

	  		$("#nap").focusout(function()
	  		{
				if($("#nap").val().length>6)
				{
					$("#valida").load("conector.php?op=1&ger=" + $("#cmbgerencia").val() + "&nap=" + $("#nap").val() + "&fol=" + $("#folio").val(), function() 
					{
						if($("#valida").text().length > 0)
						{
							alert($("#valida").text());
							$("#nap").val("");
							$("#nap").focus();
						}
					});
				}
	  		});

			$(".fecha_movi").focus(function()
			{
				var i = this.id.replace("fecha_mov","");
				$("#afo_cedente" + i).focus();
			});

			$("#telefono, #celular").focusout(function()
			{
				if($(this).val() != '')
				{
					var tel=$(this).val();
					
					if(String(tel.substring(0, 3)).search(/^\d+$/) != -1)
					{
						tipo=this.id;
						$.ajax(
						{
							type: "POST",
							url: "./ajax/valida_tel.php",
							data: "folio=" + $.trim($("#sfolio").text()) + "&tel=" + tel + "&tipo=" + tipo,
							success: function(response)
							{
								if(response > 0)
								{
									alert("Este n\u00famero ya fue utilizado anteriormente en uno o m\u00e1s folios");
									$("#" + tipo).val('');
								}
							}
						});
						return false;
					}
				}
			});

			$("#cerrar-s, #cerrar, #cerrar-av").hover(function()
        	{
        		if($(this).prop("src").substr(-5,1)==1)
        			$(this).prop("src","./img/cerrar2.png");
        		else
        			$(this).prop("src","./img/cerrar1.png");
        	});

			$("#cerrar-s").click(function()
        	{
        		$("#div-captura").css("display","none");
        	});

			$("#cerrar-av").click(function()
        	{
        		$("#vis-av").css("display","none");
        	});

			$(".subir-pdf").hover(function()
			{
				let scr=$(this).prop("src").split("/");
				if(scr[scr.length-1].length > 7)
        			$(this).prop("src","./img/pdf.png");
        		else
        			$(this).prop("src","./img/pdf.png");
			});

			$("#cap-cero").click(function()
			{
				$("#div-captura").css("display","block");
			});

			$("#subir-hoja-captura").click(function(event)
			{
				event.preventDefault();
				$("#file-hoja-captura").trigger("click");
			});

			window.hojaCapturaTemporal = null;

			function aplicaHojaCapturaBasica(info)
			{
				if(info.asesor!="")
					$("#empleado, #empleado0").val(info.asesor);
				$("#monto_acomp0").val(info.acompa);
				$("#afo_cedente0").val(info.afore);
				$("#cad_hoja_cap").val(info.empresa + "|" + info.email + "|" + info.cel + "|" + info.cp + "|" + info.apovol);
			}

			$("#file-hoja-captura").change(function()
			{
				let archivo = this.files[0];
				if(!archivo)
					return;

				let datos = new FormData();
				datos.append("op","hoja_captura");
				datos.append("txtfolio", $("#txtfolio").val());
				datos.append("file-hoja-captura", archivo);

				$.ajax({
					url: "./interfaces2.php",
					type: "POST",
					data: datos,
					processData: false,
					contentType: false,
					success: function(response)
					{
						if(typeof response=="string")
						{
							try
							{
								response = $.parseJSON($.trim(response));
							}
							catch(error)
							{
								alert("No se pudo procesar la hoja de captura\n" + $.trim(response));
								$("#file-hoja-captura").val("");
								return;
							}
						}

						if(!response.ok)
						{
							alert(response.mensaje);
							$("#file-hoja-captura").val("");
							return;
						}

						let info = response.datos;
						window.hojaCapturaTemporal = info;
						aplicaHojaCapturaBasica(info);
						$("#file-hoja-captura").val("");
					},
					error: function(xhr)
					{
						let mensaje = "No se pudo procesar la hoja de captura";
						if(xhr.responseText)
							mensaje += "\n" + $.trim(xhr.responseText);
						alert(mensaje);
						$("#file-hoja-captura").val("");
					}
				});
			});

			$("#btn-guardar-new").click(function()
			{
				let paso=1;
				if($("#cfolio").val()!="")
				{
					if($("#cnombre").val()!="")
					{
						if($("#cnss").val()!="")
						{
							if($("#cnss").val().length!=11)
							{
								paso=0;
								alert("El NSS debe tener 11 caracteres capturados");
								$("#cnss").focus();
							}
						}
						if(paso==1)
						{
							if($("#ccurp").val()!="")
							{
								if($("#ccurp").val().length!=18)
								{
									paso=0;
									alert("El CURP debe tener 18 caracteres capturados");
									$("#ccurp").focus();
								}
								if(paso==1)
								{
									if($("#cmov").val()!=0)
									{
										if($("#cfec_traspaso").val()!="")
										{
											if($("#cnap").val()!="")
											{
												if($("#cnap").val().length!=10)
												{
													paso=0;
													alert("El NAP debe tener 10 caracteres capturados");
													$("#cnap").focus();
												}
												if(paso==1)
												{
													if($("#csaldo").val()!="")
													{
														if($("#cestatus").val()!=0)
														{
															$.ajax(
															{
																type: "POST",
																url: "./ajax/facilitador.php",
																data: $("#frm-cap-new").serialize() + "&op=25",
																success: function(response)
																{
																	alert(response);
																}
															});
															return false;
														}
														else
														{
															alert("La captura del estatus es obligatoria");
															$("#cestatus").focus();
														}

													}
													else
													{
														alert("La captura del saldo es obligatoria");
														$("#csaldo").focus();
													}
												}
											}
											else
											{
												alert("La captura del NAP es obligatoria");
												$("#cnap").focus();
											}
										}
										else
										{
											alert("La captura de la fecha del traspaso es obligatoria");
											$("#cfec_traspaso").focus();
										}
									}
									else
									{
										alert("La captura del tipo de movimiento es obligatoria");
										$("#cmov").focus();
									}
								}
							}
							else
							{
								alert("La captura del CURP es obligatoria");
								$("#ccurp").focus();
							}							
						}
					}
					else
					{
						alert("La captura del nombre del cliente es obligatoria");
						$("#cnombre").focus();
					}
				}
				else
				{
					alert("La captura del folio es obligatoria");
					$("#cfolio").focus();
				}
			});

			$("#inter-pdf").hover(function()
			{
				let f=$(this).attr("src").split("/");
				if(f[f.length-1]=="pdf.png")
					$(this).attr("src",$(this).attr("src").replace("pdf.png","pdf_in.png"));
				else
					$(this).attr("src",$(this).attr("src").replace("pdf_in.png","pdf.png"));
			});

			$("#valida_AV").click(function()
			{
				if($("#dfolio_av").val() != "")
				{
					$.post("./ajax/facilitador.php",{ op: 39, folio_av: $("#dfolio_av").val()  }, function(response)
					{
						if(response != "")
						{
							let d=response.split("|");
							$("#folio-av").text(d[0]);
							$("#tramite-av").text(d[1]);
							$("#cliente-av").text(d[2]);
							$("#nss-av").text(d[3]);
							$("#curp-av").text(d[4]);
							$("#nap-av").text(d[5]);
							$("#vis-av").css("display","inline-block");
						}
						else
							alert("No se encontró información del folio AV capturado");
					});
				}
				else
					alert("No se ha seleccionado la AV");
			});
			
			$("#btn-cancela-av").click(function()
			{
				$("#folio-av").text("");
				$("#tramite-av").text("");
				$("#cliente-av").text("");
				$("#nss-av").text("");
				$("#curp-av").text("");
				$("#nap-av").text("");
				$("#vis-av").css("display","none");
				$("#dfolio_av").val("");
			});

			$("#btn-valida-av").click(function()
			{
				if(confirm("¿La información del cliente concuerda en la afore y en la Apovol?"))
				{
					$("#av_validada").val(1);
					$("#folio-av").text("");
					$("#tramite-av").text("");
					$("#cliente-av").text("");
					$("#nss-av").text("");
					$("#curp-av").text("");
					$("#nap-av").text("");
					$("#vis-av").css("display","none");
				}
				else
				{
					$("#folio-av").text("");
					$("#tramite-av").text("");
					$("#cliente-av").text("");
					$("#nss-av").text("");
					$("#curp-av").text("");
					$("#nap-av").text("");
					$("#vis-av").css("display","none");
					$("#dfolio_av").val("");				
				}
			});

  		  	$("input[type=file]").change(function()
  		  	{
  		  		if(this.id=="file-hoja-captura")
  		  			return true;

  		  		let cad="";
  		  		let info="";
  		  		var archivo = $(this).prop("files")[0];
  		  		var e_arc=$(this).val().split(".");
	  			if(e_arc[1].toUpperCase()=="PDF")
	  			{
  		  			info="0|" + $("#txtfolio").val() + "|" + $("#detalle-nss").val() + "|" + $("#dCURP").val();
  		  			cad=$("#empleado0").val() + "|" + $("#tipo_mov0").val() + "|" + $("#sc-fec_mov").val() + "|" + $("#sc-nap").val() + "|" + $("#afo_cedente0").val() + "|" + $("#sc-saldo").val() + "|" + $("#monto_acomp0").val() + "|";
	  		  		if(this.id=="sc-pdf")
	  		  		{
	  		  			$("#frm-envio-sc #info").val(info);
	  		  			$("#frm-envio-sc #cad").val(cad);
  		  				$(this).appendTo("#frm-envio-sc");
  		  				$("#frm-envio-sc").submit();
	  		  		}
	  		  		else
	  		  		{
	  		  			$("#frm-envio-si #info").val(info);
	  		  			$("#frm-envio-si #cad").val(cad);
  		  				$(this).appendTo("#frm-envio-si");
  		  				$("#frm-envio-si").submit();
	  		  		}
	  			}
	  			else
	  				alert("El archivo que quieres subir no es un archivo PDF");
  		  	});

  		  	$("#cfolio").focusout(function()
  		  	{
  		  		if($("#cfolio").val() != "")
					$.ajax(
					{
						type: "POST",
						url: "./ajax/facilitador.php",
						data: "folio=" + $("#cfolio").val() + "&tipo=ne&op=56",
						success: function(response)
						{
							d=response.split("|");
							$("#cfec_traspaso").val(d[0]);
							$("#cmov").val(d[1]);
							$("#cestatus").val(d[2]);
							$("#csaldo").val(d[3]);
							$("#csaldo").formatCurrency();
						}
					});
					return false;
  		  	});
		});
	</script>
</html>
