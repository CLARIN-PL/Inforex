<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_corpus_users_update extends CAction{
		
	function checkPermission(){
		global $user, $corpus;
		if (!isset($user['role']['admin']) && $corpus['user_id']!=$user['user_id'])
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
		else
			return true;
	} 
	
	function execute(){
		global $corpus, $db;
		
		$users = $_POST['active_users'];

		$db->execute("DELETE FROM users_corpus_roles WHERE corpus_id = {$corpus['id']} AND role='read'");
		foreach ((array) $users as $user){
			$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($user, $corpus['id'], 'read'));
		}
		$db->execute("DELETE FROM users_corpus_roles WHERE corpus_id = {$corpus['id']} " . (count($users) ? ("AND user_id NOT IN ('".implode("','",$users)."')") : "" ));
		$db->execute("DELETE FROM corpus_perspective_roles WHERE corpus_id = {$corpus['id']} " . (count($users) ? ("AND user_id NOT IN ('".implode("','",$users)."')") : "" ));
		
		$this->set("action_performed", "Zmiany ustawień zostały zapisane");
		return null;
	}	
} 

?>
