<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveAnnotation_table extends CPerspective {

    function execute(){
		$report = $this->page->report;
    	$anns = DbAnnotation::getReportAnnotations($report[DB_COLUMN_REPORTS__REPORT_ID]);
    	$anns = $this->groupAnnotations($anns);
        $this->page->set("anns", $anns);
	}

	function groupAnnotations($anns){
        $groups = array();
        foreach ($anns as $an){
            $key = sprintf("%s_%s_%s", $an['text'], $an['lemma'], $an['type']);
            $groups[$key] = $an;
        }
        ksort($groups);
        return array_values($groups);
    }

}
