<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_annotation_shared_attribute_update extends CPageCorpus {

    function __construct(){
        // ToDo: prawo edycji anotacji CORPUS_ROLE_ANNOTATE_AGREEMENT powinno dotyczyć wyłącznie anotacji o stage=agreement
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		$annotationId = $this->getRequestParameterRequired("annotation_id");
        $sharedAttributeId = $this->getRequestParameterRequired("shared_attribute_id");
        $value = $this->getRequestParameter("value", "");

        return $this->updateSharedAttributeValue($annotationId, $sharedAttributeId, $value);
	}

    /**
     *
     */
	function updateSharedAttributeValue($annotationId, $sharedAttributeId, $value){
	    DbAnnotation::setSharedAttributeValue($annotationId, $sharedAttributeId, $value, $this->getUserId());
	    $attr = CDbAnnotationSharedAttribute::get($sharedAttributeId);
        if ( $attr['type'] == DB_SHARED_ATTRIBUTE_TYPES_ENUM
                && strlen(trim($value)) > 0
                && !CDbAnnotationSharedAttribute::existsAttributeEnumValue($sharedAttributeId, $value)){
            CDbAnnotationSharedAttribute::addAttributeEnumValue($sharedAttributeId, $value);
        }
        $result = array();
        $result["annotation_id"] = $annotationId;
        $result["shared_attribute_id"] = $sharedAttributeId;
        $result["value"] = $value;
        return $result;
    }

}