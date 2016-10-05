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
		$comparision_mode = strval($_GET['comparision_mode']);
		$comparision_modes = array();
		$comparision_modes["borders"] = "borders";
		$comparision_modes["categories"] = "borders and categories";
		$comparision_modes["borders_lemmas"] = "borders and lemmas";
		$comparision_modes["lemmas"] = "borders, categories and lemmas";
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
		$subcorpus_ids = $_GET['subcorpus_ids'];
		$corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
		$flags = DbCorporaFlag::getFlags();
		$corpus_flag_id = intval($_GET['corpus_flag_id']);
		$flag_id = intval($_GET['flag_id']);
		$flag = array();
		
		/* Setup variables */
		$annotation_sets = DbAnnotationSet::getAnnotationSetsAssignedToCorpus($corpus_id);
		
		if ( !is_array($subcorpus_ids) ){
			$subcorpus_ids = array();
		}
		
		if ( $corpus_flag_id != 0 && $flag_id != 0 ){
			$flag = array($corpus_flag_id => $flag_id);
		}
		
		if ( !isset($comparision_modes[$comparision_mode]) ){
			$comparision_mode = "borders";
		}
		
		if ( $annotation_set_id > 0 ){
			echo "x";
			$annotators = DbAnnotation::getUserAnnotationCount($corpus_id, $subcorpus_ids, $annotation_set_id, $flag, "agreement");
		}
		
		$annotator_a_id = intval($_GET['annotator_a_id']);
		$annotator_b_id = intval($_GET['annotator_b_id']);
		
		if ( $annotator_a_id ){
			echo "a";
			$annotation_set_a = DbAnnotation::getUserAnnotations($annotator_a_id, $corpus_id, $subcorpus_ids, $annotation_set_id, $flag, "agreement");
		}
		
		if ( $annotator_b_id ){
			$annotation_set_b = DbAnnotation::getUserAnnotations($annotator_b_id, $corpus_id, $subcorpus_ids, $annotation_set_id, $flag, "agreement");
		}

		if ( $annotator_a_id && $annotator_b_id ){
			$agreement = compare($annotation_set_a, $annotation_set_b, "key_generator_${comparision_mode}");
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
		$this->set("comparision_mode", $comparision_mode);
		$this->set("comparision_modes", $comparision_modes);
		$this->set("subcorpora", $subcorpora);
		$this->set("subcorpus_ids", $subcorpus_ids);
		$this->set("corpus_flags", $corpus_flags);
		$this->set("flags", $flags);
		$this->set("corpus_flag_id", $corpus_flag_id);
		$this->set("flag_id", $flag_id);
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
	//$annotations_border = array();
	$copy_ans1 = array();
	//$copy_ans1_border = array();
	$copy_ans2 = array();
	//$copy_ans2_border = array();
	
	foreach ($ans1 as $as){
		$key = $key_generator($as);
		//$key_border = key_generator_borders($as);
		if ( isset($ans1[$key]) ){
			echo "Warning: duplicated annotation in DB1 $key with $key_generator\n";
		}
		else{
			$copy_ans1[$key] = $as;
			//$copy_ans1_border[$key_border][] = $key;
			//$annotations_border[$key_border] = 1;
		}
		$annotations[$key] = $as;
	}

	foreach ($ans2 as $as){
		$key = $key_generator($as);
		//$key_border = key_generator_borders($as);
		if ( isset($ans2[$key]) ){
			echo "Warning: duplicated annotation in DB2 $key with $key_generator\n";
		}
		else{
			$copy_ans2[$key] = $as;
			//$copy_ans2_border[$key_border][] = $key;
			//$annotations_border[$key_border] = 1;
		}
		$annotations[$key] = $as;
	}
	
	$only1 = array_diff_key($copy_ans1, $copy_ans2);
	$only2 = array_diff_key($copy_ans2, $copy_ans1);
	$both = array_intersect_key($copy_ans1, $copy_ans2);

	return array("only_a"=>$only1, "only_b"=>$only2, "a_and_b"=>$both, "annotations"=>$annotations, "annotations_a"=>$copy_ans1, "annotations_b"=>$copy_ans2);
}

function key_generator_borders($row){
	return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to'])), "_");
}

function key_generator_borders_lemmas($row){
	return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to']), $row['lemma']), "_");
}

function key_generator_categories($row){
	return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to']), $row['type_id']), "_");
}

function key_generator_lemmas($row){
	return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to']), $row['type_id'], $row['lemma']), "_");
}

function pcs($both, $only1, $only2){
	return $both*200.0/(2.0*$both+$only1+$only2);
}

?>
