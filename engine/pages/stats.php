<?php

class Page_stats extends CPage{
	
	function execute(){
		global $mdb2;

		$sql = "SELECT content FROM reports WHERE status=2";
		$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
		
		$token_count = 0;
		$char_count = 0;
		
		foreach ($rows as $row){
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
		}
		
		$sql = "SELECT count(*) FROM reports r JOIN reports_annotations a ON (r.id = a.report_id) WHERE status=2";
		$annotation_count = $mdb2->query($sql)->fetchOne();
		
		
		$this->set('report_count', number_format(count($rows), 0, "", "."));
		$this->set('token_count', number_format($token_count, 0, "", "."));
		$this->set('char_count', number_format($char_count, 0, "", "."));
		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
	}
	
}

?>


