<?php
	include("../funciones/encripta.php");
	
	$id=addslashes(trim($_POST["id"]));
	$filtro1=addslashes(trim($_POST["filtro1"]));
	$filtro2=addslashes(trim($_POST["filtro2"]));

	$conecta= new conector();
	$conecta->consulta("SET NAMES utf8");

	switch($id)
	{
		case "usuario": $query="Select a.id_gerencia,gerencia from autoriza_recert a 
									inner join (select distinct id_gerencia,gerencia 
												from gerencias) g on (a.id_gerencia=g.id_gerencia) 
								where folio='$filtro2'";
						$bd_au=$conecta->consulta($query);
						if ($bd_au->num_rows != 0)
						{
							$tb_au=mysqli_fetch_array($bd_au);
							$bd_em=$conecta->consulta("select no_empleado from empleados e inner join (select clave from usuarios where nombre='$filtro1') u on (e.clave=u.clave)");
							$tb_em=mysqli_fetch_array($bd_em);
							echo $tb_au['id_gerencia'] . "|" . $tb_au['gerencia'] . "|" . $tb_em['no_empleado'];
						}
						else
						{
							$query=  "select ug.id_gerencia,gerencia,no_empleado from usuarios_gerencia ug inner join (select clave from usuarios where ";
							$query.= "nombre='$filtro1') u on (ug.clave=u.clave) inner join (select distinct id_gerencia,gerencia from gerencias) g on (";
							$query.= "ug.id_gerencia=g.id_gerencia) inner join empleados e on (ug.clave=e.clave)"; 
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
							{
								$tb=mysqli_fetch_array($bd);
								echo $tb['id_gerencia'] . "|" . $tb['gerencia'] . "|" . $tb['no_empleado'];
							}
							else
								echo "0|Sin gerencia";
						}
		break;
		case "folio_av": $query="Select cliente,monto,ifnull(nombre,'') as asesor,pago,no_empleado,nss,
									   fec_captura, fec_envio_banco, fec_liquida,fec_prim_dom, fec_acepta,pagado
								from (Select * from av_general where folio='$filtro1') avg
									left join (Select * from av_pagos where folio='$filtro1') avp on (avg.folio=avp.folio)
									left join (Select * from av_fechas where folio='$filtro1') avf on (avg.folio=avf.folio)
									left join empl_usuarios u on (avg.id_empleado=u.no_empleado)";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$fondo=0;
							$tb=mysqli_fetch_array($bd);
							if($tb['pagado']==3)
							{
								$folio=str_replace("AV-","", $filtro1);
								$query_a="Select ajuste from ajustes where motivo like '%$folio%'";
								$bd_a=$conecta->consulta($query_a);
								if ($bd_a->num_rows != 0)
									$fondo=4;
								else
									$fondo=3;
							}

							echo $tb['cliente'] . "|" . $tb['monto'] . "|" . $tb['asesor'] . "|" . $tb['pago'] . "|" . $tb['no_empleado'] . "|". $tb['fec_captura'] . "|" . $tb['fec_envio_banco'] . "|" . $tb['fec_liquida'] . "|" . $tb['fec_prim_dom'] . "|" . $tb['fec_acepta'] . "|";

							$nss=$tb['nss'];
							if($nss != "")
							{
								$query="select count(*) v from av where nss='$nss'";
								$bd=$conecta->consulta($query);
								$tb=mysqli_fetch_array($bd);
								echo $tb['v'] . "|" . $fondo;
							}
							else
								echo "|" . $fondo;
						}
						else
							echo "Este folio no existe";
		break;
		case "asesor_av": $query="select eu.no_empleado, id_gerencia
						  		  from (Select no_empleado from empl_usuarios where nombre='$filtro1') eu
						  		 	inner join (Select no_empleado,id_gerencia from usuarios_nivel_gerencia 
						  		 				where pertenece=1) ung on (eu.no_empleado=ung.no_empleado)";	 				
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$tb=mysqli_fetch_array($bd);
							echo $tb['no_empleado'] . "|" . $tb['id_gerencia'];
						}
						else
							echo "Este asesor no existe";
		break;
		case "asigna_av": $dato=explode("|",$filtro2);
						// pagado en estatus 3 significa Fondo de ahorro y el pago va en cero
						$mensaje= "Folio de APOVOL asignado con éxito";
						$pago=0;
						if($dato[1] > 0)
						{
							if($dato[1]==0)
							{
								$pago=1500;
								$dato[1]=0;
							}
							if($dato[1]==6)
								$pago=1300;
							$query="Update av_general set id_empleado=" . $dato[0] . ", tipo=" . $dato[1] . " where folio='$filtro1'";
							$conecta->consulta($query);
							$query="Update av_pagos set pago=$pago,fec_gen_pago='" . date("d/m/Y") . "' where folio='$filtro1'";
							$conecta->consulta($query);
							if($dato[1]==5 or $dato[1]==6)
							{
								$query="Delete comp_av where folio='$filtro1'";
								$conecta->consulta($query);
								if($dato[1]==5)
								{
									$query="Select no_empleado from empl_usuarios where nombre='" . $dato[2] . "'";
									$bd=$conecta->consulta($query);
									$tb=mysqli_fetch_array($bd);
									$no_empleado=$tb['no_empleado'];
									$query="Insert into comp_av values('$filtro1',$no_empleado,'')";	
								}
								else
									$query="Insert into comp_av values('$filtro1',0,'" .  $dato[2] . "')";
								$conecta->consulta($query);
							}
						}
						else
							$mensaje= "No se seleccionó el tipo de Apovol. No se asignó la apovol al asesor";
						echo $mensaje;
		break;
		case "d_cc": $bd=$conecta->consulta("Select no_empleado from empl_usuarios where nombre='$filtro1'");
					 $tb=mysqli_fetch_array($bd);
					 $no_empleado=$tb['no_empleado'];
					 $query="Delete from control_cambios where tipo='gerencia' and no_empleado=$no_empleado";
					 $conecta->consulta($query);
		break;
		case "descartar": $bd=$conecta->consulta("Insert into descartar_doble values('$filtro1')");
						  echo "listo";
		break;
		case "descontar": $datos= explode("|",$filtro1);
						  $query="Select id_gerencia, no_empleado, m30 
						  		  from calculo_pago where folio='" . $datos[0] . "'
						  		  union
						  		  Select id_gerencia, no_empleado, 1 
						  		  from respaldo_calculo_pago where folio='" . $datos[0] . "'";
						  $bd=$conecta->consulta($query);
					 	  $tb=mysqli_fetch_array($bd);
					 	  $m30==$tb['m30'];
					 	  $id_gerencia=$tb['id_gerencia'];
					 	  $no_empleado=$tb['no_empleado'];
						  if($datos[2] > 0)
						  {
						  	$query="Select clave from empl_usuarios where no_empleado=$no_empleado";
						  	$bd=$conecta->consulta($query);
					 	  	$tb=mysqli_fetch_array($bd);
					 	  	$clave=$tb['clave'];
					 	  	$datos[2]=$datos[2] * -1;
					 	  	$cc="CC" . (1000 + $id_gerencia);
					 	  	$motivo="CLIENTE NO ADMINISTRADO EN CARTERA PRINCIPAL PAGO CERTIFICACION " . $datos[6] . ". FOLIO " . $datos[0];
					 	  	$query="Insert into ajustes values('$clave','" . date("d/m/Y") . "',''," . $datos[2] . ",'$motivo',0,'$cc','PART1006',1,0,'')";
					 	  	$conecta->consulta($query);
						  }
						  if($datos[3] > 0)
						  {
						  	$query="Select clave from usuarios_nivel_gerencia 
						  			where id_gerencia=$id_gerencia and id_nivel=4 and pertenece=1";
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
							{
						 	  	$tb=mysqli_fetch_array($bd);
						 	  	$clave=$tb['clave'];
						 	  	$datos[3]=$datos[3] * -1;
						 	  	$cc="CC" . (1000 + $id_gerencia);
						 	  	$motivo="CLIENTE NO ADMINISTRADO EN CARTERA PRINCIPAL PAGO CERTIFICACION " . $datos[6] . " FOLIO " . $datos[0];
						 	  	$query="Insert into ajustes values('$clave','" . date("d/m/Y") . "',''," . $datos[3] . ",'$motivo',0,'$cc','PART1007',1,0,'')";
						 	  	$conecta->consulta($query);
						 	}
						  }
						  if($datos[4] > 0)
						  {
						  	$query="Select clave from usuarios_nivel_gerencia 
						  			where id_gerencia=$id_gerencia and id_nivel=5 and pertenece=1";
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
							{
						 	  	$tb=mysqli_fetch_array($bd);
						 	  	$clave=$tb['clave'];
						 	  	$datos[4]=($datos[4] - $datos[3] - $datos[2]) * -1;
						 	  	$cc="CC" . (1000 + $id_gerencia);
						 	  	$motivo="CLIENTE NO ADMINISTRADO EN CARTERA PRINCIPAL PAGO CERTIFICACION " . $datos[6] . " FOLIO " . $datos[0];
						 	  	$query="Insert into ajustes values('$clave','" . date("d/m/Y") . "',''," . $datos[4] . ",'$motivo',0,'$cc','PART1008',1,0,'')";
						 	  	$conecta->consulta($query);
						 	}
						  }
						  if($datos[5] > 0)
						  {
						  	$query="Select clave from usuarios_nivel_gerencia 
						  			where id_gerencia=$id_gerencia and id_nivel=13 and pertenece=1";
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
							{
						 	  	$tb=mysqli_fetch_array($bd);
						 	  	$clave=$tb['clave'];
						 	  	$datos[5]=($datos[5] - $datos[4]) * -1;
						 	  	$cc="CC" . (1000 + $id_gerencia);
						 	  	$motivo="CLIENTE NO ADMINISTRADO EN CARTERA PRINCIPAL PAGO CERTIFICACION " . $datos[6] . " FOLIO " . $datos[0];
						 	  	$query="Insert into ajustes values('$clave','" . date("d/m/Y") . "',''," . $datos[5] . ",'$motivo',0,'$cc','PART1008',1,0,'')";
						 	  	$conecta->consulta($query);
						 	}
						  }
						 echo "Ajustes de descuento generados";
		break;
		case "folio_cart":  $datos=explode("|", $filtro2);
							$servicio=$datos[0];
							$nivel=$datos[1];

							if($servicio==3)
							{
								$d=explode("/",$filtro1);
								$f=trim($d[0]);
								$t=trim($d[1]);
								$query="Select nss,curp,cliente as trabajador, email as email_trab, cel as celular,tel as telefono
										from av_general where tramite='" . $t . "' and folio='" . $f . "'";
							}
							else
							{
								if($servicio==1)
									$fol="folio_sl";
								else
									$fol="folio_rc";
								$query="Select nss,curp,cliente as trabajador, email as email_trab, cel as celular,tel as telefono
										from det_otros_servicios where " . $fol . "='$filtro1'";
							}
						   if($servicio==1)
						   {
							   $bd=$conecta->consulta($query);
							   if ($bd->num_rows == 0)
							   {
								   $query="Select nss,curp,trabajador,email_trab,celular,telefono
										   from (Select folio,nss from produccion where folio='$filtro1') p
										   		inner join (Select * from prod_trabajador 
										   					where folio='$filtro1') pt on (p.folio=pt.folio)
										   union
										   Select nss,curp,trabajador,email_trab,celular,telefono
										   from (Select folio,nss from produccion_2018 where folio='$filtro1') p
										   		inner join (Select * from prod_trabajador_2018 
										   					where folio='$filtro1') pt on (p.folio=pt.folio)
										   union
										   Select nss,curp,trabajador,email_trab,celular,telefono
										   from (Select folio,nss from produccion_2017 where folio='$filtro1') p
										   		inner join (Select * from prod_trabajador_2017 
										   					where folio='$filtro1') pt on (p.folio=pt.folio)
										   union
										   Select nss,curp,trabajador,email_trab,celular,telefono
										   from (Select folio,nss from respaldo_produccion where folio='$filtro1') p
										   		inner join (Select * from respaldo_prod_trabajador 
										   					where folio='$filtro1') pt on (p.folio=pt.folio)";
							   }
						   }
						   $bd=$conecta->consulta($query);
						   if ($bd->num_rows != 0)
						   {
						   		$tb=mysqli_fetch_array($bd);
						   		$cad=$tb["nss"] . "|" . $tb["curp"] . "|" . $tb["trabajador"] . "|" . $tb["email_trab"] . "|" . $tb["celular"] . "|" . $tb["telefono"];

						   		if($servicio<2)
							   		$query="Select folio,os.no_empleado,fec_alta,servicio,pagado,
							   					   if(os.nap='','',concat(os.nap,' (',nombre,')')) as nap
							   				from (Select * from otros_servicios where folio='$filtro1' and servicio=$servicio) os
							   					left join naps n on (os.nap=n.nap)
							   					left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
							   	else
							   		$query="Select avg.tramite,id_empleado as no_empleado,fec_alta,3,pagado,
							   					   if(avg.nap='','',concat(avg.nap,' (',nombre,')')) as nap
											from (Select * from av_general where tramite='" . $t . "' and folio='" . $f . "') avg
												left join (Select tramite,fec_captura as fec_alta from av_fechas 
														   where tramite='" . $t . "' and folio='" . $f . "') avf 
														on (avg.tramite=avf.tramite)
												left join (Select tramite,pagado from av_pagos 
														   where tramite='" . $t . "' and folio='" . $f . "') avp 
														on (avg.tramite=avp.tramite)
												left join naps n on (avg.nap=n.nap)
												left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
						   		$bd=$conecta->consulta($query);
							    if ($bd->num_rows != 0)
							    {
							   		$tb=mysqli_fetch_array($bd);
							   		if($tb["pagado"]==0 or $tb["pagado"]==2 or $nivel==12)
							   		{
								   	   $cad.="|" . $tb["fec_alta"] . "|" . $tb["servicio"] . "|" . $tb["nap"] . "|";

								   		$query="Select nombre, gerencia
								   				from (Select * from empl_usuarios 
								   					  where no_empleado=" . $tb['no_empleado']  . ") eu
								   					inner join (Select distinct no_empleado,id_gerencia
								   								from usuarios_nivel_gerencia) ung 
								   							on (eu.no_empleado=ung.no_empleado)
								   					inner join (Select distinct id_gerencia,gerencia
								   								from gerencias) g on (ung.id_gerencia=g.id_gerencia)";
								   		$bd=$conecta->consulta($query);
								   		$tb=mysqli_fetch_array($bd);
								   		$cad.=$tb["nombre"] . "|" . $tb["gerencia"];
								   		if($servicio==3)
								   		{
								   			$query="Select tipo,dia_cobro,monto,nombre,clabe,banco
													from (Select * from av_pagos where tramite='" . $t . "' and folio='" . $f . "') avp
														left join (Select * from av_general 
																   where tramite='" . $t . "' and folio='" . $f . "') avg 
																on (avp.tramite=avg.tramite)
														left join empl_usuarios eu on (avg.acompaniante=eu.no_empleado)";
									   		$bd=$conecta->consulta($query);
										    if ($bd->num_rows != 0)
										    {
										    	$tb=mysqli_fetch_array($bd);
										    	$cad.="|" . $tb['tipo'] . "|" . $tb['dia_cobro'] . "|" . $tb['monto'] . "|" . $tb['nombre'] . "|" . $tb['clabe'] . "|" . $tb['banco'];
										    }
										    else
										    	$cad.="||||||";
										}
								   	}
								   	else
								   	{
								   		$cad="";
								   		echo "Este folio fue pagado con anterioridad. Ya no puede ser modificado";
								   	}
							    }
							    else
							    {
							   		$cad.="|||||";
							   		if($servicio==3)
							   			$cad.="||||||";
							    }
							   	echo $cad;
						   }
						   else
						   {
						   		if($servicio < 3)
									echo "|||||||||";
								else
									echo "|||||||||||||||";
						   }
		break;
		case "nss_cart":   $datos=explode("|", $filtro2);
							$servicio=$datos[0];
							$nivel=$datos[1];
							if($servicio==3)
							{
								$fol='folio';
							   	$query="Select tramite as folio,nss,curp,cliente as trabajador,
							   				   email as email_trab,cel as celular,tel as telefono
									   from av_general where nss='$filtro1'";
							}
							else
							{
								if($servicio==1)
									$fol="folio_sl";
								else
									$fol="folio_rc";
							   $query="Select " . $fol . ",nss,curp,cliente as trabajador, email as email_trab, 
							   					   cel as celular,tel as telefono
									   from det_otros_servicios where nss='$filtro1'";
							}
						   $folio='';
						   $bd=$conecta->consulta($query);
						   if ($bd->num_rows != 0)
						   {
						   		$tb=mysqli_fetch_array($bd);
						   		$nss=$tb["nss"];
						   		$folio=$tb[$fol];
						   		$cad= $tb[$fol] . "|" . $tb["curp"] . "|" . $tb["trabajador"] . "|" . $tb["email_trab"] . "|" . $tb["celular"] . "|" . $tb["telefono"];
						   }
						   else
						   {
						   		if($servicio==1)
						   		{
									$query= "Select *
											 from (Select p.folio,fec_traspaso,curp,trabajador,
											 			  email_trab,celular,telefono	
											       from (Select folio,fec_traspaso from produccion 
											       		 where nss='$filtro1') p
											   			inner join prod_trabajador pt on (p.folio=pt.folio)
												   union
												   Select p.folio,fec_traspaso,curp,trabajador,
												   		  email_trab,celular,telefono
												   from (Select folio,fec_traspaso from produccion_2018 
												   		 where nss='$filtro1') p
												   		inner join prod_trabajador_2018 pt on (p.folio=pt.folio)
												   union
												   Select p.folio,fec_traspaso,curp,trabajador,
												   		  email_trab,celular,telefono
												   from (Select folio,fec_traspaso from produccion_2017 
												   		 where nss='$filtro1') p
												   		inner join prod_trabajador_2017 pt on (p.folio=pt.folio)
												   union
												   Select p.folio,fec_traspaso,curp,trabajador,
												   		  email_trab,celular,telefono
												   from (Select folio,fec_traspaso from respaldo_produccion 
												   		 where nss='$filtro1') p
												   		inner join respaldo_prod_trabajador pt on (p.folio=pt.folio)) d
											  order by str_to_date(fec_traspaso,'%d/%m/%Y') desc limit 0,1";
								   $bd=$conecta->consulta($query);
								   if ($bd->num_rows != 0)
								   {
								   		$tb=mysqli_fetch_array($bd);
								   		$nss=$tb["nss"];
								   		$folio=$tb["folio"];
								   		$cad= $tb["folio"] . "_|" . $tb["curp"] . "|" . $tb["trabajador"] . "|" . $tb["email_trab"] . "|" . $tb["celular"] . "|" . $tb["telefono"];
								   }
								   else
								   		$cad="|||||";
								}
								else
									$cad="|||||";
						   }
						   if($folio != "")
						   {
						   		if($servicio<3)
						   			$query="Select folio,os.no_empleado,fec_alta,servicio,pagado,
							   					   if(os.nap='','',concat(os.nap,' (',nombre,')')) as nap,'' as estatus
							   				from (Select os.*
								   				  from (Select * from otros_servicios where servicio=$servicio) os
															inner join (Select " . $fol . " from det_otros_servicios 
																		where " . $fol . "='$folio') d 
																	on (os.folio=d." . $fol . ")) os
							   					left join naps n on (os.nap=n.nap)
							   					left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
							   	else
						   			$query="Select avg.tramite,id_empleado as no_empleado,fec_alta,3,pagado,
							   					   if(avg.nap='','',concat(avg.nap,' (',nombre,')')) as nap,estatus
							   				from (Select * from av_general where tramite='$folio') avg
							   					left join (Select * from av_pagos where tramite='$folio') avp on (avg.tramite=avp.tramite)
							   					left join (Select tramite,fec_captura as fec_alta from av_fechas 
							   							   where tramite='$folio') avf on (avg.tramite=avf.tramite)
							   					left join naps n on (avg.nap=n.nap)
							   					left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
						   		$bd=$conecta->consulta($query);
							    if ($bd->num_rows != 0)
							    {
							   		$tb=mysqli_fetch_array($bd);
							   		if($tb["pagado"]==0 or $tb["pagado"]==2 or $nivel==12)
							   		{
								   	    $cad.="|" . $tb["fec_alta"] . "|" . $tb["servicio"] . "|" . $tb["nap"] . "|";
								   	    $estatus=$tb['estatus'];
								   		$query="Select nombre, gerencia
								   				from (Select * from empl_usuarios 
								   					  where no_empleado=" . $tb['no_empleado']  . ") eu
								   					inner join (Select distinct no_empleado,id_gerencia
								   								from usuarios_nivel_gerencia) ung 
								   							on (eu.no_empleado=ung.no_empleado)
								   					inner join (Select distinct id_gerencia,gerencia
								   								from gerencias) g on (ung.id_gerencia=g.id_gerencia)";
								   		$bd=$conecta->consulta($query);
								   		$tb=mysqli_fetch_array($bd);
								   		$cad.=$tb["nombre"] . "|" . $tb["gerencia"];
								   		if($servicio==3)
								   		{
								   			$query="Select tipo,dia_cobro,monto,nombre,clabe,banco
													from (Select * from av_pagos where tramite='$folio') avp
														left join (Select * from av_general 
																   where tramite='$folio') avg on (avp.tramite=avg.tramite)
														left join empl_usuarios eu on (avg.acompaniante=eu.no_empleado)";
									   		$bd=$conecta->consulta($query);
										    if ($bd->num_rows != 0)
										    {
										    	$tb=mysqli_fetch_array($bd);
										    	$cad.="|" . $tb['tipo'] . "|" . $tb['dia_cobro'] . "|" . $tb['monto'] . "|" . $tb['nombre'] . "|" . $tb['clabe'] . "|" . $tb['banco'] . "|" . $estatus;
										    }
										    else
										    	$cad.="|||||||";
								   		}
								   	}
								   	else
								   	{
								   		$cad="";
								   		echo "Este folio de este NSS fue pagado con anterioridad. Ya no puede ser modificado";
								   	}
							    }
							    else
							    {
							    	if($servicio==3)
							    		$cad.="||||||||||||";
								   		
								   	else
							    		$cad.="||||||";
							    }
						   }
						   else
						   {
						   		if($servicio<3)
						   			$cad.="||||||";
						   		else
						   			$cad.="||||||||||||";
						   }
						   echo $cad;
		break;
		case "curp_cart":  $datos=explode("|", $filtro2);
							$servicio=$datos[0];
							$nivel=$datos[1];
							if($servicio==3)
							{
								$fol='folio';
							   	$query="Select tramite as folio,nss,cliente as trabajador,
							   				   email as email_trab,cel as celular,tel as telefono
									   from av_general where curp='$filtro1'";
							}
							else
							{
								if($servicio==1)
									$fol="folio_sl";
								else
									$fol="folio_rc";
							   $query="Select " . $fol . ",nss,cliente as trabajador, email as email_trab, 
							   					   cel as celular,tel as telefono
									   from det_otros_servicios where curp='$filtro1'";
							}
						   $folio='';
						   $bd=$conecta->consulta($query);
						   if ($bd->num_rows != 0)
						   {
						   		$tb=mysqli_fetch_array($bd);
						   		$nss=$tb["nss"];
						   		$folio=$tb[$fol];
						   		$cad= $tb[$fol] . "|" . $tb["nss"] . "|" . $tb["trabajador"] . "|" . $tb["email_trab"] . "|" . $tb["celular"] . "|" . $tb["telefono"];
						   }
						   else
						   {
						   		if($servicio==1)
						   		{
									$query= "Select *
											 from (Select p.folio,fec_traspaso,nss,trabajador,
											 			  email_trab,celular,telefono		
											       from (Select * from prod_trabajador where curp='$filtro1') pt
											       		inner join produccion  p on (pt.folio=p.folio)
												   union
												   Select p.folio,fec_traspaso,nss,trabajador,
												   		  email_trab,celular,telefono
											       from (Select * from prod_trabajador_2018 
											       		 where curp='$filtro1') pt
											       		inner join produccion_2018  p on (pt.folio=p.folio)
												   union
												   Select p.folio,fec_traspaso,nss,trabajador,
												   		  email_trab,celular,telefono
											       from (Select * from prod_trabajador_2017 
											       		  where curp='$filtro1') pt
											       		inner join produccion_2017  p on (pt.folio=p.folio)
												   union
												   Select p.folio,fec_traspaso,nss,trabajador,
												   		  email_trab,celular,telefono
											       from (Select * from respaldo_prod_trabajador 
											       		 where curp='$filtro1') pt
											       		inner join respaldo_produccion  p on (pt.folio=p.folio)) d
											  order by str_to_date(fec_traspaso,'%d/%m/%Y') desc limit 0,1";
								   $bd=$conecta->consulta($query);
								   if ($bd->num_rows != 0)
								   {
								   		$tb=mysqli_fetch_array($bd);
								   		$nss=$tb["nss"];
								   		$folio=$tb["folio"];
								   		$cad= $tb["folio"] . "_|" . $tb["nss"] . "|" . $tb["trabajador"] . "|" . $tb["email_trab"] . "|" . $tb["celular"] . "|" . $tb["telefono"];
								   }
								   else
								   		$cad="|||||";
								}
								else
							    {
							    	if($servicio==3)
							    		$cad.="|||||||||||";
								   		
								   	else
							    		$cad.="|||||";
							    }
						   }
						   if($folio != "")
						   {
						   		if($servicio<3)
						   			$query="Select folio,os.no_empleado,fec_alta,servicio,pagado,
							   					   if(os.nap='','',concat(os.nap,' (',nombre,')')) as nap, '' as estatus
							   				from (Select os.*
								   				  from (Select * from otros_servicios where servicio=$servicio) os
															inner join (Select " . $fol . " from det_otros_servicios 
																		where " . $fol . "='$folio') d 
																	on (os.folio=d." . $fol . ")) os
							   					left join naps n on (os.nap=n.nap)
							   					left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
							   	else
						   			$query="Select avg.tramite,id_empleado as no_empleado,fec_alta,3,pagado,
							   					   if(avg.nap='','',concat(avg.nap,' (',nombre,')')) as nap,estatus
							   				from (Select * from av_general where tramite='$folio') avg
							   					left join (Select * from av_pagos 
							   							   where tramite='$folio') avp on (avg.tramite=avp.tramite)
							   					left join (Select tramite,fec_captura as fec_alta from av_fechas 
							   							   where tramite='$folio') avf on (avg.tramite=avf.tramite)
							   					left join naps n on (avg.nap=n.nap)
							   					left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
						   		$bd=$conecta->consulta($query);
							    if ($bd->num_rows != 0)
							    {
							   		$tb=mysqli_fetch_array($bd);
							   		if($tb["pagado"]==0 or $tb["pagado"]==2 or $nivel==12)
							   		{
								   	    $cad.="|" . $tb["fec_alta"] . "|" . $tb["servicio"] . "|" . $tb["nap"] . "|";
								   	    $estatus=$tb['estatus'];
								   		$query="Select nombre, gerencia
								   				from (Select * from empl_usuarios 
								   					  where no_empleado=" . $tb['no_empleado']  . ") eu
								   					inner join (Select distinct no_empleado,id_gerencia
								   								from usuarios_nivel_gerencia) ung 
								   							on (eu.no_empleado=ung.no_empleado)
								   					inner join (Select distinct id_gerencia,gerencia
								   								from gerencias) g on (ung.id_gerencia=g.id_gerencia)";
								   		$bd=$conecta->consulta($query);
								   		$tb=mysqli_fetch_array($bd);
								   		$cad.=$tb["nombre"] . "|" . $tb["gerencia"];
								   		if($servicio==3)
								   		{
								   			$query="Select tipo,dia_cobro,monto,nombre,clabe,banco
													from (Select * from av_pagos where tramite='$folio') avp
														left join (Select * from av_general 
																   where tramite='$folio') avg on (avp.tramite=avg.tramite)
														left join empl_usuarios eu on (avg.acompaniante=eu.no_empleado)";
									   		$bd=$conecta->consulta($query);
										    if ($bd->num_rows != 0)
										    {
										    	$tb=mysqli_fetch_array($bd);
										    	$cad.="|" . $tb['tipo'] . "|" . $tb['dia_cobro'] . "|" . $tb['monto'] . "|" . $tb['nombre'] . "|" . $tb['clabe'] . "|" . $tb['banco'] . "|" . $estatus;
										    }
										    else
										    	$cad.="|||||||";
								   		}
								   	}
								   	else
								   	{
								   		$cad="";
								   		echo "El folio con esta CURP fue pagado con anterioridad. Ya no puede ser modificado";
								   	}
							    }
							    else
							    	$cad.="||||||";

						   }
						   else
						   {
						   		if($servicio<3)
						   			$cad.="||||||";
						   		else
						   			$cad.="||||||||||||";
						   }
						   echo $cad;
		break;
		case "asesor_cart": $query="Select if(sc='',gerencia,concat(gerencia,' / ',sc)) as gerencia, e.celular AS telefono, u.email
										from (Select no_empleado, clave from carobra_2014.empl_usuarios where nombre='$filtro1') eu
										inner join (Select distinct no_empleado,id_gerencia 
										from carobra_2014.usuarios_nivel_gerencia 
										where pertenece=1) ung on (eu.no_empleado=ung.no_empleado)
										inner join (Select distinct id_gerencia,gerencia 
										from carobra_2014.gerencias) g on (ung.id_gerencia=g.id_gerencia)
										inner join carobra_2014.estructura_gerencia eg on (ung.id_gerencia=eg.id_gerencia)
										INNER JOIN carobra.empleados e ON eu.no_empleado = e.no_empleado
										INNER JOIN carobra.usuarios u ON eu.clave = u.clave";
						   $bd=$conecta->consulta($query);
						   if ($bd->num_rows != 0)
						   {
						   		$tb=mysqli_fetch_array($bd);
						   		echo $tb["gerencia"]."|".$tb["telefono"]."|".$tb["email"];
						   }
						   else
							   	echo "Asesor no encontrado";
		break;
		case "nap_baja": $naps=explode(" ", $filtro1);
						 $bd=$conecta->consulta("Select baja from naps where nap='" . $naps[0] . "'");
						 $tb=mysqli_fetch_array($bd);
						 echo $tb["baja"];
		break;
		case "empl_nap": $query="Select clave,ifnull(nap,'') as nap,nf
						 		 from (Select clave,no_empleado, nf from empl_usuarios where nombre='$filtro1') e
						 		 	left join naps n on (e.no_empleado=n.no_empleado)";

						 $bd=$conecta->consulta($query);
						 if ($bd->num_rows != 0)
						 {
						 	$tb=mysqli_fetch_array($bd);
						 	$d=explode("|", $filtro2);
						 	$fec=explode("/", $d[0]);
						 	if($tb["nap"] != $d[1])
						 	{
						 		echo $tb["nap"] . "|" . $tb["nf"] . "|0|n|0";
						 	}
						 	else
						 	{
							 	echo $tb["nap"] . "|" . $tb["nf"];
							 	$clave=$tb["clave"];
								$query="Select count(*) as v from acompaniamiento a
										inner join (Select folio from produccion 
													where nap='" . $d[1] . "' 
														and fec_traspaso like '%/" . $fec[1] . "/" . $fec[2] . "%') p 
												on (a.folio=p.folio)";
								$bd=$conecta->consulta($query);
								$tb=mysqli_fetch_array($bd);
								echo "|" . $tb['v'];

								$query="select * from insuficiencias i
											inner join (SELECT * FROM usuarios_nivel_gerencia WHERE clave='$clave') ung 
													on (i.clave=ung.clave)
										where fec_pago='' and ajuste < -200";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
									echo "|negativo|1";
								else
									echo "|positivo|1";
							}
						 }
						 else
						 	echo "";
		break;
		case "observa": $query="Select observa from empl_observa where no_empleado=$filtro1";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$tb=mysqli_fetch_array($bd);
						 	echo $tb["observa"];
						}
						else
						 	echo "";
		break;
		case "guarda_observa":  $query="Select no_empleado, observa from empl_observa where no_empleado=$filtro1";
								$bd=$conecta->consulta($query);
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									if($filtro2==$tb["observa"])
										$observa=$filtro2;
									else
									{
										if(strpos($filtro2, $tb["observa"]) != false)
											$observa=$filtro2;
										else
											$observa=$tb["observa"] . " " . str_replace($tb["observa"],"", $filtro2);
									}
									$query="Update empl_observa set observa='$observa' where no_empleado=$filtro1";
								}
								else
								 	$query="insert into empl_observa values($filtro1,'$filtro2')";
								$conecta->consulta($query);
								echo "Observaciones guardadas con éxito";
		break;
		case "chec_actividad":  $query="Select * from control_act where clave='$filtro1' and fecha='$filtro2'";
							   	$bd=$conecta->consulta($query);					   	
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									$fec=explode("/", $tb['fecha']);
									$fecha = $fec[2] . "-" . $fec[1] . "-" . $fec[0];

									$date1 = new DateTime($fecha);
									$date2 = new DateTime(date("Y-m-d"));
									$diff = $date1->diff($date2);
									if($diff->days<=2)
									{
										echo $tb['salida'] . "|" . $tb['llamadas'] . "|" . $tb['citas'] . "|" . $tb['canceladas'] . "|" . $tb['cierres'] . "|" . $tb['falta_FCT'] . "|" . $tb['traspasos'] . "|" . $tb['lug_brocker'] . "|" . $tb['lug_asesor'] . "|" . $tb['lug_cliente'] . "|" . $tb['lug_publico'] . "|" . $tb['lug_otro'] . "|" . $tb['saldo_dia'];
									}
									else
										echo "Ya hubo una actividad previa capturada para este día. Esta actividad ya no se puede modificar por que han pasado más de dos días";


								}
								else
									echo "";
		break;
		case "guarda_actividad": $d=explode("|", $filtro1);
								 $t=explode("|", $filtro2);
								 $i=0;
								 while($i < count($t))
								 {
								 	if($t[$i]=="")
								 		$t[$i]=0;
								 	$i++;
								 }

								 $t[12]= str_replace(",","",str_replace("$","",$t[12]));
								 $query="Select * from control_act where clave='" . $d[0] . "' and fecha='" . $d[1] . "'";
								 $bd=$conecta->consulta($query);
								 if ($bd->num_rows != 0)
									$query="Update control_act set salida='" . $t[0] . "', citas=" . $t[1] . ", canceladas=" . $t[3] . ", cierres=" . $t[4] . ", falta_FCT=" . $t[5] . ", traspasos=" . $t[6] . ", saldo_dia=" . $t[12] . ", llamadas=" . $t[2] . ", lug_brocker=" . $t[7] . ", lug_asesor=" . $t[8] . ", lug_cliente=" . $t[9] . ", lug_publico=" . $t[10] . ", lug_otro=" . $t[11] . " where clave='" . $d[0] . "' and fecha='" . $d[1] . "'";
								 else
								 	$query="Insert into control_act values('" . $d[0] . "','" . $d[1] . "','" . $t[0] . "'," . $t[1] . "," . $t[2] . "," . $t[3] . "," . $t[4] . "," . $t[5] . "," . $t[6] . "," . $t[7] . "," . $t[8] . "," . $t[9] . "," . $t[10] . "," . $t[11] . "," . $t[12] . ")";
								 $conecta->consulta($query);
								 echo "Actividad guardada con éxito";
		break;
		case "sol_anticipo":
			$fec_act10= date ( 'Y-m-j' ,strtotime ( '+10 day' , strtotime ( date("Y-n-j"))));
			$fec_ant_sell= date ( 'Y-m-j' ,strtotime ( '-912 day' , strtotime ( date("Y-n-j"))));

			$query="Select if(datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-03-01')>=0,1,0) as p
					from produccion where folio='$filtro1'";

			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$pasa=$tb["p"];

			if($pasa==1)
			{
				$query="Select distinct anio,mes
						from (Select * from produccion where folio='$filtro1') p
							inner join fechas_cierre_com fc on (datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
									and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0)";

				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);

				$mes=$tb["mes"];
				$anio=$tb["anio"];

				if($anio < 2023)
					$por_incentivo=0.001500;
				else
					$por_incentivo=0;

				$query="select * 
						from (Select p.nap, count(*) v 
							  from produccion p
								inner join (Select * from produccion where folio='6400084251') pf on (p.nap=pf.nap)
								inner join (Select * from fechas_cierre_com 
											where anio=$anio and mes=$mes) fe 
										on (str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date(fec_ini,'%d/%m/%Y') 
												and str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date(fec_fin,'%d/%m/%Y'))	 
							  where p.id_estatus_prod in (1,5,15,25,27,28) and p.id_tipo_mov=1
							  group by p.nap) d 
						where v > if($mes=8 or $mes=9 or $mes=10,36,18)";

				$bd=$conecta->consulta($query);
				if ($bd->num_rows == 0)
				{
					$query="Select distinct (if(edad >=60,0,saldo_estimado) * porcentaje * por_anticipo) * por_av as anticipo,
								   edad,ifnull(folio_av,'') as folio_av,estatus,if(edad >=60,0,saldo_estimado) as saldo_estimado
							from (Select p.folio, fec_traspaso,ap.estatus,e.clave,
								  		 if(saldo_estimado>=if(mt=8,1500000,1200000),if(mt=8,1500000,1200000),if(saldo_estimado >10000,saldo_estimado,0)) as saldo_estimado,folio_av,
								  		ifnull(if(ifnull(folio_av,'') = '',if(ifnull(pf.folio,'')='',if(d < 0,0.7,0),1),if(d < 0,1,if(datediff(concat(substr(fec_envio,-4),'-',substr(fec_envio,4,2),'-',substr(fec_envio,1,2)),concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2))) <= 30,1,0))),0) as por_av,
								  		 if(unb.n_b=5,unb.n_b,if(unb.n_b=13,unb.n_b,n_a)) as nivel, 
								 	 TIMESTAMPDIFF(YEAR,concat(anio_nac,'-',mes_nac,'-',dia_nac),'$fec_act10') AS edad
								  from (select prod.folio,no_empleado,
								  			   if(id_tipo_mov=1,saldo_estimado,0) as saldo_estimado,
								  				fec_traspaso,
												if(mid(curp,5,2)*1 >= 5,1900 + (mid(curp,5,2)*1),2000 + (mid(curp,5,2)*1)) as anio_nac, 
												mid(curp,7,2) as mes_nac, mid(curp,9,2) as dia_nac,
												datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-05-21') as d,
												if(datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-07-31') >=0 and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-10-13') <=0,8,0) as mt
										from (Select folio, id_tipo_mov,fec_traspaso,p.no_empleado,
													 id_estatus_prod,
													 if(left(nss,1)='I',if(saldo_estimado>750000,750000,saldo_estimado),saldo_estimado) as saldo_estimado
											  from (Select * from produccion where folio='$filtro1'
														and id_estatus_prod in (3,5,15,32)) p
												inner join naps n on (p.nap=n.nap)) prod 
											inner join prod_trabajador pt on (prod.folio=pt.folio)
											left join (Select * from pagos_folios_sin_av 
													   where tipo_autoriza=2 or tipo_autoriza=3) pf 
													on (prod.folio=pf.folio)
										where id_estatus_prod != 32 or ifnull(pf.folio,'') !='') p 
								  	left join (Select * from pagos_folios_sin_av 
											   where tipo_autoriza=1 or tipo_autoriza=3) pf 
											on (p.folio=pf.folio)
									inner join empleados e on (p.no_empleado=e.no_empleado)
									left join (select clave,id_nivel as n_a from usuarios_nivel 
											   where id_nivel=3 or id_nivel=7) una on (e.clave=una.clave)
									left join (Select clave,max(id_nivel) as n_b 
											   from (Select distinct clave,id_nivel from usuarios_nivel_gerencia
											   		 where id_nivel=5
									  				 union
									  				 Select distinct clave,id_nivel from usuarios_nivel_gerencia
									  				 where id_nivel=13) n
												   group by clave) unb on (e.clave=unb.clave)
									left join (Select folio_pr,avp.estatus, avp.folio as folio_av,fec_envio
											   from apovol_prod ap 
											   		inner join av_pagos avp on (ap.folio_av=avp.tramite)
											   		inner join av_fechas avf on (ap.folio_av=avf.tramite)
											   where avp.estatus !='ENVIADO' and avp.estatus !='RECHAZADO' 
											   		and avp.estatus !='Cancelada' 
											   		and avp.estatus !='En Captura'
											   		and avp.estatus !='Rechazada') ap 
											on (p.folio=ap.folio_pr)) p 
								inner join usuarios_comision uc on (p.clave=uc.clave)
								inner join usuarios_nivel uni on (p.clave=uni.clave)
								inner join por_anticipos pa on (p.clave=pa.clave)
								inner join (Select * from tabla_comision where activa=1 and periodo=1) tc
										on (uc.id_tabla_comision=tc.id_tabla_comision and tc.nivel_usuario=if(p.nivel=7,3,p.nivel) 
												and tc.nom_tabla_comision like '%2021-II%')
								inner join tabla_bonos tb on (tc.id_tabla_comision=tb.id_tabla_comision) 
								where activo=1 
									and (saldo_estimado>=rango_inf and saldo_estimado<=rango_sup)";
					$bd=$conecta->consulta($query);
					if ($bd->num_rows == 0)
					{
						$query="Select distinct folio,if(edad>= 60,0,saldo_estimado) as saldo_estimado,
									   edad,'' as folio_av,'' as estatus,
									   if(edad>= 60,0,saldo_estimado) * if(str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('31/07/2023','%d/%m/%Y') and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('31/01/2024','%d/%m/%Y'),por_anticipo * porcentaje,0.005) as anticipo
								from (Select distinct p.folio,saldo_estimado,p.clave,fec_traspaso,edad,nivel
									  from (Select prod.folio,prod.no_empleado,fec_traspaso,nap,
											 	   if(saldo_estimado>=if(mt=8,1500000,1200000),if(mt=8,1500000,1200000),if(saldo_estimado >10000,saldo_estimado,0)) as saldo_estimado,
										 		   TIMESTAMPDIFF(YEAR,concat(anio_nac,'-',mes_nac,'-',dia_nac),'$fec_act10') AS edad,
										 		   e.clave,if(unb.n_b=5,unb.n_b,if(unb.n_b=13,unb.n_b,n_a)) as nivel 
								  		  	from (Select p.folio,fec_traspaso, no_empleado,id_estatus_prod,p.nap,
								  		  			     if(datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-07-31') >=0 and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-10-13') <=0,8,0) as mt,
											   		     if(left(nss,1)='I',if(saldo_estimado>750000,750000,saldo_estimado),saldo_estimado) as saldo_estimado
												  from (Select * from produccion where folio='$filtro1'
												  			and id_estatus_prod in (3,5,15,32)) p
														inner join fechas_cierre_com fc 
																on (datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
																	and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0)
														left join tempo_nap_folio_mes tp 
																on (p.nap=tp.nap and fc.anio=tp.anio and fc.mes=tp.mes)
														left join (Select * from pagos_folios_sin_av 
																   where tipo_autoriza=2 or tipo_autoriza=3) pf 
																on (p.folio=pf.folio)
												  where id_tipo_mov=1 and cierre_cap=1 and id_estatus_anticipo=0 
												  	and saldo_estimado > 0 
													and str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('01/06/2022','%d/%m/%Y') 
													and ifnull(tp.nap,'')='' and id_estatus_prod != 32 or ifnull(pf.folio,'') !=''
												  union 
											  	  Select p.folio,fec_traspaso, no_empleado,id_estatus_prod,p.nap,
											  		     if(datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-07-31') >=0 and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2023-10-13') <=0,8,0) as mt,
											   		     if(left(nss,1)='I',if(saldo_estimado>750000,750000,saldo_estimado),saldo_estimado) as saldo_estimado
												  from (Select * from produccion where folio='$filtro1'
															and id_estatus_prod in (3,5,15,32)) p
														inner join fechas_cierre_com fc 
																on (datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
																	and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0)
														left join tempo_nap_folio_mes tp 
																on (p.nap=tp.nap and fc.anio=tp.anio and fc.mes=tp.mes)
														left join (Select * from pagos_folios_sin_av 
																   where tipo_autoriza=2 or tipo_autoriza=3) pf 
																on (p.folio=pf.folio)			
												  where id_tipo_mov=1 and cierre_cap=1 
													and id_estatus_anticipo=0 and saldo_estimado > 0
													and (id_estatus_prod=3 or id_estatus_prod=5 or id_estatus_prod=15)
													and str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('01/06/2022','%d/%m/%Y') 
													and ifnull(tp.nap,'')='' and id_estatus_prod != 32 or ifnull(pf.folio,'') !='') prod
												inner join empleados e on (prod.no_empleado=e.no_empleado)
												left join (select clave,id_nivel as n_a from usuarios_nivel 
														   where id_nivel=3 or id_nivel=7) una on (e.clave=una.clave)
												left join (Select clave,max(id_nivel) as n_b
								  				   		   from (Select distinct clave,id_nivel from usuarios_nivel_gerencia 
								  				   		   		 where id_nivel=5
										  				   union
										  				   Select distinct clave,id_nivel from usuarios_nivel_gerencia 
										  				   		  where id_nivel=13) n
								  				   		   group by clave) unb on (e.clave=unb.clave)
												inner join (Select folio,
																mid(curp,7,2) as mes_nac, mid(curp,9,2) as dia_nac,
																if(mid(curp,5,2)*1 >= 5,1900 + (mid(curp,5,2)*1),2000 + (mid(curp,5,2)*1)) as anio_nac
															from prod_trabajador) pt on (prod.folio=pt.folio)) p
										left join prod_trabajador pt on (p.folio=pt.folio)
										inner join empleados e on (p.nap=e.nae)
									  where observa !='Folio con informacion duplicada') p
									inner join usuarios_comision uc on (p.clave=uc.clave) 
									inner join por_anticipos pa on (p.clave=pa.clave) 
									inner join (select * from tabla_comision 
												where activa=1 and periodo=1) tc 
											on (uc.id_tabla_comision=tc.id_tabla_comision 
												and tc.nivel_usuario=if(p.nivel=7,3,p.nivel)
												and tc.nom_tabla_comision like '%2021-II%')
									inner join tabla_bonos tb 
											on (tc.id_tabla_comision=tb.id_tabla_comision)";
						$bd=$conecta->consulta($query);
					}
					if ($bd->num_rows != 0)
					{
						$tb=mysqli_fetch_array($bd);
						$anticipo=$tb['anticipo'];
						$edad=$tb['edad'];
						$folio_av=$tb['folio_av'];
						$estatus_av=$tb['estatus'];
						$saldo=$tb['saldo_estimado'];

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
							{
								$bd=$conecta->consulta("Select estatus from estatus_bloquea where tipo='anticipos'");
								$tb = $bd->fetch_array(MYSQLI_NUM);
								$p=$tb['estatus'];
								if($p==0)
								{
									$conecta->consulta("Update estatus_bloquea set estatus=1 where tipo='anticipos'");
									$p=1;
								}
								if($p==1)
									$estatus=3;
								else 
									$estatus=1;
							}
							else
								$estatus=1;
						}
						else
						{ 
							$conecta->consulta("Update estatus_bloquea set estatus=0 where tipo='anticipos'");
							$estatus=1;
						}

						$fecha=(date("d")+1) . date("/m/") . (date("Y")-1); 

						$query="Select * from pagos pa 
									inner join (Select folio from prod_trabajador 
												where curp in (Select curp from prod_trabajador where folio='$filtro1')) pt 
											on(pa.folio=pt.folio)
								where STR_TO_DATE(fec_pago, '%d/%m/%Y') >= STR_TO_DATE('$fecha', '%d/%m/%Y') and saldo_real > 0";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)		
							echo "1|300||||0|0|0";
						else
						{
							$query="Select p.folio from produccion p
									where left(folio,2)='FN' and folio='$filtro1'
										and STR_TO_DATE(fec_traspaso, '%d/%m/%Y') >= STR_TO_DATE('29/05/2023', '%d/%m/%Y')";
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
								echo "1|400||||0|0|0";
							else
								echo "$" . number_format($anticipo, 2, '.', ',') . "|" . $edad . "|" . $folio_av . "|" . $estatus . "|" . $estatus_av . "|$0.00|" . "$" .  number_format($anticipo + ($saldo * $por_incentivo), 2, '.', ',') . "|" . "$" . number_format($saldo * $por_incentivo, 2, '.', ',');
						}
					}

				}
				else
					echo "1|0||||0|0|0";
			}
		break;
		case "sol_pago_anticipo":
			$fecha= date("d/m/Y");
			$datos=explode("|", $filtro2);

			$anticipo=str_replace(",","",str_replace("$","",$datos[0]));
			$anticipo_mtto=str_replace(",","",str_replace("$","",$datos[1]));
			$estatus=$datos[2];



				$query="Select distinct anio,mes
						from (Select * from produccion where folio='$filtro1') p
							inner join fechas_cierre_com fc on (datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
									and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0)";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);

				$mes=$tb["mes"];
				$anio=$tb["anio"];

				if($anio < 2023)
					$por_incentivo=0.001500;
				else
					$por_incentivo=0;
		
			$query="Update produccion 
					set id_estatus_anticipo=$estatus, fec_anticipo='$fecha', monto_anticipo=$anticipo,monto_incentivo=saldo_estimado * $por_incentivo 
					where folio='$filtro1'";
			$conecta->consulta($query);
			echo "Anticipo generado";
		break;
		case "tiendas_dp":
			$d=explode(" ", $filtro1);
			$bd=$conecta->consulta("Select direccion from tiendas_dp where no_tienda=" . $d[0]);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0];
		break;
		case "tiendas_bd":
			$query="Select clave_tienda,nombre,zona 
					from (Select * from tiendas_bd where nombre_tienda='" . $filtro1 . "') t 
						left join (Select clave,nombre from empl_usuarios where clave !='') eu on (t.lider=eu.clave)";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0] . "|" . $tb[1] . "|" . $tb[2];
		break;
		case "zonas_bd":
			$query="Select nombre 
					from (Select * from zonas_bd where zona='" . $filtro1 . "') t 
						inner join (Select clave,nombre from empl_usuarios where clave !='') eu on (t.lider=eu.clave)";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0];
		break;
		case "no_ct":
			$query="Select max(no_promotor) + 1
					from (Select trim(substring(no_promotor,3,4)) * 1 as no_promotor from empleados_dp) e";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			$n1= $tb[0];

			$query="Select max(no_promotor) + 1
					from (Select trim(substring(no_promotor,4,4)) * 1 as no_promotor from empleados_dp) e";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			$n2= $tb[0];
			if($n1 > $n2)
				echo $n1;
			else
				echo $n2;
		break;
		case "sol_dp":
			$query="Select cliente from prod_deportenis where solicitud='$filtro1'";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0];
		break;
		case "sol_ct_dp":
			$query="Select concat(no_promotor,' ',e.promotor) as promotor 
					from empleados_dp e
						inner join (Select promotor from prod_deportenis where solicitud='$filtro1') p on (e.no_promotor=p.promotor)";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0];
		break;
		case "act_est_trasp":
			$fec_bloqueo="";
			$bd=$conecta->consulta("Select * from bloqueo_pago");
			if ($bd->num_rows != 0)
			{
				$tb=$bd->fetch_array(MYSQLI_NUM);
				$fec_bloqueo=$tb[0];
				$hora_bloqueo=$tb[1];
			}
			$pasa=1;
			if(date("d/m/Y")==$fec_bloqueo)
			{
				if(date("G")>= $hora_bloqueo)
					$pasa=0;
			}
			if($pasa==1)
			{
				$fec_tope_ap= date ( 'Y-m-j' ,strtotime ( '-90 day' , strtotime ( date("Y-n-j"))));

				$n_aleatorio=rand(10000,99999);

				$tem_tabla="prod_tempo_" . $n_aleatorio;
				$conecta->consulta("CREATE TABLE $tem_tabla LIKE prod_tempo");

			//--------------------------------------------------------------------------------------------------------------------- Por NSS
				$query="Insert into $tem_tabla
						Select folio,fec_traspaso,d.nss,'',2
					  	from (Select nss, max(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2))) as fec 
						      from (select distinct fec_traspaso,pt.nss
							    	from (Select folio,nss,fec_traspaso from produccion
									  	  where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) pt
										inner join (select nss, v
													from (select nss, count(*) v 
													  	  from (Select nss,fec_traspaso from produccion where nss != '' 
									  								and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) d
													  	  group by nss) v 
													where v>1) v on (pt.nss=v.nss)) d
						      group by nss) d
					  		inner join produccion p 
					  				on p.nss=d.nss and concat(substr(fec,-2),'/',substr(fec,6,2),'/',substr(fec,1,4))=p.fec_traspaso";
				$conecta->consulta($query);	
				usleep(100000);	

				$query="Insert into $tem_tabla
						Select folio,fec_traspaso,pt.nss,'',1 
						from (Select folio,fec_traspaso,nss from produccion 
							  where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) pt
							inner join (select nss, v
										from (select nss, count(*) v 
										  	  from (Select nss,fec_traspaso from produccion where nss != '' 
						  								and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) d
										  	  group by nss) v 
										where v=1) v on (pt.nss=v.nss)";
				$conecta->consulta($query);	
				usleep(200000);	

				$query="Insert into apovol_prod
						Select distinct tramite,pt.folio,'S',id_estatus_prod
						from (Select ag.tramite,nss 
							  from (Select tramite from av_fechas 
								    where datediff(concat(substr(fec_captura,-4),'-',substr(fec_captura,4,2),'-',substr(fec_captura,1,2)),'$fec_tope_ap')>=0) af
								inner join (select av.* from (Select * from av_general av where nss != '' and nss != '00000000000') av
												left join apovol_prod ap on (av.tramite=ap.folio_av)
											where ifnull(ap.folio_av,'')='') ag on (af.tramite=ag.tramite)
							  where nss !='') av
							inner join (Select * from $tem_tabla where estatus=2) v on (av.nss=v.dato1)
							inner join produccion pt on (v.dato1=pt.nss and v.fec_traspaso=pt.fec_traspaso)
						where tramite !=''";
				$conecta->consulta($query);	
				usleep(200000);

				$query="Insert into apovol_prod
				        Select distinct tramite,pt.folio,'S',id_estatus_prod
						from (Select ag.tramite,nss 
							  from (Select tramite from av_fechas 
								    where datediff(concat(substr(fec_envio,-4),'-',substr(fec_envio,4,2),'-',substr(fec_envio,1,2)),'$fec_tope_ap')>=0) af
								inner join (select av.* from (Select * from av_general av where nss != '' or curp != '') av
												left join apovol_prod ap on (av.tramite=ap.folio_av)
											where ifnull(ap.folio_av,'')='') ag on (af.tramite=ag.tramite)
							  where nss !='') av
							inner join (Select * from $tem_tabla where estatus=1) v on (av.nss=v.dato1)
							inner join produccion pt on (v.dato1=pt.nss)
						where tramite !=''";
				$conecta->consulta($query);	
				usleep(200000);	

				$query="Update apovol_prod ap, 
							   (Select distinct tramite,pt.folio
							    from (Select * from $tem_tabla where estatus=2) pt
									inner join av_general av on (pt.dato1=av.nss)) pt
						set folio_pr=folio
						where ap.folio_av=pt.tramite";
				$conecta->consulta($query);
				usleep(500000);
			//------------------------------------------------------------------------------------------------------------------------------

			//--------------------------------------------------------------------------------------------------------------------- Por CURP
				$query="Insert into $tem_tabla
					    Select distinct p.folio,fec_traspaso,curp,trabajador,1
					    from (Select folio,fec_traspaso from produccion 
							  where nss=''
							  	and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) p 
					   		inner join prod_trabajador pt on (p.folio=pt.folio)
					   	where curp != ''";
				$conecta->consulta($query);

				$query="Update $tem_tabla tt,
							   (Select dato1, v
								from (select dato1, count(*) v 
								  	  from  $tem_tabla d
								  	  where dato2!='' and dato1 !=''
								  	  group by dato1) v 
								where v>1) v
						set estatus=2
						where tt.dato1=v.dato1";
				$conecta->consulta($query);
				usleep(100000);	

				$query="Insert into apovol_prod
						Select distinct tramite,v.folio,'C',id_estatus_prod
						from (Select af.tramite,curp 
							  from (Select tramite from av_fechas 
								    where datediff(concat(substr(fec_captura,-4),'-',substr(fec_captura,4,2),'-',substr(fec_captura,1,2)),'$fec_tope_ap')>=0) af
								inner join (select av.*,ap.folio_av 
											from (Select * from av_general av where (nss = '' or nss='00000000000') and curp != '') av
												left join apovol_prod ap on (av.tramite=ap.folio_av)
											where ifnull(ap.folio_av,'')='') av on (af.tramite=av.tramite)) av 
							inner join (Select distinct pt.folio,ptf.fec_traspaso,pt.dato1
										from $tem_tabla pt
											inner join (Select dato1,max(fec_traspaso) as fec_traspaso from $tem_tabla 
														where estatus=2 and dato2!='' group by dato1) ptf 
													on (pt.dato1=ptf.dato1 and pt.fec_traspaso=ptf.fec_traspaso)) v on (av.curp=v.dato1)
							inner join produccion pt on (v.folio=pt.folio)";
				$conecta->consulta($query);
				usleep(100000);

				$query="Insert into apovol_prod
						Select distinct tramite,v.folio,'C',id_estatus_prod
						from (Select af.tramite,curp 
							  from (Select tramite from av_fechas 
								    where datediff(concat(substr(fec_captura,-4),'-',substr(fec_captura,4,2),'-',substr(fec_captura,1,2)),'$fec_tope_ap')>=0) af
								inner join (select av.*,ap.folio_av 
											from (Select * from av_general av where (nss = '' or nss='00000000000') and curp != '') av
												left join apovol_prod ap on (av.tramite=ap.folio_av)
											where ifnull(ap.folio_av,'')='') av on (af.tramite=av.tramite)) av 
							inner join (Select * from $tem_tabla where estatus=1 and dato2!='') v on (av.curp=v.dato1)
							inner join produccion pt on (v.folio=pt.folio)";
				$conecta->consulta($query);
				usleep(100000);	

				$query="Update apovol_prod ap, 
							   (Select tramite,pt.folio
							    from (Select distinct pt.folio,ptf.fec_traspaso,pt.dato1
										from $tem_tabla pt
											inner join (Select dato1,max(fec_traspaso) as fec_traspaso from $tem_tabla 
														where estatus=2 and dato2!='' group by dato1) ptf 
													on (pt.dato1=ptf.dato1 and pt.fec_traspaso=ptf.fec_traspaso)) pt
									inner join av_general av on (pt.dato1=av.curp)) pt
						set folio_pr=folio
						where ap.folio_av=pt.tramite";
				$conecta->consulta($query);
				usleep(500000);

				$query="Update apovol_prod ap,
							   (Select folio,id_estatus_prod from produccion
						  		where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_tope_ap')>=0) pt
						set ap.id_estatus_prod=pt.id_estatus_prod 
						where ap.folio_pr=pt.folio";
				$conecta->consulta($query);
				usleep(500000);

				$conecta->consulta("Delete from $tem_tabla");
				usleep(200000);

				$query="Insert into $tem_tabla
						Select ap.folio_av,'',folio_pr,'',id_estatus_prod
						from (select folio_av,v
							  from (SELECT folio_av, count(*) as v FROM apovol_prod group by folio_av) v 
							  where v>1) v 
							inner join apovol_prod ap on (v.folio_av=ap.folio_av)";
				$conecta->consulta($query);
				usleep(200000);

				$conecta->consulta("Delete from apovol_prod where folio_av in (Select folio from $tem_tabla)");
				usleep(200000);

				$query="Insert into apovol_prod
						Select distinct ap.folio,dato1,'S',id_estatus_prod 
						from (Select folio,max(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2))) as fec
							  from (select pt.folio,dato1,p.fec_traspaso
								    from $tem_tabla pt 
										inner join produccion p on (pt.dato1=p.folio)) d 
							  group by folio) ap 
							inner join (Select pt.folio,dato1,p.fec_traspaso, id_estatus_prod from $tem_tabla pt 
											inner join produccion p on (pt.dato1=p.folio)) pt
									on (ap.folio=pt.folio and concat(substr(fec,-2),'/',substr(fec,6,2),'/',substr(fec,1,4))=pt.fec_traspaso)";
				$conecta->consulta($query);
				usleep(200000);

				$query="Delete from apovol_prod
						where folio_av in (select folio_av from (SELECT folio_av, count(*) as v FROM apovol_prod group by folio_av) v where v>1)
							and id_estatus_prod=6";
				$conecta->consulta($query);
				usleep(200000);						

				$query="Delete from apovol_prod
						where folio_av in (select folio_av from (SELECT folio_av, count(*) as v FROM apovol_prod group by folio_av) v where v>1)
							and id_estatus_prod>1";
				$conecta->consulta($query);
				usleep(200000);	

				$conecta->consulta("Drop table $tem_tabla");

				echo "Actualizacion exitosa";
			}
			else
				echo "En estos momentos no se puede ejecutar la interface. Esta ejecutandose el proceso de pagos...";
		break;
		case "prop_nap":
			$query="Select nap,baja from naps
					where no_empleado in (Select no_empleado from empl_usuarios where nombre='$filtro1')";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0] . "|" . $tb[1];
		break;
		case "naps":
			$query="Select nombre,baja 
					from (Select * from naps where nap='$filtro1') n
						inner join empl_usuarios eu on (n.no_empleado=eu.no_empleado)";
			$bd=$conecta->consulta($query);
			$tb = $bd->fetch_array(MYSQLI_NUM);
			echo $tb[0] . "|" . $tb[1];
		break;
		case "valida_av":
				$d= explode("/", $filtro1);
				$no_tramite=trim($d[1]);

				$pasa=0;
				if($no_tramite !="")
				{
					$bd=$conecta->consulta("Select folio_pr from apovol_prod where folio_av='$no_tramite'");
					if ($bd->num_rows != 0)
					{
						$tb=mysqli_fetch_array($bd);
						if($tb['folio_pr']==$filtro2)
						{
							$pasa=1;
						}
						else
							echo $pasa . "|" . $tb['folio_pr'];
					}
					else
						$pasa=1;
					if($pasa==1)
					{
						$bd=$conecta->consulta("Select folio,tramite,nss,curp,nap,cliente from av_general where tramite='$no_tramite'");
						$tb=mysqli_fetch_array($bd);
						echo $pasa . "|" . $tb['folio'] . "|" . $tb['tramite'] . "|" . $tb['nss'] . "|" . $tb['curp'] . "|" . $tb['nap'] . "|" . $tb['cliente'];
					}
				}
				else
					echo $pasa . "|No se ha detectado el número de trámite de la apovol";
		break;
		case "municipio":
			//$filtro1=str_replace("_"," ",$filtro1);
			$query="Select distinct municipio from carobra.cartera_registros where estado='$filtro1' order by municipio";

			$bd=$conecta->consulta($query);
 ?>
 			<option value=""></option>
<?php 
			while($tb=mysqli_fetch_array($bd))
			{
?>
 				<option value="<?php echo $tb["municipio"]; ?>"><?php echo $tb["municipio"]; ?></option>
<?php 
			}
		break;
		case "asesor_ae":
			echo "|";
			$query="Select distinct gerencia from (Select clave from empl_usuarios where nombre='$filtro1') eu 
						inner join usuarios_nivel_gerencia ung on (eu.clave=ung.clave)
						inner join (Select distinct id_gerencia,gerencia from gerencias) g 
								on (ung.id_gerencia=g.id_gerencia)
					where pertenece=1";
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				echo $tb["gerencia"];
			}
		break;
		case 'cliente_informacion':
				$conecta = new conector();
				$conecta->consulta("SET NAMES utf8");
			
				// Verificar la conexión antes de continuar
				if (!$conecta->conexion) {
					die("Conexión fallida: " . mysqli_connect_error());
				}
			
				// Obtener datos del POST y sanitizar
				$cliente = mysqli_real_escape_string($conecta->conexion, $_POST['cliente']);
				$mes = mysqli_real_escape_string($conecta->conexion, $_POST['mes']);
				$anio = mysqli_real_escape_string($conecta->conexion, $_POST['anio']);
			
				// Consulta SQL para obtener monto, meses a renovar y si está marcado para renovación
				/*$query = "SELECT r.renovaciones, p.monto
							FROM plan_ideal.renovaciones r
							INNER JOIN plan_ideal.pagos p ON r.id_cliente = p.id_cliente
							INNER JOIN plan_ideal.clientes c ON r.id_cliente = c.id_cliente
							WHERE r.anio = $anio 
							AND r.mes = $mes 
							AND CONCAT(c.nombre, ' ', c.apaterno, ' ', c.amaterno) = '$cliente'";*/
				$query = "SELECT eg.sc, eg.brocker, g.gerencia, u.nombre AS asesor, CONCAT(c.nombre, ' ', c.apaterno, ' ', c.amaterno) AS nombre_completo, c.nss, c.curp, c.fec_alta, p.fec_pago, p.fec_liquida, fp.frecuencia, cc.tel, e.celular AS tel_asesor, CASE WHEN p.id_pago IS NOT NULL THEN 'Pagado' ELSE 'No pagado' END AS ha_pagado, 
				r.renovacion, p.monto, r.no_renovacion, CONCAT(r.no_renovacion,'/',nr.no_meses) as nm, r.pago_renovacion
											FROM plan_ideal.renovaciones r
											inner join (Select id_renovacion,count(*) as no_meses from plan_ideal.renovaciones
														group by id_renovacion) nr
														on (r.id_renovacion = nr.id_renovacion)
											LEFT JOIN plan_ideal.pagos p ON (r.id_cliente = p.id_cliente)
											INNER JOIN plan_ideal.clientes c ON (r.id_cliente = c.id_cliente)
											INNER JOIN plan_ideal.cli_contacto cc ON (c.id_cliente = cc.id_cliente) 
											INNER JOIN plan_ideal.forma_pago fp ON (c.forma_pago = fp.no_meses)
											INNER JOIN carobra.usuarios u ON (c.asesor = u.nombre) 
											INNER JOIN carobra.empleados e ON (u.clave = e.clave) 
											INNER JOIN ( SELECT DISTINCT no_empleado, id_gerencia FROM carobra_2014.usuarios_nivel_gerencia WHERE id_nivel = 3 AND pertenece = 1 ) ung ON e.no_empleado = ung.no_empleado 
											INNER JOIN ( SELECT DISTINCT id_gerencia, gerencia FROM carobra_2014.gerencias ) g ON ung.id_gerencia = g.id_gerencia 
											INNER JOIN carobra_2014.estructura_gerencia eg ON (g.id_gerencia = eg.id_gerencia)
											WHERE r.anio = $anio 
											AND r.mes = $mes 
											AND CONCAT(c.nombre, ' ', c.apaterno, ' ', c.amaterno) = '$cliente'";
			
				$resultado = $conecta->consulta($query);
			
				if ($resultado && mysqli_num_rows($resultado) > 0) {
					$fila = mysqli_fetch_assoc($resultado);
			
					// Nueva consulta para obtener el número de meses con renovación en todos los años posteriores al mes y año indicados
					$query_meses_reno = "SELECT COUNT(*) as meses_reno 
										FROM plan_ideal.renovaciones r
										INNER JOIN plan_ideal.clientes c ON r.id_cliente = c.id_cliente
										WHERE (r.anio > $anio OR (r.anio = $anio AND r.mes >= $mes)) 
										AND r.renovacion = 1 
										AND CONCAT(c.nombre, ' ', c.apaterno, ' ', c.amaterno) = '$cliente'";
			
					$resultado_meses_reno = $conecta->consulta($query_meses_reno);
					$meses_reno = 0;
			
					if ($resultado_meses_reno && mysqli_num_rows($resultado_meses_reno) > 0) {
						$fila_meses_reno = mysqli_fetch_assoc($resultado_meses_reno);
						$meses_reno = $fila_meses_reno['meses_reno'];
					}
			
					// Respuesta en JSON con los datos obtenidos
					echo json_encode([
						'sc' => $fila['sc'],
						'brocker' => $fila['brocker'],
						'gerencia' => $fila['gerencia'],
						'asesor' => $fila['asesor'],
						'nombre_completo' => $fila['nombre_completo'],
						'nss' => $fila['nss'],
						'curp' => $fila['curp'],
						'fec_alta' => $fila['fec_alta'],
						'fec_pago' => $fila['fec_pago'],
						'fec_liquida' => $fila['fec_liquida'],
						'frecuencia' => $fila['frecuencia'],
						'tel' => $fila['tel'],
						'tel_asesor'=> $fila['tel_asesor'],
						'ha_pagado' => $fila['ha_pagado'],
						'monto' => htmlspecialchars($fila['monto']),
						'no_meses' => $fila['nm'],
						'renovaciones' => htmlspecialchars($fila['renovaciones']),
						'pago_renovacion' => $fila['pago_renovacion'],
						'meses_reno' => $meses_reno
					]);
				} else {
					echo json_encode([
						'sc' => '',
						'brocker' => '',
						'gerencia' => '',
						'asesor' => '',
						'nombre_completo' => '',
						'nss' => '',
						'curp' => '',
						'fec_alta' => '',
						'fec_pago' => '',
						'fec_liquida' => '',
						'frecuencia' => '',
						'tel' => '',
						'tel_asesor'=> '',
						'ha_pagado' => '',
						'monto' => 0,
						'no_meses' => '',
						'renovaciones' => 0,
						'pago_renovacion' => '',
						'meses_reno' => 0
					]);
				}
			break;
			case 'gerencias_inv':
				$sql="Select gerente,ifnull(id_brocker,0) as id_brocker from invikta.gerencias where gerencia='$filtro1'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				if (!empty($tb)) 
				{
					echo $tb["gerente"] . "|" . $tb["id_brocker"];
				} 
			break;
			case 'val_acompa_inv':
				$sql="Select clave from invikta.empleados where nombre='$filtro1'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$clave=$tb["clave"];
				if($clave==$filtro2)
					echo 0;
				else
					echo 1;
			break;
	}
?>
