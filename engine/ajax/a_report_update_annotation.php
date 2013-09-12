<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_update_annotation extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji anotacji.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user;
		$annotation_id = intval($_POST['annotation_id']);	
		$type = strval($_POST['type']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$report_id = intval($_POST['report_id']);
		$attributes = strval($_POST['attributes']);
		$error = null;

		$content = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
		$content = normalize_content($content);

		//$html = new HtmlStr(html_entity_decode($content, ENT_COMPAT, "UTF-8"), true);
		$html = new HtmlStr($content, true);
		//$text_revalidate = $html->getText($from, $to);
		$text_revalidate = custom_html_entity_decode($html->getText($from, $to));

		//if ( str_replace(" ","",$text) != str_replace(" ","", $text_revalidate) ){
		if ( preg_replace("/\n+|\r+|\s+/","",$text) != preg_replace("/\n+|\r+|\s+/","", $text_revalidate) ){
			$error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
					"Typ: <b>$type</b><br/>" .
					"Pozycja: [<b>$from,$to</b>]<br/>" .
					"Przesłana jednostka: <b><pre>$text</pre></b><br/>" .
					"Jednostka z bazy: <b><pre>$text_revalidate</pre></b>";
			echo json_encode(array("error"=>$error));
			return;
		}
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');		
		if ($row = $table_annotations->getRow($annotation_id)){
			$row['from'] = $from;
			$row['to'] = $to;
			$row['text'] = $text;
			$row['type_id'] = DbAnnotation::getIdByName($type);
			$table_annotations->updateRow($annotation_id, $row);
			
			// Get and iterate through list of annotation attributes
			$annotation_attributes = db_fetch_rows("SELECT * FROM annotation_types_attributes WHERE annotation_type = '$type'");
			$annotation_attributes_names = array();
			foreach ($annotation_attributes as $a)
				$annotation_attributes_names[$a['name']] = $a['id'];
			
			$attributes = explode("\n", $attributes);
			foreach ($attributes as $a){
				list($name, $value) = explode("=", $a);
				if (isset($annotation_attributes_names[$name]))
					db_replace("reports_annotations_attributes",
						array(
							"value"=>$value,
							"annotation_id"=>$annotation_id, 
							"annotation_attribute_id"=>$annotation_attributes_names[$name],
							"user_id"=>$user['user_id'])
							);
			}
		}else{
			echo json_encode(array("error"=>"Wystąpił nieznany problem z zapisem anotacji."));
			return;			
		}
		
		return array("from"=>$from, "to"=>$to, "text"=>$html->getText($from, $to), "annotation_id"=>$annotation_id);		
		//echo json_encode($json);
	}
	
}
?>
