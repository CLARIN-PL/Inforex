<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_home extends CPagePublic {

	function __construct()
    {
    	parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

    function execute(){
		global $user;

        $user_id = isset($user["user_id"]) 
                    ? intval($user["user_id"])
                    : null ;
		
		$private_corpora = DbCorpus::getPrivateCorporaForUser($user_id, intval(hasRole(USER_ROLE_ADMIN)));
		$public_corpora = DbCorpus::getCorpora(1);
		$this->set('user_id', $user_id);
		$this->set('corpus_private', $private_corpora);
		$this->set('corpus_public', $public_corpora);
	}
}


?>
