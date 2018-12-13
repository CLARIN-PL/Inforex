<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class SqlBuilderJoin {

    var $table = null;
    var $tableAlias = null;
    var $joinOn = array();
    var $params = array();

    function __construct($table, $tableAlias, $joinOn, $params=array()){
        $this->table = $table;
        $this->tableAlias = $tableAlias;
        $this->joinOn = $joinOn;
        $this->params = $params;
    }

    function getTable(){
        return $this->table;
    }

    function getTableAlias(){
        return $this->tableAlias;
    }

    function getJoinOn(){
        return $this->joinOn;
    }

    function getParams(){
        return $this->params;
    }
}