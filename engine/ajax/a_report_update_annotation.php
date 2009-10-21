<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_update_annotation{
	
	function execute(){
		global $mdb2;
		$report_id = intval($_POST['report_id']);
		$annotation_id = intval($_POST['annotation_id']);
		$content = strval($_POST['content']);
		
		$content = normalize_content($content);

		if (preg_match('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', $content, $tab)){
			$annotation_type = $tab[1];
			$annotation_text = $tab[2];
		}
		else{
			$json = array("error" => "Wykryto niespójność: zmodyfikowana adnotacja nie została znaleziona w treści dokumentu.");
			echo json_encode($json);
			return "";
		}		

		$content_old = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
		$content_old = normalize_content($content_old);

		// Usuń aktualizowaną adnotację, aby porównać zawartości
		$content_cmp = preg_replace('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', '$2', $content);
		$content_old_cmp = preg_replace('/<an#'.$annotation_id.':(.*?)>(.*?)<\/an>/', '$2', $content_old);

		if ( $content_cmp != $content_old_cmp ){
			$json = array("error" => "Wykryto niespójność: raport różni się od oryginału nie tylko zmienioną adnotacją.", "raport"=> $content_cmp);
			echo json_encode($json);
			return "";			
		}
		
		$mdb2->query(sprintf("UPDATE reports_annotations SET type='%s', text='%s' WHERE id=%d", 
				mysql_escape_string($annotation_type),
				mysql_escape_string($annotation_text),
				$annotation_id));
				
		$mdb2->query("UPDATE reports SET content='".mysql_escape_string($content)."' WHERE id=$report_id");
		
		$json = array("success" =>  1, "raport"=> $content_cmp);
		echo json_encode($json);
	}
	
}
?>
