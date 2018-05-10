<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_stats extends CPageCorpus{

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

	function execute(){
		global $corpus;
        $this->includeJs("js/c_autoresize.js");
        $this->manageFilters();

        $corpus_id = $corpus['id'];
        $status = intval($_GET['status']);
        $flag = $_GET['flag'];
        $flag_status = $_GET['flag_status'];
        $set_filters = array();
        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();
        $features = DbCorpus::getCorpusExtColumnsWithMetadata($corpus['ext']);

        $session_flag = $_SESSION['stats']['flags']['flag'];
        $session_flag_status = $_SESSION['stats']['flags']['flag_status'];

        if($session_flag != null && $session_flag_status != null && $session_flag != "-" && $session_flag_status != "-"){
            $this->set("flag_set", true);
        }


        $this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));
        $this->set("flags", $flags);
        $this->set("selected_flag", $flag);
        $this->set("flag_status", $flag_status);
        $this->set("corpus_flags", $corpus_flags);
        $this->set("status", $status);
        $this->set("statuses", $statuses = DbStatus::getAll());
        $this->set("features", $features);
        $this->set("selected_filters", $_SESSION['stats']);
		$this->set('stats', DbCorpusStats::_getStats($corpus['id'], $_SESSION['stats']));
	}

    function manageFilters(){
        $filters = $_GET;

        if(isset($filters['metadata'])){
            $_SESSION['stats']['metadata'][$filters['metadata']] = $filters['value'];
        }

        if(isset($filters['status'])){
            $_SESSION['stats']['status'] = $filters['status'];
        }
        if(isset($filters['flag'])){
            $_SESSION['stats']['flags']['flag'] = $filters['flag'];
            $_SESSION['stats']['flags']['flag_status'] = $filters['flag_status'];
        }
    }
}


