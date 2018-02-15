<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_morpho_agreement_check extends CPage{
	
	var $isSecure = true;

    function checkPermission(){
        return hasCorpusRole('agreement_morpho');
    }
		
	function execute(){
		$this->includeJs("js/c_autoresize.js");
		$this->includeJs("js/page_morpho_agreement_check.js");
		$this->includeJs("libs/datatables/datatables-fixed-columns/dataTables.fixedColumns.min.js");
		$this->includeCss("libs/datatables/datatables-fixed-columns/fixedColumns.dataTables.min.css");
		$this->includeCss("css/page_morpho_agreement_check.css");

		global $db, $user, $corpus;

		/* Variable declaration */
		$corpus_id = $corpus['id'];
        $usersMorphoDisambSet = array();

        $subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
        $subcorpus_ids = $_GET['subcorpus_ids'];


        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();
        $corpus_flag_id = intval($_GET['corpus_flag_id']);

        /*
         * getting selected annotators
         */
        $annotator_a_id = strval($_GET['annotator_a_id']);
        $annotator_b_id = strval($_GET['annotator_b_id']);
		$bothAnnotatorsSet = $annotator_a_id != null and $annotator_b_id != null;

		ChromePhp::log($bothAnnotatorsSet);

        /*
         * setting selected reports
         */
		if(isset($_GET['subcorpus_ids'])){
			$selectedSubcorp = DbCorpus::getSubcorporaByIds($_GET['subcorpus_ids']);
			// get reports for selected corpora only
			$selectedSubcorpIds = array_map(function($it){return intval($it['subcorpus_id']);}, $selectedSubcorp);
			$reports = DbReport::getReports(null, $selectedSubcorpIds, null, null, array("id", "title","corpora", "author", "subcorpus_id"));
		} else{
			// getting all reports for corpus
			// check if annotators are set before
            $selectedSubcorp = $subcorpora;
		  	$reports = DbReport::getReportsByCorpusId($corpus_id, "id, title,corpora, author, subcorpus_id");
		}

		$reports_ids = array_map(function($it){return intval($it['id']);}, $reports);
        $annotators = MorphoUtil::getPossibleAnnotatorsQuick( $reports_ids);

        // clearing reports if annotators are not set
        if(!$bothAnnotatorsSet){
        	$reports = array();
        	$reports_ids = array();
        } else{
			$usersMorphoDisambSet = DbTokensTagsOptimized::getUsersOwnDecisionsByReports($reports_ids, $annotator_a_id, $annotator_b_id);
		}


		foreach($reports as $key => $field){
        	$stats = $this->getTokensCntAndUserDecisions($field, $usersMorphoDisambSet[$reports[$key]['id']]);
            $reports[$key]['total_tokens'] =  $stats['total_tokens'];
            $reports[$key]['divergent'] =  $stats['divergent'];
            $reports[$key]['PSA'] =  $stats['PSA'];
		}

		$this->set('selectedSubcorp', $selectedSubcorp);
		$this->set('reports', $reports);
		$this->set('usersMorphoDisambSet', $usersMorphoDisambSet);

		$annotation_set_a = array();
		$annotation_set_b = array();

		$agreement = array();
		$pcs = array();
		$comparision_mode = strval($_GET['comparision_mode']);
		$comparision_modes = array();
        $comparision_modes["base_ctag"] = "bases and ctags";
		$comparision_modes["base"] = "bases";

		$flag_id = intval($_GET['flag_id']);
		$flag = array();

//		$this->setup_annotation_type_tree($corpus_id);

//        $annotation_types = CookieManager::getAnnotationTypeTreeAnnotationTypes($corpus_id);

		if ( !is_array($subcorpus_ids) ){
			$subcorpus_ids = array();
		}
		
		if ( $corpus_flag_id !== 0 && $flag_id !== 0 ){
			$flag = array($corpus_flag_id => $flag_id);
		}
		
		if ( !isset($comparision_modes[$comparision_mode]) ){
			$comparision_mode = "borders";
		}

//		$annotators = DbAnnotation::getUserAnnotationCount($corpus_id, $subcorpus_ids, null, null, $annotation_types, $flag, "agreement");
//		$annotators = MorphoUtil::getPossibleAnnotators(null, $reports_ids);
//		ChromePhp::log($annotators);

		// TODO: do ujednolicenia z setupUserSelectionAB

//		$annotation_set_final_count = DbAnnotation::getAnnotationCount(null, $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "final");
//		$annotation_set_final_doc_count = DbAnnotation::getAnnotationDocCount(null, $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "final");
//
//		if ( $annotator_a_id == "final" ){
//			$annotation_set_a = DbAnnotation::getUserAnnotations(null, $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "final");
//		}
//		else if ( intval($annotator_a_id) > 0 ) {
//			$annotation_set_a = DbAnnotation::getUserAnnotations(intval($annotator_a_id), $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "agreement");
////			$userMorphoDisambSet = DbTokensTagsOptimized::getUsersOwnDecisions();
//		}
//
//		if ( $annotator_b_id == "final" ){
//			$annotation_set_b = DbAnnotation::getUserAnnotations(null, $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "final");
//		}
//		else if ( intval($annotator_b_id) > 0 ) {
//			$annotation_set_b = DbAnnotation::getUserAnnotations($annotator_b_id, $corpus_id, $subcorpus_ids, null, $annotation_types, $flag, "agreement");
//		}
//
//		ChromePhp::log($annotation_set_b);
		
//		if ( $annotator_a_id && $annotator_b_id ){
//			$annotation_types = array();
//			foreach ($annotation_set_a as $an){
//				$annotation_types[$an["annotation_name"]] = 1;
//			}
//
//			foreach ($annotation_set_b as $an){
//				$annotation_types[$an["annotation_name"]] = 1;
//			}
//
//			foreach ( array_keys($annotation_types) as $annotation_name ){
//				$agreement = compare($annotation_set_a, $annotation_set_b, "key_generator_${comparision_mode}", $annotation_name);
//				$pcs_value = pcs(count($agreement['a_and_b']), count($agreement['only_a']), count($agreement['only_b']));
//				$pcs[$annotation_name] = array("only_a"=>count($agreement['only_a']), "only_b"=>count($agreement['only_b']), "a_and_b"=>count($agreement['a_and_b']), "pcs"=>$pcs_value);
//			}
//
//			$agreement = compare($annotation_set_a, $annotation_set_b, "key_generator_${comparision_mode}");
//			ksort($agreement['annotations']);
//			$pcs_value = pcs(count($agreement['a_and_b']), count($agreement['only_a']), count($agreement['only_b']));
//			$pcs["all"] = array("only_a"=>count($agreement['only_a']), "only_b"=>count($agreement['only_b']), "a_and_b"=>count($agreement['a_and_b']), "pcs"=>$pcs_value);
//		}

//		ChromePhp::log($pcs);
//		ChromePhp::log($agreement);
//		ChromePhp::log($annotation_sets);

		/* Assign variables to the template */
//		$this->set("annotation_sets", $annotation_sets);
		$this->set("annotation_set_final_count", intval($annotation_set_final_count));
		$this->set("annotation_set_final_doc_count", intval($annotation_set_final_doc_count));
//		$this->set("annotation_set_id", $annotation_set_id);
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

	/**
	 * Ustaw strukturę dostępnych typów anotacji.
	 * @param unknown $corpus_id
	 */
	private function setup_annotation_type_tree($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);
//		ChromePhp::log($annotations);
		$this->set('annotation_types',$annotations);
	}

	// todo!
	private function getTokensCntAndUserDecisions($report, $usersMorphoDisamb){
		$ret_dict = array();
        $ret_dict['total_tokens'] = 123;

        if($usersMorphoDisamb == null){
            $ret_dict['divergent'] = 'n/a';
            $ret_dict['PSA'] = 'n/a';
		}
		else {
            $ret_dict['divergent'] = 12123;
            $ret_dict['PSA'] = 456;
		}
		return $ret_dict;
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
 * @param string $annotation_name_filter Jeżeli ustawiony, to filtruje po nazwach anotacji.
 * @return unknown[]|number[]|string[]
 */
function compare($ans1, $ans2, $key_generator, $annotation_name_filter=null){
	$annotations = array();
	//$annotations_border = array();
	$copy_ans1 = array();
	//$copy_ans1_border = array();
	$copy_ans2 = array();
	//$copy_ans2_border = array();
	
	foreach ($ans1 as $as){
		if ( $annotation_name_filter != null && $as['annotation_name'] != $annotation_name_filter ){
			continue;
		}
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
		if ( $annotation_name_filter != null && $as['annotation_name'] != $annotation_name_filter ){
			continue;
		}
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

function key_generator_base($row){
    return implode(array($row['report_id'], sprintf("%08d", $row['from']), sprintf("%08d", $row['to'])), "_");
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
	if ( (2*$both + $only1 + $only2) == 0 ){
		return 0;
	}
	else{
		return $both*200.0/(2.0*$both+$only1+$only2);
	}
}

?>
