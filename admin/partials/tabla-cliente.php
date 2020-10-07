<?php
    include('../includes/config.php');
    include('../includes/consultas.php');
    
    $consulta = new Consultas();
    $consulta->tabla="clientes";
    $consulta->campos="id_cliente,tipodoc_id,numero_documento,nombre,apellido,direccion";
    $consulta->condicion = "upper(nombre) like '%".mb_strtoupper($_GET['busqueda'])."%' 
                            OR upper(apellido) like '%".mb_strtoupper($_GET['busqueda'])."%'";
	$clientes =$consulta->obtenerRegistrosCondicion($dbh);
	$clientes_contar = $consulta->cantidad_registros;
?>
<table id="table-cliente" class="table-sm table-bordered">
    <thead class="thead-dark">
        <tr>
            <th style="width:5%">#</th>
            <th>CLIENTE</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php
    if($clientes_contar == 0)
    {
?>
        <tr>
            <td class="text-center text-danger" colspan="3"> --Datos No Encontrados-- </td>
        </tr>
<?php
    } else {
        $x=0;
        foreach($clientes as $cliente)
        { $x+=1;
?>
        <tr>
            <td><?=$x?></td>
            <td><?=$cliente['nombre']." ".$cliente['apellido']?></td>
            <td title="Seleccionar Cliente">
                <button type="button" class="btn btn-xs text-white bg-alert text-center" style="color:white"  
                    onclick="seleccionarCliente(<?=$cliente['id_cliente']?>)">
                    <i class="fa fa-check"></i>
                </button>
            </td>
        </tr>
<?php
        }
    }
?>
    </tbody>
</table>