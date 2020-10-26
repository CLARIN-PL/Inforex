<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ajax_annotation_shared_attribute_values extends CPagePublic {

	function execute(){
        $search = $this->getRequestParameter('search', '');
        $annotationId = $this->getRequestParameterRequired('annotation_id');
        $attributeId = $this->getRequestParameterRequired('attribute_id');

        $an = DbAnnotation::get($annotationId);

        $values[] = array(
            "text" => "Values for other annotations with similar phrase",
            "children" => $this->collectPossibleValues($annotationId, $attributeId));

        $values[] = array(
            "text" => "Values similar to the annotation phrase",
            "children" => $this->collectPossibleValuesByWords($attributeId, $an['text']));

        $values[] = array(
            "text" => "Values matched to the search phrase",
            "children" => $this->collectAttributeValues($attributeId, $search));

        $values = $this->getNotEmptyGroups($values);
        $results = array("results"=>$values, "pagination"=> array( "more" => false));

        header('Content-Type: application/json');
        echo json_encode($results);
        die();
	}

	function getNotEmptyGroups($groups){
        $notempty = array();
        foreach ($groups as $value){
            if (count($value['children'])>0){
                $notempty[] = $value;
            }
        }
        return $notempty;
    }

	function collectPossibleValues($annotationId, $attributeId){
        $group1 = array();
        foreach ($this->getPossibleValues($annotationId, $attributeId) as $row){
            $group1[] = array("id"=>$row['value'], "text"=>$row['value'], "count"=>$row['vc']);
        }
        return $group1;
    }

    function collectPossibleValuesByWords($attributeId, $text){
        $group3 = array();
        foreach ($this->getPossibleValuesByWords($attributeId, $text) as $row){
            $group3[] = array("id"=>$row['value'], "text"=>$row['value'], "description"=>$row['description']);
        }
        return $group3;
    }

    function collectAttributeValues($attributeId, $search){
        $group2 = array();
        foreach ($this->getAttributeValues($attributeId, $search) as $row){
            $group2[] = array("id"=>$row['value'], "text"=>$row['value'], "description"=>$row['description']);
        }
        return $group2;
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
        $or = $this->getSqlWhereConditionForPhrases($search);
        if (count($or)>0) {
            $builder->addWhere(new SqlBuilderWhere("(" . implode(" OR ", $or) . ")", array()));
        } else {
            return array();
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
        $or = $this->getSqlWhereConditionForPhrases($search);
        if (count($or)>0) {
            $builder->addWhere(new SqlBuilderWhere("(" . implode(" OR ", $or) . ")", array()));
        }
        $builder->addWhere(new SqlBuilderWhere("att.shared_attribute_id = ?", array($attributeId)));
        $builder->addOrderBy("value");

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }

    function getSqlWhereConditionForPhrases($phrase){
        $or = array();
        $keywords = preg_split("/[ -]+/u", $phrase);
        foreach ($keywords as $word){
            if ( strlen($word) > 3 ) {
                $or[] = "value LIKE '%" . $this->getDb()->escape_string($word) . "%'";
            }
        }
        return $or;
    }
}
