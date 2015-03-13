<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_annotation_browser extends CPage{
	
	var $isSecure = false;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_READ) || $corpus['public'];
	}
		
	function execute(){
		global $db, $user, $corpus;
		
		$corpus_id = $corpus['id'];
		$annotation_type_id = intval($_GET['annotation_type_id']);

		$sql = "SELECT t.annotation_type_id, t.name, count(*) AS count" .
				" FROM annotation_types t" .
				" JOIN reports_annotations_optimized an ON (an.type_id=t.annotation_type_id)" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE r.corpora = ?" .
				" GROUP BY an.type_id ".
				" ORDER BY t.name ";
		$annotation_types = $db->fetch_rows($sql, array($corpus_id));
		
		$this->set("annotation_types", $annotation_types);
		$this->set("annotation_type_id", $annotation_type_id);
		
	}
}


?>
