<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annmap_get_report_links extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }

    function execute(){

		$corpusId = intval($_POST['corpus_id']);
		$annotationType = $_POST['type'];
		$annotationText = $_POST['text'];
		$filters = $_SESSION['annmap'];

		return DbAnnotation::getAnnotationReportLinks($corpusId, $annotationType, $annotationText, $filters);
	}
	
}