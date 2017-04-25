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
        $setVisibility = $_POST['setAccess_str'];
		$element_type = $_POST['element_type'];
		$parent_id = intval($_POST['parent_id']);
		$user_id = $user['user_id'];
		$username = $user['screename'];
        $custom_annotation = $_POST['customAnnotation'];
        $corpus = $_POST['corpus'];
		
		if ($element_type=="annotation_set"){
			$sql = 'INSERT INTO annotation_sets (description, public, user_id) VALUES ("'.$desc_str.'", "'.$setVisibility.'", "'.$user_id.'");';
		}
		else if ($element_type=="annotation_subset"){
			$sql = 'INSERT INTO annotation_subsets (description, annotation_set_id) VALUES ("'.$desc_str.'", "'.$parent_id.'")';
		}
		else if ($element_type=="annotation_type"){
			$group_id = $_POST['set_id'];
			$level = 0;
			$short_description = $_POST['short'];
            $shortlist = ($_POST['visibility'] == 'Hidden' ? 1 : 0);
			$css = $_POST['css'];
			$sql = 'INSERT INTO annotation_types (name,  description,annotation_subset_id, group_id, level, short_description, css, shortlist) VALUES ("'.$name_str.'", "'.$desc_str.'", "'.$parent_id.'", "'.$group_id.'", "'.$level.'", "'.$short_description.'", "'.$css.'", "'.$shortlist.'")';
		}
				
		db_execute($sql);
		$last_id = $mdb2->lastInsertID();

		//Assign annotation set to corpora if called from corpus settings -> custom annotation sets.
        if($custom_annotation != null){
            $sql = "INSERT INTO annotation_sets_corpora(annotation_set_id, corpus_id) VALUES ({$last_id}, {$corpus});";
            db_execute($sql);
        }

		return array("last_id"=>$last_id, "user" => $username);
	}
	
}
?>
