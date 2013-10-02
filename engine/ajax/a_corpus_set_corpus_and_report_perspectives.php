<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_set_corpus_and_report_perspectives extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $corpus;
		
		ob_start();
		if ($_POST['operation_type'] == "add")
			$db->execute("INSERT INTO corpus_and_report_perspectives(perspective_id, corpus_id, access) VALUES (\"{$_POST['perspective_id']}\", {$corpus['id']}, \"{$_POST['access']}\")");
		else if ($_POST['operation_type'] == "remove"){
			$db->execute("DELETE FROM corpus_and_report_perspectives WHERE perspective_id=\"{$_POST['perspective_id']}\" AND corpus_id = {$corpus['id']}");
			$error = $db->mdb2->errorInfo();
			if(!isset($error[0]))
				$db->execute("DELETE FROM corpus_perspective_roles WHERE report_perspective_id=\"{$_POST['perspective_id']}\" AND corpus_id = {$corpus['id']}");
		}
		else if ($_POST['operation_type'] == "update")
			$db->execute("UPDATE corpus_and_report_perspectives SET access=\"{$_POST['access']}\" WHERE perspective_id=\"{$_POST['perspective_id']}\" AND corpus_id = {$corpus['id']}");
		
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else
			return;
	}
	
}
?>
