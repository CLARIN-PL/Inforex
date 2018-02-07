<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_get_annotation_relation_types extends CPage {
		
	function execute(){

	    global $corpus;

		/*if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}*/

        $relationSetIds = CookieManager::getRelationSets($corpus['id']);

        //If there are no relation sets selected, return an empty array.
        if(empty($relationSetIds)){
            return array();
        } else {
            $annotation_id = intval($_POST['annotation_id']);
            $sql = "SELECT rt.id, rt.name, rt.description, rs.name AS set_name  FROM relation_types rt " .
                  " JOIN relation_sets rs USING (relation_set_id)" .
                  " JOIN corpora_relations cr ON cr.relation_set_id = rs.relation_set_id AND cr.corpus_id = ? " .
                  " WHERE (rt.id IN (" .
                         "SELECT relation_type_id " .
                         "FROM relations_groups " .
                         "WHERE part='source' " .
                              "AND (" .
                              "annotation_set_id=(" .
                                   "SELECT group_id " .
                                   "FROM annotation_types " .
                                   "WHERE annotation_type_id=(" .
                                         "SELECT type_id " .
                                         "FROM reports_annotations " .
                                         "WHERE id=?" .
                                   ")" .
                              ") " .
                              "OR " .
                              "annotation_subset_id=(" .
                                   "SELECT annotation_subset_id " .
                                   "FROM annotation_types " .
                                   "WHERE annotation_type_id=(" .
                                        "SELECT type_id " .
                                        "FROM reports_annotations " .
                                        "WHERE id=?" .
                                   ")" .
                              ") " .
                         ") " .
                ") AND rs.relation_set_id IN(".implode(",", array_fill(0, count($relationSetIds), "?")).") )
                ORDER BY rs.name, name";
        }

        $params =  array($corpus['id'], $annotation_id, $annotation_id);
        $params = array_merge($params, $relationSetIds);

		$result = db_fetch_rows($sql, $params);
		ChromePhp::log($result);
		return $result;
	}
	
}
?>
