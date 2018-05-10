<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_get_corpora extends CPage {
    var $isSecure = false;

    function execute(){
        global $db, $user;

        $text = "%".$_POST['match_text']."%";

        if (!intval($user['user_id'])){
            $sql="SELECT corp.name, corp.id as corpus_id FROM corpora corp WHERE (corp.name LIKE ? AND corp.public = 1)";
            $corpus = $db->fetch_rows($sql, array($text));
        } else{
            $sql="SELECT corp.name, corp.corpus_id FROM (SELECT c.id AS corpus_id, c.name FROM corpora c LEFT JOIN users_corpus_roles ucs ON c.id=ucs.corpus_id WHERE (ucs.user_id={$user['user_id']} AND ucs.role='". CORPUS_ROLE_READ ."')  OR c.user_id={$user['user_id']} OR c.public = 1 GROUP BY c.id) corp WHERE corp.name LIKE ?";
            $corpus = $db->fetch_rows($sql, array($text));
        }


        return $corpus;
    }

}