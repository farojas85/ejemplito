$(document).ready(function() {
    $('#table-cliente').basictable();
});

$('#btn-buscar-cliente').on('click',function(){
    limpiar()
    $("#cliente-modal").modal('show')
})
$('#buscar_cliente').on("change",function(){
    //buscar_cliente = $(this).val()
    $.ajax({
        url:'partials/tabla-cliente.php',
        type:'GET',
        data:{
            accion:'buscar-cliente',
            busqueda: $(this).val()
        },
        success:function(respuesta){
            $("#tabla-cliente-form").html(respuesta)
        }
    })
})

function limpiar()
{
    $('#buscar_cliente').val('')
    $("#tabla-cliente-form").html('')
}
function seleccionarCliente(id_cliente)
{
    $.ajax({
        url:'controllers/ClienteController.php',
        type:'GET',
        dataType: "json",
        data:{
            accion:'seleccionar-cliente',
            id_cliente: id_cliente
        },
        success:function(respuesta){
            let cliente = respuesta
            if(cliente)
            {
                $('#tipodoc_id').val(cliente.tipodoc_id)
                $("#id_cliente").val(cliente.id_cliente)
                $("#nombre_cliente").val(cliente.nombre+' '+cliente.apellido)
                $('#documento').html(cliente.documento)
                $('#numero_documento').val(cliente.numero_documento)
                $('#direccion').val(cliente.direccion)
                $('#cliente-modal').modal('hide')
                $('#datos_clientes').css('display','block')
            }
        }
    })
}

$('#btn-buscar-plato').on('click',function(){
    $('#buscar_plato').val('')
    $("#tabla-plato-form").html('')
    $("#plato-modal").modal('show')
})

$('#buscar_plato').on("change",function(){
    //buscar_cliente = $(this).val()
    $.ajax({
        url:'partials/tabla-plato.php',
        type:'GET',
        data:{
            accion:'buscar-plato',
            busqueda: $(this).val()
        },
        success:function(respuesta){
            $("#tabla-plato-form").html(respuesta)
        }
    })
})

function seleccionarPlato(Platoid,index)
{
    cantidad = $('#cantidad_'+index).val();
    $.ajax({
        url:'controllers/PedidoController.php',
        type:'GET',
        data:{
            accion:'anadir-carrito',
            Platoid: Platoid,
            cantidad:cantidad
        },
        success:function(respuesta){
            if(respuesta=='V'){
                Swal.fire( 'Carrito Pedido','¡ Ya está seleccionado el Plato !','warning')
            } 
            $.ajax({
                url:'partials/pedidos-detalle.php',
                type:'GET',
                success:function(respuesta){
                    $('#tabla-detalle').html(respuesta)
                }
            })
        }
    })
}

function eliminarItem(item)
{
    $.ajax({
        url:'controllers/PedidoController.php',
        type:'GET',
        data:{
            accion:'eliminar-item',
            item:item
        },
        success:function(respuesta){
            if(respuesta==1)
            {
                Swal.fire( 'Carrito Pedido','¡ Item Eliminado Satisfactoriamente !','success')
                $.ajax({
                    url:'partials/pedidos-detalle.php',
                    type:'GET',
                    success:function(respuesta){
                        $('#tabla-detalle').html(respuesta)
                    }
                })
            }
        }
    })
}

$('#btn-guardar-pedido').on('click',function(){
    let cliente = $('#id_cliente').val();
    let fecha = $('#fecha_pedido').val();
    let tipodoc =$('#tipodoc_id').val();
    let sub_total = $('#sub_total').val();
    let igv = $('#igv').val();
    let total = $('#total').val();
    $('#form-pedido').parsley().validate();
    
    if($('#form-pedido').parsley().isValid()) {
        $.ajax({
            url:'controllers/PedidoController.php',
            type:'POST',
            data:{
                accion:'guardar-pedido',
                cliente_id : cliente,
                tipodoc: tipodoc,
                fecha: fecha,
                sub_total:sub_total,
                igv:igv,
                total:total,
            },
            success:function(respuesta){
                console.log(respuesta)
                let idpedido = respuesta;
                $('#idpedidos').val(idpedido)
                if(respuesta >=1)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pedidos',
                        text: 'Pedido Registrado Satisfactoriamente',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if(result.isConfirmed)
                        {
                            $('#comprobantes-modal').modal('show')
                            //window.location.href="create-order.php";
                        }
                    })
                }
            }
        })
    }
})

function limpiarCarrito()
{
    $.ajax({
        url:'controllers/PedidoController.php',
        type:'GET',
        data:{
            accion:'limpiar-carrito',
        },
        success:function(respuesta){
            window.location.href="create-order.php";
        }
    })
}

function imprimirHoja()
{
    let idpedido  = $('#idpedidos').val()
    $('#comprobantes-modal').modal('hide')
    window.open("partials/crear-documento.php?id="+idpedido,'_BLANK');
    limpiarCarrito()
}

function imprimirTicket()
{
    let idpedido  = $('#idpedidos').val()
    $('#comprobantes-modal').modal('hide')
    window.open("partials/crear-ticket.php?id="+idpedido,'_BLANK');
    limpiarCarrito()
}