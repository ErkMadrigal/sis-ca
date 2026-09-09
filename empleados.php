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
		<style>
			#vis-info	{
							position: absolute;
							width: 500px;
							margin-left: 140px;
							margin-top: 80px;
							border: 1px solid #A7A5A5;
							border-radius: 20px;
							padding: 10px;
							background: white;
							display: none;
							z-index: 10;
						}

			.ajuste	{	padding-top: 4px;	}

			textarea {	resize: none;	}
		</style>
	</head>
	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?php
		$fg = $_SESSION["ultimoAcceso"]; 
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
  $periodo=10;
     	if($periodo > 1000)
     	{
      		session_destroy();
      		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888/");
    	}
    	else
    	{
    		$_SESSION["ultimoAcceso"] = $fa;
    		$_SESSION["autentifica"] = "p";
   		}
		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente","http://carobra.ddns.me:8888/");
		}
		else
		{
			reg_acceso($_SESSION["nombre"],"empleados");
//---------------------------------------------------------------Recepción de parámetros
			$_SESSION["autentifica"]="";		
			$noempleado=addslashes(trim($_POST["noempleado"]));
			$nombre_emp=addslashes(trim($_POST["nombre_emp"]));
			$activo=addslashes(trim($_POST["activo"]));
			$staff=addslashes(trim($_POST["staff"]));
			$lista_negra=addslashes(trim($_POST["lista_negra"]));
			$factura=addslashes(trim($_POST["factura"]));
			$fec_ingreso=addslashes(trim($_POST["fec_ingreso"]));
			$fec_factura=addslashes(trim($_POST["fec_factura"]));
			$fec_nap=addslashes(trim($_POST["fec_nap"]));
			$regimen_fiscal=addslashes(trim($_POST["regimen_fiscal"]));
			$domicilio=addslashes(trim($_POST["domicilio"]));
			$telefono=addslashes(trim($_POST["telefono"]));
			$celular=addslashes(trim($_POST["celular"]));
			$email=addslashes(trim($_POST["email"]));
			$emaili=addslashes(trim($_POST["emaili"]));
			$pwd=addslashes(trim($_POST["pwd"]));
			$banco=addslashes(trim($_POST["banco"]));
			$clabe=addslashes(trim($_POST["clabe"]));
			$deportenis=addslashes(trim($_POST["deportenis"]));
			$ase_bernal=addslashes(trim($_POST["ase_bernal"]));
			$desc_nap_factura=addslashes(trim($_POST["desc_nap_factura"]));
			$desc_nap_gerencia=addslashes(trim($_POST["desc_nap_gerencia"]));
			$no_prom=addslashes(trim($_POST["no_prom"]));
			$categoria=addslashes(trim($_POST["categoria"]));
			$cen_cost=addslashes(trim($_POST["cen_cost"]));
			$no_contpaq=addslashes(trim($_POST["no_contpaq"]));
			$nap=addslashes(trim($_POST["nap"]));
			$nae=addslashes(trim($_POST["nae"]));
			$nss=addslashes(trim($_POST["nss"]));
			$rfc=addslashes(trim($_POST["rfc"]));
			$curp=addslashes(trim($_POST["curp"]));
			$costo_a=addslashes(trim($_POST["costo_a"]));
			$edo_civil=addslashes(trim($_POST["sel_edo_civil"]));
			$sueldo=addslashes(trim($_POST["sueldo"]));
			$infonavit=addslashes(trim($_POST["sel_infonavit"]));
			$nom_gerente=addslashes(trim($_POST["nom_gerente"]));
			$puesto=addslashes(trim($_POST["puesto"]));
			$fec_imss=addslashes(trim($_POST["fec_imss"]));
			$horario=addslashes(trim($_POST["horario"]));
			$dias_descanso=addslashes(trim($_POST["dias_descanso"]));
			$hora_comida=addslashes(trim($_POST["hora_comida"]));
			$dir_trabajo=addslashes(trim($_POST["dir_trabajo"]));
			$actividades=addslashes(trim($_POST["actividades"]));
//----------------------------------------------------------------------------------------

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
				titulo("Empleados");
				$pag="REC_HUMANOS";
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
//--------------------------------------------------------------Declaración de objetos
					$bd= new conector();
					$seldato= new select("info");
					$selreg= new select("regimen_fiscal");
					$tdatos=new table('datos','','100%');
					$txtdato=new textbox("noempleado");
					$sel_infonavit= new select("sel_infonavit");
					$sel_edo_civil= new select("sel_edo_civil");
					$fenvio= new form("fenvio","empleados.php");
					$encabeza= new div("empleados","encabeza_pie");
					$imgnue= new img("nuevo","./img/nuevo1.png","./img/nuevo2.png","boton");
					$imginhabilita=new img("inhabilita","./img/anexa_inha.png","","boton_tabla");
					$imggua= new img("guardar","./img/bguardar1.png","./img/bguardar2.png","boton");
					$imganexa=new img("anexa","./img/anexar1.png","./img/anexar2.png","boton_tabla");
					$imgborrar= new img("borrar","./img/borrar1.png","./img/borrar2.png","boton_tabla");
//--------------------------------------------------------------------------------------- Ventana observa
?>
			<div id='vis-info'>
				<table width="98%">
					<tr>
						<th colspan="8" style='text-align: right; padding: 5px 0px 8px 0px;'>
							<img src="./img/cerrar1.png" id="cerrar" width="12px">
						</th>
					</tr>
					<tr>
						<th style='text-align: left'>Observaciones</th>
					</tr>
					<tr>
						<td style='text-align: right'>
							<textarea name="observa" id='observa' rows="10" cols="60"></textarea>
						</td>
					</tr>
					<tr>
						<td style='text-align: center'>
							<img src="./img/bguardar1.png" id="guarda_ob" name='guarda_ob' class='boton'>
						</td>
					</tr>
				</table>
			</div>
<?php
					$tipo_nap="nap";
					$encabeza->crear();
						echo "<br>";
						$fenvio->crear();
							tex_h("naps");
							tex_h("op",1);
							tex_h("contiene");
							tex_h("dul");
							if(strlen($noempleado)==0 && strlen($nombre_emp)==0)
							{ 
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->td("No empleado:",'','','',1,1,'right');
										$tdatos->td($txtdato,'','','',1,1,'left');
										$txtdato->attr("size","5");
										$txtdato->attr("maxlength","5");
										$tdatos->td('','120px');
										$tdatos->td("Nombre:",'','','',1,1,'right');
										$txtdato->identificador("nombre_emp");
										$tdatos->td($txtdato,'','','',1,1,'left');

//$_SESSION["nombre"] = "ROSA EVELIA RODRIGUEZ VAZQUEZ";

										if($_SESSION["nombre"] == "ROSA EVELIA RODRIGUEZ VAZQUEZ")
											$query="Select nombre
													from (Select nombre from usuarios u
															inner join (Select * 
																		from usuarios_gerencia 
																		where id_gerencia=124) ug 
																	on (u.clave=ug.clave)
														  union
														  Select nombre from usuarios u 
															left join usuarios_gerencia ug 
																	on (u.clave=ug.clave) 
														  where (ifnull(ug.clave,'')='')) u 
													where nombre like '%$%'";
										else
										{
											if($_SESSION["nombre"] == "LESLIE JANETTE MALDONADO HERRERA")
												$query="Select nombre
														from (Select nombre from usuarios u
																inner join (Select * 
																			from usuarios_gerencia 
																			where id_gerencia=594) ug 
																		on (u.clave=ug.clave)
															  union
															  Select nombre from usuarios u 
																left join usuarios_gerencia ug 
																		on (u.clave=ug.clave) 
															  where (ifnull(ug.clave,'')='')) u 
														where nombre like '%$%'";
											else
												$query="Select nombre from usuarios where nombre like '%$%'";
										}
										$_SESSION["nombre_emp"]=$query;
										$txtdato->css("width","300px");
									$tdatos->close_tr();
								$tdatos->close();
							}
							else
							{
								$style="";
								if($lista_negra==1)
									$style="style='color:red; font-weight: bold;'";
								$tdatos->crear();
									tex_h("noempleado",$noempleado);
									tex_h("nombre_emp",utf8_encode($nombre_emp));
									$ana_display="none"; 
									$tdatos->tr();
										$tdatos->td("No:",'7%','','',1,1,'right');
										$tdatos->td('<span id="ne"><u>' . $noempleado . '</u></span>','5%','','',1,1,'left');
										$tdatos->td("Nombre:",'5%','','',1,1,'right');
										$tdatos->td('<span ' . $style . '><u>' . utf8_encode($nombre_emp) . '</u></span>','','','',1,4,'left');
										$tdatos->td("Activo:",'5%','','',1,1,'right');
										$checked="";
										if($activo==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='activo' name='activo' $checked />",'3%','','',1,1,'left');
										$tdatos->td("Ingreso:",'8%','','',1,1,'right');
										$txtdato->identificador("fec_ingreso");
										$tdatos->td($txtdato,'15%','','',1,1,'left','',$fec_ingreso);
										$txtdato->attr("size","10");
										$tdatos->td("<a id='btn-observa'>Observaciones</a>",'10%','','',1,1,'right');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->td("",'58%','','',1,1,'right');
										$tdatos->td("Staff:",'5%','','',1,1,'right');
										$checked_staff="";
										if($staff==1)
											$checked_staff="checked";
										$tdatos->td("<input type='checkbox' id='staff' name='staff' $checked_staff />",'3%','','',1,1,'left');
										$tdatos->td("Lista negra:",'19%','','',1,1,'right');
										$checked_ln="";
										if($lista_negra==1)
											$checked_ln="checked";
										$tdatos->td("<input type='checkbox' id='lista_negra' name='lista_negra' $checked_ln />",'3%','','',1,1,'left');
										$tdatos->td("",'14%','','',1,1,'right');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();	
									$tdatos->tr();
										$query="Select clave from empleados
												where no_empleado=$noempleado";
										$tb=$bd->consulta($query);
										if ($tb->num_rows == 0)
										{
											$query="Select clave from usuarios 
													where nombre='" . utf8_encode($nombre_emp) ."'";
											$tb=$bd->consulta($query);
											$row=$tb->fetch_array(MYSQLI_NUM);
											$clave=$row[0];											
										}
										else
										{
											$row=$tb->fetch_array(MYSQLI_NUM);
											$clave=$row[0];
										}
										$permiso_sc=0;
										$permiso_ger=0;
										$permiso_nap=0;
										$query="Select distinct desc_nivel from niveles n 
													inner join usuarios_nivel_gerencia uni 
															on (n.id_nivel=uni.id_nivel)
											  	where uni.clave='$clave'
											  	union
											    Select desc_nivel from niveles n 
													inner join usuarios_nivel uni on (n.id_nivel=uni.id_nivel) 
													inner join usuarios u on (uni.clave=u.clave) where uni.clave='$clave'";
										$tb=$bd->consulta($query);
										if ($tb->num_rows != 0)
										{
											while($row=$tb->fetch_array(MYSQLI_NUM))
											{
												if($row[0]=='Analista' or $row[0]=='Gerente' or $row[0]=='SubBroker' or $row[0]=='Auxiliar de llenado' or $row[0]=='Soporte DAP' or $row[0]=='Socio Comercial')
													$permiso_ger=1;
												if($row[0]=='SubBroker' or $row[0]=='Socio Comercial')
													$permiso_nap=1;
												if($row[0]=='Socio Comercial')
													$permiso_sc=1;
											}
										}
										echo "<td>";
											echo "<tr>";
												$tdatos->td("Nivel:",'23%','','',1,1,'left',"top");
												echo "<td width='5%'></td>";
												$tdatos->td("Gerencia:",'27%','','',1,1,'left','top');
												echo "<td width='5%'></td>";
												$tdatos->td("Brockers:",'35%','','',1,1,'left','top');
												echo "<td width='5%'></td>";
											echo "</tr>";
											echo "<tr>";
												$query="Select id_nivel,desc_nivel 
														from niveles order by desc_nivel";
												$seldato->identificador("nivel");
												$seldato->query($query);
												$tdatos->td($seldato,'','','',1,1,'left','top',$nivel);
												$seldato->css("width","100%");

												$imganexa->identificador("anexa_nivel");
												$tdatos->td($imganexa,'','','',1,1,'left','top');
												
												$seldato->identificador("gerencia");
												$query="Select distinct id_gerencia,gerencia 
														from gerencias order by gerencia";
												$seldato->query($query);
												$tdatos->td($seldato,'','','',1,1,'left','top',$gerencia);
												$seldato->css("width","100%");


												$fil="Select distinct gerencia from gerencias g 
														inner join usuarios_gerencia uge 
															on (g.id_gerencia=uge.id_gerencia) 
													  where clave='$clave'";
//echo $fil;
												$tb=$bd->consulta($fil);
												if($tb->num_rows < 1 or $permiso_ger==1)
												{
													$imganexa->identificador("anexa_ger");
													$tdatos->td($imganexa,'','','',1,1,'left','top');
												}
												else 
													$tdatos->td($imginhabilita,'','','',1,1,'left','top');

												$seldato->identificador("brocker");
												$query="Select no_empleado, brocker
														from (Select distinct brocker 
															  FROM estructura_gerencia where brocker !='') b
															inner join empl_usuarios u on (b.brocker=u.nombre)
														order by brocker";
												$seldato->query($query);
												$tdatos->td($seldato,'','','',1,1,'left','top',$brocker);
												$seldato->css("width","100%");

												if($permiso_sc==1)
												{
													$imganexa->identificador("anexa_sc");
													$tdatos->td($imganexa,'','','',1,1,'left','top');
												}
												else 
													$tdatos->td($imginhabilita,'','','',1,1,'left','top');

											echo "</tr>";
											$fil="Select desc_nivel from niveles n 
													inner join usuarios_nivel uni 
															on (n.id_nivel=uni.id_nivel) 
												  where clave='$clave'
												  union
												  Select concat(desc_nivel, ' (CC)') from niveles n 
													inner join (Select * from control_cambios 
																where tipo='nivel') cc 
															on (n.id_nivel=cc.valor)
													inner join (Select no_empleado 
																from empl_usuarios
																where clave='$clave') eu 
															on (cc.no_empleado=eu.no_empleado)";
											$tb=$bd->consulta($fil);
											if ($tb->num_rows > 0)
											{
												$fil= "<ul id='univel' style='font-size:0.9em;'>";
												while($row=$tb->fetch_array(MYSQLI_NUM))
												{
													if($row[0]=="Asesor en entrenamiento")
														$tipo_nap="nae";
													$fil.= "<li>" . utf8_encode($row[0]) . "</li>";
												}
												$fil.= "</ul>";
											}
											else
												$fil="";
											echo "<tr>";
												$tdatos->td($fil,'20%','','tdniveles',1,1,'left','top');
													$hoy=date("Y-m-d");
													$fil="Select gerencia,pertenece,
																 if(pertenece=1,brocker,''),if(pertenece=1,sc,'')
														  from (Select g.id_gerencia,gerencia, pertenece 
															    from (Select distinct id_gerencia,gerencia from gerencias) g 
																	inner join usuarios_gerencia uge 
																			on (g.id_gerencia=uge.id_gerencia) 
															    where clave='$clave'
															    union 
															    Select g.id_gerencia,
															    	   concat(g.gerencia,' (CC)'),
															    	   pertenece 
															    from (Select distinct id_gerencia,gerencia
															    	  from gerencias) g 
																	inner join (Select no_empleado,
																					   gerencia,pertenece 
																				from control_cambios 
																				where tipo='nivel' 
																					and datediff(concat(substr(fecha,-4),'-',substr(fecha,4,2),'-',substr(fecha,1,2)),'2029-07-26')<=0) cc 
																			on (g.id_gerencia=cc.gerencia)
																	inner join (Select no_empleado 
																				from empl_usuarios 
																				where clave='$clave') eu 
																			on (cc.no_empleado=eu.no_empleado) 
															    union 
															    Select g.id_gerencia,
															    	   concat(gerencia,' (CC)'),pertenece 
															    from (Select distinct id_gerencia,gerencia
															     	  from gerencias) g 
																	inner join (Select no_empleado,valor,
																					   pertenece 
																				from control_cambios 
																				where tipo='gerencia' 
																					and datediff(concat(substr(fecha,-4),'-',substr(fecha,4,2),'-',substr(fecha,1,2)),'2029-07-26')<=0) cc 
																			on (g.id_gerencia=cc.valor) 
																	inner join (Select no_empleado 
																				from empl_usuarios 
																				where clave='$clave') eu 
																			on (cc.no_empleado=eu.no_empleado)) d
																inner join estructura_gerencia eg 
																		on (d.id_gerencia=eg.id_gerencia)
														  order by gerencia";
//echo $fil;
												echo "<td></td>";

												$tb=$bd->consulta($fil);
												if ($tb->num_rows > 0)
												{
													$fil= "<div style='width:100%; height:60px; overflow-y:auto; font-size:0.85em;'><ul id='ugerencias'>";
													while($row=$tb->fetch_array(MYSQLI_NUM))
													{
														if($row[1]==1)
														{
															$style="style='background:#7E7A7A; color:white;'";
															$brocker=$row[2];
															$sc=$row[3];
														}
														else
															$style="";
														$fil.= "<li $style>" . utf8_encode($row[0]) . "</li>";
													}
													$fil.= "</ul></div>";
												}
												else 
													$fil="";
												$tdatos->td($fil,'','','tdgerencias',1,1,'left','top');
													$fil=  "Select distinct nombre from empl_usuarios eu 
																inner join usuarios_brocker ub 
																		on (eu.no_empleado=ub.id_brocker)
																inner join empleados e 
																		on (ub.no_empleado=e.no_empleado)
															where e.clave='$clave'
															union
															Select distinct concat(nombre, ' (CC)') from empl_usuarios eu 
																inner join (Select * from control_cambios 
																			where tipo='nivel') cc 
																					on (eu.no_empleado=cc.gerencia) 
																inner join (Select no_empleado from empl_usuarios 
																			where clave='$clave') eus 
																			on (cc.no_empleado=eus.no_empleado)";
												echo "<td></td>";
												$tb=$bd->consulta($fil);
												if ($tb->num_rows > 0)
												{
													$fil= "<div style='width:100%; height:60px; overflow-y:auto;font-size:0.85em;'><ul id='ubrockers'>";
													while($row=$tb->fetch_array(MYSQLI_NUM))
													{
														$fil.= "<li>" . utf8_encode($row[0]) . "</li>";
													}
													$fil.= "</ul></div>";
												}
												else 
													$fil="";
												$tdatos->td($fil,'','','tdbrockers',1,1,'left','top');
												echo "<td></td>";
											echo "</tr>";
										echo "</td>";
									$tdatos->css("border-color","#9E9E9E","#tdniveles, #tdgerencias, #tdbrockers");
									$tdatos->css("border-style","solid","#tdniveles, #tdgerencias, #tdbrockers");
									$tdatos->css("border-width","1px","#tdniveles, #tdgerencias, #tdbrockers");
									$tdatos->css("border-radius","4px 4px 4px 4px","#tdniveles, #tdgerencias, #tdbrockers");
									$tdatos->css("font-weight","normal","#tdniveles, #tdgerencias, #tdbrockers");
									$tdatos->css("background","#D0E2EF","#tdniveles, #tdgerencias, #tdbrockers");
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();				
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->td("Categoría:&nbsp;&nbsp;",'10%','','',1,1,'right',"top");
										$seldato->identificador("categoria");
										$seldato->query("Select id_categoria,categoria from categorias");
										$tdatos->td($seldato,'30%','','',1,1,'left','top',$categoria);
										$seldato->css("width","85%");
										$tdatos->td("Centro de Costos:&nbsp;&nbsp;",'20%','','',1,1,'right',"top");
										$seldato->identificador("cen_cost");
										$seldato->query("Select * from centros_costo_area");
										$tdatos->td($seldato,'36%','','',1,1,'left','top',$cen_cost);
										$seldato->css("width","100%");
										$tdatos->td("",'2%','','',1,1,);
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->td("Brocker:&nbsp;&nbsp;","10%",'','',1,1,'right','middle');
										$tdatos->td("<span id='spsb'>" . utf8_encode($brocker) . '</span>','30%','','tdsb',1,1,'left');
										$tdatos->td("Socio:&nbsp;&nbsp;",'20%','','',1,1,'right','middle');
										$tdatos->td("<span id='spsc'>" . utf8_encode($sc) . '</span>','36%','','tdsc',1,2,'left');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->th("Domicilio","96%",'',1,1,'left');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$txtdato->identificador("domicilio");
										$tdatos->td($txtdato,'96%','','',1,1,'','',utf8_encode($domicilio));
										$txtdato->css("width","100%");
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->th("Telefono","12%",'',1,1,'left');
										$tdatos->th("Celular","12%",'',1,1,'left');
										$tdatos->th("Email Personal","30%",'',1,1,'left');
										$tdatos->th("Email Institucional","30%",'',1,1,'left');
										$tdatos->th("pwd","12%",'',1,1,'left');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$txtdato->identificador("telefono");
										$tdatos->td($txtdato,'12%','','',1,1,'','',$telefono);
										$txtdato->css("width","98%");
										$txtdato->identificador("celular");
										$tdatos->td($txtdato,'12%','','',1,1,'','',$celular);
										$txtdato->css("width","98%");
										$txtdato->identificador("demail");
										$tdatos->td($txtdato,'30%','','',1,1,'','',utf8_encode($email));
										$txtdato->css("width","98%");
										$txtdato->identificador("emaili");
										$tdatos->td($txtdato,'30%','','',1,1,'','',utf8_encode($emaili));
										$txtdato->css("width","98%");
										$tdatos->td("<input type='text' id='pwd' name='pwd' style='color: white; width: 98%;' value='$pwd'>",'12%','','',1,1,'');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();

								$tdatos->crear();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->td(utf8_encode("Fec. alta NAP"),'15%','','',1,1,"left",'');
										$tdatos->td("NAP","15%",'','',1,1,"left",'');
										$tdatos->td("NAE",'15%','','',1,1,"left",'top');
										$tdatos->td("NSS",'15%','','',1,1,"left",'top');
										$tdatos->td("RFC",'15%','','',1,1,"left",'top');
										$tdatos->td("CURP",'21%','','',1,1,"left",'top');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$txtdato->identificador("fec_nap");
										$tdatos->td($txtdato,'15%','','',1,1,'left','',$fec_nap);
										$txtdato->css("width","75%");
										$txtdato->identificador("txt-nap");
										$tdatos->td($txtdato,'15%','','',1,1,'','',$nap);
										$txtdato->css("width","98%");
										$txtdato->identificador("txt-nae");
										$tdatos->td($txtdato,'15%','','',1,1,'','',$nae);
										$txtdato->css("width","98%");
										$txtdato->identificador("txt-nss");
										$tdatos->td($txtdato,'15%','','',1,1,'','',$nss);
										$txtdato->css("width","98%");
										$txtdato->identificador("txt-rfc");
										$tdatos->td($txtdato,'15%','','',1,1,'','',$rfc);
										$txtdato->css("width","98%");
										$txtdato->identificador("txt-curp");
										$tdatos->td($txtdato,'21%','','',1,1,'','',$curp);
										$txtdato->css("width","98%");
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br><hr><br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->td("Factura",'12%','','',1,1,"right");
										$checked='';
										if($factura==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='factura' name='factura' $checked />",'2%','','',1,1,'left');
										$tdatos->td('A partir de:','12%','','',1,1,'right');
										$txtdato->identificador("fec_factura");
										$tdatos->td($txtdato,'15%','','',1,1,'','',$fec_factura);
										$txtdato->css("width","75%");
										$tdatos->td('Regimen:','10%','','',1,1,'right');
										$selreg->lista("AE","Actividad Empresarial");
										$selreg->lista("IF","Incorporaci&oacute;n Fiscal");
										$selreg->lista("PM","Persona Moral");
										$selreg->lista("R","RESICO");
										$tdatos->td($selreg,'25%','','',1,1,'','',$regimen_fiscal);
										$selreg->css("width","98%");
										$tdatos->td('Dto. NAP-Factura: ','18%','','',1,1,'right');
										$checked="";
										if($desc_nap_factura==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='desc_nap_factura' name='desc_nap_factura' $checked />",'2%','','',1,1,'left');
										$tdatos->th("","2%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->th("","2%",1,1,'');
										$tdatos->td('Dto. NAP-Gerencia: ','18%','','',1,1,'right');
										$checked="";
										if($desc_nap_gerencia==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='desc_nap_gerencia' name='desc_nap_gerencia' $checked />",'2%','','',1,1,'left');
										$tdatos->th("Asesoría Bernal:","22%",'',1,1,'right');
										$checked="";

										if($ase_bernal==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='ase_bernal' name='ase_bernal' $checked />",'2%','','',1,1,'left');
										$tdatos->td('Costo asesoría: ','18%','','',1,1,'right');
										$txtdato->identificador("txt-costo-asesoria");
										$tdatos->td($txtdato,'12%','','',1,1,'','',$costo_a);
										$txtdato->css("width","98%");
										$tdatos->th("","24%",1,1,'');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br><hr><br>";

								if($_SESSION["nombre"] != "DAVID ALEJANDRO FARAH MADE PONCE" and $_SESSION["nombre"] != "CARLA RENATA TRUJILLO TALAVERA" and $_SESSION["nombre"] != "CLEADY AMAYRANI GARCIA TORRES")
								{
									$tdatos->crear();
										$tdatos->tr();
											$tdatos->th("Nombre cuenta",'22%','',1,1,'left');
											$tdatos->th("Paterno cuenta",'15%','',1,1,'left');
											$tdatos->th("Materno cuenta",'15%','',1,1,'left');
											$tdatos->th("Banco","21%",'',1,1,'left');
											$tdatos->th("CLABE","19%",'',1,1,'left');
											$tdatos->th("Activa","6%");
											$tdatos->th("","2%");
										$tdatos->close_tr();
										$tdatos->tr();
											$txtdato->identificador("nombre_cta");
											$tdatos->td($txtdato);
											$txtdato->css("width","98%");

											$txtdato->identificador("ap_cta");
											$tdatos->td($txtdato);
											$txtdato->css("width","98%");

											$txtdato->identificador("am_cta");
											$tdatos->td($txtdato);
											$txtdato->css("width","98%");

											$seldato->identificador("banco");
											$seldato->query("Select id_banco,banco from carobra.bancos");
											$tdatos->td($seldato);
											$seldato->css("width","98%");
											$txtdato->identificador("clabe");
											$tdatos->td($txtdato);
											$txtdato->css("width","98%");
											$imganexa->identificador("anexa_banco");
											$tdatos->th("","2%");
											$tdatos->td($imganexa,'','','',1,2,'right');
											//$tdatos->td($imgnue,'','','',1,1,'right');
										$tdatos->close_tr();
										$fil=  "Select nombre_cta,banco,clabe,activo 
												from usuarios_banco uba 
													inner join carobra.bancos b 
															on (uba.id_banco=b.id_banco) 
												where no_empleado=$noempleado";
										$tb=$bd->consulta($fil);
										if ($tb->num_rows > 0)
										{
											$i=1;
											$clase="td_blanco";
											while($row=$tb->fetch_array(MYSQLI_NUM))
											{
												if($clase=="td_blanco")
													$clase="td_gris";
												else
													$clase="td_blanco";
												$tdatos->tr();
													$tdatos->td(utf8_encode($row[0]),'',$clase,'nom_cta' . $i,1,3,'left');
													$tdatos->td(utf8_encode($row[1]),'',$clase,'banco' . $i,1,1,'left');
													$tdatos->td($row[2],'',$clase,'clabe' . $i,1,1,'left');
													if($row[3]==1)
														$checked="checked";
													else 
														$checked="";
													$tdatos->td("<input type='checkbox' id='activob$i' name='activob$i' class='act_cta' $checked />",'',$clase,'',1,1,"center");
													$imgborrar->identificador("borrar$i");
													$tdatos->td($imgborrar,'',$clase);
												$tdatos->close_tr();
												$i++;
											}
										}
										$tdatos->css("border-color","#9E9E9E","#tdnaps");
										$tdatos->css("border-style","solid","#tdnaps");
										$tdatos->css("border-width","1px","#tdnaps");
										$tdatos->css("border-radius","4px 4px 4px 4px","#tdnaps");
										$tdatos->css("font-weight","normal","#tdnaps");
										$tdatos->css("background","#D0E2EF","#tdnaps");
									$tdatos->close();
								}
							}

							if(strlen($noempleado)!=0 || strlen($nombre_emp)!=0)
							{ 
								echo "<br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->td('No. Contpap','20%','','',1,1,'left');
										$tdatos->td('Regimen','30%','','',1,1,'left');
										$tdatos->td('','20%');
										$tdatos->td('','10%');
										$tdatos->td('No. promotor','20%','','',1,1,'left');
									$tdatos->close_tr();
									$tdatos->tr();
										$tdatos->td("<input type='text' id='no_contpap' name='no_contpap' value='$no_contpaq' disabled>",'20%','','',1,1,'left');
										$seldato->identificador("recluta");
										$fil="select u.clave,nombre 
											  from (select * from usuarios_nivel 
											  		where id_nivel in (2,4,5,6)) uni 
											  	inner join (select clave,nombre from usuarios 
											  				where activo=1) u on (uni.clave=u.clave) 
											  order by nombre";
										$seldato->query($fil);
										$fil=  "Select recluta from empleados 
												where no_empleado=$noempleado";
										$tb=$bd->consulta($fil);
										
										if ($tb->num_rows > 0)
										{
											$row=$tb->fetch_array(MYSQLI_NUM);
											$tdatos->td($seldato,'30%','','',1,1,'left','top',$row[0]);
										}
										else
											$tdatos->td($seldato,'30%','','',1,1,'left','top');
										$tdatos->td('Deportenis:','20%','','',1,1,'right');
										$checked="";
										if($deportenis==1)
											$checked="checked";
										$tdatos->td("<input type='checkbox' id='deportenis' name='deportenis' $checked />",'10%','','',1,1,'left');
											$tdatos->td("<input type='text' id='no_prom' name='no_prom' value='$no_prom'>",'20%','','',1,1,'left');
									$tdatos->close_tr();
								$tdatos->close();
								echo "<br>";
								$tdatos->crear();
									$tdatos->tr();
										$tdatos->td("",'35%','','',1,1,'left');
										$tdatos->td($imgnue,'15%','','',1,1,'right');
										$tdatos->td($imggua,'15%','','',1,1,'right');
										$tdatos->td("",'35%','','',1,1,'left');
									$tdatos->close_tr();
								$tdatos->close();
							}
						$fenvio->close();
					$encabeza->close();
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
  		  		window.location="http://carobra.ddns.me";
  		  	});
  		  	
  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

  		  	$("#contenido").css("overflow-y","auto");
  		  	$("#cerrar").css("cursor","pointer");

  		  	$("#noempleado").focus();
  		  	$("#nivel").focus();
  			$("#noempleado").focusout(function() 
  			{
  				if($("#noempleado").val() != "")
  				{
  			  		$("#contiene").val($('#fenvio').attr("action"));
  			  		$('#fenvio').attr("action","conector.php");
  		  			$('#fenvio').submit();
  				}
  			});
  			$('#noempleado').keydown(function(event) 
  			{
  				if (event.which == 13) 
  				{
  					if($("#noempleado").val() != "")
  					{
  	  			  		$("#contiene").val($('#fenvio').attr("action"));
  	  			  		$('#fenvio').attr("action","conector.php");
  	  		  			$('#fenvio').submit();
  	  		  		}
  					else
  						$("#nombre_emp").focus();
  				}
  			});

			$("#nombre_emp").autocomplete
			({
				source:'autocompleta.php?c=nombre_emp',minLength:3,
				select: function( event, ui )
				{
					$("#nombre_emp").val(ui.item.value);
		  			$("#contiene").val($('#fenvio').attr("action"));
  			  		$('#fenvio').attr("action","conector.php");
  			  		$('#fenvio').submit();
					return false;
				}
			});

	  		$("#fec_ingreso, #fec_factura, #fec_imss, #fec_nap").datepicker(
	  		{
	  			showOn: 'both',
	  		  	buttonImage: './img/calendario.png',
	  		  	buttonImageOnly: true,
	  		  	changeYear: false,
	  		  	numberOfMonths: 1,
	  		});

	  		$("#nuevo").click(function()
	  		{
				window.location="empleados.php";
	  		});

		$('#nivel').change(function()
		{
				if($("#nivel").val()==5)
					$("#td_agrega_nap").css("display","table-cell");
				else
					$("#td_agrega_nap").css("display","none");
			if($("#nivel").val()==1)
				$("#tranalista").css("display","table-row");
			else
				$("#tranalista").css("display","none");
		});

		$('#guardar').click(function()
		{
			$("#op").val(6);
		  	$("#contiene").val($('#fenvio').attr("action"));
			$('#fenvio').attr("action","conector.php");
		  	$('#fenvio').submit();
		});

		$('.boton_tabla').click(function()
		{
			var pasa=0;
			var obj_dul=this.id.substring(6);

			if((obj_dul=="nivel" && $("#nivel").val() != "") || (obj_dul=="ger" && $("#gerencia").val() != "") || (obj_dul=="nap" && $("#nap").val() != "") || (obj_dul=="banco" && $("#nombre_cta").val() != "" && $("#banco").val() != "" && $("#clabe").val() != "") || (obj_dul=="sc" && $("#brocker").val() != ""))
				pasa=1;

			if(pasa==1)
			{
				$pasa=0;
				if(obj_dul=="banco")
				{
					if($("#clabe").val().length==18)
						$pasa=1;
					else
					{
						alert("La CLABE debe de tener 18 digitos. La informacion bancaria no se guardara");
						$("#clabe").focus();
					}
				}
				else
					$pasa=1;

				if($pasa==1)
				{
					$("#op").val(2);
					$("#dul").val(obj_dul);
					$("#contiene").val($('#fenvio').attr("action"));
					$('#fenvio').attr("action","conector.php");
				  	$('#fenvio').submit();
				}
			}
			else
			{
				if(this.id.substring(0,6)=="borrar")
				{
					obj_dul=this.id.substring(6);
					$("#clabe").val($("#clabe" + this.id.substring(6)).text());
					$("#nombre_cta").val($("#nom_cta" + this.id.substring(6)).text());
					var fil= "\u00BFSeguro que quieres eliminar la cuenta bancaria " + $("#nombre_cta").val() + " con CLABE " + $("#clabe").val() + "?";  
					if(confirm(fil))
					{
						$("#op").val(4);
					  	$("#contiene").val($('#fenvio').attr("action"));
						$('#fenvio').attr("action","conector.php");
				  		$('#fenvio').submit();
					}
				}
			}
		});

			$('li').click(function()
			{
				var fil="";
				var obj_dul=$(this).parent().attr("id");

				if(obj_dul=="univel")
					fil= "\u00BFSeguro que deseas eliminar el nivel de " + $(this).text() + " de la configuraci\u00f3n de este empleado?";
				else
				{
					if(obj_dul=="ugerencias")
					{
					  	fil="\u00BFMarcar la gerencia de " + $(this).text() + " como gerencia base de este empleado?";
						if(confirm(fil))
						{		
							$("#op").val(5);
							$("#dul").val(obj_dul + "_base");
							$("#naps").val($(this).text());
						  	$("#contiene").val($('#fenvio').attr("action"));
							$('#fenvio').attr("action","conector.php");
					  		$('#fenvio').submit();
					  		fil="";
					  	}
					  	else
							fil= "\u00BFSeguro que deseas eliminar la gerencia de " + $(this).text() + " de la configuraci\u00f3n de este empleado?";
					}
					else
					{
						if(obj_dul=="ubrockers")
							fil= "\u00BFSeguro que deseas eliminar el Brocker de " + $(this).text() + " de la configuraci\u00f3n de este empleado?";
						else
							fil= "\u00BFSeguro que deseas eliminar el NAP " + $(this).text() + " de la configuraci\u00f3n de este empleado?";
					}
				}
				if(fil != "")
				{
					if(confirm(fil))
					{
						$("#op").val(5);
						$("#dul").val(obj_dul);
						$("#naps").val($(this).text());
					  	$("#contiene").val($('#fenvio').attr("action"));
						$('#fenvio').attr("action","conector.php");
				  		$('#fenvio').submit();
					}
				}
			});

			$('.act_cta').click(function()
			{
				$("#op").val(3);
				$("#clabe").val($("#clabe" + this.id.substring(7)).text());
			  	$("#contiene").val($('#fenvio').attr("action"));
				$('#fenvio').attr("action","conector.php");
		  		$('#fenvio').submit();
			});

			$("#btn-observa").click(function()
			{
			   	$.post("./ajax/obtener_dato.php",{ id: "observa", filtro1: $("#ne").text() }, function(response)
				{
					$("textarea#observa").text(response);
					$("#vis-info").css("display","block");
				});
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

        	$("#guarda_ob").hover(function()
        	{
        		if($(this).prop("src").substr(-5,1)==1)
        			$(this).prop("src","./img/bguardar2.png");
        		else
        			$(this).prop("src","./img/bguardar1.png");
        	});

        	$("#guarda_ob").click(function()
        	{
			   	$.post("./ajax/obtener_dato.php",{ id: "guarda_observa", filtro1: $("#ne").text(), filtro2: $("textarea#observa").val() }, function(response)
				{
					alert(response);
				});
        	});

        	$("#pwd").hover(function()
        	{	
        		if($(this).css("color")=='rgb(29, 67, 132)')
        			$(this).css("color","white");
        		else
        			$(this).css("color","#1D4384");
        	});

			$(".boton, .boton_tabla").hover(function()
			{
				if($(this).attr("src")==$(this).data("img1"))
					$(this).attr("src",$(this).data("img2"));
				else
					$(this).attr("src",$(this).data("img1"));
			});
		});
	</script>
</html>
