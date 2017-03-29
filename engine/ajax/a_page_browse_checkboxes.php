<?php

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * This class handles the logic of the checkboxes. Information about the documents selected by the user
 * is stored in the database in the table `ReportUserSelection`.
 */

class Ajax_page_browse_checkboxes extends CPage {
	
	var $isSecure = false;

	function execute(){

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

        //Handles all checkbox operations.
        if($user_id != null) {

            //Deletes selected checkboxes.
            if ($mode == "delete") {
                ChromePhp::log($document);
                ReportUserSelection::deleteDocuments($user_id, $document);

            //Delets all selected checkboxes in the corpus.
            } else if ($mode == "clear") {
                ReportUserSelection::clearDocuments($user_id, $corpus_id);

            //Selects checkboxes.
            } else if ($mode == "insert") {
                if(empty($document)){
                    return "";
                }

                ChromePhp::log("Inserting");

                $records = ReportUserSelection::selectCheckedDocs($corpus_id, $user_id);

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

            //Gets the amount of selected checkboxes by the user in the corpus.
            } else if ($mode == "get_amount") {
                $amount = ReportUserSelection::getNumberOfSelected($corpus_id, $user_id);
                return $amount[0]['amount'];
            }

        } else{
            return "";
        }
        }
}