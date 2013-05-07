<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbCorpusRelation{
	
	/**
	 * Funkcja pobiera z bazy danych nazwy typów relacji i ich ilości dla danego korpusu
	 * Return (relation_name, relation_count, relation_id). 
	 */
	static function getRelationsData($corpus_id,$document_id=false){
  		global $db;
  		$sql = "SELECT rs.name AS relation_name, count(rel.id) AS relation_count, rs.relation_set_id AS relation_id " .
				"FROM relation_sets rs " .
				"LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) " .
				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
				"LEFT JOIN reports_annotations an ON (rel.source_id=an.id) " .
				"LEFT JOIN reports rep ON (rep.id=an.report_id) " .
				"WHERE rep.corpora=? " .
				($document_id ? " AND an.report_id = ? " : "") .
				"GROUP BY rs.relation_set_id " .
				"ORDER BY rs.name";			
				
		$args = array($corpus_id);
		
		if ($document_id)
			$args[] = $document_id;								
		
		return $db->fetch_rows($sql, $args);
	}
	
	
	static function getRelationsListData($corpus_id, $relation_id=false, $document_id=false){
  		global $db;
  		$sql = "SELECT rs.name AS relation_name, count(an.type) AS relation_count, rty.name AS relation_type " .
  				"FROM relation_sets rs " .
  				"LEFT JOIN relation_types rty ON rs.relation_set_id=rty.relation_set_id " .
  				"LEFT JOIN relations rel ON rel.relation_type_id=rty.id " .
  				"LEFT JOIN reports_annotations an ON rel.source_id=an.id " .
  				"LEFT JOIN reports rep ON rep.id=an.report_id " .
  				"WHERE rep.corpora=? " .
  				($relation_id ? " AND rs.relation_set_id = ? " : "") .
  				($document_id ? " AND an.report_id = ? " : "") .
  				"GROUP BY rty.name;";
		
		$args = array($corpus_id);
		if ($relation_id)
			$args[] = $relation_id;								
		if ($document_id)
			$args[] = $document_id;
 		
				
		return $db->fetch_rows($sql, $args);
	}
	

	/**
	 * Funkcja pobiera z bazy danych listę relacji dla typu relation_types - pobiera tylko relations_limit wpisów (inicjalizacja tablicy z listą relacji)
	 * Return (document_id, subcorpus_name, source_text, source_type, target_text, target_type). 
	 */
	static function getRelationList($corpus_id, $relation_types, $relation_set_id, $relations_limit_to, $relations_limit_from=false, $document_id=false){
		global $db;
  		$sql = "SELECT rep.id AS document_id, cor.name AS subcorpus_name, an_sou.text AS source_text, an_sou.type AS source_type, an_tar.text AS target_text, an_tar.type AS target_type " .
  				"FROM relation_sets rs " .
  				"LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) " .
  				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
  				"LEFT JOIN reports_annotations an_sou ON (rel.source_id=an_sou.id) " .
  				"LEFT JOIN reports_annotations an_tar ON (rel.target_id=an_tar.id) " .
  				"LEFT JOIN reports rep ON (rep.id=an_sou.report_id) " .
  				"LEFT JOIN corpus_subcorpora cor ON (cor.subcorpus_id=rep.subcorpus_id) " .
  				"WHERE rep.corpora=? " .
  				($document_id ? " AND an_sou.report_id = ? " : "") .
  				"AND rty.name=? " .
				"AND rs.relation_set_id=? " .
  				"LIMIT " .
  				($relations_limit_from ? "? " : "0 ") .
				", ? ;";	

		$args = array($corpus_id);
		
		if ($document_id)
			$args[] = $document_id;
 		$args[] = $relation_types;
 		$args[] = $relation_set_id;
 		if ($relations_limit_from)
			$args[] = $relations_limit_from; 		
 		$args[] = $relations_limit_to;			  				
			
  		return $db->fetch_rows($sql, $args);
	}
	
	
	//TODO to delete
	static function getRelationsByRelationSetIds($relation_set_ids){
		global $db;
		if (!$relation_set_ids) $relation_set_ids = array();
		$sql = "SELECT rs.name AS name, rel.relation_type_id AS type " .
				"FROM relation_sets rs " .
				"LEFT JOIN relation_types rty ON rs.relation_set_id=rty.relation_set_id " .
				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
				"WHERE rty.relation_set_id IN ('0','". implode("','",$relation_set_ids) ."') " .
	            "AND rel.relation_type_id IS NOT NULL " .
				"GROUP BY rel.relation_type_id ";
		return $db->fetch_rows($sql);		
	}
	
	//TODO to delete
	static function getRelationsBySets($report_ids, $relation_type_ids){
		global $db;
	    $sql = "SELECT reports_annotations.report_id as report_id, rel.id, rel.relation_type_id, rel.source_id, rel.target_id, relation_types.name " .
	            "FROM " .
	                "(SELECT * " .
	                "FROM relations " .
	                "WHERE source_id IN " .
	                    "(SELECT id " .
	                    "FROM reports_annotations " .
	                    "WHERE report_id IN('" . implode("','",$report_ids) . "')) " .
	                "AND relation_type_id " .
	                "IN (".implode(",",$relation_type_ids).")) rel " .
	            "LEFT JOIN relation_types " .
	            "ON rel.relation_type_id=relation_types.id " .
	            "LEFT JOIN reports_annotations " .
	            "ON rel.source_id=reports_annotations.id ";
		return $db->fetch_rows($sql);		            				
	}
	
	static function getRelationsBySets2($report_ids=null, $relation_set_ids=null, $relation_type_ids=null){
		global $db;
	    $sql = "SELECT reports_annotations.report_id as report_id, " .
	    		"      rel.id, " .
	    		"      rel.relation_type_id, " .
	    		"      rel.source_id, " .
	    		"      rel.target_id, " .
	    		"      relation_types.relation_set_id, " .
	    		"      relation_types.name, " .
	    		"      relation_sets.name as rsname " .
	            "FROM " .
	                "(SELECT * " .
	                "FROM relations " .
	                "WHERE source_id IN " .
	                    "(SELECT id " .
	                    "FROM reports_annotations " .
	                    "WHERE report_id IN('0','" . implode("','",$report_ids) . "')) " .
						") rel " .
	            "LEFT JOIN relation_types " .
	            "ON rel.relation_type_id=relation_types.id " .
	            "LEFT JOIN reports_annotations " .
	            "ON rel.source_id=reports_annotations.id " .
	            "LEFT JOIN relation_sets " .
	            "ON relation_types.relation_set_id=relation_sets.relation_set_id ";
		$orwhere = array();
		if ($relation_set_ids <> null && count($relation_set_ids) > 0)
			$orwhere[] = "relation_types.relation_set_id IN (" . implode(",",$relation_set_ids) . ")";						            
		if ($relation_type_ids <> null && count($relation_type_ids) > 0)
			$orwhere[] = "relation_types.id IN (" . implode(",",$relation_type_ids) . ")";	
		if (count($orwhere) > 0) 
			$sql .= " WHERE ( " . implode(" OR ",$orwhere) . " ) ";		
		
		return $db->fetch_rows($sql);			
	}
	
	
	
}
?>