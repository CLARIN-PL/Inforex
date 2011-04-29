<?php
class Page_annotation_edit extends CPage{

	var $isSecure = true;
	var $roles = array("admin");
	
	function execute(){		
		global $user;
		$sql = "SELECT annotation_set_id AS id, description FROM annotation_sets";
		$annotationSets = db_fetch_rows($sql);
		$this->set("annotationSets", $annotationSets);
	}
}


?>