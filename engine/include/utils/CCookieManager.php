<?php

/**
 * Contains a set of methods to store and obtained specific data to/from the cookie.
 *
 * @author Michał Marcińczuk
 */
class CookieManager {

    /**
     * Returns a list of selected annotation types for given corpus.
     * @param $corpusId Corpus identifier for which the selection should be returned
     * @return a list of annotation type identifiers
     */
    static function getAnnotationTypeTreeAnnotationTypes($corpusId){
        $annotationTypesStr = trim(strval($_COOKIE[$corpusId . '_annotation_lemma_types']));
        $annotationTypes = array();
        foreach ( explode(",", $annotationTypesStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $annotationTypes[] = $id;
            }
        }
        return $annotationTypes;
    }

    static function getRelationAgreementAnnotationTypes($corpusId){
        $annotationTypesStr = trim(strval($_COOKIE[$corpusId . '_ann_type_relation_agreement_check']));
        $annotationTypes = array();
        foreach ( explode(",", $annotationTypesStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $annotationTypes[] = $id;
            }
        }
        return $annotationTypes;
    }

    static function getRelationAgreementSubcorpora(){
        $subcorporaStr = trim(strval($_COOKIE['relation_check_subcorpora']));
        $subcorpora = array();
        foreach ( explode(",", $subcorporaStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $subcorpora[] = $id;
            }
        }
        return $subcorpora;
    }

    static function getRelationAgreementRelationTypes($corpusId){
        $relationTypesStr = trim(strval($_COOKIE[$corpusId . '_rel_type_relation_agreement_check']));
        $relationTypes = array();
        foreach ( explode(",", $relationTypesStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $relationTypes[] = $id;
            }
        }
        return $relationTypes;
    }

    static function getSelectedAnnotationTypeTreeAnnotationTypes($corpusId){
        $annotationTypesStr = trim(strval($_COOKIE[$corpusId . '_annotation_lemma_layers']));
        $annotationTypes = array();
        foreach ( explode(",", $annotationTypesStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $annotationTypes[] = $id;
            }
        }
        return $annotationTypes;
    }

    /**
     * Returns a list of selected relation sets for given corpus.
     * @param $corpusId Corpus identifier for which the selection should be returned
     * @return a list of relation set identifiers
     */
    static function getRelationSets($corpusId){
        $relationSetsStr = trim(strval($_COOKIE[$corpusId . '_relation_sets']));
        $relationSets = array();
        foreach ( explode(",", $relationSetsStr) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $relationSets[] = $id;
            }
        }
        return $relationSets;
    }

}