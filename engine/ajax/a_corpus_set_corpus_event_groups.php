<?php
class Ajax_corpus_set_corpus_event_groups extends CPage {
	
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
		$event_group_id = intval($_POST['event_group_id']);
		$operation_type = $_POST['operation_type'];

		if ($operation_type=="add")
			db_execute("INSERT INTO corpus_event_groups(event_group_id, corpus_id) VALUES ($event_group_id, $corpus_id)");
		else if ($operation_type=="remove")
			db_execute("DELETE FROM corpus_event_groups WHERE event_group_id=$event_group_id AND corpus_id=$corpus_id");
		echo json_encode(array("success"=>1));
	}
	
}
?>
