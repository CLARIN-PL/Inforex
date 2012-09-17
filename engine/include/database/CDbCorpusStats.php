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
	static function getWordsFrequnces($corpus_id, $subcorpus_id=null, $class=null, $disamb=true){
		global $db;

		$docs = DbReport::getReportsCount($corpus_id, $subcorpus_id);

		$sql = "SELECT tt.base, COUNT(DISTINCT t.token_id) AS c, COUNT(DISTINCT r.id) AS docs" .
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

		$rows = $db->fetch_rows($sql, $args);
			
		foreach ($rows as &$r){			
			$r['docs_c'] = $r['docs']/$r['c'];
			$r['docs_per'] = $r['docs']/$docs * 100;
		} 
		
		return $rows;
	}
	
	/**
	 * Return a list of docuemnt lengths.
	 */
	static function getDocumentLengths($corpus_id=null, $subcorpus_id=null){
		
		global $db;
		
		$sql = "SELECT COUNT(t.token_id) AS count" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" WHERE 1=1" .
				( $corpus_id ? " AND corpora=?" : "") .
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				" GROUP BY r.id";
		
		$params = array();
		if ( $corpus_id ) $params[] = $corpus_id;
		if ( $subcorpus_id ) $params[] = $subcorpus_id;
		
		return $db->fetch_rows($sql, $params);		
	}

	/**
	 */
	static function getDocumentClassCounts($class, $corpus_id=null, $subcorpus_id=null){
		
		global $db;
		
		$sql = "SELECT COUNT(DISTINCT tt.token_id) AS count" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" LEFT JOIN tokens_tags tt ON (tt.token_id=t.token_id AND (tt.ctag = '$class' OR tt.ctag LIKE '$class:%'))" .
				" WHERE 1=1" .
				( $corpus_id ? " AND corpora=?" : "") .
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				" GROUP BY r.id";
		
		$params = array();
		if ( $corpus_id ) $params[] = $corpus_id;
		if ( $subcorpus_id ) $params[] = $subcorpus_id;
		
		return $db->fetch_rows($sql, $params);		
	}
	
	
	static function getDocumentClassCountsNorm($class, $corpus_id=null, $subcorpus_id=null){
		global $db;
		
		$sql = "SELECT ROUND(COUNT(DISTINCT tt.token_id)/COUNT(DISTINCT t.token_id)*100) AS count" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" LEFT JOIN tokens_tags tt ON (tt.token_id=t.token_id AND (tt.ctag = '$class' OR tt.ctag LIKE '$class:%'))" .
				" WHERE 1=1" .
				( $corpus_id ? " AND corpora=?" : "") .
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				" GROUP BY r.id";
		
		$params = array();
		if ( $corpus_id ) $params[] = $corpus_id;
		if ( $subcorpus_id ) $params[] = $subcorpus_id;
		
		return $db->fetch_rows($sql, $params);		
	}
	
	static function getDocumentClassCountsRatio($class1, $class2, $corpus_id=null, $subcorpus_id=null){
		
		global $db;
		
		$sql = "SELECT COUNT(DISTINCT tt1.token_id)/COUNT(DISTINCT tt2.token_id) AS count" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" LEFT JOIN tokens_tags tt1 ON (tt1.token_id=t.token_id AND (tt1.ctag = '$class1' OR tt1.ctag LIKE '$class1:%'))" .
				" LEFT JOIN tokens_tags tt2 ON (tt2.token_id=t.token_id AND (tt2.ctag = '$class2' OR tt2.ctag LIKE '$class2:%'))" .
				" WHERE 1=1" .
				( $corpus_id ? " AND corpora=?" : "") .
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				" GROUP BY r.id";
		
		$params = array();
		if ( $corpus_id ) $params[] = $corpus_id;
		if ( $subcorpus_id ) $params[] = $subcorpus_id;
		
		$rows = $db->fetch_rows($sql, $params);
		
		return $rows;		
	}
	
	static function getDocumentLengthsInSubcorpora($corpus_id=null){
		$stats = array();		
		$subc = DbCorpus::getCorpusSubcorpora($corpus_id);
		foreach ($subc as $s){
			$stats[$s['name']] = DbCorpusStats::getDocumentLengths(null, $s['subcorpus_id']);
		}
		return $stats;
	}

	static function getDocumentClassCountsNormInSubcorpora($class, $corpus_id=null){
		$stats = array();		
		$subc = DbCorpus::getCorpusSubcorpora($corpus_id);
		foreach ($subc as $s){
			$stats[$s['name']] = DbCorpusStats::getDocumentClassCountsNorm($class, null, $s['subcorpus_id']);
		}
		return $stats;
	}

	static function getDocumentClassCountsRatioInSubcorpora($class1, $class2, $corpus_id=null){
		$stats = array();		
		$subc = DbCorpus::getCorpusSubcorpora($corpus_id);
		foreach ($subc as $s){
			$stats[$s['name']] = DbCorpusStats::getDocumentClassCountsRatio($class1, $class2, null, $s['subcorpus_id']);
		}
		return $stats;
	}

	static function getDocumentLengthsBySubcorpora2($corpus_id=null){
		
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