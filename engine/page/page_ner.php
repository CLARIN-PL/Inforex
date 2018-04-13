<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_ner extends CPagePublic {

	function execute(){
		global $config;

		$this->includeJs("libs/lobipanel/js/lobipanel.js");
		$this->includeCss("libs/lobipanel/css/lobipanel.css");

		$this->set('models', $config->get_liner2_api());
	}
	
}