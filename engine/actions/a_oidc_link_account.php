<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Action_oidc_link_account extends CAction
{
    var $isSecure = false;

    function execute()
    {
        global $auth;

        $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
        try {
            if ($mode === 'update') {
                $login = isset($_POST['username']) ? trim($_POST['username']) : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                if ($login === '' || $password === '') {
                    $this->redirect('index.php?page=login_oidc_link&error=login');
                    return 'login_oidc_link';
                }

                $user = DbUser::verifyLegacyPassword($login, $password);
                if (!$user) {
                    $this->redirect('index.php?page=login_oidc_link&error=login');
                    return 'login_oidc_link';
                }

                $auth->linkPendingOidcIdentityToUser($user['user_id']);
                $this->redirect($auth->consumePostLoginReturnUrl());
                return 'home';
            }

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            if ($email === '' || $name === '') {
                $this->redirect('index.php?page=login_oidc_link&error=email_empty');
                return 'login_oidc_link';
            }

            $auth->createUserFromPendingOidcIdentity($name, $email);
            $this->redirect($auth->consumePostLoginReturnUrl());
            return 'home';
        } catch (Exception $e) {
            $this->redirect('index.php?page=login_oidc_link&error=email_duplicate');
            return 'login_oidc_link';
        }
    }
}

?>
