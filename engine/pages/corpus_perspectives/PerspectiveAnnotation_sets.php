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
		$sql = "SELECT ase.annotation_set_id AS id, ase.description, asco.corpus_id AS cid, count(ra.id) as count_ann" .
				" FROM annotation_sets ase" .
				" LEFT JOIN annotation_sets_corpora asco ON" .
					" (ase.annotation_set_id = asco.annotation_set_id AND asco.corpus_id = ?)" .
				" LEFT JOIN annotation_types at ON" .
					" (ase.annotation_set_id = at.group_id)" .
				" LEFT JOIN reports_annotations ra ON" .
					" (at.name = ra.type AND ra.report_id IN " .
						" (SELECT id FROM reports r WHERE r.corpora = ?))" .
				" GROUP BY ase.annotation_set_id";		
		$this->page->set('annotationsList', $db->fetch_rows($sql, array($corpus['id'], $corpus['id'])));
	}
}
?>
