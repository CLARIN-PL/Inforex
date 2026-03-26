<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PHPUnitTools {

    static public function isPHPUnitRunning() {

        // detect if code running under PHPUnit control
        //  two constatnts are defined by this system
        if (! defined('PHPUNIT_COMPOSER_INSTALL') 
            && ! defined('__PHPUNIT_PHAR__')) {
            // is not PHPUnit run
            return False;
        } else {
            return True;
        } 

    } // isPHPUnitRunning()

} // PHPUnitTools class

?>
