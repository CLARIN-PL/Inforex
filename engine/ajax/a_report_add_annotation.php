<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_add_annotation{
	
	function execute(){
		global $mdb2;
		$type = intval($_POST['type']);
		$report_id = intval($_POST['report_id']);
		$content = strval($_POST['content']);
		//$mdb2->query("UPDATE reports SET type=$type WHERE id=$id");
		
		$content = normalize_content($content);

		if (preg_match("/<an#0:(.*?)>(.*?)<\/an>/", $content, $tab)){
			$annotation_type = $tab[1];
			$annotation_text = $tab[2];
		}else{
			die("No new annotation was found! in [$content]");
		}
		
		$content_undo = preg_replace("/<an#0:.*?>(.*?)<\/an>/", "$1", $content);
		
		$content_old = $mdb2->queryOne("SELECT content FROM reports WHERE id=$report_id");
		$content_old = normalize_content($content_old);
		
		if ($content_undo==$content_old){
			// Wstaw nową adnotację i pobierz jej identyfikator
			$mdb2->query("INSERT INTO reports_annotations (report_id, type, text) VALUES (" .
					"$report_id, '$annotation_type', '$annotation_text');");
			$anid = mysql_insert_id();

			// Wstaw identyfikator adnotacji do treści
			$content = preg_replace("/<an#0:(.*?)>(.*?)<\/an>/", "<an#$anid:$1>$2</an>", $content);
			
			$mdb2->query("UPDATE reports SET content='".mysql_escape_string($content)."' WHERE id=$report_id");
			
			
			$json = array("success"=>true,
							"anid"=>$anid,
							);
		}else{		
			for ($i=0; $i<strlen($content_old); $i++){
				if ($content_old[$i]!=$content_undo[$i]){
					$diff_old_bin = "";
					$diff_old_txt = "";
					$diff_undo_bin = "";
					$diff_undo_txt = "";
					$diff_from = $i;
					for ($n=$i; $n<strlen($content_old) && $n<$i+10; $n++){
						$diff_old_bin .= ord($content_old[$n]).",";
						$diff_old_txt .= $content_old[$n];
						$diff_undo_bin .= ord($content_undo[$n]).",";
						$diff_undo_txt .= $content_undo[$n];
					}
					break;
				}
			}
			
			$json = array("success"=>false,
							"diff_old_bin"=>$diff_old_bin, 
							"diff_old_txt"=>$diff_old_txt, 
							"diff_undo_bin"=>$diff_undo_bin, 
							"diff_undo_txt"=>$diff_undo_txt,
							"diff_from"=>$diff_from 
							);
			fb($json);			
		}		
		echo json_encode($json);
	}
	
}
?>
