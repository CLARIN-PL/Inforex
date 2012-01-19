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
					$result_lists['html'] .= '<tr class="tests_items empty_chunk">';
					$result_lists['html'] .= '	<td style="vertical-align: middle">' . $error_num . '</td>';
					$result_lists['html'] .= '	<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus=' . $corpus_id . '&amp;subpage=annotator&amp;id=' . $report_id . '">' . $report_id . '</a></td>';
					$result_lists['html'] .= '	<td colspan="2" style="vertical-align: middle">' . $empty_chunks_count . '</td>';							
					$result_lists['html'] .= '</tr>';
					foreach ($empty_chunks['data'] as $key => $chunk){
						if(isset($empty_chunks['data'][$key+1])){
							$result_lists['html'] .= '<tr class="tests_errors empty_chunk">';
							$result_lists['html'] .= '	<td colspan="3" class="empty"></td>';
							$result_lists['html'] .= '	<td style="vertical-align: middle">Pusty chunk: znajduje się w linii ' . $chunk . '</td>';
							$result_lists['html'] .= '</tr>';
						}
					}		
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
					$result_lists['html'] .= '<tr class="tests_items wrong_tokens">';
					$result_lists['html'] .= '	<td style="vertical-align: middle">' . $error_num . '</td>';
					$result_lists['html'] .= '	<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus=' . $corpus_id . '&amp;subpage=annotator&amp;id=' . $report_id . '">' . $report_id . '</a></td>';
					$result_lists['html'] .= '	<td colspan="2" style="vertical-align: middle">' . $wrong_tokens_count . '</td>';							
					$result_lists['html'] .= '</tr>';					
					foreach($count_wrong_tokens['data'] as $token){
						$token_id = $token['id'];
						$token_from = $token['from'];
						$token_to = $token['to'];
						$result_lists['html'] .= '<tr class="tests_errors wrong_tokens">';
						$result_lists['html'] .= '	<td colspan="3" class="empty"></td>';
						$result_lists['html'] .= '	<td style="vertical-align: middle">Dla tokenu o indeksie ' . $token_id . ' i zakesie [' . $token_from . ', ' . $token_to . '] nie istnieje token będący jego następnikiem</td>';
						$result_lists['html'] .= '</tr>';
					}
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
					$result_lists['html'] .= '<tr class="tests_items tokens_out_of_scale">';
					$result_lists['html'] .= '	<td style="vertical-align: middle">' . $error_num . '</td>';
					$result_lists['html'] .= '	<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus=' . $corpus_id . '&amp;subpage=annotator&amp;id=' . $report_id . '">' . $report_id . '</a></td>';
					$result_lists['html'] .= '	<td colspan="2" style="vertical-align: middle">' . $wrong_tokens_count . '</td>';							
					$result_lists['html'] .= '</tr>';					
					foreach($count_wrong_tokens['data'] as $token){
						$token_id = $token['id'];
						$token_from = $token['from'];
						$token_to = $token['to'];
						$token_content_length = $token['content_length'];
						$result_lists['html'] .= '<tr class="tests_errors tokens_out_of_scale">';
						$result_lists['html'] .= '	<td colspan="3" class="empty"></td>';
						$result_lists['html'] .= '	<td style="vertical-align: middle">Token o indeksie ' . $token_id . ' i zakesie [' . $token_from . ', ' . $token_to . '] wykracza poza ramy dokumentu o długości [' . $token_content_length . ']</td>';
						$result_lists['html'] .= '</tr>';
					}
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
					$result_lists['html'] .= '<tr class="tests_items wrong_annotations">';
					$result_lists['html'] .= '	<td style="vertical-align: middle">' . $error_num . '</td>';
					$result_lists['html'] .= '	<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus=' . $corpus_id . '&amp;subpage=annotator&amp;id=' . $report_id . '">' . $report_id . '</a></td>';
					$result_lists['html'] .= '	<td colspan="2" style="vertical-align: middle">' . $wrong_annotations_count . '</td>';							
					$result_lists['html'] .= '</tr>';					
					foreach($count_wrong_annotations['data'] as $annotation){
						$annotation_type = $annotation['annotation_type'];
						$annotation_id = $annotation['annotation_id'];
						$annotation_text = $annotation['annotation_text'];
						$annotation_from = $annotation['annotation_from'];
						$annotation_to = $annotation['annotation_to'];
						$token_id = $annotation['token_id'];
						$token_from = $annotation['token_from'];
						$token_to = $annotation['token_to'];
						$result_lists['html'] .= '<tr class="tests_errors wrong_annotations">';
						$result_lists['html'] .= '	<td colspan="3" class="empty"></td>';
						$result_lists['html'] .= '	<td style="vertical-align: middle">Anotacja: <span class="' . $annotation_type . '" title="an#' . $annotation_id . ':' . $annotation_type . '">' . $annotation_text . '</span> o zakresie [' . $annotation_from . ',' . $annotation_to . '] przecina się z tokenem o indeksie ' . $token_id . ' i zakesie [' . $token_from . ', ' . $token_to . ']</td>';
						$result_lists['html'] .= '</tr>';
					}
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
					$result_lists['html'] .= '<tr class="tests_items wrong_annotations_by_annotation">';
					$result_lists['html'] .= '	<td style="vertical-align: middle">' . $error_num . '</td>';
					$result_lists['html'] .= '	<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus=' . $corpus_id . '&amp;subpage=annotator&amp;id=' . $report_id . '">' . $report_id . '</a></td>';
					$result_lists['html'] .= '	<td colspan="2" style="vertical-align: middle">' . $wrong_annotations_count . '</td>';							
					$result_lists['html'] .= '</tr>';					
					foreach($count_wrong_annotations['data'] as $annotation){
						$annotation_type1 = $annotation['type1'];
						$annotation_type2 = $annotation['type2'];
						$annotation_id1 = $annotation['id1'];
						$annotation_id2 = $annotation['id2'];
						$annotation_text1 = $annotation['text1'];
						$annotation_text2 = $annotation['text2'];
						$result_lists['html'] .= '<tr class="tests_errors wrong_annotations_by_annotation">';
						$result_lists['html'] .= '	<td colspan="3" class="empty"></td>';
						$result_lists['html'] .= '	<td style="vertical-align: middle"><span class="' . $annotation_type1 . '" title="an#' . $annotation_id1 . ':' . $annotation_type1 . '">' . $annotation_text1 . '</span> <span class="' . $annotation_type2 . '" title="an#' . $annotation_id2 . ':' . $annotation_type2 . '">' . $annotation_text2 . '</span></td>';
						$result_lists['html'] .= '</tr>';
					}
				}
			}
		}		
					
		echo json_encode(array("success" => 1, "data" => $result_lists, "error_num" => $error_num));
	}	
}
?>