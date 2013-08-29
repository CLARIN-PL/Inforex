<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_sens_edit_add_word extends CPage {
	function execute(){
		global $db, $mdb2;
		$name = $_POST['wordname'];
		$wsd_name = "wsd_" . $name;
		
/*		$sql = " SELECT * FROM annotation_types WHERE name like '" . $wsd_name . "'";
		
		$result = $db->fetch_one($sql);
		if(count($result)){
			$error_msg = 'Word ' . $name . ' alredy exist';
			echo json_encode(array("error"=>$error_msg));
			return;
		}
*/		$sql = "INSERT INTO annotation_types (name, group_id, annotation_subset_id) VALUES (?, 2, 21)";
			
		$db->execute($sql, array($wsd_name));
		
//		print_r($db->mdb2->errorInfo());
		$error = $db->mdb2->errorInfo();
		if(isset($error[0])){
			$error_msg = 'Word ' . $name . ' alredy exist';
			throw new Exception($error_msg);
			return;
		}
		
			
		$sql = "INSERT INTO annotation_types_attributes (annotation_type, name, type) VALUES (?, 'sense', 'radio')";
		$db->execute($sql, array($wsd_name));
		
//		print_r($db->mdb2->errorInfo());
		
		$rows_id = $mdb2->lastInsertID();
		return array("rows_id" => $rows_id);
	}	
}
?>