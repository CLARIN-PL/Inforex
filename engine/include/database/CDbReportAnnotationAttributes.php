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

    static function updateAttributeValue($annotation_id, $attr_id, $user_id, $value)
    {
        global $db;
        $sql_replace = "REPLACE reports_annotations_attributes SET annotation_id = ?, annotation_attribute_id = ?, value = ?, user_id = ?";
        $db->execute($sql_replace, array($annotation_id, $attr_id, $value, $user_id));
    }
}