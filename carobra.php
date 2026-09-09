<?php
	session_start();

   function conect_BD($query,$op)
   {
		$bd=mysqli_connect('localhost','root','admin');
		//$bd=mysqli_connect('localhost','administra','JoJoJa**01');
		if (!$bd)
		{
			die('Error con la conexión a la BD: ' . mysqli_error($bd));
		}
		else
		{
			mysqli_select_db($bd,"desarr58_carobra");
			if(mysqli_multi_query($bd,$query))
			{
				if($op==1)
				{
 					if($result=mysqli_store_result($bd))
 					{
 						$tb=array();
 						while ($row = mysqli_fetch_array($result))
 						{
 							$tb[] = $row;
 						}
 						$RS[] = $tb;
 						return $RS;
 					}
 					else
 						die('Error al leer los datos: ' . mysqli_error($bd));
				}
				else
				{
					return true;
				}
			}
			else
			{
				echo $query . "<br>";
				die('Error al ejecutar la consulta: ' . mysqli_error($bd));
			}
		}
		mysqli_close($bd);
   }
   
   function titulo($men)
   {
   		echo "<div id='titulo'>";
   			echo "<span>$men</span>";
   		echo "</div>";
   }
   
	function pie($men)
   	{
   		echo "<div id='men_pie' style='top:0px;'>";
   			echo "<span>$men</span>";
   		echo "</div>";
   	}

   	function consul_gen($op,$param1,$param2)
	{
		switch($op)
		{
			case 1: //valida acceso de usuario al sistema
				$fil="Select nombre from usuarios where clave='$param1' and SHA1(contrasena)=SHA1('$param2') and activo=1";
				break;
			case 2: //Selecciona la página a cargar
				$fil=  "select pagina from paginas inner join modulos on paginas.id_modulo=modulos.id_modulo where ";
				$fil.= "nom_pagina='" .  utf8_decode($param1) . "' and modulo='" . str_replace(' ','_',$param2) . "'";
				break;
			case 3: //genera las opciones del menu
				$fil=  "Select nom_pagina from paginas inner join modulos on (paginas.id_modulo=modulos.id_modulo) ";
				$fil.= "inner join permisos on (paginas.id_pagina=permisos.id_pagina) inner join usuarios on ";
				$fil.= "(permisos.clave_usuario=usuarios.clave) where modulo='" . str_replace(' ','_',$param1) . "' ";
				$fil.= "and usuarios.nombre='$param2'";
				break;
			case 4: // despliega usuarios
				$fil=  "select nombre,clave,email,desc_nivel,activo from usuarios inner join niveles on usuarios.id_nivel=niveles.id_nivel ";
				$fil.= "order by nombre";
				break;
			case 5: // despliegas permisos para usuarios
				$fil=  "Select nom_pagina,ifnull(p,0) as permiso from paginas as pa left join (select ifnull(id_pagina,0) as id_pagina,";
				$fil.= "count(*) as p from permisos where clave_usuario='$param1' group by id_pagina) as pe on (pa.id_pagina=pe.id_pagina) ";
				$fil.= "where id_modulo=$param2";
				break;
			case 6: // seleccionar empleado por nombre
				$fil=  "Select ifnull(no_empleado,'') as no_empleado,nombre,fec_ingreso,id_nivel,activo, email,domicilio,telefono,";
				$fil.= "celular,id_banco,clabe,id_gerencia from usuarios left join empleados on empleados.clave=usuarios.clave where "; 
				$fil.=  "usuarios.clave='$param1'";
				break;
			case 7: // seleccionar empleado por numero de empledo
				$fil=  "Select ifnull(no_empleado,'') as no_empleado,usuarios.clave,nombre,fec_ingreso,id_nivel,activo, email,domicilio,";
				$fil.= "telefono,celular,id_banco,clabe,id_gerencia from usuarios left join empleados on empleados.clave=usuarios.clave where "; 
				$fil.=  "no_empleado=$param1";
				break;
			case 8: // Listar gerencias
				$fil=  "Select * from gerencias order by gerencia,nap";
				break; 
		}
		return(conect_BD($fil,1));
	}
	
	function menu($usuario)
	{
		echo "<div id='menu'>";
			$fil="Select modulo from modulos order by id_modulo";
			$tb=conect_BD($fil,1);
			if ( count($tb[0]) > 0 )
			{
				echo "<table id='tmenu'><tr>";
					$i=0;
					while($i < count($tb[0]))
					{
						echo "<td>" . str_replace('_',' ',$tb[0][$i][0]) . "</td>";
						$i++;
					}
				echo "</tr></table>";
			}
		echo "</div>";
	}





	function const_select($nom,$sel,$query,$consecutivo,$selected,$class,$style,$onchange,$extra,$size,$dis)
	{
		$fils="<select value='' name='" . $nom . "' id='" . $sel . "' size=$size $dis class='" . $class . "' style='" . $style . "'";
		if($onchange != "")
		{
			$fils=$fils . " onchange=" . '"' . $onchange . '"' . ">";
		}
		else
		{
			$fils=$fils . ">";
		}
		echo $fils;
			if(substr($query,0,6)=="Select" or substr($query,0,4)=="call")
			{
				$td=conect_BD($query,1);
				echo "<option value=''>&nbsp;</option>";
				
				foreach($td as $registro)
					foreach($registro as $celda)
					{	
						if($selected==$celda[0])
						{
							echo "<option value='" . $celda[0] . "' SELECTED>". utf8_encode($celda[1]) . "</option>";
						}
						else
						{
							echo "<option value='" . $celda[0] . "'>". utf8_encode($celda[1]) . "</option>";
						}
					}
				if(strlen($extra) > 0)
				{
					if($selected==$extra)
					{
						echo "<option value='" . $extra . "' SELECTED>". $extra . "</option>";
					}
					else
					{
						echo "<option value='" . $extra . "'>". $extra . "</option>";
					}
				}
			}
			else
			{
				echo "<option value=0>&nbsp;</option>";
				$opc="";
				$j=0;
				$i=1;
				while ($j < strlen($query))
				{
					if(substr($query,$j,1)==',')
					{
						if($consecutivo==1)
							if($i==$selected)
							{
								echo "<option value=$i SELECTED>" . $opc . "</option>";
							}
							else
							{
								echo "<option value=$i>" . $opc . "</option>";
							}
						else 
							if($opc==$selected)
							{
								echo "<option value=$opc SELECTED>" . $opc . "</option>";
							}
							else
							{
								echo "<option value=$opc>" . $opc . "</option>";
							}
						$i++;
						$opc="";
					}
					else
					{
						$opc=$opc  . substr($query,$j,1);
					}
					$j++;
				}
				if($consecutivo==1)
					if($i==$selected)
					{
						echo "<option value=$i SELECTED>" . $opc . "</option>";
					}
					else
					{
						echo "<option value=$i>" . $opc . "</option>";
					}
				else 
					if($opc==$selected)
					{
						echo "<option value=$opc SELECTED>" . $opc . "</option>";
					}
					else
					{
						echo "<option value=$opc>" . $opc . "</option>";
					}
			}
		echo "</select>";
	}

	function conecta_FTP()
	{
		$usuario="usftp";
		$contra="carobra01";
		$id_ftp=ftp_connect("http://carobra.ddns.me")  or die("No se pudo conectar........");
		if(ftp_login($id_ftp,$usuario,$contra))
		{
			ftp_pasv($id_ftp,true);
			return $id_ftp;
		}
		else
			echo "usuario incorrecto o contraseña erronea";
	}
	
	function SubirArchivo($archivo_local,$archivo_remoto,$folder)
	{
		$archivo="./httpdocs/" . $folder . "/" . $archivo_remoto;
		$id_ftp=conecta_FTP();
		if(ftp_put($id_ftp,$archivo,$archivo_local,FTP_BINARY))
			$result= true;
		else
			$result= false;
		ftp_quit($id_ftp);
		return $result;
	}
?>