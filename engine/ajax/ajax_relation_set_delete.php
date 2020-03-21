<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_relation_set_delete extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = ROLE_SYSTEM_EDITOR_SCHEMA_RELATIONS;
    }

    function execute(){
        global $db, $user;

        if (!intval($user['user_id'])){
            throw new Exception("Brak identyfikatora użytkownika");
        }

        $relation_set_id = intval($_POST['element_id']);

        $sql = "SELECT * FROM relation_types WHERE relation_set_id = ?";
        $result = $db->fetch_rows($sql, array($relation_set_id));
        if (count($result)>0){
            throw new UserDataException("You cannot delete this relation set. Delete all relation types from the relation set first.");
        }

        $sql = "DELETE FROM relation_sets WHERE relation_set_id = ?";
        $db->execute($sql, array($relation_set_id));
        return;
    }


}
