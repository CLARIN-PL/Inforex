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
		$sortName			= $_POST['sortname']; 
		$sortOrder			= $_POST['sortorder'];
		$pageElements		= max(1, intval($_POST['rp']));
		$page				= max(1, intval($_POST['page']));	
		$cid				= $_POST['corpus'];

		$limitStart = intval(($page - 1) * $pageElements);
		$limitCount = intval($pageElements);

		
		$sql = "SELECT an.*, r.content FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE r.corpora = ? AND an.type_id = ?" .
				" ORDER BY an.report_id, an.from, an.to" .
				" LIMIT ?,?";

		$params = array($corpus_id, $annotation_type_id, $limitStart, $limitCount);
		$rows = $db->fetch_rows($sql, $params);
        $result = array();        
		foreach ($rows as $row){
			
			$from = $row['from'];
			$to = $row['to'];
			
			$html = new HtmlStr2($row['content']);
			$left = $html->getTextAlign($from-50, $from-1);
			$right = $html->getTextAlign($to+1, $to+50);
						
			$cells = array(
				"report_id" => $row['report_id'], 
				"annotation" => "<b>".$row['text']."</b>",
				"left" => $left,
				"right" => $right);
				
	       	$result[] = array('id' => $row['id'], 'cell' => $cells);
		}

        $total = $db->fetch_one("SELECT COUNT(*) FROM reports_annotations_optimized an" .
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE r.corpora = ? AND an.type_id = ?", array($corpus_id, $annotation_type_id));        

        // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $result));
		die;
	}
}

?>
