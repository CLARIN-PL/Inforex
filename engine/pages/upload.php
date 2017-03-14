<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_upload extends CPage{

	function Page_upload(){
		parent::CPage();
		$this->includeJs("libs/bootstrap/dist/js/bootstrap.min.js");
        $this->includeCss("libs/bootstrap/dist/css/bootstrap.min.css");
        $this->includeCss("css/bootstrap_fix.css");
    }

    function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_ADD_DOCUMENTS) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $corpus;

		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus['id']));

	}
}


?>
