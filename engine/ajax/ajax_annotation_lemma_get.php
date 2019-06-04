<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_lemma_get extends CPageCorpus {
    function __construct(){
        parent::__construct("annotation_lemma_get", "Returns annotation lemma for given annotation id");
        $this->anyPerspectiveAccess[] = "annotation_lemma";
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }
	
	function execute(){
		$annotation_id = intval($_POST['annotation_id']);
		$lemma = strval(DbReportAnnotationLemma::getAnnotationLemma($annotation_id));		
		return array("lemma" => $lemma);
	}
}