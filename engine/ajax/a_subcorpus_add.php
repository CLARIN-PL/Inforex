<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_subcorpus_add extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $corpus, $mdb2;

		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";
		$corpus_id = $corpus['id'];
		$corpus_name = strval($_POST['name_str']);
		$corpus_desc = strval($_POST['desc_str']);
				
		ob_start();
		$db->execute($sql, array($corpus_id, $corpus_name, $corpus_desc));
		$error_buffer_content = ob_get_contents();
		ob_clean();
		
		if(strlen($error_buffer_content))
			throw new Exception("Error: ". $error_buffer_content);
		else{
			$last_id = $mdb2->lastInsertID();
			return array("last_id"=>$last_id);
		}
	}
	
}
?>
