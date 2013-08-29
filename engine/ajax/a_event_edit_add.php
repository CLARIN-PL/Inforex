<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_event_edit_add extends CPage {
	
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
		/*$event_id = intval($_POST['event_id']);
		$type_id = intval($_POST['type_id']);
		//$user_id = intval($user['user_id']);
		$sql = "INSERT INTO reports_events_slots (report_event_id, event_type_slot_id, user_id, creation_time, user_update_id, update_time) " .
				"VALUES ({$event_id}, {$type_id}, {$user['user_id']}, now(),{$user['user_id']}, now() )";*/
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$parent_id = intval($_POST['parent_id']);
		
		$element_type = $_POST['element_type'];
		
		if ($element_type=="event_group"){
			$sql = 'INSERT INTO event_groups (name, description) VALUES ("'.$name_str.'", "'.$desc_str.'")';
		}
		else if ($element_type=="event_type"){
			$sql = 'INSERT INTO event_types (name, description, event_group_id) VALUES ("'.$name_str.'", "'.$desc_str.'", "'.$parent_id.'")';
		}
		else if ($element_type=="event_type_slot"){
			$sql = 'INSERT INTO event_type_slots (name, description, event_type_id) VALUES ("'.$name_str.'", "'.$desc_str.'", "'.$parent_id.'")';
		}
				
		db_execute($sql);
		$last_id = $mdb2->lastInsertID();
		return array("last_id"=>$last_id);
	}
	
}
?>
