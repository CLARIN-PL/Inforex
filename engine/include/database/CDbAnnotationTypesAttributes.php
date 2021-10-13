<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CDbAnnotationTypesAttributes
{

    static function getAnnotationTypeAttributeIdForSensByAnnotationId($annotation_id)
    {
        global $db;
        $sql = "SELECT ata.id FROM annotation_types_attributes ata
                JOIN reports_annotations an ON (an.type_id = ata.annotation_type_id)
                WHERE an.id = ? AND ata.name = 'sense'";
        return $db->fetch_one($sql, array($annotation_id));
    }
}