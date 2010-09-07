<?php
class Page_import extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $corpus;
		
		$url = isset($_POST['url']) ? strval($_POST['url']) : null;
		fb($url);
		
		
		$this->set("url", $url);
	}
}


?>
