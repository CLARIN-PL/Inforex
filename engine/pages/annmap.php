<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_annmap extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole(CORPUS_ROLE_READ) 
			&& hasCorpusRole(CORPUS_ROLE_BROWSE_ANNOTATIONS);
	}
	
	function execute(){		
		global $mdb2, $corpus, $db;
		
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
				
		$ext_where = null;
		if ( count($set_filters) ){
			foreach ($set_filters as $k=>$v)
				$ext_where .= " AND re.$k='$v'";
		}

		/* Zainicjalizuj tablicę $annmap do przechowywania hierarchicznych statystyk anotacji
		$sql = "SELECT ans.description setname," .
				"	ansub.description subsetname," .
				"	at.name typename" .
				" FROM annotation_types at" .
				" LEFT JOIN annotation_subsets ansub" .
				"	ON (at.annotation_subset_id=ansub.annotation_subset_id)" .
				" JOIN annotation_sets ans ON (at.group_id=ans.annotation_set_id)" . 
				" LEFT JOIN annotation_sets_corpora ac " .
				"	ON (ac.annotation_set_id = ans.annotation_set_id)" .
				" WHERE ac.corpus_id = ?";
						
		$annotation_sets = db_fetch_rows($sql, array($corpus_id));
		$anntype_to_set = array();
		$anntype_to_subset = array();
		foreach ($annotation_sets as $as){			
			$set = $as['setname'];
			$subset = $as['subsetname']==NULL ? "!uncategorized" : $as['subsetname'];
			$anntype = $as['typename'];
			if ( !isset($annmap[$set]) ){
				$annmap[$set]['meta'] 
					= array( 'count' => 0, 'unique' => 0, 'docs' => array(), 'rows' => array() );
			}
			if ( !isset($annmap[$set]['rows'][$subset]) ){
				$annmap[$set]['rows'][$subset] 
					= array( 'count' => 0, 'unique' => 0, 'docs' => array(), 'rows' => array() );
			}		
			if ( !isset($annmap[$set]['rows'][$subset]['rows'][$anntype])){
				$annmap[$set]['rows'][$subset]['rows'][$anntype] = array("type"=>$as["typename"]);
			}
			$anntype_to_set[$anntype] = $set;
			$anntype_to_subset[$anntype] = $subset;
		}
				
		$sql = "SELECT a.type," .
				"	 COUNT(*) AS count," .
				"	 COUNT(DISTINCT(a.text)) AS `unique`," .
				"    COUNT(DISTINCT(r.id)) AS docs" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				( $ext_where ? " JOIN $ext_table re ON (r.id=re.id)" : "") .
				" WHERE r.corpora=?" . 
				( $subcorpus ? " AND r.subcorpus_id = ?" : "") .
				( $status ? " AND r.status = ?" : "") .		
				$ext_where .		
				" GROUP BY a.type" .
				" ORDER BY a.type;";
		$annotations_count = $db->fetch_rows($sql, $params); 
		
		$sql = "SELECT a.type, a.text, COUNT(*) AS count, r.title" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				( $ext_where ? " JOIN $ext_table re ON (r.id=re.id)" : "") .
				" WHERE r.corpora=? AND a.stage='final'" . 
				( $subcorpus ? " AND r.subcorpus_id = ?" : "") .
				( $status ? " AND r.status = ?" : "") .
				$ext_where .		
				" GROUP BY a.type, a.text" .
				" ORDER BY a.type, count DESC";
		$annotations = $db->fetch_rows($sql, $params);
		$annotation_map = array();

		foreach ($annotations as $an){
			$annotation_map[$an['type']][] = $an;			
		}
		foreach ($annotations_count as $k=>$an){
			$annotations_count[$k]['details'] = $annotation_map[$an['type']];
		}
				
		foreach ($annotations_count as $ac){
			$anntype = $ac['type'];
			$set = $anntype_to_set[$anntype];
			$subset = $anntype_to_subset[$anntype];		
			
			$annmap[$set]['rows'][$subset]['rows'][$anntype] = $ac;
			$annmap[$set]['rows'][$subset]['meta']['count'] +=$ac['count'];				
			$annmap[$set]['rows'][$subset]['meta']['unique']+=$ac['unique'];
			$annmap[$set]['meta']['count'] +=$ac['count'];				
			$annmap[$set]['meta']['unique']+=$ac['unique'];
		}
		*/
		
		$annmap = DbAnnotation::getAnnotationSetsWithCount($corpus_id, $subcorpus, $status);
		
		/* Fill template */		
		$this->set("filters", HelperDocumentFilter::getCorpusCustomFilters($corpus_id, $set_filters));													
		$this->set("sets", $annmap);
		$this->set("subcorpus", $subcorpus);
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
		$this->set("status", $status);
		$this->set("statuses", $statuses = DbStatus::getAll());
	}
}


?>
