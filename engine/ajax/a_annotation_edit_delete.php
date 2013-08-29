<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_delete extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate'))
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
		
		if ($element_type=="annotation_set"){
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
			$sql = "DELETE FROM annotation_sets WHERE annotation_set_id=$element_id";
			db_execute($sql);
		}
		else if ($element_type=="annotation_subset"){
			/*$sql = "DELETE FROM event_type_slots " .
					"WHERE event_type_id = {$element_id}";
			db_execute($sql);*/
			$sql = "DELETE FROM annotation_subsets WHERE annotation_subset_id=$element_id";
			db_execute($sql);
		}
		else if ($element_type=="annotation_type"){
			$element_id = $_POST['element_id'];
			$sql = "DELETE FROM annotation_types WHERE name=\"$element_id\"";
			db_execute($sql);
		}
		return;
	}
	
}
?>
