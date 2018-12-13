<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnTitle extends ReportColumn {

    function __construct(){
        parent::__construct("title", "document title", "Title", false, true);
        $this->width = "600";
        $this->align = "left";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r.corpora", "corpora"));
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r.title", "title"));
    }

    function postProcessValue($value, $row){
        $link = '<a href="index.php?page=report&corpus=%d&id=%d">%s</a>';
        return sprintf($link, $row['corpora'], $row['id'], $value);
    }
}