<?php
	session_start();

	include("../funciones/encripta.php");
	
	$op=addslashes(trim($_POST["op"]));

	$conecta= new conector();

	$conecta->consulta("SET NAMES utf8");

	switch ($op) 
	{
		case 1:  /* ------------------------------------------------------- Guardar empleado Deportenis */
			$ri=addslashes(trim($_POST["txt-ri"]));
			$club=addslashes(trim($_POST["txt-club"]));
			$car=addslashes(trim($_POST["txt-car"]));
			$etica=addslashes(trim($_POST["txt-etica"]));
			$no_ct=addslashes(trim($_POST["no_ct"]));
			$nombre_dp=addslashes(trim($_POST["txt-nombre-dp"]));
			$promotor=addslashes(trim($_POST["txt-promotor"]));
			$tienda=addslashes(trim($_POST["txt-tienda"]));
			$celula=addslashes(trim($_POST["sel-celula"]));
			$nivel=addslashes(trim($_POST["sel-nivel"]));
			$estatus=addslashes(trim($_POST["sel-estatus"]));
			$fec_activo=addslashes(trim($_POST["txt-fec-activo"]));
			$fec_baja=addslashes(trim($_POST["txt-fec-baja"]));
			$fec_nac=addslashes(trim($_POST["txt-fec-nac"]));
			$tel=addslashes(trim($_POST["txt-tel"]));
			$email=addslashes(trim($_POST["txt-email"]));
			$observa=addslashes(trim($_POST["txt-observa"]));
			$supervisor=addslashes(trim($_POST["txt-supervisor"]));
			$estado=addslashes(trim($_POST["sel-estado"]));
			$turno=addslashes(trim($_POST["sel-turno"]));
			$coordinador=addslashes(trim($_POST["txt-coordinador"]));
			$contrato=addslashes(trim($_POST["chk-contrato"]));
			$reclutador=addslashes(trim($_POST['txt-reclutador']));
			$coordinador=addslashes(trim($_POST['txt-coordinador']));

			if($contrato=="on")
				$contrato=1;
			else
				$contrato=0;
			$d=explode(" ",$tienda);
			$tienda=$d[0];

			if($coordinador != '')
			{
				$sql="Select no_promotor from empleados_dp where promotor='$coordinador'";
				$bd=$conecta->consulta($sql);
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$coordinador=$tb["no_promotor"];
				}
				else
					$coordinador='';
			}

			if($reclutador != '')
			{
				$sql="Select no_promotor from empleados_dp where promotor='$reclutador'";
				$bd=$conecta->consulta($sql);
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$reclutador=$tb["no_promotor"];
				}
				else
					$reclutador='';
			}

			$bd=$conecta->consulta("Select 1 from empleados_dp where no_promotor='$no_ct'");
			if ($bd->num_rows != 0)
			{
				$query="Update empleados_dp 
						set promotor='$promotor',tienda=$tienda,celula=$celula,nivel='$nivel',car=$car,
							estatus='$estatus',fec_activo='$fec_activo',fec_baja='$fec_baja',club=$club,
							fec_nac='$fec_nac',tel='$tel',email='$email',observa='$observa',ri=$ri,
							nombre_dp='$nombre_dp',turno='$turno', etica=$etica,contrato=$contrato,
							reclutador='$reclutador', coordinador='$coordinador'
					    where no_promotor='$no_ct'";

				$bd=$conecta->consulta("Select * from usuarios_comision_dp where ct='$no_ct'");
				if ($bd->num_rows == 1)
					$conecta->consulta("Update usuarios_comision_dp set celula=$celula where ct='$no_ct'");
			}
			else
				$query="Insert into empleados_dp values('$no_ct','$promotor','$nombre_dp',$tienda,'$coordinador',$celula,'$nivel','$estatus','$turno','$fec_activo','$fec_baja','$fec_nac','$tel','$email','$observa',$club,$car,$etica,$ri,$contrato,'$reclutador')";

			$conecta->consulta($query);

			echo "Datos de empleado guardados con éxito";
		break;

		case 2:  /* ----------------------------------------------------------- Guardar nueva célula Deportenis */
			$celula=addslashes(trim($_POST["celula"]));
			$lider=addslashes(trim($_POST["lider"]));
			$sc=addslashes(trim($_POST["sc"]));

			if($lider != "")
			{
				$l=explode("(",$lider);
				$lider=str_replace(")","",$l[1]);
			}

			if($sc != "")
			{
				$bd=$conecta->consulta("Select no_promotor from empleados_dp where promotor='$sc'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$sc=$tb["no_promotor"];
				}
				else 
					$sc="";
			}
			$bd=$conecta->consulta("Select max(no_celula)+1 as no_celula from celulas");
			$tb=mysqli_fetch_array($bd);
			$no_celula=$tb["no_celula"];

			$conecta->consulta("Insert into celulas values($no_celula,'$celula','$lider','$sc')");
			echo "Célula dada de alta con éxito";
		break;
		case 3:  /* ------------------------------------ Visualizar detalles de Célula Deportenis */
			$celula=addslashes(trim($_POST["celula"]));
			$usuario=addslashes(str_replace("_"," ",trim($_POST["usuario"])));

			$n=0;
			if($usuario=="ZULEYMA BEATRIZ VERA GRANIEL" or $usuario=="Administrador del Sistema")
				$n=12;

			if($celula != 1000)
			{
				$query="Select ifnull(esc.promotor,'') as socio, ifnull(eli.promotor,'') as lider
						from (Select * from celulas where no_celula=$celula) c 
							left join empleados_dp esc on (c.supervisor=esc.no_promotor)
							left join empleados_dp eli on (c.lider=eli.no_promotor)";
				$bd_s=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd_s);
				echo "1|" . $tb["lider"] . "|" . $tb["socio"] . "|";
			}
			else
				echo "0|||";

			if($celula < 1000)
				$query="Select ifnull(s.supervisor,'') as supervisor,c.celula,t.tienda,alias,c.coordinador,
							   e.no_promotor,promotor,nombre_dp,
							   if(nivel='P','Promotor',if(nivel='L','Líder',if(nivel='C','Coordinador','Socio Comercial'))) as nivel,
							   if(e.estatus='A','Activo','Baja') as estatus,
							   if(turno='MT','Medio turno',if(turno='TC','Turno completo',if(turno='FS','Fin de Semana',''))) as turno, 
							   fec_activo,fec_baja,fec_nac,observa,tel,email,club,
							   ifnull(rec.reclutador,'') as reclutador
						from (Select * from empleados_dp where celula=$celula) e 
							inner join (Select no_tienda,concat(no_tienda,' ',tienda) as tienda,estado,
											   coordinador from tiendas_dp) t 
									on (e.tienda=t.no_tienda)
							left join (Select no_promotor,promotor as coordinador from empleados_dp 
									   where no_promotor != '') c on (t.coordinador=c.no_promotor)
							left join estados est on (t.estado=est.no_edo) 
							inner join celulas c on (e.celula=c.no_celula)
							left join (Select no_promotor,promotor as supervisor from empleados_dp 
									   where no_promotor != '') s on (c.supervisor=s.no_promotor)
							left join (Select no_promotor,promotor as reclutador from empleados_dp 
									   where no_promotor != '') rec on (e.reclutador=rec.no_promotor)
						order by s.supervisor,c.celula,t.tienda,e.no_promotor";
			else
			{
				if($n==0)
					$query="Select ifnull(s.supervisor,'') as supervisor,c.celula,t.tienda,alias,
								   c.coordinador,e.no_promotor,promotor,nombre_dp,
							   	   if(nivel='P','Promotor',if(nivel='L','Líder',if(nivel='C','Coordinador','Socio Comercial'))) as nivel,
							   	   if(e.estatus='A','Activo','Baja') as estatus,
							   	   if(turno='MT','Medio turno',if(turno='TC','Turno completo',if(turno='FS','Fin de Semana',''))) as turno, 
							   	   fec_activo,fec_baja,fec_nac,observa,tel,email,club,
							   	   ifnull(rec.reclutador,'') as reclutador
							from empleados_dp e
								inner join (Select no_tienda,concat(no_tienda,' ',tienda) as tienda,estado,
												   coordinador from tiendas_dp) t 
										on (e.tienda=t.no_tienda)
								left join (Select no_promotor,promotor as coordinador from empleados_dp 
										   where no_promotor != '') c on (t.coordinador=c.no_promotor)
								left join estados est on (t.estado=est.no_edo) 
								inner join celulas c on (e.celula=c.no_celula)
								left join (Select no_promotor,promotor as supervisor from empleados_dp 
										   where no_promotor != '') s on (c.supervisor=s.no_promotor)
								left join (Select no_promotor,promotor as reclutador from empleados_dp 
									   where no_promotor != '') rec on (e.reclutador=rec.no_promotor)
							order by s.supervisor,c.celula,t.tienda,e.no_promotor";
				else
					$query="Select ifnull(s.supervisor,'') as supervisor,c.celula,t.tienda,alias,
								   c.coordinador,e.no_promotor,promotor,nombre_dp,
							   	   if(nivel='P','Promotor',if(nivel='L','Líder',if(nivel='C','Coordinador','Socio Comercial'))) as nivel,
							   	   if(e.estatus='A','Activo','Baja') as estatus,
							   	   if(turno='MT','Medio turno',if(turno='TC','Turno completo',if(turno='FS','Fin de Semana',''))) as turno, 
							   	   fec_activo,fec_baja,fec_nac,observa,tel,email,club,
							   	   ifnull(rec.reclutador,'') as reclutador
							from empleados_dp e
								inner join (Select no_tienda,concat(no_tienda,' ',tienda) as tienda,estado,
												   coordinador from tiendas_dp) t on (e.tienda=t.no_tienda)
								left join (Select no_promotor,promotor as coordinador from empleados_dp 
										   where no_promotor != '') c on (t.coordinador=c.no_promotor)
								left join estados est on (t.estado=est.no_edo)
								inner join celulas c on (e.celula=c.no_celula)
								left join (Select no_promotor,promotor as supervisor from empleados_dp 
										   where no_promotor != '') s on (c.supervisor=s.no_promotor)
								left join (Select no_promotor,promotor as reclutador from empleados_dp 
									   	   where no_promotor != '') rec on (e.reclutador=rec.no_promotor)
							order by s.supervisor,c.celula,t.tienda,e.no_promotor";
			}
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
?>
				<table width="100%" style="font-size: 9px;">
					<thead>
						<tr style="background: #D8D8D8;">
							<th style="min-width: 120px;">Socio Comercial</th>
							<th style="min-width: 100px;">Célula</th>
							<th style="min-width: 120px;">Tienda</th>
							<th style="min-width: 60px;">Estado Tienda</th>
							<th style="min-width: 120px;">Coordinador</th>
							<th style="min-width: 35px;">No. CT</th>
							<th style="min-width: 120px;">Empleado</th>
							<th style="min-width: 120px;">Nombre Dp</th>
							<th style="min-width: 70px;">Nivel</th>
							<th style="min-width: 60px;">Estatus</th>
							<th style="min-width: 60px;">Turno</th>
							<th style="min-width: 60px;">Fec. ingreso</th>
							<th style="min-width: 60px;">Fec. baja</th>
							<th style="min-width: 60px;">Fec. nac.</th>
							<th style="min-width: 60px;">Club</th>
							<th style="min-width: 60px;">Reclutador</th>
<?php
							if($n==12)
							{
?>
								<th style="min-width: 60px;">Teléfono</th>
								<th style="min-width: 120px;">Email</th>
<?php
							}
?>
							<th style="min-width: 200px;">Observaciones</th>
						</tr>
					</thead>
					<tbody>
<?php
						$color="#D8D8D8";
						while($tb=mysqli_fetch_array($bd))
						{
							if($color=="#D8D8D8")
								$color="#FFFFFF";
							else
								$color="#D8D8D8";
?>
							<tr style="background: <?php echo $color; ?>">
								<td><?php echo $tb['supervisor']; ?></td>
								<td><?php echo $tb['celula']; ?></td>
								<td><?php echo $tb['tienda']; ?></td>
								<td><?php echo $tb['alias']; ?></td>
								<td><?php echo $tb['coordinador']; ?></td>
								<td><?php echo $tb['no_promotor']; ?></td>
								<td><?php echo $tb['promotor']; ?></td>
								<td><?php echo $tb['nombre_dp']; ?></td>
								<td><?php echo $tb['nivel']; ?></td>
								<td><?php echo $tb['estatus']; ?></td>
								<td><?php echo $tb['turno']; ?></td>
								<td><?php echo $tb['fec_activo']; ?></td>
								<td><?php echo $tb['fec_baja']; ?></td>
								<td><?php echo $tb['fec_nac']; ?></td>
								<td><?php echo $tb['club']; ?></td>
								<td><?php echo $tb['reclutador']; ?></td>
<?php
							if($n==12)
							{
?>
								<td><?php echo $tb['tel']; ?></td>
								<td><?php echo $tb['email']; ?></td>
<?php
							}
?>
								<td><?php echo $tb['observa']; ?></td>
							</tr>
<?php
						}
?>
					</tbody>
				</table>
<?php
			}
			else
				echo "<h3>No existe información para esta célula</h3>";
		break;
		case 4:  /* ----------------------------------------------------------- Guardar tiendas de Deportenis */
			$no_tienda=addslashes(trim($_POST["txt-no-tienda"]));
			$tienda=addslashes(trim($_POST["txt-tienda-t"]));
			$direc=addslashes(trim($_POST["txt-dir-tienda"]));
			$celula=addslashes(trim($_POST["sel-celula-t"]));
			$estado=addslashes(trim($_POST["sel-estado"]));
			$coordinador=addslashes(trim($_POST["txt-ct"]));
			$cambaceo=addslashes(trim($_POST["chk-cambaceo"]));

			if($cambaceo=="on")
				$cambaceo=1;
			else 
				$cambaceo=0;

			if($coordinador != "")
			{
				$query="Select no_promotor from empleados_dp 
						where promotor='$coordinador' and estatus='A'";

				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$coordinador=$tb["no_promotor"];
				}
				else 
					$coordinador="";
			}
			$bd=$conecta->consulta("Select no_tienda from tiendas_dp where no_tienda=$no_tienda");
			if ($bd->num_rows == 0)
			{
				$mensaje="Tienda dada de alta con éxito";
				$query="Insert into tiendas_dp values('$no_tienda','$tienda','$direc',$celula,$estado,'$coordinador',$cambaceo)";
			}
			else
			{
				$mensaje="Datos de la tienda actualizados con éxito";
				$query="Update tiendas_dp 
						set direccion='$direc', celula=$celula, estado=$estado, 
							coordinador='$coordinador', cambaceo=$cambaceo
						where no_tienda=$no_tienda";
			}
			$conecta->consulta($query);
			echo $mensaje;
		break;
		case 5:  /* ----------------------------------------------------------- Guardar Socios Comerciales Estructura Comercial */
			$tipo=addslashes(trim($_POST["tipo"]));
			$sc=addslashes(trim($_POST["txt-sc"]));
			$sb=addslashes(trim($_POST["txt-sb"]));
			$ss=addslashes(trim($_POST["txt-ss"]));
			$oficina=addslashes(trim($_POST["txt-oficina"]));

			if($tipo=='sc')
			{
				if($sc !="")
				{
					$conecta->consulta("Insert into sc_comercial values('$sc')");
					$mensaje="Socio Comercial dado de alta exitosamente";
				}
				else
					$mensaje="No se hacapturado el Socio Comercial";
			}
			else
			{
				if($tipo=='sb')
				{
					if($sb !="")
					{
						$conecta->consulta("Insert into sb_comercial values('$sb')");
						$mensaje="SubBrocker dado de alta exitosamente";
					}
					else
						$mensaje="No se hacapturado el SubBrocker";
				}
				else
				{
					if($tipo=='oficina')
					{
						if($oficina !="")
						{
							$conecta->consulta("Insert into oficina_comercial values('$oficina')");
							$mensaje="Oficina dada de alta exitosamente";
						}
						else
							$mensaje="No se hacapturado la oficina";
					}
					else
					{
						if($ss !="")
						{
							$conecta->consulta("Insert into ss_comercial values('$ss')");
							$mensaje="Socio-SubBrocker dada de alta exitosamente";
						}
						else
							$mensaje="No se hacapturado el Socio-SubBrocker";
					}
				}
			}
			mensaje($mensaje,"../est_comercial.php");
		break;
		case 6:  /* ----------------------------------------------------------- Habilitar gerencias en Estructura Comercial */
			$id=addslashes(trim($_POST["id"]));
			$valor=addslashes(trim($_POST["valor"]));

			$conecta->consulta("Update gerencias_ec set estatus=$valor where id_gerencia=$id");
			if($valor==1)
				$conecta->consulta("Insert into estructura_comercial values('','',$id)");
			else
				$conecta->consulta("Delete from estructura_comercial where id_gerencia=$id");
			echo "Gerencia actualizada exitosamente en estructura comercial";
		break;
		case 7:  /* ----------------------------------------------------------- Guardar tabla Dportenis */
			$datos=addslashes(trim($_POST["datos"]));
			$d=explode("|",$datos);
			if($d[0]==0)
			{
				$mensaje="Tabla ingresada exitosamente";
				$query="Insert into tc_deportenis values(0,'" . $d[1] . "','" . $d[2] . "','" . $d[3] . "','" . $d[4] . "','" . $d[5] . "'," . $d[6] . ")";
			}
			else
			{
				$mensaje="Tabla actualizada exitosamente";
				$query="Update tc_deportenis 
						set nom_tc='" . $d[1] . "', fec_creacion='" . $d[2] . "', tipo='" . $d[3] . "', fec_ini='" . $d[4] . "', 
							fec_fin='" . $d[5] . "', activa=" . $d[6] . "
						where id_tc=" .  $d[0];
			}
			$conecta->consulta($query);
			echo $mensaje;
		break;
		case 8:  /* ----------------------------------------------------------- Eliminar tabla Dportenis */
			$id=addslashes(trim($_POST["id"]));

			$conecta->consulta("Delete from tc_deportenis where id_tc=$id");
			echo "La tabla fue eliminada del sistema";
		break;
		case 9:  /* ----------------------------------------------------------- Eliminar tabla Dportenis */
			$id=addslashes(trim($_POST["id"]));
			$tipo=addslashes(trim($_POST["tipo"]));

			if($tipo=='T')
			{
				$aprobada_p=0;
				$activada_p=0;
				$aprobada_l=0;
				$activada_l=0;
				$bd=$conecta->consulta("Select * from tabla_com_tarjeta_dp where id_tabla=$id");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$aprobada_p=$tb['aprobada_p'];
					$activada_p=$tb['activada_p'];
					$aprobada_l=$tb['aprobada_l'];
					$activada_l=$tb['activada_l'];
				}
?>
				<div class="row ml-0 mr-0 pl-0 pt-3 pr-0 justify-content-center">
					<div class="col-10 pt-3 pl-0 pr-3">
						<table class="tablas" width="100%">
							<tr>
								<th width="25%">Nivel</th>
								<th width="25%">Aprobada</th>
								<th width="25%">Activada</th>
								<th width="25%">Total</th>
							</tr>
							<tr>
								<td>&nbsp;Promotor</td>
								<td>
									<input type="text" id="txt-aprobada-p" class="form-control form-control-sm moneda" value="<?php echo $aprobada_p; ?>" data-men="No se ha ingresado la comisión de aprobadas del promotor">
								</td>
								<td>
									<input type="text" id="txt-activada-p" class="form-control form-control-sm moneda" value="<?php echo $activada_p; ?>" data-men="No se ha ingresado la comisión de activadas del promotor">
								</td>
								<th class="tot-prom moneda"><?php echo $aprobada_p + $$activada_p; ?></th>
							</tr>
							<tr>
								<td>&nbsp;Líder</td>
								<td>
									<input type="text" id="txt-aprobada-l" class="form-control form-control-sm moneda" value="<?php echo $aprobada_l; ?>" data-men="No se ha ingresado la comisión de aprobadas del líder">
								</td>
								<td>
									<input type="text" id="txt-activada-l" class="form-control form-control-sm moneda" value="<?php echo $activada_l; ?>" data-men="No se ha ingresado la comisión de activadas del líder">
								</td>
								<th class="tot-lider moneda"><?php echo $aprobada_l + $$activada_l; ?></th>
							</tr>
							<tr>
								<td>&nbsp;Total</td>
								<th class="tot-aprob moneda"><?php echo $aprobada_p + $aprobada_l; ?></th>
								<th class="tot-activa moneda"><?php echo $activada_p + $activada_l; ?></th>
								<th class="total moneda"><?php echo $aprobada_p + $$activada_p + $aprobada_l + $$activada_l; ?></th>
							</tr>
						</table>
					</div>
				</div>
<?php
			}
			else
			{
				$color="#D8D8D8";
?>
				<div class="row ml-0 mr-0 pl-0 pt-3 pr-0 justify-content-center">
					<div class="col-11 pt-3 pl-0 pr-3">
						<table class="tablas" width="100%">
							<tr style="background: <?php echo $color; ?>;">
								<th>Promedio inicial</th>
								<th>Promedio final</th>
								<th>Insentivo</th>
								<th></th>
							</tr>
<?php
							$query="Select id_com,promedio_ini,promedio_fin,comision from tabla_com_prom_dp where id_tabla=$id order by id_com";
							$bd=$conecta->consulta($query);
							while($tb=mysqli_fetch_array($bd))
							{
								if($color=="#D8D8D8")
									$color="#FFFFFF";
								else
									$color="#D8D8D8";
?>
								<tr id="<?php echo 'tcc_' . $tb['id_com']; ?>" style="background: <?php echo $color; ?>;">
									<td>
										<input type="text" class="form-control form-control-sm moneda txt-prom_ini" value="<?php echo $tb['promedio_ini']; ?>" data-id="<?php echo $tb['id_com']; ?>">
									</td>
									<td>
										<input type="text" class="form-control form-control-sm moneda txt-prom_fin" value="<?php echo $tb['promedio_fin']; ?>" data-id="<?php echo $tb['id_com']; ?>">
									</td>
									<td>
										<input type="text" class="form-control form-control-sm moneda comision" value="<?php echo $tb['comision']; ?>" data-id="<?php echo $tb['id_com']; ?>">
									</td>
									<td class='pb-1'>
										<img src="../img/borrar1.png" class="btn-eliminar-comp" id="<?php echo 'borrap' . $id; ?>" data-idcom="<?php echo $tb['id_com']; ?>" data-id="<?php echo $id; ?>" style='cursor:pointer;'>
									</td>
								</tr>
<?php
							}
							if($color=="#D8D8D8")
								$color="#FFFFFF";
							else
								$color="#D8D8D8";
?>
							<tr id="<?php echo 'tcc_0'; ?>" style="background: <?php echo $color; ?>;">
								<td>
									<input type="text" class="form-control form-control-sm moneda txt-prom_ini" data-id=0>
								</td>
								<td>
									<input type="text" class="form-control form-control-sm moneda txt-prom_fin" data-id=0>
								</td>
								<td>
									<input type="text" class="form-control form-control-sm moneda comision" data-id=0>
								</td>
								<td>
									<img src="../img/aceptar2.png" class="btn-guardar-comp" id="acepta-comp" data-id="<?php echo $id; ?>" style='cursor:pointer;'>
								</td>
							</tr>
						</table>
					</div>
				</div>
<?php
			}
		break;
		case 10:  /* ----------------------------------------------------------- Guardar comisiones tarjeta Dportenis */
			$id=addslashes(trim($_POST["id"]));
			$com=addslashes(trim($_POST["com"]));
			$tipo=addslashes(trim($_POST["tipo"]));
			$id_com=addslashes(trim($_POST["id_com"]));
			$aprob_p=addslashes(trim($_POST["aprob_p"]));
			$aprob_l=addslashes(trim($_POST["aprob_l"]));
			$activa_p=addslashes(trim($_POST["activa_p"]));
			$activa_l=addslashes(trim($_POST["activa_l"]));
			$prom_ini=str_replace(",","",addslashes(trim($_POST["prom_ini"])));
			$prom_fin=str_replace(",","",addslashes(trim($_POST["prom_fin"])));

			$mensaje="Tabla de comisiones guardada exitosamente";
			if($tipo=='T')
			{
				$bd=$conecta->consulta("Select * from tabla_com_tarjeta_dp where id_tabla=$id");
				if ($bd->num_rows != 0)
					$query="Update tabla_com_tarjeta_dp
							set aprobada_p=$aprob_p, activada_p=$activa_p, aprobada_l=$aprob_l, activada_l=$activa_l
							where id_tabla=$id";
				else
					$query= "Insert into tabla_com_tarjeta_dp values($id,$aprob_p,$activa_p,$aprob_l,$activa_l)";
			}
			else
			{
				if($id_com==0)
				{
					$bd=$conecta->consulta("Select ifnull(max(id_com),0) + 1 as n from tabla_com_prom_dp where id_tabla=$id");
					$tb=mysqli_fetch_array($bd);
					$id_com=$tb['n'];
					$query="Insert into tabla_com_prom_dp values($id,$id_com,$prom_ini,$prom_fin,$com)";
				}
				else
				{
					$query="Delete from tabla_com_prom_dp where id_tabla=$id and id_com=$id_com";
					$mensaje="Tabla de comisiones guardada exitosamente";
				}
			}
			$query=str_replace("$","",$query);
			$conecta->consulta($query);
			echo $mensaje;
		break;
		case 11:  /* ----------------------------------------------------------- Guardar comisiones tarjeta Dportenis */
			$tipo=addslashes(trim($_POST["tipo"]));
			$bd=$conecta->consulta("Select * from tc_deportenis where tipo='$tipo' and activa=1");
			$tc="";
			while($tb=mysqli_fetch_array($bd))
			{
				if($tc=="")
					$tc=$tb['id_tc'] . "|" . $tb['nom_tc'];
				else
					$tc.=">" . $tb['id_tc'] . "|" . $tb['nom_tc'];
			}
			echo $tc;
		break;

		case 12:  /* --------------------------------------------------------------------- Asignar esquema de pagos Dportenis */
			$tipo=addslashes(trim($_POST["tipo"]));
			$valor=addslashes(trim($_POST["valor"]));
			$celula=addslashes(trim($_POST["celula"]));
			$esquema=addslashes(trim($_POST["esquema"]));
			$esquema2=addslashes(trim($_POST["esquema2"]));

			$error=0;
			$q_valor="";
			$q_esquema="";
			if((($tipo=="celula" and $valor != 0) or ($tipo!="celula" and $valor != "")) and $esquema != 0)
			{
				if($tipo=="nivel")
				{
					if($valor != "S")
					{
						$query="Delete from usuarios_comision_dp 
								where ct in (Select no_promotor from empleados_dp where nivel='$valor')";
						$conecta->consulta($query);
						$query="Insert into usuarios_comision_dp
								Select no_promotor,celula,$esquema,1 from empleados_dp where nivel='$valor' and estatus='A'";
						$conecta->consulta($query);
					}
					else
					{
						$query="Delete from usuarios_comision_dp 
								where ct in (Select no_promotor from empleados_dp where nivel='$valor')
									and celula=$celula";
						$conecta->consulta($query);
						$query="Insert into usuarios_comision_dp
								Select no_promotor,c.no_celula,$esquema,1 
								from (Select * from empleados_dp where nivel='$valor' and estatus='A') e 
									inner join (Select * from celulas where no_celula=$celula) c on (e.no_promotor=c.supervisor)";
						$conecta->consulta($query);
					}
				}

				if($tipo=="celula")
				{
					$query="Delete from usuarios_comision_dp 
							where ct in (Select no_promotor from empleados_dp where celula='$valor')";
					$conecta->consulta($query);
					$query="Insert into usuarios_comision_dp
							Select no_promotor,celula,$esquema,1 from empleados_dp where celula='$valor' and estatus='A'";
					$conecta->consulta($query);
				}
				if($tipo=="promotor")
				{
					$t=explode("(", $valor);
					$ct=str_replace(")", "", $t[1]);
 
 					$query="Select ct from usuarios_comision_dp 
 							where ct='$ct' and id_esquema=$esquema and celula=$celula";

					$bd=$conecta->consulta($query);
					if ($bd->num_rows == 0)
					{
						if($celula==1000)
							$query="Insert into usuarios_comision_dp 
									Select no_promotor,celula,$esquema,1 from empleados_dp 
									where no_promotor='$ct'";
						else
							$query="Insert into usuarios_comision_dp 
									Select no_promotor,$celula,$esquema,1 from empleados_dp 
									where no_promotor='$ct'";
						$conecta->consulta($query);
					}
					else
					{
						$error=1;
						echo "Ya existe este esquema para el CT: $ct";
					}
				}
				if($tipo=="segunda" or $tipo=="promotor")
				{
					$t=explode("(", $valor);
					$ct=str_replace(")", "", $t[1]);

					if($tipo=="segunda")
					{
						$query="Insert into usuarios_comision_dp values('$ct',$celula,$esquema,1)";
						$conecta->consulta($query);						
					}

					$q_esquema=" where ct='$ct'";
				}
				if($tipo!="segunda" and $tipo!="promotor")
					$q_esquema=" where id_esquema=$esquema";
				if($error==0)
					echo "Tabla asignada exitosamente|";
			}
			else
			{
				echo "|";
				if($esquema > 0)
					$q_esquema=" where id_esquema=$esquema";
				else
				{
					if($tipo=="nivel")
						$q_valor=" where nivel='$valor' and estatus='A'";
					if($tipo=="celula")
						$q_valor=" where celula='$valor' and estatus='A'";
					if($tipo=="promotor" or $tipo=="segunda")
					{
						$t=explode("(", $valor);
						$ct=str_replace(")", "", $t[1]);
						$q_valor=" where no_promotor='$ct'";
					}
				}
			}
			if($error==0)
			{
				$query="Select ct,promotor,if(nivel='S',cs.celula,c.celula) as celula,t.tienda,nombre_esquema,e.activo,esq.id_esquema,
							   if(nivel='S','Socio Comercial',if(nivel='L','Líder',if(nivel='C','Coordinador','Promotor'))) as nivel
						from (Select * from usuarios_comision_dp $q_esquema) e
							inner join (Select * from empleados_dp $q_valor) edp on (e.ct=edp.no_promotor)
							inner join celulas c on (e.celula=c.no_celula)
							left join celulas cs on (edp.no_promotor=cs.supervisor and e.celula=cs.no_celula)
							inner join tiendas_dp t on (edp.tienda=t.no_tienda)
							inner join (Select distinct id_esquema, nombre_esquema from esquemas_com_dp) esq on (e.id_esquema=esq.id_esquema)
						order by nombre_esquema,promotor";
				$color="#D8D8D8";
?>
				<div class="col-12 pt-0 pr-2" style="height: 250px; overflow-y: auto;">
					<table class="tablas" width="100%">
						<tr style="background: <?php echo $color; ?>;">
							<th width="10%">CT</th>
							<th width="20%">Promotor</th>
							<th width="10%">Nivel</th>
							<th width="20%">Célula</th>
							<th width="17%">Tienda</th>
							<th width="18%">Esquema</th>
							<th width="5%">Activo</th>
						</tr>
<?php
						$bd=$conecta->consulta($query);
						while($tb=mysqli_fetch_array($bd))
						{
							if($color=="#D8D8D8")
								$color="#FFFFFF";
							else
								$color="#D8D8D8";

							$checked="";
							if($tb['activo']==1)
								$checked="checked";
?>
							<tr style="background: <?php echo $color; ?>;">
								<td class="text-left"><?php echo $tb['ct']; ?></td>
								<td class="text-left"><?php echo $tb['promotor']; ?></td>
								<td class="text-left"><?php echo $tb['nivel']; ?></td>
								<td class="text-left"><?php echo $tb['celula']; ?></td>
								<td class="text-left"><?php echo $tb['tienda']; ?></td>
								<td class="text-left"><?php echo $tb['nombre_esquema']; ?></td>
								<td class="text-center">
									<input type='checkbox' class='asignados' data-id="<?php echo $tb['ct'] . '>' . $tb['id_esquema']; ?>" <?php echo $checked; ?>>
								</td>
							</tr>
<?php
						}
?>
					</table>
				</div>
<?php	
			}	
		break;
		case 13:  /* ----------------------------------------------------------- Asignar tablas a usuarios dportenis */
			$id=addslashes(trim($_POST["id_esquema"]));
			$ct=addslashes(trim($_POST["ct"]));
			$valor=addslashes(trim($_POST["valor"]));

			$query="Update usuarios_comision_dp set activo=$valor where ct='$ct' and id_esquema=$id";
			$conecta->consulta($query);
			
			echo "Asignación modificada";
		break;
		case 14:  /* ----------------------------------------------------------- Generar acumulado promedio mensual dportenis */
			$anio=addslashes(trim($_POST["anio"]));
			$mes=addslashes(trim($_POST["mes"]));

			if($mes <10)
				$fec="0" . $mes . "/" . $anio;
			else
				$fec=$mes . "/" . $anio;
			$fec_act=date("Y-m-d");

			$bd=$conecta->consulta("Delete from acum_promedios_dp where pagado=0");
			sleep(2);
			$query="Insert into acum_promedios_dp
					Select distinct p.promotor,np,promedio,comision,np * comision as bono,'',0 
					from (Select promotor, count(*) as np, avg(importe) as promedio 
						  from (Select * from prod_deportenis where right(fecha,7)='$fec' and estatus_sol=1 
									and mot_estatus=1 and est_recepcion='Completo' and estatus_com='A' and importe>=500) p 
	  					  group by promotor) p 
						inner join (Select * from empleados_dp where estatus='A') e on (p.promotor=e.no_promotor) 
						inner join (Select * from usuarios_comision_dp where activo=1) uc on (e.no_promotor=uc.ct) 
						inner join (Select id_tc from tc_deportenis where tipo='P' and activa=1 
										and datediff('$fec_act',concat(substr(fec_ini,-4),'-',substr(fec_ini,4,2),'-',substr(fec_ini,1,2)))>=0 
										and datediff('$fec_act',concat(substr(fec_fin,-4),'-',substr(fec_fin,4,2),'-',substr(fec_fin,1,2)))<=0) t 
							on (uc.id_tabla_comision=t.id_tc) 
						inner join tabla_com_prom_dp tc on (t.id_tc=tc.id_tabla and (promedio>=promedio_ini and promedio<=promedio_fin)) 
					order by promedio desc";
			$bd=$conecta->consulta($query);
			echo 1;
		break;
		case 15:  /* ------------------------------------------------------------------------------------- Generar información Slovo */
			$estatus = addslashes(trim($_POST["sel-estatus"]));
			$nss = addslashes(trim($_POST["txt-nss"]));
			$nombre = addslashes(trim($_POST["txt-nombre"]));
			$rfc = addslashes(trim($_POST["txt-rfc"]));
			$curp = addslashes(trim($_POST["txt-curp"]));
			$comisionista = addslashes(trim($_POST["txt-asesor"]));
			$gerencia = addslashes(trim($_POST["txt-gerencia"]));
			$ref_bancaria = addslashes(trim($_POST["txt-ref-bancaria"]));
			$cred_infonavit = addslashes(trim($_POST["sel-infonavit"]));
			$no_credito = addslashes(trim($_POST["txt-no-credito"]));
			$enfermo = addslashes(trim($_POST["sel-enfermedad"]));
			$enfermedad = addslashes(trim($_POST["txt-enfermedad"]));
			$sal_diario = addslashes(trim($_POST["txt-sal-diario"]));
			$cuota_men = addslashes(trim($_POST["cuota"]));
			$fec_env_slovo = addslashes(trim($_POST["txt-fec-slovo"]));
			$fec_alta = addslashes(trim($_POST["txt-fec-alta"]));
			$mot_alta = addslashes(trim($_POST["txt-mot-alta"]));
			$fec_reactiva = addslashes(trim($_POST["txt-fec-reactiva"]));
			$fec_baja = addslashes(trim($_POST["txt-fec-baja"]));
			$mot_baja = addslashes(trim($_POST["txt-mot-baja"]));
			$tel = addslashes(trim($_POST["txt-tel"]));
			$cel = addslashes(trim($_POST["txt-cel"]));
			$email = addslashes(trim($_POST["txt-email"]));
			$dir = addslashes(trim($_POST["txt-dir"]));
			$cp = addslashes(trim($_POST["txt-cp"]));
			$rs_alta = addslashes(trim($_POST["txt-rs-alta"]));
			$rs_actual = addslashes(trim($_POST["txt-rs-actual"]));
			$llamada = addslashes(trim($_POST["txt-llamada"]));
			$embarazo = addslashes(trim($_POST["sel-embarazo"]));
			$meses = addslashes(trim($_POST["meses"]));
			$observa_carobra = addslashes(trim($_POST["txt-observa-carobra"]));

			$sal_diario = str_replace(",", "", str_replace("$", "", $sal_diario)) * 1;
			$cuota_men = str_replace(",", "", str_replace("$", "", $cuota_men)) * 1;

			$nombreCompleto = preg_replace('/\s*\(.*?\)/', '', $nombre);
			$nombreCompleto = trim($nombreCompleto);

			$clave_permiso = addslashes(trim($_POST["txt-permiso"]));

			if (preg_match('/^\d+/', $nss, $matches)) 
			{
				$nssLimpio = $matches[0];
			} 
			else 
			{
				$nssLimpio = $nss;
			}

			if ($meses == "") 
			{
				$meses = 0;
			}

			$puedeActualizar = ($clave_permiso === "administra" || $clave_permiso === "MAFAVENHER");
			$puedeActualizar=true;
			$mensajeExito = "";
			$mensajeError = "";

			$bd = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE nombre='$comisionista'");
			$tb = mysqli_fetch_array($bd);
			$clave = $tb["clave"];

			$bd = $conecta->consulta("SELECT clave_asesor FROM slovo_clientes WHERE clave_asesor='$clave'");
			if ($bd->num_rows == 0) 
			{
				$conecta->consulta("INSERT INTO permisos VALUES('$clave', 88)");
				usleep(500000);
			}

			$bd = $conecta->consulta("SELECT nss FROM slovo_clientes WHERE nss='$nssLimpio'");
			if ($bd->num_rows == 0) 
			{
				$query = "INSERT INTO slovo_clientes VALUES ('$nssLimpio','$nombreCompleto','$curp','$rfc',$sal_diario,$cuota_men,'$ref_bancaria','$cred_infonavit','$no_credito','$clave','$enfermo','$enfermedad','$embarazo',$meses,'$estatus')";
				$conecta->consulta($query);
				usleep(500000);
				$mensajeExito = "Información guardada exitosamente";
			} 
			else 
			{
				if ($puedeActualizar) 
				{
					$query = "UPDATE slovo_clientes 
							SET nombre='$nombreCompleto', curp='$curp', rfc='$rfc', sal_diario=$sal_diario, cuota_mensual=$cuota_men, ref_bancaria='$ref_bancaria', cred_infonavit='$cred_infonavit', no_credito='$no_credito', clave_asesor='$clave', enfermo='$enfermo', enfermedad='$enfermedad', embarazo='$embarazo', meses_embarazo=$meses, estatus='$estatus' 
							WHERE nss='$nssLimpio'";
					$conecta->consulta($query);
					usleep(500000);
					$mensajeExito = "Información actualizada exitosamente";
				} 
				else 
				{
					$mensajeError = "No tiene permisos para actualizar la información";
				}
			}


			$bd = $conecta->consulta("SELECT nss FROM slovo_contacto WHERE nss='$nssLimpio'");
			if ($bd->num_rows == 0) 
			{
				$query = "INSERT INTO slovo_contacto VALUES ('$nssLimpio', '$tel', '$cel', '$email', '$dir', '$cp')";
				$conecta->consulta($query);
				usleep(500000);
			} 
			else 
			{
				if ($puedeActualizar) 
				{
					$query = "UPDATE slovo_contacto SET tel='$tel', cel='$cel', email='$email', direc='$dir', cp='$cp' WHERE nss='$nssLimpio'";
					$conecta->consulta($query);
					usleep(500000);
				}
			}


			$bd = $conecta->consulta("SELECT nss FROM slovo_fechas WHERE nss='$nssLimpio'");
			if ($bd->num_rows == 0) 
			{
				$query = "INSERT INTO slovo_fechas VALUES ('$nssLimpio', '$fec_env_slovo', '$fec_alta', '$fec_reactiva', '$fec_baja', '$mot_alta', '$mot_baja')";
				$conecta->consulta($query);
				usleep(500000);
			} 
			else 
			{
				if ($puedeActualizar) 
				{
					$query = "UPDATE slovo_fechas SET fec_envio_slovo='$fec_env_slovo', fec_alta='$fec_alta', fec_reactiva='$fec_reactiva', fec_baja='$fec_baja', motivo_alta='$mot_alta', motivo_baja='$mot_baja' WHERE nss='$nssLimpio'";
					$conecta->consulta($query);
					usleep(500000);
				}
			}

			$bd = $conecta->consulta("SELECT nss FROM slovo_otros WHERE nss='$nssLimpio'");
			if ($bd->num_rows == 0) 
			{
				$query = "INSERT INTO slovo_otros VALUES ('$nssLimpio', '$rs_alta', '$rs_actual', '$llamada', '$observa_carobra')";
				$conecta->consulta($query);
			} 
			else 
			{
				if ($puedeActualizar) 
				{
					$query = "UPDATE slovo_otros SET razon_soc_alta='$rs_alta', razon_soc_actual='$rs_actual', llamada_servicio='$llamada', observa_carobra='$observa_carobra' WHERE nss='$nssLimpio'";
					$conecta->consulta($query);
				}
			}

			if ($mensajeError !== "") 
			{
				echo $mensajeError;
			} 
			else 
			{
				echo $mensajeExito;
			}
		break;
		case 16:  /* ----------------------------------------------------------- Generar acumulado promedio mensual dportenis */
			$nt=addslashes(trim($_POST["no_tramite"]));
			$nss=addslashes(trim($_POST["nss"]));

			if($nss != "")
			{
				$bd=$conecta->consulta("Select nss from slovo_gen where nss='$nss' and no_tramite != $nt");
				if ($bd->num_rows != 0)
					echo "";
				else
					echo "ok";
			}
			else
				echo "ok";
		break;
		case 17:  /* --------------------------------------------------------------------- Actualizar factura de archivo PDF */
			$fecha=addslashes(trim($_POST["fecha"]));
			$folio=addslashes(trim($_POST["folio"]));
			$clave=addslashes(trim($_POST["clave"]));
			$estatus=addslashes(trim($_POST["estatus"]));
			$retenido=addslashes(trim($_POST["retenido"]));
			$fec_pago=addslashes(trim($_POST["fec_pago"]));
			$tipo_fac=addslashes(trim($_POST["tipo-fac"]));
			$archivo=addslashes(trim($_POST["nom-archivo"]));
			$asesor=addslashes(trim($_POST["txt-asesor"]));

			$fec_bloqueo="";
			$estatus_bloquea=0;
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
					$estatus_bloquea=1;
			}

			$query="Update facturas 
					set estatus_fac=$estatus, nom_pdf='$archivo', retenido=if($estatus=4,if($estatus_bloquea=0,0,2),1), fec_fac='$fecha',
					    no_factura='$folio', fec_subir='" . date("d/m/Y") . "'
					where fec_pago='$fec_pago' and clave='$clave' and tipo='$tipo_fac'";
			$conecta->consulta($query);
			if($estatus_bloquea==1 and $estatus==4)
				mensaje("Tu factura subi\u00F3 despu\u00E9s del tiempo l\u00EDmite para ser considerada en el pago de este d\u00EDa. Ser\u00E1 considerada para la siguiente fecha de pago");
?>
			<form  id="frmenvio" action="../facturas.php" method="post">
				<input type='hidden' id="mensaje" name='mensaje'>
				<input type='hidden' id="txt-asesor" name='txt-asesor' value='<?php echo  utf8_decode($asesor);?>'>
			</form>
<script type="text/javascript">
			document.getElementById("frmenvio").submit();
</script>
<?php
		break;
		case 18:  /* --------------------------------------------------------------------- Actualizar factura de archivo PDF */
			$nombre=addslashes(trim($_POST["nombre"]));

			$bd=$conecta->consulta("Select nombre from empl_usuarios where nombre='$nombre' and nombre !=''");
			if ($bd->num_rows != 0)
				echo "1";
			else
			{
				if($nombre != "")
				{				
					$nombres="";
					$nom=explode(" ",$nombre);
					$i=0;
					$nom1="";
					$nom2="";
					$nom3="";
					$nom4="";
					while($i <= (count($nom)-1))
					{
						$nom1.=$nom[$i] . " ";
						if($i!=0)
							$nom2.=$nom[$i] . " ";
						if($i!=1)
							$nom3.=$nom[$i] . " ";
						if($i!=2)
							$nom4.=$nom[$i] . " ";
						$i++;
					}
					$nom1=trim($nom1);
					$nom2=trim($nom2);
					$nom3=trim($nom3);

					$bd=$conecta->consulta("Select nombre from empl_usuarios where nombre like '%$nom1%'");
					if ($bd->num_rows != 0)
					{
						while($tb=mysqli_fetch_array($bd))
						{
							if($nombres=='')
								$nombres=$tb['nombre'];
							else
								$nombres.="\n" . $tb['nombre'];
						}
					}
					$bd=$conecta->consulta("Select nombre from empl_usuarios where nombre like '%$nom2%'");
					if ($bd->num_rows != 0)
					{
						while($tb=mysqli_fetch_array($bd))
						{
							if($nombres=='')
								$nombres=$tb['nombre'];
							else
								$nombres.="\n" . $tb['nombre'];
						}
					}
					$bd=$conecta->consulta("Select nombre from empl_usuarios where nombre like '%$nom3%'");
					if ($bd->num_rows != 0)
					{
						while($tb=mysqli_fetch_array($bd))
						{
							if($nombres=='')
								$nombres=$tb['nombre'];
							else
								$nombres.="\n" . $tb['nombre'];
						}
					}
					$bd=$conecta->consulta("Select nombre from empl_usuarios where nombre like '%$nom4%'");
					if ($bd->num_rows != 0)
					{
						while($tb=mysqli_fetch_array($bd))
						{
							if($nombres=='')
								$nombres=$tb['nombre'];
							else
								$nombres.="\n" . $tb['nombre'];
						}
					}
					if($nombres != "")
						echo "Existen nombres parecidos al nombre del nuevo usuario ingresado:\n\n" . $nombres;
				}
			}
		break;
		case 19:  /* ------------------------------------------------------------- Marcar solicitudes Dportenis duplicadas */
			$sol=addslashes(trim($_POST["sol"]));

			$conecta->consulta("Delete from sol_dp_duplicadas where solicitud='$sol'");
			usleep(500000);
			$conecta->consulta("Insert into sol_dp_duplicadas values('$sol')");
			usleep(500000);
			$conecta->consulta("Update prod_deportenis set est_recepcion='Solicitud duplicada' where solicitud='$sol'");
			echo "Solicitud marcada como duplicada";
		break;
		case 20:  /* ------------------------------------------------------------- Cambio de CT en solicitud de Dportenis */
			$ct=addslashes(trim($_POST["ct"]));
			$sol=addslashes(trim($_POST["sol"]));

			$cts=explode(" ",$ct);
			$ct=$cts[0];
			$conecta->consulta("Delete from sol_dp_cts where solicitud='$sol'");
			usleep(500000);
			$conecta->consulta("Insert into sol_dp_cts values('$sol','$ct')");
			usleep(500000);
			$conecta->consulta("Update prod_deportenis set promotor='$ct' where solicitud='$sol'");
			echo "Cambio de CT en solicitud exitosa";
		break;
		case 21:  /* ------------------------------------------------------------------- Visualizar/ocultar diapositiva */
			$id=addslashes(trim($_POST["id"]));
			$estatus=addslashes(trim($_POST["estatus"]));

			if($estatus==1)
			{
				$query="Select max(orden) + 1 as orden from diapositivas";
				$bd=$conecta->consulta($query);

				$tb=mysqli_fetch_array($bd);
				$orden=$tb['orden'];
				$query="Update diapositivas set estatus=$estatus, orden=$orden where id=$id";
			}
			else
				$query="Update diapositivas set estatus=$estatus, orden=0 where id=$id";
			$conecta->consulta($query);
			echo "Estatus de diapositiva actualizado con éxito";
		break;
		case 22:  /* ------------------------------------------------------------------------------- Eliminar diapositiva */
			$id=addslashes(trim($_POST["id"]));

			$conecta->consulta("Delete from diapositivas where id=$id");
			echo "Diapositiva eliminada con éxito";
		break;

		case 23:  /* ------------------------------------------------------------------------------- Visualizar diapositivas activas y su orden */
			$bd=$conecta->consulta("Select id,titulo, orden from diapositivas where estatus=1 order by orden");
?>
			<table style="width: 95%; margin: 10px;">
<?php
				$i=1;
				$color="#EFEFEF";
				while($tb=mysqli_fetch_array($bd))
				{
					if($color=="#EFEFEF")
						$color="#FFFFFF";
					else
						$color="#EFEFEF";
?>
					<tr>
						<th style="text-align: left;border: 1px solid #AEAEAE; padding-left: 5px; background: <?php echo $color?>;">
							<?php echo $tb['titulo']; ?>
						</th>
						<td style="border: 1px solid #AEAEAE; background: <?php echo $color?>;">
<?php
							if($i > 1)
							{
?>
								<img src="./img/btn_subir.png" class='o-subir' width="25px" data-id="<?php echo $tb['id']; ?>" data-orden="<?php echo $tb['orden']; ?>" style="cursor: pointer;">
<?php
							}
?>
						</td>
						<td style="border: 1px solid #AEAEAE; background: <?php echo $color?>;">
<?php
							if($i < $bd->num_rows)
							{
?>
								<img src="./img/btn_bajar.png" class='o-bajar' width="25px" data-id="<?php echo $tb['id']; ?>" data-orden="<?php echo $tb['orden']; ?>" style="cursor: pointer;">
<?php
							}
?>
						</td>
					</tr>
<?php
					$i++;
				}
?>
			</table>
<?php
		break;
		case 24:  /* ------------------------------------------------------------------------------- Eliminar diapositiva */
			$id=addslashes(trim($_POST["id"]));
			$mov=addslashes(trim($_POST["mov"]));
			$orden=addslashes(trim($_POST["orden"]));

			if($mov=="s")
				$query="Select id,orden from diapositivas where orden<$orden
						order by orden desc limit 0,1";
			else
				$query="Select id,orden from diapositivas where orden>$orden
						order by orden limit 0,1";

			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$id_t=$tb["id"];
			$orden_t=$tb["orden"];

			$conecta->consulta("Update diapositivas set orden=$orden_t where id=$id");
			$conecta->consulta("Update diapositivas set orden=$orden where id=$id_t");
			
			echo "t";
		break;
		case 25:  /* ------------------------------------------------------------------------------- Eliminar diapositiva */
			$folio=addslashes(trim($_POST["cfolio"]));
			$nombre=addslashes(trim($_POST["cnombre"]));
			$nss=addslashes(trim($_POST["cnss"]));
			$curp=addslashes(trim($_POST["ccurp"]));
			$fec_traspaso=addslashes(trim($_POST["cfec_traspaso"]));
			$mov=addslashes(trim($_POST["cmov"]));
			$nap=addslashes(trim($_POST["cnap"]));
			$saldo=addslashes(trim($_POST["csaldo"]));
			$sello="";
			$estatus=addslashes(trim($_POST["cestatus"]));

			$query="Select no_empleado from empl_usuarios 
					where nombre='" . utf8_encode($_SESSION["nombre"]) . "'";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$analista=$tb["no_empleado"];

			$saldo=str_replace(",","",str_replace("$","",$saldo));
			$bd=$conecta->consulta("Select * from produccion where folio='$folio'");
			if ($bd->num_rows == 0)
			{
				$bd=$conecta->consulta("Select * from naps where nap='$nap' and baja=0");
				if ($bd->num_rows != 0)
				{
					$saldo=str_replace(",","",str_replace("$","",$saldo));
					$query="Insert into produccion values('$folio','" . date("d/m/Y") . "','$nss',$analista,0,$mov,'$fec_traspaso','','$nap',$saldo,0,0,0,'','',0,0,$estatus,0,'SI','','',0)";
					$conecta->consulta($query);
					usleep(100000);
					$query="Insert into prod_trabajador values('$folio','','$nombre','','','','','$curp','',0,'$sello',0,'')";
					$conecta->consulta($query);
					echo "El folio $folio fue capturado exitosamente";
				}
				else
					echo "Este NAP no existe en el sistema o está dado de baja. NO se guardó la información";				
			}
			else
			{
				$query="Select * from produccion 
						where folio='$folio' and id_usuario_cap=0 and no_empleado=0
							and saldo_estimado=0 and afo_cedente=0 and id_estatus_prod=1";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$bd=$conecta->consulta("Select * from naps where nap='$nap' and baja=0");
					if ($bd->num_rows != 0)
					{
						$query="Update produccion 
								set nss='$nss', nap='$nap', saldo_estimado=$saldo, id_usuario_cap=$analista
								where folio='$folio'";
						$conecta->consulta($query);
						$query="Update prod_trabajador set CURP='$curp', trabajador='$nombre'
								where folio='$folio'";
						$conecta->consulta($query);
						echo "El folio $folio fue capturado exitosamente";
					}
					else 
						echo "Este NAP no existe en el sistema o está dado de baja. NO se guardó la información";
				}
				else
					echo "Este folio ya existe en el sistema. NO se guardó la información";
			}
		break;
		case 26:  /* ------------------------------------------------------------------------------- Eliminar diapositiva */
			$folio=addslashes(trim($_POST["cfolio"]));
			$nombre=addslashes(trim($_POST["cnombre"]));
			$nss=addslashes(trim($_POST["cnss"]));
			$curp=addslashes(trim($_POST["ccurp"]));
			$fec_envio=addslashes(trim($_POST["cfec-envio"]));
			$nap=addslashes(trim($_POST["cnap"]));
			$monto=addslashes(trim($_POST["cmonto"]));
			$estatus=addslashes(trim($_POST["cestatus"]));
			$no_tramite=addslashes(trim($_POST["ctramite"]));
			$frecuencia=addslashes(trim($_POST["cfrecuencia"]));
			$fec_envio_banco=addslashes(trim($_POST["cfec-envio-banco"]));
			$fec_pendiente=addslashes(trim($_POST["cfec-pendiente"]));
			$fec_rechazo=addslashes(trim($_POST["cfec-rechazo"]));
			$fec_cancela=addslashes(trim($_POST["cfec-cancela"]));
			$dia_cobro=addslashes(trim($_POST["cdia-cobro"]));

			$bd=$conecta->consulta("Select * from av_general where folio='$folio'");
			if ($bd->num_rows == 0)
			{
				$bd=$conecta->consulta("Select * from av_general where tramite='$no_tramite'");
				if ($bd->num_rows == 0)
				{
					$bd=$conecta->consulta("Select * from naps where nap='$nap' and baja=0");
					if ($bd->num_rows != 0)
					{
						$monto=str_replace(",","",str_replace("$","",$monto));

						$query="Insert into av_general values('P','$folio','$no_tramite',0,0,0,0,'$nss','$curp','$nap','$nombre','',0,'','','')";
						$conecta->consulta($query);
						usleep(100000);
						$query="Insert into av_fechas values('$folio','$no_tramite','" . date("d/m/Y") . "','$fec_envio','$fec_envio_banco','','','','$fec_cancela','','','','',0,'$fec_rechazo')";
						$conecta->consulta($query);
						usleep(100000);
						$query="Insert into av_pagos values('$folio','$no_tramite','$estatus','','','','$frecuencia','$dia_cobro','','','','',0,$monto,0,'$fec_pendiente')";
						$conecta->consulta($query);
						usleep(100000);

						echo "El folio $folio fue capturado exitosamente";
					}
					else
						echo "Este NAP no existe en el sistema o está dado de baja. NO se guardó la información";
				}
				else
					echo "Este número de trámite ya existe en el sistema. NO se guardó la información";
			}
			else
				echo "Este folio ya existe en el sistema. NO se guardó la información";
		break;
		case 27:  /* ------------------------------------------------------------------------------- Valida folio para captura producción */
			$folio=addslashes(trim($_POST["folio"]));
			$nombre=addslashes(trim($_POST["nombre"]));
			$nss=addslashes(trim($_POST["nss"]));
			$curp=addslashes(trim($_POST["curp"]));
			$fec_traspaso=addslashes(trim($_POST["fec_traspaso"]));
			$nap=addslashes(trim($_POST["nap"]));
			$tipo_mov=addslashes(trim($_POST["tipo_mov"]));
			$celular=addslashes(trim($_POST["celular"]));
			$email=addslashes(trim($_POST["email"]));
			$cp=addslashes(trim($_POST["cp"]));
			$afore=addslashes(trim($_POST["afore"]));
?>
			<form  id="frmenvio" action="../cap_prod.php" method="post">
				<input type='hidden' id="mensaje" name='mensaje'>
				<input type='hidden' id="cfolio" name='cfolio' value='<?php echo $folio; ?>'>
			</form>
<?php				
			$bd=$conecta->consulta("Select cierre_cap from produccion where folio='$folio'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$fc=$tb["cierre_cap"];
				if($fc==1)
				{
?>
<script type="text/javascript">
					document.getElementById("mensaje").value="Se cerró la captura del folio " + <?php echo $folio; ?> + " ya no puede ser modificado";
					document.getElementById("cfolio").value="";
					document.getElementById("frmenvio").submit();
</script>
<?php
				}
				else
				{
					if($afore=="")
						$afo=0;
					else
					{
						$bd=$conecta->consulta("Select clave from afores where afore='$afore'");
						if ($bd->num_rows == 0)
						{
							$bd=$conecta->consulta("Select clave from afores where afore like '%$afore%'");
							if ($bd->num_rows == 0)
							{
								$bd=$conecta->consulta("Select clave from afores where '$afore' like concat('%',afore,'%')");
								if ($bd->num_rows == 0)
									$afo=0;
								else
								{
									$tb=mysqli_fetch_array($bd);
									$afo=$tb["clave"];
								}
							}
							else
							{
								$tb=mysqli_fetch_array($bd);
								$afo=$tb["clave"];
							}
						}
						else
						{
							$tb=mysqli_fetch_array($bd);
							$afo=$tb["clave"];
						}
					}

					$query="Update produccion set nss='$nss', fec_traspaso='$fec_traspaso', nap='$nap', id_tipo_mov=$tipo_mov, afo_cedente=$afo 
							where folio='$folio'";
					$conecta->consulta($query);
					usleep(500000);

					$query="Update prod_trabajador set celular='$celular', trabajador='$nombre', curp='$curp', email_trab='$email', cp='$cp'
							where folio='$folio'";
					$conecta->consulta($query);
					usleep(500000);
?>
<script type="text/javascript">
					document.getElementById("mensaje").value="Información del folio interfasada con éxito";
					document.getElementById("frmenvio").submit();
</script>
<?php
				}
			}
			else
			{
?>
<script type="text/javascript">
				document.getElementById("mensaje").value="La información del folio no puede ser interfazado por que aún no se sube desde la interface de producción";
				document.getElementById("cfolio").value="";
				document.getElementById("frmenvio").submit();
</script>
<?php
			}
		break;
		case 28:  /* -------------------------------------------------------------------------------------------- Selecciona cuota Slovo */
			$salario=addslashes(trim($_POST["salario"]));
				$bd=$conecta->consulta("Select cuota from slovo_sal_cuotas where salario=$salario");
				$tb=mysqli_fetch_array($bd);
				echo "$" . number_format($tb["cuota"], 2, '.', ',');
		break;
		case 29:  /* ------------------------------------------------------------------ Detectar posibles duplicados en clientes de Slovo*/
			$cliente=addslashes(trim($_POST["cliente"]));
			$bd=$conecta->consulta("Select nss from slovo_clientes where nombre='$cliente'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$nss=$tb["nss"];
				echo "Este nombre ya fue asociado al NSS $nss. ¿Continuar de todas formas?";
			}
			else
				echo "ok";
		break;
		case 30:  /* ------------------------------------------------------------------ Detectar supervisor de células de Dportenis*/
			$supervisor=addslashes(trim($_POST["supervisor"]));
			$celula=addslashes(trim($_POST["celula"]));

			$query="Select no_promotor 
					from (Select no_promotor from empleados_dp where promotor='$supervisor') e
						inner join (Select supervisor from celulas where no_celula=$celula) c on (e.no_promotor=c.supervisor)";
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
				echo "Ok";
			else
				echo "No";
		break;
		case 31:  /* ------------------------------------------------------------------ Asignar Socio Comercial al seleccionar célula*/
			$celula=addslashes(trim($_POST["celula"]));
			
			$query="Select ifnull(promotor,'') as socio
					from (Select * from celulas where no_celula=$celula) c
						left join (Select no_promotor,promotor from empleados_dp where no_promotor != '') s 
									on (c.supervisor=s.no_promotor)";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			echo $tb['socio'];
		break;
		case 32:  /* ------------------------------------------------------------------ Asignar Coordinador y estado al seleccionar tienda*/
			$tienda=addslashes(trim($_POST["tienda"]));
			
			$t=explode(" ", $tienda);
			$query="Select alias,ifnull(promotor,'') as coordinador
					from (Select * from tiendas_dp where no_tienda=" . $t[0] . ") t
						left join (Select no_promotor,promotor from empleados_dp where no_promotor != '') s 
								on (t.coordinador=s.no_promotor)
						left join estados e on (t.estado=e.no_edo)
								";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);

			echo $tb['alias'] . "|" . $tb['coordinador'];
		break;
		case 33:  /* ------------------------------------------------------------------ Detectar coordinador de tienda de Dportenis*/
			$coordinador=addslashes(trim($_POST["coordinador"]));
			$tienda=addslashes(trim($_POST["tienda"]));

			$t=explode(" ",$tienda);
			$query="Select no_promotor
					from (Select no_promotor from empleados_dp where promotor='$coordinador') e
						inner join (Select coordinador from tiendas where no_tienda=" . $t[0] . ") c on (e.no_promotor=c.coordinador)";
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
				echo "Ok";
			else
				echo "No";
		break;
		case 34:  /* ------------------------------------------------------------------------ Guardar o actualizar esquema de pago*/
			$esquema=addslashes(trim($_POST["txt-nom-esquema"]));
			$tipo_com_sc=addslashes(trim($_POST["sel-tipo-sc"]));
			$com_compra1_msi_sc=addslashes(trim($_POST["txt-scom_compra1-msi"]));
			$com_compra1_mci_sc=addslashes(trim($_POST["txt-scom_compra1-mci"]));
			$com_compra1_mic_sc=addslashes(trim($_POST["txt-scom_compra1-mic"]));
			$com_compra1_crj_sc=addslashes(trim($_POST["txt-scom_compra1-crj"]));
			$com_compra1_activa_sc=addslashes(trim($_POST["txt-scom_compra1-activa"]));
			$com_compra1_aprobada_sc=addslashes(trim($_POST["txt-scom_compra1-aprobada"]));

/*
			$com_compra2_msi_sc=addslashes(trim($_POST["txt-scom_compra2-msi"]));
			$com_compra2_mci_sc=addslashes(trim($_POST["txt-scom_compra2-mci"]));
			$com_compra2_mic_sc=addslashes(trim($_POST["txt-scom_compra2-mic"]));
			$com_compra2_crj_sc=addslashes(trim($_POST["txt-scom_compra2-crj"]));
*/
			$tipo_com_l=addslashes(trim($_POST["sel-tipo-l"]));
			$com_compra1_msi_l=addslashes(trim($_POST["txt-lcom_compra1-msi"]));
			$com_compra1_mci_l=addslashes(trim($_POST["txt-lcom_compra1-mci"]));
			$com_compra1_mic_l=addslashes(trim($_POST["txt-lcom_compra1-mic"]));
			$com_compra1_crj_l=addslashes(trim($_POST["txt-lcom_compra1-crj"]));
			$com_compra1_activa_l=addslashes(trim($_POST["txt-lcom_compra1-activa"]));
			$com_compra1_aprobada_l=addslashes(trim($_POST["txt-lcom_compra1-aprobada"]));
/*
			$com_compra2_msi_l=addslashes(trim($_POST["txt-lcom_compra2-msi"]));
			$com_compra2_mci_l=addslashes(trim($_POST["txt-lcom_compra2-mci"]));
			$com_compra2_mic_l=addslashes(trim($_POST["txt-lcom_compra2-mic"]));
			$com_compra2_crj_l=addslashes(trim($_POST["txt-lcom_compra2-crj"]));
*/
			$tipo_com_p=addslashes(trim($_POST["sel-tipo-p"]));
			$com_compra1_msi_p=addslashes(trim($_POST["txt-pcom_compra1-msi"]));
			$com_compra1_mci_p=addslashes(trim($_POST["txt-pcom_compra1-mci"]));
			$com_compra1_mic_p=addslashes(trim($_POST["txt-pcom_compra1-mic"]));
			$com_compra1_crj_p=addslashes(trim($_POST["txt-pcom_compra1-crj"]));
			$com_compra1_activa_p=addslashes(trim($_POST["txt-pcom_compra1-activa"]));
			$com_compra1_aprobada_p=addslashes(trim($_POST["txt-pcom_compra1-aprobada"]));
/*
			$com_compra2_msi_p=addslashes(trim($_POST["txt-pcom_compra2-msi"]));
			$com_compra2_mci_p=addslashes(trim($_POST["txt-pcom_compra2-mci"]));
			$com_compra2_mic_p=addslashes(trim($_POST["txt-pcom_compra2-mic"]));
			$com_compra2_crj_p=addslashes(trim($_POST["txt-pcom_compra2-crj"]));
*/
			$tipo_com_c=addslashes(trim($_POST["sel-tipo-c"]));
			$com_compra1_msi_c=addslashes(trim($_POST["txt-ccom_compra1-msi"]));
			$com_compra1_mci_c=addslashes(trim($_POST["txt-ccom_compra1-mci"]));
			$com_compra1_mic_c=addslashes(trim($_POST["txt-ccom_compra1-mic"]));
			$com_compra1_crj_c=addslashes(trim($_POST["txt-ccom_compra1-crj"]));
			$com_compra1_activa_c=addslashes(trim($_POST["txt-ccom_compra1-activa"]));
			$com_compra1_aprobada_c=addslashes(trim($_POST["txt-ccom_compra1-aprobada"]));
/*
			$com_compra2_msi_c=addslashes(trim($_POST["txt-ccom_compra2-msi"]));
			$com_compra2_mci_c=addslashes(trim($_POST["txt-ccom_compra2-mci"]));
			$com_compra2_mic_c=addslashes(trim($_POST["txt-ccom_compra2-mic"]));
			$com_compra2_crj_c=addslashes(trim($_POST["txt-ccom_compra2-crj"]));
*/
			$car = array("%", "$", ",");
			$com_compra1_msi_sc = str_replace($car, "", $com_compra1_msi_sc);
			$com_compra1_mci_sc = str_replace($car, "", $com_compra1_mci_sc);
			$com_compra1_mic_sc = str_replace($car, "", $com_compra1_mic_sc);
			$com_compra1_crj_sc = str_replace($car, "", $com_compra1_crj_sc);
			$com_compra1_activa_sc = str_replace($car, "", $com_compra1_activa_sc);
			$com_compra1_aprobada_sc = str_replace($car, "", $com_compra1_aprobada_sc);
/*
			$com_compra2_msi_sc = str_replace($car, "", $com_compra2_msi_sc);
			$com_compra2_mci_sc = str_replace($car, "", $com_compra2_mci_sc);
			$com_compra2_mic_sc = str_replace($car, "", $com_compra2_mic_sc);
			$com_compra2_crj_sc = str_replace($car, "", $com_compra2_crj_sc);
*/
			$com_compra1_msi_l = str_replace($car, "", $com_compra1_msi_l);
			$com_compra1_mci_l = str_replace($car, "", $com_compra1_mci_l);
			$com_compra1_mic_l = str_replace($car, "", $com_compra1_mic_l);
			$com_compra1_crj_l = str_replace($car, "", $com_compra1_crj_l);
			$com_compra1_activa_l = str_replace($car, "", $com_compra1_activa_l);
			$com_compra1_aprobada_l = str_replace($car, "", $com_compra1_aprobada_l);
/*
			$com_compra2_msi_l = str_replace($car, "", $com_compra2_msi_l);
			$com_compra2_mci_l = str_replace($car, "", $com_compra2_mci_l);
			$com_compra2_mic_l = str_replace($car, "", $com_compra2_mic_l);
			$com_compra2_crj_l = str_replace($car, "", $com_compra2_crj_l);
*/
			$com_compra1_msi_p = str_replace($car, "", $com_compra1_msi_p);
			$com_compra1_mci_p = str_replace($car, "", $com_compra1_mci_p);
			$com_compra1_mic_p = str_replace($car, "", $com_compra1_mic_p);
			$com_compra1_crj_p = str_replace($car, "", $com_compra1_crj_p);
			$com_compra1_activa_p = str_replace($car, "", $com_compra1_activa_p);
			$com_compra1_aprobada_p = str_replace($car, "", $com_compra1_aprobada_p);
/*
			$com_compra2_msi_p = str_replace($car, "", $com_compra2_msi_p);
			$com_compra2_mci_p = str_replace($car, "", $com_compra2_mci_p);
			$com_compra2_mic_p = str_replace($car, "", $com_compra2_mic_p);
			$com_compra2_crj_p = str_replace($car, "", $com_compra2_crj_p);
*/
			$com_compra1_msi_c = str_replace($car, "", $com_compra1_msi_c);
			$com_compra1_mci_c = str_replace($car, "", $com_compra1_mci_c);
			$com_compra1_mic_c = str_replace($car, "", $com_compra1_mic_c);
			$com_compra1_crj_c = str_replace($car, "", $com_compra1_crj_c);
			$com_compra1_activa_c = str_replace($car, "", $com_compra1_activa_c);
			$com_compra1_aprobada_c = str_replace($car, "", $com_compra1_aprobada_c);
/*
			$com_compra2_msi_c = str_replace($car, "", $com_compra2_msi_c);
			$com_compra2_mci_c = str_replace($car, "", $com_compra2_mci_c);
			$com_compra2_mic_c = str_replace($car, "", $com_compra2_mic_c);
			$com_compra2_crj_c = str_replace($car, "", $com_compra2_crj_c);
*/
			$bd=$conecta->consulta("Select id_esquema from esquemas_com_dp where nombre_esquema='$esquema'");
			if ($bd->num_rows == 0)
			{
				$bd=$conecta->consulta("Select ifnull(max(id_esquema),0) + 1 as id_esquema from esquemas_com_dp");
				$tb=mysqli_fetch_array($bd);
				$id_esquema=$tb["id_esquema"];

				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','SC',1,'$tipo_com_sc',$com_compra1_msi_sc,$com_compra1_mci_sc,$com_compra1_mic_sc,$com_compra1_crj_sc,$com_compra1_activa_sc,$com_compra1_aprobada_sc)";
				$conecta->consulta($query);
/*
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','SC',2,'$tipo_com_sc',$com_compra2_msi_sc,$com_compra2_mci_sc,$com_compra2_mic_sc,$com_compra2_crj_sc)";
				$conecta->consulta($query);
*/
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','L',1,'$tipo_com_l',$com_compra1_msi_l,$com_compra1_mci_l,$com_compra1_mic_l,$com_compra1_crj_l,$com_compra1_activa_l,$com_compra1_aprobada_l)";
				$conecta->consulta($query);
/*
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','L',2,'$tipo_com_l',$com_compra2_msi_l,$com_compra2_mci_l,$com_compra2_mic_l,$com_compra2_crj_l)";
				$conecta->consulta($query);
*/
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','P',1,'$tipo_com_p',$com_compra1_msi_p,$com_compra1_mci_p,$com_compra1_mic_p,$com_compra1_crj_p,$com_compra1_activa_p,$com_compra1_aprobada_p)";
				$conecta->consulta($query);
/*
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','P',2,'$tipo_com_p',$com_compra2_msi_p,$com_compra2_mci_p,$com_compra2_mic_p,$com_compra2_crj_p)";
				$conecta->consulta($query);
*/
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','C',1,'$tipo_com_c',$com_compra1_msi_c,$com_compra1_mci_c,$com_compra1_mic_c,$com_compra1_crj_c,$com_compra1_activa_c,$com_compra1_aprobada_c)";
				$conecta->consulta($query);
/*
				$query="Insert into esquemas_com_dp values($id_esquema,'$esquema','" . date("d/m/Y") . "','C',2,'$tipo_com_c',$com_compra2_msi_c,$com_compra2_mci_c,$com_compra2_mic_c,$com_compra2_crj_c)";
				$conecta->consulta($query);
*/
			}
			else
			{
				$tb=mysqli_fetch_array($bd);
				$id_esquema=$tb["id_esquema"];

				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_sc', meses_sin_int=$com_compra1_msi_sc, 
							meses_con_int=$com_compra1_mci_sc,microcredito=$com_compra1_mic_sc,creditoj=$com_compra1_crj_sc,
							activacion=$com_compra1_activa_sc, aprobada=$com_compra1_aprobada_sc
						where id_esquema=$id_esquema and nivel='SC' and compra=1";
				$conecta->consulta($query);
/*
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_sc', meses_sin_int=$com_compra2_msi_sc, 
							meses_con_int=$com_compra2_mci_sc,microcredito=$com_compra2_mic_sc,creditoj=$com_compra2_crj_sc
						where id_esquema=$id_esquema and nivel='SC' and compra=2";
				$conecta->consulta($query);
*/
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_l', meses_sin_int=$com_compra1_msi_l, 
							meses_con_int=$com_compra1_mci_l,microcredito=$com_compra1_mic_l,creditoj=$com_compra1_crj_l,
							activacion=$com_compra1_activa_l, aprobada=$com_compra1_aprobada_l
						where id_esquema=$id_esquema and nivel='L' and compra=1";
				$conecta->consulta($query);
/*
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_l', meses_sin_int=$com_compra2_msi_l, 
							meses_con_int=$com_compra2_mci_l,microcredito=$com_compra2_mic_l,creditoj=$com_compra2_crj_l
						where id_esquema=$id_esquema and nivel='L' and compra=2";
				$conecta->consulta($query);
*/
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_p', meses_sin_int=$com_compra1_msi_p, 
							meses_con_int=$com_compra1_mci_p,microcredito=$com_compra1_mic_p,creditoj=$com_compra1_crj_p,
							activacion=$com_compra1_activa_p, aprobada=$com_compra1_aprobada_p
						where id_esquema=$id_esquema and nivel='P' and compra=1";
				$conecta->consulta($query);
/*
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_p', meses_sin_int=$com_compra2_msi_p, 
							meses_con_int=$com_compra2_mci_p,microcredito=$com_compra2_mic_p,creditoj=$com_compra2_crj_p
						where id_esquema=$id_esquema and nivel='P' and compra=2";
				$conecta->consulta($query);
*/
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_c', meses_sin_int=$com_compra1_msi_c, 
							meses_con_int=$com_compra1_mci_c,microcredito=$com_compra1_mic_c,creditoj=$com_compra1_crj_c,
							activacion=$com_compra1_activa_c, aprobada=$com_compra1_aprobada_c
						where id_esquema=$id_esquema and nivel='C' and compra=1";
				$conecta->consulta($query);
/*
				$query="Update esquemas_com_dp 
						set nombre_esquema='$esquema', tipo_comision='$tipo_com_c', meses_sin_int=$com_compra2_msi_c, 
							meses_con_int=$com_compra2_mci_c,microcredito=$com_compra2_mic_c,creditoj=$com_compra2_crj_c
						where id_esquema=$id_esquema and nivel='C' and compra=2";
				$conecta->consulta($query);
*/
			}
			echo "Esquema guardado o actualizado con éxito";
		break;
		case 35:
			$anio=addslashes(trim($_POST["anio"]));
			$periodo=addslashes(trim($_POST["nom_periodo"]));
			
			$bd=$conecta->consulta("Select ifnull(max(mes),0)+1 as n from fechas_cierre_com where anio=$anio");
			$tb=mysqli_fetch_array($bd);
			$n=$tb["n"];
			if($n>=13)
			{
				$conecta->consulta("Insert into fechas_cierre_com values($anio,$n,'$periodo','','',0,'')");
				echo "Nombre de período anexado con éxito";
			}
			else
				echo "No se puede agregar un nuevo periodo si antes no se han capturado las fechas de inicio y cierre de los meses";
		break;
		case 36:
			$n=addslashes(trim($_POST["n"]));
			$cal=addslashes(trim($_POST["cal"]));
			$anio=addslashes(trim($_POST["anio"]));
			
			if($cal=="ec")
			{
				$query="Select mes,nom_periodo from fechas_cierre_com where anio=$anio order by mes";
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
						$cad.="|" . $tb["mes"] . ">" . $mes;
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
					$cad.="|" . $i . ">" . mes($i);
					$i++;
				}
			}
			echo $cad;
		break;
		case 37:
			$tipo=addslashes(trim($_POST["tipo"]));
			$ger=addslashes(trim($_POST["ger"]));
			$clave=addslashes(trim($_POST["clave"]));

			if($clave != "")
				$query="Select * from expediente_dig where clave='$clave' and tipo_doc != 'REC'";
			else
				$query="Select * from expediente_dig where gerencia='$ger' and tipo_doc != 'REC'";

			if($tipo != 'T')
				$query="Select clave,archivo from ($query) ed where tipo_doc like '%$tipo%'";
			else 
				$query="Select clave,archivo from ($query) ed";

			$bd=$conecta->consulta($query);
			$cad="";
			if ($bd->num_rows != 0)
			{
				while($tb=mysqli_fetch_array($bd))
				{
					if($cad=="")
						$cad=$tb['clave'] . ">" . $tb['archivo'];
					else
						$cad.="|" . $tb['clave'] . ">" . $tb['archivo'];
				}
			}
			echo $cad;
		break;
		case 38:
			$ger=addslashes(trim($_POST["ger"]));
			$clave=addslashes(trim($_POST["clave"]));
			$fec_ini=addslashes(trim($_POST["fec_ini"]));
			$fec_fin=addslashes(trim($_POST["fec_fin"]));

			$fec=explode("/", $fec_ini);
			$fec_ini_u=$fec[2] . "-" . $fec[1] . "-" . $fec[0];
			$fec=explode("/", $fec_fin);
			$fec_fin_u=$fec[2] . "-" . $fec[1] . "-" . $fec[0];

			if($clave != "")
				$query="Select clave, substring(archivo,-12) as fecha, archivo from expediente_dig where clave='$clave' and tipo_doc = 'REC'";
			else
				$query="Select clave, substring(archivo,-12) as fecha, archivo from expediente_dig where gerencia='$ger' and tipo_doc = 'REC'";

			$query="Select clave,archivo,concat(substring(fecha,5,4),'-',substring(fecha,3,2),'-',substring(fecha,1,2)) as fecha
					from ($query) ed";

			$query="Select * from ($query) d where datediff(fecha,'$fec_ini_u')>=0 and datediff(fecha,'$fec_fin_u')<=0";

			$bd=$conecta->consulta($query);
			$cad="";
			if ($bd->num_rows != 0)
			{
				while($tb=mysqli_fetch_array($bd))
				{
					if($cad=="")
						$cad=$tb['clave'] . ">" . $tb['archivo'];
					else
						$cad.="|" . $tb['clave'] . ">" . $tb['archivo'];
				}
			}
			echo $cad;
		break;
		case 39:
			$fol_tram=addslashes(trim($_POST["folio_av"]));
			$d=explode("/", $fol_tram);
			$folio=trim($d[0]);

			$bd=$conecta->consulta("Select * from av_general where folio='$folio'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				echo $tb['folio'] . "|" . $tb['tramite'] . "|" . $tb['cliente'] . "|" . $tb['nss'] . "|" . $tb['curp'] . "|" . $tb['nap'];
			}
			else
				echo "";
		break;
		case 40:
			$archivo=addslashes(trim($_POST["archivo"]));
			$rfc=addslashes(trim($_POST["rfc"]));
			$conecta->consulta("Update facturas set rfc='$rfc' where nom_pdf='$archivo'");
		break;
		case 41:
			$folio=addslashes(trim($_POST["folio"]));
			$motivo=addslashes(trim($_POST["motivo"]));
			$bd_anio=addslashes(trim($_POST["anio"]));

			$bd=$conecta->consulta("Select no_empleado,monto_anticipo,monto_incentivo from produccion$bd_anio where folio='$folio'");
			$tb=mysqli_fetch_array($bd);
			$no_empleado=$tb['no_empleado'];
			$anticipo=$tb['monto_anticipo'];
			$anti_mtto=$tb['monto_incentivo'];

			$clave='';

			$bd=$conecta->consulta("Select clave from empl_usuarios where no_empleado=$no_empleado");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$clave=$tb['clave'];
			}
			$conecta->consulta("Update produccion$bd_anio set id_estatus_prod=6 where folio='$folio'");

			$bd=$conecta->consulta("Select * from rechazos where rechazo='$motivo' and id_rechazo > 9999");
			if ($bd->num_rows == 0)
			{
				$bd=$conecta->consulta("Select ifnull(max(id_rechazo),9999) + 1 as id from rechazos where id_rechazo >= 10000");
				$tb=mysqli_fetch_array($bd);
				$id=$tb["id"];
				$conecta->consulta("Insert into rechazos values($id,'$motivo')");
			}
			else
			{
				$tb=mysqli_fetch_array($bd);
				$id=$tb["id_rechazo"];
			}

			$bd=$conecta->consulta("Select * from prod_rechazos where folio='$folio'");
			if ($bd->num_rows != 0)
				$query="Update prod_rechazos set id_rechazo=$id, descontado=1 where folio='$folio'";
			else
				$query="Insert into prod_rechazos values('$folio','',0,$id,1)";

			$conecta->consulta($query);

			if($clave != '')
			{
				$bd=$conecta->consulta("Select * from ajustes where motivo like '%$folio%'");
				if ($bd->num_rows == 0)
				{
					if($anticipo > 0)
					{
						$anticipo=$anticipo * -1;
						$desc_motivo="Descuento de anticipo del folio $folio por rechazo ($motivo)";
						$query="Insert into ajustes values(0,'',$clave','" . date("d/m/Y") . "','',$anticipo,'$desc_motivo',0,'','',0,0,'','O')";
						$conecta->consulta($query);
					}

					if($anti_mtto > 0)
					{
						$anti_mtto=$anti_mtto * -1;
						$desc_motivo="Descuento de anticipo mtto del folio $folio por rechazo ($motivo)";
						$query="Insert into ajustes values(0,'',$clave','" . date("d/m/Y") . "','',$anti_mtto,'$desc_motivo',0,'','',0,0,'','O')";
						$conecta->consulta($query);
					}
					$bd=$conecta->consulta("Select * from segundo_anticipo where folio='$folio'");
					if ($bd->num_rows != 0)
					{
						$tb=mysqli_fetch_array($bd);
						$seg_anticipo=$tb['monto'];
						if($seg_anticipo > 0)
						{
							$seg_anticipo=$seg_anticipo * -1;
							$desc_motivo="Descuento de segundo anticipo mtto del folio $folio por rechazo ($motivo)";
							$query="Insert into ajustes values(0,'$clave','" . date("d/m/Y") . "','',$seg_anticipo,'$desc_motivo',0,'','',0,0,'','O')";
							$conecta->consulta($query);
						}
					}
				}
			}
			echo "Folio rechazado correctamente";
		break;
		case 42:
			$tienda=addslashes(trim($_POST["txt-tienda"]));
			$zona=addslashes(trim($_POST["txt-zona-tienda"]));
			$clave=addslashes(trim($_POST["txt-clave-tienda"]));
			$lider=addslashes(trim($_POST["txt-lider-tienda"]));

			$d=explode("(",$lider);
			$bd=$conecta->consulta("Select clave from empl_usuarios where nombre='" . trim($d[0]) . "'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$clave_u=$tb['clave'];

				$bd=$conecta->consulta("Select * from tiendas_bd where clave_tienda='$clave'");
				if ($bd->num_rows != 0)
					$query="Update tiendas_bd set nombre_tienda='$tienda', lider='$clave_u', zona='$zona' where clave_tienda='$clave'";
				else
					$query="Insert into tiendas_bd values('$clave','$tienda','$clave_u','$zona')";
				$conecta->consulta($query);

				echo "Datos de la tienda dados de alta o actualizados exitosamente";
			}
			else
				echo "Este lider no existe como usuario en el sistema Carobra";
		break;
		case 43:
			$zona=addslashes(trim($_POST["txt-zona"]));
			$lider=addslashes(trim($_POST["txt-lider-zona"]));

			$d=explode("(",$lider);
			$bd=$conecta->consulta("Select clave from empl_usuarios where nombre='" . trim($d[0]) . "'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$clave_u=$tb['clave'];

				$bd=$conecta->consulta("Select * from zonas_bd where zona='$zona'");
				if ($bd->num_rows != 0)
					$query="Update zonas_bd set lider='$clave_u' where zona='$zona'";
				else
					$query="Insert into zonas_bd values('$zona','$clave_u')";
				$conecta->consulta($query);
				echo "Datos de la zona dados de alta o actualizados exitosamente";
			}
			else
				echo "Este lider no existe como usuario en el sistema Carobra";
		break;
		case 44:
			$nivel=addslashes(trim($_POST["nivel"]));
			$tienda=addslashes(trim($_POST["tienda"]));

			$query="";
			if($tienda != "")
			{
				if($nivel=="P")
					$query="Select nombre
							from (Select lider from tiendas_bd where nombre_tienda='$tienda') t 
								left join empl_usuarios eu on (t.lider=eu.clave)";
				else
				{
					if($nivel=="LT")
						$query="Select nombre
								from (Select zona from tiendas_bd where nombre_tienda='$tienda') t 
									left join zonas_bd z on (t.zona=z.zona)
									left join empl_usuarios eu on (z.lider=eu.clave)";
				}
			}
			
			if($query != "")
			{
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				
				echo $tb['nombre'];
			}
			else
				echo "";
		break;
		case 45:
			$clave=addslashes(trim($_POST["clave"]));
			$no_promotor=addslashes(trim($_POST["txt-no-promotor"]));
			$nivel=addslashes(trim($_POST["sel-nivel"]));
			$tienda=addslashes(trim($_POST["txt-tienda-e"]));
			$fec_alta=addslashes(trim($_POST["txt-fec-alta"]));
			$edo_civil=addslashes(trim($_POST["sel-edo-civil"]));
			$sueldo=addslashes(trim($_POST["txt-sueldo"]));
			$credito=addslashes(trim($_POST["sel-credito"]));
			$horario=addslashes(trim($_POST["txt-horario"]));
			$h_comida=addslashes(trim($_POST["txt-hora-comida"]));
			$d_descanso=addslashes(trim($_POST["txt-dias-descanso"]));
			$actividades=addslashes(trim($_POST["txt-actividades"]));
			$dir_trabajo=addslashes(trim($_POST["txt-dir-trabajo"]));

			$sueldo= str_replace(",","",str_replace("$","",$sueldo));
			if($tienda != "")
			{
				$bd=$conecta->consulta("Select clave_tienda from tiendas_bd where nombre_tienda='$tienda'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$clave_t=$tb['clave_tienda'];
				}
			}
			$bd=$conecta->consulta("Select * from empleados_bd where clave='$clave'");
			if ($bd->num_rows != 0)
				$query="Update empleados_bd 
						set no_bradescard='$no_promotor', tienda='$clave_t', edo_civil='$edo_civil', sueldo=$sueldo, cred_infonavit='$credito',
							nivel='$nivel', fec_imss='$fec_alta', horario='$horario', dias_descanso=$d_descanso, hora_comida='$h_comida',
							dir_trabajo='$dir_trabajo', actividades='actividades' where clave='$clave'";
			else
				$query="Insert into empleados_bd values('$clave','$no_promotor','$clave_t','$edo_civil',$sueldo,'$credito','$nivel','$fec_alta','$horario',$d_descanso,'$h_comida','$dir_trabajo','$actividades')";
			$conecta->consulta($query);

			echo "Datos de empleado ingresados exitosamente";
		break;
		case 46:
			$id=addslashes(trim($_POST["id"]));
			$datos=addslashes(trim($_POST["tabla"]));
			$activo=addslashes(trim($_POST["activo"]));
			$nivel=addslashes(trim($_POST["sel-nivel"]));
			$tiendas=addslashes(trim($_POST["tiendas"]));
			$estados=addslashes(trim($_POST["estados"]));
			$esquema=addslashes(trim($_POST["txt-esquema"]));
			$fec_ini=addslashes(trim($_POST["txt-fec-ini"]));
			$fec_fin=addslashes(trim($_POST["txt-fec-fin"]));
			$periodo_acum=addslashes(trim($_POST["periodo-acum"]));
			$tipo_esquema=addslashes(trim($_POST["tipo-esquema"]));
			$tipo_periodo=addslashes(trim($_POST["tipo-periodo"]));
			$tipo_tienda=addslashes(trim($_POST["sel-tipo-tienda"]));
			$pago=str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago"]))));

			if($tipo_periodo=="A")
				$pago=0;

			$por_tienda=0;

			if(trim(substr($tiendas,0,10))  !="Selecciona")
				$por_tienda=1;

			$por_estado=0;
			if(trim(substr($estados,0,10)) !="Selecciona")
				$por_estado=1;

			$bd=$conecta->consulta("Select id_esquema from esquemas_pagos_bd where id_esquema=$id");
			if ($bd->num_rows == 0)
			{
				$bd=$conecta->consulta("Select ifnull(max(id_esquema),0) + 1 as id from esquemas_pagos_bd");
				$tb=mysqli_fetch_array($bd);
				$id=$tb["id"];
				$query="Insert into esquemas_pagos_bd values($id,'$esquema','$tipo_esquema','$nivel','$tipo_tienda','$tipo_periodo','$fec_ini','$fec_fin',$por_tienda,$por_estado,$pago,$activo)";
			}
			else
				$query="Update esquemas_pagos_bd 
						set esquema='$esquema', tipo_esquema='$tipo_esquema', nivel='$nivel', tipo_tienda='$tipo_tienda', 
							periodo='$tipo_periodo',fec_ini='$fec_ini', fec_fin='$fec_fin', por_tienda=$por_tienda, por_estado=$por_estado, pago=$pago, activo=$activo 
						where id_esquema=$id";
			$conecta->consulta($query);

			if($tipo_tienda=="")
			{
				$i=0;
				$t=explode("|",$tiendas);
				if(count($t) > 0)
				{
					$conecta->consulta("Delete from esquema_pagos_por_tienda_bd where id_esquema=$id");
					while($i < count($t))
					{
						$tienda= explode("(",$t[$i]);
						$ct=str_replace(")","",$tienda[1]);
						$conecta->consulta("Insert into esquema_pagos_por_tienda_bd values($id,'$ct')");
						$i++;
					}
				}
			}

			if($por_estado==1)
			{
				$i=0;
				$t=explode("|",$estados);
				if(count($t) > 0)
				{
					$conecta->consulta("Delete from esquema_pagos_por_estado_bd where id_esquema=$id");
					while($i < count($t))
					{
						$bd=$conecta->consulta("Select no_edo from estados where estado='" . $t[$i] . "'");
						$tb=mysqli_fetch_array($bd);
						$conecta->consulta("Insert into esquema_pagos_por_estado_bd values($id," . $tb["no_edo"] . ")");
						$i++;
					}
				}
			}
			if($tipo_periodo=="A")
			{
				$bd=$conecta->consulta("Select id_esquema from esquema_detalle_bd where id_esquema=$id");

				if ($bd->num_rows != 0)
					$bd=$conecta->consulta("Delete from esquema_detalle_bd where id_esquema=$id");


				$r=explode(">",$datos);

				$i=0;
				$r=explode(">",$datos);

				while($i < count($r))
				{
					$d=explode("|",$r[$i]);

					if($d[0] != "")
					{
						$j=$i+1;
						$query="Insert into esquema_detalle_bd values($id,$j,'$periodo_acum'," . $d[0] . "," . $d[1] . "," . str_replace(",","",str_replace("$","",$d[2])) . ")";
						$conecta->consulta($query);			
					}
					else
						$i=10;
					$i++;
				}
			}
			echo "Información actualizada con éxito";
		break;
		case 47:
			$archivo= $_FILES['file-formato']['name'];
			$archivo_tem =$_FILES['file-formato']['tmp_name'];

			$titulo=addslashes(trim($_POST["titulo"]));
			$activo=addslashes(trim($_POST["activo"]));
			$descargable=addslashes(trim($_POST["descargable"]));

			$query="";
			$pasa_file=0;
			if($titulo != "")
			{
				$bd=$conecta->consulta("Select * from formatos where titulo='$titulo'");
				if ($bd->num_rows != 0)
				{
					if($archivo=="")
						$query="Update formatos set descargable=$descargable, activo=$activo where titulo='$titulo'";
					else 
					{
						$query="Update formatos 
								set descargable=$descargable, activo=$activo, archivo='$archivo'
								where titulo='$titulo'";
						$pasa_file=1;
					}
				}
				else
				{
					if($archivo !="")
					{
						$fecha=date("d/m/Y");
						$query="Insert into formatos values('$fecha','$titulo','$archivo',$descargable,$activo)";
						$pasa_file=1;
					}
					else 
						echo "No se ha subido ningún archivo con el formato";
				}
			}
			else 
				echo "No se ha capturado el titulo del formato";

			if($pasa_file==1)
			{
				if(move_uploaded_file($archivo_tem,"../formatos/" . $archivo))
				{
					$conecta->consulta($query);
					echo "Información de formato subida exitosamente";
				}
			}
			else
			{
				if($query!="")
				{
					$conecta->consulta($query);
					echo "Información de formato subida exitosamente";
				}
			}
		break;
		case 48:
			$folio=addslashes(trim($_POST["txt-folioc"]));
			$nae=addslashes(trim($_POST["txt-nae"]));
			$opor=addslashes(trim($_POST["txt-oportunidadc"]));
			$nss=addslashes(trim($_POST["txt-nss"]));
			$curp=addslashes(trim($_POST["txt-curp"]));
			$cliente=addslashes(trim($_POST["txt-cliente"]));
			$asesor=addslashes(trim($_POST["txt-asesor"]));
			$tel=addslashes(trim($_POST["txt-tel"]));
			$cel=addslashes(trim($_POST["txt-cel"]));
			$email=addslashes(trim($_POST["txt-email"]));
			$afore=addslashes(trim($_POST["sel-afore"]));
			$empresa=addslashes(trim($_POST["txt-empresa"]));
			$fec_cierre=addslashes(trim($_POST["txt-fec-cierre"]));
			$salario=str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-salario"]))));
			$saldo=str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-saldo"]))));
			$cp=addslashes(trim($_POST["txt-cp"]));
			$estatus=addslashes(trim($_POST["sel-estatus"]));

			$n=explode("(",$nae);
			$nae=str_replace(")","",$n[1]);				

			$fecha=date("d/m/Y");

			if($afore=="")
				$afore=0;


			$query="Select no_empleado from empleados inner join usuarios on (empleados.clave=usuarios.clave) 
					where nombre='" . utf8_encode($_SESSION["nombre"]) . "'";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$analista=$tb["no_empleado"];
			usleep(500000);
			
			$query="Select no_empleado from empleados inner join usuarios 
						on (empleados.clave=usuarios.clave) 
					where nombre='$asesor'";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$empleado=$tb["no_empleado"];

			$mensaje="";
			$query="Select folio,
						   if(str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('31/07/2023','%d/%m/%Y') and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('01/05/2024','%d/%m/%Y'),1,0) as est_rango 
					from produccion where folio='$folio'";
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$estatus=$tb["est_rango"];

				$q_empleado="";
				if($estatus==1)
					$q_empleado=", no_empleado=$empleado";
				Else
					$mensaje="Este folio está fuera de rango para la asignación de un asesor que no es dueño del NAE. Se guardarán los datos, pero el asesor no será modificado";

				$query="Select * from produccion 
						where folio='$folio' and id_usuario_cap=0 and no_empleado=0
							and saldo_estimado=0 and afo_cedente=0 and id_estatus_prod=1";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$query="Update produccion set nap='$nae' where folio='$folio'";
					$conecta->consulta($query);
				}
				$query="Update produccion set afo_cedente=$afore,id_usuario_cap=$analista,nss='$nss',
							saldo_estimado=$saldo $q_empleado
						where folio='$folio'";
			}
			else
				$query="Insert into produccion values('$folio','$fecha','$nss',$analista,$empleado,1,'$fec_cierre','','$nae',$saldo,$afore,1,0,'','',0,0,$estatus,0,'NO','','',0)";
			$conecta->consulta($query);

			$bd=$conecta->consulta("Select folio from prod_trabajador where folio='$folio'");
			if ($bd->num_rows != 0)
				$query="Update prod_trabajador 
					  	set trabajador='" . utf8_decode($cliente) . "',empresa='" . utf8_decode($empresa) . "',telefono='$tel',
					  		celular='$cel',CURP='$curp',email_trab='$email',sal_mes=$salario,cp='$cp' 
					    where folio='$folio'";
			else 
				$query="Insert into prod_trabajador values('$folio','$opor','" . utf8_decode($cliente) . "','" . utf8_decode($empresa) . "','$tel','$cel','','$curp','$email',$salario" . ",'',0,'$cp')";
			$conecta->consulta($query);

			if($mensaje =="")
				echo "Datos guardados con éxito";
			else
				echo $mensaje;
		break;
		case 49:
			$fec_sist=addslashes(trim($_POST["fecha"]));

			$f=explode("/",$fec_sist);
			$fec_sist=$f[3] . "-" . $f[2] . "-" . $f[1];
			$fecha=date("Y-m-j");
			$nuevafecha = strtotime('-3 months', strtotime($fecha));
			$nuevafecha = date('Y-m-j' , $nuevafecha);
			$fecha = strtotime($nuevafecha);
			$fecha=date("Y-m-j", $fecha);

echo $fecha . "|" . $fec_sist;
/*
			$fecha3 = strtotime($fecha . "H:i:00",time());
			$fecha_entrada = strtotime($fec_sist . "00:00:00");
				
			if($fecha3 > $fecha_entrada)
				echo "Fecha sobrepasa el rango permitido";
			else 
				echo "..................";
*/
		break;
		case 50:
			$folio=addslashes(trim($_POST["folio"]));
			$tipo_autoriza=addslashes(trim($_POST["tipo_autoriza"]));

			$query="Select clave from empl_usuarios where nombre='" . utf8_encode($_SESSION["nombre"]) . "'";
			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
			$autoriza=$tb["clave"];

			if($tipo_autoriza==1 or $tipo_autoriza==3)
			{
				$query="Update produccion set id_estatus_anticipo=0, fec_anticipo='', fec_pago_ant='' where folio='$folio'";
				$conecta->consulta($query);
				$conecta->consulta("Delete from carobra.acum_det_anticipo_2024 WHERE folio='$folio'");
				usleep(500000);
				$query="Delete from carobra.acum_det_prod_2024
						where folio='$folio' and anticipo=0 
							and com_asesor + com_gerente + com_brocker + com_sc=0";
				$conecta->consulta($query);
				$conecta->consulta("Delete from apovol_prod where folio_pr='$folio'");
			}

			$query="Insert into pagos_folios_sin_av values('$folio','" . date("d/m/Y") . "',$tipo_autoriza,'$autoriza')";
			$conecta->consulta($query);

			echo "El pago del folio $folio esta en proceso";
		break;
		case 51:
			$celula=addslashes(trim($_POST["celula"]));
			$sc=addslashes(str_replace("_","",trim($_POST["sc"])));
			$lider=addslashes(str_replace("_"," ",trim($_POST["lider"])));

			if($lider != "")
			{
				$l=explode("(",$lider);
				$lider=str_replace(")","",$l[1]);
			}

			if($sc != "")
			{
				$bd=$conecta->consulta("Select no_promotor from empleados_dp where promotor='$sc'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$sc=$tb["no_promotor"];
				}
				else 
					$sc="";
			}

			$bd=$conecta->consulta("Select * from celulas where no_celula=$celula");
			if($bd->num_rows != 0) 
				$conecta->consulta("Update celulas set lider='$lider', supervisor='$sc' where no_celula=$celula");	
			
			echo "Datos guardados con éxito";
		break;
		case 52:
			$tienda=addslashes(trim($_POST["tienda"]));
			$query="Select t.*,ifnull(promotor,'') as promotor from tiendas_dp t
						left join empleados_dp e on (t.coordinador=e.no_promotor)
					where  t.tienda='$tienda'";

			$bd=$conecta->consulta($query);
			$tb=mysqli_fetch_array($bd);
 			echo $tb['no_tienda'] . "|" . $tb['celula'] . "|" . $tb['direccion'] . "|" .  $tb['estado'] . "|" . $tb['promotor'] . "|" . $tb['cambaceo'];
		break;
		case 54: 
			$solicitud=addslashes(trim($_POST["solici"]));
			$observacion=addslashes(trim($_POST["sel-observaciones"]));
			$acciones=addslashes(trim($_POST["sel-acciones"]));
			$notas=addslashes(trim($_POST["txt-notas"]));
			$promotor=addslashes(trim($_POST["promotor"]));
			$fec__activacion =addslashes(trim($_POST["fec-ingreso"]));
			$cliente=addslashes(trim($_POST["text-cliente"]));
			$fec_captura=date("d/m/Y");
			$cambaceo=0;
			if ($_POST["chk_cambaceo"]=="on") 
			$cambaceo=1;

			$bd=$conecta->consulta("select * from auditorias_dp where solicitud=$solicitud");
			if ($bd->num_rows!=0) 
			{
				if ($observacion=="Solicitud correcta")
				{
					$conecta->consulta("Update prod_dportenis set auditado=1 where solicitud=$solicitud");
					$query="Update auditorias_dp 
							set promotor='$promotor',cliente='$cliente',fec_activacion='$fec__activacion',
								observaciones='$observacion',notas='$notas',accion='',
								cambaceo=$cambaceo 
							where solicitud=$solicitud";
					$conecta->consulta($query);
				}
				else
				{
					if ($acciones=="Retención")
					{
						$query="Update prod_dportenis set estatus_sol=2,auditado=1 
								where solicitud=$solicitud";
						$conecta->consulta($query);
						$query="Update auditorias_dp 
								set promotor='$promotor',cliente='$cliente',fec_activacion='$fec__activacion',
									observaciones='$observacion',notas='$notas',
									accion='$acciones',cambaceo=$cambaceo 
								where solicitud=$solicitud";
						$conecta->consulta($query);
					}
					else
					{
						$conecta->consulta("Update prod_dportenis set auditado=1 where solicitud=$solicitud");
						$query="Update auditorias_dp 
								set promotor='$promotor',cliente='$cliente',fec_activacion='$fec__activacion',
									observaciones='$observacion',notas='$notas',
									accion='$acciones',cambaceo=$cambaceo 
								where solicitud=$solicitud";
						$conecta->consulta($query);
					}
				}
			}
			else
			{
				if ($observacion=="Solicitud correcta")
				{
					$conecta->consulta("Update prod_dportenis set auditado=1 where solicitud=$solicitud");
					$query="Insert into auditorias_dp 
								values($solicitud,'$promotor','$cliente','$fec__activacion','$observacion',
										'$notas','',$cambaceo,'$fec_captura')";
					$conecta->consulta($query);
				}
				else
				{
					if ($acciones=="Retención")
					{
						$conecta->consulta("Update prod_dportenis set estatus_sol=2,auditado=1 where solicitud=$solicitud");
						$query="Insert into auditorias_dp 
									values($solicitud,'$promotor','$cliente','$fec__activacion',
											'$observacion','$notas','$acciones',$cambaceo,'$fec_captura')";
						$conecta->consulta($query);
					}
					else
					{
						$conecta->consulta("Update prod_dportenis set auditado=1 where solicitud=$solicitud");
						$query="Insert into auditorias_dp 
									values($solicitud,'$promotor','$cliente','$fec__activacion',
											'$observacion','$notas','$acciones',$cambaceo,'$fec_captura')";
						$conecta->consulta($query);
					}
				}
			}
			echo " Datos de Auditoria guardados con éxito...";
		break;
		case 55:
			$solicitud=addslashes(trim($_POST["solicitud"]));
			
			$bd=$conecta->consulta("Select * from prod_dportenis where solicitud=$solicitud");
			if ($bd->num_rows!=0) 
			{
				$conecta->consulta("Update prod_dportenis set estatus_sol=1 where solicitud=$solicitud");

				$bd=$conecta->consulta("Select * from auditorias_dp where solicitud=$solicitud");
				if ($bd->num_rows!=0)
					$conecta->consulta("Delete from auditorias_dp where solicitud=$solicitud");
			}

			echo "Solicitud liberada con éxito..";
		break;
		case 56:
			$folio=addslashes(trim($_POST["folio"]));

			$query="Select * from produccion where folio='$folio' and id_usuario_cap=0 
						and no_empleado=0 and saldo_estimado=0 and afo_cedente=0 and id_estatus_prod=1";
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				echo $tb["fec_traspaso"] . "|" . $tb["id_tipo_mov"] . "|" . $tb["id_estatus_prod"] . "|";

				$query="Select saldo_real from pagos where folio='$folio'";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				echo $tb["saldo_real"];
			}
		break;
		case 57:
			$id=addslashes(trim($_POST["id"]));
			$campana=addslashes(trim($_POST["campana"]));
			$fec_ini=addslashes(trim($_POST["fec_ini"]));
			$fec_fin=addslashes(trim($_POST["fec_fin"]));

			if($id==0)
			{
				$mensaje="Campaña guardada con éxito";
				$query="Insert into fec_cierre_campana values(0,'$campana','$fec_ini','$fec_fin')";
			}
			else
			{
				if($campana !="")
				{
					$mensaje="Campaña actualizada con éxito";
					$query="Update fec_cierre_campana 
							set campana='$campana', fec_ini='$fec_ini', fec_fin='$fec_fin'
							where id_campana=$id";
				}
				else 
				{
					$mensaje="Campaña eliminada con éxito";
					$query="Delete from fec_cierre_campana where id_campana=$id";
				}
			}
			$conecta->consulta($query);
			echo $mensaje;
		break;
		case 58:
			$solicitud=addslashes(trim($_POST["solicitud"]));
			$n=1;
			$n=1;
			$r1=1;
			$r2=2;
			$c1=1;
			$c2=4;
			$query="Select * from img_auditoria_dp where solicitud=$solicitud";
			$nom_arc=array();
			$bd=$conecta->consulta($query);
			while($tb=mysqli_fetch_array($bd))
			{
				$d=explode(".",$tb["nom_img"]);
				$ext=$d[1];
				$nom_arc[]=$tb["nom_img"];
				if($ext!="pdf")
				{
					$archivo1="./auditoria_dp/" . $tb["nom_img"];
					$archivo="./auditoria_dp/" . $tb["nom_img"];					
				}
				else
				{
					$archivo1="./img/archivopdf.png";
					$archivo="./auditoria_dp/" . $tb["nom_img"];
				}

				if($n==1)
				{
?>					
					<div class="<?php echo 'row row-'.$r1.'-'.$r2;?>">
<?php
				}
?>
					<div class="<?php echo'col col-' . $c1 . '-' . $c2 . ' p20'?>" style='border: 1px solid white; padding: 15px;'>
						<a href="<?php echo $archivo; ?>" target="_blank">
							<img src="<?php echo $archivo1; ?>" width="100%">
						</a>
					</div>
<?php		
				if($n==4)
				{
					$r1++;
					$r2++;
					$c1=1;
					$c2=4;
?>
					</div>
					<div class="<?php echo'row row-'.$r1.'-'.$r2;?>">
<?php
					$i=0;
					while($i < count($nom_arc))
					{
?>

						<div class="<?php echo 'col col-' . $c1 . '-' . $c2 . ' p10'?>" >
						
						</div>
<?php
						$i++;
						$c1+=3;
						$c2+=3;						
					}
?>
					</div>
<?php
					$c1=1;
					$c2=4;
					$r1++;
					$r2++;
					$n=0;
				}
				else
				{
					$c1+=3;
					$c2+=3;
				}
				$n++;
			}
		break;
		case 59:
			$an=addslashes(trim($_POST["an"]));

			$bd=$conecta->consulta("Select * from proscore.parametros where id=1");
			$tb=mysqli_fetch_array($bd);
			$fec_ini=$tb["fec_ini"];
			$fec_fin=$tb["fec_fin"];

			switch ($an) 
			{
				case 'Afore':
					$queryd="Delete from proscore.afore
							 where str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y') 
							 	and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
					$query="Insert into proscore.afore
							Select p.folio,trabajador,clave,fec_traspaso,fec_pago,saldo_real
							from carobra_2014.produccion p
								inner join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
								inner join carobra_2014.pagos pa on (p.folio=pa.folio)
								inner join carobra_2014.empl_usuarios e on (p.no_empleado=e.no_empleado)
							where id_estatus_prod=1 
								and str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y') 
							 	and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
				break;
				case 'Slovo':
					$queryd="Delete from proscore.slovo
							 where str_to_date(fec_alta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							 	and str_to_date(fec_alta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
					$query="Insert into proscore.slovo
							Select distinct clave_asesor,c.nss,fec_alta,nombre
							from carobra_2014.slovo_clientes c
								inner join carobra_2014.slovo_fechas sf on (c.nss=sf.nss)
							where estatus='ACTIVO'
								and str_to_date(fec_alta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							 	and str_to_date(fec_alta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
				break;
			}
			$conecta->consulta($queryd);
			sleep(1);
			$conecta->consulta($query);
			sleep(2);
			$bd=$conecta->consulta("Select * from proscore.control_ventas_act where negocio='$an'");
			if ($bd->num_rows != 0)
				$query="Update proscore.control_ventas_act set fecha='" . date("d/m/Y") . "' 
						where negocio='$an'";
			else
				$query="Insert into proscore.control_ventas_act values('$an','" . date("d/m/Y") . "')";
			$conecta->consulta($query);
			echo "Venta de " . $an . " actualizada"; 
		break;
		case 60:
			$id=addslashes(trim($_POST["id"]));
			$estatus=addslashes(trim($_POST["estatus"]));

			$conecta->consulta("Update proscore.parametros set estatus='$estatus' where id=$id");
			if($estatus=="Cerrado")
				echo "Operación realizada con éxito";
		break;
		case 61:
			$fec_ini=addslashes(trim($_POST["fec_ini"]));
			$fec_fin=addslashes(trim($_POST["fec_fin"]));
			$campana=addslashes(trim($_POST["campana"]));
			$estatus=addslashes(trim($_POST["estatus"]));

			$bd=$conecta->consulta("Select max(id) + 1 as id from proscore.parametros");
			$tb=mysqli_fetch_array($bd);
			$id=$tb["id"];

			$fi=explode("/",$fec_ini);
			$fec_ini_u=$fi[2] . "-" . $fi[1] . "-" . $fi[0];

			$m_ini=$fi[1] * 1;
			$a_ini=$fi[2];

			$ff=explode("/",$fec_fin);
			$fec_fin_u=$ff[2] . "-" . $ff[1] . "-" . $ff[0];
			$fec_tope=$ff[2] . "-" . $ff[1] . "-07";

			$df1=new DateTime($fec_fin_u);
			$df2=new DateTime($fec_tope);

			$f=explode("/",$fec_fin);
			$fec_fin_u=$f[2] . "-" . $f[1] . "-" . $f[0];
			$fec_tope=$f[2] . "-" . $f[1] . "-07";

			$d1=new DateTime($fec_fin_u);
			$d2=new DateTime($fec_tope);

			if($d1 >= $d2)
			{
				$m_fin=$f[1] * 1;
				$a_fin=$f[2];
			}
			else
			{
				$m_fin=($f[1] * 1) - 1;
				$a_fin=$f[2];
				if($m_fin == 0)
				{
					$m_fin=12;
					$a_fin=$f[2] - 1;
				}
			}

			if($a_ini==$a_fin and $m_ini > $m_fin)
			{
				$m_fin=$m_ini;
			}
			$query="Insert into proscore.parametros values($id,'$campana','$fec_ini','$fec_fin',$a_ini,$m_ini,$a_fin,$m_fin,'$estatus')";

			$conecta->consulta($query);
			echo "Nuevo período dado de alta correctamente";
		break;
		case 62:
			$sc=addslashes(trim($_POST["sc"]));
			$ssb=addslashes(trim($_POST["ssb"]));
			$gerencia=addslashes(trim($_POST["gerencia"]));
			$id_gerencia=0;
			$fec_captura=date("d/m/Y");
			if ($gerencia!="") 
			{
				$bd=$conecta->consulta("Select distinct id_gerencia 
										from carobra_2014.gerencias 
										where gerencia='$gerencia'");
				$tb=mysqli_fetch_array($bd);
				$id_gerencia=$tb["id_gerencia"];
			}

			$query="Insert into proscore.estructuras_especiales values(0,'$sc','$ssb',$id_gerencia,'$fec_captura')";
			
			$conecta->consulta($query);
			echo "Estructura dada de alta correctamente";
		break;
		case 63:
    		$campana=addslashes(trim($_POST["campana"]));
    		$incentivo=addslashes(trim($_POST["incentivo"]));

    		$m_comisionista=str_replace(",","",str_replace("$","",addslashes(trim($_POST["m_comisionista"]))));
    		$m_gcomisionista=str_replace(",","",str_replace("$","",addslashes(trim($_POST["m_gcomisionista"]))));
    		$m_premium=str_replace(",","",str_replace("$","",addslashes(trim($_POST["m_premium"]))));
    		$m_master=str_replace(",","",str_replace("$","",addslashes(trim($_POST["m_master"]))));
    		$m_elite=str_replace(",","",str_replace("$","",addslashes(trim($_POST["m_elite"]))));

			$query="Insert into proscore.incentivos values(0,$campana,'$incentivo',$m_comisionista,$m_gcomisionista,$m_premium,$m_master,$m_elite)";
			
			$conecta->consulta($query);
			echo "Incentivo guardado correctamente";
		break;
		case 64:
			$clave=addslashes(trim($_POST["clave"]));
    		$fechas=addslashes(trim($_POST["fechas"]));
    		$nombre=addslashes(trim($_POST["nombre"]));
    		$categoria=addslashes(trim($_POST["categoria"]));
 
 			$d=explode("|",$fechas); 
 			$fec_ini=$d[0];
 			$fec_fin=$d[1];

    		if($categoria=="Comisionista" or $categoria=="Gerente Premium")
    		{
    			$titulos=array('Negocio','Asesor/Gerente','Identificador','Cliente','Fecha','Monto');
    			$query="Select 'Afore' as an,'$nombre' as asesor,folio,trabajador,fec_traspaso,monto
    					from proscore.afore 
    					where str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
							and clave='$clave'	
						union
						Select 'Slovo' as an,'$nombre' as asesor,nss,trabajador,fec_alta,100000	
						from proscore.slovo	
						where str_to_date(fec_alta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_alta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
							and clave='$clave'
						union
						Select 'SKandia' as an,'$nombre' as asesor,identificador,'',fec_venta,monto	
						from proscore.skandia
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
							and clave='$clave'
						union
						Select 'Infinity' as an,'$nombre' as asesor,identificador,'',fec_venta,monto	
						from proscore.infinity
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
							and clave='$clave'
						union
						Select 'Plan Ideal' as an,'$nombre' as asesor,identificador,'',fec_venta,monto	
						from proscore.plan_ideal
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
							and clave='$clave'";
    		}
			if($categoria=="Gerente Comisionista")
			{
				$titulos=array('Negocio','Gerencia','Asesor/Gerente','Identificador','Cliente','Fecha','Monto');
    			$query="Select 'Afore' as an,gerencia,nombre,folio,trabajador,fec_traspaso,monto
    					from proscore.afore d 
 							inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct clave,id_gerencia 
 										from carobra_2014.usuarios_nivel_gerencia 
 										where clave='$clave') gu on(cg.id_gerencia=gu.id_gerencia) 
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Slovo' as an,gerencia,nombre,nss,trabajador,fec_alta,100000	
						from proscore.slovo	d
 							inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct clave,id_gerencia 
 										from carobra_2014.usuarios_nivel_gerencia 
 										where clave='$clave') gu on(cg.id_gerencia=gu.id_gerencia) 
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
						where str_to_date(fec_alta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_alta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'SKandia' as an,gerencia,nombre,identificador,'',fec_venta,monto	
						from proscore.skandia d
 							inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct clave,id_gerencia 
 										from carobra_2014.usuarios_nivel_gerencia 
 										where clave='$clave') gu on(cg.id_gerencia=gu.id_gerencia) 
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Infinity' as an,gerencia,nombre,identificador,'',fec_venta,monto	
						from proscore.infinity d
 							inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct clave,id_gerencia 
 										from carobra_2014.usuarios_nivel_gerencia 
 										where clave='$clave') gu on(cg.id_gerencia=gu.id_gerencia) 
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Plan Ideal' as an,gerencia,nombre,identificador,'',fec_venta,monto	
						from proscore.plan_ideal d
 							inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct clave,id_gerencia 
 										from carobra_2014.usuarios_nivel_gerencia 
 										where clave='$clave') gu on(cg.id_gerencia=gu.id_gerencia) 
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
						where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
			}
			if($categoria=="Gerente Master" or $categoria=="Gerente Elite")
			{
				$titulos=array('Negocio','Socio','Brocker','Gerencia','Asesor/Gerente','Identificador','Cliente','Fecha','Monto');
				$query="Select 'Afore' as an,sc,brocker,gerencia,nombre,folio,trabajador,
							   fec_traspaso,monto
    					from proscore.afore d 
    						inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct sc,brocker,id_gerencia 
 										from carobra_2014.estructura_gerencia eg
											inner join (Select distinct '$nombre' as estructura,
															   max(id_nivel) as id_nivel 
														from carobra_2014.usuarios_nivel_gerencia 
														where clave='$clave' 
															and (id_nivel=5 or id_nivel=13)) n 
													on (if(n.id_nivel=5,eg.brocker,eg.sc)=n.estructura)) gu 
 									on (cg.id_gerencia=gu.id_gerencia)
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Slovo' as an,sc,brocker,gerencia,nombre,nss,trabajador,fec_alta,100000	
						from proscore.slovo	d
    						inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct sc,brocker,id_gerencia 
 										from carobra_2014.estructura_gerencia eg
											inner join (Select distinct '$nombre' as estructura,
															   max(id_nivel) as id_nivel 
														from carobra_2014.usuarios_nivel_gerencia 
														where clave='$clave' 
															and (id_nivel=5 or id_nivel=13)) n 
													on (if(n.id_nivel=5,eg.brocker,eg.sc)=n.estructura)) gu 
 									on (cg.id_gerencia=gu.id_gerencia)
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_alta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_alta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'SKandia' as an,sc,brocker,gerencia,nombre,identificador,'',fec_venta,monto
						from proscore.skandia d
    						inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct sc,brocker,id_gerencia 
 										from carobra_2014.estructura_gerencia eg
											inner join (Select distinct '$nombre' as estructura,
															   max(id_nivel) as id_nivel 
														from carobra_2014.usuarios_nivel_gerencia 
														where clave='$clave' 
															and (id_nivel=5 or id_nivel=13)) n 
													on (if(n.id_nivel=5,eg.brocker,eg.sc)=n.estructura)) gu 
 									on (cg.id_gerencia=gu.id_gerencia)
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Infinity' as an,sc,brocker,gerencia,nombre,identificador,'',
							   fec_venta,monto	
						from proscore.infinity d
    						inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct sc,brocker,id_gerencia 
 										from carobra_2014.estructura_gerencia eg
											inner join (Select distinct '$nombre' as estructura,
															   max(id_nivel) as id_nivel 
														from carobra_2014.usuarios_nivel_gerencia 
														where clave='$clave' 
															and (id_nivel=5 or id_nivel=13)) n 
													on (if(n.id_nivel=5,eg.brocker,eg.sc)=n.estructura)) gu 
 									on (cg.id_gerencia=gu.id_gerencia)
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
						union
						Select 'Plan Ideal' as an,sc,brocker,gerencia,nombre,identificador,'',
							   fec_venta,monto	
						from proscore.plan_ideal d
    						inner join proscore.clave_gerencia cg on (d.clave=cg.clave)
 							inner join (Select distinct sc,brocker,id_gerencia 
 										from carobra_2014.estructura_gerencia eg
											inner join (Select distinct '$nombre' as estructura,
															   max(id_nivel) as id_nivel 
														from carobra_2014.usuarios_nivel_gerencia 
														where clave='$clave' 
															and (id_nivel=5 or id_nivel=13)) n 
													on (if(n.id_nivel=5,eg.brocker,eg.sc)=n.estructura)) gu 
 									on (cg.id_gerencia=gu.id_gerencia)
 							inner join (Select distinct id_gerencia,gerencia 
 										from carobra_2014.gerencias) g on (gu.id_gerencia=g.id_gerencia)
 							inner join carobra.usuarios u on (d.clave=u.clave)
    					where str_to_date(fec_venta,'%d/%m/%Y') >= str_to_date('$fec_ini','%d/%m/%Y')
							and str_to_date(fec_venta,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')";
			}
?>
			<div class="row ml-0 mr-0 pl-0 pt-1 pr-0">
				<div class="col-12 p-0 m-0">
					<div class="info-sv pt-0">
						<table width="100%">
							<thead>
								<tr>
<?php
									$i=0;
									$t=count($titulos);
									while($i < count($titulos))
									{
?>
										<th><?php echo $titulos[$i]; ?></th>
<?php
										$i++;
									}
?>
								</tr>
							</thead>
							<tbody>
<?php
								$class="oscuro";
								$bd=$conecta->consulta($query);
								while($tb=mysqli_fetch_array($bd))
								{
									if($class=="oscuro")
										$class="claro";
									else
										$class="oscuro";
									$i=0;
									$p=1;
?>
									<tr class="<?php echo $class;?>">
<?php
										while($i < count($titulos))
										{
											if($p==$t)
												$dato="$" . number_format($tb[$i], 2, '.', ',');
											else
												$dato= $tb[$i];
?>
											<td><?php echo $dato; ?></td>
<?php
											$i++;
											$p++;
										}
?>
									</tr>
<?php
									$i++;
								}
?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<?php
    	break;
		// Marca inicio Jesus- {Se agrego el guardad y actulizacion de aclaraciones de afore} 29/04/2024
		case 65:
			$folio = addslashes(trim($_POST["txtfolio"]));
			$usuario_cap = addslashes(trim($_POST["txtUsuarioCap"]));
			$fec_aclaracion = addslashes(trim($_POST["txtFechaAclaracion"]));
			$motivo = addslashes(trim($_POST["txtmotivo"]));
			$accion_tomada = addslashes(trim($_POST["txtAccion"]));
			$status = addslashes(trim($_POST["slcFechaPago"]));
			$solicitante = addslashes(trim($_POST["txtAsesor"]));
			$fecha_captura = date("d/m/Y");
			$mensaje = "";
			$bdp = $conecta->consulta("SELECT no_empleado FROM produccion WHERE folio = '$folio'");
			$tbp = mysqli_fetch_array($bdp);
			$no_empleado = $tbp["no_empleado"];

			if ($no_empleado == 0) 
			{
				$mensaje = "El folio no cuenta con asesor asignado";
			} 
			else 
			{
				$claveSolic = $conecta->consulta("select clave from empl_usuarios where nombre = '$solicitante'");
				$tb = mysqli_fetch_array($claveSolic);
				$claveSolicitante = $tb["clave"];
				
				$bd = $conecta->consulta("select folio from aclaraciones_afore where folio = '$folio'");
				if ($bd->num_rows != 0) {
					$query = "update aclaraciones_afore set fec_aclaracion = '$fec_aclaracion', motivo = '$motivo', 
								accion_tomada = '$accion_tomada', status = '$status', solicitante = '$claveSolicitante'  where folio = '$folio'";
				} else {
					$query = "insert into aclaraciones_afore values ('$folio', '$usuario_cap', '$fecha_captura', '$fec_aclaracion', '$motivo', 
								'$accion_tomada', '$status', '$claveSolicitante')";
				}
				$conecta->consulta($query);
				$mensaje = "Datos de aclaración guardados con éxito";
			}
			echo $mensaje;

		break;
		// Marca fin Jesus- {Se agrego el guardad y actulizacion de aclaraciones de afore} 29/04/2024

		// Marca inicio Jesus- {Se agrego el guardado del bloqueo de aclaraciones} 30/04/2024
		case 66:
			$valor = addslashes(trim($_POST["valor"]));
			$query = "select count (*) as total from bloqueo_aclaraciones";
			$valorCon = intval($valor);
			$mensaje = "";

			$bloqueoExiste =  $conecta->consulta($query);
			$tb = mysqli_fetch_array($bloqueoExiste);
			$totalRegistros = $tb['total'];

			if ($totalRegistros == 0) {
				$horaActual = date('G');
				$fechaHoraActual = new DateTime();
				$fechaHoraActual->add(new DateInterval('PT' . $valorCon . 'H'));

				$fechaBloqueo = $fechaHoraActual->format('d/m/Y');
				$horaBloqueo = $fechaHoraActual->format('G');

				$insertarRegistro = "insert into bloqueo_aclaraciones values ('$fechaBloqueo', '$horaBloqueo')";
				$resultadoInsercion = $conecta->consulta($insertarRegistro);

				$mensaje = "Datos del bloqueo guardados con éxito. El mensaje volverá a paracer desde las " . $horaBloqueo . ":00 horas nuevamente";
			}

			if ($totalRegistros > 0) {
				$eliminarRegistro = "delete from bloqueo_aclaraciones";
				$conecta->consulta($eliminarRegistro);
				$horaActual = date('G');
				$fechaHoraActual = new DateTime();
				$fechaHoraActual->add(new DateInterval('PT' . $valorCon . 'H'));

				$fechaBloqueo = $fechaHoraActual->format('d/m/Y');
				$horaBloqueo = $fechaHoraActual->format('G');

				$insertarRegistro = "insert into bloqueo_aclaraciones values ('$fechaBloqueo', '$horaBloqueo')";
				$resultadoInsercion = $conecta->consulta($insertarRegistro);

				$mensaje = "Datos del bloqueo guardados con éxito. El mensaje volverá a paracer desde las " . $horaBloqueo . ":00 horas nuevamente";
			}
			echo $mensaje;
		break;
		// Marca fin Jesus- {Se agrego el guardado del bloqueo de aclaraciones} 30/04/2024

		// Marca inicio Jesus- {Se agrego el ajuste de pago desde aclaracion} 09/05/2024
		case 67:
			$txtFolio = addslashes(trim($_POST["txtFolio"]));
			$txtClave = addslashes(trim($_POST["txtClave"]));
			$txtMotivo = addslashes(trim($_POST["txtMotivo"]));
			$txtMonto = addslashes(trim($_POST["txtMonto"]));
			$fecha = date("d/m/Y");
			$txtMonto = str_replace('$', '', $txtMonto);
			$txtMonto = str_replace(',', '', $txtMonto);

			$a = compl_ajuste(1);
			$b = compl_ajuste(0, $txtClave);
			$c = $b[0]["unidad"];
			$d = $b[0]["id_gerencia"];
			$e = $b[0]["socio"];
			$mesActual = date('m');
			$anioActual = date('Y');

			$tablaDestino = '';
			switch (strtoupper($c)) 
			{
				case 'PREVISIONAL':
					$tablaDestino = 'ajustes_previsional';
					break;
				case 'TARJETA':
					$tablaDestino = 'ajustes_tarjeta';
					break;
				case 'HIPOTECARIO':
					$tablaDestino = 'ajustes_hipotecario';
					break;
				case 'SEGUROS':
					$tablaDestino = 'ajustes_seguros';
					break;
				case 'CORPORATIVO':
					$tablaDestino = 'ajustes_corporativo';
					break;
				default:
					echo "Unidad de negocio no válida: $c";
				exit;
			}

			$respuestaMotivo = "";
			$posicion = strpos($txtMotivo, $txtFolio);
			if ($posicion !== false) 
			{
				$respuestaMotivo = $txtMotivo." "."(Respuesta a aclaración)";

			} 
			else 
			{
				
				$respuestaMotivo = $txtMotivo." "."(Respuesta a aclaración folio: ".$txtFolio.")";
			}

			$query = "INSERT INTO $tablaDestino VALUES (0, '','$txtClave', '$fecha', '', '$e', $d, $anioActual,  $mesActual, $txtMonto, '$respuestaMotivo', 0, '', '', 0, 0, '', 'O')";
			$conecta->consulta($query, "I");
			
			$consulta = "SELECT SUBSTRING(motivo, LOCATE('$txtFolio', motivo), 10) AS folio_ajuste FROM ajustes WHERE motivo LIKE'%$txtFolio%'";
			$bd = $conecta->consulta($consulta);
			if ($bd->num_rows != 0) 
			{
				$consulta = "UPDATE aclaraciones_afore SET status = 2 WHERE folio = '$txtFolio'";
				$conecta->consulta($consulta);
			}
			echo "AJUSTE GENERADO";
		break;
		// Marca fin Jesus- {Se agrego el ajuste de pago desde aclaracion} 09/05/2024

		// Marca inicio Jesus- {Se agrego el guardado de estructura especial} 13/05/2024
		case 68:
			$nivel = addslashes(trim($_POST["nivel"]));
			$estructura = addslashes(trim($_POST["estructura"]));
			$campania = addslashes(trim($_POST["campania"]));
			
			$anexasc = addslashes(trim($_POST["anexasc"]));
			$anexasb = addslashes(trim($_POST["anexasb"]));
			$anexag = addslashes(trim($_POST["anexag"]));
			
			$socio = "";
			$subbrocker = "";
			$gerencia = "";
			
			$consulta = "";
			$mensajeRegistro = "";
			$query = "";
			
			if ($nivel == "sc" and $anexasb == "" and $anexag == "") 
			{
				$socio = $anexasc;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND socio = '$anexasc' AND nivel = '$nivel' AND campania = $campania";
			}
			if ($nivel == "sc" and $anexasb !== "")  {
				$subbrocker = $anexasb;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND subbrocker = '$anexasb' AND nivel = '$nivel' AND campania = $campania";
			}
			if ($nivel == "sb" and $anexasb !== "")  {
				$subbrocker = $anexasb;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND subbrocker = '$anexasb' AND nivel = '$nivel' AND campania = $campania";
			}
			if ($nivel == "sc" and $anexag !== "")  {
				$gerencia = $anexag;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND gerencia = '$anexag' AND nivel = '$nivel' AND campania = $campania";
			}
			if ($nivel == "sb" and $anexag !== "")  {
				$gerencia = $anexag;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND gerencia = '$anexag' AND nivel = '$nivel' AND campania = $campania";
			}
			if ($nivel == "g" and $anexag !== "")  {
				$gerencia = $anexag;
				$consulta = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND gerencia = '$anexag' AND nivel = '$nivel' AND campania = $campania";
			}
			/*if ($nivel == "sb") 
			{
				$subbrocker = $anexa;
				$consulta = "SELECT * FROM estructura_especial WHERE estructura_base = '$estructura' AND subbrocker = '$anexa' AND nivel = '$nivel'";
			}
			if ($nivel == "g") 
			{
				$gerencia = $anexa;
				$consulta = "SELECT * FROM estructura_especial WHERE estructura_base = '$estructura' AND gerencia = '$anexa' AND nivel = '$nivel'";
			}*/

			$bd = $conecta->consulta($consulta);	
			if ($bd->num_rows != 0) {
				$mensajeRegistro = "YA EXISTE UNA ESTRUCTURA ESPECIAL CON ESTOS DATOS";
			}
				
			if ($mensajeRegistro != "") 
			{
				echo $mensajeRegistro;
			} 
			else 
			{
				$query = "INSERT INTO proscore.estructura_especial  VALUES ('$estructura', '$nivel', '$socio', '$subbrocker', '$gerencia', $campania)";
				$conecta->consulta($query, "I");
				echo "Datos de estructura guardados con éxito";
			}			
		break;
		case 69:
			$nivel = addslashes(trim($_POST["nivel"]));
			$estructura = addslashes(trim($_POST["estructura"]));
			$campania = addslashes(trim($_POST["campania"]));
			$query = "SELECT * FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND nivel = '$nivel' AND campania = $campania";
			// echo "Query ".$query;
			$bd = $conecta->consulta($query);
			$resultados = array();
			if ($bd->num_rows != 0) 
			{ 
				while($tb = mysqli_fetch_array($bd)) {
					$resultados[] = $tb;
				}
				header('Content-Type: application/json');
				$json = json_encode($resultados);
				echo $json;
			}
			else
			{
				header('Content-Type: application/json');
				$json = json_encode($resultados);
				echo $json;
			}
			//echo $bd;
			
		break;
		case 70:
			$nivel = addslashes(trim($_POST["nivel"]));
			$estructura = addslashes(trim($_POST["estructura_base"]));
			$campania = addslashes(trim($_POST["campania"]));
			$socio = addslashes(trim($_POST["socio"]));
			$subbrocker = addslashes(trim($_POST["subbrocker"]));
			$gerencia = addslashes(trim($_POST["gerencia"]));

			$query = "";
			if ($socio != "") {
				$query = "DELETE FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND nivel = '$nivel' AND socio = '$socio' AND campania = $campania";
				$conecta->consulta($query, "D");
				echo "Datos eliminados con éxito";

			}
			if ($subbrocker != "") {
				$query = "DELETE FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND nivel = '$nivel' AND subbrocker = '$subbrocker' AND campania = $campania";
				$conecta->consulta($query, "D");
				echo "Datos eliminados con éxito";

			}
			if ($gerencia != "") {
				$query = "DELETE FROM proscore.estructura_especial WHERE estructura_base = '$estructura' AND nivel = '$nivel' AND gerencia = '$gerencia' AND campania = $campania";
				$conecta->consulta($query, "D");
				echo "Datos eliminados con éxito";

			}


		break;
		// Marca fin Jesus- {Se agrego el guardado de estructura especial} 13/05/2024
		// Marca inicio Jesus- {calendario semanal} 05/06/2024
		case 71:
			$fecincio = addslashes(trim($_POST["startDate"]));
			$fecfin = addslashes(trim($_POST["endDate"]));
			$anio = addslashes(trim($_POST["anio"]));
			$anio = addslashes(trim($_POST["anio"]));
			$isfecfin = addslashes(trim($_POST["isfecfin"]));
			
			function days_in_month($month, $year) {
				return $month == 2 ? ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0) ? 29 : 28) : (($month - 1) % 7 % 2 ? 30 : 31);
			}
			

			if ($anio !== "")  
			{
				if ($isfecfin == "BORRAR") 
				{
					$dQuery = "DELETE FROM calendario_semanal WHERE anio = $anio";
					$conecta->consulta($dQuery);
				}

				$query = "SELECT anio, mes, semana, fec_ini, fec_fin FROM calendario_semanal WHERE anio = $anio ORDER BY semana";
				$bd = $conecta->consulta($query);
				$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
				if ($bd->num_rows == 0) 
				{
					$startDate = strtotime($fecincio);
                    $endDate = strtotime($fecfin);
                    $weekNumber = 1;
?>
			<table class="table table-bordered text-center" id="tabla-semanas">
                <tr>
                    <th>Mes</th>
                    <th style="min-width: 100px;">Semana</th>
                    <th>Fec. Inicio</th>
                    <th>Fec. Fin</th>
                </tr>
                <tr>
                    <td>
                        <select name="slc-mes" id="slc-mes-<?php echo $weekNumber; ?>">
                            <?php foreach ($meses as $index => $mes) : ?>
                                <option value="<?php echo $index + 1; ?>" <?php echo ($index + 1 == date('n', $startDate)) ? 'selected' : ''; ?>><?php echo $mes; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="min-width: 100px;" data-semana="<?php echo $weekNumber; ?>">Semana <?php echo $weekNumber; ?></td>
                    <td>
                        <input type="date" name="fec_inicial_<?php echo $weekNumber; ?>" id="fec_inicial_<?php echo $weekNumber; ?>" value="<?php echo date('Y-m-d', $startDate); ?>" />
                    </td>
                    <td>
                        <input type="date" name="fec_fin_<?php echo $weekNumber; ?>" id="fec_fin_<?php echo $weekNumber; ?>" value="<?php echo date('Y-m-d', $endDate); ?>" />
                    </td>
                </tr>
<?php
                $weekNumber++;
                $currentWeekStart = strtotime('next Monday', $endDate);

                for ($year = date('Y', $currentWeekStart); $year <= $anio; $year++) 
				{
                    for ($m = ($year == date('Y', $currentWeekStart) ? date('n', $currentWeekStart) : 1); $m <= 12; $m++) 
					{
                        $daysInMonth = days_in_month($m, $year);
                        $monthStartDate = strtotime("$year-$m-01");
                        $monthEndDate = strtotime("$year-$m-$daysInMonth");

                        while ($currentWeekStart <= $monthEndDate) 
						{
                            $currentWeekEnd = strtotime('next Sunday', $currentWeekStart);

                            if (date('j', $currentWeekEnd) > $daysInMonth && date('n', $currentWeekEnd) != date('n', $currentWeekStart)) 
							{
                                $currentWeekEnd = strtotime('last day of this month', $currentWeekStart);
                            }

                            $startMonth = date('n', $currentWeekStart);
                            $endMonth = date('n', $currentWeekEnd);
                            if ($startMonth != $endMonth) 
							{
                                $daysInStartMonth = date('t', $currentWeekStart) - date('j', $currentWeekStart) + 1;
                                $daysInEndMonth = date('j', $currentWeekEnd);
                                $majorityMonth = $daysInStartMonth > $daysInEndMonth ? $startMonth : $endMonth;
                            } 
							else 
							{
                                $majorityMonth = $startMonth;
                            }
                            ?>
                            <tr>
                                <td>
                                    <select name="slc-mes" id="slc-mes-<?php echo $weekNumber; ?>">
                                        <?php foreach ($meses as $index => $mes) : ?>
                                            <option value="<?php echo $index + 1; ?>" <?php echo ($index + 1 == $majorityMonth) ? 'selected' : ''; ?>><?php echo $mes; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td style="min-width: 100px;" class="columna-formato" data-semana="<?php echo $weekNumber; ?>">Semana <?php echo $weekNumber; ?></td>
                                <td>
                                    <input type="date" name="fec_inicial_<?php echo $weekNumber; ?>" id="fec_inicial_<?php echo $weekNumber; ?>" value="<?php echo date('Y-m-d', $currentWeekStart); ?>" />
                                </td>
                                <td>
                                    <input type="date" name="fec_fin_<?php echo $weekNumber; ?>" id="fec_fin_<?php echo $weekNumber; ?>" value="<?php echo date('Y-m-d', $currentWeekEnd); ?>" />
                                </td>
                            </tr>
                            <?php
                            $currentWeekStart = strtotime('next Monday', $currentWeekEnd + 1);
                            $weekNumber++;
                            if (date('Y', $currentWeekStart) > $anio) 
							{
                                break 2;
                            }
                        }
                    }
                }
?>
            </table>
<?php
				} 
				else 
				{
?>
					<table class="table table-bordered text-center" id="tabla-semanas">
						<tr>
							<th>Mes</th>
							<th style="min-width: 120px;">Semana</th>
							<th>Fec. Inicio</th>
                            <th>Fec. Fin</th>
                		</tr>
<?php 					while ($row = mysqli_fetch_array($bd)) 
						{
?>
							<tr>
								<td>
									<select name="slc-mes" id="slc-mes-<?php echo $row['semana']; ?>" onchange="actuliza_mes(this, <?php echo $row['mes']?>, <?php echo $row['semana']; ?>);">
										<?php foreach ($meses as $index => $mes) : ?>
											<option value="<?php echo $index + 1; ?>" <?php echo ($index + 1 == $row['mes']) ? 'selected' : ''; ?>><?php echo $mes; ?></option>
										<?php endforeach; ?>
                            		</select>
								</td>
								<td style="min-width: 120px;" data-semana="<?php echo $row['semana']; ?>">
									Semana <?php echo $row['semana']; ?>
								</td>
								<td>
                            		<input type="date" name="fec_inicial_<?php echo $row['semana']; ?>" id="fec_inicial_<?php echo $row['semana']; ?>" value="<?php echo !empty($row['fec_ini']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['fec_ini']))) : ''; ?>" />
                        		</td>
                       			 <td>
                            		<input type="date" name="fec_fin_<?php echo $row['semana']; ?>" id="fec_fin_<?php echo $row['semana']; ?>" value="<?php echo !empty($row['fec_fin']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['fec_fin']))) : ''; ?>" />
                        		</td>
							</tr>
<?php 
						}
?>
					</table>
<?php
				}
				
			}
			
		break;
		case 72:
			$semanas_json = stripslashes($_POST['semanas']);
        	$semanas = json_decode($semanas_json, true);
			
			function convertir_fecha($fecha) 
			{
				list($anio, $mes, $dia) = explode('-', $fecha);
				return $dia . '/' . $mes . '/' . $anio;
			}
			
			foreach ($semanas as $semana) 
			{
				$anio = $semana['anio'];
				$mes = $semana['mes'];
				$semana_num = $semana['semana'];
				$fec_inicial = $semana['fec_inicial'];
				$fec_fin = $semana['fec_fin'];
				$fec_inicial = convertir_fecha($fec_inicial);
            	$fec_fin = convertir_fecha($fec_fin);

				$query = "INSERT INTO calendario_semanal VALUES ($anio, $mes, $semana_num, '$fec_inicial', '$fec_fin')";
				$conecta->consulta($query,"I");
			}
			echo "Datos del calendario guardados con exito";

		break;
		case 73:
			$anio = addslashes(trim($_POST["anio"]));
			$bandera = "";
			if ($anio !== "")  
			{
				$query = "SELECT anio, mes, semana, fec_ini, fec_fin FROM calendario_semanal WHERE anio = $anio ORDER BY semana";
				$bd = $conecta->consulta($query);
				if ($bd->num_rows == 0) 
				{
					$bandera = "NO_REGISTROS";
				} 
				else 
				{
					$bandera = "SI_REGISTROS";
				}
				echo $bandera;
			}
		break;
		case 74:
			$anio = addslashes(trim($_POST["anio"]));
			$ames = intval(addslashes(trim($_POST["ames"])));
			$omes = addslashes(trim($_POST["omes"]));
			$semana = addslashes(trim($_POST["semana"]));
			$query = "UPDATE calendario_semanal SET mes = $ames WHERE anio = $anio  AND mes = $omes AND semana = $semana";
			$conecta->consulta($query);
			echo "Datos del mes guardados con exito";
		break;
		// Marca inicio Jesus- {calendario semanal} 05/06/2024
		case 75:
			$id = addslashes(trim($_POST["id"]));

			$d=explode(">", $id);

			$ct=$d[0];
			$id_esquema=$d[1];
			$query="Delete from usuarios_comision_dp where ct='$ct' and id_esquema=$id_esquema";

			$conecta->consulta($query);
		break;
		case 76:
			$folio = addslashes(trim($_POST["folio"]));
			$nss = addslashes(trim($_POST["nss"]));
			$comisionista = addslashes(trim($_POST["comisionista"]));
			$cliente = addslashes(trim($_POST["cliente"]));
			$fecmov = addslashes(trim($_POST["fecmov"]));
			$nap = addslashes(trim($_POST["nap"]));
			$saldo = addslashes(trim($_POST["saldo"]));
			//$empresa = addslashes(trim($_POST["empresa"]));
			$curp = addslashes(trim($_POST["curp"]));
			//$salario = addslashes(trim($_POST["salario"]));
			//$telefono = addslashes(trim($_POST["telefono"]));
			$celular = addslashes(trim($_POST["celular"]));
			//$cp = addslashes(trim($_POST["cp"]));
			$email = addslashes(trim($_POST["email"]));
			// $observaciones = addslashes(trim($_POST["observaciones"]));

			$fecmov = date('d/m/Y', strtotime(str_replace('-', '/', $fecmov)));	
			$saldo = str_replace('$', '', $saldo);
			$saldo = str_replace(',', '', $saldo);
			//$salario = str_replace('$', '', $salario);
			//$salario = str_replace(',', '', $salario);

			$query = "SELECT no_empleado FROM empl_usuarios WHERE nombre = '$comisionista'";
			$bd = $conecta->consulta($query);
			$tb = mysqli_fetch_array($bd);
			$no_empleado = $tb["no_empleado"];
						
			if ($folio != "") 
			{
				$p_uquery = "UPDATE produccion SET nss = '$nss', fec_traspaso = '$fecmov', nap = '$nap', 
							saldo_estimado = $saldo, no_empleado = $no_empleado  WHERE folio = '$folio'";
				
				/*$pt_uquery = "UPDATE prod_trabajador SET trabajador = '$cliente', empresa = '$empresa', 
								CURP = '$curp', sal_mes = $salario, telefono = '$telefono', celular = '$celular', 
								cp = '$cp', email_trab = '$email', observa = '$observaciones' WHERE folio = '$folio'";*/

				$pt_uquery = "UPDATE prod_trabajador SET trabajador = '$cliente', 
								CURP = '$curp', celular = '$celular', email_trab = '$email' WHERE folio = '$folio'";

				$conecta->consulta($p_uquery);
				$conecta->consulta($pt_uquery);
				echo "Datos guardados con exito";
			}


		break;
		case 77:
			$tipo = addslashes(trim($_POST["tipo"]));
			$anio = addslashes(trim($_POST["anio"]));
			$query = "SELECT * FROM plan_ideal.cuotas_zonas WHERE zona = '$tipo' AND anio = $anio";
			$bd = $conecta->consulta($query);
?>
			<div class="table-container">
				<table class="table table-striped table-bordered table-hover table-condensed table-responsive-lg table-sm">
					<tr class="table-bg-secondary">
						<th style="min-width: 100px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Zona</th>
						<th style="min-width: 300px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">División</th>
						<th style="min-width: 300px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Grupo Principal</th>
						<th style="min-width: 100px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Salario Base</td>
						<th style="min-width: 100px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Monto Minimo</th>
						<th style="min-width: 70px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Mensual</th>
						<th style="min-width: 70px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Bimestral</th>
						<th style="min-width: 70px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Semestral</th>
						<th style="min-width: 70px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Anual</th>
						<th style="min-width: 50px; position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">Año</th>
						<th style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;"></th>
						<th style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;"></th>
					</tr>
<?php
					while($tb = mysqli_fetch_array($bd)) 
					{

						switch ($tb['zona']) 
						{
							case "ZONA_LFN":
								$leyenda = "Zona Libre Fronteriza Norte";
								break;
							case "ZONA_RDP":
								$leyenda = "Resto del país";
								break;
							default:
								$leyenda = "";
								break;
						}

						$salario_base = $tb['salario_base'];
        				$info_salario_base = "$" . number_format($salario_base, 2, '.', ',');

						$monto_minimo = $tb['monto_minimo'];
        				$info_monto_minimo = "$" . number_format($monto_minimo, 2, '.', ',');

						$mensual = $tb['mensual'];
        				$info_mensual = "$" . number_format($mensual, 2, '.', ',');
						
						$bimestral = $tb['bimestral'];
        				$info_bimestral = "$" . number_format($bimestral, 2, '.', ',');

						$semestral = $tb['semestral'];
        				$info_semestral = "$" . number_format($semestral, 2, '.', ',');

						$anual = $tb['anual'];
        				$info_anual = "$" . number_format($anual, 2, '.', ',');

?>
						<tr id="<? echo 'reg_' . $tb['id_cuouta']; ?>">
							<td><? echo $leyenda; ?></td>
							<td>
								<textarea class="division" rows="2" style="min-width: 300px; border: none; background: transparent;" ><?php echo $tb['division']; ?></textarea>
							</td>
							<td>
								<textarea class="grupo" rows="2" style="min-width: 300px; border: none; background: transparent;" ><? echo $tb['grupo']; ?></textarea>
							</td>
							<td>
								<input class="moneda salario" type="text" style="border: none; background: transparent;" value="<? echo $info_salario_base; ?>"/>
							</td>
							<td>
								<input class="moneda monto"  type="text" style="border: none; background: transparent;" value="<? echo $info_monto_minimo; ?>"/>
							</td>
							<td>
								<input class="moneda mensual"  type="text" style="border: none; background: transparent;" value="<? echo $info_mensual; ?>"/>
							</td>
							<td>
								<input class="moneda bimestral"  type="text" style="border: none; background: transparent;" value="<? echo $info_bimestral; ?>"/>
							</td>
							<td>
								<input class="moneda semestral"  type="text" style="border: none; background: transparent;" value="<? echo $info_semestral; ?>"/>
							</td>
							<td>
								<input class="moneda anual" type="text" style="border: none; background: transparent;" value="<? echo $info_anual; ?>"/>
							</td>
							<td><? echo $tb['anio']; ?></td>
							<td>
								<button class="btn btn-primary btn-sm btn-modifica" data-id=<? echo $tb['id_cuouta']; ?> title="Modificar información">
									<img src="./img/flopy.png" width="15" height="15"/>
								</button>
							</td>
							<td>
								<button class="btn btn-secondary btn-sm btn-eliminar" data-id=<? echo $tb['id_cuouta']; ?> title="Eliminar información">
									<img src="./img/tache.png" width="15" height="15"/>
								</button>
							</td>
						</tr>
<?php
					}
?>
					<tr>
						<td></td>
						<td><textarea class="form-control form-control-sm" id="txt-division" rows="1"></textarea></td>
						<td><textarea class="form-control form-control-sm" id="txt-grupo" rows="1"></textarea></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-salario" value="" /></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-monto" value="" /></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-mensual" value="" /></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-bimestral" value="" /></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-semestral" value="" /></td>
						<td><input type="text" class="form-control form-control-sm moneda" id="txt-anual" value="" /></td>
						<td></td>
						<td>
							<button class="btn btn-primary btn-sm btn-guardar" title="Guardar información">
								<img src="./img/flopy.png" width="15" height="15"/>
							</button>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
			<script>
				$(".btn-modifica").on('click', function()
				{
					let id=$(this).data("id");
					if(id > 0)
					{
						let division = $("#reg_" + id + " .division").val();
						let grupo = $("#reg_" + id + " .grupo").val();
						let salario = $("#reg_" + id + " .salario").val();
						let monto = $("#reg_" + id + " .monto").val();
						let mensual = $("#reg_" + id + " .mensual").val();
						let bimestral = $("#reg_" + id + " .bimestral").val();
						let semestral = $("#reg_" + id + " .semestral").val();
						let anual = $("#reg_" + id + " .anual").val();
						let anio = $('#slc-anios').val();
						let tipo = $("#tipoCuota").val();

						if (anio == 0) 
						{
							let currentDate = new Date();
							let currentYear = currentDate.getFullYear();
							anio = currentYear;
						}

						let formData = 
						{
							'id': id,
							'division': division,
							'grupo': grupo,
							'salario': salario,
							'monto': monto,
							'mensual': mensual,
							'bimestral': bimestral,
							'semestral': semestral,
							'anual': anual,
							'anio': anio,
							'tipo': tipo,
							'op': 78
						};
						$.ajax({
							type: "POST",
							url: "./ajax/facilitador.php",
							data: formData,
							success: function(response) 
							{
								alert(response);
								actulizartabla();
							}
						});
					}
				});

				$(".btn-guardar").on('click', function()
				{
					let division = $('#txt-division').val();
					let grupo = $('#txt-grupo').val();
					let salario = $('#txt-salario').val();
					let monto = $('#txt-monto').val();
					let mensual = $('#txt-mensual').val();
					let bimestral = $('#txt-bimestral').val();
					let semestral = $('#txt-semestral').val();
					let anual = $('#txt-anual').val();
					let anio = $('#slc-anios').val();
					let tipo = $("#tipoCuota").val();

					if (anio == 0) 
					{
						let currentDate = new Date();
						let currentYear = currentDate.getFullYear();
						anio = currentYear;
					}

					if (division == "") 
					{
						alert("Por favor, ingresa la división.");
						return false;
					}
					if (grupo == "") 
					{
						alert("Por favor, ingresa el grupo principal.");
						return false;
					}
					if (salario == "") 
					{
						alert("Por favor, ingresa el salario base.");
						return false;
					}
					if (monto == "") 
					{
						alert("Por favor, ingresa el monto minimo.");
						return false;
					}
					if (mensual == "") 
					{
						alert("Por favor, ingresa la cuota mensual.");
						return false;
					}
					if (bimestral == "") 
					{
						alert("Por favor, ingresa la cuota bimestral.");
						return false;
					}
					if (semestral == "") 
					{
						alert("Por favor, ingresa la cuota semestral.");
						return false;
					}
					if (anual == "") 
					{
						alert("Por favor, ingresa la cuota anual.");
						return false;
					}
					
					let formData = 
					{
						'id': 0,
						'division': division,
						'grupo': grupo,
						'salario': salario,
						'monto': monto,
						'mensual': mensual,
						'bimestral': bimestral,
						'semestral': semestral,
						'anual': anual,
						'anio': anio,
						'tipo': tipo,
						'op': 79
					};

					$.ajax({
						type: "POST",
						url: "./ajax/facilitador.php",
						data: formData,
						success: function(response) 
						{
							alert(response);
							actulizartabla();
						}
					});
				});

				$(".btn-eliminar").on('click', function() 
				{
					let id = $(this).data("id");
					if (id !== 0) 
					{ 
						let formData = 
						{
							'id': id,
							'op': 80
						};
						$.ajax({
						type: "POST",
						url: "./ajax/facilitador.php",
						data: formData,
						success: function(response) 
						{
							alert(response);
							actulizartabla();
						}
					});
					}
				});


			</script>
<?php
		break;
		case 78:
			$id = addslashes(trim($_POST["id"]));
			$division = addslashes(trim($_POST["division"]));
			$grupo = addslashes(trim($_POST["grupo"]));
			$salario = addslashes(trim($_POST["salario"]));
			$monto = addslashes(trim($_POST["monto"]));
			$mensual = addslashes(trim($_POST["mensual"]));
			$bimestral = addslashes(trim($_POST["bimestral"]));
			$semestral = addslashes(trim($_POST["semestral"]));
			$anual = addslashes(trim($_POST["anual"]));
			$anio = addslashes(trim($_POST["anio"]));
			$tipo = addslashes(trim($_POST["tipo"]));

			$salario = str_replace('$', '', $salario);
			$salario = str_replace(',', '', $salario);
			$monto = str_replace('$', '', $monto);
			$monto = str_replace(',', '', $monto);
			$mensual = str_replace('$', '', $mensual);
			$mensual = str_replace(',', '', $mensual);
			$bimestral = str_replace('$', '', $bimestral);
			$bimestral = str_replace(',', '', $bimestral);
			$semestral = str_replace('$', '', $semestral);
			$semestral = str_replace(',', '', $semestral);
			$anual = str_replace('$', '', $anual);
			$anual = str_replace(',', '', $anual);
			$query = "UPDATE plan_ideal.cuotas_zonas SET division = '$division',grupo = '$grupo',
							salario_base = $salario, monto_minimo = $monto, mensual = $mensual,
							bimestral =$bimestral, semestral = $semestral, anual = $anual, zona = '$tipo', anio = $anio
							WHERE id_cuouta = $id";
			if ($id != "") 
			{
				$conecta->consulta($query);
				echo "Datos guardados con exito";
			}
		break;
		case 79:
			$id = addslashes(trim($_POST["id"]));
			$division = addslashes(trim($_POST["division"]));
			$grupo = addslashes(trim($_POST["grupo"]));
			$salario = addslashes(trim($_POST["salario"]));
			$monto = addslashes(trim($_POST["monto"]));
			$mensual = addslashes(trim($_POST["mensual"]));
			$bimestral = addslashes(trim($_POST["bimestral"]));
			$semestral = addslashes(trim($_POST["semestral"]));
			$anual = addslashes(trim($_POST["anual"]));
			$anio = addslashes(trim($_POST["anio"]));
			$tipo = addslashes(trim($_POST["tipo"]));

			$salario = str_replace('$', '', $salario);
			$salario = str_replace(',', '', $salario);
			$monto = str_replace('$', '', $monto);
			$monto = str_replace(',', '', $monto);
			$mensual = str_replace('$', '', $mensual);
			$mensual = str_replace(',', '', $mensual);
			$bimestral = str_replace('$', '', $bimestral);
			$bimestral = str_replace(',', '', $bimestral);
			$semestral = str_replace('$', '', $semestral);
			$semestral = str_replace(',', '', $semestral);
			$anual = str_replace('$', '', $anual);
			$anual = str_replace(',', '', $anual);
			$query = "INSERT INTO plan_ideal.cuotas_zonas VALUES ($id, '$division', '$grupo', $salario, $monto, 
						$mensual, $bimestral, $semestral, $anual, '$tipo', $anio)";
			$conecta->consulta($query);
			echo "Datos guardados con exito";

		break;
		case 80:
			$id = addslashes(trim($_POST["id"]));
			$query = "DELETE FROM plan_ideal.cuotas_zonas WHERE id_cuouta = $id";
			if ($id != "") 
			{
				$conecta->consulta($query);
				echo "Datos eliminados con exito";
			}
		break;
		case 81:
			$comisionista = addslashes(trim($_POST["comisionista"]));
			$query = "SELECT eg.sc AS sc, eg.brocker AS brocker, g.gerencia AS gerencia
										FROM (
											SELECT DISTINCT no_empleado, id_gerencia
											FROM usuarios_nivel_gerencia
											WHERE pertenece = 1
										) ung
										INNER JOIN (
											SELECT DISTINCT id_gerencia, gerencia
											FROM gerencias
										) g ON ung.id_gerencia = g.id_gerencia
										INNER JOIN empl_usuarios eu ON ung.no_empleado = eu.no_empleado
										INNER JOIN estructura_gerencia eg ON g.id_gerencia = eg.id_gerencia
										WHERE eu.nombre = '$comisionista'";
			$bd = $conecta->consulta($query);
			$resultados = array();
			if ($bd->num_rows != 0) 
			{ 
				while($tb = mysqli_fetch_assoc($bd)) {
					$resultados[] = $tb;
				}
				header('Content-Type: application/json');
				$json = json_encode($resultados);
				echo $json;
			}
			else
			{
				header('Content-Type: application/json');
				$json = json_encode($resultados);
				echo $json;
			}
		break;
		case 82:
			$clave = addslashes(trim($_POST["clave"]));
			$anticipo1 = addslashes(trim($_POST["anticipo1"]));
			$anticipo2 = addslashes(trim($_POST["anticipo2"]));
			
			$anticipo1 = ($anticipo1 / 100);
			$anticipo2 = ($anticipo2 / 100);
			$query = "UPDATE por_anticipos_sc SET por_anticipo1 = $anticipo1, por_anticipo2 = $anticipo2 WHERE clave = '$clave'";
			//echo $query;
			if ($clave != "") 
			{
				$conecta->consulta($query);
				echo "Datos guardados con exito";
			}
		break;
		case 83:
				$clave = addslashes(trim($_POST["clave"]));
				$folio = addslashes(trim($_POST["folio"]));
				$anio = date("Y");

				$q_cerfificado = "Select * FROM carobra_2014.produccion WHERE folio = '$folio'";
				$dbc =  $conecta->consulta($q_cerfificado);
				$tbc = mysqli_fetch_array($dbc);

				$q_empleado = "Select no_empleado FROM carobra_2014.empl_usuarios 
							   WHERE clave = '$clave'";
				$dbe =  $conecta->consulta($q_empleado);
				$tbe = mysqli_fetch_array($dbe);
				$no_empleado = $tbe['no_empleado'];

				// valores a conservar
				$monto_primer_ant = 0;
				$monto_segundo_ant = 0;
				$monto_total_ant = 0;
				$clave_asesor_pago = "";
				$fec_pago_primer_ant = "";
				$fec_pago_segundo_ant = "";
				$fec_pago_primer = "";
				$fec_pago_segundo = "";

				
				$com_asesor = 0;
				$com_gerente = 0;
				$com_brocker = 0;
				$com_sc = 0;

				$mansaje_primer_ant = "";
				$mansaje_segun_ant = "";

				$mansaje_cetificacion_sc = "";
				$mansaje_cetificacion_sb = "";
				$mansaje_cetificacion_g = "";
				$mansaje_cetificacion_a = "";
				$fec_traspaso = $tbc['fec_traspaso'];
				if (!empty($fec_traspaso)) 
				{
					$fecha_parts = explode('/', $fec_traspaso);
					if (count($fecha_parts) === 3) 
					{
						// Asignar valores
						$mes = (int)$fecha_parts[1];
						$aniop = (int)$fecha_parts[2];
					} 
					else 
					{
						echo "El formato de la fecha '$fec_traspaso' no es válido.";
					}
				}
				else 
				{
					echo "La fecha está vacía.";
				}

				$q_pro_certificado ="Select * FROM carobra.acum_det_prod_$anio 
									 WHERE folio = '$folio' 
									 	AND (com_asesor + com_gerente +  com_brocker + com_sc) > 0";
				$dbpc =  $conecta->consulta($q_pro_certificado);
				if ($dbpc->num_rows == 0) 
				{
					$monto_primer_ant = $tbc['monto_anticipo'];
					$fec_pago_primer_ant = $tbc['fec_pago_ant'];

					$q_unoempleado = "Update carobra_2014.produccion 
									  SET no_empleado = $no_empleado, id_estatus_anticipo = 0, 
										  fec_anticipo = '', fec_pago_ant = '', monto_anticipo = 0 
									  WHERE folio = '$folio'";
					$conecta->consulta($q_unoempleado);

					$q_segundo_ant = "Select * FROM carobra_2014.segundo_anticipo 
									  WHERE folio = '$folio'";
					$dbsant = $conecta->consulta($q_segundo_ant);
					if ($dbsant->num_rows !== 0) 
					{
						$tbsant = mysqli_fetch_array($dbsant);
						$monto_segundo_ant = $tbsant['monto'];
						$fec_pago_segundo_ant = $tbsant['fec_pago'];

						$qd_segundo = "Delete FROM carobra_2014.segundo_anticipo 
									   WHERE folio = '$folio'";
						$conecta->consulta($qd_segundo);
					}

					$monto_total_ant = $monto_primer_ant + $monto_segundo_ant;
					if ($fec_pago_primer_ant == $fec_pago_segundo_ant) 
					{
						$q_det_anticipo = "Select * FROM carobra.acum_det_anticipo_$anio 
										   WHERE folio = '$folio'";
						$dbdetant = $conecta->consulta($q_det_anticipo);
						if ($dbdetant->num_rows !== 0) 
						{
							$tbdetant = mysqli_fetch_array($dbdetant);
							$clave_asesor_pago = $tbdetant['clave'];
							$fec_pago_primer = $tbdetant['fec_pago'];
							$qd_det_anticipo = "Delete FROM carobra.acum_det_anticipo_$anio 
												WHERE folio = '$folio'";
							$conecta->consulta($qd_det_anticipo);
						}

						$q_acum_anticipo = "Select * FROM carobra.acum_pagos_anticipos_$anio 
											WHERE clave = '$clave_asesor_pago' 
												AND fec_pago = '$fec_pago_primer'";
						$dbacumant = $conecta->consulta($q_acum_anticipo);
						if ($dbacumant->num_rows !== 0) 
						{
							$qu_acum_anticipo = "Update carobra.acum_pagos_anticipos_$anio 
												 SET anticipo = (anticipo - $monto_primer_ant), 
													 segundo_anticipo = (segundo_anticipo - $monto_segundo_ant) 
												 WHERE clave = '$clave_asesor_pago' 
												 	AND fec_pago = '$fec_pago_primer'";
							$conecta->consulta($qu_acum_anticipo);
						}

						$q_pagos_otros = "Select * FROM carobra.acum_pagos_otros_$anio 
										  WHERE clave = '$clave_asesor_pago' 
										  	AND fec_pago = '$fec_pago_primer'";
						$dbpagtros = $conecta->consulta($q_pagos_otros);
						if ($dbpagtros->num_rows !== 0) 
						{
							$qu_pag_otros = "Update carobra.acum_pagos_otros_$anio 
											 SET ajuste = (ajuste + $monto_total_ant) 
											 WHERE clave = '$clave_asesor_pago' 
											 	AND fec_pago = '$fec_pago_primer'";
							$conecta->consulta($qu_pag_otros);
						} 
						else 
						{
							$q_estructura = "Select DISTINCT es.sc, es.brocker, g.id_gerencia 
											 FROM carobra_2014.gerencias g
												INNER JOIN carobra_2014.usuarios_nivel_gerencia ung 
														ON (g.id_gerencia = ung.id_gerencia)
												INNER JOIN carobra_2014.estructura_gerencia es 
														ON (ung.id_gerencia = es.id_gerencia)
											 WHERE ung.clave = '$clave_asesor_pago' 
											 	AND ung.pertenece = 1";
							$dbestruc = $conecta->consulta($q_estructura);
							$tbestruc = mysqli_fetch_array($dbestruc);
							$sc = $tbestruc['sc'];
							$brocker = $tbestruc['brocker'];
							$id_gerencia = $tbestruc['id_gerencia'];

							$qi_pag_otros = "Insert INTO carobra.acum_pagos_otros_$anio VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago', '$fec_pago_primer', $monto_total_ant, 0, 0, 0, 0, 0, 0)";
							$conecta->consulta($qi_pag_otros);
						}
						$mansaje_primer_ant = "Anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_primer\nAjuste: $monto_total_ant";
						
						$a = compl_ajuste(1);
						$b = compl_ajuste(0, $clave_asesor_pago);
						$c = $b[0]["unidad"];
						$d = $b[0]["id_gerencia"];
						$e = $b[0]["socio"];
						$tablaDestino = '';

						switch (strtoupper($c)) 
						{
							case 'PREVISIONAL':
								$tablaDestino = 'ajustes_previsional';
							break;
							case 'TARJETA':
								$tablaDestino = 'ajustes_tarjeta';
							break;
							case 'HIPOTECARIO':
								$tablaDestino = 'ajustes_hipotecario';
							break;
							case 'SEGUROS':
								$tablaDestino = 'ajustes_seguros';
							break;
							case 'CORPORATIVO':
								$tablaDestino = 'ajustes_corporativo';
							break;
							default:
								echo "Unidad de negocio no válida: $c";
							exit;
						}

						if ($tablaDestino !== "") 
						{
							$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago','$fec_pago_primer','$fec_pago_primer', '$e', $d, $aniop, $mes, $monto_total_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_ajuste);

							$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
							$monto_total_ant = -abs($monto_total_ant);
							$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m')." , $monto_total_ant, 'Descuento pago erroneo de anticipo(s) del folio $folio', 0, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_nuevo_ajuste);
							echo "Datos de anticipo actulizados con exito\n".$mansaje_primer_ant;

						}
					} 
					else 
					{
						if ($fec_pago_primer_ant !== "") 
						{
							$q_det_anticipo = "Select * FROM carobra.acum_det_anticipo_$anio 
											   WHERE folio = '$folio' 
											   		AND fec_pago = '$fec_pago_primer_ant'";
							$dbdetant = $conecta->consulta($q_det_anticipo);
							if ($dbdetant->num_rows !== 0) 
							{
								$tbdetant = mysqli_fetch_array($dbdetant);
								$clave_asesor_pago = $tbdetant['clave'];
								$fec_pago_primer = $tbdetant['fec_pago'];
								$qd_det_anticipo = "Delete FROM carobra.acum_det_anticipo_$anio 
													WHERE folio = '$folio' 
														AND fec_pago = '$fec_pago_primer_ant'";
								$conecta->consulta($qd_det_anticipo);
							}

							$q_acum_anticipo = "Select * FROM carobra.acum_pagos_anticipos_$anio
												WHERE clave = '$clave_asesor_pago' 
													AND fec_pago = '$fec_pago_primer'";
							$dbacumant = $conecta->consulta($q_acum_anticipo);
							if ($dbacumant->num_rows !== 0) 
							{
								$qu_acum_anticipo = "Update carobra.acum_pagos_anticipos_$anio 
													 SET anticipo = (anticipo - $monto_primer_ant) 
													 WHERE clave = '$clave_asesor_pago' 
													 	AND fec_pago = '$fec_pago_primer'";
								$conecta->consulta($qu_acum_anticipo);
							}

							$q_pagos_otros = "Select * FROM carobra.acum_pagos_otros_$anio 
											  WHERE clave = '$clave_asesor_pago' 
											  	AND fec_pago = '$fec_pago_primer'";
							$dbpagtros = $conecta->consulta($q_pagos_otros);
							if ($dbpagtros->num_rows !== 0) 
							{
								$qu_pag_otros = "Update carobra.acum_pagos_otros_$anio 
												 SET ajuste = (ajuste + $monto_primer_ant) 
												 WHERE clave = '$clave_asesor_pago' 
												 	AND fec_pago = '$fec_pago_primer'";
								$conecta->consulta($qu_pag_otros);
							} 
							else 
							{
								$q_estructura = "Select DISTINCT es.sc, es.brocker, g.id_gerencia 
												 FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es 
															ON (ung.id_gerencia = es.id_gerencia)
												 WHERE ung.clave = '$clave_asesor_pago' 
												 	AND ung.pertenece = 1";
								$dbestruc = $conecta->consulta($q_estructura);
								$tbestruc = mysqli_fetch_array($dbestruc);
								$sc = $tbestruc['sc'];
								$brocker = $tbestruc['brocker'];
								$id_gerencia = $tbestruc['id_gerencia'];

								$qi_pag_otros = "Insert INTO carobra.acum_pagos_otros_$anio VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago','$fec_pago_primer', $monto_primer_ant, 0, 0, 0, 0, 0, 0)";
								$conecta->consulta($qi_pag_otros);
							}
							
							$mansaje_primer_ant = "Primer anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_primer\nAjuste: $monto_primer_ant\n";
							
							$a = compl_ajuste(1);
							$b = compl_ajuste(0, $clave_asesor_pago);
							$c = $b[0]["unidad"];
							$d = $b[0]["id_gerencia"];
							$e = $b[0]["socio"];
							$tablaDestino = '';
							switch (strtoupper($c)) 
							{
								case 'PREVISIONAL':
									$tablaDestino = 'ajustes_previsional';
								break;
								case 'TARJETA':
									$tablaDestino = 'ajustes_tarjeta';
								break;
								case 'HIPOTECARIO':
									$tablaDestino = 'ajustes_hipotecario';
								break;
								case 'SEGUROS':
									$tablaDestino = 'ajustes_seguros';
								break;
								case 'CORPORATIVO':
									$tablaDestino = 'ajustes_corporativo';
								break;
								default:
									echo "Unidad de negocio no válida: $c";
								exit;
							}

							if ($tablaDestino !== "") 
							{
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_pago_primer', '$fec_pago_primer', '$e', $d, $aniop, $mes, $monto_primer_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);
							}
						}

						if ($fec_pago_segundo_ant !== "") 
						{
							$q_det_anticipo = "Select * FROM carobra.acum_det_anticipo_$anio 
											   WHERE folio = '$folio' 
											   		AND fec_pago = '$fec_pago_segundo_ant'";
							$dbdetant = $conecta->consulta($q_det_anticipo);
							if ($dbdetant->num_rows !== 0) 
							{
								$tbdetant = mysqli_fetch_array($dbdetant);
								$clave_asesor_pago = $tbdetant['clave'];
								$fec_pago_segundo = $tbdetant['fec_pago'];
								$qd_det_anticipo = "Delete FROM carobra.acum_det_anticipo_$anio 
													WHERE folio = '$folio' 
														AND fec_pago = '$fec_pago_segundo_ant'";
								$conecta->consulta($qd_det_anticipo);
							}

							$q_acum_anticipo = "Select * FROM carobra.acum_pagos_anticipos_$anio 
												WHERE clave = '$clave_asesor_pago' 
													AND fec_pago = '$fec_pago_segundo'";
							$dbacumant = $conecta->consulta($q_acum_anticipo);
							if ($dbacumant->num_rows !== 0) 
							{
								$qu_acum_anticipo = "Update carobra.acum_pagos_anticipos_$anio 
													 SET segundo_anticipo = (segundo_anticipo - $monto_segundo_ant) 
													 WHERE clave = '$clave_asesor_pago' 
													 	AND fec_pago = '$fec_pago_segundo'";
								$conecta->consulta($qu_acum_anticipo);
							}

							$q_pagos_otros = "Select * FROM carobra.acum_pagos_otros_$anio 
											  WHERE clave = '$clave_asesor_pago' 
											  		AND fec_pago = '$fec_pago_segundo'";
							$dbpagtros = $conecta->consulta($q_pagos_otros);
							if ($dbpagtros->num_rows !== 0) 
							{
								$qu_pag_otros = "Update carobra.acum_pagos_otros_$anio 
												 SET ajuste = (ajuste + $monto_segundo_ant) 
												 WHERE clave = '$clave_asesor_pago' 
												 	AND fec_pago = '$fec_pago_segundo'";
								$conecta->consulta($qu_pag_otros);
							} 
							else 
							{
								$q_estructura = "Select DISTINCT es.sc, es.brocker, g.id_gerencia 
												 FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es 
															ON (ung.id_gerencia = es.id_gerencia)
												 WHERE ung.clave = '$clave_asesor_pago' 
												 	AND ung.pertenece = 1";
								$dbestruc = $conecta->consulta($q_estructura);
								$tbestruc = mysqli_fetch_array($dbestruc);
								$sc = $tbestruc['sc'];
								$brocker = $tbestruc['brocker'];
								$id_gerencia = $tbestruc['id_gerencia'];

								$qi_pag_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
													VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago', '$fec_pago_segundo', $monto_segundo_ant, 0, 0, 0, 0, 0, 0)";
								$conecta->consulta($qi_pag_otros);
							}
							$mansaje_segun_ant = "Segundo anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_segundo\nAjuste: $monto_segundo_ant";
							
							$a = compl_ajuste(1);
							$b = compl_ajuste(0, $clave_asesor_pago);
							$c = $b[0]["unidad"];
							$d = $b[0]["id_gerencia"];
							$e = $b[0]["socio"];

							$tablaDestino = '';
							switch (strtoupper($c)) 
							{
								case 'PREVISIONAL':
									$tablaDestino = 'ajustes_previsional';
								break;
								case 'TARJETA':
									$tablaDestino = 'ajustes_tarjeta';
								break;
								case 'HIPOTECARIO':
									$tablaDestino = 'ajustes_hipotecario';
								break;
								case 'SEGUROS':
									$tablaDestino = 'ajustes_seguros';
								break;
								case 'CORPORATIVO':
									$tablaDestino = 'ajustes_corporativo';
								break;
								default:
									echo "Unidad de negocio no válida: $c";
								exit;
							}

							if ($tablaDestino !== "") 
							{
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_pago_segundo', '$fec_pago_segundo', '$e', $d, $aniop, $mes, $monto_segundo_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);
							}
							
						}
						$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
						$monto_total_ant = -abs($monto_total_ant);
						
						$a = compl_ajuste(1);
						$b = compl_ajuste(0, $clave_asesor_pago);
						$c = $b[0]["unidad"];
						$d = $b[0]["id_gerencia"];
						$e = $b[0]["socio"];

						$tablaDestino = '';
						switch (strtoupper($c)) 
						{
							case 'PREVISIONAL':
								$tablaDestino = 'ajustes_previsional';
							break;
							case 'TARJETA':
								$tablaDestino = 'ajustes_tarjeta';
							break;
							case 'HIPOTECARIO':
								$tablaDestino = 'ajustes_hipotecario';
							break;
							case 'SEGUROS':
								$tablaDestino = 'ajustes_seguros';
							break;
							case 'CORPORATIVO':
								$tablaDestino = 'ajustes_corporativo';
							break;
							default:
								echo "Unidad de negocio no válida: $c";
							exit;
						}

						if ($tablaDestino !== "") 
						{
							$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $monto_total_ant, 'Descuento pago erroneo de anticipo(s) del folio $folio', 0, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_nuevo_ajuste);
						}
						echo "Datos de anticipo actulizados con exito \n".$mansaje_primer_ant.$mansaje_segun_ant;
					}
				} 
				else 
				{
					$monto_primer_ant = $tbc['monto_anticipo'];
					$fec_pago_primer_ant = $tbc['fec_pago_ant'];

					$q_unoempleado = "UPDATE carobra_2014.produccion SET no_empleado = $no_empleado, id_estatus_anticipo = 0, 
										fec_anticipo = '', fec_pago_ant = '', monto_anticipo = 0, id_estatus_prod = 1 WHERE folio = '$folio'";
					$conecta->consulta($q_unoempleado);

					$q_segundo_ant = "SELECT * FROM carobra_2014.segundo_anticipo WHERE folio = '$folio'";
					$dbsant = $conecta->consulta($q_segundo_ant);
					if ($dbsant->num_rows !== 0) 
					{
						$tbsant = mysqli_fetch_array($dbsant);
						$monto_segundo_ant = $tbsant['monto'];
						$fec_pago_segundo_ant = $tbsant['fec_pago'];

						$qd_segundo = "DELETE FROM carobra_2014.segundo_anticipo WHERE folio = '$folio'";
						$conecta->consulta($qd_segundo);
					}

					$monto_total_ant = $monto_primer_ant + $monto_segundo_ant;
					if ($fec_pago_primer_ant == $fec_pago_segundo_ant) 
					{
						$q_det_anticipo = "SELECT * FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio'";
						$dbdetant = $conecta->consulta($q_det_anticipo);
						if ($dbdetant->num_rows !== 0) 
						{
							$tbdetant = mysqli_fetch_array($dbdetant);
							$clave_asesor_pago = $tbdetant['clave'];
							$fec_pago_primer = $tbdetant['fec_pago'];
							$qd_det_anticipo = "DELETE FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio'";
							$conecta->consulta($qd_det_anticipo);
						}

						$q_acum_anticipo = "SELECT * FROM carobra.acum_pagos_anticipos_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
						$dbacumant = $conecta->consulta($q_acum_anticipo);
						if ($dbacumant->num_rows !== 0) 
						{
							$qu_acum_anticipo = "UPDATE carobra.acum_pagos_anticipos_$anio SET anticipo = (anticipo - $monto_primer_ant), 
												segundo_anticipo = (segundo_anticipo - $monto_segundo_ant) WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
							$conecta->consulta($qu_acum_anticipo);
						}

						$q_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
						$dbpagtros = $conecta->consulta($q_pagos_otros);
						if ($dbpagtros->num_rows !== 0) 
						{
							$qu_pag_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $monto_total_ant) 
													WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
							$conecta->consulta($qu_pag_otros);
						} 
						else 
						{
							$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
											INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
											INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
											WHERE ung.clave = '$clave_asesor_pago' AND ung.pertenece = 1";
							$dbestruc = $conecta->consulta($q_estructura);
							$tbestruc = mysqli_fetch_array($dbestruc);
							$sc = $tbestruc['sc'];
							$brocker = $tbestruc['brocker'];
							$id_gerencia = $tbestruc['id_gerencia'];

							$qi_pag_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
												VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago', '$fec_pago_primer', $monto_total_ant, 0, 0, 0, 0, 0, 0)";
							$conecta->consulta($qi_pag_otros);
						}
						$mansaje_primer_ant = "Anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_primer\nAjuste: $monto_total_ant\n";


						$a = compl_ajuste(1);
						$b = compl_ajuste(0, $clave_asesor_pago);
						$c = $b[0]["unidad"];
						$d = $b[0]["id_gerencia"];
						$e = $b[0]["socio"];

						$tablaDestino = '';
						switch (strtoupper($c)) 
						{
							case 'PREVISIONAL':
								$tablaDestino = 'ajustes_previsional';
							break;
							case 'TARJETA':
								$tablaDestino = 'ajustes_tarjeta';
							break;
							case 'HIPOTECARIO':
								$tablaDestino = 'ajustes_hipotecario';
							break;
							case 'SEGUROS':
								$tablaDestino = 'ajustes_seguros';
							break;
							case 'CORPORATIVO':
								$tablaDestino = 'ajustes_corporativo';
							break;
							default:
								echo "Unidad de negocio no válida: $c";
							exit;
						}

						if ($tablaDestino !== "") 
						{
							$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_pago_primer', '$fec_pago_primer', '$e', $d, $aniop, $mes, $monto_total_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_ajuste);
						}

						$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
						$monto_total_ant = -abs($monto_total_ant);

						$a = compl_ajuste(1);
						$b = compl_ajuste(0, $clave_asesor_pago);
						$c = $b[0]["unidad"];
						$d = $b[0]["id_gerencia"];
						$e = $b[0]["socio"];

						$tablaDestino = '';
						switch (strtoupper($c)) 
						{
							case 'PREVISIONAL':
								$tablaDestino = 'ajustes_previsional';
							break;
							case 'TARJETA':
								$tablaDestino = 'ajustes_tarjeta';
							break;
							case 'HIPOTECARIO':
								$tablaDestino = 'ajustes_hipotecario';
							break;
							case 'SEGUROS':
								$tablaDestino = 'ajustes_seguros';
							break;
							case 'CORPORATIVO':
								$tablaDestino = 'ajustes_corporativo';
							break;
							default:
								echo "Unidad de negocio no válida: $c";
							exit;
						}

						if ($tablaDestino !== "") 
						{
							$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $monto_total_ant, 'Descuento pago erroneo de anticipo(s) del folio $folio', 0, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_nuevo_ajuste);
						}

						$q_desc_certificacion = "SELECT * FROM carobra.acum_det_prod_$anio WHERE folio = '$folio'";
						$dbdesccer = $conecta->consulta($q_desc_certificacion);
						if ($dbdesccer->num_rows !== 0) 
						{
							$tbdesccer = mysqli_fetch_array($dbdesccer);
								
							$com_asesor = $tbdesccer['com_asesor'];
							$com_gerente = $tbdesccer['com_gerente'];
							$com_brocker = $tbdesccer['com_brocker'];
							$com_sc = $tbdesccer['com_sc'];
							$fecha_pago_cer = $tbdesccer['fec_pago'];
								
							$sc = $tbdesccer['sc'];
							$brocker = $tbdesccer['brocker'];
							$gerencia = $tbdesccer['id_gerencia'];
							$asesor = $tbdesccer['no_empleado'];

							$clave_sc = "";
							$clave_brocker = "";
							$clave_gerente = "";
							$clave_asesor = "";

							if ($com_sc !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE nombre = '$sc'";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_sc = $tbe['clave'];
							}
							if ($com_brocker !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE nombre = '$brocker'";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_brocker = $tbe['clave'];
							}
							if ($com_gerente !== 0) 
							{
								$q_empleado = "Select distinct clave from carobra_2014.usuarios_nivel_gerencia where id_nivel=4 and pertenece=1 and id_gerencia = $gerencia";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_gerente = $tbe['clave'];
							}
							if ($com_asesor !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE no_empleado = $asesor";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_asesor = $tbe['clave'];
							}

							$qd_certificacion = "DELETE FROM carobra.acum_det_prod_$anio WHERE folio = '$folio'";
							$conecta->consulta($qd_certificacion);
							
							if ($clave_sc !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								
								if ($com_brocker !== 0) 
								{
									$descuento = $com_sc - $com_brocker;
								} 
								else 
								{
									if ($com_asesor == 0) 
									{
										$descuento = $com_sc - $monto_total_ant;
									}
									else 
									{
										$descuento = $com_sc - $com_asesor - $com_gerente;
									}
								}

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_sc' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_sc', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer' AND com_sc > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_sc = (com_sc - $descuento) WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$mansaje_cetificacion_sc = "Certificiación SC\nClave: $clave_sc\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
								$b = compl_ajuste(0, $clave_sc);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}
								
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_sc', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_sc', '$fec_cap', '', '$e', $d,  ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión SC de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}
							if ($clave_brocker !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								
								if ($clave_asesor == $clave_brocker) 
								{
									$descuento = $com_brocker - $monto_total_ant;
								} 
								else 
								{
									$descuento = $com_brocker - $com_asesor - $com_gerente;
								}

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_brocker' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_brocker', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer' AND com_sb > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_sb = (com_sb - $descuento) WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$a = compl_ajuste(1);
								$b = compl_ajuste(0, $clave_brocker);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								if ($tablaDestino !== "") 
								{
									$mansaje_cetificacion_sb = "Certificiación brocker\nClave: $clave_brocker\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
									$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_brocker', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
									$conecta->consulta($qi_ajuste);
								}

								

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;

								$b = compl_ajuste(0, $clave_brocker);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								if ($tablaDestino !== "") 
								{
									$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_brocker', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión Brocker de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
									$conecta->consulta($qi_nuevo_ajuste);
								}
								
							}
							if ($clave_gerente !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								$descuento = $com_gerente;

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_gerente' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_gerente', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer' AND com_gerente > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_gerente  = (com_gerente - $descuento) WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$mansaje_cetificacion_g = "Certificiación gerente\nClave: $clave_gerente\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
								
								$b = compl_ajuste(0, $clave_gerente);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								if ($tablaDestino !== "") {
									$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_gerente', '$fecha_pago_cer', '$fecha_pago_cer',  '$e', '$d', $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
									$conecta->consulta($qi_ajuste);
								}

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_gerente', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión gerente de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}
							if ($clave_asesor !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								$descuento = $com_asesor - $monto_total_ant;

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_asesor' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer' AND com_asesor > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_asesor  = (com_asesor  - $descuento) WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$mansaje_cetificacion_a = "Certificiación asesor\nClave: $clave_asesor\nFec pag: $fecha_pago_cer\nAjuste: $descuento";
								
								$b = compl_ajuste(0, $clave_asesor);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión asesor de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}

						}
						echo "Datos de anticipo y cerficación actulizados con exito\n".$mansaje_primer_ant.$mansaje_cetificacion_sc.$mansaje_cetificacion_sb.$mansaje_cetificacion_g.$mansaje_cetificacion_a;
					} 
					else 
					{
						if ($fec_pago_primer_ant !== "") 
						{
							$q_det_anticipo = "SELECT * FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio' AND fec_pago = '$fec_pago_primer_ant'";
							$dbdetant = $conecta->consulta($q_det_anticipo);
							if ($dbdetant->num_rows !== 0) 
							{
								$tbdetant = mysqli_fetch_array($dbdetant);
								$clave_asesor_pago = $tbdetant['clave'];
								$fec_pago_primer = $tbdetant['fec_pago'];
								$qd_det_anticipo = "DELETE FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio' AND fec_pago = '$fec_pago_primer_ant'";
								$conecta->consulta($qd_det_anticipo);
							}

							$q_acum_anticipo = "SELECT * FROM carobra.acum_pagos_anticipos_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
							$dbacumant = $conecta->consulta($q_acum_anticipo);
							if ($dbacumant->num_rows !== 0) 
							{
								$qu_acum_anticipo = "UPDATE carobra.acum_pagos_anticipos_$anio SET anticipo = (anticipo - $monto_primer_ant) WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
								$conecta->consulta($qu_acum_anticipo);
							}

							$q_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
							$dbpagtros = $conecta->consulta($q_pagos_otros);
							if ($dbpagtros->num_rows !== 0) 
							{
								$qu_pag_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $monto_primer_ant) 
														WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_primer'";
								$conecta->consulta($qu_pag_otros);
							} 
							else 
							{
								$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
												INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
												INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
												WHERE ung.clave = '$clave_asesor_pago' AND ung.pertenece = 1";
								$dbestruc = $conecta->consulta($q_estructura);
								$tbestruc = mysqli_fetch_array($dbestruc);
								$sc = $tbestruc['sc'];
								$brocker = $tbestruc['brocker'];
								$id_gerencia = $tbestruc['id_gerencia'];

								$qi_pag_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
													VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago', '$fec_pago_primer', $monto_primer_ant, 0, 0, 0, 0, 0, 0)";
								$conecta->consulta($qi_pag_otros);
							}
							
							$mansaje_primer_ant = "Primer anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_primer\nAjuste: $monto_primer_ant\n";
							
							$b = compl_ajuste(0, $clave_asesor_pago);
							$c = $b[0]["unidad"];
							$d = $b[0]["id_gerencia"];
							$e = $b[0]["socio"];

							$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}
							$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_pago_primer', '$fec_pago_primer', '$e', $d, $aniop, $mes, $monto_primer_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_ajuste);
						}

						if ($fec_pago_segundo_ant !== "") 
						{
							$q_det_anticipo = "SELECT * FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio' AND fec_pago = '$fec_pago_segundo_ant'";
							$dbdetant = $conecta->consulta($q_det_anticipo);
							if ($dbdetant->num_rows !== 0) 
							{
								$tbdetant = mysqli_fetch_array($dbdetant);
								$clave_asesor_pago = $tbdetant['clave'];
								$fec_pago_segundo = $tbdetant['fec_pago'];
								$qd_det_anticipo = "DELETE FROM carobra.acum_det_anticipo_$anio WHERE folio = '$folio' AND fec_pago = '$fec_pago_segundo_ant'";
								$conecta->consulta($qd_det_anticipo);
							}

							$q_acum_anticipo = "SELECT * FROM carobra.acum_pagos_anticipos_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_segundo'";
							$dbacumant = $conecta->consulta($q_acum_anticipo);
							if ($dbacumant->num_rows !== 0) 
							{
								$qu_acum_anticipo = "UPDATE carobra.acum_pagos_anticipos_$anio SET segundo_anticipo = (segundo_anticipo - $monto_segundo_ant) WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_segundo'";
								$conecta->consulta($qu_acum_anticipo);
							}

							$q_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_segundo'";
							$dbpagtros = $conecta->consulta($q_pagos_otros);
							if ($dbpagtros->num_rows !== 0) 
							{
								$qu_pag_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $monto_segundo_ant) 
														WHERE clave = '$clave_asesor_pago' AND fec_pago = '$fec_pago_segundo'";
								$conecta->consulta($qu_pag_otros);
							} 
							else 
							{
								$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
												INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
												INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
												WHERE ung.clave = '$clave_asesor_pago' AND ung.pertenece = 1";
								$dbestruc = $conecta->consulta($q_estructura);
								$tbestruc = mysqli_fetch_array($dbestruc);
								$sc = $tbestruc['sc'];
								$brocker = $tbestruc['brocker'];
								$id_gerencia = $tbestruc['id_gerencia'];

								$qi_pag_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
													VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor_pago', '$fec_pago_segundo', $monto_segundo_ant, 0, 0, 0, 0, 0, 0)";
								$conecta->consulta($qi_pag_otros);
							}
							$mansaje_segun_ant = "Segundo anticipo\nClave: $clave_asesor_pago\nFec pago: $fec_pago_segundo\nAjuste: $monto_segundo_ant\n";

							$b = compl_ajuste(0, $clave_asesor_pago);
							$c = $b[0]["unidad"];
							$d = $b[0]["id_gerencia"];
							$e = $b[0]["socio"];

							$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

							$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_pago_segundo', '$fec_pago_segundo', '$e', $d, $aniop, $mes, $monto_segundo_ant, 'Pago erróneo de anticipo del folio $folio', 1, '', '', 0, 0, '', 'O')";
							$conecta->consulta($qi_ajuste);
						}

						$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
						$monto_total_ant = -abs($monto_total_ant);
						$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor_pago', '$fec_cap', '', '$e', $d,  ".date('Y').",".date('m').", $monto_total_ant, 'Descuento pago erroneo de anticipo(s) del folio $folio', 0, '', '', 0, 0, '', 'O')";
						$conecta->consulta($qi_nuevo_ajuste);

						$q_desc_certificacion = "SELECT * FROM carobra.acum_det_prod_$anio WHERE folio = '$folio'";
						$dbdesccer = $conecta->consulta($q_desc_certificacion);
						if ($dbdesccer->num_rows !== 0) 
						{
							$tbdesccer = mysqli_fetch_array($dbdesccer);
								
							$com_asesor = $tbdesccer['com_asesor'];
							$com_gerente = $tbdesccer['com_gerente'];
							$com_brocker = $tbdesccer['com_brocker'];
							$com_sc = $tbdesccer['com_sc'];
							$fecha_pago_cer = $tbdesccer['fec_pago'];
								
							$sc = $tbdesccer['sc'];
							$brocker = $tbdesccer['brocker'];
							$gerencia = $tbdesccer['id_gerencia'];
							$asesor = $tbdesccer['no_empleado'];

							$clave_sc = "";
							$clave_brocker = "";
							$clave_gerente = "";
							$clave_asesor = "";

							if ($com_sc !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE nombre = '$sc'";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_sc = $tbe['clave'];
							}
							if ($com_brocker !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE nombre = '$brocker'";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_brocker = $tbe['clave'];
							}
							if ($com_gerente !== 0) 
							{
								$q_empleado = "Select distinct clave from carobra_2014.usuarios_nivel_gerencia where id_nivel=4 and pertenece=1 and id_gerencia = $gerencia";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_gerente = $tbe['clave'];
							}
							if ($com_asesor !== 0) 
							{
								$q_empleado = "SELECT clave FROM carobra_2014.empl_usuarios WHERE no_empleado = $asesor";
								$dbe =  $conecta->consulta($q_empleado);
								$tbe = mysqli_fetch_array($dbe);
								$clave_asesor = $tbe['clave'];
							}

							$qd_certificacion = "DELETE FROM carobra.acum_det_prod_$anio WHERE folio = '$folio'";
							$conecta->consulta($qd_certificacion);
							
							if ($clave_sc !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								
								if ($com_brocker !== 0) 
								{
									$descuento = $com_sc - $com_brocker;
								} 
								else 
								{
									if ($com_asesor == 0) 
									{
										$descuento = $com_sc - $monto_total_ant;
									}
									else 
									{
										$descuento = $com_sc - $com_asesor - $com_gerente;
									}
								}

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_sc' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_sc', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer' AND  com_sc > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_sc = (com_sc - $descuento) WHERE clave = '$clave_sc' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$mansaje_cetificacion_sc = "Certificiación SC\nClave: $clave_sc\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
								
								$b = compl_ajuste(0, $clave_sc);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}
								
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_sc', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_sc', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión SC de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);

							}
							if ($clave_brocker !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								
								if ($clave_asesor == $clave_brocker) 
								{
									$descuento = $com_brocker - $monto_total_ant;
								} 
								else 
								{
									$descuento = $com_brocker - $com_asesor - $com_gerente;
								}

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_brocker' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_brocker', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer' AND com_sb > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_sb = (com_sb - $descuento) WHERE clave = '$clave_brocker' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$b = compl_ajuste(0, $clave_brocker);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								$mansaje_cetificacion_sb = "Certificiación brocker\nClave: $clave_brocker\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_brocker', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_brocker', '$fec_cap', '',  '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión brocker de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}
							if ($clave_gerente !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								$descuento = $com_gerente;

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_gerente' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_gerente', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer' AND com_gerente > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_gerente  = (com_gerente - $descuento) WHERE clave = '$clave_gerente' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$b = compl_ajuste(0, $clave_gerente);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}
								
								$mansaje_cetificacion_g = "Certificiación  gerente\nClave: $clave_gerente\nFec pag: $fecha_pago_cer\nAjuste: $descuento\n";
								$qi_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_gerente', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_gerente', '$fec_cap', '', '$e', $d, ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión gerente de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}
							if ($clave_asesor !== "") 
							{
								$q_acum_pagos_otros = "SELECT * FROM carobra.acum_pagos_otros_$anio WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
								$dbacum_pagos_otro =  $conecta->consulta($q_acum_pagos_otros);
								$descuento = $com_asesor - $monto_total_ant;

								if ($dbacum_pagos_otro->num_rows !== 0) 
								{
									$qu_acum_pagos_otros = "UPDATE carobra.acum_pagos_otros_$anio SET ajuste = (ajuste + $descuento) 
															WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_pagos_otros);
								} 
								else 
								{
									$q_estructura = "SELECT DISTINCT es.sc, es.brocker, g.id_gerencia FROM carobra_2014.gerencias g
													INNER JOIN carobra_2014.usuarios_nivel_gerencia ung ON (g.id_gerencia = ung.id_gerencia)
													INNER JOIN carobra_2014.estructura_gerencia es ON (ung.id_gerencia = es.id_gerencia)
													WHERE ung.clave = '$clave_asesor' AND ung.pertenece = 1";
									$dbestruc = $conecta->consulta($q_estructura);
									$tbestruc = mysqli_fetch_array($dbestruc);
									$sc = $tbestruc['sc'];
									$brocker = $tbestruc['brocker'];
									$id_gerencia = $tbestruc['id_gerencia'];

									$qi_acum_pagos_otros = "INSERT INTO carobra.acum_pagos_otros_$anio 
																VALUES ('$sc', '$brocker', $id_gerencia, '$clave_asesor', '$fecha_pago_cer', $descuento, 0, 0, 0, 0, 0, 0)";
									$conecta->consulta($qi_acum_pagos_otros);
								}

								$q_acum_comisiones = "SELECT * FROM carobra.acum_pagos_comisiones_$anio WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer' AND com_asesor > 0";
								$dbacumcom = $conecta->consulta($q_acum_comisiones);
								if ($dbacumcom->num_rows !== 0) 
								{
									$qu_acum_comisiones = "UPDATE carobra.acum_pagos_comisiones_$anio SET com_asesor  = (com_asesor  - $descuento) WHERE clave = '$clave_asesor' AND fec_pago = '$fecha_pago_cer'";
									$conecta->consulta($qu_acum_comisiones);
								}

								$mansaje_cetificacion_a = "Certificiación asesor\nClave: $clave_asesor\nFec pag: $fecha_pago_cer\nAjuste: $descuento";
								
								$b = compl_ajuste(0, $clave_gerente);
								$c = $b[0]["unidad"];
								$d = $b[0]["id_gerencia"];
								$e = $b[0]["socio"];

								$tablaDestino = '';
								switch (strtoupper($c)) 
								{
									case 'PREVISIONAL':
										$tablaDestino = 'ajustes_previsional';
									break;
									case 'TARJETA':
										$tablaDestino = 'ajustes_tarjeta';
									break;
									case 'HIPOTECARIO':
										$tablaDestino = 'ajustes_hipotecario';
									break;
									case 'SEGUROS':
										$tablaDestino = 'ajustes_seguros';
									break;
									case 'CORPORATIVO':
										$tablaDestino = 'ajustes_corporativo';
									break;
									default:
										echo "Unidad de negocio no válida: $c";
									exit;
								}

								$qi_ajuste = "insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor', '$fecha_pago_cer', '$fecha_pago_cer', '$e', $d, $aniop, $mes, $descuento, 'Pago erróneo de certificación del folio $folio', 1, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_ajuste);

								$fec_cap = date("d") . "/" . date("m") . "/" . date("Y");
								$descuento = $descuento * -1;
								$qi_nuevo_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0,'','$clave_asesor', '$fec_cap', '', '$e', $d,  ".date('Y').",".date('m').", $descuento, 'Ajuste por pago erroneo de comisión asesor de certificación del $folio', 0, '', '', 0, 0, '', 'O')";
								$conecta->consulta($qi_nuevo_ajuste);
							}

						}
						echo "Datos de anticipo y cerficación actulizados con exito\n".$mansaje_primer_ant.$mansaje_segun_ant.$mansaje_cetificacion_sc.$mansaje_cetificacion_sb.$mansaje_cetificacion_g.$mansaje_cetificacion_a;
					}
				}

		break;
		case 84:
			$ajustes = json_decode($_POST['ajustes'], true);
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$values = array();
			$fec_carga = date('d/m/Y H:i');
			$a = compl_ajuste2(1);
			$gerencia = "";
			$id_gerencia = 0;
			$filas_fallidas = array();
		
			foreach ($ajustes as $ajuste) 
			{
				
				$indice = $ajuste['indice'];
				
				$empleado = $ajuste['clave'];
				$cen_costo = $ajuste['cen_costo'];
				$partida = $ajuste['partida'];
				$gerencia_llega = $ajuste['gerencia'];

				$fecha = $ajuste['fecha'];
				$socio_llega = $ajuste['socio'];
				$fec_act = $ajuste['fec_act'];
				
				$ajuste_valor =  $ajuste['ajuste_valor'];
				$motivo = mb_substr($ajuste['motivo'], 0, 250, 'UTF-8');
				
				$tipo_ajuste = $ajuste['tipo_ajuste'];
				$afecta_sb = $ajuste['afecta_sb'];
				$afecta_op = $ajuste['afecta_op'];

				$clave_validacion = "NO_VALIDO";
				$centro_costo_validacion = "NO_VALIDO";
				$partida_validacion = "NO_VALIDO";
				$ajuste_validacion = "NO_VALIDO";
				$fecha_validacion = "NO_VALIDO";
				$fecha_validacion_fec_act = "NO_VALIDO";
				$gerencia_validacion = "NO_VALIDO";
				$mes = 0;
				$anio = 0;
				
				$cen_costo = obtenerTextoEntreParentesis($cen_costo);
				$partida = obtenerPartida($partida);
				$unidad = "";
				$nombre = "";
				$clave = "";
				$resultado = procesarEmpleado($empleado);
				$clave = $resultado['clave'];
				$nombre = $resultado['nombre'];

				if (!empty($gerencia_llega)) 
				{
					if (preg_match('/\((\d+)\)/', $gerencia_llega, $coincidencias)) 
					{
						$id_gerencia = (int)$coincidencias[1];
						$gerencia = trim(preg_replace('/\(\d+\)/', '', $gerencia_llega));
					} 
					else 
					{
						$id_gerencia = obtener_gerencia_id($gerencia_llega);
						$gerencia = $gerencia_llega;
					}
				}
				else 
				{
					$b = compl_ajuste2(0, $clave);
					$id_gerencia = $b[0]["id_gerencia"];
					$gerencia = "";
				}

				if ($socio_llega !== "") 
				{
					$unidad = obtener_unidad($socio_llega, "");
				}
				else if ($socio_llega == "") 
				{
					$resultado = obtener_unidad("", $gerencia);
					$unidad = $resultado['unidad'];
					$socio_llega = $resultado['socio'];
				}
				

				$ajuste_limpio = str_replace(array('$', ',', ' '), '', $ajuste_valor);
				if (is_numeric($ajuste_limpio)) 
				{
					$ajuste_validacion = "VALIDO";
				} 
				else 
				{
					$ajuste_limpio = 0;
					$ajuste_validacion = "VALIDO";
				}

				if ($tipo_ajuste !== "") 
				{
					$tipo_ajuste = 0;
				} 
				else 
				{
					$tipo_ajuste = 0;
				}

				if ($afecta_sb !== "") 
				{
					$afecta_sb = 0;
				} 
				else
				{
					$afecta_sb = 0;
				}
				
				if ($afecta_op == "" or strtolower($afecta_op) == 'o') 
				{
					$afecta_op = "O";
				} 
				elseif (strtolower($afecta_op) == 'f') 
				{
					$afecta_op = "F";
				}

				$fecha_resultado = validarFecha($fecha);
				$fecha = $fecha_resultado['valor'];
				$fecha_validacion = $fecha_resultado['validacion'];

				$fec_act_resultado = validarFecha($fec_act);
				$fec_act = $fec_act_resultado['valor'];
				$fecha_validacion_fec_act = $fec_act_resultado['validacion'];

				if ($fecha_validacion_fec_act === "VALIDO") 
				{
					$anio = $fec_act_resultado['anio'];
					$mes = $fec_act_resultado['mes'];
				} 
				else 
				{
					$mes = 0;
					$anio = 0;
				}
				
				$values[] = "(0, '$indice', '$nombre', '$clave', '$fecha', $id_gerencia, '$gerencia', '$fec_act' ,$anio, $mes,  '$unidad', '$socio_llega', $ajuste_limpio,'$motivo', '$cen_costo', '$partida', 
																$tipo_ajuste, $afecta_sb, '$afecta_op','$clave_validacion', '$centro_costo_validacion', 
																'$partida_validacion', '$ajuste_validacion', '$fecha_validacion', '$fecha_validacion_fec_act', '$gerencia_validacion', '$usuario_cap', '$fec_carga')";
			}

			//print_r($values);
			if (!empty($values)) 
			{
				
				$qi_ajustes = "INSERT INTO carobra_2014.temp_ajustes VALUES " . implode(', ', $values);
				//echo $qi_ajustes;
				$conecta->consulta($qi_ajustes);
				//echo "Termino....";
			}

			$qvalida_clave = "UPDATE carobra_2014.temp_ajustes a,carobra.usuarios u
								set clave_validacion='VALIDO', empleado = nombre
								where a.clave = u.clave";
			$conecta->consulta($qvalida_clave);

			$qvalida_costo = "UPDATE carobra_2014.temp_ajustes ta
								LEFT JOIN carobra_2014.centros_costo_area cc 
								ON ta.cen_costo = cc.id_cc
								SET ta.centro_costo_validacion = 'VALIDO'
								WHERE ta.cen_costo = cc.id_cc OR ta.cen_costo = ''";
			$conecta->consulta($qvalida_costo);

			$qvalida_partida = "UPDATE carobra_2014.temp_ajustes ta
									LEFT JOIN carobra_2014.partidas_area_negocio pt 
									ON ta.partida = pt.no_partida
									SET ta.partida_validacion = 'VALIDO'
									WHERE ta.partida = pt.no_partida OR ta.partida = ''";
			$conecta->consulta($qvalida_partida);


			$qvalida_gerencia = "UPDATE carobra_2014.temp_ajustes ta
									LEFT JOIN carobra_2014.gerencias_negocio gn 
									ON ta.id_gerencia = gn.id_gerencia
									SET 
										ta.gerencia_validacion = CASE 
											WHEN gn.id_gerencia IS NOT NULL THEN 'VALIDO'
											ELSE 'NO_VALIDO'
										END,
										ta.gerencia = CASE 
											WHEN gn.id_gerencia IS NOT NULL THEN gn.gerencia
											ELSE ''
										END";
			$conecta->consulta($qvalida_gerencia);


			//$fecha_hora_actual = date('d/m/Y H');
			$q_temp_ajuste = "SELECT * 
								FROM carobra_2014.temp_ajustes 
								WHERE usuario_cap = '$usuario_cap'
                  				AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i')
								ORDER BY 
									(CASE WHEN clave_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN centro_costo_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN partida_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN ajuste_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN fecha_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN fec_act_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN gerencia_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC";
			
			$q_sum = "SELECT SUM(ajuste) AS total FROM temp_ajustes WHERE usuario_cap = '$usuario_cap'
						AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i')";
			$dbtotal = $conecta->consulta($q_sum);
			$tb = mysqli_fetch_array($dbtotal);
			$total = $tb['total'];

			$q_ajustes_err = "SELECT * 
								FROM carobra_2014.temp_ajustes 
								WHERE usuario_cap = '$usuario_cap'
								AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i')
								AND (clave_validacion = 'NO_VALIDO' OR 
									centro_costo_validacion = 'NO_VALIDO' OR
									partida_validacion = 'NO_VALIDO' OR 
									ajuste_validacion = 'NO_VALIDO' OR
									fecha_validacion = 'NO_VALIDO' OR
									fec_act_validacion = 'NO_VALIDO' OR
									gerencia_validacion = 'NO_VALIDO')";
			$dberr = $conecta->consulta($q_ajustes_err);
			if ($dberr->num_rows !== 0) 
			{
				while ($fila = $dberr->fetch_assoc()) 
				{
					$filas_fallidas[] = $fila;
				}
			}

									
			$dba = $conecta->consulta($q_temp_ajuste);
			if ($dba->num_rows !== 0) 
			{
    			$resultados = array();
				if ($dba->num_rows !== 0) {
					while ($fila = $dba->fetch_assoc()) 
					{
						$resultados[] = $fila;
					}
				}
			}

			$respuesta = array(
				'ajusts' => $resultados,
				'total' => $total,
				'errores' => $filas_fallidas
			);				
			echo json_encode($respuesta);
			
		break;
		case 85:
			$id = addslashes(trim($_POST["id_ajuste"]));
			$qd_temp_ajuste = "Delete FROM carobra_2014.temp_ajustes WHERE id_ajuste = $id";
			$conecta->consulta($qd_temp_ajuste);
			echo "Datos eliminados con exito";
		break;
		case 86:
			$id = addslashes(trim($_POST["id"]));
			$clave = addslashes(trim($_POST["clave"]));
			$fecha = addslashes(trim($_POST["fecha"]));
			$ajuste = addslashes(trim($_POST["ajuste"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$centro = addslashes(trim($_POST["centro"]));
			$partida = addslashes(trim($_POST["partida"]));
			$clave = obtenerTextoEntreParentesis($clave);


			if ($cen_costo !== "") 
			{
				if (preg_match('/\(([^)]+)\)/', $cen_costo, $matches)) 
				{
					$cen_costo = $matches[1];
				}
			}

			if ($partida !== "") 
			{
				if (preg_match('/\(([^)]+)\)/', $partida, $matches)) 
				{
					$partida = $matches[1];
				}
			}
			
			if (!empty($fecha)) 
			{
				$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
				
				if ($fecha_obj) 
				{
					$fecha = $fecha_obj->format('d/m/Y');
				} 
				else 
				{
					$fecha = '';
				}
			}

			$ajuste_limpio = str_replace(array('$', ',', ' ','(',')'), '', $ajuste);
			if (is_numeric($ajuste_limpio)) 
			{
				$ajuste = $ajuste_limpio;
			} 
			else 
			{
				$ajuste = 0;
			}

			$qu_ajuste_tempo = "Update carobra_2014.temp_ajustes 
								SET clave = '$clave', fecha = '$fecha', ajuste = $ajuste, 
									motivo = '$motivo', cen_costo = '$centro', 
									partida = '$partida', clave_validacion  = 'VALIDO',  
									centro_costo_validacion = 'VALIDO', 
									partida_validacion = 'VALIDO', 
									ajuste_validacion = 'VALIDO', fecha_validacion = 'VALIDO'
								WHERE id_ajuste = $id";
			$conecta->consulta($qu_ajuste_tempo);

			$response = array(
				'id' => $id,
				'clave' => $clave,
				'fecha' => $fecha,
				'ajuste' => $ajuste,
				'motivo' => $motivo,
				'centro' => $centro,
				'partida' => $partida,
			);
			header('Content-Type: application/json');
			echo json_encode($response);
		break;
		case 87:
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$qd_ajustes="Delete from carobra_2014.temp_ajustes 
						 WHERE usuario_cap = '$usuario_cap'";
			$conecta->consulta($qd_ajustes);
			echo "Datos eliminados con exito";
		break;
		case 88:
			$c= explode("|",addslashes(trim($_POST["carga"])));
			$clave=$c[0];
			$fec_carga=$c[1];

			/*$query="Insert into ajustes
					INSERT INTO ajustes_corporativo
					Select 0,'$clave',clave, fecha,'', id_gerencia, anio, mes, ajuste,motivo,0,cen_costo,partida,
					tipo_ajuste,afecta_sb,'',afecta_op 
					from temp_ajustes 
					where usuario_cap='$clave' and fec_carga='$fec_carga'";*/
			// Consulta para ajustes corporativo
			$query_corporativo = "
				INSERT INTO carobra_2014.ajustes_corporativo
				SELECT 0, '$clave', clave, fecha, '', socio, id_gerencia, anio, mes, ajuste, motivo, 0, cen_costo, partida, 
					tipo_ajuste, afecta_sb, '', afecta_op
				FROM temp_ajustes 
				WHERE usuario_cap = '$clave' AND fec_carga = '$fec_carga' AND unidad_negocio = 'CORPORATIVO'
				AND clave_validacion = 'VALIDO' AND 
				centro_costo_validacion = 'VALIDO' AND
				partida_validacion = 'VALIDO' AND 
				ajuste_validacion = 'VALIDO' AND
				fecha_validacion = 'VALIDO' AND
				fec_act_validacion = 'VALIDO' AND
				gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query_corporativo);

			// Consulta para ajustes hipotecario
			$query_hipotecario = "
				INSERT INTO carobra_2014.ajustes_hipotecario
				SELECT 0, '$clave', clave, fecha, '', socio, id_gerencia, anio, mes, ajuste, motivo, 0, cen_costo, partida, 
					tipo_ajuste, afecta_sb, '', afecta_op
				FROM temp_ajustes 
				WHERE usuario_cap = '$clave' AND fec_carga = '$fec_carga' AND unidad_negocio = 'HIPOTECARIO'
				AND clave_validacion = 'VALIDO' AND 
				centro_costo_validacion = 'VALIDO' AND
				partida_validacion = 'VALIDO' AND 
				ajuste_validacion = 'VALIDO' AND
				fecha_validacion = 'VALIDO' AND
				fec_act_validacion = 'VALIDO' AND
				gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query_hipotecario);

			// Consulta para ajustes previsional
			$query_previsional = "
				INSERT INTO carobra_2014.ajustes_previsional
				SELECT 0, '$clave', clave, fecha, '', socio, id_gerencia, anio, mes, ajuste, motivo, 0, cen_costo, partida, 
					tipo_ajuste, afecta_sb, '', afecta_op
				FROM temp_ajustes 
				WHERE usuario_cap = '$clave' AND fec_carga = '$fec_carga' AND unidad_negocio = 'PREVISIONAL'
				AND clave_validacion = 'VALIDO' AND 
				centro_costo_validacion = 'VALIDO' AND
				partida_validacion = 'VALIDO' AND 
				ajuste_validacion = 'VALIDO' AND
				fecha_validacion = 'VALIDO' AND
				fec_act_validacion = 'VALIDO' AND
				gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query_previsional);

			// Consulta para ajustes seguros
			$query_seguros = "
				INSERT INTO carobra_2014.ajustes_seguros
				SELECT 0, '$clave', clave, fecha, '', socio, id_gerencia, anio, mes, ajuste, motivo, 0, cen_costo, partida, 
					tipo_ajuste, afecta_sb, '', afecta_op
				FROM temp_ajustes 
				WHERE usuario_cap = '$clave' AND fec_carga = '$fec_carga' AND unidad_negocio = 'SEGUROS'
				AND clave_validacion = 'VALIDO' AND 
				centro_costo_validacion = 'VALIDO' AND
				partida_validacion = 'VALIDO' AND 
				ajuste_validacion = 'VALIDO' AND
				fecha_validacion = 'VALIDO' AND
				fec_act_validacion = 'VALIDO' AND
				gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query_seguros);

			// Consulta para ajustes tarjeta
			$query_tarjeta = "
				INSERT INTO carobra_2014.ajustes_tarjeta
				SELECT 0, '$clave', clave, fecha, '', socio, id_gerencia, anio, mes, ajuste, motivo, 0, cen_costo, partida, 
					tipo_ajuste, afecta_sb, '', afecta_op
				FROM temp_ajustes 
				WHERE usuario_cap = '$clave' AND fec_carga = '$fec_carga' AND unidad_negocio = 'TARJETA'
				AND clave_validacion = 'VALIDO' AND 
				centro_costo_validacion = 'VALIDO' AND
				partida_validacion = 'VALIDO' AND 
				ajuste_validacion = 'VALIDO' AND
				fecha_validacion = 'VALIDO' AND
				fec_act_validacion = 'VALIDO' AND
				gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query_tarjeta);	
			usleep(50000);
			$query="Delete from temp_ajustes 
					where usuario_cap='$clave' and fec_carga='$fec_carga' 
					AND clave_validacion = 'VALIDO' AND 
					centro_costo_validacion = 'VALIDO' AND
					partida_validacion = 'VALIDO' AND 
					ajuste_validacion = 'VALIDO' AND
					fecha_validacion = 'VALIDO' AND
					fec_act_validacion = 'VALIDO' AND
					gerencia_validacion = 'VALIDO'";
			$conecta->consulta($query);
			echo "Los ajustes ya estan registrados para pagos";
		break;
		case 89:
			$chk_renovacion="";
			$mes = addslashes(trim($_POST["sel-mes"]));
			$anio = addslashes(trim($_POST["sel-anio"]));
			$id_cliente = addslashes(trim($_POST["id-cliente"]));
			$pago_renovacion=addslashes(trim($_POST["txt-pago-ren"]));
			$id_renovacion = addslashes(trim($_POST["id-renovacion"]));
			$chk_renovacion = addslashes(trim($_POST["chk-renovacion"]));
			$no_renovacion = addslashes(trim($_POST["sel-meses-renovar"]));

			$pago_renovacion=str_replace(['$', ','], '', $pago_renovacion);
			$renovacion=0;
			if($chk_renovacion=="on")
				$renovacion=1;

			if($id_renovacion > 0)
			{
            	$query="Delete from plan_ideal.renovaciones where id_renovacion=$id_renovacion";
				$conecta->consulta($query);
			}
			else
			{
				if($renovacion==1)
				{
	            	$query="Select ifnull(max(id_renovacion),0) + 1 as id 
	            			from plan_ideal.renovaciones";
					$bd=$conecta->consulta($query);
					$tb=mysqli_fetch_array($bd);
					$id_renovacion=$tb["id"];
				}
			}
			if($id_renovacion > 0)
			{
	            for ($i = 1; $i <= $no_renovacion; $i++) 
	            {
					$query="Insert into plan_ideal.renovaciones values($id_renovacion,$id_cliente,'" . date("d/m/Y") . "',$i,$anio,$mes,1,$pago_renovacion)";
					$conecta->consulta($query);
					$mes++;
					if($mes > 12)
					{
						$mes=1;
						$anio++;
					}
					$pago_renovacion=0;
	            }
			}
            echo "Información guardada con éxito";
		break;
		case 90:
			$ajustes_temp = json_decode($_POST['ajustes'], true);
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			
			$fecha_cap = $ajustes_temp[0]['fecha'];
			/*list($fecha, $hora_completa) = explode(' ', $fecha_cap);
			$hora = explode(':', $hora_completa)[0];
			$fecha_cap = $fecha . ' ' . $hora;*/

			foreach ($ajustes_temp as $ajuste) 
			{
				$id = $ajuste['id'];
				$qd_temp_ajuste = "Delete FROM carobra_2014.temp_ajustes WHERE id_ajuste = $id";
				$conecta->consulta($qd_temp_ajuste);
			}

			$q_temp_ajuste = "SELECT * 
								FROM carobra_2014.temp_ajustes 
								WHERE usuario_cap = '$usuario_cap'
                  				AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_cap', '%d/%m/%Y %H:%i')
								ORDER BY 
									(CASE WHEN clave_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN centro_costo_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN partida_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN ajuste_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN fecha_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC";
			
			$q_sum = "SELECT SUM(ajuste) AS total FROM temp_ajustes WHERE usuario_cap = '$usuario_cap'
						AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_cap', '%d/%m/%Y %H:%i')";
			$dbtotal = $conecta->consulta($q_sum);
			$tb = mysqli_fetch_array($dbtotal);
			$total = $tb['total'];
									
			$dba = $conecta->consulta($q_temp_ajuste);
			$registros = array();
			while ($row = mysqli_fetch_assoc($dba)) 
			{
				$registros[] = $row;
			}

			$respuesta = [
				'mensaje' => 'Datos eliminados con éxito',
				'registros' => $registros,
				'total' => $total
			];
			header('Content-Type: application/json');
			echo json_encode($respuesta);
		break;
		case 91:
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$clave = addslashes(trim($_POST["clave"]));
			$ajuste = addslashes(trim($_POST["ajuste"]));
			
			$fecha_captura = addslashes(trim($_POST["fecha_captura"]));
			/*list($fecha, $hora_completa) = explode(' ', $fecha_captura);
			$hora = explode(':', $hora_completa)[0];
			$fecha_captura = $fecha . ' ' . $hora;*/

			$tipo = addslashes(trim($_POST["tipo"]));
			$operador = addslashes(trim($_POST["operador"]));


			$qw_query = "";

			if ($tipo == 'CLAVE') 
			{
				if (preg_match('/\(([^)]+)\)/', $clave, $matches)) 
				{
					$clave = trim($matches[1]);
				}

				$qw_query = "AND clave = '$clave'";
			}
			if ($tipo == 'AJUSTE') 
			{
				$ajuste_limpio = str_replace(['$', ','], '', $ajuste);

				if ($operador !== "") 
				{
					$qw_query = "AND ajuste $operador $ajuste_limpio";
				}
				else 
				{
					$qw_query = "AND ajuste = $ajuste_limpio";

				}
			}
			if ($tipo == 'MOTIVO') 
			{
				$qw_query = "AND motivo LIKE '%$motivo%'";
			}
			
			if ($tipo == '') 
			{
				$qw_query = "";
			}

			$q_temp_ajuste = "SELECT * 
								FROM carobra_2014.temp_ajustes 
								WHERE usuario_cap = '$usuario_cap'
                  				AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_captura', '%d/%m/%Y %H:%i')
								$qw_query
								ORDER BY 
									(CASE WHEN clave_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN centro_costo_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN partida_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN ajuste_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN fecha_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC";
			$q_sum = "SELECT SUM(ajuste) AS total FROM temp_ajustes WHERE usuario_cap = '$usuario_cap'
						AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_captura', '%d/%m/%Y %H:%i')
						$qw_query";
			$dbtotal = $conecta->consulta($q_sum);
			$tb = mysqli_fetch_array($dbtotal);
			$total = $tb['total'];
									
			$dba = $conecta->consulta($q_temp_ajuste);
			$registros = array();
			while ($row = mysqli_fetch_assoc($dba)) 
			{
				$registros[] = $row;
			}

			$respuesta = [
				'mensaje' => 'Datos filtrados con éxito',
				'registros' => $registros,
				'total' => $total
			];
			header('Content-Type: application/json');
			echo json_encode($respuesta);
		break;
		case 92:
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$clave = addslashes(trim($_POST["clave"]));
			$ajuste = addslashes(trim($_POST["ajuste"]));
			
			$fecha_captura = addslashes(trim($_POST["fecha_captura"]));
			$tipo = addslashes(trim($_POST["tipo"]));
			$operador = addslashes(trim($_POST["operador"]));
			$qw_query = "";

			if ($tipo == 'CLAVE') 
			{
				if (preg_match('/\(([^)]+)\)/', $clave, $matches)) 
				{
					$clave = trim($matches[1]);
				}

				$qw_query = "AND clave = '$clave'";
			}
			if ($tipo == 'AJUSTE') 
			{
				$ajuste_limpio = str_replace(['$', ','], '', $ajuste);
				
				if ($operador !== "") 
				{
					$qw_query = "AND ajuste $operador $ajuste_limpio";
				}
				else 
				{
					$qw_query = "AND ajuste = $ajuste_limpio";

				}
			}
			if ($tipo == 'MOTIVO') 
			{
				$qw_query = "AND motivo LIKE '%$motivo%'";
			}
			
			if ($tipo == '') 
			{
				$qw_query = "";
			}

			$query="Select sum(ajuste) as total from temp_ajustes 
									where usuario_cap='$usuario_cap' and fec_carga='$fecha_captura' $qw_query";
							$bd=$conecta->consulta($query);
							$tb=mysqli_fetch_array($bd);
							$total=$tb["total"];
			$query="Select * from temp_ajustes 
				where usuario_cap='$usuario_cap' and fec_carga='$fecha_captura' $qw_query";

?>
			<div class="table-responsive" id="cont-tabla">
				<table class="table table-striped table-sm" id="t-ajustes" name="t-ajustes">
					<thead>
						<tr>
							<th class="fuente-encabezado" style="width: 50px;"></th>
							<th style="width: 50px;">Indice</th>
							<th style="width: 100px;">Clave <input type="checkbox" class="header-checkbox" value="CLAVE"></th>
							<th style="width: 100px;">Fecha</th>
							<th style="width: 100px;">unidad_negocio</th>
							<th style="width: 100px;">socio_negocio</th>
							<th style="width: 100px;">Fec. activación</th>
							<th style="width: 100px;">Ajuste <input type="checkbox" class="header-checkbox" value="AJUSTE"></th>
							<th style="width: 300px;">Motivo <input type="checkbox" class="header-checkbox" value="MOTIVO"></th>
							<th style="width: 100px;">Cen_Costo</th>
							<th style="width: 100px;">Partida</th>
							<th style="width: 50px;">Tipo Ajuste</th>
							<th style="width: 50px;">afecta_sb</th>
							<th style="width: 50px;">afecta_op</th>
							<th style="width: 60px;"></th>
						</tr>
					</thead>
					<tbody id="contenido-tabla">
<?php
							$bd=$conecta->consulta($query);
							if ($bd->num_rows != 0)
							{
								$i=1;
								while($tb=mysqli_fetch_array($bd))
								{
									$color="blue";
									if($tb["ajuste"] < 0)
										$color="red";
?>
									<tr data-id=<?php echo $tb["id_ajuste"]; ?>>
										<td><input type="checkbox" class="select-ajuste" data-id="<?php echo $tb["id_ajuste"]; ?>" data-fecha="<?php echo $tb["fec_carga"]; ?>"></td>
										<td style="text-align: right"><?php echo $i; ?></td>
										<td><?php echo $tb["clave"]; ?></td>
										<td><?php echo $tb["fecha"]; ?></td>
										<td><?php echo $tb["unidad_negocio"]; ?></td>
										<td><?php echo $tb["socio"]; ?></td>
										<td><?php echo $tb["fec_act"]; ?></td>
										<td class="moneda" style="color: <?php echo $color; ?>">
											<?php echo $tb["ajuste"]; ?>
										</td>
										<td><?php echo $tb["motivo"]; ?></td>
										<td><?php echo $tb["cen_costo"]; ?></td>
										<td><?php echo $tb["partida"]; ?></td>
										<td><?php echo $tb["tipo_ajuste"]; ?></td>
										<td><?php echo $tb["afecta_sb"]; ?></td>
										<td><?php echo $tb["afecta_op"]; ?></td>
										<td class="td-icon">
											<div class="btn-group" role="group">
												<i class="bi bi-pencil-square btn-correct mr-2" title="Corregir" data-id=<?php echo $tb["id_ajuste"]; ?>></i>
												<i class="bi bi-x-square btn-delete" title="Eliminar" data-id=<?php echo $tb["id_ajuste"]; ?>></i>
											</div>
										</td>
									</tr>
<?php
									$i++;
								}											
							}
?>
					</tbody>
				</table>
			</div>
<?php
			$color="blue";
			if($total < 0)
				$color="red";
?>
			<div class='row mt-3' id="cont-botones">
				<div class="col-6 text-right mr-0 pr-1">
					<label class="total">Monto total de los ajustes: <strong style="font-size: 1.1rem; color: <?php echo $color; ?>"><?php echo "$" . number_format($total, 2, '.', ',');; ?></strong></label>
				</div>
				<div class="col-3 text-left mr-0 pr-1">
					<a class="btn btn-carobra btn-sm btn-block p-1 mr-2" id="btn-eliminar" style="background: #9B0B10;">
					<span>Eliminar Seleccionados</span>
					</a>
				</div>
				<div class="col-3 text-left mr-0 pr-3">
					<a class="btn btn-carobra btn-sm btn-block active p-1" id="btn-autoriza">
						<span>Autorizar Ajustes</span>
					</a>
				</div>	
			</div>
			<script>
				$('body').on('focusout', '.moneda',function()
				{
					$(this).formatCurrency();
				});

				autocompletar('txt-clave-ajuste', 4, 'usuario_clave_nombre', $("txt-clave-ajuste").data('n') + "|" + $("txt-clave-ajuste").data('ne'));

				autocompletar('txt-centro-ajuste', 4, 'centro_costo_nombre', $("txt-centro-ajuste").data('n') + "|" + $("txt-centro-ajuste").data('ne'));
			
				autocompletar('txt-partida-ajuste', 4, 'nopartida_partida', $("txt-partida-ajuste").data('n') + "|" + $("txt-partida-ajuste").data('ne'));

				autocompletar('txt-clave-ajuste-tempo', 4, 'usuario_clave_nombre', $("txt-clave-ajuste-tempo").data('n') + "|" + $("txt-clave-ajuste-tempo").data('ne'));

				autocompletar('txt-motivo-tempo', 4, 'temp_ajuste_motivo', $("txt-motivo-tempo").data('n') + "|" + $("txt-motivo-tempo").data('ne'));

				$("#sel-carga").change(function()
				{
					$("#frm-envio").submit();
				});

				$('.btn-correct').on('click', function(e) 
				{
					let id=$(this).data("id");
					let tr=$("tr[data-id=" + id + "] ");
					let fechaTexto = tr.find('td:eq(3)').text();
					let partesFecha = fechaTexto.split('/');
					let fechaFormatoDate = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
					$('#txt-id-ajuste').val(id);
					$('#txt-clave-ajuste').val(tr.find('td:eq(2)').text());
					$('#txt-fecha-ajuste').val(fechaFormatoDate);
					$('#txt-ajuste').val(tr.find('td:eq(7)').text());
					$('#txt-motivo-ajuste').val(tr.find('td:eq(8)').text());
					$('#txt-centro-ajuste').val(tr.find('td:eq(9)').text());
					$('#txt-partida-ajuste').val(tr.find('td:eq(10)').text());

					$('#modal-ajuste-temp').modal('show');
					$(".moneda").formatCurrency();
				});

				$('#btn-aceptar-ajuste').on('click', function(e) 
				{
					let id = $('#txt-id-ajuste').val();
					let clave = $('#txt-clave-ajuste').val();
					let fecha = $('#txt-fecha-ajuste').val();
					let ajuste = $('#txt-ajuste').val();
					let motivo = $('#txt-motivo-ajuste').val();
					let centro = $('#txt-centro-ajuste').val();
					let partida = $('#txt-partida-ajuste').val();

					if (clave == "") 
					{
						alert("Por favor completa el campo clave");
						$('#txt-clave-ajuste').focus();
						return;
					}
					if (fecha == "") 
					{
						alert("Por favor completa el campo fecha");
						$('#txt-fecha-ajuste').focus();
						return;
					}
					if (ajuste == "") 
					{
						alert("Por favor completa el campo ajuste");
						$('#txt-ajuste').focus();
						return;
					}
					else
					{
						if(ajuste.substring(0,1)=="(")
							ajuste=ajuste.replace(/[$(),]/g,'') * -1;
					}

					if (motivo == "") 
					{
						alert("Por favor completa el campo motivo");
						$('#txt-motivo-ajuste').focus()
						return;
					}

					formData = {
						'id': id,
						'clave': clave,
						'fecha': fecha,
						'ajuste': ajuste,
						'motivo': motivo,
						'centro': centro,
						'partida': partida,
						'op': 86
					};

					$.ajax({
						type: "POST",
						url: "./ajax/facilitador.php",
						data: formData,
						success: function(response) 
						{
							alert("Datos actulizados con exito");
							let row=$("tr[data-id=" + id + "] ");
							
							row.removeClass('highlight-row');
							row.find('td:eq(2)').removeClass('highlight-cell');
							row.find('td:eq(3)').removeClass('highlight-cell');
							row.find('td:eq(7)').removeClass('highlight-cell');
							row.find('td:eq(8)').removeClass('highlight-cell');
							row.find('td:eq(9)').removeClass('highlight-cell');
							row.find('td:eq(10)').removeClass('highlight-cell');

							row.find('td:eq(2)').text(response.clave);
							row.find('td:eq(3)').text(response.fecha);
							row.find('td:eq(7)').text(response.ajuste); 
							row.find('td:eq(8)').text(response.motivo);
							row.find('td:eq(9)').text(response.centro);
							row.find('td:eq(10)').text(response.partida);

							if((response.ajuste * 1) < 0)
								row.find('th:eq(0)').css("color","red"); 

							$(".moneda").formatCurrency();
							$(".moneda").css("text-align","right");
							$('#modal-ajuste-temp').modal('hide');

							$("#frm-envio").submit();
						}
					});
				});

				$('.btn-delete').on('click', function(e) 
				{
					let id=$(this).data("id");
					let tr=$("tr[data-id=" + id + "] ");
					let n=tr.find('td:eq(0)').text();

					if(confirm("¿Confirmas que quieres eliminar el ajuste número " + n + "?"))
					{
						formData = 
						{
							'id_ajuste': id,
							'op': 85
						};

						$.ajax({
							type: "POST",
							url: "./ajax/facilitador.php",
							data: formData,
								success: function(response) 
								{
									alert(response);
									$("#frm-envio").submit();
								}
						});					
					}
				});

				$("#btn-autoriza").click(function()
				{
					formData = 
					{
						'carga': $("#sel-carga").val(),
						'op': 88
					};

					$.ajax({
						type: "POST",
						url: "./ajax/facilitador.php",
						data: formData,
							success: function(response) 
							{
								alert(response);
								$("#frm-envio").submit();
							}
					});									
				});

				$('#activa, #desactiva').click(function(evento)
				{
					if($(this).attr("id")=="activa")
					{
						$("input[type=checkbox].select-ajuste").each(function() 
						{
							$(this).attr("checked","checked"); 
						});
						}
					else
					{
						$("input[type=checkbox].select-ajuste").each(function()
						{
							$(this).removeAttr("checked");
						});
					}
				});

				$('#btn-eliminar').on('click', function(e)
				{
					e.preventDefault();
					let selectedData = [];
					
					$('.select-ajuste:checked').each(function() 
					{
						let id = $(this).data('id');
						let fecha = $(this).data('fecha');
				
						selectedData.push({ id: id, fecha: fecha });
					});

					if (selectedData.length > 0) 
					{
						let usuario_cap = "<?php echo $clav; ?>";
						let formData =
						{
							'usuario_cap': usuario_cap,
							'ajustes': JSON.stringify(selectedData),
							'op': 90
						};
						if (confirm("¿Confirmas que quieres eliminar los ajustes seleccionados?")) 
						{
							$.ajax({
							type: "POST",
							url: "./ajax/facilitador.php",
							data: formData,
								success: function(response) 
								{
									alert(response.mensaje);
									$("#frm-envio").submit();
								}
							});
						}
					}
					else 
					{
						alert('Por favor, seleccione al menos un registro para eliminar.');
					}
				});

				$('input[type=checkbox].header-checkbox').on('change', function() 
				{
					let selectedValue = $(this).val();
						
					if ($(this).prop('checked')) 
					{
						$(this).prop('checked', true);

						$('#modal-ajuste-filtro').modal('show');
					} 
					else 
					{
						$('#modal-ajuste-filtro').modal('hide');
					}

					if (selectedValue === 'CLAVE') 
					{
						$('input.header-checkbox[value="AJUSTE"]').prop('checked', false);
						$('input.header-checkbox[value="MOTIVO"]').prop('checked', false);
						$('#filtro-clave').css('display','block');
						$('#filtro-ajuste').css('display','none');
						$('#filtro-motivo').css('display','none');
						$('#filtro-ajuste-operador').css('display','none');

					} 
					else if (selectedValue === 'AJUSTE') 
					{
						$('input.header-checkbox[value="CLAVE"]').prop('checked', false);
						$('input.header-checkbox[value="MOTIVO"]').prop('checked', false);
						$('#filtro-clave').css('display','none');
						$('#filtro-ajuste').css('display','block');
						$('#filtro-ajuste-operador').css('display','block');
						$('#filtro-motivo').css('display','none');
					} 
					else if (selectedValue === 'MOTIVO') 
					{
						$('input.header-checkbox[value="CLAVE"]').prop('checked', false);
						$('input.header-checkbox[value="AJUSTE"]').prop('checked', false);
						$('#filtro-clave').css('display','none');
						$('#filtro-ajuste').css('display','none');
						$('#filtro-motivo').css('display','block');
						$('#filtro-ajuste-operador').css('display','none');
					}
				});

				$('#btn-cancelar-filtrar').on('click', function(e) 
				{
					$('.header-checkbox').prop('checked', false);
					$('#txt-clave-ajuste-tempo').val('');
					$('#txt-motivo-tempo').val('');
					$('#txt-cantidad-tempo').val('');
					$('#filtro-ajuste-operador').val('');
					$('#modal-ajuste-filtro').modal('hide');
				});

				$('#btn-aceptar-filtrar').on('click', function(e) 
				{
					e.preventDefault();
					let checkedCheckbox = $('input[type=checkbox].header-checkbox:checked');
					if (checkedCheckbox.length > 0) 
					{
						let selectedValue = checkedCheckbox.val();
						let valid = false;
						let tipo = "";
						if (selectedValue == 'CLAVE') 
						{
							if ($('#txt-clave-ajuste-tempo').val() != "") 
							{
								valid = true;
								tipo = 'CLAVE';
							} 
							else 
							{
								valid = true;
								tipo = '';
							}
						} 
						else if (selectedValue == 'AJUSTE') 
						{
							if ($('#txt-cantidad-tempo').val() != "") 
							{
								valid = true;
								tipo = 'AJUSTE';
							} else 
							{
								valid = true;
								tipo = '';
							}
						} 
						else if (selectedValue == 'MOTIVO') 
						{
							if ($('#txt-motivo-tempo').val() != "") 
							{
								valid = true;
								tipo = 'MOTIVO';
							} 
							else 
							{
								valid = true;
								tipo = '';
							}
						}

						if (valid) 
						{
							let formData = 
							{
								'op': 92,
								'motivo': $('#txt-motivo-tempo').val(),
								'clave': $('#txt-clave-ajuste-tempo').val(),
								'ajuste': $('#txt-cantidad-tempo').val(),
								'usuario_cap': $('#txt-usuario-captura').val(),
								'fecha_captura': $('#txt-fecha-captura').val(),
								'tipo': tipo,
								'operador':  $('#sel-operador').val()
							};
							console.log(formData);

							$('#cont-principal').html('');

							$.ajax({
								type: "POST",
								url: "./ajax/facilitador.php",
								data: formData,
								success: function(response) 
								{
									console.log(response);
									$('#cont-principal').html(response);
									$('.header-checkbox').prop('checked', false);
									$('#txt-clave-ajuste-tempo').val('');
									$('#txt-motivo-tempo').val('');
									$('#txt-cantidad-tempo').val('');
									$('#filtro-ajuste-operador').val('');
									$('#modal-ajuste-filtro').modal('hide');
								} 
							});
						}
					}
				});
			</script>
<?php
		break;
		case 93:
			// Obtener los datos del formulario
			$comisionista = addslashes(trim($_POST["comisionista"]));
			if (preg_match('/\(([^)]+)\)/', $comisionista, $matches)) 
			{
				$comisionista = trim($matches[1]);
			}
			$sueldo = addslashes(trim(str_replace(",", "", $_POST["sueldo"])));
			$no_activaciones = addslashes(trim($_POST["no_activaciones"]));
			//$activo = isset($_POST["activo"]) ? 1 : 0;  // Verificar si el checkbox está marcado
			$activo = addslashes(trim($_POST["activo"]));
			
			// Verificar si el comisionista ya existe
			$query_check = "Select ct_empleado FROM sueldo_dp WHERE ct_empleado = '$comisionista'";
			$dbcheck = $conecta->consulta($query_check);
		
			if ($dbcheck->num_rows !== 0) 
			{
				// Si existe, hacemos una actualización
				$sueldo=str_replace(",","",str_replace("$","",$sueldo));
				$query_update ="Update sueldo_dp 
								SET sueldo = $sueldo, no_activaciones = $no_activaciones, 
								 	activo = $activo 
								 WHERE ct_empleado = '$comisionista'";
				$conecta->consulta($query_update);
				echo "Registro actualizado correctamente.";
			} 
			else 
			{
				$sueldo=str_replace(",","",str_replace("$","",$sueldo));
				$query_insert = "Insert INTO sueldo_dp VALUES ('$comisionista',$sueldo,$no_activaciones, $activo)";
				$conecta->consulta($query_insert);
				echo  "Registro insertado correctamente.";
			}
		break;
		case 94: 
			// Obtener el comisionista del formulario
			$comisionista = addslashes(trim($_POST['comisionista']));
			if (preg_match('/\(([^)]+)\)/', $comisionista, $matches)) 
				$comisionista = trim($matches[1]);
	
			$query="Select sueldo, no_activaciones, activo FROM sueldo_dp 
					WHERE ct_empleado = '$comisionista'";
			$result = $conecta->consulta($query);
		
			// Si existe el registro, retornamos los datos
			if ($result->num_rows > 0) 
			{
				$tb = mysqli_fetch_array($result);
				$data = array(
					'sueldo' => $tb['sueldo'],
					'no_activaciones' => $tb['no_activaciones'],
					'activo' => $tb['activo']
				);
				echo json_encode($data); // Enviamos los datos al cliente
			} 
			else
				echo json_encode(array('error' => 'No hay registros'));
		break;
		case 95:
			$anio=addslashes(trim($_POST["anio"]));

			$cf="";
			$nbd="ajustes";
			if($anio < date("Y")-1)
				$nbd="carobra_respaldos.ajustes_$anio";
			$query="Select distinct fec_pago from $nbd where substring(fec_pago,-4)=$anio 
					order by str_to_date(fec_pago,'%d/%m/%Y')";
			$bd=$conecta->consulta($query);

			while($tb = mysqli_fetch_array($bd))
			{
				if($cf=="")
					$cf=$tb["fec_pago"];
				else
					$cf.="|" . $tb["fec_pago"];
			}
			echo $cf;
		break;
		case 96:
			$nombre = addslashes(trim($_POST["nombre"]));
			$folio = addslashes(trim($_POST["folio"]));

			$q_update ="Update prod_trabajador SET trabajador = '$nombre' 
						WHERE folio = '$folio'";
			$conecta->consulta($q_update);
			echo "Datos actulizados con exito";
		break;			
		case 97:
			$saldo = addslashes(trim($_POST["saldo"]));
			$id_pago = addslashes(trim($_POST["id-pago"]));
			$no_agrega = addslashes(trim($_POST["no-agrega"]));				
			$nuevo_pago = addslashes(trim($_POST["nuevo-pago"]));
			$id_cliente = addslashes(trim($_POST["id-cliente"]));
			$formaPago = addslashes(trim($_POST["sel-formaPago"]));
			$fec_pago = addslashes(trim($_POST["txt-fec-pago"]));
			$fec_liquida = addslashes(trim($_POST["txt-fec-liquida"]));
			$banco = addslashes(trim($_POST["sel-banco"]));

			$monto_cliente = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-cliente"]))));

			$mes_ini = addslashes(trim($_POST["sel-mes-ini"]));
			$monto = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago"]))));

			$mes_ini_1 = addslashes(trim($_POST["sel-mes-ini-1"]));
			$monto_1 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-1"]))));
			$mes_ini_2 = addslashes(trim($_POST["sel-mes-ini-2"]));
			$monto_2 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-2"]))));
			$mes_ini_3 = addslashes(trim($_POST["sel-mes-ini-3"]));
			$monto_3 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-3"]))));
			$mes_ini_4 = addslashes(trim($_POST["sel-mes-ini-4"]));
			$monto_4 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-4"]))));
			$mes_ini_5 = addslashes(trim($_POST["sel-mes-ini-5"]));
			$monto_5 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-5"]))));
			$mes_ini_6 = addslashes(trim($_POST["sel-mes-ini-6"]));
			$monto_6 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-6"]))));
			$mes_ini_7 = addslashes(trim($_POST["sel-mes-ini-7"]));
			$monto_7 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-7"]))));
			$mes_ini_8 = addslashes(trim($_POST["sel-mes-ini-8"]));
			$monto_8 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-8"]))));
			$mes_ini_9 = addslashes(trim($_POST["sel-mes-ini-9"]));
			$monto_9 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-9"]))));
			$mes_ini_10 = addslashes(trim($_POST["sel-mes-ini-10"]));
			$monto_10 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-10"]))));
			$mes_ini_11 = addslashes(trim($_POST["sel-mes-ini-11"]));
			$monto_11 = str_replace(",","",str_replace("$","",addslashes(trim($_POST["txt-pago-11"]))));

			$fec_captura=date("d/m/Y");
			if($nuevo_pago==0)
				$conecta->consulta("Delete from plan_ideal.pagos where id_pago=$id_pago");

			$query="Select ifnull(max(id_pago),0) + 1 as id_pago from plan_ideal.pagos";
			$bd=$conecta->consulta($query);
			$tb = mysqli_fetch_array($bd);
			$id_pago=$tb["id_pago"];

			$i=0;
			$t=0;
			while($i <= $no_agrega)
			{
				$no_pago=1;

				switch ($i) 
				{
					case 0: 
						$mi=$mes_ini;
						$m=$monto;
					break;
					case 1: 
						$mi=$mes_ini_1;
						$m=$monto_1;
					break;
					case 2: 
						$mi=$mes_ini_2;
						$m=$monto_2;
					break;
					case 3: 
						$mi=$mes_ini_3;
						$m=$monto_3;
					break;
					case 4: 
						$mi=$mes_ini_4;
						$m=$monto_4;
					break;
					case 5: 
						$mi=$mes_ini_5;
						$m=$monto_5;
					break;
					case 6: 
						$mi=$mes_ini_6;
						$m=$monto_6;
					break;
					case 7: 
						$mi=$mes_ini_7;
						$m=$monto_7;
					break;
					case 8: 
						$mi=$mes_ini_8;
						$m=$monto_8;
					break;
					case 9: 
						$mi=$mes_ini_9;
						$m=$monto_9;
					break;
					case 10: 
						$mi=$mes_ini_10;
						$m=$monto_10;
					break;
					case 11: 
						$mi=$mes_ini_11;
						$m=$monto_11;
					break;
				}

				$t+=$m;
				$fec=explode(" ",$mi);
				$mes=mes($fec[0]);
				$anio=$fec[1];

				if($anio=='')
					$anio=date("Y");

				switch ($formaPago) 
				{
					case 1: $tipo_pago="MENSUAL";break;
					case 2: $tipo_pago="BIMESTRAL";break;
					case 6: $tipo_pago="SEMESTRAL";break;
					case 12: $tipo_pago="ANUAL";break;
				}

				while($no_pago <= $formaPago)
				{
					$query="Insert into plan_ideal.pagos values($id_pago,'$fec_captura',$no_pago,$id_cliente,$anio,$mes,'$fec_pago',$monto_cliente,$banco,'$fec_liquida','$tipo_pago',$m,0,0)";	
					$conecta->consulta($query);
					$mes++;
					if($mes > 12)
					{
						$mes=1;
						$anio++;
					}
					$m=0;
					$monto_cliente=0;
					$no_pago++;
				}
				$i++;
			}
			if($nuevo_pago==1)
			{
				$saldo=$monto_cliente-$t;

				$query="Update plan_ideal.clientes set saldo_cliente=$saldo 
						where id_cliente=$id_cliente";
				$conecta->consulta($query);
			}
			echo "Pago guardado con éxito";
		break;
		case 98:
			$nss = addslashes(trim($_POST["nss"]));
			$query="Select sc.*,eu.nombre as asesor, if(sc='',gerencia,concat(gerencia,' / ', sc)) as gerencia
					from (Select * from slovo_clientes where nss='$nss') sc
						inner join empl_usuarios eu on (sc.clave_asesor=eu.clave)
						inner join (Select clave,id_gerencia from usuarios_nivel_gerencia where pertenece=1) ung 
								on (sc.clave_asesor=ung.clave)
						inner join estructura_gerencia eg on (ung.id_gerencia=eg.id_gerencia)
						inner join (Select distinct id_gerencia,gerencia from gerencias) g on (ung.id_gerencia=g.id_gerencia)";
			$bd = $conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
				$tb = mysqli_fetch_array($bd);
				$rfc = $tb["rfc"];
				$curp = $tb["curp"];
				$nombre = $tb["nombre"];
				$enfermo = $tb["enfermo"];
				$estatus = $tb["estatus"];
				$embarazo = $tb["embarazo"];
				$gerencia = $tb["gerencia"];
				$comisionista = $tb["asesor"];
				$sal_diario = $tb["sal_diario"];
				$no_credito = $tb["no_credito"];
				$enfermedad = $tb["enfermedad"];
				$cuota_men = $tb["cuota_mensual"];
				$ref_bancaria = $tb["ref_bancaria"];
				$cred_infonavit = $tb["cred_infonavit"];
				$meses_embarazo = $tb["meses_embarazo"];
				$nssr = $tb["nss"];

				$bd = $conecta->consulta("Select * from slovo_fechas where nss='$nss'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$fec_alta = $tb["fec_alta"];
					$fec_baja = $tb["fec_baja"];
					$mot_alta = $tb["motivo_alta"];
					$mot_baja  =$tb["motivo_baja"];
					$fec_reactiva = $tb["fec_reactiva"];
					$fec_env_slovo = $tb["fec_envio_slovo"];
				}
				else
				{
					$fec_alta="";			$fec_baja="";			$mot_alta="";
					$mot_baja="";			$fec_reactiva="";		$fec_env_slovo="";
				}

				$bd=$conecta->consulta("Select * from slovo_contacto where nss='$nss'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$cp=$tb["cp"];
					$cel=$tb["cel"];
					$tel=$tb["tel"];
					$dir=$tb["direc"];
					$email=$tb["email"];
				}
				else
				{
					$cp="";		$cel="";		$tel="";		$dir="";		$email="";
				}

				$bd=$conecta->consulta("Select * from slovo_otros where nss='$nss'");
				if ($bd->num_rows != 0)
				{
					$tb = mysqli_fetch_array($bd);
					$rs_alta = $tb["razon_soc_alta"];
					$llamada = $tb["llamada_servicio"];
					$rs_actual = $tb["razon_soc_actual"];
					$observa_carobra = $tb["observa_carobra"];
				}
				else
					$rs_alta=$llamada=$rs_actual=$observa_carobra="";
			}
			else 
			{
				$cp=$cel=$dir=$rfc=$tel=$curp=$email=$nombre=$estatus=$rs_alta=$gerencia=$fec_alta="";
				$fec_baja=$mot_alta=$mot_baja=$rs_actual=$fec_reactiva=$observa_carobra=$nssr = "";
				$comisionista=$ref_bancaria=$fec_env_slovo=$cred_infonavit=$no_credito=$embarazo="";
				$cuota_men=$sal_diario=$meses_embarazo=0;
			}
			if($enfermo != "NO")
				$enabled_enf = true;
			if($cred_infonavit != "NO")
				$enabled_info = true;
			if($embarazo != "NO")
				$enabled_embarazo = true;

			$response = [
					'rfc' => $rfc,
					'curp' => $curp,
					'nombre' => $nombre,
					'enfermo' => $enfermo,
					'estatus' => $estatus,
					'embarazo' => $embarazo,
					'gerencia' => $gerencia,
					'comisionista' => $comisionista,
					'sal_diario' => $sal_diario,
					'no_credito' => $no_credito,
					'enfermedad' => $enfermedad,
					'cuota_men' => $cuota_men,
					'ref_bancaria' => $ref_bancaria,
					'cred_infonavit' => $cred_infonavit,
					'meses_embarazo' => $meses_embarazo,
					'fec_alta' => $fec_alta,
					'fec_baja' => $fec_baja,
					'mot_alta' => $mot_alta,
					'mot_baja' => $mot_baja,
					'fec_reactiva' => $fec_reactiva,
					'fec_env_slovo' => $fec_env_slovo,
					'cp' => $cp,
					'cel' => $cel,
					'tel' => $tel,
					'dir' => $dir,
					'email' => $email,
					'rs_alta' => $rs_alta,
					'llamada' => $llamada,
					'rs_actual' => $rs_actual,
					'observa_carobra' => $observa_carobra,
					'enabled_enf' => $enabled_enf,
					'enabled_info' => $enabled_info,
					'enabled_embarazo' => $enabled_embarazo,
					'nss' => $nssr
			];
			header('Content-Type: application/json');
			echo json_encode($response);
		break;
		case 99:
			$folio = addslashes(trim($_POST["folio"]));
			$afore = addslashes(trim($_POST["afore"]));
			$saldo = addslashes(trim($_POST["saldo"]));
			$asesor = addslashes(trim($_POST["asesor"]));
			$saldo_limpio = str_replace(['$', ','], '', $saldo);
			$mensaje = "";
			$no_empleado = 0;
			$cliente = addslashes(trim($_POST["cliente"]));

			// Validación: El folio está certificado
			$q_rechazado = "SELECT * FROM carobra_2014.produccion WHERE id_estatus_prod = 1 AND folio = '$folio'";
			$bdr = $conecta->consulta($q_rechazado);

			if ($bdr->num_rows !== 0) 
			{
				$mensaje = "El folio se encuentra certificado.";
			} else 
			{
				$q_certificado = "SELECT * FROM carobra_2014.produccion WHERE id_estatus_prod = 6 AND folio = '$folio'";
				$bdc = $conecta->consulta($q_certificado);

				if ($bdc->num_rows !== 0) {
					$mensaje = "El folio se encuentra rechazado.";
				} else 
				{
					$fieldsToUpdate = [];
					$actualizacionRealizada = false;

					if ($afore !== "") 
					{
						$fieldsToUpdate[] = "afo_cedente = '$afore'";
					}

					if ($asesor !== "") 
					{
						$q_noempleado = "SELECT no_empleado FROM carobra_2014.empl_usuarios WHERE nombre = '$asesor'";
						$bda = $conecta->consulta($q_noempleado);
						if ($bda->num_rows > 0) 
						{
							$tba = mysqli_fetch_array($bda);
							$no_empleado = $tba['no_empleado'];
							$fieldsToUpdate[] = "no_empleado = '$no_empleado'";
						} 
						else 
						{
							$mensaje = "Asesor no encontrado.";
						}
					}

					if ($cliente !== "") 
					{
						$q_trabajador = "SELECT * FROM carobra_2014.prod_trabajador WHERE folio = '$folio'";
						$bdtra = $conecta->consulta($q_trabajador);

						if ($bdtra->num_rows !== 0) 
						{
							$q_iu = "UPDATE carobra_2014.prod_trabajador SET trabajador = UPPER('$cliente') WHERE folio = '$folio'";
						} 
						else 
						{
							$q_iu = "INSERT INTO prod_trabajador(folio, no_oportunidad, trabajador, empresa, telefono, celular, observa, CURP, email_trab, sal_mes, fec_sello, vigencia, cp)
									VALUES ('$folio', '', '$cliente', '', '', '', '', '', '', 0, '', 0, '')";
						}
						$conecta->consulta($q_iu);
						$actualizacionRealizada = true;
					}

					if ($saldo_limpio !== "") 
					{
						$q_monto = "SELECT * FROM carobra_2014.produccion WHERE monto_anticipo != 0 AND folio = '$folio'";
						$bdm = $conecta->consulta($q_monto);

						if ($bdm->num_rows !== 0) 
						{
							$mensaje = "El monto de anticipo ya fue pagado.";
						} 
						else 
						{
							$fieldsToUpdate[] = "saldo_estimado = '$saldo_limpio'";
						}
					}

					if (!empty($fieldsToUpdate)) 
					{
						$q_update = "UPDATE carobra_2014.produccion SET " . implode(", ", $fieldsToUpdate) . " WHERE folio = '$folio'";
						$conecta->consulta($q_update);
						$actualizacionRealizada = true;
					}

					if ($actualizacionRealizada && $mensaje === "") 
					{
						$mensaje = "Datos actualizados correctamente";
					} 
					elseif ($mensaje === "") 
					{
						$mensaje = "No se encontraron campos para actualizar.";
					}
				}
			}
			echo $mensaje;
		break;
		case 100:
			$folio = addslashes(trim($_POST["folio"]));
			$cliente = "";
            $asesor = "";
            $gerencia = "";
            $brocker = "";
            $sc = "";
			
			$q_estruc = "Delete from carobra_2014.estruc_empleados";
			$conecta->consulta($q_estruc);

			$q_estruc_insert = "Insert into estruc_empleados
							Select distinct clave,no_empleado,sc,brocker,ung.id_gerencia,gerencia
							from usuarios_nivel_gerencia ung
							inner join estructura_gerencia eg on (ung.id_gerencia=eg.id_gerencia)
							inner join (Select distinct id_gerencia,gerencia from gerencias) g on (ung.id_gerencia=g.id_gerencia)
							where pertenece = 1";
    		$conecta->consulta($q_estruc_insert);

            $query = "Select p.folio, IFNULL(pt.trabajador, '') AS cliente, IFNULL(eu.nombre, '') AS asesor, IFNULL(ee.gerencia, '') AS gerencia, IFNULL(ee.brocker, '') AS brocker, IFNULL(ee.sc, '') AS sc
                        FROM produccion p
                        INNER JOIN prod_trabajador pt ON (p.folio = pt.folio)
                        LEFT JOIN afores af ON (p.afo_cedente = af.clave)
                        LEFT JOIN empl_usuarios eu ON (p.no_empleado = eu.no_empleado)
                        LEFT JOIN estruc_empleados ee ON (eu.no_empleado = ee.no_empleado)
                        WHERE p.folio = '$folio'";
            $bd = $conecta->consulta($query);
            if ($bd->num_rows !== 0) 
			{
                $tb = mysqli_fetch_array($bd);
                $cliente = $tb['cliente'];
                $asesor = $tb['asesor'];
                $gerencia = $tb['gerencia'];
                $brocker = $tb['brocker'];
                $sc = $tb['sc'];
				$folio = $tb['folio'];
            } 
            else 
            {
                $cliente = "";
                $asesor = "";
                $gerencia = "";
                $brocker = "";
                $sc = "";
            }
			$response = [
				"folio" => $folio,
				"cliente" => $cliente,
				"asesor" => $asesor,
				"gerencia" => $gerencia,
				"brocker" => $brocker,
				"sc" => $sc
			];
			header('Content-Type: application/json');
			echo json_encode($response);
		break;
		case 101:
			$folio = addslashes(trim($_POST["folio"]));
			$anio = date('Y');
			$mensaje = "";

			$q_acum_det = "SELECT * FROM carobra.acum_det_prod_$anio WHERE folio = '$folio'";
			$bd = $conecta->consulta($q_acum_det);
			if ($bd->num_rows == 0) 
			{
				$qi_acum_det = "insert into carobra.acum_det_prod_$anio
									Select distinct	eu.sc,
									eu.brocker,
									cp.id_gerencia,cp.folio,1,fec_pago,fec_traspaso,cp.no_empleado,anticipo,incentivo,rechazo,
									p.saldo_estimado,
									saldo_real,m30,edad,
									comision_asesor,comision_gerente,comision_broker,comision_socio,0,0,0,0,'','','',0.70
								from carobra_2014.calculo_pago cp
									inner join carobra_2014.estruc_empleados eu on (cp.id_gerencia=eu.id_gerencia)
									inner join (Select folio,saldo_estimado from carobra_2014.produccion where folio='$folio') p
											on (cp.folio=p.folio)
								where cp.folio='$folio'";
				$conecta->consulta($qi_acum_det);
				$mensaje = "Folio extemporaneo habilitado";
				echo $mensaje;
			} 
			else 
			{
				$tdd = mysqli_fetch_array($bd);
				$fec_traspaso = $tdd['fec_traspaso'];
				$date_fec_traspaso = DateTime::createFromFormat('d/m/Y', $fec_traspaso);
				$date_target = DateTime::createFromFormat('d/m/Y', '17/08/2024');
				$folio = $tdd['folio'];
				$folio_av = $tdd['folio_av'];

				if ($date_fec_traspaso >= $date_target) 
				{
					$q_update = "UPDATE carobra.acum_det_prod_$anio SET com_asesor = 0, com_gerente = 0, com_brocker = 0, com_sc = 0, estatus_av = 'Rechazada' WHERE folio = '$folio'";
					$conecta->consulta($q_update);
					$mensaje = "Folio extemporaneo habilitado";
				} 
				else 
				{
					$q_sin_av = "SELECT * FROM carobra_2014.pagos_folios_sin_av WHERE folio = '$folio'";
					$bdsin = $conecta->consulta($q_sin_av);
					if ($bdsin->num_rows !== 0) 
					{
						$q_update = "UPDATE carobra.acum_det_prod_$anio SET com_asesor = 0, com_gerente = 0, com_brocker = 0, com_sc = 0, estatus_av = 'Rechazada' WHERE folio = '$folio'";
						$conecta->consulta($q_update);
						$mensaje = "Folio extemporaneo habilitado";
					} 
					else 
					{
						if (!is_null($folio_av) && $folio_av !== "") 
						{
							$q_update = "UPDATE carobra.acum_det_prod_$anio SET com_asesor = 0, com_gerente = 0, com_brocker = 0, com_sc = 0 WHERE folio = '$folio'";
							$conecta->consulta($q_update);
							$mensaje = "Folio extemporaneo habilitado";
						} 
						else 
						{
							$q_update = "UPDATE carobra.acum_det_prod_$anio SET com_asesor = 0, com_gerente = 0, com_brocker = 0, com_sc = 0, estatus_av = 'Rechazada' WHERE folio = '$folio'";
							$conecta->consulta($q_update);
							$mensaje = "Folio extemporaneo habilitado";
						}
					}
					
				}	
			}
			echo $mensaje;
		break;
		case 102:
			$clave = addslashes(trim($_POST["clave"]));
			$area = addslashes(trim($_POST["area"]));
			$fec_captura = date('d/m/Y');
			$mensaja = "";

			$q_clave = "SELECT clave FROM areas_negocio WHERE clave = '$clave'";
			$bdc = $conecta->consulta($q_clave);
			if ($bdc->num_rows !== 0) 
			{
				$mensaja = "YA_EXISTE_CLAVE";
			} 
			else 
			{
				$q_insert = "INSERT INTO areas_negocio VALUES ('$clave', '$area', 1, '$fec_captura')";
				$conecta->consulta($q_insert);
				$mensaja = "REGISTRO_EXITOSO";
			}
			echo $mensaja;
		break;
		case 103:
			$clave = addslashes(trim($_POST["clave"]));
			$mensaja = "";
			$q_delete = "DELETE FROM areas_negocio WHERE clave = '$clave'";
			$conecta->consulta($q_delete);
			$mensaja = "Eliminación exitosa";
			echo $mensaja;
		break;
		case 104:
			$queryg = "SELECT DISTINCT g.id_gerencia, g.gerencia
						FROM gerencias g
						WHERE g.id_gerencia NOT IN (
							SELECT CAST(SUBSTRING(cc.id_cc, 5) AS UNSIGNED) AS id_gerencia_original
							FROM centros_costo_area cc
							WHERE cc.id_cc LIKE 'CC10%'
						)";
			$bdg = $conecta->consulta($queryg);
			$resultados = array();

			if ($bdg->num_rows !== 0) 
			{
				while ($fila = mysqli_fetch_assoc($bdg)) 
				{
					$resultados[] = array(
						'id_gerencia' => $fila['id_gerencia'],
						'gerencia' => $fila['gerencia']
					);
				}
			}
			header('Content-Type: application/json');
			echo json_encode($resultados);
		break;
		case 105:
			$centro = addslashes(trim($_POST["centro"]));
			$areaNegocio = addslashes(trim($_POST["areaNegocio"]));
			$id = addslashes(trim($_POST["id"]));
			$socio = addslashes(trim($_POST["socio"]));

			$mensaje = "";

			if ($id !== "") 
			{
				$q_clave = "SELECT id_cc FROM centros_costo_area WHERE id_cc = '$id'";
				$bdc = $conecta->consulta($q_clave);
				if ($bdc->num_rows !== 0) 
				{
					$mensaje = "YA_EXISTE_CLAVE";
					echo $mensaje;
				}
			} 
			else 
			{
				$q_next_id = "
					SELECT LPAD(
							MIN(gap), 4, '0'
						) AS next_id
					FROM (
						SELECT CAST(SUBSTRING(id_cc, 3) AS UNSIGNED) + 1 AS gap
						FROM centros_costo_area
						WHERE id_cc LIKE 'CC%'
						AND CAST(SUBSTRING(id_cc, 3) AS UNSIGNED) >= 5000
						AND CAST(SUBSTRING(id_cc, 3) AS UNSIGNED) + 1 NOT IN (
							SELECT CAST(SUBSTRING(id_cc, 3) AS UNSIGNED) FROM centros_costo_area
						)
						UNION
						SELECT 5000 AS gap
						FROM DUAL
						WHERE NOT EXISTS (
							SELECT 1
							FROM centros_costo_area
							WHERE CAST(SUBSTRING(id_cc, 3) AS UNSIGNED) >= 5000
						)
					) available_gaps
					LIMIT 1;";

				$bd_next = $conecta->consulta($q_next_id);

				if ($bd_next && $bd_next->num_rows > 0) 
				{
					$row = $bd_next->fetch_assoc();
					$next_id = $row['next_id'];
				} 
				else 
				{
					$next_id = "5000";
				}

				$id = "CC" . $next_id;

				$q_clave_auto = "SELECT id_cc FROM centros_costo_area WHERE id_cc = '$id'";
				$bdc_auto = $conecta->consulta($q_clave_auto);
				if ($bdc_auto->num_rows !== 0) 
				{
					$mensaje = "YA_EXISTE_CLAVE";
					echo $mensaje;
				}
			}
			$q_insert = "INSERT INTO centros_costo_area (id_cc, nombre_cc) 
						VALUES ('$id', '$centro')";
			$conecta->consulta($q_insert);
			$mensaje = "REGISTRO_EXITOSO";
			echo $mensaje;
		break;
		case 106:
			$id_cc = addslashes(trim($_POST["id_cc"]));
			$mensaja = "";
			$q_delete = "DELETE FROM centros_costo_area WHERE id_cc = '$id_cc'";
			$conecta->consulta($q_delete);
			$mensaja = "Eliminación exitosa";
			echo $mensaja;
		break;
		case 107:
			$nombre = addslashes(trim($_POST["nombre"]));
			$tipo = addslashes(trim($_POST["tipo"]));
			$area = addslashes(trim($_POST["area"]));
			$no_partida = "";
			$mensaje = "";

			$q_max_partida = "SELECT MAX(CAST(SUBSTRING(no_partida, 5) AS UNSIGNED)) AS max_partida 
							FROM partidas_area_negocio 
							WHERE no_partida LIKE 'PART%'";

			$bdc_max = $conecta->consulta($q_max_partida);

			if ($bdc_max && $bdc_max->num_rows > 0) 
			{
				$row = $bdc_max->fetch_assoc();
				$next_number = isset($row['max_partida']) ? $row['max_partida'] + 1 : 1000;
			} 
			else 
			{
				$next_number = 1000;
			}

			$no_partida = "PART" . str_pad($next_number, 4, "0", STR_PAD_LEFT);

			if ($area == "COMUN") 
			{
				$area = "";
			}
			
			$q_insert = "INSERT INTO partidas_area_negocio (no_partida, partida, tipo, socio_negocio) 
						VALUES ('$no_partida', '$nombre', $tipo, '$area')";
			$conecta->consulta($q_insert);
			$mensaje = "REGISTRO_EXITOSO";
			echo $mensaje;
		break;
		case 108:
			$no_partida = addslashes(trim($_POST["no_partida"]));
			$mensaja = "";
			$q_delete = "DELETE FROM partidas_area_negocio WHERE no_partida = '$no_partida'";
			$conecta->consulta($q_delete);
			$mensaja = "Eliminación exitosa";
			echo $mensaja;
		break;
		case 109:
			$empleado = addslashes(trim($_POST["empleado"]));
			$fec_prestamo = addslashes(trim($_POST["fec_prestamo"]));
			$monto = addslashes(trim($_POST["monto"]));
			$no_pagos = addslashes(trim($_POST["no_pagos"]));
			$monto_pago = addslashes(trim($_POST["monto_pago"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$centro = addslashes(trim($_POST["centro"]));
			$responsable = addslashes(trim($_POST["responsable"]));
			$periodo = addslashes(trim($_POST["periodo"]));
			$fec_desc = addslashes(trim($_POST["fec_desc"]));
			$observa = addslashes(trim($_POST["observa"]));
			$fechaActual = new DateTime("now", new DateTimeZone("America/Mexico_City"));
			$fec_cap = $fechaActual->format("d/m/Y");
			$clave = "";
			$partida = "";

			if (!empty($fec_prestamo)) 
				$fec_prestamo = DateTime::createFromFormat('Y-m-d', $fec_prestamo)->format('d/m/Y');
			else 
				$fec_prestamo = '';

			if (!empty($fec_desc)) 
				$fec_desc = DateTime::createFromFormat('Y-m-d', $fec_desc)->format('d/m/Y');
			else 
				$fec_desc = '';

			if (empty($monto) || $monto === '$0.00') 
				$monto_double = 0.0;
			else 
			{
				$monto_limpio = str_replace(['$', ','], '', $monto);
				$monto_double = (double)$monto_limpio;
			}

			if (empty($monto_pago) || $monto_pago === '$0.00') 
				$monto_pago_double = 0.0;
			else 
			{
				$monto_pago_limpio = str_replace(['$', ','], '', $monto_pago);
				$monto_pago_double = (double)$monto_pago_limpio;
			}

			if ($empleado == "") 
            	$clave = "";
            else 
            {
                $qusuario = "Select clave FROM empl_usuarios WHERE nombre = '$empleado'";
            	$bd =  $conecta->consulta($qusuario);
                if ($bd->num_rows !== 0) 
                {
                    $tb = mysqli_fetch_array($bd);
                    $clave = $tb["clave"];
            	}
            }
			$sql="Select 1 from prestamos where clave_empleado='$clave' and fec_prestamo='$fec_prestamo' and monto=$monto_double and no_pagos=$no_pagos
					and monto_pago=$monto_pago_double and motivo='$motivo' and fec_captura='$fec_cap'";
			$bd=$conecta->consulta($sql);
			if ($bd->num_rows == 0)
			{
				$query = "Insert INTO carobra_2014.prestamos VALUES (0, '$clave', '$fec_prestamo', $monto_double, $no_pagos, $monto_pago_double, '$motivo', '$fec_cap', '$centro', '$periodo', '$responsable','$fec_desc',0,'$observa')";
				$conecta->consulta($query);

				$id_prestamos = $conecta->obtener_id();

				$values = [];
				$total_pagos = $no_pagos;
				for ($i = 1; $i <= $no_pagos; $i++) 
				{
					$values[] = "(0, $i, '',$monto_pago_double, $id_prestamos, '$clave', $total_pagos, '$periodo')";
				}

				if (!empty($values)) 
				{
					$qpagos_query = "Insert INTO carobra_2014.prestamos_pagos(id_pago, no_pago, fec_pago, monto, id_prestamos, clave, total_pagos, periodo) VALUES " . implode(", ", $values);
					$conecta->consulta($qpagos_query);
				}

				if($motivo != "FINANCIAMIENTO AUTOS")
				{
					//$a = compl_ajuste(1);
					$b = compl_ajuste(0, $clave);
					$c = $b[0]["unidad"];
					$d = $b[0]["id_gerencia"];
					$e = $b[0]["socio"];
					$mesActual = date('m');
					$anioActual = date('Y');

					$tablaDestino = '';
					switch (strtoupper($c)) 
					{
						case 'PREVISIONAL': $tablaDestino = 'ajustes_previsional'; break;
						case 'TARJETA': $tablaDestino = 'ajustes_tarjeta'; break;
						case 'HIPOTECARIO': $tablaDestino = 'ajustes_hipotecario'; break;
						case 'SEGUROS': $tablaDestino = 'ajustes_seguros'; break;
						case 'CORPORATIVO': $tablaDestino = 'ajustes_corporativo'; break;
						default:
							echo "Unidad de negocio no válida: $c";
							exit;
					}
					$partida="";
					$bd=$conecta->consulta("Select partida from motivos_prestamos where motivo='$motivo'");
					if ($bd->num_rows != 0)
					{
						$tb=mysqli_fetch_array($bd);
						$partida=$tb["partida"];
					}
					if ($tablaDestino) 
					{
						$q_ajuste = "Insert INTO carobra_2014.$tablaDestino VALUES (0, '$responsable', '$clave', '$fec_cap', '', '$e', $d, $anioActual, $mesActual, $monto_double, '$motivo', 0, '$centro', '$partida', 0, 0, '', 'O')";

						$conecta->consulta($q_ajuste);
					}
					$q_con_ajuste = "Select * FROM carobra_2014.$tablaDestino 
									WHERE clave= '$clave' AND fecha= '$fec_cap' AND socio= '$e' AND id_gerencia= $d AND mes= $mesActual 
										AND anio= $anioActual AND motivo= '$motivo' AND ajuste= $monto_double";
					$dbajust = $conecta->consulta($q_con_ajuste);
					if ($dbajust->num_rows !== 0) 
					{
						$empleado = $empleado.' ('.$id_prestamos.')';
						echo "Datos almacenados con éxito|$empleado|EXITO|$id_prestamos";
					} 
					else 
					{
						$empleado = $empleado.' ('.$id_prestamos.')';
						echo "Datos almacenados con éxito|$empleado|ERROR|$id_prestamos";
					}
				}
				else
					echo "Datos almacenados con éxito|$empleado|EXITO|$id_prestamos";
			}
			else
				echo "Los los datos proporcionados ya hay un préstamo generado anteriormente||EXITO|0";
		break;
		case 110:
			$empleado = addslashes(trim($_POST["empleado"]));
			$response = [];
			$query = "SELECT ee.sc, ee.brocker, ee.id_gerencia, ee.gerencia 
                        FROM carobra_2014.estruc_empleados ee
                        INNER JOIN empl_usuarios eu ON (ee.clave = eu.clave)
                        WHERE eu.nombre =  '$empleado'";
			$bd = $conecta->consulta($query);
			
			if ($bd->num_rows > 0) 
			{
				while ($row = mysqli_fetch_assoc($bd)) 
				{
					$response[] = $row;
				}
			} 
			else 
			{
				$response = [];
			}

			header('Content-Type: application/json');
			echo json_encode($response);

		break;
		case 111:
			$empleado = addslashes(trim($_POST["empleado"]));
			$fec_prestamo = addslashes(trim($_POST["fec_prestamo"]));
			$monto = addslashes(trim($_POST["monto"]));
			$semana = addslashes(trim($_POST["semana"]));
			$monto_semana = addslashes(trim($_POST["monto_semana"]));
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$clave = "";
			$centro = "";
			$motivo_anterior = "";

			if (!empty($fec_prestamo)) 
			{
				$fec_prestamo = DateTime::createFromFormat('Y-m-d', $fec_prestamo)->format('d/m/Y');
			} 
			else 
			{
				$fec_prestamo = '';
			}

			$q_motivo_anterior = "SELECT motivo FROM prestamos WHERE id_prestamos = $id_prestamo";
			$bd_motivo_anterior = $conecta->consulta($q_motivo_anterior);
			if ($bd_motivo_anterior->num_rows !== 0) 
			{
				$tb_motivo = mysqli_fetch_array($bd_motivo_anterior);
				$motivo_anterior = $tb_motivo['motivo'];
			}

			if (empty($monto) || $monto === '$0.00') 
			{
				$monto_double = 0.0;
			} 
			else 
			{
				$monto_limpio = str_replace(['$', ','], '', $monto);
				$monto_double = (double)$monto_limpio;
			}

			if (empty($monto_semana) || $monto_semana === '$0.00') 
			{
				$monto_semana_double = 0.0;
			} 
			else 
			{
				$monto_semana_limpio = str_replace(['$', ','], '', $monto_semana);
				$monto_semana_double = (double)$monto_semana_limpio;
			}

			if ($empleado == "") 
            {
            	$clave = "";
            } 
            else 
            {
                $qusuario = "SELECT clave FROM empl_usuarios WHERE nombre = '$empleado'";
            	$bd =  $conecta->consulta($qusuario);
                if ($bd->num_rows !== 0) 
                {
                    $tb = mysqli_fetch_array($bd);
                    $clave = $tb["clave"];
            	}
            }

			$query = "UPDATE prestamos SET clave_empleado = '$clave', fec_prestamo = '$fec_prestamo', monto = $monto_double, 
						no_semanas = $semana, monto_semanal = $monto_semana_double, motivo = '$motivo' WHERE id_prestamos = $id_prestamo";
			$conecta->consulta($query);

			$q_existentes = "SELECT COUNT(*) AS total FROM prestamos_pagos WHERE id_prestamos = $id_prestamo";
			$resultado = $conecta->consulta($q_existentes);
			$existentes = 0;
			if ($resultado) 
			{
				$fila = mysqli_fetch_assoc($resultado);
				$existentes = $fila['total'];
			}

			if ($semana > $existentes) 
			{
				for ($i = $existentes + 1; $i <= $semana; $i++) 
				{
					$query_insert = "INSERT INTO prestamos_pagos (id_pago, no_pago, fec_pago, monto, id_prestamos) 
									VALUES (0, $i, '', 0, $id_prestamo)";
					$conecta->consulta($query_insert);
				}
			}

			if ($semana < $existentes) 
			{
				$query_delete = "DELETE FROM prestamos_pagos WHERE id_prestamos = $id_prestamo AND no_pago > $semana";
				$conecta->consulta($query_delete);
			}

			$q_centro = "SELECT cc.id_cc FROM estruc_empleados ee JOIN centros_costo cc ON ee.gerencia = cc.nombre_cc WHERE ee.clave = '$clave'";
			$bdcc = $conecta->consulta($q_centro);
			if ($bdcc->num_rows !== 0) 
			{
				$tbcc = mysqli_fetch_array($bdcc);
				$centro = $tbcc["id_cc"];
			} 
			else 
			{
				$centro = "";
			}

			$q_ajuste = "UPDATE ajustes SET ajuste = $monto_double, cen_cost = '$centro', motivo = '$motivo' WHERE motivo = '$motivo_anterior' AND clave = '$clave'";
			$conecta->consulta($q_ajuste);

			echo "Datos actualizados con éxito|$empleado";
		break;
		case 112:
			$clave = "";
			$area = addslashes(trim($_POST["area"]));
			$nombre = addslashes(trim($_POST["nombre"]));
			$fec_captura = date('d/m/Y');
			$mensaja = "";

			$q = "SELECT MAX(CAST(SUBSTRING(clave, LENGTH('$area') + 1) AS UNSIGNED)) AS max_num 
					FROM socio_area_negocio 
					WHERE areas_negocio = '$area' AND clave LIKE '$area%'";

			$result = $conecta->consulta($q);

			if ($result->num_rows !== 0) 
			{
				$row = mysqli_fetch_assoc($result);
				$max_num = $row['max_num'];

				if ($max_num !== null) 
				{
					$new_num = $max_num + 1;
				} 
				else 
				{
					$new_num = 1;
				}
				$clave = $area . $new_num;
			}
			$q_insert = "INSERT INTO socio_area_negocio(clave, nombre, estatus, areas_negocio, fec_captura) VALUES ('$clave', '$nombre', 1, '$area', '$fec_captura')";
			$conecta->consulta($q_insert);
			$mensaja = "REGISTRO_EXITOSO";
		
			echo $mensaja;
		break;
		case 113:
			$clave = addslashes(trim($_POST["clave"]));
			$mensaja = "";
			$q_delete = "DELETE FROM socio_area_negocio WHERE clave = '$clave'";
			$conecta->consulta($q_delete);
			$mensaja = "Eliminación exitosa";
			echo $mensaja;
		break;
		case 114:
			$area = addslashes(trim($_POST["area"]));
			$respuesta = [];
			$query = "SELECT clave, nombre FROM socio_area_negocio WHERE areas_negocio = '$area' AND estatus = 1 ";
			$bdsa =  $conecta->consulta($query);
			if ($bdsa->num_rows == 0) 
			{
				$respuesta = [];
			} 
			else 
			{
				while ($fila = mysqli_fetch_assoc($bdsa)) 
				{
					$respuesta[] = 
					[
						"clave" => $fila["clave"],
						"nombre" => $fila["nombre"]
					];
				}
			}
			header('Content-Type: application/json');
			echo json_encode($respuesta);
		break;
		case 115:
			$gerencia = addslashes(trim($_POST["gerencia"]));
			$unidad = addslashes(trim($_POST["unidad"]));
			$socio = addslashes(trim($_POST["socio"]));
			$fec_captura = date('d/m/Y');
			$mensaje = "";

			$bd=$conecta->consulta("Select MAX(id_gerencia) + 1 as id FROM gerencias_negocio");
			$tb=mysqli_fetch_array($bd);
			$nuevo_id=$tb["id"];

			$q_insert = "Insert INTO gerencias_negocio (id_gerencia, gerencia, clave_unidad, clave_socio, fec_captura) 
						 VALUES ($nuevo_id,'$gerencia','$unidad','$socio','$fec_captura')";
			$conecta->consulta($q_insert);

			$mensaje = "Datos guardados correctamente";
			echo $mensaje;
		break;
		case 116:
			$id = addslashes(trim($_POST["id_gerencia"]));
			$mensaja = "";
			$q_delete = "DELETE FROM gerencias_negocio WHERE id_gerencia = $id";
			$conecta->consulta($q_delete);
			$mensaja = "Eliminación exitosa";
			echo $mensaja;
		break;
		case 117:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$motivo_prestamo = addslashes(trim($_POST["motivo_prestamo"]));
			$fec_prestamo = addslashes(trim($_POST["fec_prestamo"]));
			$clave_prestamo = addslashes(trim($_POST["clave_prestamo"]));

			$mensaja = "";
			$tablaDestino = '';
			$b = compl_ajuste(0, $clave_prestamo);
			$c = $b[0]["unidad"];
			switch (strtoupper($c)) 
			{
				case 'PREVISIONAL':
						$tablaDestino = 'ajustes_previsional';
				break;
				case 'TARJETA':
						$tablaDestino = 'ajustes_tarjeta';
				break;
				case 'HIPOTECARIO':
						$tablaDestino = 'ajustes_hipotecario';
				break;
				case 'SEGUROS':
						$tablaDestino = 'ajustes_seguros';
				break;
				case 'CORPORATIVO':
						$tablaDestino = 'ajustes_corporativo';
				break;
				default:
						echo "Unidad de negocio no válida: $c";
				exit;
			}
			$query_valida = "SELECT * FROM $tablaDestino WHERE clave = '$clave_prestamo' AND fecha = '$fec_prestamo' AND motivo = '$motivo_prestamo' AND fec_pago != '' AND estatus = 1";
			$bdv =  $conecta->consulta($query_valida);
			if ($bdv->num_rows !== 0) 
			{
				echo $mensaja = "El préstamo ya cuenta con un ajuste generado";

			} 
			else 
			{
				$query_p = "SELECT * FROM prestamos_pagos WHERE id_prestamos = $id_prestamo  AND fec_pago <> '' AND monto <> 0 ";
				$bdp =  $conecta->consulta($query_p);
				if ($bdp->num_rows !== 0) 
				{
					echo $mensaja = "El préstamo cuenta con pagos realizados por lo cual no es posible eliminarlo";
				}
				else
				{
					$query_dp = "DELETE FROM prestamos_pagos WHERE id_prestamos = $id_prestamo";
					$query_dpp = "DELETE FROM prestamos WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($query_dp);
					$conecta->consulta($query_dpp);

					$query_da = "DELETE FROM $tablaDestino WHERE clave = '$clave_prestamo' AND fecha = '$fec_prestamo' AND motivo = '$motivo_prestamo' AND fec_pago = '' AND estatus = 0";
					$conecta->consulta($query_da);
					echo $mensaja = "El préstamo fue eliminado con exito";
				}
			}
		break;
		case 118:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$query = "Select p.id_prestamos, p.monto AS monto_total, p.no_pagos, p.monto_pago,
							COUNT(CASE WHEN pp.monto > 0 AND pp.fec_pago != '' THEN 1 END) AS pagos_realizados,
							COALESCE(SUM(CASE WHEN pp.monto > 0 AND pp.fec_pago != '' THEN pp.monto ELSE 0 END), 0) AS monto_pagado,
							p.periodo,detener_cobro
					FROM prestamos p
					LEFT JOIN prestamos_pagos pp ON p.id_prestamos = pp.id_prestamos
					WHERE p.id_prestamos = $id_prestamo
					GROUP BY p.id_prestamos, p.monto, p.no_pagos";
			$db = $conecta->consulta($query);

			$query_semanas = "Select no_pago, total_pagos FROM prestamos_pagos WHERE fec_pago != '' AND monto != 0 AND id_prestamos = $id_prestamo";

			$dbs = $conecta->consulta($query_semanas);
			$semanas_sin_pago = [];
			if ($dbs->num_rows > 0) 
			{
				while ($row = $dbs->fetch_assoc()) 
				{
					$semanas_sin_pago[] = $row['no_pago'];
				}
			}
			
			if ($db->num_rows != 0) 
			{
				$result = $db->fetch_assoc();
				echo json_encode([
					'data' => $result,
					'semanas_sin_pago' => $semanas_sin_pago
				]);
			} 
			else 
				echo json_encode([]);
		break;
		case 119:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$empleado = addslashes(trim($_POST["empleado"]));
			$semana = addslashes(trim($_POST["semana"]));
			$monto_semana = addslashes(trim($_POST["monto"]));
			$detener_cobro = addslashes(trim($_POST["detener_cobro"]));

			if($semana!="")
			{
				// Convertir el monto semanal a double
				if (empty($monto_semana) || $monto_semana === '$0.00') 
				{
					$monto_semana_double = 0.0;
				} 
				else 
				{
					$monto_semana_limpio = str_replace(['$', ','], '', $monto_semana);
					$monto_semana_double = (double)$monto_semana_limpio;
				}

				// Consultar el estado actual
				$q_prestamo_ant = "Select no_pagos, clave_empleado, monto_pago FROM prestamos 
								   WHERE id_prestamos = $id_prestamo";
				$db = $conecta->consulta($q_prestamo_ant);
				if ($db->num_rows !== 0) 
				{
					$tb = mysqli_fetch_array($db);
					$monto_semanal_ant = $tb["monto_pago"];
					$no_semanas_ant = $tb["no_pagos"];
					$clave_empleado_ant = $tb["clave_empleado"];
				} 
				else 
				{
					echo "Error: Préstamo no encontrado.";
					exit;
				}

				// Actualizar los datos 
				$q_prestamo = "Update prestamos SET monto_pago = $monto_semana_double, no_pagos = $semana
							   WHERE id_prestamos = $id_prestamo";
				$conecta->consulta($q_prestamo);

				// Verificar pagos realizados para mantenerlos intactos
				$q_pagos_realizados = "Select no_pago, monto FROM prestamos_pagos 
									   WHERE id_prestamos = $id_prestamo AND fec_pago != '' 
									   AND monto != 0";
				$result_pagos_realizados = $conecta->consulta($q_pagos_realizados);

				$pagos_realizados = [];
				$total_pagado = 0.0;

				while ($row = mysqli_fetch_assoc($result_pagos_realizados)) 
				{
					$pagos_realizados[] = $row['no_pago'];
					$total_pagado += $row['monto'];
				}

				// Calcular el monto restante por pagar
				$monto_restante = ($monto_semanal_ant * $no_semanas_ant) - $total_pagado;

				// Ajustar los registros en prestamos_pagos
				if ($semana < $no_semanas_ant) 
				{
					// Mantener los registros necesarios y actualizar 
					$q_delete = "Delete FROM prestamos_pagos 
								WHERE id_prestamos = $id_prestamo 
								AND no_pago > $semana 
								AND no_pago NOT IN (" . implode(',', $pagos_realizados) . ")";
					$conecta->consulta($q_delete);

					// Actualizar en los registros restantes
					$q_update_total_pagos = "UPDATE prestamos_pagos 
											SET total_pagos = $semana 
											WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($q_update_total_pagos);

					// Actualizar el próximo registro con el monto restante
					$proximo_pago = $pagos_realizados ? max($pagos_realizados) + 1 : 1;
					$q_update_restante = "UPDATE prestamos_pagos 
										SET monto = 0
										WHERE id_prestamos = $id_prestamo 
										AND no_pago = $proximo_pago";
					$conecta->consulta($q_update_restante);
				} 
				elseif ($semana > $no_semanas_ant) 
				{
					// Insertar nuevos registros si las semanas aumentan
					$values = [];
					for ($i = $no_semanas_ant + 1; $i <= $semana; $i++) 
					{
						if (!in_array($i, $pagos_realizados)) 
						{
							$values[] = "(0, $i, '', 0.0, $id_prestamo, '$clave_empleado_ant', $semana)";
						}
					}

					if (!empty($values)) 
					{
						$q_insert = "Insert INTO prestamos_pagos (id_pago, no_pago, fec_pago, monto, id_prestamos, clave, total_pagos) VALUES " . implode(", ", $values);
						$conecta->consulta($q_insert);
					}

					// Actualizar  en todos los registros
					$q_update_total_pagos = "Update prestamos_pagos 
											SET total_pagos = $semana 
											WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($q_update_total_pagos);
				}
			}
			$conecta->consulta("Update prestamos set detener_cobro=$detener_cobro where id_prestamos=$id_prestamo");

			echo "Datos actualizados con éxito|$empleado";
		break;
		case 120:
			$gastos = json_decode($_POST['gastos'], true);
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$values = array();
			$fec_carga = date('d/m/Y H:i');
			$gerencia = "";
			$id_gerencia = 0;
			$filas_fallidas = array();
			foreach ($gastos as $gasto) 
			{
				$indice = $gasto['indice'];
				$proveedor = $gasto['proveedor'];
				$resultado = procesarProveerdor($proveedor);
				$prove = $resultado['proveedor'];
				$no_prove = $resultado['no_proveedor'];
				$prove_validacion = 'NO_VALIDO';
				//echo $prove." | ".$no_prove;
				$fecha = $gasto['fecha'];
				$fecha_resultado = validarFecha($fecha);
				$fecha = $fecha_resultado['valor'];
				$fecha_validacion = $fecha_resultado['validacion'];

				$socio = $gasto['socio'];
				$gerencia_llega = $gasto['gerencia'];
				$ajuste_valor = $gasto['gasto'];
				$motivo = mb_substr($gasto['motivo'], 0, 150, 'UTF-8');
				$cen_costo = $gasto['cent_costo'];
				$partida = $gasto['partida'];

				$cen_costo = obtenerTextoEntreParentesis($cen_costo);
				$partida = obtenerPartida($partida);
				$observacion = mb_substr($gasto['observacion'], 0, 150, 'UTF-8');


				if (!empty($gerencia_llega)) 
				{
					if (preg_match('/\((\d+)\)/', $gerencia_llega, $coincidencias)) 
					{
						$id_gerencia = (int)$coincidencias[1];
						$gerencia = trim(preg_replace('/\(\d+\)/', '', $gerencia_llega));
					} 
					else 
					{
						$id_gerencia = obtener_gerencia_id($gerencia_llega);
						$gerencia = $gerencia_llega;
					}
				}

				$ajuste_limpio = str_replace(array('$', ',', ' '), '', $ajuste_valor);
				if (is_numeric($ajuste_limpio)) 
				{
					$ajuste_validacion = "VALIDO";
				} 
				else 
				{
					$ajuste_limpio = 0;
					$ajuste_validacion = "VALIDO";
				}

				$centro_costo_validacion = "NO_VALIDO";
				$partida_validacion = "NO_VALIDO";
				$gerencia_validacion = "NO_VALIDO";
				$socio_validacion = "NO_VALIDO";

				$values[] = "(0, $indice, '$prove', '$no_prove', '$fecha', '$socio', '$gerencia', $id_gerencia, $ajuste_limpio, '$motivo', '$cen_costo', '$partida', '$observacion', '$prove_validacion', '$gerencia_validacion', '$socio_validacion', '$centro_costo_validacion', '$partida_validacion', '$usuario_cap', '$fec_carga')";
			}

			if (!empty($values)) 
			{
				$qi_ajustes = "INSERT INTO carobra_2014.temp_gastos VALUES " . implode(', ', $values);
				//echo $qi_ajustes;
				$conecta->consulta($qi_ajustes);
				//echo "Termino....";
			}

			$qvalida_clave = "UPDATE carobra_2014.temp_gastos a, carobra_2014.proveedores u
								set proveedor_validacion = 'VALIDO', a.proveedor = u.proveedor 
								where a.no_proveerdor = u.no_prov";
			$conecta->consulta($qvalida_clave);

			$qvalida_costo = "UPDATE carobra_2014.temp_gastos ta
							LEFT JOIN carobra_2014.centros_costo_area cc 
							ON ta.cent_costo = cc.id_cc
							SET ta.centro_validacion = 'VALIDO'
							WHERE ta.cent_costo = cc.id_cc OR ta.cent_costo = ''";
			$conecta->consulta($qvalida_costo);

			$qvalida_partida = "UPDATE carobra_2014.temp_gastos ta
									LEFT JOIN carobra_2014.partidas_area_negocio pt 
									ON ta.partida = pt.no_partida
									SET ta.partida_Validacion = 'VALIDO'
									WHERE ta.partida = pt.no_partida OR ta.partida = ''";
			$conecta->consulta($qvalida_partida);

			$qvalida_gerencia = "UPDATE carobra_2014.temp_gastos ta
									LEFT JOIN carobra_2014.gerencias_negocio gn 
									ON ta.id_gerencia = gn.id_gerencia
									SET 
										ta.gerencia_validacion = CASE 
											WHEN gn.id_gerencia IS NOT NULL THEN 'VALIDO'
											ELSE 'NO_VALIDO'
										END,
										ta.gerencia = CASE 
											WHEN gn.id_gerencia IS NOT NULL THEN gn.gerencia
											ELSE ''
										END";
			$conecta->consulta($qvalida_gerencia);

			$qvalida_socio = "UPDATE carobra_2014.temp_gastos a, carobra_2014.socio_area_negocio u
								set socio_validacion = 'VALIDO', a.socio = u.clave 
								where a.socio = u.clave";
			$conecta->consulta($qvalida_socio);

			$q_temp_gasto = "SELECT * 
								FROM carobra_2014.temp_gastos 
								WHERE usuario_cap = '$usuario_cap'
              					AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i')
								ORDER BY 
									(CASE WHEN proveedor_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN centro_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN partida_Validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN gerencia_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN socio_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC";
			
			$q_max_gasto = "SELECT SUM(gasto) AS total 
								FROM temp_gastos WHERE usuario_cap = '$usuario_cap' 
								AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i') ";

			$dbtotal = $conecta->consulta($q_max_gasto);
			$tb = mysqli_fetch_array($dbtotal);
			$total = $tb['total'];

			$q_ajustes_err = "SELECT * 
								FROM carobra_2014.temp_gastos 
								WHERE usuario_cap = '$usuario_cap'
								AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fec_carga', '%d/%m/%Y %H:%i')
								AND (proveedor_validacion = 'NO_VALIDO' OR 
								centro_validacion = 'NO_VALIDO' OR
								partida_validacion = 'NO_VALIDO' OR 
								gerencia_validacion = 'NO_VALIDO' OR
								socio_validacion = 'NO_VALIDO')";
			
			$dberr = $conecta->consulta($q_ajustes_err);
			if ($dberr->num_rows !== 0) 
			{
				while ($fila = $dberr->fetch_assoc()) 
				{
					$filas_fallidas[] = $fila;
				}
			}

			$dba = $conecta->consulta($q_temp_gasto);
			if ($dba->num_rows !== 0) 
			{
				$resultados = array();
				if ($dba->num_rows !== 0) {
					while ($fila = $dba->fetch_assoc()) 
					{
						$resultados[] = $fila;
					}
				}
			}

			$respuesta = array(
				'gastos' => $resultados,
				'total' => $total,
				'errores' => $filas_fallidas
			);				
			echo json_encode($respuesta);
		break;
		case 121:
			$id = addslashes(trim($_POST["id_gasto"]));
			$qd_temp_gastos = "DELETE FROM carobra_2014.temp_gastos WHERE id_gasto = $id";
			$conecta->consulta($qd_temp_gastos);
			echo "Datos eliminados con exito";
		break;
		case 122:
			$gasto_temp = json_decode($_POST['gastos'], true);
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			
			$fecha_cap = $gasto_temp[0]['fecha'];

			foreach ($gasto_temp as $gasto) 
			{
				$id = $gasto['id'];
				$qd_temp_gasto = "DELETE FROM carobra_2014.temp_gastos WHERE id_gasto = $id";
				$conecta->consulta($qd_temp_gasto);
			}

			$q_temp_gasto = "SELECT * 
								FROM carobra_2014.temp_gastos 
								WHERE usuario_cap = '$usuario_cap'
              					AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_cap', '%d/%m/%Y %H:%i')
								ORDER BY 
									(CASE WHEN proveedor_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN centro_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN partida_Validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN gerencia_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC,
									(CASE WHEN socio_validacion = 'NO_VALIDO' THEN 1 ELSE 0 END) DESC";
			
			$q_max_gasto = "SELECT SUM(gasto) AS total 
								FROM temp_gastos WHERE usuario_cap = '$usuario_cap' 
								AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_cap', '%d/%m/%Y %H:%i') ";

			$dbtotal = $conecta->consulta($q_max_gasto);
			$tb = mysqli_fetch_array($dbtotal);
			$total = $tb['total'];

			$q_ajustes_err = "SELECT * 
								FROM carobra_2014.temp_gastos 
								WHERE usuario_cap = '$usuario_cap'
								AND STR_TO_DATE(fec_carga, '%d/%m/%Y %H:%i') = STR_TO_DATE('$fecha_cap', '%d/%m/%Y %H:%i')
								AND (proveedor_validacion = 'NO_VALIDO' OR 
								centro_validacion = 'NO_VALIDO' OR
								partida_validacion = 'NO_VALIDO' OR 
								gerencia_validacion = 'NO_VALIDO' OR
								socio_validacion = 'NO_VALIDO')";
			
			$dberr = $conecta->consulta($q_ajustes_err);
			if ($dberr->num_rows !== 0) 
			{
				while ($fila = $dberr->fetch_assoc()) 
				{
					$filas_fallidas[] = $fila;
				}
			}
			else 
			{
				$filas_fallidas = [];
			}

			$dba = $conecta->consulta($q_temp_gasto);
			if ($dba->num_rows !== 0) 
			{
				$resultados = array();
				if ($dba->num_rows !== 0) {
					while ($fila = $dba->fetch_assoc()) 
					{
						$resultados[] = $fila;
					}
				}
				else 
				{
					$resultados = [];
				}
			}

			$respuesta = array(
				'mensaje' => 'Datos eliminados con éxito',
				'gastos' => $resultados,
				'total' => $total,
				'errores' => $filas_fallidas
			);
			header('Content-Type: application/json');				
			echo json_encode($respuesta);
		break;
		case 123:
			$id = addslashes(trim($_POST["id"]));
			$clave = addslashes(trim($_POST["proveedor"]));
			$fecha = addslashes(trim($_POST["fecha"]));
			$gasto = addslashes(trim($_POST["gasto"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$centro = addslashes(trim($_POST["centro"]));
			$partida = addslashes(trim($_POST["partida"]));

			$resultado = procesarProveerdor($clave);
			$prove = $resultado['proveedor'];
			$no_prove = $resultado['no_proveedor'];

			if (!empty($centro) && preg_match('/\(([^)]+)\)/', $centro, $matches)) 
			{
				$centro = $matches[1];
			}

			if (!empty($partida) && preg_match('/\(([^)]+)\)/', $partida, $matches)) 
			{
				$partida = $matches[1];
			}

			if (!empty($fecha)) 
			{
				$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
				$fecha = $fecha_obj ? $fecha_obj->format('d/m/Y') : '';
			}

			$ajuste_limpio = str_replace(array('$', ',', ' ', '(', ')'), '', $gasto);
			$ajuste = is_numeric($ajuste_limpio) ? $ajuste_limpio : 0;

			$qu_gasto_tempo = "UPDATE temp_gastos SET 
				proveedor = '$prove', 
				no_proveerdor = '$no_prove', 
				fecha = '$fecha',  
				gasto = $ajuste, 
				cent_costo = '$centro', 
				partida = '$partida', 
				centro_validacion = 'VALIDO',
				partida_validacion = 'VALIDO',
				gerencia_validacion = 'VALIDO',
				socio_validacion = 'VALIDO',
				proveedor_validacion = 'VALIDO'
				WHERE id_gasto = '$id'";

			$conecta->consulta($qu_gasto_tempo);

			$response = array(
				'id' => $id,
				'proveedor' => $prove,
				'fecha' => $fecha,
				'gasto' => $ajuste,
				'motivo' => $motivo,
				'centro' => $centro,
				'partida' => $partida
			);
			header('Content-Type: application/json');
			echo json_encode($response);
		break;
		case 124:
			//echo "owo";
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$fecha_carga = addslashes(trim($_POST["fecha"]));
			$values = [];
			$values2 = [];

			$query_gasto = "SELECT *
							FROM temp_gastos
							WHERE usuario_cap = '$usuario_cap' 
							AND fec_carga =  '$fecha_carga' 
							AND proveedor_validacion = 'VALIDO' 
							AND gerencia_validacion = 'VALIDO' 
							AND socio_validacion = 'VALIDO' 
							AND centro_validacion = 'VALIDO' 
							AND partida_Validacion = 'VALIDO'";

			// Ejecutar consulta
			$db = $conecta->consulta($query_gasto);

			if ($db->num_rows !== 0) {
				// Obtener el máximo valor de no_gasto actual en la tabla
				$q_max = "SELECT MAX(CAST(SUBSTRING(no_gasto, 2) AS UNSIGNED)) AS max_no_gasto FROM carobra_2014.gastos";
				$stmt = $conecta->consulta($q_max);
				$tb = mysqli_fetch_array($stmt);
				$max = $tb['max_no_gasto'] ? $tb['max_no_gasto'] : 0; // Si es NULL, inicia desde 0

				while ($fila = $db->fetch_assoc()) {
					$max++; // Incrementar para cada fila
					$no_gasto = "G" . $max;

					$fecha = $fila['fecha'];
					$motivo = $fila['motivo'];
					$no_proveerdor = $fila['no_proveerdor'];
					$partida =  $fila['partida'];
					$gasto = $fila['gasto'];
					$observacion = $fila['observacion'];
					$cc =  $fila['cent_costo'];
					$id_gerencia =  $fila['id_gerencia'];
					$socio =  $fila['socio'];


					$values[] = "('$no_gasto', '$fecha', '$socio', '$motivo', '$no_proveerdor', '$partida', $gasto, '$observacion', 0, '')";
					$values2[] = "('$no_gasto', '$cc', $gasto, 0, $id_gerencia)";
				}
			} else {
				$values = [];
				$values2 = [];
			}

			// Insertar los valores en la base de datos
			if (!empty($values)) {
				$qi_gastos = "INSERT INTO carobra_2014.gastos VALUES " . implode(', ', $values);
				//echo $qi_gastos;
				$conecta->consulta($qi_gastos);
			}
			if (!empty($values2)) {
				$qi_ccgastos = "INSERT INTO carobra_2014.cc_gasto VALUES " . implode(', ', $values2);
				//echo $qi_ccgastos;
				$conecta->consulta($qi_ccgastos);
			}

			$q_delete = "DELETE FROM temp_gastos 
							WHERE usuario_cap = '$usuario_cap' 
							AND fec_carga =  '$fecha_carga' 
							AND proveedor_validacion = 'VALIDO' 
							AND gerencia_validacion = 'VALIDO' 
							AND socio_validacion = 'VALIDO' 
							AND centro_validacion = 'VALIDO' 
							AND partida_Validacion = 'VALIDO'";
			$conecta->consulta($q_delete);
			//echo $q_delete;
			echo "Gastos cargados con exito";
		break;
		case 125:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$responsable = addslashes(trim($_POST["responsable"]));
			$empleado = addslashes(trim($_POST["empleado"]));

			$q_prestamo = "Select * FROM prestamos WHERE id_prestamos = $id_prestamo";
			$dbp = $conecta->consulta($q_prestamo);
			if ($dbp->num_rows != 0) 
			{
				$tb = mysqli_fetch_array($dbp);
				$clave = $tb['clave_empleado'];
				$monto = $tb['monto'];
				$motivo = $tb['motivo'];
				$cen_cost = $tb['cen_cost'];
				$responsable = $tb['responsable'];
				
				$fec_captura = date('d/m/Y');
				$mes = date('m');
				$anio = date('Y');

				$b = compl_ajuste(0, $clave);
				$c = $b[0]["unidad"];
				$d = $b[0]["id_gerencia"];
				$e = $b[0]["socio"];

				$tablaDestino = '';
				switch (strtoupper($c)) {
					case 'PREVISIONAL':
						$tablaDestino = 'ajustes_previsional';
						break;
					case 'TARJETA':
						$tablaDestino = 'ajustes_tarjeta';
						break;
					case 'HIPOTECARIO':
						$tablaDestino = 'ajustes_hipotecario';
						break;
					case 'SEGUROS':
						$tablaDestino = 'ajustes_seguros';
						break;
					case 'CORPORATIVO':
						$tablaDestino = 'ajustes_corporativo';
						break;
					default:
						echo "Unidad de negocio no válida: $c";
						exit;
				}

				$partida="";
				$bd=$conecta->consulta("Select partida from motivos_prestamos where motivo='$motivo'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$partida=$tb["partida"];
				}

				if ($tablaDestino) 
				{
					$q_ajuste = "Insert INTO $tablaDestino VALUES (0, '$responsable', '$clave', '$fec_captura', '', '$e', $d, $anio, $mes, $monto, '$motivo', 0, '$cen_cost', '$partida', 0, 0, '', 'O')";

					$conecta->consulta($q_ajuste);
					echo "Ajuste almacenado con éxito|$empleado";
				}
			}
		break;
		case 126:
			$id_ajuste = addslashes(trim($_POST["id"]));
			$destino = addslashes(trim($_POST["destino"]));
			$tablaDestino = "";
                //echo ":3 ".$indice;
            switch (strtoupper($destino)) 
            {
                case 'P':
                    $tablaDestino = 'ajustes_previsional';
                break;
                case 'T':
                    $tablaDestino = 'ajustes_tarjeta';
                break;
                case 'H':
                    $tablaDestino = 'ajustes_hipotecario';
                break;
                case 'S':
                    $tablaDestino = 'ajustes_seguros';
                break;
                case 'C':
                    $tablaDestino = 'ajustes_corporativo';
                break;
                default:
                    echo "Unidad de negocio no válida: $destino";
                exit;
            }

			if ($id_ajuste !== "")
			{
				$query = "DELETE FROM $tablaDestino WHERE id_ajuste = $id_ajuste";
				$conecta->consulta($query);
				echo "Ajuste eliminado con éxito";
			}
		break;
		case 127:
			$id_ajuste = addslashes(trim($_POST["id"]));
			$destino = addslashes(trim($_POST["destino"]));
			$responsable = addslashes(trim($_POST["responsable"]));
			$empleado = addslashes(trim($_POST["clave"]));
			$fecha = addslashes(trim($_POST["fecha"]));
			$fec_pago = addslashes(trim($_POST["fec_pago"]));
			$socio = addslashes(trim($_POST["socio"]));
			$gerencia_llega = addslashes(trim($_POST["gerencia"]));
			$anio = addslashes(trim($_POST["anio"]));
			$mes = addslashes(trim($_POST["mes"]));
			$ajuste = addslashes(trim($_POST["ajuste"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$estatus = addslashes(trim($_POST["estatus"]));
			$cen_costo = addslashes(trim($_POST["centro"]));
			$partida = addslashes(trim($_POST["partida"]));
			$tipo = addslashes(trim($_POST["tipo"]));
			$afecta_sb = addslashes(trim($_POST["afecta_sb"]));
			$no_gasto = addslashes(trim($_POST["no_gasto"]));
			$afecta_op = addslashes(trim($_POST["afecta_op"]));
			
			$cen_costo = obtenerTextoEntreParentesis($cen_costo);
			$partida = obtenerPartida($partida);
			$resultado = procesarEmpleado($empleado);
			$clave = $resultado['clave'];
			$nombre = $resultado['nombre'];
			

			if (!empty($gerencia_llega)) 
			{
				if (preg_match('/\((\d+)\)/', $gerencia_llega, $coincidencias)) 
				{
					$id_gerencia = (int)$coincidencias[1];
					$gerencia = trim(preg_replace('/\(\d+\)/', '', $gerencia_llega));
				} 
				else 
				{
					$id_gerencia = obtener_gerencia_id($gerencia_llega);
				}
			}
			
			if (!empty($no_gasto)) 
			{
				if (preg_match('/\(([^)]+)\)/', $no_gasto, $coincidencias)) 
				{
					$gasto_no = $coincidencias[1];
					//echo "Valor entre paréntesis: " . $gasto_no;
				} 
				else 
				{
					$gasto_no = "";
				}
			} 
			else 
			{
				$gasto_no = "";
			}
			//echo ":3 ".$gato_no;
			

			if (!empty($fecha)) 
			{
				$fecha = DateTime::createFromFormat('Y-m-d', $fecha)->format('d/m/Y');
			} 
			else 
			{
				$fecha = '';
			}

			if (!empty($fec_pago)) 
			{
				$fec_pago = DateTime::createFromFormat('Y-m-d', $fec_pago)->format('d/m/Y');
			} 
			else 
			{
				$fec_pago = '';
			}
			

			if (empty($ajuste) || $ajuste === '$0.00') 
			{
				$ajuste_double = 0.0;
			} 
			else 
			{
				$ajuste_limpio = str_replace(['$', ','], '', $ajuste);
				$ajuste_double = (double)$ajuste_limpio;
			}
			

			$tablaDestino = "";
        	
            switch (strtoupper($destino)) 
            {
                case 'P':
                    $tablaDestino = 'ajustes_previsional';
                break;
                case 'T':
                    $tablaDestino = 'ajustes_tarjeta';
                break;
                case 'H':
                    $tablaDestino = 'ajustes_hipotecario';
                break;
                case 'S':
                    $tablaDestino = 'ajustes_seguros';
                break;
                case 'C':
                    $tablaDestino = 'ajustes_corporativo';
                break;
                default:
                    echo "Unidad de negocio no válida: $destino";
                exit;
            }

			$query_up = "UPDATE $tablaDestino 
							SET responsable = '$responsable',
							clave = '$clave', 
							fecha = '$fecha',
							fec_pago = '$fec_pago',
							socio = '$socio',
							id_gerencia = $id_gerencia,
							anio = $anio,
							mes = $mes,
							ajuste = $ajuste_double,
							motivo = '$motivo', 
							estatus = $estatus,
							cen_cost = '$cen_costo',
							partida = '$partida', 
							tipo_ajuste = $tipo,
							afecta_sb = $afecta_sb,
							no_gasto = '$gasto_no',
							afecta_op = '$afecta_op' 
							WHERE id_ajuste = $id_ajuste";
			$conecta->consulta($query_up);
			//echo $query_up;
			echo "Ajuste actualizado con éxito|$destino-$id_ajuste";
		break;
		case 128:
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$fec_carga = addslashes(trim($_POST["fec_carga"]));
			
			$empleado = addslashes(trim($_POST["clave"]));
			$fecha = addslashes(trim($_POST["fecha"]));
			$fec_pago = addslashes(trim($_POST["fec_pago"]));
			$socio_llega = addslashes(trim($_POST["socio"]));
			$gerencia_llega = addslashes(trim($_POST["gerencia"]));
			$anio = addslashes(trim($_POST["anio"]));
			$mes = addslashes(trim($_POST["mes"]));
			$ajuste = addslashes(trim($_POST["ajuste"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$estatus = addslashes(trim($_POST["estatus"]));
			$cen_costo = addslashes(trim($_POST["centro"]));
			$partida = addslashes(trim($_POST["partida"]));
			$tipo = addslashes(trim($_POST["tipo"]));
			$no_gasto = addslashes(trim($_POST["no_gasto"]));
			
			$cen_costo = obtenerTextoEntreParentesis($cen_costo);
			$partida = obtenerPartida($partida);
			$resultado = procesarEmpleado($empleado);
			$clave = $resultado['clave'];
			$nombre = $resultado['nombre'];

			if (!empty($gerencia_llega)) 
			{
				if (preg_match('/\((\d+)\)/', $gerencia_llega, $coincidencias)) 
				{
					$id_gerencia = (int)$coincidencias[1];
					$gerencia = trim(preg_replace('/\(\d+\)/', '', $gerencia_llega));
				} 
				else 
				{
					$id_gerencia = obtener_gerencia_id($gerencia_llega);
				}
			}
			
			if (!empty($no_gasto)) 
			{
				if (preg_match('/\(([^)]+)\)/', $no_gasto, $coincidencias)) 
				{
					$gasto_no = $coincidencias[1];
					//echo "Valor entre paréntesis: " . $gasto_no;
				} 
				else 
				{
					$gasto_no = "";
				}
			} 
			else 
			{
				$gasto_no = "";
			}
			//echo ":3 ".$gato_no;
			

			if (!empty($fecha)) 
			{
				$fecha = DateTime::createFromFormat('Y-m-d', $fecha)->format('d/m/Y');
			} 
			else 
			{
				$fecha = '';
			}

			if (!empty($fec_pago)) 
			{
				$fec_pago = DateTime::createFromFormat('Y-m-d', $fec_pago)->format('d/m/Y');
			} 
			else 
			{
				$fec_pago = '';
			}
			

			if (empty($ajuste) || $ajuste === '$0.00') 
			{
				$ajuste_double = 0.0;
			} 
			else 
			{
				$ajuste_limpio = str_replace(['$', ','], '', $ajuste);
				$ajuste_double = (double)$ajuste_limpio;
			}

			if ($socio_llega !== "") 
			{
				$unidad = obtener_unidad($socio_llega, "");
			}
			else if ($socio_llega == "") 
			{
				$resultado = obtener_unidad("", $gerencia);
				$unidad = $resultado['unidad'];
				$socio_llega = $resultado['socio'];
			}

			if($usuario_cap == '')
			{
				$query="Select clave from carobra.usuarios 
						where nombre='" . utf8_encode($_SESSION["nombre"]) . "'";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				$usuario_cap=$tb["clave"];					
			}
			if($fec_carga =="")
				$fec_carga=date("d/m/Y G:i");
			$q_indice = "Select ifnull(MAX(indice) + 1,1) AS siguiente FROM temp_ajustes 
						 WHERE usuario_cap  = '$usuario_cap' AND fec_carga  = '$fec_carga'";

			$dbs = $conecta->consulta($q_indice);
			if ($dbs->num_rows !== 0) 
			{
				$tbs = mysqli_fetch_array($dbs);
				$se_inidice = $tbs['siguiente'];

				$q_insert = "INSERT INTO temp_ajustes VALUES (0, $se_inidice, '$nombre', '$clave', '$fecha', $id_gerencia, '$gerencia_llega', '$fec_pago', 
									$anio, $mes, '$unidad', '$socio_llega', $ajuste_double, '$motivo', '$cen_costo', '$partida', 
									$tipo, 0, 'O', 'VALIDO', 'VALIDO', 'VALIDO', 'VALIDO', 'VALIDO', 'VALIDO', 'VALIDO', 
									'$usuario_cap', '$fec_carga')";
				//echo $q_insert;
				$conecta->consulta($q_insert);
				echo $usuario_cap . "|" . $fec_carga . "|Ajuste guardado con exito";
			}
		break;
		case 129:
			$ajustes_temp = json_decode($_POST['gastos'], true);
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));	
			$fecha_cap = $ajustes_temp[0]['fecha'];
		
			foreach ($ajustes_temp as $ajuste) 
			{
				$id = $ajuste['id'];
				$qd_temp_ajuste = "Delete FROM carobra_2014.temp_gastos WHERE id_gasto = $id";
				$conecta->consulta($qd_temp_ajuste);
			}

			echo "Datos eliminados con éxito|$usuario_cap|$fecha_cap";
		break;
		case 130:
			$id = addslashes(trim($_POST["id_ajuste"]));
			$usuario_cap = addslashes(trim($_POST["usuario_cap"]));
			$fec_carga = addslashes(trim($_POST["fec_carga"]));

			$qd_temp_ajuste = "Delete FROM carobra_2014.temp_gastos WHERE id_gasto = $id";
			$conecta->consulta($qd_temp_ajuste);
			echo "Datos eliminados con exito|$usuario_cap|$fec_carga";
		break;
		case 131:
			$tipo = addslashes(trim($_POST["tipo"]));
			$valor = addslashes(trim($_POST["valor"]));

			switch ($tipo) 
			{
				case 'a':
					$sql="Select areas_negocio from socio_area_negocio where clave='$valor'";
					$bd=$conecta->consulta($sql);
					$tb=mysqli_fetch_array($bd);
					echo $tb["areas_negocio"];
				break;
				case 'g':
					$sql="Select distinct sc,brocker from estruc_empleados where gerencia='$valor'";
					$bd=$conecta->consulta($sql);
					$tb=mysqli_fetch_array($bd);
					echo $tb["sc"] . "|" . $tb["brocker"];
				break;
			}
		break;
		case 132:
			$folio = addslashes(trim($_POST["folio"]));
			$socio = addslashes(trim($_POST["socio"]));
			$new_socio = addslashes(trim($_POST["sel-socio"]));
			$gerencia = addslashes(trim($_POST["txt-gerencia"]));
			$tipo_ajuste = addslashes(trim($_POST["sel-tipo-ajuste"]));
			$afecta_sb = addslashes(trim($_POST["sel-afecta-sb"]));
			$empleado = addslashes(trim($_POST["txt-empleado"]));
			$motivo = addslashes(trim($_POST["txt-motivo"]));
			$cc = addslashes(trim($_POST["txt-cc"]));
			$partida = addslashes(trim($_POST["txt-partida"]));
			$no_gasto = addslashes(trim($_POST["txt-no-gasto"]));
			$ajuste=addslashes(trim($_POST["txt-ajuste"]));

			$sql_empleado="";
			$sql_ajuste="";
			$sql_gerencia="";
			$sql_socio="";
			$sql_clave='clave';
			$sql_ins_socio="";
			$sql_ins_gerencia='id_gerencia';
			$sql_ins_cc='cen_cost';
			$sql_ins_partida='partida';
			$sql_ins_ajuste='ajuste';
			if($ajuste != "")
			{
				$sig=1;
				if(substr($ajuste, 0, 1)=="(")
					$sig=-1;
				$car = ["(",")","%","$",","];
				$ajuste=str_replace($car,"",$ajuste);	
				$ajuste*=$sig;
				$sql_ajuste=", ajuste=$ajuste";
				$sql_ins_ajuste=$ajuste;
			}

			if($empleado != "")
			{
				$sql="Select clave from carobra.usuarios where nombre='$empleado'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$clave=$tb["clave"];
				$sql_empleado=", clave='$clave'";
				$sql_clave="'$clave'";
			}

			if($gerencia != "")
			{
				$sql="Select id_gerencia from gerencias_negocio where gerencia='$gerencia'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$id_gerencia=$tb["id_gerencia"];
				$sql_gerencia=", id_gerencia=$id_gerencia";
				$sql_ins_gerencia=$id_gerencia;
			}

			if($cc != "")
			{
				$sql="Select id_cc from centros_costo_area where nombre_cc='$cc'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$id_cc=$tb["id_cc"];
				$sql_ins_cc="'$id_cc'";
			}

			if($partida != "")
			{
				$sql="Select no_partida from partidas_area_negocio where partida='$partida'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$id_partida=$tb["no_partida"];
				$sql_ins_partida="'$id_partida'";
			}

			$t2="";
			if($socio != $new_socio and $new_socio !='')
			{
				$sql="Select areas_negocio from socio_area_negocio where clave='$new_socio'";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$t2=substr($tb["areas_negocio"], 0, 1);
				$socio=$new_socio;
				$sql_ins_socio="'$socio'";
			}

			if($socio !='')
			{
				$sql_socio=", socio='$socio'";
			}

			$p=explode("-",$folio);
			$t=$p[0];
			$id=$p[1];
			$q_in="id_ajuste";
			switch ($t) 
			{
				case 'C': $bd_a="carobra_2014.ajustes_corporativo"; break;
				case 'H': $bd_a="carobra_2014.ajustes_hipotecario"; break;
				case 'I': 
					$q_in="id_insuficiencia";
					$bd_a="carobra_2014.insuficiencias";
				break;
				case 'P': $bd_a="carobra_2014.ajustes_previsional"; break;
				case 'S': $bd_a="carobra_2014.ajustes_seguros"; break;
				case 'T': $bd_a="carobra_2014.ajustes_tarjeta"; break;
			}

			if($t2 != $t and $t2 != "")
			{
				$q_in2="id_ajuste";
				switch ($t2) 
				{
					case 'C': $bd_a2="carobra_2014.ajustes_corporativo"; break;
					case 'H': $bd_a2="carobra_2014.ajustes_hipotecario"; break;
					case 'I': 
						$q_in="id_insuficiencia";
						$bd_a2="carobra_2014.insuficiencias";
					break;
					case 'P': $bd_a2="carobra_2014.ajustes_previsional"; break;
					case 'S': $bd_a2="carobra_2014.ajustes_seguros"; break;
					case 'T': $bd_a2="carobra_2014.ajustes_tarjeta"; break;
				}

				If($t2 != "I")
				{
					$sql="Insert into $bd_a2
						  Select 0,responsable,$sql_clave,fecha,fec_pago,$sql_ins_socio,$sql_ins_gerencia,anio,mes,
						  		 $sql_ins_ajuste,'$motivo',estatus,$sql_ins_cc,$sql_ins_partida,$tipo_ajuste,
						  		 $afecta_sb,'$no_gasto','O'
						  from $bd_a where $q_in=$id";
					$bd = $conecta->prepare($sql);
			        if ($bd->execute()) 
			        {
				        if ($bd->affected_rows > 0)
				        {
				            $folio= $t2 . "-" . $conecta->obtener_id();
				            $conecta->consulta("Delete from  $bd_a  where $q_in=$id");
				            echo "Este ajuste con folio $t-$id, al cambiar de área de negocio tiene ahora nuevo folio: $folio|$folio";
				        }
				        else
				    		echo "Algo salió mal, no se generó ningún cambio. Vuelve a intentarlo por favor|";
				    }
				}
			}
			else
			{
				$sql="Update $bd_a
					  set tipo_ajuste=$tipo_ajuste, afecta_sb=$afecta_sb, 
					  	  no_gasto='$no_gasto', cen_cost='$id_cc', partida='$id_partida', motivo='$motivo'
					  	  $sql_empleado $sql_ajuste $sql_gerencia $sql_socio
					  where $q_in=$id";
				$conecta->consulta($sql);
				echo "Actualización exitosa|";
			}
		break;
		case 133:
			$folio = addslashes(trim($_POST["folio"]));

			$p=explode("-",$folio);
			$t=$p[0];
			$id=$p[1];
			$q_in="id_ajuste";
			switch ($t) 
			{
				case 'C': $bd_a="carobra_2014.ajustes_corporativo"; break;
				case 'H': $bd_a="carobra_2014.ajustes_hipotecario"; break;
				case 'I': 
					$q_in="id_insuficiencia";
					$bd_a="carobra_2014.insuficiencias";
				break;
				case 'P': $bd_a="carobra_2014.ajustes_previsional"; break;
				case 'S': $bd_a="carobra_2014.ajustes_seguros"; break;
				case 'T': $bd_a="carobra_2014.ajustes_tarjeta"; break;
			}

			$conecta->consulta("Delete from $bd_a where $q_in=$id");
			echo "Ajuste eliminado con éxito";
		break;
		case 134:
			$area = addslashes(trim($_POST["area"]));

			$socios=array();
			$sql="Select clave,nombre from socio_area_negocio";
			if($area!="")
				$sql.=" where areas_negocio='$area'";
			$sql.=" order by clave";

			$bd=$conecta->consulta($sql);

			while($tb=mysqli_fetch_array($bd))
			{
				$socios[] = [
							    "clave" => $tb["clave"],
							    "nombre" => $tb["nombre"]
				  			];
			}
			echo json_encode($socios);
		break;

		case 135:
			$nivel = addslashes(trim($_POST["nivel"]));
			$sc = addslashes(trim($_POST["sc"]));
			$sb = addslashes(trim($_POST["sb"]));
			$gerencia = addslashes(trim($_POST["gerencia"]));
			$asesor = addslashes(trim($_POST["asesor"]));
			
			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			else
				$nombre=$_SESSION["nombre"];

			$pasa=1;
			if($sc!='')
			{
				if($nivel==13)
				{
					if($nombre!=$sc)
						$pasa=0;
				}
				else
					$pasa=0;
			}

			if($sb!='')
			{
				if($nivel==13)
				{
					if($nombre!=$sc)
						$pasa=0;
				}
				else
				{
					if($nivel==5)
					{
						if($nombre!=$sb)
							$pasa=0;
					}
					else
						$pasa=0;
				}						
			}

		break;
		case 136:
			$id = addslashes(trim($_POST["id"]));
			if($id!=0)
			{
				$sql="Select 0 as id_pagina,' Todas' as pagina
					  union
					  Select id_pagina,nom_pagina from paginas where id_modulo=$id 
					  order by pagina";

				$bd=$conecta->consulta($sql);
				if ($bd->num_rows != 0)
				{
					while($tb=mysqli_fetch_array($bd))
					{
						$paginas[] = array("id_pagina" => $tb['id_pagina'],
									       "pagina" => $tb['pagina']
									       );
					}
					echo json_encode($paginas);
				}
			}
		break;
		case 137:
			$sql="Select 0 as id_modulo,' Todos' as modulo
				  union
				  Select * from modulos order by modulo";

			$bd=$conecta->consulta($sql);
			if ($bd->num_rows != 0)
			{
				while($tb=mysqli_fetch_array($bd))
				{
					$modulos[] = array("id_modulo" => $tb['id_modulo'],
								       "modulo" => $tb['modulo']
								       );
				}
				echo json_encode($modulos);
			}
		break;
		case 138:
			$id = addslashes(trim($_POST["id"]));
			$fi = addslashes(trim($_POST["fi"]));
			$ff = addslashes(trim($_POST["ff"]));
			$clave = addslashes(trim($_POST["clave"]));
			$rango = addslashes(trim($_POST["rango"]));
			$concepto=addslashes(trim($_POST["concepto"]));

			if($fi=="")
				$var_detalle=' data-clave="' . $clave . '" data-fi="" data-ff="" data-rango="' . $rango . '" data-id="' . $id . '"';
			else
				$var_detalle=' data-clave="' . $clave . '" data-fi="' . $fec_ini . '" data-ff="' . $fec_fin . '" data-rango="" data-id="' . $id . '"';

			if($id=="bc")
			{
				$ids = ["bcc", "bcg", "bcs", "bcsi"];
				$conceptos=["BONOS COMISIONISTA","BONOS GERENTES","BONOS SOCIOS","BONOS SOCIOS"];
			}
			else
			{
				$ids=[]; $conceptos=[];
				$ids[]=$id;
				$conceptos[]=$concepto;
			}
			$i=0;
			$class_header='class="header"';
			$excepciones= ['folio','edad','nap','comision','com. socio','com. anticipo bono','com. bono cert','avs','registros'];

			$pc=0;
			while($i < count($ids))
			{
				$id=$ids[$i];
				$concepto=$conceptos[$i];
				$sql="CALL detalle_pagos('$id','$clave','$fi','$ff','$rango')";

				$bd=$conecta->consulta($sql);


				if ($bd->num_rows != 0)
				{
					$rows = [];
					while ($tb = $bd->fetch_assoc()) 
					{
					    $rows[] = $tb;
					}

					$filtered = array_values(array_filter($rows));
					if (!empty($filtered)) 
					{
						$headers = [];
						$headers = array_keys($filtered[0]);
					}
					$style="";
					if($id=="csc" or $id=='bcc' or $id=='bcs' or $id=='cc' or $id=='cg')
						$style='style="justify-content: flex-start;"';

					if (empty($filtered)): 
?>
						<div class="alert alert-info"></div>
<?php
					else: 
?>
	          			<div <?php echo $class_header; ?>>
	          				<h4 class="mb-3"><?php echo $concepto; ?></h4>
<?php
							if($pc==0)
							{
		          				echo '<img src="./img/excel.png" class="btn-excel" id="btn-excel-detalle"' . $var_detalle . ' width="35px">';
?>
								<button class="btn-cerrar-detalle" title="Cerrar">&times;</button>
<?php
								$pc=1;
							}

?>
	          			</div>
	          			<div class="cont-tabla-detalle" <?php echo $style; ?>>
					      <table class="table table-striped table-bordered det-pagos">
					        <thead class="table-dark">
					          <tr>
					            <?php foreach ($headers as $h): ?>
					              <th scope="col"><?php echo htmlspecialchars($h); ?></th>
					            <?php endforeach; ?>
					          </tr>
					        </thead>
					        <tbody>
								<?php foreach ($filtered as $row): ?>
								  <tr>
								    <?php foreach ($headers as $h): ?>
								      <?php
								        $raw = $row[$h] ?? '';
								        $class = 'text-left';
								        if (is_numeric($raw)) 
								        {
								            $numVal = $raw;
								            $tit=strtolower($h);
								            if (in_array($tit, $excepciones))
								            	$val=$numVal;
								            else
									            $val = '$ ' . number_format($numVal, 2);
								            $class = 'text-right';
								            if ($numVal < 0) 
								            	$class .= ' text-danger';
								        } 
								        else 
								            $val = htmlspecialchars((string)$raw);
								        echo '<td class="'.$class.'">'.$val.'</td>';
								      ?>
								    <?php endforeach; ?>
								  </tr>
								<?php endforeach; ?>
					        </tbody>
					      </table>
					    </div>
<?php 
						$class_header='class="header mt-5"';
					endif;
				}
				$i++;
			}
		break;
		case 139:
			$clave = addslashes(trim($_POST["clave"]));
			$nombre= addslashes(trim($_POST["nombre"]));
			$id_nivel = addslashes(trim($_POST["sel-nivel"]));
			$gerencia = addslashes(trim($_POST["sel-gerencia"]));

			$administra=0;
			if ($_POST["chk-administra"]=="on") 
				$administra=1;

			$analista=0;
			if ($_POST["chk-analista"]=="on") 
				$analista=1;

			$bd=$conecta->consulta("Select 1 from invikta.empleados where clave='$clave'");
			if ($bd->num_rows != 0)
				$sql="Update invikta.empleados 
					  set id_gerencia=$gerencia, id_nivel=$id_nivel, administra=$administra, analista=$analista
					  where clave='$clave'";
			else
				$sql="Insert into invikta.empleados values('$clave','$nombre',$gerencia,$id_nivel,$administra,$analista)";
			$conecta->consulta($sql);

			$sql="Insert INTO permisos
				  Select '$clave', 2104
				  WHERE NOT EXISTS (SELECT 1 FROM permisos
							        WHERE clave_usuario = '$clave'
							        	AND id_pagina = 2014)
				  union all
				  Select '$clave', 2112
				  WHERE NOT EXISTS (SELECT 1 FROM permisos
							        WHERE clave_usuario = '$clave'
							        	AND id_pagina = 2112)";
			$conecta->consulta($sql);

			echo "Datos del empleado actualizados con éxito";
		break;
		case 140:
			$id = addslashes(intval($_POST['id'] ?? 0));

			$sql = "Select id_tabla_bonos, rango_inf, rango_sup, porcentaje FROM invikta.tabla_bonos 
			        WHERE id_tabla_comision = $id
			        ORDER BY id_tabla_bonos ASC";

			$bd=$conecta->consulta($sql);
			$bonos = [];
			if ($bd->num_rows != 0)
			{
				while ($tb = $bd->fetch_assoc()) 
				{
				    $bonos[] = 	[
							        "id_tabla_bonos" => $tb["id_tabla_bonos"],
							        "rango_inf"      => "$" . number_format($tb["rango_inf"], 2, ".", ","),
							        "rango_sup"      => "$" . number_format($tb["rango_sup"], 2, ".", ","),
							        "porcentaje"     => number_format($tb["porcentaje"], 6, ".", "")
				    			];
				}				
				header("Content-Type: application/json");
				echo json_encode($bonos);
			}
			else
			{
			    header("Content-Type: application/json");
			    echo json_encode([]);
			}
		break;
		case 141:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$id_bono = addslashes(trim($_POST["id_bono"]));
			$ri = addslashes(trim($_POST["ri"]));
			$rs = addslashes(trim($_POST["rs"]));
			$por = addslashes(trim($_POST["por"]));

			$ri=str_replace(['$', ','],'',$ri);
			$rs=str_replace(['$', ','],'',$rs);

			if($id_bono==0)
			{
				$sql="Select ifnull(max(id_tabla_bonos),0) + 1 as id_tabla_bonos from invikta.tabla_bonos
					  where id_tabla_comision=$id_tabla";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$id_bono=$tb["id_tabla_bonos"];

				$sql="Insert into invikta.tabla_bonos values($id_bono,$id_tabla,$ri,$rs,$por);";
			}
			else
				$sql="Update invikta.tabla_bonos set rango_inf=$ri, rango_sup=$rs, porcentaje=$por
					  where id_tabla_comision=$id_tabla and id_tabla_bonos=$id_bono";
			$conecta->consulta($sql);
			echo "Rango guaerdado con éxito";
		break;
		case 142:
			$d = explode("|",addslashes(trim($_POST["elimina"])));
			$gerencia=$d[0];
			$promotor=$d[1];

			$sql="Select clave from carobra.usuarios where nombre='$promotor'";
			$bd=$conecta->consulta($sql);
			$tb=mysqli_fetch_array($bd);
			$clave=$tb["clave"];
			$sql="Delete from estruc_empleados_otros where gerencia='$gerencia' and nivel='promotor' and clave='$clave'";
			$conecta->consulta($sql);

			echo "Promotor eliminado con éxito";
		break;

		case 143:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$id_bono = addslashes(trim($_POST["id_bono"]));

			$conecta->consulta("Delete from invikta.tabla_bonos where id_tabla_comision=$id_tabla and id_tabla_bonos=$id_bono");

			echo "Promotor eliminado con éxito";
		break;
		case 144:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$valor = addslashes(trim($_POST["valor"]));

			$conecta->consulta("Update invikta.tabla_comision set activa=$valor where id_tabla_comision=$id_tabla");

			echo "Tabla actualizada con éxito";
		break;
		case 145:
			$nivel = addslashes(trim($_POST["nivel"]));

			$data = [];

			if ($nivel > 0) 
			{
				$sql="Select id_tabla_comision, nom_tabla_comision FROM invikta.tabla_comision 
					  WHERE nivel_usuario = $nivel and activa=1";
			    $bd = $conecta->consulta($sql);
			    while ($tb = $bd->fetch_assoc()) 
			    {
			        $data[] = [
			            'id' => $tb['id_tabla_comision'],
			            'nombre' => $tb['nom_tabla_comision']
			        ];
			    }
			}

			header('Content-Type: application/json');
			echo json_encode($data);
		break;
		case 146:
			$tipo = addslashes(trim($_POST["tipo"]));
			$valor = addslashes(trim($_POST["valor"]));
			$nivel = addslashes(trim($_POST["nivel"]));
			$tabla = addslashes(trim($_POST["tabla"]));

			switch ($tipo) 
			{
				case 'brocker':
					$sql="Insert into invikta.empleados_tablas
						  Select clave,$tabla,1
						  from invikta.empleados e
							inner join invikta.gerencias g on (e.id_gerencia=g.id_gerencia)
						  where id_brocker=$valor and id_nivel=$nivel";

				break;
				case 'gerencia':
					$sql="Insert into invikta.empleados_tablas
						  Select clave,$tabla,1
						  from invikta.empleados e
							inner join invikta.gerencias g on (e.id_gerencia=g.id_gerencia)
						  where e.id_gerencia=$valor and id_nivel=$nivel";
				break;
				case 'empleado':
					$sql="Insert into invikta.empleados_tablas
						  Select clave,$tabla,1 from invikta.empleados e
						  where nombre='$valor'";
				break;
				default:
					$sql="Insert into invikta.empleados_tablas
						  Select clave,$tabla,1 from invikta.empleados e
						  where id_nivel=$nivel";
				break;
			}
			$conecta->consulta($sql);

			echo "Asignación exitosa";
		break;
		case 147:
			$folio = addslashes(trim($_POST["folio"]));
			$acompa = addslashes(trim($_POST["acompa"]));
			$asesor = addslashes(trim($_POST["asesor"]));
			$analista = addslashes(trim($_POST["analista"]));
			$confirma = addslashes(trim($_POST["confirma"]));
			$prop_nap = addslashes(trim($_POST["prop_nap"]));

			$sql="";
			$bd=$conecta->consulta("Select clave,id_estatus_anticipo from invikta.prod_inv where folio='$folio'");
			if ($bd->num_rows == 0)
				$sql="Insert into invikta.prod_inv
					  Select '$folio','$analista',clave,$confirma,0,'','',0,0,$acompa,'$prop_nap','',''
					  from invikta.empleados where nombre='$asesor'";
			else
			{
				$tb=mysqli_fetch_array($bd);
				$estatus_ant=$tb["id_estatus_anticipo"];
				$clave_bd=$tb["clave"];

				if($asesor!='')
				{
					$bd=$conecta->consulta("Select clave from invikta.empleados where nombre='$asesor'");
					$tb=mysqli_fetch_array($bd);
					$clave=$tb["clave"];					
				}
				else
					$clave= $clave_bd;

				if($clave != $clave_bd)
				{
					if($estatus_ant==0)
					  	$sql="Update invikta.prod_inv
					  		  set clave='$clave', confirma=$confirma,acompa=$acompa
					  		  where folio='$folio'";
					else
						echo "No puede asignarse otro asesor a este folio por que ya cuenta con pago de anticipo o está en proceso de pago";
				}
				else
					$sql="Update invikta.prod_inv
						  set confirma=$confirma, acompa=$acompa
						  where folio='$folio'";
			}
			if($sql !="")
			{
				$conecta->consulta($sql);
				echo "Folio asignado con éxito";				
			}
		break;
		case 148:
			$id_pago = addslashes(trim($_POST["id_pago"]));
			$conecta->consulta("Update plan_ideal.pagos set envio_recibo=2 where id_pago=$id_pago");
			echo "Envio de email bloqueado";
		break;
		case 149:
			$empleado = addslashes(trim($_POST["empleado"]));

			$sql="Select concat(gerencia,' (',e.id_gerencia,')') as gerencia 
				  from (Select id_gerencia from invikta.empleados where nombre='$empleado') e
				  	join gerencias g on (e.id_gerencia=g.id_gerencia)"; 
			$bd=$conecta->consulta($sql);
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				echo $tb["gerencia"];
			}
			else
				echo "";
		break;
		case 150:
			$monto = addslashes(trim($_POST["monto"]));
				
			echo "$" . number_format($monto, 2, '.', ',');
		break;
		case 151:
			$id = addslashes(trim($_POST["id_ajuste"]));
			$gerencia = addslashes(trim($_POST["gerencia"]));
			$empleado = addslashes(trim($_POST["txt-empleado"]));
			$motivo = addslashes(trim($_POST["txt-motivo"]));
			$monto = addslashes(trim($_POST["txt-monto"]));

			$monto=str_replace(['$', ','],"",$monto);

			$g=explode("(", $gerencia);
			$id_gerencia= str_replace(")","", $g[1]);

			$bd=$conecta->consulta("Select clave from carobra.usuarios where nombre='" . utf8_encode($_SESSION["nombre"]) . "'");
			$tb=mysqli_fetch_array($bd);
			$responsable=$tb["clave"];

			if($id=="")
			{
				$bd=$conecta->consulta("Select clave from invikta.empleados where nombre='" . utf8_encode($empleado) . "'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$clave=$tb["clave"];

					$mes=date("m");
					$anio=date("Y");
					$fecha=date("d/m/Y");

					$sql="Insert into invikta.ajustes values(0,'$responsable','$clave','$fecha','',$id_gerencia,$anio,$mes,$monto,'$motivo',0)";
					$conecta->consulta($sql);
					echo "Ajuste generado con éxito";
				}
				else
					echo "El empleado seleccionado no existe en Invikta";
			}
			else
			{
				$bd=$conecta->consulta("Select clave from invikta.empleados where nombre='" . utf8_encode($empleado) . "'");
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$clave=$tb["clave"];

					$sql="Update invikta.ajustes 
						  set clave='$clave',responsable='$responsable',id_gerencia=$id_gerencia,motivo='$motivo', ajuste=$monto
						  where id_ajuste=$id";
					$conecta->consulta($sql);
					echo "Ajuste actualizado con éxito";
				}
			}
		break;
		case 152:
			$v= addslashes(trim($_POST["p"]));
			$valor = addslashes(trim($_POST["valor"]));

			$p=explode("|", $valor);
			$pagina=$p[0];
			$clave=$p[1];

			$conecta->consulta("Delete from permisos where clave_usuario='$clave' and id_pagina=$pagina");

			if($v==1)
				$conecta->consulta("Insert into permisos values('$clave',$pagina);");
		break;
		case 153:
			$nivel= addslashes(trim($_POST["nivel"]));
			$tipo= addslashes(trim($_POST["tipo"]));
			$clave = addslashes(trim($_POST["c"]));
			$fec_pago = addslashes(trim($_POST["f"]));

			$p=-1;
			if($nivel==3)
				$p=1;
			switch ($tipo) 
			{
				case "anti":
					$sql="Select p.folio,trabajador as Cliente,monto_anticipo as Anticipo
							from (SELECT * FROM invikta.prod_inv WHERE clave='$clave' and fec_pago_ant='$fec_pago') p
								join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
								join carobra.usuarios u on (p.clave=u.clave)";
					$moneda=['anticipo'];
				break;
				case "dant": 
					$sql="Select p.folio,trabajador as Cliente,pi.fec_pago_ant as 'Fec. pago',monto_anticipo * -1 as 'Desc. anticipo'
							from (SELECT * FROM invikta.pagos_comision WHERE clave_asesor='$clave' and fec_pago='$fec_pago') p
								join invikta.prod_inv pi on (p.folio=pi.folio)
								join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)";
					$moneda=['desc. anticipo'];
				 break;
				case "acom": 
					$sql="Select p.folio,trabajador as Cliente,nombre as asesor,acompa as Acompañamiento
							from (SELECT * FROM invikta.prod_inv WHERE prop_nap='$clave' and fec_pago_acompa='$fec_pago') p
								join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
								join carobra.usuarios u on (p.clave=u.clave)";
					$moneda=['acompañamiento'];
				break;
				case "dacom": 
					$sql="Select p.folio,trabajador as Cliente,nombre as asesor,acompa * -1 as 'Desc. Acompañamiento'
							from (SELECT * FROM invikta.prod_inv WHERE clave='$clave' and fec_pago_acompa='$fec_pago') p
								join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
								join carobra.usuarios u on (p.clave=u.clave)";
					$moneda=['Desc. Acompañamiento'];
				break;
				case "case": 
					$sql="Select p.folio as Folio,fec_traspaso as 'Fec. traspaso',nombre as Asesor,saldo_real as 'Saldo real',
								 estatus as Estatus,edad as Edad,
								 if(movilidad=1,'Mayor o igual a 18 meses','Menor a 18 meses') as Movilidad,
								 tc_asesor as 'Tabla com.',por_asesor as '%',
								 case when ei.id_nivel=3 then com_asesor
									else case when p.clave_asesor='$clave' then 0
											else com_asesor * -1
										 end
								 end as 'Comision'
						  from (Select clave,id_nivel from invikta.empleados where clave='$clave') ei
						  	join (SELECT * FROM invikta.pagos_comision WHERE fec_pago='$fec_pago') p 
						  		on (ei.clave= case when ei.id_nivel=3 then clave_asesor
						  						   when ei.id_nivel=4 then clave_gerente
						  						   when ei.id_nivel=5 then clave_brocker
						  					  end)
						  	join invikta.prod_inv pi on (p.folio=pi.folio)
						  	join carobra_2014.produccion pr on (p.folio=pr.folio)
						  	join carobra.usuarios u on (p.clave_asesor=u.clave)
						  	join carobra_2014.estatus e on (pr.id_estatus_prod=e.id_estatus)";
					$moneda=['comision'];
				break;
				case "cger":
					$sql="Select p.folio as Folio,fec_traspaso as 'Fec. traspaso',nombre as Asesor,saldo_real as 'Saldo real',
								 estatus as Estatus,edad as Edad,
								 if(movilidad=1,'Mayor o igual a 18 meses','Menor a 18 meses') as Movilidad,
								 tc_gerente as 'Tabla com.',por_gerente as '%',com_gerente as 'Comision'
						  from (SELECT * FROM invikta.pagos_comision WHERE clave_gerente='$clave' and fec_pago='$fec_pago') p
						  	join invikta.prod_inv pi on (p.folio=pi.folio)
						  	join carobra_2014.produccion pr on (p.folio=pr.folio)
						  	join carobra.usuarios u on (p.clave_asesor=u.clave)
						  	join carobra_2014.estatus e on (pr.id_estatus_prod=e.id_estatus)";
					$moneda=['comision'];
				break;
				case "cbro": $titulo="Detalle de Comisiones brocker"; break;
				case "ajus":
					$sql="Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from invikta.ajustes where clave='$clave' and fec_pago='$fec_pago'";
					$moneda=['ajuste'];
				 break;
				case "ajus_car":
					$sql="Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from carobra_2014.ajustes_previsional where clave='$clave' and fec_pago='$fec_pago'
						  union all
						  Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from carobra_2014.ajustes_corporativo where clave='$clave' and fec_pago='$fec_pago'
						  union all
						  Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from carobra_2014.ajustes_hipotecario where clave='$clave' and fec_pago='$fec_pago'
						  union all
						  Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from carobra_2014.ajustes_seguros where clave='$clave' and fec_pago='$fec_pago'
						  union all
						  Select id_ajuste as ID,motivo as Motivo,ajuste as Ajuste 
						  from carobra_2014.ajustes_tarjeta where clave='$clave' and fec_pago='$fec_pago'
						  union all
						  Select id_insuficiencia,motivo,ajuste
						  from carobra_2014.insuficiencias where clave='$clave' and fec_pago='$fec_pago'";
					$moneda=['ajuste'];
				 break;
			}
	        $bd = $conecta->consulta($sql);
	        $rows = [];
	        while ($tb = $bd->fetch_assoc()) 
	        {
	            $rows[] = $tb;
	        }

	        $filtered = array_values(array_filter($rows));
	        if (empty($filtered)) return;

	        $headers = array_keys($filtered[0]);
?>
	        <table class="table table-bordered table-striped t-permisos">
	          	<thead>
	            	<tr class='encabezado'>
<?php
			        	foreach ($headers as $i => $h) 
			        	{
			            	echo "<th class='f-small'>" . htmlspecialchars($h) . "</th>";
			        	}
?>
					</tr>
	          	</thead>
	          	<tbody>
<?php
		        	foreach ($filtered as $row) 
		        	{
		        		echo "<tr>";
			            	foreach ($headers as $h) 
			            	{
			                	$raw = $row[$h] ?? '';
			                	$tit = strtolower($h);
                    			if (!in_array($tit, array_map('strtolower', $moneda)))
			                		echo "<td class='f-small'>" . htmlspecialchars($raw) . "</td>";
			                	else
			                		echo "<td class='f-small text-right'>$" . number_format(htmlspecialchars($raw), 2, '.', ',') . "</td>";
			            	}
			        	echo "</tr>";
			    	}
?>
			    </tbody>
	        </table>
<?php
		break;
		case 154:
			$clave = addslashes(trim($_POST["clave"]));
			$nombre= addslashes(trim($_POST["nombre"]));
			$id_nivel = addslashes(trim($_POST["sel-nivel"]));
			$gerencia = addslashes(trim($_POST["sel-gerencia"]));

			$administra=0;
			if ($_POST["chk-administra"]=="on") 
				$administra=1;

			$analista=0;
			if ($_POST["chk-analista"]=="on") 
				$analista=1;

			$bd=$conecta->consulta("Select 1 from feram.empleados where clave='$clave'");
			if ($bd->num_rows != 0)
				$sql="Update feram.empleados 
					  set id_gerencia=$gerencia, id_nivel=$id_nivel, administra=$administra, analista=$analista
					  where clave='$clave'";
			else
				$sql="Insert into feram.empleados values('$clave','$nombre',$gerencia,$id_nivel,$administra,$analista)";
			$conecta->consulta($sql);
			echo "Datos del empleado actualizados con éxito";
		break;
		case 155:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$valor = addslashes(trim($_POST["valor"]));

			$conecta->consulta("Update feram.tabla_comision set activa=$valor where id_tabla_comision=$id_tabla");

			echo "Tabla actualizada con éxito";
		break;
		case 156:
			$id = addslashes(intval($_POST['id'] ?? 0));

			$sql = "Select id_tabla_bonos, rango_inf, rango_sup, porcentaje FROM feram.tabla_bonos 
			        WHERE id_tabla_comision = $id
			        ORDER BY id_tabla_bonos ASC";

			$bd=$conecta->consulta($sql);
			$bonos = [];
			if ($bd->num_rows != 0)
			{
				while ($tb = $bd->fetch_assoc()) 
				{
				    $bonos[] = 	[
							        "id_tabla_bonos" => $tb["id_tabla_bonos"],
							        "rango_inf"      => "$" . number_format($tb["rango_inf"], 2, ".", ","),
							        "rango_sup"      => "$" . number_format($tb["rango_sup"], 2, ".", ","),
							        "porcentaje"     => number_format($tb["porcentaje"], 6, ".", "")
				    			];
				}				
				header("Content-Type: application/json");
				echo json_encode($bonos);
			}
			else
			{
			    header("Content-Type: application/json");
			    echo json_encode([]);
			}
		break;
		case 157:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$id_bono = addslashes(trim($_POST["id_bono"]));

			$conecta->consulta("Delete from feram.tabla_bonos where id_tabla_comision=$id_tabla and id_tabla_bonos=$id_bono");

			echo "Promotor eliminado con éxito";
		break;
		case 158:
			$id_tabla = addslashes(trim($_POST["id_tabla"]));
			$id_bono = addslashes(trim($_POST["id_bono"]));
			$ri = addslashes(trim($_POST["ri"]));
			$rs = addslashes(trim($_POST["rs"]));
			$por = addslashes(trim($_POST["por"]));

			$ri=str_replace(['$', ','],'',$ri);
			$rs=str_replace(['$', ','],'',$rs);

			if($id_bono==0)
			{
				$sql="Select ifnull(max(id_tabla_bonos),0) + 1 as id_tabla_bonos from feram.tabla_bonos
					  where id_tabla_comision=$id_tabla";
				$bd=$conecta->consulta($sql);
				$tb=mysqli_fetch_array($bd);
				$id_bono=$tb["id_tabla_bonos"];

				$sql="Insert into feram.tabla_bonos values($id_bono,$id_tabla,$ri,$rs,$por);";
			}
			else
				$sql="Update feram.tabla_bonos set rango_inf=$ri, rango_sup=$rs, porcentaje=$por
					  where id_tabla_comision=$id_tabla and id_tabla_bonos=$id_bono";
			$conecta->consulta($sql);
			echo "Rango guaerdado con éxito";
		break;
		case 159:
			$motivo = trim($_POST["motivo"] ?? "");
			$partida = trim($_POST["partida"] ?? "");

			if ($motivo == "")
			{
				header("Content-Type: application/json");
				echo json_encode([
					"estatus" => "ERROR",
					"mensaje" => "Por favor capture el nuevo motivo de préstamo"
				]);
				break;
			}

			if (function_exists("mb_strtoupper"))
			{
				$motivo = mb_strtoupper($motivo, "UTF-8");
				$partida = mb_strtoupper($partida, "UTF-8");
			}
			else
			{
				$motivo = strtoupper($motivo);
				$partida = strtoupper($partida);
			}

			$motivo_sql = addslashes($motivo);
			if ($partida != "" && preg_match('/\(([^)]+)\)/', $partida, $matches))
				$partida = $matches[1];

			$partida_sql = addslashes($partida);
			$conecta->consulta("Insert into motivos_prestamos (motivo, partida) VALUES ('$motivo_sql', '$partida_sql')");

			$motivos = [];
			$bd = $conecta->consulta("Select motivo, partida FROM motivos_prestamos ORDER BY motivo");
			if ($bd->num_rows != 0)
			{
				while ($tb = mysqli_fetch_assoc($bd))
				{
					$motivos[] = [
						"motivo" => $tb["motivo"],
						"partida" => $tb["partida"]
					];
				}
			}

			header("Content-Type: application/json");
			echo json_encode([
				"estatus" => "OK",
				"mensaje" => "Motivo agregado con éxito",
				"motivo" => $motivo,
				"partida" => $partida,
				"motivos" => $motivos
			]);
		break;

		case 160:
			$empleado = addslashes(trim($_POST["empleado"]));
			$response = [];
			$query = "SELECT g.gerencia, g.gerente FROM invikta.empleados e
						LEFT JOIN invikta.gerencias g on e.id_gerencia = g.id_gerencia
						where e.nombre =  '$empleado'";
			$bd = $conecta->consulta($query);
			
			if ($bd->num_rows > 0) 
			{
				while ($row = mysqli_fetch_assoc($bd)) 
				{
					$response[] = $row;
				}
			} 
			else 
			{
				$response = [];
			}


			header('Content-Type: application/json');
			echo json_encode($response);

		break;

		case 161:
			$empleado = addslashes(trim($_POST["empleado"]));
			$fec_prestamo = addslashes(trim($_POST["fec_prestamo"]));
			$monto = addslashes(trim($_POST["monto"]));
			$no_pagos = addslashes(trim($_POST["no_pagos"]));
			$monto_pago = addslashes(trim($_POST["monto_pago"]));
			$motivo = addslashes(trim($_POST["motivo"]));
			$responsable = addslashes(trim($_POST["responsable"]));
			$periodo = addslashes(trim($_POST["periodo"]));
			$fec_desc = addslashes(trim($_POST["fec_desc"]));
			$observa = addslashes(trim($_POST["observa"]));
			$fechaActual = new DateTime("now", new DateTimeZone("America/Mexico_City"));
			$fec_cap = $fechaActual->format("d/m/Y");
			$clave = "";

			if (!empty($fec_prestamo)) 
				$fec_prestamo = DateTime::createFromFormat('Y-m-d', $fec_prestamo)->format('d/m/Y');
			else 
				$fec_prestamo = '';

			if (!empty($fec_desc)) 
				$fec_desc = DateTime::createFromFormat('Y-m-d', $fec_desc)->format('d/m/Y');
			else 
				$fec_desc = '';

			if (empty($monto) || $monto === '$0.00') 
				$monto_double = 0.0;
			else 
			{
				$monto_limpio = str_replace(['$', ','], '', $monto);
				$monto_double = (double)$monto_limpio;
			}

			if (empty($monto_pago) || $monto_pago === '$0.00') 
				$monto_pago_double = 0.0;
			else 
			{
				$monto_pago_limpio = str_replace(['$', ','], '', $monto_pago);
				$monto_pago_double = (double)$monto_pago_limpio;
			}

			if ($empleado == "") 
				$clave = "";
			else 
			{
				$qusuario = "Select clave FROM invikta.empleados WHERE nombre = '$empleado'";
				$bd =  $conecta->consulta($qusuario);
				if ($bd->num_rows !== 0) 
				{
					$tb = mysqli_fetch_array($bd);
					$clave = $tb["clave"];
				}
			}
			$sql="Select 1 from invikta.prestamos_inv where clave_empleado='$clave' and fec_prestamo='$fec_prestamo' and monto=$monto_double and no_pagos=$no_pagos
					and monto_pago=$monto_pago_double and motivo='$motivo' and fec_captura='$fec_cap'";
			$bd=$conecta->consulta($sql);
			if ($bd->num_rows == 0)
			{
				$query = "Insert INTO invikta.prestamos_inv VALUES (0, '$clave', '$fec_prestamo', $monto_double, $no_pagos, $monto_pago_double, '$motivo', '$fec_cap', '$periodo', '$responsable','$fec_desc',0,'$observa')";
				$conecta->consulta($query);

				$id_prestamos = $conecta->obtener_id();

				$values = [];
				$total_pagos = $no_pagos;
				for ($i = 1; $i <= $no_pagos; $i++) 
				{
					$values[] = "(0, $i, '', 0, $id_prestamos, '$clave', $total_pagos, '$periodo')";
				}


				if (!empty($values)) 
				{
					$qpagos_query = "Insert INTO invikta.prestamos_pagos_inv(id_pago, no_pago, fec_pago, monto, id_prestamos, clave, total_pagos, periodo) VALUES " . implode(", ", $values);
					$conecta->consulta($qpagos_query);
				}


				if(stripos($motivo, 'FINANCIAMIENTO') === false)
				{
					//$a = compl_ajuste(1);
					$b = compl_ajuste(0, $clave);
					$c = $b[0]["unidad"];
					$d = $b[0]["id_gerencia"];
					// $e = $b[0]["socio"];
					$mesActual = date('m');
					$anioActual = date('Y');

					$tablaDestino = 'ajustes';
					
					if ($tablaDestino) 
					{
						$q_ajuste = "Insert INTO invikta.$tablaDestino VALUES (0, '$responsable', '$clave', '$fec_cap', '', $d, $anioActual, $mesActual, $monto_double, '$motivo', 0)";

						$conecta->consulta($q_ajuste);
					}

					$q_con_ajuste = "Select * FROM invikta.$tablaDestino 
									WHERE clave= '$clave' AND fecha= '$fec_cap' AND id_gerencia= $d AND mes= $mesActual 
										AND anio= $anioActual AND motivo= '$motivo' AND ajuste= $monto_double";
					$dbajust = $conecta->consulta($q_con_ajuste);
					if ($dbajust->num_rows !== 0) 
					{
						$empleado = $empleado.' ('.$id_prestamos.')';
						echo "Datos almacenados con éxito|$empleado|EXITO|$id_prestamos";
					} 
					else 
					{
						$empleado = $empleado.' ('.$id_prestamos.')';
						echo "Datos almacenados con éxito|$empleado|ERROR|$id_prestamos";
					}
				}
				else
					echo "Datos almacenados con éxito|$empleado|EXITO|$id_prestamos";
			}
			else
				echo "Los los datos proporcionados ya hay un préstamo generado anteriormente||EXITO|0";
		break;


		case 162:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$motivo_prestamo = addslashes(trim($_POST["motivo_prestamo"]));
			$fec_prestamo = addslashes(trim($_POST["fec_prestamo"]));
			$clave_prestamo = addslashes(trim($_POST["clave_prestamo"]));

			$mensaja = "";
			$tablaDestino = 'ajustes';
			$b = compl_ajuste(0, $clave_prestamo);
			$c = $b[0]["unidad"];
			
			$query_valida = "SELECT * FROM invikta.$tablaDestino WHERE clave = '$clave_prestamo' AND fecha = '$fec_prestamo' AND motivo = '$motivo_prestamo' AND fec_pago != '' AND estatus = 1";
			$bdv =  $conecta->consulta($query_valida);
			if ($bdv->num_rows !== 0) 
			{
				echo $mensaja = "El préstamo ya cuenta con un ajuste generado";

			} 
			else 
			{
				$query_p = "SELECT * FROM invikta.prestamos_pagos_inv WHERE id_prestamos = $id_prestamo  AND fec_pago <> '' AND monto <> 0 ";
				$bdp =  $conecta->consulta($query_p);
				if ($bdp->num_rows !== 0) 
				{
					echo $mensaja = "El préstamo cuenta con pagos realizados por lo cual no es posible eliminarlo";
				}
				else
				{
					$query_dp = "DELETE FROM invikta.prestamos_pagos_inv WHERE id_prestamos = $id_prestamo";
					$query_dpp = "DELETE FROM invikta.prestamos_inv WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($query_dp);
					$conecta->consulta($query_dpp);

					$query_da = "DELETE FROM $tablaDestino WHERE clave = '$clave_prestamo' AND fecha = '$fec_prestamo' AND motivo = '$motivo_prestamo' AND fec_pago = '' AND estatus = 0";
					$conecta->consulta($query_da);
					echo $mensaja = "El préstamo fue eliminado con exito";
				}
			}
		break;

		case 163:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$query = "Select p.id_prestamos, p.monto AS monto_total, p.no_pagos, p.monto_pago,
							COUNT(CASE WHEN pp.monto > 0 AND pp.fec_pago != '' THEN 1 END) AS pagos_realizados,
							COALESCE(SUM(CASE WHEN pp.monto > 0 AND pp.fec_pago != '' THEN pp.monto ELSE 0 END), 0) AS monto_pagado,
							p.periodo,detener_cobro
					FROM invikta.prestamos_inv p
					LEFT JOIN invikta.prestamos_pagos_inv pp ON p.id_prestamos = pp.id_prestamos
					WHERE p.id_prestamos = $id_prestamo
					GROUP BY p.id_prestamos, p.monto, p.no_pagos";
			$db = $conecta->consulta($query);

			$query_semanas = "Select no_pago, total_pagos FROM invikta.prestamos_pagos_inv WHERE fec_pago != '' AND monto != 0 AND id_prestamos = $id_prestamo";

			$dbs = $conecta->consulta($query_semanas);
			$semanas_sin_pago = [];
			if ($dbs->num_rows > 0) 
			{
				while ($row = $dbs->fetch_assoc()) 
				{
					$semanas_sin_pago[] = $row['no_pago'];
				}
			}
			
			if ($db->num_rows != 0) 
			{
				$result = $db->fetch_assoc();
				echo json_encode([
					'data' => $result,
					'semanas_sin_pago' => $semanas_sin_pago
				]);
			} 
			else 
				echo json_encode([]);
		break;

		case 164:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$responsable = addslashes(trim($_POST["responsable"]));
			$empleado = addslashes(trim($_POST["empleado"]));

			$q_prestamo = "Select * FROM invikta.prestamos_inv WHERE id_prestamos = $id_prestamo";
			$dbp = $conecta->consulta($q_prestamo);
			if ($dbp->num_rows != 0) 
			{
				$tb = mysqli_fetch_array($dbp);
				$clave = $tb['clave_empleado'];
				$monto = $tb['monto'];
				$motivo = $tb['motivo'];
				$responsable = $tb['responsable'];
				
				$fec_captura = date('d/m/Y');
				$mes = date('m');
				$anio = date('Y');

				$b = compl_ajuste(0, $clave);
				$c = $b[0]["unidad"];
				$d = $b[0]["id_gerencia"];
				$e = $b[0]["socio"];

				$tablaDestino = 'ajustes';
				


				if ($tablaDestino) 
				{
					$q_ajuste = "Insert INTO invikta.$tablaDestino VALUES (0, '$responsable', '$clave', '$fec_captura', '', '$e', $d, $anio, $mes, $monto, '$motivo', 0)";

					$conecta->consulta($q_ajuste);
					echo "Ajuste almacenado con éxito|$empleado";
				}
			}
		break;

		case 165:
			$motivo = trim($_POST["motivo"] ?? "");

			if ($motivo == "")
			{
				header("Content-Type: application/json");
				echo json_encode([
					"estatus" => "ERROR",
					"mensaje" => "Por favor capture el nuevo motivo de préstamo"
				]);
				break;
			}

			if (function_exists("mb_strtoupper"))
			{
				$motivo = mb_strtoupper($motivo, "UTF-8");
			}
			else
			{
				$motivo = strtoupper($motivo);
			}

			$motivo_sql = addslashes($motivo);
			

			$conecta->consulta("Insert into invikta.motivos_prestamos_inv (motivo) VALUES ('$motivo_sql')");

			$motivos = [];
			$bd = $conecta->consulta("Select motivo FROM invikta.motivos_prestamos_inv ORDER BY motivo");
			if ($bd->num_rows != 0)
			{
				while ($tb = mysqli_fetch_assoc($bd))
				{
					$motivos[] = [
						"motivo" => $tb["motivo"]
					];
				}
			}

			header("Content-Type: application/json");
			echo json_encode([
				"estatus" => "OK",
				"mensaje" => "Motivo agregado con éxito",
				"motivo" => $motivo,
				"motivos" => $motivos
			]);
		break;

		case 166:
			$id_prestamo = addslashes(trim($_POST["id_prestamo"]));
			$empleado = addslashes(trim($_POST["empleado"]));
			$semana = addslashes(trim($_POST["semana"]));
			$monto_semana = addslashes(trim($_POST["monto"]));
			$detener_cobro = addslashes(trim($_POST["detener_cobro"]));

			if($semana!="")
			{
				// Convertir el monto semanal a double
				if (empty($monto_semana) || $monto_semana === '$0.00') 
				{
					$monto_semana_double = 0.0;
				} 
				else 
				{
					$monto_semana_limpio = str_replace(['$', ','], '', $monto_semana);
					$monto_semana_double = (double)$monto_semana_limpio;
				}

				// Consultar el estado actual
				$q_prestamo_ant = "Select no_pagos, clave_empleado, monto_pago FROM invikta.prestamos_inv 
									WHERE id_prestamos = $id_prestamo";
				$db = $conecta->consulta($q_prestamo_ant);
				
				if ($db->num_rows !== 0) 
				{
					$tb = mysqli_fetch_array($db);
					$monto_semanal_ant = $tb["monto_pago"];
					$no_semanas_ant = $tb["no_pagos"];
					$clave_empleado_ant = $tb["clave_empleado"];
				} 
				else 
				{
					echo "Error: Préstamo no encontrado.";
					exit;
				}


				// Actualizar los datos 
				$q_prestamo = "Update invikta.prestamos_inv SET monto_pago = $monto_semana_double, no_pagos = $semana
								WHERE id_prestamos = $id_prestamo";
				$conecta->consulta($q_prestamo);


				// Verificar pagos realizados para mantenerlos intactos
				$q_pagos_realizados = "Select no_pago, monto FROM invikta.prestamos_pagos_inv 
										WHERE id_prestamos = $id_prestamo AND fec_pago != '' 
										AND monto != 0";
				$result_pagos_realizados = $conecta->consulta($q_pagos_realizados);
				$pagos_realizados = [];
				$total_pagado = 0.0;


				while ($row = mysqli_fetch_assoc($result_pagos_realizados)) 
				{
					$pagos_realizados[] = $row['no_pago'];
					$total_pagado += $row['monto'];
				}

				// Calcular el monto restante por pagar
				$monto_restante = ($monto_semanal_ant * $no_semanas_ant) - $total_pagado;

				

				// Ajustar los registros en prestamos_pagos
				if ((int)$semana < (int)$no_semanas_ant) 
				{
					// Mantener los registros necesarios y actualizar 
					$q_delete = "Delete FROM invikta.prestamos_pagos_inv
								WHERE id_prestamos = $id_prestamo 
								AND no_pago > $semana 
								AND no_pago NOT IN (" . implode(',', $pagos_realizados) . ")";
					$conecta->consulta($q_delete);
					


					// Actualizar en los registros restantes
					$q_update_total_pagos = "UPDATE invikta.prestamos_pagos_inv
											SET total_pagos = $semana 
											WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($q_update_total_pagos);

					// Actualizar el próximo registro con el monto restante
					$proximo_pago = $pagos_realizados ? max($pagos_realizados) + 1 : 1;
					$q_update_restante = "UPDATE invikta.prestamos_pagos_inv
										SET monto = 0
										WHERE id_prestamos = $id_prestamo 
										AND no_pago = $proximo_pago";
					$conecta->consulta($q_update_restante);
				} 
				elseif ((int)$semana > (int)$no_semanas_ant) 
				{
					// Insertar nuevos registros si las semanas aumentan
					$values = [];
					for ($i = $no_semanas_ant + 1; $i <= $semana; $i++) 
					{
						if (!in_array($i, $pagos_realizados)) 
						{
							$values[] = "(0, $i, '', 0.0, $id_prestamo, '$clave_empleado_ant', $semana)";
						}
					}

					if (!empty($values)) 
					{
						$q_insert = "Insert INTO invikta.prestamos_pagos_inv (id_pago, no_pago, fec_pago, monto, id_prestamos, clave, total_pagos) VALUES " . implode(", ", $values);
						
						$conecta->consulta($q_insert);
					}

					// Actualizar  en todos los registros
					$q_update_total_pagos = "Update invikta.prestamos_pagos_inv 
											SET total_pagos = $semana 
											WHERE id_prestamos = $id_prestamo";
					$conecta->consulta($q_update_total_pagos);
				}
			}
			$conecta->consulta("Update invikta.prestamos_inv set detener_cobro=$detener_cobro where id_prestamos=$id_prestamo");

			echo "Datos actualizados con éxito|$empleado";
		break;
	}
?>
