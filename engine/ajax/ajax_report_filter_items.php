<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_report_filter_items extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    function execute(){
        $filterKey = isset($_POST['filter_key']) ? trim($_POST['filter_key']) : "";
        if ($filterKey === "") {
            throw new UserDataException("Missing filter key.");
        }

        $filters = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        $filter = $filters->getFilter($filterKey);
        if ($filter === null) {
            throw new UserDataException("Unknown filter key.");
        }

        if ($filter->isLazyLoadable() && !$filter->hasItemsLoaded()) {
            $filters->loadItemsForFilter($this->getDb(), $filterKey);
        }

        $this->set("filter", $filter);
        $this->set("page", "corpus_documents");
        $this->set("corpus", $this->getCorpus());

        $templatePath = Config::Cfg()->get_path_engine() . "/templates/" . $filter->getTemplate();
        $html = $this->template->fetch($templatePath);

        return array(
            "filter_key" => $filterKey,
            "html" => $html
        );
    }
}
