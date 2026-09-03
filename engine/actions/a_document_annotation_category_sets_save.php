<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../include/database/CDbDocumentAnnotationCategory.php';

class Action_document_annotation_category_sets_save extends CAction {

    function checkPermission(){
        if (hasRole('admin') || hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner()) {
            return true;
        }
        return 'Brak prawa do zarzadzania ustawieniami korpusu';
    }

    function execute(){
        global $corpus;

        $annotationSetIds = isset($_POST['annotation_set_ids']) && is_array($_POST['annotation_set_ids'])
            ? $_POST['annotation_set_ids']
            : array();

        DbDocumentAnnotationCategory::replacePerspectiveAnnotationSets(
            intval($corpus['id']),
            DbDocumentAnnotationCategory::PERSPECTIVE_ID,
            $annotationSetIds
        );

        $this->set('info', 'Document annotation category sets were saved.');
        return '';
    }
}
