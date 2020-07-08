<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
$PATH_CONFIG = "../engine";
$PATH_CONFIG_LOCAL = "../config";

require_once("$PATH_CONFIG/config.php");
if ( file_exists("$PATH_CONFIG_LOCAL/config.local.php") ) {
	require_once("$PATH_CONFIG_LOCAL/config.local.php");
}
require_once($config->get_path_engine() . '/include.php');

/********************************************************************8
 * Połączenie z bazą danych (nowy sposób)
 */
$db=new Database($config->dsn);
$db->set_encoding('utf-8');
/********************************************************************/
ob_start();

$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
$row = $db->fetch("SELECT * FROM images WHERE id=?", array($id));
$width = isset($_GET['width']) ? intval($_GET['width']) : 0;

if ($row){
	$filename = $config->path_secured_data . "/images/" . $row['id']."_".$row['hash_name'];
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	
	if ($ext == "png" )
		$img = imagecreatefrompng($filename);
	else
		$img = imagecreatefromjpeg($filename);
	
	if ($width){
		$size = getimagesize($filename);
		
		$img_width = $size[0];
		$img_height = $size[1];
		$new_width = $width;
		$new_height = $img_height*$new_width/$img_width;
		
		$des = imagecreate($new_width, $new_height);
		imagecopyresampled($des, $img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height);
		$img = $des;
	}
	
	$buffer = trim(ob_get_clean());
	
	if ( $buffer )
		print $buffer;
	else{
		header( "Content-type: image/$ext" );
		imagepng($img);
	}
}else{
	ob_end_clean();
	echo "No image";
}
