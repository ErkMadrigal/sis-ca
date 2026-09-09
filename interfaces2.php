<?
	include("./funciones/encripta.php");
	if(file_exists(__DIR__ . '/vendor/autoload.php'))
		include './vendor/autoload.php';
	else
	{
		include './PHPExcel/IOFactory.php';
		if(!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory') && class_exists('PHPExcel_IOFactory'))
			class_alias('PHPExcel_IOFactory', 'PhpOffice\\PhpSpreadsheet\\IOFactory');
		if(!class_exists('PhpOffice\\PhpSpreadsheet\\Shared\\Date') && class_exists('PHPExcel_Shared_Date'))
			class_alias('PHPExcel_Shared_Date', 'PhpOffice\\PhpSpreadsheet\\Shared\\Date');
	}

	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Shared\Date;

	$conecta= new conector();

	function convierte_fecha($fecha)
	{
		if($fecha != "")
		{
			if (is_numeric($fecha)) 
			{
	        	$fec= Date::excelToDateTimeObject($fecha);
	        	return $fec->format('d/m/Y');
	        }
	     	else 
	     		return "";
		}
		else
	     	return "";
    }

	$op= addslashes(trim($_POST["op"]));

	$pasa=0;

	if($op=="catalogo_nomina")
	{
		$archivo= $_FILES['subir-catalogo']['name'];
		$archivo_tem =$_FILES['subir-catalogo']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{
				try {
						$pag="pago_nomina.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();
		        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = trim($celda->getValue());
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}

		            	$cols = ['Código','Apellido paterno','Apellido materno','Nombre','RFC','CURP'];
		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[$columna])) 
		                		throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para actualizar el número de empleado Contpaq en SISCA");
		        		}

		        		$i=0;
		        		$conecta->consulta("Delete from carobra.empleados_contpaq");
						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$no_contpaq = $hoja->getCell($encabezados['Código'] . $fila)->getValue();
				            $paterno = trim($hoja->getCell($encabezados['Apellido paterno'] . $fila)->getValue());
				            $materno=trim($hoja->getCell($encabezados['Apellido materno'] . $fila)->getValue());
				            $nombre=trim($hoja->getCell($encabezados['Nombre'] . $fila)->getValue());
				            $rfc=trim($hoja->getCell($encabezados['RFC'] . $fila)->getValue());
				            $curp=trim($hoja->getCell($encabezados['CURP'] . $fila)->getValue());

				            $paterno=str_replace("'","",$paterno);
				            $materno=str_replace("'","",$materno);
				            $nombre=str_replace("'","",$nombre);

				            if($i==0)
				            {
								$sql = "Insert into carobra.empleados_contpaq (no_contpaq,nombre,paterno,materno,rfc,curp) values\n('$no_contpaq','$nombre','$paterno','$materno','$rfc','$curp')";
								$i++;
				            }
							else
							{
								if($i<=400)
								{
									$sql.=",\n ('$no_contpaq','$nombre','$paterno','$materno','$rfc','$curp')";
									$i++;
								}
								else
								{
									$sql.=",\n ('$no_contpaq','$nombre','$paterno','$materno','$rfc','$curp')";
									//echo $sql . ";<br><br>";
									$conecta->consulta($sql . ";");
									$i=0;
								}								
							}
				        }
						//echo $sql . ";<br><br>";
						$conecta->consulta($sql . ";");

						$conecta->consulta("Update carobra.empleados set no_contpaq=''");
						
				        $sql="Update carobra.empleados e, carobra.empleados_contpaq q
				        	  set e.no_contpaq=q.no_contpaq
				        	  where e.rfc=q.rfc and e.rfc !='' and e.curp=q.curp and e.curp !=''";

				        $bd = $conecta->prepare($sql);

				        if ($bd->execute())
    						$j=$bd->affected_rows;
/*
						$sql="Update carobra.empleados e, carobra.empleados_contpaq q
				        	  set e.no_contpaq=q.no_contpaq
				        	  where e.curp=q.curp and e.curp !='' and e.no_contpaq =''";
				        $bd = $conecta->prepare($sql);
				        if ($bd->execute())
    						$j+=$bd->affected_rows;
*/
    					$sql="update carobra.empleados e, carobra.usuarios u
	    					  set no_contpaq=''
	    					  where e.clave=u.clave and activo=0";
	    				$conecta->consulta($sql);

    					$sql="Select u.clave, q.no_contpaq,q.rfc,q.curp
							  FROM carobra.empleados_contpaq q
								LEFT JOIN carobra.empleados e ON (q.no_contpaq = e.no_contpaq)
								JOIN carobra.usuarios u 
							    	ON (u.nombre = CONCAT(q.nombre, ' ', q.paterno, ' ', q.materno))
							  WHERE activo=1 and (e.no_contpaq IS NULL OR e.no_contpaq = '') and u.nombre !=''";

						$bd=$conecta->consulta($sql);
						if ($bd->num_rows != 0)
						{
							while($tb=mysqli_fetch_array($bd))
							{
								$rfc=$tb["rfc"];
								$curp=$tb["curp"];
								$clave=$tb["clave"];
								$nq=$tb["no_contpaq"];
								$sql="Update carobra.empleados 
									  set no_contpaq='$nq',rfc='$rfc',curp='$curp'
									  where clave='$clave'";
								$conecta->consulta($sql);
								$j++;
							}
						}
	    				$mensaje="Se actualizaron $j en empleados SISCA";
						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje",$mensaje);
						echo "</form>";
						$pasa=1;
					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="pago_nomina")
	{	
		$archivo= $_FILES['subir-nomina']['name'];
		$archivo_tem =$_FILES['subir-nomina']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{				
				try {
						$pag="pago_nomina.php";
						$archivo="./produccion/" . $archivo;


						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();

		        		$j=0;
		        		$c=0;

		        		foreach ($hoja->getRowIterator() as $fila) 
		        		{
		        			$i=0;
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = trim($celda->getValue());		                		
		                		if (strpos($valor, "Fecha") !== false) 
		                		{
    								$f=explode(":", $valor);

									$fec = trim($f[1]);

									// Reemplazar "Abr" por "Apr"
									$fec = str_replace(
									    ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
									    ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
									    $fec);
									$fec = DateTime::createFromFormat("d/M/Y", $fec);
									$fecha = $fec->format("d/m/Y");
    							}
		                		if($valor=="Código")
		                			$j=1;
		                		if($i > 10)
		                			break;
		                		$i++;
		                	}
		                	$c++;
		                	if($j==1)
		                		break;
		            	}	            	
		        		foreach ($hoja->getRowIterator($c, $c) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = str_replace("*","",trim($celda->getValue()));
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}	
		            		            	
		            	$cols = ['Código','Empleado','Sueldo','NETO'];
		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[$columna])) 
		                		throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para subir el pago de nómina en SISCA");
		        		}
		        		$i=0;
		        		$men_empleados="";
		        		$bd = $conecta->prepare("Delete from carobra.pago_nomina where fecha='$fecha'");
						$bd->execute(); 
						for ($fila = ($c + 1); $fila <= $numeroFilas; $fila++)
						{
							if($hoja->getCell($encabezados['Código'] . $fila)->getValue() != "")
							{
								$no_contpaq = $hoja->getCell($encabezados['Código'] . $fila)->getValue();
								$empleado = $hoja->getCell($encabezados['Empleado'] . $fila)->getValue();
					            $monto = trim($hoja->getCell($encabezados['NETO'] . $fila)->getValue());

					            if(substr($no_contpaq, 0, 2)=="00")
					            {
									$sql = "Insert into carobra.pago_nomina
														(fecha,no_contpaq,monto)
									        select ?,?,?
									        where  exists (select 1 from carobra.empleados 
									        			   where no_contpaq = ?)";

							        $bd = $conecta->prepare($sql);
							        $bd->bind_param("ssds",$fecha,$no_contpaq,$monto,$no_contpaq);
							        if ($bd->execute()) 
							        {
								        if ($bd->affected_rows > 0)
								            $i++;
								        else
								        {
								        	if($men_empleados=="")
								        		$men_empleados=$no_contpaq . " --> " . $empleado;
								        	else
								        		$men_empleados.="\n" . $no_contpaq . " --> " . $empleado;
								        }
								    }					            	
					            }
							}
						}

						$mensaje="El archivo de pagos de nómina subió correctamente";
						if($men_empleados!="")
							$mensaje.="\n\n" . "Sin embargo hay uno o más números de empleado de Contpaq que no se encontró correspondencia en SISCA:\n\n" .  $men_empleados;

						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("dispersa","Ok");
							tex_h("fecha",$fecha);
							tex_h("mensaje",$mensaje);
						echo "</form>";
						$pasa=1;

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="inter_empleados")
	{

		$archivo= $_FILES['subir-interface']['name'];
		$archivo_tem =$_FILES['subir-interface']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{				
				try {
						$pag="inter_empleados.php";
						$archivo="./produccion/" . $archivo;


						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();

		        		$j=0;
		        		$c=0;	            	
		        		foreach ($hoja->getRowIterator($c, $c) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = str_replace("*","",trim($celda->getValue()));
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}	

		            	$cols = ['Clave','Nombre','Apellido Paterno','Apellido Materno','Fec Ingreso','NSS','CURP','RFC','NAP','Email','Telefono','Celular','Domicilio','Banco','CLABE','Staff','Categoria'];
		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[$columna])) 
		                		throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para subir el nuevo empleado");
		        		}
		        		
		        		$i=0;
		        		$men_empleados="";
		        		$signos=array("*","-","_");
						$letras=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$clave = $hoja->getCell($encabezados['Clave'] . $fila)->getValue();
							if(strlen(trim($clave)) > 0)
							{							
								$rfc=trim($hoja->getCell($encabezados['RFC'] . $fila)->getValue());
					            $curp=trim($hoja->getCell($encabezados['CURP'] . $fila)->getValue());
					            $nombre = $hoja->getCell($encabezados['Nombre'] . $fila)->getValue();
					            $clabe=trim($hoja->getCell($encabezados['CLABE'] . $fila)->getValue());
					            $banco=trim($hoja->getCell($encabezados['Banco'] . $fila)->getValue());
								$email=trim($hoja->getCell($encabezados['Email'] . $fila)->getValue());
								$cel=trim($hoja->getCell($encabezados['Celular'] . $fila)->getValue());
								$staff=trim($hoja->getCell($encabezados['Staff'] . $fila)->getValue());
								$tel=trim($hoja->getCell($encabezados['Telefono'] . $fila)->getValue());
								$domi=trim($hoja->getCell($encabezados['Domicilio'] . $fila)->getValue());
					            $categoria = $hoja->getCell($encabezados['Categoria'] . $fila)->getValue();
					            $apaterno = $hoja->getCell($encabezados['Apellido Paterno'] . $fila)->getValue();
					            $amaterno = $hoja->getCell($encabezados['Apellido Materno'] . $fila)->getValue();
					            $nap=str_pad(trim($hoja->getCell($encabezados['NAP'] . $fila)->getValue()), 10, "0", STR_PAD_LEFT);
					            $nss=str_pad(trim($hoja->getCell($encabezados['NSS'] . $fila)->getValue()), 11, "0", STR_PAD_LEFT);
					            $fec_ingreso = convierte_fecha(trim($hoja->getCell($encabezados['Fec Ingreso'] . $fila)->getValue()));


					            $nombre_compl=trim(trim($nombre) . ' ' . trim($apaterno) . ' ' . trim($amaterno));
					            $nombre_compl=str_replace('  ',' ',$nombre_compl);

								$contrasena="";
								$ic=$jc=1;
								while($ic <= 6)
								{
									$n=rand(0,25);
									if($jc==1)
									{
										$contrasena.= strtoupper($letras[$n]);
										$jc=2;
									}
									else
									{ 
										$contrasena.= $letras[$n];
										$jc=1;
									}
									$ic++;
								}
								$ic=1;
								while($ic <= 2)
								{
									$n=rand(0,2);
									$contrasena.=$signos[$n];
									$ic++;
								}
								$n=rand(0,99);
								if($n < 10)
									$contrasena.="0" . $n;
								else 
									$contrasena.= $n;

								$sql ="Insert into carobra.usuarios values('$clave','$nombre_compl','$contrasena',1,'$email','','',1,0,0)";
								$conecta->consulta($sql);
//echo $sql . "<br><br>";
								$men_empleados.="La contraseña para $nombre_compl ($clave) es: $contrasena<br>";

								$n_staff=0;
								if($staff=='SI')
									$n_staff=1;

								$n_cat=0;
								$bd=$conecta->consulta("Select id_categoria from carobra_2014.categorias where categoria='$categoria'");
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									$n_cat=$tb["id_categoria"];								
								}

								$sql="Insert into carobra.empleados
									  Select max(no_empleado) + 1 as no_empleado,'$clave','$fec_ingreso','$domi','$tel','$cel',0,'','','',0,'','$rfc','$curp','$nss',0,0,0,'',$n_cat,'','','',0,$n_staff,0 
									  from carobra.empleados";
								$conecta->consulta($sql);
//echo $sql . "<br><br>";
								$n_banco=0;
								$bd=$conecta->consulta("Select id_banco from carobra.bancos where banco='$banco'");
								if ($bd->num_rows != 0)
								{
									$tb=mysqli_fetch_array($bd);
									$n_banco=$tb["id_banco"];
								}

								$sql="Insert into carobra.usuarios_banco
									  Select no_empleado,'$nombre','$apaterno','$amaterno',$n_banco,'$clabe',1 
									  from carobra.empleados where clave='$clave'";
								$conecta->consulta($sql);
//echo $sql . "<br><br>";
							}
				        }

						$mensaje="La interface de empleados subió correctamente";

						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje",$men_empleados);
						echo "</form>";
						$pasa=1;

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="hoja_captura")
	{
		header('Content-Type: application/json; charset=utf-8');

		function hoja_captura_valor($hoja, $celda)
		{
			return trim((string)$hoja->getCell($celda)->getFormattedValue());
		}

		function limpia_texto_captura($valor)
		{
			$valor = str_replace(array("\xC2\xA0", chr(194) . chr(160)), ' ', (string)$valor);
			$valor = preg_replace('/[\x{200B}-\x{200D}\x{202A}-\x{202E}\x{2060}\x{FEFF}]/u', '', $valor);
			return trim($valor);
		}

		function limpia_telefono_captura($valor)
		{
			return preg_replace('/[^0-9]/', '', limpia_texto_captura($valor));
		}

		function limpia_compacto_captura($valor)
		{
			$valor = limpia_texto_captura($valor);
			return preg_replace('/[\s\p{Z}\p{C}]+/u', '', $valor);
		}

		$folio_txt = addslashes(trim($_POST["txtfolio"]));
		$archivo = $_FILES['file-hoja-captura']['name'];
		$archivo_tem = $_FILES['file-hoja-captura']['tmp_name'];

		if(strlen($archivo) == 0 || !is_uploaded_file($archivo_tem))
		{
			echo json_encode(array("ok"=>false,"mensaje"=>"No se seleccionó un archivo de hoja de captura."));
			exit;
		}

		try
		{
			$spreadsheet = IOFactory::load($archivo_tem);
			$hoja = $spreadsheet->getActiveSheet();

			$folio_hoja = hoja_captura_valor($hoja, "B6");
			if($folio_hoja != $folio_txt)
			{
				echo json_encode(array("ok"=>false,"mensaje"=>"La información de la hoja de captura no corresponde al folio " . $folio_txt));
				exit;
			}

			$asesor=strtoupper(limpia_texto_captura(hoja_captura_valor($hoja, "B2")));
			$asesor=str_replace("'","",$asesor);
			$acompa=hoja_captura_valor($hoja, "B15");
			$salario=hoja_captura_valor($hoja, "B18");
			$bd=$conecta->consulta("Select 1 from carobra.usuarios where nombre='$asesor'");
			if (!$bd || $bd->num_rows == 0)
				$asesor="";

			$afore=limpia_compacto_captura(hoja_captura_valor($hoja, "B16"));
			$afore=str_replace("'","",$afore);
			$bd=$conecta->consulta("Select clave from afores where upper(afore)='" . strtoupper($afore) . "'");

			$afore=0;
			if ($bd && $bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$afore=$tb["clave"];
			}
			$empresa=strtoupper(limpia_texto_captura(hoja_captura_valor($hoja, "B19")));
			if($empresa=="")
				$empresa="NO TIENE";
			$datos = array(
				"asesor" => $asesor,
				"apovol" => hoja_captura_valor($hoja, "B3"),
				"cel" => limpia_telefono_captura(hoja_captura_valor($hoja, "B10")),
				"tel" => limpia_telefono_captura(hoja_captura_valor($hoja, "B11")),
				"email" => limpia_compacto_captura(hoja_captura_valor($hoja, "B12")),
				"acompa" => str_replace(array("$",","),'', $acompa),
				"afore" => $afore,
				"salario" => str_replace(array("$",","),'', $salario),
				"empresa" => $empresa,
				"cp" => hoja_captura_valor($hoja, "B21")
			);
			echo json_encode(array("ok"=>true,"datos"=>$datos));
			exit;
		}
		catch (Exception $e)
		{
			echo json_encode(array("ok"=>false,"mensaje"=>$e->getMessage()));
			exit;
		}
	}

	if($op=="inter_av1000")
	{
		$archivo= $_FILES['subir-archivo']['name'];
		$archivo_tem =$_FILES['subir-archivo']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{
				try {
						$pag="inter_AV1000.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();
		        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = trim($celda->getValue());
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}

		            	$cols = ['Aportación Voluntaria','CURP','Fecha Enviado','NAP','Oportunidad PFG','Folio P2000'];
		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[$columna])) 
		                		throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para la interface AV1000");
		        		}

		        		$i=0;
		        		$j=0;
						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$folio = $hoja->getCell($encabezados['Aportación Voluntaria'] . $fila)->getValue();
				            $curp=trim($hoja->getCell($encabezados['CURP'] . $fila)->getValue());
				            $fec_envio = convierte_fecha(trim($hoja->getCell($encabezados['Fecha Enviado'] . $fila)->getValue()));
				            $nap=str_pad(trim($hoja->getCell($encabezados['NAP'] . $fila)->getValue()), 10, "0", STR_PAD_LEFT);
				            $cliente=str_replace("OP-","",trim($hoja->getCell($encabezados['Oportunidad PFG'] . $fila)->getValue()));
				            $tramite=trim($hoja->getCell($encabezados['Folio P2000'] . $fila)->getValue());

							$sql = "Insert into av_general
							        select 'P','$folio','$tramite',0,0,0,0,'','$curp','$nap',
							        	   '$cliente','',0,'','',''
							        from av_general
							        where not exists (select 1 from av_general where folio = '$folio')
							        limit 1";

							$nr = $conecta->consulta($sql);
							if ($nr > 0) 
							{
					            $i++;
								$sql = "Insert into av_fechas values ('$folio','$tramite','','','$fec_envio','','','','','','','','','','','','')";
						        $conecta->consulta($sql);

								$sql = "Insert into av_pagos values ('$folio','$tramite','','','','','','','','','','',0,0,0,'','')";
						        $conecta->consulta($sql);
							}
							else
							{
								$j++;
					        	$sql_curp="";
					        	if(strlen($curp)==18)
					        		$sql_curp=", curp='$curp'";

					        	$sql="Update av_general 
					        		  set cliente='$cliente', nap='$nap' $sql_curp
					        		  where folio='$folio'";
					        	$conecta->consulta($sql);

					        	if($fec_envio !='')
					        	{
						        	$sql="Update av_fechas set fec_envio='$fec_envio'
						        		  where folio='$folio'";
						        	$conecta->consulta($sql);					        		
					        	}

							}
				        }

						$mensaje="El archivo de la interface AV1000 subió correctamente.\n Se insertaron $i registro(s) y se actualizaron $j";

						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje",$mensaje);
						echo "</form>";
						$pasa=1;
					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}


	

	// if($op == "inter_transfer")
	// {
	// 	$archivo = $_FILES['subir-archivo']['name'];
	// 	$archivo_tem = $_FILES['subir-archivo']['tmp_name'];

	// 	if (strlen($archivo) > 0) {
	// 		if (move_uploaded_file($archivo_tem, "./produccion/" . $archivo)) {
	// 			try {
	// 				$pag = "inter_transfer.php";
	// 				$archivo = "./produccion/" . $archivo;

	// 				$spreadsheet = IOFactory::load($archivo);
	// 				$hoja = $spreadsheet->getActiveSheet();

	// 				function limpiarTexto($texto) {
	// 					$texto = strtoupper(trim($texto));

	// 					$tmp = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
	// 					if ($tmp !== false) {
	// 						$texto = $tmp;
	// 					}

	// 					$texto = str_replace(['_', '-', '.'], ' ', $texto);
	// 					$texto = preg_replace('/\s+/', ' ', $texto);

	// 					return $texto;
	// 				}

	// 				$encabezados = [];
	// 				$numeroFilas = $hoja->getHighestRow();
	// 				foreach ($hoja->getRowIterator(1, 1) as $fila) {
	// 					foreach ($fila->getCellIterator() as $celda) {
	// 						$valor = trim($celda->getValue());
	// 						if (!empty($valor))
	// 							$encabezados[$valor] = $celda->getColumn();
	// 					}
	// 				}


	// 				$cols = [
	// 					'semana..',
	// 					'Mes_Procesar',
	// 					'NAP',
	// 					'Num_Formulario',
	// 					'cod_cuenta',
	// 					'Correo',
	// 					'Fecha Traspaso',
	// 					'ley',
	// 					'Afo Receptora',
	// 					'Entidad',
	// 					'Afore cedente',
	// 					'Rango Antiguedad',
	// 					'TEL_MOVIL',
	// 					'Cancelación',
	// 					'P+',
	// 					'Nombre',
	// 					'Apellido Paterno',
	// 					'Apellido Materno',
	// 					'Saldo',
	// 					'Fecha_alta',
	// 					'FOLIO_SOL_TRASPASO',
	// 					'FECHA_TRAMITE',
	// 				];


	// 				foreach ($cols as $columna) {
	// 					if (!isset($encabezados[$columna]))
	// 						throw new Exception("No se encontró la columna $columna");
	// 				}
	// 				$conecta->consulta("START TRANSACTION");

	// 				$bd_estados = $conecta->consulta("SELECT no_edo, estado FROM estados");
	// 				$bd_afores  = $conecta->consulta("SELECT clave, afore FROM afores");

	// 				$estadosMap = [];
	// 				foreach ($bd_estados as $row) {
	// 					$estado = trim($row['estado']);
	// 					if (strtoupper($estado) === 'MEXICO') {
	// 						$estado = 'ESTADO DE MEXICO';
	// 					}
	// 					$nombre = limpiarTexto($estado); // ya limpio
	// 					$estadosMap[$nombre] = $row['no_edo'];
	// 				}

	// 				$aforeMap = [];
	// 				foreach ($bd_afores as $row) {
	// 					$nombre = limpiarTexto($row['afore']);
	// 					$aforeMap[$nombre] = $row['clave'];
	// 				}

	// 				// PROCESO
	// 				$lote = [];
	// 				$tamanoLote = 10000;
	// 				$procesados = 0;

	// 				for ($fila = 2; $fila <= $numeroFilas; $fila++) {

	// 					$semana = trim($hoja->getCell($encabezados['semana..'] . $fila)->getValue()); 
	// 					$mes_procesar = trim((string)$hoja->getCell($encabezados['Mes_Procesar'] . $fila)->getFormattedValue());
	// 					$cod_cuenta = trim($hoja->getCell($encabezados['cod_cuenta'] . $fila)->getValue()); 
	// 					$ley = trim($hoja->getCell($encabezados['ley'] . $fila)->getValue()); 
	// 					$cancelacion = trim($hoja->getCell($encabezados['Cancelación'] . $fila)->getValue()); 
	// 					$p = trim((string)$hoja->getCell($encabezados['P+'] . $fila)->getValue());
	// 					$p = ($p === '' ? 0 : (int)$p);
						
	// 					$correo = trim($hoja->getCell($encabezados['Correo'] . $fila)->getValue());

	// 					$nombre = trim($hoja->getCell($encabezados['Nombre'] . $fila)->getValue());
	// 					$apellido_paterno = trim($hoja->getCell($encabezados['Apellido Paterno'] . $fila)->getValue());
	// 					$apellido_materno = trim($hoja->getCell($encabezados['Apellido Materno'] . $fila)->getValue());
						
	// 					$nombre_completo = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
	// 					$Saldo = trim($hoja->getCell($encabezados['Saldo'] . $fila)->getValue());
	// 					$Fecha_alta = convierte_fecha(
	// 						trim($hoja->getCell($encabezados['Fecha_alta'] . $fila)->getValue())
	// 					);

	// 					$Fecha_tramite = convierte_fecha(
	// 						trim($hoja->getCell($encabezados['FECHA_TRAMITE'] . $fila)->getValue())
	// 					);

	// 					$FOLIO_SOL_TRASPASO = trim($hoja->getCell($encabezados['FOLIO_SOL_TRASPASO'] . $fila)->getValue());


	// 					$TEL_MOVIL = trim($hoja->getCell($encabezados['TEL_MOVIL'] . $fila)->getValue());


	// 					$num_formulario = trim($hoja->getCell($encabezados['Num_Formulario'].$fila)->getValue());

	// 					if ($num_formulario == '')
	// 						continue;

	// 					$num_formulario = (int)$num_formulario;

	// 					$NAP = "";
	// 					$valorNAP = trim((string)$hoja->getCell($encabezados['NAP'] . $fila)->getValue());

	// 					if ($valorNAP !== '' && strlen($valorNAP) >= 7 && strlen($valorNAP) <= 10) {
	// 						$NAP = str_pad($valorNAP, 10, "0", STR_PAD_LEFT);
	// 					}

	// 					$fecha_Traspaso = convierte_fecha(trim($hoja->getCell($encabezados['Fecha Traspaso'] . $fila)->getValue()));


	// 					// AFORE RECEPTORA
	// 					$Afo_Receptora = trim($hoja->getCell($encabezados['Afo Receptora'] . $fila)->getValue());
	// 					$Afo_Receptora = preg_replace('/^\d+\s*/', '', $Afo_Receptora);
	// 					$Afo_Receptora = limpiarTexto($Afo_Receptora);

	// 					$idAfoReceptora = $aforeMap[$Afo_Receptora] ?? 0;
	// 					$idAfoReceptora = $idAfoReceptora ?: 0;

	// 					// ENTIDAD
	// 					$Entidad = trim($hoja->getCell($encabezados['Entidad'] . $fila)->getValue());
	// 					$Entidad = preg_replace('/^\d+\s*/', '', $Entidad);
	// 					$Entidad = limpiarTexto($Entidad);

	// 					$idEntidad = $estadosMap[$Entidad] ?? 0;
	// 					$idEntidad = $idEntidad ?: 0;

	// 					// AFORE CEDENTE
	// 					$Afore_cedente = trim($hoja->getCell($encabezados['Afore cedente'] . $fila)->getValue());
	// 					$Afore_cedente = addslashes(explode(' ', $Afore_cedente)[0]);

	// 					// ANTIGUEDAD
	// 					$Rango_Antiguedad = trim($hoja->getCell($encabezados['Rango Antiguedad'] . $fila)->getValue());
	// 					$Rango_Antiguedad = preg_replace('/^\d+\.\s*/', '', $Rango_Antiguedad);
	// 					$Rango_Antiguedad = addslashes($Rango_Antiguedad);

	// 					$nombre_completo = addslashes($nombre_completo);
	// 					$Saldo = addslashes($Saldo);
	// 					$Fecha_alta = addslashes($Fecha_alta);

	// 					$Fecha_tramite = addslashes($Fecha_tramite);
						
	// 					$FOLIO_SOL_TRASPASO = addslashes($FOLIO_SOL_TRASPASO);

	// 					$semana = addslashes($semana);
	// 					$mes_procesar = addslashes($mes_procesar);
	// 					$cod_cuenta = addslashes($cod_cuenta);
	// 					$ley = addslashes($ley);
	// 					$cancelacion = addslashes($cancelacion);

	// 					$NAP = addslashes($NAP);
	// 					$fecha_Traspaso = addslashes($fecha_Traspaso);

	// 					if ($correo != '' || $TEL_MOVIL != '') {

	// 						$correo = addslashes($correo);
	// 						$TEL_MOVIL = addslashes($TEL_MOVIL);
	// 					}

	// 					// INSERT
	// 					$lote[] = "($num_formulario, '$NAP', '$fecha_Traspaso', $idAfoReceptora, '$Afore_cedente', $idEntidad, '$Rango_Antiguedad', '$semana', '$mes_procesar', '$cod_cuenta', '$ley', '$cancelacion', '$p', '$nombre_completo', '$Saldo', '$Fecha_alta', '$FOLIO_SOL_TRASPASO', '$Fecha_tramite', '$TEL_MOVIL', '$correo')";
	// 					$procesados++;

						

	// 					if (count($lote) >= $tamanoLote) {

	// 						$sql_insert = "INSERT INTO clawback 
	// 						(folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad, semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo, saldo, fec_alta, sol_traspaso, fec_tramite, telefono, email_trab ) 
	// 						VALUES " . implode(',', $lote) . " 
	// 						ON DUPLICATE KEY UPDATE cancelacion = VALUES(cancelacion)";
	// 						// echo $sql_insert . "\n\n";
	// 						$conecta->consulta($sql_insert);
	// 						$lote = [];
	// 					}
	// 				}

	// 				if (!empty($lote)) {
	// 					$sql_insert = "INSERT INTO clawback 
	// 					(folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad, semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo, saldo, fec_alta, sol_traspaso, fec_tramite, telefono, email_trab ) 
	// 					VALUES " . implode(',', $lote) . " 
	// 					ON DUPLICATE KEY UPDATE cancelacion = VALUES(cancelacion)";
	// 					// echo $sql_insert . "\n\n";
	// 					$conecta->consulta($sql_insert);
	// 				}

	// 					$conecta->consulta("COMMIT");
					
	// 					if (file_exists($archivo)) {
	// 						unlink($archivo);
	// 					}
	// 					$numeroFilas = $numeroFilas - 1; // Excluir fila de encabezados
	// 					// $mensaje="El archivo de la Interface transfer out subió correctamente.\n Se actualizaron $numeroFilas registro(s)";
	// 					$mensaje = "El archivo se subió correctamente.\nSe procesaron $procesados registro(s).";

	// 					echo "<form id='envia' action='$pag' method='post'>";
	// 						tex_h("mensaje",$mensaje);
	// 					echo "</form>";
	// 					$pasa=1;
	// 				}catch (Exception $e){
	// 					$conecta->consulta("ROLLBACK");

	// 					if (file_exists($archivo)) {
	// 						unlink($archivo);
	// 					}

	// 					echo "<form id='envia' action='$pag' method='post'>";
	// 						tex_h("mensaje",$e->getMessage());
	// 					echo "</form>";
	// 				}

	// 		}
	// 	}
	// }

	if ($op == "inter_transfer")
	{
		$archivo     = $_FILES['subir-archivo']['name']     ?? '';
		$archivo_tem = $_FILES['subir-archivo']['tmp_name']  ?? '';
		$error_subida = $_FILES['subir-archivo']['error']    ?? UPLOAD_ERR_NO_FILE;

		/* ── 1. Revisar el código de error del upload antes que nada ── */
		if ($error_subida !== UPLOAD_ERR_OK) {

			$mapaErrores = [
				UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor (upload_max_filesize).',
				UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido por el formulario.',
				UPLOAD_ERR_PARTIAL    => 'El archivo se subió solo parcialmente. Intenta de nuevo.',
				UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
				UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
				UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el servidor.',
				UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.',
			];

			$mensaje = $mapaErrores[$error_subida] ?? ('Error desconocido al subir el archivo (código ' . $error_subida . ').');
			$pag     = "interfaces2.php";

			echo "<form id='envia' action='$pag' method='post'>";
				tex_h("mensaje", $mensaje);
			echo "</form>";

		} elseif (strlen($archivo) > 0) {

			$destino = "./produccion/" . $archivo;

			/* ── 2. Si move_uploaded_file falla, reportarlo ── */
			if (!move_uploaded_file($archivo_tem, $destino)) {

				$mensaje = 'No se pudo guardar el archivo en el servidor. Revisa permisos de la carpeta ./produccion/ o si ya existe un archivo con ese nombre bloqueado.';
				$pag     = "interfaces2.php";

				echo "<form id='envia' action='$pag' method='post'>";
					tex_h("mensaje", $mensaje);
				echo "</form>";

			} else {

				try {
					/* Límites subidos antes de que PhpSpreadsheet cargue el archivo */
					ini_set('memory_limit', '512M');
					set_time_limit(120);

					$pag     = "interfaces2.php";
					$archivo = $destino;

					$spreadsheet = IOFactory::load($archivo);
					$hoja        = $spreadsheet->getActiveSheet();

					function txt($valor) {
						return trim((string) $valor);
					}

					function limpiarTexto($texto) {
						$texto = strtoupper(trim((string) $texto));

						$tmp = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
						if ($tmp !== false) {
							$texto = $tmp;
						}

						$texto = str_replace(['_', '-', '.'], ' ', $texto);
						$texto = preg_replace('/\s+/', ' ', $texto);
						$texto = trim($texto);

						return $texto;
					}

					function leerFecha($hoja, $columna, $fila) {
						$celda = $hoja->getCell($columna . $fila);
						$valor = $celda->getValue();

						if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($celda)) {
							try {
								$fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);
								return $fechaObj->format('d/m/Y');
							} catch (\Throwable $e) {
								return '';
							}
						}

						$texto = trim((string) $valor);
						if ($texto === '') return '';

						if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $texto, $m)) {
							return sprintf('%02d/%02d/%04d', (int) $m[1], (int) $m[2], (int) $m[3]);
						}

						return '';
					}

					/* Inserta un lote; si el INSERT completo truena, reintenta
					fila por fila para aislar cuál(es) folio(s) están mal, en
					vez de perder el lote entero por una sola fila corrupta. */
					function insertarLote($conecta, array $lote, array $folios, $updateCompleto, array &$erroresSQL) {
						if (empty($lote)) return 0;

						$columnas = "(folio, nap, fec_traspaso, afo_receptora, afo_cedente, estado, antiguedad, semana, mes_procesar, cod_cuenta, ley, cancelacion, pplus, nombre_Completo, saldo, fec_alta, sol_traspaso, num_formulario, fec_tramite, telefono, email_trab)";

						$sql = "INSERT INTO clawback $columnas VALUES " . implode(',', $lote) . " ON DUPLICATE KEY UPDATE " . $updateCompleto;

						if ($conecta->consulta($sql)) {
							return count($lote);
						}

						if (count($lote) === 1) {
							$erroresSQL[] = [
								'folio'   => $folios[0],
								'mensaje' => mysqli_error($conecta->conexion),
							];
							return 0;
						}

						$insertadosOk = 0;
						foreach ($lote as $i => $fila) {
							$sqlFila = "INSERT INTO clawback $columnas VALUES $fila ON DUPLICATE KEY UPDATE " . $updateCompleto;
							if ($conecta->consulta($sqlFila)) {
								$insertadosOk++;
							} else {
								$erroresSQL[] = [
									'folio'   => $folios[$i],
									'mensaje' => mysqli_error($conecta->conexion),
								];
							}
						}
						return $insertadosOk;
					}

					$encabezados = [];
					$numeroFilas = $hoja->getHighestRow();

					foreach ($hoja->getRowIterator(1, 1) as $fila) {
						foreach ($fila->getCellIterator() as $celda) {
							$valorOriginal = trim((string) $celda->getValue());
							if ($valorOriginal === '') continue;
							$clave = limpiarTexto($valorOriginal);
							$encabezados[$clave] = $celda->getColumn();
						}
					}

					$cols = [
						'semana..',
						'Mes_Procesar',
						'NAP',
						'Num_Formulario',
						'cod_cuenta',
						'Correo',
						'Fecha Traspaso',
						'ley',
						'Afo Receptora',
						'Entidad',
						'Afore cedente',
						'Rango Antiguedad',
						'TEL_MOVIL',
						'Cancelación',
						'P+',
						'Nombre',
						'Apellido Paterno',
						'Apellido Materno',
						'Saldo',
						'Fecha_alta',
						'FOLIO_SOL_TRASPASO',
						'FECHA_TRAMITE',
					];

					$colEncontrada = [];
					$faltantes     = [];

					foreach ($cols as $columna) {
						$clave = limpiarTexto($columna);
						if (isset($encabezados[$clave])) {
							$colEncontrada[$columna] = $encabezados[$clave];
						} else {
							$faltantes[] = $columna;
						}
					}

					if (!empty($faltantes)) {
						throw new Exception(
							"No se encontraron estas columnas en el Excel: " . implode(', ', $faltantes) .
							". Encabezados detectados en el archivo: " . implode(', ', array_keys($encabezados))
						);
					}

					$bd_estados = $conecta->consulta("SELECT no_edo, estado FROM estados");
					$bd_afores  = $conecta->consulta("SELECT clave, afore FROM afores");

					$estadosMap = [];
					foreach ($bd_estados as $row) {
						$estado = txt($row['estado']);
						if (strtoupper($estado) === 'MEXICO') {
							$estado = 'ESTADO DE MEXICO';
						}
						$estadosMap[limpiarTexto($estado)] = $row['no_edo'];
					}

					$aforeMap = [];
					foreach ($bd_afores as $row) {
						$aforeMap[limpiarTexto($row['afore'])] = $row['clave'];
					}

					/* Ya no hay un solo START TRANSACTION/COMMIT envolviendo
					el archivo completo: cada lote se inserta (y si falla, se
					aísla fila por fila) de forma independiente. */

					$lote        = [];
					$folioLote   = [];
					$tamanoLote  = 200;
					$procesados  = 0;
					$omitidos    = 0;
					$insertados  = 0;
					$erroresSQL  = [];

					$updateCompleto =
						"nap=VALUES(nap), fec_traspaso=VALUES(fec_traspaso), " .
						"afo_receptora=VALUES(afo_receptora), afo_cedente=VALUES(afo_cedente), " .
						"estado=VALUES(estado), antiguedad=VALUES(antiguedad), " .
						"semana=VALUES(semana), mes_procesar=VALUES(mes_procesar), " .
						"cod_cuenta=VALUES(cod_cuenta), ley=VALUES(ley), " .
						"cancelacion=VALUES(cancelacion), pplus=VALUES(pplus), " .
						"nombre_Completo=VALUES(nombre_Completo), saldo=VALUES(saldo), " .
						"fec_alta=VALUES(fec_alta), sol_traspaso=VALUES(sol_traspaso), " .
						"num_formulario=VALUES(num_formulario), fec_tramite=VALUES(fec_tramite), " .
						"telefono=VALUES(telefono), email_trab=VALUES(email_trab)";

					for ($fila = 2; $fila <= $numeroFilas; $fila++) {

						$semana       = txt($hoja->getCell($colEncontrada['semana..'] . $fila)->getValue());
						$mes_procesar = trim((string) $hoja->getCell($colEncontrada['Mes_Procesar'] . $fila)->getFormattedValue());
						$cod_cuenta   = txt($hoja->getCell($colEncontrada['cod_cuenta'] . $fila)->getValue());
						$ley          = txt($hoja->getCell($colEncontrada['ley'] . $fila)->getValue());
						$cancelacion  = txt($hoja->getCell($colEncontrada['Cancelación'] . $fila)->getValue());
						$p            = trim((string) $hoja->getCell($colEncontrada['P+'] . $fila)->getValue());
						$p            = ($p === '' ? 0 : (int) $p);

						$correo = txt($hoja->getCell($colEncontrada['Correo'] . $fila)->getValue());

						$nombre           = txt($hoja->getCell($colEncontrada['Nombre'] . $fila)->getValue());
						$apellido_paterno = txt($hoja->getCell($colEncontrada['Apellido Paterno'] . $fila)->getValue());
						$apellido_materno = txt($hoja->getCell($colEncontrada['Apellido Materno'] . $fila)->getValue());

						$nombre_completo = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
						$Saldo           = txt($hoja->getCell($colEncontrada['Saldo'] . $fila)->getValue());
						$Fecha_alta      = leerFecha($hoja, $colEncontrada['Fecha_alta'], $fila);
						$Fecha_tramite   = leerFecha($hoja, $colEncontrada['FECHA_TRAMITE'], $fila);

						$FOLIO_SOL_TRASPASO = txt($hoja->getCell($colEncontrada['FOLIO_SOL_TRASPASO'] . $fila)->getValue());
						$TEL_MOVIL          = txt($hoja->getCell($colEncontrada['TEL_MOVIL'] . $fila)->getValue());
						$num_formulario     = txt($hoja->getCell($colEncontrada['Num_Formulario'] . $fila)->getValue());

						if ($num_formulario == '') continue;
						$num_formulario = (int) $num_formulario;

						$NAP        = "";
						$valorNAP   = trim((string) $hoja->getCell($colEncontrada['NAP'] . $fila)->getValue());
						if ($valorNAP !== '' && strlen($valorNAP) >= 7 && strlen($valorNAP) <= 10) {
							$NAP = str_pad($valorNAP, 10, "0", STR_PAD_LEFT);
						}

						if ($nombre === '' || $apellido_paterno === '' || $cod_cuenta === '' || $valorNAP === '') {
							$omitidos++;
							continue;
						}

						if (strlen((string) $num_formulario) > 10) {
							$omitidos++;
							continue;
						}

						$fecha_Traspaso = leerFecha($hoja, $colEncontrada['Fecha Traspaso'], $fila);

						$Afo_Receptora = txt($hoja->getCell($colEncontrada['Afo Receptora'] . $fila)->getValue());
						$Afo_Receptora = preg_replace('/^\d+\s*/', '', $Afo_Receptora);
						$Afo_Receptora = limpiarTexto($Afo_Receptora);
						$idAfoReceptora = $aforeMap[$Afo_Receptora] ?? 0;

						$Entidad = txt($hoja->getCell($colEncontrada['Entidad'] . $fila)->getValue());
						$Entidad = preg_replace('/^\d+\s*/', '', $Entidad);
						$Entidad = limpiarTexto($Entidad);
						$idEntidad = $estadosMap[$Entidad] ?? 0;

						$Afore_cedente_txt = txt($hoja->getCell($colEncontrada['Afore cedente'] . $fila)->getValue());
						$Afore_cedente_txt = preg_replace('/^\d+\s*/', '', $Afore_cedente_txt);
						$Afore_cedente_txt = limpiarTexto($Afore_cedente_txt);
						$idAforeCedente    = $aforeMap[$Afore_cedente_txt] ?? 0;

						$Rango_Antiguedad = txt($hoja->getCell($colEncontrada['Rango Antiguedad'] . $fila)->getValue());
						$Rango_Antiguedad = preg_replace('/^\d+\.\s*/', '', $Rango_Antiguedad);
						$Rango_Antiguedad = addslashes($Rango_Antiguedad);

						$nombre_completo    = addslashes($nombre_completo);
						$Saldo              = addslashes($Saldo);
						$Fecha_alta         = addslashes($Fecha_alta);
						$Fecha_tramite      = addslashes($Fecha_tramite);
						$FOLIO_SOL_TRASPASO = addslashes($FOLIO_SOL_TRASPASO);
						$semana             = addslashes($semana);
						$mes_procesar       = addslashes($mes_procesar);
						$cod_cuenta         = addslashes($cod_cuenta);
						$ley                = addslashes($ley);
						$cancelacion        = addslashes($cancelacion);
						$NAP                = addslashes($NAP);
						$fecha_Traspaso     = addslashes($fecha_Traspaso);

						if ($correo != '' || $TEL_MOVIL != '') {
							$correo    = addslashes($correo);
							$TEL_MOVIL = addslashes($TEL_MOVIL);
						}

						$lote[]      = "('$num_formulario', '$NAP', '$fecha_Traspaso', $idAfoReceptora, $idAforeCedente, $idEntidad, '$Rango_Antiguedad', '$semana', '$mes_procesar', '$cod_cuenta', '$ley', '$cancelacion', '$p', '$nombre_completo', '$Saldo', '$Fecha_alta', '$FOLIO_SOL_TRASPASO', '$num_formulario', '$Fecha_tramite', '$TEL_MOVIL', '$correo')";
						$folioLote[] = $num_formulario;
						$procesados++;

						if (count($lote) >= $tamanoLote) {
							$insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
							$lote      = [];
							$folioLote = [];
						}
					}

					if (!empty($lote)) {
						$insertados += insertarLote($conecta, $lote, $folioLote, $updateCompleto, $erroresSQL);
					}

					if (file_exists($archivo)) {
						unlink($archivo);
					}

					$mensaje = "El archivo se subió correctamente.\nSe procesaron $procesados registro(s), de los cuales $insertados se guardaron en la base de datos.";
					if ($omitidos > 0) {
						$mensaje .= "\nSe ignoraron $omitidos fila(s) que no parecían datos reales (totales/pie del reporte u otros datos incompletos).";
					}
					if (!empty($erroresSQL)) {
						$mensaje .= "\n\nOJO: " . count($erroresSQL) . " folio(s) NO se pudieron guardar por error de base de datos:";
						foreach (array_slice($erroresSQL, 0, 20) as $e) {
							$mensaje .= "\n  Folio {$e['folio']}: {$e['mensaje']}";
						}
						if (count($erroresSQL) > 20) {
							$mensaje .= "\n  ... y " . (count($erroresSQL) - 20) . " más.";
						}
					}

					echo "<form id='envia' action='$pag' method='post'>";
						tex_h("mensaje", $mensaje);
					echo "</form>";
					$pasa = 1;

				} catch (\Throwable $e) {

					if (isset($archivo) && file_exists($archivo)) {
						unlink($archivo);
					}

					$pag = $pag ?? "interfaces2.php";

					echo "<form id='envia' action='$pag' method='post'>";
						tex_h("mensaje", $e->getMessage() . " (línea " . $e->getLine() . ")");
					echo "</form>";
				}

			}

		}
	}

	if($op=="inter_av5")
	{
		$archivo= $_FILES['subir-archivo']['name'];
		$archivo_tem =$_FILES['subir-archivo']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{
				try {
						$pag="inter_AV5.php";
						$archivo="./produccion/" . $archivo;
						$fec_mes_ant = strtotime ( '-35 days' , strtotime ( date('Y-m-j')));
						$fec_mes_ant = date ( 'd/m/Y' , $fec_mes_ant);

						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();
		        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = trim($celda->getValue());
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}

		            	$cols = ['NAP','Folio de trámite','Nombre Completo','CURP','Importe','NSS','Estatus de la Aportación Voluntaria'];

		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[$columna])) 
		                		throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para la interface AV5");
		        		}

		        		$mapaEstatus = [
						        			'Aceptada' => 1,
						        			'VIGENTE' => 2,
						        			'Pendiente' => 3,
						        			'Enviado' => 4,
						        			'En Captura' => 5,
						        			'Modificado' => 6,
						        			'Hold' => 7,
						        			'Cancelada' => 8,
						        			'Rechazada' => 9,
						        			'-' => 10,
						        		];

		        		$registros = [];
						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$registro = [];
							foreach ($cols as $columna)
							{
								$registro[$columna] = trim((string)$hoja->getCell($encabezados[$columna] . $fila)->getFormattedValue());
							}

							if ($registro['CURP'] === '')
								continue;

							$estatus = $registro['Estatus de la Aportación Voluntaria'];
							if (!isset($mapaEstatus[$estatus]))
								throw new Exception("Estatus no reconocido en la fila $fila: $estatus");

							$registro['NAP'] = str_pad($registro['NAP'], 10, "0", STR_PAD_LEFT);
							$registro['NSS'] = str_pad($registro['NSS'], 11, "0", STR_PAD_LEFT);
							$registro['id_estatus'] = $mapaEstatus[$estatus];

							$registros[] = $registro;
						}
						
						usort($registros, function ($a, $b) 
						{
					        $cmp = strcasecmp(trim($a['CURP']), trim($b['CURP']));
					        if ($cmp !== 0)
					                return $cmp;

					        $cmp = ((int)$a['id_estatus']) <=> ((int)$b['id_estatus']);
					        if ($cmp !== 0)
					                return $cmp;

					        return strcasecmp(trim($b['Folio de trámite']), trim($a['Folio de trámite']));
						  });

		        		$i=0;
		        		$curp_base="";
						foreach ($registros as $registro)
						{
							$folio = $registro['Folio de trámite'];
				            $curp = $registro['CURP'];
				            $nss = $registro['NSS'];
				            $nap = $registro['NAP'];
				            $cliente = $registro['Nombre Completo'];
							$monto = str_replace(['$',',',' '],"",$registro['Importe']);
				            $estatus = $registro['Estatus de la Aportación Voluntaria'];
				            $id_estatus = $registro['id_estatus'];

				            if($curp!="-" and trim($curp) !="" and $curp_base!=$curp)
				            {
					        	if($folio !='-' and trim($folio) !='')
					        	{
									$i++;
					        		$sql_curp="";
					        		if(strlen($curp)==18)
					        			$sql_curp=", curp='$curp'";

						        	$sql="Update av_general 
						        		  set cliente='$cliente', nap='$nap', nss='$nss' $sql_curp
						        		  where folio='$folio';";
						        	$conecta->consulta($sql);
//echo $sql . "<br><br>";
						        	$sql="Update av_pagos set estatus='$estatus', monto=$monto
						        		  where folio='$folio'";

						        	$conecta->consulta($sql);
						        	//$conecta->multi_consulta($sql);
//echo $sql . "<br><br>";
						        	if(strlen($curp)==18)
						        	{
										$query="Select folio_pr,fec_envio, p.id_estatus_prod 
													from (Select * from av_fechas where folio='$folio') avf
														inner join apovol_prod ap on (avf.tramite=ap.folio_av)
													inner join produccion p on (ap.folio_pr=p.folio)
												where p.id_estatus_prod != 6";
//echo $query . "<br><br>";
										$bd=$conecta->consulta($query);
										if ($bd->num_rows == 0)
										{
											$query="Select p.folio,fec_traspaso from produccion p
														inner join (Select folio from prod_trabajador 
																	where curp='$curp') pt 
															on (p.folio=pt.folio)
													where str_to_date(fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_mes_ant','%d/%m/%Y') 
														and id_estatus_prod!=6
													order by str_to_date(fec_traspaso,'%d/%m/%Y') desc
													limit 0,1";
											$bd=$conecta->consulta($query);
//echo $query . "<br><br>";
											if ($bd->num_rows != 0)
											{
												$tb=mysqli_fetch_array($bd);
												$folio_pr=$tb["folio"];

												$sql="Delete from apovol_prod where folio_pr='$folio_pr'";
												$conecta->consulta($sql);

												$sql="Insert into apovol_prod
													  Select distinct avg.tramite,'$folio_pr','C',1
													  from av_general avg
													  	inner join av_fechas avf 
													  			on (avg.tramite=avf.tramite)
													  where avg.folio='$folio' and avg.tramite != ''
													  	and avf.fec_envio !=''";
												$conecta->consulta($sql);
//echo $sql . "<br><br>";
											}
										}
									}
						        }
						        $curp_base=$curp;
						    }
				        }
						$mensaje="El archivo de la interface AV5 subió correctamente.\n Se actualizaron $i registro(s)";

						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje",$mensaje);
						echo "</form>";
						$pasa=1;
					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="inter_cumplimiento")
	{
		$fecha= addslashes(trim($_POST["txt-fecha"]));
		$periodo= addslashes(trim($_POST["sel-periodo"]));

		$c=explode("-", $periodo);
		$anio=$c[0];
		$periodo=$c[1];

		$f = DateTime::createFromFormat("Y-m-d", $fecha);
		$fecha = $f->format("d/m/Y");

		$archivo= $_FILES['subir-cumplimiento']['name'];
		$archivo_tem =$_FILES['subir-cumplimiento']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{
				try {
						$pag="inter_cumplimiento.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
						
						$nom_hojas = ['base folios','base por Asesor','Criterios'];

						$error=0;
						$pasa_aclara=0;
						foreach ($nom_hojas as $hoja_nombre) 
						{
					    	if($hoja_nombre=="base folios")
					    	{
								$hoja = $spreadsheet->getSheetByName($hoja_nombre);

							    if ($hoja === null)
							    {
							    	$error=1;
							        $mensaje="No se encontró la hoja $hoja_nombre";
							    }
							    else
							    {
					        		$encabezados = [];
					        		$numeroFilas = $hoja->getHighestRow();
					        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
					        		{
					            		foreach ($fila->getCellIterator() as $celda) 
					            		{
					                		$valor = trim($celda->getValue());
					                		if (!empty($valor))
					                		{
												$encabezados[$valor] = $celda->getColumn();
					                		}
					                	}
					            	}

					            	$cols = ['FOLIO',' SALDO','NUM. ASESOR'];
					        		foreach ($cols as $columna) 
					        		{
					            		if (!isset($encabezados[trim($columna)]))
					                		throw new Exception("No se encontró la columna " . trim($columna) . ", puede que el archivo no contenga el layout requerido para la interface de Cumplimiento");
					        		}
					        		$i=1;
					        		$aclara=0;
					        		$conecta->consulta("Delete from cumplimiento where anio=$anio and periodo='$periodo'");
					        		$sql="Insert into cumplimiento values ";
									for ($fila = 2; $fila <= $numeroFilas; $fila++)
									{
										$folio =$hoja->getCell($encabezados['FOLIO'] . $fila)->getValue();
										if($folio!='')
										{
								            $saldo=trim($hoja->getCell($encabezados['SALDO'] . $fila)->getValue());
								            $nap=str_pad(trim($hoja->getCell($encabezados['NUM. ASESOR'] . $fila)->getValue()), 10, "0", STR_PAD_LEFT);

								            $sql.="($anio,'$periodo','$fecha','$folio',$saldo,'$nap',0,0,0,0)";
								            if($i==200)
								            {
								            	$i=0;
								            	$sql.=";";
								            	$conecta->consulta($sql);
								            	$sql="Insert into cumplimiento values ";
								            }
								            else
								            	$sql.=", ";
											$i++;
										}
									}
									if($i > 1)
									{
						            	$sql=rtrim(trim($sql), ',') . ";";
						            	$conecta->consulta($sql);
									}
								}
					    	}
					    	else
					    	{
					    		if($hoja_nombre=="base por Asesor")
									$cols = ['NUM. ASESOR','AV','Registro',];

								if($hoja_nombre=="Criterios")
									$cols = ['NUM. ASESOR','cruces de AV','Registro','ACLARACIONES DARACA'];
								$hoja = $spreadsheet->getSheetByName($hoja_nombre);

								if ($hoja !== null)
								{
									$pasa_aclara=1;

					        		$encabezados = [];
					        		$numeroFilas = $hoja->getHighestRow();
					        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
					        		{
					            		foreach ($fila->getCellIterator() as $celda) 
					            		{
					                		$valor = trim($celda->getValue());
					                		if (!empty($valor)) 
					                    		$encabezados[$valor] = $celda->getColumn();
					                	}
					            	}
									$no_aclaraciones=0;
					        		foreach ($cols as $columna) 
					        		{
					            		if (!isset($encabezados[trim($columna)]))
					            		{
					            			if(trim($columna) !="ACLARACIONES DARACA")
					                			throw new Exception("No se encontró la columna " . trim($columna) . ", puede que el archivo no contenga el layout requerido para la interface de Cumplimiento");
					                		else
					                			$no_aclaraciones=1;
					            		}
					        		}

									$avCase = "";
									$regCase = "";
									$pagoCase = "";
									$aclaraCase = "";
									$aclara=0;
									for ($fila = 2; $fila <= $numeroFilas; $fila++)
									{
										$nap=str_pad(trim($hoja->getCell($encabezados[$cols[0]] . $fila)->getValue()), 10, "0", STR_PAD_LEFT);
										$av =$hoja->getCell($encabezados[$cols[1]] . $fila)->getValue();
							            $reg=trim($hoja->getCell($encabezados[$cols[2]] . $fila)->getValue());

							            if($hoja_nombre=="Criterios")
							            {
							            	$aclara=0;
							            	if($no_aclaraciones==0)
							            	{
								            	$ac=trim($hoja->getCell($encabezados[$cols[3]] . $fila)->getValue());
								            	if($ac=='ACLARACION')
								            		$aclara=1;							            		
							            	}
							            }
							            $pago=0;
							            if($av > 0 or $reg > 0)
							            	$pago=1;

							            if($nap!='0000000000')
							            {
								            $avCase .= "WHEN nap = '$nap' THEN $av\n";
								            $regCase.= "WHEN nap = '$nap' THEN $reg\n";
								            $pagoCase.="WHEN nap = '$nap' THEN $pago\n";
								            $aclaraCase.="WHEN nap = '$nap' THEN $aclara\n";
							            }
							            else
							            	$fila= $numeroFilas + 1;
							        }

					            	$sql="Update cumplimiento
									      SET avs = CASE
									                	$avCase
									                	else 0
									            	END,
							            	  registros = CASE
							                					$regCase
							                					else 0
							            				  END,
							            	  pago = CASE
							                			$pagoCase
							                			else 0
									            	 END,
									          aclaraciones= CASE
									                			$aclaraCase
									                			else 0
											            	END
									      where anio=$anio and periodo='$periodo'";
									$conecta->consulta($sql);
								}
					    	}
						}
						if($pasa_aclara==1)
						{
							echo "<form id='envia' action='$pag' method='post'>";
								tex_h("mensaje","Interface ejecutada con éxito");
							echo "</form>";
							$pasa=1;							
						}
						else
						{
					    	$error=1;
					        $mensaje="No se encontró la hoja base por Asesor ni la de Criterios";
						}

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="inter_movilidad")
	{
		$bloqueo=0;
		$fec_bloqueo="";
		$bd3=$conecta->consulta("Select * from bloqueo_inter");
		if ($bd3->num_rows != 0)
		{
			$tb3=$bd3->fetch_array(MYSQLI_NUM);
			$fec_bloqueo=$tb3[0];
			$hora_bloqueo=$tb3[1];
		}
		if(date("d/m/Y")==$fec_bloqueo)
		{
			if(date("G")>= $hora_bloqueo)
				$bloqueo=1;
		}

		if($_SESSION["nombre"]=="Administrador del Sistema")
			$bloqueo=0;

		if($bloqueo==0)
		{
			$archivo= $_FILES['subir-movilidad']['name'];
			$archivo_tem =$_FILES['subir-movilidad']['tmp_name'];

			if(strlen($archivo) > 0)
			{
				if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
				{
					try {
							$pag="inter_movilidad.php";
							$archivo="./produccion/" . $archivo;

							$spreadsheet = IOFactory::load($archivo);
			        		$hoja = $spreadsheet->getActiveSheet();

							$error=0;
							$pasa_aclara=0;
			        		$encabezados = [];
			        		$numeroFilas = $hoja->getHighestRow();
			        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
			        		{
			            		foreach ($fila->getCellIterator() as $celda) 
			            		{
			                		$valor = strtolower(trim($celda->getValue()));
			                		if (!empty($valor)) 
			                    		$encabezados[$valor] = $celda->getColumn();
			                	}
			            	}

			            	$cols = ['folio solicitud','fecha de certificación anterior','fecha de registro','clave de afore cedente','rango'];
			        		foreach ($cols as $columna) 
			        		{
			            		if (!isset($encabezados[trim($columna)]))
			                		throw new Exception("No se encontró la columna " . trim($columna) . ", puede que el archivo no contenga el layout requerido para la interface de Movilidad");
			        		}

			        		$sql="";
			        		$aclara=0;
			        		$fec_captura=date("d/m/Y");
			        		$sql="Insert into movilidad (folio,fec_captura,fec_sello,afore,movilidad) values ";
			        		$i=1;
							for ($fila = 2; $fila <= $numeroFilas; $fila++)
							{
								$folio =$hoja->getCell($encabezados['folio solicitud'] . $fila)->getValue();
	 		  					$fec_sello = convertirFecha(trim($hoja->getCell($encabezados['fecha de certificación anterior'] . $fila)->getValue()));
	// 		  					if($fec_sello == "")
	// 		  						$fec_sello = convertirFecha(trim($hoja->getCell($encabezados['fecha de registro'] . $fila)->getValue()));
	 		  					$afore=$hoja->getCell($encabezados['clave de afore cedente'] . $fila)->getValue();
					            $movilidad=$hoja->getCell($encabezados['rango'] . $fila)->getValue();

					            if($movilidad==2)
					            	$movilidad=1;
					            if($folio!="")
					            {
					            	$sql.=" ('$folio','$fec_captura','$fec_sello',$afore,$movilidad),";
					            	if($i==600)
					            	{
						            	$sql=rtrim(trim($sql), ',') . " on duplicate key update fec_sello=values(fec_sello), 
						            		 fec_captura=values(fec_captura), movilidad=values(movilidad);";
//echo $sql . "<br><br>";
						            	$conecta->consulta($sql);

						            	$sql="Insert into movilidad (folio,fec_captura,fec_sello,afore,movilidad) values ";
						            	$i=0;
					            	}
					            }
					            $i++;
							}
							if($i > 1)
							{
				            	$sql=rtrim(trim($sql), ',') . " on duplicate key update fec_sello=values(fec_sello), fec_captura=values(fec_captura), movilidad=values(movilidad);";
//echo $sql . "<br><br>";
				            	$conecta->consulta($sql);							
							}
							echo "<form id='envia' action='$pag' method='post'>";
								tex_h("mensaje","Interface ejecutada con éxito");
							echo "</form>";
							$pasa=1;

						} catch (Exception $e) 	{
													echo "<form id='envia' action='$pag' method='post'>";
														tex_h("mensaje",$e->getMessage());
													echo "</form>";
													$pasa=1;
												}
				}
			}
		}
		else
			mensaje("En estos momentos no se puede ejecutar la interface. Esta ejecutandose el proceso de pagos.","inter_movilidad.php");
	}

	if($op=="inter_pagos_pi")
	{
		$per= addslashes(trim($_POST["sel-mes-pago"]));
		$d=explode("|", $per);
		$mes=$d[0];
		$anio=$d[1];

		$archivo= $_FILES['subir-interface']['name'];
		$archivo_tem =$_FILES['subir-interface']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{			
				try {
						$pag="interface_pagos_pi.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
						
						$nom_hojas = ['Cuota_PI','Inscripciones'];

						$fecha=date("d/m/Y");
						foreach ($nom_hojas as $hoja_nombre) 
						{
					    	if($hoja_nombre=="Cuota_PI")
					    	{	
								$hoja = $spreadsheet->getSheetByName($hoja_nombre);

							    if ($hoja === null)
							    {
							    	$error=1;
							        $mensaje="No se encontró la hoja $hoja_nombre";
							    }
							    else
							    {
					        		$encabezados = [];
					        		$numeroFilas = $hoja->getHighestRow();
				        		
					        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
					        		{
					            		foreach ($fila->getCellIterator() as $celda) 
					            		{
					                		$valor = trim($celda->getValue());
					                		if (!empty($valor)) 
					                    		$encabezados[$valor] = $celda->getColumn();
					                	}
					            	}

					            	$cols = ['ID','Banco','Forma pago','Cuota PI','Monto','Estatus','Fec. Pago']; 
					        		foreach ($cols as $columna) 
					        		{
					            		if (!isset($encabezados[trim($columna)]))
					                		throw new Exception("No se encontró la columna " . trim($columna) . ", puede que el archivo no contenga el layout requerido para la interface de Pagos");
					        		}

									$sql="Update plan_ideal.clientes c, plan_ideal.pagos p
										  set saldo_cliente=saldo_cliente-saldo_mov
										  where c.id_cliente=p.id_cliente and anio=$anio and mes=$mes and no_pago=1";
									$conecta->consulta($sql);

									$conecta->consulta("Drop table IF EXISTS plan_ideal.pagos_backup");

									$sql="Create table plan_ideal.pagos_backup as
										  Select * from plan_ideal.pagos where anio=$anio and mes=$mes";
									$conecta->consulta($sql);

					        		$conecta->consulta("Delete from plan_ideal.pagos where anio=$anio and mes=$mes and motivo='MENSUAL'");
					        		$sql="Delete from plan_ideal.pagos
					        			  where id_pago in (Select id_pago 
					        			  					from (Select id_pago from plan_ideal.pagos
					        			  						  where anio=$anio and mes=$mes and no_pago=1 and motivo!='MENSUAL') p)";
					        		$conecta->consulta($sql);

				        			$bd=$conecta->consulta("Select ifnull(max(id_pago),0) + 1 as id_pago from plan_ideal.pagos");
									$tb=mysqli_fetch_array($bd);
									$id_pago=$tb["id_pago"];

					        		$sql="Insert into plan_ideal.pagos values ";
									for ($fila = 2; $fila <= $numeroFilas; $fila++)
									{									
										$estatus =strtoupper($hoja->getCell($encabezados['Estatus'] . $fila)->getValue());
										if($estatus=="ACTIVO")
										{
											$id =$hoja->getCell($encabezados['ID'] . $fila)->getValue();	
								            $forma=strtoupper(trim($hoja->getCell($encabezados['Forma pago'] . $fila)->getValue()));
								            $cuota=$hoja->getCell($encabezados['Cuota PI'] . $fila)->getValue();
								            $monto=$hoja->getCell($encabezados['Monto'] . $fila)->getValue();
								            $fec_pago=convierte_fecha(trim($hoja->getCell($encabezados['Fec. Pago'] . $fila)->getValue()));
								            $banco=trim($hoja->getCell($encabezados['Banco'] . $fila)->getValue());
								            $saldo=$monto-$cuota;
								            $bd=$conecta->consulta("Select id_banco from plan_ideal.bancos where banco='$banco'");
								            if ($bd->num_rows != 0)
								            {
												$tb=mysqli_fetch_array($bd);
												$id_banco=$tb["id_banco"];
								            }
								            else
								            	$id_banco=0;

								            if($forma=="MENSUAL")
								            {
								            	$sql.="($id_pago,'$fecha',1,$id,$anio,$mes,'$fec_pago',$cuota,$id_banco,'','$forma',$monto,0,0,$saldo),";
								            	$id_pago++;
								            }							            
								            else
								            {
								            	switch ($forma) 
								            	{
								            		case 'BIMESTRAL': $n=2; break;
								            		case 'SEMESTRAL': $n=6; break;
								            		case 'ANUAL': $n=12; break;
								            	}
								            	$i=1;
								            	$m=$mes;
								            	$a=$anio;
								            	while($i <=$n)
								            	{
								            		$sql.="($id_pago,'$fecha',$i,$id,$a,$m,'$fec_pago',$cuota,$id_banco,'','$forma',$monto,0,0,$saldo),";
								            		if($i==1)
								            			$cuota=$monto=0;
								            			$saldo=0;
								            		$m++;
								            		if($m > 12)
								            		{
								            			$m=1;
								            			$a++;
								            		}
								            		$i++;
								            	}
								            	$id_pago++;
								            }
								        }
									}
					            	$sql=rtrim(trim($sql), ',') . ";";
//echo $sql;
					            	$conecta->consulta($sql);

					            	$sql="Update plan_ideal.pagos p
						            		join plan_ideal.pagos_backup pb on (p.id_cliente=pb.id_cliente and p.anio=pb.anio and p.mes=pb.mes)
						            	  set p.generado_recibo=pb.generado_recibo, p.envio_recibo=pb.envio_recibo";
						            $conecta->consulta($sql);

					            	$sql="Update plan_ideal.clientes c, plan_ideal.pagos p
										  set saldo_cliente=saldo_cliente + saldo_mov
										  where c.id_cliente=p.id_cliente and anio=$anio and mes=$mes and no_pago=1";
									$conecta->consulta($sql);

									$conecta->consulta("Drop table IF EXISTS plan_ideal.pagos_backup");
								}
					    	}
						}
						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje","Interface ejecutada con éxito");
						echo "</form>";
						$pasa=1;

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="inter_inscrip_pi")
	{
		$per= addslashes(trim($_POST["sel-mes-pago"]));
		$d=explode("|", $per);
		$mes=$d[0];
		$anio=$d[1];

		$archivo= $_FILES['subir-interface']['name'];
		$archivo_tem =$_FILES['subir-interface']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{			
				try {
						$pag="inter_inscrip_pi.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
						
						$nom_hojas = ['Cuota_PI','Inscripciones'];

						$fecha=date("d/m/Y");
						foreach ($nom_hojas as $hoja_nombre) 
						{
				    		if($hoja_nombre=="Inscripciones")
				    		{
								$hoja = $spreadsheet->getSheetByName($hoja_nombre);

							    if ($hoja === null)
							    {
							    	$error=1;
							        $mensaje="No se encontró la hoja $hoja_nombre";
							    }
							    else
							    {
					        		$encabezados = [];
					        		$numeroFilas = $hoja->getHighestRow();
				        		
					        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
					        		{
					            		foreach ($fila->getCellIterator() as $celda) 
					            		{
					                		$valor = trim($celda->getValue());
					                		if (!empty($valor)) 
					                    		$encabezados[$valor] = $celda->getColumn();
					                	}
					            	}

					            	$cols = ['ID','Estatus','Inscripcion','Monto','Fec. Pago']; 
					        		foreach ($cols as $columna) 
					        		{
					            		if (!isset($encabezados[trim($columna)]))
					                		throw new Exception("No se encontró la columna " . trim($columna) . ", puede que el archivo no contenga el layout requerido para la interface de Pagos");
					        		}

					        		$conecta->consulta("Delete from plan_ideal.pagos_inscripciones where anio=$anio and mes=$mes");
					        		
					        		$sql="Insert into plan_ideal.pagos_inscripciones values ";
									for ($fila = 2; $fila <= $numeroFilas; $fila++)
									{
						        		$estatus =strtoupper($hoja->getCell($encabezados['Estatus'] . $fila)->getValue());
										if($estatus=="ACTIVO")
										{
											$id =$hoja->getCell($encabezados['ID'] . $fila)->getValue();	
								            $monto=$hoja->getCell($encabezados['Monto'] . $fila)->getValue();
								            $fec_pago=convierte_fecha(trim($hoja->getCell($encabezados['Fec. Pago'] . $fila)->getValue()));
											
											$sql.="('$fecha',$id,'$fec_pago',$anio,$mes,$monto),";
										}
									}
					            	$sql=rtrim(trim($sql), ',') . ";";
//echo $sql;
					            	$conecta->consulta($sql);
					        	}
				    		}
						}
						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje","Interface ejecutada con éxito");
						echo "</form>";
						$pasa=1;

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}


	if($op=="inter_cancela")
	{
		$archivo= $_FILES['subir-interface']['name'];
		$archivo_tem =$_FILES['subir-interface']['tmp_name'];

		if(strlen($archivo) > 0)
		{
			if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
			{
				try {
						$pag="inter_cancela.php";
						$archivo="./produccion/" . $archivo;

						$spreadsheet = IOFactory::load($archivo);
		        		$hoja = $spreadsheet->getActiveSheet();

		        		$encabezados = [];
		        		$numeroFilas = $hoja->getHighestRow();
		        		foreach ($hoja->getRowIterator(1, 1) as $fila) 
		        		{
		            		foreach ($fila->getCellIterator() as $celda) 
		            		{
		                		$valor = strtoupper(trim($celda->getValue()));
		                		if (!empty($valor)) 
		                    		$encabezados[$valor] = $celda->getColumn();
		                	}
		            	}

		            	$cols = ['Mes_Procesar','Semana certificación','Num_Formulario','Fecha Traspaso','FECHA_TRAMITE','Probale fecha Certificación','Saldo','Afo Receptora','Afore cedente','Rango Antiguedad','Fecha_alta','TEL_MOVIL','Cancelación','NAP'];

		        		foreach ($cols as $columna) 
		        		{
		            		if (!isset($encabezados[strtoupper($columna)]))
		            		{
		            			if($columna!='CANCELACIóN')
		                			throw new Exception("No se encontró la columna $columna, puede que el archivo no contenga el layout requerido para la interface de cancelaciones");
		            		}
		        		}
		        		$i=0;
		        		$cache_afore = [];
		        		$fec_captura=date("d/m/Y");
		        		$sql="Insert into cancelaciones (folio,trabajador,nap,curp,asesor,recuperador,fec_traspaso,fec_tramite,fec_certifica,fec_alta,saldo,afo_receptora,afo_cedente,antiguedad,cel,cancelacion,mes_procesar,sem_certifica) values";

						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$folio = $hoja->getCell($encabezados['NUM_FORMULARIO'] . $fila)->getValue();
							$nap = $hoja->getCell($encabezados['NAP'] . $fila)->getValue();
							$mes_procesar = $hoja->getCell($encabezados['MES_PROCESAR'] . $fila)->getValue();
							$sem_certifica = $hoja->getCell($encabezados['SEMANA CERTIFICACIóN'] . $fila)->getValue();
							$saldo = $hoja->getCell($encabezados['SALDO'] . $fila)->getValue();
							$cel = $hoja->getCell($encabezados['TEL_MOVIL'] . $fila)->getValue();
							$cancela = $hoja->getCell($encabezados['CANCELACIóN'] . $fila)->getValue();
							$fec_traspaso=convierte_fecha(trim($hoja->getCell($encabezados['FECHA TRASPASO'] . $fila)->getValue()));
							$fec_tramite=trim($hoja->getCell($encabezados['FECHA_TRAMITE'] . $fila)->getValue());
							$fec_certifica=convierte_fecha(trim($hoja->getCell($encabezados['PROBALE FECHA CERTIFICACIóN'] . $fila)->getValue()));
							$fec_alta=substr(trim($hoja->getCell($encabezados['FECHA_ALTA'] . $fila)->getValue()), 0,10);
							$afo_receptora = $hoja->getCell($encabezados['AFO RECEPTORA'] . $fila)->getValue();
							$afo_cedente = substr(trim($hoja->getCell($encabezados['AFORE CEDENTE'] . $fila)->getValue()),0,3);
							$antiguedad = substr(trim($hoja->getCell($encabezados['RANGO ANTIGUEDAD'] . $fila)->getValue()),0,1);

    						if (!isset($cache_afore[$afo_receptora])) 
    						{
						        $bd=$conecta->consulta("Select clave FROM afores WHERE afore like '%$afo_receptora%'");
						        if ($bd->num_rows != 0)
						        {
						        	$tb=mysqli_fetch_array($bd);
						        	$cache_empresas[$afo_receptora] = $tb["clave"];
						        	$afo_receptora = $tb["clave"];
						        }
						    } 
						    else 
						        $afo_receptora = $cache_afore[$afo_receptora];

						    if($afo_cedente=="")
						    	$afo_cedente=0;
						    if($afo_cedente=="Afi")
						    	$afo_cedente=100;

						    if(strtoupper(trim($mes_procesar))!="TOTAL" and $mes_procesar!="")
						    	$sql.="('$folio','','$nap','','','','$fec_captura','$fec_traspaso','$fec_tramite','$fec_certifica','$fec_alta',$saldo,$afo_receptora,$afo_cedente,$antiguedad,'$cel',$cancela,'$mes_procesar','$sem_certifica'),";
						    else
						    	$fila = $numeroFilas + 1;
						    $i++;
						}
		            	$sql=rtrim(trim($sql), ',') . " ON DUPLICATE KEY UPDATE cancelacion = values(cancelacion);";

		            	$conecta->consulta($sql);
		            	$sql="Update carobra_2014.cancelaciones c
		            			join carobra_2014.produccion p on (c.folio=p.folio)
		            			join carobra_2014.prod_trabajador pt on (c.folio=pt.folio)
		            			join carobra.empleados e on (p.no_empleado=e.no_empleado)
		            		  set c.asesor=e.clave, c.trabajador=pt.trabajador, c.curp=pt.curp
		            		  where asesor=''";
		            	$conecta->consulta($sql);

		            	$anio=date("Y")-2;

		            	while($anio >= 2017)
		            	{
			            	$sql="Update carobra_2014.cancelaciones c
			            			join carobra_respaldos.produccion_$anio p on (c.folio=p.folio)
			            			join carobra_respaldos.prod_trabajador_$anio pt on (c.folio=pt.folio)
			            			join carobra.empleados e on (p.no_empleado=e.no_empleado)
			            		  set c.asesor=e.clave, c.trabajador=pt.trabajador, c.curp=pt.curp
			            		  where asesor=''";
			            	$conecta->consulta($sql);
			            	$anio--;
		            	}

		            	$sql="Update carobra_2014.cancelaciones c
		            			join carobra_respaldos.respaldo_produccion p on (c.folio=p.folio)
		            			join carobra_respaldos.respaldo_prod_trabajador pt on (c.folio=pt.folio)
		            			join carobra.empleados e on (p.no_empleado=e.no_empleado)
		            		  set c.asesor=e.clave, c.trabajador=pt.trabajador, c.curp=pt.curp
		            		  where asesor=''";
		            	$conecta->consulta($sql);

						echo "<form id='envia' action='$pag' method='post'>";
							tex_h("mensaje","Interface ejecutada con éxito");
						echo "</form>";
						$pasa=1;

					} catch (Exception $e) 	{
												echo "<form id='envia' action='$pag' method='post'>";
													tex_h("mensaje",$e->getMessage());
												echo "</form>";
												$pasa=1;
											}
			}
		}
	}

	if($op=="inter_actualiza_cuotas")
	{
		header('Content-Type: application/json; charset=utf-8');

		$archivo= $_FILES['file-excel']['name'];
		$archivo_tem =$_FILES['file-excel']['tmp_name'];

		function normaliza_header2($txt)
		{
			$txt = strtoupper(trim($txt));
			$txt = preg_replace('/\s+/',' ',$txt);
			return $txt;
		}

		function to_number2($valor)
		{
			$valor = trim($valor);
			if($valor==="") return 0;
			$valor = str_replace(['$',',',' '],'',$valor);
			$valor = str_replace(["\t","\r","\n"],'',$valor);
			return is_numeric($valor) ? (float)$valor : 0;
		}

		if(strlen($archivo) == 0)
		{
			echo json_encode(["ok"=>false,"mensaje"=>"No se recibió archivo."]);
			exit;
		}

		if(!preg_match("/\.(xls|xlsx)$/i",$archivo))
		{
			echo json_encode(["ok"=>false,"mensaje"=>"El archivo debe ser Excel (.xls o .xlsx)."]);
			exit;
		}

		if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
		{
			try {
					$archivo="./produccion/" . $archivo;
					$spreadsheet = IOFactory::load($archivo);
	        		$hoja = $spreadsheet->getActiveSheet();

	        		$encabezados = [];
	        		foreach ($hoja->getRowIterator(1, 1) as $fila)
	        		{
	            		foreach ($fila->getCellIterator() as $celda)
	            		{
	                		$valor = normaliza_header2($celda->getValue());
	                		if ($valor != "")
	                			$encabezados[$valor] = $celda->getColumn();
	                	}
	            	}

	            	$cols = ["ID CLIENTE","MONTO"];
	        		foreach ($cols as $columna)
	        		{
	            		if (!isset($encabezados[$columna]))
	                	{
	                		echo json_encode(["ok"=>false,"mensaje"=>"No se encontró la columna $columna en el archivo."]);
	                		exit;
	                	}
	        		}

	        		$numeroFilas = $hoja->getHighestRow();
	        		$actualizados = 0;
	        		for ($fila = 2; $fila <= $numeroFilas; $fila++)
	        		{
	        			$id_cliente = trim($hoja->getCell($encabezados["ID CLIENTE"] . $fila)->getValue());
	        			$monto = to_number2($hoja->getCell($encabezados["MONTO"] . $fila)->getValue());

	        			if($id_cliente==="")
	        				continue;

	        			$sql = "Update plan_ideal.clientes set cuota_plan_ideal=$monto where clave_pideal='$id_cliente'";
	        			$conecta->consulta($sql);
	        			$actualizados++;
	        		}

					echo json_encode(["ok"=>true,"mensaje"=>"Se actualizaron $actualizados registros."]);
					exit;
				} catch (Exception $e) {
					echo json_encode(["ok"=>false,"mensaje"=>$e->getMessage()]);
					exit;
				}
		}
		else
		{
			echo json_encode(["ok"=>false,"mensaje"=>"No se pudo guardar el archivo."]);
			exit;
		}
	}

	if($op=="inter_cuotas_pi")
	{

		header('Content-Type: application/json; charset=utf-8');


		$sal_rdp = addslashes(trim($_POST["txt-sal-minimo-RDP"]));
		$sal_lfn = addslashes(trim($_POST["txt-sal-minimo-LFN"]));

		$archivo= $_FILES['file-excel']['name'];
		$archivo_tem =$_FILES['file-excel']['tmp_name'];

		function normaliza_header($txt)
		{
			$txt = strtoupper(trim($txt));
			$txt = preg_replace('/\s+/',' ',$txt);
			return $txt;
		}

		function to_number($valor)
		{
			$valor = trim($valor);
			if($valor==="") return 0;
			$valor = str_replace(['$',',',' '],'',$valor);
			$valor = str_replace(["\t","\r","\n"],'',$valor);
			return is_numeric($valor) ? (float)$valor : 0;
		}

		if(strlen($archivo) == 0)
		{
			echo json_encode(["ok"=>false,"mensaje"=>"No se recibió archivo."]);
			exit;
		}

		if(!preg_match("/\.(xls|xlsx)$/i",$archivo))
		{
			echo json_encode(["ok"=>false,"mensaje"=>"El archivo debe ser Excel (.xls o .xlsx)."]);
			exit;
		}

		if($sal_rdp=="" || $sal_lfn=="")
		{
			echo json_encode(["ok"=>false,"mensaje"=>"Faltan salarios mínimos."]);
			exit;
		}

		if(move_uploaded_file($archivo_tem,"./produccion/" . $archivo))
		{
				try {
					$archivo="./produccion/" . $archivo;

					$spreadsheet = IOFactory::load($archivo);

					$sheetNames = $spreadsheet->getSheetNames();
					$hojas = ["Zona Fronteriza","Resto del Pais"];
					sort($sheetNames);
					$hojas_sorted = $hojas;
					sort($hojas_sorted);
					if(count($sheetNames) != 2 || $sheetNames !== $hojas_sorted)
					{
						echo json_encode(["ok"=>false,"mensaje"=>"Las hojas deben llamarse exactamente: Zona Fronteriza y Resto del Pais."]);
						exit;
					}
					$anio = date("Y");
					$fec_cap = date("d/m/Y");

					$eliminar = 0;
					foreach ($hojas as $nombre_hoja)
					{
						$hoja = $spreadsheet->getSheetByName($nombre_hoja);
						if(!$hoja)
						{
							echo json_encode(["ok"=>false,"mensaje"=>"No se encontró la hoja $nombre_hoja."]);
							exit;
						}
						$numeroFilas = $hoja->getHighestRow();

						$encabezados = [];
						foreach ($hoja->getRowIterator(1, 1) as $fila)
						{
							foreach ($fila->getCellIterator() as $celda)
							{
								$valor = normaliza_header($celda->getValue());
								if($valor != "")
									$encabezados[$valor] = $celda->getColumn();
							}
						}

						$cols = ["DIVISION","GRUPO PRINCIPAL","MENSUAL","BIMESTRAL","SEMESTRAL","ANUAL"];
						foreach ($cols as $columna)
						{
							if (!isset($encabezados[$columna]))
							{
								echo json_encode(["ok"=>false,"mensaje"=>"No se encontró la columna $columna en la hoja $nombre_hoja."]);
								exit;
							}
						}

						$zona_g = ($nombre_hoja=="Zona Fronteriza") ? "ZONA_LFN" : "ZONA_RDP";
						$salario_minimo = ($nombre_hoja=="Zona Fronteriza") ? (float)$sal_lfn : (float)$sal_rdp;

						$i=0;
						$sql = "";

						if($eliminar==0)
						{
							$conecta->consulta("Delete from plan_ideal.cuotas_plan_ideal where anio='$anio'");
							$eliminar=1;
						}

						for ($fila = 2; $fila <= $numeroFilas; $fila++)
						{
							$division = trim($hoja->getCell($encabezados["DIVISION"] . $fila)->getValue());
							$grupo = trim($hoja->getCell($encabezados["GRUPO PRINCIPAL"] . $fila)->getValue());
							$mensual = to_number($hoja->getCell($encabezados["MENSUAL"] . $fila)->getValue());
							$bimestral = to_number($hoja->getCell($encabezados["BIMESTRAL"] . $fila)->getValue());
							$semestral = to_number($hoja->getCell($encabezados["SEMESTRAL"] . $fila)->getValue());
							$anual = to_number($hoja->getCell($encabezados["ANUAL"] . $fila)->getValue());

							if($division=="" && $grupo=="" && $mensual==0 && $bimestral==0 && $semestral==0 && $anual==0)
								continue;

							$division = str_replace("'","",$division);
							$grupo = str_replace("'","",$grupo);

							if($i==0)
							{
								$sql = "Insert into plan_ideal.cuotas_plan_ideal (zona_g,division,grupo,salario_minimo,costo_carobra_mensual,costo_carobra_bimestral,costo_carobra_semestral,costo_carobra_anual,costo_cliente_mensual,costo_cliente_bimestral,costo_cliente_semestral,costo_cliente_anual,anio,fec_cap) values" .
									   "('$zona_g','$division','$grupo',$salario_minimo,0,0,0,0,$mensual,$bimestral,$semestral,$anual,$anio,'$fec_cap')";
								$i++;
							}
							else
							{
								if($i<=400)
								{
									$sql.=", ('$zona_g','$division','$grupo',$salario_minimo,0,0,0,0,$mensual,$bimestral,$semestral,$anual,$anio,'$fec_cap')";
									$i++;
								}
								else
								{
									$sql.=", ('$zona_g','$division','$grupo',$salario_minimo,0,0,0,0,$mensual,$bimestral,$semestral,$anual,$anio,'$fec_cap')";
									$conecta->consulta($sql . ";");
									$t_insert += $i;
									$i=0;
								}
							}
						}

						if($i>0)
						{
							$conecta->consulta($sql . ";");
							$t_insert += $i;
						}
					}
					echo json_encode(["ok"=>true,"mensaje"=>"Se procesaron $t_insert registros."]);
					exit;
				} catch (Exception $e) {
											echo json_encode(["ok"=>false,"mensaje"=>$e->getMessage()]);
											exit;
										}
		}
		else
		{
			echo json_encode(["ok"=>false,"mensaje"=>"No se pudo guardar el archivo."]);
			exit;
		}
	}

	if($pasa==1)
	{

	
?>
		<script type="text/javascript">
			document.getElementById("envia").submit();
		</script>
<?
	}
?>
