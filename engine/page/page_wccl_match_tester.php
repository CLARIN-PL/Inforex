<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_wccl_match_tester extends CPage{
	
	var $isSecure = false;
	
	function checkPermission(){
		return true; 
	}
	
	function execute(){
		global $config;
		
		$annotation_types = array();
		$annotation_types[] = "t3_time";
		$annotation_types[] = "t3_date";
		$annotation_types[] = "t3_duration";
		$annotation_types[] = "t3_set";
		$annotation_types[] = "t3_range";
						
		$this->set('corpora', $config->wccl_match_tester_corpora);
		$this->set('annotation_types', $annotation_types);
	}
}


?>
