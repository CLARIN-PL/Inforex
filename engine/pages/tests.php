<?php
 
 /**
  * Strona z testami spójności
  */
 
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
		$wrong_annotations_by_annotation_lists = array();
		$annotations_types = array();
		
		
		$documents_in_corpus = DbReport::getReportsByCorpusId($corpus['id'],' count(*) AS count ');
/*				
		$corpus_reports = DbReport::getReportsByCorpusId($corpus['id'],' id, content ');	
		$rows = DbAnnotation::getAnnotationTypes('name, group_id');
		foreach($rows as $row){
			$annotations_types[$row['name']] = $row['group_id']; 	
		}
		
		// Dla wszystkich dokumentów w korpusie
		foreach($corpus_reports as $report){
			// Chunki
			$empty_chunks = CclIntegrity::checkChunks($report['content']);
			if($empty_chunks['count']){
				$empty_chunk_lists[] = array("document_id" => $report['id'], "count" => $empty_chunks['count'], "data" => $empty_chunks['data']);
			}
			
			// Tokeny			
			$tokens_list = DbToken::getTokenByReportId($report['id']);
			$count_wrong_tokens = TokensIntegrity::checkTokens($tokens_list);	
			if($count_wrong_tokens['count'])
				$wrong_tokens_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_tokens['count'], "data" => $count_wrong_tokens['data']);
				
			$count_wrong_tokens = TokensIntegrity::checkTokensScale($tokens_list,$report['content']);	
			if($count_wrong_tokens['count'])
				$tokens_out_of_scale_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_tokens['count'], "data" => $count_wrong_tokens['data']);

			// Anotacje				
			$annotations_list = DbAnnotation::getAnnotationByReportId($report['id']);
			$count_wrong_annotations = AnnotationsIntegrity::checkAnnotationsByTokens($annotations_list, $tokens_list);	
			if($count_wrong_annotations['count'])
				$wrong_annotations_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_annotations['count'], "data" => $count_wrong_annotations['data']);
				
			$count_wrong_annotations = AnnotationsIntegrity::checkAnnotationsByAnnotation($annotations_list,$annotations_types);	
			if($count_wrong_annotations['count'])
				$wrong_annotations_by_annotation_lists[] = array("document_id" => $report['id'], "count" => $count_wrong_annotations['count'], "data" => $count_wrong_annotations['data']);
		}
*/		
	
		$this->set('corpus_id',$corpus['id']);
		$this->set('documents_in_corpus',$documents_in_corpus[0]['count']);
		$this->set('reports_wrong_annotations',$wrong_annotations_lists);
		$this->set('reports_tokens_out_of_scale',$tokens_out_of_scale_lists);
		$this->set('reports_wrong_tokens',$wrong_tokens_lists);
		$this->set('reports_empty_chunk',$empty_chunk_lists);
		$this->set('reports_wrong_annotations_by_annotation',$wrong_annotations_by_annotation_lists);
		if(count($wrong_annotations_lists))	$this->set('count_reports_wrong_annotations',count($wrong_annotations_lists));
		if(count($tokens_out_of_scale_lists)) $this->set('count_reports_tokens_out_of_scale',count($tokens_out_of_scale_lists));
		if(count($wrong_tokens_lists)) $this->set('count_reports_wrong_tokens',count($wrong_tokens_lists));
		if(count($empty_chunk_lists)) $this->set('count_reports_empty_chunk',count($empty_chunk_lists));
		if(count($wrong_annotations_by_annotation_lists)) $this->set('count_reports_wrong_annotations_by_annotation',count($wrong_annotations_by_annotation_lists));				
	}	
 } 
?>
