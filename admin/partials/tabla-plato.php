<?php
    include('../includes/config.php');
    include('../includes/consultas.php');
    
    $consulta = new Consultas();
    $consulta->tabla="tblplatos";
    $consulta->campos="Platoid,Platoname,Platoprecio";
    $consulta->condicion = "upper(Platoname) like '%".mb_strtoupper($_GET['busqueda'])."%'";
	$platos =$consulta->obtenerRegistrosCondicion($dbh);
	$platos_contar = $consulta->cantidad_registros;
?>
<table id="tabla-plato" class="table-sm table-bordered">
    <thead class="thead-dark">
        <tr>
            <th style="width:5%">#</th>
            <th>PLATO</th>
            <th>PRECIO</th>
            <th>CANT.</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php
    if($platos_contar == 0)
    {
?>
        <tr>
            <td class="text-danger text-center" colspan="4">--B&Uacute;SQUEDA NO REALIZADA--</td>
        </tr>
<?php
    } else {
        $x=0;
        foreach($platos as $plato)
        { $x+=1;
?>
        <tr>
            <td><?=$x?></td>
            <td><?=$plato['Platoname']?></td>
            <td><?=number_format($plato['Platoprecio'],2)?></td>
            <td><input type="number" name="" id="cantidad_<?=$x?>" class="form-control" size="10" min="1" max="50" value=1> </td>
            <td title="Seleccionar Plato">
                <button type="button" class="btn btn-xs text-white bg-alert text-center" style="color:white"  
                    onclick="seleccionarPlato(<?=$plato['Platoid']?>,<?=$x?>)">
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