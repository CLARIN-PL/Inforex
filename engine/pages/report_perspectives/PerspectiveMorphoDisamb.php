<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveMorphoDisamb extends CPerspective
{

    var $annotationsClear = array();

    function execute()
    {
        global $corpus, $user;

        $report = $this->page->report;
        $corpusId = $corpus['id'];

        $htmlStr = ReportContent::getHtmlStr($report);

        $tokens = DbToken::getTokenByReportId($report['id']);
        $htmlStr = ReportContent::insertTokensWithIds($htmlStr, $tokens);

        $this->page->includeJs("js/jquery/jquery-editable-select.min.js");
        $this->page->includeCss("css/jquery-editable-select.min.css");

//        var_dump(DBTokensTagsOptimized::getTokensTagsUserDecision(array_column($tokens, 'token_id'), $user['user_id'])); die();

        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
//        $this->page->set("tokensTags", DBTokensTagsOptimized::getTokensTags(array_column($tokens, 'token_id')));
        $this->page->set("tokensTags", DBTokensTagsOptimized::getTokensTagsUserDecision(array_column($tokens, 'token_id'), $user['user_id']));
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));

    }

//    private function get user decisions
}
