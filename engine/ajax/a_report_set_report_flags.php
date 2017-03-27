<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_report_flags extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak praw <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$report_id = intval($_POST['report_id']);
		$cflag_id = intval($_POST['cflag_id']);
        $flag_id = intval($_POST['flag_id']);
        $user_id = $_SESSION['_authsession']['data']['user_id'];
        $subcorpus_id = $_POST['subcorpus_id'];

        $params = array('report_id' => $report_id);

        ChromePhp::log($_POST);

        if(!empty($cflag_id) && !empty($flag_id)){
            $params['corpora_flag_id'] = $cflag_id;
            $params['flag_id'] = $flag_id;
        }

        /*
         * Masowa zmiana statusu flagi i subkorpusu.
         */
        if(isset($_POST['multiple']) === true){

            $corpus_id = ($_POST['corpus_id']);
            $records = ReportUserSelection::selectCheckedDocs($corpus_id, $user_id);

            foreach($records as $record){
                $document_ids[] = $record['id'];
            }

            if(empty($document_ids)){
                return;
            }

            foreach($document_ids as $document){
                $params['report_id'] = $document;

                if($flag_id != "" && $cflag_id != "") {
                    $db->replace("reports_flags", $params);
                }

                if($subcorpus_id != -1){
                    DbReport::changeReportsSubcorpus($subcorpus_id, $document);
                }
            }

        }
        //Zmiana jednej flagi
        else {
            if ($flag_id){
                $db->replace("reports_flags", $params);
            }
            else {
                    DbReportFlag::deleteReportFlag($cflag_id, $report_id);
            }
        }
        ChromePhp::log("wychodzi z funkcji");
		return;
	}
	
}
?>
