<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbCorpusStats{
	
	static function getUniqueBaseCount($corpus_id, $subcorpus_id, $class, $disamb=true, $phrases=null){
		global $db;
		
		$params = array($corpus_id);
		
		if ($subcorpus_id){
			$params[] = $subcorpus_id;
		}
		
		if ($class){
			$params[] = $class;
		}

		$phrases_sql = null;
		if ($phrases !== null ){
			$phrases_sql = array();
			foreach ( $phrases as $p ){
				$phrases_sql[] = " b.text LIKE ? ";
				$params[] = $p;
			}
			$phrases_sql = implode(" OR ", $phrases_sql);
		}
		
		$sql = "SELECT COUNT(DISTINCT base_id) AS c" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags_optimized tto USING (token_id)" .
				($phrases_sql ? " JOIN bases b ON (b.id = tto.base_id)" : "").
				" WHERE r.corpora = ?" .
				($subcorpus_id ? " AND r.subcorpus_id = ?" : "") .
				($class ? " AND tto.pos = ?"  : "") .
				($disamb ? " AND tto.disamb = 1" : "") .
				($phrases_sql ? " AND $phrases_sql" : "");
		return $db->fetch_one($sql, $params);			
	}
	
	/**
	 * Calculate words frequences according given criteria.
	 * 
	 * Return tuples (base, word_count, document_count). 
	 */
	static function getWordsFrequnces($corpus_id, $subcorpus_id=null, $class=null, $disamb=true, $phrases=null, $limit_from=null, $limit_by=null){
		global $db;

		$params = array($corpus_id);
		
		if ($subcorpus_id){
			$params[] = $subcorpus_id;
		}
		
		if ($class){
			$params[] = $class;
		}

		$phrases_sql = null;
		if ($phrases !== null ){
			$phrases_sql = array();
			foreach ( $phrases as $p ){
				$phrases_sql[] = " b.text LIKE ? ";
				$params[] = $p;
			}
			$phrases_sql = implode(" OR ", $phrases_sql);
		}
		
		$sql = "SELECT base_id AS id, b.text AS base, tto.pos, COUNT(DISTINCT t.token_id) AS c, COUNT(DISTINCT r.id) AS docs" .
				" FROM tokens t" .
				" JOIN reports r ON (t.report_id=r.id)" .
				" JOIN tokens_tags_optimized tto USING (token_id)" .
				" JOIN bases b ON (b.id = tto.base_id)" .
				" WHERE r.corpora = ?" .
				($subcorpus_id ? " AND r.subcorpus_id = ?" : "") .
				($class ? " AND tto.pos = ?"  : "") . 
				($disamb ? " AND tto.disamb = 1" : "") .
				($phrases_sql ? " AND $phrases_sql" : "").
				" GROUP BY tto.base_id" .
				" ORDER BY c DESC" .
				($limit_from !== null && $limit_by!==null ? " LIMIT $limit_from, $limit_by" : "");
		
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
			" GROUP BY tto.base_id, r.subcorpus_id";
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
	
	/**
	 * 
	 * @param unknown $corpus_id
	 * @param unknown $annotation_type_id
	 * @param unknown $limit_from
	 * @param unknown $limit_by
	 */
	function getAnnotationFrequency($corpus_id, $subcorpus_id, $annotation_set_id=null, $annotation_type_id=null, $phrases=null, $stage=null, $limit_from=null, $limit_by=null){
		global $db;
		
		$params = array($corpus_id);

		if ($subcorpus_id) $params[] = $subcorpus_id;
		if ($annotation_set_id) $params[] = $annotation_set_id;
		if ($annotation_type_id) $params[] = $annotation_type_id;
		if ($stage) $params[] = $stage;
		
		$phrases_sql = null;
		if ($phrases !== null && count($phrases) > 0 ){
			$phrases_sql = array();
			foreach ( $phrases as $p ){
				$phrases_sql[] = " an.text LIKE ? ";
				$params[] = $p;
			}
			$phrases_sql = implode(" OR ", $phrases_sql);			
		}
		
		$sql = "SELECT an.text, at.name AS type_name, COUNT(DISTINCT an.id) AS c, COUNT(DISTINCT an.report_id) AS docs".
				" FROM reports_annotations_optimized an".
				" JOIN reports r ON (r.id = an.report_id)".
				" JOIN annotation_types at ON (at.annotation_type_id = an.type_id)".
				" WHERE r.corpora = ?".
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				( $annotation_set_id ? " AND at.group_id=?" : "").
				( $annotation_type_id ? " AND an.type_id = ?" : "").
				( $stage ? " AND an.stage = ?" : "").
				( $phrases_sql != null ? " AND ( $phrases_sql ) " : "") .
				" GROUP BY an.text, an.type_id".
				" ORDER BY c DESC".
				($limit_from !== null && $limit_by!==null ? " LIMIT $limit_from, $limit_by" : "");
		fb($sql);
		$rows = $db->fetch_rows($sql, $params);
				
		foreach ($rows as &$r){
			$r['no'] = ++$limit_from;
		}
				
		return $rows;
	}
	
	static function getUniqueAnnotationCount($corpus_id, $subcorpus_id, $annotation_set_id=null, $annotation_type_id=null, $phrases=null, $stage=null){
		global $db;
	
		$params = array($corpus_id);
	
		if ($subcorpus_id) $params[] = $subcorpus_id;
		if ($annotation_set_id) $params[] = $annotation_set_id;
		if ($annotation_type_id) $params[] = $annotation_type_id;
		if ($stage) $params[] = $stage;
		
		$phrases_sql = null;
		if ($phrases !== null ){
			$phrases_sql = array();
			foreach ( $phrases as $p ){
				$phrases_sql[] = " an.text LIKE ? ";
				$params[] = $p;
			}
			$phrases_sql = implode(" OR ", $phrases_sql);
		}
		
		$sql = "SELECT COUNT(DISTINCT an.text, an.type_id)".
				" FROM reports_annotations_optimized an".
				" JOIN reports r ON (r.id = an.report_id)".
				" JOIN annotation_types at ON (at.annotation_type_id = an.type_id)".
				" WHERE r.corpora = ?".
				( $subcorpus_id ? " AND subcorpus_id=?" : "") .
				( $annotation_set_id ? " AND at.group_id=?" : "").
				( $annotation_type_id ? " AND an.type_id = ?" : "").
				( $stage ? " AND an.stage = ?" : "").
				( $phrases_sql != null ? " AND ( $phrases_sql ) " : "");

		return $db->fetch_one($sql, $params);
	}

	/**
	 * Return
	 */
	static function getAnnotationFrequencesPerSubcorpus($corpus_id, $ans_keys=null, $annotation_stage=null){
		global $db;
	
		$params = array();
		$params[] = $corpus_id;
		if ( $annotation_stage ) $params[] = $annotation_stage;
		if ($class){
			$params[] = $class;
		}
	
		$texts_sql = null;
		if ( $ans_keys != null && is_array($ans_keys) ){
			$ans_keys = array_filter($ans_keys);
			if ( count($ans_keys)>0 ){
				$texts_sql = " CONCAT(an.text,':',at.name) IN (". implode(",", array_fill(0,  count($ans_keys), "?")) .")";
				$params = array_merge($params, $ans_keys);
			}
		}
	
		$sql = "SELECT CONCAT(an.text,':',at.name) as text, r.subcorpus_id, COUNT(DISTINCT an.id) AS c, COUNT(DISTINCT r.id) AS docs".
				" FROM reports_annotations_optimized an".
				" JOIN reports r ON (an.report_id=r.id)".
				" JOIN annotation_types at ON (at.annotation_type_id = an.type_id)".
				" WHERE r.corpora = ?".
				( $annotation_stage ? " AND an.stage = ?" : "").
				( $texts_sql ? " AND " .$texts_sql : "") .
				" GROUP BY an.text, an.type_id, r.subcorpus_id";
				
		return $db->fetch_rows($sql, $params);
	}

    static function _getStats($corpus_id, $session){
        global $db;

        $params = array($corpus_id);
        $ext_table = DbCorpus::getCorpusExtTable($corpus_id);
        $filters = $session;

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array();
            $params[] = intval($filters['flags']['flag']);
            $params[] = $corpus_id;
            $params[] = intval($filters['flags']['flag_status']);
        } else{
            $flag_active = false;
        }

        $where_metadata = "";
        $sql_metadata = "";
        if(isset($filters['metadata'])){
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

        if ( $filters['status'] && $filters['status'] != '0'){
            ChromePhp::log("Status");
            $params[] = intval($filters['status']);
            $status = true;
        } else{
            $status = false;
        }


        $report_count = 0;
        $token_count = 0;
        $char_count = 0;
        $all_char_count = 0;
        $stats = array();

        $sql = "SELECT r.content, r.subcorpus_id, IFNULL(s.name, '[unassigned]') AS subcorpus_name" .
            " FROM reports r " .
            $sql_metadata.
            ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
            " LEFT JOIN corpus_subcorpora s USING (subcorpus_id)" .
            " WHERE r.corpora=?" .
            ($flag_active ? " AND rf.flag_id = ? " : "") .
            ( $status ? " AND r.status = ? " : "")
            .$where_metadata.
            " ORDER BY subcorpus_name";

        foreach ($db->fetch_rows($sql, $params) as $row){

            $content = $row['content'];
            $content = strip_tags($content);

            preg_match_all("/(\pL|\pM|\pN)+/", $content, $m);
            $tokens_count = count($m[0]);

            $chars_count = mb_strlen(str_replace(" ", "", $content));
            $subcorpus_id = $row['subcorpus_id'];

            if ( isset($stats[$subcorpus_id]) ){
                $stats[$subcorpus_id]['documents']++;
                $stats[$subcorpus_id]['words'] += $tokens_count;
                $stats[$subcorpus_id]['chars'] += $chars_count;
            }else{
                $stats[$subcorpus_id] = array(
                    'name' => $row['subcorpus_name'],
                    'documents' => 1,
                    'words' => $tokens_count,
                    'chars' => $chars_count
                );
            }
        }

        $sql = "SELECT r.subcorpus_id, COUNT(t.token_id) AS tokens" .
            " FROM reports r" .
            $sql_metadata.
            ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
            " JOIN tokens t ON (t.report_id = r.id)" .
            " WHERE r.corpora=?" .
            ($flag_active ? " AND rf.flag_id = ? " : "") .
            ( $status ? " AND r.status = ? " : "")
            .$where_metadata.
            " GROUP BY r.subcorpus_id ";

        foreach ($db->fetch_rows($sql, $params) as $row){
            $stats[$row['subcorpus_id']]['tokens'] = $row['tokens'];
        }

        $documents = 0;
        $words = 0;
        $chars = 0;
        $tokens = 0;

        foreach ($stats as $k=>$s){
            $documents += $s['documents'];
            $words += $s['words'];
            $chars += $s['chars'];
            $tokens += $s['tokens'];
        }
        $stats['summary'] = array( "documents"=>$documents, "words"=>$words,
            "chars"=>$chars, "tokens"=>$tokens);
        return $stats;
    }
}

?>
