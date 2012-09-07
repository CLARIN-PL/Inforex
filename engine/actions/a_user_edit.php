<?php

class Action_user_edit extends CAction{
		
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN))
			return true;
		else{
			$this->set("action_permission_denied", "Tylko administrator ma prawo edytować użytkowników");
			return false;
		}			
	} 
	
	function execute(){
		global $db;
		
		$sql = "UPDATE users SET login = '{$_POST['login']}', screename = '{$_POST['name']}' ". (strlen($_POST['password']) ? ", password = MD5('{$_POST['password']}') " : "") ." WHERE user_id = {$_POST['user_id']}";
				 
		$db->execute($sql);
		$this->set("action_performed", "Zmieniono dane użytkownika \"". $_POST['name'] );	
		return null;
	}	
} 

?>