<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_delete_annotation extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
        $this->anyCorpusRole[] = CORPUS_ROLE_DELETE_ANNOTATIONS;
    }
	
	function execute(){
		global $db;
		$annotation_id = intval($_POST['annotation_id']);
		
		$rels = $db->fetch_one("SELECT COUNT(*) FROM relations WHERE source_id = ? OR target_id = ?", array($annotation_id, $annotation_id));
		
		if ( $rels > 0 ){
			$json = array("error"=>"This annotation cannot be deleted because it is referenced by some relations. Please remove the relations first.");	
		}
		else{		
			$db->execute("DELETE FROM reports_annotations_optimized WHERE id=?", array($annotation_id));
			$json = array("success" => "ok");
		}		
		return $json;
	}
	
}
