<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

// ToDo: Move common methods to an external file
require_once(Config::Config()->get_path_engine() . "/page/page_lps_stats.php");

/**
 */
class Ajax_lps_get_corr_tags extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		$corr_type = strval($_POST['corr_type']);
		$subcorpus_id = intval($_POST['subcorpus_id']);
		$corpus_id = intval($_POST['corpus_id']);
  
		$where = array();
		if (intval($subcorpus) > 0)
			$where['subcorpus_id'] = intval($subcorpus);
		
		$documents = DbReport::getExtReportsFiltered($corpus_id, $where, array());
				
		$tags = Page_lps_stats::get_error_type_tags($documents, $corr_type);	
		
		return array("errors"=>$c->errors, "tags"=>$tags);
	}
	
}
