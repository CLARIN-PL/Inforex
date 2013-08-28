<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_set_corpus_perspective_roles extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $db, $corpus;

		ob_start();
		if ($_POST['operation_type'] == "add")
			$db->execute("INSERT INTO corpus_perspective_roles(report_perspective_id, corpus_id, user_id) VALUES (\"{$_POST['perspective_id']}\", {$corpus['id']}, \"{$_POST['user_id']}\")");
		else if ($_POST['operation_type'] == "remove")
			$db->execute("DELETE FROM corpus_perspective_roles WHERE report_perspective_id=\"{$_POST['perspective_id']}\" AND corpus_id={$corpus['id']} AND user_id={$_POST['user_id']}");
		
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else
			return;
	}	
}
?>
