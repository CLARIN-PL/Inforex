<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_annotation_contexts_export extends CPageCorpus{

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_BROWSE_ANNOTATIONS;
    }

	function execute(){
		global $db, $corpus;
		
		$time_start 		= microtime(true); 
		$corpus_id 			= $corpus['id'];
		$annotation_type_id = isset($_GET['annotation_type_id']) ? intval($_GET['annotation_type_id']) : 0;
		$annotation_orth    = isset($_GET['annotation_orth']) ? strval($_GET['annotation_orth']) : '';
		$annotation_lemma   = isset($_GET['annotation_lemma']) ? strval($_GET['annotation_lemma']) : '';
		$annotation_stage = isset($_GET['annotation_stage']) ? strval($_GET['annotation_stage']) : '';
		$this->set("interupted", 0);

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
				$report = $db->fetch("SELECT r.content, f.format FROM reports r" .
					" JOIN reports_formats f ON (r.format_id = f.id) WHERE r.id = ?", array($row['report_id']));
				$last_report_id = $row['report_id'];
				$html = ReportContent::getHtmlStr($report);
			}
			
			$left = $html->getTextAlign($from-50, $from-1, true, false);
			$right = $html->getTextAlign($to+1, $to+50, false, true);
			
			$stage = $row['stage'];
			
			$cells = array(
				"id" => $row['id'], 
				"report_id" => $row['report_id'], 
				"annotation" => $this->cleanText($row['text']),
				"source" => $row['source'],
				"left" => $this->cleanText(html_entity_decode($left, ENT_QUOTES | ENT_HTML5, 'UTF-8')),
				"right" => $this->cleanText(html_entity_decode($right, ENT_QUOTES | ENT_HTML5, 'UTF-8')),
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
