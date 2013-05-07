<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_add_event extends CPage {
	
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

		$report_id = intval($_POST['report_id']);
		$event_type_id = intval($_POST['type_id']);
		$user_id = intval($user['user_id']);
		
		$sql = "INSERT INTO reports_events (report_id, event_type_id, user_id, creation_time) " .
				"VALUES ($report_id, $event_type_id, $user_id, now())";
		db_execute($sql);
		$event_id = $mdb2->lastInsertID();
		echo json_encode(array("success"=>1, "event_id"=>$event_id));
	}
	
}
?>
