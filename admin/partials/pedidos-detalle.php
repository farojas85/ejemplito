<?php
    session_start();
?>
<table>
    <thead>
        <tr>
            <th></th>
            <th>Cant.</th>
            <th width="50%">Descripcion</th>
            <th>Prec. Unit.</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
<?php
    $sub_total = 0;
    $igv=0;
    $total =0;
    if(!isset($_SESSION['carrito']))
    {
?>
        <tr>
            <td colspan="5" class="text-danger text-center"> -- Platos no A&ntilde;adidos -- </td>
        </tr>
<?php
    } else {    
        $item=0;    
        foreach($_SESSION['carrito'] as $carrito)
        {
           
            $sub_total += $carrito['importe'];
?>
        <tr>
            <td>
                <button type="button" class="btn bg-danger btn-sm"
                    onclick="eliminarItem(<?=$item?>)">
                    <i class="fa fa-trash text-white"></i>
                </button>
            </td>
            <td><?=$carrito['cantidad']?></td>
            <td><?=$carrito['detalle']?></td,2)>
            <td><?=number_format($carrito['precio'],2)?></td>
            <td><?=number_format($carrito['importe'],2)?></td>
        </tr>
<?php
            $item+=1;
        }
    }

    $total = $sub_total + $igv;
?>
        <tr>
            <td colspan="3"></td>
            <td class="text-center font-weight-bold"><b>SUB TOTAL S/</b></td>
            <td>
                <?=number_format($sub_total,2)?>
                <input type="hidden" id="sub_total" value="<?=$sub_total?>">
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td class="text-center font-weight-bold"><b>IGV S/</b></td>
            <td>
                <?=number_format($igv,2)?>
                <input type="hidden" id="igv" value="<?=$igv?>">
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td class="text-center font-weight-bold"><b>TOTAL S/</b></td>
            <td>
                <?=number_format($total,2)?>
                <input type="hidden" id="total" value="<?=$total?>">
            </td>
        </tr>
    </tbody>
</table>