<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnStatus extends ReportColumn {

    function __construct(){
        parent::__construct("status_name", "document status", "Status", false);
        $this->width = 100;
        $this->align = "center";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $sqlBuilder->addJoinTable(
            new SqlBuilderJoin("reports_statuses", "rs", "r.status=rs.id"));
        $sqlBuilder->addSelectColumn(
            new SqlBuilderSelect("rs.status", "status_name"));
    }

}