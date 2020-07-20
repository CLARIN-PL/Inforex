<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_annotation_relation extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
    }
		
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}

		$relation_id = intval($_POST['relation_id']);
		
		$sql = "DELETE FROM relations " .
				"WHERE id={$relation_id}";
		$this->getDb()->execute($sql);
		return;
	}
	
}
