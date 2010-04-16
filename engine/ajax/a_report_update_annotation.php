<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_update_annotation extends CPage {
	
	function execute(){
		global $mdb2;
	
		$annotation_id = intval($_POST['annotation_id']);	
		$type = strval($_POST['type']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$report_id = intval($_POST['report_id']);
		$error = null;

		$content = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
		$content = normalize_content($content);

		$content_no_html = preg_replace('/<([a-z]+)( .*?)?>(.*)<\/$1>/', '$3', $content);
		$content_no_html = preg_replace('/<\/?p>/', '', $content_no_html);
		$content_no_html = preg_replace('/<an#[0-9]+:[a-z_]+>(.*?)<\/an>/', '$1', $content_no_html);
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
			//$row['type'] = $type;
			$table_annotations->updateRow($annotation_id, $row);
			// nop
		}else{
			echo json_encode(array("error"=>"Wystąpił nieznany problem z zapisem anotacji."));
			return;			
		}
		
		$json = array("success"=>1, "from"=>$from, "to"=>$to, "text"=>$text, "annotation_id"=>$annotation_id);		
		echo json_encode($json);
/////		
//		
//		$report_id = intval($_POST['report_id']);
//		$annotation_id = intval($_POST['annotation_id']);
//		$content = strval($_POST['content']);
//		
//		$content = normalize_content($content);
//
//		if (preg_match('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', $content, $tab)){
//			$annotation_type = $tab[1];
//			$annotation_text = $tab[2];
//		}
//		else{
//			$json = array("error" => "Wykryto niespójność: zmodyfikowana adnotacja nie została znaleziona w treści dokumentu.");
//			echo json_encode($json);
//			return "";
//		}		
//
//		$content_old = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
//		$content_old = normalize_content($content_old);
//
//		// Usuń aktualizowaną adnotację, aby porównać zawartości
//		$content_cmp = preg_replace('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', '$2', $content);
//		$content_old_cmp = preg_replace('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', '$2', $content_old);
//
//		if ( $content_cmp != $content_old_cmp ){
//			$json = array("error" => "Wykryto niespójność: raport różni się od oryginału nie tylko zmienioną adnotacją.", "raport"=> $content_cmp);
//			echo json_encode($json);
//			return "";			
//		}
//		
//		$mdb2->query(sprintf("UPDATE reports_annotations SET type='%s', text='%s' WHERE id=%d", 
//				mysql_escape_string($annotation_type),
//				mysql_escape_string($annotation_text),
//				$annotation_id));
//				
//		$mdb2->query("UPDATE reports SET content='".mysql_escape_string($content)."' WHERE id=$report_id");
//		
//		$json = array("success" =>  1, "raport"=> $content_cmp);
//		echo json_encode($json);
	}
	
}
?>
