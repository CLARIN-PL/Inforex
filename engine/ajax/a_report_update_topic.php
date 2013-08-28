<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_update_topic extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('edit_documents') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus;
	
		$report_id = intval($_POST['report_id']);
		$topic_id = intval($_POST['topic_id']);
		
		if (!intval($corpus['id'])){
			throw new Exception("Brakuje identyfikatora korpusu!");
		}

		if (!intval($user['user_id'])){
			throw new Exception("Brakuje identyfikatora użytkownika!");
		}
				
		$report = new CReport($report_id);			
		$report->type = $topic_id;
		$report->save();
		
		return;
	}
	
}
?>
