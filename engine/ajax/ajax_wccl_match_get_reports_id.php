<?php
 
class Ajax_wccl_match_get_reports_id extends CPageCorpus {

	function __construct(){
	    parent::__construct();
	    $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

    function execute(){
		global $corpus;

		$reports_id = array();
		
		$ccl_folder = sprintf("%s/ccls/corpus%04d", Config::Config()->get_path_secured_data(), $corpus['id']);
		
		if ( file_exists($ccl_folder) ){
			$files = scandir($ccl_folder);
			sort($files);
			foreach ($files as $file){
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if ( $ext == "xml" && intval(basename($file, ".xml")) > 0 ){
					$reports_id[] = intval(basename($file, ".xml"));
				}
			}
		}
		else{
			echo "Folder not found $ccl_folder";
			return array();
		}
									
		return $reports_id;
	}
	
}
