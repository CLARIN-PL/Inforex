<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ajax_annotation_shared_attribute_values extends CPagePublic {

	function execute(){
        $search = $_POST['search'];
        $page = $_POST['page'];
        $annotationId = intval($_POST['annotation_id']);
        $attributeId = intval($_POST['attribute_id']);

        header('Content-Type: application/json');

        $an = DbAnnotation::get($annotationId);

        $group1 = array();
        foreach ($this->getPossibleValues($annotationId, $attributeId) as $row){
            $group1[] = array("id"=>$row['value'], "text"=>$row['value'], "count"=>$row['vc']);
        }

        $group2 = array();
        foreach ($this->getAttributeValues($attributeId, $search) as $row){
            $group2[] = array("id"=>$row['value'], "text"=>$row['value'], "description"=>$row['description']);
        }

        $group3 = array();
        foreach ($this->getPossibleValuesByWords($attributeId, $an['text']) as $row){
            $group3[] = array("id"=>$row['value'], "text"=>$row['value'], "description"=>$row['description']);
        }

        $values = array();
        $values[] = array("text"=>"Annotations with the same text", "children"=>$group1);
        $values[] = array("text"=>"Values containing an annotation word", "children"=>$group3);
        $values[] = array("text"=>"Searched values", "children"=>$group2);

        $results = array("results"=>$values, "pagination"=> array( "more" => false));
        echo json_encode($results);
        die();
	}

	function getPossibleValues($annotationId, $attributeId){
	    global $db;
        $an = DbAnnotation::get($annotationId);
        $report = DbReport::get($an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ID]);
        $corpusId = $report[DB_COLUMN_REPORTS__CORPUS_ID];

        $builder = new SqlBuilder(DB_TABLE_REPORTS_ANNOTATIONS, "an");
        $builder->addSelectColumn(new SqlBuilderSelect("a.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", "vc"));
        $builder->addJoinTable(new SqlBuilderJoin("reports", "r", "an.report_id = r.id"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_shared_attributes", "a", "a.annotation_id = an.id AND a.shared_attribute_id = ?", array($attributeId)));
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));
        $builder->addWhere(new SqlBuilderWhere("SOUNDEX(an.text) = SOUNDEX(?)", array($an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT])));
        $builder->addWhere(new SqlBuilderWhere("value IS NOT NULL", array()));
        $builder->addWhere(new SqlBuilderWhere("value != ''", array()));
        $builder->addOrderBy("vc DESC, value ASC");
        $builder->addGroupBy("value");

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }

    function getPossibleValuesByWords($attributeId, $search){
        global $db;
        $builder = new SqlBuilder("shared_attributes_enum", "att");
        $builder->addSelectColumn(new SqlBuilderSelect("att.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("att.description", "description"));
        $or = array();
        foreach (explode(" ", strtolower($search)) as $word){
            $or[] = "value LIKE '%$word%'";
        }
        if (count($or)>0) {
            $builder->addWhere(new SqlBuilderWhere("(" . implode(" OR ", $or) . ")", array()));
        }
        $builder->addWhere(new SqlBuilderWhere("att.shared_attribute_id = ?", array($attributeId)));
        $builder->addOrderBy("value");

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }

    function getAttributeValues($attributeId, $search){
        global $db;
	    $builder = new SqlBuilder("shared_attributes_enum", "att");
	    $builder->addSelectColumn(new SqlBuilderSelect("att.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("att.description", "description"));
        $builder->addWhere(new SqlBuilderWhere("value LIKE '%$search%'", array()));
        $builder->addWhere(new SqlBuilderWhere("att.shared_attribute_id = ?", array($attributeId)));
        $builder->addOrderBy("value");

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }
}