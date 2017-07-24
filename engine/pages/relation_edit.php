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
        $this->includeJs("js/c_autoresize.js");

		$sql = "SELECT relation_set_id AS id, name as description FROM relation_sets";
		$relationSets = db_fetch_rows($sql);
		$this->set("relationSets", $relationSets);

        $sql = "SELECT ans.annotation_set_id AS id, ans.name, ans.description, ans.public" .
            " FROM annotation_sets ans " .
            " ORDER BY id";
        $annotationSets = db_fetch_rows($sql);
        $this->set("annotationSets", $annotationSets);

        $sql = "SELECT * FROM relations_groups";
        $relationGroups = db_fetch_rows($sql);
        $this->set("relationsGroups", $relationGroups);
	}
}


?>