<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

require_once($config->path_engine . "/pages/lps_stats.php");

/**
 */
class Ajax_lps_get_corr_tags extends CPage {
	
	function checkPermission(){
		if ( hasRole('loggedin') )
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		
		$corr_type = strval($_POST['corr_type']);
		$subcorpus_id = intval($_POST['subcorpus_id']);
		$tags = Page_lps_stats::get_error_type_tags($corr_type, $subcorpus_id);	
		
		$json = array( "success"=>1, "errors"=>$c->errors, "tags"=>$tags );
				
		echo json_encode($json);
	}
	
}
?>
