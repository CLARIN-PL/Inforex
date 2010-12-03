<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_add_annotation extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
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

		$html = new HtmlStr(html_entity_decode($content, ENT_COMPAT, "UTF-8"), true);
		$text_revalidate = $html->getText($from, $to);

		if ( $text != $text_revalidate ){
			$error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
					"Typ: <b>$type</b><br/>" .
					"Pozycja: [<b>$from,$to</b>]<br/>" .
					"Przesłana jednostka: <b>'$text'</b><br/>" .
					"Jednostka z bazy: <b>'$text_revalidate'</b>";
			
			$sequence_db = array();
			$sequence_db_chars = array();
			for ($i=0; $i<$from; $i++){
				$sequence_db[] = mb_substr($content_no_html, $i, 1);
			}
			
			$sequence = explode("|", $context);
			$sequence_chars = array();
			foreach ($sequence as $c) $sequence_chars[] = $c<200 ? chr($c) : $c;
			
			$sequence = "<table>" .
					"<tr><td style='border-bottom: 1px solid blue'>".implode("</td><td style='border-bottom: 1px solid blue'>", $sequence_db)."</td></tr>" .
					"<tr><td>".implode("</td><td>", $sequence_chars)."</td></tr>" .
					"<tr><td>".implode("</td><td>", $sequence)."</td></tr>" .
					"</table>";
			
			echo json_encode(array("error"=>$error.$sequence));
			return;
		}
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');
		if ($table_annotations->insertRow(array('report_id'=>$report_id, 'type'=>$type, 'from'=>$from, 'to'=>$to, 'text'=>$text, 'user_id'=>$user['user_id']))){
			$annotation_id = $mdb2->lastInsertID();
		}else{
			echo json_encode(array("error"=>"Wystąpił nieznany problem z dodaniem anotacji do bazy."));
			return;			
		}
		
		$json = array("success"=>1, "from"=>$from, "to"=>$to, "text"=>$text, "annotation_id"=>$annotation_id);		
		echo json_encode($json);
	}
	
}
?>
