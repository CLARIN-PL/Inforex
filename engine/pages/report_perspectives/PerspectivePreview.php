<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectivePreview extends CPerspective {

	function execute()
	{
        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_relation_sets.js");
        $this->page->includeJs("js/c_autoaccordionview.js");

		global $corpus;

        $report = $this->page->report;
        $corpusId = $corpus['id'];
        $stages = array("new", "final", "discarded");

        $force_annotation_set_id = intval($_GET['annotation_set_id']);
		$stage = strval($_COOKIE['stage']);
		if ( !in_array($stage, $stages) ){
		    $stage = "final";
        }
        $anStages = array($stage);

		$relationSetIds = CookieManager::getRelationSets($corpusId);

        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));
        $annotationTypes = CookieManager::getAnnotationTypeTreeAnnotationTypes($corpusId);

        $annotations = DbAnnotation::getReportAnnotations($report['id'], null, null, null, $annotationTypes, $anStages, false);
        $relations = DbReportRelation::getReportRelations($this->page->cid, $this->page->id, null);
        $htmlStr = ReportContent::insertAnnotationsWithRelations($htmlStr, $annotations, $relations);

        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("stage", $stage);
		$this->page->set("stages", $stages);
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));
        $this->page->set("annotations", $annotations);
        $this->page->set("relations", $relations);
	}
}
?>
