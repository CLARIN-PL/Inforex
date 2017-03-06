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

                        $sqlDelete = "DELETE FROM users_checkboxes WHERE (user_id = " . $user_id . " AND report_id IN (" . $docs . "));";
                        db_execute($sqlDelete);

                    } else if ($mode == "get_all") {
                        $sqlSelect = "SELECT uc.report_id as id FROM users_checkboxes uc
                                      JOIN reports r ON uc.report_id = r.id 
                                      WHERE (r.corpora = " . $corpus_id . " AND uc.user_id = " . $user_id . ")";
                        $records = db_fetch_rows($sqlSelect);

                        foreach ($records as $record) {
                            $ids[] = $record['id'];
                        }

                        return $ids;

                    } else if ($mode == "clear") {
                        $sqlDelete = "DELETE FROM `users_checkboxes` "
                            . "WHERE report_id IN (SELECT r_id FROM "
                            . "(SELECT uc.report_id as r_id FROM users_checkboxes uc "
                            . "JOIN reports r ON uc.report_id = r.id "
                            . "WHERE (uc.user_id = " . $user_id . " AND r.corpora = " . $corpus_id . " )) AS T);";
                        db_execute($sqlDelete);
                    } else if ($mode == "insert") {

                        $sqlSelect = "SELECT * FROM users_checkboxes WHERE (user_id = " . $user_id . ");";
                        $records = db_fetch_rows($sqlSelect);

                        $taken_ids = array();

                        foreach ($records as $record) {
                            $taken_ids[] = $record['report_id'];
                        }

                        foreach ($document as $doc) {
                            if (!in_array($doc, $taken_ids)) {
                                $values .= "(" . $user_id . " , " . $doc . " ),";
                            }
                        }

                        if (!empty($values)) {
                            $values = rtrim($values, ",");
                            $sqlInsert = "INSERT INTO users_checkboxes VALUES " . $values;
                            db_execute($sqlInsert);
                        }
                    } else if ($mode == "get_amount") {
                        $sqlSelect = "SELECT COUNT(*) as amount FROM users_checkboxes uc
                                      JOIN reports r ON uc.report_id = r.id 
                                      WHERE (r.corpora = " . $corpus_id . " AND uc.user_id = " . $user_id . ")";

                        $amount = db_fetch_rows($sqlSelect);
                        return $amount[0]['amount'];
                    } else{
                        fb("Else?!");
                    }

                } else{
                    return "";
                }
        }
}