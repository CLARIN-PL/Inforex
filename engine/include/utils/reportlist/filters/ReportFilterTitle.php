<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterTitle extends ReportFilter {

    function __construct(){
        parent::__construct("title", "Title contains a phrase");
        $this->template = "report_filters/inc_filter_search.tpl";
    }

    public function applyTo($sqlBuilder){
        global $db;
        $search_escaped = $db->quote(implode(" ", $this->getValue()));
        $sqlBuilder->addWhere(new SqlBuilderWhere("r.title LIKE CONCAT('%',".$search_escaped.",'%')", array()));
    }
}
