<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbToken{
	
	static function saveToken($report_id, $from, $to, $eos=0){
		global $db;
		$sql = "INSERT INTO tokens(`report_id`, `from`, `to`, `eos`) VALUES(?,?,?,?);";
		$db->execute($sql, array($report_id, $from, $to, $eos));
		return $db->last_id();
	}

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
				"WHERE report_id IN('" . implode("','",$report_ids) . "') ORDER BY report_id, `from`";
		return $db->fetch_rows($sql);
	}

	static function deleteReportTokens($report_id){
		global $db;
		$sql = "DELETE FROM tokens WHERE report_id=?";
		$db->execute($sql, array($report_id));
		
		DbToken::cleanAfterDelete();
	}
	
	static function deleteToken($token_id){
		global $db;
		$sql = "DELETE FROM tokens WHERE id=?";
		$db->execute($sql, array($token_id));
		
		DbToken::cleanAfterDelete();
	}
	static function clean(){
		global $db;
		$sql = "DELETE t.* FROM tokens t".
				" LEFT JOIN reports ON (t.report_id = reports.id) ".
				" WHERE reports.id IS NULL";
		$db->execute($sql);
		
		DbToken::cleanAfterDelete();
	}
	
	static function cleanAfterDelete(){
		DbCTag::clean();
		DbBase::clean();
	}
}

?>