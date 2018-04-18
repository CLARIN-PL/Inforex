<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_lemma_delete extends CPageCorpus {
	function __construct(){
	    parent::__construct();
	    $this->anyPerspectiveAccess[] = "annotation_lemma";
    }

    function execute(){
		$lemma_id = intval($_POST['annotation_id']);
		
		if(!$lemma_id){
			throw new Exception("Lemma id not provided.");
		}
		
		DbReportAnnotationLemma::deleteAnnotationLemma($lemma_id);
		return;
	}
}