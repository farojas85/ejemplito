<?php
//c0d3.ph03n1x@gmail.com
/*
VARIABLE $_POST['item'] formato a enviar desde un js:

item="{{340,Matricula 2017};{440,Matricula 2016};{340,Matricula 2015};{340,Matricula 2014};{340,Matricula 2013}}";

Todo la clase se puede reformular comenzando en el construct con el nro de RUC 
Tener en cuenta en la implementacion de la clase FacturaElectronica.php

*/
//error_reporting(E_ALL);
//ini_set('display_errors','1');

include("../../clases/Utilitario.php");
include('../model/numero_letras.class.php');
require('../model/Pago.php');
include('../../pdfs/fpdf.php');
require("../../smbclient/smbclient.php");
require('xmlseclibs/xmlseclibs.php');
require('../../m_tarea_programada/phpmailer/class.phpmailer.php');
require('../../m_tarea_programada/phpmailer/PHPMailerAutoload.php');
include('../../m_api/conexion_bd.php');

//use RobRichards\XMLSecLibs\XMLSecurityDSig;
//use RobRichards\XMLSecLibs\XMLSecurityKey;

class BoletaElectronica
{
	public $conexion,$utili,$correo;
	public $parametros_faltantes;
	public $modulo="matricula";
	var $api_service;			var $conectar_bd;
	var $num_documento;			var $std_sunat;				var $fecha_emision;
	var $digest_value;

	function __construct()
	{
		include("../../conexion/ConeEdusysNet.php");
		$this->conexion=$ConeEdusysNet;
		$this->utili = new utilitario();
		$this->correo = new PHPMailer();

	}

	private function completar_parametros($parametro)
	{
		foreach($parametro as $key => $value){
			if($key=="item"){
				
			}else{
				if($value==""){
					
					}
			}
		}
		
	}

	function get_pago_reciente($codalu)
	{
		//aqui se requiere obtener el 
		$fecha_pago=date("Y-m-d");
		$this->utili->campos = "*"; //secuencia,canalu,usecod
		$this->utili->tabla = "control_pagos1_vn cp1
							   inner join control_pagos2_vn cp2 
							   on cp1.secuencia=cp2.secxcp1 ";
		$this->utili->condicion = "where cod_alu_prov_empl='".$codalu."' and fecha='".$fecha_pago."' and serie_documento='SSS' and num_documento='NNN' and estado='CA'";
		//or estado=PE - en el caso de que el padre haya pagado mas, y quiere que dentre a cuenta de algunas de las pensiones.  
		$rs_control_pago_vn = $this->utili->get_datos_condicion($this->conexion);
		//print_r($rs_control_pago1_vn);
		return $rs_control_pago_vn;
	}

	function get_pago_pasado()
	{
		return "Por implementar";
	}
	
	function get_concepto_pago($secuencia)
	{
		$this->utili->campos = "*"; //secuencia,canalu,usecod
		$this->utili->tabla = "master_pagos_vn mp inner join cronograma_pagos cp on mp.codcropag=cp.secitm";
		$this->utili->condicion = "where idmaspag='".$secuencia."' 
			and cp.anomat=(select anomat from master_pagos_vn where idmaspag='".$secuencia."')";
		$rs_concepto_pago = $this->utili->get_datos_condicion($this->conexion);
		//print_r($rs_control_pago1_vn);
		$rs_concepto_pago->Close();
		if (strpos($rs_concepto_pago->Fields('descropag'), trim($rs_concepto_pago->Fields('anomat')))==false) {
			$des_concepto=trim($rs_concepto_pago->Fields('descropag'))." ".trim($rs_concepto_pago->Fields('anomat'));
		}else{
			 $des_concepto=trim($rs_concepto_pago->Fields('descropag'));
		 }
		return $des_concepto;
	}
	
	function prueba($parametro){
		print_r($parametro['item']);
	}
	private function verificacion_parametros($parametro)
	{
		$parametros_faltantes=array();
		$estado_parametros=1;
		$verificacion_indice = array(0 => 'monto_total',
									 1 =>'serie_numero_boleta',
									 2 =>'ruc',
									 3 =>'nombre_parte_firmante',
									 4 => 'nombre_responsable_economico',
									 5 => 'dni_responsable_economico',
									 6 => 'nombre_razon_social',
									 7 => 'streetname',
									 8 => 'subdivision_name',
									 9 => 'direccion_cliente',
									 10 =>'item');
		foreach ($verificacion_indice as $key => $value) {
			if (!array_key_exists($value,$parametro)) {
				$estado_parametros=0;
			}else{
				if ($parametro[$value]=="" or is_null($parametro[$value])) {
					if ($value=="direccion_cliente") {
						if ($parametro["monto_total"]>=700) {
							$estado_parametros=0;
							array_push($parametros_faltantes, $value);
		
						}else{
							$estado_parametros=1;		
						}
					}else{
							$estado_parametros=0;
							array_push($parametros_faltantes, $value);
					}
				}
			}
		}
		session_start();
		$_SESSION['parametros_faltantes']=$parametros_faltantes;
		return $estado_parametros;
	}

	public function adecuacion_item_js($item_js)
	{
		$tam=strlen($item_js);
		$item=substr($item_js,1,$tam-2);
		$item=explode(';',$item);
		$parametro_m=array();
		for($i=0;$i<sizeof($item);$i++){

			$parametro_m[$i]=array();
			$item_detalle=explode(',',$item[$i]);
			$parametro_m[$i]['monto']=substr($item_detalle[0],1);
			$tam=strlen($item_detalle[1]);
			$parametro_m[$i]['concepto']=substr($item_detalle[1],0,$tam-1);
		}

		return $parametro_m;

	}

	public function actualizar_informacion_boleta($serie_numero,$usua,$secuencia,$codrazsoc)
	{
		$pago=new Pago();
		$sn=explode("-", $serie_numero);
		$pago->serie_documento=$sn[0];
		$pago->num_documento=$sn[1];
		$pago->idusua=$usua;
		$pago->secuencia=$secuencia;
		$pago->tipo_documento='07';
		$pago->codrazsoc=$codrazsoc;
		$pago->actualizar_boleta($this->conexion);

	}


	public function update_tbl_boletaf1($serie_numero,$dni_cliente)
	{
		$pago=new Pago();
		$sn=explode("-", $serie_numero);
		$pago->serie_documento=$sn[0];
		$pago->num_documento=$sn[1];
		$pago->idusua=$dni_cliente;
		$pago->update_tbl_boletaf1($this->conexion);

	}

	public function actualizar_firma_rpta_boleta($secuencia,$digestValue,$codrpta,$descodrpta)
	{
		$pago=new Pago();
		$pago->flag=$digestValue;
		$pago->ruta=$codrpta;
		$pago->desconf=$descodrpta;
		$pago->secuencia=$secuencia;
		$pago->actualizar_firma_codrpta_boleta($this->conexion);
	}

	public function get_serie_numero_boleta($cod_raz_social)
	{
		//09: CALICANTO , 10: COMAFE
		//Momentaneo
		$tbl_raz_social=array("09"=>"f1","10"=>"f2");
		
		$numeros=array();
		if ($cod_raz_social=='10') {
			$this->utili->campos = "num_documento";
			$this->utili->tabla = "control_pagos1_vn";
			$this->utili->condicion = "where serie_documento<>'PAG' and num_documento<>'BANC' and num_documento<>'NNN' and tipo_documento = '07' and cod_raz_social='".$cod_raz_social."'"; 
			$rs_boletas = $this->utili->get_datos_condicion($this->conexion);
			while (!$rs_boletas->EOF) {
				$numeros[]=(int)trim($rs_boletas->Fields('num_documento'));
				$rs_boletas->MoveNext();	
			}
			$next_boleta=max($numeros)+1;
		}else{
			$this->utili->campos = "max(boleta_numero)";
			$this->utili->tabla = "tbl_boleta".$tbl_raz_social[$cod_raz_social];
			$this->utili->condicion = "where boleta_numero<>0";
			$rs_tbl_boleta = $this->utili->get_datos_condicion($this->conexion);
			$next_boleta=(int)$rs_tbl_boleta->Fields(0)+1;
		}

		//echo "-- ".$rs_boletas->RecordCount();
		return "B001-".$next_boleta;

	}

	public function get_direccion_cliente($dni)
	{
		$this->utili->campos = "dirapo";
		$this->utili->tabla = "cuadro_familiar";
		$this->utili->condicion = "where dniapo='".$dni."' and dirapo<>''";
		$rs_cuadro_familiar = $this->utili->get_datos_condicion($this->conexion);
		if ($rs_cuadro_familiar->RecordCount()>0) {
			return trim($rs_cuadro_familiar->Fields('dirapo'));
		}else{
			return "";
		}
		
	}

	public function get_razon_social($cod_local)
	{

		switch ($cod_local) {
			case '01': //Constitucion
				$this->utili->campos = "*";
				$this->utili->tabla = "razon_social";
				$this->utili->condicion = "where codrazsoc='09'";
				$rs_razon_social = $this->utili->get_datos_condicion($this->conexion);
				break;
			case '02': //Crespo Castilla
				$this->utili->campos = "*";
				$this->utili->tabla = "razon_social";
				$this->utili->condicion = "where codrazsoc='10'";
				$rs_razon_social = $this->utili->get_datos_condicion($this->conexion);
				break;
		}

		return $rs_razon_social;


	}
	
	public function get_info_alumno($codalu)
	{
		$this->utili->campos = "apepat||' '||apemat||' '||nomalu as nombre";
		$this->utili->tabla = "alumno";
		$this->utili->condicion = "where codalu='".$codalu."'";
		$rs_alumno = $this->utili->get_datos_condicion($this->conexion);

		return $rs_alumno->Fields('nombre');
		
	}
	public function generar_xml_boleta($parametro)
	{
		$indice_item=0;
		$convertirLetra=new numerosALetras;
		if ($this->verificacion_parametros($parametro)){
			$nro_items=sizeof($parametro['item']);
			//$parametro=$this->getParametro($parametro);
			
			$xml  = new DOMDocument('1.0', 'ISO-8859-1');
			$xml->xmlStandalone=false;
			$xml->formatOutput = true;
			$raiz = $xml->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', 'Invoice');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:cac','urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:cbc','urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ccts','urn:un:unece:uncefact:documentation:2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ds','http://www.w3.org/2000/09/xmldsig#');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ext','urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:qdt','urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:sac','urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:udt','urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
			$xml->appendChild($raiz);
			
			$nivel1=$xml->createElement('ext:UBLExtensions');
			$raiz->appendChild($nivel1);
			
			$nivel2=$xml->createElement('ext:UBLExtension');
			$nivel1->appendChild($nivel2);
			
			$nivel3=$xml->createElement('ext:ExtensionContent');
			$nivel2->appendChild($nivel3);
				
			$nivel4=$xml->createElement('sac:AdditionalInformation');
			$nivel3->appendChild($nivel4);
			
			$nivel5=$xml->createElement('sac:AdditionalMonetaryTotal');
			$nivel4->appendChild($nivel5);
			
			$nivel6=$xml->createElement('cbc:ID','1003');
			$nivel5->appendChild($nivel6);
			//AQUI ES LA SUMA DE TODOS LOS MONTOS SEGUN TIPO -> OPERACIONES GRAVADAS, INAFECTAS, -.EXONERADAS-.
			$nivel6=$xml->createElement('cbc:PayableAmount',$parametro['monto_total']);
			$nivel6->setAttributeNS('','currencyID','PEN');
			$nivel5->appendChild($nivel6);
			
			$nivel5=$xml->createElement('sac:AdditionalProperty');
			$nivel4->appendChild($nivel5);
			
			$nivel6=$xml->createElement('cbc:ID','1000');
			$nivel5->appendChild($nivel6);
			$nivel6=$xml->createElement('cbc:Value',$convertirLetra->convertir($parametro['monto_total']));
			$nivel5->appendChild($nivel6);
			
			
			$nivel2=$xml->createElement('ext:UBLExtension');
			$nivel1->appendChild($nivel2);
			
			$nivel3=$xml->createElement('ext:ExtensionContent');
			$nivel2->appendChild($nivel3);

			$nivel1=$xml->createElement('cbc:UBLVersionID','2.0');
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cbc:CustomizationID','1.0');
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cbc:ID',$parametro['serie_numero_boleta']);
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cbc:IssueDate',date("Y-m-d"));
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cbc:InvoiceTypeCode','03');
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cbc:DocumentCurrencyCode','PEN');
			$raiz->appendChild($nivel1);
			
			$nivel1=$xml->createElement('cac:Signature');
			$raiz->appendChild($nivel1);
			
			$nivel2=$xml->createElement('cbc:ID','SignSUNAT');
			$nivel1->appendChild($nivel2);
			
			$nivel2=$xml->createElement('cac:SignatoryParty');
			$nivel1->appendChild($nivel2);
			
			$nivel3=$xml->createElement('cac:PartyIdentification');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:ID',$parametro['ruc']);
			$nivel3->appendChild($nivel4);
			
			$nivel3=$xml->createElement('cac:PartyName');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:Name',$parametro['nombre_razon_social']);
			$nivel3->appendChild($nivel4);
			
			$nivel2=$xml->createElement('cac:DigitalSignatureAttachment');
			$nivel1->appendChild($nivel2);
			
			$nivel3=$xml->createElement('cac:ExternalReference');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:URI','#signature');
			$nivel3->appendChild($nivel4);
			
			$nivel1=$xml->createElement('cac:AccountingSupplierParty');
			$raiz->appendChild($nivel1);
			
			$nivel2=$xml->createElement('cbc:CustomerAssignedAccountID',$parametro['ruc']);
			$nivel1->appendChild($nivel2);
			
			$nivel2=$xml->createElement('cbc:AdditionalAccountID','6');
			$nivel1->appendChild($nivel2);
			
			$nivel2=$xml->createElement('cac:Party');
			$nivel1->appendChild($nivel2);
			
			
			$nivel3=$xml->createElement('cac:PostalAddress');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:ID','100101');
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cbc:StreetName',$parametro['streetname']);
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cbc:CitySubdivisionName',$parametro['subdivision_name']);
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cbc:CityName','HUANUCO');
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cbc:CountrySubentity','HUANUCO');
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cbc:District','HUANUCO');
			$nivel3->appendChild($nivel4);
			
			$nivel4=$xml->createElement('cac:Country');
			$nivel3->appendChild($nivel4);
			
			$nivel5=$xml->createElement('cbc:IdentificationCode','PE');
			$nivel4->appendChild($nivel5);
			
			$nivel3=$xml->createElement('cac:PartyLegalEntity');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:RegistrationName',$parametro['nombre_razon_social']);
			$nivel3->appendChild($nivel4);
			
			//Cliente
			$nivel1=$xml->createElement('cac:AccountingCustomerParty');
			$raiz->appendChild($nivel1);
			
			$nivel2=$xml->createElement('cbc:CustomerAssignedAccountID',$parametro['dni_responsable_economico']);
			$nivel1->appendChild($nivel2);
			
			$nivel2=$xml->createElement('cbc:AdditionalAccountID','1');
			$nivel1->appendChild($nivel2);
			
			$nivel2=$xml->createElement('cac:Party');
			$nivel1->appendChild($nivel2);

			//Direccion del adquiriente
			if ($parametro['direccion_cliente']!="") {
				$nivel3=$xml->createElement('cac:PhysicalLocation');
				$nivel2->appendChild($nivel3);

				$nivel4=$xml->createElement('cbc:Description',$parametro['direccion_cliente']);
				$nivel3->appendChild($nivel4);
			}
			
			$nivel3=$xml->createElement('cac:PartyLegalEntity');
			$nivel2->appendChild($nivel3);
			
			$nivel4=$xml->createElement('cbc:RegistrationName',$parametro['nombre_responsable_economico']);
			$nivel3->appendChild($nivel4);
			
			//Monto del servicio
			$nivel1=$xml->createElement('cac:LegalMonetaryTotal');
			$raiz->appendChild($nivel1);
			
			$nivel2=$xml->createElement('cbc:PayableAmount',$parametro['monto_total']);
			$nivel2->setAttributeNS('','currencyID','PEN');
			$nivel1->appendChild($nivel2);
			
			//InvoiceLine por cada item, pension -mora, etc
			while($indice_item<$nro_items){
				$nivel1=$xml->createElement('cac:InvoiceLine');
				$raiz->appendChild($nivel1);
			
				$nivel2=$xml->createElement('cbc:ID',$indice_item+1);
				$nivel1->appendChild($nivel2);
			
				$nivel2=$xml->createElement('cbc:InvoicedQuantity','1');
				$nivel2->setAttributeNS('','unitCode','ZZ');
				$nivel1->appendChild($nivel2);
			
				$nivel2=$xml->createElement('cbc:LineExtensionAmount','00.00');
				$nivel2->setAttributeNS('','currencyID','PEN');
				$nivel1->appendChild($nivel2);
			
				$nivel2=$xml->createElement('cac:PricingReference');
				$nivel1->appendChild($nivel2);
			
				$nivel3=$xml->createElement('cac:AlternativeConditionPrice');
				$nivel2->appendChild($nivel3);
			
				$nivel4=$xml->createElement('cbc:PriceAmount',$parametro['item'][$indice_item]['monto']);
				$nivel4->setAttributeNS('','currencyID','PEN');
				$nivel3->appendChild($nivel4);
			
				$nivel4=$xml->createElement('cbc:PriceTypeCode','01');
				$nivel3->appendChild($nivel4);
			
				$nivel2=$xml->createElement('cac:TaxTotal');
				$nivel1->appendChild($nivel2);
			
				$nivel3=$xml->createElement('cbc:TaxAmount','00.00');
				$nivel3->setAttributeNS('','currencyID','PEN');
				$nivel2->appendChild($nivel3);
			
				$nivel3=$xml->createElement('cac:TaxSubtotal');
				$nivel2->appendChild($nivel3);
			
				$nivel4=$xml->createElement('cbc:TaxAmount','00.00');
				$nivel4->setAttributeNS('','currencyID','PEN');
				$nivel3->appendChild($nivel4);
			
				$nivel4=$xml->createElement('cac:TaxCategory');
				$nivel3->appendChild($nivel4);
			
				$nivel5=$xml->createElement('cbc:TaxExemptionReasonCode','20');
				$nivel4->appendChild($nivel5);
			
				$nivel5=$xml->createElement('cac:TaxScheme');
				$nivel4->appendChild($nivel5);
			
				$nivel6=$xml->createElement('cbc:ID','1000');
				$nivel5->appendChild($nivel6);
			
				$nivel6=$xml->createElement('cbc:Name','IGV');
				$nivel5->appendChild($nivel6);
			
				$nivel6=$xml->createElement('cbc:TaxTypeCode','VAT');
				$nivel5->appendChild($nivel6);
			
				$nivel2=$xml->createElement('cac:Item');
				$nivel1->appendChild($nivel2);
			
				$nivel3=$xml->createElement('cbc:Description',$parametro['item'][$indice_item]['concepto']);
				$nivel2->appendChild($nivel3);
			
			
				$nivel2=$xml->createElement('cac:Price');
				$nivel1->appendChild($nivel2);
			
				$nivel3=$xml->createElement('cbc:PriceAmount',$parametro['item'][$indice_item]['monto']);
				$nivel3->setAttributeNS('','currencyID','PEN');
				$nivel2->appendChild($nivel3);
				
				$indice_item++;
			}

			return $xml;
			
		}else{
			return "0";
		}
	}

	public function firmar_xml($xml,$ruc)
	{
		//$parametro en firmar xml -- codrazsoc
		//$public='public'.$codrazsoc.'.pem';
		//$private='private'.$codrazsoc.'.pem';
		$xml_sig=$xml->saveXML();
		$doc = new DOMDocument();
		$doc->loadXML($xml_sig);

		// Create a new Security object 
		$objDSig = new XMLSecurityDSig('ds',"IDFirma-1");
		// Use the c14n exclusive canonicalization
		$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
		// Sign using SHA-256
		$objDSig->addReference(
		    $doc, 
		    XMLSecurityDSig::SHA1, 
		    array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
		    array('force_uri' => true)
		);

		// Create a new (private) Security key
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
		// Load the private key
		$objKey->loadKey('private_'.$ruc.'.pem', true);
		/* 
		If key has a passphrase, set it using 
		$objKey->passphrase = '<passphrase>';
		*/
		//whetosing
		// Sign the XML file
		$doc->getElementsByTagName('ExtensionContent')->item(0);
		$objDSig->sign($objKey);

		// Add the associated public key to the signature
		$objDSig->add509Cert(file_get_contents('public_'.$ruc.'.pem'), true, false, array('subjectName' => true)); // array('issuerSerial' => true, 'subjectName' => true)); 
		// Append the signature to the XML
		$objDSig->appendSignature($doc->getElementsByTagName('ExtensionContent')->item(1));
		
		$_SESSION['objDSig']=$objDSig->sigNode;
		return $doc;
	}


	public function generar_reporte_pdf($parametro)
	{
		switch ($parametro['tipodoc']) {
			case '07':
				$titdocu = 'BOLETA ELECTRONICA';
				$nomdocu = utf8_decode('BOLETA DE VENTA ELECTRÓNICA');
				$numxml = '03';
				$docum = "boleta";
				break;			
			case '08':
				$titdocu = 'FACTURA ELECTRONICA';
				$nomdocu = utf8_decode('FACTURA DE VENTA ELECTRÓNICA');
				$numxml = '01';
				$docum= "factura";
				break;
		}

		$convertirLetra=new numerosALetras;
		define('FPDF_FONTPATH','font/');
		$des_raz_soc=array("20601635306" => array(0 => "CORPORACION EDUCATIVA",1 => "CALICANTO E.I.R.L."),
						   "20601728657" => array(0 => "INVERSIONES", 1 => "COMAFE E.I.R.L"));

		$pdf=new FPDF();

		$pdf->FPDF('P','mm','A4');//Orientacion de la pagina ( Vertical(L:Vertical) ,Horizontal(P:Horizontal) , milimetro, A4)
		$pdf->SetAutoPageBreak(1);
		$pdf->Open();
		$pdf->AddPage();
		$pdf->SetTitle($titdocu);

		//****************************IMAGEN DEL ESCUDO****************************************
		$pdf->Image('../../../img/logo_roosevelt.jpeg',12,12,40,22,0,'','');

		$pdf->SetFont('Arial','',11); // tamaño letra
		$pdf->SetFillColor(255,255,255); //fondo
		$pdf->SetDrawColor(255,255,255); //borde
		$pdf->SetTextColor(0,0,0); // Color del texto
		$pdf->SetXY(55,12);
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][0]),1,1,'C',0);
		$pdf->SetXY(55,18);
		$pdf->SetFont('Arial','B',16); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][1]),1,1,'C',0);
		$pdf->SetXY(55,26);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFont('Arial','',9); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($parametro['streetname']),0,1,'C',0);
		$pdf->SetXY(55,30.5);
		$pdf->Cell(60,5,utf8_decode('Huánuco - Huánuco - Huánuco'),0,1,'C',0);

		$pdf->SetXY(55,35);
		//$pdf->Cell(60,5,utf8_decode('Teléfono: 514236   Celular: 969385737'),0,1,'C',0);

		//DIBUJAMOS EL RECTÁNGULO REDONDEADO
			$pdf->SetLineWidth(0.25);
			//$pdf->SetDrawColor(0,0,0);
			$pdf->RoundedRect(135,10,65,29,2,'DF');
			$pdf->SetXY(135,12);
			$pdf->SetFont('Arial','',12); // tamaño letra
			$pdf->Cell(65,6,"R.U.C. ".$parametro['ruc'],0,1,'C',0);
			$pdf->SetXY(135,20);
			$pdf->SetFont('Arial','B',10); // tamaño letra
			$pdf->SetFillColor(200,200,200); //fondo
			$pdf->Cell(65,7.5,$nomdocu,1,1,'C',1);

			$pdf->SetXY(135,30);
			$pdf->SetFont('Arial','B',15); // tamaño letra
			$pdf->Cell(65,6, $parametro['serie_numero_boleta'],0,1,'C',0);

		//DATOS DEL CLIENTE
			$pdf->SetXY(12,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("Señor (a) : "),0,1,'L',0);

			$pdf->SetXY(28,50);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(20,5, $parametro['nombre_responsable_economico'],0,1,'L',0);

			$pdf->SetXY(12,55);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("Dirección : "),0,1,'L',0);

			$pdf->SetXY(28,55);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(20,5, $parametro['direccion_cliente'],0,1,'L',0);

			$pdf->SetXY(12,60);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("D.N.I. : "),0,1,'L',0);
			$pdf->SetXY(24,60);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(20,5, $parametro['dni_responsable_economico'],0,1,'L',0);

		//Fecha y Hora
			$pdf->SetXY(134,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(36,5, utf8_decode("Fecha de Emisión "),0,1,'L',0);
			
			$pdf->SetXY(170,50);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(36,5, ": ".$parametro['fecha_emision'],0,1,'L',0);

		//Cuadro de Detalle de Factura
			$pdf->SetLineWidth(0.50);
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,70,186,180,'');

			$pdf->SetLineWidth(0.25);
			$pdf->SetFillColor(220,220,220); //fondo
			
			$pdf->SetXY(12,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"CANTIDAD",1,1,'C',1);
			
			$pdf->SetXY(37,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(111,8,utf8_decode("DESCRIPCIÓN"),1,1,'C',1);
			
			$pdf->SetXY(148,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"PREC. UNIT.",1,1,'C',1);
			
			$pdf->SetXY(173,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"IMPORTE",1,1,'C',1);
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,78,25,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(37,78,111,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(148,78,25,172,'');

			$i=0;
			$cy=78;
			

			while ($i<sizeof($parametro['item'])) {
				$cantidad = 1;
				$cx=12;
				$pdf->SetXY($cx,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,7, $cantidad,0,0,'C',0);

				$pdf->SetXY($cx+30,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,7,$parametro['item'][$i]['concepto'],0,0,'L',0);

				//PREC. UNIT

				$pdf->SetXY($cx+145,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,7,number_format($parametro['item'][$i]['monto'],2),0,0,'L',0);

				//IMPORTE	
				$pdf->SetXY($cx+165,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,7,number_format($parametro['item'][$i]['monto'],2),0,0,'L',0);

				if ($this->modulo == "matricula") {
					$cy=$cy+6;

					$pdf->SetXY($cx+35,$cy);
					$pdf->SetFont('Arial','',8); // tamaño letra
					$pdf->Cell(25,7,$parametro['info_operacion'][$i],0,0,'L',0);
				}

				$cy=$cy+6;

				$i++;
			}

			if ($this->modulo=="matricula") {
				$pdf->SetXY($cx+30,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,7,"ALUMNO: ".strtoupper($parametro['nombre_alumno']),0,0,'L',0);
			}
			//Informacion Boleta y banco


		//SUBTOTALES

			$pdf->SetXY(148,250);
			$pdf->SetFont('Arial','B',9); // tamaño letra
			//$pdf->Cell(25,7, "Sub Total",1,1,'C',1);
			$pdf->Cell(25,7, "TOTAL",1,1,'C',1);
			//$pdf->SetXY(148,256);
			// $pdf->SetFont('Arial','',8); // tamaño letra
			// $pdf->Cell(25,7, "I.G.V",1,1,'C',1);
			// $pdf->SetXY(148,262);
			// $pdf->SetFont('Arial','',8); // tamaño letra
			// $pdf->Cell(25,7, "TOTAL",1,1,'C',1);

		//SUBTOTALES PRECIO
			$pdf->SetLineWidth(0.40);
			$pdf->RoundedRect(12,251,135,6,2,"");
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->SetXY(12,250.5);
			$pdf->Cell(150,7, "SON: ".strtoupper($convertirLetra->convertir($parametro['monto_total']))." SOLES",0,1,'L',0);

			$pdf->SetXY(173,250);
			$pdf->SetFont('Arial','B',9); // tamaño letra
			$pdf->Cell(25,6.9,"S/ ".number_format($parametro['monto_total'],2),1,1,'C',0);
			//$pdf->SetXY(173,250);
			//$pdf->Cell(5,6, "S/",0,1,'R',0);

			$pdf->SetXY(12,258);
			$pdf->SetFont('Arial','B',7); // tamaño letra
			$frase1 = utf8_decode("Bienes Transferidos en la Amazonía para ser consumidos en la misma.");
			$pdf->Cell(135,5,$frase1,0,0,'L',0);

			$pdf->SetXY(12,261);
			$pdf->SetFont('Arial','B',7); // tamaño letra
			$frase2 = utf8_decode("Servicios Prestados en la Amazonía.");
			$pdf->Cell(135,5,$frase2,0,0,'L',0);

			// $pdf->SetXY(173,256);
			// $pdf->Cell(25,6, "0.00",1,1,'R',0);
			// $pdf->SetXY(173,256);
			// $pdf->Cell(5,6, "S/",0,1,'R',0);

			// $pdf->SetXY(173,262);
			// $pdf->Cell(25,7,number_format($parametro['monto_total'],2),1,1,'R',0);
			// $pdf->SetXY(173,262);
			// $pdf->Cell(5,7, "S/",0,1,'R',0);

			$pdf->SetLineWidth(0.50);
			$pdf->Rect(148,250,50,7,"");

			//Firma
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(80,270);
			$pdf->Cell(150,10,$parametro['digest_value'],0,1,'L',0);

			//Aviso
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(20,280);
			$pdf->Cell(170,10,"Representacion impresa de la boleta de venta electronica. Puede verificarla utilizando su clave SOL",1,1,'C',0);

			$dir=$_SERVER['DOCUMENT_ROOT'];
			$pdf->Output($dir.'/roosevelt/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/'.$docum.'/REPO/R'.$parametro['ruc'].'-'.$numxml.'-'.$parametro['serie_numero_boleta'].'.pdf','F');
			if (file_exists($dir.'/roosevelt/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/'.$docum.'/REPO/R'.$parametro['ruc'].'-'.$numxml.'-'.$parametro['serie_numero_boleta'].'.pdf')) {
				return 1;
			}else{
				return 0;
			}
	}

	public function send_xml_smb_windows_server($ruta,$ruc,$serie_numero,$id_documento = '03')
	{

		$smbc_20601635306 = new smbclient('//192.168.1.250/data', 'facturador', '**15975321**');
		$smbc_20601728657= new smbclient('//192.168.1.250/data2', 'facturador', '**15975321**');
		$name_doc = $ruc.'-'.$id_documento.'-'.$serie_numero.'.xml';
		${'smbc_'.$ruc}->put($ruta,$name_doc);
	}
	
	public function get_xml_smb_windows_server($ruc,$nombre_archivo,$destino)
	{
		$smbc_20601635306 = new smbclient('//192.168.1.250/firma', 'facturador', '**15975321**');
		$smbc_20601728657= new smbclient('//192.168.1.250/firma2', 'facturador', '**15975321**');

		if (!${'smbc_'.$ruc}->get($nombre_archivo,$destino)) {
			//print "Failed to retrive file: ";
			//print join("\n",${'smbc_'.$ruc}->get_last_cmd_stdout());
			return 0;
		}else{
			//print "Transferred file sucessfully";
			return 1;
		}


	}

	public function get_rpta_sunat_windows_server($ruc,$nombre_archivo,$ruta)
	{
		$smbc_20601635306 = new smbclient('//192.168.1.250/rpta', 'facturador', '**15975321**');
		$smbc_20601728657= new smbclient('//192.168.1.250/rpta2', 'facturador', '**15975321**');
		$nombre_archivo_zip="R".$nombre_archivo.".zip";
		if (!${'smbc_'.$ruc}->get($nombre_archivo_zip,$ruta.$nombre_archivo_zip)) {
			//print "Failed to retrive file: ";
			//print join("\n",${'smbc_'.$ruc}->get_last_cmd_stdout());
			return 0;
		}else{
			$zip = new ZipArchive;
			$zip->open($ruta.$nombre_archivo_zip);
			$zip->extractTo($ruta);
    		$zip->close();
    		unlink($ruta.$nombre_archivo_zip);
			return 1;
			//print "Transferred file sucessfully";
		}

	}

	public function read_rpta_xml($nombre_archivo,$ruta)
	{
		$nombre_archivo_rpta="R-".$nombre_archivo.".xml";
		$dom = new DOMDocument('1.0','utf-8'); 
		$dom->load($ruta.$nombre_archivo_rpta); 
     	$code_respuesta=$dom->getElementsByTagName("ResponseCode")[0]->childNodes[0]->nodeValue;
     	unset($dom);
     	return $code_respuesta;	
     	//echo $code_respuesta;	
	}

	public function get_digest_value_xml($nombre_archivo,$ruta)
	{
		$nombre_archivo_firma=$nombre_archivo.".xml";
		$dom= new DOMDocument('1.0','utf-8');
		$dom->load($ruta.$nombre_archivo_firma);
		$digest_value=$dom->getElementsByTagName('DigestValue')[0]->childNodes[0]->nodeValue;
		unset($dom);
		return $digest_value;
	}

	

	public function get_boleta_electronica()
	{

	}

	public function get_info_banco($banco)
	{

		$this->utili->campos = "*"; //secuencia,canalu,usecod
		$this->utili->tabla = "bancos";
		$this->utili->condicion = "where codban='".$banco."'";
		$rs_banco = $this->utili->get_datos_condicion($this->conexion);
		
		return trim($rs_banco->Fields('desbanco'));
	}

	function send_email($parametro,$type_doc,$numxml) {

		switch ($type_doc) {
			case 'boleta':
				$nomdocu="BOLETA DE VENTA ELECTRONICA";
				break;
			case 'factura':
				$nomdocu="FACTURA DE VENTA ELECTRONICA";
				break;
		}

		$my_path = $_SERVER['DOCUMENT_ROOT'].'/roosevelt/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/'.$type_doc.'/REPO/R'.$parametro['ruc'].'-'.$numxml.'-'.$parametro['serie_numero_boleta'].'.pdf';

		if (file_exists($my_path)) {
			$this->correo->IsSMTP();
			$this->correo->SMTPAuth = true;
			$this->correo->SMTPSecure = 'tls';	
			$this->correo->Host = "smtp.gmail.com";
			$this->correo->Port = 587;
			$this->correo->Username   = "recaudaciones@rooseveltschool.pe";
			$this->correo->Password   = "tarea010203";
			$this->correo->SetFrom("recaudaciones@rooseveltschool.pe", "Comprobante Electronico Roosevelt");
			//$lista_correo[$i];
			$this->correo->AddAddress($parametro['mail_cliente']);
			//$correo->AddAddress("@vonneumann.pe", "DATA ".date('d-m'));
			//$correo->AddAddress("etarazona@vonneumann.pe", "DATA ".date('d-m'));
			$this->correo->Subject = "Comprobante Electronico ".$parametro['nombre_razon_social'];

	//<img src="https://1.bp.blogspot.com/-6baTJzoE38U/Vxzk12Ckm8I/AAAAAAAAOJ0/rpi5-hc1DJkozjVH5bLCYSReTID2wk0OQCLcB/s1600/logo%2Bpara%2Bfondo%2Bblanco2.png" style="    width: 180px;">
	
			$mensajeHtml = ''.
			'<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" width="801px">'.
				'<tbody>'.
					'<tr>'.
						'<td width="801px">'.
							'<div align="right">'.
								'<img src="../../../img/logo_roosevelt.jpeg" style="width: 180px;">'.
							'</div>'.
						'</td>'.
					'</tr>'.
					'<tr>'.
						'<td style="padding:10px; color:black;">'.
							'<b>'.
								'Estimado Cliente, <br>'.
								'Sr(a). '.$parametro['nombre_responsable_economico'].'<br>'.
								'DNI '.$parametro['dni_responsable_economico'].
							'</b>'.
							'<br><br>'.
							'<span style="color: gray;">'.
								'Informamos a usted que el documento '.$parametro['serie_numero_boleta'].
								', ya se encuentra disponible.<br><br>'.
								'<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" cellspacing="2" cellpadding="2" border="0">'.
									'<tbody>'.
										'<tr>'.
											'<td width="20"></td><td style="font-weight:bold;color:#215f96">Tipo</td>'.
											'<td style="font-weight:bold;color:#4d9621">:</td>'.
											'<td>'.$nomdocu.'</td>'.
										'</tr>'.
										'<tr>'.
											'<td></td><td style="font-weight:bold;color:#215f96">Numero</td>'.
											'<td style="font-weight:bold;color:#215f96">:</td>'.
											'td>'.$parametro['serie_numero_boleta'].'</td>'.
										'</tr>'.
										'<tr>'.
											'<td></td><td style="font-weight:bold;color:#215f96">Fecha Emision</td>'.
											'<td style="font-weight:bold;color:#215f96">:</td>'.
											'<td>'.$parametro['fecha_emision'].'</td>'.
										'</tr>'.
									'</tbody>'.
								'</table>'.
							'</span>'.
						'</td>'.
					'</tr>'.
					'<tr>'.
						'<td style="padding:10px">'.
							'<br>'.
							'<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" width="801px" cellspacing="0" cellpadding="0" border="0">'.
								'<tbody>'.
									'<tr>'.
										'<td valign="top" style="color: black;">'.
											'Saluda Atentamente,<br><br>'.
											'<b><span style="color : black;">ROOSEVELT SCHOOL</span></b></td><td valign="top"></td>'.
										'</td>'.
									'</tr>'.
								'</tbody>'.
							'</table>'.
						'</td>'.
					'</tr>'.
				'</tbody>'.
			'</table>';
			
			$this->correo->MsgHTML($mensajeHtml);

			$my_path = $_SERVER['DOCUMENT_ROOT'].'/roosevelt/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/'.$type_doc.'/REPO/R'.$parametro['ruc'].'-'.$numxml.'-'.$parametro['serie_numero_boleta'].'.pdf';
			$this->correo->AddAttachment($my_path);

			$this->utili->valor1 = $parametro['id_razon_social'];
			$documento = explode('-', $parametro['num_documento']);
			$num_documento = $documento[1];
			$serie_documento = $documento[0];
			$this->utili->seriedoc = $serie_documento;
			$this->utili->numdoc = $num_documento;
 
			if(!$this->correo->Send()) {
				$std_send_email = 'PE';
				$this->utili->actualiza_estadoEmailboleta($this->conexion,$std_send_email);
  				//echo "Hubo un error: " . $this->correo->ErrorInfo;
			} else {
				$std_send_email = 'GE';
				$this->utili->actualiza_estadoEmailboleta($this->conexion,$std_send_email);
  				//echo "Mensaje enviado con exito.";
			}

			/*$MSG='<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" width="801px">
<tbody><tr>
<td width="801px">
<div align="right">
<img src="../../../img/logo_roosevelt.jpeg" style="width: 180px;"></div>
</td>
</tr>
<tr>
<td style="padding:10px; color:black;"><b>
				Estimado Cliente, <br>
				Sr(a).
				'.$parametro['nombre_responsable_economico'].'<br>DNI&nbsp;'.$parametro['dni_responsable_economico'].'</b>
<br>
<br>
	<span style="
    color: gray;
">
			Informamos a usted que el documento '.$parametro['serie_numero_boleta'].', 
			ya se encuentra disponible
			<br>
<br>
<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" cellspacing="2" cellpadding="2" border="0">
<tbody><tr>
<td width="20"></td><td style="font-weight:bold;color:#215f96">Tipo</td><td style="font-weight:bold;color:#4d9621">:</td><td>BOLETA DE VENTA ELECTRONICA</td>
</tr>
<tr>
<td></td><td style="font-weight:bold;color:#215f96">Numero</td><td style="font-weight:bold;color:#215f96">:</td><td>'.$parametro['serie_numero_boleta'].'</td>
</tr>
<tr>
<td></td><td style="font-weight:bold;color:#215f96">Monto</td><td style="font-weight:bold;color:#215f96">:</td><td>'.$parametro['monto_total'].'</td>
</tr>
<tr>
<td></td><td style="font-weight:bold;color:#215f96">Fecha Emision</td><td style="font-weight:bold;color:#215f96">:</td><td>'.$parametro['fecha_emision'].'</td>
</tr>
</tbody></table>
</td>
</tr>
<tr>
<td style="padding:10px">
<br>
<table style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px" width="801px" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td valign="top" style="
    color: black;
">
				Saluda atentamente, <br>
<br>
<b><span style="color : black;">COLEGIO VON NEUMANN</span></b></td><td valign="top"></td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>';*/
			
		}
		
		}
	//flujo=0
	//si es uno el flujo falla por lo tanto se ira a generacion de reporte sin firma.

		//Anulacion de boletas

		public function genera_nota_credito_xml($parametro)
		{
			$xml  = new DOMDocument('1.0', 'ISO-8859-1');
			$xml->xmlStandalone=false;
			$xml->formatOutput = true;
			$raiz = $xml->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2', 'CreditNote');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:cac','urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:cbc','urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ccts','urn:un:unece:uncefact:documentation:2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ds','http://www.w3.org/2000/09/xmldsig#');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ext','urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:qdt','urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:sac','urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:udt','urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
			$raiz->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
			$xml->appendChild($raiz);

			$nivel1 = $xml->createElement('ext:UBLExtensions');
			$raiz->appendChild($nivel1);
			$nivel2 = $xml->createElement('ext:UBLExtension');
			$nivel1->appendChild($nivel2);
			$nivel3 = $xml->createElement('ext:ExtensionContent');
			$nivel2->appendChild($nivel3);
			$nivel4 = $xml->createElement('sac:AdditionalInformation');
			$nivel3->appendChild($nivel4);
			$nivel5 = $xml->createElement('sac:AdditionalMonetaryTotal');
			$nivel4->appendChild($nivel5);
			$nivel6 = $xml->createElement('cbc:ID','1001');
			$nivel5->appendChild($nivel6);
			$nivel6 = $xml->createElement('cbc:PayableAmount',$parametro['monto_total']);
			$nivel6->setAttributeNS('','currencyID','PEN');
			$nivel5->appendChild($nivel6);

			$nivel2 = $xml->createElement('ext:UBLExtension');
			$nivel1->appendChild($nivel2);
			$nivel3 = $xml->createElement('ext:ExtensionContent');
			$nivel2->appendChild($nivel3);

			$nivel1 = $xml->createElement('cbc:UBLVersionID','2.0');
			$raiz -> appendChild($nivel1);
			$nivel1 =  $xml->createElement('cbc:CustomizationID','1.0');
			$raiz -> appendChild($nivel1);
			$nivel1 = $xml->createElement('cbc:ID',$parametro['serie_numero']);
			$raiz -> appendChild($nivel1);
			$nivel1 =  $xml->createElement('cbc:IssueDate',$parametro['fecha_emision']);
			$raiz -> appendChild($nivel1);
			$nivel1 = $xml->createElement('cbc:DocumentCurrencyCode','PEN');
			$raiz -> appendChild($nivel1);
			$nivel1 = $xml->createElement('cac:DiscrepancyResponse');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:ReferenceID',$parametro['boleta_anulada']);
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cbc:ResponseCode',$parametro['cod_tipo_nota']);
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cbc:Description',$parametro['sustento_notac']);
			$nivel1 -> appendChild($nivel2);

			$nivel1 =  $xml->createElement('cac:BillingReference');
			$raiz -> appendChild($nivel1);
			$nivel2 = $xml->createElement('cac:InvoiceDocumentReference');
			$nivel1 -> appendChild($nivel2);
			$nivel3 = $xml->createElement('cbc:ID',$parametro['boleta_anulada']);
			$nivel2 -> appendChild($nivel3);
			$nivel3 = $xml->createElement('cbc:DocumentTypeCode',$parametro['tipo_doc_modifica']);
			$nivel2 -> appendChild($nivel3);

			$nivel1 =  $xml->createElement('cac:Signature');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:ID','SignSUNAT');
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cac:SignatoryParty');
			$nivel1 -> appendChild($nivel2);
			$nivel3 =  $xml->createElement('cac:PartyIdentification');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:ID',$parametro['ruc']);
			$nivel3 -> appendChild($nivel4);
			$nivel3 =  $xml->createElement('cac:PartyName');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:Name',$parametro['nombre_razon_social']);
			$nivel3 -> appendChild($nivel4);
			$nivel2 =  $xml->createElement('cac:DigitalSignatureAttachment');
			$nivel1 -> appendChild($nivel2);
			$nivel3 =  $xml->createElement('cac:ExternalReference');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:URI','#signature');
			$nivel3 -> appendChild($nivel4);

			$nivel1 =  $xml->createElement('cac:AccountingSupplierParty');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:CustomerAssignedAccountID',$parametro['ruc']);
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cbc:AdditionalAccountID','6'); //REV
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cac:Party');
			$nivel1 -> appendChild($nivel2);
			$nivel3 =  $xml->createElement('cac:PartyName');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:Name');
			$nivel3 -> appendChild($nivel4);
			$nivel3 =  $xml->createElement('cac:PostalAddress');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:ID','100101');
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cbc:StreetName',$parametro['streetname']);
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cbc:CitySubdivisionName',$parametro['subdivision_name']);
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cbc:CityName','HUANUCO');
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cbc:CountrySubentity','HUANUCO');
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cbc:District','HUANUCO');
			$nivel3 -> appendChild($nivel4);
			$nivel4 =  $xml->createElement('cac:Country');
			$nivel3 -> appendChild($nivel4);
			$nivel5 =  $xml->createElement('cbc:IdentificationCode','PE');
			$nivel4 -> appendChild($nivel5);
			$nivel3 =  $xml->createElement('cac:PartyLegalEntity');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:RegistrationName',$parametro['nombre_razon_social']);
			$nivel3 -> appendChild($nivel4);

			$nivel1 =  $xml->createElement('cac:AccountingCustomerParty');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:CustomerAssignedAccountID',$parametro['dni_cliente']);
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cbc:AdditionalAccountID','1');
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cac:Party');
			$nivel1 -> appendChild($nivel2);
			$nivel3 =  $xml->createElement('cac:PartyLegalEntity');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cbc:RegistrationName',$parametro['nombre_cliente']);
			$nivel3 -> appendChild($nivel4);

			$nivel1 =  $xml->createElement('cac:TaxTotal');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:TaxAmount','00.00');
			$nivel2->setAttributeNS('','currencyID','PEN');
			$nivel1 -> appendChild($nivel2);
			$nivel2 =  $xml->createElement('cac:TaxSubtotal');
			$nivel1 -> appendChild($nivel2);
			$nivel3 =  $xml->createElement('cbc:TaxAmount','00.00');
			$nivel3 -> setAttributeNS('','currencyID','PEN');
			$nivel2 -> appendChild($nivel3);
			$nivel3 =  $xml->createElement('cac:TaxCategory');
			$nivel2 -> appendChild($nivel3);
			$nivel4 =  $xml->createElement('cac:TaxScheme');
			$nivel3 -> appendChild($nivel4);
			$nivel5 =  $xml->createElement('cbc:ID','1000');
			$nivel4 -> appendChild($nivel5);
			$nivel5 =  $xml->createElement('cbc:Name','IGV');
			$nivel4 -> appendChild($nivel5);

			$nivel1 =  $xml->createElement('cac:LegalMonetaryTotal');
			$raiz -> appendChild($nivel1);
			$nivel2 =  $xml->createElement('cbc:PayableAmount',$parametro['monto_total']);
			$nivel2->setAttributeNS('','currencyID','PEN');
			$nivel1 -> appendChild($nivel2);
			$cont=0;
			while ($cont < sizeof($parametro['item']) ) {
				$nivel1 =  $xml->createElement('cac:CreditNoteLine');
				$raiz -> appendChild($nivel1);
				$nivel2 =  $xml->createElement('cbc:ID',$cont+1);
				$nivel1 -> appendChild($nivel2);
				$nivel2 =  $xml->createElement('cbc:CreditedQuantity','1');
				$nivel2->setAttributeNS('','unitCode','ZZ');
				$nivel1 -> appendChild($nivel2);
				$nivel2 =  $xml->createElement('cbc:LineExtensionAmount','00.00');//OPERACION EXONERADA
				$nivel2->setAttributeNS('','currencyID','PEN');
				$nivel1 -> appendChild($nivel2);
				$nivel2 =  $xml->createElement('cac:PricingReference');
				$nivel1 -> appendChild($nivel2);
				$nivel3 =  $xml->createElement('cac:AlternativeConditionPrice');
				$nivel2 -> appendChild($nivel3);
				$nivel4 =  $xml->createElement('cbc:PriceAmount',$parametro['item'][$cont]['monto']); //MONTO_1 -MORA
				$nivel4->setAttributeNS('','currencyID','PEN');
				$nivel3 -> appendChild($nivel4);
				$nivel4 =  $xml->createElement('cbc:PriceTypeCode','01');
				$nivel3 -> appendChild($nivel4);
				$nivel2 =  $xml->createElement('cac:TaxTotal');
				$nivel1 -> appendChild($nivel2);
				$nivel3 =  $xml->createElement('cbc:TaxAmount','00.00');
				$nivel3->setAttributeNS('','currencyID','PEN');
				$nivel2 -> appendChild($nivel3);
				$nivel3 =  $xml->createElement('cac:TaxSubtotal');
				$nivel2 -> appendChild($nivel3);
				$nivel4 =  $xml->createElement('cbc:TaxAmount','00.00');
				$nivel4->setAttributeNS('','currencyID','PEN');
				$nivel3 -> appendChild($nivel4);
				$nivel4 =  $xml->createElement('cac:TaxCategory');
				$nivel3 -> appendChild($nivel4);
				$nivel5 =  $xml->createElement('cbc:TaxExemptionReasonCode','20');
				$nivel4 -> appendChild($nivel5);
				$nivel5 =  $xml->createElement('cac:TaxScheme');
				$nivel4 -> appendChild($nivel5);
				$nivel6 =  $xml->createElement('cbc:ID','1000');
				$nivel5 -> appendChild($nivel6);
				$nivel6 =  $xml->createElement('cbc:Name','IGV');
				$nivel5 -> appendChild($nivel6);
				$nivel6 =  $xml->createElement('cbc:TaxTypeCode','VAT');
				$nivel5 -> appendChild($nivel6);
				$nivel2 =  $xml->createElement('cac:Item');
				$nivel1 -> appendChild($nivel2);
				$nivel3 =  $xml->createElement('cbc:Description',$parametro['item'][$cont]['concepto']); //DESCRIPCION item
				$nivel2 -> appendChild($nivel3);
				/*$nivel3 =  $xml->createElement('cac:SellersItemIdentification');
				$nivel2 -> appendChild($nivel3);
				$nivel4 =  $xml->createElement('cbc:ID');
				$nivel3 -> appendChild($nivel4);*/
				$nivel2 =  $xml->createElement('cac:Price');
				$nivel1 -> appendChild($nivel2);
				$nivel3 =  $xml->createElement('cbc:PriceAmount',$parametro['item'][$cont]['monto']);//monto item
				$nivel3->setAttributeNS('','currencyID','PEN');
				$nivel2 -> appendChild($nivel3);
				$cont++;
			}
			return $xml;
		}

		function registrar_nota_credito($serie,$numero,$tipo,$motivo,$seq_pago)
		{
			$pago = new Pago();
			$pago->serie_documento =  $serie;
			$pago->num_documento = $numero;
			$pago->tipo_documento = $tipo;
			$pago->desconf = $motivo;
			$pago->secuencia = $seq_pago;
			$pago->registrar_nota_credito($this->conexion);
		}

		function actualizar_estado_cp1($estado,$secuencia)
		{
			$pago = new Pago();
			$pago->flag=$estado;
			$pago->secuencia=$secuencia;
			$pago->actualizar_estado_cp1($this->conexion);

		}

	public function generar_nota_credito_pdf($parametro)
	{
		define('FPDF_FONTPATH','font/');
		$des_raz_soc=array("20601635306" => array(0 => "CORPORACION EDUCATIVA",1 => "CALICANTO E.I.R.L."),
						   "20601728657" => array(0 => "INVERSIONES", 1 => "COMAFE E.I.R.L"));

		$pdf=new FPDF();

		$pdf->FPDF('P','mm','A4');//Orientacion de la pagina ( Vertical(L:Vertical) ,Horizontal(P:Horizontal) , milimetro, A4)
		$pdf->SetAutoPageBreak(1);
		$pdf->Open();
		$pdf->AddPage();
		$pdf->SetTitle('NOTA DE CREDITO');

		//****************************IMAGEN DEL ESCUDO****************************************
		$pdf->Image('../../../img/von22.jpg',12,12,40,22,0,'','');

		$pdf->SetFont('Arial','',11); // tamaño letra
		$pdf->SetFillColor(255,255,255); //fondo
		$pdf->SetDrawColor(255,255,255); //borde
		$pdf->SetTextColor(0,0,0); // Color del texto
		$pdf->SetXY(55,12);
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][0]),1,1,'C',0);
		$pdf->SetXY(55,18);
		$pdf->SetFont('Arial','B',16); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][1]),1,1,'C',0);
		$pdf->SetXY(55,26);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFont('Arial','',9); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($parametro['streetname']),0,1,'C',0);
		$pdf->SetXY(55,30.5);
		$pdf->Cell(60,5,utf8_decode('Huánuco - Huánuco'),0,1,'C',0);


		$pdf->SetXY(55,35);
		//$pdf->Cell(60,5,utf8_decode('Teléfono: 514236   Celular: 969385737'),0,1,'C',0);

		//DIBUJAMOS EL RECTÁNGULO REDONDEADO
			$pdf->SetLineWidth(0.25);
			//$pdf->SetDrawColor(0,0,0);
			$pdf->RoundedRect(135,10,65,29,2,'DF');
			$pdf->SetXY(135,12);
			$pdf->SetFont('Arial','',12); // tamaño letra
			$pdf->Cell(65,6,"R.U.C. ".$parametro['ruc'],0,1,'C',0);
			$pdf->SetXY(135,20);
			$pdf->SetFont('Arial','B',10); // tamaño letra
			$pdf->SetFillColor(200,200,200); //fondo
			$pdf->Cell(65,7.5,utf8_decode("NOTA DE CRÉDITO ELECTRÓNICA"),1,1,'C',1);

			$pdf->SetXY(135,30);
			$pdf->SetFont('Arial','B',15); // tamaño letra
			$pdf->Cell(65,6, $parametro['serie_numero_boleta'],0,1,'C',0);

		//DATOS DEL CLIENTE
			$pdf->SetXY(12,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("Señor (a) : "),0,1,'L',0);

			$pdf->SetXY(28,50);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(20,5, $parametro['nombre_responsable_economico'],0,1,'L',0);

			//$pdf->SetXY(12,55);
			//$pdf->SetFont('Arial','B',8); // tamaño letra
			//$pdf->Cell(20,5, utf8_decode("Dirección : "),0,1,'L',0);

			//$pdf->SetXY(28,55);
			//$pdf->SetFont('Arial','',8); // tamaño letra
			//$pdf->Cell(20,5, $parametro['direccion_cliente'],0,1,'L',0);

			$pdf->SetXY(12,55);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("D.N.I. : "),0,1,'L',0);
			$pdf->SetXY(24,55);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(20,5, $parametro['dni_responsable_economico'],0,1,'L',0);

		//Fecha y Hora
			$pdf->SetXY(134,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(36,5, utf8_decode("Fecha de Emisión "),0,1,'L',0);
			$pdf->SetXY(160,50);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(36,5, ": ".$parametro['fecha_emision'],0,1,'L',0);

			$pdf->SetXY(134,55);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(36,5, utf8_decode("Doc. Relacionado "),0,1,'L',0);

			$pdf->SetXY(160,55);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(36,5,  ": ".$parametro['boleta_anulada'],0,1,'L',0);

			$pdf->SetXY(12,60);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("Motivo de Anulación "),0,1,'L',0);

			$pdf->SetXY(40,60);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(90,5, ": ".$parametro['motivo'],0,1,'L',0);

			$pdf->SetXY(134,60);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(36,5, utf8_decode("Tipo Moneda "),0,1,'L',0);

			$pdf->SetXY(160,60);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(36,5,  ": PEN ",0,1,'L',0);
		//Cuadro de Detalle de Factura
			$pdf->SetLineWidth(0.50);
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,70,186,180,'');

			$pdf->SetLineWidth(0.25);
			$pdf->SetFillColor(220,220,220); //fondo
			
			$pdf->SetXY(12,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"CANTIDAD",1,1,'C',1);
			
			$pdf->SetXY(37,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(111,8,utf8_decode("DESCRIPCIÓN"),1,1,'C',1);
			
			$pdf->SetXY(148,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"PREC. UNIT.",1,1,'C',1);
			
			$pdf->SetXY(173,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"IMPORTE",1,1,'C',1);
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,78,25,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(37,78,111,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(148,78,25,172,'');


			$i=0;
			$cy=78;
			

			while ($i<sizeof($parametro['item'])) {
				$cantidad = 1;
				$cx=12;

				$concepto =trim($parametro['item'][$i]['concepto']);
				$posicion_coincidencia = strpos($concepto, 'MORA');
				$strlen = strlen($concepto);

				$alto_y =7;

				$pdf->SetXY($cx,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y, $cantidad,0,0,'C',0);

				$pdf->SetXY($cx+26,$cy);
				$pdf->SetFont('Arial','',7); // tamaño letra			
				 
				
				if($posicion_coincidencia === false){
					if($strlen>=68){
						$alto_y_2 = 3.5;
					}
					else{
						$alto_y_2 = 7;
					}	
					$pdf->MultiCell(109,$alto_y_2,$concepto,0,'L',0);
				}
				else{
					$pdf->Cell(109,$alto_y,$concepto,0,0,'L',0);
				}

				//PREC. UNIT

				$pdf->SetXY($cx+136,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y,$parametro['item'][$i]['monto'],0,0,'C',0);

				//IMPORTE	
				$pdf->SetXY($cx+161,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y,$parametro['item'][$i]['monto'],0,0,'C',0);

				//if ($this->modulo == "matricula") {
				//	$cy=$cy+6;

				//	$pdf->SetXY($cx+35,$cy);
				//	$pdf->SetFont('Arial','',8); // tamaño letra
				//	$pdf->Cell(25,$alto_y,$parametro['info_operacion'][$i],1,0,'L',0);
				//}

				$cy=$cy+$alto_y;

				$i++;
			} 

			//if ($this->modulo=="matricula") {
			//	$pdf->SetXY($cx+30,$cy);
			//	$pdf->SetFont('Arial','',8); // tamaño letra
			//	$pdf->Cell(25,7,"ALUMNO: ".strtoupper($parametro['nombre_alumno']),0,0,'L',0);
			//}
			//Informacion Boleta y banco


		//SUBTOTALES

			$pdf->SetXY(148,250);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "Sub Total",1,1,'C',1);
			$pdf->SetXY(148,256);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "I.G.V",1,1,'C',1);
			$pdf->SetXY(148,262);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "TOTAL",1,1,'C',1);

			$pdf->SetXY(173,250);
			$pdf->Cell(25,6,$parametro['monto_total'],1,1,'R',0);
			$pdf->SetXY(173,250);
			$pdf->Cell(5,6, "S/",0,1,'R',0);

			$pdf->SetXY(173,256);
			$pdf->Cell(25,6, "0.00",1,1,'R',0);
			$pdf->SetXY(173,256);
			$pdf->Cell(5,6, "S/",0,1,'R',0);

			$pdf->SetXY(173,262);
			$pdf->Cell(25,7, $parametro['monto_total'],1,1,'R',0);
			$pdf->SetXY(173,262);
			$pdf->Cell(5,7, "S/",0,1,'R',0);

			$pdf->SetLineWidth(0.50);
			$pdf->Rect(148,250,50,19,"");

			//Firma
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(80,270);
			$pdf->Cell(150,10,$parametro['digest_value'],0,1,'L',0);

			//Aviso
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(20,280);
			$pdf->Cell(170,10,"Representacion impresa de la boleta de venta electronica. Puede verificarla utilizando su clave SOL",1,1,'C',0);

		$dir=$_SERVER['DOCUMENT_ROOT'];

		$pdf->Output($dir.'/intranet/sftp/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/nota_credito/REPO/R'.$parametro['ruc'].'-07-'.$parametro['serie_numero_boleta'].'.pdf','F');
		if (file_exists($dir.'/intranet/sftp/sis_colegio/documento/pago_xml/'.$parametro['ruc'].'/nota_credito/REPO/R'.$parametro['ruc'].'-07-'.$parametro['serie_numero_boleta'].'.pdf')) {
			return 1;
		}else{
			return 0;
		}
	}

	public function imprimir_nota_credito_pdf($parametro)
	{
		define('FPDF_FONTPATH','font/');
		$des_raz_soc=array("20601635306" => array(0 => "CORPORACION EDUCATIVA",1 => "CALICANTO E.I.R.L."),
						   "20601728657" => array(0 => "INVERSIONES", 1 => "COMAFE E.I.R.L"));

		$pdf=new FPDF();

		$pdf->FPDF('P','mm','A4');//Orientacion de la pagina ( Vertical(L:Vertical) ,Horizontal(P:Horizontal) , milimetro, A4)
		$pdf->SetAutoPageBreak(1);
		$pdf->Open();
		$pdf->AddPage();
		$pdf->SetTitle('NOTA DE CREDITO');

		//****************************IMAGEN DEL ESCUDO****************************************
		$pdf->Image('../../../img/von22.jpg',12,12,40,22,0,'','');

		$pdf->SetFont('Arial','',11); // tamaño letra
		$pdf->SetFillColor(255,255,255); //fondo
		$pdf->SetDrawColor(255,255,255); //borde
		$pdf->SetTextColor(0,0,0); // Color del texto
		$pdf->SetXY(55,12);
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][0]),1,1,'C',0);
		$pdf->SetXY(55,18);
		$pdf->SetFont('Arial','B',16); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($des_raz_soc[$parametro['ruc']][1]),1,1,'C',0);
		$pdf->SetXY(55,26);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFont('Arial','',9); // tamaño letra
		$pdf->Cell(60,5,utf8_decode($parametro['streetname']),0,1,'C',0);
		$pdf->SetXY(55,30.5);
		$pdf->Cell(60,5,utf8_decode('Huánuco - Huánuco'),0,1,'C',0);


		$pdf->SetXY(55,35);
		//$pdf->Cell(60,5,utf8_decode('Teléfono: 514236   Celular: 969385737'),0,1,'C',0);

		//DIBUJAMOS EL RECTÁNGULO REDONDEADO
			$pdf->SetLineWidth(0.25);
			//$pdf->SetDrawColor(0,0,0);
			$pdf->RoundedRect(135,10,65,29,2,'DF');
			$pdf->SetXY(135,12);
			$pdf->SetFont('Arial','',12); // tamaño letra
			$pdf->Cell(65,6,"R.U.C. ".$parametro['ruc'],0,1,'C',0);
			$pdf->SetXY(135,20);
			$pdf->SetFont('Arial','B',10); // tamaño letra
			$pdf->SetFillColor(200,200,200); //fondo
			$pdf->Cell(65,7.5,utf8_decode("NOTA DE CRÉDITO ELECTRÓNICA"),1,1,'C',1);

			$pdf->SetXY(135,30);
			$pdf->SetFont('Arial','B',15); // tamaño letra
			$pdf->Cell(65,6, $parametro['serie_numero_boleta'],0,1,'C',0);

		//DATOS DEL CLIENTE
			$pdf->SetXY(12,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("Señor (a) : "),0,1,'L',0);

			$pdf->SetXY(28,50);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(20,5, $parametro['nombre_responsable_economico'],0,1,'L',0);

			//$pdf->SetXY(12,55);
			//$pdf->SetFont('Arial','B',8); // tamaño letra
			//$pdf->Cell(20,5, utf8_decode("Dirección : "),0,1,'L',0);

			//$pdf->SetXY(28,55);
			//$pdf->SetFont('Arial','',8); // tamaño letra
			//$pdf->Cell(20,5, $parametro['direccion_cliente'],0,1,'L',0);

			$pdf->SetXY(12,55);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(20,5, utf8_decode("D.N.I. : "),0,1,'L',0);
			$pdf->SetXY(24,55);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(20,5, $parametro['dni_responsable_economico'],0,1,'L',0);

		//Fecha y Hora
			$pdf->SetXY(134,50);
			$pdf->SetFont('Arial','B',8); // tamaño letra
			$pdf->Cell(36,5, utf8_decode("Fecha de Emisión "),0,1,'L',0);
			$pdf->SetXY(170,50);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(36,5, ": ".$parametro['fecha_emision'],0,1,'L',0);
		//Cuadro de Detalle de Factura
			$pdf->SetLineWidth(0.50);
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,70,186,180,'');

			$pdf->SetLineWidth(0.25);
			$pdf->SetFillColor(220,220,220); //fondo
			
			$pdf->SetXY(12,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"CANTIDAD",1,1,'C',1);
			
			$pdf->SetXY(37,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(111,8,utf8_decode("DESCRIPCIÓN"),1,1,'C',1);
			
			$pdf->SetXY(148,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"PREC. UNIT.",1,1,'C',1);
			
			$pdf->SetXY(173,70);
			$pdf->SetFont('Arial','',7); // tamaño letra
			$pdf->Cell(25,8,"IMPORTE",1,1,'C',1);
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(12,78,25,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(37,78,111,172,'');
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->Rect(148,78,25,172,'');


			$i=0;
			$cy=78;
			

			while ($i<sizeof($parametro['item'])) {
				$cantidad = 1;
				$cx=12;

				$concepto =trim($parametro['item'][$i]['concepto']);
				$posicion_coincidencia = strpos($concepto, 'MORA');
				$strlen = strlen($concepto);

				$alto_y =7;

				$pdf->SetXY($cx,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y, $cantidad,0,0,'C',0);

				$pdf->SetXY($cx+26,$cy);
				$pdf->SetFont('Arial','',7); // tamaño letra			
				 
				
				if($posicion_coincidencia === false){
					if($strlen>=68){
						$alto_y_2 = 3.5;
					}
					else{
						$alto_y_2 = 7;
					}	
					$pdf->MultiCell(109,$alto_y_2,$concepto,0,'L',0);
				}
				else{
					$pdf->Cell(109,$alto_y,$concepto,0,0,'L',0);
				}

				//PREC. UNIT

				$pdf->SetXY($cx+136,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y,$parametro['item'][$i]['monto'],0,0,'C',0);

				//IMPORTE	
				$pdf->SetXY($cx+161,$cy);
				$pdf->SetFont('Arial','',8); // tamaño letra
				$pdf->Cell(25,$alto_y,$parametro['item'][$i]['monto'],0,0,'C',0);

				//if ($this->modulo == "matricula") {
				//	$cy=$cy+6;

				//	$pdf->SetXY($cx+35,$cy);
				//	$pdf->SetFont('Arial','',8); // tamaño letra
				//	$pdf->Cell(25,$alto_y,$parametro['info_operacion'][$i],1,0,'L',0);
				//}

				$cy=$cy+$alto_y;

				$i++;
			} 

			//if ($this->modulo=="matricula") {
			//	$pdf->SetXY($cx+30,$cy);
			//	$pdf->SetFont('Arial','',8); // tamaño letra
			//	$pdf->Cell(25,7,"ALUMNO: ".strtoupper($parametro['nombre_alumno']),0,0,'L',0);
			//}
			//Informacion Boleta y banco


		//SUBTOTALES

			$pdf->SetXY(148,250);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "Sub Total",1,1,'C',1);
			$pdf->SetXY(148,256);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "I.G.V",1,1,'C',1);
			$pdf->SetXY(148,262);
			$pdf->SetFont('Arial','',8); // tamaño letra
			$pdf->Cell(25,7, "TOTAL",1,1,'C',1);

			$pdf->SetXY(173,250);
			$pdf->Cell(25,6,$parametro['monto_total'],1,1,'R',0);
			$pdf->SetXY(173,250);
			$pdf->Cell(5,6, "S/",0,1,'R',0);

			$pdf->SetXY(173,256);
			$pdf->Cell(25,6, "0.00",1,1,'R',0);
			$pdf->SetXY(173,256);
			$pdf->Cell(5,6, "S/",0,1,'R',0);

			$pdf->SetXY(173,262);
			$pdf->Cell(25,7, $parametro['monto_total'],1,1,'R',0);
			$pdf->SetXY(173,262);
			$pdf->Cell(5,7, "S/",0,1,'R',0);

			$pdf->SetLineWidth(0.50);
			$pdf->Rect(148,250,50,19,"");

			//Firma
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(80,270);
			$pdf->Cell(150,10,$parametro['digest_value'],0,1,'L',0);

			//Aviso
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(20,280);
			$pdf->Cell(170,10,"Representacion impresa de la boleta de venta electronica. Puede verificarla utilizando su clave SOL",1,1,'C',0);

		$dir=$_SERVER['DOCUMENT_ROOT'];

		$pdf->Output();
	}

	public function get_control_pagos1($secuencia)
	{
		$this->utili->campos = "*";
		$this->utili->tabla = "control_pagos1_vn";
		$this->utili->condicion = "where secuencia=".$secuencia."";
		$rs_pago = $this->utili->get_datos_condicion($this->conexion);
		
		return $rs_pago;
		
	}

	public function get_nota_credito($serie,$numero)
	{
		$this->utili->campos = "*";
		$this->utili->tabla = "nota_credito";
		$this->utili->condicion = "where serie='".$serie."' AND numero=".$numero."";
		$rs_nc = $this->utili->get_datos_condicion($this->conexion);
		
		return $rs_nc;
		
	}

	public function actualiza_estado_pdf($ConeEdusysNet,$estado,$seriedoc,$numdoc,$razon_social){
		//En esta parte solo es necesario la condicion de numero de boleta pero la tabla puede variar
		//en el caso de que existan varias series el numero de boleta no sera el PrimaryKey
		$sql = "UPDATE control_pagos1_vn SET std_pdf = '".$estado."' 
					WHERE cod_raz_social = '".$razon_social."' and tipo_documento = '07' and serie_documento = '".$seriedoc."' and num_documento = '".$numdoc."'";
		$result = $ConeEdusysNet->SelectLimit($sql) or die($ConeEdusysNet->ErrorMsg());
	}

	public function actualizar_boleta_academia()
	{
		$this->api_service = new conexion_bd();
		$this->conectar_bd = $this->api_service->conex("sis_von_academia");		

		//CONSULTA PARA OBTENER EL MAXIMO NÚMERO DE DOCUMENTO
		$consulta = "UPDATE control_pagos1_vn 
							SET std_sunat = '".$this->std_sunat."', 
								fecha_emision = '".$this->fecha_emision."', 
								digest_value ='".$this->digest_value."', 
								sent_email = 'PE' 
					WHERE tipo_documento='07' AND cod_raz_social='10' 
							AND CAST(num_documento AS INT) = ".$this->num_documento;
		
		pg_query($consulta) or die("Error query ".pg_last_error());			
		pg_close($this->conectar_bd);
		
	}

	public function actualizar_boleta_von_colegio($tipodoc,$razon_social)
	{
		$this->api_service = new conexion_bd();
		$this->conectar_bd = $this->api_service->conex("sis_von_academia");		

		//CONSULTA PARA OBTENER EL MAXIMO NÚMERO DE DOCUMENTO
		$consulta = "UPDATE control_pagos1_vn 
							SET std_sunat = '".$this->std_sunat."', 
								fecha_emision = '".$this->fecha_emision."', 
								digest_value ='".$this->digest_value."', 
								sent_email = 'PE' 
					WHERE tipo_documento='".$tipodoc."' AND cod_raz_social='".$razon_social."' 
							AND CAST(num_documento AS INT) = ".$this->num_documento;
		
		pg_query($consulta) or die("Error query ".pg_last_error());			
		pg_close($this->conectar_bd);
		
	}


}
?>