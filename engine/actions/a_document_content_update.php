<?php

class Action_document_content_update extends CAction{
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("edit_documents") || isCorpusOwner())
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
		$report->content = $content;
		$report->save();

		$this->set("info", "The document content was saved.");

		return "";
	}
		
} 

?>
