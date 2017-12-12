<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_delete extends CPage {
	
	function checkPermission(){
		return true;
		if (hasRole('admin') || hasCorpusRole('annotate'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		ChromePhp::log($_POST);

		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		
		if ($element_type=="annotation_set"){
			$sql = "DELETE FROM annotation_sets WHERE annotation_set_id = ?";
			$db->execute($sql, array($element_id));
		}
		else if ($element_type=="annotation_subset"){
			$sql = "DELETE FROM annotation_subsets WHERE annotation_subset_id = ?";
            $db->execute($sql, array($element_id));
		}
		else if ($element_type=="annotation_type"){
			$element_id = $_POST['element_id'];
			$sql = "DELETE FROM annotation_types WHERE annotation_type_id = ?";
            $db->execute($sql, array($element_id));
		}
		return;
	}
	
}
?>
