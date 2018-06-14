<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_event_slot extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EDIT_DOCUMENTS;
    }
		
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$slot_id = intval($_POST['slot_id']);
		
		$sql = "DELETE FROM reports_events_slots " .
				"WHERE report_event_slot_id={$slot_id}";
		db_execute($sql);
		
		return;
	}
	
}
