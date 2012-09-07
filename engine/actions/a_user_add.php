<?php

class Action_user_add extends CAction{
		
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN))
			return true;
		else{
			$this->set("action_permission_denied", "Tylko administrator ma prawo dodawać użytkowników");
			return false;
		}			
	} 
	
	function execute(){
		global $db, $mdb2;
		
		$sql = "INSERT INTO users ( login, screename, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', MD5('{$_POST['password']}'))";
		$db->execute($sql);
		$this->set("action_performed", "Dodano użytkownika \"". $_POST['name'] . "\" o id:".$mdb2->lastInsertID());	
		
		return null;
	}	
} 

?>
