<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
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
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$event_id = intval($_POST['event_id']);
		
		$sql = "DELETE FROM reports_events_slots " .
				"WHERE report_event_id={$event_id}";				
		db_execute($sql);
		$sql = "DELETE FROM reports_events " .
				"WHERE report_event_id={$event_id}";				
		db_execute($sql);
		
		return;
	}
	
}
?>
