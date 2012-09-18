<?php
class Ajax_corpus_set_corpus_perspective_roles extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $db, $corpus;

		if ($_POST['operation_type'] == "add")
			$db->execute("INSERT INTO corpus_perspective_roles(report_perspective_id, corpus_id, user_id) VALUES (\"{$_POST['perspective_id']}\", {$corpus['id']}, \"{$_POST['user_id']}\")");
		else if ($_POST['operation_type'] == "remove")
			$db->execute("DELETE FROM corpus_perspective_roles WHERE report_perspective_id=\"{$_POST['perspective_id']}\" AND corpus_id={$corpus['id']} AND user_id={$_POST['user_id']}");
		
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
		else
			echo json_encode(array("success"=>1));
	}	
}
?>
