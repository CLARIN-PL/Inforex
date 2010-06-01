<?php
class Page_corpus extends CPage{

	var $isSecure = true;
	
	function execute(){		
		$this->set_roles();
		$this->set_owner();
	}
	
	/**
	 * Wczytaj i ustaw dane ról
	 */
	function set_roles(){
		global $corpus, $user;
		if (isset($user['role']['admin']) || $corpus['user_id']==$user['user_id']){				
			$roles = db_fetch_rows("SELECT *" .
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
			$this->set('users_roles', $users_roles);
			
			$corpus_roles = db_fetch_rows("SELECT * FROM corpus_roles");
			$this->set('corpus_roles', $corpus_roles);
			$this->set('corpus_roles_span', count($corpus_roles)+1);
		}		
	}
	
	/**
	 * Wczytaj i ustaw dane właściciela korpusu
	 */
	function set_owner(){
		global $corpus, $user;
		$owner = db_fetch("SELECT * FROM users WHERE user_id = {$corpus['user_id']}");
		$this->set('owner', $owner);
	}
}


?>
