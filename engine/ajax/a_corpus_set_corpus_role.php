<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_corpus_set_corpus_role extends CPage {		
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;			
		else
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
	} 
	
	function execute(){
		global $corpus, $db;
		
		ob_start();	
		if ($_POST['operation_type'] == "add")
			$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($_POST['user_id'], $corpus['id'], $_POST['role']));
		else if ($_POST['operation_type'] == "remove")	
			$db->execute("DELETE FROM users_corpus_roles WHERE corpus_id={$corpus['id']} AND user_id={$_POST['user_id']} AND role=\"{$_POST['role']}\" ");
			
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else
			return;
	}	
} 

?>
