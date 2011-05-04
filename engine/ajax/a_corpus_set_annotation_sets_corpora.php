<?php
class Ajax_corpus_set_annotation_sets_corpora extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasRole('corpus_owner'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}
		$corpus_id = intval($_POST['corpus_id']);
		$annotation_set_id = intval($_POST['annotation_set_id']);
		$operation_type = $_POST['operation_type'];

		if ($operation_type=="add")
			db_execute("INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES ($annotation_set_id, $corpus_id)");
		else if ($operation_type=="remove")
			db_execute("DELETE FROM annotation_sets_corpora WHERE annotation_set_id=$annotation_set_id AND corpus_id=$corpus_id"); 
		echo json_encode(array("success"=>1,"table"=>$_POST));
	}
	
}
?>
