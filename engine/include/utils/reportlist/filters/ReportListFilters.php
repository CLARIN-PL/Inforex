<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Handle the filtered list of reports to display.
 * Responsible for:
 * * reading user configuration,
 * * loading configuration from session,
 * * storing configuration in session,
 */
class ReportListFilters {

    var $cid = null;
    var $filters = array();
    var $order = array();

    /**
     * ReportFilter constructor.
     * @param $corpusId
     */
    function __construct($db, $corpusId){
        $this->cid = $corpusId;

        $this->order = $this->getFilterOrder();
        $this->filters = $this->createFilters();

        $this->loadValues();
        $this->saveValues();

        $this->setFilterOrder($this->order);

        $this->loadItems($db);
    }

    function getFilterOrder(){
        $keyC = sprintf("filter_order_%d", $this->cid);
        $order = isset($_COOKIE[$keyC]) ? explode(",",$_COOKIE[$keyC]) : array();
        if (isset($_GET["filter_order"])){
            $order = explode(",", $_GET["filter_order"]);
        }
        setcookie($keyC, implode(",", $order));
        return $order;
    }

    function loadValues(){
        foreach ($this->filters as $k=>&$f){
            $name = sprintf("filter_%d_%s", $this->cid, $f->getKey());
            if ( isset($_COOKIE[$name]) && $_COOKIE[$name] != "" ){
                $val = $_COOKIE[$name];
                $f->setValue($val == "" ? array() : explode("|", $val));
            }
        }
        foreach ($this->filters as &$f){
            $name = $f->getKey();
            if ( isset($_GET[$name]) ){
                $val = $_GET[$name];
                $f->setValue($val == "" ? array() : explode("|", $val));
            }
        }
    }

    function setFilterOrder($order){
        foreach ($this->filters as $f){
            $tOrder = array_replace([], $order);
            if ( !in_array($f->getKey(), $tOrder) ){
                $tOrder[] = $f->getKey();
            }
            $f->setOrder(implode(",", $tOrder));
        }
    }

    function saveValues(){
        foreach ($this->filters as $f){
            $name = sprintf("filter_%d_%s", $this->cid, $f->getKey());
            $value =  implode("|", $f->getValue());
            setcookie($name, $value);
        }
    }

    function createFilters(){
        $filters = array();
        $filters[] = new ReportFilterSearch();
        if (DbToken::getTokenCountByCorpusId($this->cid)) {
            $filters[] = new ReportFilterBase();
        }
        $filters[] = new ReportFilterEnumSubcorpus();
        $filters[] = new ReportFilterEnumLanguage();

        $rows = DbCorporaFlag::getCorpusFlags($this->cid);
        foreach ($rows as $row){
            $key = $row[DB_COLUMN_CORPORA_FLAGS__CORPORA_FLAG_ID];
            $name = $row[DB_COLUMN_CORPORA_FLAGS__NAME];
            $filter = new ReportFilterEnumFlag($key, $name);
            $filter->setDescription($row[DB_COLUMN_CORPORA_FLAGS__DESCRIPTION]);
            $filters[] = $filter;
        }

        $filtersMap = array();
        foreach ($filters as $f){
            $filtersMap[$f->getKey()] = $f;
        }
        return $filtersMap;
    }

    function createBaseSql(){
        $baseSql = new SqlBuilder("reports", "r");
        $baseSql->addSelectColumn(new SqlBuilderSelect("r.id", "id"));
        $baseSql->addWhere(new SqlBuilderWhere("r.corpora = ?", array($this->cid)));
        return $baseSql;
    }

    function getSql(){
        $baseSql = new SqlBuilder("reports", "r");
        $baseSql->addSelectColumn(new SqlBuilderSelect("r.id", "id"));
        $baseSql->addWhere(new SqlBuilderWhere("r.corpora = ?", array($this->cid)));
        foreach ($this->getFiltersActive(true) as $f){
            $f->applyTo($baseSql);
        }
        return $baseSql;
    }

    function getBaseSql(){
        return $this->baseSql;
    }

    /**
     * @param Database $db
     */
    function loadItems($db){
        $sql = $this->createBaseSql();
        foreach ($this->filters as $f){
            if ($f->isActive()){
                ChromePhp::log($sql);
                ChromePhp::log($f);
                if ( is_subclass_of($f, "ReportFilterEnum") ) {
                    ChromePhp::log("loadItems");
                    $f->loadItems($db, clone $sql);
                }
                $f->applyTo($sql);
            }
        }
        foreach ($this->filters as $f){
            if ( !$f->isActive() &&  is_subclass_of($f, "ReportFilterEnum")){
                $f->loadItems($db, clone $sql);
            }
        }
    }

    function getFilters(){
        return $this->filters;
    }

    function getFiltersActive(){
        $filters = array();
        foreach ($this->order as $key){
            if ( isset($this->filters[$key])){
                $filter = $this->filters[$key];
                if ( $filter->isActive() ){
                    $filters[] = $filter;
                }
            }
        }
        foreach ($this->filters as $k=>$f){
            if ($f->isActive() && !in_array($k, $this->order)){
                $filters[] = $f;
            }
        }
        return $filters;
    }

    function getFiltersInactive(){
        $filters = array();
        foreach ($this->filters as $k=>$f){
            if (!$f->isActive()){
                $filters[] = $f;
            }
        }
        return $filters;
    }

}