<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_annotation_frequency extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}	
	
	function execute(){
		global $corpus;

 		$subcorpus_id = intval($_GET['subcorpus_id']);		
 		$annotation_type_id = intval($_GET['annotation_type_id']);
 		$corpus_id = $corpus['id'];

 		$this->set("subcorpus_id", $subcorpus_id);
 		$this->set("annotation_type_id", $annotation_type_id);
 		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
 		$this->set("phrase", strval($_GET['phrase']));
 		$this->set("annotation_types", DbAnnotation::getAnnotationByTypeCount($corpus_id));
 		$this->set("annotation_stage", strval($_GET['annotation_stage']));
 		$this->set("annotation_stages", DbAnnotation::getAnnotationByStageCount($corpus_id));
 		$this->set("annotation_set_id", strval($_GET['annotation_set_id']));
 		$this->set("annotation_sets", DbAnnotation::getAnnotationBySetCount($corpus_id));
	}		

}
 
?>
