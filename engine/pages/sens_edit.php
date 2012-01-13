<?php
class Page_sens_edit extends CPage{

	var $isSecure = true;
//	var $roles = array("admin", "editor_schema_relations");
	
	function execute(){		
		global $db;
		$sql = "SELECT * FROM annotation_types_attributes ORDER BY annotation_type";
		$sens = $db->fetch_rows($sql);
		foreach($sens as $key => $value){
			$sens[$key]['annotation_type'] = substr($sens[$key]['annotation_type'], 4); // obcinanie wsd_ 
		}
		$this->set("sensList", $sens);
	}
}


?>