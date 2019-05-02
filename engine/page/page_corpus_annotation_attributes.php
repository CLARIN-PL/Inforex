<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_annotation_attributes extends CPageCorpus {

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

		ChromePhp::log($this->getCorpusId());

        $this->set("attributes", CDbAnnotationSharedAttribute::getAll());
        $this->set("attribute_id", $attributeId);
        $this->set("attribute_values", CDbAnnotationSharedAttribute::getAttributeAnnotationValues(
                $this->getCorpusId(), $attributeId, $languageCode, $subcorpusId));
        $this->set("languages", DbLang::getLangUsedInCorpus($this->getCorpusId()));
        $this->set("language", $languageCode);
        $this->set('subcorpora', DbCorpus::getCorpusSubcorpora($this->getCorpusId()));
        $this->set("subcorpus_id", $subcorpusId);
	}
		
}
