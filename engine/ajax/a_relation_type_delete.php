<?php
class Ajax_relation_type_delete extends CPage {
	
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

		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		
		if ($element_type=="relation_type"){
			/*$sql = "DELETE FROM event_type_slots " .
					"WHERE event_type_id = {$element_id}";
			db_execute($sql);*/
			$sql = "SELECT * FROM relations WHERE relation_type_id={$element_id} LIMIT 1";
			$result = db_fetch_rows($sql);
			if (count($result)>0){
				echo json_encode(array("error"=>"You cannot delete this relation type. There is at least one existing relation in database.", "error_code"=>"RELATION_TYPE_DELETE_ERROR"));
				return;
			}
			
			
			$sql = "DELETE FROM relation_types WHERE id=$element_id";
			db_execute($sql);
		}
		echo json_encode(array("success"=>1));
	}
	
}
?>
