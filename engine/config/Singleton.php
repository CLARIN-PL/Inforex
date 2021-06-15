<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *
 * Abstract structure for Singleton pattern class
 * requires PHP 5.3+ for static keyword work properly
 */

namespace engine\Config\Singleton;

abstract class Singleton {

    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    protected function  __construct() { }

    /**
     * Prevent object cloning
     */
    final private function  __clone() { }

    /**
     * Returns new or existing Singleton instance
     * @return Singleton
     */
    final public static function getInstance(){
        if(null !== static::$_instance){
            return static::$_instance;
        }
        static::$_instance = new static();
        return static::$_instance;
    }
   
}
?>
