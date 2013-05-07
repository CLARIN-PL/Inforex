<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_relation_edit extends CPage{

	var $isSecure = true;
	var $roles = array("admin", "editor_schema_relations");
	
	function execute(){		
		global $user;
		$sql = "SELECT annotation_set_id AS id, description FROM annotation_sets";
		$annotationSets = db_fetch_rows($sql);
		$this->set("annotationSets", $annotationSets);
	}
}


?>