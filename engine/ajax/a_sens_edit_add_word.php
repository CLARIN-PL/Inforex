<?php
class Ajax_sens_edit_add_word extends CPage {
	var $isSecure = false;
	function execute(){
		global $db;
		$name = $_POST['wordname'];
		$wsd_name = "wsd_" . $name;
		
		$sql = " SELECT * FROM annotation_types WHERE name=? ";
		
		$result = $db->fetch_one($sql, array($wsd_name));
		
		if(count($result)){
			$error_msg = 'Word ' . $name . ' alredy exist';
			echo json_encode(array("error"=>$error_msg));
		}
		else{
			$sql = "INSERT INTO annotation_types (name, group_id, annotation_subset_id) VALUES (?, 2, 21)";
			$db->execute($sql, array($wsd_name));
		
			$sql = "INSERT INTO annotation_types_attributes (annotation_type, name, type) VALUES (?, 'sense', 'radio')";
			$db->execute($sql, array($wsd_name));		
		
			echo json_encode(array("success" => 1));
		}
	}	
}
?>