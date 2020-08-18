<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_get_annotation_attributes extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }

    function execute(){
		$annotation_id = intval($_POST['annotation_id']);
		
		if ($annotation_id<=0){
			throw new Exception("No identifier of annotation found");
		}
			
		$rows_attributes = $this->getDb()->fetch_rows("SELECT ta.*, v.value" .
				" FROM reports_annotations_optimized a" .
				" JOIN annotation_types_attributes ta ON (ta.annotation_type_id=a.type_id)" .
				" LEFT JOIN reports_annotations_attributes v ON (v.annotation_id=a.id AND v.annotation_attribute_id=ta.id)" .
				" WHERE a.id = $annotation_id");
		
		$attributes = array();
		foreach ($rows_attributes as $r){
			$attr = $r;
			$rows_values = $this->getDb()->fetch_rows("SELECT * FROM annotation_types_attributes_enum WHERE annotation_type_attribute_id=".intval($r['id']));
			$values = array();
			foreach ($rows_values as $v)
				$values[] = array("value"=>$v['value'], "description"=>$v['description']);
			$attr['values'] = $values;
			$attributes[] = $attr;
		}
		
		return $attributes;
	}
	
}
