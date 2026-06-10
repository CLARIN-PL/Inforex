<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_validation extends CPageAdministration {

    function execute(){
        global $db;
        $mode = $_POST['mode'];

        if($mode == 'create'){
            $login = $_POST['login'];
            $results = $db->fetch("SELECT * FROM users WHERE login = ?", array($login));
        } else{
            $login = $_POST['login'];
            $id = $_POST['id'];
            $results = $db->fetch("SELECT * FROM users WHERE login = ? AND user_id != ?", array($login, $id));
        }

        if($results != null){
            echo "false";
        } else{
            echo "true";
        }
        return;
    }
}
