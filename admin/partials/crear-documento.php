<?php
session_start();
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:../index.php');

}
else{
    require_once '../librerias/fdpdf/fpdf.php';
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


    $pdf = new FPDF('L','mm','A5'); // Tamaño tickt 80mm x 150 mm (largo aprox)
    //$pdf->SetMargins(30, 25 , 30);

    $pdf->AddPage();

    $pdf->image('../images/logo_2.jpg',3,5,50,30);
    $pdf->SetFont('Helvetica','',12);
    $pdf->Cell(120,4,'Recreo Wado',0,1,'C');
    $pdf->SetFont('Helvetica','',8);
    $pdf->Cell(120,4,'R.U.C.: 103331234567',0,1,'C');
    $pdf->Cell(120,4,'Av. Universitaria Km 3.5',0,1,'C');
    $pdf->Cell(120,4,utf8_decode('Pillco Marca / Huánuco'),0,1,'C');
    $pdf->Cell(120,4,'999 888 777',0,1,'C');
    $pdf->Cell(120,4,'wado@wado.com',0,1,'C');

    $pdf->SetLineWidth(0.75);
    $pdf->SetFillColor(255,255,255); //fondo
    $pdf->RoundedRect(135,10,65,29,2,'DF');
    $pdf->SetXY(135,12);
    $pdf->SetFont('Arial','B',12); // tamaño letra
    $pdf->Cell(65,6,"R.U.C. 103331234567",0,1,'C',0);
    $pdf->SetXY(135,20);
    $pdf->SetFont('Arial','B',10); // tamaño letra
    $pdf->SetFillColor(200,200,200); //fondo
    $pdf->Cell(65,7.5,utf8_decode($tipo_comprobante),1,1,'C',1);
    $pdf->SetXY(135,30);
    $pdf->SetFont('Arial','B',15); // tamaño letra
    $pdf->Cell(65,6, $pedido->serie." - ".$pedido->numero,0,1,'C',0);

    //DATOS DEL CLIENTE
    //Nombres
    $pdf->SetXY(12,44);
    $pdf->SetFont('Arial','B',8); // tamaño letra
    $pdf->Cell(20,5, utf8_decode("Señor(a) : "),0,1,'L',0);

    $pdf->SetXY(28,44);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,5, $pedido->cliente,0,1,'L',0);
    
    //Dirección
    $pdf->SetXY(12,50);
    $pdf->SetFont('Arial','B',8); // tamaño letra
    $pdf->Cell(20,5, utf8_decode("Dirección : "),0,1,'L',0);

    $pdf->SetXY(28,50);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $pdf->Cell(20,5, $pedido->direccion,0,1,'L',0);

    //Documento
    $pdf->SetXY(12,56);
    $pdf->SetFont('Arial','B',8); // tamaño letra
    $pdf->Cell(20,5, utf8_decode($pedido->tipo_documento.": "),0,1,'L',0);
    $pdf->SetXY(28,56);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $pdf->Cell(20,5, $pedido->numero_documento,0,1,'L',0);
    
    //Fecha y Hora
    $pdf->SetXY(134,50);
    $pdf->SetFont('Arial','B',8); // tamaño letra
    $pdf->Cell(36,5, utf8_decode("Fecha de Emisión "),0,1,'L',0);
    $pdf->SetXY(170,50);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $date=date_create($pedido->fechaentrega);
      
    $pdf->Cell(36,5, ": ".date_format($date,"d/m/Y"),0,1,'L',0);

    //Cuadro de Detalle de Factura
    $pdf->SetLineWidth(0.50);
    $pdf->SetDrawColor(0,0,0);
    $pdf->Rect(12,65,186,40,'');

    $pdf->SetLineWidth(0.25);
    $pdf->SetFillColor(220,220,220); //fondo

    $pdf->SetXY(12,65);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(25,8,"CANTIDAD",1,1,'C',1);
    
    $pdf->SetXY(37,65);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(111,8,utf8_decode("DESCRIPCIÓN"),1,1,'C',1);
    
    $pdf->SetXY(148,65);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(25,8,"PREC. UNIT.",1,1,'C',1);
    
    $pdf->SetXY(173,65);
    $pdf->SetFont('Arial','',7); // tamaño letra
    $pdf->Cell(25,8,"IMPORTE",1,1,'C',1);

    
    $pdf->SetDrawColor(0,0,0);
    $pdf->Rect(12,73,25,32,'');
    
    $pdf->SetDrawColor(0,0,0);
    $pdf->Rect(37,73,111,32,'');
    
    $pdf->SetDrawColor(0,0,0);
    $pdf->Rect(148,73,25,32,'');

    $cy=73;
    $i=0;
    foreach($pedido_detalle as $detalle)
    {
        $cantidad = 1;
        $cx=12;

        $concepto =trim($detalle['detalle']);
        $alto_y =7;

        //cantidad
        $pdf->SetXY($cx,$cy);
        $pdf->SetFont('Arial','',10); // tamaño letra
        $pdf->Cell(25,$alto_y, $detalle['cantidad'],0,0,'C',0);

        //Detalle
        $pdf->SetXY($cx+26,$cy);
        $pdf->SetFont('Arial','',10); // tamaño letra	
        $pdf->Cell(109,$alto_y,$concepto,0,0,'L',0);

        //Precio Unitario
        $pdf->SetXY($cx+136,$cy);
        $pdf->SetFont('Arial','',10); // tamaño letra
        $pdf->Cell(25,$alto_y,number_format($detalle['precio'],2),0,0,'C',0);

        //IMPORTE	
        $pdf->SetXY($cx+161,$cy);
        $pdf->SetFont('Arial','',10); // tamaño letra
        $pdf->Cell(25,$alto_y,$detalle['importe'],0,0,'C',0);

        $cy=$cy+$alto_y;
		$i++;
    }

    //SUBTOTALES
    $pdf->SetLineWidth(0.40);
    $pdf->RoundedRect(12,106,130,6,2,"");
    $pdf->SetFont('Arial','B',7); // tamaño letra
    $pdf->SetXY(14,106);
    $pdf->Cell(150,7, "SON: ".strtoupper($convertirLetra->convertir($pedido->total))." SOLES",0,1,'L',0);
            
    $pdf->SetXY(148,105);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $pdf->Cell(25,7, "Sub Total",1,1,'C',1);
    $pdf->SetXY(148,112);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $pdf->Cell(25,7, "I.G.V",1,1,'C',1);
    $pdf->SetXY(148,119);
    $pdf->SetFont('Arial','',8); // tamaño letra
    $pdf->Cell(25,7, "TOTAL",1,1,'C',1);

    $pdf->SetXY(173,105);
    $pdf->Cell(25,7,$pedido->subtotal,1,1,'R',0);
    $pdf->SetXY(173,105);
    $pdf->Cell(5,7, "S/",0,1,'R',0);

    $pdf->SetXY(173,112);
    $pdf->Cell(25,7, "0.00",1,1,'R',0);
    $pdf->SetXY(173,112);
    $pdf->Cell(5,7, "S/",0,1,'R',0);

    $pdf->SetXY(173,119);
    $pdf->Cell(25,7, $pedido->total,1,1,'R',0);
    $pdf->SetXY(173,119);
    $pdf->Cell(5,7, "S/",0,1,'R',0);

    $pdf->SetLineWidth(0.50);
    $pdf->Rect(148,105,50,21,"");
    
    $pdf->SetFont('Arial','',8);
    $pdf->SetXY(20,125);
    $pdf->Cell(170,3,"Representacion impresa del Comprobante de Venta",0,1,'C',0);

    $tempDir = 'imagen_qr.png';

    $codeContents = RUC_EMPRESA." | ".$pedido->numero_documento.' | '.
                    $pedido->serie." | ".$pedido->numero." | ".$pedido->total;

    QRcode::png($codeContents, $tempDir, QR_ECLEVEL_L, 1);

    $pdf->image($tempDir,10,113,35,35);

    $pdf->output();


}