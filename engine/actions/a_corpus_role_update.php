<?php

class Action_corpus_role_update extends CAction{
		
	function checkPermission(){
		global $user, $corpus;
		if (!isset($user['role']['admin']) && $corpus['user_id']!=$user['user_id'])
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
		else
			return true;
	} 
	
	function execute(){
		global $corpus, $user;
		//$this->set("action_error", "Brak identyfikatora");
		
		$users_roles = $_POST['role'];
		db_execute("DELETE FROM users_corpus_roles WHERE corpus_id = {$corpus['id']}");
		foreach ($users_roles as $user_id=>$roles){
			foreach ($roles as $role=>$desc)
				db_execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($user_id, $corpus['id'], $role));
		}
		
		$this->set("action_performed", "Zmiany ustawień zostały zapisane");
		return null;
	}
	
} 

?>
