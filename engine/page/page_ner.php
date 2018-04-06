<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $config;						
		$this->set('models', $config->get_liner2_api());
	}
	
}


?>
