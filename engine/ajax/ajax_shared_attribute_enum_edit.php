<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_edit extends CPageAdministration {

    function execute(){
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
            CDbAnnotationSharedAttribute::updateAnnotationAttributeValues($attributeId, $enumOldValue, $enumNewValue);
        }

		return;
	}
	
}