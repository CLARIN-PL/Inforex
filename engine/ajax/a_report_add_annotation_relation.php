<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_add_annotation_relation extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $mdb2, $user, $db;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
			return;
		}

		$relation_type_id = intval($_POST['relation_type_id']);
		$source_id = intval($_POST['source_id']);
		$target_id = intval($_POST['target_id']);
		$user_id = intval($user['user_id']);
		
		$sql = "SELECT * FROM relations " .
				"WHERE relation_type_id={$relation_type_id} " .
				"AND source_id={$source_id} " .
				"AND target_id={$target_id} ";
		$result = $db->fetch_one($sql);
		if (count($result)==0){
			$sql = "INSERT INTO relations (relation_type_id, source_id, target_id, date, user_id) " .
					"VALUES ({$relation_type_id},{$source_id},{$target_id},now(),{$user_id})";
			$db->execute($sql);
			$relation_id = $mdb2->lastInsertID();
		}
		else {
			throw new Exception("Relacja w bazie już istnieje!");
		}
		$sql = "SELECT name FROM relation_types " .
				"WHERE id={$relation_type_id} ";		
		
		return array("relation_id"=>$relation_id, "relation_name"=>$db->fetch_one($sql));
	}
	
}
?>
