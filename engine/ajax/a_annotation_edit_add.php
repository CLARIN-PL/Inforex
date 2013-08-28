<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_add extends CPage {
	
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
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		$element_type = $_POST['element_type'];
		$parent_id = intval($_POST['parent_id']);
		
		
		if ($element_type=="annotation_set"){
			$sql = 'INSERT INTO annotation_sets (description) VALUES ("'.$desc_str.'")';
		}
		else if ($element_type=="annotation_subset"){
			$sql = 'INSERT INTO annotation_subsets (description, annotation_set_id) VALUES ("'.$desc_str.'", "'.$parent_id.'")';
		}
		else if ($element_type=="annotation_type"){
			$group_id = $_POST['set_id'];
			$level = 0;
			$short_description = $_POST['short'];
			$css = $_POST['css'];
			$sql = 'INSERT INTO annotation_types (name,  description,annotation_subset_id, group_id, level, short_description, css) VALUES ("'.$name_str.'", "'.$desc_str.'", "'.$parent_id.'", "'.$group_id.'", "'.$level.'", "'.$short_description.'", "'.$css.'")';
		}
				
		db_execute($sql);
		$last_id = $mdb2->lastInsertID();
		return array("last_id"=>$last_id);
	}
	
}
?>
