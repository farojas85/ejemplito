<?php 
// DB credentials.
// define('DB_HOST','localhost');
// define('DB_USER','root');
// define('DB_PASS','');
// define('DB_NAME','wado');
define('DB_HOST','bgpsoa6syperyxgneahk-mysql.services.clever-cloud.com');
define('DB_USER','u5re7qp56uh5x8oi');
define('DB_PASS','iT7dKDLRPBzqKzRl39rU');
define('DB_NAME','bgpsoa6syperyxgneahk');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>