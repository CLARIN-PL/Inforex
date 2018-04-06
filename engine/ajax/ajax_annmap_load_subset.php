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
		$corpus_id = intval($_POST['corpus_id']);
		$set_id = intval($_POST['set_id']);
		$subsets = DbAnnotation::getAnnotationSubsetsWithCount($corpus_id, $set_id, $_SESSION['annmap']);

		return $subsets;
	}

}