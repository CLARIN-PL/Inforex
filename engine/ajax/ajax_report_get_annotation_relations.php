<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_annotation_relations extends CPageCorpus {


    function __construct(){
        // TODO prawo edycji anotacji CORPUS_ROLE_ANNOTATE_AGREEMENT powinno dotyczyć wyłącznie anotacji o stage=agreement
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }

	function execute(){
		global $user, $corpus;
		$corpusId = $corpus['id'];

        $annotation_id = intval($_POST['annotation_id']);
        $annotation_mode = $_POST['annotation_mode'];
        $relationSetIds = CookieManager::getRelationSets($corpusId);
        $rels_imploded = implode(",", $relationSetIds);

        //If there are no relation sets selected, return an empty array.
        if(empty($relationSetIds)){
            return array();
        } else{
            //Only find relations with appropriate stage.
            if($annotation_mode == 'relation_agreement' || $annotation_mode == 'agreement'){
                $annotation_mode = 'agreement';
                $where_sql = "AND user_id = " . $user['user_id'];
            }
            $sql =  "SELECT rr.id, rr.stage, relation_types.name, rr.target_id, rr.user_id, reports_annotations.text, reports_annotations.type " .
                "FROM ((SELECT * FROM relations WHERE (source_id={$annotation_id} AND stage = '{$annotation_mode}'  {$where_sql})) rr " .
                "JOIN relation_types  ON rr.relation_type_id=relation_types.id) " .
                "JOIN reports_annotations ON rr.target_id=reports_annotations.id
				 WHERE relation_types.relation_set_id IN (".$rels_imploded.")";
            $result = db_fetch_rows($sql);
            return $result;
        }
	}
	
}