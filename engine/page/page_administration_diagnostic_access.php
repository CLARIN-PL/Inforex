<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_diagnostic_access extends CPage{

	function execute(){
		global $config;
		$items = array();

        $validatorAjax = new PageAccessValidator($config->path_engine, "ajax");
        $validatorAjax->process();
        $items = array_merge($items, $validatorAjax->items);

        $validatorPage = new PageAccessValidator($config->path_engine, "page");
        $validatorPage->process();
        $items = array_merge($items, $validatorPage->items);

        $this->set("items", $items);
	}
	
}