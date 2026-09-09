<?php


		case 160:
			$empleado = addslashes(trim($_POST["empleado"]));
			$response = [];
			$query = "SELECT nombre
						FROM invikta.empleados 
						WHERE nombre =  '$empleado'";
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
					$values[] = "(0, $i, '',$monto_pago_double, $id_prestamos, '$clave', $total_pagos, '$periodo')";
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
					$e = $b[0]["socio"];
					$mesActual = date('m');
					$anioActual = date('Y');

					$tablaDestino = 'ajustes';
					
					if ($tablaDestino) 
					{
						$q_ajuste = "Insert INTO invikta.$tablaDestino VALUES (0, '$responsable', '$clave', '$fec_cap', '', '$e', $d, $anioActual, $mesActual, $monto_double, '$motivo', 0)";

						$conecta->consulta($q_ajuste);
					}
					$q_con_ajuste = "Select * FROM invikta.$tablaDestino 
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
				if ($semana < $no_semanas_ant) 
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