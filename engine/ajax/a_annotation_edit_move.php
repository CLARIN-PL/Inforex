<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_move extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}
		$set_id = intval($_POST['set_id']);
		$corpora_id = intval($_POST['corpora_id']);
		$move_type = $_POST['move_type'];
		
		if ($move_type=="assign"){
			$sql="INSERT INTO annotation_sets_corpora (annotation_set_id, corpus_id) VALUES ($set_id, $corpora_id)";
		}
		else if ($move_type=="unassign"){
			$sql="DELETE FROM annotation_sets_corpora WHERE annotation_set_id=$set_id AND corpus_id=$corpora_id";
		}
		db_execute($sql);				
		return;
	}
	
}
?>
