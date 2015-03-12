<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_dictgen extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole(CORPUS_ROLE_READ) 
			&& hasCorpusRole(CORPUS_ROLE_BROWSE_ANNOTATIONS);
	}
	
	function execute(){		
		global $corpus, $db;
		
		$corpus_id = $corpus['id'];
		$subcorpus = $_GET['subcorpus'];
		$status = intval($_GET['status']);
		$custom_filters = HelperDocumentFilter::gatherCorpusCustomFilters($_POST);		
		$ext_table = DbCorpus::getCorpusExtTable($corpus_id);
		$set_filters = array();		
				
		$params = array($corpus_id);
		if ($subcorpus)
			$params[] = $subcorpus;
			
		if ( $status > 0 )
			$params[] = $status;				
		
		$annmap = DbAnnotation::getAnnotationSetsWithCount($corpus_id, $subcorpus, $status);
		
		
		$sql = "SELECT * FROM reports_annotations_optimized a" .
				" JOIN annotation_types t ON (a.annotation_type_id = t.type_id)" .
				" JOIN reports r ON (r.id=a.report_id)" .
				" WHERE r.corpora = ? AND t.group_id = ?";
		$params = array(7, 1);
		$annotations = $db->fetch_rows($sql, $params);
		
		/* Fill template */
		$this->set("annotations", $annotations);		
		$this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));													
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
	}
}


?>
