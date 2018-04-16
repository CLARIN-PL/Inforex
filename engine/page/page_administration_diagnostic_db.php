<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_diagnostic_db extends CPageAdministration {

	function execute(){
		global $db;
		
		$variables = array();
		$sql = "SHOW GLOBAL STATUS LIKE '%stmt%'";
		
		$rows = $db->fetch_rows($sql);
		foreach ($rows as $row){
			$variables[] = array("name"=>$row['Variable_name'], "value"=>$row['Value']);
		}
		
		
		$this->set("variables", $variables);
	}
	
}