<?php

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportUserSelection{

    /*
     * Deletes selected reports from table `reports_users_selection`.
     *  $docs - array containing ids of reports to be deleted.
     */
    static function deleteDocuments($user_id, $docs){
        global $db;

        $params = array_merge(array($user_id), $docs);

        $sql = "DELETE FROM reports_users_selection WHERE (user_id = ? AND report_id IN (".implode(',', array_fill(0, count($docs),'?')) . "));";
        $db->execute($sql, $params);
    }

    /*
     * Deletes ALL selected documents from table `reports_users_selection` for a given user and corpus.
     */
    static function clearDocuments($user_id, $corpus_id){
        global $db;

        $sql = "DELETE FROM `reports_users_selection` "
            . "WHERE report_id IN (SELECT r_id FROM "
            . "(SELECT uc.report_id as r_id FROM reports_users_selection uc "
            . "JOIN reports r ON uc.report_id = r.id "
            . "WHERE (uc.user_id = ? AND r.corpora = ? )) AS T);";
        $db->execute($sql, array($user_id, $corpus_id));
    }

    /*
     * Returns all documents selected by a given user in a given corpus.
     */
    static function selectCheckedDocs($corpus_id, $user_id){
        global $db;
        ChromePhp::log("Teraz select");
        $sql = "SELECT uc.report_id as id FROM reports_users_selection uc
                          JOIN reports r ON uc.report_id = r.id 
                          WHERE (r.corpora = ? AND uc.user_id = ?)";
        return $db->fetch_rows($sql, array($corpus_id, $user_id));
    }

    /*
     * Inserts into `reports_users_selection` id's of newly selected documents.
     * $values - array containing alternately user id and document's id. For example, for user with id = 1: [1, 58121, 1, 57214, 1, 68127].
     */
    static function insertCheckboxes($values){
        global $db;

        $sql = "INSERT IGNORE INTO reports_users_selection VALUES ".implode(',', array_fill(0, count($values)/2,'(?,?)'));
        $db->execute($sql, $values);
    }

    /*
     * Returns number of selected documents by given user in given corpus.
     */
    static function getNumberOfSelected($corpus_id, $user_id){
        global $db;

        $sql = "SELECT COUNT(*) as amount FROM reports_users_selection uc
                JOIN reports r ON uc.report_id = r.id 
                WHERE (r.corpora = ? AND uc.user_id = ?)";

        return $db->fetch_rows($sql, array($corpus_id, $user_id));
    }
}