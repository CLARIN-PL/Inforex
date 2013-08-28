<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_event_edit_update extends CPage {
	
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
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$element_id = intval($_POST['element_id']);
		
		$element_type = $_POST['element_type'];
		
		if ($element_type=="event_group")
			$sql = "UPDATE event_groups SET name=\"$name_str\", description=\"$desc_str\" WHERE event_group_id=$element_id";
		else if ($element_type=="event_type")
			$sql = "UPDATE event_types SET name=\"$name_str\", description=\"$desc_str\" WHERE event_type_id=$element_id";
		else if ($element_type=="event_type_slot")
			$sql = "UPDATE event_type_slots SET name=\"$name_str\", description=\"$desc_str\" WHERE event_type_slot_id=$element_id";
		db_execute($sql);
		return;
	}
	
}
?>
