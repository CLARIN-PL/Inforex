<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_event_edit extends CPage{

	var $isSecure = true;
	var $roles = array("admin", "editor_schema_events");
	
	function execute(){		
		global $user;
		$sql = "SELECT event_group_id AS id, name, description FROM event_groups";
		$eventGroups = db_fetch_rows($sql);
		$this->set("eventGroups", $eventGroups);
	}
}


?>