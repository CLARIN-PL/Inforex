<?php
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
		global $db;
		$user = $this->getAuthData();
		// Pobierz role użytkownika
		if ($user){
			$roles = $db->fetch_rows("SELECT * FROM users_roles us JOIN roles USING (role) WHERE user_id=?", array($user['user_id']));
			$login = $db->fetch_one("SELECT login FROM users WHERE user_id=?", array($user['user_id']));
			$user['role']['loggedin'] = "User is loggedin to the system";
			//$user['login'] = $login;
			foreach ($roles as $role){
				$user['role'][$role['role']] = $role['description'];
			}
			
			UserActivity::log($user['user_id']);
		}
		
		return $user;		
	}

	function redirectToClarinLogin(){
        global $config;
        header('Location: '.$config->federationLoginUrl."http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    }

    function getClarinUser(){
        global $config;

        // sudo apt-get install php5-curl
        if(isset($_COOKIE['clarin-pl-token'])) {
            $token = $_COOKIE['clarin-pl-token'];
            $curl = curl_init($config->federationValidateTokenUrl . $token);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // invalid token
            if ($httpcode !== 200) {
                $this->redirectToClarinLogin();
                return null;
            }
            return json_decode($response, true);
        }
        return null;
    }

	function getClarinLogin()
    {
        $userClarin = $this->getClarinUser();

        if ($userClarin) {
            $user = DbUser::getByClarinLogin($userClarin['login']);
            if ($user) {
                $id = $user['user_id'];
                $login = $user['login'];

                $this->setAuth($login);
                $this->setAuthData('user_id', $id);
                $this->setAuthData('screename', $user['screename']);

                UserActivity::login($id);
                return ($this->getUserData());
            } // user has clarin account but no inforex account
            else {
                return null;
            }
        }
        $this->redirectToClarinLogin();
    }
}

?>