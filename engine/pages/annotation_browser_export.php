<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_annotation_browser_export extends CPage{
	
	var $isSecure = false;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_READ) || $corpus['public'];
	}
		
	function execute(){
		global $db, $user, $corpus;
		
		$time_start 		= microtime(true); 
		$corpus_id 			= $corpus['id'];
		$annotation_type_id = $_GET['annotation_type_id'];
		$annotation_orth    = strval($_GET['annotation_orth']);
		$annotation_lemma   = strval($_GET['annotation_lemma']);
		$annotation_stage = strval($_GET['annotation_stage']);
		$sortName			= $_GET['sortname']; 
		$sortOrder			= $_GET['sortorder'];
		$pageElements		= max(1, intval($_GET['rp']));
		$page				= max(1, intval($_GET['page']));	
		$cid				= $_GET['corpus'];

		$sql = "SELECT an.*, t.name AS type" .
				" FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
				($annotation_lemma ? " JOIN reports_annotations_lemma l ON (an.id = l.report_annotation_id)" : "") .
				" WHERE r.corpora = ?" .
				($annotation_type_id ? " AND an.type_id = ?" : "") .
				($annotation_orth ? " AND an.text = ? " : "") .
				($annotation_lemma ? " AND l.lemma = ? " : "") .
				($annotation_stage ? " AND an.stage = ? " : "") .
				" ORDER BY an.report_id, an.from, an.to";
				
		$params = array($corpus_id);
		if ( $annotation_type_id ){
			$params[] = $annotation_type_id;
		}
		if ( $annotation_orth ){
			$params[] = $annotation_orth;
		}
		if ( $annotation_lemma ){
			$params[] = $annotation_lemma;
		}
		if ( $annotation_stage ){
			$params[] = $annotation_stage;
		}

		$rows = $db->fetch_rows($sql, $params);
        $items = array();     
		$html = null;
		$last_report_id = null;
           
		foreach ($rows as $row){
			
			$from = $row['from'];
			$to = $row['to'];
			
			if ( $last_report_id != $row['report_id']){
				$content = $db->fetch_one("SELECT content FROM reports WHERE id = ?", array($row['report_id']));
				$last_report_id = $row['report_id'];
				$html = new HtmlStr2($content);
			}
			
			$left = $html->getTextAlign($from-50, $from-1, true, false);
			$right = $html->getTextAlign($to+1, $to+50, false, true);
			
			$stage = $row['stage'];
			
			$cells = array(
				"id" => $row['id'], 
				"report_id" => $row['report_id'], 
				"annotation" => $this->cleanText($row['text']),
				"source" => $row['source'],
				"left" => $this->cleanText($left),
				"right" => $this->cleanText($right),
				"stage" => $stage,
				"type" => $row['type']);
				
	       	$items[] = $cells;
	       	
	       	if ( microtime(true) - $time_start > 20 ){
	       		$this->set("interupted", 1);
	       		break;	       		
	       	}
		}
		$this->set("rows", $items);
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="annotations.csv"');		
	}
		
	function cleanText($text){
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = str_replace("\t", " ", $text);
		return $text;
	}
}


?>
