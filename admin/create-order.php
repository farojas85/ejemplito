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
	<link rel="stylesheet" type="text/css" href="css/table-style.css" />
	<link rel="stylesheet" type="text/css" href="css/basictable.css" />
	<script type="text/javascript" src="js/jquery.basictable.min.js"></script>
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
		<div class="left-content">
			<div class="mother-grid-inner">
				<?php include('includes/header.php');?>
				<div class="clearfix"> </div>
			</div>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Inicio</a><i class="fa fa-angle-right"></i>Pedido </li>
			</ol>
			<div class="grid-form">
				<div class="grid-form1">
					<h3 id="forms-horizontal">CREAR VENTA</h3>
					<form class="form-horizontal" id="form-pedido">
						<div class="form-group">
							<label for="fecha_pedido" class="col-md-2 control-label hor-form">FECHA</label>
							<div class="col-md-3">
							<input type="date" class="form-control" id="fecha_pedido" placeholder="FechaPedido" value="<?=date('Y-m-d')?>" required="">
							</div>
						</div>
						<div class="form-group">
							<label for="nombre_cliente" class="col-md-2 control-label hor-form">CLIENTE</label>
							<div class="col-md-10">
								<input type="hidden" name="id_cliente" id="id_cliente" value="">
								<input type="hidden" name="tipodoc_id" id="tipodoc_id" value="">
								<div class="input-group">
									<span class="input-group-addon" style="cursor:pointer" id="btn-buscar-cliente">
										<i class="fa fa-search"></i>
									</span>
									<input type="text" class="form-control" id="nombre_cliente" placeholder="Nombre Cliente" required="" >
									<span class="input-group-addon" style="cursor:pointer">
										<i class="fa fa-plus"></i>
									</span>
								</div>
							</div>
							<span style="display:none" id="datos_clientes">
								<label id="documento" class="col-md-2 control-label hor-form">D.N.I</label>
								<div class="col-md-2">
									<input type="text" class="form-control" id="numero_documento" placeholder="Número Documento Identidad" readonly="">
								</div>
								<label class="col-md-1 control-label hor-form">DIRECCI&Oacute;N</label>
								<div class="col-md-7">
									<input type="text" class="form-control" id="direccion" placeholder="Dirección Cliente">
								</div>
							</span>							
						</div>
						<div class="form-group row">
							<button type="button" class="btn bg-primary" id="btn-buscar-plato">
								<i class="fa fa-plus"></i> A&ntilde;adir Plato
							</button>							
							<div class="table-responsive" id="tabla-detalle">
								<table >
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
										$item = 0;
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
											$item +=1;
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
							</div>
						</div>
						<div class="form-group row">
							<button type="button" class="btn bg-success" id="btn-guardar-pedido">
								<i class="fa fa-save"></i> Guardar
							</button>	
						</div>
					</form>					
				</div>
			</div>
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
			<div class="inner-block"></div>
			<?php include('includes/footer.php');?>	
		</div>
		<?php include('includes/sidebarmenu.php');?>
		<div class="clearfix"></div>
		<div class="modal" tabindex="-1" role="dialog" id="cliente-modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Buscar Cliente</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="cliente-modal-body">
						<?php include('partials/buscar-cliente.php'); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="plato-modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Buscar Plato</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="plato-modal-body">
						<?php include('partials/buscar-plato.php'); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="comprobantes-modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">IMPRIMIR COMPROBANTE</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="comprobantes-modal-body">
						<input type="hidden" id="idpedidos" name="idpedido">
						<?php include('partials/elige-comprobante.php'); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			var toggle = true;

			$(".sidebar-icon").click(function (){
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
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="librerias/parsley/parsley.min.js"></script>
		<script src="librerias/i18n/es.js"></script>
		<script src="librerias/sweetalert2/sweetalert2.min.js"></script>
		<script src="js/modulos/pedidos.js"></script>
		<!-- <script src="js/funciones.js"></script> -->
			
		
</body>

</html>

<?php } ?>