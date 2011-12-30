<?
/**
 * 
 */
class DbCorpus{
	
	/**
	 * Return list of subcorpus. 
	 */
	static function getCorpusSubcorpora($corpus_id){
		global $db;
		
		$sql = "SELECT *" .
				" FROM corpus_subcorpora" .
				" WHERE corpus_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of corpus flags. 
	 */
	static function getCorpusFlags($corpus_id){
		global $db;
		
		$sql = "SELECT short " .
				"FROM corpora_flags " .
				"WHERE corpora_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
}

?>