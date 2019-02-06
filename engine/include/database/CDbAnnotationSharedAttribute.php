<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CDbAnnotationSharedAttribute{

    function get($sharedAttributeId){
        global $db;
        $sql = "SELECT * FROM shared_attributes WHERE id = ?";
        return $db->fetch($sql, array($sharedAttributeId));
    }

    function existsAttributeEnumValue($sharedAttributeId, $value){
        global $db;
        $sql = "SELECT * FROM shared_attributes_enum WHERE shared_attribute_id = ? AND `value` = ?";
        return count($db->fetch_rows($sql, array($sharedAttributeId, $value)))>0;
    }

    function addAttributeEnumValue($sharedAttributeId, $value){
        global $db;
        $sql = "INSERT INTO shared_attributes_enum (shared_attribute_id, `value`) VALUES(?, ?)";
        $db->execute($sql, array($sharedAttributeId, $value));
    }

}