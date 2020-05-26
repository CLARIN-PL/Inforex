<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

class PerspectiveMorphodisamb extends CPerspective
{

    var $annotationsClear = array();

    function __construct(CPage $page, $document){
        parent::__construct($page, $document);
        $this->page->includeJs("js/jquery/jquery-editable-select.min.js");
        $this->page->includeCss("css/jquery-editable-select.min.css");
    }


    function execute()
    {
        global $corpus, $user;

        $report = $this->page->report;
        $corpusId = $corpus['id'];

        $htmlStr = ReportContent::getHtmlStr($report);
        $tokens = DbToken::getTokenByReportId($report['id']);
        $htmlStr = ReportContent::insertTokensWithIds($htmlStr, $tokens);

        $this->page->set("content", Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("tokensTags", DbTokensTagsOptimized::getTokensTagsUserDecision(array_column($tokens, 'token_id'), $user['user_id']));
        $this->page->set('annotation_types', DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets', DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));

    }
}
