<?php
class Page_user_admin extends CPage{

	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){		
		global $db;
		$sql = "SELECT u.user_id, u.login, u.screename, u.email, " .
				"	group_concat(role) AS roles" .
				" FROM users u" .
				" LEFT JOIN users_roles ur USING (user_id)" .
				" GROUP BY u.user_id" .
				" ORDER BY u.login";
		$this->set("all_users", $db->fetch_rows($sql));
	}
}
?>