<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_annotation_attribute_bindings_get extends CPageAdministration {

    function execute(){
        global $db;

        $annotationTypeId = intval($this->getRequestParameterRequired('annotation_type_id'));
        $annotationType = $db->fetch(
            "SELECT at.annotation_type_id, at.name, ans.name AS set_name, ansub.name AS subset_name " .
            "FROM annotation_types at " .
            "LEFT JOIN annotation_sets ans ON ans.annotation_set_id = at.group_id " .
            "LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id " .
            "WHERE at.annotation_type_id = ?",
            array($annotationTypeId)
        );

        if (!$annotationType) {
            throw new Exception('Annotation type not found');
        }

        $attributes = $db->fetch_rows(
            "SELECT sa.id, sa.name, sa.type, sa.description, " .
            "CASE WHEN EXISTS (" .
                "SELECT 1 FROM annotation_types_shared_attributes atsa " .
                "WHERE atsa.shared_attribute_id = sa.id AND atsa.annotation_type_id = ?" .
            ") THEN 1 ELSE 0 END AS assigned " .
            "FROM shared_attributes sa " .
            "ORDER BY sa.name, sa.id",
            array($annotationTypeId)
        );

        foreach ($attributes as &$attribute) {
            $attribute['assigned'] = intval($attribute['assigned']) === 1;
        }

        return array('annotation_type' => $annotationType, 'attributes' => $attributes);
    }
}
