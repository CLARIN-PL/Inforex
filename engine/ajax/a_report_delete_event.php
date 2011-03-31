<?php
/**
  metoda usuwajaca zdarzenie
 * 
 */
class Ajax_report_delete_event extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('edit_documents') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$event_id = intval($_POST['event_id']);
		
		$sql = "DELETE FROM reports_events_slots " .
				"WHERE report_event_id={$event_id}";				
		db_execute($sql);
		$sql = "DELETE FROM reports_events " .
				"WHERE report_event_id={$event_id}";				
		db_execute($sql);
		echo json_encode(array("success"=>1));
	}
	
}
?>
