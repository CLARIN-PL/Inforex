<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterEnumLanguage extends ReportFilterEnum {

    function __construct(){
        parent::__construct("lang", "Language");
        $this->addSqlJoin(new SqlBuilderJoin("lang", "ln", "r.lang = ln.code"));
        $this->setSqlColumnKey(new SqlBuilderSelect("r.lang", "lang_code"));
        $this->setSqlColumnText(new SqlBuilderSelect("ln.language", "language"));
    }

    function applyTo($sqlBuilder){
        if ( count($this->getValue()) == 1 ) {
            $sqlBuilder->addWhere(new SqlBuilderWhere("r.lang = ?", $this->getValue()));
        } else {
            $set = implode("?", array_fill(0, count($this->getValue()), "?"));
            $sqlBuilder->addWhere(new SqlBuilderWhere("r.lang IN ($set)", $this->getValue()));
        }
    }
}
