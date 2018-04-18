<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_diagnostic_access extends CPageAdministration {

	function execute(){
		global $config;
		global $user;

		$this->includeJs("libs/bootstrap-sortable/moment.min.js"); // reguired by boostrap-sortable.js
        $this->includeJs("libs/bootstrap-sortable/bootstrap-sortable.js");
        $this->includeCss("libs/bootstrap-sortable/bootstrap-sortable.css");

		$items = array();

        $validatorAjax = new PageAccessValidator($config->path_engine, "ajax");
        $validatorAjax->process();
        $items = array_merge($items, $validatorAjax->items);

        $validatorPage = new PageAccessValidator($config->path_engine, "page");
        $validatorPage->process();
        $items = array_merge($items, $validatorPage->items);

        ChromePhp::log($user['role']);

        $this->set("items", $items);
	}
	
}