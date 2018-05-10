<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_add_event extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;

    }
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}
		
		$report_id = intval($_POST['report_id']);
		$event_type_id = intval($_POST['type_id']);
		$user_id = intval($user['user_id']);
		
		try{
			DbReportEvent::addEvent($report_id, $event_type_id, $user_id);
		}catch(Exception $e){
			throw new Exception("Błąd zapytania SQL");
		}
		$event_id = $mdb2->lastInsertID();
		return array("event_id"=>$event_id);
	}
	
}