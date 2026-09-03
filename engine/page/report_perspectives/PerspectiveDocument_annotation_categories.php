<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../../include/database/CDbDocumentAnnotationCategory.php';

class PerspectiveDocument_annotation_categories extends CPerspective {

    function execute() {
        $corpusId = intval($this->document['corpora']);
        $reportId = intval($this->document['id']);

        $this->page->set('document_annotation_category_tree', DbDocumentAnnotationCategory::getAvailableAnnotationTree($corpusId));
        $this->page->set('document_annotation_category_selected_ids', DbDocumentAnnotationCategory::getDocumentAnnotationTypeIds($reportId));
        $this->page->set('document_annotation_category_selected', DbDocumentAnnotationCategory::getDocumentAnnotations($reportId));
    }
}
