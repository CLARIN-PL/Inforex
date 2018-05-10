<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_relations extends CPage{

    function __construct(){
        parent::__construct("Relations between annotations", "Display a list of relations between annotations");
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }
	
	function execute(){
		global $corpus;

		$this->includeJs("js/c_autoresize.js");

		$mode = $_COOKIE['relation_mode'];
		if($mode == null){
		    $mode = 'final';
        }

        //Get a list of users with at least one relation.
        $users_with_relations = DbCorpusRelation::getUsersWithRelation($corpus['id']);
		$selected_user = $_COOKIE['relation_user'];

		// Parametry stronicowania - liczba relacji na stronę
		$relations_limit = 40;
		
		$relation_types = DbCorpusRelation::getRelationsData($corpus['id'], null, $mode, $selected_user);
		
		$i=0;
		foreach($relation_types as $rel_t){
			$relation_types[$i++]['types'] = DbCorpusRelation::getRelationsListData($corpus['id'], $rel_t['relation_id'], false, true);
		}
		$relation_list = DbCorpusRelation::getRelationList($corpus['id'],$relation_types[0]['types'][0]['relation_type'],$relation_types[0]['relation_id'],$relations_limit, false, false, true);


		// Obliczenie ilosci podstron
		$relations_pages = array();
		$from = 0;
		$to = $relation_types[0]['types'][0]['relation_count'];
		for($i=$from; $i <= $to; $i += $relations_limit){
			if($i + $relations_limit < $to){
				$relations_pages[] = array('from'=>$i, 'to'=>$i + $relations_limit);
			}
			else{
				$relations_pages[] = array('from'=>$i, 'to'=>$to);
			}			 
		}

        $this->set('corpus_id',$corpus['id']);
		$this->set('relation_set_id',$relation_types[0]['relation_id']);
		$this->set('relations_limit',$relations_limit);
		$this->set('relations_type',$relation_types);
		$this->set('relations_list',$relation_list);
		$this->set('relations_pages', $relations_pages);
		$this->set('users', $users_with_relations);
		$this->set('selected_user', $selected_user);
		$this->set('mode', $mode);
	}	
 } 
?>
