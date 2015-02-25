<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_get extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$shared_attribute_id = intval($_POST['shared_attribute_id']);
		
		$sql = "SELECT value, description FROM shared_attributes_enum WHERE shared_attribute_id=?";
				
		$result = $db->fetch_rows($sql, array($shared_attribute_id));
		return $result;
	}
	
}
?>
