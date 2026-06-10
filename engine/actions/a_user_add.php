<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_user_add extends CAction{
		
	function checkPermission(){
		return hasRole(USER_ROLE_ADMIN);
	}
	
	function execute(){
		try{
			DbUser::createNewUser($_POST['login'], $_POST['name'], $_POST['email'], 'NOT SET');
			$this->set("action_performed", "Added user: \"". $_POST['name'] . "\", id: ".$this->getDb()->last_id());
		} catch(Exception $e){
			$this->set("action_error", "Error: ".$e->getMessage());
		}

        unset($_POST);
			
		return null;
	}	
} 

?>
