<?php

class Page_stats extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2, $corpus;
		$this->set('checked', $this->_getStats("SELECT content FROM reports WHERE corpora={$corpus['id']} AND status=2"));
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
		
		while ($row = mysql_fetch_array($r->result)){
			$content = $row['content'];
			$content = strip_tags($content);
			$content = str_replace("\n\r", " ", $content);
			$content = str_replace("\r\n", " ", $content);
			$content = str_replace("\r", " ", $content);
			$content = str_replace("\n", " ", $content);
			$content = str_replace("\t", " ", $content);
			$tokens = explode(" ", $content);
			$token_count += count($tokens);
			
			$all_char_count += strlen($content); 
			$content = str_replace(" ", "", $content);
			$char_count += strlen($content);
			$report_count++;
		}
				
		$stats = array();
		$stats['report_count'] = number_format($report_count, 0, "", " "); 
		$stats['token_count'] =  number_format($token_count, 0, "", " ");
		$stats['char_count'] =  number_format($char_count, 0, "", " ");
		$stats['avg_length'] =  number_format($char_count/$report_count, 0, "", " ");
		$stats['avg_tokens'] =  number_format($token_count/$report_count, 0, "", " ");
		$stats['size'] =  number_format($all_char_count/(1024*1024), 2, ".", " ");
		return $stats;
	}	
}

?>


