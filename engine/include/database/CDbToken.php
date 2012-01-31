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
	
	static function getTokensByReportIds($report_ids){
		global $db;
		$sql = "SELECT * FROM tokens " .
				//"LEFT JOIN tokens_tags " .
				//"ON (tokens.token_id=tokens_tags.token_id) " .
				"WHERE report_id IN('" . implode("','",$report_ids) . "') ORDER BY report_id, `from`";
		return $db->fetch_rows($sql);
	}
	
	
	
}

?>