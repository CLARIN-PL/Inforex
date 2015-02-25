<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_annotation_types_get extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$shared_attribute_id = intval($_POST['shared_attribute_id']);
		
		$sql = "SELECT ant.annotation_type_id, ant.name, atsa.shared_attribute_id " .
				"FROM annotation_types ant " .
				"LEFT JOIN annotation_types_shared_attributes atsa " .
				"ON ant.annotation_type_id = atsa.annotation_type_id " .
				"AND atsa.shared_attribute_id = ? " .
				"ORDER BY ant.name";
				
		$result = $db->fetch_rows($sql, array($shared_attribute_id));
		return $result;
	}
	
}
?>
