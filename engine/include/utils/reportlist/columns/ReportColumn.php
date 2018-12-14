<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumn{


    var $name;
    var $key;
    var $header;
    var $description;
    var $visible;
    var $pinned;

    var $width = 100;
    var $align;

    function __construct($key, $name, $header, $description=null, $pinned=false){
        $this->key = $key;
        $this->name = $name;
        $this->header = $header;
        $this->description = $description;
        $this->pinned = $pinned;
    }

    function getName(){
        return $this->name;
    }

    function getKey(){
        return $this->key;
    }

    function getHeader(){
        return $this->header;
    }

    function getDescription(){
        return $this->description;
    }

    function setDescription($description){
        $this->description = $description;
    }

    function isVisible(){
        return $this->visible;
    }

    function setVisible($visible){
        $this->visible = $visible;
    }

    function isPinned(){
        return $this->pinned;
    }

    function getWidth(){
        return $this->width;
    }

    function getAlign(){
        return $this->align;
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        ChromePhp::error( get_class($this) . "->applyTo not defined");
    }

    /**
     * @param $value original value
     * @return post processed value
     */
    function postProcessValue($value, $row){
        return $value;
    }
}