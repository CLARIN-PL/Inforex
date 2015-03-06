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
			$this->set("action_permission_denied", "Tylko administrator ma prawo edytować dane użytkowników");
			return false;
		}			
	} 
	
	function execute(){
		global $db;
			

		$values = array();
		$keys = array();
		
		$user_id = $_POST['user_id'];
		
		$values['login'] = strval($_POST['login']);
		$values['screename'] = strval($_POST['name']);
		$values['email'] = strval($_POST['email']);
		if ( isset($_POST['password']) ){
			$params['passowrd'] = md5(strval($_POST['password']));
		}
		
		$keys['user_id'] = intval($user_id);

		$db->update("users", $values, $keys);
		
		$roles = $_POST['roles'];
		if ( !is_array($roles) ){
			$roles = array();
		}
		DbUserRoles::set($user_id, $roles);
		
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			$this->set("action_error", "Error: (". $error[1] . ") -> ".$error[2]);
		else
			$this->set("action_performed", "Updated user \"". $_POST['name'] ."\"");	
			
		return null;
	}	
} 

?>