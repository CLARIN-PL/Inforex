<?php
class Ajax_corpus_set_corpus_and_report_perspectives extends CPage {
	
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
		$perspective_id = $_POST['perspective_id'];
		$access = $_POST['access'];
		$operation_type = $_POST['operation_type'];

		if ($operation_type=="add")
			db_execute("INSERT INTO corpus_and_report_perspectives(perspective_id, corpus_id, access) VALUES (\"$perspective_id\", $corpus_id, \"$access\")");
		else if ($operation_type=="remove"){
			db_execute("DELETE FROM corpus_and_report_perspectives WHERE perspective_id=\"$perspective_id\" AND corpus_id=$corpus_id");
			db_execute("DELETE FROM corpus_perspective_roles WHERE report_perspective_id=\"$perspective_id\" AND corpus_id=$corpus_id");
		}
		else if ($operation_type=="update")
			db_execute("UPDATE corpus_and_report_perspectives SET access=\"$access\" WHERE perspective_id=\"$perspective_id\" AND corpus_id=$corpus_id");
		echo json_encode(array("success"=>1));
	}
	
}
?>
