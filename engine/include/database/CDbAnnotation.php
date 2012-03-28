<?

class DbAnnotation{
	
	/**
	 * Return list of annotations. 
	 */
	static function getAnnotationByReportId($report_id,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports_annotations " .
				" WHERE report_id = ?";

		return $db->fetch_rows($sql, array($report_id));
	}
	
	/**
	 * Return list of annotations types. 
	 */
	static function getAnnotationTypes($fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_types ";

		return $db->fetch_rows($sql);
	}
	
	static function getAnnotationTypesByCorpora($corpus_id,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .		
				" FROM annotation_sets a_s " .
				" LEFT JOIN annotation_sets_corpora a_s_c ON (a_s.annotation_set_id=a_s_c.annotation_set_id) " .
				" WHERE a_s_c.corpus_id=? ";
		
		return $db->fetch_rows($sql,array($corpus_id));
	}
	
	static function getAnnotationTypesBySets($report_ids, $relation_ids){
		global $db;
	    $sql = "SELECT DISTINCT type, report_id " .
	            "FROM reports_annotations " .
	            "WHERE report_id IN('" . implode("','",$report_ids) . "') " .
	            "AND " .
	                "(id IN " .
	                    "(SELECT source_id " .
	                    "FROM relations " .
	                    "WHERE relation_type_id " .
	                    "IN " .
	                        "(".implode(",",$relation_ids).") ) " .
	                "OR id " .
	                "IN " .
	                    "(SELECT target_id " .
	                    "FROM relations " .
	                    "WHERE relation_type_id " .
	                    "IN " .
	                        "(".implode(",",$relation_ids).") ) )";
		return $db->fetch_rows($sql);
	}
	
	static function getAnnotationsBySets($report_ids=null, $annotation_layers=null, $annotation_names=null){
		global $db;
		$sql = "SELECT *, if(ra.type like 'wsd%', 'sense', ra.type) as type FROM reports_annotations ra " .
				"LEFT JOIN annotation_types at " .
					"ON (ra.type=at.name) " .
				"LEFT JOIN reports_annotations_attributes raa " .
					"ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();		
		$andwhere[] = " stage='final' ";
		if ($report_ids <> null && count($report_ids) > 0)
			$andwhere[] = "report_id IN (" . implode(",",$report_ids) . ")";
		if ($annotation_layers <> null && count($annotation_layers) > 0)
			$orwhere[] = "at.group_id IN (" . implode(",",$annotation_layers) . ")";
		if ($annotation_names <> null && count($annotation_names) > 0)
			$orwhere[] = "ra.type IN ('" . implode("','",$annotation_names) . "')";		
		if (count($andwhere) > 0)
			$sql .= " WHERE (" . implode(" AND ", $andwhere) . ") ";
		if (count($orwhere) > 0) 
			if (count($andwhere)==0)
				$sql .= " WHERE ";
			else 			
				$sql .= " AND ( " . implode(" OR ",$orwhere) . " ) ";			
		$sql .= " ORDER BY `from`";	
								
		return $db->fetch_rows($sql); 	
				
	}
	
}

?>