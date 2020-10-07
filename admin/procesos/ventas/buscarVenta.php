<?php session_start(); ?>
<?php 
	
	require_once "clases/Conexion.php";
	require_once "clases/Ventas.php";
        $c= new conectar();
	$conexion=$c->conexion();
        $buscar = $_POST['buscar'];
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
        $idcliente=$ver[2];
        $objv->nombreCliente($idcliente);
	
         
                $SQL_READ = "select * from tblpedidos where $objv like '%".$buscar."%'";
	$sql_query = mysql_query($conexion,$SQL_READ)
                
        ?>