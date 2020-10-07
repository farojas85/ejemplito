<?php 
	require_once "clases/Conexion.php";
	require_once "clases/Ventas.php";

	$objv= new ventas();


	$c=new conectar();
	$conexion= $c->conexion();	
	$idventa=$_GET['idventa'];

 $sql="SELECT pe.idpedidos,
		pe.fechaentrega,
		pe.id_cliente,
		pla.Platoname,
        pla.Platoprecio,
        pla.Platodetalle
	from tblpedidos  as pe 
	inner join tblplatos as pla
	on pe.Platoid=pla.Platoid
	and pe.idpedidos='$idventa'";

$result=mysqli_query($conexion,$sql);

	$ver=mysqli_fetch_row($result);

	$folio=$ver[0];
	$fecha=$ver[1];
	$idcliente=$ver[2];

 ?>	

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Reporte de venta</title>
 	<link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap.css">
 </head>
 <body>
    
 		<br>
 		<table class="table">
 			<tr>
 				<td>Fecha: <?php echo $fecha; ?></td>
 			</tr>
 			<tr>
 				<td>Folio: <?php echo $folio ?></td>
 			</tr>
 			<tr>
 				<td>cliente: <?php echo $objv->nombreCliente($idcliente); ?></td>
 			</tr>
 		</table>


 		<table style="border-collapse: collapse;" border="1">
 			<tr>
                                
 				<td>nombre del plato</td>
 				<td>Precio</td>
 				<td>Cantidad</td>
 				<td>Descripcion</td>
                                
 			</tr>

 			<?php 
 			$sql="SELECT pe.idpedidos,
						pe.fechaentrega,
						pe.id_cliente,
						pla.Platoname,
                                                pla.Platoprecio,
                                                pe.cantidad,
                                                pla.Platodetalle
					from tblpedidos  as pe 
					inner join tblplatos as pla
					on pe.Platoid=pla.Platoid
					where pe.idpedidos='$idventa'";

			$result=mysqli_query($conexion,$sql);
			$total=0;
			while($ver=mysqli_fetch_row($result)):
 			 ?>

 			<tr>
                                
 				<td><?php echo $ver[3]; ?></td>
 				<td><?php echo $ver[4]; ?></td>
 				<td><?php echo $ver[5]; ?></td>
 				<td><?php echo $ver[6]; ?></td>
 			</tr>
 			<?php 
 				$total=$total + $ver[4] * $ver[5];
 			endwhile;
 			 ?>
 			 <tr>
 			 	<td colspan = "4">Total=  <?php echo "S./".$total; ?></td>
 			 </tr>
 		</table>
 </body>
 </html>