<?php
class Ajax_corpus_set_annotation_sets_corpora extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $db, $corpus;

		if ($_POST['operation_type']=="add")
			$db->execute("INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES ({$_POST['annotation_set_id']}, {$corpus['id']})");
		else if ($_POST['operation_type']=="remove")
			$db->execute("DELETE FROM annotation_sets_corpora WHERE annotation_set_id={$_POST['annotation_set_id']} AND corpus_id={$corpus['id']}"); 
		
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
		else
			echo json_encode(array("success"=>1));
	}	
}
?>
