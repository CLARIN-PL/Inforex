<?php
class Ajax_tests_integrity extends CPage {
	function execute(){
		global $db;
		
		$test_name = $_POST['name'];
		$test_from = $_POST['from'];
		$test_to = $_POST['to'];
		$corpus_id = $_POST['corpus_id'];
		$error_num = $_POST['error_num'];
		$result_lists = array();
				
		// Chunki
		if($test_name == 'empty_chunk'){
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id, content ');
			foreach($corpus_reports as $report){			
				$empty_chunks = CclIntegrity::checkChunks($report['content']);
				if($empty_chunks['count']){
					$report_id = $report['id'];
					$empty_chunks_count = $empty_chunks['count'];
					$error_num++;
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $empty_chunks_count, "test_result" => $empty_chunks['data']); 
				}
			}
		}
		
		if($test_name == 'wrong_chunk'){
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id, content ');
			foreach($corpus_reports as $report){			
				$empty_chunks = CclIntegrity::checkXSDContent($report['content']);
				if($empty_chunks['count']){
					$report_id = $report['id'];
					$empty_chunks_count = $empty_chunks['count'];
					$error_num++;
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $empty_chunks_count, "test_result" => $empty_chunks['data']); 
				}
			}
		}
		
		// Tokeny	
		if($test_name == 'wrong_tokens'){	
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id ');
			foreach($corpus_reports as $report){	
				$tokens_list = DbToken::getTokenByReportId($report['id']);
				$count_wrong_tokens = TokensIntegrity::checkTokens($tokens_list);
				if($count_wrong_tokens['count']){
					$report_id = $report['id'];
					$wrong_tokens_count = $count_wrong_tokens['count'];
					$error_num++;	
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $wrong_tokens_count, "test_result" => $count_wrong_tokens['data']);				
				}
			}
		}
		
		if($test_name == 'tokens_out_of_scale'){	
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id, content ');
			foreach($corpus_reports as $report){	
				$tokens_list = DbToken::getTokenByReportId($report['id']);
				$count_wrong_tokens = TokensIntegrity::checkTokensScale($tokens_list,$report['content']);	
				if($count_wrong_tokens['count']){
					$report_id = $report['id'];
					$wrong_tokens_count = $count_wrong_tokens['count'];
					$error_num++;					
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $wrong_tokens_count, "test_result" => $count_wrong_tokens['data']);
				}
			}
		}
		
		// Anotacje			
		if($test_name == 'wrong_annotations'){	
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id ');
			foreach($corpus_reports as $report){	
				$tokens_list = DbToken::getTokenByReportId($report['id']);	
				$annotations_list = DbAnnotation::getAnnotationByReportId($report['id']);
				$count_wrong_annotations = AnnotationsIntegrity::checkAnnotationsByTokens($annotations_list, $tokens_list);	
				if($count_wrong_annotations['count']){
					$report_id = $report['id'];
					$wrong_annotations_count = $count_wrong_annotations['count'];
					$error_num++;
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $wrong_annotations_count, "test_result" => $count_wrong_annotations['data']);					
				}
			}
		}
		
		if($test_name == 'wrong_annotations_by_annotation'){	
			$corpus_reports = DbReport::getReportsByCorpusIdLimited($corpus_id,$test_from,$test_to,' id ');
			$rows = DbAnnotation::getAnnotationTypes('name, group_id');
			$annotations_types = array();
			foreach($rows as $row){
				$annotations_types[$row['name']] = $row['group_id']; 	
			}
			foreach($corpus_reports as $report){	
				$annotations_list = DbAnnotation::getAnnotationByReportId($report['id']);
				$count_wrong_annotations = AnnotationsIntegrity::checkAnnotationsByAnnotation($annotations_list,$annotations_types);	
				if($count_wrong_annotations['count']){
					$report_id = $report['id'];
					$wrong_annotations_count = $count_wrong_annotations['count'];
					$error_num++;
					$result_lists[] = array("error_num" => $error_num, "report_id" => $report_id, "wrong_count" => $wrong_annotations_count, "test_result" => $count_wrong_annotations['data']);					
				}
			}
		}		
					
		echo json_encode(array("success" => 1, "data" => $result_lists, "error_num" => $error_num));
	}	
}
?>