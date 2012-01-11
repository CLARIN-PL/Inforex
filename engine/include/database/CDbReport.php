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