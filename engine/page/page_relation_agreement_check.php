<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_relation_agreement_check extends CPage{

    var $isSecure = true;

    function checkPermission(){
        //global $corpus;
        return hasCorpusRole(CORPUS_ROLE_AGREEMENT_CHECK);
    }

    function execute(){
        $this->includeJs("js/c_autoresize.js");

        global $corpus;

        /* Variable declaration */
        $corpus_id = intval($corpus['id']);
        $subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();
        $corpus_flag_id = intval($_COOKIE["relation_check_flag"]);
        $flag_id = intval($_COOKIE["relation_check_flag_type"]);
        $flag = array();

        $this->setup_annotation_relation_trees($corpus_id);

        $annotation_types = CookieManager::getRelationAgreementAnnotationTypes($corpus_id);
        $relation_types = CookieManager::getRelationAgreementRelationTypes($corpus_id);
        $subcorpora_ids = CookieManager::getRelationAgreementSubcorpora();

        if ( $corpus_flag_id !== 0 && $flag_id !== 0 ){
            $flag = array(
                          'corpus_flag_id' => $corpus_flag_id,
                          'flag_id' => $flag_id);
        }

        // TODO: do ujednolicenia z setupUserSelectionAB
        $annotator_a_id = intval($_COOKIE['relation_check_annotator_a_id']);
        $annotator_b_id = intval($_COOKIE['relation_check_annotator_b_id']);

        $selected_users = array(
            'a' => $annotator_a_id,
            'b' => $annotator_b_id
        );

        $annotators = DbRelationAgreement::getUserRelationCount($corpus_id, $subcorpora_ids, $annotation_types, $relation_types, $flag);
        $relation_agreement = DbRelationAgreement::getRelationsAgreement($corpus_id, $subcorpora_ids, $annotation_types, $relation_types, $flag, $selected_users);

        $agreement = $relation_agreement['relations_compared'];
        $pcs = $relation_agreement['pcs'];

        /* Assign variables to the template */
        $this->set("annotators", $annotators);
        $this->set("annotator_a_id", $annotator_a_id);
        $this->set("annotator_b_id", $annotator_b_id);
        $this->set("agreement", $agreement);
        $this->set("pcs", $pcs);
        $this->set("subcorpora", $subcorpora);
        $this->set("subcorpus_ids", $subcorpora_ids);
        $this->set("corpus_flags", $corpus_flags);
        $this->set("flags", $flags);
        $this->set("corpus_flag_id", $corpus_flag_id);
        $this->set("flag_id", $flag_id);
    }

    /**
     * Ustaw strukturę dostępnych typów anotacji.
     * @param unknown $corpus_id
     */
    private function setup_annotation_relation_trees($corpus_id){
        $annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);
        $relations = DbRelationSet::getRelationTree($corpus_id);

        $this->set('relation_types',$relations);
        $this->set('annotation_types',$annotations);
    }

}

