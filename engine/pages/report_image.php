<?php

class Page_report_image extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $config;
		$f = fopen($config->path_engine . "/data/sources/corpus_3/xyz.jpg", "r");
		//fread()
		
		header ('Content-Type: ' . $details['mime']);
	}
	
}

?>


