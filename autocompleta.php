<?php
	session_start();

	$servidor='localhost';
	$usuario='root';
	$psw='Cr0g2021!';
	//$psw="admin2020$";
	$bd='carobra_2014';

	$id=$_GET['c'];
	$d=$_GET['term'];

	$conexion= new mysqli();
	$conexion->connect($servidor,$usuario,$psw,$bd);
	$fil=$_SESSION[$id];
	while(strlen(strstr($fil,"$",true))> 0)
	{
		$fil=strstr($fil,"$",true) . $d . substr(strstr($fil,"$"),1-strlen(strstr($fil,"$")));
	}
	$datos=array();
	$tb=$conexion->query($fil);
	if ($tb->num_rows > 0)
	{
		while ($row = $tb->fetch_array(MYSQLI_NUM))
	    {
			//$datos[]=array('value'=>utf8_encode($row[0]));
			$datos[]=array('value'=>$row[0]);
    	}
	}
	$conexion->close;
	echo json_encode($datos);
?>