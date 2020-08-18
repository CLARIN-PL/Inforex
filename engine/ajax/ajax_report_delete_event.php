<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_event extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EDIT_DOCUMENTS;
    }
		
	function execute(){
		global $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$event_id = intval($_POST['event_id']);
		
		$sql = "DELETE FROM reports_events_slots " .
				"WHERE report_event_id={$event_id}";				
		$this->getDb()->execute($sql);
		$sql = "DELETE FROM reports_events " .
				"WHERE report_event_id={$event_id}";				
		$this->getDb()->execute($sql);
		
		return;
	}
	
}
