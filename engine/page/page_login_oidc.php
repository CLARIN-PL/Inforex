<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_login_oidc extends CPagePublic
{
    function execute()
    {
        $this->redirect('index.php');
    }
}

?>
