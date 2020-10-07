<?php
session_start();

include('../includes/config.php');
include('../includes/consultas.php');

$fecha_inicio= "";
if(isset($_GET['fecha_inicio']))
{
    $fecha_inicio= $_GET['fecha_inicio'];
}
$fecha_final= "";
if(isset($_GET['fecha_final']))
{
    $fecha_final= $_GET['fecha_final'];
}
$consulta = new Consultas();
$consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                    "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
$consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                    "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                    "c.numero_documento,c.direccion";
$consulta->condicion = "tp.fechaentrega BETWEEN '".$_GET['fecha_inicio']."' AND '".$_GET['fecha_final']."'";
$pedidos =$consulta->obtenerRegistrosCondicion($dbh);
$pedidos_contar = $consulta->cantidad_registros;

    
?>
<h3 class="text-center">Reporte Diario</h3>
<table id="table-pedido" class="table-sm table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Cliente</th>
            <th>Total (S/)</th>
        </tr>
    </thead>
    <tbody>
<?php 
    $item = 0;
    $suma_total =0;
    foreach($pedidos as $pedido): 
        $item +=1;
        $suma_total += $pedido['total'];
        $date=date_create($pedido['fechaentrega']);
?>
        <tr>
            <td><?=$item?></td>
            <td><?=date_format($date,"d/m/Y")?></td>
            <td><?=$pedido['serie'].'-'.$pedido['numero']?></td>
            <td><?=$pedido['cliente']?></td>
            <td><?=$pedido['total']?></td>
        </tr>
<?php
    endforeach;
?>
        <tr>
            <td colspan="3"></td>
            <td class="text-center font-weight-bold"><b>TOTAL S/</b></td>
            <td>
                <?=number_format($suma_total,2)?>
                <input type="hidden" id="suma_total" value="<?=$suma_total?>">
            </td>
        </tr>

    </tbody>
</table>