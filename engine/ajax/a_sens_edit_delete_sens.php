<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_delete_sens extends CPage {
	function execute(){
		global $db;
		$name = $_POST['name'];
		
		$sql = "SELECT * FROM reports_annotations_attributes WHERE value=? ";
		$result = $db->fetch_rows($sql, array($name));
		
		if(count($result)){
			$error_msg = 'Sens ' . $name . ' is used ' . count($result) . ' time' . (count($result)>1 ? 's' : '');
			throw new Exception($error_msg);
			return;
		}
		
		$sql = "DELETE FROM annotation_types_attributes_enum WHERE value=? ";
		$db->execute($sql, array($name));		
		return;
	}	
}
?>