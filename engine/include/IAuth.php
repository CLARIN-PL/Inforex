<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Abstract Auth library interface.
 */
interface IAuth {

    public function __construct($params);
    
    public function start();
    public function logout();

    public function setAuth($username);
    public function setAuthData($name, $value, $overwrite = true);
    public function checkAuth();
    public function getAuthData($name = null);

} // IAuth interface

?>
