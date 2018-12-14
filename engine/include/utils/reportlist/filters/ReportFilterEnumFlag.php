<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterEnumFlag extends ReportFilterEnum {

    var $flagId=null;

    function __construct($flagId, $flagName){
        parent::__construct("flag_$flagId", "Flag: $flagName");
        $this->flagId = $flagId;
        $flagKey = "fl_$flagId";
        $flagKeyF = "flf_$flagId";
        $this->addSqlJoin(new SqlBuilderJoin("reports_flags", $flagKey, "r.id = $flagKey.report_id AND $flagKey.corpora_flag_id = ?", array($this->flagId)));
        $this->addSqlJoin(new SqlBuilderJoin("flags", $flagKeyF, "IFNULL($flagKey.flag_id, -1) = $flagKeyF.flag_id"));
        $this->setSqlColumnKey(new SqlBuilderSelect("IFNULL($flagKey.flag_id,-1)", "{$flagKey}_id"));
        $this->setSqlColumnText(new SqlBuilderSelect("$flagKeyF.name", "{$flagKey}_name"));
        $this->template = "report_filters/inc_filter_flag.tpl";
    }

    function applyTo(&$sqlBuilder){
        $flagKey = "fl_" . $this->flagId;
        $sqlBuilder->addJoinTable(new SqlBuilderJoin("reports_flags", $flagKey, "r.id = $flagKey.report_id AND $flagKey.corpora_flag_id = ?", array($this->flagId)));

        $set = implode(",", array_fill(0, count($this->getValue()), "?"));
        $sqlBuilder->addWhere(new SqlBuilderWhere("IFNULL($flagKey.flag_id,-1) IN ($set)", $this->getValue()));
    }
}
