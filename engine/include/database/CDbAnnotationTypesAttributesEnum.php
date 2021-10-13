<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CDbAnnotationTypesAttributesEnum
{

    static function getAnnotationTypeAttributesEnumRowsByAttributeId($attr_id)
    {
        global $db;
        $sql = "SELECT * FROM annotation_types_attributes_enum WHERE annotation_type_attribute_id=? ORDER BY value * 1";
        $params = array(intval($attr_id['id']));
        return $db->fetch_rows($sql, $params);
    }
}