<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_add extends CPageAdministration {

    function execute(){
		$shared_attribute_id = $this->getRequestParameterRequired('shared_attribute_id');
		$value = $this->getRequestParameterRequired('value_str');
		$desc = $this->getRequestParameterRequired('desc_str');
		CDbAnnotationSharedAttribute::addAttributeEnumValueWithDescription($shared_attribute_id, $value, $desc);
		return;
	}
	
}