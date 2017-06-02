<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_document_update_content extends CAction{
	
	var $annotations_to_update = array();
	var $annotations_to_delete = array();
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("edit_documents") || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $user, $mdb2, $corpus;
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
		$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$report->corpora}&amp;id={$report->id}";
		$this->set("info", "The document was saved. <b><a href='$link'>Edit the document</a> &raquo;</b>");

		return "";
	}
	
} 

?>
