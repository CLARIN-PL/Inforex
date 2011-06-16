<?php
class Page_home extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $user;
		
		$user_id = intval($user[id]);
		
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`" .
				" FROM corpora c" .
				" JOIN reports r ON (c.id = r.corpora)" .
				" LEFT JOIN users_corpus_roles cr ON (c.id=cr.corpus_id AND c.user_id=? AND role='read')" .
				" WHERE c.public = 1" .
				"    OR c.user_id = ?" .
				"    OR 1=?" .
				" GROUP BY c.id";
		$corpora = db_fetch_rows($sql, array($user_id, $user_id, intval(hasRole("admin"))) );
		$this->set('corpus_set', $corpora);
	}
}


?>
