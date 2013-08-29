<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annmap_load_tags extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2;
		$corpus_id = intval($_POST['corpus_id']);
		$annotation_type = $_POST['annotation_type'];
		
		$status = intval($_POST['status']);
		$subcorpus = intval($_POST['subcorpus']);
		
		$tags = DbAnnotation::getAnnotationTags($corpus_id, $annotation_type, $subcorpus, $status);
		
		return $tags;
	}

}
?>
