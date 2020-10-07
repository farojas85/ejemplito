<div class="form-horizontal">
    <div class="form-group row">
        <label for="nombre_cliente" class="col-md-3 control-label hor-form">Tipo Reporte</label>
        <div class="col-md-5">
            <select class="form-control1" name="tipo_reporte" id="tipo_reporte" required="">
                <option value="00">-Seleccionar-</option>
                <option value="1">DIARIO</option>
                <option value="2">MENSUAL</option>
                <option value="3">ANUAL</option>
            </select>
        </div>
    </div>
    <div class="form-group row" id="rango-tipo">

    </div>
    <div class="form-group row text-center" id="rango-tipo">
        <button type="button" class="btn bg-success" id="btn-buscar-reporte"
            onclick="">
            <i class="fa fa-search text-white"></i> Buscar
        </button>
    </div>
</div>