<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_event_edit_delete extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('editor_schema_events'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		
		if ($element_type=="event_group"){
			/*$sql = "DELETE FROM event_type_slots " .
					"WHERE event_type_slot_id " .
					"IN (SELECT * " .
						"FROM (SELECT ets.event_type_slot_id AS id " .
							"FROM event_type_slots ets " .
							"JOIN event_types " .
								"ON event_types.event_group_id={$element_id} " .
								"AND ets.event_type_id=event_types.event_type_id) " .
						"bla)";
			db_execute($sql);
			$sql = "DELETE FROM event_types " .
					"WHERE event_group_id = {$element_id}";
			db_execute($sql);*/
			$sql = "DELETE FROM event_groups WHERE event_group_id=$element_id";
			db_execute($sql);
		}
		else if ($element_type=="event_type"){
			/*$sql = "DELETE FROM event_type_slots " .
					"WHERE event_type_id = {$element_id}";
			db_execute($sql);*/
			$sql = "DELETE FROM event_types WHERE event_type_id=$element_id";
			db_execute($sql);
		}
		else if ($element_type=="event_type_slot"){
			$sql = "DELETE FROM event_type_slots WHERE event_type_slot_id=$element_id";
			db_execute($sql);
		}
		return;
	}
	
}
?>
