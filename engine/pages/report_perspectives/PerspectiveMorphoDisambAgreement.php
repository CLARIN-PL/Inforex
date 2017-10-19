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


        $annotatorAId = $this->setAnnotatorFromCookie($users, $tokenIds, 'a');
        $annotatorBId = $this->setAnnotatorFromCookie($users, $tokenIds, 'b');

        if($annotatorAId && $annotatorBId){
            $annotatorsDiffCnt = $this->getUsersDifferingDecisionsCnt($tokenIds, $annotatorAId, $annotatorBId);
            $tokensLen = count($tokenIds);
            $this->page->set('annotators_diff', number_format(($tokensLen - $annotatorsDiffCnt) / $tokensLen * 100, 0).'%');
        }
    }

    private function getPossibleAnnotators($tokenIds){
        $users = DBTokensTagsOptimized::getUsersDecisionCount($tokenIds);
        $tokensLen = count($tokenIds);

        foreach($users as $key => $user)
            $users[$key]['annotation_count'] = number_format(($tokensLen - $user['annotation_count']) / $tokensLen * 100, 0).'%';

        // passing '-1' as user_id, will return only tagger tags
        array_unshift($users, array('user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%'));
        return $users;
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
                $this->page->set("tokensTagsAnnotator".strtoupper($annotatorLetter), DBTokensTagsOptimized::getTokensTagsOnlyUserDecison($tokenIds, $annotatorId));
                $this->page->set("annotator".strtoupper($annotatorLetter)."Name", $cookieAnnotator['screename']);
                return intval($annotatorId);
            }
        }
        return null;
    }

    private function groupArr($arr, $groupingOn){
        $result = array();
        foreach ($arr as $data) {
            $id = $data[$groupingOn];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        return $result;
    }
    private function getUsersDifferingDecisionsCnt($token_ids, $userA, $userB){
        global $user;

        $tags = DBTokensTagsOptimized::getUsersOwnDecisions($token_ids, $userA, $userB);
        $grouped = $this->groupArr($tags, 'token_id');
        foreach($grouped as $key => $tags)
            $grouped[$key] = $this->groupArr($tags, 'user_id');

        $differ = 0;

        foreach($grouped as $tags){
            if((count($tags)) < 2){
                $differ++; // only one user made decision
                continue;
            }
            $firstUserDecisions = array_pop($tags);
            $secondUserDecisions = array_pop($tags);

            foreach($firstUserDecisions as $firstUserTag){
                $foundTag = array_filter($secondUserDecisions, function($item) use ($firstUserTag){
                    if ($item['ctag'] !== $firstUserTag['ctag']
                        || $item['base_id'] !== $firstUserTag['base_id'])
                        return false;
                    return true;
                });
                if(count($foundTag) === 0){
                    $differ++;
                    break;
                }
            }
        }
        return $differ;
    }
}
