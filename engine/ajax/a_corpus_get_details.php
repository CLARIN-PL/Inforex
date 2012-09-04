<?php
class Ajax_corpus_get_details extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db;

		$corpusId = $_POST['corpus_id'];
		
		$sql = "SELECT name, description, public, ext, screename FROM corpora c LEFT JOIN users u ON (c.user_id=u.user_id) WHERE c.id=?";
		echo json_encode($db->fetch_rows($sql,array($corpusId)));
	}
	
}
?>
