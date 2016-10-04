<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_agreement_check extends CPage{
	
	var $isSecure = true;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_AGREEMENT_CHECK);
	}
		
	function execute(){
		global $db, $user, $corpus;
		
		/* Variable declaration */
		$corpus_id = $corpus['id'];
		$annotation_set_id = intval($_GET['annotation_set_id']);
		$annotators = array();
		$annotation_set_a = array();
		$annotation_set_b = array();
		$agreement = array();
		$pcs = 0;
		
		/* Setup variables */
		$annotation_sets = DbAnnotationSet::getAnnotationSetsAssignedToCorpus($corpus_id);
		
		if ( $annotation_set_id > 0 ){
			$annotators = DbAnnotation::getUserAnnotationCount($corpus_id, $annotation_set_id, "agreement");
			//$annotators = DbAnnotation::getUserAnnotationCount();
		}
		
		$annotator_a_id = intval($_GET['annotator_a_id']);
		$annotator_b_id = intval($_GET['annotator_b_id']);
		
		if ( $annotator_a_id ){
			$annotation_set_a = DbAnnotation::getUserAnnotations($annotator_a_id, $corpus_id, $annotation_set_id, "agreement");
		}
		
		if ( $annotator_b_id ){
			$annotation_set_b = DbAnnotation::getUserAnnotations($annotator_b_id, $corpus_id, $annotation_set_id, "agreement");
		}

		if ( $annotator_a_id && $annotator_b_id ){
			$agreement = compare($annotation_set_a, $annotation_set_b, "row_key_full");
			ksort($agreement['annotations']);
			$pcs = pcs(count($agreement['a_and_b']), count($agreement['only_a']), count($agreement['only_b']));
		}
		
		/* Assign variables to the template */
		$this->set("annotation_sets", $annotation_sets);
		$this->set("annotation_set_id", $annotation_set_id);
		$this->set("annotators", $annotators);
		$this->set("annotator_a_id", $annotator_a_id);
		$this->set("annotator_b_id", $annotator_b_id);
		$this->set("agreement", $agreement);
		$this->set("pcs", $pcs);
	}
		
}

/** TODO do przeniesienia do osobnego pliku */

/**
 * 
 * @param unknown $name
 * @param unknown $ans1
 * @param unknown $ans2
 * @param unknown $key_generator
 * @param string $type
 * @return unknown[]|number[]|string[]
 */
function compare($ans1, $ans2, $key_generator){
	$annotations = array();
	$copy_ans1 = array();
	$copy_ans2 = array();
	foreach ($ans1 as $as){
		$key = $key_generator($as);
		if ( isset($ans1[$key]) ){
			echo "Warning: duplicated annotation in DB1 $key with $key_generator\n";
		}
		else{
			$copy_ans1[$key] = $as;
		}
		$annotations[$key] = $as;
	}

	foreach ($ans2 as $as){
		$key = $key_generator($as);
		if ( isset($ans2[$key]) ){
			echo "Warning: duplicated annotation in DB2 $key with $key_generator\n";
		}
		else{
			$copy_ans2[$key] = $as;
		}
		$annotations[$key] = $as;
	}
	$only1 = array_diff_key($copy_ans1, $copy_ans2);
	$only2 = array_diff_key($copy_ans2, $copy_ans1);
	$both = array_intersect_key($copy_ans1, $copy_ans2);

	return array("only_a"=>$only1, "only_b"=>$only2, "a_and_b"=>$both, "annotations"=>$annotations);
}

function row_key_full($row){
	return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to']), $row['type_id']), "_");
}

function pcs($both, $only1, $only2){
	return $both*200.0/(2.0*$both+$only1+$only2);
}

?>
