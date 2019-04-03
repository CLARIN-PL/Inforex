<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotations_with_attribute_value_get extends CPageCorpus {

    function __construct(){
        parent::__construct("annotations_with_attribute_value", "Returns a list of annotations with given attribute value");
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }
	
	function execute(){
		$attributeId = $this->getRequestParameterRequired("attribute_id");
		$attributeValue = $this->getRequestParameterRequired("attribute_value");
        $languageCode = $this->getRequestParameter("language", "");

		return CDbAnnotationSharedAttribute::getAnnotationsWithAttributeValue($attributeId, $attributeValue, $languageCode);
	}
}