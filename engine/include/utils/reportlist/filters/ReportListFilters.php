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
    var $filterState = null;

    /**
     * ReportFilter constructor.
     * @param $corpusId
     */
    function __construct($db, $corpusId, $userId){
        $this->cid = $corpusId;
        $this->userId = $userId;

        $this->filters = $this->createFilters();
        $this->loadValues();

        $order = $this->getFilterOrder();
        $order = $this->validateFilterOrder($order);
        $this->order = $order;
        $this->saveFilterOrder($order);

        $this->saveValues();

        $this->setFilterOrder($order);
        $this->loadItems($db, $order);
    }

    function shouldPreloadInactiveEnumItems($filter){
        return false;
    }

    function getFilter($key){
        return isset($this->filters[$key]) ? $this->filters[$key] : null;
    }

    function loadItemsForFilter($db, $filterKey){
        $filter = $this->getFilter($filterKey);
        if (!$filter || !is_subclass_of($filter, "ReportFilterEnum")) {
            return false;
        }

        $sql = $this->createBaseSql();
        foreach ($this->getFiltersActive() as $activeFilter){
            $activeFilter->applyTo($sql);
        }

        $filter->loadItems($db, clone $sql);
        return true;
    }

    function getFilterOrder(){
        $state = $this->getFilterState();
        $keyC = sprintf("filter_order_%d", $this->cid);
        $order = isset($state["o"]) ? explode(",", $state["o"]) : array();
        if (count($order) == 0 && isset($_COOKIE[$keyC])) {
            $order = explode(",", $_COOKIE[$keyC]);
        }
        if (isset($_GET["filter_order"])){
            $order = explode(",", $_GET["filter_order"]);
        }
        return $order;
    }

    function validateFilterOrder($order){
        foreach ($this->filters as $f){
            if ($f->isActive() && !in_array($f->getKey(), $order)){
                $order[] = $f->getKey();
            } if (!$f->isActive() && in_array($f->getKey(), $order)){
                $order = array_diff($order, array($f->getKey()));
            }
        }
        $order = array_filter($order);
        return $order;
    }

    function saveFilterOrder($order){
        $this->order = $order;
    }

    function loadValues(){
        $reset = isset($_GET['reset']) ? intval($_GET['reset']) > 0 : false;
        $state = $reset ? array() : $this->getFilterState();
        if (!$reset) {
            foreach ($this->filters as $k => &$f) {
                $name = sprintf("filter_%d_%s", $this->cid, $f->getKey());
                if (isset($state[$f->getKey()]) && $state[$f->getKey()] != "") {
                    $val = $state[$f->getKey()];
                    $f->setValue($val == "" ? array() : explode(",", $val));
                } elseif (isset($_COOKIE[$name]) && $_COOKIE[$name] != "") {
                    $val = $_COOKIE[$name];
                    $f->setValue($val == "" ? array() : explode(",", $val));
                }
            }
        }
        foreach ($this->filters as &$f){
            $name = $f->getKey();
            if ( isset($_GET[$name]) ){
                $val = $_GET[$name];
                $f->setValue($val == "" ? array() : explode(",", $val));
            }
        }
    }

    function setFilterOrder($order){
        foreach ($this->filters as $f){
            $tOrder = array_replace(array(), $order);
            if ( !in_array($f->getKey(), $tOrder) ){
                $tOrder[] = $f->getKey();
            }
            $f->setOrder(implode(",", $tOrder));
        }
    }

    function saveValues(){
        $state = array();
        if (count($this->order) > 0) {
            $state["o"] = implode(",", $this->order);
        }

        foreach ($this->filters as $f){
            $value =  implode(",", $f->getValue());
            if ($value !== "") {
                $state[$f->getKey()] = $value;
            }
        }

        $this->setCookieIfChanged($this->getFilterStateCookieName(), $this->buildFilterStateCookie($state));
        $this->clearLegacyCookies();
    }

    function setCookieIfChanged($name, $value){
        $currentValue = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;

        if ($value === "") {
            if ($currentValue !== null) {
                setrawcookie($name, "", time() - 3600);
            }
            return;
        }

        if ($currentValue !== $value) {
            setrawcookie($name, $value);
        }
    }

    function getFilterStateCookieName(){
        return sprintf("rf_%d", $this->cid);
    }

    function getFilterState(){
        if ($this->filterState !== null) {
            return $this->filterState;
        }

        $state = array();
        $cookieName = $this->getFilterStateCookieName();
        if (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] !== "") {
            foreach (explode("&", $_COOKIE[$cookieName]) as $part) {
                if ($part === "") {
                    continue;
                }
                $item = explode("=", $part, 2);
                $key = rawurldecode($item[0]);
                $value = isset($item[1]) ? rawurldecode($item[1]) : "";
                if ($key !== "") {
                    $state[$key] = $value;
                }
            }
        }

        $this->filterState = $state;
        return $state;
    }

    function buildFilterStateCookie($state){
        $parts = array();
        foreach ($state as $key => $value) {
            if ($value !== "") {
                $parts[] = rawurlencode($key) . "=" . rawurlencode($value);
            }
        }
        return implode("&", $parts);
    }

    function clearLegacyCookies(){
        $orderCookie = sprintf("filter_order_%d", $this->cid);
        if (isset($_COOKIE[$orderCookie])) {
            setrawcookie($orderCookie, "", time() - 3600);
        }

        foreach ($this->filters as $f) {
            $name = sprintf("filter_%d_%s", $this->cid, $f->getKey());
            if (isset($_COOKIE[$name])) {
                setrawcookie($name, "", time() - 3600);
            }
        }
    }

    function createFilters(){
        $filters = array();
        $filters[] = new ReportFilterSelected($this->userId);
        $filters[] = new ReportFilterSearch();
        $filters[] = new ReportFilterTitle();
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
        $baseSql->addWhere(new SqlBuilderWhere("r.deleted = 0"));
        return $baseSql;
    }

    function getSql(){
        $baseSql = new SqlBuilder("reports", "r");
        $baseSql->addSelectColumn(new SqlBuilderSelect("r.id", "id"));
        $baseSql->addWhere(new SqlBuilderWhere("r.corpora = ?", array($this->cid)));
        $baseSql->addWhere(new SqlBuilderWhere("r.deleted = 0"));
        foreach ($this->getFiltersActive() as $f){
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
    function loadItems($db, $order){
        $sql = $this->createBaseSql();

        $map = array();
        foreach ($this->filters as $f){
            $map[$f->getKey()] = $f;
        }

        foreach ($order as $o){
            if (isset($map[$o])){
                $f = $map[$o];
                if ($f->isActive()){
                    if ( is_subclass_of($f, "ReportFilterEnum") ) {
                        $f->loadItems($db, clone $sql);
                    }
                    $f->applyTo($sql);
                }
            }
        }

        foreach ($this->filters as $f){
            if ($f->isActive() && !in_array($f->getKey(), $order)){
                if ( is_subclass_of($f, "ReportFilterEnum") ) {
                    $f->loadItems($db, clone $sql);
                }
                $f->applyTo($sql);
            }
        }

        foreach ($this->filters as $f){
            if ( !$f->isActive() &&  is_subclass_of($f, "ReportFilterEnum")){
                if (!$this->shouldPreloadInactiveEnumItems($f)) {
                    continue;
                }
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
