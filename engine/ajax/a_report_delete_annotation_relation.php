<?php
/**
  metoda usuwajaca relacje
  a_report_add_relation (relation_type_id, source_id, target_id  [date, user_id]) 
  ->rel, src, targ isUnique
 * 
 */
class Ajax_report_delete_annotation_relation extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$relation_id = intval($_POST['relation_id']);
		
		$sql = "DELETE FROM relations " .
				"WHERE id={$relation_id}";
		db_execute($sql);
		echo json_encode(array("success"=>1));
	}
	
}
?>
