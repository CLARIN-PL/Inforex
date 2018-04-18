<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_lemma_get extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyPerspectiveAccess[] = "annotation_lemma";
    }
	
	function execute(){
		$annotation_id = intval($_POST['annotation_id']);
		$lemma = strval(DbReportAnnotationLemma::getAnnotationLemma($annotation_id));		
		return array("lemma" => $lemma);
	}
}