<?php
require '../../vendor/autoload.php';

use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};
use Peru\Sunat\{HtmlParser, Ruc, RucParser};

include("../includes/config.php");
include('../includes/Consultas.php');
include("../models/Cliente.php");

$clientecontroller = new ClienteController();

if(isset($_POST['accion']) || !empty($_POST['accion']))
{
    switch($_POST['accion'])
    {
        case 'registrar-cliente': $clientecontroller->guardarCliente($dbh);break;
    }
}

if(isset($_GET['accion']) || !empty($_GET['accion']))
{
    switch($_GET['accion'])
    {
        case 'valida-dni': $clientecontroller->validarDocumento();break;
        case 'seleccionar-cliente': $clientecontroller->seleccionarCliente($dbh);break;
    }
}

class ClienteController
{
    public function validarDocumento()
    {
        if(isset($_GET['tipo_documento']))
        {
            switch($_GET['tipo_documento'])
            {
                case 1: $this->validaDni();break;
                case 3: $this->validaRuc();break;
                default: $this->noValidado();break;
            }
        }       
    }

    public function validaDni()
    {
        if(isset($_GET['numero_documento']))
        {
            $consulta = new Dni(new ContextClient(), new DniParser());
            $persona = $consulta->get($_GET['numero_documento']);

            if (!$persona) {
                $persona  = new stdClass;
                $persona->codVerifica = "NE";
            }
        }
        echo json_encode($persona);
    } 

    public function validaRuc()
    {
        if(isset($_GET['numero_documento']))
        {
            $consulta = new Ruc(new ContextClient(), new RucParser(new HtmlParser()));
            $persona = $consulta->get($_GET['numero_documento']);

            if (!$persona) {
                $persona  = new stdClass;
                $persona->codVerifica = "NE";
            }
        }
        echo json_encode($persona);
    }

    public function noValidado()
    {
        $persona  = new stdClass;
        $persona->codVerifica = "NE";
        echo json_encode($persona);
    }

    public function guardarCliente($dbh)
    {
        $cliente = new Cliente();
        $cliente->tipodoc_id = $_POST['tipo_documento'];
        $cliente->numero_documento = $_POST['numero_documento'];
        $cliente->nombre = $_POST['nombre'];
        $cliente->apellido = $_POST['apellido'];
        $cliente->telefono = $_POST['telefono'];
        $cliente->direccion = $_POST['direccion'];

        $consulta = new Consultas();
        $consulta->tabla="clientes";
        $consulta->campos ="tipodoc_id,numero_documento,nombre,apellido,telefono,direccion";
        $consulta->valores="?,?,?,?,?,?";
        
        $datos = [
            $cliente->tipodoc_id, 
            $cliente->numero_documento,
            $cliente->nombre,
            $cliente->apellido,
            $cliente->telefono,
            $cliente->direccion 
        ]; 

        $registro =$consulta->insertarDatos($dbh,$datos);
        
        echo $registro;
    }

    public function seleccionarCliente($dbh)
    {
        $consulta = new Consultas();
        $consulta->tabla="clientes c 
                            inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
        $consulta->campos ="id_cliente,tipodoc_id,numero_documento,nombre,apellido,telefono,direccion,td.nombre_corto as documento";
        $consulta->condicion="c.id_cliente =".$_GET['id_cliente'];

        $registro = $consulta->obtenerUnoRegistroCondicion($dbh);
        echo json_encode($registro);
    }
}