<?php
class Page_corpus extends CPage{

	var $isSecure = true;
	var $roles = array("corpus_owner");
	
	function execute(){		
		$this->set_roles();
		$this->set_perspectives();
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
			foreach($users_roles as $key => $u_roles){
				if(!in_array("read",$u_roles['role']))
					unset($users_roles[$key]);
			}
			$this->set('users_roles', $users_roles);
			
			$corpus_roles = db_fetch_rows("SELECT * FROM corpus_roles");
			foreach($corpus_roles as $key => $c_role){
				if($c_role['role']== "read")
					unset($corpus_roles[$key]);
			}
			$this->set('corpus_roles', $corpus_roles);
			$this->set('corpus_roles_span', count($corpus_roles)+1);
		}		
	}
	
	/**
	 * Wczytaj i ustaw dane perspektyw
	 */
	function set_perspectives(){
		global $corpus, $user, $db;
		if (isset($user['role']['admin']) || $corpus['user_id']==$user['user_id']){				
/*			$sql = "SELECT rp.id, " .
					" carp.access, " .
					" rp.title, " .
					" cpr.user_id, " .
					" cpr.corpus_id AS cid " .
					" FROM report_perspectives rp " .												
					" LEFT JOIN corpus_perspective_roles cpr " .
						" ON rp.id=cpr.report_perspective_id " .
						" AND cpr.corpus_id=" . $corpus['id'] .
					" LEFT JOIN corpus_and_report_perspectives carp ". 
						" ON carp.perspective_id=rp.id " . 
						" AND carp.corpus_id=" . $corpus['id'] . " ";		
*/			
			$sql = "SELECT * " .
					" FROM report_perspectives rp " .
					" RIGHT JOIN corpus_and_report_perspectives carp " .
						" ON rp.id=carp.perspective_id " .
					" LEFT JOIN corpus_perspective_roles cpr " .
						" ON rp.id=cpr.report_perspective_id " .
					" WHERE cpr.corpus_id=" . $corpus['id'] .
						" AND carp.corpus_id=" . $corpus['id'] ;
			$rows = $db->fetch_rows($sql);
			
			$corpus_perspectivs = array();
			$users_perspectives = array();
			foreach ($rows as $row){
				$users_perspectives[$row['user_id']][] = $row['id'];
				$corpus_perspectivs[$row['id']]['title'] = $row['title'];
				$corpus_perspectivs[$row['id']]['access'] = $row['access'];				 
			}
			
//			print_r($users_perspectives);
/*			foreach($users_roles as $key => $u_roles){
				if(!in_array("read",$u_roles['role']))
					unset($users_roles[$key]);
			}
			$this->set('users_roles', $users_roles);
			
			$corpus_roles = db_fetch_rows("SELECT * FROM corpus_roles");
			foreach($corpus_roles as $key => $c_role){
				if($c_role['role']== "read")
					unset($corpus_roles[$key]);
			}
			$this->set('corpus_rs', $corpus_roles);
			$this->set('corpus_roles_span', count($corpus_roles)+1);
	*/	
			$this->set('corpus_perspectivs', $corpus_perspectivs);
			$this->set('users_perspectives', $users_perspectives);
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
