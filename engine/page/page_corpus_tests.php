<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_tests extends CPageCorpus {

    public function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_RUN_TESTS;
    }

	function execute(){
		global $corpus;
		
		$documents_in_corpus = DbReport::getReportsByCorpusId($corpus['id'],' count(*) AS count ');
	
		$this->set('corpus_id',$corpus['id']);
		$this->set('documents_in_corpus',$documents_in_corpus[0]['count']);
		$this->set('annotations_in_corpus',DbAnnotation::getAnnotationTypesByCorpora($corpus['id']));
	}	
 }
