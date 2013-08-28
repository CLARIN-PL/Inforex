<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_delete extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $mdb2;

		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		
		if ($element_type=="corpus"){
			$sql = "DELETE FROM corpora WHERE id = ?";			
		}
		
		if ($element_type=="subcorpus"){
			$sql = "DELETE FROM corpus_subcorpora WHERE subcorpus_id = ?";
		}
		
		if ($element_type=="flag"){
			$sql = "DELETE FROM corpora_flags WHERE corpora_flag_id = ?";
		}
		ob_start();
		$db->execute($sql, array($element_id));

		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else
			return;
	}	
}
?>
