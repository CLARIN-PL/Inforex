<?

class DbTag{
	
	static function getTagsByReportIds($report_ids){
		global $db;

		$sql = "SELECT tokens_tags.*, tokens.report_id as report_id" .
				" FROM tokens JOIN tokens_tags ON tokens_tags.token_id=tokens.token_id " .
				" WHERE tokens.report_id IN (" . implode(",",$report_ids) . ")";

		return $db->fetch_rows($sql);
	}
	
	
	
}

?>