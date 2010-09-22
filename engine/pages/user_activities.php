<?php
class Page_user_activities extends CPage{

	var $isSecure = true;
	var $roles = array("admin");
	
	function execute(){		
		global $user;
		
		$activities = db_fetch_rows("" .
				"SELECT a.*, u.`screename`, TIMESTAMPDIFF(MINUTE, `started`, `ended`) AS duration" .
				" FROM `user_activities` a" . 
				" JOIN `users` u" .
				" USING (`user_id`)");
		
		$this->set("activities", $activities);
				
	}
}


?>
