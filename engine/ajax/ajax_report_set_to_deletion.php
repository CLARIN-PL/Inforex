<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_to_deletion extends CPageCorpus {
	
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_DELETE_DOCUMENTS;
    }

	function execute(){
		global $db;
        $records = ReportUserSelection::selectCheckedDocs($this->getCorpusId(), $this->getUserId());
        foreach($records as $record){
            $documentId = $record['id'];
            $db->update(DB_TABLE_REPORTS, array("deleted"=>1), array("id"=>$documentId));
        }

	}
	
}