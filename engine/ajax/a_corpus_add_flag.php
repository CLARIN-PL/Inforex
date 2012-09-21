<?php
class Ajax_corpus_add_flag extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $corpus, $mdb2;

		$sql = "INSERT INTO corpora_flags (corpora_id, name, short, sort) VALUES (?, ?, ?, ?)";
		ob_start();
		$db->execute($sql, array($corpus['id'], $_POST['name_str'], $_POST['desc_str'], $_POST['element_sort']));
		
		$error_buffer_content = ob_get_contents();
		ob_clean();
		if(strlen($error_buffer_content))
			echo json_encode(array("error"=> "Error: ". $error_buffer_content));
		else{
			$last_id = $mdb2->lastInsertID();
			echo json_encode(array("success"=>1, "last_id"=>$last_id));
		}
	}	
}
?>
