<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../include/database/CDbDocumentAnnotationCategory.php';

class Ajax_document_annotation_category_sets_save extends CPageCorpus {

    function execute(){
        $annotationSetIds = isset($_POST['annotation_set_ids']) && is_array($_POST['annotation_set_ids'])
            ? $_POST['annotation_set_ids']
            : array();

        DbDocumentAnnotationCategory::replacePerspectiveAnnotationSets(
            intval($this->getCorpusId()),
            DbDocumentAnnotationCategory::PERSPECTIVE_ID,
            $annotationSetIds
        );

        return array('message' => 'Document annotation category sets were saved.');
    }
}
