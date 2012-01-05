<?
/**
 * 
 */
class DbTests{
	
	static function getCorpusReportIdsAndContent($corpus_id){
		global $db;
		
		$sql = " SELECT id, content " .
				" FROM reports " .
				" WHERE corpora = ?";

		return $db->fetch_rows($sql, array($corpus_id));
	}
}
?>