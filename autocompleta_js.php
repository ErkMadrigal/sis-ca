<?php
	session_start();
	
	$id=$_GET['id'];
	$res=$_GET['valor'];
	$extra=$_GET['extra'];
	if(is_null($extra))
		$extra="";

	include("./funciones/encripta.php");

	$conecta= new conector();

	switch($id)
	{
		case "folio_sv":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select *
						from (Select folio from carobra_2014.produccion 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_2014.produccion p
							  	inner join naps n on (p.nap=n.nap)
							  where n.no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2022 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2021 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2020 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2019 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2018 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.produccion_2017 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select folio from carobra_respaldos.respaldo_produccion 
							  where no_empleado=$noemp and folio like '%$res%'
							  union all
							  Select no_oportunidad 
							  from (Select folio,no_oportunidad from carobra_2014.prod_trabajador 
							  		where no_oportunidad like '%$res%') pt 
							  		inner join carobra_2014.produccion p on (pt.folio=p.folio)
							  where no_empleado=$noemp) d
						limit 0,10";
			}
			else
				$query="Select *
						from (Select folio from carobra_2014.produccion where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2022 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2021 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2020 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2019 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2018 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.produccion_2017 where folio like '%$res%'
							  union
							  Select folio from carobra_respaldos.respaldo_produccion 
							  where folio like '%$res%'
							  union 
							  Select no_oportunidad from carobra_2014.prod_trabajador where no_oportunidad like '%$res%') d
						limit 0,10";
		break;
		case "folio_inv":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select folio from produccion p
							inner join invikta.prod_inv pi on (p.folio=pi.folio)
				 		where folio like '%$res%' and pi.clave='$clave'
				 		limit 0,10";
			}
			else
				$query="Select folio from produccion where folio like '%$res%' and no_empleado=15814 limit 0,10"; 
		break;

		case "folio_cb":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select cb.folio
						from (Select folio from clawback where folio like '%$res%') cb 
							inner join (Select folio from produccion where no_empleado=$noemp) p 
									on (cb.folio=p.folio)
						limit 0,10";
			}
			else
				$query="Select folio from clawback where folio like '%$res%' limit 0,10";
		break;
		case "folio_sp":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select folio from produccion where no_empleado=$noemp and folio like '%$res%' 
							and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10";
			}
			else
				$query="Select folio from produccion where folio like '%$res%'
							and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10"; 
		break;
		case "folio_av":
			$d=explode("|", $extra);
			switch ($d[0]) 
			{
				case 3:
					$noemp=$d[1];
					$query="Select concat(folio,' / ',tramite) from av_general 
							where id_empleado=$noemp and concat(folio,' / ',tramite)  like '%$res%'
							limit 0,10";
				break;
				case 4:
					$query="Select folio
							from (Select id_empleado,concat(folio,' / ',tramite) as folio 
								  from av_general where concat(folio,' / ',tramite)  like '%$res%' limit 0,5) avg
								inner join usuarios_nivel_gerencia ung on (avg.id_empleado=ung.no_empleado)
								inner join (Select distinct id_gerencia
											from usuarios_nivel_gerencia ung 
												inner join (Select no_empleado from empl_usuarios 
															where nombre='"  . $_SESSION["nombre"] . "') eu 
														on (ung.no_empleado=eu.no_empleado)
											where pertenece=1) eg on (ung.id_gerencia=eg.id_gerencia)";
				break;
				case 5:
					$query="Select folio
							from (Select id_empleado,concat(folio,' / ',tramite) as folio 
								  from av_general where concat(folio,' / ',tramite)  like '%$res%' limit 0,5) avg
								inner join usuarios_nivel_gerencia ung on (avg.id_empleado=ung.no_empleado)
								inner join (Select id_gerencia from estructura_gerencia 
											where brocker='"  . $_SESSION["nombre"] . "') eg on (ung.id_gerencia=eg.id_gerencia)";
				break;
				case 13:
					$query="Select folio
							from (Select id_empleado,concat(folio,' / ',tramite) as folio 
								  from av_general where concat(folio,' / ',tramite)  like '%$res%' limit 0,5) avg
								inner join usuarios_nivel_gerencia ung on (avg.id_empleado=ung.no_empleado)
								inner join (Select id_gerencia from estructura_gerencia 
											where sc='"  . $_SESSION["nombre"] . "') eg on (ung.id_gerencia=eg.id_gerencia)";
				break;
				default:
					$query="Select concat(folio,' / ',tramite) from av_general where concat(folio,' / ',tramite)  like '%$res%' limit 0,10";
				break;
			}
		break;
		case "nss_sv":
			$d=explode("|", $extra);
			$query="Select *
					from (Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_2014.produccion 
						  		where nss like '%$res%') p 
						  	inner join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2022 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2022 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2021 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2021 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2020 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2020 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2019 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2019 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2018 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2018 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.produccion_2017 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.prod_trabajador_2017 pt on (p.folio=pt.folio)
						  union 
						  Select concat(nss,' / ',curp) as nss 
						  from (Select folio,nss from carobra_respaldos.respaldo_produccion 
						  		where nss like '%$res%') p 
						  	inner join carobra_respaldos.respaldo_prod_trabajador pt 
						  			on (p.folio=pt.folio)) d
					limit 0,10";
		$bd=$conecta->consulta($query);
		if ($bd->num_rows == 0)
			$query="Select *
					from (Select concat(nss,' / ',curp) as nss 
						  from carobra_2014.produccion p 
						  	inner join (Select folio,curp from carobra_2014.prod_trabajador 
						  where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2022 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2022 
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2021 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2021 
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2020 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2020 
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2019 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2019 
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2018 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2018 
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.produccion_2017 p 
						  	inner join (Select folio,curp from carobra_respaldos.prod_trabajador_2017
						  				where curp like '%$res%') pt on (p.folio=pt.folio)
						  union
						  Select concat(nss,' / ',curp) as nss 
						  from carobra_respaldos.respaldo_produccion p 
						  	inner join (Select folio,curp from carobra_respaldos.respaldo_prod_trabajador
						  				where curp like '%$res%') pt on (p.folio=pt.folio)) d
					limit 0,10";
		break;
		case "nss_inv":
			$d=explode("|", $extra);

			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select concat(nss,'/',curp) as nss from produccion p
							inner join prod_trabajador pt on (p.folio=pt.folio)
							inner join invikta.prod_inv pi on (p.folio=pi.folio)
				 		where concat(nss,'/',curp) like '%$res%' and pi.clave='$clave'
				 		limit 0,10";
			}
			else
				$query="Select concat(nss,'/',curp) as nss from produccion p
							inner join prod_trabajador pt on (p.folio=pt.folio)
						where concat(nss,'/',curp) like '%$res%' and no_empleado=15814 limit 0,10"; 
		break;
		case "nss_sp":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select nss from produccion where no_empleado=$noemp and nss like '%$res%'
							and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10";
			}
			else
				$query="Select nss from produccion where nss like '%$res%'
							and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10";
		break;
		case "nss_av":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select distinct concat(nss,' / ',curp) from av_general 
						where id_empleado=$noemp and concat(nss,' / ',curp)  like '%$res%'
						limit 0,10";
			}
			else
				$query="Select distinct concat(nss,' / ',curp) from av_general where concat(nss,' / ',curp)  like '%$res%' limit 0,10";
		break;

		case "nss_slovo":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select nss from slovo_clientes 
						where clave_asesor='$clave' and nss like '%$res%'
						limit 0,10";
			}
			else
				$query="Select nss from slovo_clientes where nss like '%$res%' limit 0,10";
		break;

		case "cliente_sv":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select *
						from (Select trabajador 
							  from (Select folio from carobra_2014.produccion where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador from carobra_2014.prod_trabajador
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.produccion_2022 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador 
							  				from carobra_respaldos.prod_trabajador_2022
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.produccion_2021 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador 
							  				from carobra_respaldos.prod_trabajador_2021
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.produccion_2020 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador 
							  				from carobra_respaldos.prod_trabajador_2020
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.produccion_2019 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador 
							  				from carobra_respaldos.prod_trabajador_2019
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.produccion_2017 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador from carobra_respaldos.prod_trabajador_2017
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)
							  union
							  Select trabajador 
							  from (Select folio from carobra_respaldos.respaldo_produccion 
							  		where no_empleado=$noemp) p 
							  	inner join (Select folio,trabajador 
							  				from carobra_respaldos.respaldo_prod_trabajador
							  				where trabajador like '%$res%') pt on (p.folio=pt.folio)) d
						limit 0,10";
			}
			else
				$query="Select *
						from (Select trabajador
							  from carobra_2014.produccion p
								inner join (Select folio,trabajador from carobra_2014.prod_trabajador 
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2022 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2022
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2021 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2021
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2020 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2020
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2019 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2019
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2018 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2018
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.produccion_2017 p
								inner join (Select folio,trabajador 
											from carobra_respaldos.prod_trabajador_2017
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)
							  union
							  Select trabajador
							  from carobra_respaldos.respaldo_produccion p
								inner join (Select folio,trabajador 
											from carobra_respaldos.respaldo_prod_trabajador
											where trabajador like '%$res%' limit 0,30) pt 
										on (p.folio=pt.folio)) d
						limit 0,10";
		break;
		case "cliente_inv":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select trabajador from prod_trabajador p
							inner join invikta.prod_inv pi on (p.folio=pi.folio)
				 		where trabajador like '%$res%' and pi.clave='$clave'
				 		limit 0,10";
			}
			else
				$query="Select trabajador from prod_trabajador pt 
							inner join produccion p on (pt.folio=p.folio)
						where trabajador like '%$res%' and no_empleado=15814 limit 0,10"; 
		break;
		case "cliente_fr":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select trabajador from prod_trabajador p
							inner join feram.prod_fr pi on (p.folio=pi.folio)
				 		where trabajador like '%$res%' and pi.clave='$clave'
				 		limit 0,10";
			}
			else
				$query="Select trabajador from prod_trabajador pt 
							inner join produccion p on (pt.folio=p.folio)
						where trabajador like '%$res%' and no_empleado=2085 limit 0,10"; 
		break;
		case "cliente_cb":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select trabajador
						from (Select * from prod_trabajador where trabajador like '%$res%') pt
							inner join (Select folio from produccion where no_empleado=$noemp) p
									on (pt.folio=p.folio)
							inner join clawback c on (p.folio=c.folio)
						limit 0,10";
			}
			else
				$query="Select trabajador
						from (Select * from prod_trabajador where trabajador like '%$res%') pt
							inner join clawback c on (pt.folio=c.folio)
						limit 0,10";
		break;

		case "cliente_sp":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$noemp=$d[1];
				$query="Select trabajador 
					  	from (Select folio,fec_traspaso from produccion where no_empleado=$noemp) p 
					  		inner join (Select folio,trabajador from prod_trabajador
					  					where trabajador like '%$res%') pt on (p.folio=pt.folio)
					  	where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10";
			}
			else
				$query="Select trabajador
							  from (Select folio,fec_traspaso from produccion) p
								inner join (Select folio,trabajador from prod_trabajador 
											where trabajador like '%$res%') pt on (p.folio=pt.folio)
						where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'2022-06-18')>=0
						limit 0,10";
		break;
		case "cliente_av":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select cliente from av_general 
						where id_empleado=$noemp and cliente  like '%$res%' limit 0,10";
			}
			else
				$query="Select cliente from av_general where cliente  like '%$res%' limit 0,10";
		break;
		case "cliente_slovo":
			$d=explode("|", $extra);
			if($d[0]==3)
			{
				$clave=$d[1];
				$query="Select nombre from slovo_clientes where clave_asesor='$clave' and nombre  like '%$res%' limit 0,10";
			}
			else
				$query="Select nombre from slovo_clientes where nombre  like '%$res%' limit 0,10";
		break;
		case "oficina_sv":
			$query="Select distinct oficina from estructura_comercial where oficina like '%$res%'";
		break;

		case "nap_sv":
			$d=explode("|", $extra);
			switch ($d[0]) 
			{
				case 3:
					$query="Select concat(nombre,' (',nap,')') as nap from carobra_2014.naps n
								inner join (Select * from carobra_2014.empl_usuarios where nombre='" . utf8_decode($_SESSION["nombre"]) . "') u
										on (n.no_empleado=u.no_empleado)
							where CONVERT(CONCAT(nombre, ' (', nap, ')') USING utf8mb4) COLLATE utf8mb4_general_ci LIKE '%$res%'";
				break;
				case 4:
					$query="Select concat(nombre,' (',n.nap,')') as nap from carobra_2014.naps n
								inner join carobra_2014.gerencias g on (n.nap=g.nap)
								inner join (Select distinct id_gerencia from carobra_2014.usuarios_nivel_gerencia ung
												inner join (Select * from carobra_2014.empl_usuarios 
															where nombre='" . utf8_decode($_SESSION["nombre"]) . "') u 
														on (ung.no_empleado=u.no_empleado)
											where pertenece=1) u on (g.id_gerencia=u.id_gerencia)
								inner join carobra_2014.empl_usuarios ue on (n.no_empleado=ue.no_empleado)
							where CONVERT(CONCAT(nombre, ' (', n.nap, ')') USING utf8mb4) COLLATE utf8mb4_general_ci LIKE '%$res%'";
				break;
				case 5:
					$query="Select concat(nombre,' (',n.nap,')') as nap from carobra_2014.naps n
								inner join carobra_2014.gerencias g on (n.nap=g.nap)
								inner join (Select * from carobra_2014.estructura_gerencia 
											where brocker='" . utf8_decode($_SESSION["nombre"]) . "') eg on (g.id_gerencia=eg.id_gerencia)
								inner join carobra_2014.empl_usuarios ue on (n.no_empleado=ue.no_empleado)
							where CONVERT(CONCAT(nombre, ' (', n.nap, ')') USING utf8mb4) COLLATE utf8mb4_general_ci LIKE '%$res%'";
				break;
				case 13:
					$query="Select concat(nombre,' (',n.nap,')') as nap from carobra_2014.naps n
								inner join carobra_2014.gerencias g on (n.nap=g.nap)
								inner join (Select * from carobra_2014.estructura_gerencia 
											where sc='" . utf8_decode($_SESSION["nombre"]) . "') eg on (g.id_gerencia=eg.id_gerencia)
								inner join carobra_2014.empl_usuarios ue on (n.no_empleado=ue.no_empleado)
							where CONVERT(CONCAT(nombre, ' (', n.nap, ')') USING utf8mb4) COLLATE utf8mb4_general_ci LIKE '%$res%'";
				break;
				default:
					$query="Select concat(nombre,' (',nap,')') as nap from carobra_2014.naps n
								inner join carobra_2014.empl_usuarios u on (n.no_empleado=u.no_empleado)
							where CONVERT(CONCAT(nombre, ' (', nap, ')') USING utf8mb4) COLLATE utf8mb4_general_ci LIKE '%$res%'";
				break;
			}
		break;
		case "sc_sv":
			$d=explode("|", $extra);
			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select distinct sc from $estruc where sc like '%$res%'";
		break;
		case "sc_to":
			$d=explode("|", $extra);
			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select 'TODOS' as sc
						union all
						Select distinct sc from $estruc where sc like '%$res%'";
		break;
		case "ssb_sv":
			$d=explode("|", $extra);
			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select distinct ss from estructura_comercial where ss like '%$res%'";
			else
				$query="Select distinct ss from estructura_comercial 
						where sc='" . utf8_decode($_SESSION["nombre"]) . "' and ss like '%$res%'";
		break;
		case "sb_sv":
			$d=explode("|", $extra);

			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
			{
				$d[0]=13;
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			}
			else
				$nombre=$_SESSION["nombre"];

			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select distinct brocker from $estruc where brocker like '%$res%'";
			if($d[0]==13)
					$query="Select distinct brocker from $estruc where sc='" . $nombre . "' and brocker like '%$res%'";
		break;
		case "sb_inv":
			$d=explode("|", $extra);

			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
			{
				$d[0]=13;
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			}
			else
				$nombre=$_SESSION["nombre"];

			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==12)
				$query="Select distinct brocker from invikta.brockers where brocker like '%$res%'";
		break;

		case "sb_fr":
			$d=explode("|", $extra);

			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
			{
				$d[0]=13;
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			}
			else
				$nombre=$_SESSION["nombre"];

			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==12)
				$query="Select distinct brocker from feram.brockers where brocker like '%$res%'";
		break;
		case "asesor_sv":
			$d=explode("|", $extra);
			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";
			if($d[0]==6 or $d[0]==2 or $d[0]==12 or $d[0]==1)
			{
				if($_SESSION["nombre"]=='DIEGO PEREZ JARAMILLO')
					$query="Select nombre 
							from (Select distinct clave from usuarios_nivel_gerencia
								  where id_gerencia != 124) ung 
								inner join usuarios u on (ung.clave=u.clave)
							where nombre like '%$res%' limit 0,20";
				else
					$query="Select nombre from usuarios where nombre like '%$res%' limit 0,10";
			}
			if($d[0]==13)
			{
				$q13="";
				if($_SESSION["nombre"]=='LUIS FERNANDO CONTRERAS RAMIREZ')
				{
					$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
					$q13=" where nombre !='FERNANDO ARISTEO RAMIREZ CABRERA'";
				}
				else
					$nombre=$_SESSION["nombre"];
				$query="Select distinct nombre 
					  from (Select distinct id_gerencia from $estruc where sc='" . utf8_decode($nombre) . "') eg
					 	inner join (Select distinct no_empleado,id_gerencia from usuarios_nivel_gerencia 
									where id_nivel=3 and pertenece=1) ung on (eg.id_gerencia=ung.id_gerencia)
					 	inner join (Select * from empl_usuarios where nombre like '%$res%') eu 
					 			on (ung.no_empleado=eu.no_empleado) $q13 
					  limit 0,10";
			}
			if($d[0]==5)
				$query="Select distinct nombre 
						from (Select id_gerencia from $estruc 
							  where brocker='" . utf8_decode($_SESSION["nombre"]) . "') eg
						 	inner join (Select distinct no_empleado,id_gerencia 
						 				from usuarios_nivel_gerencia 
										where id_nivel=3 and pertenece=1) ung 
									on (eg.id_gerencia=ung.id_gerencia)
						 	inner join (Select * from empl_usuarios where nombre like '%$res%') eu 
						 			on (ung.no_empleado=eu.no_empleado)
						limit 0,10";
			if($d[0]==4)
				$query="Select distinct nombre
						from (Select no_empleado,id_gerencia from usuarios_nivel_gerencia 
							  where id_nivel=4) ger
						  	inner join (Select no_empleado from empl_usuarios
						  				where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eug 
						  			on (ger.no_empleado=eug.no_empleado)
						 	inner join (Select distinct no_empleado,id_gerencia 
						 				from usuarios_nivel_gerencia 
										where (id_nivel=3 or id_nivel=7) and pertenece=1) ung 
									on (ger.id_gerencia=ung.id_gerencia)
						 	inner join (Select * from empl_usuarios where nombre like '%$res%') eu 
						 			on (ung.no_empleado=eu.no_empleado)
						limit 0,10";
		break;
		case "asesor_inv":
			$d=explode("|", $extra);

			if($d[0]==12)
				$query="Select nombre from invikta.empleados where nombre like '%$res%' limit 0,10";
			else
			{
				if($d[0]==5)
					$query="Select distinct nombre 
							from invikta.empleados e
								inner join invikta.gerencias g on (e.id_gerencia=g.id_gerencia)
								inner join invikta.brocker b on (g.id_brocker=b.id_brocker)
								inner join (Select clave from empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eb 
										on (b.clave_brocker=eb.clave)
							where nombre like '%$res%' limit 0,10";
				else
				{
					if($d[0]==4)
						$query="Select distinct nombre 
								from invikta.empleados e
									inner join invikta.gerencias g on (e.id_gerencia=g.id_gerencia)
									inner join (Select clave from invikta.empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eg 
										on (g.gerente=eg.clave)
								where nombre like '%$res%' limit 0,10";
				}
			}
		break;
		case "asesor_fr":
			$d=explode("|", $extra);

			if($d[0]==12)
				$query="Select nombre from feram.empleados where nombre like '%$res%' limit 0,10";
			if($d[0]==5)
				$query="Select distinct nombre 
						from invikta.empleados e
							inner join feram.gerencias g on (e.id_gerencia=g.id_gerencia)
							inner join feram.brocker b on (g.id_brocker=b.id_brocker)
							inner join (Select clave from empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eb 
									on (b.clave_brocker=eb.clave)
						where nombre like '%$res%' limit 0,10";
			if($d[0]==4)
				$query="Select distinct nombre 
						from invikta.empleados e
							inner join feram.gerencias g on (e.id_gerencia=g.id_gerencia)
							inner join (Select clave from empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eg 
								on (g.gerente=eg.clave)
						where nombre like '%$res%' limit 0,10";
		break;
		case "gerencia_sv":
			$d=explode("|", $extra);
			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";

			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
			{
				$d[0]=13;
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			}
			else
				$nombre=$_SESSION["nombre"];

			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select *
						from (Select 'TODAS' as gerencia 
							  union 
							  Select distinct gerencia from gerencias) g 
						where gerencia like '%$res%'";
			if($d[0]==13)
				$query="Select gerencia 
						from (Select id_gerencia from $estruc 
							  where sc='$nombre') tp 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (tp.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
			if($d[0]==5)
				$query="Select gerencia 
						from (Select id_gerencia from $estruc 
							  where brocker='$nombre') tp 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (tp.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
			if($d[0]==1)
				$query="Select gerencia
						from (Select id_gerencia 
							  from (Select clave from empl_usuarios 
							  		where nombre='$nombre') eu 
								inner join usuarios_nivel_gerencia ung on (eu.clave=ung.clave)) e 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (e.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
		break;

		case "gerencia_to":
			$d=explode("|", $extra);
			if($d[1]=="ec")
				$estruc="estructura_comercial";
			else
				$estruc="estructura_gerencia";

			if($_SESSION["nombre"]=='NORMANDA VICTORIA GARCIA VILLANUEVA')
			{
				$d[0]=13;
				$nombre='FERNANDO ARISTEO RAMIREZ CABRERA';
			}
			else
				$nombre=$_SESSION["nombre"];

			if($d[0]==6 or $d[0]==2 or $d[0]==12)
				$query="Select distinct gerencia from gerencias where gerencia like '%$res%'";
			if($d[0]==13)
				$query="Select gerencia 
						from (Select id_gerencia from $estruc 
							  where sc='$nombre') tp 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (tp.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
			if($d[0]==5)
				$query="Select gerencia 
						from (Select id_gerencia from $estruc 
							  where brocker='$nombre') tp 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (tp.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
			if($d[0]==1)
				$query="Select gerencia
						from (Select id_gerencia 
							  from (Select clave from empl_usuarios 
							  		where nombre='$nombre') eu 
								inner join usuarios_nivel_gerencia ung on (eu.clave=ung.clave)) e 
							inner join (Select distinct id_gerencia, gerencia from gerencias) g 
									on (e.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%'";
		break;

		case "gerencia_inv":
			$d=explode("|", $extra);

			if($d[0]==12)
				$query="Select *
						from (Select 'TODAS' as gerencia 
							  union 
							  Select distinct gerencia from invikta.gerencias) g 
						where gerencia like '%$res%'";
			if($d[0]==5)
				$query="Select gerencia 
						from invikta.gerencias g
							inner join invikta.brocker b on (g.id_brocker=b.id_brocker)
							inner join (Select clave from invikta.empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eb 
								on (b.clave_brocker=eb.clave)
						where gerencia like '%$res%'";
			if($d[0]==4)

				$query="Select g.gerencia 
						from invikta.gerencias g
							inner join (Select clave from invikta.empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eb 
								on (g.gerente=eb.clave)
						where gerencia like '%$res%'";
		break;
		case "gerencia_fr":
			$d=explode("|", $extra);

			if($d[0]==12)
				$query="Select *
						from (Select 'TODAS' as gerencia 
							  union 
							  Select distinct gerencia from feram.gerencias) g 
						where gerencia like '%$res%'";
			if($d[0]==5)
				$query="Select gerencia 
						from invikta.gerencias g
							inner join feram.brocker b on (g.id_brocker=b.id_brocker)
							inner join (Select clave from empleados where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eb 
								on (b.clave_brocker=eb.clave)
						where gerencia like '%$res%'";
		break;
		case "solicitud_dp":
			if($extra==99)
				$query="Select solicitud 
						from (Select solicitud,promotor from prod_deportenis where solicitud like '%$res%' limit 0,10) p
							inner join (Select no_promotor,c.celula from empleados_dp e
											inner join (Select celula from empleados_dp
														where promotor='" . utf8_decode($_SESSION["nombre"]) . "') c on (e.celula=c.celula)) e 
										on (p.promotor=e.no_promotor)";
			else
				$query="Select solicitud from prod_deportenis where solicitud like '%$res%' limit 0,10";
		break;
		case "cliente_dp":
			if($extra==100)
				$query="Select cliente from prod_dportenis where cliente like '%$res%' limit 0,10";
			else
			{
				if($extra==1)
					$query="Select cliente
							from prod_dportenis dp
								inner join (Select no_prom_dep
										    from (Select clave from empl_usuarios where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
												inner join empleados e on (eu.clave=e.clave)) c on (dp.promotor=c.no_prom_dep)
							where cliente like '%$res%' limit 0,10";
				else
				{
					if($extra==2)
						$query="Select cliente
								from prod_dportenis dp
									inner join empleados_dp e on (dp.promotor=e.no_promotor)
									inner join tiendas_dp t on (e.tienda=t.no_tienda)
									inner join (Select no_prom_dep
											    from (Select clave from empl_usuarios 
											    	  where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
													inner join empleados e on (eu.clave=e.clave)) c on (t.coordinador=c.no_prom_dep)
								where cliente like '%$res%' limit 0,10";
					else
					{
						if($extra==3)
							$query="Select cliente
									from prod_dportenis dp
										inner join empleados_dp e on (dp.promotor=e.no_promotor)
										inner join (Select celula,no_promotor from empleados_dp where nivel='L') cel on (e.celula=cel.celula)
										inner join (Select no_prom_dep
												    from (Select clave from empl_usuarios 
												    	  where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
														inner join empleados e on (eu.clave=e.clave)) c on (cel.no_promotor=c.no_prom_dep)
									where cliente like '%$res%' limit 0,10";
						else
							$query="Select cliente
									from prod_dportenis dp
										inner join empleados_dp e on (dp.promotor=e.no_promotor)
										inner join celulas cel on (e.celula=cel.no_celula)
										inner join (Select no_prom_dep
												    from (Select clave from empl_usuarios 
												    	  where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
														inner join empleados e on (eu.clave=e.clave)) c on (cel.supervisor=c.no_prom_dep)
									where cliente like '%$res%' limit 0,10";
					}
				}
			}
		break;
		case "promotor_dp":
			if($extra==100)
				$query="Select promotor from empleados_dp where promotor like '%$res%' limit 0,10";
			else
			{
				if($extra==2)
					$query="Select e.promotor
							from empleados_dp e
								inner join tiendas_dp t on (e.tienda=t.no_tienda)
								inner join (Select no_prom_dep
									        from (Select clave from empl_usuarios 
									        where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
												inner join empleados e on (eu.clave=e.clave)) c on (t.coordinador=c.no_prom_dep)
							where promotor like '%$res%' limit 0,10";
				else
				{
					if($extra==3)
						$query="Select e.promotor
								from empleados_dp e
									inner join (Select celula,no_promotor from empleados_dp where nivel='L') cel on (e.celula=cel.celula)
									inner join (Select no_prom_dep
										        from (Select clave from empl_usuarios 
										        where nombre='ALEJANDRO DE LA ROSA TORRES') eu 
													inner join empleados e on (eu.clave=e.clave)) c on (cel.no_promotor=c.no_prom_dep)
								where promotor like '%$res%' limit 0,10";
					else
						$query="Select e.promotor
								from empleados_dp e
									inner join celulas cel on (e.celula=cel.no_celula)
									inner join (Select no_prom_dep
										        from (Select clave from empl_usuarios 
										        where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
													inner join empleados e on (eu.clave=e.clave)) c 
											on (cel.supervisor=c.no_prom_dep)
								where promotor like '%$res%' limit 0,10";
				}
			}
		break;
		case "celula_dp":
			if($extra==100)
				$query="Select *
						from (Select 'TODAS' as celula
							  union 
							  Select distinct celula from celulas) c 
						where celula like '%$res%'";
			else
				$query="Select celula
						from celulas cel
							inner join (Select no_prom_dep
								        from (Select clave from empl_usuarios 
								        where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
											inner join empleados e on (eu.clave=e.clave)) c 
									on (cel.supervisor=c.no_prom_dep)
						where celula like '%$res%' limit 0,10";
		break;
		case "tienda_dp":
			if($extra==100)
				$query="Select tienda from tiendas_dp where tienda like '%$res%' limit 0,10";
			else
			{
				if($extra==2)
					$query="Select distinct t.tienda 
							from empleados_dp e
								inner join tiendas_dp t on (e.tienda=t.no_tienda)
								inner join	(Select no_prom_dep
										     from (Select clave from empl_usuarios 
										     where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
												inner join empleados e on (eu.clave=e.clave)) c 
										on (t.coordinador=c.no_prom_dep)
							where t.tienda like '%$res%' limit 0,10";
				else
				{
					if($extra==3)
						$query="Select distinct t.tienda
								from celulas cel
									inner join (Select celula,tienda,no_promotor 
												from empleados_dp where nivel='L') l 
											on (cel.no_celula=l.celula)
									inner join (Select no_prom_dep
									    		from (Select clave from empl_usuarios 
									    	  	where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
													inner join empleados e on (eu.clave=e.clave)) c 
											on (l.no_promotor=c.no_prom_dep)
									inner join tiendas_dp t on (l.tienda=t.no_tienda)
								where t.tienda like '%$res%' limit 0,10";
					else
						$query="Select distinct t.tienda
								from celulas cel
									inner join (Select no_prom_dep
										        from (Select clave from empl_usuarios 
										        where nombre='" . utf8_decode($_SESSION["nombre"]) . "') eu 
													inner join empleados e on (eu.clave=e.clave)) c 
											on (cel.supervisor=c.no_prom_dep)
									inner join empleados_dp e on (cel.no_celula=e.celula)
									inner join tiendas_dp t on (e.tienda=t.no_tienda)
								where t.tienda like '%$res%' limit 0,10";
				}
			}
		break;
		case "socio_dp":
			$query="Select distinct promotor 
					from empleados_dp e 
						inner join celulas c on (e.no_promotor=c.supervisor)
					where promotor like '%$res%' limit 0,10";
		break;
		case "cc":
			$query="Select nombre_cc from centros_costo where nombre_cc like '%$res%' limit 0,10";
		break;
		case "partida":
			$query="Select partida from partidas where partida like '%$res%' limit 0,10";
		break;
		case "ngasto":
			$query="Select no_gasto from ajustes where no_gasto like '%$res%' limit 0,10";
		break;
		case "napf":
			$d=explode("|", $extra);
			$ne=$d[1];
			$nivel=$d[0];

			if($nivel==3)
				$query="Select concat(nombre,' (', nap, ')') as nap
						from (Select no_empleado,nombre from empl_usuarios where no_empleado=$ne) u 
							inner join naps n on (u.no_empleado=n.no_empleado)
						where concat(nombre,' (', nap, ')') like '%$res%'";
			else
			{
				if($nivel==4)
					$query="Select concat(nombre,' (', n.nap, ')') as nap
							from (Select distinct nap from usuarios_nivel_gerencia ung
									left join gerencias g on (ung.id_gerencia=g.id_gerencia) 
								  where no_empleado=$ne and pertenece=1 and id_nivel=4) ung
								inner join naps n on (g.nap=n.nap)
								inner join empl_usuarios eu on (n.no_empleado=eu.no_empleado)
							where baja=0 and concat(nombre,' (', n.nap, ')') like '%$res%' limit 0,10";
				else
				{
					if($nivel==5)
						$query="Select concat(nombre,' (', n.nap, ')') as nap
								from (Select distinct nap from usuarios_nivel_gerencia ung
										left join gerencias g on (ung.id_gerencia=g.id_gerencia)
									  where no_empleado=$ne and id_nivel=5) ung
									left join naps n on (g.nap=n.nap)
									left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)
								where baja=0 and concat(nombre,' (', n.nap, ')') like '%$res%' limit 0,10";
					else
					{
						if($nivel==13)
							$query="Select concat(nombre,' (', n.nap, ')') as nap
									from (Select distinct nap from usuarios_nivel_gerencia ung
											left join gerencias g on (ung.id_gerencia=g.id_gerencia)
										  where no_empleado=$ne and id_nivel=13) ung
										left join naps n on (ung.nap=n.nap)
										left join empl_usuarios eu on (n.no_empleado=eu.no_empleado)
									where baja=0 and concat(nombre,' (', n.nap, ')') like '%$res%' 
									limit 0,10";
						else
							$query="Select concat(nombre,' (', nap, ')') as nap from empl_usuarios u 
										inner join naps n on (u.no_empleado=n.no_empleado)
									where baja=0 and concat(nombre,' (', nap, ')') like '%$res%' 
									limit 0,10";
					}
				}
			}
		break;
		case "scf":
			$d=explode("|", $extra);
			$ne=$d[1];
			$nivel=$d[0];

			if($nivel==13)
				$query="Select nombre 
						from (Select no_empleado,nombre from empl_usuarios where no_empleado=$ne) eu
							inner join (Select distinct no_empleado from usuarios_nivel_gerencia 
										where id_nivel=13) ung on (eu.no_empleado=ung.no_empleado)
						where nombre like '%$res%'";
			else
			{
				if($nivel==6 or $nivel==12)
					$query="Select nombre 
							from empl_usuarios eu
								inner join (Select distinct no_empleado from usuarios_nivel_gerencia 
											where id_nivel=13) ung on (eu.no_empleado=ung.no_empleado)
							where nombre like '%$res%' limit 0,10";
			}
		break;
		case "sbf":
			$d=explode("|", $extra);
			$ne=$d[1];
			$nivel=$d[0];

			if($nivel==13)
				$query="Select distinct brocker
						from (Select distinct id_gerencia from usuarios_nivel_gerencia 
							  where no_empleado=$ne and id_nivel=13) ung
							inner join estructura_gerencia eg on (ung.id_gerencia=eg.id_gerencia)
						where brocker != '' and brocker like '%$res%' limit 0,10";
			else
			{
				if($nivel==5)
					$query="Select nombre 
							from (Select no_empleado,nombre from empl_usuarios where no_empleado=$ne) eu
								inner join (Select distinct no_empleado from usuarios_nivel_gerencia 
											where id_nivel=5) ung on (eu.no_empleado=ung.no_empleado)
							where nombre like '%$res%'";
				else
				{
					if($nivel==6 or $nivel==12)
						$query="Select nombre 
								from empl_usuarios eu
									inner join (Select distinct no_empleado from usuarios_nivel_gerencia where id_nivel=5) ung 
											on (eu.no_empleado=ung.no_empleado)
								where nombre like '%$res%' limit 0,10";
				}
			}
		break;
		case "gerenciaf":
			$d=explode("|", $extra);
			$ne=$d[1];
			$nivel=$d[0];

			if($nivel==13)
				$query="Select gerencia
						from (Select distinct id_gerencia from usuarios_nivel_gerencia 
							  where no_empleado=$ne and id_nivel=13) ung
							inner join (Select distinct id_gerencia,gerencia from gerencias) g 
									on (ung.id_gerencia=g.id_gerencia)
						where gerencia like '%$res%' limit 0,10";
			else
			{
				if($nivel==5)
					$query="Select gerencia
							from (Select distinct id_gerencia from usuarios_nivel_gerencia 
								  where no_empleado=$ne and id_nivel=5) ung
								inner join (Select distinct id_gerencia,gerencia from gerencias) g 
										on (ung.id_gerencia=g.id_gerencia)
							where gerencia like '%$res%' limit 0,10";
				else
				{
					if($nivel==4)
						$query="Select gerencia
								from (Select distinct id_gerencia from usuarios_nivel_gerencia 
									  where no_empleado=$ne and id_nivel=4) ung
									inner join (Select distinct id_gerencia,gerencia from gerencias) g 
											on (ung.id_gerencia=g.id_gerencia)
								where gerencia like '%$res%' limit 0,10";
					else
					{
						if($nivel==6 or $nivel==12)
							$query="Select gerencia
									from (Select 'Todas' as gerencia
										  union
										  Select distinct gerencia from gerencias) g 
									where gerencia like '%$res%' limit 0,10";
					}
				}
			}
		break;
		case "usuarios":
			$query="Select nombre from usuarios where nombre like '%$res%'";
		break;
		case "comisionista":
			$query="Select * 
					from (Select 'TODOS' as nombre
					      union
						  Select distinct nombre from empl_usuarios eu 
							inner join empleados e on (eu.clave=e.clave)	
						  where categoria=1) d 
					where nombre like '%$res%'";
		break;
		case "ger-comisionista":
			$query="Select *
					from (Select 'TODOS' as nombre
					      union
						  Select distinct nombre from empl_usuarios eu 
							inner join empleados e on (eu.clave=e.clave)	
						  where categoria=2) d 
					where nombre like '%$res%'";
		break;
		case "ger-premium":
			$query="Select *
					from (Select 'TODOS' as nombre
					      union
						  Select distinct nombre from empl_usuarios eu 
							inner join empleados e on (eu.clave=e.clave)	
						  where categoria=3) d 
					where nombre like '%$res%'";
		break;
		case "ger-master":
			$query="Select *
					from (Select 'TODOS' as nombre
					      union
						  Select distinct nombre from empl_usuarios eu 
							inner join empleados e on (eu.clave=e.clave)	
						  where categoria=4) d 
					where nombre like '%$res%'";
		break;
		case "ger-elite":
			$query="Select *
					from (Select 'TODOS' as nombre
					      union
						  Select distinct nombre from empl_usuarios eu 
							inner join empleados e on (eu.clave=e.clave)	
						  where categoria=5) d 
					where nombre like '%$res%'";
		break;
		case "ssb_ep":
				$query="Select distinct brocker from estructura_gerencia where brocker like '%$res%'";
		break;
		case "sc_ep":
				$query="Select distinct sc from estructura_gerencia where sc like '%$res%'";
		break;
		case "gerencia_ep":
					$query="Select *
						from (Select 'TODAS' as gerencia 
							  union 
							  Select distinct gerencia from gerencias) g 
						where gerencia like '%$res%'";
		break;
		case "folio_afore_aclaracion":
			$query = "select  distinct folio from produccion where folio like '%$res%'";
		break;
		case "cliente_registro_esp":
			$query = "SELECT q.cliente FROM (
    					SELECT CONCAT(pt.trabajador, ' (', p.nss, '/', pt.CURP,')') AS cliente, 
           					p.id_tipo_mov,
           					p.fec_traspaso
   	 					FROM prod_trabajador pt 
   						INNER JOIN produccion p ON pt.folio = p.folio
    					WHERE p.id_tipo_mov = 2
    					AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= STR_TO_DATE('01/06/2024', '%d/%m/%Y')
					) AS q 
					WHERE q.cliente LIKE '%$res%'
					LIMIT 10";
		break;
		case "comisionista_registro_esp":
			$query = "SELECT eu.nombre FROM empl_usuarios eu 
						INNER JOIN usuarios_nivel_gerencia ung ON (eu.no_empleado = ung.no_empleado) 
						WHERE ung.id_nivel = 3 AND ung.pertenece = 1 AND nombre LIKE '%$res%'";
		break;
		case "folio_correcion_asesor":
			$query = "SELECT folio FROM produccion 
						WHERE folio LIKE '%$res%' 
						AND STR_TO_DATE(fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
		break;
		case "empleado_correcion_asesor":
			$query = "SELECT nombre FROM empl_usuarios 
						WHERE activo = 1 AND nombre LIKE '%$res%'
						ORDER BY nombre ASC";
		break;
		case "usuario_clave_nombre":
			$query = "Select CONCAT(nombre, ' (',clave,')') AS usuario FROM carobra.usuarios
					  where CONCAT(nombre, ' (',clave, ')') LIKE '%$res%'
					  LIMIT 5";
		break;
		case "centro_costo_nombre":
			$query = "SELECT * 
						FROM (
							SELECT CONCAT(cc.nombre_cc, '(', cc.id_cc, ')') AS centro 
							FROM carobra_2014.centros_costo_area cc
						) t
						WHERE t.centro LIKE '%$res%'
						LIMIT 5";
		break;
		case "nopartida_partida":
			$query = "Select * 
						FROM (SELECT CONCAT(p.partida, '(', p.no_partida, ')') AS partida FROM carobra_2014.partidas p) t
						WHERE t.partida LIKE '%$res%'
						LIMIT 5";
		break;
		case "naps_nombres":
			$query = "Select *
						FROM (
							SELECT CONCAT(eu.nombre, '(', n.nap, ')') AS nombre_nap
							FROM naps n
							INNER JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
						) t
						WHERE t.nombre_nap LIKE '%$res%'";
		break;
		case "temp_ajuste_motivo":
			$query = "Select motivo FROM temp_ajustes WHERE motivo LIKE '%$res%'";
		break;
		case "folios_mes_en_adelante":
			$query = "Select p.folio 
						FROM produccion p
						INNER JOIN prod_trabajador pt ON (p.folio = pt.folio)
						WHERE p.folio LIKE '%$res%'
						AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
		break;
		case "sc_historico":
			$query = "Select DISTINCT sc FROM estruc_empleados WHERE sc LIKE '%$res%'";
		break;
		case "folios_dos_meses":
			$query = "Select p.folio 
						FROM produccion p
						INNER JOIN prod_trabajador pt ON (p.folio = pt.folio)
						WHERE p.folio LIKE '%$res%'
						AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)";
		break;
		case "folios_anio_en_adelante":
			$query = "Select p.folio 
						FROM produccion p
						INNER JOIN prod_trabajador pt ON (p.folio = pt.folio)
						WHERE p.folio LIKE '%$res%'
						AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) LIMIT 10";
		break;
		case "empleados_activos":
			$query = "Select nombre FROM carobra_2014.empl_usuarios WHERE activo = 1 AND nombre LIKE '%$res%' LIMIT 10";
		break;
		case "empleados_prestamos_activos":
			$query = "Select CONCAT(nombre, ' (', p.id_prestamos ,')') FROM carobra_2014.empl_usuarios eu 
						INNER JOIN carobra_2014.prestamos p ON eu.clave = p.clave_empleado
						WHERE activo = 1 AND nombre LIKE '%$res%'";
		break;

		case "empleados_activos_inv":
			$query = "Select nombre FROM invikta.empleados WHERE nombre LIKE '%$res%' LIMIT 10";
		break;

		case "empleados_prestamos_activos_inv":
			$query = "Select CONCAT(nombre, ' (', p.id_prestamos ,')') FROM invikta.empleados eu 
						INNER JOIN invikta.prestamos_inv p ON eu.clave = p.clave_empleado
						WHERE nombre LIKE '%$res%'";
		break;
		case "proveedore_clave":
			$query = "Select * FROM ( SELECT CONCAT(p.proveedor, ' (', p.no_prov, ')') AS proveedor 
						FROM carobra_2014.proveedores p ) t 
						WHERE t.proveedor LIKE '%$res%' LIMIT 5 ";
		break;
		case "clave_gasto":
			$query = "Select * FROM ( SELECT CONCAT(p.motivo, ' (', p.no_gasto, ')') AS gasto 
						FROM carobra_2014.gastos p ) t 
						WHERE t.gasto LIKE '%$res%' LIMIT 10";
		break;
		case "gerencia_neg":
			$query = "Select gerencia FROM carobra_2014.gerencias_negocio WHERE gerencia LIKE '%$res%' LIMIT 20";
		break;
		case "partida_socio":
			$query="Select concat(partida,' (',no_partida,')') as partida from partidas_area_negocio
					where concat(partida,' (',no_partida,')') like '%$res%' LIMIT 10";
		break;

		// case "":
			
		// break;
	}
	$datos=array();
	$conecta->consulta("SET NAMES utf8");
	$tb=$conecta->consulta($query);
	if ($tb->num_rows > 0)
	{
		while ($row = $tb->fetch_array(MYSQLI_NUM))
	    {
			$datos[]=$row[0];
    	}
	}
	echo json_encode($datos);
?>