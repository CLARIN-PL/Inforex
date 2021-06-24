<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_user_validation extends CPageAdministration {

    function execute(){
        $mode = $_POST['mode'];
        $login = $_POST['login'];

        if ($mode == 'create'){
            $exists = DbUser::checkUserLoginExists($login);
        } else{
            $id = $_POST['id'];
            $exists = DbUser::checkUserIdAndLoginExists($login, $id);
        }
        return $exists == 1;
    }
}
