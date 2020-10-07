<?  
	include("../../conexion/ConeEdusysNet.php");
	include("../model/Conducta.php");
	
	$conducta = new Conducta();
	
	$conducta->secuencia = $_POST['secuencia'];
	$conducta->codloc= $_POST['local'];
	$conducta->anomat = $_POST['anomat'];
	$conducta->tipconf = $_POST['tipconf'];
	$conducta->codcatcond = $_POST['codcatcond'];
	$conducta->tipniv = $_POST['tipniv'];
	$conducta->codgraini = $_POST['codgraini'];
	$conducta->codgrafin = $_POST['codgrafin'];
	$conducta->codcicini = $_POST['codcicini'];
	$conducta->codcicfin = $_POST['codcicfin'];
	
	$conducta->categoria_conducta_aulas1_actualizar($ConeEdusysNet);
?>