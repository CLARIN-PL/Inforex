<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_ner extends CPagePublic {

    function __construct(){
        // LobiPanel registers on DOM ready, before page_ner.js initializes panels.
        $this->includeJs("libs/lobipanel/js/lobipanel.js");
        $this->includeCss("libs/lobipanel/css/lobipanel.css");
        parent::__construct();
    }

    function execute(){

		$this->set('models', Config::Cfg()->get_liner2_api());
	}
	
}
