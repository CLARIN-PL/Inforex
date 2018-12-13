<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnFlag extends ReportColumn {

    var $flagId;
    var $name;
    var $shortName;

    function __construct($flagId, $name, $shortName){
        parent::__construct("flag_$flagId", $name, "F:$shortName", false);
        $this->flagId = $flagId;
        $this->name = $name;
        $this->shortName = $shortName;

        $this->width = 50;
        $this->align = "center";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $alias = sprintf("f%d", $this->flagId);
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("$alias.flag_id", $this->getKey()));
        $sqlBuilder->addJoinTable(new SqlBuilderJoin("reports_flags", $alias, "r.id = $alias.report_id AND $alias.corpora_flag_id=?", array($this->flagId)));
    }

    /**
     * @param $value original value
     * @return post processed value
     */
    function postProcessValue($value, $row){
        return sprintf('<img src="gfx/flag_%s.png" title="" style="vertical-align: baseline">', $value);
    }
}