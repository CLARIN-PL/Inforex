<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportColumnTokenization extends ReportColumn {

    function __construct(){
        parent::__construct("tokenization", "tokenization method", "Tokenization", false);
        $this->width = 100;
        $this->align = "center";
    }

    /**
     * @param SqlBuilder $sqlBuilder
     */
    function applyTo(&$sqlBuilder){
        $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r.tokenization", "tokenization"));
    }

}