<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_document extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('delete_documents') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$report_id = intval($_POST['report_id']);

		DbReport::deleteReport($report_id);
		
		return;
	}	
}
?>
