<?php
class Ajax_relation_type_update extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('editor_schema_relations'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$element_id = intval($_POST['element_id']);
		
		$element_type = $_POST['element_type'];
		
		if ($element_type=="relation_type")
			$sql = "UPDATE relation_types SET name=\"$name_str\", description=\"$desc_str\" WHERE id=$element_id";
		db_execute($sql);
		echo json_encode(array("success"=>1));
	}
	
}
?>
