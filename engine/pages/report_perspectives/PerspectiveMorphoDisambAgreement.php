<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveMorphoDisambAgreement extends CPerspective
{
    function checkPermission(){
        if (hasCorpusRole('agreement_check'))
            return true;
        else
            return "Brak prawa do edycji dokumentów";
    }

    function execute()
    {
        global $corpus, $user;

        $this->page->includeJs('js/page_report_morphodisamb.js');
        $this->page->includeCss('css/page_report_morphodisamb.css');

        $report = $this->page->report;
        $corpusId = $corpus['id'];

        $tokens = DbToken::getTokenByReportId($report['id']);
        $tokenIds = array_column($tokens, 'token_id');

        $htmlStr = ReportContent::getHtmlStr($report);
        $htmlStr = ReportContent::insertTokensWithIds($htmlStr, $tokens);

        $this->page->includeJs("js/jquery/jquery-editable-select.min.js");
        $this->page->includeCss("css/jquery-editable-select.min.css");

        $this->page->set("content",             Reformat::xmlToHtml($htmlStr->getContent()));
        $this->page->set("tokensTags",          DBTokensTagsOptimized::getTokensTags($tokenIds));
        $this->page->set("finalTagsDecision",   DBTokensTagsOptimized::getTokenTagsOnlyFinalDecision($tokenIds));
        $this->page->set('annotation_types',    DbAnnotation::getAnnotationStructureByCorpora($corpusId));
        $this->page->set('relation_sets',       DbRelationSet::getRelationSetsAssignedToCorpus($corpusId));


        // users that have marked this document as done
        $users = $this->getPossibleAnnotators($tokenIds);
        $this->page->set('users', $users);

        $this->setAnnotatorFromCookie($users, $tokenIds, 'a');
        $this->setAnnotatorFromCookie($users, $tokenIds, 'b');
    }

    private function getPossibleAnnotators($tokenIds){
        $users = DBTokensTagsOptimized::getUsersDecisionCount($tokenIds);
        $tokensLen = count($tokenIds);

        foreach($users as $key => $user)
            $users[$key]['annotation_count'] = number_format(($tokensLen - $user['annotation_count']) / $tokensLen, 2).'%';

        // passing '-1' as user_id, will return only tagger tags
        array_unshift($users, ['user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%']);
        return $users;
    }

    private function getCookieAnnotator($possibleUsers, $cookieUserId){
        // using '==' for type coercion
        if ($cookieUserId == -1)
            return ['user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%'];

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

        if ( isset($_COOKIE[$corpusId .'_morpho_annotator_'.$annotatorLetter.'_id']) ){
            $annotatorId = $_COOKIE[$corpusId .'_morpho_annotator_'.$annotatorLetter.'_id'];
            $cookieAnnotator = $this->getCookieAnnotator($possibleUsers, $annotatorId);

            if($cookieAnnotator !== null){
                $this->page->set("tokensTagsAnnotator".strtoupper($annotatorLetter), DBTokensTagsOptimized::getTokensTagsOnlyUserDecison($tokenIds, $annotatorId));
                $this->page->set("annotator".strtoupper($annotatorLetter)."Name", $cookieAnnotator['screename']);
            }
        }
    }
}
