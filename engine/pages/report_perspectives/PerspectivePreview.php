<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectivePreview extends CPerspective {
	
	function execute()
	{
		$force_annotation_set_id = intval($_GET['annotation_set_id']);
		
		PerspectiveAnnotator::set_panels();
		PerspectiveAnnotator::set_annotation_menu();
		PerspectiveAnnotator::set_relations();
		PerspectiveAnnotator::set_relation_sets();		
		PerspectiveAnnotator::set_annotations(null, null, null, $force_annotation_set_id);
	}
}
?>
