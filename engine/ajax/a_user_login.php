<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_user_login extends CPage {
	
	function execute(){
		global $auth;
		if ($auth->checkAuth()){
			$user = $auth->getUserData();
			UserActivity::login($user['user_id']);
			return;
		}else{
			throw new Exception($auth->getStatus());			
		}		
	}
	
}
?>
