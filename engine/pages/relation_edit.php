<?php
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