<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_relations extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }

	function execute(){
		$report_id = intval($_POST['report_id']);

		$sql = 	"SELECT DISTINCT source_id, target_id " .
				"FROM relations " .
				"WHERE source_id " .
				"IN " .
					"(SELECT id " .
					"FROM reports_annotations " .
					"WHERE report_id={$report_id})"; 

		$result = db_fetch_rows($sql);
		
		return $result;
	}
	
}