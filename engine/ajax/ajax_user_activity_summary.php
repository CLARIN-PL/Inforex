<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_activity_summary extends CPageCorpus {

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
                                WHERE a.user_id = ?";
            } else if($mode == "summary"){
                $sql = "SELECT CONCAT(at.category, '/', at.name) AS name, COUNT(a.activity_page_id) as 'num_of_activities', COUNT(CASE WHEN (a.datetime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() = TRUE) THEN 1 END) as 'num_last_30' FROM activities a
                JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                WHERE a.user_id = ?
                GROUP BY at.name
                ";
            }
            $data = $db->fetch_rows($sql, array($user_id));
        } else if($type == "corpus"){
            $corpus_id = $_POST['corpus_id'];

            if($mode == "list"){
                $sql = " SELECT a.datetime, CONCAT(at.category, '/', at.name) AS name, c.name AS 'corpus', a.report_id FROM activities a
                                LEFT JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                                LEFT JOIN corpora c ON c.id = a.corpus_id
                                WHERE (a.user_id = ? AND a.corpus_id = ?)";
            } else if($mode == "summary"){
                $sql = "SELECT CONCAT(at.category, '/', at.name) AS name, COUNT(a.activity_page_id) as 'num_of_activities', COUNT(CASE WHEN (a.datetime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() = TRUE) THEN 1 END) as 'num_last_30' FROM activities a
                JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                WHERE (a.user_id = ? AND a.corpus_id = ?)
                GROUP BY at.name
                ";
            }

            $data = $db->fetch_rows($sql, array($user_id, $corpus_id));
        }

        return $data;
    }
}
