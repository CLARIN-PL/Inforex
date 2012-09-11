<?php

require_once($config->path_engine . "/pages/lps_stats.php");

/**
 */
class Ajax_lps_get_tag_docs extends CPage {
	
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
		$tag = strval($_POST['tag']);
		$subcorpus_id = intval($_POST['subcorpus_id']);
		$tags = Page_lps_stats::get_error_tag_docs($tag, $subcorpus_id);			
		$json = array( "success"=>1, "errors"=>$c->errors, "docs"=>$tags );				
		echo json_encode($json);
	}
	
}
?>
