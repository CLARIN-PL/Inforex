<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CookieResetter {

    /***
     *  test if resetCOOKIES=1 phrase is in GET parameters
     *  if so, delete all cookies saved in user browser for 
     *  this application
     */
    static function resetAllCookies() {

        // if &resetCOOKIES=1 is in URL
        if(isset($_GET['resetCOOKIES']) && $_GET['resetCOOKIES']) {
            // unset cookies
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    // except 'gpw' i 'authchallenge' logged user cookies
                    if(($name != 'gpw') && ($name != 'authchallenge')){
                        setcookie($name, '', time()-1000);
                        setcookie($name, '', time()-1000, '/');
                    }
                }
            }
        }

    } // resetAllCookies()


} // CookieResetter class

?>
