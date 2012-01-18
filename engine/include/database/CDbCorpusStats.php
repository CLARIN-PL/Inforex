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
	static function getWordsFrequnces($corpus_id, $subcorpus_id=false, $class=false, $disamb=true){
		global $db;

		$sql = "SELECT tt.base, COUNT(*) AS c, COUNT(DISTINCT r.id) AS docs" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags tt USING (token_id)" .
				" WHERE r.corpora = ?" .
				($subcorpus_id ? " AND r.subcorpus_id = ?" : "") .
				($class ? " AND (tt.ctag = '$class' OR tt.ctag LIKE '$class:%')"  : "") . 
				($disamb ? " AND tt.disamb = 1" : "") .				
				" GROUP BY tt.base" .
				" ORDER BY c DESC";
		
		$args = array($corpus_id);
		
		if ($subcorpus_id)
			$args[] = $subcorpus_id;
		
		return $db->fetch_rows($sql, $args);
	}
	
	static function getDocumentLengthStats($corpus_id=null, $subcorpus_id=null){
		
		global $db;
		
		$sql = "SELECT COUNT(t.token_id) AS length" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" WHERE 1=1" .
				( $corpus_id ? " AND corpora=?" : "") .
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				" GROUP BY r.id";
		
		$params = array();
		if ( $corpus_id ) $params[] = $corpus_id;
		if ( $subcorpus_id ) $params[] = $subcorpus_id;
		
		$rows = $db->fetch_rows($sql, $params);
		
		$max = 0;
		foreach ($rows as $r)
			$max = max($max, $r['length']);
			
		$buckets = round($max/10);
		
		$stats = array();
		for ($i=1; $i<$buckets; $i++){
			$stats[$i*10] = 0;
		}
		
		foreach ($rows as $r){
			$stats[floor($r['length']/10+1)*10]++;
		}
		return $stats;
	}

	static function getDocumentLengtBySubcorpora($corpus_id=null){
		
		global $db;
		
		$stats = array();		
		$subc = DbCorpus::getCorpusSubcorpora($corpus_id);
		
		foreach ($subc as $s){
			$stats[$s['name']] = DbCorpusStats::getDocumentLengthStats(null, $s['subcorpus_id']);
		}
		
		$mstats = array();
		foreach ($subc as $s){
			$mstats[0][] = $s['name'];		
		}
		foreach ($stats as $name=>$s){
			foreach ($s as $bucket=>$count){
				$mstats[$bucket] = array();
				foreach ($subc as $s){
					$mstats[$bucket][] = intval($stats[$s['name']][$bucket]);
				}
			}
		}
		return $mstats;
	}
	
}

?>