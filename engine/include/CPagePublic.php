<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Class CPagePublic represent any page which can be accessed by any user. No access restrictions.
 */
class CPagePublic extends CPage {

    function __construct(){
        parent::__construct();
        $this->anySystemRole = array(ROLE_SYSTEM_USER_PUBLIC);
    }

}