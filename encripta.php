<?php	
	date_default_timezone_set('America/Mexico_City');

	function encripta($mensaje)
	{
		$car=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","k","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","-","*",".");
		$l=count($car)-1;
		$res=$car[mt_rand(0,$l)] . $car[mt_rand(0,$l)] . mt_rand(1,9);
		$r=$res[2];
		if($r==1 or $r==4 or $r==7)
			$p=1;
		else 
		{
			if($r==2 or $r==5 or $r==8)
				$p=2;
			else 
				$p=3;
		}
		for ( $i = 0 ; $i < strlen($mensaje)  ; $i ++) 
		{
			switch($p)
			{
				case 1:	$res.=$car[mt_rand(0,$l)] . $mensaje[$i] . $car[mt_rand(0,$l)] . $car[mt_rand(0,$l)]; break;
				case 2:	$res.=$car[mt_rand(0,$l)] . $car[mt_rand(0,$l)] . $mensaje[$i] . $car[mt_rand(0,$l)]; break;
				case 3: $res.=$car[mt_rand(0,$l)] . $car[mt_rand(0,$l)] . $car[mt_rand(0,$l)] . $mensaje[$i]; break; 
			}
		}
		$res.=$car[mt_rand(0,$l)] . $car[mt_rand(0,$l)] . $car[mt_rand(0,$l)];
		return $res;
	}

    function mes($m)
    {
        $m=strtolower($m);
        switch($m)
        {
            case ($m=="ene" or $m=="enero" or $m=="jan"): return "01"; break;
            case ($m=="feb" or $m=="febrero"): return "02"; break;
            case ($m=="mar" or $m=="marzo"): return "03"; break;
            case ($m=="abr" or $m=="abril" or $m=="apr"): return "04"; break;
            case ($m=="may" or $m=="mayo"): return "05"; break;
            case ($m=="jun" or $m=="junio"): return "06"; break;
            case ($m=="jul" or $m=="julio"): return "07"; break;
            case ($m=="ago" or $m=="agosto" or $m=="aug"): return "08"; break;
            case ($m=="sep" or $m=="septiembre"): return "09"; break;
            case ($m=="oct" or $m=="octubre"): return "10"; break;
            case ($m=="nov" or $m=="noviembre"): return "11"; break;
            case ($m=="dic" or $m=="diciembre" or $m=="dec"): return "12"; break;
			case "01": return "Enero"; break;
			case "02": return "Febrero"; break;
			case "03": return "Marzo"; break;
			case "04": return "Abril"; break;
			case "05": return "Mayo"; break;
			case "06": return "Junio"; break;
			case "07": return "Julio"; break;
			case "08": return "Agosto"; break;
			case "09": return "Septiembre"; break;
			case "10": return "Octubre"; break;
			case "11": return "Noviembre"; break;
			case "12": return "Diciembre"; break;
        }
    }	
    	
	function desencripta($mensaje)
	{
		$j=0;
		$res="";
		$r=$mensaje[2];
		if($r=="1" or $r=="4" or $r=="7")
			$p=1;
		else 
		{
			if($r=="2" or $r=="5" or $r=="8")
				$p=2;
			else 
				$p=3;
		}
		$mensaje=substr($mensaje,3,(strlen($mensaje)-3-(3-$p)));
		for ( $i = 0 ; $i < strlen($mensaje); $i ++)
		{
			if($j==$p)
			{
				$res.=$mensaje[$i];
			}
			if($j==3)
				$j=0;
			else
				$j++;
		}
		return $res;
	}
	
	function recibe_POST($var)
	{
		if(isset($_POST[$var]))
			return addslashes(trim($_POST[$var]));
		else
			return '';
	}

	function recibe_GET($var)
	{
		if(isset($_GET[$var]))
			return addslashes(trim($_GET[$var]));
		else
			return '';
	}
	
	function mensaje($mensaje,$destino="")
	{
		if($mensaje <> "")
			echo "<script type='text/javascript'>alert('$mensaje')</script>";
		if($destino <> "")
			echo "<script type='text/javascript'>window.location='$destino'</script>";
	}

	class conector
	{
		private $servidor;
		private $usuario;
		private $psw;
		private $bd;
		var $conexion;
		private $tbc;

		//function __construct($db="sisca",$server="localhost",$user="root", $contra="")
		function __construct($db="carobra_2014",$server="localhost",$user="root", $contra="Cr0g2021!")
		//function __construct($db="carobra_2014",$server="192.168.0.205",$user="temp", $contra="Temp01*")
		{
			$this->servidor=$server;
			$this->usuario=$user;
			$this->psw=$contra;
			$this->bd=$db;
			$this->conexion= new mysqli();
			$this->conexion->connect($this->servidor,$this->usuario,$this->psw,$this->bd);
		}
		
		function consulta($fil)
		{
			if(strtolower(substr($fil, 0,6))=="update" or strtolower(substr($fil, 0,6))=="insert" or strtolower(substr($fil, 0,6))=="delete")
			{
				if($this->conexion->query($fil)=== TRUE)
					return $this->conexion->affected_rows;
				else
					return false;
			}
	 			else
			{
				$this->tbc=$this->conexion->query($fil);
		        while ($this->conexion->more_results() && $this->conexion->next_result()) 
		        {
		            $extra = $this->conexion->use_result();
		            if ($extra instanceof mysqli_result) 
		            {
		                $extra->free();
		            }
		        }

		        return $this->tbc;
			}
		}

		function multi_consulta($query,$query2='',$tipo='U',$nom_q='')
		{
			do {
					$mensaje="";
					if (!$this->conexion->multi_query($query)) 
					{
						switch($tipo)
						{
							case "U": $mensaje="Hubo un error en la actualización($nom_q)"; break;
							case "I": $mensaje="Hubo un error en la inserción($nom_q)"; break;
						}
						//echo $mensaje;
					}
					if(!$this->conexion->more_results())
					{
						if($query2 != '')
						{
							return $this->conexion->query($query2);
/*
							$this->tbc=$this->conexion->query($query2);
							if ($this->tbc === false)
							    //return $this->tbc->error;
							    return "--------->|" . $query2 . "|<br>" . $this->tbc->error . "<br>";
							else
								return $this->tbc;
*/
						}
					}
				} while ($this->conexion->next_result());
		}

		function obtener_id()
		{
			return $this->conexion->insert_id;
		}

		function prepare($sql)
		{
			return $this->conexion->prepare($sql);
		}

		function count_afecta()
		{
			return $this->conexion->affected_rows;
		}
	}

	function nivel_usuario($nombre="",$clave="",$ne=0)
	{
		$nivel=0;
		$conecta= new conector();
		if($nombre !="")
		{
			$bd=$conecta->consulta("Select clave from usuarios where nombre='$nombre'");
			$tb=mysqli_fetch_array($bd);
			$clave=$tb['clave'];
		}
		else
		{
			if($clave =="")
			{
				if($ne==0)
				{
					$bd=$conecta->consulta("Select clave from empleados where no_empleado=$ne");
					$tb=mysqli_fetch_array($bd);
					$clave=$tb['clave'];
				}				
			}
		}
		if($clave != "")
		{
			$bd=$conecta->consulta("Select 1 from usuarios_nivel where clave='$clave' and id_nivel=12");
			if ($bd->num_rows != 0)
			{
				$nivel=12;	
			}
			else
			{
				$query="Select distinct fecha,valor from control_cambios cc 
							inner join empleados e on (cc.no_empleado=e.no_empleado) 
						where tipo='nivel' and clave='$clave' 
							and datediff(concat(substr(fecha,-4),'-',substr(fecha,4,2),'-',substr(fecha,1,2)),'" . date('Y-m-d') . "')<=0 
						order by str_to_date(fecha,'%d/%m/%Y') desc limit 0,1";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$tb=mysqli_fetch_array($bd);
					$nivel=$tb['valor'];
				}
				else
				{
					$query="Select id_nivel
							from (Select id_nivel from usuarios_nivel where clave='$clave'
								  union
								  Select max(id_nivel) from usuarios_nivel_gerencia 
								  where clave='$clave') d
							order by if(id_nivel=7,3,id_nivel) desc";



					$bd=$conecta->consulta($query);
					$i=0;
					while($tb=mysqli_fetch_array($bd))
						$niveles[]=$tb['id_nivel'];

					if(in_array('12', $niveles, true))
						$nivel=12;
					else
					{
						if(in_array('6', $niveles, true))
							$nivel=6;
						else 
						{
							if(in_array('2', $niveles, true))
								$nivel=2;
							else 
							{
								if(in_array('13', $niveles, true))
									$nivel=13;
								else
								{
									if(in_array('5', $niveles, true))
										$nivel=5;
									else 
									{
										if(in_array('4', $niveles, true))
											$nivel=4;
										else 
										{
											if(in_array('1', $niveles, true))
												$nivel=1;
											else 
											{
												if(in_array('3', $niveles, true))
													$nivel=3;
												else 
													$nivel=7;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $nivel;
	}

	function compl_ajuste($actualiza=0,$clave='', $nombre='')
	{
		$conecta=new conector();

		if($actualiza==1)
		{
			$conecta->consulta("Truncate estruc_empleados");
			$query="Insert into estruc_empleados
					Select distinct clave,no_empleado,sc,brocker,
						   ung.id_gerencia,gerencia
					from usuarios_nivel_gerencia ung
						inner join estructura_gerencia eg 
								on (ung.id_gerencia=eg.id_gerencia)
						inner join (Select distinct id_gerencia,gerencia from gerencias) g 
								on (ung.id_gerencia=g.id_gerencia)
					where pertenece=1";

			$conecta->consulta($query);		
		}
		if($nombre != '')
		{
			$bd=$conecta->consulta("Select clave from carobra.usuarios where nombre='$nombre'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$clave=$tb["clave"];
			}
		}
		if($clave != '')
		{
			$data = array();
			$query="Select '$clave' as clave,clave_unidad as unidad,
							clave_socio as socio,eg.id_gerencia
					from (Select id_gerencia from estruc_empleados where clave='$clave') eg
						inner join gerencias_negocio gn on (eg.id_gerencia=gn.id_gerencia)
					union
					Select '$clave',clave_unidad,clave_socio,ug.id_gerencia
					from (Select clave 
						  from (Select distinct u.clave,ifnull(id_nivel,0) as nivel
							    from carobra.usuarios u 
									left join (Select clave,id_nivel from carobra_2014.usuarios_nivel 
											   where id_nivel=3) uni 
											on (u.clave=uni.clave)) d
						  where clave='$clave' and nivel=0) c
						inner join (Select * from carobra_2014.usuarios_gerencia 
									where clave='$clave' and pertenece=1) ug 
								on (c.clave=ug.clave)
						inner join carobra_2014.gerencias_negocio gn on (ug.id_gerencia=gn.id_gerencia)";
			$bd=$conecta->consulta($query);
			while ($tb = $bd->fetch_assoc()) 
			{
				$data[] = $tb;
			}
			return $data;
		}
		else
			return '';
	}

	function compl_ajuste2($actualiza=0,$clave='', $nombre='')
	{
		$conecta=new conector();

		if($actualiza==1)
		{
			$conecta->consulta("Truncate estruc_empleados2");
			$query="Insert into estruc_empleados2
					Select distinct clave,no_empleado,sc,brocker,
						   ung.id_gerencia,gerencia
					from usuarios_nivel_gerencia ung
						inner join estructura_gerencia eg 
								on (ung.id_gerencia=eg.id_gerencia)
						inner join (Select distinct id_gerencia,gerencia from gerencias) g 
								on (ung.id_gerencia=g.id_gerencia)
					where pertenece=1";
			$conecta->consulta($query);		
		}
		if($nombre != '')
		{
			$bd=$conecta->consulta("Select clave from carobra.usuarios where nombre='$nombre'");
			if ($bd->num_rows != 0)
			{
				$tb=mysqli_fetch_array($bd);
				$clave=$tb["clave"];
			}
		}
		if($clave != '')
		{
			$data = array();
			$query="Select '$clave' as clave,clave_unidad as unidad,
							clave_socio as socio,eg.id_gerencia
					from (Select id_gerencia from estruc_empleados2 where clave='$clave') eg
						inner join gerencias_negocio gn on (eg.id_gerencia=gn.id_gerencia)
					union
					Select '$clave',clave_unidad,clave_socio,ug.id_gerencia
					from (Select clave 
						  from (Select distinct u.clave,ifnull(id_nivel,0) as nivel
							    from carobra.usuarios u 
									left join (Select clave,id_nivel from carobra_2014.usuarios_nivel 
											   where id_nivel=3) uni 
											on (u.clave=uni.clave)) d
						  where clave='$clave' and nivel=0) c
						inner join (Select * from carobra_2014.usuarios_gerencia 
									where clave='$clave' and pertenece=1) ug 
								on (c.clave=ug.clave)
						inner join carobra_2014.gerencias_negocio gn on (ug.id_gerencia=gn.id_gerencia)";
			$bd=$conecta->consulta($query);
			while ($tb = $bd->fetch_assoc()) 
			{
				$data[] = $tb;
			}
			return $data;
		}
		else
			return '';
	}

	function tablas_sv($filtro,$valor1,$valor2='',$valor3='')
	{
		$conecta= new conector();
		if($filtro=='Folio/Oport.')
		{
				if(strlen($valor1)==7)
					$param="no_oportunidad";
				else
					$param="folio";
			if($param=='folio')
			{
				$bd=$conecta->consulta("Select $param from produccion where $param='$valor1'");
				if ($bd->num_rows != 0)
				{
					$cad="carobra_2014.produccion|carobra_2014.autoriza|carobra_2014.calculo_pago|carobra_2014.prod_trabajador";
					$query="Select * from carobra.acum_det_prod_" . date("Y") . " where folio='$valor1'";
					$bd=$conecta->consulta($query);
					if ($bd->num_rows != 0)
						$cad.="|carobra.acum_det_prod_" . date("Y");
					else
						$cad.="|carobra.acum_det_prod_" . (date("Y")-1);
					return $cad;
				}
				else
				{
					$cad="";
					$a_sv=date("Y") - 1;
					while($a_sv > 2017)
					{
						$query="Select $param from carobra_respaldos.produccion_$a_sv 
								where $param='$valor1'";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$cad="carobra_respaldos.produccion_$a_sv|carobra_2014.autoriza|carobra_respaldos.calculo_pago_$a_sv|carobra_respaldos.prod_trabajador_$a_sv";
							if($a_sv <= 2022)
								$cad.="|carobra.acum_det_prod_$a_sv";
							else
								$cad.="|carobra.acum_det_prod_2021";
							$a_sv=2000;
						}
						$a_sv--;
					}
					if($cad=="")
						$cad="carobra_respaldos.respaldo_produccion|carobra_respaldos.respaldo_autoriza|carobra_respaldos.respaldo_calculo_pago|carobra_respaldos.respaldo_prod_trabajador|carobra.acum_det_prod_2021";
					return $cad;
				}
			}
			else 
			{
				$query="Select $param from prod_trabajador where $param='$valor1'";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$cad="carobra_2014.produccion|carobra_2014.autoriza|carobra_2014.calculo_pago|carobra_2014.prod_trabajador";
					$query="Select * from carobra.acum_det_prod_" . date("Y") . " where folio='$valor1'";
					$bd=$conecta->consulta($query);
					if ($bd->num_rows != 0)
						$cad.="|carobra.acum_det_prod_" . date("Y");
					else
						$cad.="|carobra.acum_det_prod_" . (date("Y")-1);
					return $cad;
				}
				else
				{
					$cad="";
					$a_sv=date("Y") - 1;
					while($a_sv > 2017)
					{
						$query="Select $param from carobra_respaldos.prod_trabajador_$a_sv 
								where $param='$valor1'";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$cad="carobra_respaldos.produccion_$a_sv|carobra_2014.autoriza|carobra_respaldos.calculo_pago_$a_sv|carobra_respaldos.prod_trabajador_$a_sv";
							if($a_sv <= 2022)
								$cad.="|carobra.acum_det_prod_$a_sv";
							else
								$cad.="|carobra.acum_det_prod_2021";
							$a_sv=2000;
						}
						$a_sv--;
					}
					if($cad=="")
						$cad="carobra_respaldos.respaldo_produccion|carobra_respaldos.respaldo_autoriza|carobra_respaldos.respaldo_calculo_pago|carobra_respaldos.respaldo_prod_trabajador|carobra.acum_det_prod_2021";
					return $cad;
				}
			}
		}
		else
		{
			if($filtro=='Cliente')
			{
				$query="Select folio from prod_trabajador 
						where trabajador='" . utf8_decode($valor1) . "'";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$cad="carobra_2014.produccion|carobra_2014.autoriza|carobra_2014.calculo_pago|carobra_2014.prod_trabajador";
					$query="Select * from carobra.acum_det_prod_" . date("Y") . " where folio='$valor1'";
					$bd=$conecta->consulta($query);
					if ($bd->num_rows != 0)
						$cad.="|carobra.acum_det_prod_" . date("Y");
					else
						$cad.="|carobra.acum_det_prod_" . (date("Y")-1);
					return $cad;
				}
				else
				{
					$cad="";
					$a_sv=date("Y") - 1;
					while($a_sv > 2017)
					{
						$query="Select folio from carobra_respaldos.prod_trabajador_$a_sv 
								where trabajador='" . utf8_decode($valor1) . "'";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$cad="carobra_respaldos.produccion_$a_sv|carobra_2014.autoriza|carobra_respaldos.calculo_pago_$a_sv|carobra_respaldos.prod_trabajador_$a_sv";
							if($a_sv <= 2022)
								$cad.="|carobra.acum_det_prod_$a_sv";
							else
								$cad.="|carobra.acum_det_prod_2021";
							$a_sv=2000;
						}
						$a_sv--;
					}
					if($cad=="")
						$cad="carobra_respaldos.respaldo_produccion|carobra_respaldos.respaldo_autoriza|carobra_respaldos.respaldo_calculo_pago|carobra_respaldos.respaldo_prod_trabajador|carobra.acum_det_prod_2021";
					return $cad;
				}
			}
			else
			{
				$fec_ini=substr($valor2,-4) . "-" . substr($valor2,3,2) . "-" . substr($valor2,0,2);
				$fec_fin=substr($valor3,-4) . "-" . substr($valor3,3,2) . "-" . substr($valor3,0,2);

				$query="Select folio from produccion 
					    where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_ini')>=0 
				  		  and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_fin')<=0
				  		limit 0,1";
				$bd=$conecta->consulta($query);
				if ($bd->num_rows != 0)
				{
					$cad="carobra_2014.produccion|carobra_2014.autoriza|carobra_2014.calculo_pago|carobra_2014.prod_trabajador";
					$query="Select * from carobra.acum_det_prod_" . date("Y") . " where folio='$valor1'";
					$bd=$conecta->consulta($query);
					if ($bd->num_rows != 0)
						$cad.="|carobra.acum_det_prod_" . date("Y");
					else
						$cad.="|carobra.acum_det_prod_" . (date("Y")-1);
					return $cad;
				}
				else
				{
					$cad="";
					$a_sv=date("Y") - 2;
					while($a_sv > 2017)
					{
						$query="Select folio from carobra_respaldos.produccion_$a_sv 
							    where datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_ini')>=0 
						  		  and datediff(concat(substr(fec_traspaso,-4),'-',substr(fec_traspaso,4,2),'-',substr(fec_traspaso,1,2)),'$fec_fin')<=0
						  		limit 0,1";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows != 0)
						{
							$cad="carobra_respaldos.produccion_$a_sv|carobra_2014.autoriza|carobra_respaldos.calculo_pago_$a_sv|carobra_respaldos.prod_trabajador_$a_sv";
							if($a_sv <= 2022)
								$cad.="|carobra.acum_det_prod_$a_sv";
							else
								$cad.="|carobra.acum_det_prod_2021";
							$a_sv=2000;
						}
						$a_sv--;
					}
					if($cad=="")
						$cad="carobra_respaldos.respaldo_produccion|carobra_respaldos.respaldo_autoriza|carobra_respaldos.respaldo_calculo_pago|carobra_respaldos.respaldo_prod_trabajador|carobra.acum_det_prod_2021";
					return $cad;
				}
			}
		}
	}

	function tablas_sv2($filtro,$valor1,$valor2='',$valor3='')
	{
		$conecta= new conector();

		$i=1;
		$cad="";
		while($i <= 8)
		{
			switch ($i) 
			{
				case 1: $anio_pr=""; break;
				case 2: $anio_pr="_2022"; break;
				case 3: $anio_pr="_2021"; break;
				case 4: $anio_pr="_2020"; break;
				case 5: $anio_pr="_2019"; break;
				case 6: $anio_pr="_2018"; break;
				case 7: $anio_pr="_2017"; break;
				case 8: $anio_pr=""; break;
			}
			if($i==1)
				$bd_pr="carobra_2014.";
			else
			{
				if($i < 8)
					$bd_pr="carobra_respaldos.";
				else
					$bd_pr="carobra_respaldos.respaldo_";
			}
			$query="Select folio from " . $bd_pr . "produccion" . $anio_pr . $where_filtro;
			$bd=$conecta->consulta($query);
			if ($bd->num_rows != 0)
			{
				switch ($i) 
				{
					case 1: 
						$cad="carobra_2014.produccion|carobra_2014.autoriza|carobra_2014.calculo_pago|carobra_2014.prod_trabajador";
						
						$acum_prod="|carobra.acum_det_prod_" . date("Y");
						$query="Select * from carobra.acum_det_prod_" . date("Y") . " 
								where folio='$valor1'";
						$bd=$conecta->consulta($query);
						if ($bd->num_rows == 0)
						{
							$query="Select * from carobra.acum_det_prod_" . (date("Y")-1) . " 
									where folio='$valor1'";
							$bd=$conecta->consulta($query);
							$acum_prod="|carobra.acum_det_prod_" . (date("Y")-1);
						}
						$cad.=$acum_prod;
					break;
					case 2: 
						$cad="carobra_respaldos.produccion_2022|carobra_2014.autoriza|carobra_respaldos.calculo_pago_2022|carobra_respaldos.prod_trabajador_2022|carobra.acum_det_prod_2022"; 
					break;
					case 3: 
						$cad="carobra_respaldos.produccion_2021|carobra_2014.autoriza|carobra_respaldos.calculo_pago_2021|carobra_respaldos.prod_trabajador_2021|carobra.acum_det_prod_2021"; 
					break;
					case 4: 
						$cad="carobra_respaldos.produccion_2020|carobra_2014.autoriza|carobra_respaldos.calculo_pago_2020|carobra_respaldos.prod_trabajador_2020|carobra.acum_det_prod_2024"; 
					break;
					case 5: 
						$cad="carobra_respaldos.produccion_2019|carobra_2014.autoriza|carobra_respaldos.calculo_pago_2019|carobra_respaldos.prod_trabajador_2019|carobra.acum_det_prod_2024"; 
					break;
					case 6: 
						$cad="carobra_respaldos.produccion_2018|carobra_respaldos.autoriza_2018|carobra_respaldos.calculo_pago_2018|carobra_respaldos.prod_trabajador_2018|carobra.acum_det_prod_2024"; 
					break;
					case 7: 
						$cad="carobra_respaldos.produccion_2017|carobra_respaldos.autoriza_2017|carobra_respaldos.calculo_pago_2017|carobra_respaldos.prod_trabajador_2017|carobra.acum_det_prod_2024"; 
					break;
					case 8: 
						$cad="carobra_respaldos.respaldo_produccion|carobra_respaldos.respaldo_autoriza|carobra_respaldos.respaldo_calculo_pago|carobra_respaldos.respaldo_prod_trabajador|carobra.acum_det_prod_2024"; 
					break;
				}
				$i=8;
			}
			$i++;
		}
		return $cad;
	}

	function conecta_FTP()
	{
		$usuario="usftp";
		$contra="carobra01";
		$id_ftp=ftp_connect("carobra.ddns.me")  or die("No se pudo conectar...");
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
		if (substr($archivo_remoto,-4)=="xlsx")
		{
			$archivo= "./afore/" . $folder . "/" . $archivo_remoto;
			$id_ftp=conecta_FTP();
			if(ftp_put($id_ftp,$archivo,$archivo_local,FTP_BINARY))
				$result= true;
			else
				$result= false;
			ftp_quit($id_ftp);
		}
		else 
			$result= false;
		return $result;
	}
	
	function SubirTexto($archivo_local,$archivo_remoto,$folder)
	{
		if (substr($archivo_remoto,-3)=="txt")
		{
			$archivo="./afore/" . $folder . "/" . $archivo_remoto;
			$id_ftp=conecta_FTP();
			if(ftp_put($id_ftp,$archivo,$archivo_local,FTP_BINARY))
				$result= true;
			else
				$result= false;
			ftp_quit($id_ftp);
		}
		else 
			$result= false;
		return $result;
	}
	
	function tex_h($id,$val="")
	{
		echo "<input type='hidden' id='$id' name='$id' value='$val'/>";
	}
	
	function envia_email($body,$from,$cliente_email,$subjet)
	{
		include("./PHPMailer/class.phpmailer.php");
			
		$mail = new PHPMailer();
		
/*
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		//$mail->Host = "smtp.mail.carobra.com.mx";
		$mail->Host = "mail.carobra.com.mx";
		$mail->Username = "administrador@carobra.com.mx";
		$mail->Password = "JoJoJaJe4";
		$mail->Port = 587;
*/		
		$mail->IsSMTP();
		$mail->SMTPDebug  = 2;
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPSecure = "tls";
		$mail->Username = "carobra1234567890@gmail.com";
		$mail->Password = "carobra01";
		$mail->Port = 465;
		
		$mail->SetFrom('carobra1234567890@gmail.com', 'administrador Carobra');
		//$mail->From = "carobra1234567890@gmail.com";
		//$mail->FromName = $from;
		
		$mail->AddAddress($cliente_email);
		$mail->IsHTML(true);
		$mail->Subject = $subjet;
		$mail->Body = $body;
		$exito = $mail->Send();
	
		return exito;
	}
/*	
	function bitacora($tipo_mov,$usuario,$tabla,$valor_id,$campo,$tipo_campo,$valor)
	{
		$conecta= new conector();
		$actualiza= new conector();
		
		if($tipo_mov=='update')
		{
			$c=explode("|", $campo);
			$v=explode("|", $valor);
			$tc=explode("|", $tipo_campo);

			switch($tabla)
			{
				case "produccion":$comp=" and folio='$valor_id'"; break;
			}
			$i=0;
			$query='';
			while($i< count($c))
			{
				$valor=($tc[$i]=='texto') ? "'" . $v[$i] . "'" : $v[$i];
				$fil="Select " . $c[$i] . " from $tabla where " . $c[$i] . "=$valor" . $comp;
				$bd=$conecta->consulta($fil);
				if ($bd->num_rows == 0)
				{
					$query="Insert into bitacora values(0,'" . date("d/m/Y") . "','" . date("H:i:s") . "','$usuario','$tabla','$valor_id',1,'" . $c[$i] . "','" . $v[$i] . "')";
					echo $query . "<br>";
					$actualiza->consulta($query);
				}
				$i++;
			}
		}
	}
*/

	function convertirFecha($fecha) 
	{
	    if (empty($fecha) || $fecha === "0000-00-00" || $fecha === "-") 
	        return "";

	    $date = DateTime::createFromFormat("Y-m-d", $fecha);
	    if ($date)
	        return $date->format("d/m/Y");
	   else
	        return $fecha;
	}

	function convertirFecha_date($fecha) 
	{
	    if (empty($fecha) || $fecha === "00/00/0000" || $fecha === "-") 
	        return "";

	    $date = DateTime::createFromFormat("d/m/Y", $fecha);
	    if ($date)
	        return $date->format("Y-m-d");
	   else
	        return $fecha;
	}

	function leer_numeros($n)
	{
		$n=str_replace(",","",str_replace("$","",$n));
		$d=explode(".",$n);
		if(count($d)>1)
			$cent=" " . $d[1] . "/100 MN";
		else
			$cent=" 0/100 MN";
		$n=$d[0];
		$i=strlen($n)-1;
		$cad='';
		$p=1;
		while($i>=0)
		{
			if($p==1 or $p==4)
			{
				switch($n[$i])
				{
					case '0': $aux=""; break;
					case '1': $aux="un"; break;
					case '2': $aux="dos"; break;
					case '3': $aux="tres"; break;
					case '4': $aux="cuatro"; break;
					case '5': $aux="cinco"; break;
					case '6': $aux="seis"; break;
					case '7': $aux="siete"; break;
					case '8': $aux="ocho"; break;
					case '9': $aux="nueve"; break;
				}
				if($p==4)
				{
					if($n[$i]==1)
						$aux='';
					$cad= $aux . " mil " . $cad;
				}
				else
					$cad=$aux;
			}
			if($p==2 or $p==5)
			{
				switch($n[$i])
				{
					case '0': $cad="" . $cad; break;
					case '1':
								if($p==2)
								{
									switch($n[$i + 1])
									{
										case '0': $cad="diez"; break;
										case '1': $cad="once"; break;
										case '2': $cad="doce"; break;
										case '3': $cad="trece"; break;
										case '4': $cad="catorce"; break;
										case '5': $cad="quince"; break;
										case '6': $cad="dieciseis"; break;
										case '7': $cad="diecisiete"; break;
										case '8': $cad="dieciocho"; break;
										case '9': $cad="diecinueve"; break;	
									}
								}
								else
								{
									switch($n[$i + 1])
									{
										case '0': $cad="diez " . $cad; break;
										case '1': $cad="once " . $cad; break;
										substr('abcdef', 1);
										case '2': $cad="doce " . substr($cad,3); break;
										case '3': $cad="trece " . substr($cad,4); break;
										case '4': $cad="catorce " . substr($cad,6); break;
										case '5': $cad="quince " . substr($cad,5); break;
										case '6': $cad="dieciseis " . substr($cad,4); break;
										case '7': $cad="diecisiete " . substr($cad,5); break;
										case '8': $cad="dieciocho " . substr($cad,4); break;
										case '9': $cad="diecinueve " . substr($cad,5); break;	
									}
								}
								break;
					case '2': 
								if($p==2)
								{
									if($n[$i + 1]==0)
										$cad="veinte";
									else
										$cad="veinti" . $cad;
								}
								else
								{
									if($n[$i + 1]==0)
										$cad="veinte" . $cad;
									else
									{
										if($n[$i + 1]==1)
											$cad="veintiun " . $cad;
										else
											$cad="veinti" . $cad;
									}
								}
								break;
					default:
								switch($n[$i])
								{
									case '3': $aux='treinta'; break;
									case '4': $aux='cuarenta'; break;
									case '5': $aux='cincuenta'; break;
									case '6': $aux='sesenta'; break;
									case '7': $aux='setenta'; break;
									case '8': $aux='ochenta'; break;
									case '9': $aux='noventa'; break;
								}
								if($p==2)
								{
									if($n[$i + 1]>0)
										$cad=$aux . " y " . $cad;
									else
										$cad=$aux;
								}
								else
								{
									if($n[$i + 1]>0)
									{
										if($n[$i + 1]==1)
											$cad=$aux . " y un " . $cad;
										else
											$cad=$aux . " y " . $cad;
									}
									else
										$cad=$aux . $cad;					
								}
								break;
				}
			}
			if($p==3)
			{
				switch($n[$i])
				{
					case '0': $aux=""; break;
					case '1': $aux="ciento"; break;
					case '2': $aux="doscientos"; break;
					case '3': $aux="trescientos"; break;
					case '4': $aux="cuatrocientos"; break;
					case '5': $aux="quinientos"; break;
					case '6': $aux="seiscientos"; break;
					case '7': $aux="setecientos"; break;
					case '8': $aux="ochocientos"; break;
					case '9': $aux="novecientos"; break;	
				}
				if($n[$i]=='1' and $cad=='')
					$aux='cien';
				$cad= $aux . " " . $cad;
			}
			$p++;
			$i--;
		}
		if($n==1)
			$cad.= " peso " . $cent;
		else
			$cad.= " pesos " . $cent;
		return $cad;
	}

	function reg_acceso($nombre,$pagina)
	{
		$conecta= new conector();

		$bd=$conecta->consulta("Select no_empleado from empl_usuarios where nombre ='$nombre'");
		$tb=mysqli_fetch_array($bd);
		$ne=$tb["no_empleado"];
		$bd=$conecta->consulta("Select id_pagina from paginas where pagina ='$pagina" . ".php'");		
		$tb=mysqli_fetch_array($bd);
		$pag=$tb["id_pagina"];
		
		$bd=$conecta->consulta("Select ifnull(max(no_acceso),0) as na from carobra.acceso where usuario=$ne");
		$tb=mysqli_fetch_array($bd);
		$na=$tb["na"];

		$p=1;
		if($na > 0)
		{
			$bd=$conecta->consulta("Select * from carobra.acceso where usuario=$ne and no_acceso=1");
			$tb=mysqli_fetch_array($bd);
			$pagina=$tb["pagina"];
			$fecha=$tb["fecha"];
			$hora=$tb["hora"];
			$dh=explode(":",$hora);
			$h=$dh[0];
			$m=$dh[1];
			if($pagina==$pag and $fecha==date("d/m/Y") and $h==date("H") and $m==date("i"))
				$p=0;
			else
			{
				$conecta->consulta("Update carobra.acceso set no_acceso=no_acceso + 1 where usuario=$ne");
				$conecta->consulta("Delete from carobra.acceso where usuario=$ne and no_acceso=51");
			}
		}
		if($p==1)
		{
			$query="Insert into carobra.acceso values($ne,1,$pag,'" . date("H:i:s") . "','" . date("d/m/Y") . "')";
			$conecta->consulta($query);
		}
	}

	function bitacora_venpag($consultor,$clave,$mes_ini,$mes_fin,$fec_ini,$fec_fin,$fec_pago)
	{
		$conecta= new conector();

		$fecha= date('d/m/Y');
		$hora= date('H:i:s');

		$bd=$conecta->consulta("Select clave from usuarios where nombre='$consultor'");
		$tb=mysqli_fetch_array($bd);
		$clave1=$tb["clave"];

		if ($mes_ini!="" and $mes_fin!="" and $fec_ini!="" and $fec_fin!="" and $fec_pago!="") 
			$query="Insert into carobra.bitacora_venpag values('$fecha','$hora','$clave1','$clave','$mes_ini','$mes_fin','$fec_ini','$fec_fin','$fec_pago')";
		else
		{
			if ($mes_ini!="" and $mes_fin!="") 
				$query="Insert into carobra.bitacora_venpag values('$fecha','$hora','$clave1','$clave','$mes_ini','$mes_fin','','','')";
			else
			{
				if ($fec_ini!="" and $fec_fin!="") 
					$query="Insert into carobra.bitacora_venpag values('$fecha','$hora','$clave1','$clave','','','$fec_ini','$fec_fin','')";
				else
				{
					if ($fec_pago!="")
					{
						$query="Insert into carobra.bitacora_venpag values('$fecha','$hora','$clave1','$clave','','','','','$fec_pago')";	
					}
				}	
			}
		}
		$conecta->consulta($query);
	}

	function bitacora($usuario,$proceso,$cad,$tabla='')
	{
		$c=explode("|",$cad);
		$fecha= date('d/m/Y');
		$hora= date('H:i:s');
		
		$con= new conector();

		$bd=$con->consulta("Select clave from usuarios where nombre='$usuario'");
		$tb=mysqli_fetch_array($bd);
		$clave=$tb["clave"];

		switch ($proceso) 
		{
			case 'IP':	//Interface de producción
				$folio=$c[0];
				$nss=$c[1];
				$id_tipo_mov=$c[2];
				$fec_cierre=$c[3];
				$nap=$c[4];
				$saldo=$c[5];
				$id_estatus=$c[6];
				$n_opor=$c[7];
				$nombre=$c[8];
				$curp=$c[9];
				$salario=$c[10];
				$tramite=$c[11];
				$query="Insert into bitacoras.produccion values('$clave','$fecha','$hora','IP','$folio','$nss',0,$id_tipo_mov,'$fec_cierre','$nap',$saldo,0,$id_estatus,-1,'$tramite')";	
				$con->consulta($query);
				$query="Insert into bitacoras.prod_trabajador values('$clave','$fecha','$hora','IP','$folio','$n_opor','$nombre','','','','$curp','',$salario,'')";
				$con->consulta($query);
			break;
			case 'CPNAP':	//Captura producción NAP
				if($tabla=="P")
				{
					$folio=$c[0];
					$nss=$c[1];
					$empleado=$c[2];
					$id_tipo_mov=$c[3];
					$fec_cierre=$c[4];
					$nap=$c[5];
					$saldo=$c[6];
					$afo_cedente=$c[7];
					$id_estatus=$c[8];
					$query="Insert into bitacoras.produccion values('$clave','$fecha','$hora','CNAP','$folio','$nss',$empleado,$id_tipo_mov,'$fec_cierre','$nap',$saldo,$afo_cedente,$id_estatus,-1,'$tramite')";
				}
				else 
				{
					if($tabla=="PT")
					{
						$folio=$c[0];
						$nombre=$c[1];
						$empresa=$c[2];
						$telefono=$c[3];
						$celular=$c[4];
						$curp=$c[5];
						$email_trab=$c[6];
						$sal_mes=$c[7];
						$cp=$c[8];
						$query="Insert into bitacoras.prod_trabajador values('$clave','$fecha','$hora','CNAP','$folio','','$nombre','$empresa','$telefono','$celular','$curp','$email_trab',$salario,'$cp')";
					}
					if($tabla=="AP")
					{
						$folio=$c[0];
						$tramite=$c[1];
						$query="Insert into bitacoras.produccion values('$clave','$fecha','$hora','CNAP','$folio','',-1,-1,'','',-1,-1,-1,-1,'$tramite')";
					}
				}
				$con->consulta($query);
			break;
			case 'VP':		//Valida Captura
				if($tabla=='CC')
					$query="Insert into bitacoras.produccion
							Select '$clave','$fecha','$hora','VP',p.folio,'',-1,-1,'','',-1,-1,-1,1,'' 
							from carobra_2014.produccion p 
								inner join carobra_2014.prod_trabajador pt on (p.folio=pt.folio)
						  	where fec_captura='$cad' and p.folio=pt.folio 
							  	and no_empleado != 0 and CURP !='' and id_tipo_mov!=0 
							  	and fec_traspaso!='' and trabajador!='' and empresa!='' 
							  	and (telefono!='' or celular!='') 
							  	and email_trab != '' and cp != '' and nap != ''";
				else
					$query="Insert into bitacoras.produccion
							Select '$clave','$fecha','$hora','VP',p.folio,'',-1,-1,'','',-1,-1,
									5,-1,'' 
							from carobra_2014.produccion
							where fec_captura='$cad' and cierre_cap=1 
								and (id_estatus_prod > 2 and id_estatus_prod < 6)";
				$con->consulta($query);
			break;
			case 'IPBI':	//Interface de corrección de estatus
				$folio=$c[0];
				$id_estatus=$c[1];
				$query="Insert into bitacoras.produccion values('$clave','$fecha','$hora','IPBI','$folio','',-1,-1,'','',-1,0,$id_estatus,-1,'')";
				$con->consulta($query);
			break;
		}
	}

	function chatbot($folio,$tipo='')
	{
		$fecha= date('d/m/Y');
		$hora= date('H:i:s');
		
		$con= new conector();

		if($tipo=='')
		{
			$query="Select p.folio,curp,no_empleado,nap,cierre_cap,id_estatus_prod,id_rechazo,
						   avp.folio as folio_av,estatus
					from (Select * from carobra_2014.produccion where folio='$folio') p
						left join (Select * from carobra_2014.prod_trabajador where folio='$folio') pt 
								on (p.folio=pt.folio)
						left join (Select * from carobra_2014.prod_rechazos where folio='$folio') pr 
								on (p.folio=pr.folio)
						left join (Select folio_pr,max(folio_av * 1) as folio_av 
									from carobra_2014.apovol_prod ap
									where folio_pr='$folio'
									group by folio_pr) ap on (p.folio=ap.folio_pr)
						left join carobra_2014.av_pagos avp on (ap.folio_av=tramite)";
			$bd=$con->consulta("Select * from chatbot.chatbot where folio='$folio'");
			if ($bd->num_rows != 0)
				$query="Update chatbot.chatbot c,
							   ($query) p
						set c.fecha='$fecha',c.hora='$hora',c.curp=p.curp,c.cierre_cap=p.cierre_cap,
							c.estatus=p.id_estatus_prod,c.id_rechazo=p.id_rechazo,c.folio_av=p.folio_av,
							c.estatus_av=p.estatus,c.nap=p.nap, c.no_empleado=p.no_empleado
						where c.folio=p.folio";
			else
				$query="Insert into chatbot.chatbot
						Select '$fecha','$hora', p.*
						from ($query) p";
			$con->consulta($query);			
		}
		else
		{
			if($tipo=='VP')
			{
				$fecha_cap=$folio;
				$query="Update chatbot.chatbot c,
					   		   (Select folio,cierre_cap,id_estatus_prod from carobra_2014.produccion
								where fec_captura='$fecha_cap' and cierre_cap=1) p
						set c.fecha='$fecha',c.hora='$hora',c.cierre_cap=p.cierre_cap,
							c.estatus=p.id_estatus_prod
						where c.folio=p.folio";
			}
			$con->consulta($query);
		}
	}

	function bloqueo_cierre()
	{
		$conecta= new conector();

		$bd=$conecta->consulta("Select * from bloqueo_cierre");
		if ($bd->num_rows != 0)
			return "S";
		else
			return "N";
	}

	function obtener_gerencia($clave) 
    {
		$conecta= new conector();
        $query = "SELECT id_gerencia FROM estruc_empleados WHERE clave = '$clave'";
        $resultado = $conecta->consulta($query);

        if ($resultado->num_rows > 0) 
        {
            $fila = $resultado->fetch_assoc();
            return $fila['id_gerencia'];
        } 
        else 
        {
            return 0;
        }
    }

	function obtener_gerencia_id($gerencia) 
    {
		$conecta= new conector();
        $query = "SELECT id_gerencia FROM gerencias_negocio WHERE gerencia = '$gerencia'";
        $resultado = $conecta->consulta($query);

        if ($resultado->num_rows > 0) 
        {
            $fila = $resultado->fetch_assoc();
            return $fila['id_gerencia'];
        } 
        else 
        {
            return 0;
        }
    }

	function obtener_socio($nombre, $unidadNeg)
	{
		$conecta= new conector();
        $query = "SELECT clave FROM socio_area_negocio WHERE nombre= '$nombre' AND areas_negocio = '$unidadNeg'";
        $resultado = $conecta->consulta($query);

        if ($resultado->num_rows > 0) 
        {
            $fila = $resultado->fetch_assoc();
            return $fila['clave'];
        } 
        else 
        {
            return '';
        }
	}

	function obtener_unidad($socio = "", $gerencia = "")
	{
		$conecta = new conector();
		
		if ($socio !== "") {
			$query = "SELECT an.clave AS unidad 
					FROM areas_negocio an 
					INNER JOIN socio_area_negocio san ON (an.clave = san.areas_negocio) 
					WHERE san.clave = '$socio'";
			$resultado = $conecta->consulta($query);

			if ($resultado->num_rows > 0) 
			{
				$fila = $resultado->fetch_assoc();
				return $fila['unidad'];
			} 
			else 
			{
				return '';
			}
		} 
		elseif ($gerencia !== "") 
		{
			$query = "SELECT an.clave AS unidad, san.clave AS socio 
					FROM areas_negocio an 
					INNER JOIN socio_area_negocio san ON (an.clave = san.areas_negocio) 
					LEFT JOIN gerencias_negocio gn ON (san.clave = gn.clave_socio) 
					WHERE gn.id_gerencia = $gerencia";
			$resultado = $conecta->consulta($query);

			if ($resultado->num_rows > 0) 
			{
				$fila = $resultado->fetch_assoc();
				return ['unidad' => $fila['unidad'], 'socio' => $fila['socio']];
			} 
			else 
			{
				return ['unidad' => '', 'socio' => ''];
			}
		}
	}

	function obtenerTextoEntreParentesis($cadena) 
	{
		if (mb_detect_encoding($cadena, 'UTF-8', true) === false) 
		{
			$cadena = utf8_encode($cadena);
		}

		if (preg_match('/\((.*?)\)/u', $cadena, $coincidencias))
		{
			return $coincidencias[1];
		}

		return $cadena;
	}

	function obtenerPartida($cadena) 
	{
		if (preg_match('/\bPART\d+\b/', $cadena, $coincidencias)) 
		{
			return $coincidencias[0];
		}
		return '';
	}

	function validarFecha($fecha) 
	{
		$regex = '/^\d{2}\/\d{2}\/\d{4}$/';
			
		if (empty($fecha)) 
		{
			return 
			[
				'valor' => $fecha,
				'validacion' => "NO_VALIDO",
				'anio' => null,
				'mes' => null
			];
		}
			
		if (preg_match($regex, $fecha)) 
		{
			list($dia, $mes, $anio) = explode('/', $fecha);
			
			if (checkdate((int)$mes, (int)$dia, (int)$anio)) 
			{
				return 
				[
					'valor' => $fecha,
					'validacion' => "VALIDO",
					'anio' => (int)$anio,
					'mes' => (int)$mes
				];
			}
		}
			
		return 
		[
			'valor' => $fecha,
			'validacion' => "NO_VALIDO",
			'anio' => 0,
			'mes' => 0
		];
	}

	function procesarEmpleado($empleado) 
	{
		$clave = obtenerTextoEntreParentesis($empleado);
		$nombre = $empleado;

		if (preg_match('/\((.*?)\)/', $empleado)) 
		{
			$nombre = trim(preg_replace('/\s*\(.*?\)\s*/', '', $empleado));
		} 
		elseif (strlen($empleado) > 10) 
		{
			$b = compl_ajuste2(0, '', $empleado);
			$clave = isset($b[0]["clave"]) ? $b[0]["clave"] : 'NO_VALIDO';
		} 
		else 
		{
			$clave = $empleado;
			$nombre = "";
		}

		return [
			'clave' => $clave,
			'nombre' => $nombre
		];
	}

	function procesarProveerdor($proveedor) 
	{
		$conecta = new conector();
		$proveedor = trim($proveedor);

		if (preg_match('/^(.+?)\s*\(([^)]+)\)$/', $proveedor, $matches)) 
		{
			$nombreProveedor = trim($matches[1]);
			$noProveedor = trim($matches[2]);
		} 
		elseif (preg_match('/^PROV\d+$/', $proveedor)) 
		{
			$nombreProveedor = "";
			$noProveedor =  $proveedor;
			$query = "SELECT proveedor FROM proveedores WHERE no_prov = '$noProveedor'";
			$resultado = $conecta->consulta($query);
			if ($resultado->num_rows > 0) 
			{
				$fila = $resultado->fetch_assoc();
				$nombreProveedor = $fila['proveedor'];
			} 
			else 
			{
				$nombreProveedor = '';
			}
			
			
		} 
		else 
		{
			$nombreProveedor = $proveedor;
			$noProveedor = "";
			$query = "SELECT no_prov FROM proveedores WHERE proveedor = '$nombreProveedor'";
			$resultado = $conecta->consulta($query);
			if ($resultado->num_rows > 0) 
			{
				$fila = $resultado->fetch_assoc();
				$noProveedor = $fila['no_prov'];

			} 
			else 
			{
				$noProveedor = '';
			}
		}

		return [
			'proveedor' => $nombreProveedor,
			'no_proveedor' => $noProveedor
		];
	}


?>
