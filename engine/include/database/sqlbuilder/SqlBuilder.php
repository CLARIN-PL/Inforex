<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class SqlBuilder {

    var $table;
    var $tableAlias;
    var $select = array();
    var $joins = array();
    var $wheres = array();
    var $groupBy = array();
    var $orderBy = array();
    var $offset = null;
    var $limit = null;

    function __construct($table, $tableAlias){
        $this->table = $table;
        $this->tableAlias = $tableAlias;
    }

    function setSelectColumn($columns){
        $this->select = array();
        foreach ($columns as $c){
            $this->addSelectColumn($c);
        }
    }

    /**
     * @param SqlBuilderSelect $column
     */
    function addSelectColumn($column){
        $this->select[$column->getColumn()] = $column->getAlias();
    }

    function addJoinTable($sqlBuilderJoin){
        $this->joins[$sqlBuilderJoin->getTableAlias()] = $sqlBuilderJoin;
    }

    /**
     * @param SqlBuilderWhere $sqlBuilderWhere
     */
    function addWhere($sqlBuilderWhere){
        $this->wheres[$sqlBuilderWhere->getCondition()] = $sqlBuilderWhere;
    }

    function addGroupBy($column){
        $this->groupBy[] = $column;
    }

    function addOrderBy($column){
        $this->orderBy[] = $column;
    }

    function setLimitOffset($limit, $offset){
        $this->offset = $offset;
        $this->limit = $limit;
    }

    function getSql(){
        $params = array();
        $sql = "SELECT " . $this->getSqlSelect();
        $sql .= " FROM " . $this->table . " " . $this->tableAlias;

        list($sqlJoin, $paramJoin) = $this->getSqlJoin();
        $sql .= $sqlJoin;
        $params = array_merge($params, $paramJoin);

        if ( count($this->wheres) > 0 ){
            $sqlWhere = array();
            foreach ($this->wheres as $where){
                $sqlWhere[] = $where->getCondition();
                $params = array_merge($params, $where->getParameters());
            }
            $sql .= " WHERE " . implode(" AND ", $sqlWhere);
        }
        $sql .= $this->getSqlGroupBy();
        $sql .= $this->getSqlOrderBy();
        if ( $this->limit ){
            $sql .= " LIMIT " . intval($this->limit);
            if ( $this->offset ){
                $sql .= " OFFSET " . intval($this->offset);
            }
        }
        return array($sql, $params);
    }

    function getSqlSelect(){
        $select = [];
        foreach ($this->select as $k=>$v) {
            $select[] =  $v==null ? $k : "$k $v";
        }
        return implode(", ", $select);
    }

    function getSqlGroupBy(){
        if ( count($this->groupBy) > 0 ){
            return " GROUP BY " . implode(", ", $this->groupBy);
        } else {
            return "";
        }
    }

    function getSqlJoin(){
        $joinSql = "";
        $params = array();
        foreach ($this->joins as $join){
            $joinSql .= sprintf(" LEFT JOIN %s AS %s ON (%s)", $join->getTable(), $join->getTableAlias(), $join->getJoinOn());
            $params = array_merge($params, $join->getParams());
        }
        return array($joinSql, $params);
    }

    function getSqlOrderBy(){
        if ( count($this->orderBy) == 0 ){
            return "";
        } else {
            return " ORDER BY " . implode(", ", $this->orderBy);
        }
    }
}