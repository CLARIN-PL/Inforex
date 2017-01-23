<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbCorpusStats{
	
	static function getUniqueBaseCount($corpus_id, $subcorpus_id, $class, $disamb=true){
		global $db;
		
		$params = array($corpus_id);
		
		if ($subcorpus_id){
			$params[] = $subcorpus_id;
		}
		
		if ($class){
			$params[] = $class;
		}
		
		$sql = "SELECT COUNT(DISTINCT base_id) AS c" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags_optimized tto USING (token_id)" .
				" WHERE r.corpora = ?" .
				($subcorpus_id ? " AND r.subcorpus_id = ?" : "") .
				($class ? " AND tto.pos = ?"  : "") .
				($disamb ? " AND tto.disamb = 1" : "");
		return $db->fetch_one($sql, $params);			
	}
	
	/**
	 * Calculate words frequences according given criteria.
	 * 
	 * Return tuples (base, word_count, document_count). 
	 */
	static function getWordsFrequnces($corpus_id, $subcorpus_id=null, $class=null, $disamb=true, $limit_from=null, $limit_by=null){
		global $db;

		$params = array($corpus_id);
		
		if ($subcorpus_id){
			$params[] = $subcorpus_id;
		}
		
		if ($class){
			$params[] = $class;
		}

		$sql = "SELECT base_id AS id, b.text AS base, COUNT(DISTINCT t.token_id) AS c, COUNT(DISTINCT r.id) AS docs" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags_optimized tto USING (token_id)" .
				" JOIN bases b ON (b.id = tto.base_id)" .
				" WHERE r.corpora = ?" .
				($subcorpus_id ? " AND r.subcorpus_id = ?" : "") .
				($class ? " AND tto.pos = ?"  : "") . 
				($disamb ? " AND tto.disamb = 1" : "") .
				" GROUP BY tto.base_id" .
				" ORDER BY c DESC LIMIT $limit_from, $limit_by";
		
		$rows = $db->fetch_rows($sql, $params);
			
		foreach ($rows as &$r){
			$r['no'] = ++$limit_from;
		} 
		
		return $rows;
	}

	/**
	 * Return 
	 */
	static function getWordsFrequencesPerSubcorpus($corpus_id, $class=null, $disamb=true, $base_ids=null){
		global $db;

		$params = array();
		$params[] = $corpus_id;
		
		if ($class){
			$params[] = $class;
		}
		
		$base_ids_sql = null;
		if ( $base_ids != null && is_array($base_ids) ){
			$base_ids = array_filter($base_ids);
			if ( count($base_ids)>0 ){
				$base_ids_sql = " tto.base_id IN (". implode(",", array_fill(0,  count($base_ids), "?")) .")";
				$params = array_merge($params, $base_ids);
			}
		}

		$sql = "SELECT base_id, r.subcorpus_id, COUNT(DISTINCT t.token_id) AS c, COUNT(DISTINCT r.id) AS docs".
			" FROM tokens t".
			" JOIN reports r ON (t.report_id=r.id)".
			" JOIN tokens_tags_optimized tto USING (token_id)".
			" WHERE r.corpora = ?".
				( $disamb ? " AND tto.disamb = 1" : "" ).
				($class ? " AND tto.pos = ?"  : "") .
				( $base_ids_sql ? " AND " .$base_ids_sql : "") .
			" GROUP BY tto.base_id, r.subcorpus_id".
			" ORDER BY base_id, r.subcorpus_id";
		return $db->fetch_rows($sql, $params);
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
		
		if (!is_array($class1))
			$class1 = array($class1);
		
		if (!is_array($class2))
			$class2 = array($class2);

		$class1_cond = array();
		foreach ( $class1 as $c ){
			$class1_cond[] = "tt1.ctag = '$c'";
			$class1_cond[] = "tt1.ctag LIKE '$c:%'";
		}

		$class2_cond = array();
		foreach ( $class2 as $c ){
			$class2_cond[] = "tt2.ctag = '$c'";
			$class2_cond[] = "tt2.ctag LIKE '$c:%'";
		}
		
		$sql = "SELECT COUNT(DISTINCT tt1.token_id)/COUNT(DISTINCT tt2.token_id) AS count" .
				" FROM reports r " .
				" JOIN tokens t ON (r.id=t.report_id)" .
				" LEFT JOIN tokens_tags tt1 ON (tt1.token_id=t.token_id AND (" . implode(" OR ", $class1_cond) . "))" .
				" LEFT JOIN tokens_tags tt2 ON (tt2.token_id=t.token_id AND (" . implode(" OR ", $class2_cond) . "))" .
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
