<?php
/**
metoda dodajaca nowy slot do zdarzenia (pusty)
 * 
 */
class Ajax_report_add_event_slot extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$event_id = intval($_POST['event_id']);
		$type_id = intval($_POST['type_id']);
		//$user_id = intval($user['user_id']);
		
		$sql = "INSERT INTO reports_events_slots (report_event_id, event_type_slot_id, user_id, creation_time, user_update_id, update_time) " .
				"VALUES ({$event_id}, {$type_id}, {$user['user_id']}, now(),{$user['user_id']}, now() )";
		db_execute($sql);
		$slot_id = $mdb2->lastInsertID();
		echo json_encode(array("success"=>1, "slot_id"=>$slot_id));
	}
	
}
?>
