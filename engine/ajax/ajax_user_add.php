<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_add extends CPageAdministration {

    function execute(){

        $sql = "INSERT INTO users ( login, screename, email, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', '{$_POST['email']}', MD5('{$_POST['password']}'))";
        $this->getDb()->execute($sql);
        $error = $this->getDb()->errorInfo();
        if(isset($error[0])) {
            $this->set("action_error", "Error: (" . $error[1] . ") -> " . $error[2]);
        }

        try{
            $this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$this->getDb()->last_id());
        } catch(Exception $e){
            $this->set("action_error", "Error: ".$e->getMessage());
        }

        $data = array("id" => $this->getDb()->last_id());

        return $data;
    }
}
