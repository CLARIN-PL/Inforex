<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../include/database/CDbDocumentAnnotationCategory.php';

class Action_document_annotation_categories_save extends CAction {

    function checkPermission(){
        if (hasRole('admin') || hasCorpusRole(CORPUS_ROLE_EDIT_DOCUMENTS) || isCorpusOwner()) {
            return true;
        }
        return 'Brak prawa do edycji dokumentów';
    }

    function execute(){
        global $user, $corpus;

        $reportId = intval($_POST['report_id']);
        $report = DbReport::getReportById($reportId);
        if (!$report) {
            $this->set('error', 'Document not found.');
            return '';
        }

        if (intval($report['corpora']) !== intval($corpus['id'])) {
            $this->set('error', 'Document does not belong to the current corpus.');
            return '';
        }

        $typeIds = isset($_POST['annotation_type_ids']) && is_array($_POST['annotation_type_ids'])
            ? $_POST['annotation_type_ids']
            : array();

        DbDocumentAnnotationCategory::replaceDocumentAnnotationTypeIds(
            $reportId,
            intval($report['corpora']),
            intval($user['user_id']),
            $typeIds
        );

        $this->set('info', 'Document annotation categories were saved.');
        return '';
    }
}
