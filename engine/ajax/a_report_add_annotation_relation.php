<?php
/**
  metoda dodajaca relacje
  a_report_add_relation (relation_type_id, source_id, target_id  [date, user_id]) 
  ->rel, src, targ isUnique
 * 
 */
class Ajax_report_add_annotation_relation extends CPage {
	
	/*function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('annotate') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do dodawania anotacji <small>[checkPermission]</small>.";
	}*/
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}

		$relation_type_id = intval($_POST['relation_type_id']);
		$source_id = intval($_POST['source_id']);
		$target_id = intval($_POST['target_id']);
		$user_id = intval($user['user_id']);
		//echo json_encode(array("error"=>"Podana relacja już istnieje w bazie")); TODO
		$sql = "INSERT INTO relations (relation_type_id, source_id, target_id, date, user_id) " .
				"VALUES ({$relation_type_id},{$source_id},{$target_id},now(),{$user_id})";
		db_execute($sql);
		echo json_encode(array("success"=>1));
		/*$type = strval($_POST['type']);
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
				
			echo json_encode(array("error"=>$error));
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
		echo json_encode($json);*/
	}
	
}
?>
