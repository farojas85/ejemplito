<?php session_start(); ?>
<?php

error_reporting(0);
include('includes/config.php');
$pid=intval($_GET['pid']);
$sql = "Delete from tblplatos where Platoid=:pid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':pid', $pid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
  
    echo"<script language='javascript'>window.location='manage-packages.php'</script>;";
}