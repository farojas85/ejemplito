<?php
include('../includes/config.php');
include('../includes/consultas.php');

$consulta = new Consultas();
$consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                    "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
$consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                    "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                    "c.numero_documento,c.direccion";
$consulta->orden="WHERE upper(c.nombre) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                    "OR upper(c.apellido) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                    "OR concat(tp.serie,'-',tp.numero) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                "ORDER BY tp.fechaentrega desc, tp.idpedidos";
$pedidos =$consulta->obtenerRegistrosTodosOrden($dbh);
$pedidos_contar = $consulta->cantidad_registros;

$limite = 10;
if (!isset($_GET['pagina'])) {
    $pagina = 1;
} else{
    $pagina = $_GET['pagina'];
}
$total_paginas = ceil($pedidos_contar/$limite);
$inicio = ($pagina-1)*$limite;

$consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                    "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
$consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                    "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                    "c.numero_documento,c.direccion";
$consulta->limite =$limite;
$consulta->inicio = $inicio;
$consulta->orden="WHERE upper(c.nombre) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                    "OR upper(c.apellido) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                    "OR concat(tp.serie,'-',tp.numero) LIKE '%".mb_strtoupper($_GET['buscar'])."%' ".
                "ORDER BY tp.fechaentrega desc, tp.idpedidos";
$pedidos =$consulta->obtenerRegistrosTodosOrdenPaginacion($dbh);
$pedidos_contar = $consulta->cantidad_registros;
?>
<table style="text-align: center;" id="table-pedido">
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Cliente</th>
            <th>Total (S/)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
<?php
    $item=0;
    if($pedidos_contar == 0)
    {
?>
        <tr>
            <td colspan="6" class="text-center text-danger">--DATOS NO ENCONTRADOS--</td>
        </tr>
<?php
    } else {
        foreach($pedidos as $pedido)
        {
            $item +=1;
?>
        <tr>
            <td><?=$item?></td>
            <td><?=$pedido['fechaentrega']?></td>
            <td><?=$pedido['serie'].'-'.$pedido['numero']?></td>
            <td><?=$pedido['cliente']?></td>
            <td><?=$pedido['total']?></td>
            <td>
                <button type="button" class="btn bg-info btn-xs"
                    title="Imprimir Documento Hoja A4/A5"
                    onclick="imprimirHoja(<?=$pedido['idpedidos']?>)">
                    <i class="fa fa-file-pdf-o text-white"></i>
                </button>
                <button type="button" class="btn bg-success btn-xs"
                    title="Imprimir Ticket"
                    onclick="imprimirTicket(<?=$pedido['idpedidos']?>)">
                    <i class="fa fa-file-text-o text-white"></i>
                </button>
                <button type="button" class="btn bg-danger btn-xs"
                    title="Eliminar Comprobante"
                    onclick="eliminarPedido(<?=$pedido['idpedidos']?>)">
                    <i class="fa fa-trash-o text-white"></i>
                </button>
            </td>
        </tr>
<?php
        }    
    }
?>
    </tbody>
</table>
<ul class="pagination">
    <li><a href="?pagina=1&buscar=<?=$_GET['buscar']?>">Primero</a></li>
    
    <?php for($p=1; $p<=$total_paginas; $p++){?>
        
        <li class="<?= $pagina == $p ? 'active' : ''; ?>"><a href="<?='?pagina='.$p."&buscar=".$_GET['buscar'];?>"><?= $p; ?></a></li>
    <?php }?>
    <li><a href="?pagina=<?=$total_paginas?>&buscar=<?=$_GET['buscar']?>">&Uacute;ltimo</a></li>
</ul>