<?

class DbReport{
	
	/**
	 * Return list of reports. 
	 */
	static function getReportsByCorpusId($corpus_id,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports " .
				" WHERE corpora = ?";

		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of reports with limit 
	 */
	static function getReportsByCorpusIdLimited($corpus_id,$limit_from,$limit_to,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports " .
				" WHERE corpora=? " .
				" LIMIT ". $limit_from .", " . $limit_to . " ";
		return $db->fetch_rows($sql, array($corpus_id));
	}	
	
	/**
	 * Return list of reports
	 * Input (reports.corpora, select, join, where, group_by)
	 * Return (select)
	 */
	static function getReportsByCorpusIdWithParameters($corpus_id,$select,$join,$where,$group_by){
  		global $db;
  		$sql = "SELECT ".
  				$select .
  				" FROM reports r " .
  				$join.  				
  				" WHERE r.corpora=? " .
  				$where .
  				$group_by;
  							
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of reports
	 * Input (reports.corpora, select, join, where, group_by)
	 * Return (select)
	 */
	static function getReportsByReportsListWithParameters($report_ids,$select,$join,$where,$group_by){
  		global $db;
  		$sql = "SELECT ".
  				$select .
  				" FROM reports r " .
  				$join.  				
  				" WHERE r.id IN  ('". implode("','",$report_ids) ."') " .
  				$where .
  				$group_by;

		return $db->fetch_rows($sql);
	}
  							

	static function getReports($corpus_id=null,$subcorpus_id=null,$documents_id=null, $flags=null){
		global $db;
		
		$where = array();
		if ( $corpus_id <> null && count($corpus_id) > 0)
			$where[] = "corpora IN (" . implode(",", $corpus_id) . ")";
		if ( $subcorpus_id <> null && count($subcorpus_id) > 0)
			$where[] = "subcorpus_id IN (" . implode(",", $subcorpus_id) . ")";
		if ( $documents_id <> null && count($documents_id) > 0)
			$where[] = "id IN (" . implode(",", $documents_id) . ")";
			
		$sql = " SELECT * FROM reports ";
		
		if ($flags <> null && count($flags) > 0){
			$sql .= "LEFT JOIN reports_flags rf ON reports.id=rf.report_id " .
					"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id ";
			foreach ($flags as $flag_name=>$flag_values){
				$where[] = "(cf.short=\"$flag_name\" AND rf.flag_id IN (". implode(",", $flag_values) .") )";
			}
		}		
		
		if ( count($where) > 0 )
			$sql .= " WHERE " . implode(" OR ", $where);
	
		return $db->fetch_rows($sql);
	}
}
?>