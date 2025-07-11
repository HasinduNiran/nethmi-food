<?php 
define('DB_HOST','localhost');
define('DB_USER','nexaraso_nethmi'); 
define('DB_PASS','nexaraso_nethmi');
define('DB_NAME','nexaraso_nethmi');

$link = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if($link->connect_error){ 
die('Connection Failed'.$link->connect_error);
}
?>
