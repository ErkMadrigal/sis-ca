<?php 
	class conector
	{
		private $servidor;
		private $usuario;
		private $psw;
		private $bd;
		var $conexion;
		private $tbc;

		function __construct($db="carobra",$server="localhost",$user="root", $contra="admin")
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
			$this->tbc=$this->conexion->query($fil);
			return $this->tbc;
		}
	}

	class select
	{
		private $bd;
		private $servidor;
		private $usuario;
		private $pwd;
		var $fil;
		var $clase;
		private $datos;
		var $identificador;
		
		function __construct($id,$clase="")
		{
			$this->fil="";
			$this->bd="";
			$this->pwd="";
			$this->usuario="";
			$this->servidor="";
			$this->clase=$clase;
			$this->identificador=$id;
		}

		function conectar($db="",$server="",$user="", $contra="")
		{
			$this->bd=$db;
			$this->servidor=$server;
			$this->usuario=$user;
			$this->pwd=$contra;
		}
		function query($query)
		{
			$this->fil=$query;
		}

		function lista($valor1,$valor2)
		{
			$i=count($this->datos);
        	$this->datos[$i][0]=$valor1;
        	$this->datos[$i][1]=$valor2;
		}
		
		function css($prop,$valor,$elemento="")
		{
?>
<script type="text/javascript">
			css('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>','<?php echo $elemento?>')
</script>
<?php
		}
		
		function attr($prop,$valor)
		{
?>
<script type="text/javascript">
			attr('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>')
</script>
<?php
		}
		
		function crear($selected=null)
		{
			if($this->fil <> "")
			{
				$i=0;
				if($this->bd == "")
					$con_select=new conector();
				else 
				{
					if($this->servidor =="")
						$con_select=new conector($this->bd);
					else 
						$con_select=new conector($this->bd,$this->servidor,$this->usuario,$this->pwd);
				}
				$tb=$con_select->consulta($this->fil);
			    while ($row = $tb->fetch_array(MYSQLI_NUM)) 
    			{
	        		$this->datos[$i][0]=$row[0];
	        		$this->datos[$i][1]=$row[1];
    	    		$i++;
    			}
    			$tb->close;
				$con_select->close;
			}
			$fils="<select id='" . $this->identificador . "' name='" . $this->identificador . "'"; 
			if($this->clase <> "")
				$fils.=" class='" . $this->clase . "'";
			$fils.=">";
			echo $fils;
			echo "<option class='op2' value=''>&nbsp;</option>";
			$clasop="op1";
			for ($i=0;$i<count($this->datos);$i++)
			{
				if($selected==$this->datos[$i][0])
					echo "<option class='$clasop' value='" . $this->datos[$i][0] . "' SELECTED>" . utf8_encode($this->datos[$i][1]) . "</option>";
				else 
					 echo "<option class='$clasop' value='" . $this->datos[$i][0] . "'>" . utf8_encode($this->datos[$i][1]) . "</option>";
				if($clasop=="op1")
					$clasop="op2";
				else 
					$clasop="op1";
			}
			echo "</select>";
			$iden=$this->identificador;
		}
	}
	
	class textbox
	{
		var $tipo;
		var $clase;
		var $identificador;
		private $lista;
	
		function __construct($id, $clase="",$tipo="text")
		{
			$this->tipo=$tipo;
			$this->clase=$clase;
			$this->identificador=$id;
		}
				
		function css($prop,$valor)
		{
?>
<script type="text/javascript">
			css('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>','')
</script>
<?php
		}
		
		function attr($prop,$valor)
		{
?>
<script type="text/javascript">
			attr('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>')
</script>
<?php
		}
		
		function autocompletar($query,$carlon=1)
		{
			$_SESSION[$this->identificador] =$query;
?>
<script type="text/javascript">
			$("#" + '<?php echo $this->identificador?>').data("minl",'<?php echo $carlon?>');
			$("#" + '<?php echo $this->identificador?>').data("t",1);
</script>
<?php
		}
		
		function autocompletar_lista($lista,$carlon=1)
		{
			$this->lista=array_values($lista);
?>
<script type="text/javascript">
			$("#" + '<?php echo $this->identificador?>').data("minl",'<?php echo $carlon?>');
			$("#" + '<?php echo $this->identificador?>').data("t",2);
			var datos= new Array(<?php echo "'" . implode("','", $this->lista) . "'"?>);
</script>
<?php
		}

		function crear($valor="",$alterno="")
		{
			$fils="<input ";
			if(strlen($alterno)> 0)
				$fils.=  "placeholder='" . $alterno . "' "; 
			$fils.="type='" . $this->tipo . "' id='" . $this->identificador . "' name='";
			$fils.= $this->identificador . "' value='" . $valor . "'>";
			echo $fils;
		}
	}
	
	class img
	{
		var $alt;
		var $img_t;
		var $img_a;
		var $clase;
		var $identificador;
		
		function __construct($id,$imgt,$imga="",$clase="")
		{
			$this->alt="";
			$this->img_t=$imgt;
			$this->img_a=$imga;
			$this->clase=$clase;
			$this->identificador=$id;
		}
		
		function alt($alt)
		{
			$this->alt=$alt;
		}
		
		function css($prop,$valor)
		{
?>
<script type="text/javascript">
			css('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>','')
</script>
<?php
		}
		
		function attr($prop,$valor)
		{
?>
<script type="text/javascript">
			attr('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>')
</script>
<?php
		}
		
		function crear()
		{
			$fil=  "<img src='" . $this->img_t . "' ";
			if($this->identificador <> "")
				$fil.= "id='$this->identificador' name='$this->identificador' ";
			if($this->clase <> "")
				$fil.="class='$this->clase' ";
			$fil.= "/>";
			echo $fil;
			if($this->img_a <> "")
			{
?>
<script type="text/javascript">
				$("#" + '<?php echo $this->identificador?>').data("img1",'<?php echo $this->img_t?>');
				$("#" + '<?php echo $this->identificador?>').data("img2",'<?php echo $this->img_a?>');
</script>
<?php				
			}
		}
	}
	
	class div
	{
		var $clase;
		var $identificador;
		var $posicion;
		var $top;
		var $left;
		var $width;
		var $height;
		
		function __construct($id,$clase="",$position="",$width="", $height="")
		{
			$this->top="";
			$this->left="";
			$this->width=$width;
			$this->clase=$clase;
			$this->height=$height;
			$this->posicion=$position;
			$this->identificador=$id;
		}
		
		function ubica($t,$l)
		{
			$this->top=$t;
			$this->left=$l;
		}
		
		function css($prop,$valor)
		{
?>
<script type="text/javascript">
			css('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>','')
</script>
<?php
		}
		
		function attr($prop,$valor)
		{
?>
<script type="text/javascript">
			attr('<?php echo $this->identificador?>','<?php echo $prop?>','<?php echo $valor?>')
</script>
<?php
		}
		
		function crear()
		{
			$fil="<div id='" . $this->identificador . "' ";
			if($this->clase <> "")
				$fil.="class='" . $this->clase . "'";
			$fil.=">";
			echo $fil;
			if($this->posicion <> "")
			{
?>
<script type="text/javascript">
				css('<?php echo $this->identificador?>','position','<?php echo $this->posicion?>','')
</script>
<?php
			}
			if($this->top <> "")
			{
?>
<script type="text/javascript">
				css('<?php echo $this->identificador?>','top','<?php echo $this->top?>','')
</script>
<?php
			}
			if($this->left <> "")
			{
?>
<script type="text/javascript">
				css('<?php echo $this->identificador?>','left','<?php echo $this->left?>','')
</script>
<?php
			}
			if($this->width <> "")
			{
?>
<script type="text/javascript">
				css('<?php echo $this->identificador?>','width','<?php echo $this->width?>','')
</script>
<?php
			}
			if($this->height <> "")
			{
?>
<script type="text/javascript">
				css('<?php echo $this->identificador?>','height','<?php echo $this->height?>','')
</script>
<?php
			}
		}
		
		function close()
		{
			echo "</div>";
		}
	}
?>

<script type="text/javascript">
	function css(iden,prop,valor,elemento)
	{
		$('#' + iden + ' ' + elemento).css(prop,valor);
	}

	function attr(iden,prop,valor)
	{
		$('#' + iden).attr(prop,valor);
	}

	$(document).ready(function(){
		$("input[type=text]").keypress(function()
		{
			if($("#" + this.id).data("minl") != undefined)
			{
				minl=$("#" + this.id).data("minl");
				cid=this.id;
				if($("#" + this.id).data("t") == 1)
				{
					$("#" + this.id).autocomplete({source:'autocompleta.php?c=' + cid,minLength:minl});
				}
				else
				{
					$("#" + this.id).autocomplete({source:datos,minLength:minl});
				}
			}
		});

		$("img").mouseenter(function(evento)
		{
			if($("#" + this.id).data("img2") != undefined)
				$("#" + this.id).attr("src",$("#" + this.id).data("img2"));
		});

		$("img").mouseleave(function(evento)
		{
			if($("#" + this.id).data("img2") != undefined)
				$("#" + this.id).attr("src",$("#" + this.id).data("img1"));
		});
	});
</script>