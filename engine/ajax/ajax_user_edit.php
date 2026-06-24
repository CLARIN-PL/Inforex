<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_edit extends CPageAdministration {

    function execute(){

        $values = array();
        $keys = array();

        $user_id = $_POST['user_id'];

        $values['login'] = strval($_POST['login']);
        $values['screename'] = strval($_POST['name']);
        $values['email'] = strval($_POST['email']);
        $keys['user_id'] = intval($user_id);
        $this->getDb()->update("users", $values, $keys);

        if (!empty($_POST['unlink_auth_identity'])) {
            DbUser::unlinkAuthIdentity($user_id);
        }

        $roles = $_POST['roles'];
        if ( !is_array($roles) ){
            $roles = array();
        }
        DbUserRoles::set($user_id, $roles);

        $error = $this->getDb()->errorInfo();
        if(isset($error[0]))
            $this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
        else
            $this->set("action_performed", "Updated user \"". $_POST['name'] ."\"");

        return null;
    }
}
