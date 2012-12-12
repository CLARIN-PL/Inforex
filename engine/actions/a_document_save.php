<?php

class Action_document_save extends CAction{
	
	var $annotations_to_update = array();
	var $annotations_to_delete = array();
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("edit_documents") || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $user, $mdb2, $corpus;
		$report_id = intval($_POST['report_id']);
		$status_id = intval($_POST['status']);
		$content = stripslashes(strval($_POST['content']));
		$comment = stripslashes(strval($_POST['comment']));
		$date = strval($_POST['date']);
		$confirm = intval($_POST['confirm']);
		$edit_type = strval($_COOKIE['edit_type']);
		
		$error = null;
		
		if (!intval($corpus['id'])){
			$this->set("error", "Brakuje identyfikatora korpusu!");
			return "";
		}

		if (!intval($user['user_id'])){
			$this->set("error", "Brakuje identyfikatora użytkownika!");
			return "";
		}
		
		$missing_fields = array();
		if (!strval($content)) $missing_fields[] = "<b>treść</b>";
		
		if (count($missing_fields)){
			$this->set("content", $content);
			$this->set("date", $date);
			$this->set("error", "Enter missing data: ".implode(", ", $missing_fields));
			return "";	
		}
		
		if (count($missing_fields) == 0 	// wszystkie pola uzupełnione 
			 && intval($corpus['id']) 		// dostępny identyfikator korpusu
			 && intval($user['user_id']))	// dostępny identyfikator użytkownika
		{		
			$report = new CReport($report_id);			

			/** Pobierz treść przed zmianą */
			$content_before  = $report->content;

			$report->assign($_POST);
			$report->corpora = $corpus['id'];
			$report->user_id = $user['user_id'];			
			
			
			// Usuń anotacje in-line
			$report->content = preg_replace("/<anb id=\"([0-9]+)\" type=\"([\\p{Ll}_0-9]+)\"\/>/", "", $report->content); 
			$report->content = preg_replace("/<ane id=\"([0-9]+)\"\/>/", "", $report->content);
			if ($report->id){
				
				if($edit_type == 'no_annotation'){
					$content_with_space = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($report->content))));
					$content_without_space = preg_replace("/\n+|\r+|\s+/","",$content_with_space);
					$content_before_with_space = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($content_before))));
					$content_before_without_space = preg_replace("/\n+|\r+|\s+/","",$content_before_with_space);
					if($content_before_without_space == $content_without_space){
						/** The document is going to be updated */
						$report->save();
						$this->updateFlag($report->id, $report->corpora);
						/** Oblicz różnicę */
						$df = new DiffFormatter();
						$diff = $df->diff($content_before, $report->content, true);
						if ( trim($diff) != "" || trim($comment)!=""){
							$deflated = gzdeflate($diff);
							$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated, "comment"=>$comment);		
							db_insert("reports_diffs", $data);
						}					
					
						$this->set("info", "The document was saved.");
					}
					else{
						$df = new DiffFormatter();
						$diff = $df->diff($content_before, $report->content, true);
						$this->set("error", "The document was not saved.");
						$this->set("wrong_changes", true);
						$this->set("document_changes", $df->formatDiff($diff));
						$this->set("wrong_document_content", $content);
					}
				}
				else{
					if (!$this->isVerificationRequired($report_id, $content, $confirm, $comment)){		
						
						/** The document is going to be updated */
						$report->save();
						$this->updateFlag($report->id, $report->corpora);
						/** Oblicz różnicę */
						$df = new DiffFormatter();
						$diff = $df->diff($content_before, $report->content, true);
						if ( trim($diff) != "" || trim($comment)!=""){
							$deflated = gzdeflate($diff);
							$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated, "comment"=>$comment);		
							db_insert("reports_diffs", $data);
						}					
					
						$this->set("info", "The document was saved.");
					
						foreach ($this->annotations_to_delete as $an)
							$an->delete();
							
						foreach ($this->annotations_to_update as $an)
							$an->save();
					}
				}
			}else{			
				$report->save();
				$this->updateFlag($report->id, $report->corpora);
				$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$report->corpora}&amp;id={$report->id}";
				$this->set("info", "The document was saved. <b><a href='$link'>Edit the document</a> &raquo;</b>");
			}
		}else{
			$this->set("error", "The document was not saved.");
		}

				
		return "";
	}
	
	function updateFlag($report_id, $corpus_id){
		global $db;
		$corpora_flag_id = $db->fetch_one( 
			"SELECT corpora_flag_id " .
			"FROM corpora_flags " .
			"WHERE corpora_id=? " .
			"AND short=\"Tokens\"", array($corpus_id));
		if ($corpora_flag_id){
			$db->execute(
				"REPLACE reports_flags (corpora_flag_id, report_id, flag_id) " .
				"VALUES (?,?,?)", array($corpora_flag_id, $report_id, 5));
		}	
		
		
	}
	
	/**
	 * 
	 */
	function isVerificationRequired($report_id, $content, $confirm, $comment){
		/*
		$confirm_after = stripslashes($content);
		$confirm_after = preg_replace("/<an#([0-9]+):([a-z_]+)>/", '<span class="$2" title="#$1:$2">', $confirm_after);
		$confirm_after = preg_replace("/<\/an#([0-9]+)>/", "</span>", $confirm_after);
				
		$report = new CReport($report_id);		
		*/
		$annotations = db_fetch_rows("SELECT a.*, u.screename, t.group_id" .
				" FROM reports_annotations a" .
				" LEFT JOIN annotation_types t ON (a.type=t.name)" .
				" LEFT JOIN users u USING (user_id)" .
				" WHERE a.report_id=$report_id" .
				" ORDER BY `from`");
		/*
		$htmlStr = new HtmlStr(html_entity_decode(stripslashes($report->content), ENT_COMPAT, "UTF-8"));
		
		foreach ($annotations as $ann){
			echo $ann['group_id'];
			if ( !isset($ann['group_id']) )
				$htmlStrs[$ann['group_id']] = new HtmlStr(html_entity_decode(stripslashes($report->content), ENT_COMPAT, "UTF-8"));
		}
		
		foreach ($annotations as $ann){
			try{
				$group = $ann['group_id'];
				$htmlStrs[$group]->insertTag($ann['from'], sprintf("<span class='%s'>", $ann['type']), $ann['to']+1, "</span>");
			}catch (Exception $ex){
				$htmlStrs[$group]->insert($ann['from'], "<hr/>");
				$htmlStrs[$group]->insert($ann['to']+1, "<hr/>");
				custom_exception_handler($ex);
			}
		}
		$confirm_before = $htmlStrs[1]->getContent();
		*/
				
		// Check annotations
		list($annotations_new, $wrong_annotations) = HtmlParser::readInlineAnnotationsWithOverlapping($content);
		
		$changes = array();
		
		if (count($wrong_annotations)){
			$this->set("wrong_changes", true);
			foreach ($wrong_annotations as $id=>&$a){
				$an = new CReportAnnotation($id);
				$a["from"] = $an->from;
				$a["to"] = $an->to;
				$a["type"] = $an->type;
				$a["text"] = $an->text;
			}
			$this->set("wrong_annotations", $wrong_annotations);
			$this->set("wrong_document_content", $content);
			$this->set("error", "The document was not saved.");
			return true;
		}
		
		foreach ($annotations as $a)
		{						
			if (!isset($annotations_new[$a['id']]))
			{
				$an = new CReportAnnotation($a['id']);
				$this->annotations_to_delete[] = $an;
				$changes[] = array("action"=>"removed", "data1"=>$an, "data2"=>null); 
			}
			else
			{
				list($from, $to, $type, $id, $text) = $annotations_new[$a['id']];
				if ($from > $to){
					$an = new CReportAnnotation($a['id']);
					$this->annotations_to_delete[] = $an;
					$changes[] = array("action"=>"removed", "data1"=>$an, "data2"=>null);
				}
				elseif ($a['text'] != $text || $a['from'] != $from || $a['to'] != $to )
				{
					$anb = new CReportAnnotation($id);
					$anb->text = trim($anb->text);
					
					$an = new CReportAnnotation($id);
					$an->from = $from;
					$an->to = $to;
					$an->type = $type;
					$an->text = trim($text);
					
					$this->annotations_to_update[] = $an;

					if ($a['text'] != $text && $a['from'] == $from && $a['to'] == $to && $an->text == $anb->text){
						$anb->text = $a['text'];
						$changes[] = array("action"=>"remove_whitespaces", "data1"=>$anb, "data2"=>$an);
					}
					else{
						$changes[] = array("action"=>"changed", "data1"=>$anb, "data2"=>$an);
					} 
				}
			}
		}
		
		if (!$confirm && count($changes)>0)
		{							
			$this->set("confirm", true);
			$this->set("confirm_content", $content);
			$this->set("confirm_changed", $changes);
			$this->set("confirm_comment", $comment);
			return true;
		}
		else
			return false;		
	}
	
} 

?>
