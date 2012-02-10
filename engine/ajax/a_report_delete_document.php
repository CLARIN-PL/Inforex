<?php
/**
  metoda usuwajaca dokument
 * 
 */
class Ajax_report_delete_document extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('delete_documents') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$report_id = intval($_POST['report_id']);
		
		$sql = "DELETE FROM reports WHERE id={$report_id}";				
		$db->execute($sql);
		echo json_encode(array("success"=>1));
	}	
}
?>
