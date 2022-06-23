<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Page_corpus_annotation_statistics extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }

	function manageFilters(){
	    $filters = $_GET;

        if(isset($filters['status'])){
            $_SESSION['annmap']['status'] = $filters['status'];
        }

		if (isset($filters['stage'])) {
			$_SESSION['annmap']['annotation']['stage'] = $filters['stage'];
		}

		if (isset($filters['user_id'])) {
			$_SESSION['annmap']['annotation']['user'] = $filters['user_id'];
		}

        if(isset($filters['flag'])){
            $_SESSION['annmap']['flags']['flag'] = $filters['flag'];
            $_SESSION['annmap']['flags']['flag_status'] = $filters['flag_status'];
        }

        if(isset($filters['use_url'])){
			foreach($filters as $filter=>$value){
                if(preg_match_all('/(metadata_)(.)/', $filter, $matches)){
                	$metadata_index = $matches[2][0];
                	$metadata_field = $filters[$filter];
                	$metadata_value = $filters["value_".$metadata_index];

                    $_SESSION['annmap']['metadata'][$metadata_field] = $metadata_value;
				}
			}
        } else{
            if(isset($filters['metadata'])){
                $_SESSION['annmap']['metadata'][$filters['metadata']] = $filters['value'];
            }
		}
    }
	
	function execute(){		
		global $corpus, $db;
		//unset($_SESSION['annmap']);

		$this->manageFilters();
		
		$corpus_id = $corpus['id'];
		$status = intval($_GET['status']);
		$flag = $_GET['flag'];
		$flag_status = $_GET['flag_status'];
		$set_filters = array();
        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();
        $features = DbCorpus::getCorpusExtColumnsWithMetadataFilters($corpus['ext']);
        $corpus_users =  DbCorporaUsers::getCorpusUsers($corpus_id);

		$ext_where = null;
		if ( count($set_filters) ){
			foreach ($set_filters as $k=>$v)
				$ext_where .= " AND re.$k='$v'";
		}

		$session_flag = $_SESSION['annmap']['flags']['flag'];
		$session_flag_status = $_SESSION['annmap']['flags']['flag_status'];
		$session_stage = $_SESSION['annmap']['annotation']['stage'];
		$session_user = $_SESSION['annmap']['annotation']['user'];

		if($session_flag != null && $session_flag_status != null && $session_stage != null
			&& $session_flag != "-" && $session_flag_status != "-" || $session_stage != "-" || $session_user != "-"){
            $this->set("flag_set", true);
        }
		
		$annmap = DbAnnotation::getAnnotationSetsWithCount($corpus_id, $_SESSION['annmap']);
		$statuses = DbStatus::getAll();
		
		/* Fill template */		
		$this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));													
		$this->set("sets", $annmap);
		$this->set("flags", $flags);
		$this->set("selected_flag", $flag);
		$this->set("flag_status", $flag_status);
		$this->set("corpus_flags", $corpus_flags);
		$this->set("corpus_users", $corpus_users);
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
		$this->set("status", $status);
		$this->set("statuses", $statuses);
		$this->set("features", $features);
		$this->set("selected_filters", $_SESSION['annmap']);
	}
}


?>
