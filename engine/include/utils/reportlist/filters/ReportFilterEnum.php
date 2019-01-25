<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterEnum extends ReportFilter {

    /** @var ReportFilterEnumItem array  */
    var $items = array();

    /**
     * ReportFilterEnum constructor.
     * @param String $key
     * @param String $name
     */
    function __construct($key, $name){
        parent::__construct($key, $name);
        $this->template = "report_filters/inc_filter_enum.tpl";
    }

    /**
     * @param Database $db
     * @param SqlBuilder $sqlBuilder
     */
    function loadItems($db, $sqlBuilder){
        foreach ($this->getSqlJoin() as $join){
            $sqlBuilder->addJoinTable($join);
        }
        $alias = $this->getKey() . "_count";
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", $alias));
        $sqlBuilder->addSelectColumn($this->getSqlColumnKey());
        $sqlBuilder->addSelectColumn($this->getSqlColumnText());
        $sqlBuilder->addGroupBy($this->getSqlColumnKey()->getAlias());
        $sqlBuilder->addOrderBy($this->getSqlColumnText()->getAlias());
        list($sql, $params) = $sqlBuilder->getSql();
        $rows = $db->fetch_rows($sql, $params);

        $items = array();
        foreach ($rows as $row){
            $key = $row[$this->getSqlColumnKey()->getAlias()];
            $items[] = new ReportFilterEnumItem(
                $key,
                $row[$this->getSqlColumnText()->getAlias()],
                in_array($key, $this->getValue()),
                $row[$alias]);
        }
        $this->items = $items;
    }

    function getItems(){
        return $this->items;
    }
}
