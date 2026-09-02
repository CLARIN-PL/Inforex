<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../include/database/CDbDocumentAnnotationCategory.php';

class Ajax_document_annotation_categories_save extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EDIT_DOCUMENTS;
    }

    function execute(){
        $reportId = intval($_POST['report_id']);
        $report = DbReport::getReportById($reportId);
        if (!$report) {
            throw new Exception('Document not found.');
        }

        if (intval($report['corpora']) !== intval($this->getCorpusId())) {
            throw new Exception('Document does not belong to the current corpus.');
        }

        $typeIds = isset($_POST['annotation_type_ids']) && is_array($_POST['annotation_type_ids'])
            ? $_POST['annotation_type_ids']
            : array();

        DbDocumentAnnotationCategory::replaceDocumentAnnotationTypeIds(
            $reportId,
            intval($report['corpora']),
            intval($this->getUserId()),
            $typeIds
        );

        return array(
            'report_id' => $reportId,
            'annotation_type_ids' => array_values(array_map('intval', $typeIds))
        );
    }
}
