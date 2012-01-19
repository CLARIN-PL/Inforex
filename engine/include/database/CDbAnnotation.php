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
	
}

?>