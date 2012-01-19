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
  							

	static function getReports($corpus_id=null,$subcorpus_id=null,$documents_id=null){
		global $db;
		
		$where = array();
		if ( $corpus_id <> null && count($corpus_id) > 0)
			$where[] = "corpora IN (" . implode(",", $corpus_id) . ")";
		if ( $subcorpus_id <> null && count($subcorpus_id) > 0)
			$where[] = "subcorpora_id IN (" . implode(",", $subcorpus_id) . ")";
		if ( $documents_id <> null && count($documents_id) > 0)
			$where[] = "id IN (" . implode(",", $documents_id) . ")";
			
		$sql = " SELECT * FROM reports";
		if ( count($where) > 0 )
			$sql .= " WHERE " . implode(" OR ", $where);
	
		return $db->fetch_rows($sql);
	}
}

?>