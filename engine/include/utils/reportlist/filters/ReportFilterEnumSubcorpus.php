<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterEnumSubcorpus extends ReportFilterEnum {

    function __construct(){
        parent::__construct("subcorpus", "Subcorpus");
        $this->addSqlJoin(new SqlBuilderJoin("corpus_subcorpora", "cs", "r.subcorpus_id = cs.subcorpus_id"));
        $this->setSqlColumnKey(new SqlBuilderSelect("r.subcorpus_id", "subcorpus_id"));
        $this->setSqlColumnText(new SqlBuilderSelect("cs.name", "subcorpus_name"));
    }

    function applyTo($sqlBuilder){
        if ( count($this->getValue()) == 1 ) {
            $sqlBuilder->addWhere(new SqlBuilderWhere("r.subcorpus_id = ?", $this->getValue()));
        } else {
            $set = implode("?", array_fill(0, count($this->getValue()), "?"));
            $sqlBuilder->addWhere(new SqlBuilderWhere("r.subcorpus_id IN ($set)", $this->getValue()));
        }
    }
}
