<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_add_event_slot extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;

    }
	
	function execute(){
		global $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}

		$event_id = intval($_POST['event_id']);
		$type_id = intval($_POST['type_id']);
		//$user_id = intval($user['user_id']);
		
		$sql = "INSERT INTO reports_events_slots (report_event_id, event_type_slot_id, user_id, creation_time, user_update_id, update_time) " .
				"VALUES ({$event_id}, {$type_id}, {$user['user_id']}, now(),{$user['user_id']}, now() )";
		$this->getDb()->execute($sql);
		$slot_id = $this->getDb()->last_id();
		
		return array("slot_id"=>$slot_id);
	}
	
}
