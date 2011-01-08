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
		$content = stripslashes(strval($_POST['content']));
		$title = stripslashes(strval($_POST['title']));
		$date = strval($_POST['date']);
		$confirm = intval($_POST['confirm']);
		
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
		//if (!strval($title)) $missing_fields[] = "<b>tytuł</b>";
		
		if (count($missing_fields)){
			$this->set("content", $content);
			$this->set("title", $title);
			$this->set("date", $date);
			$this->set("error", "Enter missing data: ".implode(", ", $missing_fields));
			return "";	
		}
		
		if (count($missing_fields) == 0 	// wszystkie pola uzupełnione 
			 && intval($corpus['id']) 		// dostępny identyfikator korpusu
			 && intval($user['user_id']))	// dostępny identyfikator użytkownika
		{		
			$report = new CReport($report_id);			
			$report->assign($_POST);
			$report->corpora = $corpus['id'];
			$report->user_id = $user['user_id'];
			
			// Usuń anotacje in-line
			$report->content = preg_replace("/<an#([0-9]+):([a-z_]+)>/", "", $report->content); 
			$report->content = str_replace("</an>", "", $report->content); 
			
			if ($report->id){
				fb("Tutaj jestem");
				if (!$this->isVerificationRequired($report_id, $content, $confirm)){		
					// The document is going to be updated
					$report->save();
					$this->set("info", "The document was saved.");
					
					foreach ($this->annotations_to_delete as $an)
						$an->delete();
						
					foreach ($this->annotations_to_update as $an)
						$an->save();
				}
			}else{			
				$report->save();
				$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$report->corpora}&amp;id={$report->id}";
				$this->set("info", "The document was saved. <b><a href='$link'>Edit the document</a> &raquo;</b>");
			}
		}else{
			$this->set("error", "The document was not saved.");
		}

				
		return "";
	}
	
	/**
	 * 
	 */
	function isVerificationRequired($report_id, $content, $confirm){
		$confirm_after = stripslashes($content);
		$confirm_after = preg_replace("/<an#([0-9]+):([a-z_]+)>/", '<span class="$2" title="#$1:$2">', $confirm_after);
		$confirm_after = str_replace("</an>", "</span>", $confirm_after);
				
		$report = new CReport($report_id);		
		$annotations = db_fetch_rows("SELECT a.*, u.screename FROM reports_annotations a LEFT JOIN annotation_types t ON (a.type=t.name) LEFT JOIN users u USING (user_id) WHERE a.report_id=$report_id order by `from`");
		try{
			$htmlStr = new HtmlStr(html_entity_decode(stripslashes($report->content), ENT_COMPAT, "UTF-8"));
			foreach ($annotations as $ann){
				$htmlStr->insertTag($ann['from'], sprintf("<span class='%s'>", $ann['type']), $ann['to']+1, "</span>");
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		$confirm_before = $htmlStr->getContent();
				
		// Check annotations
		$annotations_new = HtmlParser::readInlineAnnotations($content);
		
		$changes = array();
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
				if ($a['text'] != $text || $a['from'] != $from || $a['to'] != $to )
				{
					$from_t = ($a['from'] != $from ? '<span style="color: red">'.$from."</span>" : $from);
					$to_t = $a['to'] != $to ? '<span style="color: red">'.$to."</span>" : $to;
					$text_t = $a['text'] != $text ? '<span style="color: red">'.$text."</span>" : $text;
					$annotations_changed[] = sprintf("#%d <br/>[%d,%d,%s]='%s' <b>=></b><br/> [%s,%s,%s]='%s'", $a['id'], $a['from'], $a['to'], $a['type'], $a['text'], $from_t, $to_t, $type, $text_t);
					
					$an = new CReportAnnotation($id);
					$anb = clone $an;
					$an->id = $id;
					$an->from = $from;
					$an->to = $to;
					$an->type = $type;
					$an->text = $text;
					$this->annotations_to_update[] = $an;

					$changes[] = array("action"=>"changed", "data1"=>$anb, "data2"=>$an); 
				}
			}
		}
		
		if (!$confirm && count($changes)>0)
		{							
			$this->set("confirm", true);
			$this->set("confirm_before", $confirm_before);
			$this->set("confirm_after", $confirm_after);
			$this->set("confirm_content", $content);
			$this->set("confirm_changed", $changes);
			return true;
		}
		else
			return false;		
	}
	
} 

?>
