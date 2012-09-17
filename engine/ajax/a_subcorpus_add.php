<?php
class Ajax_subcorpus_add extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $corpus, $mdb2;

		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";
		
		$db->execute($sql, array($corpus['id'], $_POST['name_str'], $_POST['desc_str']));
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
		else{
			$last_id = $mdb2->lastInsertID();
			echo json_encode(array("success"=>1, "last_id"=>$last_id));
		}
	}
	
}
?>
