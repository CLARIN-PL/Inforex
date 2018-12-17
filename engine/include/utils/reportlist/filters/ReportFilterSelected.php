<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterSelected extends ReportFilter {

    var $userId;

    function __construct($userId){
        parent::__construct("selected", "User selection");
        $this->template = "report_filters/inc_filter_selected.tpl";
        $this->userId = $userId;
    }

    public function applyTo($sqlBuilder){
        $sqlBuilder->addJoinTable(new SqlBuilderJoin("reports_users_selection", "rus", "rus.report_id=r.id AND rus.user_id=?", array($this->userId)));
        if ( $this->isActive() ) {
            $sqlBuilder->addWhere(new SqlBuilderWhere("rus.report_id IS NOT NULL", array()));
        }
    }
}
