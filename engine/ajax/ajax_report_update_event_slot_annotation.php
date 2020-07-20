<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_update_event_slot_annotation extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
    }
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$slot_id = intval($_POST['slot_id']);
		$annotation_id = intval($_POST['annotation_id']);
		
		$sql = "UPDATE reports_events_slots " .
				"SET report_annotation_id={$annotation_id}, user_update_id={$user['user_id']}, update_time=now()" .
				"WHERE report_event_slot_id={$slot_id}";

		$this->getDb()->execute($sql);
 		return;
	}
	
}
