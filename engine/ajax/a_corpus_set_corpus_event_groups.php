<?php
class Ajax_corpus_set_corpus_event_groups extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $corpus;

		if ($_POST['operation_type']=="add")
			$db->execute("INSERT INTO corpus_event_groups(event_group_id, corpus_id) VALUES ({$_POST['event_group_id']}, {$corpus['id']})");
		else if ($_POST['operation_type']=="remove")
			$db->execute("DELETE FROM corpus_event_groups WHERE event_group_id={$_POST['event_group_id']} AND corpus_id={$corpus['id']}");
		
		$error = $db->mdb2->errorInfo();
		if(isset($error[0]))
			echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
		else
			echo json_encode(array("success"=>1));
	}
	
}
?>
