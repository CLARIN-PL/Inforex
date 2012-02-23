<?php

class Page_stats extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}
		
	function execute(){
		global $mdb2, $corpus;
		$this->set('stats', $this->_getStats("SELECT r.content, r.subcorpus_id, s.name AS subcorpus_name" .
							" FROM reports r" .
							" JOIN corpus_subcorpora s USING (subcorpus_id)" .
							" WHERE r.corpora={$corpus['id']}" .
							"   AND r.status=2" .
							" ORDER BY subcorpus_name"));
		//$this->set('all', $this->_getStats("SELECT content FROM reports"));
	}

	function _getStats($sql){
		global $mdb2;

		$r = $mdb2->query($sql);
		if (PEAR::isError($r)){
			die ("<pre>".$r->getUserId()."</pre>");
		}
		
		$report_count = 0;
		$token_count = 0;
		$char_count = 0;
		$all_char_count = 0;
		
		/** Tablica na statystyki */
		$stats = array();
		
		while ($row = mysql_fetch_array($r->result)){
									
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
		
		$documents = 0;
		$words = 0;
		$chars = 0;
		
		foreach ($stats as $k=>$s){
			$documents += $s['documents'];
			$words += $s['words'];
			$chars += $s['chars'];
		}
		$stats['summary'] = array( "documents"=>$documents, "words"=>$words, "chars"=>$chars);
				
		//$stats = array();
		//$stats['report_count'] = number_format($report_count, 0, "", " "); 
		//$stats['token_count'] =  number_format($token_count, 0, "", " ");
		//$stats['char_count'] =  number_format($char_count, 0, "", " ");
		//$stats['avg_length'] =  number_format($char_count/$report_count, 0, "", " ");
		//$stats['avg_tokens'] =  number_format($token_count/$report_count, 0, "", " ");
		//$stats['size'] =  number_format($all_char_count/(1024*1024), 2, ".", " ");
		
		
		
		return $stats;
	}	
}

?>


