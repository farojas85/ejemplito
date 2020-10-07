<?php session_start(); ?>
<?php 

	
	$index=$_POST['ind'];
	unset($_SESSION['tablaComprasTemp'][$index]);
	$datos=array_values($_SESSION['tablaComprasTemp']);
	unset($_SESSION['tablaComprasTemp']);
	$_SESSION['tablaComprasTemp']=$datos;

 ?>