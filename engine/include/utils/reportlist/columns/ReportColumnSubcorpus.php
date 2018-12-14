<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnSubcorpus extends ReportColumn {

    function __construct(){
        parent::__construct("subcorpus_name", "Subcorpus name", "Subcorpus", false, true);
        $this->width = 100;
        $this->align = "center";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $sqlBuilder->addJoinTable(
            new SqlBuilderJoin("corpus_subcorpora", "cs", "r.subcorpus_id=cs.subcorpus_id"));
        $sqlBuilder->addSelectColumn(
            new SqlBuilderSelect("cs.name", "subcorpus_name"));
    }

}