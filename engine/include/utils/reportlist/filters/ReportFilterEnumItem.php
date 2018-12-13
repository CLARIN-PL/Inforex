<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class ReportFilterEnumItem {

    var $key = "";
    var $name = "";
    var $selected = false;
    var $count = 0;

    function __construct($key, $name, $selected, $count){
        $this->key = $key;
        $this->name = $name;
        $this->selected = $selected;
        $this->count = $count;
    }

    function isSelected(){
        return $this->selected;
    }

    function getKey(){
        return $this->key;
    }

    function getName(){
        return $this->name;
    }

    function getCount(){
        return $this->count;
    }
}