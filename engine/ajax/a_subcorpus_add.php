<?php
class Ajax_subcorpus_add extends CPage {
	
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
		
		$element_type = $_POST['element_type'];
		$corpus_id = $_POST['corpus_id'];
		
		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";
		
		
		$db->execute($sql, array($corpus_id, $name_str, $desc_str));
		$last_id = $mdb2->lastInsertID();
		echo json_encode(array("success"=>1, "last_id"=>$last_id));
	}
	
}
?>
