<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_set_annotation_sets_corpora extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $db, $corpus;
		
		ob_start();
		if ($_POST['operation_type']=="add")
			$db->execute("INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES ({$_POST['annotation_set_id']}, {$corpus['id']})");
		else if ($_POST['operation_type']=="remove")
			$db->execute("DELETE FROM annotation_sets_corpora WHERE annotation_set_id={$_POST['annotation_set_id']} AND corpus_id={$corpus['id']}"); 
		
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else
			return;
	}	
}
?>
