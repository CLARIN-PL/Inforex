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
		$wrong_tokens_lists = array();
		
		$corpus_reports = DbReport::getReportsByCorpusId($corpus['id'],' id, content ');	
		foreach($corpus_reports as $report){
			// Chunki
			$empty_chunks = CclIntegrity::checkChunks($report['content']);
			if($empty_chunks)
				$empty_chunk_lists[] = array("document_id" => $report['id'], "count" => $empty_chunks);

			// Tokeny			
			$tokens_list = DbToken::getTokenByReportId($report['id']);
			$count_wrong_tokens = TokensIntegrity::checkTokens($tokens_list);	
			if($count_wrong_tokens > 0)
				$wrong_tokens_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_tokens);

			// Anotacje				
		}
		
	
		$this->set('corpus_id',$corpus['id']);
		$this->set('reports',$wrong_tokens_lists);//$empty_chunk_lists);
		$this->set('test_order',$test_order);
	}	
 } 
?>
