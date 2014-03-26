<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_annotation_edit extends CPage{

	var $isSecure = true;
	var $roles = array("admin");
	
	function execute(){		
		global $user;
		$sql = "SELECT annotation_set_id AS id, description" .
				" FROM annotation_sets" .
				" ORDER BY description";
		$annotationSets = db_fetch_rows($sql);
		$this->set("annotationSets", $annotationSets);
	}
}


?>