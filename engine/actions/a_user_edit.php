<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
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
		
		$sql = "UPDATE users SET login = '{$_POST['login']}', screename = '{$_POST['name']}', email = '{$_POST['email']}' ". (strlen($_POST['password']) ? ", password = MD5('{$_POST['password']}') " : "") ." WHERE user_id = {$_POST['user_id']}";
		$db->execute($sql);
		
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			$this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
		else
			$this->set("action_performed", "Updated user \"". $_POST['name'] ."\"");	
		return null;
	}	
} 

?>