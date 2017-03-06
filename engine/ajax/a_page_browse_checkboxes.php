<?php

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_page_browse_checkboxes extends CPage {
	
	var $isSecure = false;
	function execute(){
		global $mdb2, $corpus, $db;
        $user_id = $_SESSION['_authsession']['data']['user_id'];
        $document = $_POST['data'];
        $mode = $_POST['mode'];
        $corpus_id = $_POST['corpus'];

        if($mode == "is_user_logged"){
            if($user_id != null){
                return true;
            } else{
                return false;
            }
        }

        if($user_id != null) {

            if ($mode == "delete") {
                foreach ($document as $doc) {
                    $docs .= $doc . ",";
                }

                $docs = rtrim($docs, ',');
                ReportUserSelection::deleteDocuments($user_id, $docs);

            } else if ($mode == "get_all") {
                $records = ReportUserSelection::getAllDocuments($corpus_id, $user_id);
                foreach ($records as $record) {
                    $ids[] = $record['id'];
                }

                return $ids;

            } else if ($mode == "clear") {
                ReportUserSelection::clearDocuments($user_id, $corpus_id);

            } else if ($mode == "insert") {
                $records = ReportUserSelection::selectCheckedDocs($user_id);

                $taken_ids = array();

                foreach ($records as $record) {
                    $taken_ids[] = $record['report_id'];
                }

                foreach ($document as $doc) {
                    if (!in_array($doc, $taken_ids)) {
                        $values[] = $user_id;
                        $values[] = $doc;
                    }
                }

                if (!empty($values)) {
                    ReportUserSelection::insertCheckboxes($values);
                }
            } else if ($mode == "get_amount") {
                $amount = ReportUserSelection::getNumberOfSelected($corpus_id, $user_id);
                return $amount[0]['amount'];
            }

        } else{
            return "";
        }
        }
}