<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annmap_load_type extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2;
		$corpus_id = intval($_POST['corpus_id']);
		$subset_id = intval($_POST['subset_id']);
		$status = intval($_POST['status']);
		$subcorpus = intval($_POST['subcorpus']);
		
		$types = DbAnnotation::getAnnotationTypesWithCount($corpus_id, $subset_id, $subcorpus, $status);

		return $types;
	}

}
?>
