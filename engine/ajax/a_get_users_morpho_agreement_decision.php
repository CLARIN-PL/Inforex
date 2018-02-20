<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_get_users_morpho_agreement_decision extends CPage{

//    private $defaultTagsetId = 1;
//
//    function __construct(){
//        parent::__construct();
//
//    }

    function checkPermission(){
        if (hasRole(USER_ROLE_LOGGEDIN)){
                return true;
            } else{
            return "Brak prawa do edycji.";
        }
    }
//    private function getAnnotatorDecision($annotatorId){
//        if($annotatorId == -1){
//            $dec = DbTokensTagsOptimized::getTokensTags($tokens_present, false);
//        } else if($annotatorId == 'final'){
//
//        }
//    }

	public function execute(){
		global $corpus, $user;
        $user_id = $user['user_id'];


		$annotator_a_id = $_POST['annotator_a'];
		$annotator_b_id = $_POST['annotator_b'];
		$reports_ids = $_POST['report_ids'];
        $compare_mode = $_POST['compare_mode'];



		$user_a_decisions = DbTokensTagsOptimized::getUserOwnDecisionsByReports($reports_ids, $annotator_a_id);
		$user_b_decisions = DbTokensTagsOptimized::getUserOwnDecisionsByReports($reports_ids, $annotator_b_id);

		$tokens_present = array();
        foreach(array_merge($user_a_decisions,  $user_b_decisions) as $dec){
            $tokens_present[] = $dec['token_id'];
        }
        $tagger_decisions = DbTokensTagsOptimized::getTokensTags($tokens_present, false);

        if($compare_mode == 'base_ctag'){
            $comparisonFcn = function($a,$b){
                return ($a['ctag'] == $b['ctag'] && $a['base_text'] == $b['base_text']);
            };
        } else{
            $comparisonFcn = function($a,$b){
                return $a['base_text'] == $b['base_text'];
            };
        }
        $report = DbTokensTagsOptimized::prepareReportSummary($user_a_decisions, $user_b_decisions,  $reports_ids[0]);
        $report = DbTokensTagsOptimized::getDecisionDifferences($report, $tagger_decisions, $comparisonFcn,
            $annotator_a_id == 'final', $annotator_b_id == 'final');

        return $report;
	}
}
