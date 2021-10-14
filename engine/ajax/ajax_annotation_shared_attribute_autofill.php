<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ajax_annotation_shared_attribute_autofill extends CPagePublic {

	function execute(){
		//$attributes = $this->getRequestParameterRequired("attributes");
		// returns empty array if ajax doesn't sent attributes table
		// in POST ( no rows in annotation lemma window )
		$attributes = $this->getRequestParameter("attributes",array());
		

        foreach ($attributes as &$attribute){
            $attribute["value"] = $this->getBestValue($attribute["annotation_id"], $attribute["attribute_id"]);
        }

        return $attributes;
	}

	function getBestValue($annotationId, $attributeId){
        $orthMatch = $this->getExactMatchValues($annotationId, $attributeId);
        if ( $orthMatch ){
            return $orthMatch["value"];
        }

        $wordMatch = $this->getPossibleValuesByWords($annotationId, $attributeId);
        if ( $wordMatch ){
            return $wordMatch["value"];
        }

        return "";
    }

    private function getExactMatchValues($annotationId, $attributeId){
        
        $an = DbAnnotation::get($annotationId);
        $report = DbReport::get($an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ID]);
        $corpusId = $report[DB_COLUMN_REPORTS__CORPUS_ID];

        $builder = new SqlBuilder(DB_TABLE_REPORTS_ANNOTATIONS, "an");
        $builder->addSelectColumn(new SqlBuilderSelect("a.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", "vc"));
        $builder->addJoinTable(new SqlBuilderJoin("reports", "r", "an.report_id = r.id"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_shared_attributes", "a", "a.annotation_id = an.id AND a.shared_attribute_id = ?", array($attributeId)));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_lemma", "ral", "an.id = ral.report_annotation_id"), array());
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));
        $builder->addWhere(new SqlBuilderWhere("(LOWER(an.text) = LOWER(?) OR LOWER(ral.lemma) = LOWER(?))",
            array($an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT], $an[DB_COLUMN_REPORTS_ANNOTATIONS__LEMMA])));
        $builder->addWhere(new SqlBuilderWhere("value IS NOT NULL", array()));
        $builder->addWhere(new SqlBuilderWhere("value != ''", array()));
        $builder->addOrderBy("vc DESC, value ASC");
        $builder->addGroupBy("value");

        list($sql, $params) = $builder->getSql();
        return $this->getDb()->fetch($sql, $params);
    }

    function getPossibleValuesByWords($annotationId, $attributeId){

        $an = DbAnnotation::get($annotationId);

        $builder = new SqlBuilder("shared_attributes_enum", "att");
        $builder->addSelectColumn(new SqlBuilderSelect("att.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("att.description", "description"));
        $builder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", "vc"));
        $or = array();
        foreach (explode(" ", strtolower($an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT])) as $word){
            if ( strlen($word) > 4 ) {
                $or[] = "value LIKE '%" . $this->getDb()->escape($word) . "%'";
            }
        }
        if (count($or)>0) {
            $builder->addWhere(new SqlBuilderWhere("(" . implode(" OR ", $or) . ")", array()));
        } else {
            return array();
        }
        $builder->addWhere(new SqlBuilderWhere("att.shared_attribute_id = ?", array($attributeId)));
        $builder->addOrderBy("value");
        $builder->addOrderBy("vc DESC, value ASC");

        list($sql, $params) = $builder->getSql();
        return $this->getDb()->fetch($sql, $params);
    }

}
