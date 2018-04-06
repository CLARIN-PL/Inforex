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
            $auth->start();
            if ($auth->checkAuth()){
                $user = $auth->getUserData();
            }else{
                $this->redirect('?error=login');
                return;
            }
            DbUser::updateClarinUser($user['user_id'], $clarin_login);
        } else {
            $email = $_POST['email'];
            $name = $_POST['name'];

            if($email === '' || $name === ''){
                $this->redirect('?error=email_empty');
                return;
            }

            try{
                DbUser::createNewUser($clarin_login, $name, $email, 'NOT SET', $clarin_login);
            } catch (Exception $e){
                $this->redirect('?error=email_duplicate');
                return;
            }
        }
        $this->redirect('index.php');
        return;
    }
}
