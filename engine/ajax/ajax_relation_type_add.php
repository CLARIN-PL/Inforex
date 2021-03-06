<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_relation_type_add extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_EDITOR_SCHEMA_RELATIONS;
    }
	
	function execute(){
		global $user;

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
		
		if ($element_type=="relation_type"){
			$sql = 'INSERT INTO relation_types (name, description, relation_set_id) VALUES ("'.$name_str.'", "'.$desc_str.'", "'.$parent_id.'")';
		}
				
		$this->getDb()->execute($sql);
		$last_id = $this->getDb()->last_id();
		return array("last_id"=>$last_id);
	}
	
}
