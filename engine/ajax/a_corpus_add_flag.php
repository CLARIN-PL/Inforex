<?php
class Ajax_corpus_add_flag extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user, $mdb2;

		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$corpus_id = $_POST['corpus_id'];
		$element_sort = $_POST['element_sort'];
		
		$sql = "INSERT INTO corpora_flags (corpora_id, name, short, sort) VALUES (?, ?, ?, ?)";
		$db->execute($sql, array($corpus_id, $name_str, $desc_str, $element_sort));
		
		$last_id = $mdb2->lastInsertID();
		echo json_encode(array("success"=>1, "last_id"=>$last_id));
	}
	
}
?>
