<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_lemma_save extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyPerspectiveAccess[] = "annotation_lemma";
    }
	
	function execute(){
		$lemma_id = intval($_POST['annotation_id']);
		$lemma_text = $_POST['annotation_lemma_text'];

		if(!$lemma_text){
			throw new Exception("Empty lemma text");
		}
		
		DbReportAnnotationLemma::saveAnnotationLemma($lemma_id, $lemma_text);
		return;
	}
}