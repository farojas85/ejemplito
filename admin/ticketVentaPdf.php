<?php 
	require_once "clases/Conexion.php";
	require_once "clases/Ventas.php";

	$objv= new ventas();


	$c=new conectar();
	$conexion= $c->conexion();	
	$idventa=$_GET['idpedidos'];

 $sql="SELECT pe.idpedidos,
		pe.fechaentrega,
		pe.id_cliente,
		pla.Platoname,
        pla.Platoprecio
   
	from tblpedidos as pe 
	inner join tblplatos as pla
	on pe.Platoid=pla.Platoid
	where pe.idpedidos='$idventa'";

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
 	<style type="text/css">
            p{
                font-size: 5px;
            }
            u{
                text-align: center;
            }
		
@page {
            margin-top: 0.3em;
            margin-left: 0.6em;
        }
    body{
    	font-size: xx-small;
    }
    td{
        font-size: 5px;
    }
   
	</style>

 </head>
 <body>
 <center><h4>WADO</h4></center>

     <p>PEDIDOS 971188803 / 971188807 somos "VIP WADO" en facebook</p>
     
                ******************************************
                
 		<p>
                    Fecha: <?php echo $fecha; ?><br>
 		
                    Folio: <?php echo $folio ?><br>
 		
 			Cliente: <?php echo $objv->nombreCliente($idcliente); ?>
                        ********************************************
 		</p>
 		
 		<table style="border-collapse: collapse;" border="1">
 			<tr>
 				<td>Nombre</td>
 				<td>Precio</td>
                                <td>Cantidad</td>
                                <td>Importe</td>
                                
 			</tr>
 			<?php 
 				$sql="SELECT pe.idpedidos,
							pe.fechaentrega,
							pe.id_cliente,
							pla.Platoname,
					        pla.Platoprecio,
					        pe.cantidad
						from tblpedidos  as pe 
						inner join tblplatos as pla
						on pe.Platoid=pla.Platoid
						where pe.idpedidos='$idventa'";

				$result=mysqli_query($conexion,$sql);
				$total=0;
				while($mostrar=mysqli_fetch_row($result)){
 			 ?>
 			<tr>
 				<td><?php echo $mostrar[3]; ?></td>
 				<td><?php echo $mostrar[4] ?></td>
                                <td><?php echo $mostrar[5] ?></td>
                                <td><?php echo $mostrar[5] * $mostrar[4] ?></td>
 			</tr>
 			<?php
 				$total=$total + $mostrar[4]*$mostrar[5];
 				} 
 			 ?>
                        <tr>
                             <td>Total:</td>
                             <td>-</td>
                             <td>-</td>
                             <td> <?php echo "$".$total ?></td><
                        <tr>
 			
 		</table>
                ********************************************
                <center>GRACIAS POR TU PREFERENCIA</center>
 		
 </body>
 </html>
 