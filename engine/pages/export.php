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
		
		$corpus_id = $corpus['id'];
		
		$corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
		$flags = DbCorporaFlag::getFlags();
	
		$this->setup_annotation_type_tree($corpus_id);
        $this->setup_relation_type_tree($corpus_id);
        $this->set("corpus_flags", $corpus_flags);
		$this->set("flags", $flags);
		$this->set("exports", $this->getExports($corpus['id']));
	}

	/**
	 * Ustaw strukturę dostępnych typów anotacji.
	 * @param int $corpus_id
	 */
	private function setup_annotation_type_tree($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);
		$this->set('annotation_types',$annotations);
	}

	private function setup_relation_type_tree($corpus_id){
        $relations = DbRelationSet::getRelationStructureTree($corpus_id);
        $this->set('relation_types', $relations);
    }
	
	/**
	 * Return tasks for $corpus_id.
     * @param int $corpus_id
	 */
	function getExports($corpus_id){
		global $db;
		$sql = "SELECT * FROM exports WHERE corpus_id = ?" .
				" ORDER BY `datetime_submit` DESC, export_id DESC";		
		return $db->fetch_rows($sql, array($corpus_id));		
	}
	
	static function getExportFilePath($export_id){
		global $config;
		return $config->path_exports . DIRECTORY_SEPARATOR . sprintf("inforex_export_%d.7z", $export_id);
	}
}


?>
