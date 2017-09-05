<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_user_admin extends CPage{

	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){		
		global $db;
        $this->includeJs("js/c_autoresize.js");

		$sql = "SELECT u.user_id, u.login, u.screename, u.email, " .
				"	group_concat(role SEPARATOR ', ') AS roles" .
				" FROM users u" .
				" LEFT JOIN users_roles ur USING (user_id)" .
				" GROUP BY u.user_id" .
				" ORDER BY u.login";
		$users = $db->fetch_rows($sql);

		foreach($users as $key => $user){
		    $last_activity_sql = "  SELECT datetime as 'last_activity' FROM `activities`
                                WHERE user_id = ? 
                                ORDER BY datetime DESC";
		    $last_activity = $db->fetch_one($last_activity_sql, array($user['user_id']));
		    $users[$key]['last_activity'] = $last_activity;
        }

		$this->set("all_users", $users);
	}
}
?>