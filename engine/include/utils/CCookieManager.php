<?php

/**
 * Contains a set of methods to store and obtained specific data to/from the cookie.
 *
 * @author Michał Marcińczuk
 */
class CookieManager {

    /**
     * Returns a list of selected annotation types for given corpus.
     * @param $corpus_id Corpus identifier for which the selection should be returned
     * @return a list of annotation type identifiers
     */
    static function getAnnotationTypeTreeAnnotationTypes($corpus_id){
        $annotation_types_str = trim(strval($_COOKIE[$corpus_id . '_annotation_lemma_types']));
        $annotation_types = array();
        foreach ( explode(",", $annotation_types_str) as $id ){
            $id = intval($id);
            if ( $id > 0 ){
                $annotation_types[] = $id;
            }
        }
        return $annotation_types;
    }

}