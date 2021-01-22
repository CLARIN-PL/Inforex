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
		$sql = "SELECT t.id, t.name, t.cid, t.count_ann FROM (" .
			" SELECT ase.annotation_set_id AS id, ase.name as name,asco.corpus_id AS cid, count(ra.id) as count_ann" .
			" FROM annotation_sets ase" .
			" LEFT JOIN annotation_sets_corpora asco ON (ase.annotation_set_id = asco.annotation_set_id)" .
			" LEFT JOIN reports_annotations ra ON (ase.annotation_set_id = ra.group AND ra.report_id IN (SELECT id FROM reports r WHERE r.corpora = ?))" .
			" WHERE  asco.corpus_id = ?" .
			" GROUP BY ase.annotation_set_id" .
			" UNION ALL" .
			" SELECT ase.annotation_set_id as id, ase.name as name, null as cid, 0 as count_ann" .
			" FROM annotation_sets ase" .
			" LEFT JOIN annotation_sets_corpora asco ON ase.annotation_set_id = asco.annotation_set_id" .
			" WHERE ase.annotation_set_id IS NOT NULL" .
			" GROUP BY ase.name) t" .
			" GROUP BY t.name ORDER BY t.id";
		$this->page->set('annotationsList', $db->fetch_rows($sql, array($corpus['id'], $corpus['id'])));
	}
}
?>
