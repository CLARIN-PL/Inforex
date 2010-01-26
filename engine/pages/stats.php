<?php

class Page_stats extends CPage{
	
	function execute(){
		global $mdb2;
		$this->set('checked', $this->_getStats("SELECT content FROM reports WHERE status=2"));
		$this->set('all', $this->_getStats("SELECT content FROM reports"));

		$sql = "SELECT count(*) FROM reports r JOIN reports_annotations a ON (r.id = a.report_id) WHERE status=2";
		$annotation_count = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT type, COUNT(*) AS count FROM reports_annotations GROUP BY type ORDER BY count desc;";
		$annotations_count = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);

		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
		$this->set('tags', $annotations_count);		
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
			
			$content = str_replace(" ", "", $content);
			$char_count += strlen($content);
			$report_count++;
		}
				
		$stats = array();
		$stats['report_count'] = number_format($report_count, 0, "", "."); 
		$stats['token_count'] =  number_format($token_count, 0, "", ".");
		$stats['char_count'] =  number_format($char_count, 0, "", ".");
		return $stats;
	}	
}

?>


