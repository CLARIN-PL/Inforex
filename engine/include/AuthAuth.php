<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once 'Auth/Auth.php';

/**
 * Implements  IAuth interface with pear Auth library.
 */
class AuthAuth implements IAuth {

    private $AuthObject = null;

    //public function __construct($storageDriver, $options = '', $loginFunction = '', $showLogin = true) {
    public function __construct($params){ 

        $storageDriver = 'MDB2';
        $loginFunction = null;
        $showLogin = false;
        $this->AuthObject = new Auth($storageDriver, $params, $loginFunction, $showLogin);

    } // __construct()

    public function start() {

        $this->AuthObject->start();

    } // start()

    public function logout() {

        $this->AuthObject->logout();

    } // logout()

    public function setAuth($username) {

        $this->AuthObject->setAuth($username);

    } // setAuth()

    function setAuthData($name, $value, $overwrite = true) {

        $this->AuthObject->setAuthData($name,$value,$overwrite);

    } // setAuthData()

    public function checkAuth() {

        return $this->AuthObject->checkAuth();

    } // checkAuth()

    public function getAuthData($name = null) {

        return $this->AuthObject->getAuthData($name);

    } // getAuthData()

} // AuthAuth
?>
