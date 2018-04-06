<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_relation_set_edit extends CPage {

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
        $element_id = intval($_POST['element_id']);
        $access = $_POST['set_access'] == "public" ? 1 : 0;

        $sql = "UPDATE relation_sets SET name = ?, description = ?, public = ? WHERE relation_set_id = ?";
        $db->execute($sql, array($name_str, $desc_str, $access, $element_id));

        return;
    }

}