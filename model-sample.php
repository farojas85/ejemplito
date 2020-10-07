<?  
include("../../adodb/includes/functions.inc.php");
class Conducta {
	
	//VARIABLES DE CONFIGURACIÓN CONDUCTA 1
	var $secuencia;			var $anomat; 		var $codloc;		var $tipconf;
	
	//VARIABLES DE CONFIGURACIÓN CONDUCTA 2
	var $tipniv;			var $codgraini; 	var $codgrafin;		var $codtipcic;	
	var $codtipevacond;		var $codtipcalc; 	var $calnumlet;		var $cantcomp;		
	var $cantindic;			var $tippromcomp; 	var $tippromfinal;
	
	//VARIABLES DE CATEGORIA CONDUCTA AULA 1
	var $codcatcond;		var $codcicini; 	var $codcicfin;	
	
	//VARIABLES DE CATEGORIA CONDUCTA AULA 2
	var $codcic;		var $codgra; 	var $obscatcond;	
	
	//VARIABLES DE MOVIMIENTO CONDUCTA
	var $fecmovcond;		var $sec_matri_xseccion; 	var $codalu;			var $codcur;	
	var $codpro;			var $feciniseg; 			var $fecfinseg;			var $fecinisusp;		
	var $fecfinsusp;		var $stdcondjust; 			var $feccondjust;
	var $mot_codmotcond;	var $tipcond;				var $codtipamon;
	var $notacond; 			var $comentario;			var $stdmovcond;
	
	//VARIABLES DE SEGUIMIENTO DE CONDUCTA
	var $idseguimiento;		var $estado;				var $descrip_caso;		var $fecreg;
	var $fecha_seg;			var $descrip_detalle;		var $accion;			var $observ;					
	
	function local_listado($ConeEdusysNet){		
		$sql = "SELECT * FROM local ORDER BY codloc DESC ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function tipo_configuracion_listado($ConeEdusysNet){		
		$sql = "SELECT * FROM tipo_configuracion WHERE tipconf not in ('00') ORDER BY tipconf DESC ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function tipo_evaluacion_conducta_listado($ConeEdusysNet){		
		$sql = "SELECT * FROM tipo_evaluacion_conducta WHERE codtipevacond not in (0) ORDER BY codtipevacond ASC ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_listado($ConeEdusysNet){		
		$sql = "SELECT * FROM categoria_conducta WHERE codcatcond not in ('00') ORDER BY codcatcond DESC ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function obtengo_tipo_Configuracion($ConeEdusysNet){
		$sql = "SELECT distinct(tipconf) as tipconf 
				FROM configuracion_conducta1 cc1 INNER 
				JOIN configuracion_conducta2 cc2 ON cc1.secuencia = cc2.secuencia
				WHERE anomat = ".$this->anomat;
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		$data = $rs->Fields('tipconf');
		return $data;
	}
	
	//MANTENIMIENTO CONFIGURACIÓN CONDUCTA 1
	function configuracion_conducta1_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO configuracion_conducta1(secuencia,anomat,codloc,tipconf) VALUES ( %s, %s,%s, %s)",
			GetSQLValueString($this->secuencia, "int"),
			GetSQLValueString($this->anomat, "int"),
			GetSQLValueString($this->codloc, "text"),
			GetSQLValueString($this->tipconf, "text"));
			$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
			return $rs;
	}
	
	//MANTENIMIENTO CONFIGURACIÓN CONDUCTA 2
	function configuracion_conducta2_listado($ConeEdusysNet){		
		$sql = "SELECT secuencia,cc2.tipniv,desniv,codgraini,desgra as desgra_ini,codgrafin,desgra as desgra_fin,
					   cc2.codtipcic,destipcic,cc2.codtipevacond,destipevacond,cc2.codtipcalc,destipcalc,
					   calnumlet,cantcomp,cantindic,tippromcomp,tippromfinal
				FROM configuracion_conducta2 cc2 INNER
				JOIN nivel n ON cc2.tipniv=n.tipniv
				JOIN grados g ON cc2.codgraini=g.codgra
				JOIN tipo_evaluacion te ON cc2.codtipcic=te.codtipcic
				JOIN tipo_evaluacion_conducta tec ON cc2.codtipevacond=tec.codtipevacond
				JOIN tipo_calculo tc ON cc2.codtipcalc=tc.codtipcalc 
				ORDER BY secuencia,tipniv,codgraini,codgrafin DESC ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function configuracion_conducta2_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO configuracion_conducta2 (secuencia,tipniv,codgraini,codgrafin,codtipcic,codtipevacond,codtipcalc,calnumlet,cantcomp,cantindic,tippromcomp,tippromfinal) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->tipniv, "text"),
					   GetSQLValueString($this->codgraini, "int"),
					   GetSQLValueString($this->codgrafin, "int"),
					   GetSQLValueString($this->codtipcic, "text"),
					   GetSQLValueString($this->codtipevacond, "int"),
					   GetSQLValueString($this->codtipcalc, "int"),
					   GetSQLValueString($this->calnumlet, "text"),
					   GetSQLValueString($this->cantcomp, "int"),
					   GetSQLValueString($this->cantindic, "int"),
					   GetSQLValueString($this->tippromcomp, "text"),
					   GetSQLValueString($this->tippromfinal, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function configuracion_conducta2_actualizar($ConeEdusysNet){
		$sql = sprintf("UPDATE configuracion_conducta2 
						SET codtipcic=%s,codtipevacond=%s,codtipcalc=%s,calnumlet=%s,cantcomp=%s,cantindic=%s,tippromcomp=%s,tippromfinal=%s 
						WHERE secuencia=%s and tipniv=%s and codgraini=%s and codgrafin=%s",
					   GetSQLValueString($this->codtipcic, "text"),
					   GetSQLValueString($this->codtipevacond, "int"),
					   GetSQLValueString($this->codtipcalc, "int"),
					   GetSQLValueString($this->calnumlet, "text"),
					   GetSQLValueString($this->cantcomp, "int"),
					   GetSQLValueString($this->cantindic, "int"),
					   GetSQLValueString($this->tippromcomp, "text"),
					   GetSQLValueString($this->tippromfinal, "text"),
					   GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->tipniv, "text"),
					   GetSQLValueString($this->codgraini, "int"),
					   GetSQLValueString($this->codgrafin, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function configuracion_conducta2_eliminar($ConeEdusysNet){
		$sql = sprintf("DELETE FROM configuracion_conducta2 WHERE secuencia=%s and tipniv=%s and codgraini=%s and codgrafin=%s",
                       GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->tipniv, "text"),
					   GetSQLValueString($this->codgraini, "int"),
					   GetSQLValueString($this->codgrafin, "int"));
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	//MANTENIMIENTO CATEGORIA CONDUCTA AULAS 1
	function categoria_conducta_aulas1_listado($ConeEdusysNet){		
		$sql = "SELECT secuencia,cca1.codloc,desloc,anomat,cca1.tipconf,destipconf,cca1.codcatcond,descatcond,
       				   cca1.tipniv,desniv,codgraini,codgrafin,cca1.codcicini,cca1.codcicfin
				FROM categoria_conducta_aulas1 cca1 INNER
				JOIN local l ON cca1.codloc=l.codloc
				JOIN tipo_configuracion tc ON cca1.tipconf=tc.tipconf
				JOIN categoria_conducta cc ON cca1.codcatcond=cc.codcatcond
				JOIN nivel n ON cca1.tipniv=n.tipniv
				ORDER BY secuencia,tipniv,codgraini,codgrafin,codcicini,codcicfin DESC  ";
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_aulas1_insertar($ConeEdusysNet){
		echo $sql = sprintf("INSERT INTO categoria_conducta_aulas1 (secuencia,codloc,anomat,tipconf,codcatcond,tipniv,codgraini,codgrafin,codcicini,codcicfin) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codloc, "text"),
					   GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->tipconf, "text"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString($this->tipniv, "text"),
					   GetSQLValueString($this->codgraini, "int"),
					   GetSQLValueString($this->codgrafin, "int"),
					   GetSQLValueString($this->codcicini, "text"),
					   GetSQLValueString($this->codcicfin, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_aulas1_actualizar($ConeEdusysNet){
		$sql = sprintf("UPDATE categoria_conducta_aulas1 
						SET codloc=%s,anomat=%s,tipconf=%s,codcatcond=%s,tipniv=%s,codgraini=%s,codgrafin=%s,codcicini=%s,codcicfin=%s 
						WHERE secuencia=%s",
					   GetSQLValueString($this->codloc, "text"),
					   GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->tipconf, "text"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString($this->tipniv, "text"),
					   GetSQLValueString($this->codgraini, "int"),
					   GetSQLValueString($this->codgrafin, "int"),
					   GetSQLValueString($this->codcicini, "text"),
					   GetSQLValueString($this->codcicfin, "text"),
					   GetSQLValueString($this->secuencia, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_aulas1_eliminar($ConeEdusysNet){
		$sql = sprintf("DELETE FROM categoria_conducta_aulas1 WHERE secuencia=%s",
                       GetSQLValueString($this->secuencia, "int"));
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	//MANTENIMIENTO CATEGORIA CONDUCTA AULAS 2
	function categoria_conducta_aulas2_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO categoria_conducta_aulas2 (secuencia,codcic,codgra,codcatcond,obscatcond) VALUES (%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->codgra, "int"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString(utf8_decode($this->obscatcond), "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_aulas2_actualizar($ConeEdusysNet){
		$sql = sprintf("UPDATE categoria_conducta_aulas2 
						SET codcatcond=%s,obscatcond=%s 
						WHERE secuencia=%s and codcic=%s and codgra=%s",
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString(utf8_decode($this->obscatcond), "text"),
					   GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->codgra, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function categoria_conducta_aulas2_eliminar($ConeEdusysNet){
		$sql = sprintf("DELETE FROM categoria_conducta_aulas2 
						WHERE secuencia=%s and codcic=%s and codgra=%s and codcatcond=%s",
                       GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->codgra, "int"),
					   GetSQLValueString($this->codcatcond, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	//MANTENIMIENTO MOVIMIENTO CONDUCTA
	function movimiento_conducta_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO movimiento_conducta (secuencia,codloc,anomat,codcic,fecmovcond,sec_matri_xseccion,codalu,codcur,codpro,feciniseg,fecfinseg,fecinisusp,fecfinsusp,stdcondjust,feccondjust,mot_codmotcond,codcatcond,tipcond,codtipamon,notacond,comentario,stdmovcond) 
						VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codloc, "text"),
					   GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->fecmovcond, "text"),
					   GetSQLValueString($this->sec_matri_xseccion, "int"),
					   GetSQLValueString($this->codalu, "text"),
					   GetSQLValueString($this->codcur, "text"),
					   GetSQLValueString($this->codpro, "text"),
					   GetSQLValueString($this->feciniseg, "text"),
					   GetSQLValueString($this->fecfinseg, "text"),
					   GetSQLValueString($this->fecinisusp, "text"),
					   GetSQLValueString($this->fecfinsusp, "text"),
					   GetSQLValueString($this->stdcondjust, "text"),
					   GetSQLValueString($this->feccondjust, "text"),
					   GetSQLValueString($this->mot_codmotcond, "text"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString($this->tipcond, "int"),
					   GetSQLValueString($this->codtipamon, "text"),
					   GetSQLValueString($this->notacond, "int"),
					   GetSQLValueString(utf8_decode($this->comentario), "text"),
					   GetSQLValueString($this->stdmovcond, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function movimiento_conducta_actualizar_pre($ConeEdusysNet){
		$sql = sprintf("UPDATE movimiento_conducta 
						SET codcur=%s,codpro=%s,
						feciniseg=%s,fecfinseg=%s,
						fecinisusp=%s,fecfinsusp=%s,
						stdcondjust=%s,feccondjust=%s,
						mot_codmotcond=%s,codcatcond=%s,
						tipcond=%s,codtipamon=%s,
						notacond=%s,comentario=%s,stdmovcond=%s
					    WHERE secuencia=%s and codloc=%s and anomat=%s and codcic=%s and fecmovcond=%s and sec_matri_xseccion=%s and codalu=%s",
					   GetSQLValueString($this->codcur, "text"),
					   GetSQLValueString($this->codpro, "text"),
					   GetSQLValueString($this->feciniseg, "text"),
					   GetSQLValueString($this->fecfinseg, "text"),
					   GetSQLValueString($this->fecinisusp, "text"),
					   GetSQLValueString($this->fecfinsusp, "text"),
					   GetSQLValueString($this->stdcondjust, "text"),
					   GetSQLValueString($this->feccondjust, "text"),
					   GetSQLValueString($this->mot_codmotcond, "text"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString($this->tipcond, "int"),
					   GetSQLValueString($this->codtipamon, "text"),
					   GetSQLValueString($this->notacond, "int"),
					   GetSQLValueString(utf8_decode($this->comentario), "text"),
					   GetSQLValueString($this->stdmovcond, "int"),
					   GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->codloc, "text"),
					   GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->fecmovcond, "text"),
					   GetSQLValueString($this->sec_matri_xseccion, "int"),
					   GetSQLValueString($this->codalu, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function movimiento_conducta_actualizar($ConeEdusysNet){
		$sql = sprintf("UPDATE movimiento_conducta 
						SET codcur=%s,codpro=%s,
						feciniseg=%s,fecfinseg=%s,
						fecinisusp=%s,fecfinsusp=%s,
						stdcondjust=%s,feccondjust=%s,codcic=%s,
						mot_codmotcond=%s,codcatcond=%s,
						tipcond=%s,codtipamon=%s,
						notacond=%s,comentario=%s
					    WHERE secuencia=%s",
					   GetSQLValueString($this->codcur, "text"),
					   GetSQLValueString($this->codpro, "text"),
					   GetSQLValueString($this->feciniseg, "text"),
					   GetSQLValueString($this->fecfinseg, "text"),
					   GetSQLValueString($this->fecinisusp, "text"),
					   GetSQLValueString($this->fecfinsusp, "text"),
					   GetSQLValueString($this->stdcondjust, "text"),
					   GetSQLValueString($this->feccondjust, "text"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->mot_codmotcond, "text"),
					   GetSQLValueString($this->codcatcond, "text"),
					   GetSQLValueString($this->tipcond, "int"),
					   GetSQLValueString($this->codtipamon, "text"),
					   GetSQLValueString($this->notacond, "int"),
					   GetSQLValueString(utf8_decode($this->comentario), "text"),
					   GetSQLValueString($this->secuencia, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function movimiento_conducta_eliminar_pre($ConeEdusysNet){
		$sql = sprintf("DELETE FROM movimiento_conducta 
						WHERE secuencia=%s and stdmovcond=%s",
                       GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->stdmovcond, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function movimiento_conducta_eliminar($ConeEdusysNet){
		$sql = sprintf("DELETE FROM movimiento_conducta 
						WHERE secuencia=%s",
                       GetSQLValueString($this->secuencia, "int")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	var $per_evaluada; var $amonestacion; var $cat_conducta; var $tip_cond; var $mot_cond; var $fechregi; 
	 var $fechsuce;  var $observacion; var $con_1; var $con_2; var $con_3; var $con_4; var $con_5; var $con_6; var $con_7;
	  var $semana;
	
	function conducta_alumno_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO conducta_alumno (anomat, secuencia_xaula, per_evaluar, ciclo, codalu, 
           											 amonestacion, cat_conducta, tipo_cond, mot_cond, fecha_registro, 
            										fecha_suceso, observacion, con_1, con_2, con_3, con_4, con_5, 
            										con_6, con_7,semana,codpro) 
						VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->secuencia, "int"),
					   GetSQLValueString($this->per_evaluada, "int"),
					   GetSQLValueString($this->codcic, "text"),
					   GetSQLValueString($this->codalu, "text"),
					   GetSQLValueString($this->amonestacion, "text"),
					   GetSQLValueString($this->cat_conducta, "text"),
					   GetSQLValueString($this->tip_cond, "int"),
					   GetSQLValueString($this->mot_cond, "text"),
					   GetSQLValueString($this->fechregi, "date"),
					   GetSQLValueString($this->fechsuce, "date"),
					   GetSQLValueString($this->observacion, "text"),
					   GetSQLValueString($this->con_1, "text"),
					   GetSQLValueString($this->con_2, "text"),
					   GetSQLValueString($this->con_3, "text"),
					   GetSQLValueString($this->con_4, "text"),
					   GetSQLValueString($this->con_5, "text"),
					   GetSQLValueString($this->con_6, "text"),
					   GetSQLValueString($this->con_7, "text"),
					   GetSQLValueString($this->semana, "int"),
					   GetSQLValueString($this->codpro, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	var $nota;
	
	function  consolidado_notas_actualizar2($ConeEdusysNet){
						
					  $updateSQL = sprintf("UPDATE curso_xalumno2
					   SET  nota  = %s
					   WHERE  ( curso_xalumno2.anomat   = %s ) AND
						  ( curso_xalumno2.secuencia   = %s ) AND
						  ( curso_xalumno2.codcic   = %s ) AND
						  ( curso_xalumno2.codcur   = %s ) AND
						  ( curso_xalumno2.codalu   = %s )",
						    GetSQLValueString( $this->nota, "int"),
							GetSQLValueString( $this->anomat, "int"),
                           GetSQLValueString( $this->secuencia, "text"),
						   GetSQLValueString( $this->codcic, "text"),
						   GetSQLValueString( $this->codcur, "text"),
						   GetSQLValueString( $this->codalu, "text"));
        $rs = $ConeEdusysNet->Execute($updateSQL) or die($ConeEdusysNet->ErrorMsg());
        return $rs;	
	}
	
	function  conducta_notas_actualizar($ConeEdusysNet){
						
					  $updateSQL = sprintf("UPDATE conducta_almacena_nota
					   SET  nota  = %s
					   WHERE  ( conducta_almacena_nota.anomat   = %s ) AND
						  ( conducta_almacena_nota.secuencia   = %s ) AND
						  ( conducta_almacena_nota.codcic   = %s ) AND
						  ( conducta_almacena_nota.semana   = %s ) AND
						  ( conducta_almacena_nota.codalu   = %s )",
						    GetSQLValueString( $this->nota, "int"),
							GetSQLValueString( $this->anomat, "int"),
                           GetSQLValueString( $this->secuencia, "text"),
						   GetSQLValueString( $this->codcic, "text"),
						   GetSQLValueString( $this->semana, "int"),
						   GetSQLValueString( $this->codalu, "text"));
        $rs = $ConeEdusysNet->Execute($updateSQL) or die($ConeEdusysNet->ErrorMsg());
        return $rs;	
	}
	
	function  conducta_notas_actualizar_padre($ConeEdusysNet){
						
					  $updateSQL = sprintf("UPDATE conducta_almacena_nota_padre
					   SET  nota  = %s
					   WHERE  ( conducta_almacena_nota_padre.anomat   = %s ) AND
						  ( conducta_almacena_nota_padre.secuencia   = %s ) AND
						  ( conducta_almacena_nota_padre.codcic   = %s ) AND
						  ( conducta_almacena_nota_padre.codalu   = %s )",
						    GetSQLValueString( $this->nota, "numeric"),
							GetSQLValueString( $this->anomat, "int"),
                           GetSQLValueString( $this->secuencia, "text"),
						   GetSQLValueString( $this->codcic, "text"),
						   GetSQLValueString( $this->codalu, "text"));
        $rs = $ConeEdusysNet->Execute($updateSQL) or die($ConeEdusysNet->ErrorMsg());
        return $rs;	
	}
	
	//FICHA DE SEGUIMIENTO DE CONDUCTA
	function seguimiento_conducta_insertar($ConeEdusysNet){
		$sql = sprintf("INSERT INTO seguimiento_conducta (anomat,sec_matri_xseccion,codalu,codpro,descrip_caso,estado,fecreg) 
						VALUES (%s,%s,%s,%s,%s,%s,%s)",
			           GetSQLValueString($this->anomat, "int"),
					   GetSQLValueString($this->sec_matri_xseccion, "int"),
					   GetSQLValueString($this->codalu, "text"),
					   GetSQLValueString($this->codpro, "text"),
					   GetSQLValueString($this->descrip_caso, "text"),
					   GetSQLValueString($this->estado, "text"),
					   GetSQLValueString($this->fecreg, "date")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	
	function seguimiento_conducta_actualizar($ConeEdusysNet){
		$sql = sprintf("UPDATE seguimiento_conducta set descrip_caso= %s, fecreg= %s
						WHERE idseguimiento=%s AND codalu = %s",
			           GetSQLValueString($this->descrip_caso, "text"),
					   GetSQLValueString($this->fecreg, "date"),
					   GetSQLValueString($this->idseguimiento, "int"),
					   GetSQLValueString($this->codalu, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
	function seguimiento_conducta_eliminar($ConeEdusysNet){
		$sql = sprintf("DELETE FROM seguimiento_conducta WHERE idseguimiento=%s AND codalu = %s",   
					   GetSQLValueString($this->idseguimiento, "int"),
					   GetSQLValueString($this->codalu, "text")
					   );
		$rs = $ConeEdusysNet->Execute($sql) or die($ConeEdusysNet->ErrorMsg());
		return $rs;		
	}
}
?>