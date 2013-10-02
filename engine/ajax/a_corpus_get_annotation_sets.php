<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_get_annotation_sets extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('corpus_owner'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}
		$corpusId = $_POST['corpus_id'];
		
		$sql = "SELECT annotation_sets.annotation_set_id AS id, " .
				"annotation_sets.description, " .
				"annotation_sets_corpora.corpus_id AS cid, " .
				"count(reports_annotations.id) as count_ann " .
				"FROM annotation_sets " .
				"LEFT JOIN annotation_sets_corpora " .
					"ON annotation_sets.annotation_set_id=annotation_sets_corpora.annotation_set_id " .
					"AND annotation_sets_corpora.corpus_id=$corpusId " .
				"LEFT JOIN annotation_types " .
					"ON annotation_sets.annotation_set_id=annotation_types.group_id " .
				"LEFT JOIN reports_annotations " .
					"ON annotation_types.name=reports_annotations.type " .
					"AND reports_annotations.report_id IN " .
						"(SELECT id " .
						"FROM reports " .
						"WHERE corpora=$corpusId) " .
				"GROUP BY annotation_sets.annotation_set_id";		
		$result = db_fetch_rows($sql);
		return $result;
	}
	
}
?>
