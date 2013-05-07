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
		PerspectiveAnnotator::set_panels();
		PerspectiveAnnotator::set_annotation_menu();
		PerspectiveAnnotator::set_relations();
		PerspectiveAnnotator::set_relation_sets();		
		PerspectiveAnnotator::set_annotations();
	}
}
?>
