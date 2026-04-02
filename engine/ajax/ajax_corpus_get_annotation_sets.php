<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_get_annotation_sets extends CPageCorpus {
	
	function execute(){
		global $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}
		$corpusId = $_POST['corpus_id'];
		
		$sql = "SELECT annotation_sets.annotation_set_id AS id, " .
            	"annotation_sets.name, " .
				"annotation_sets.description, " .
				"annotation_sets_corpora.corpus_id AS cid, " .
				"COALESCE(rac.count_ann, 0) AS count_ann " .
				"FROM annotation_sets " .
				"LEFT JOIN annotation_sets_corpora " .
					"ON annotation_sets.annotation_set_id = annotation_sets_corpora.annotation_set_id " .
					"AND annotation_sets_corpora.corpus_id = ? " .
				"LEFT JOIN (" .
					" SELECT ra.`group` AS annotation_set_id, COUNT(*) AS count_ann " .
					" FROM reports_annotations ra " .
					" JOIN reports r ON r.id = ra.report_id " .
					" WHERE r.corpora = ? " .
					" GROUP BY ra.`group`" .
				") rac ON rac.annotation_set_id = annotation_sets.annotation_set_id " .
				"ORDER BY annotation_sets.annotation_set_id";
		$result = $this->getDb()->fetch_rows($sql, array($corpusId, $corpusId));
		return $result;
	}
	
}
