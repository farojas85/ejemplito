<?php
session_start();
error_reporting(0);

include('includes/config.php');
include('includes/consultas.php');

if(strlen($_SESSION['alogin'])==0)
	{	
	header('location:index.php');
}
else{
	$consulta = new Consultas();
	$consulta->tabla="tbltipodocumentos";
	$consulta->campos="*";
	$tipoDocumentos =$consulta->obtenerRegistrosTodos($dbh);
	$tipo_contar = $consulta->cantidad_registros;

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>Creación de Cliente Nuevo</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Pooled Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<script
		type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="css/morris.css" type="text/css" />
	<link href="css/font-awesome.css" rel="stylesheet">
	<script src="js/jquery-2.1.4.min.js"></script>
	<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet'
		type='text/css' />
	<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
	<link rel="stylesheet" href="librerias/sweetalert2/sweetalert2.min.css" type='text/css' />
	<style>
		.errorWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #fff;
			border-left: 4px solid #dd3d36;
			-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
		}

		.succWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #fff;
			border-left: 4px solid #5cb85c;
			-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
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
				<li class="breadcrumb-item"><a href="index.html">Inicio</a><i class="fa fa-angle-right"></i>Clientes
				</li>
			</ol>
			<!--grid-->
			<div class="grid-form">
				<!---->
				<div class="grid-form1">
					<h3>Crear Cliente</h3>
					<?php 
						if($error){
					?>
					<div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?>
					</div>
					<?php 
					} 
					else if($msg){?><div class="succWrap"><strong>EXITO</strong>:<?php echo htmlentities($msg); 
					?> 
					</div>
					<?php }?>
					<div class="tab-content">
						<div class="tab-pane active" id="horizontal-form">
							<form class="form-horizontal" name="formCliente" id="formCliente" method="post" 
									enctype="multipart/form-data" >
								<div class="form-group row">
									<label for="focusedinput" class="col-md-2 control-label">Tipo Documento</label>
									<div class="col-md-3">
										<select class="form-control1" name="tipo_documento" id="tipo_documento" required="">
											<option value="">-Seleccionar-</option>
										<?php
											if($tipo_contar == 0)
											{
												echo "<option value='cc'>-CONFIGURAR-</option>";
											} else {
												foreach($tipoDocumentos as $tipo)
												{
													echo "<option value='".$tipo['idtipodoc']."'>".$tipo['nombre_corto']."</option>";
												}
											}
										?>
										</select>
									</div>
									<label for="focusedinput" class="col-md-2 control-label">N&uacute;mero Documento:</label>
									<div class="col-md-3">
										<input type="text" class="form-control1" name="numero_documento" id="numero_documento"
											placeholder="Número Documento Identidad" maxlength="15"  
											onkeypress="return soloNumeros(event)" required="" >
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-md-2 control-label">Nombre del cliente</label>
									<div class="col-md-8">
										<input type="text" class="form-control1" name="nombre" id="nombre"
											placeholder="Nombre del cliente" required>
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Apellido del
										cliente</label>
									<div class="col-sm-8">
										<input type="text" class="form-control1" name="apellido" id="apellido"
											placeholder="Apellido del cliente" required>
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Dirección</label>
									<div class="col-sm-8">
										<input class="form-control" rows="5" cols="50" name="direccion" id="direccion"
											placeholder="Direccion del cliente" required>
									</div>
								</div>
								<div class="form-group">
									<div class="form-group">
										<label for="focusedinput" class="col-sm-2 control-label">Telefono</label>
										<div class="col-sm-8">
											<textarea type="text" class="form-control1" name="telefono" id="telefono"
												placeholder="Telefono del cliente" required></textarea>

										</div>
									</div>
									<div class="row">
										<div class="col-sm-8 col-sm-offset-2">
											<button type="button" name="submit" class="btn-primary btn" id="btn-guardar">Crear</button>

											<button type="button" class="btn-inverse btn">Reiniciar</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!--//grid-->

					<!-- script-for sticky-nav -->
					<script>
						$(document).ready(function () {
							var navoffeset = $(".header-main").offset().top;
							$(window).scroll(function () {
								var scrollpos = $(window).scrollTop();
								if (scrollpos >= navoffeset) {
									$(".header-main").addClass("fixed");
								} else {
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

			$(".sidebar-icon").click(function () {
				if (toggle) {
					$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
					$("#menu span").css({ "position": "absolute" });
				}
				else {
					$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
					setTimeout(function () {
						$("#menu span").css({ "position": "relative" });
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
		<script src="librerias/parsley/parsley.min.js"></script>
		<script src="librerias/i18n/es.js"></script>
		<script src="librerias/sweetalert2/sweetalert2.min.js"></script>
		<!-- /Bootstrap Core JavaScript -->
		<script src="js/modulos/cliente.js"></script>

</body>

</html>
<?php } ?>