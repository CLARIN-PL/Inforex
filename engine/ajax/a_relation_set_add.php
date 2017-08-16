<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_relation_set_add extends CPage {

    function checkPermission(){
        if (hasRole('admin') || hasRole('editor_schema_relations'))
            return true;
        else
            return "Brak prawa do edycji.";
    }

    function execute(){
        global $db, $user;

        if (!intval($user['user_id'])){
            throw new Exception("Brak identyfikatora użytkownika");
        }

        $name_str = $_POST['name_str'];
        $desc_str = $_POST['desc_str'];
        $setVisibility = $_POST['setAccess_str'];
        $user_id = $user['user_id'];

        $sql = 'INSERT INTO relation_sets (name, description, public, user_id) VALUES (?, ?, ?, ?)';
        $db->execute($sql, array($name_str, $desc_str, $setVisibility, $user_id));

        $last_id = $db->last_id();
        return array("last_id"=>$last_id, "user" => $user['screename']);
    }

}
?>
