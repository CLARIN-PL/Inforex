<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_export extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_EXPORT); 
	}
	
	function execute(){		
		global $corpus, $db;
				
		$this->set("exports", $this->getExports($corpus['id']));
	}
	
	/**
	 * Return tasks for $corpus_id.
	 */
	function getExports($corpus_id){
		global $db;
		$sql = "SELECT * FROM exports WHERE corpus_id = ?" .
				" ORDER BY `datetime_submit` DESC";		
		return $db->fetch_rows($sql, array($corpus_id));		
	}
	
	static function getExportFilePath($export_id){
		global $config;
		return $config->path_exports . DIRECTORY_SEPARATOR . sprintf("inforex_export_%d.7z", $export_id);
	}
}


?>
