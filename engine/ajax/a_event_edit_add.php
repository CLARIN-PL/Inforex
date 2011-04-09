<?php
/**
metoda dodajaca nowy slot do zdarzenia (pusty)
 * 
 */
class Ajax_event_edit_add extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
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
		/*$event_id = intval($_POST['event_id']);
		$type_id = intval($_POST['type_id']);
		//$user_id = intval($user['user_id']);
		$sql = "INSERT INTO reports_events_slots (report_event_id, event_type_slot_id, user_id, creation_time, user_update_id, update_time) " .
				"VALUES ({$event_id}, {$type_id}, {$user['user_id']}, now(),{$user['user_id']}, now() )";*/
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$element_type = $_POST['element_type'];
		
		if ($element_type=="event_group"){
			$sql = 'INSERT INTO event_groups (name, description) VALUES ("'.$name_str.'", "'.$desc_str.'")';
		}
				
		db_execute($sql);
		$last_id = $mdb2->lastInsertID();
		echo json_encode(array("success"=>1, "last_id"=>$last_id));
	}
	
}
?>
