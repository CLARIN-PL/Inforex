<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_annotation_contexts extends CPageCorpus {

    function __construct(){
        parent::__construct("Annotation attributes", "Browse annotations by their attribute values");
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }
		
	function execute(){
		global $db, $corpus;
		

	}
		
}
