<?php session_start(); ?>
<?php

error_reporting(0);
include('includes/config.php');
$pid=intval($_GET['pid']);
$sql = "Delete from tblusers where id=:pid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':pid', $pid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
 
     echo"<script language='javascript'>window.location='manage-users.php'</script>;";
}


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

