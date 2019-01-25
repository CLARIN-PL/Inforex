<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnId extends ReportColumn {

    function __construct(){
        parent::__construct("id", "document id", "Id", false, true);
        $this->width = 50;
        $this->align = "center";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r.id", "id"));
    }

}