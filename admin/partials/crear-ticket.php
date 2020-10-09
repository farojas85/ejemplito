<?php
session_start();
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:../index.php');

}
else{

    // CONFIGURACIÓN PREVIA
    require('../librerias/fdpdf/fpdf.php');
    include '../includes/config.php';
    include '../includes/consultas.php';
    include '../includes/numero_letras.class.php';
    include '../librerias/phpqrcode/qrlib.php';
    define("PEN",'S/');
    define("RUC_EMPRESA",'103331234567');
    //CONSULTAS
    $convertirLetra=new numerosALetras;
    //PEDIDO CABECERA
    $consulta = new Consultas();
    $consulta->tabla="tblpedidos tp inner join clientes c on tp.id_cliente = c.id_cliente ".
                        "inner join tbltipodocumentos td on c.tipodoc_id = td.idtipodoc";
    $consulta->campos="tp.idpedidos,concat(c.nombre,' ',c.apellido) as cliente,serie,".
                        "numero,fechaentrega,subtotal,igv,total,td.nombre_corto as tipo_documento,".
                        "c.numero_documento,c.direccion";
    $consulta->condicion = " tp.idpedidos=".$_GET['id'];

    $pedido =$consulta->obtenerUnoRegistroCondicion($dbh);
    $pedido_contar = $consulta->cantidad_registros;

    $tipo_comprobante = ($pedido->serie == 'F001') ? 'FACTURA DE VENTA' : 'BOLETA DE VENTA';

    //PEDIDO DETALLES
    $consulta->tabla="tblpedidos_detalle tpd";
    $consulta->campos="tpd.idpedidodet,tpd.idpedidos,tpd.cantidad,tpd.detalle,".
                        "tpd.precio,tpd.importe";
    $consulta->condicion = " tpd.idpedidos=".$_GET['id'];

    $pedido_detalle =$consulta->obtenerRegistrosCondicion($dbh);
    $pedido_detalle_contar = $consulta->cantidad_registros;


    $pdf = new FPDF('P','mm',array(80,200)); // Tamaño tickt 80mm x 150 mm (largo aprox)

    $pdf->AddPage();

    $pdf->image('../images/logo_black.png',16,2,40,20);
    $pdf->SetFont('Helvetica','',12);
    $pdf->SetFont('Helvetica','',8);

    $pdf->SetXY(10,22);
    $pdf->Cell(60,4,'R.U.C.: '.RUC_EMPRESA,0,1,'C');
    $pdf->Cell(60,4,'Av. Universitaria Km 3.5',0,1,'C');
    $pdf->Cell(60,4,utf8_decode('Pillco Marca / Huánuco'),0,1,'C');
    $pdf->Cell(60,4,'999 888 777',0,1,'C');
    $pdf->SetXY(10,38);
    $pdf->Cell(60,4,'wado@wado.com',0,1,'C');

    //CABECERA TICKET
    $pdf->SetFont('Arial','B',8); // tamaño letra
    $pdf->SetFillColor(200,200,200); //fondo
    $pdf->Cell(65,7.5,utf8_decode($tipo_comprobante),0,1,'C',1);
    $pdf->SetFont('Arial','B',12); // tamaño letra
    $pdf->Cell(65,6, $pedido->serie." - ".$pedido->numero,0,1,'C',0);
    //DATOS CLIENTES
    $pdf->SetFont('Arial','B',7); // tamaño letra
    $pdf->SetXY(10,55);
    $pdf->Cell(36,4, utf8_decode("Fecha de Emisión "),0,1,'L',0);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->SetXY(32,55);
    $date=date_create($pedido->fechaentrega);
    $pdf->Cell(36,4, ": ".date_format($date,"d/m/Y"),0,1,'L',0);
    $pdf->SetFont('Arial','B',7); // tamaño letra
    $pdf->SetXY(10,59);
    $pdf->Cell(20,4, utf8_decode("Señor(a) : "),0,1,'L',0);
    $pdf->SetXY(22,59);
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(20,4, $pedido->cliente,0,1,'L',0);
    $pdf->SetXY(10,63);
    $pdf->SetFont('Arial','B',7); // tamaño letra
    $pdf->Cell(20,4, utf8_decode($pedido->tipo_documento.": "),0,1,'L',0);
    $pdf->SetXY(23,63);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(20,4, $pedido->numero_documento,0,1,'L',0);
    $pdf->SetXY(10,67);
    $pdf->SetFont('Arial','B',7); // tamaño letra
    $pdf->Cell(20,4, utf8_decode("Dirección : "),0,1,'L',0);
    $pdf->SetXY(23,67);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(20,4, $pedido->direccion,0,1,'L',0);

    $pdf->Cell(30, 10, 'Plato', 0);
    $pdf->Cell(5, 10, 'Ca.',0,0,'R');
    $pdf->Cell(10, 10, 'Precio',0,0,'R');
    $pdf->Cell(15, 10, 'Total',0,0,'R');
    $pdf->Ln(8);
    $pdf->Cell(60,0,'','T');
    $pdf->Ln(0);

    foreach($pedido_detalle as $detalle)
    {
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->MultiCell(28,4,$detalle['detalle'],0,'L'); 
        $pdf->Cell(35, -5, $detalle['cantidad'],0,0,'R');
        $pdf->Cell(10, -5, number_format($detalle['precio'], 2, '.', ' '),0,0,'R');
        $pdf->Cell(15, -5, PEN." ".number_format($detalle['importe'], 2, '.', ' '),0,0,'R');
        $pdf->Ln(2);       
    }
    $pdf->Ln(6);
    $pdf->Cell(60,0,'','T');
    $pdf->Ln(2);

    $pdf->Cell(25, 10, 'SUB TOTAL', 0);    
    $pdf->Cell(20, 10, '', 0);
    $pdf->Cell(15, 10, PEN." ".number_format($pedido->subtotal, 2, '.', ' '),0,0,'R');
    $pdf->Ln(3);    
    $pdf->Cell(25, 10, 'I.G.V 18%', 0);    
    $pdf->Cell(20, 10, '', 0);
    $pdf->Cell(15, 10, PEN." ".number_format($pedido->igv, 2, '.', ' '),0,0,'R');
    $pdf->Ln(3);    
    $pdf->Cell(25, 10, 'TOTAL', 0);    
    $pdf->Cell(20, 10, '', 0);
    $pdf->Cell(15, 10, PEN." ".number_format($pedido->total, 2, '.', ' '),0,0,'R');
    $pdf->Ln(10);
    $pdf->MultiCell(63,4,"SON: ".strtoupper($convertirLetra->convertir($pedido->total))." SOLES",1,'L');
    $pdf->Ln(5);
    $pdf->Cell(63,4,"Representacion impresa del Comprobante de Venta",0,1,'C',0);

    $tempDir = 'imagen_qr.png';

    $codeContents = RUC_EMPRESA." | ".$pedido->numero_documento.' | '.
                    $pedido->serie." | ".$pedido->numero." | ".$pedido->total;

    QRcode::png($codeContents, $tempDir, QR_ECLEVEL_L, 1);

    $pdf->image($tempDir,20,null,40,40);
    
    //QRcode::png('PHP QR Code :)');
    $pdf->output();
}