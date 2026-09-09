<?php 
	session_start();

	include("../funciones/encripta.php");
	
	$n=recibe_POST('n');
	$pwd=recibe_POST('txt-pwd');		
	$usuario=recibe_POST('txt-usuario');
	$pwd_new=recibe_POST('txt-new-pwd');
	$unegocio=recibe_POST('sel-unegocio');

	$conecta= new conector();
		
	$conecta->consulta("SET NAMES utf8");

	if($n == 1)
	{
		$query= "Select nombre from usuarios where clave='" . utf8_encode($usuario) . "' and SHA1(contrasena)=SHA1('$pwd') and activo=1";

		$bd=$conecta->consulta($query);

		if ($bd->num_rows != 0)
		{
			$tb=mysqli_fetch_array($bd);
?>
			|
			<div class='row titulo-prm'>
				<h3>Cambiar contraseña</h3>
			</div>
			<form  id="frm-contra">
				<input type='hidden' id='txt-usuario' name='txt-usuario' value='<?php echo utf8_encode($usuario); ?>' />
				<div class="form-group">
					<div class="col-xs-12 form-group-sm">
						<label for='txt-new-pwd' class='control-label'>Nueva contraseña</label>
						<input type="password" class="form-control" id="txt-new-pwd" name="txt-new-pwd" style="margin-bottom: 10px;">
					</div>
					<div class="col-xs-12 form-group-sm" >
						<label for='txt-confirm-pwd' class='control-label'>Confirmar contraseña</label>
						<input type="password" class="form-control" id="txt-confirm-pwd" name="txt-confirm-pwd" placeholder="" style="margin-bottom: 3px;">
					</div>
				</div>
			</form>
			<div class="col-xs-12 text-center" style="margin-bottom: 3px;">
				<span style='font-size: 10px;'>
					Tu nueva contraseña debe de ser de 10 caracteres. Debe contener al menos 2 mayúsculas y 2 minúsculas, al menos debe tener 2 caracteres numéricos y un caracter especial (unicamente *,-,_)
				</span>
			</div>
			<div class="col-xs-12 text-center" id='men_error' style='font-size: 12px; color:red;'>
				&nbsp;&nbsp;
			</div>
			<div class="col-xs-12 col-sm-6">
				<button class="btn btn-primary active btn-sm btn-block" id="btn-cancela" style="margin-bottom: 10px;">
					<span class="glyphicon glyphicon-remove"></span> Cancelar
				</button>
			</div>
			<div class="col-xs-12 col-sm-6">
				<button class="btn btn-primary active btn-sm btn-block" id="btn-act-change">
					<span class="glyphicon glyphicon-ok"></span> Aceptar
				</button>
			</div>
<?php
		}
		else
			echo "|N";
	}
	if($n==2)
	{
		$query="Update usuarios set contrasena='$pwd_new' where clave='" . utf8_encode($usuario) . "'";
		$conecta->consulta($query);
		echo "|Contraseña cambiada con éxito";
	}
	if($n==3)
	{
		$query= "Select nombre from usuarios where clave='" . utf8_encode($usuario) . "' and SHA1(contrasena)=SHA1('$pwd') and activo=1";
		$bd=$conecta->consulta($query);

		if ($bd->num_rows != 0)
		{
			$row=$bd->fetch_array(MYSQLI_NUM);
			$_SESSION["nombre"]=$row[0];
			echo "|Y";
			echo "|afore/contiene.php";
		}
		else
			echo "|N";
	}
?>