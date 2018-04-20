<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 */
class Ajax_lps_validate_xml extends CPage {
	
	function checkPermission(){
		if ( hasRole('loggedin') )
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $config;
	
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));

		if ( strlen(trim($content)) == 0 ){
			return array("errors"=>array(array("line"=>"", "col"=>"", "description"=>"Empty document content")));
		}

		$c = new MyDOMDocument();
		$c->loadXML($content);
		$c->schemaValidate("{$config->path_engine}/resources/lps/lps.xsd");

		return array("errors"=>$c->errors );
	}
	
}
?>
