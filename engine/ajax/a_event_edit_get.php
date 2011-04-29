<?php
class Ajax_event_edit_get extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('editor_schema_events'))
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
		
		if ($parent_type=="event_group"){
			$sql = "SELECT event_type_id AS id, name, description FROM event_types WHERE event_group_id={$parent_id}";
		} 
		else if ($parent_type=="event_type"){
			$sql = "SELECT event_type_slot_id AS id, name, description FROM event_type_slots WHERE event_type_id={$parent_id}";
		}
				
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
