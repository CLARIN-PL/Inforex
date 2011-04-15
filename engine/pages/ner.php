<?php
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $config;
		
		$models = array();
		$models[1] = "Context [-3,+2]";
		$models[2] = "Context [-1,+1]";
		
		$this->set('models', $models);
	}
	
}


?>
