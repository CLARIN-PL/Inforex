<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_corpus_assign extends CPageCorpus {

    function execute(){
        global $db;

        $corpus_id = $_POST['corpus_id'];
        $mode = $_POST['mode'];

        if($mode == "get"){
            return DbCorporaUsers::getCorpusReadUsersWithLastActivity($corpus_id);
        } else{
            $match_text = '%' . $_POST['match_text'] . '%';

            $sql = "SELECT * FROM users WHERE ((screename LIKE ? OR login LIKE ? OR email LIKE ?) AND user_id NOT IN(".
                "SELECT u.user_id FROM users_corpus_roles us JOIN users u ON us.user_id = u.user_id AND us.role = '".CORPUS_ROLE_READ."' AND us.corpus_id = ?))";

            $users = $db->fetch_rows($sql, array($match_text, $match_text, $match_text, $corpus_id));

            return $users;
        }
    }
}
