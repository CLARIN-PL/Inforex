<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_validation extends CPage {

    function checkPermission(){
        return true;
    }

    function execute(){
        global $db;
        $mode = $_POST['mode'];

        if($mode == 'create'){
            $login = $_POST['login'];
            $sql_select = "SELECT * FROM users WHERE login = '" . $login . "'";
        } else{
            $login = $_POST['login'];
            $id = $_POST['id'];
            $sql_select = "SELECT * FROM users WHERE (login = '" . $login . "' AND user_id != " . $id . ")";
        }

        $results = $db->fetch($sql_select);


        if($results != null){
            echo "false";
        } else{
            echo "true";
        }
        die();
    }
}
?>
