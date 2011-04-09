<?php
class Page_event_edit extends CPage{

	var $isSecure = true;
	var $roles = array("admin");
	
	function execute(){		
		global $user;
		
		
		$sql = "SELECT event_group_id AS id, name, description FROM event_groups";
		
		$eventGroups = db_fetch_rows($sql);
		
		
		/*$activities = db_fetch_rows("" .
				"SELECT a.*, u.`screename`, TIMESTAMPDIFF(MINUTE, `started`, `ended`) AS duration" .
				" FROM `user_activities` a" . 
				" JOIN `users` u" .
				" USING (`user_id`)");
		
		$this->set("activities", $activities);
				
		*/
		$this->set("eventGroups", $eventGroups);

	}
}


?>