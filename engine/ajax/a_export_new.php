<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_export_new extends CPage {

	function checkPermission(){
		return hasRole('admin') || hasCorpusRole('export');
	}
	
	function execute(){
		global $corpus, $db;
		$corpus_id = $corpus['id'];
		
		$extractors = $_POST['extractors'];
		$selectors = $_POST['selectors'];
		$description = $_POST['description'];
		$indices = $_POST['indices'];
		
		$attributes = array();
		$attributes['corpus_id'] = $corpus_id;
		$attributes['datetime_submit'] = date("Y-m-d H:i:s");
		$attributes['description'] = strval($description);
		$attributes['extractors'] = strtolower(strval($extractors));
		$attributes['selectors'] = strtolower(strval($selectors));
		$attributes['indices'] = strtolower(strval($indices));
		$attributes['status'] = "new";
		
		fb($attributes);
		
		$db->insert("exports", $attributes);
		
		return array();
	}
	
}
?>
