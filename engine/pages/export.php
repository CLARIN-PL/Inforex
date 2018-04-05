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
		$this->set("users", DbCorporaUsers::getCorpusUsers($corpus_id));

		$this->set("morpho_users", DbCorporaUsers::getCorpusUsersWithMorphoTaggs($corpus_id));
	}

	/**
	 * Ustaw strukturę dostępnych typów anotacji.
	 * @param int $corpus_id
	 */
	private function setup_annotation_type_tree($corpus_id){
		$annotations = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);

		$morphoAnnotations = array(
			array(
				'name' => 'Only tagger',
				'value'=> 'tagger',
				'help' => 'Get only tagger decisions.'
			),
            array(
                'name' => 'User',
                'value'=> 'user',
                'help' => 'Get specific user decision.'
            ),
            array(
                'name' => 'Final',
                'value'=> 'final',
                'help' => 'Get final annotation decision after agreement in 2+1 system.'
            ),
            array(
                'name' => 'Final or tagger',
                'value'=> 'final_or_tagger',
                'help' => 'Get final decision or tagger if final annotation is not present.'
            ),
		);
		$this->set('annotation_types',$annotations);
		$this->set('morpho_annotation_types',$morphoAnnotations);
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
		$sql = "SELECT e.*, COUNT(ee.id) AS 'errors' FROM exports e 
				LEFT JOIN export_errors ee ON e.export_id = ee.export_id
				WHERE e.corpus_id = ?	
				GROUP BY e.export_id
				ORDER BY e.`datetime_submit` DESC, e.export_id DESC";
		$exports = $db->fetch_rows($sql, array($corpus_id));
		ChromePhp::log($exports);
		return $exports;
	}
	
	static function getExportFilePath($export_id){
		global $config;
		return $config->path_exports . DIRECTORY_SEPARATOR . sprintf("inforex_export_%d.7z", $export_id);
	}
}


?>
