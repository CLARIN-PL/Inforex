<?php
class Page_user_admin extends CPage{

	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){		
		global $db;
		$sql = "SELECT user_id, login, screename, email FROM users";
		$this->set("all_users", $db->fetch_rows($sql));
	}
}
?>