function soloNumeros(evt){
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
}

//Solo permite introducir numeros.
  
$("#numero_documento").keyup(function(){

    let tipo_documento = $('#tipo_documento').val();
    let numero_documento = $(this).val();

    if($(this).val().length >=8)
    {
        $('#nombre').val("");
        $('#apellido').val("");

        $.ajax({
            url:'controllers/ClienteController.php',
            type:'GET',
            dataType: "json",
            data:{
                accion:'valida-dni',
                numero_documento:numero_documento,
                tipo_documento : tipo_documento
            },
            success:function(respuesta){
                if(respuesta.codVerifica != 'NE')
                {
                    if(tipo_documento == 1)
                    {
                        $('#nombre').val(respuesta.nombres)
                        $('#apellido').val(respuesta.apellidoPaterno+' '+respuesta.apellidoMaterno)
                    } else if(tipo_documento == 3)
                    {
                        $('#nombre').val(respuesta.razonSocial)
                        $('#apellido').val("")
                    } else {
                        $('#nombre').val("");
                        $('#apellido').val(""); 
                    }
                } else {
                    $('#nombre').val("");
                    $('#apellido').val(""); 
                }
            }
        })
    }
});

$("#btn-guardar").on("click",function(){
    let tipo_documento = $('#tipo_documento').val();
    let numero_documento = $('#numero_documento').val();
    let nombre = $('#nombre').val();
    let apellido = $('#apellido').val();
    let telefono = $('#telefono').val();
    let direccion = $('#direccion').val();

    $('#formCliente').parsley().validate();

    if($('#formCliente').parsley().isValid()) {

        $.ajax({
            url:'controllers/ClienteController.php',
            type:'POST',
            data:{
                accion:'registrar-cliente',
                tipo_documento : tipo_documento,
                numero_documento:numero_documento,
                nombre : nombre,
                apellido: apellido,
                telefono: telefono,
                direccion: direccion
            },
            success:function(respuesta){
                if(respuesta >=1)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Clientes',
                        text: 'Cliente Registrado Satisfactoriamente',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if(result.isConfirmed)
                        {
                            window.location.href="create-client.php";
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Clientes',
                        text: respuesta,
                        confirmButtonText:'Aceptar'
                    })
                }
            }
        })
    }
})
$(".btn-inverse").on("click",function(){
    window.location.href="create-client.php";
})