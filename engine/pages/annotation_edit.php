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
        $this->includeJs("js/c_autoresize.js");
		global $user;

		$sql = "SELECT ans.annotation_set_id AS id, ans.description, ans.public, u.screename " .
				" FROM annotation_sets ans" .
                " JOIN users u ON u.user_id = ans.user_id " .
				" ORDER BY ans.description";
		$annotationSets = db_fetch_rows($sql);
		$this->set("annotationSets", $annotationSets);
	}
}


?>