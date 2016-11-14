<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_annotation extends CPage {
	
	function checkPermission(){
		// TODO prawo edycji anotacji CORPUS_ROLE_ANNOTATE_AGREEMENT powinno dotyczyć wyłącznie anotacji o stage=agreement
		if ( hasRole(USER_ROLE_ADMIN) 
				|| isCorpusOwner() 
				|| ( (hasCorpusRole(CORPUS_ROLE_ANNOTATE) || hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT)) && hasCorpusRole(CORPUS_ROLE_DELETE_ANNOTATIONS) ) )
			return true;
		else
			return "Brak prawa do dodawania anotacji.";
	}
	
	function execute(){
		global $db;
		$annotation_id = intval($_POST['annotation_id']);
		
		$rels = $db->fetch_one("SELECT COUNT(*) FROM relations WHERE source_id = ? OR target_id = ?", array($annotation_id, $annotation_id));
		
		if ( $rels > 0 ){
			$json = array("error"=>"This annotation cannot be deleted because it is referenced by some relations. Please remove the relations first.");	
		}
		else{		
			$db->execute("DELETE FROM reports_annotations_optimized WHERE id=?", array($annotation_id));
			$json = array("success" => "ok");
		}		
		return $json;
	}
	
}
?>
