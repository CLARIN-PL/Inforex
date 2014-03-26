<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_get extends CPage {
	
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
		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="annotation_set"){
			$sql = "SELECT annotation_subset_id AS id, description " .
					" FROM annotation_subsets " .
					" WHERE annotation_set_id={$parent_id}" .
					" ORDER BY description";
			$result = db_fetch_rows($sql);
			$sql = "SELECT id, name, description " .
					"FROM corpora " .
					"WHERE id IN " .
						"(SELECT corpus_id " .
						"FROM annotation_sets_corpora " .
						"WHERE annotation_set_id=$parent_id)";
			array_push($result, db_fetch_rows($sql));
			$sql = "SELECT id, name, description " .
					"FROM corpora " .
					"WHERE id NOT IN " .
						"(SELECT corpus_id " .
						"FROM annotation_sets_corpora " .
						"WHERE annotation_set_id=$parent_id)";
			array_push($result, db_fetch_rows($sql));
			
		} 
		else if ($parent_type=="annotation_subset"){
			$sql = "SELECT name, short_description AS short, description, css" .
					" FROM annotation_types" .
					" WHERE annotation_subset_id={$parent_id}" .
					" ORDER BY name";
			$result = db_fetch_rows($sql);
		}
				
		return $result;
	}
	
}
?>
