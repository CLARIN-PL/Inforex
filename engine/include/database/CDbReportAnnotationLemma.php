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
	
	static function getLemmasByReportsIds($reports_ids){
		global $db;
		$sql = "SELECT * FROM reports_annotations_lemma ral ".
				"JOIN reports_annotations rao ON(ral.report_annotation_id = rao.id) ".
				"WHERE rao.report_id IN(".implode(",",$reports_ids).");";

		$lemmas = $db->fetch_rows($sql);
		$lemmasByReports = array();
		foreach($lemmas as $lemma){
			$report_id = $lemma['report_id'];
			if(!array_key_exists($report_id, $lemmasByReports)){
				$lemmasByReports[$report_id] = array();
			}
			$lemmasByReports[$report_id][] = $lemma;
		}
		
		return $lemmasByReports;
	}
	
	static function deleteAnnotationLemmaByAnnotationRegex($report_id, $regex){
		global $db;
		$sql = "DELETE ral.* FROM reports_annotations_lemma ral 
				LEFT JOIN reports_annotations_optimized rao ON(ral.report_annotation_id = rao.id)
				LEFT JOIN annotation_types at ON(at.annotation_type_id = rao.type_id) 
				WHERE at.name REGEXP ? AND report_id = ?";
		$db->execute($sql, array($regex, $report_id));
	}

}