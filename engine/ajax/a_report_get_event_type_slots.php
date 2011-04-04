<?php
/**
 * metoda pobierajaca typy slotów dla danego typu zdarzenia
 * 
 */
class Ajax_report_get_event_type_slots extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2, $user;
		$type_id = intval($_POST['type_id']);

		$sql = "SELECT event_type_slots.event_type_slot_id, event_type_slots.name FROM event_type_slots WHERE event_type_slots.event_type_id={$type_id}";
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
