<?
/**
 * 
 */
class DbCorpusStats{
	
	/**
	 * Calculate words frequences according given criteria.
	 * 
	 * Return tuples (base, word_count, document_count). 
	 */
	static function getWordsFrequnces($corpus_id, $subcorpus_id=false, $ign=false, $disamb=true){
		global $db;

		$sql = "SELECT tt.base, COUNT(*) AS c, COUNT(DISTINCT r.id) AS docs" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags tt USING (token_id)" .
				" WHERE r.corpora = ?" .
				($subcorpus_id!==false ? " AND r.subcorpus_id = ?" : "") .
				($ign ? " AND tt.ctag = 'ign'" : "") . 
				($disamb ? " AND tt.disamb = 1" : "") .				
				" GROUP BY tt.base" .
				" ORDER BY c DESC";
		
		$args = array($corpus_id);
		if ($subcorpus_id!==false)
			$args[] = $subcorpus_id;
		
		return $db->fetch_rows($sql, $args);
	}
	
}

?>