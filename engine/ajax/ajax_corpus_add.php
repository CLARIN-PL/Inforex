<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_add extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

	
	function execute(){
		global $user;

		$corpus = new CCorpus();
        $corpus->name = strval($_POST['name']);
        $corpus->description = strval($_POST['description']);
        $corpus->user_id = $user['user_id'];
        $corpus->public = $_POST['ispublic'] === "true";
        $corpus->date_created = date('Y-m-d h:i:s', time());
        $corpus->save();

		return array("last_id"=>$corpus->id);
	}
	
}
