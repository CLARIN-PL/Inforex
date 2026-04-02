<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveAnnotation_sets extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;

		$sql = "SELECT ase.annotation_set_id AS id, " .
			" ase.name AS name, " .
			" asco.corpus_id AS cid, " .
			" COALESCE(rac.count_ann, 0) AS count_ann " .
			"FROM annotation_sets ase " .
			"LEFT JOIN annotation_sets_corpora asco " .
				"ON ase.annotation_set_id = asco.annotation_set_id " .
				"AND asco.corpus_id = ? " .
			"LEFT JOIN (" .
				" SELECT ra.`group` AS annotation_set_id, COUNT(*) AS count_ann " .
				" FROM reports_annotations ra " .
				" JOIN reports r ON r.id = ra.report_id " .
			" WHERE r.corpora = ? " .
				" GROUP BY ra.`group`" .
			") rac ON rac.annotation_set_id = ase.annotation_set_id " .
			"ORDER BY ase.annotation_set_id";

		$this->page->set('annotationsList', $db->fetch_rows($sql, array($corpus['id'], $corpus['id'])));
	}
}
?>
