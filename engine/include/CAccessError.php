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
    var $page;

    function __construct($message, $rolesRequired, $rolesGranted, $page){
        $this->message = $message;
        $this->rolesRequired = $rolesRequired;
        $this->rolesGranted = $rolesGranted;
        $this->page = $page;
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

    function getPage(){
        return $this->page;
    }
}