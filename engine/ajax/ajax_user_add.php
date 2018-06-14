<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_add extends CPageAdministration {

    function execute(){
        global $db, $mdb2;

        $sql = "INSERT INTO users ( login, screename, email, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', '{$_POST['email']}', MD5('{$_POST['password']}'))";
        $db->execute($sql);
        $error = $db->mdb2->errorInfo();
        if(isset($error[0])) {
            $this->set("action_error", "Error: (" . $error[1] . ") -> " . $error[2]);
        }

        try{
            $this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$mdb2->lastInsertID());
        } catch(Exception $e){
            $this->set("action_error", "Error: ".$e->getMessage());
        }

        $data = array("id" => $mdb2->lastInsertID());

        return $data;
    }
}
