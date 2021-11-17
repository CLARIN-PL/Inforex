<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_agreement_morphology extends CPageCorpus {
	
    public function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_AGREEMENT_MORPHOLOGY;
        $this->includeJs("libs/datatables/datatables-fixed-columns/dataTables.fixedColumns.min.js");
        $this->includeCss("libs/datatables/datatables-fixed-columns/fixedColumns.dataTables.min.css");
    }

    function execute(){
		global $db, $user, $corpus;

		/* Variable declaration */
		$corpus_id = $corpus['id'];
        $usersMorphoDisambSet = array();

        $subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
        $subcorpus_ids = $_GET['subcorpus_ids'];


        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();
        $corpus_flag_id = $_GET['corpus_flag_id'];
        $flag_id = intval($_GET['flag_id']);


        $flag = array();

        if ( $corpus_flag_id !== "Select flag"){
            $flag = array($corpus_flag_id => array($flag_id));
        }

        /*
         * getting selected annotators
         */
        $annotator_a_id = strval($_GET['annotator_a_id']);
        $annotator_b_id = strval($_GET['annotator_b_id']);

		$bothAnnotatorsSet = $annotator_a_id != '' and $annotator_b_id != '';

        /*
	     * setting up and getting comparison modes
	     */
        $comparision_mode = strval($_GET['comparision_mode']);
        $comparision_modes = array();
        $comparision_modes["base_ctag"] = "bases and ctags";
        $comparision_modes["base"] = "bases";


        /*
         * setting selected reports
         */
		if(isset($_GET['subcorpus_ids'])){
			$selectedSubcorp = DbCorpus::getSubcorporaByIds($_GET['subcorpus_ids']);
			// get reports for selected corpora only
			$selectedSubcorpIds = array_map(function($it){return intval($it['subcorpus_id']);}, $selectedSubcorp);

			$reports = DbReport::getReports(null, $selectedSubcorpIds, null, $flag, array("id", "title","corpora", "author", "subcorpus_id"));
		} else{
			// getting all reports for corpus
			// check if annotators are set before
            $selectedSubcorp = $subcorpora;
		  	$reports = DbReport::getReports($corpus_id, null,null,$flag,array("id, title,corpora, author, subcorpus_id"));
		}

		$reports_ids = array_map(function($it){return intval($it['id']);}, $reports);
        $annotators = MorphoUtil::getPossibleAnnotatorsQuick( $reports_ids);

        // clearing reports if annotators are not set
        if(!$bothAnnotatorsSet){
        	$reports = array();
        	$reports_ids = array();
        } else{
            $this->setupReportsPSA($reports_ids, $reports, $annotator_a_id, $annotator_b_id, $comparision_mode);

            $reportsLen = DbReport::getTokenCountForCorpusReports($corpus_id);
            $reports = mergeArraysOnKeys($reports, $reportsLen, 'id', 'report_id');
		}


		$this->set('selectedSubcorp', $selectedSubcorp);
		$this->set('reports', $reports);

		$flag_id = intval($_GET['flag_id']);
		$flag = array();

		if ( !is_array($subcorpus_ids) ){
			$subcorpus_ids = array();
		}
		
		if ( !isset($comparision_modes[$comparision_mode]) ){
			$comparision_mode = "borders";
		}

		$this->set("annotators", $annotators);
		$this->set("annotator_a_id", $annotator_a_id);
		$this->set("annotator_b_id", $annotator_b_id);
		$this->set("comparision_mode", $comparision_mode);
		$this->set("comparision_modes", $comparision_modes);
		$this->set("subcorpora", $subcorpora);
		$this->set("subcorpus_ids", $subcorpus_ids);
		$this->set("corpus_flags", $corpus_flags);
		$this->set("flags", $flags);
		$this->set("corpus_flag_id", $corpus_flag_id);
		$this->set("flag_id", $flag_id);
	}

	private function setupReportsPSA($reports_ids, &$reports, $annotator_a_id, $annotator_b_id, $comparison_mode){
		$stats = null;
		if(is_numeric($annotator_a_id) && is_numeric($annotator_b_id)) { // check if final is not selected
			$stats = DbTokensTagsOptimized::getPSAForReportAndUser($reports_ids, $annotator_a_id, $annotator_b_id, $comparison_mode);
		} else if ($annotator_a_id === 'final' || $annotator_b_id === 'final') {
			$stats = DbTokensTagsOptimized::getPSAForReportAndUserWithFinal($reports_ids, $annotator_a_id, $annotator_b_id, $comparison_mode);
		}


		$global_stats = array(
			'both' => 0,
			'only_a' => 0,
			'only_b' => 0
		);

		foreach($reports as $key => $report){
            $reports[$key] ['usersCnt'] = array(
                'both' => 0,
                'only_a' => 0,
                'only_b' => 0
            );

            $reports[$key] ['psa'] = 0;

            if($stats[$report['id']] !== null){
                $reports[$key] ['usersCnt'] = array_replace($reports[$key] ['usersCnt'], $stats[$report['id']]);
                $reports[$key] ['psa'] = psa(
                	$reports[$key] ['usersCnt']['both'],
					$reports[$key] ['usersCnt']['only_a'],
					$reports[$key] ['usersCnt']['only_b']);
                $global_stats['both'] += $reports[$key] ['usersCnt']['both'];
                $global_stats['only_a'] += $reports[$key] ['usersCnt']['only_a'];
                $global_stats['only_b'] += $reports[$key] ['usersCnt']['only_b'];
			}
		}

		$global_stats['psc'] = psa(
			$global_stats['both'],
			$global_stats['only_a'],
			$global_stats['only_b']
		);
		$this->set('globalPSC', $global_stats['psc']);
	}
}

function psa($both, $only1, $only2){
	if ( (2*$both + $only1 + $only2) == 0 ){
		return 0;
	}
	else{
		return $both*200.0/(2.0*$both+$only1+$only2);
	}
}

function array_find($heystack, $f) {
    foreach ($heystack as $key=>$item) {
        if (call_user_func($f, $key) === true) {
            return $key;
		}
    }
    return null;
}

function mergeArraysOnKeys($arr1, $arr2, $key1, $key2){
	$result = array();

	foreach($arr1 as $arr1_key => $it1){
		foreach($arr2 as $arr2_key => $it2){
			if($it1[$key1] == $it2[$key2]){
				$result[] = array_merge($it1, $it2);
			}
		}
	}
	return $result;
}
?>
