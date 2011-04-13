<?php
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