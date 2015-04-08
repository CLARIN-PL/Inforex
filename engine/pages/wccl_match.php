<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_wccl_match extends CPage{
	
	var $isSecure = true;

	function checkPermission(){
		return isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER); 
	}
		
	function execute(){
		global $config, $corpus, $user, $db;
				
		$rules = $db->fetch("SELECT * FROM wccl_rules WHERE user_id = ? AND corpus_id = ?",
			array($user['user_id'], $corpus['id']));

		$this->set("rules", $rules['rules']);				
		$this->set("annotations", $rules['annotations']);
	}
}


?>
