<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annmap_get_report_links extends CPage {
	var $isSecure = false;
	function execute(){
		global $db;
		$corpusId = intval($_POST['id']);
		$annotationType = $_POST['type'];
		$annotationText = $_POST['text']; 
		$sql = "SELECT DISTINCT r.id, r.title" .
				" FROM reports_annotations ra" .
				" JOIN reports r ON ra.report_id=r.id" .
				" WHERE r.corpora=? AND ra.type=? AND ra.text=?" .
				" ORDER BY r.title, r.id";
		$result = $db->fetch_rows($sql, array($corpusId, $annotationType, $annotationText));
		return $result;
	}
	
}
?>
