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
class Ajax_lps_get_tag_docs extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
  		global $corpus;
		$tag = strval($_POST['tag']);
		$corpus_id = 22;
 
		$subcorpus_id = array_get_int($_POST, "subcorpus_id", 0);
		$deceased_gender = array_get_str($_POST, "deceased_gender", null);
		$deceased_maritial = array_get_str($_POST, "deceased_maritial", null);
		$deceased_source = array_get_str($_POST, "deceased_source", null);
		
		$where_ext = array();
		if ( $deceased_gender !== null )
			$where_ext['deceased_gender'] = $deceased_gender;
		if ( $deceased_maritial !== null )
			$where_ext['deceased_maritial'] = $deceased_maritial;
		if ( $deceased_source != null )
			$where_ext['deceased_source'] = $deceased_source;
		
		$where = array();
		if ( $subcorpus_id > 0)
			$where = array("subcorpus_id"=>$subcorpus_id);
		
		$tags = Page_lps_stats::get_error_tag_docs($corpus_id, $tag, $where, $where_ext);			
		return array( "errors"=>$c->errors, "docs"=>$tags );
	}
	
}
