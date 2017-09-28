<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveMorphoDisambAgreement extends CPerspective
{

//    var $annotationsClear = array();

    function execute()
    {
        global $corpus, $user;

        $this->page->includeJs('js/page_report_morphodisamb.js');
        $this->page->includeCss('css/page_report_morphodisamb.css');

        $report = $this->page->report;
        $corpusId = $corpus['id'];

        $htmlStr = ReportContent::getHtmlStr($report);

        $tokens = DbToken::getTokenByReportId($report['id']);
        $htmlStr = ReportContent::insertTokensWithIds($htmlStr, $tokens);

        $this->page->includeJs("js/jquery/jquery-editable-select.min.js");
        $this->page->includeCss("css/jquery-editable-select.min.css");

        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("tokensTags", DBTokensTagsOptimized::getTokensTags(array_column($tokens, 'token_id')));
        $this->page->set("finalTagsDecision", DBTokensTagsOptimized::getTokenTagsOnlyFinalDecision(array_column($tokens, 'token_id')));
//        $this->page->set("tokensTags", DBTokensTagsOptimized::getTokensTagsUserDecision(array_column($tokens, 'token_id'), $user['user_id']));
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));

        // users that have marked this document as done
        $users = [[
                'user_id' =>1,
                'screename' => 'Anotator1'
            ],
            [
                'user_id' =>84,
                'screename' => 'Anotator84'
            ],
            [
                'user_id' =>3,
                'screename' => 'Anotator3'
            ],
            [
                'user_id' =>4,
                'screename' => 'Anotator4'
            ],
        ];
        $this->page->set('users', $users);

        if ( isset($_COOKIE[$corpusId .'_morpho_annotator_a_id']) ){
            $annotatorA = $_COOKIE[$corpusId .'_morpho_annotator_a_id'];
            $this->page->set("tokensTagsAnnotatorA", DBTokensTagsOptimized::getTokensTagsOnlyUserDecison(array_column($tokens, 'token_id'), $annotatorA));
        }
        if ( isset($_COOKIE[$corpusId .'_morpho_annotator_b_id']) ) {
            $annotatorB = $_COOKIE[$corpusId . '_morpho_annotator_b_id'];
            $this->page->set("tokensTagsAnnotatorB", DBTokensTagsOptimized::getTokensTagsOnlyUserDecison(array_column($tokens, 'token_id'), $annotatorB));
        }
    }
}
