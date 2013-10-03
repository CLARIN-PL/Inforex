<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_word_frequency extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}	
	
	function execute(){
		global $corpus;
		
		$ctag = $_GET['ctag'];
		$subcorpus = $_GET['subcorpus'];		
		$corpus_id = $corpus['id'];
		
		$set_filters = HelperDocumentFilter::gatherCorpusCustomFilters($_POST);				
			
		$this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));									
		$this->set("classes", Tagset::getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		//$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true, $set_filters));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
	}		

}
 
?>