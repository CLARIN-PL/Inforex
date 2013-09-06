<?php

class DbReportAnnotationLemma{
	
	static function saveAnnotationLemma($id, $lemma){
		global $db;
		$sql = "INSERT INTO reports_annotations_lemma (report_annotation_id,lemma) ".
				"VALUES (?,?) ON DUPLICATE KEY UPDATE lemma=?;";
		
		$db->execute($sql, array($id,$lemma,$lemma));
	}
	
	static function deleteAnnotationLemma($id){
		global $db;
		$sql = "DELETE FROM reports_annotations_lemma WHERE report_annotation_id=?;";
		$db->execute($sql,array($id));
	}
	
}