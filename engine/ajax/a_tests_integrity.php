<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_tests_integrity extends CPage {
	private $result_lists = array();
	private $error_num;
	private $annotations_types = array();
	private $active_annotations_type = array();
	
// --- Ajax execute function	
	function execute(){
		$test_name = $_POST['name'];
		$this->active_annotations_type = $_POST['annotations_active'];
		$this->error_num = $_POST['error_num'];
		$corpus_reports = DbReport::getReportsByCorpusIdLimited($_POST['corpus_id'], $_POST['from'], $_POST['to'],
						' id'.($test_name == 'tokens_out_of_scale' || 
								$test_name == 'empty_chunk' ||
								$test_name == 'wrong_chunk' ||
								$test_name == 'wrong_annotations_by_sentence'
								? ',content ' : ' '));
								
		foreach($corpus_reports as $report){
			$count_wrongs = $this->$test_name($report);
			if($count_wrongs['count'])
				$this->result_lists[] = array("error_num" => $this->error_num++, "report_id" => $report['id'], "wrong_count" => $count_wrongs['count'], "test_result" => $count_wrongs['data']);
		}
		return $this->echo_result(); 
	}	
	
// --- test functions	
	function empty_chunk($report){
		return CclIntegrity::checkChunks($report['content']);		
	}
	
	function wrong_chunk($report){
		return CclIntegrity::checkXSDContent($report['content']);
	}
	
	function wrong_tokens($report){
		$tokens_list = DbToken::getTokenByReportId($report['id']);
		return TokensIntegrity::checkTokens($tokens_list);
	}
	
	function tokens_out_of_scale($report){
		$tokens_list = DbToken::getTokenByReportId($report['id']);
		return TokensIntegrity::checkTokensScale($tokens_list,$report['content']);	
	}
	
	function wrong_annotations($report){
		$tokens_list = DbToken::getTokenByReportId($report['id']);	
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),$this->active_annotations_type);
		return AnnotationsIntegrity::checkAnnotationsByTokens($annotations_list, $tokens_list);	
	}
	
	function wrong_annotations_by_annotation($report){
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),$this->active_annotations_type);
		if(!count($this->annotations_types))
			$this->set_annotations_types();
		return AnnotationsIntegrity::checkAnnotationsByAnnotation($annotations_list,$this->annotations_types);	
	}

	function wrong_annotations_duplicate($report){
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),$this->active_annotations_type);
		return AnnotationsIntegrity::checkAnnotationsDuplicate($annotations_list);	
	}

	function wrong_annotation_in_annotation($report){
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),$this->active_annotations_type);
		return AnnotationsIntegrity::checkAnnotationInAnnotation($annotations_list);	
	}

	function wrong_annotation_chunks_type($report){
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),null,array("chunk_np", "chunk_adjp", "chunk_vp", "chunk_agp", "chunk_qp"));
		return AnnotationsIntegrity::checkAnnotationChunkType($annotations_list);	
	}
	
	function wrong_annotations_by_sentence($report){
		$annotations_list = DbAnnotation::getAnnotationsBySets(array($report['id']),$this->active_annotations_type);
		$tokens_list = DbToken::getTokenByReportId($report['id']);
		return AnnotationsIntegrity::checkAnnotationsBySentence($annotations_list, $report['content'], $tokens_list);	
	}

// --- sets functions	
	function set_annotations_types(){
		$rows = DbAnnotation::getAnnotationTypes('name, group_id');
		foreach($rows as $row){
			$this->annotations_types[$row['name']] = $row['group_id']; 	
		}
	}	
	
// --- result function
	function echo_result(){
		return array("data" => $this->result_lists, "error_num" => $this->error_num);	
	}
}
?>