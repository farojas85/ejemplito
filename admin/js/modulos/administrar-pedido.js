function imprimirHoja(idpedido)
{
    window.open("partials/crear-documento.php?id="+idpedido,'_BLANK');
}

function imprimirTicket(idpedido)
{
    window.open("partials/crear-ticket.php?id="+idpedido,'_BLANK');
}

function eliminarPedido(idpedido)
{
    $.ajax({
        url:'controllers/PedidoController.php',
        type:'POST',
        data:{
            accion:'eliminar-pedido',
            idpedido:idpedido,
        },
        success:function(respuesta){
            Swal.fire({
                icon: 'success',
                title: 'Pedidos',
                text: 'Registro Eliminado Satisfactoriamente',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if(result.isConfirmed)
                {
                    window.location.href="manage-order.php";
                }
            })
        }
    })
}

function elegirTipoReporte()
{
    $('#tipo-reportes-modal').modal('show')
    $('#tipo_reporte').val('00')
    $("#rango-tipo").html('')
}

$('#tipo_reporte').on("change",function(){

    switch($(this).val())
    {
        case '1': filtroDiario();break;
        case '2': filtroMensual();break;
        case '3': filtroAnual();break;
    }
   
})

function filtroDiario()
{
    $.ajax({
        url:'partials/rango-diario-reporte.php',
        type:'GET',
        success:function(respuesta){
            $("#rango-tipo").html(respuesta)
        }
    })
}

function filtroMensual()
{
    $.ajax({
        url:'partials/rango-mensual.php',
        type:'GET',
        success:function(respuesta){
            $("#rango-tipo").html(respuesta)
        }
    })
}

function filtroAnual()
{
    $.ajax({
        url:'partials/rango-anual.php',
        type:'GET',
        success:function(respuesta){
            $("#rango-tipo").html(respuesta)
        }
    })
}

$('#btn-buscar-reporte').on("click",function(){
    tipo_reporte = $('#tipo_reporte').val();
    switch(tipo_reporte)
    {
        case '1': reporteDiario();break;
        case '2': reporteMensual();break;
        case '3': reporteAnual();break;
    }   
})

function reporteDiario()
{
    fecha_inicio=$('#fecha_inicio').val();
    fecha_final=$('#fecha_final').val();
    $.ajax({
        url:'partials/reporte-diario.php',
        type:'GET',
        data:{
            fecha_inicio:fecha_inicio,
            fecha_final: fecha_final
        },
        success:function(respuesta){
            $("#pedido-reporte").html(respuesta)
            $('#tipo-reportes-modal').modal('hide')
        }
    })
}
function reporteMensual()
{
    mes=$('#mes').val();
    $.ajax({
        url:'partials/reporte-mensual.php',
        type:'GET',
        data:{
            mes:mes
        },
        success:function(respuesta){
            $("#pedido-reporte").html(respuesta)
            $('#tipo-reportes-modal').modal('hide')
        }
    })
}
function reporteAnual()
{
    anio=$('#anio').val();
    $.ajax({
        url:'partials/reporte-anual.php',
        type:'GET',
        data:{
            anio:anio
        },
        success:function(respuesta){
            $("#pedido-reporte").html(respuesta)
            $('#tipo-reportes-modal').modal('hide')
        }
    })
}

function refrescarPagina()
{
    window.location.href="manage-order.php";
}