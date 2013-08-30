<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annmap_load_subset extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2;
		$corpus_id = intval($_POST['corpus_id']);
		$set_id = intval($_POST['set_id']);
		$status = intval($_POST['status']);
		$subcorpus = intval($_POST['subcorpus']);
		
		$subsets = DbAnnotation::getAnnotationSubsetsWithCount($corpus_id, $set_id, $subcorpus, $status);
		
		
		return $subsets;
	}

}
?>
