<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_document_content_update extends CAction{
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_EDIT_DOCUMENTS) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $user, $corpus;
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));
		
		$error = null;
		
		if (!intval($corpus['id'])){
			$this->set("error", "Brakuje identyfikatora korpusu!");
			return "";
		}

		if (!intval($user['user_id'])){
			$this->set("error", "Brakuje identyfikatora użytkownika!");
			return "";
		}
		
		$report = new CReport($report_id);			
		$content_before  = $report->content;
		$report->content = $content;
		$report->save();
		
		$df = new DiffFormatter();
		$diff = $df->diff($content_before, $report->content, true);
		if ( trim($diff) != "" ){
			$deflated = gzdeflate($diff);
			$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated);		
			DbReport::insertReportDiffs($data);
		}

		//$this->set("info", "Document was saved");

		return "";
	}
		
} 

?>
