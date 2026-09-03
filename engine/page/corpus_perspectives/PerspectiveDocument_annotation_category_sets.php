<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once dirname(__FILE__) . '/../../include/database/CDbDocumentAnnotationCategory.php';

class PerspectiveDocument_annotation_category_sets extends CCorpusPerspective {

    function execute() {
        global $corpus;

        $assignedSets = DbAnnotationSet::getAnnotationSetsAssignedToCorpus($corpus['id']);
        $selectedSetIds = DbDocumentAnnotationCategory::getPerspectiveAnnotationSetIds($corpus['id']);
        $perspectiveEnabled = intval($this->page->getDb()->fetch_one(
            'SELECT COUNT(*) FROM corpus_and_report_perspectives WHERE corpus_id = ? AND perspective_id = ?',
            array($corpus['id'], DbDocumentAnnotationCategory::PERSPECTIVE_ID)
        )) > 0;

        foreach ($assignedSets as &$set) {
            $set['selected_for_perspective'] = in_array(intval($set['annotation_set_id']), $selectedSetIds);
        }

        $this->page->set('documentAnnotationCategoryPerspectiveSets', $assignedSets);
        $this->page->set('documentAnnotationCategoryPerspectiveEnabled', $perspectiveEnabled);
    }
}
