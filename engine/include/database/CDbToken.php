<?

class DbToken{
	
	/**
	 * Return list of tokens. 
	 */
	static function getTokenByReportId($report_id,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM tokens " .
				" WHERE report_id = ?";

		return $db->fetch_rows($sql, array($report_id));
	}
}

?>