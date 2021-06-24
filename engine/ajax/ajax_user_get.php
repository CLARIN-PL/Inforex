<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_user_get extends CPageCorpus {

	/**
	 * Returns JSON with user data.
	 */
	function execute(){

		$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		
		$user = DbUser::get($user_id);
		$user['roles'] = DbUserRoles::get($user_id);
						 				
		return $user;		
	}	
}