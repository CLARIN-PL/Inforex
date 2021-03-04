<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_annotation_types extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }
		
	function execute(){
		global $user;
		
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
		$result = $this->getDb()->fetchAll($sql);
		
		return $result;
	}
	
}
