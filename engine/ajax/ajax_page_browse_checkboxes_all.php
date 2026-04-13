<?php

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * This class handles the logic of the checkboxes. Information about the documents selected by the user
 * is stored in the database in the table `ReportUserSelection`.
 */

class Ajax_page_browse_checkboxes_all extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    function execute(){
        $filters = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        list($sql, $params) = $filters->getSql()->getSql();

        $this->getDb()->execute(
            "REPLACE INTO reports_users_selection (user_id, report_id) SELECT ?, id FROM ($sql) AS selected_reports",
            array_merge(array($this->getUserId()), $params));
        return "";
    }
}
