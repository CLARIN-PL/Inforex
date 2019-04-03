<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class SqlBuilderWhere {

    var $condition = null;
    var $parameters = array();

    /**
     * SqlBuilderWhere constructor.
     * @param $condition
     * @param $parameters
     */
    function __construct($condition, $parameters){
        $this->condition = $condition;
        $this->parameters= $parameters;
    }

    function getCondition(){
        return $this->condition;
    }

    function getParameters(){
        return $this->parameters;
    }

}