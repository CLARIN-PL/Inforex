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
        $exists = 0;
        if ($mode == 'create'){
            if($login !=='') {
                $exists = DbUser::checkUserLoginExists($login);
            }
        } else{
            $id = $_POST['id'];
            if($login !=='') {
                $exists = DbUser::checkUserIdAndLoginExists($login, $id);
            }
        }
        return $exists == 1;
    }
}
