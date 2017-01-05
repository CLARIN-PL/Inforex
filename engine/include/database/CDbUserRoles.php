<?php

class DbUserRoles{
	
	/**
	 * Return user roles
	 * @param $user_id User identifier
	 * @return array of role names, i.e. array("admin")
	 */
	static function get($user_id){
		global $db;
		$sql = "SELECT role FROM users_roles WHERE user_id=?";
		$params = array($user_id);
		$rows = $db->fetch_ones($sql, "role", $params);
		return $rows;	
	}
	
	/**
	 * Set roles for user.
	 * @param $user_id User identifier
	 * @param $roles Array of roles to set
	 */
	static function set($user_id, $roles){
		global $db;
		$db->execute("BEGIN");
		$db->execute("DELETE FROM users_roles WHERE user_id = ?", array($user_id));
		foreach ( $roles as $role ){
			$db->execute("INSERT INTO users_roles (user_id, role) VALUES(?, ?)",
						array($user_id, $role));
		}
		$db->execute("COMMIT");
	}
	
}

?>