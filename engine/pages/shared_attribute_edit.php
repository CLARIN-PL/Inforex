<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_shared_attribute_edit extends CPage{

	var $isSecure = true;
	//var $roles = array("admin", "editor_schema_shared_attributes");
	
	function execute(){		
		global $user;
		$sql = "SELECT id, name, type, description FROM shared_attributes";
		$sharedAttributes = db_fetch_rows($sql);
		$this->set("sharedAttributes", $sharedAttributes);
	}
}


?>