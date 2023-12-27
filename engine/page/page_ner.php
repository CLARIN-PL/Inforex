<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_ner extends CPagePublic {

    function __construct(){
        parent::__construct();
        $this->includeJs("libs/lobipanel/js/lobipanel.js");
        $this->includeCss("libs/lobipanel/css/lobipanel.css");
    }

    function execute(){

		$this->set('models', Config::Cfg()->get_liner2_api());
	}
	
}
