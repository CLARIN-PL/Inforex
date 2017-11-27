<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_relation_sets extends CPage {

    function checkPermission(){
        if (hasRole(USER_ROLE_LOGGEDIN)){
            return true;
        }
        else
            return "Brak prawa do edycji.";
    }

    function execute(){
        global $db;

        $corpus_id = $_POST['corpus_id'];
        $relation_set_id = $_POST['relation_set_id'];
        $mode = $_POST['operation_type'];

        if($mode == "add"){
            $sql_insert = "INSERT INTO corpora_relations VALUES(?, ?)";
            $db->execute($sql_insert, array($corpus_id, $relation_set_id));
        } else{
            $sql_insert = "DELETE FROM corpora_relations WHERE(corpus_id = ? AND relation_set_id = ?)";
            $db->execute($sql_insert, array($corpus_id, $relation_set_id));
        }

        return;
    }

}
?>
