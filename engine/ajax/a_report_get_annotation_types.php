<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_annotation_types extends CPage {
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	function execute(){
		global $mdb2, $user;
		
		$annotation_id = intval($_POST['annotation_id']);
		$relation_type_id = intval($_POST['relation_type_id']);
		
		$sql =  "SELECT DISTINCT name " .
				"FROM annotation_types " .
				/*"WHERE group_id=(" .
					"SELECT group_id " .
					"FROM annotation_types " .
					"WHERE name=(" .
						"SELECT type " .
						"FROM reports_annotations " .
						"WHERE id={$annotation_id}" .
					")" .
				") " .*/
				"WHERE group_id IN (". 
					"SELECT annotation_set_id " .
					"FROM relations_groups " .
					"WHERE part='target' " .
					"AND relation_type_id=$relation_type_id" .
				") " .
				"OR annotation_subset_id IN (". 
					"SELECT annotation_subset_id " .
					"FROM relations_groups " .
					"WHERE part='target' " .
					"AND relation_type_id=$relation_type_id" .
				") ";
		$result = $mdb2->query($sql)->fetchAll();
		
		return $result;
	}
	
}
?>
