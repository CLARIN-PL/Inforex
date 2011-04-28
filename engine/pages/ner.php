<?php
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $config;
		
		$models = array();
		$models[1] = "All proper names (trained on Wikinews with context [-1,+1])";
		$models[2] = "First names, surnames, cities, countries and roads (trained on 4 corpora with context [-3,+2])";
		$models[3] = "First names, surnames, cities, countries and roads (trained on 4 corpora with context [-1,+1])";
		$models[4] = "5 types of names with 10 features";
		
		$this->set('models', $models);
	}
	
}


?>
