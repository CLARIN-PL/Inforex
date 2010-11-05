<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
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
		global $mdb2;
	
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

		$content_no_html = preg_replace('/<([a-z]+)( .*?)?>(.*)<\/$1>/', '$3', $content);
		$content_no_html = preg_replace('/<\/?p>/', '', $content_no_html);
		$content_no_html = preg_replace('/<an#[0-9]+:[a-z_]+>(.*?)<\/an>/', '$1', $content_no_html);
		$content_no_html = preg_replace('/<chunk type="[^>]*">(.*?)<\/chunk>/', '$1', $content_no_html);
		$content_no_html = preg_replace('/<br\/?>/', "", $content_no_html);
		$text_revalidate = mb_substr($content_no_html, $from, $to-$from+1);

		if ( $text != $text_revalidate ){
			$error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
					"Typ: <b>$type</b><br/>" .
					"Pozycja: [<b>$from,$to</b>]<br/>" .
					"Przesłana jednostka: <b>$text</b><br/>" .
					"Jednostka z bazy: <b>$text_revalidate</b>";
			echo json_encode(array("error"=>$error));
			return;
		}
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');		
		if ($row = $table_annotations->getRow($annotation_id)){
			$row['from'] = $from;
			$row['to'] = $to;
			$row['text'] = $text;
			$row['type'] = $type;
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
							"annotation_attribute_id"=>$annotation_attributes_names[$name])
						);
			}
		}else{
			echo json_encode(array("error"=>"Wystąpił nieznany problem z zapisem anotacji."));
			return;			
		}
		
		$json = array("success"=>1, "from"=>$from, "to"=>$to, "text"=>$text, "annotation_id"=>$annotation_id);		
		echo json_encode($json);
	}
	
}
?>
