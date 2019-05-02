<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_annotation_attributes_export extends CPageCorpus {

    function __construct(){
        parent::__construct("Annotation attributes", "Browse annotations by their attribute values");
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;

        $this->includeJs("libs/select2/js/select2.full.js");
        $this->includeCss("libs/select2/css/select2.min.css");
    }
		
	function execute(){
		$attributeId = $this->getRequestParameter("attribute_id", null);
        $subcorpusId = $this->getRequestParameter("subcorpus_id", null);
        $languageCode = $this->getRequestParameter("language", "");
        $this->set("rows", CDbAnnotationSharedAttribute::getAttributeAnnotationValues(
            $this->getCorpusId(), $attributeId, $languageCode, $subcorpusId));

        $filename = "attribute_${attributeId}_values.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
    }
		
}
