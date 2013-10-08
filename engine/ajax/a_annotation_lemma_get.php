<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_lemma_get extends CPage {
	var $isSecure = true;
	
 	function checkPermission(){
 		if (hasRole(USER_ROLE_ADMIN) || hasPerspectiveAccess("annotation_lemma"))
 			return true;
 		else
 			return "Brak prawa do edycji.";
 	}
	
	function execute(){
		$report_id = intval($_POST['report_id']);
		$types = $_POST['annotation_types'];
		
		if(!$types || empty($types) || count($types) <= 0){
			throw new Exception("No annotation types provided");
		}
		
		
		$annotations = DbAnnotation::getReportAnnotationsByTypes($report_id, $types);
		$annotations = $this->orderBySentenceNumbers($report_id, $annotations);
		
		return $annotations;
	}

	function orderBySentenceNumbers($report_id, $annotations){
		
		$annotations_ordered = array();
		
		$report = DbReport::getReportById($report_id);
		$content = $report['content'];

		$reportHtml = new HtmlStr2($content);
		$sentencePositions = $reportHtml->getSentencesPositions();
		$sentenceCount = count($sentencePositions);
		$currentSentenceIndex = 0;
		
		
		foreach($annotations as $annotation){
			$from = $annotation['from'];
			// Ustaw indeks bieżącego zdania
			while($currentSentenceIndex < $sentenceCount-1 && $sentencePositions[$currentSentenceIndex+1] <= $from){
				$currentSentenceIndex++;
			}
			$annotation['local_from'] = $from - $sentencePositions[$currentSentenceIndex];
			
			if(is_array($annotations_ordered[$currentSentenceIndex])){
				$annotations_ordered[$currentSentenceIndex][] = $annotation;
			}else{
				$annotations_ordered[$currentSentenceIndex] = array($annotation);
			}
			
		}
		
		
		return $annotations_ordered;
	}
	
}
?>
