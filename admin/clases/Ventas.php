<?php 

class ventas{
	public function obtenDatosProducto($Platoid){
		$c=new conectar();
		$conexion=$c->conexion();

		$sql="SELECT Platoname,Platoregion,Platoprecio,Platodetalle
                                from tblplatos  
                                where Platoid='$Platoid'";
            
                $data = [];
                if($result=mysqli_query($conexion,$sql))
                {
                     while($ver = mysqli_fetch_row($result))
                    {
                        $data=array(
                            'nombre' => $ver[0],
                            'region' => $ver[1],
                            'precio' => $ver[2],
                            'detalle' => $ver[3]
                        );
                    }
                }
                /*$data=[];
                while($ver = mysqli_fetch_row($result))
                {
                   
                }*/
               
                //$ver=mysqli_fetch_row($result);
                
		

				
		return $data;
        }

	public function crearVenta(){

		$c= new conectar();
		$conexion=$c->conexion();

		
		
		$datos=$_SESSION['tablaComprasTemp'];
		$idventa=self::creaFolio();
		$r=0;

		for ($i=0; $i < count($datos) ; $i++) {                    
                    $d=explode("||", $datos[$i]);
                    $sql="INSERT into tblpedidos(idpedidos,
                                                Platoid,
                                                id_cliente,
                                                precio,
                                                cantidad,
                                                fechaentrega)
                            values ('$idventa',
                                            '$d[0]',
                                            '$d[4]',    
                                            '$d[2]',
                                            '$d[5]',
                                            '$d[6]')";
			$r=$r + $result=mysqli_query($conexion,$sql);
		}

		return $r;
	}

	public function creaFolio(){
		$c= new conectar();
		$conexion=$c->conexion();

		$sql="SELECT idpedidos from tblpedidos group by idpedidos desc ";

		if($resul=mysqli_query($conexion,$sql))
                {
                    while($fila=mysqli_fetch_row($resul))
                    {
                        $id = $fila[0];
                        if($id=="" or $id==null or $id==0){
                            return 1;
                        }else{
                            return $id + 1;
                        }
                   }
                }
		
	}
	public function nombreCliente($idCliente){
		$c= new conectar();
		$conexion=$c->conexion();

		 $sql="SELECT apellido,nombre 
			from clientes 
			where id_cliente='$idCliente'";
		$result=mysqli_query($conexion,$sql);

		$ver=mysqli_fetch_row($result);

		return $ver[0]." ".$ver[1];
	}

	public function obtenerTotal($idventa){
		$c= new conectar();
		$conexion=$c->conexion();

		$sql="SELECT precio,cantidad 
				from tblpedidos 
				where idpedidos='$idventa'";
		$result=mysqli_query($conexion,$sql);

		$total=0;

		while($ver=mysqli_fetch_row($result)){
			$total=$total + $ver[0] * $ver[1];
		}

		return $total;
	}
        
        //SELECT * from tblpedidos tp
        //          inner join clientes cli on tp.id_cliente = cli.id
        //          inner join tblplatos tbl on tp.Platoid = tbl.id
        //where fechapedido >= '$fechainio' and fechapedido <='$fechafin'
}

?>