<?php
class Page_administration extends CPage{

	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji korpusu";
	} 

	function execute(){		
		global $db, $user, $corpus;
		
		if($user){
			if (hasRole(USER_ROLE_ADMIN))
				$sql = "SELECT id, name FROM `corpora`";
			elseif(isCorpusOwner())
				$sql = "SELECT c.id, c.name FROM corpora c  WHERE c.user_id={$user['user_id']}";
			if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
				$this->set("corpusList", $db->fetch_rows($sql));
			if(hasCorpusRole(CORPUS_ROLE_MANAGER) && !isCorpusOwner()){
				$sql = "SELECT subcorpus_id AS id, name, description FROM corpus_subcorpora WHERE corpus_id=?";
				$this->set("subcorpusList", $db->fetch_rows($sql, array($corpus['id'])));
				$sql = "SELECT corpora_flag_id AS id, name, short, sort FROM corpora_flags WHERE corpora_id=?";
				$this->set("flagsList", $db->fetch_rows($sql, array($corpus['id'])));
			}
		}
	}
}


?>