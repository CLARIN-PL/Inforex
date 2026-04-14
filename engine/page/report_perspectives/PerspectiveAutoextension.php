<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAutoextension extends CPerspective {

	function execute()
	{
		global $db;

		$verify = isset($_REQUEST['verify']) ? true : false;
		$reportId = $this->page->getRequestParameterRequired('id');
        $annotationSetId = $this->page->getRequestParameter('annotation_set_id', null);
        $report = new TableReport($reportId);

		$annotationSets = DbAnnotation::getBootstrappedAnnotationsSummary($reportId);

		if ( count($annotationSets)==1 && $annotationSetId == 0 ){
            $annotationSetId = $annotationSets[0]['annotation_set_id'];
		}

		$annotationsNew = $annotationSetId === null
			? array()
			: DbAnnotation::getNewBootstrappedAnnotations($db, $reportId, $annotationSetId);
        $htmlStr = ReportContent::getHtmlStrForReport($report);
        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotationsNew);

		$annotationSetTypes = array();
		if ($annotationSetId !== null){
			$annotationSetTypes[$annotationSetId] = DbAnnotation::getAnnotationTypesForChangeList($db, $annotationSetId);
		}

		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('annotation_types', $annotationSetTypes);
		$this->page->set('annotation_sets', $annotationSets);
		$this->page->set('annotation_set_id', $annotationSetId);
	}
}
