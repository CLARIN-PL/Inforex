<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_report_flags extends CPageCorpus {
	
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

	function execute(){
		global $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$report_id = intval($_POST['report_id'] ?? 0);
		$cflag_id = intval($_POST['cflag_id'] ?? 0);
        $flag_id = intval($_POST['flag_id'] ?? 0);
        $user_id = intval($user['user_id']);
        $subcorpus_id = intval($_POST['subcorpus_id'] ?? -1);

        /*
         * Masowa zmiana statusu flagi i subkorpusu.
         */
        if(isset($_POST['multiple']) === true){

            $corpus_id = ($_POST['corpus_id']);
            $records = ReportUserSelection::selectCheckedDocs($corpus_id, $user_id);

            foreach($records as $record){
                $document = $record['id'];

                // Empty selects become 0; PHP 8 considers 0 != "" true.
                if($flag_id > 0 && $cflag_id > 0) {
                    DbReportFlag::changeFlagStatus($cflag_id, $flag_id, $document, $user_id);
                }

                if($subcorpus_id > 0){
                    DbReport::changeReportsSubcorpus($subcorpus_id, $document);
                }
            }

        }
        //Zmiana jednej flagi
        else {
            if ($cflag_id <= 0 || $report_id <= 0){
                throw new InvalidArgumentException("Brak identyfikatora flagi lub dokumentu");
            }
            if ($flag_id > 0){
                DbReportFlag::changeFlagStatus($cflag_id, $flag_id, $report_id, $user_id);
            }
            else {
                    DbReportFlag::deleteReportFlag($cflag_id, $report_id);
            }
        }
		return;
	}
	
}
