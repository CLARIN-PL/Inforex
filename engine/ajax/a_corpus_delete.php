<?php
class Ajax_corpus_delete extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		
		if ($element_type=="corpus"){
			$sql = "DELETE FROM corpora WHERE id = ?";			
		}
		
		if ($element_type=="subcorpus"){
			$sql = "DELETE FROM corpus_subcorpora WHERE subcorpus_id = ?";
		}
		$db->execute($sql, array($element_id));
		echo json_encode(array("success"=>1));
	}	
}
?>
