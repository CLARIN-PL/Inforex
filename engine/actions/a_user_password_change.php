<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_user_password_change extends CAction{
		
	function checkPermission(){
		global $user;
		if (!isset($user['role']['loggedin'])){
			$this->set("action_permission_denied", "Tylko zalogowany użytkownik ma prawo zmiany hasła");
			return false;
		}			
		else
			return true;
	} 
	
	function execute(){
		global $db, $user;
		
		$sql = "SELECT PASSWORD FROM users WHERE user_id = {$user['user_id']}";
		if($db->fetch_one($sql) == md5($_POST['old_pass'])){
			$sql = "UPDATE users SET password = MD5('{$_POST['new_pass2']}') WHERE user_id = {$user['user_id']}";
			$db->execute($sql);
			$this->set("action_performed", "Hasło zostało zmienione");	
		}
		else{
			$this->set("action_error", "Błędne stare hasło");
		}
		
		return null;
	}	
} 

?>
