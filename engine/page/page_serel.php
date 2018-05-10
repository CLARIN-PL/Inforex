<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_serel extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $config;		
		
		$autosubmit = isset($_GET['q']);
		$question = isset($_GET['q']) ? str_replace("_", " ", strval($_GET['q'])) : "Jakie miasta leżą w Polsce?";
		
		$this->set('autosubmit', $autosubmit);
		$this->set('question', $question);
	}
}


?>
