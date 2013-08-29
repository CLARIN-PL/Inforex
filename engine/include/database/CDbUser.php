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
	
	
}

?>