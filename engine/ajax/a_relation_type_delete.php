<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_relation_type_delete extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('editor_schema_relations'))
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
		
		if ($element_type=="relation_type"){
			/*$sql = "DELETE FROM event_type_slots " .
					"WHERE event_type_id = {$element_id}";
			db_execute($sql);*/
			$sql = "SELECT * FROM relations WHERE relation_type_id={$element_id} LIMIT 1";
			$result = db_fetch_rows($sql);
			if (count($result)>0){
				throw new Exception("You cannot delete this relation type. There is at least one existing relation in database.");
			}
			
			
			$sql = "DELETE FROM relation_types WHERE id=$element_id";
			db_execute($sql);
		}
		return;
	}
	
}
?>
