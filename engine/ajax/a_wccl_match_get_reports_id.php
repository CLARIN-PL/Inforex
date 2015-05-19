<?php
 
class Ajax_wccl_match_get_reports_id extends CPage {
	
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $config, $corpus;

		$reports_id = array();
		
		$ccl_folder = sprintf("%s/ccls/corpus%04d", $config->path_secured_data, $corpus['id']);
		
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
?>
 