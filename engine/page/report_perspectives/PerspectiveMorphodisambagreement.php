<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */


class PerspectiveMorphodisambagreement extends CPerspective
{

    function checkPermission(){
        if (hasCorpusRole('agreement_check'))
            return true;
        else
            return "Brak prawa do edycji dokumentów";
    }

    function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->morphoUtil = new MorphoUtil();
        $this->page->includeJs("js/c_widget_user_selection_a_b.js");
        $this->page->includeJs('js/page_report_morphodisamb.js');
        $this->page->includeCss('css/page_report_morphodisamb.css');
        $this->page->includeJs("js/jquery/jquery-editable-select.min.js");
        $this->page->includeCss("css/jquery-editable-select.min.css");
    }

    function execute()
    {
        global $corpus, $user;

        $report = $this->page->report;
        $corpusId = $corpus['id'];

        $tokens = DbToken::getTokenByReportId($report['id']);
        $tokenIds = array_column($tokens, 'token_id');

        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokensWithIds($htmlStr, $tokens);

        $this->page->set("content",             Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("tokensTags",          DbTokensTagsOptimized::getTokensTags($tokenIds));
        $this->page->set("finalTagsDecision",   DbTokensTagsOptimized::getTokenTagsOnlyFinalDecision($tokenIds));
        $this->page->set('annotation_types',    DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets',       DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));


        // users that have at least one changed value compared to default tagger
        $users = MorphoUtil::getPossibleAnnotators($tokenIds);
        $this->page->set('users', $users);


        $annotatorAId = $this->setAnnotatorFromCookie($users, $tokenIds, 'a');
        $annotatorBId = $this->setAnnotatorFromCookie($users, $tokenIds, 'b');

        if($annotatorAId && $annotatorBId){
            $annotatorsDiffCnt = MorphoUtil::getUsersDifferingDecisionsCnt($tokenIds, $annotatorAId, $annotatorBId);
            $tokensLen = count($tokenIds);
            if($tokensLen == 0)
                $this->page->set('annotators_diff', '');
            else{
                $this->page->set('annotators_diff', number_format(($tokensLen - $annotatorsDiffCnt) / $tokensLen * 100, 0).'%');
            }
        }
    }

    private function getCookieAnnotator($possibleUsers, $cookieUserId){
        // using '==' for type coercion
        if ($cookieUserId == -1)
            return array('user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%');

        $filtered = array_filter($possibleUsers, function($user) use ($cookieUserId){
            return $user['user_id'] == $cookieUserId;
        });

        if(count($filtered) > 0 )
            return array_pop($filtered); //[0];

        return null;
    }

    private function setAnnotatorFromCookie($possibleUsers, $tokenIds, $annotatorLetter){
        global $corpus;
        $corpusId = $corpus['id'];
        $cookieName = "agreement_morpho_" . $corpusId .'_annotator_id_'.$annotatorLetter;

        if ( isset($_COOKIE[$cookieName]) ){
            $annotatorId = $_COOKIE[$cookieName];
            $cookieAnnotator = $this->getCookieAnnotator($possibleUsers, $annotatorId);

            if($cookieAnnotator !== null){
                $this->page->set("tokensTagsAnnotator".strtoupper($annotatorLetter), DbTokensTagsOptimized::getTokensTagsOnlyUserDecison($tokenIds, $annotatorId));
                $this->page->set("annotator".strtoupper($annotatorLetter)."Name", $cookieAnnotator['screename']);
                return intval($annotatorId);
            }
        }
        return null;
    }
}
