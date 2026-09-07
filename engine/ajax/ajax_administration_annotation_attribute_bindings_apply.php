<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_annotation_attribute_bindings_apply extends CPageAdministration {

    function execute(){
        global $db;

        $annotationTypeId = intval($this->getRequestParameterRequired('annotation_type_id'));
        $sharedAttributeIds = $this->parseIds($this->getRequestParameter('shared_attribute_ids', ''));

        if (!$db->fetch_one("SELECT COUNT(*) FROM annotation_types WHERE annotation_type_id = ?", array($annotationTypeId))) {
            throw new Exception('Annotation type not found');
        }

        if (count($sharedAttributeIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($sharedAttributeIds), '?'));
            $validCount = intval($db->fetch_one(
                "SELECT COUNT(*) FROM shared_attributes WHERE id IN (" . $placeholders . ")",
                $sharedAttributeIds
            ));
            if ($validCount !== count($sharedAttributeIds)) {
                throw new Exception('One or more shared attributes do not exist');
            }
        }

        try {
            $db->execute("START TRANSACTION");
            $db->execute(
                "DELETE FROM annotation_types_shared_attributes WHERE annotation_type_id = ?",
                array($annotationTypeId)
            );
            foreach ($sharedAttributeIds as $sharedAttributeId) {
                $db->execute(
                    "INSERT INTO annotation_types_shared_attributes (annotation_type_id, shared_attribute_id) VALUES (?, ?)",
                    array($annotationTypeId, $sharedAttributeId)
                );
            }
            $db->execute("COMMIT");
        } catch (Exception $exception) {
            $db->execute("ROLLBACK");
            throw $exception;
        }

        return array(
            'annotation_type_id' => $annotationTypeId,
            'binding_count' => count($sharedAttributeIds),
            'shared_attribute_ids' => $sharedAttributeIds
        );
    }

    private function parseIds($rawIds){
        $ids = array();
        foreach (explode(',', strval($rawIds)) as $rawId) {
            $id = intval(trim($rawId));
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
        return array_values($ids);
    }
}
