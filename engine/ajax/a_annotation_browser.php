<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_browser extends CPage {
	
	var $isSecure = false;
	function execute(){
		global $corpus, $db;
				
		$corpus_id = $_POST['corpus_id'];
		$annotation_type_id = $_POST['annotation_type_id'];
		$annotation_orth    = strval($_POST['annotation_orth']);
		$annotation_lemma   = strval($_POST['annotation_lemma']);
		$annotation_stage   = strval($_POST['annotation_stage']);
		$sortName			= $_POST['sortname']; 
		$sortOrder			= $_POST['sortorder'];
		$pageElements		= max(1, intval($_POST['rp']));
		$page				= max(1, intval($_POST['page']));	
		$cid				= $_POST['corpus'];

		$limitStart = intval(($page - 1) * $pageElements);
		$limitCount = intval($pageElements);
		
		$sql = "SELECT an.*, r.content" .
				" FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				($annotation_lemma ? " JOIN reports_annotations_lemma l ON (an.id = l.report_annotation_id)" : "") .
				" WHERE r.corpora = ?" .
				" AND an.type_id = ?" .
				($annotation_orth ? " AND an.text = ? " : "") .
				($annotation_lemma ? " AND l.lemma = ? " : "") . 
				($annotation_stage ? " AND an.stage = ? " : "") .
				" ORDER BY an.report_id, an.from, an.to" .
				" LIMIT ?,?";

		$params = array($corpus_id, $annotation_type_id);
		if ( $annotation_orth ){
			$params[] = $annotation_orth;
		}
		if ( $annotation_lemma ){
			$params[] = $annotation_lemma;
		}
		if ( $annotation_stage ){
			$params[] = $annotation_stage;
		}
		
		$rows = $db->fetch_rows($sql, array_merge($params, array($limitStart, $limitCount)));
        $result = array();        
		foreach ($rows as $row){
			
			$from = $row['from'];
			$to = $row['to'];
			
			try{
				$html = new HtmlStr2($row['content']);
				$left = $html->getTextAlign($from-50, $from-1, true, false);
				$right = $html->getTextAlign($to+1, $to+50, false, true);				
			}
			catch(Exception $ex){
				$left = $ex->getMessage();
				$right = $ex->getMessage();
			}
			
			$stage = $row['stage'];
			if ( $stage == 'new' ){
				$stage = sprintf('<a href="?corpus=%d&page=report&subpage=autoextension&id=%d" target="_blank" title="Verify annotation">new</a>',
							$corpus_id, $row['report_id']);
			}			
			
			$cells = array(
				"report_id" => sprintf('<a href="?page=report&subpage=preview&id=%d" target="_blank">%d</a>', $row['report_id'], $row['report_id']), 
				"annotation" => "<b>".$row['text']."</b>",
				"source" => $row['source'],
				"left" => $left,
				"right" => $right,
				"stage" => $stage);
				
	       	$result[] = array('id' => $row['id'], 'cell' => $cells);
		}

        $total = $db->fetch_one("SELECT COUNT(*)" .
        		" FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				($annotation_lemma ? " JOIN reports_annotations_lemma l ON (an.id = l.report_annotation_id)" : "") .
				" WHERE r.corpora = ?" .
				" AND an.type_id = ?" .
				($annotation_orth ? " AND an.text = ? " : "") .
				($annotation_lemma ? " AND l.lemma = ? " : "") .
				($annotation_stage ? " AND an.stage = ? " : "")
				, 
				$params);        

        // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $result));
		die;
	}
}

?>
