<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_word_frequency extends CPageCorpus{

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    function execute(){
		global $corpus;

		$ctag = $_GET['ctag'];
		$subcorpus_id = $_GET['subcorpus_id'];		
		$corpus_id = $corpus['id'];
		
		$set_filters = HelperDocumentFilter::gatherCorpusCustomFilters($_POST);				
			
		$this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));									
		$this->set("classes", Tagset::getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus_id", $subcorpus_id);
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
		$this->set("phrase", strval($_GET['phrase']));
	}		

}
 
?>
