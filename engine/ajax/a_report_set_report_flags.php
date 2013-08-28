<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_report_flags extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak praw <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$report_id = intval($_POST['report_id']);
		$cflag_id = intval($_POST['cflag_id']);
		$flag_id = intval($_POST['flag_id']);
		
		if ($flag_id){		
			$sql = "REPLACE reports_flags SET corpora_flag_id={$cflag_id}, report_id={$report_id}, flag_id={$flag_id}";
			$result = db_execute($sql);
		}
		else {
			$sql = "DELETE FROM reports_flags WHERE corpora_flag_id={$cflag_id} AND report_id={$report_id}";
			$result = db_execute($sql);
		}

		return;
	}
	
}
?>
