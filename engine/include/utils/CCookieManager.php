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