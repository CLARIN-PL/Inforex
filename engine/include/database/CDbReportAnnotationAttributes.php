<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CDbReportAnnotationAttributes
{

    static function getAnnotationTypeAttributesEnumRowsByAttributeId($annotation_id, $attr_id, $user_id)
    {
        global $db;
        $sql = "SELECT value FROM reports_annotations_attributes att
                WHERE att.annotation_id = ? AND att.annotation_attribute_id = ?  AND att.user_id = ?";
        $params = array($annotation_id, $attr_id, $user_id);

        return $db->fetch_one($sql, $params);
    }

    static function updateAttributeValue($annotation_id, $attr_id, $user_id, $value, $stage='agreement')
    {
        global $db;
        $sql_replace = "REPLACE reports_annotations_attributes SET annotation_id = ?, annotation_attribute_id = ?, value = ?, user_id = ?, stage = ?";
        $db->execute($sql_replace, array($annotation_id, $attr_id, $value, $user_id, $stage));
    }

    static function deleteAttributeValue($annotation_id, $attr_id, $user_id, $value, $stage='final')
    {
        global $db;
        $sql_replace = "DELETE FROM reports_annotations_attributes WHERE annotation_id = ? AND annotation_attribute_id = ? AND user_id = ? AND stage = ?";
        $db->execute($sql_replace, array($annotation_id, $attr_id, $user_id, $stage));
    }
}