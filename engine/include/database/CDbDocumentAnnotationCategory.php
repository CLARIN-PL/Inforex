<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbDocumentAnnotationCategory {

    const PERSPECTIVE_ID = 'document_annotation_categories';

    static function getPerspectiveAnnotationSets($corpusId, $perspectiveId = self::PERSPECTIVE_ID) {
        global $db;

        $sql = "SELECT pas.annotation_set_id, ans.name, ans.description\n"
            . "FROM corpus_report_perspective_annotation_sets pas\n"
            . "JOIN annotation_sets ans ON ans.annotation_set_id = pas.annotation_set_id\n"
            . "WHERE pas.corpus_id = ? AND pas.perspective_id = ?\n"
            . "ORDER BY ans.name, ans.annotation_set_id";

        return $db->fetch_rows($sql, array($corpusId, $perspectiveId));
    }

    static function getPerspectiveAnnotationSetIds($corpusId, $perspectiveId = self::PERSPECTIVE_ID) {
        $rows = self::getPerspectiveAnnotationSets($corpusId, $perspectiveId);
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = intval($row['annotation_set_id']);
        }
        return $ids;
    }

    static function replacePerspectiveAnnotationSets($corpusId, $perspectiveId, $annotationSetIds) {
        global $db;

        $annotationSetIds = array_values(array_unique(array_map('intval', is_array($annotationSetIds) ? $annotationSetIds : array())));

        $allowedRows = DbAnnotationSet::getAnnotationSetsAssignedToCorpus($corpusId);
        $allowedIds = array();
        foreach ($allowedRows as $row) {
            $allowedIds[] = intval($row['annotation_set_id']);
        }

        foreach ($annotationSetIds as $annotationSetId) {
            if (!in_array($annotationSetId, $allowedIds)) {
                throw new Exception("Annotation set $annotationSetId is not assigned to this corpus.");
            }
        }

        $db->execute(
            "DELETE FROM corpus_report_perspective_annotation_sets WHERE corpus_id = ? AND perspective_id = ?",
            array($corpusId, $perspectiveId)
        );

        foreach ($annotationSetIds as $annotationSetId) {
            $db->execute(
                "INSERT INTO corpus_report_perspective_annotation_sets(corpus_id, perspective_id, annotation_set_id) VALUES (?, ?, ?)",
                array($corpusId, $perspectiveId, $annotationSetId)
            );
        }
    }

    static function getAvailableAnnotationTypes($corpusId, $perspectiveId = self::PERSPECTIVE_ID) {
        global $db;

        $sql = "SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ans.description AS set_description,\n"
            . "       ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name, ansub.description AS subset_description,\n"
            . "       at.annotation_type_id AS type_id, at.name AS type_name, at.short_description AS type_description\n"
            . "FROM corpus_report_perspective_annotation_sets pas\n"
            . "JOIN annotation_sets ans ON ans.annotation_set_id = pas.annotation_set_id\n"
            . "JOIN annotation_types at ON at.group_id = ans.annotation_set_id\n"
            . "LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id\n"
            . "WHERE pas.corpus_id = ? AND pas.perspective_id = ?\n"
            . "ORDER BY ans.name, ansub.name, at.name";

        return $db->fetch_rows($sql, array($corpusId, $perspectiveId));
    }

    static function getAvailableAnnotationTree($corpusId, $perspectiveId = self::PERSPECTIVE_ID) {
        $rows = self::getAvailableAnnotationTypes($corpusId, $perspectiveId);
        $tree = array();

        foreach ($rows as $row) {
            $setId = intval($row['set_id']);
            $subsetKey = $row['subset_id'] === null ? 'uncategorized' : strval($row['subset_id']);

            if (!isset($tree[$setId])) {
                $tree[$setId] = array(
                    'id' => $setId,
                    'name' => $row['set_name'],
                    'description' => $row['set_description'],
                    'subsets' => array(),
                );
            }

            if (!isset($tree[$setId]['subsets'][$subsetKey])) {
                $tree[$setId]['subsets'][$subsetKey] = array(
                    'id' => $row['subset_id'] === null ? null : intval($row['subset_id']),
                    'name' => $row['subset_name'] ? $row['subset_name'] : 'Uncategorized',
                    'description' => $row['subset_description'],
                    'types' => array(),
                );
            }

            $tree[$setId]['subsets'][$subsetKey]['types'][] = array(
                'id' => intval($row['type_id']),
                'name' => $row['type_name'],
                'description' => $row['type_description'],
            );
        }

        foreach ($tree as &$set) {
            $set['subsets'] = array_values($set['subsets']);
        }

        return array_values($tree);
    }

    static function getDocumentAnnotationTypeIds($reportId) {
        global $db;

        $rows = $db->fetch_rows(
            "SELECT annotation_type_id FROM reports_document_annotation_types WHERE report_id = ? ORDER BY annotation_type_id",
            array($reportId)
        );

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = intval($row['annotation_type_id']);
        }
        return $ids;
    }

    static function getDocumentAnnotations($reportId) {
        global $db;

        $sql = "SELECT rdat.annotation_type_id AS type_id, at.name AS type_name, at.short_description AS type_description,\n"
            . "       ans.annotation_set_id AS set_id, ans.name AS set_name,\n"
            . "       ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name\n"
            . "FROM reports_document_annotation_types rdat\n"
            . "JOIN annotation_types at ON at.annotation_type_id = rdat.annotation_type_id\n"
            . "JOIN annotation_sets ans ON ans.annotation_set_id = at.group_id\n"
            . "LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id\n"
            . "WHERE rdat.report_id = ?\n"
            . "ORDER BY ans.name, ansub.name, at.name";

        return $db->fetch_rows($sql, array($reportId));
    }

    static function replaceDocumentAnnotationTypeIds($reportId, $corpusId, $userId, $typeIds, $perspectiveId = self::PERSPECTIVE_ID) {
        global $db;

        $typeIds = array_values(array_unique(array_map('intval', is_array($typeIds) ? $typeIds : array())));
        $allowedRows = self::getAvailableAnnotationTypes($corpusId, $perspectiveId);
        $allowedTypeIds = array();
        foreach ($allowedRows as $row) {
            $allowedTypeIds[] = intval($row['type_id']);
        }

        foreach ($typeIds as $typeId) {
            if (!in_array($typeId, $allowedTypeIds)) {
                throw new Exception("Annotation type $typeId is not available in this perspective.");
            }
        }

        $db->execute("DELETE FROM reports_document_annotation_types WHERE report_id = ?", array($reportId));

        foreach ($typeIds as $typeId) {
            $db->execute(
                "INSERT INTO reports_document_annotation_types(report_id, annotation_type_id, user_id, creation_time) VALUES (?, ?, ?, NOW())",
                array($reportId, $typeId, $userId)
            );
        }
    }
}
