<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_event_edit_get extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_EDITOR_SCHEMA_EVENTS;
    }
	
	function execute(){
		global $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}
		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="event_group"){
			$sql = "SELECT event_type_id AS id, name, description FROM event_types WHERE event_group_id={$parent_id}";
		} 
		else if ($parent_type=="event_type"){
			$sql = "SELECT event_type_slot_id AS id, name, description FROM event_type_slots WHERE event_type_id={$parent_id}";
		}
				
		$result = $this->getDb()->fetch_rows($sql);
		return $result;
	}
	
}
