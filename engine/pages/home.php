<?php
class Page_home extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $user;
		
		$user_id = intval($user[user_id]);
		
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`" .
				" FROM corpora c" .
				" LEFT JOIN reports r ON (c.id = r.corpora)" .
				" LEFT JOIN users_corpus_roles cr ON (c.id=cr.corpus_id AND cr.user_id=? AND role='". CORPUS_ROLE_READ ."')" .
				" WHERE c.public = 1" .
				"    OR c.user_id = ?" .
				"    OR cr.user_id = ?" .
				"    OR 1=?" .
				" GROUP BY c.id" .
				" ORDER BY c.name";
		$corpora = db_fetch_rows($sql, array($user_id, $user_id, $user_id, intval(hasRole(USER_ROLE_ADMIN))) );
		$this->set('corpus_set', $corpora);
	}
}


?>
