<?php
session_start();
error_reporting(0);
require_once "clases/Conexion.php"; 
$c= new conectar();
$conexion=$c->conexion();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');

}
else{


	?>
<!DOCTYPE HTML>
<html>
<head>
<title>crear orden</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Pooled Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="css/morris.css" type="text/css"/>
<link href="css/font-awesome.css" rel="stylesheet"> 
<script src="js/jquery-2.1.4.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />

<script src="librerias/jquery-3.2.1.min.js"></script>
<script src="librerias/alertifyjs/alertify.js"></script>
<script src="librerias/bootstrap/js/bootstrap.js"></script>
<script src="librerias/select2/js/select2.js"></script>
<script src="js/funciones.js"></script>

  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head> 
<body>
   <div class="page-container">
   <!--/content-inner-->
<div class="left-content">
	   <div class="mother-grid-inner">
              <!--header start here-->
<?php include('includes/header.php');?>
							
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
	<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Inicio</a><i class="fa fa-angle-right"></i>Pedido </li>
            </ol>
		<!--grid-->
 	<div class="grid-form">
 
<!---->
  <div class="grid-form1">
  	       <h3>Crear Pedido</h3>
  	        	  <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>EXITO</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
  	         <div class="tab-content">
						<div class="tab-pane active" id="horizontal-form">
							<form class="form-horizontal" id="frmVentasProductos" enctype="multipart/form-data">
								<div class="form-group">
										<label for="focusedinput" class="col-sm-2 control-label">Nombre completo del cliente</label>
                                                                                <div class="col-sm-8">
					<select class="form-control input-sm" id="clienteVenta" name="clienteVenta">
                                        <option value="A">SELECCIONA EL CLIENTE</option>
			
                                        <?php
                                        $sql="SELECT id_cliente,nombre from clientes";
                                        $result=mysqli_query($conexion,$sql);
                                        while ($cliente=mysqli_fetch_row($result)):
					?>
					<option value="<?php echo $cliente[0] ?>"><?php echo $cliente[1] ?></option>
                                        <?php endwhile; ?>
                                        </select>
								</div>
                                                                </div>

	
<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Detalles del pedido</label>
									<div class="col-sm-8">
                                            
                                        <select class="form-control input-sm" id="productoVenta" name="productoVenta">
                                        <option value="A">SELECCIONA EL PLATO</option>
			
                                        <?php
                                        $sql="SELECT Platoid,Platoname from tblplatos";
                                        $result=mysqli_query($conexion,$sql);
                                        while ($plato=mysqli_fetch_row($result)):
					?>
					<option value="<?php echo $plato[0] ?>"><?php echo $plato[1] ?></option>
                                        <?php endwhile; ?>
                                        </select>
                                       
                                                                            
									</div>
								</div>
<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Fecha de entrega</label>
									<div class="col-sm-8">
                                                                            <input type="date" class="form-control1" name="fecha" id="fecha" placeholder="Ingrese la fecha del pedido" required>
                                                                            
									</div>
								</div>   
<div class="form-group">
                        <label for="focusedinput" class="col-sm-2 control-label">Precio</label>
                        <div class="col-sm-8">
                            <input readonly="" type="text" class="form-control1 input-sm" id="precioV" name="precioV">
			</div>
</div>
<div class="form-group">
    <label for="focusedinput" class="col-sm-2 control-label">Cantidad</label>
    <div class="col-sm-8">
        <input  type="text" class="form-control1 input-sm" id="cantidadV" name="cantidadV">
    </div>
</div>
<div class="form-group">                                                            
                        <div class="col-sm-8">
			<span class="btn btn-primary" id="btnAgregaVenta">Agregar</span>
			<span class="btn btn-danger" id="btnVaciarVentas">Vaciar ventas</span>     
                        </div>
                    </div>
                                                            

	<div class="col-sm-4">
		<div id="tablaVentasTempLoad">hola</div>
	</div>
								<div class="row">
					</div>
					</div>
					
					</form>

     
      

      
      <div class="panel-footer">
		
	 </div>
    </form>
  </div>
 	</div>
 	<!--//grid-->

<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function() {
			 var navoffeset=$(".header-main").offset().top;
			 $(window).scroll(function(){
				var scrollpos=$(window).scrollTop(); 
				if(scrollpos >=navoffeset){
					$(".header-main").addClass("fixed");
				}else{
					$(".header-main").removeClass("fixed");
				}
			 });
			 
		});
		</script>
                
		<!-- /script-for sticky-nav -->
<!--inner block start here-->
<div class="inner-block">

</div>
<!--inner block end here-->
<!--copy rights start here-->
<?php include('includes/footer.php');?>
<!--COPY rights end here-->
</div>
</div>
  <!--//content-inner-->
		<!--/sidebar-menu-->
					<?php include('includes/sidebarmenu.php');?>
							  <div class="clearfix"></div>		
							</div>
							<script>
							var toggle = true;
										
							$(".sidebar-icon").click(function() {                
							  if (toggle)
							  {
								$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
								$("#menu span").css({"position":"absolute"});
							  }
							  else
							  {
								$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
								setTimeout(function() {
								  $("#menu span").css({"position":"relative"});
								}, 400);
							  }
											
											toggle = !toggle;
										});
							</script>
<!--js -->
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
   <!-- /Bootstrap Core JavaScript -->	   $.ajax({
				type:"POST",
				data:"Platoid=" + $('#productoVenta').val(),
				url:"procesos/ventas/llenarFormProducto.php",
				success:function(r){
					//dato=jQuery.parseJSON(r);
                                        console.log(r);
                                        $('#precioV').val(dato['precio']);

				}
			});

</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){

		$('#tablaVentasTempLoad').load("procesos/ventas/tablaVentasTemp.php");

		$('#productoVenta').change(function(){
			$.ajax({
				type:"POST",
				data:"Platoid=" + $('#productoVenta').val(),
				url:"procesos/ventas/llenarFormProducto.php",
				success:function(r){
					dato=jQuery.parseJSON(r);
                                       $('#precioV').val(parseFloat(dato['precio']).toFixed(2));

				}
			});
		});
                $('#btnAgregaVenta').click(function(){
			vacios=validarFormVacio('frmVentasProductos');

			if(vacios > 0){
				alertify.alert("Debes llenar todos los campos!!");
				return false;
			}

			datos=$('#frmVentasProductos').serialize();
			$.ajax({
				type:"POST",
				data:datos,
				url:"procesos/ventas/agregaProductoTemp.php",
				success:function(r){
					$('#tablaVentasTempLoad').load("procesos/ventas/tablaVentasTemp.php");
				}
			});
		});

		$('#btnVaciarVentas').click(function(){

		$.ajax({
			url:"../procesos/ventas/vaciarTemp.php",
			success:function(r){
				$('#tablaVentasTempLoad').load("procesos/ventas/tablaVentasTemp.php");
			}
		});
	});

	});

	function quitarP(index){
		$.ajax({
			type:"POST",
			data:"ind=" + index,
			url:"procesos/ventas/quitarproducto.php",
			success:function(r){
				$('#tablaVentasTempLoad').load("procesos/ventas/tablaVentasTemp.php");
				alertify.success("Se quito el producto :D");
			}
		});
	}

	function crearVenta(){
		$.ajax({
			url:"procesos/ventas/crearVenta.php",
			success:function(r){
				if(r > 0){
					$('#tablaVentasTempLoad').load("procesos/ventas/tablaVentasTemp.php");
					$('#frmVentasProductos')[0].reset();
					alertify.alert("Venta creada con exito, consulte la informacion de esta en ventas hechas :D");
				}else if(r==0){
					alertify.alert("No hay lista de venta!!");
				}else{
					alertify.error("No se pudo crear la venta");
				}
			}
		});
	}
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#clienteVenta').select2();
		$('#productoVenta').select2();

	});
</script>
                
<?php } ?>