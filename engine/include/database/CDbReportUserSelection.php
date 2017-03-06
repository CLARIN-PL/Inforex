<?php

/**
 * Created by PhpStorm.
 * User: mszewczyk
 * Date: 3/6/17
 * Time: 1:59 PM
 */
class ReportUserSelection{

    static function getAllDocuments($corpus_id, $user_id){
        global $db;

        $sql = "SELECT uc.report_id as id FROM reports_users_selection uc
                JOIN reports r ON uc.report_id = r.id 
                WHERE (r.corpora = " . $corpus_id . " AND uc.user_id = " . $user_id . ")";

        return $db->fetch_rows($sql, array($corpus_id, $user_id));
    }

    static function deleteDocuments($user_id, $docs){
        global $db;

        $ids = explode(',', $docs);
        $params = array_merge(array($user_id), $ids);

        ChromePhp::log(implode(',', array_fill(0, count($ids),'?')));
        $sql = "DELETE FROM reports_users_selection WHERE (user_id = ? AND report_id IN (".implode(',', array_fill(0, count($ids),'?')) . "));";
        $db->execute($sql, $params);
    }

    static function clearDocuments($user_id, $corpus_id){
        global $db;

        $sql = "DELETE FROM `reports_users_selection` "
            . "WHERE report_id IN (SELECT r_id FROM "
            . "(SELECT uc.report_id as r_id FROM reports_users_selection uc "
            . "JOIN reports r ON uc.report_id = r.id "
            . "WHERE (uc.user_id = ? AND r.corpora = ? )) AS T);";
        $db->execute($sql, array($user_id, $corpus_id));
    }

    static function selectCheckedDocs($user_id){
        global $db;

        $sql = "SELECT * FROM reports_users_selection WHERE (user_id = ?);";
        return $db->fetch_rows($sql, array($user_id));
    }

    static function insertCheckboxes($values){
        global $db;

        $sql = "INSERT INTO reports_users_selection VALUES ".implode(',', array_fill(0, count($values)/2,'(?,?)'));
        $db->execute($sql, $values);
    }

    static function getNumberOfSelected($corpus_id, $user_id){
        global $db;

        $sql = "SELECT COUNT(*) as amount FROM reports_users_selection uc
                JOIN reports r ON uc.report_id = r.id 
                WHERE (r.corpora = ? AND uc.user_id = ?)";

        return $db->fetch_rows($sql, array($corpus_id, $user_id));
    }
}