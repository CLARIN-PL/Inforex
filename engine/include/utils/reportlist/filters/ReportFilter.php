<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilter {

    var $key = "";
    var $name = "";
    var $type = "";
    var $template = "";
    var $value = array();
    var $order = "";
    var $orderCancel = "";
    var $description = null;

    var $sqlJoin = array();
    /** @var SqlBuilderSelect null  */
    var $sqlColumnKey = null;
    /** @var SqlBuilderSelect null  */
    var $sqlColumnText = null;

    function __construct($key, $name){
        $this->key = $key;
        $this->name = $name;
    }

    function getKey(){
        return $this->key;
    }

    function getName(){
        return $this->name;
    }

    function getValue(){
        return $this->value;
    }

    function setValue($value){
        $this->value = $value;
    }

    function getOrder(){
        return $this->order;
    }

    function setOrder($order){
        $this->order = $order;
    }

    function getOrderCancel(){
        return implode(",", array_diff(explode(",", $this->order), array($this->getKey())));
    }

    function getDescription(){
        return $this->description;
    }

    function setDescription($description){
        $this->description = $description;
    }

    /**
     * @param SqlBuilderJoin $sqlBuilderJoin
     */
    function addSqlJoin($sqlBuilderJoin){
        $this->sqlJoin[] = $sqlBuilderJoin;
    }

    function getSqlJoin(){
        return $this->sqlJoin;
    }

    /**
     * @param SqlBuilderSelect $column
     */
    function setSqlColumnKey($column){
        $this->sqlColumnKey = $column;
    }

    function getSqlColumnKey(){
        return $this->sqlColumnKey;
    }

    /**
     * @param SqlBuilderSelect $column
     */
    function setSqlColumnText($column){
        $this->sqlColumnText = $column;
    }

    function getSqlColumnText(){
        return $this->sqlColumnText;
    }

    function getTemplate(){
        return $this->template;
    }

    function isActive(){
        return count($this->value) > 0;
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo($sqlBuilder){
        ChromePhp::error( get_class($this) . "->applyTo not defined");
    }

}
