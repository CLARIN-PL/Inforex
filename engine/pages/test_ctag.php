<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_test_ctag extends CPage{

//	var $isSecure = false;
	
	function execute(){
		// todo - make local
		// for jquery-editable-select
		// https://github.com/indrimuska/jquery-editable-select
		$this->includeJs("http://rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.js");
        $this->includeCss("http://rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.css");
	}
}


?>
