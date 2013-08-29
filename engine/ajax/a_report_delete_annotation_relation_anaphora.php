<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_annotation_relation_anaphora extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$relation_id = intval($_POST['relation_id']);
		$source_id = intval($_POST['source_id']);
		$target_id = intval($_POST['target_id']);
		
		
		$sql = "DELETE FROM relations WHERE id=?";
		db_execute($sql,array($relation_id));
		
		$sql = "SELECT id " .
				"FROM reports_annotations " .
				"WHERE (id=? " .
				"OR id=?) " .
				"AND type='anafora_wyznacznik'";
		$results = db_fetch_rows($sql, array($source_id, $target_id));
		$deleteId = array();
		
		$debug = "0 ";
		foreach ($results as $result){
			$sql = "SELECT * " .
					"FROM relations " .
					"WHERE source_id=? " .
					"OR target_id=? " .
					"LIMIT 1";
			$isRelation = db_fetch_one($sql, array($result['id'],$result['id']));
			if (!$isRelation){
				$debug .= "1 ";
				$sql = "DELETE FROM reports_annotations_optimized WHERE id=?";
				db_execute($sql, array($result['id']));
				$deleteId[]=$result['id'];
			}
		}
		
		return array("deletedId"=>$deleteId);
	}
	
}
?>
