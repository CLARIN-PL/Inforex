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

	public function execute(){
		global $corpus, $user;
        $user_id = $user['user_id'];


		$annotator_a_id = $_POST['annotator_a'];
		$annotator_b_id = $_POST['annotator_b'];
		$reports_ids = $_POST['report_ids'];


        $reports = DbTokensTagsOptimized::getUsersOwnDecisionsByReports($reports_ids, $annotator_a_id, $annotator_b_id);

        return $reports; //array('ret'=>$tags, 'user'=>$user);
	}
}
