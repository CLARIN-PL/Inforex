<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_document_edit extends CPage{

	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_ADD_DOCUMENTS) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){

		global $corpus;
				
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();
		$formats = DbReport::getAllFormats();

		if (!$this->get('date')){
			$this->set('date', date("Y-m-d"));
		}
		
		$row = array("format_id" => 2);
			
		$this->set('features', $features);
		$this->set('subcorpora', $subcorpora);
		$this->set('statuses', $statuses);
		$this->set('formats', $formats);
		$this->set('row', $row);
	}
}


?>
