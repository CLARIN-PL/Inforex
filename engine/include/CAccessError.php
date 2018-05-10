<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class AccessError {

    var $message;
    var $rolesRequired = array();
    var $rolesGranted = array();

    function __construct($message, $rolesRequired, $rolesGranted){
        $this->message = $message;
        $this->rolesRequired = $rolesRequired;
        $this->rolesGranted = $rolesGranted;
    }

    function getMessage(){
        return $this->message;
    }

    function getRolesRequired(){
        return $this->rolesRequired;
    }

    function getRolesGranted(){
        return $this->rolesGranted;
    }
}