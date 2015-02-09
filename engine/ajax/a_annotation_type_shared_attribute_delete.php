<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_type_shared_attribute_delete extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$annotation_type_id = intval($_POST['annotation_type_id']);
		$shared_attribute_id = intval($_POST['shared_attribute_id']);
		
		$sql = "DELETE FROM annotation_types_shared_attributes WHERE annotation_type_id=? AND shared_attribute_id=?";
		$db->execute($sql, array($annotation_type_id, $shared_attribute_id));				
		return;
	}
	
}
?>
