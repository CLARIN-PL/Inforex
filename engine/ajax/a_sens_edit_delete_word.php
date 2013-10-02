<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_delete_word extends CPage {
	function execute(){
		global $db, $mdb2;
		$name = $_POST['name'];
		$wsd_name = "wsd_" . $name;
		
		$sql = "SELECT * FROM reports_annotations WHERE type=? ";
		$result = $db->fetch_rows($sql, array($wsd_name));
		
		if(count($result)){
			$error_msg = 'Word ' . $name . ' have ' . count($result) . ' annotations';
			throw new Exception($error_msg);
			return;
		}
		
		
		$sql = "DELETE FROM annotation_types WHERE name=? ";
		$db->execute($sql, array($wsd_name));		
		$sql = "DELETE FROM annotation_types_attributes WHERE annotation_type=? ";
		$db->execute($sql, array($wsd_name));		
		return;
	}	
}
?>