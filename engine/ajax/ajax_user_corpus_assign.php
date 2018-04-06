<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_corpus_assign extends CPage {

    function execute(){
        global $db;

        $corpus_id = $_POST['corpus_id'];
        $mode = $_POST['mode'];

        if($mode == "get"){
            $sql = "SELECT u.user_id, u.screename, u.email, u.login, us.role" .
                " FROM users_corpus_roles us " .
                " RIGHT JOIN users u ON (us.user_id=u.user_id AND us.role = '".CORPUS_ROLE_READ."' AND us.corpus_id=?)" .
                " ORDER BY u.screename";

            $users = $db->fetch_rows($sql,array($corpus_id));

            foreach($users as $key => $user){
                $last_activity_sql = "  SELECT datetime as 'last_activity' FROM `activities`
                                WHERE (user_id = ? AND corpus_id = ?)
                                ORDER BY datetime DESC";
                $last_activity = $db->fetch_one($last_activity_sql, array($user['user_id'], $corpus_id));
                if($last_activity != null){
                    $last_activity_date = new DateTime($last_activity);
                    $last_activity = $last_activity_date->format('H:i:s d-m-Y');
                } else{
                    $last_activity = "";
                }
                $users[$key]['last_activity'] = $last_activity;
            }

            return $users;
        } else{
            $match_text = '%' . $_POST['match_text'] . '%';

            $sql = "SELECT * FROM users WHERE ((screename LIKE ? OR login LIKE ? OR email LIKE ?) AND user_id NOT IN(".
                "SELECT u.user_id FROM users_corpus_roles us JOIN users u ON us.user_id = u.user_id AND us.role = '".CORPUS_ROLE_READ."' AND us.corpus_id = ?))";

            $users = $db->fetch_rows($sql, array($match_text, $match_text, $match_text, $corpus_id));

            return $users;
        }
    }
}