<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportListColumns{

    /**
     * @var array(ReportColumn) array
     */
    var $columns = array();

    var $cid = null;

    /**
     * ReportListColumns constructor.
     * @param Database $db
     * @param int $cid
     */
    function __construct($db, $cid){
        $this->cid = $cid;
        $this->columns = $this->createColumns();
        $this->loadValues();
    }

    function loadValues(){
        $name = sprintf("report_columns_%d", $this->cid);
        $values = isset($_COOKIE[$name]) ? explode(",", $_COOKIE[$name]) : array();
        if ( isset($_GET["columns"]) ){
            $values = is_array($_GET["columns"]) ? $_GET["columns"] : array($_GET["columns"]);
        }
        $valuesSet = arrayToAssoc($values);
        foreach ($this->columns as &$c){
            if ( isset($valuesSet[$c->getKey()]) ){
                $c->setVisible(true);
            }
        }
        setcookie($name, implode(",", $values));
    }

    function createColumns(){
        $columns = array();
        $columns[] = new ReportColumnId();
        $columns[] = new ReportColumnSubcorpus();
        $columns[] = new ReportColumnStatus();
        $columns[] = new ReportColumnTokenization();
        foreach (DbCorporaFlag::getCorpusFlags($this->cid) as $row){
            $key = $row[DB_COLUMN_CORPORA_FLAGS__CORPORA_FLAG_ID];
            $name = $row[DB_COLUMN_CORPORA_FLAGS__NAME];
            $short = $row[DB_COLUMN_CORPORA_FLAGS__SHORT];
            $column = new ReportColumnFlag($key, $name, $short);
            $column->setDescription($row[DB_COLUMN_CORPORA_FLAGS__DESCRIPTION]);
            $columns[] = $column;
        }
        $columns[] = new ReportColumnTitle();
        return $columns;
    }

    function getColumns(){
        return $this->columns;
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo($sqlBuilder){

    }

    /**
     * Apply post processing to the table row.
     * @param $row
     */
    function postProcessTableRow(&$row){
        ChromePhp::log($row);
        foreach ($this->columns as $c){
            if ( isset($row[$c->getKey()]) ){
                $row[$c->getKey()] = $c->postProcessValue($row[$c->getKey()], $row);
            }
        }
    }
}