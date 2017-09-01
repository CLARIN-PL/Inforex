<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_clarin_new_user extends CPage {


    var $isSecure = false;

    function execute(){
        global $db, $auth;

        $clarin_user = $auth->getClarinUser();
        $clarin_login = $clarin_user['login'];


        if($_POST['mode'] == 'update'){
            if ($auth->checkAuth()){
                $user = $auth->getUserData();
            }else{
                return $this->redirect('index.php');
            }

            DbUser::updateClarinUser($user['user_id'], $clarin_login);
        } else {
            $email = $_POST['email'];
            $name = $_POST['name'];
            try{
                DbUser::createNewUser($clarin_login, $name, $email, 'NOT SET', $clarin_login);
            } catch (Exception $e){
                // todo
                //
            }
        }
        return $this->redirect('index.php');
    }
}
?>
