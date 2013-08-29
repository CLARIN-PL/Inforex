<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_home extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $user;
		
		$user_id = intval($user[user_id]);
		
		$private_corpora = DbCorpus::getPrivateCorporaForUser($user_id, intval(hasRole(USER_ROLE_ADMIN)));
		$public_corpora = DbCorpus::getCorpora(1);
		$this->set('user_id', $user_id);
		$this->set('corpus_private', $private_corpora);
		$this->set('corpus_public', $public_corpora);
	}
}


?>
