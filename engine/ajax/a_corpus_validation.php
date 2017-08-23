<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_validation extends CPage {

    var $isSecure = false;

    function checkPermission(){
        return true;
    }

    function execute(){
        global $db;

        $type = $_POST['type'];
        $mode = $_POST['mode'];
        $corpus_id = (int)$_POST['corpus_id'];

        switch($type){
            case('create_corpus'): {
                $name = $_POST['corpus_name'];
                $sql_select = "SELECT * FROM corpora WHERE name = ?";
                $results = $db->fetch($sql_select, array($name));

                break;
            }

            case('edit_corpus_name'): {
                $name = $_POST['nameDescription'];
                $sql_select = "SELECT * FROM corpora WHERE (BINARY name = ? AND id != ?)";
                $results = $db->fetch_rows($sql_select, array($name, $corpus_id));
                break;
            }

            case('subcorpora'): {
                if($mode == "create"){
                    $name = $_POST['subcorporaCreateName'];
                    $sql_select = "SELECT * FROM corpus_subcorpora WHERE (BINARY name = ? AND corpus_id = ?)";
                    $results = $db->fetch_rows($sql_select, array($name, $corpus_id));
                    ChromePhp::log($results);

                } else{
                    $name = $_POST['subcorporaEditName'];
                    $subcorpus_id = $_POST['subcorpus_id'];
                    $sql_select = "SELECT * FROM corpus_subcorpora WHERE (BINARY name = ? AND corpus_id = ? AND subcorpus_id != ?)";
                    $results = $db->fetch_rows($sql_select, array($name, $corpus_id, $subcorpus_id));
                    ChromePhp::log($results);
                }

                break;
            }

            case('flag'): {
                if($mode == "create"){
                    $name = $_POST['flagNameCreate'];
                    ChromePhp::log($name);
                    $sql_select = "SELECT * FROM corpora_flags WHERE (name = ? AND corpora_id = ?)";
                    ChromePhp::log($sql_select);
                    $results = $db->fetch_rows($sql_select, array($name, $corpus_id));

                    ChromePhp::log($results);

                } else{
                    $name = $_POST['flagNameEdit'];
                    $flag_id = $_POST['flag_id'];
                    $sql_select = "SELECT * FROM corpora_flags WHERE (BINARY name = ? AND corpora_id = ? AND corpora_flag_id != ?)";
                    $results = $db->fetch_rows($sql_select, array($name, $corpus_id, $flag_id));
                    ChromePhp::log($results);
                }

                break;
            }
        };

        if($results != null){
            echo "false";
        } else{
            echo "true";
        }
        die();
    }
}
?>
