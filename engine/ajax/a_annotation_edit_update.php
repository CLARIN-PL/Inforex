<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_update extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
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
		
		if ($element_type=="annotation_set")
			$sql = "UPDATE annotation_sets SET description=\"$desc_str\" WHERE annotation_set_id=$element_id";
		else if ($element_type=="annotation_subset")
			$sql = "UPDATE annotation_subsets SET description=\"$desc_str\" WHERE annotation_subset_id=$element_id";
		else if ($element_type=="annotation_type"){
			$element_id = $_POST['element_id'];
			$name_prev = $_POST['name_prev'];
			$group_id = $_POST['set_id'];
			$level = 0;
			$short_description = $_POST['short'];
			$css = $_POST['css'];
			$sql = "UPDATE annotation_types SET " .
					"description=\"$desc_str\", " .
					"group_id=\"$group_id\", " .
					"level=$level, " .
					"short_description=\"$short_description\", " .
					"css=\"$css\" WHERE " .
					"name=\"$element_id\"";
			
			//$sql = "UPDATE event_type_slots SET name=\"$name_str\", description=\"$desc_str\" WHERE event_type_slot_id=$element_id";
		}
		db_execute($sql);
		return;
	}
	
}
?>
