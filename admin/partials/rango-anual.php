<?php
    include('../includes/config.php');
    include('../includes/consultas.php');
    
    $consulta = new Consultas();
    $consulta->tabla="tblpedidos";
    $consulta->campos="DISTINCT(extract(year from fechaentrega)) as anio";
    $consulta->orden = "ORDER BY anio desc";
	$anios =$consulta->obtenerRegistrosTodosOrden($dbh);
	$anios_contar = $consulta->cantidad_registros;
?>
<div class="form-horizontal">
    <div class="form-group row">
        <label for="nombre_cliente" class="col-md-3 control-label hor-form">A&Ntilde;O</label>
        <div class="col-md-4">
            <select class="form-control1" name="anio" id="anio" required="">
                <option value="">-Seleccionar-</option>
<?php
    if($anios_contar == 0)
    {
?>
                <option value='-1'>VENTAS NO REGISTRADAS</option>
<?php
    } else {
        foreach($anios as $anio)
        {
?>
                <option value="<?=$anio['anio']?>"><?=$anio['anio']?></option>
<?php
        }
    }
?>
            </select>
        </div>
    </div>
</div>