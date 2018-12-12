<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_corpus_documents2 extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
        $this->includeJs("libs/lz-string.js");
    }

	function execute(){
		//$corpus = $this->getCorpus();
		$this->set("from", 0);
		$this->setTableColumns();
		$this->setFilters();
	}

	function setTableColumns(){
        $columns = array();
        $columns["checkbox_action"] = "checkbox";
        $columns["id"] = "Id";
        $columns["lp"] = "No.";
        $columns["subcorpus_id"] = "Subcorpus";
        $columns["title"] = "Title";
        $columns["status_name"] = "Status";
        $columns["tokenization"] = "Tokenization";
        $this->set("columns", $columns);
    }

    function setFilters(){
        $filters = new FilteredReportList($this->getDb(), $this->getCorpusId());

        $this->set("filter_notset", $filters->getFiltersInactive());
        $this->set("filter_active", $filters->getFiltersActive());
    }

}