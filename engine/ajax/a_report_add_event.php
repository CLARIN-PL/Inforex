<?php
/**
  metoda dodajaca relacje
  a_report_add_relation (relation_type_id, source_id, target_id  [date, user_id]) 
  ->rel, src, targ isUnique
 * 
 */
class Ajax_report_add_event extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$report_id = intval($_POST['report_id']);
		$event_type_id = intval($_POST['type_id']);
		$user_id = intval($user['user_id']);
		
		$sql = "INSERT INTO reports_events (report_id, event_type_id, user_id, creation_time) " .
				"VALUES ($report_id, $event_type_id, $user_id, now())";
		db_execute($sql);
		$event_id = $mdb2->lastInsertID();
		/*$relation_type_id = intval($_POST['relation_type_id']);
		$source_id = intval($_POST['source_id']);
		$target_id = intval($_POST['target_id']);
		$user_id = intval($user['user_id']);
		
		$sql = "SELECT * FROM relations " .
				"WHERE relation_type_id={$relation_type_id} " .
				"AND source_id={$source_id} " .
				"AND target_id={$target_id} ";
		$result = db_fetch_one($sql);
		if (count($result)==0){
			$sql = "INSERT INTO relations (relation_type_id, source_id, target_id, date, user_id) " .
					"VALUES ({$relation_type_id},{$source_id},{$target_id},now(),{$user_id})";
			db_execute($sql);
		}
		else {
			echo json_encode(array("error"=>"Relacja w bazie już istnieje!"));
		}*/
		echo json_encode(array("success"=>1, "event_id"=>$event_id));
	}
	
}
?>
