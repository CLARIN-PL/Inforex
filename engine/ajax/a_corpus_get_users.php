<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_get_users extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}
		$corpusId = $_POST['corpus_id'];
		
		$sql = "SELECT *" .
					" FROM users_corpus_roles us " .
					" RIGHT JOIN users u ON (us.user_id=u.user_id AND us.corpus_id=?)" .
					" ORDER BY u.screename";
		$roles = $db->fetch_rows($sql,array($corpusId));					
		$users = array();
		foreach ($roles as $role){
			if($role['role']=="read"){
				$users[$role['user_id']]['role'] = $role['role'];
			}
			$users[$role['user_id']]['screename'] = $role['screename']; 
			$users[$role['user_id']]['user_id'] = $role['user_id']; 
		}				
		
		return $users;
	}
	
}
?>
