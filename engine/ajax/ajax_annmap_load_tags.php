<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annmap_load_tags extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }

	function execute(){
		$corpus_id = intval($_POST['corpus_id']);
		$annotation_type = $_POST['annotation_type'];
		$tags = DbAnnotation::getAnnotationTags($corpus_id, $annotation_type, $_SESSION['annmap']);
		return $tags;
	}

}