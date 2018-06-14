<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_annotation_edit_get extends CPagePublic {

    function execute(){

		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="annotation_set"){
			$sql = "SELECT annotation_subset_id AS id, name, description " .
					" FROM annotation_subsets " .
					" WHERE annotation_set_id={$parent_id}" .
					" ORDER BY name";
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
			$sql = "SELECT at.annotation_type_id as id, at.name, at.short_description AS short, at.description, count(ra.id) as number_used, at.css, at.shortlist" .
					" FROM annotation_types at" .
                    " LEFT JOIN reports_annotations ra ON ra.type_id = at.annotation_type_id " .
					" WHERE at.annotation_subset_id={$parent_id}" .
                    " GROUP BY at.annotation_type_id" .
					" ORDER BY at.name";
			$result = db_fetch_rows($sql);
		}
				
		return $result;
	}
	
}