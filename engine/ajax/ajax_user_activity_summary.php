<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_activity_summary extends CPageCorpus {

    const ACTIVITY_LIST_LIMIT = 500;

    function execute(){
        global $db;

        $user_id = $_POST['user_id'];
        $mode = $_POST['mode'];
        $type = $_POST['type'];
        $data = array();

        if($type == "all"){
            if($mode == "list"){
                $sql = " SELECT a.datetime, CONCAT(at.category, '/', at.name) AS name, c.name AS 'corpus', a.report_id FROM activities a
                                LEFT JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                                LEFT JOIN corpora c ON c.id = a.corpus_id
                                WHERE a.user_id = ?
                                ORDER BY a.datetime DESC
                                LIMIT " . self::ACTIVITY_LIST_LIMIT;
            } else if($mode == "summary"){
                $sql = "SELECT CONCAT(at.category, '/', at.name) AS name, COUNT(a.activity_page_id) as 'num_of_activities', SUM(CASE WHEN a.datetime >= NOW() - INTERVAL 30 DAY THEN 1 ELSE 0 END) as 'num_last_30' FROM activities a
                JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                WHERE a.user_id = ?
                GROUP BY a.activity_type_id, at.category, at.name
                ORDER BY num_of_activities DESC
                ";
            }
            $data = $db->fetch_rows($sql, array($user_id));
        } else if($type == "corpus"){
            $corpus_id = $_POST['corpus_id'];

            if($mode == "list"){
                $sql = " SELECT a.datetime, CONCAT(at.category, '/', at.name) AS name, c.name AS 'corpus', a.report_id FROM activities a FORCE INDEX (activities_corpus_user_datetime_idx)
                                LEFT JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                                LEFT JOIN corpora c ON c.id = a.corpus_id
                                WHERE (a.user_id = ? AND a.corpus_id = ?)
                                ORDER BY a.datetime DESC
                                LIMIT " . self::ACTIVITY_LIST_LIMIT;
            } else if($mode == "summary"){
                $sql = "SELECT CONCAT(at.category, '/', at.name) AS name, COUNT(a.activity_page_id) as 'num_of_activities', SUM(CASE WHEN a.datetime >= NOW() - INTERVAL 30 DAY THEN 1 ELSE 0 END) as 'num_last_30' FROM activities a FORCE INDEX (activities_corpus_user_datetime_idx)
                JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                WHERE (a.user_id = ? AND a.corpus_id = ?)
                GROUP BY a.activity_type_id, at.category, at.name
                ORDER BY num_of_activities DESC
                ";
            }

            $data = $db->fetch_rows($sql, array($user_id, $corpus_id));
        }

        return $data;
    }
}
