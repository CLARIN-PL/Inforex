<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_stats extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}
		
	function execute(){
		global $mdb2, $corpus;
		$this->set('stats', $this->_getStats($corpus['id']));
	}

	function _getStats($corpus_id){
		global $db;

		$report_count = 0;
		$token_count = 0;
		$char_count = 0;
		$all_char_count = 0;		
		$stats = array();

		$sql = "SELECT r.content, r.subcorpus_id, IFNULL(s.name, '[unassigned]') AS subcorpus_name" .
							" FROM reports r" .
							" LEFT JOIN corpus_subcorpora s USING (subcorpus_id)" .
							" WHERE r.corpora=?" .
							"   AND r.status=2" .
							" ORDER BY subcorpus_name";

		foreach ($db->fetch_rows($sql, array($corpus_id)) as $row){
									
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
				" JOIN tokens t ON (t.report_id = r.id)" .
				" WHERE r.corpora=?" .
				"   AND r.status=2" .
				" GROUP BY r.subcorpus_id ";

		foreach ($db->fetch_rows($sql, array($corpus_id)) as $row){			
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


