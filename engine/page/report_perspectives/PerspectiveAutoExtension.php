<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAutoExtension extends CPerspective {

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

		$annotationsNew = DbAnnotation::getNewBootstrappedAnnotations($db, $reportId, $annotationSetId);
        $htmlStr = ReportContent::getHtmlStrForReport($report);
        $htmlStr = ReportContent::insertAnnotations($htmlStr, $annotationsNew);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($reportId));

		$annotationSetTypes = array();
		foreach ($annotationSets as $set){
			$asetid = $set['annotation_set_id'];
			$annotationSetTypes[$asetid] = DbAnnotation::getAnnotationTypesForChangeList($db, $asetid);
		}

		$this->page->set('verify', $verify);
		$this->page->set('annotations', $annotationsNew);
		$this->page->set('content', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('annotation_types', $annotationSetTypes);
		$this->page->set('annotation_sets', $annotationSets);
		$this->page->set('annotation_set_id', $annotationSetId);
	}
}