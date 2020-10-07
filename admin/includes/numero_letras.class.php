<?php
class numerosALetras{

		var $resultado;
		var $antes_soles_despues='Soles';
		var $antes_con_despues='con';
		var $despues = 'centimos';
		var $antes_sin_despues='';
		/*
			Constructor
		*///
		//funcion para dar los ceros en generar nuevo document
		function ContarCeros($texto){
			$newtexto="";
			$ltexto = strlen($texto);
			if( $ltexto == 5){
				$newtexto="00".$texto;
			}
			elseif( $ltexto == 6){
				$newtexto="0".$texto;
			}
			else{
				$newtexto=$texto;
			}
			return $newtexto;
		}
		function redondear_dos_decimal($valor) {
		$float_redondeado=round($valor * 100) / 100;
		return $float_redondeado;
		}
		function redondeado($numero, $decimales) {
			$factor = pow(10, $decimales);
			return (round($numero*$factor)/$factor); 
		}
		function __construct($valor=''){
				if(is_numeric($valor))
					return $this->convertir($valor);
				return false;
			}
		/*
			Retorna el valor de la centena que se le envie como parametro.
		*/
		function centenas($centenas){
				$valores = array('Cero','Uno','Dos','Tres','Cuatro','Cinco','Seis',
				'Siete','Ocho','Nueve','Diez','Once','Doce','Trece','Catorce',
				'Quince',20=>'Veinte',30=>'Treinta',40=>'Cuarenta',50=>'Cincuenta',
				60=>'Sesenta',70=>'Setenta',80=>'Ochenta',90=>'Noventa',100=>'Ciento',
				101=>'Quinientos',102=>'SeteCientos',103=>'NoveCientos',104=>'Doscientos',105=>'Trescientos',106=>'Cuatrocientos',107=>'Seiscientos',108=>'Ochocientos');
		
				switch($centenas){
						case '1': return $valores[100]; break;
						case '5': return $valores[101]; break;
						case '7': return $valores[102]; break;
						case '9': return $valores[103]; break;
						case '2': return $valores[104]; break;
						case '3': return $valores[105]; break;
						case '4': return $valores[106]; break;
						case '6': return $valores[107]; break;
						case '8': return $valores[108]; break;
						default: return $valores[$VCentena];
					}
			}
		/*
			Retorna el valor de la unidad que se le envie como parametro.
		*/
		function unidades($unidad){
				$valores = array('Cero','un','Dos','Tres','Cuatro','Cinco','Seis',
				'Siete','Ocho','Nueve','Diez','Once','Doce','Trece','Catorce',
				'Quince',20=>'Veinte',30=>'Treinta',40=>'Cuarenta',50=>'Cincuenta',
				60=>'Sesenta',70=>'Setenta',80=>'Ochenta',90=>'Noventa',100=>'Ciento',
				101=>'Quinientos',102=>'Setecientos',103=>'Novecientos'
				);
			
				return $valores[$unidad];
			}
	
		/*
			Retorna el valor de la decena que se le envie como parametro
		*/
		function decenas($decena){
				$valores = array('Cero','Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete',
				'Ocho','Nueve','Diez','Once','Doce','Trece','Catorce','Quince',20=>'Veinte',30=>'Treinta',
				40=>'Cuarenta',50=>'Cincuenta',60=>'Sesenta',70=>'Setenta',80=>'Ochenta',90=>'Noventa',
				100=>'Ciento',101=>'Quinientos',102=>'Setecientos',103=>'Novecientos');
		
				return $valores[$decena];
			}
	
	
		function evalua($valor){
				if($valor==0)
					return 'Cero';
					
				$decimales = 0;
				$letras = '';
				while($valor!=0){
						// Validamos si supera los 100 millones
						if($valor>=1000000000)
							return 'L&iacute;mite de aplicaci&oacute;n exedido.';
						
						//Centenas de MillÃ³n
						if (($valor<1000000000) and ($valor>=100000000)){
								if ((intval($valor/100000000)==1) and (($valor-(intval($valor/100000000)*100000000))<1000000))
										$letras.=(string)'cien millones ';
									else{
										$letras.=$this->centenas(intval($valor/100000000));
										If ((intval($valor/100000000)<>1) and (intval($valor/100000000)<>5) and (intval($valor/100000000)<>7) and (intval($valor/100000000)<> 9))
												$letras.=(string)'Ciento ';
											else
												$letras.=(string)' ';
									}
								$valor=$valor-(Intval($valor/100000000)*100000000);
							}
			
						//Decenas de MillÃ³n
						if(($valor<100000000) and ($valor>=10000000)){
								if(intval($valor/1000000)<16){
										$tempo=$this->decenas(intval($valor/1000000));
										$letras.=(string)$tempo;
										$letras.=(string)' millones ';
										$valor=$valor-(intval($valor/1000000)*1000000);
									}else{
										$letras.=$this->decenas(intval($valor/10000000)*10);
										$valor=$valor-(intval($valor/10000000)*10000000);
										if ($valor>1000000)
											$letras.=$letras.' y ';
									}
							}
			
						//Unidades de Millon
						if(($valor<10000000) and ($valor>=1000000)){
									$tempo=(intval($valor/1000000));
									if($tempo==1)
											$letras.=(string)' un mill&oacute;n ';
										else{
											$tempo= unidades(intval($valor/1000000));
											$letras.=(string)$tempo;
											$letras.=(string)" millones ";
										}
								$valor=$valor-(intval($valor/1000000)*1000000);
							}
			
						//Centenas de Millar
						if(($valor<1000000) and ($valor>=100000)){
								$tempo=(intval($valor/100000));
								$tempo2=($valor-($tempo*100000));
								if(($tempo==1) and ($tempo2<1000))
										$letras.=(string) 'cien mil ';
									else{
										$tempo=$this->centenas(intval($valor/100000));
										$letras.=(string)$tempo;
										$tempo=(intval($valor/100000));
										if(($tempo <> 1) and ($tempo <> 5) and ($tempo <> 7) and ($tempo <> 9))
												$letras.=(string)'Ciento ';
											else
												$letras.=(string)' ';
									}
								$valor=$valor-(intval($valor/100000)*100000);
							}
			
						//Decenas de Millar
						if(($valor<100000) and ($valor>=10000)){
								$tempo=(intval($valor/1000));
								if($tempo<16){
										$tempo=$this->decenas(intval($valor/1000));
										$letras.=(string)$tempo;
										$letras.=(string)' mil ';
										$valor=$valor-(intval($valor/1000)*1000);
									}else{
										$tempo=$this->decenas(intval($valor/10000)*10);
										$letras.=(string) $tempo;
										$valor=$valor-(intval(($valor/10000))*10000);
										if($valor>1000)
												$letras.=(string)' y ';
											else
												$letras.=(string)' mil ';
									}
							}
			
			
						//Unidades de Millar
						if(($valor<10000) and ($valor>=1000)){
								$tempo=intval($valor/1000);
								if($tempo==1)
									$letras.=(string)'';//'un';
									else{
										$tempo=$this->unidades(intval($valor/1000));
										$letras.=(string) $tempo;
									}
								$letras.=(string)' mil ';
								$valor=$valor-(intval($valor/1000)*1000);
							}
			
						//Centenas
						if(($valor<1000) and ($valor>99)){
								if ((intval($valor/100)==1) and (($valor-(intval($valor/100)*100))<1))
										$letras.='cien ';
									else{
										$temp=(intval($valor/100));
										$l2=$this->centenas($temp);
										$letras.=(string) $l2;
										if((intval($valor/100)<>1) and (intval($valor/100)<>5) and (intval($valor/100)<>7) and (intval($valor/100)<>9) and (intval($valor/100)<>3)and (intval($valor/100)<>4)and (intval($valor/100)<>6) and (intval($valor/100)<>8) and (intval($valor/100)<>2))
												$letras.='Ciento ';
											else
												$letras.=(string)' ';
									}
								$valor=$valor-(intval($valor/100)*100);
							}
			
						//Decenas
						if(($valor<100) and ($valor>9)){
								if($valor<16){
										$tempo=$this->decenas(intval($valor));
										$letras.=$tempo;
										$Numer =$valor-Intval($valor);
									}else{
										$tempo=$this->decenas(Intval(($valor/10))*10);
										$letras.=(string)$tempo;
										$valor=$valor-(Intval(($valor/10))*10);
										if($valor>0.99)
											$letras.=(string)' y ';
									}
							}
			
						//Unidades
						if(($valor<10) And ($valor>0.99)){
								$tempo=$this->unidades(intval($valor));
								$letras.=(string)$tempo;
								$valor=$valor-intval($valor);
							}
			
						//Decimales
						if($decimales<=0)
							if(($letras <> "Error en Conversi&oacute;n a Letras") and (strlen(trim($letras))>0))
								$letras .= (string) ' ';
					return $letras;
				}
			}
		/*
			Retorna el texto de el numero enviado como parametros
		*/
	  function convertir($valor){
				ob_start();
				$tt = $valor;
				$valor = intval($tt);
				$decimales = $tt - intval($tt);
				$decimales=round($decimales,2);
				$decimales = substr($decimales,strpos($decimales,'.'),strlen($decimales))*(100);
				$decimales= round($decimales);

				//Parte entera
				print $this->evalua($valor);
				//**print "$this->antes_soles_despues";
				//Parte Decimal
				if($decimales){
						print " $this->antes_con_despues ";
						print " $decimales/100";
						//**print $this->evalua($decimales);
						//**print " $this->despues";
					}else{
						//**print " $this->antes_sin_despues ";
						print " con 00/100 ";
					}
				return $this->resultado = $texto = ob_get_clean();
			}
	}
?>