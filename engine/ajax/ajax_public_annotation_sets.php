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

        $used_in_corpora = DbAnnotationSet::getCorporaAnnotationSetStats($annotation_set_id);

        return $used_in_corpora;

    }
}
