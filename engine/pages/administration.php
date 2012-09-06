<?php
class Page_administration extends CPage{

	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji korpusu";
	} 

	function execute(){		
		global $db, $user;		
		
		if($user){
			if (hasRole(USER_ROLE_ADMIN))
				$sql = "SELECT id, name FROM `corpora`";
			else
				$sql = "SELECT c.id, c.name FROM corpora c  WHERE c.user_id={$user['user_id']}";
			$this->set("corpusList", $db->fetch_rows($sql));
		}
	}
}


?>