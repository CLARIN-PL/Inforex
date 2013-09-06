<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveAnnotation_lemma extends CPerspective {
	
	function execute(){
		$corpus_id = 7;
		$this->setup_annotation_config($corpus_id);
	}
	
	private function setup_annotation_config($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id); 
		$this->page->set('annotation_types',$annotations);
	}
	
}