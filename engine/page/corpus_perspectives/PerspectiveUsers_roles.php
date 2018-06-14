<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveUsers_roles extends CCorpusPerspective {
	
	function execute()
	{
		$this->set_users_roles();
		$this->set_corpus_roles();
	}
	
	function set_users_roles(){
		global $corpus, $db;
		$roles = $db->fetch_rows("SELECT *" .
				" FROM users_corpus_roles us " .
				" RIGHT JOIN users u ON (us.user_id=u.user_id AND us.corpus_id={$corpus['id']})" .
				" WHERE u.user_id != {$corpus['user_id']}" .
				" ORDER BY u.screename");

		$users_roles = array();
		foreach ($roles as $role){
			$users_roles[$role['user_id']]['role'][] = $role['role'];
			$users_roles[$role['user_id']]['screename'] = $role['screename']; 
			$users_roles[$role['user_id']]['user_id'] = $role['user_id']; 
		}
		foreach($users_roles as $key => $u_roles){
			if(!in_array(CORPUS_ROLE_READ,$u_roles['role']))
				unset($users_roles[$key]);
		}
		$this->page->set('users_roles', $users_roles);
	}
	
	function set_corpus_roles(){
		global $db;
		$corpus_roles = $db->fetch_rows("SELECT * FROM corpus_roles WHERE role != 'read'");
		$this->page->set('corpus_roles', $corpus_roles);
	}
}
?>
