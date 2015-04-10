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
		$annotation_stage = strval($_GET['annotation_stage']);
		$annotation_orth = strval($_GET['annotation_orth']);
		$annotation_lemma = strval($_GET['annotation_lemma']);
		$annotation_type_id = intval($_GET['annotation_type_id']);
		
		$sql = "SELECT an.stage, COUNT(*) AS count" .
				" FROM reports_annotations_optimized an ".
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE r.corpora = ?" .
				" GROUP BY an.stage";
		$annotation_stages = $db->fetch_rows($sql, array($corpus_id));
		// Set default annotation stage if only one is present
		if ( $annotation_stage=="" ){
			foreach ( $annotation_stages as $stage ){
				if ( $stage['stage'] == 'final'){
					$annotation_stage = 'final';
				}
			}
			if ( $annotation_stage == "" && count($annotation_stages) > 0 ){
				$annotation_stage = $annotation_stages[0]['stage'];
			}
		}

		$sql = "SELECT t.annotation_type_id, t.name, count(*) AS count, s.description, s.annotation_set_id" .
				" FROM annotation_types t" .
				" JOIN reports_annotations_optimized an ON (an.type_id=t.annotation_type_id)" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" JOIN annotation_sets s ON (t.group_id = s.annotation_set_id)" .
				" WHERE r.corpora = ? AND an.stage = ?" .
				" GROUP BY an.type_id ".
				" ORDER BY s.description, t.name ";
		$params = array($corpus_id, $annotation_stage);
		$annotation_types = $db->fetch_rows($sql, $params);
		
		$sql = "SELECT an.text, an.type_id AS annotation_type_id, COUNT(*) AS count" .
				" FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE an.type_id = ? AND r.corpora = ? AND an.stage = ?" .
				" GROUP BY an.text" .
				" ORDER BY count DESC, an.text";
		$annotation_orths = $db->fetch_rows($sql, array($annotation_type_id, $corpus_id, $annotation_stage)); 

		$sql = "SELECT l.lemma AS text, an.type_id AS annotation_type_id, COUNT(*) AS count" .
				" FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" JOIN reports_annotations_lemma l ON (an.id = l.report_annotation_id)" .
				" WHERE an.type_id = ? AND r.corpora = ? AND an.stage = ?" .
				" GROUP BY l.lemma" .
				" ORDER BY count DESC, l.lemma";
		$annotation_lemmas = $db->fetch_rows($sql, array($annotation_type_id, $corpus_id, $annotation_stage)); 
				
		$this->set("annotation_stages", $annotation_stages);
		$this->set("annotation_stage", $annotation_stage);
		$this->set("annotation_types", $annotation_types);
		$this->set("annotation_type_id", $annotation_type_id);
		$this->set("annotation_orths", $annotation_orths);
		$this->set("annotation_orth", $annotation_orth);		
		$this->set("annotation_lemmas", $annotation_lemmas);
		$this->set("annotation_lemma", $annotation_lemma);
	}
		
}


?>
