<?php

class DbUser{
	
	static function addCorpusRoles($user_id, $corpus_roles = array()){
		
		foreach($corpus_roles as $corpus_id => $roles){
			foreach($roles as $role){
				DbUser::addSingleCorpusRole($user_id, $corpus_id, $role);
			}
		}
		
	}
	
	static function addSingleCorpusRole($user_id, $corpus_id, $role){
		global $db;
		$sql = "INSERT INTO users_corpus_roles(user_id, corpus_id, role) values (?,?,?);";
		$db->execute($sql, array($user_id, $corpus_id, $role));
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
	}
	
	/**
	 * Zwraca tablicę z danymi użytkownika z tabeli users.
	 */
	static function get($user_id){
		global $db;
		return $db->fetch("SELECT * FROM users WHERE user_id = ?", $user_id);
	}

	static function getByClarinLogin($clarinLogin){
	    global $db;
        return $db->fetch("SELECT * FROM users WHERE clarin_login LIKE ?", $clarinLogin);
    }

    static function updateClarinUser($id, $clarin_login){
        global $db;
        $sql = "UPDATE users SET `clarin_login` = ? WHERE user_id = ?";
        $db->execute($sql, array($clarin_login, $id));

        $error = $db->mdb2->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }

    static function createNewUser($login, $screename, $email, $password='None', $clarin_login=null){
        global $db;
        $sql = "INSERT INTO users ( login, screename, email, password, clarin_login ) ".
          "VALUES (?,?,?,?,?)";
        $db->execute($sql,array($login, $screename, $email, $password, $clarin_login));

        $error = $db->mdb2->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }
}

?>