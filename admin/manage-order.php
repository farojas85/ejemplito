<?php
session_start();

include('includes/config.php');
include('includes/consultas.php');

if(strlen($_SESSION['alogin'])==0)
	{	
	header('location:index.php');
}
else{
	$consulta = new Consultas();
    $consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                        "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
    $consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                        "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                        "c.numero_documento,c.direccion";
	$pedidos =$consulta->obtenerRegistrosTodos($dbh);
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
	$consulta->orden = "ORDER BY tp.fechaentrega desc,tp.idpedidos desc";
    $pedidos =$consulta->obtenerRegistrosTodosOrdenPaginacion($dbh);
	$pedidos_contar = $consulta->cantidad_registros;

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>Administrar pedidos</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script
		type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="css/morris.css" type="text/css" />
	<link href="css/font-awesome.css" rel="stylesheet">
	<script src="js/jquery-2.1.4.min.js"></script>
	<link rel="stylesheet" href="librerias/sweetalert2/sweetalert2.min.css" type='text/css' />
	<link rel="stylesheet" type="text/css" href="css/table-style.css" />
	<link rel="stylesheet" type="text/css" href="css/basictable.css" />
	<script type="text/javascript" src="js/jquery.basictable.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#table-pedido').basictable();

			//   $('#table-breakpoint').basictable({
			//     breakpoint: 768
			//   });

			//   $('#table-swap-axis').basictable({
			//     swapAxis: true
			//   });

			//   $('#table-force-off').basictable({
			//     forceResponsive: false
			//   });

			//   $('#table-no-resize').basictable({
			//     noResize: true
			//   });

			//   $('#table-two-axis').basictable();

			//   $('#table-max-height').basictable({
			//     tableWrapper: true
			//   });
		});
	</script>
	<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet'
		type='text/css' />
	<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
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
				<li class="breadcrumb-item"><a href="index.html">Inicio</a><i class="fa fa-angle-right"></i>Administrar
					pedidos</li>
			</ol>
			<div class="agile-grids">
				<!-- tables -->

				<div class="agile-tables">
					<div class="w3l-table-info">
						<h2>
							Administrar pedido
							<button type="button" class="btn bg-primary" id="btn-buscar-plato"
								onclick="elegirTipoReporte()">
								<i class="fa fa-file text-white"></i> Reportes
							</button>
							<button type="button" class="btn bg-warning" id="btn-buscar-plato"
								onclick="refrescarPagina()">
								<i class="fa fa-retweet text-white"></i> Refrescar
							</button>
						</h2>
						<div class="table-responsive" id="pedido-reporte">
							<div class="form-horizontal">
								<div class="form-group row">
									<label for="nombre_cliente" class="col-md-2 control-label hor-form">B&uacute;squeda</label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="busqueda_pedido" name="busqueda_pedido" 
											placeholder="Ingrese Cliente a Buscar" >
									</div>
								</div>
							</div>
							<div id="tabla-pedidos-form">
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
			$item = 0;
			foreach($pedidos as $pedido): 
				$item +=1;
				$date=date_create($pedido['fechaentrega']);
		?>
							<tr>
								<td><?=$item ?></td>
								<td><?=date_format($date,"d/m/Y")?></td>
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
							<?php endforeach; ?>
							</tbody>
						</table>
						
							<ul class="pagination">
								<li><a href="?pagina=1">Primero</a></li>
								
								<?php for($p=1; $p<=$total_paginas; $p++){?>
									
									<li class="<?= $pagina == $p ? 'active' : ''; ?>"><a href="<?= '?pagina='.$p; ?>"><?= $p; ?></a></li>
								<?php }?>
								<li><a href="?pagina=<?= $total_paginas; ?>">&Uacute;ltimo</a></li>
							</ul>
								</div>
						</div>
					</div>
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
		<div class="modal" tabindex="-1" role="dialog" id="tipo-reportes-modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">TIPO REPORTES</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="tipo-reportes-modal-body">
						<input type="hidden" id="idpedidos" name="idpedido">
						<?php include('partials/elige-tipo-reporte.php'); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
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
		<script src="librerias/sweetalert2/sweetalert2.min.js"></script>
		<script src="js/modulos/administrar-pedido.js"></script>
		<!-- /Bootstrap Core JavaScript -->

</body>

</html>
<?php  
}
?>