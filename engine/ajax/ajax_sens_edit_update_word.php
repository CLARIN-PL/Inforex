<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_update_word extends CPage {
	function execute(){
		global $db;
		ChromePhp::log($_POST);
		$new_name = $_POST['newwordname'];
		$wsd_new_name = "wsd_" . $new_name;
		$id = $_POST['id'];
		$old_name = $_POST['oldwordname'];
		$sql = " SELECT annotation_type_id FROM annotation_types_attributes WHERE id = ?";
		$annotation_type_id = $db->fetch_one($sql, array($id));

		$sql = " UPDATE annotation_types SET name= ? WHERE annotation_type_id = ? ";

		$db->execute($sql, array($wsd_new_name, $annotation_type_id));
		
		$sql = " SELECT value FROM annotation_types_attributes_enum WHERE annotation_type_attribute_id = ? ";
		$rows = $db->fetch_rows($sql, array($id));
		$old_name_length = strlen($old_name);
		foreach($rows as $row){
			$old_sens_name = $row['value'];
			$sens_num = substr($old_sens_name,$old_name_length);
			$new_sens_name = $new_name . $sens_num;
			
			$sql = " UPDATE annotation_types_attributes_enum SET value='" . 
					$new_sens_name . 
					"' WHERE value='" . 
					$old_sens_name . "' ";
		
			$db->execute($sql);
		}
			
		return array("sens_num" => $id);
	}	
}