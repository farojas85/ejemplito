<?php
session_start();

include('../includes/config.php');
include('../includes/consultas.php');

setlocale(LC_TIME,"es_ES");

$mes= "";
if(isset($_GET['mes']))
{
    $mes= $_GET['mes'];
}
$consulta = new Consultas();
$consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                    "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
$consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                    "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                    "c.numero_documento,c.direccion";
$consulta->condicion = "EXTRACT(MONTH FROM tp.fechaentrega) = '".$_GET['mes']."' ".
                        "ORDER BY tp.fechaentrega asc,tp.idpedidos asc";
$pedidos =$consulta->obtenerRegistrosCondicion($dbh);
$pedidos_contar = $consulta->cantidad_registros;

$mes = $_GET['mes'];
$nombre_mes = "";
switch($mes)
{
    case '01': $nombre_mes="Enero";break;
    case '02': $nombre_mes="Febrero";break;
    case '03': $nombre_mes="Marzo";break;
    case '04': $nombre_mes="Abril";break;
    case '05': $nombre_mes="Mayo";break;
    case '06': $nombre_mes="Junio";break;
    case '07': $nombre_mes="Julio";break;
    case '08': $nombre_mes="Agosto";break;
    case '09': $nombre_mes="Setiembre";break;
    case '10': $nombre_mes="Octubre";break;
    case '11': $nombre_mes="Noviembre";break;
    case '12': $nombre_mes="Diciembre";break;
}
?>
<h3 class="text-center">Reporte Mensual de <?=$nombre_mes?></h3>
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
    $suma_total =0; 
    if($pedidos_contar == 0){
?>
    <tr>
        <td class="text-center text-danger" colspan="5">--PEDIDOS NO ENCONTRADOS / NO REGISTRADOS DEL MES --</td>
    </tr>
<?php
    } else {
        $item = 0;        
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
    }
?>
        <tr>
            <td colspan="3"></td>
            <td class="text-center font-weight-bold"><b>TOTAL S/</b></td>
            <td>
                <?=number_format($suma_total,2)?>
                <input type="hidden" id="suma_total" value="<?=$suma_total?>">
            </td>
        </tr>

    <tbody>
</table>