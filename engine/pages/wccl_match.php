<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_wccl_match extends CPage{
	
	var $isSecure = true;

	function checkPermission(){
		return isCorpusOwner(); 
	}
		
	function execute(){
		global $config;		
	}
}


?>
