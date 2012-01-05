<?php
 
 class Page_tests extends CPage{
 	
 	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute()
	{
		global $corpus;
		$test_order = 'empty_chunks';
		$empty_chunk_lists = array();
		
		$corpus_reports = DbTests::getCorpusReportIdsAndContent($corpus['id']);	
		foreach($corpus_reports as $report){
			$count_empty_chunks = 0;
			$chunk_list = explode('</chunk>', $report['content']);
			foreach ($chunk_list as $chunk){
				$chunk = str_replace("<"," <",$chunk);
				$chunk = str_replace(">","> ",$chunk);
				$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
				$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
				if($tmpStr2 == "")
					$count_empty_chunks++;							
			}
			if($count_empty_chunks > 1)
				$empty_chunk_lists[] = array("document_id" => $report['id'], "count" => $count_empty_chunks-1);
		}
		
		
		
		
		$this->set('corpus_id',$corpus['id']);
		$this->set('reports',$empty_chunk_lists);
		$this->set('test_order',$test_order);
	}	
 } 
?>
