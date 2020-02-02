<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_upload extends CPageCorpus {

	function __construct(){
		parent::__construct();
		$this->anyCorpusRole[] = CORPUS_ROLE_ADD_DOCUMENTS;
    }

	function execute(){
		global $corpus;
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus['id']));

		$redirect = $this->get("redirect");
		if ( $redirect ){
            $this->redirect($redirect);
        }
	}
}