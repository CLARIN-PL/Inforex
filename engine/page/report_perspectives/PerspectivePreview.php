<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectivePreview extends CPerspective {

    function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_relation_sets.js");
        $this->page->includeJs("js/c_autoaccordionview.js");
    }

	function execute()
	{
		global $corpus;

        $report = $this->page->report;
        $corpusId = $corpus['id'];
        $stages_annotations = array("new", "final", "discarded", "agreement");
        $stages_relations = array("final", "discarded", "agreement");
        $relationTypeIds = CookieManager::getRelationSets($corpusId);
        $annotationTypes = CookieManager::getAnnotationTypeTreeAnnotationTypes($corpusId);
        $preview_users = DbAnnotation::getUsersWithAnnotations($report['id']);
        $selected_user = CookieManager::getPreviewUser($report['id']);

        if($selected_user){
            $user = array($selected_user);
        } else{
            $user = null;
        }

        $force_annotation_set_id = intval($_GET['annotation_set_id']);
		$stage_annotations = strval($_COOKIE['stage_annotations']);
		if ( !in_array($stage_annotations, $stages_annotations) ){
		    $stage_annotations = "final";
        }
        $stage_relations = strval($_COOKIE['stage_relations']);
        if ( !in_array($stage_relations, $stages_relations) ){
            $stage_relations = "final";
        }


        /* Wymuś określony tryb w oparciu i prawa użytkownika */
        if ( hasCorpusRole(CORPUS_ROLE_ANNOTATE) && !hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ){
            $annotation_mode = "final";
        } else if ( !hasCorpusRole(CORPUS_ROLE_ANNOTATE) && hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) ){
            $annotation_mode = "agreement";
        } else{
            /* Użytkownik nie ma dostępu do żadnego trybu */
            // ToDo: zgłosić brak prawa dostępu
        }


        $anStages = array($stage_annotations);

        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokens($htmlStr, DbToken::getTokenByReportId($report['id']));

        $annotations = DbAnnotation::getReportAnnotations($report['id'], $user, null, null, $annotationTypes, $anStages);
        $relations = DbReportRelation::getReportRelations($this->page->cid, $this->page->id, $relationTypeIds, $annotationTypes, $stage_annotations, $stage_relations);
        $htmlStr = ReportContent::insertAnnotationsWithRelations($htmlStr, $annotations, $relations);

        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("stage_annotations", $stage_annotations);
        $this->page->set("stage_relations", $stage_relations);
        $this->page->set("stages_annotations", $stages_annotations);
        $this->page->set("stages_relations", $stages_relations);
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));
        $this->page->set("annotations", $annotations);
        $this->page->set("relations", $relations);
        $this->page->set("preview_users", $preview_users);
        $this->page->set("selected_user", $selected_user);

        /* Setup active accordion panel */
        $accordions = array("collapseConfiguration", "collapseAnnotations", "collapseRelations");
        $activeAccordion = $_COOKIE['accordion_active'];
        if ( !in_array($activeAccordion, $accordions) ){
            $activeAccordion = $accordions[0];
        }
        $this->page->set("active_accordion", $activeAccordion);
	}
}
?>
