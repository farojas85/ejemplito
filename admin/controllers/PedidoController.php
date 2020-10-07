<?php
session_start();
require '../../vendor/autoload.php';
use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};
use Peru\Sunat\{HtmlParser, Ruc, RucParser};

include("../includes/config.php");
include('../includes/Consultas.php');
include("../models/Cliente.php");
include('../models/Pedido.php');
include('../models/PedidoDetalle.php');

$pedidocontroller = new PedidoController();

if(isset($_POST['accion']) || !empty($_POST['accion']))
{
    switch($_POST['accion'])
    {
        case 'guardar-pedido': $pedidocontroller->guardarPedido($dbh);break;
        case 'eliminar-pedido': $pedidocontroller->eliminarPedido($dbh);break;
    }
}
if(isset($_GET['accion']) || !empty($_GET['accion']))
{
    switch($_GET['accion'])
    {
        case 'anadir-carrito': $pedidocontroller->anadirCarrito($dbh);break;
        case 'eliminar-item': $pedidocontroller->eliminarItem();break;
        case 'limpiar-carrito': $pedidocontroller->limpiarCarrito();break;
    }
}

class PedidoController
{
    public function anadirCarrito($dbh)
    {
        
        $consulta = new Consultas();
        $consulta->tabla="tblplatos";
        $consulta->campos ="Platoid,Platoname,Platoprecio";
        $consulta->condicion="Platoid =".$_GET['Platoid'];

        $plato = $consulta->obtenerUnoRegistroCondicion($dbh);
        $mensaje = ""; 
        if(!isset($_SESSION['carrito']))
        {
            $mensaje = "F";
            $_SESSION['carrito'] = array();

            array_push($_SESSION['carrito'],[ 
                'id' => $plato->Platoid,
                'cantidad' => $_GET['cantidad'],
                'detalle' => $plato->Platoname,
                'precio' => $plato->Platoprecio,
                'importe' => $_GET['cantidad']*$plato->Platoprecio
            ]);
        } else {
            $id = array_search($_GET['Platoid'],array_column($_SESSION['carrito'],'id'));
            if($id !== false) {
                $mensaje = "V";
            } else {
                $mensaje = "F";
                array_push( $_SESSION['carrito'],[
                    'id' => $plato->Platoid,
                    'cantidad' => $_GET['cantidad'],
                    'detalle' => $plato->Platoname,
                    'precio' => $plato->Platoprecio,
                    'importe' => $_GET['cantidad']*$plato->Platoprecio
                ]);
            }
        }
        echo $mensaje;
    }

    public function eliminarItem()
    {
       unset($_SESSION['carrito'][$_GET['item']]);
       
        if(count($_SESSION['carrito'])==0)
        {
            unset($_SESSION['carrito']);
        }
        echo 1;
    }

    public function guardarPedido($dbh)
    {
        //Modelo Pedido
        $registrado_id = 0;

        $pedido = new Pedido();
        $pedido->id_cliente = $_POST['cliente_id'];
        $pedido->fechaentrega = $_POST['fecha'];
        $pedido->serie = ($_POST['tipodoc'] ==3) ? 'F001' : 'B001';
        $pedido->numero = $this->maxIdBoleta($_POST['tipodoc'],$dbh);
        $pedido->subtotal = $_POST['sub_total'];
        $pedido->igv = $_POST['igv'];
        $pedido->total = $_POST['total'];
        $pedido->status =1;
        
        
        $consulta = new Consultas();
        $consulta->tabla="tblpedidos";
        $consulta->campos ="id_cliente,fechaentrega,serie,numero,subtotal,igv,total,status";
        $consulta->valores="?,?,?,?,?,?,?,?";

        $datos = [
            $pedido->id_cliente, 
            $pedido->fechaentrega,
            $pedido->serie,
            $pedido->numero,
            $pedido->subtotal,
            $pedido->igv,
            $pedido->total,
            $pedido->status
        ];
        
        $registrop =$consulta->insertarDatos($dbh,$datos);
        if($registrop >=1)
        {
            $registrado_id +=1;
        }
        
        foreach($_SESSION['carrito'] as $carrito)
        {
            $detalle = new PedidoDetalle();
            $detalle->idpedidos =  $registrop;
            $detalle->Platoid = $carrito['id'];
            $detalle->cantidad =  $carrito['cantidad'];
            $detalle->detalle =  $carrito['detalle'];
            $detalle->precio =  $carrito['precio'];
            $detalle->importe =  $carrito['importe'];

            $consulta = new Consultas();
            $consulta->tabla="tblpedidos_detalle";
            $consulta->campos ="idpedidos,Platoid,cantidad,detalle,precio,importe";
            $consulta->valores="?,?,?,?,?,?";

            $datos = [
                $detalle->idpedidos, 
                $detalle->Platoid,
                $detalle->cantidad,
                $detalle->detalle,
                $detalle->precio,
                $detalle->importe
            ]; 

            $registro =$consulta->insertarDatos($dbh,$datos);
            if($registro >=1) {
                $registrado_id +=1;
            }
        }

        if($registrado_id >= 2)
        {
            echo $registrop;
        }

    }

    public function limpiarCarrito()
    {
        unset($_SESSION['carrito']);
        echo 1;
    }

    public function maxIdBoleta($tipodoc,$dbh)
    {
        $consulta = new Consultas();
        $consulta->campos="max(numero) as numero";
        $consulta->tabla="tblpedidos";
        $consulta->condicion = "serie ='B001'";
        if($tipodoc == 3)
        {
            $consulta->condicion = "serie ='F001'";
        }
        $resultado = $consulta->obtenerUnoRegistroCondicion($dbh);
        return (!$resultado->numero) ? 1 : $resultado->numero +1;
    }

    public function eliminarPedido($dbh)
    {
        $consulta = new Consultas();
        $consulta->tabla="tblpedidos_detalle";
        $consulta->condicion = "idpedidos = ".$_POST['idpedido'];
        $respuesta = $consulta->eliminarRegistro($dbh);

        if($respuesta)
        {
            $consulta->tabla="tblpedidos";
            $consulta->condicion = "idpedidos = ".$_POST['idpedido'];
            $respuesta = $consulta->eliminarRegistro($dbh);
            echo $respuesta;
        }
    }
}