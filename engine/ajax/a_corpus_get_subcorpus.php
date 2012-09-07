<?php
class Ajax_corpus_get_subcorpus extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$corpusId = $_POST['corpus_id'];
		
		$sql = "SELECT subcorpus_id AS id, name, description FROM corpus_subcorpora WHERE corpus_id=?";
		
		echo json_encode($db->fetch_rows($sql,array($corpusId)));
	}
	
}
?>
