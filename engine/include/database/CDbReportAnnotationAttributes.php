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
                WHERE att.annotation_id = ? AND att.annotation_attribute_id = ?  AND att.user_id = ? AND att.stage ='agreement'";
        $params = array($annotation_id, $attr_id, $user_id);

        return $db->fetch_one($sql, $params);
    }

    static function updateAttributeValue($annotation_id, $attr_id, $user_id, $value, $stage)
    {
        global $db;
        if($stage == null){
            $stage="agreement";
        }
        $sql_replace = "REPLACE reports_annotations_attributes SET annotation_id = ?, annotation_attribute_id = ?, value = ?, user_id = ?, stage = ?";
        $db->execute($sql_replace, array($annotation_id, $attr_id, $value, $user_id, $stage));
    }

    static function deleteAttributeValue($annotation_id, $attr_id, $user_id, $stage='final')
    {
        global $db;
        $sql_replace = "DELETE FROM reports_annotations_attributes WHERE annotation_id = ? AND annotation_attribute_id = ? AND user_id = ? AND stage = ?";
        $db->execute($sql_replace, array($annotation_id, $attr_id, $user_id, $stage));
    }

    public static function getAnnotators($reportIds){
        global $db;

        $reportIdsString = count($reportIds) > 0 ? implode(',', $reportIds) : 'null';

        $sql = "SELECT count(*) as annotation_count, a.user_id, u.screename FROM (           
	                SELECT COUNT(*), raa.user_id as user_id, raa.value FROM  reports_annotations_attributes raa
	                LEFT JOIN reports_annotations_optimized ra ON ra.id = raa.annotation_id
	                WHERE ra.report_id in (". $reportIdsString .") AND raa.user_id IS NOT NULL AND ra.stage = 'final' AND raa.stage='agreement'
	                GROUP BY raa.user_id, raa.value
	                ORDER BY raa.user_id) a
                LEFT JOIN users u on u.user_id = a.user_id
                GROUP by a.user_id";
        return $db->fetch_rows($sql);
    }
}