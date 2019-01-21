<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_edit extends CPageAdministration {

    function execute(){
		global $db;

		$attributeId = intval($_POST['attributeId']);
		$enumOldValue = strval($_POST['enumOldValue']);
        $enumNewValue = strval($_POST['enumNewValue']);
		$enumDescription = strval($_POST['enumDescription']);

		if ( $attributeId && $enumNewValue && $enumOldValue) {
            $sql = "UPDATE shared_attributes_enum SET value=?, description =? WHERE shared_attribute_id = ? AND value = ?";
            $params = array($enumNewValue, $enumDescription, $attributeId, $enumOldValue);
            $db->execute($sql, $params);

            if ($enumOldValue != $enumNewValue) {
                $sql = "UPDATE reports_annotations_shared_attributes SET value = ? WHERE shared_attribute_id = ? AND value = ?";
                $params = array($enumNewValue, $attributeId, $enumOldValue);
                $db->execute($sql, $params);
            }
        }
		return;
	}
	
}