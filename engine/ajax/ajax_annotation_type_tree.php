<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_type_tree extends CPagePublic {

	function execute(){
		$cid = strval($_POST['corpusId']);
        $annotation_sets =  DbAnnotation::getAnnotationStructureByCorpora($cid);
		return $annotation_sets;
	}

} // Ajax_annotation_type_tree
