<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_annotation_schema extends CPage{

	function execute(){
        $this->includeJs("js/c_autoresize.js");
		$sql = "SELECT ans.annotation_set_id AS id, ans.name, ans.description, ans.public, u.screename " .
				" FROM annotation_sets ans" .
                " JOIN users u ON u.user_id = ans.user_id " .
				" ORDER BY id";
		$annotationSets = db_fetch_rows($sql);
		$this->set("annotationSets", $annotationSets);
	}
}