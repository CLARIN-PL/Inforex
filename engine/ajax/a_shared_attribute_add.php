<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_add extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$name_str = strval($_POST['name_str']);
		$type_str = strval($_POST['type_str']);
		$desc_str = strval($_POST['desc_str']);
		
		$sql = "INSERT INTO shared_attributes (name, type, description) VALUES (?, ?, ?)";
		$db->execute($sql, array($name_str, $type_str, $desc_str));				
		$last_id = $db->last_id();
		return array("last_id"=>$last_id);
	}
	
}
?>
