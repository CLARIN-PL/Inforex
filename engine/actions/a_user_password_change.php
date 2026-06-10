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
		$this->set("action_error", "Zmiana hasła lokalnego jest wyłączona. Uwierzytelnianie jest obsługiwane przez Keycloak.");
		
		return null;
	}	
} 

?>
