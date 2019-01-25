<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_corpus_documents extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
        $this->includeJs("libs/lz-string.js");
    }

	function execute(){
		$this->set("from", 0);
        $filters = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        $columns = new ReportListColumns($this->getDb(), $this->getCorpusId());

        $this->set("columns", $columns->getColumns());
        $this->set("filter_notset", $filters->getFiltersInactive());
        $this->set("filter_active", $filters->getFiltersActive());
        $this->set('corpus_flag_ids', DbCorporaFlag::getCorpusFlags($this->getCorpusId()));
        $this->set('available_flags', DbFlag::getAll());
        $this->set('subcorpora', DbCorpus::getCorpusSubcorpora($this->getCorpusId()));
	}

}