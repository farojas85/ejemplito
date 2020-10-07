<?php session_start(); ?>
<?php 
	
	require_once "../../clases/Conexion.php";

	$c= new conectar();
	$conexion=$c->conexion();

        $idcliente=$_POST['clienteVenta'];
	$idproducto=$_POST['productoVenta'];
	$precio=$_POST['precioV'];
        $cantidad = $_POST['cantidadV'];
        $fecha =  $_POST['fecha'];

	$sql="SELECT nombre,apellido 
			from clientes 
			where id_cliente='$idcliente'";
	$result=mysqli_query($conexion,$sql);

	$c=mysqli_fetch_row($result);

	$ncliente=$c[1]." ".$c[0];

	$sql="SELECT Platoname
			from tblplatos 
			where Platoid='$idproducto'";
	$result=mysqli_query($conexion,$sql);

	$nombreproducto=mysqli_fetch_row($result)[0];

	$articulo=$idproducto."||".
				$nombreproducto."||".
				$precio."||".
				$ncliente."||".
				$idcliente."||".
                                $cantidad."||".
                                $fecha;

	$_SESSION['tablaComprasTemp'][]=$articulo;

 ?>