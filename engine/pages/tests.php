<?php
 
 class Page_tests extends CPage{
 	
 	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute()
	{
		global $corpus;
		$empty_chunk_lists = array();
		$wrong_tokens_lists = array();
		$tokens_out_of_scale_lists = array();
		$wrong_annotations_lists = array();
		
		$corpus_reports = DbReport::getReportsByCorpusId($corpus['id'],' id, content ');	
		// Dla wszystkich dokumentów w korpusie
		foreach($corpus_reports as $report){
			// Chunki
			$empty_chunks = CclIntegrity::checkChunks($report['content']);
			if($empty_chunks)
				$empty_chunk_lists[] = array("document_id" => $report['id'], "count" => $empty_chunks);

			// Tokeny			
			$tokens_list = DbToken::getTokenByReportId($report['id']);
			$count_wrong_tokens = TokensIntegrity::checkTokens($tokens_list);	
			if($count_wrong_tokens)
				$wrong_tokens_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_tokens);
				
			$count_wrong_tokens = TokensIntegrity::checkTokensScale($tokens_list,$report['content']);	
			if($count_wrong_tokens)
				$tokens_out_of_scale_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_tokens);

			// Anotacje				
			$annotations_list = DbAnnotation::getAnnotationByReportId($report['id']);
			$count_wrong_annotations = AnnotationsIntegrity::checkAnnotations($annotations_list, $tokens_list);	
			if($count_wrong_annotations)
				$wrong_annotations_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_annotations);
		}
		
	
		$this->set('corpus_id',$corpus['id']);
		$this->set('reports_wrong_annotations',$wrong_annotations_lists);
		$this->set('reports_tokens_out_of_scale',$tokens_out_of_scale_lists);
		$this->set('reports_wrong_tokens',$wrong_tokens_lists);
		$this->set('reports_empty_chunk',$empty_chunk_lists);
		if(count($wrong_annotations_lists))	$this->set('count_reports_wrong_annotations',count($wrong_annotations_lists));
		if(count($tokens_out_of_scale_lists)) $this->set('count_reports_tokens_out_of_scale',count($tokens_out_of_scale_lists));
		if(count($wrong_tokens_lists)) $this->set('count_reports_wrong_tokens',count($wrong_tokens_lists));
		if(count($empty_chunk_lists)) $this->set('count_reports_empty_chunk',count($empty_chunk_lists));		
	}	
 } 
?>
