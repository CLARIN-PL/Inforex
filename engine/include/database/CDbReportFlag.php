<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbReportFlag{
	
	/*
	 * Return list of corpora_flags ids
	 * 
	 * index_flags: array, values: corpora_flags.corpora_flag_id or corpora_flags.short
	 */
	static function getReportFlagData($report_ids, $corpora_flag_ids){
		global $db;
			//$sql .= "LEFT JOIN reports_flags rf ON reports.id=rf.report_id " .
			//		"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id ";
					
		$sql = "SELECT rf.report_id, cf.short " .
				"FROM reports_flags rf " .
				"LEFT JOIN corpora_flags cf " .
				"ON cf.corpora_flag_id=rf.corpora_flag_id " .
				"WHERE rf.report_id " .
				"IN ('" . implode("','",$report_ids) . "') " .
				"AND cf.corpora_flag_id " .
				"IN ('" . implode("','",$corpora_flag_ids) . "') " .
				"AND rf.flag_id " .
				"IN (3,4)";
		return $db->fetch_rows($sql);
	}

	/**
	 * 
	 */
	static function getReportFlags($report_id){
		global $db;
		$sql = "SELECT r.id, cf.short, rf.flag_id" .
				" FROM reports_flags rf " .
				" JOIN reports r ON r.id = rf.report_id" .
				" JOIN corpora_flags cf USING (corpora_flag_id)" .
				" WHERE r.id = ?";
		$flags = $db->fetch_rows($sql, array($report_id));
		$docflags = array();
		foreach ($flags as $f)
			$docflags[strtolower($f['short'])] = $f['flag_id'];
		return $docflags;
	}
}

?>