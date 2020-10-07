<?php session_start(); ?>
<?php 
	
	require_once "../../clases/Conexion.php";
	require_once "../../clases/Ventas.php";
	$obj= new ventas();

        //print_r($_SESSION['tablaComprasTemp']);

	if(count($_SESSION['tablaComprasTemp'])==0){
		echo 0;
	}else{
		$result=$obj->crearVenta();
		//unset($_SESSION['tablaComprasTemp']);
		echo $result;
	}
 ?>