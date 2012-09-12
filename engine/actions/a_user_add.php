<?php

class Action_user_add extends CAction{
		
	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){
		global $db, $mdb2;
		
		$sql = "INSERT INTO users ( login, screename, email, password ) VALUES ('{$_POST['login']}', '{$_POST['name']}', '{$_POST['email']}', MD5('{$_POST['password']}'))";
		$db->execute($sql);
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			$this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
		else
			$this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$mdb2->lastInsertID());
			
		return null;
	}	
} 

?>
