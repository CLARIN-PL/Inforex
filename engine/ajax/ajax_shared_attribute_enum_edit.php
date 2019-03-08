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

		$attributeId = intval($this->getRequestParameter('attributeId'));
		$enumOldValue = $this->getRequestParameter('enumOldValue');
        $enumNewValue = $this->getRequestParameter('enumNewValue');
		$enumDescription = $this->getRequestParameter('enumDescription');

		if ( $enumNewValue == $enumOldValue ){
            CDbAnnotationSharedAttribute::updateAttributeDescription($attributeId, $enumNewValue, $enumDescription);
        } else if ( CDbAnnotationSharedAttribute::existsAttributeEnumValue($attributeId, $enumNewValue) ){
            CDbAnnotationSharedAttribute::updateAnnotationAttributeValues($attributeId, $enumOldValue, $enumNewValue);
            CDbAnnotationSharedAttribute::deleteAttributeValue($attributeId, $enumOldValue);
        } else {
            CDbAnnotationSharedAttribute::updateAttributeValue($attributeId, $enumOldValue, $enumNewValue);
            CDbAnnotationSharedAttribute::updateAttributeDescription($attributeId, $enumNewValue, $enumDescription);
        }

//        if ($enumOldValue != $enumNewValue) {
//
//            $sql = "UPDATE reports_annotations_shared_attributes SET description = ? WHERE shared_attribute_id = ? AND value = ?";
//            $params = array($enumNewValue, $attributeId, $enumOldValue);
//            $db->execute($sql, $params);
//
//            CDbAnnotationSharedAttribute::existsAttributeEnumValue($attributeId, $enumNewValue);
//        } else {
//            $sql = "UPDATE shared_attributes_enum SET description =? WHERE shared_attribute_id = ? AND value = ?";
//            $params = array($enumNewValue, $enumDescription, $attributeId, $enumOldValue);
//            $db->execute($sql, $params);
//        }

		return;
	}
	
}