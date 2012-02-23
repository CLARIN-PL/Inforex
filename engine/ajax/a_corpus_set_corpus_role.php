<?php

class Ajax_corpus_set_corpus_role extends CPage {		
	function checkPermission(){
		if (hasRole('admin') || isCorpusOwner())
			return true;			
		else
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
	} 
	
	function execute(){
		global $corpus, $db, $user;
		
		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}
		
		$role = $_POST['role'];
		$user_id = $_POST['user_id'];
		$operation_type = $_POST['operation_type'];
		$corpus_id = intval($_POST['corpus_id']);
		
		if ($operation_type=="add")
			$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($user_id, $corpus_id, $role));
		else if ($operation_type=="remove")	
			$db->execute("DELETE FROM users_corpus_roles WHERE corpus_id={$corpus_id} AND user_id={$user_id} AND role=\"$role\" ");
			
		echo json_encode(array("success"=>1));		
	}	
} 

?>
