<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_public_annotation_sets extends CPage
{

    var $isSecure = false;

    function execute()
    {
        $annotation_set_id = $_POST['annotation_set_id'];

        $used_in_corpora = DbAnnotationSet::getCorporaOfAnnotationSet($annotation_set_id);
        $public_corpora = array();
        foreach($used_in_corpora as $corpus){
            if($corpus['public'] == 1){
                $public_corpora[] = $corpus;
            }
        }

        return $public_corpora;

    }
}
?>
