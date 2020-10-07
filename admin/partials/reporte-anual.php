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
$consulta->tabla="tblpedidos";
$consulta->campos="extract(year from fechaentrega) as anio,".
                    "LPAD(EXTRACT(MONTH from fechaentrega),2,'00') as mes,".
                    "SUM(total) as total";
$consulta->orden = "WHERE extract(year from fechaentrega) = '".$_GET['anio']."' GROUP BY anio,mes ORDER BY anio desc";
$pedidos =$consulta->obtenerRegistrosTodosOrden($dbh);
$pedidos_contar = $consulta->cantidad_registros;
?>
<h3 class="text-center">Reporte Anual de <?=$_GET['anio']?></h3>
<table id="table-pedido" class="table-sm table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Mes</th>
            <th>Total (S/)</th>
        </tr>
    </thead>
    <tbody>
<?php
    $suma_total = 0;
    if($pedidos_contar == 0)
    {
?>
        <tr>
            <td class="text-center text-danger" colspan="3">--PEDIDOS NO ENCONTRADOS / NO REGISTRADOS DEL A&Ntilde;O --</td>
        </tr>
<?php
    } else {
        $item = 0;
        foreach($pedidos as $pedido){
            $item +=1;
            $suma_total +=$pedido['total'];
            $nombre_mes = "";
            switch($pedido['mes'])
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
        <tr>
            <td><?=$item?></td>
            <td><?=$nombre_mes?></td>
            <td><?=$pedido['total']?></td>
        </tr>
<?php
        }
    }
?>
         <tr>
            <td></td>
            <td class="text-right font-weight-bold"><b>TOTAL</b></td>
            <td>
                <?="S/ ".number_format($suma_total,2)?>
                <input type="hidden" id="suma_total" value="<?=$suma_total?>">
            </td>
        </tr>
    </tbody>
</table>
