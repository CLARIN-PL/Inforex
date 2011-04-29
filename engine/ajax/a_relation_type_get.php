<?php
class Ajax_relation_type_get extends CPage {
	
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
		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="annotation_set"){
			$sql = "SELECT id, name, description FROM relation_types WHERE annotation_set_id={$parent_id}";
		} 
				
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
