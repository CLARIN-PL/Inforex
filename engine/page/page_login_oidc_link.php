<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_login_oidc_link extends CPagePublic
{
    function execute()
    {
        global $auth;

        $context = $auth->getOidcLinkContext();
        if (!$context) {
            $this->redirect('index.php?page=login_oidc');
            return;
        }

        $this->set('screenname', $context['screenname']);
        $this->set('email', $context['email']);
        $this->set('username', $context['username']);
        $this->set('provider', $context['provider']);
    }
}

?>
