<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_validation extends CPage {

    function checkPermission(){
        return true;
    }

    function execute(){
        global $db;

        $type = $_POST['type'];
        $mode = $_POST['mode'];

        if($type == 'create_corpus'){
            $name = $_POST['corpus_name'];
            $sql_select = "SELECT * FROM corpora WHERE name = ?";
            $results = $db->fetch($sql_select, array($name));
        }

        if($results != null){
            echo "false";
        } else{
            echo "true";
        }
        die();
    }
}
?>
