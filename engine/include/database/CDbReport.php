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
  							

	static function getReports($corpus_id=null,$subcorpus_id=null,$documents_id=null,$fields=null){
		global $db;
		
		$where = array();
		if ( $corpus_id <> null && count($corpus_id) > 0)
			$where[] = "corpora IN (" . implode(",", $corpus_id) . ")";
		if ( $subcorpus_id <> null && count($subcorpus_id) > 0)
			$where[] = "subcorpus_id IN (" . implode(",", $subcorpus_id) . ")";
		if ( $documents_id <> null && count($documents_id) > 0)
			$where[] = "id IN (" . implode(",", $documents_id) . ")";
			
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports";
		
		$sql .= (count($where) ? " WHERE " . implode(" OR ", $where) : "");
	
		return $db->fetch_rows($sql);
	}
	
	/**
	 * Return list of reports ids. 
	 */
	static function getReportIds($corpus_ids,$subcorpus_ids, $document_ids, $flag_names, $flag_values){
		global $db;
		$reports = array();		
		//if flags are given		
		if ( !(empty($flag_names) || empty($flag_values )) ) {
			foreach($flag_names as $flag_name){
				$flag_name_s = 'AND cf.short=\'' . $flag_name . '\' ';
				$sql = "SELECT rf.report_id AS id " .
						"FROM reports_flags rf " .
						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
						"WHERE cf.corpora_id=? " .
						$flag_name_s .	
						"AND rf.flag_id=? ";
				foreach ($corpus_ids as $id){
					foreach ($flag_values[$flag_name] as $flag_v){
						foreach ($db->fetch_rows($sql, array($id,$flag_v)) as $report){
							if ( intval($report['id'])){
								$reports[$report['id']] = 1;				
							}
						}
					}
				}
			
				$sql = "SELECT r.id " .
						"FROM reports r " .
						"LEFT JOIN reports_flags rf ON r.id=rf.report_id " .
						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
						"WHERE r.subcorpus_id=? " .
						$flag_name_s .
						"AND rf.flag_id=? ";
				foreach ($subcorpus_ids as $id){
					foreach ($flag_values[$flag_name] as $flag_v){
						foreach ($db->fetch_rows($sql, array($id,$flag_v)) as $report){
							if ( intval($report['id'])){
								$reports[$report['id']] = 1;				
							}
						}
					}
				}
			
				$sql = "SELECT rf.report_id AS id " .
						"FROM reports_flags rf " .
						"LEFT JOIN corpora_flags cf " .
						"ON cf.corpora_flag_id=rf.corpora_flag_id " .
						"WHERE rf.report_id=? " .
						$flag_name_s .
						"AND rf.flag_id=? ";	
				foreach ($document_ids as $id){
					foreach ($flag_values[$flag_name] as $flag_v){
						foreach ($db->fetch_rows($sql, array($id,$flag_v)) as $report){
							if ( intval($report['id'])){
								$reports[$report['id']] = 1;				
							}
						}
					}
				}
			}	
		}
		else{
			if (!empty($corpus_ids)){
				$sql = "SELECT id FROM reports WHERE corpora IN (".implode("','",$corpus_ids).")";
				foreach ($db->fetch_rows($sql) as $report)
					if ( intval($report['id']))
						$reports[$report['id']] = 1;
			}
			if (!empty($subcorpus_ids)){
				$sql = "SELECT id FROM reports WHERE subcorpus_id IN (".implode("','",$subcorpus_ids).")";		
				foreach ($db->fetch_rows($sql) as $report){
					if ( intval($report['id']))
					$reports[$report['id']] = 1;
				}
				
			}
				
			foreach ($document_ids as $report_id)
				$reports[$report_id] = 1;	
		}

		return array_keys($reports);
	}	
	
}

?>