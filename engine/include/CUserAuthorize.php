<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class UserAuthorize extends Auth{

	function __construct($dsn){
		$params = array(
		            "dsn" => $dsn,
		            "table" => "users",
		            "usernamecol" => "login",
		            "passwordcol" => "password",
		            "db_fields" => array("user_id", "screename")
		            );
		parent::__construct("MDB2", $params, null, false);
	}
		
	function authorize($logout=true){
		if ($logout){
			$this->logout();
		}else{			
			$this->start();
		} 		
	}		
	
	function getUserData(){	
		$user = $this->getAuthData();
		// Pobierz role użytkownika
		if ($user){
			$roles = db_fetch_rows("SELECT * FROM users_roles us JOIN roles USING (role) WHERE user_id=".$user['user_id']);
			$user['role']['loggedin'] = "User is loggedin to the system";
			foreach ($roles as $role){
				$user['role'][$role['role']] = $role['description'];
			}
			
			UserActivity::log($user['user_id']);
		}
		
		return $user;		
	}
	
}

?>