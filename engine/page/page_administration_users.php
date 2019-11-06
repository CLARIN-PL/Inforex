<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_users extends CPageAdministration {

	function execute(){
		global $db;
		$sql = "SELECT u.user_id, u.login, u.screename, u.email, " .
				"	group_concat(role SEPARATOR ', ') AS roles" .
				" FROM users u" .
				" LEFT JOIN users_roles ur USING (user_id)" .
				" GROUP BY u.user_id" .
				" ORDER BY u.login";
		$users = $db->fetch_rows($sql);
		$this->set("all_users", $users);
	}
}