<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterSearch extends ReportFilter {

    function __construct(){
        parent::__construct("search", "Content contains a phrase");
        $this->template = "report_filters/inc_filter_search.tpl";
    }

    public function applyTo($sqlBuilder){
        global $db;
        $search_escaped = $db->mdb2->quote(implode(" ", $this->getValue()), "text", true);
        $sqlBuilder->addWhere(new SqlBuilderWhere("r.content LIKE CONCAT('%',".$search_escaped.",'%')", array()));
    }
}
