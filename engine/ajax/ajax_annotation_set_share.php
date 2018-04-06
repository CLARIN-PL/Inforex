<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_set_share extends CPage {

    var $isSecure = false;
    function execute(){
        global $db;
        $mode = $_POST['mode'];
        $annotation_set_id = $_POST['annotation_set_id'];

        if($mode == "get"){
            $owner_id = $_POST['owner_id'];
            $sql = "SELECT u.user_id, u.screename, u.login, uas.annotation_set_id AS annotation_set_id FROM users u LEFT JOIN users_annotation_sets uas ON uas.user_id = u.user_id AND uas.annotation_set_id = ? WHERE u.user_id != ? ORDER BY u.screename ";
            $users_annotations = $db->fetch_rows($sql, array($annotation_set_id, $owner_id));

            return $users_annotations;
        } else if($mode == "add"){
            $user_id = $_POST['user_id'];

            $sql = "INSERT INTO users_annotation_sets VALUES(?, ?)";
            $db->execute($sql, array($user_id, $annotation_set_id));

        } else if($mode == "remove"){
            $user_id = $_POST['user_id'];
            $sql = "DELETE FROM users_annotation_sets WHERE(user_id = ? AND annotation_set_id = ?)";

            $db->execute($sql, array($user_id, $annotation_set_id));
        }

    }
}

