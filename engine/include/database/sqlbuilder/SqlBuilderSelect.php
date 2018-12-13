<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class SqlBuilderSelect {

    var $column = null;
    var $alias = null;

    function __construct($column, $alias){
        $this->column = $column;
        $this->alias = $alias;
    }

    function getColumn(){
        return $this->column;
    }

    function getAlias(){
        return $this->alias;
    }
}