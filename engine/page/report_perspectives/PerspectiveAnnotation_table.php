<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotation_table extends CPerspective {

    function execute(){
        global $db;
		$report = $this->page->report;
    	//$anns = DbAnnotation::getReportAnnotations($report[DB_COLUMN_REPORTS__REPORT_ID]);

    	$builder = new SqlBuilder(DB_TABLE_REPORTS_ANNOTATIONS, "an");
    	$builder->addSelectColumn(new SqlBuilderSelect("an.text", "text"));
        $builder->addSelectColumn(new SqlBuilderSelect("lm.lemma", "lemma"));
        $builder->addSelectColumn(new SqlBuilderSelect("at.name", "type"));
        $builder->addSelectColumn(new SqlBuilderSelect("sa.value", "eid"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_lemma", "lm", "an.id = lm.report_annotation_id"));
        $builder->addJoinTable(new SqlBuilderJoin("annotation_types", "at", "an.type_id = at.annotation_type_id"));
        $builder->addJoinTable(
            new SqlBuilderJoin("reports_annotations_shared_attributes", "sa", "sa.annotation_id = an.id"));
        $builder->addWhere(new SqlBuilderWhere("an.report_id = ?", array($report[DB_COLUMN_REPORTS__REPORT_ID])));
        list($sql, $params) = $builder->getSql();
        $anns = $db->fetch_rows($sql, $params);

    	$anns = $this->groupAnnotations($anns);
        $this->page->set("anns", $anns);
	}

	function groupAnnotations($anns){
        $groups = array();
        foreach ($anns as $an){
            $key = sprintf("%s_%s_%s_%s", $an['text'], $an['lemma'], $an['type'], $an['eid']);
            $groups[$key] = $an;
        }
        ksort($groups);
        return array_values($groups);
    }

}
