<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_shared_attribute_enum_delete extends CPageAdministration {


    function execute(){
		$shared_attribute_id = $this->getRequestParameter('shared_attribute_id');
		$value_str = $this->getRequestParameter('value_str');
		CDbAnnotationSharedAttribute::deleteAttributeValue($shared_attribute_id, $value_str);
		return;
	}
	
}