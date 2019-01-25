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
        ChromePhp::log($this->getCorpusId());

        $filters = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        list($sql, $param) = $filters->getSql()->getSql();
        $ids = $this->getDb()->fetch_ones($sql, 'id', $param);

        $values = array();
        foreach ($ids as $id){
            $values[] = $this->getUserId();
            $values[] = $id;
            if ( count($values) > 1000 ) {
                ReportUserSelection::insertCheckboxes($values);
                $values = array();
            }
        }
        if (count($values) > 0){
            ReportUserSelection::insertCheckboxes($values);
        }
        return "";
    }
}