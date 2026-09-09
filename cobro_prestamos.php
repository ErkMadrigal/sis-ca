<?php
	    $anio_p=date("Y");
	    $mes_p=date("m");

	    $periodos = [
				        ["id" => "SEMANAL","dias" => 5],
				        ["id" => "QUINCENAL","dias" => 13],
				        ["id" => "MENSUAL","dias" => 26]
	    			];

		$conecta->consulta("Update invikta.prestamos_pagos_inv set monto=0 where fec_pago='' and monto > 0");


		$conecta->consulta("Delete from invikta.ajustes where responsable='PRESTAMOS' and fec_pago='' and estatus=0");

		foreach ($periodos as $pe) 
		{
			$query="Insert into invikta.ajustes
					Select 0,'PRESTAMOS',p.clave_empleado,
							DATE_FORMAT(NOW(), '%d/%m/%Y') AS fecha_actual,'',
							ee.id_gerencia,$anio_p,$mes_p,-p.monto_pago AS ajuste, 
							CONCAT('PAGO ', pp.no_pago, ' DE ', pp.total_pagos, ' DE ', p.motivo) AS motivo,
							0
					FROM invikta.prestamos_inv p
						inner join invikta.prestamos_pagos_inv pp on (p.id_prestamos=pp.id_prestamos)
						left join invikta.empleados ee on (ee.clave = p.clave_empleado)
						left join invikta.gerencias gn on (ee.id_gerencia=gn.id_gerencia)
					where pp.no_pago = 1 
						and p.periodo='" . $pe["id"] . "' and p.fec_prestamo !='' and fec_desc=''  
						and DATEDIFF(NOW(), STR_TO_DATE(p.fec_prestamo, '%d/%m/%Y')) >= " . $pe["dias"] . "
						and (pp.fec_pago IS NULL OR pp.fec_pago = '')";
			$conecta->consulta($query);

			$query="Insert into invikta.ajustes
					Select 0,'PRESTAMOS',p.clave_empleado,
							DATE_FORMAT(NOW(), '%d/%m/%Y') AS fecha_actual,'',
							ee.id_gerencia,$anio_p,$mes_p,-p.monto_pago AS ajuste, 
							CONCAT('PAGO ', pp.no_pago, ' DE ', pp.total_pagos, ' DE ', p.motivo) AS motivo,
							0
					FROM invikta.prestamos_inv p
						inner join invikta.prestamos_pagos_inv pp on (p.id_prestamos=pp.id_prestamos)
						left join invikta.empleados ee on (ee.clave = p.clave_empleado)
						left join invikta.gerencias gn on (ee.id_gerencia=gn.id_gerencia)
					where pp.no_pago = 1 
						and p.periodo='" . $pe["id"] . "' and p.fec_prestamo !='' and fec_desc!=''  
						and STR_TO_DATE(fec_desc, '%d/%m/%Y') > CURDATE()
						and (pp.fec_pago IS NULL OR pp.fec_pago = '')";
			//$conecta->consulta($query);
		}

    	foreach ($periodos as $pe) 
        {
			
			// LEFT JOIN centros_costo_area cc ON ee.gerencia = cc.nombre_cc
			$query="Update invikta.prestamos_pagos_inv pp
						INNER JOIN invikta.prestamos_inv p ON pp.id_prestamos = p.id_prestamos
						LEFT JOIN invikta.empleados ee ON ee.clave = p.clave_empleado
					set pp.monto=p.monto_pago
					where pp.no_pago = 1 and p.periodo='" . $pe["id"] . "' and p.fec_prestamo !='' and fec_desc='' 
						and DATEDIFF(NOW(), STR_TO_DATE(p.fec_prestamo, '%d/%m/%Y')) >= " . $pe["dias"] . "
					    and (pp.fec_pago IS NULL OR pp.fec_pago = '')";
			$conecta->consulta($query);

			// LEFT JOIN centros_costo_area cc ON ee.gerencia = cc.nombre_cc
			$query="Update invikta.prestamos_pagos_inv pp
						INNER JOIN invikta.prestamos_inv p ON pp.id_prestamos = p.id_prestamos
						LEFT JOIN invikta.empleados ee ON ee.clave = p.clave_empleado
					set pp.monto=p.monto_pago
					where pp.no_pago = 1 and p.periodo='" . $pe["id"] . "' and p.fec_prestamo !='' and fec_desc!='' 
						and STR_TO_DATE(fec_desc, '%d/%m/%Y') > CURDATE()
					    and (pp.fec_pago IS NULL OR pp.fec_pago = '')";
			$conecta->consulta($query);
		}


		foreach ($periodos as $pe) 
		{
			$query="Insert into invikta.ajustes
					Select 0,'PRESTAMOS',p.clave_empleado,'$fec_pago' AS fecha_actual,
							'',ee.id_gerencia,$anio_p,$mes_p,-p.monto_pago AS ajuste,
							CONCAT('PAGO ', pp.no_pago, ' DE ', p.no_pagos, ' DE ', p.motivo) AS motivo,
						0
					from invikta.prestamos_inv p
						inner join invikta.prestamos_pagos_inv pp on (p.id_prestamos=pp.id_prestamos)
						inner join invikta.prestamos_pagos_inv pp_prev 
								on (pp_prev.id_prestamos = pp.id_prestamos 
										AND pp_prev.no_pago = pp.no_pago - 1)
						left join invikta.empleados ee on (ee.clave = p.clave_empleado)
						left join invikta.gerencias gn on (ee.id_gerencia=gn.id_gerencia)
					where pp.no_pago > 1 AND pp_prev.fec_pago != '' and detener_cobro=0 
						AND pp.fec_pago = '' and p.periodo='" . $pe["id"] . "'
						AND DATEDIFF(NOW(), STR_TO_DATE(pp_prev.fec_pago, '%d/%m/%Y')) >= " . $pe["dias"];
			$conecta->consulta($query);
		}

        foreach ($periodos as $pe) 
        {
			$query="Update invikta.prestamos_pagos_inv pp
						inner join invikta.prestamos_inv p on (pp.id_prestamos=p.id_prestamos)
						inner join invikta.prestamos_pagos_inv pp_prev 
								on (pp_prev.id_prestamos = pp.id_prestamos 
										AND pp_prev.no_pago = pp.no_pago - 1)
					set pp.monto = p.monto_pago
					where pp.no_pago > 1 AND pp_prev.fec_pago != '' AND pp.fec_pago = '' and detener_cobro=0
						and p.periodo='" . $pe["id"] . "'
					    AND DATEDIFF(NOW(), STR_TO_DATE(pp_prev.fec_pago, '%d/%m/%Y')) >= " . $pe["dias"];
			$conecta->consulta($query);
		}