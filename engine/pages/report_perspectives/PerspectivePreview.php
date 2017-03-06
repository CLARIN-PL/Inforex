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
		
		$stages = array("", "new", "final", "discarded");
		$stage = strval($_COOKIE['stage']);
		
		PerspectiveAnnotator::set_panels();
		PerspectiveAnnotator::set_annotation_menu();
		PerspectiveAnnotator::set_relations();
		PerspectiveAnnotator::set_relation_sets();		
		PerspectiveAnnotator::set_annotations($stage, null, null, $force_annotation_set_id);
		
		$this->page->set("stage", $stage);
		$this->page->set("stages", $stages);
	}
}
?>
