<?php
class Page_user_roles extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function execute(){		
		global $db;
		
		if (hasRole(USER_ROLE_ADMIN)){
			$sql = "SELECT user_id, login, screename FROM users";
			$this->set("all_users", $db->fetch_rows($sql));
		}		
	}
}
?>