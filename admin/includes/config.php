<?php 
// DB credentials.

define('DB_HOST','127.0.0.1');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','wado');
// Establish database connection.
try
{
	$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
	exit("Error: " . $e->getMessage());
}

/*
// simple conexion a la base de datos
	class conectar{
		private $servidor="localhost";
		private $usuario="root";
		private $password="";
		private $bd="wado";

		public function conexion(){
			$dbh=mysqli_connect($this->servidor,
									 $this->usuario,
									 $this->password,
									 $this->bd);
			return $dbh;
		}
	}
*/
?>