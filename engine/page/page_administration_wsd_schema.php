<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_wsd_schema extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->includeJs("js/page_administration_wsd_schema_dbpager.js");
    }

	function execute(){
		$sens = DbSens::getSensList(null,1);
		foreach($sens as $key => $value){
			$sens[$key]['annotation_name'] = substr($sens[$key]['annotation_name'], 4); // obcinanie "wsd_"
		}
		$this->set("sensList", $sens);
	}
}

?>
