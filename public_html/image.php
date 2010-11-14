<?php
chdir('../engine');
require_once("config.php");
require_once("config.local.php");
require_once("MDB2.php");

$sql_log = false;

require_once($config->path_engine . '/include/pear/FirePHPCore/fb.php');
require_once($config->path_engine . '/database.php');

$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);

$row = db_fetch("SELECT * FROM images WHERE id=?", array($id));

if ($row){
	$img = imagecreatefrompng("../secured_data/images/" . $row['id']."_".$row['hash_name']);
	header( "Content-type: image/png" );
	imagepng($img);
}else{
	echo "No image";
}
?> 
