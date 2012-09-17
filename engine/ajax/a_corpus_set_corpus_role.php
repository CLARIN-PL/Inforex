<?php

class Ajax_corpus_set_corpus_role extends CPage {		
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;			
		else
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
	} 
	
	function execute(){
		global $corpus, $db;
		
		if ($_POST['operation_type'] == "add")
			$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($_POST['user_id'], $corpus['id'], $_POST['role']));
		else if ($_POST['operation_type'] == "remove")	
			$db->execute("DELETE FROM users_corpus_roles WHERE corpus_id={$corpus['id']} AND user_id={$_POST['user_id']} AND role=\"{$_POST['role']}\" ");
			
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
		else
			echo json_encode(array("success"=>1));		
	}	
} 

?>
