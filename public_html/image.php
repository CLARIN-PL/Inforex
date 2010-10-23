<?php
//chdir('../engine');
//require_once("config.php");
//require_once("config.local.php");

$img = imagecreatefromjpeg("/home/czuk/nlp/eclipse/workspace/inforex_web/secured_data/images/1_scan.jpg");
		
header( "Content-type: image/jpeg" );
imagepng($img);
?> 
