<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_document_add extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ADD_DOCUMENTS;
    }
		
	function execute(){
		global $corpus;
				
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();
		$formats = DbReport::getAllFormats();

		ChromePhp::log($features);

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