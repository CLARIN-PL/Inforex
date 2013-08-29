<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_semquel_get_result extends CPage {
	var $isSecure = false;
	function execute(){
	
		global $config;
		$ids = $_POST['id_list'];		
		$db2 = new Database($config->relation_marks_db);
	
		$sql = " SELECT ans.begin as source_begin, " .
				" ans.end as source_end, " .
				" ant.begin as target_begin, " .
				" ant.end as target_end, " .
				" r.sentence_begin, " .
				" r.sentence_end, " .
				" t.content " .
				" FROM relations r " .
				" JOIN annotations ans ON r.annotation_source_id = ans.annotation_id " .
				" JOIN annotations ant ON r.annotation_target_id = ant.annotation_id " .
				" JOIN texts t ON t.text_id = r.text_id " .
				" WHERE r.relation_id IN ( ".$ids." ) " .
				" AND t.status LIKE 'ready' ";
		
		$result = $db2->fetch_rows($sql);
		$out = array();		
		foreach($result as $relation){
			$htmlStr =  new HtmlStr2($relation['content']);
			$htmlStr2 =  new HtmlStr2($htmlStr->getText($relation['sentence_begin'], $relation['sentence_end']));
			$htmlStr2->insertTag($relation['source_begin']-$relation['sentence_begin'],'<b>',$relation['source_end']-$relation['sentence_begin']+1,'</b>');
			$htmlStr2->insertTag($relation['target_begin']-$relation['sentence_begin'],'<b>',$relation['target_end']-$relation['sentence_begin']+1,'</b>');
			$out[] = $htmlStr2->getContent();
		}		
		return $out;
	}	
}
?>
