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
		<style>			
			#vis-info	{
							position: absolute; 
							top: 0px;
							width: 650px; 
							height: 380px; 
							font-size: 11px;
							margin-left: 130px; 
							margin-top: 0px; 
							border: 1px solid #A7A5A5; 
							border-radius: 20px; 
							background: white; 
							display: none;
							z-index: 10;
						}
			/* Estilo base para el fondo de ambos modales */
			.my-modal 
			{
				display: none;
				position: fixed;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				overflow: auto;
				background-color: rgba(0, 0, 0, 0.4);
				z-index: 1000;
			}

			#myModal 
			{
				padding-top: 50px;
			}

			#myModal .my-modal-content 
			{
				background-color: #fefefe;
				margin: auto;
				padding: 20px;
				border: 1px solid #888;
				width: 90%;
				max-width: 900px;
				box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
				position: relative;
				box-sizing: border-box;
			}

			#myModalDetalle 
			{
				padding-top: 50px;
				z-index: 1010;
			}

			#myModalDetalle .my-modal-content 
			{
				background-color: #fff;
				margin: auto;
				padding: 20px;
				border: 1px solid #aaa;
				width: 100%;
				max-width: 1200px;
				box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
				position: relative;
				box-sizing: border-box;
			}

			.my-close 
			{
				color: #aaaaaa;
				float: right;
				font-size: 28px;
				font-weight: bold;
				cursor: pointer;
			}

			.my-close:hover,
			.my-close:focus {
				color: #000;
				text-decoration: none;
			}

			.my-close-dte 
			{
				color: #aaaaaa;
				float: right;
				font-size: 28px;
				font-weight: bold;
				cursor: pointer;
			}

			.my-close-dte:hover,
			.my-close-dte:focus {
				color: #000;
				text-decoration: none;
			}

			.my-modal-title 
			{
				text-align: left;
				margin: 0 0 10px 0;
				font-size: 24px;
			}

			.my-modal-content p 
			{
				margin: 0;
				font-size: 16px;
			}


			.tabla-fechas 
			{
				width: 60%;
				border-collapse: collapse;
				margin: 0 auto;
    			font-size: 0.9em;
        	}

			.tabla-fechas th, .tabla-fechas td 
			{
				border: 1px solid #ddd; 
				padding: 8px;
				text-align: left;
			}

			.tabla-fechas th 
			{
				background-color: #f2f2f2; 
				font-weight: bold;
			}

			.tabla-fechas tr:nth-child(even) 
			{
				background-color: #f9f9f9;
			}
		</style>
	</head>
	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"]; 
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg)); 
//$periodo=700;
     	if($periodo > 600)
     	{
      		session_destroy();
      		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","index.php");	
/*
      		if($_SESSION["nombre"] =="Administrador del Sistema")
      			$_SESSION["autentifica"] = "p";
      		else
      		{
      			mensaje("Consulta temporalmente inhabilitada","contiene.php");
      		}
*/
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
			//reg_acceso($_SESSION["nombre"],"consul_pago");
//---------------------------------------------------------------------------Recepción de parametros
			$_SESSION["autentifica"]="";
			$fechas_origen = array();
			if ($_SERVER['REQUEST_METHOD'] === 'POST') 
			{
				$clave=recibe_POST("clave");
				$fecha=recibe_POST("fecha");
				$op=recibe_POST("op");
			} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') 
			{
				$clave = recibe_GET("clave");
				$fecha = recibe_GET("fec_detalle");
				$op = recibe_GET("op");
				$origen = recibe_GET("origen");
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

//$origen = "ORIGEN";

		if ($origen !== "ORIGEN") 
		{
			$divcabecera->crear();
				$imgcabecera->crear();
				$imgcabecera->css("width","100%");
				titulo("Detalle de pagos");
				$pag="REC_HUMANOS";
				menu();
			$divcabecera->close();
		}
			
		if ($origen !== "ORIGEN")
		{	
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
<?php
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: " . $_SESSION["nombre"] . "</spam>";
					echo "<div style='width:100%; text-align:right; margin-top:-15px;'>";
						echo "<spam id='email'>Email</spam>";
						echo "&nbsp;&nbsp;&nbsp;<spam id='salir'>Salir</spam>";
					echo "</div>";
				$divusuario->close();
			$divpie->close();
		}
			$divcontenedor->crear();
			if ($origen !== "ORIGEN") 
			{
				$divopcion->crear();
					$divopcion->css("background","#FFFFFF");
				$divopcion->close();
			}

				$divcontenido->crear();
//----------------------------------------------------------------------------Declaración de objetos
					$divcontenido->css("overflow-y","auto");
					$conecta= new conector();

					if(bloqueo_cierre()=="Si")
						mensaje("Consulta bloqueada temporalmente por el proceso de cierre de pagos","contiene.php");
					$seldato=new select("fecha");
					$tdatos= new table('','','100%');
					$textdato=new textbox("txtusuario");
					$divdetalle=new div("detalle_produc");
					$fenvio= new form("fenvio","consul_pago.php");
					$imgexcel=new img("imgexcel","./img/excel.png");
?>
					<div id='vis-info'></div>
<?php
//--------------------------------------------------------------------------------------------------
					if(strlen($op)==0)
					{
						//echo "<br>";
						$fenvio->crear();
							tex_h("contiene");
							tex_h("op",1);
							$tdatos->crear();
								$tdatos->tr();
									$tdatos->td('Comisionista:','7%','','',1,1,'right');
									$tdatos->td($textdato,"35%");
										$textdato->css("width","100%");
										$textdato->css("border-color","#A8B6C4");
										$textdato->css("border-style","solid");
										$textdato->css("border-width","2px");
										$fil=  "Select u.clave from usuarios u inner join empleados e 
													on (u.clave=e.clave) where nombre='";
										$fil.= utf8_decode($_SESSION["nombre"]) . "'";
										$tb=$conecta->consulta($fil);
										$row = $tb->fetch_array(MYSQLI_NUM);
										$clave=$row[0];									
										$q13="";
										if($clave=='NOVGAVI')
										{
											$clave='FARAMIREZC';
											$nivel=13;
											$q13=" where u.nombre !='FERNANDO ARISTEO RAMIREZ CABRERA'";
										}
										else
										{
											if($clave=='SEBASMARMA')
											{
												$clave='FLOMAMALA';
												$nivel=13;
												$q13=" where u.nombre !='FLOR MARIA MARTINEZ LARA'";
											}
											else
											{
												if($clave=='DIPEREZ' or $clave=='LIDJARECIA' or $clave=='TAEST')
													$nivel=12;
												else
												{
													if($clave=="CAROBRA")
														$nivel=3;
													else
														$nivel=nivel_usuario("",$clave);
												}
											}
										}
										if($nivel==12 or $nivel==2)
										{
/*
											if($clave=='DIPEREZ')
												$query="Select nombre 
														from (Select distinct clave from usuarios_nivel_gerencia
															  where id_gerencia != 124) ung 
															inner join usuarios u on (ung.clave=u.clave)
														where nombre like '%$%'";
											else
*/
												$query="Select nombre from usuarios where nombre like '%$%'";
											$_SESSION["txtusuario"]=$query;
										}
										else 
										{
											if($nivel==5)
											{
												$fil="Select distinct nombre 
													  from (Select distinct id_gerencia 
													  		from usuarios_nivel_gerencia where clave='$clave') ug
														inner join (Select distinct id_gerencia,clave 
																	from usuarios_nivel_gerencia) ug2 
																on (ug.id_gerencia=ug2.id_gerencia)
														inner join usuarios u on (ug2.clave=u.clave) 
													  where nombre like '%$%'";  
												$_SESSION["txtusuario"]=$fil;
											}
											else 
											{
												if($nivel==4)
												{
													$fil="Select distinct nombre from usuarios u 
															inner join usuarios_gerencia ug 
																	on (u.clave=ug.clave) 
															inner join (Select id_gerencia from usuarios_gerencia 
																		where clave='$clave') ug2 on (ug.id_gerencia=ug2.id_gerencia) 
															inner join (Select clave from usuarios_nivel 
																		where id_nivel=3 or id_nivel=7 or id_nivel=4) uni 
																	on (u.clave=uni.clave) 
														  where u.clave not in (select clave from usuarios_nivel_gerencia 
														  						where id_nivel=5) 
														  	and nombre like '%$%'";
													$_SESSION["txtusuario"]=$fil;				
												}
												else 
												{
													if($nivel==1 or $nivel==6)
														$_SESSION["txtusuario"]="Select nombre from usuarios 
																				 where nombre like '%$%' 
																				 	and clave='$clave'";
													else
													{
														$fil="Select distinct nombre 
															  from (select distinct id_gerencia from usuarios_nivel_gerencia
															  		where id_nivel=13 and clave='$clave') sc
															  	inner join (Select distinct clave, id_gerencia 
															  				from usuarios_nivel_gerencia) cu 
															  			on (sc.id_gerencia=cu.id_gerencia)
															  	inner join (Select clave,nombre from usuarios 
															  				where nombre like '%$%') u 
															  			on (cu.clave=u.clave)" . $q13;
														$_SESSION["txtusuario"]=$fil;
													}
												}
											}
										}
										if($clave=='LIDJARECIA')
										{
											$query="Select distinct nombre
													from (Select distinct no_empleado 
														  from usuarios_nivel_gerencia 
														  where id_gerencia in (493,594) 
														  	and pertenece=1) ung
														inner join (Select no_empleado,nombre 
																	from empl_usuarios
																	where nombre like '%$%') eu 
																on (ung.no_empleado=eu.no_empleado)";
											$_SESSION["txtusuario"]=$query;
										}
									$tdatos->td('','58%');
								$tdatos->close_tr();
							$tdatos->close();
						$fenvio->close();
						if($nivel==3 or $nivel==7)
						{
?>
<script type="text/javascript">
							$("#op").val(1);
							$("#txtusuario").val("<?php echo $_SESSION["nombre"]; ?>");
					  		$("#contiene").val($('#fenvio').attr("action"));
		  		  			$('#fenvio').attr("action","conector.php");
		 			  		$('#fenvio').submit();
</script>
<?php
						}
					}
					else 
					{
						if($op>=1)
						{
if($clave=="CAROBRA")
{
	$nivel=3;
	$clave="MAMAYSANCO";
	$usuario="Usuario de prueba";
}
							$anio=date("Y")-1;
							if ($origen !== "ORIGEN") 
							{
								$clave=desencripta($clave);
							}
							$fil="Select distinct fec_pago,fec_pago as fp from fec_pago
								  order by  str_to_date(fec_pago,'%d/%m/%Y') desc";
							$seldato->query($fil);
							$tb=$conecta->consulta("Select nombre from usuarios where clave='$clave'");
							$row = $tb->fetch_array(MYSQLI_NUM);

						if ($origen !== "ORIGEN") 
						{
							$html="<select id='fecha' name='fecha' style='width: 100%;'>";
							$html.="<option value=''></option>";
							$bd=$conecta->consulta($fil);
							while($tb=mysqli_fetch_array($bd))
							{
								$html.="<option value='" . $tb["fec_pago"] . "'>" . $tb["fec_pago"] . "</option>";
							}
							$html.="</select>";
						}
							$fenvio->crear();
								tex_h("contiene");
								tex_h("txtusuario",encripta($clave));
								tex_h("op",2);
								$tdatos->crear();
/*
									$tdatos->tr();
										$tdatos->td();
										$tdatos->td();
										$tdatos->td("Detalle de pagos de ",'','','',1,1,'right');
										$tdatos->td('<H3>' . $row[0] . '</H3>','','','',1,1,'left');
									$tdatos->close_tr();
*/									if ($origen !== "ORIGEN")
									{
										$tdatos->tr();
											$tdatos->td('Fecha de pago:','15%','','',1,1,'right');
											$tdatos->td($html,'12%');
	/*
											$tdatos->td($seldato,"12%");
												$seldato->css("width","100%");
												$seldato->css("border-color","#A8B6C4");
												$seldato->css("border-style","solid");
												$seldato->css("border-width","2px");
	*/
											$tdatos->td('','20%');
											$tdatos->td('','53%');
										$tdatos->close_tr();
									}
								$tdatos->close();
							$fenvio->close();
/*
						}
						else 
						{
							$fenvio->crear();
								tex_h("op",3);
								tex_h("contiene");
								tex_h("txtusuario",$clave);
							$fenvio->close();
*/
							$fil=  "Select distinct fec_pago,fec_pago from pagos where datediff(concat(substr(fec_pago,-4),'-',substr(fec_pago,";
		   					$fil.= "4,2),'-',substr(fec_pago,1,2)),'2019-04-15')>=0 order by  str_to_date(fec_pago,'%d/%m/%Y') desc limit 0,25";
							//$seldato->query($fil);
							$tb=$conecta->consulta("Select nombre from usuarios where clave='$clave'");
							$row = $tb->fetch_array(MYSQLI_NUM);
							$usuario=$row[0];
$prueba=0;
if($clave=="CAROBRA")
{
	$nivel=3;
	$prueba=1;
	$clave="MAMAYSANCO";
	$usuario="Usuario de prueba";
}
?>
<?php
							if($fecha != "" and empty($fechas_origen))
							{
								$f=explode("/",$fecha);
								$fec_pago_u=$f[2] . "-" . $f[1] . "-" . $f[0];
								$anio=$f[2];

								$bd=$conecta->consulta("Select datediff('$fec_pago_u','2023-08-22') as dif");
								$tb=mysqli_fetch_array($bd);
								$p=$tb["dif"];

								$bd=$conecta->consulta("Select datediff('$fec_pago_u','2022-04-19') as dif");

								$tb=mysqli_fetch_array($bd);
								$d=$tb["dif"];

								$bd=$conecta->consulta("Select datediff('$fec_pago_u','2026-08-18') as dif");

								$tb=mysqli_fetch_array($bd);
								$p_esp=$tb["dif"];								

//---------------------------------------------------------------------------------------------- Anticipos
								$query="Select * from carobra.acum_pagos_anticipos_$anio 
										where clave='$clave' and fec_pago='$fecha'";

								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									
									$nuevo_anticipo=$tb["anticipo"];
									$nuevo_incentivo=$tb["incentivo"];	
									$seg_anticipo=$tb["segundo_anticipo"];	
									$pago_anticipo=$tb["desc_anticipo"] * -1;
									$pago_incentivo=$tb["desc_incentivo"] * -1;		
									$desc_seg_anticipo=$tb["desc_segundo_anticipo"] * -1;
									$desc_ant_30=0;
									$com_especial=$tb["com_especial"];
								}
								else
								{
									$nuevo_anticipo=0;			$nuevo_incentivo=0;			$seg_anticipo=0;			
									$desc_ant_30=0;				$pago_anticipo=0;			$pago_incentivo=0;
									$desc_seg_anticipo=0;		$com_especial=0;
								}
//--------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------- Comisiones
								$query="Select * from carobra.acum_pagos_comisiones_$anio 
										where clave='$clave' and fec_pago='$fecha'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									$comision_asesor=$tb["com_asesor"];			
									$comision_incentivo=$tb["com_incentivo"];
									$comision_gerente=$tb["com_gerente"];			
									$comision_brocker=$tb["com_sb"];
									$com_socio=$tb["com_sc"];						
									$pago_bono_mtto=$tb["com_asesor_mtto"];
									$desc_bono_mtto=$tb["desc_com_asesor_mtto"];	
									$pago_bono_mttob=$tb["com_sb_mtto"];
									$desc_bono_mttob=$tb["desc_com_sb_mtto"];		
									$pago_bono_mttosc=$tb["com_cs_mtto"];
									//$com_reg_asesor=$tb["com_reg_asesor"];			
									//$com_reg_ger=$tb["com_reg_ger"];
									//$com_reg_sb=$tb["com_reg_sb"];					
									//$com_reg_sc=$tb["com_reg_sc"];
									$comision_ae=$tb["comision_ae"];				
									$comision_ge=$tb["comision_ge"];
									$comision_be=$tb["comision_be"];
									if($p < 0)
									{
										$comision_asesor=$comision_asesor - $comision_ae + 0.001; 
										$comision_gerente=$comision_gerente - $comision_ge + 0.001;
										$comision_brocker=$comision_brocker - $comision_be + 0.001;
									}
								}
								else
								{
									$comision_asesor=0;			$comision_incentivo=0;		
									$comision_gerente=0;		$comision_brocker=0;		
									$com_socio=0;				$pago_bono_mtto=0;
									$desc_bono_mtto=0;			$pago_bono_mttob=0;			
									$desc_bono_mttob=0;			$pago_bono_mttosc=0;		
									$com_reg_ger=0;				//$com_reg_asesor=0;
									$com_reg_sb=0;				$com_reg_sc=0;				
									$comision_ae=0;				$comision_ae=0;				
									$comision_ae=0;
								}
//--------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------- Facturación
								$query="Select * from carobra.acum_pagos_oper_fac_$anio where clave='$clave' and fec_pago='$fecha'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									
									$o_acompa=$tb["acompaniamiento"];				$o_acompa_desc=$tb["desc_acompaniamiento"] * -1;
									$desc_com_factura=$tb["desc_com_factura"];		$desc_ajuste_fac=$tb["ajuste_com_factura"];
								}
								else
								{
									$o_acompa=0;				$o_acompa_desc=0;			$desc_com_factura=0;
									$desc_ajuste_fac=0;
								}
//--------------------------------------------------------------------------------------------------------								
//-------------------------------------------------------------------------------------------------- Otros

								$query="Select * from carobra.acum_pagos_otros_$anio 
										where clave='$clave' and fec_pago='$fecha'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$ajustes=$tb["ajuste"];							$gasto=$tb["gasto"];
									$apovol_as=0;									$apovol_ger=$tb["com_apovol_gerente"];
									$acum_as=$tb["acum_mensual_asesor"];			$acum_ge=$tb["acum_mensual_gerente"];
									$acum_sb=$tb["acum_mensual_sb"];
								}
								else
								{
									$ajustes=0;					$gasto=0;					$apovol_as=0;
									$apovol_ger=0;				$acum_as=0;					$acum_ge=0;
									$acum_sb=0;
								}

								$pasa_ajuste=0;
								if($ajustes==0)
								{
									$sql="Select 1 from ajustes_previsional 
										  where fec_pago='$fecha' and clave='$clave'
									  	  union all
									  	  select 1 from insuficiencias 
									  	  where  fec_pago='$fecha' and clave='$clave'
									  	  union all
									  	  select 1 from ajustes_tarjeta 
									  	  where  fec_pago='$fecha' and clave='$clave'
									  	  union all
									  	  select 1 from ajustes_corporativo 
									  	  where  fec_pago='$fecha' and clave='$clave'";
									$bd=$conecta->consulta($sql);
									if ($bd->num_rows != 0)
										$pasa_ajuste=1;
								}
								else
									$pasa_ajuste=1;







								$query="Select * from carobra.acum_pagos_registros_$anio 
										where clave='$clave' and fec_pago='$fecha'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									$com_reg_am=$tb["com_asesor"] + $tb["com_ger"] + $tb["com_sb"] + $tb["com_sc"] + $tb["com_lider_proy"];	
								}
								else
									$com_reg_am=0;

//--------------------------------------------------------------------------------------------------------	
								$query="Select sum(monto) as pagos_lib
								  		from pagos_retenidos where clave='$clave' and fec_pago='$fecha' and origen='C'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$pagos_lib=$tb["pagos_lib"];
								}
								else
									$pagos_lib=0;
//--------------------------------------------------------------------------------------------------------

								$query="Select sum(comision) as descuento
										from (Select ifnull(sum(com_asesor),0) as comision 
											  from carobra_pagos.desc_incumplimiento
											  where clave_asesor='$clave' and fec_descuento='$fecha'
											  union all
											  Select ifnull(sum(com_gerente),0) as comision 
											  from carobra_pagos.desc_incumplimiento 
											  where clave_ger='$clave' and fec_descuento='$fecha'
											  union all
											  Select ifnull(sum(com_sb),0) as comision 
											  from carobra_pagos.desc_incumplimiento 
											  where clave_sb='$clave' and fec_descuento='$fecha'
											  union all
											  Select ifnull(sum(com_sc),0) as comision 
											  from carobra_pagos.desc_incumplimiento 
											  where clave_sc='$clave' and fec_descuento='$fecha') d";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$desc_incumpl=$tb["descuento"];
								}
								else
									$desc_incumpl=0;

								$query="Select sum(ajuste) as pago_compl from carobra.acum_pagos_bonos_compl 
										where clave='$clave' and fec_pago='$fecha'";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$bono_compl=$tb["pago_compl"];
								}
								else
									$bono_compl=0;
//--------------------------------------------------------------------------------------------------------
						  		$query="Select sum(bono) as compl_30 
										from (Select distinct clave,folio,bono from carobra.complemento_av
											  where fec_pago='$fecha' and clave='$clave') d
										group by clave";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$compl_30=$tb["compl_30"];
								}
								else
									$compl_30=0;

								$sql="Select clave,sum(ajuste) as monto 
									  from carobra_pagos.com_especial_certificado
									  where fec_pago='$fecha' and clave='$clave'
									  group by clave";

								$bd=$conecta->consulta($sql);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);

									$com_esp_cert=$tb["monto"];
								}
								else
									$com_esp_cert=0;

//--------------------------------------------------------------------------------------------------------
								$rec_asesor=0;					$rec_gerente=0;	

								$query="Select id_nivel from carobra.pagos_niveles_$anio 
										where clave='$clave' and fec_pago='$fecha'";

								$bd=$conecta->consulta($query);
								$tb = $bd->fetch_array(MYSQLI_NUM);
								$id_nivel=$tb[0];

							    if($id_nivel==3 or $id_nivel==7)
							    {
									if($p < 0)
									{
								   		$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$comision_asesor+$comision_incentivo+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$pago_bono_mtto + $acum_as+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$compl_30 + $com_reg_asesor+$comision_ae + $com_reg_am + $com_especial + $bono_compl + $desc_incumpl + $com_esp_cert;
								   		$comision_asesor=$comision_asesor + $comision_ae;
								   	}
								   	else
								   		$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$comision_asesor+$comision_incentivo+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$pago_bono_mtto + $acum_as+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$compl_30 + $com_reg_asesor + $com_reg_am + $com_especial + $bono_compl + $desc_incumpl + $com_esp_cert;
							   		$comision_gerente=$comision_brocker=0;
							    }
							   	else
							   	{
								   	if($id_nivel==4)
								   	{
										if($p < 0)
										{
								   			$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$comision_gerente+$rec_gerente+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$apovol_ger+$pago_bono_mtto+$acum_as+$acum_ge+$comision_asesor+$comision_incentivo+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$com_reg_ger + $com_reg_asesor+$comision_ae+$comision_ge + $com_reg_am + $com_especial + $bono_compl + $desc_incumpl + $com_esp_cert;
								   			$comision_asesor=$comision_asesor + $comision_ae;
								   		}
								   		else 
								   			$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$comision_gerente+$rec_gerente+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$apovol_ger+$pago_bono_mtto+$acum_as+$acum_ge+$comision_asesor+$comision_incentivo+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$com_reg_ger + $com_reg_asesor + $com_reg_am + $com_especial + $bono_compl + $desc_incumpl + $com_esp_cert;
								   	}
								   	else
								   	{
									   	if($id_nivel==5)
									   	{
									   			$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$gasto+$o_acompa_desc+$o_acompa-$comision_asesor+$apovol_as+$apovol_ger+$pago_bono_mttob-$desc_bono_mtto+$comision_incentivo-$acum_ge-$comision_gerente+$acum_sb+$comision_brocker+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$com_reg_sb + $com_reg_asesor+$comision_be + $com_reg_am + $com_especial+$pago_bono_mtto+$bono_compl + $desc_incumpl + $com_esp_cert;

									   			$comision_asesor=$comision_asesor * -1;
										   		$comision_gerente=$comision_gerente * -1;
									   	}
									   	else
									   	{
										   	if($id_nivel==13)
										   	{
									   			$comision_asesor=$comision_asesor * -1;
										   		$comision_gerente=$comision_gerente * -1;
										   		$comision_brocker=($comision_brocker * -1)  + $com_reg_sb;
										   		$desc_bono_mtto=$desc_bono_mtto * -1;
										   		$desc_bono_mttob=$desc_bono_mttob * -1;
												if($p < 0)
												{
											   		if($comision_brocker==0)
											   		{
											   			$com_as=0;
											   			$com_sb=$comision_asesor + $comision_gerente;
											   		}
											   		else
											   		{
											   			$com_as=$comision_asesor;
											   			$com_sb=$comision_brocker;
											   		}
													$fec=str_replace("/","-",$fecha);
													$fec_pago = strtotime($fec . " 00:00:00");
													$fec_tope = strtotime("04-02-2021 00:00:00");

													if($fec_pago >= $fec_tope)
														if($comision_brocker!=0)
															$com_ger=$comision_gerente;
														else
															$com_ger=0;
													else
														$com_ger=0;
											   		$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$apovol_ger+$desc_bono_mttob+$desc_bono_mtto+$pago_bono_mttosc+$comision_incentivo-$acum_sb+$com_as+$com_sb + $com_socio+$desc_com_factura+$desc_ajuste_fac+$com_ger+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$com_reg_sc + $com_reg_asesor + $com_reg_am + $com_especial+$pago_bono_mtto + $bono_compl + $desc_incumpl + $com_esp_cert;
											   	}
											   	else
											   		$total=$nuevo_anticipo+$nuevo_incentivo+$pago_anticipo+$pago_incentivo+$rec_asesor+$ajustes+$gasto+$o_acompa_desc+$o_acompa+$apovol_as+$apovol_ger+$desc_bono_mttob+$desc_bono_mtto+$pago_bono_mttosc+$comision_incentivo-$acum_sb+$comision_asesor+$comision_brocker + $com_socio+$desc_com_factura+$desc_ajuste_fac+$comision_gerente+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo+$com_reg_sc + $com_reg_asesor + $com_reg_sb + $com_reg_am + $com_especial+$pago_bono_mtto + $bono_compl + $desc_incumpl + $com_esp_cert;
										   	}
										   	else
								   				$total=$nuevo_anticipo+$nuevo_incentivo+$ajustes+$gasto+$o_acompa_desc+$o_acompa+$acum_as+$pago_bono_mtto+$rec_asesor+$rec_gerente+$acum_ge+$desc_com_factura+$desc_ajuste_fac+$seg_anticipo+$desc_ant_30+$desc_seg_anticipo + $com_reg_asesor + $com_reg_am + $com_especial + $bono_compl + $desc_incumpl + $com_esp_cert;
										}
									}
								}

								$clase='td_gris';    							     
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
										$tdatos->td('','10%');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->td('','','','',1,3);
										$tdatos->td("Detalle de pagos del $fecha de",'','','',1,3,'right');
										$tdatos->td('<p>' . $usuario . '</p>','','','',1,4,'left');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->td();
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th('CONCEPTO','','t_gris',1,5,'left');
										$tdatos->th('MONTOS','','t_gris',1,1,'left');
										$tdatos->td('','','','',1,3);
									$tdatos->close_tr();
									$clase="td_blanco";


									$origen = "ORIGEN";

									if($nuevo_anticipo19 != 0 or $nuevo_anticipo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('NUEVOS ANTICIPOS','',$clase,1,5,'right');
											$tdatos->td($nuevo_anticipo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='na' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='na' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}											
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($nuevo_incentivo19 != 0 or $nuevo_incentivo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th("ANTICIPO BONO",'',$clase,1,5,'right');
											$tdatos->td($nuevo_incentivo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='nb' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											}
											else
											{
												$tdatos->td("<a id='nb' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($pago_anticipo19 != 0 or $pago_anticipo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('DESCUENTOS DE ANTICIPOS OTORGADOS','',$clase,1,5,'right');
											$tdatos->td($pago_anticipo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='dao' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='dao' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
									if($pago_incentivo19 != 0 or $pago_incentivo != 0)
									{
										$tdatos->tr();
										   $tdatos->td('','','','',1,2);
										   $tdatos->th("DESCUENTOS DE ANTICIPOS_0.15% OTORGADOS",'',$clase,1,5,'right');
										   $tdatos->td($pago_incentivo,'',$clase . ' moneda','',1,1,'right');
										   if ($origen === "ORIGEN") 
										   {
											$tdatos->td("<a id='dao' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										   } 
										   else 
										   {
												$tdatos->td("<a id='dao' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										   }
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
									if($seg_anticipo19 != 0 or $seg_anticipo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('SEGUNDOS ANTICIPOS','',$clase,1,5,'right');
											$tdatos->td($seg_anticipo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='sea' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');

											} 
											else 
											{
												$tdatos->td("<a id='sea' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}


									if($desc_seg_anticipo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('DESCUENTO SEGUNDOS ANTICIPOS','',$clase,1,5,'right');
											$tdatos->td($desc_seg_anticipo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='dsea' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='dsea' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_esp_cert != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION ESPECIAL CERTIFICADOS','',$clase,1,5,'right');
											$tdatos->td($com_esp_cert,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='cec' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='cec' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
/*
									if($com_reg_asesor != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION COMISIONISTA POR REGISTROS','',$clase,1,5,'right');
											$tdatos->td($com_reg_asesor,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='cra' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='cra' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
*/
									if($com_reg_ger != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION GERENTES POR REGISTROS','',$clase,1,5,'right');
											$tdatos->td($com_reg_ger,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='crg' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='crg' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_reg_sb != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION BROCKERS POR REGISTROS','',$clase,1,5,'right');
											$tdatos->td($com_reg_sb,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='crsb' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='crsb' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_reg_sc != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION SOCIOS POR REGISTROS','',$clase,1,5,'right');
											$tdatos->td($com_reg_sc,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='crsc' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='crsc' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}											
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($desc_incumpl != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('DESCUENTOS POR INCUMPIMIENTO','',$clase,1,5,'right');
											$tdatos->td($desc_incumpl,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='d_incumpl' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='d_incumpl' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($pasa_ajuste==1)
									{
										if($ajustes==0)
											$ajustes="0.0";
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('AJUSTES','',$clase,1,5,'right');
											$tdatos->td($ajustes,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												//  $tdatos->td("",'','','',1,3,'left');
												$tdatos->td("<a id='a' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='a' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}											
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($acum_as19 != 0 or $acum_as != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('ACUMULADO MENSUAL COMISIONISTA','',$clase,1,5,'right');
											$tdatos->td($acum_as,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='aca' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='aca' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($acum_ge19 != 0 or $acum_ge != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('ACUMULADO MENSUAL GERENTE','',$clase,1,5,'right');
											$tdatos->td($acum_ge,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='acg' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='acg' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($acum_sb19 != 0 or $acum_sb != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('ACUMULADO MENSUAL BROCKER','',$clase,1,5,'right');
											$tdatos->td($acum_sb,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='acsb' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');

											} 
											else 
											{
												$tdatos->td("<a id='acsb' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($gasto19 != 0 or $gasto != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th('DESCUENTOS POR GASTOS','',$clase,1,5,'right');
										$tdatos->td($gasto,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='gas' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');

										} 
										else 
										{
											$tdatos->td("<a id='gas' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}										
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($comision_asesor19 != 0 or $comision_asesor != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION POR COMISIONISTA','',$clase,1,5,'right');
											$tdatos->td($comision_asesor,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='ca' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='ca' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($comision_incentivo19 != 0 or $comision_incentivo != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION POR INCENTIVOS','',$clase,1,5,'right');
											$tdatos->td($comision_incentivo,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='ci' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='ci' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}											
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($comision_gerente19 != 0 or $comision_gerente != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION GERENTE','',$clase,1,5,'right');
											$tdatos->td($comision_gerente,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='cg' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='cg' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
									if($comision_brocker19 != 0 or $comision_brocker != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION POR BROKER','',$clase,1,5,'right');
											$tdatos->td($comision_brocker,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='cb' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='cb' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_socio19 != 0 or $com_socio != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION POR SOCIO COMERCIAL','',$clase,1,5,'right');
											$tdatos->td($com_socio,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='csc' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='csc' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($rec_asesor19 != 0 or $rec_asesor != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION RECERTIFICACION COMISIONISTA','',$clase,1,5,'right');
											$tdatos->td($rec_asesor,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='reca' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='reca' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($rec_gerente19 != 0 or $rec_gerente != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('COMISION RECERTIFICACION GERENTE','',$clase,1,5,'right');
											$tdatos->td($rec_gerente,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='recg' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='recg' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}											
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
									if($o_acompa19 != 0 or $o_acompa != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th('PAGO ACOMPAÑAMIENTOS','',$clase,1,5,'right');
											$tdatos->td($o_acompa,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='acompa' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											} 
											else 
											{
												$tdatos->td("<a id='acompa' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}
									if($o_acompa_desc19 != 0 or $o_acompa_desc != 0)
									{
									  $tdatos->tr();
									  $tdatos->td('','','','',1,2);
									  $tdatos->th('DESCUENTOS POR ACOMPAÑAMIENTOS','',$clase,1,5,'right');
									  $tdatos->td($o_acompa_desc,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='acompa_d' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='acompa_d' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($pago_bono_mtto != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('PAGO BONO POR CERTIFICACION'),'',$clase,1,5,'right');
										$tdatos->td($pago_bono_mtto,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='b_cert' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='b_cert' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}										
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($desc_bono_mtto19 != 0 or $desc_bono_mtto != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('DESCUENTO BONO POR MANTENIMIENTO CARTERA'),'',$clase,1,5,'right');
										$tdatos->td($desc_bono_mtto,'',$clase . ' moneda','',1,1,'right');
										//$tdatos->td("<a'>detalle</a>",'','','',1,3,'left');
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($desc_bono_mttob19 != 0 or $desc_bono_mttob != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('DESCUENTO BONO POR MANTENIMIENTO CARTERA DE BROCKER'),'',$clase,1,5,'right');
										$tdatos->td($desc_bono_mttob,'',$clase . ' moneda','',1,1,'right');
										//$tdatos->td("<a'>detalle</a>",'','','',1,3,'left');
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($pago_bono_mttob19 != 0 or $pago_bono_mttob != 0)
									{
									   $tdatos->tr();
									   $tdatos->td('','','','',1,2);
									   $tdatos->th(utf8_decode('BONO BROCKER POR MTTO CARTERA'),'',$clase,1,5,'right');
									   $tdatos->td($pago_bono_mttob,'',$clase . ' moneda','',1,1,'right');
									   if ($origen === "ORIGEN") 
									   {
											$tdatos->td("<a id='mtto_sb' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
									   } 
									   else 
									   {
											$tdatos->td("<a id='mtto_sb' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
									   }									   
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($pago_bono_mttosc19 != 0 or $pago_bono_mttosc != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('BONO SOCIO COMERCIAL POR MTTO CARTERA'),'',$clase,1,5,'right');
										$tdatos->td($pago_bono_mttosc,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='mtto_sc' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='mtto_sc' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($bono_compl != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										//$tdatos->th(utf8_decode('COMPLEMENTO BONO CERTIFICADO'),'',$clase,1,5,'right');
										$tdatos->th(utf8_decode('BONO EXTRA 0.10%'),'',$clase,1,5,'right');
										$tdatos->td($bono_compl,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='b_compl' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='b_compl' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($apovol_ger19 != 0 or $apovol_ger != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('COMISION GERENTE APOVOL'),'',$clase,1,5,'right');
										$tdatos->td($apovol_ger,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='avg' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='avg' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($desc_com_factura != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th(utf8_decode('DESCUENTO COMISION FACTURA'),'',$clase,1,5,'right');
										$tdatos->td($desc_com_factura,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='dcfac' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='dcfac' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($desc_ajuste_fac != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th('AJUSTE COM FACTURA','',$clase,1,5,'right');
										$tdatos->td($desc_ajuste_fac,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='dafac' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='dafac' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}										
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}
									if($compl_30 != 0)
									{
										$tdatos->tr();
										$tdatos->td('','','','',1,2);
										$tdatos->th('PAGO COMPLEMENTO POR CUMPLIMIENTO AV','',$clase,1,5,'right');
										$tdatos->td($compl_30,'',$clase . ' moneda','',1,1,'right');
										if ($origen === "ORIGEN") 
										{
											$tdatos->td("<a id='c30' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
										} 
										else 
										{
											$tdatos->td("<a id='c30' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
										}										
										if($clase=='td_gris')
											$clase='td_blanco';
										else
											$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_reg_am != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th("REGISTROS AFORE MOVIL",'',$clase,1,5,'right');
											$tdatos->td($com_reg_am,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='ram' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											}
											else 
											{
												$tdatos->td("<a id='ram' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									if($com_especial != 0)
									{
										$tdatos->tr();
											$tdatos->td('','','','',1,2);
											$tdatos->th("COMISION ESPECIAL",'',$clase,1,5,'right');
											$tdatos->td($com_especial,'',$clase . ' moneda','',1,1,'right');
											if ($origen === "ORIGEN") 
											{
												$tdatos->td("<a id='ce' class='detalle' data-origen='ORIGEN'>detalle</a>",'','','',1,3,'left');
											}
											else 
											{
												$tdatos->td("<a id='ce' class='detalle' data-origen=''>detalle</a>",'','','',1,3,'left');
											}
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										$tdatos->close_tr();
									}

									$tdatos->tr();
										$tdatos->td();
									$tdatos->close_tr();
	//								if($total != 0)
	//								{
										$iva=1;
										if($total > 0)
										{
											$fil=  "Select factura,datediff(concat(substr(fec_factura,-4),'-',substr(fec_factura,4,2),";
											$fil.= "'-',substr(fec_factura,1,2)),concat(substr('$fecha',-4),'-',substr('$fecha',4,2),'-'";
			   								$fil.= ",substr('$fecha',1,2))),regimen_fiscal from empleados where clave='$clave'";
											$tb=$conecta->consulta($fil);
											$row = $tb->fetch_array(MYSQLI_NUM);
											$factura=$row[0];
											$reg_fiscal=$row[2];
											if($factura==1)
											{
												if($row[1]<=0)
												{
													if($clave=='JODEJESAUV' and $p_esp > 0)
														$cal_iva=$total * 0.08;
													else
														$cal_iva=$total * 0.16;

													$tdatos->tr();
														$tdatos->td('','','','',1,2);
														$tdatos->th('SUBTOTAL','',$clase,1,5,'right');
														$tdatos->th($total,'',$clase . ' moneda',1,1,'right');
														$tdatos->td('','','','',1,3);
														if($clase=='td_gris')
															$clase='td_blanco';
														else
															$clase='td_gris';
													$tdatos->close_tr();
													$tdatos->tr();
														$tdatos->td('','','','',1,2);
														$tdatos->th('IVA','',$clase,1,5,'right');
														$tdatos->th($cal_iva,'',$clase . ' moneda',1,1,'right');
														$tdatos->td('','','','',1,3);
														if($clase=='td_gris')
															$clase='td_blanco';
														else
															$clase='td_gris';
													$tdatos->close_tr();
													if($reg_fiscal !='' and $reg_fiscal!='PM')
													{
														if($clave=='JODEJESAUV' and $p_esp > 0)
															$cal_ret_iva=($total * -0.106667)/2;
														else
															$cal_ret_iva=$total * -0.106667;
														$tdatos->tr();
															$tdatos->td('','','','',1,2);
															$tdatos->th('RETENCION IVA','',$clase,1,5,'right');
															$tdatos->th($cal_ret_iva,'',$clase . ' moneda',1,1,'right');
															$tdatos->td('','','','',1,3);
															if($clase=='td_gris')
																$clase='td_blanco';
															else
																$clase='td_gris';
														$tdatos->close_tr();
														if($reg_fiscal=='R' and $d > 0)
														{
															$cal_tot=$total * -0.0125;
															$tdatos->tr();
																$tdatos->td('','','','',1,2);
																$tdatos->th('RETENCION ISR','',$clase,1,5,'right');
																$tdatos->th($cal_tot,'',$clase . ' moneda',1,1,'right');
																$tdatos->td('','','','',1,3);
																if($clase=='td_gris')
																	$clase='td_blanco';
																else
																	$clase='td_gris';
															$tdatos->close_tr();
															if($clave=='JODEJESAUV' and $p_esp > 0)
																$iva=1.014166936;
															else
																$iva=1.040833;
														}
														else
														{
															if($reg_fiscal !='' and $reg_fiscal!='PM')
																$iva=1.053333;
														}
													}
													else
													{
														if($reg_fiscal =='')
															$iva=1;
														else
															$iva=1.16;
													}
												}
											}
										}
										$query="Select clave from pagos_retenidos where clave='$clave' and fecha='$fecha' and origen='C'";
										$bd=$conecta->consulta($query);
										if ($bd->num_rows != 0)
											$mensaje='Pago retenido por facturas pendientes';
										else
											$mensaje='';
										$tdatos->tr();
											if ($origen === "ORIGEN") 
											{
												$tdatos->td('','','','',1,2,'center');
											} 
											else 
											{
												$tdatos->td($imgexcel,'','','',1,2,'center');
											}
											

											$tdatos->th($mensaje,'',$clase . ' mensaje',1,4,'left');
											if($factura==1)
											{
												if($total < 0)
													$tdatos->th('TOTAL','',$clase . ' moneda',1,1,'right');
												else
													$tdatos->th('TOTAL A FACT','',$clase . ' moneda',1,1,'right');
											}
											else
												$tdatos->th('TOTAL','',$clase,1,1,'right');
											$tdatos->th("" . ($total * $iva),'',$clase . ' moneda',1,1,'right');
											//$tdatos->td("<a id='nue_con'>Nueva consulta</a>",'','','',1,3,'right');
											$tdatos->td("",'','','',1,3,'right');
											$tdatos->td('','','','',1,3);
											if($clase=='td_gris')
												$clase='td_blanco';
											else
												$clase='td_gris';
										if($pagos_lib != 0)
										{
											$tdatos->tr();
												$tdatos->td('','','','',1,3);
												$tdatos->th(utf8_decode('PAGOS LIBERADOS'),'',$clase,1,4,'right');
												$tdatos->td($pagos_lib,'',$clase . ' moneda','',1,1,'right');
												if ($origen === "ORIGEN") {
													$tdatos->td("",'','','',1,3,'left');
												} else {
													$tdatos->td("<a id='pl' class='detalle'>detalle</a>",'','','',1,3,'left');
												}
												if($clase=='td_gris')
													$clase='td_blanco';
												else
													$clase='td_gris';
//											if($total < 0)
//												$total=0;
											$tdatos->close_tr();
											$tdatos->tr();
												$tdatos->td('','','','',1,3);
												$tdatos->th(utf8_decode('TOTAL A DEPOSITAR'),'',$clase,1,4,'right');
												//$tdatos->th(($total * $iva) + $pagos_lib,'',$clase,'',1,1,'right');
												$tdatos->th("" . ($total * $iva) + $pagos_lib,'',$clase . ' moneda',1,1,'right');
												$tdatos->td("",'','','',1,3,'right');
												//$tdatos->td("<a id='pl' class='detalle'>detalle</a>",'','','',1,3,'left');
												if($clase=='td_gris')
													$clase='td_blanco';
												else
													$clase='td_gris';
											$tdatos->close_tr();
										}
										$tdatos->close_tr();
										$tdatos->tr();
											$tdatos->td($imgexcel,'','','',1,2,'center');
										$tdatos->close_tr();
	//								}
								$tdatos->close();
								$divdetalle->crear();
								$divdetalle->close();
								$divdetalle->css("display","none");
								$divdetalle->css("height","70%");
								$divdetalle->css("top","15%");
								$divdetalle->css("width","80%");
								$divdetalle->css("left","10%");
								$divdetalle->css("z-index","6");
								$divdetalle->css("border-width","2px");
								$imgexcel->css("width","30px");
								$imgexcel->css("cursor","pointer");
if($prueba==1)
	$clave="CAROBRA";
							}
						}
					}
				$divcontenido->close();
			$divpie->close();
?>
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
   			 	$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
</script>
<?php
		}
?>

	<!--div class="modal fade" id="modalOrigen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog custom-modal-width">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel">Detalle de origen de insuficiencia</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<iframe id="contenidoPagina" src="" width="100%" height="400px"></iframe>
				</div>
			</div>
		</div>
	</div-->
		<div id="myModal" class="my-modal">
			<div class="my-modal-content">
				<span class="my-close">&times;</span>
				<h2 class="my-modal-title">Origen de insuficiencia</h2>
				<br/>
				<div class="content-container">
					<table class="tabla-fechas">
						<tr>
							<th>Hist&oacute;rico de fechas de origen</th>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div id="myModalDetalle" class="my-modal">
			<div class="my-modal-content">
				<span  id="my-close-dte" class="my-close-dte">&times;</span>
				<h2 class="my-modal-title">Detalle de origen de insuficiencia</h2>
				<div class="content-container">
					<iframe id="contenidoPaginaDetalle" src="" width="100%" height="400px"></iframe>
				</div>
			</div>
		</div>
		
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

  			$('#tmenu td').mouseenter(function(evento)
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

  		  	$(".mensaje").css("color","red");
  		  	$("#fenvio").attr('autocomplete', 'off');

  		  	$("#txtusuario").focus();
  		  	$("#fecha").focus();
  		  	
			$("#txtusuario").autocomplete
			({
				source:'autocompleta.php?c=txtusuario',minLength:4,
				select: function(event,ui) 
				{
					$("#txtusuario").val(ui.item.value);
			  		$("#contiene").val($('#fenvio').attr("action"));
  		  			$('#fenvio').attr("action","conector.php");
 			  		$('#fenvio').submit();
					return false;
				}
			});

			$("#fecha").change(function()
			{
		  		$("#contiene").val($('#fenvio').attr("action"));
		  		$('#fenvio').attr("action","conector.php");
			  	$('#fenvio').submit();
				return false;
			});

			$.formatCurrency.regions[''] = {
												symbol: '$',
												positiveFormat: '%s%n',
												negativeFormat: '-%s%n',
												decimalSymbol: '.',
												digitGroupSymbol: ',',
												groupDigits: true,
												colorize:true
											};

//			$(".td_gris, .td_blanco").formatCurrency();
			$(".moneda").formatCurrency();

			$(".detalle").css("font-size","9px");
			
	  		$("a").hover(function()
	  		{
	  		  	if(colorToHex($(this).css("color"))=="#ff0000")
	  		  		$(this).css("color","#1D4384");
	  		  	else
	  		  		$(this).css("color","red");
	  	  	});

	  		$("#imgexcel").click(function()
	  		{
	  			var fil="excel_consul_pago.php?fecha=" + '<?php echo $fecha?>' + "&clave=" + '<?php echo encripta($clave)?>';
	  		    window.open(fil, '_blank');
			    return false;
	  		});
	  		
	  		$("a").click(function()
	  		{
		  		if(this.id=="nue_con")
		  		{
  			  		$("#contiene").val($('#fenvio').attr("action"));
  			  		$('#fenvio').attr("action","conector.php");
		  			$("#fenvio").submit();
		  		}
		  		else
		  		{
					var bandera = $(this).data('origen');
				  	//var fil= "detalle_pagos.php?clave=" + '<?php echo encripta($clave)?>' + "&fecha=" + '<?php echo $fecha?>' + "&tipo=";
				  	var fil= "detalle_pagos.php?clave=" + '<?php echo encripta(($clave=='CAROBRA') ? 'MAMAYSANCO' : $clave)?>' + "&fecha=" + '<?php echo $fecha?>' + "&bandera=" + encodeURIComponent(bandera) + "&tipo=";
			  		fil=fil + this.id;
					$("#detalle_produc").load(fil,function()
					{
						console.log('fil ',fil);
						if (bandera === "ORIGEN") {
							$("#detalle_produc").css({
								"display": "inline-block",
								"position": "fixed",
								"z-index": "4000",
								"top": "50%",
								"left": "50%",
								"transform": "translate(-50%, -50%)",
								"width": "80%",
								"max-width": "900px",
								"height": "200px",
								"padding": "20px",
								"background-color": "#fff",
								"box-shadow": "0 4px 8px rgba(0, 0, 0, 0.5)",
								"border-radius": "10px",
								"overflow": "auto",
								"border": "1x solid #686868"
							});

							$("#detalle_produc").find('*').css({
								"background-color": "#fff",
								"z-index": "4001",
								"position": "relative"
							});

						} 
						else 
						{
							$("#detalle_produc").css("display","inline-block");
						}
					});
		  		}
	  		});


			$('body').on('hover', '#desglose',function()
	  		{
	  		  	if(colorToHex($(this).css("color"))=="#ff0000")
	  		  		$(this).css("color","#1D4384");
	  		  	else
	  		  		$(this).css("color","red");
	  	  	});

			$('body').on('click', '#desglose',function()
	  		{

	  			let html='';
	  			let fecha=$(this).data("fec");
	  			let clave=$(this).data("clave");

	  			html= "<table style='width: 100%''>";
				html+="<tr><td style='padding: 30px'></td></tr>";
				html+="<tr><td style='width: 15%''><img src='./img/cargando.gif' width='30%'></td></tr></table>";
	  			$("#vis-info").html(html);
				$("#vis-info").css("display","block");
			   	$.post("consul_pago_desg.php",{ fecha: fecha, clave: clave }, function(response)
				{
					$("#vis-info").html(response);
				});
	  		});

			$('body').on('hover', '#cerrar',function()
	  		{
	  		  	if(colorToHex($(this).css("color"))=="#ff0000")
	  		  		$(this).css("color","#1D4384");
	  		  	else
	  		  		$(this).css("color","red");
	  	  	});

			$('body').on('click', '#cerrar',function()
	  		{
	  			$("#vis-info").css("display","none");
	  		});

			$(".my-close").on("click", function() 
			{
        		$("#myModal").css("display","none");
    		});

			$(document).on('click', '.fec-dte', function(e) {
				e.preventDefault();
				let cadena = $(this).data('fecha');
				let partes = cadena.split('|');
				let clave = encodeURIComponent(partes[0]);
				let fec_detalle = encodeURIComponent(partes[1]);
				let op = encodeURIComponent(2);
				let origen = encodeURIComponent('ORIGEN');
				var url = 'consul_pago.php?clave=' + clave + '&fec_detalle=' + fec_detalle + '&op=' + op + '&origen=' + origen;
				$('#contenidoPaginaDetalle').attr('src', url);
				$("#myModalDetalle").css("display", "block");
				removeIframeStyles();
			});

			$(".my-close-dte").on("click", function() 
			{
        		$("#myModalDetalle").css("display","none");
    		});

			function removeIframeStyles() 
			{
				var iframe = document.getElementById("contenidoPaginaDetalle");
				iframe.onload = function() {
					var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
					var styleElement = iframeDoc.createElement("style");
					styleElement.innerHTML = `
						body, div { 
							background: none !important; 
							border: none !important; 
						}
						#contenedor, #dmensaje {
							box-shadow: none !important;
						}
					`;
					iframeDoc.head.appendChild(styleElement);
				};
			}
		});
	</script>
</html>
