<?php

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
