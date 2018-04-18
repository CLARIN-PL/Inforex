<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_set_annotation_sets_corpora extends CPageCorpus {
	
	function execute(){
		global $db;

		$annotationSetId = intval($_POST['annotation_set_id']);
		$corpusId = intval($_POST['corpus_id']);
		$operationType = $_POST['operation_type'];

		ob_start();
		switch ($operationType){
			case "add":
				$sql = "INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES (?, ?)";
				$params = array($annotationSetId, $corpusId);
                $db->execute($sql, $params);
                break;
			case "remove":
                $sql = "DELETE FROM annotation_sets_corpora WHERE annotation_set_id=? AND corpus_id=?";
                $params = array($annotationSetId, $corpusId);
                $db->execute($sql, $params);
                break;
			default:
		}
		$error_buffer_content = ob_get_contents();
		ob_clean();

		if(strlen($error_buffer_content)) {
            throw new Exception("Error: " . $error_buffer_content);
        } else {
            return;
        }
	}	
}
