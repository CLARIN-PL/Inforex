<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_add_annotation extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}

		$type = strval($_POST['type']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$report_id = intval($_POST['report_id']);
		$context = $_POST['context'];
		$error = null;

		$content = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
		$content = normalize_content($content);
		
		$html = new HtmlStr2($content, true);
		$text_revalidate = $html->getText($from, $to);

		//$html_revalidate = html_entity_decode($html_revalidate, ENT_COMPAT, "UTF-8");
		$html_revalidate = custom_html_entity_decode($text_revalidate);
		
		if ( preg_replace("/\n+|\r+|\s+/","",$text) != preg_replace("/\n+|\r+|\s+/","", $html_revalidate) ){
			$error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
					"Typ: <b>$type</b><br/>" .
					"Pozycja: [<b>$from,$to</b>]<br/>" .
					"Przesłana jednostka: <b>'$text'</b><br/>" .
					"Jednostka z bazy: <b>'$html_revalidate'</b>";
				
			throw new Exception($error);
		}
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations_optimized', 'id');
		if ($table_annotations->insertRow(array(
			'report_id'=>$report_id, 
			'type_id'=>DbAnnotation::getIdByName($type), 
			'from'=>$from, 
			'to'=>$to, 
			'text'=>$text, 
			'user_id'=>$user['user_id'],
			'source'=>'user',
			'stage'=>'final'
			))){
			$annotation_id = $mdb2->lastInsertID();
		}
		else{
			throw new Exception("Wystąpił nieznany problem z dodaniem anotacji do bazy.");
		}
		
		return array("from"=>$from, "to"=>$to, "text"=>$text, "annotation_id"=>$annotation_id);
	}
	
}
?>
