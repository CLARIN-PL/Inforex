<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportFilterBase extends ReportFilter {

    function __construct(){
        parent::__construct("base", "Token base form (slow!)");
        $this->template = "report_filters/inc_filter_search.tpl";
    }

    public function applyTo($sqlBuilder){
        $search = implode(" ", $this->getValue());
        $where = "EXISTS (SELECT 1 FROM tokens AS t
  JOIN tokens_tags_optimized AS tto ON (t.token_id=tto.token_id)
  JOIN bases AS b ON (tto.base_id=b.id)
  WHERE t.report_id = r.id AND b.text = ?)";
        $sqlBuilder->addWhere(new SqlBuilderWhere($where, array($search)));
    }
}
