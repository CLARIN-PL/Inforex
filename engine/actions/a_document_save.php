<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
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
		global $user, $corpus;
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));
		$comment = stripslashes(strval($_POST['comment']));
		$date = strval($_POST['date']);
		$confirm = intval($_POST['confirm']);
		$edit_type = strval($_COOKIE['edit_type']);
		
		$error = null;

		$missing_fields = array();
		if (!strval($content)) {
			$missing_fields[] = "<b>treść</b>";
		};
		
		if (count($missing_fields)){
			$this->set("content", $content);
			$this->set("date", $date);
			$this->set("error", "Enter missing data: ".implode(", ", $missing_fields));
			return "";	
		}
		
		$report = new TableReport($report_id);

		/** Pobierz treść przed zmianą */
		$content_before  = $report->content;
		$report->assign($_POST);
		$report->corpora = $corpus['id'];
		$report->user_id = $user['user_id'];

		// Usuń anotacje in-line
		$report->content = preg_replace("/<anb id=\"([0-9]+)\" type=\"([\\p{L}_0-9]+)\"\/>/", "", $report->content);
		$report->content = preg_replace("/<ane id=\"([0-9]+)\"\/>/", "", $report->content);

		if ($report->id){
			if($edit_type == 'no_annotation'){
				$this->updateNoAnnotations($content, $content_before, $report, $user, $comment);
			} else {
				$this->updateWithAnnotations($content, $content_before, $report, $user, $comment, $confirm);
			}
		} else {
			$report->save();
			DbReport::updateFlag($report->id, $report->corpora, 5);
			$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$report->corpora}&amp;id={$report->id}";
			$this->set("info", "The document was saved. <b><a href='$link'>Edit the document</a> &raquo;</b>");
		}

		return "";
	}

	function updateNoAnnotations($content, $content_before, $report, $user, $comment){
		$content_with_space = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($report->content))));
		$content_without_space = preg_replace("/\n+|\r+|\s+/","",$content_with_space);
		$content_before_with_space = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($content_before))));
		$content_before_without_space = preg_replace("/\n+|\r+|\s+/","",$content_before_with_space);
		if($content_before_without_space == $content_without_space){
			$parse = $report->validateSchema();
			if (count($parse)){
				$this->set("wrong_changes", true);
				$this->set("parse_error", $parse);
				$this->set("wrong_document_content", $report->content);
				$this->set("error", "The document was not saved.");
			} else {
				/** The document is going to be updated */
				$report->save();
				DbReport::updateFlag($report->id, $report->corpora, 5);
				/** Oblicz różnicę */
				$df = new DiffFormatter();
				$diff = $df->diff($content_before, $report->content, true);
				if ( trim($diff) != "" || trim($comment)!=""){
					$deflated = gzdeflate($diff);
					$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated, "comment"=>$comment);
					$this->getDb()->insert("reports_diffs", $data);
				}

				$this->set("info", "The document was saved.");
			}
		}
		else{
			$df = new DiffFormatter();
			$diff = $df->diff($content_before, $report->content, true);
			$this->set("error", "The document was not saved. In the <em>Simple</em> mode you can modify the xml tags and white spaces.");
			$this->set("wrong_changes", true);
			$this->set("document_changes", $df->formatDiff($diff));
			$this->set("wrong_document_content", $content);
		}
	}

	function updateWithAnnotations($content, $content_before, $report, $user, $comment, $confirm){
		$tmpContent = $report->content;
		$report->content = $content;
		if (!$this->isVerificationRequired($report, $confirm, $comment)){
			$report->content = $tmpContent;
			/** The document is going to be updated */
			$report->save();

			/** Oblicz różnicę */
			$df = new DiffFormatter();
			$diff = $df->diff($content_before, $report->content, true);
			if ( trim($diff) != "" || trim($comment)!=""){
				$deflated = gzdeflate($diff);
				$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated, "comment"=>$comment);
				$this->getDb()->insert("reports_diffs", $data);
			}

			$this->set("info", "The document was saved.");

			foreach ($this->annotations_to_delete as $an) {
				$an->delete();
			}

			foreach ($this->annotations_to_update as $an) {
				$an->save();
			}
		}
	}

	/**
	 * 
	 */
	function isVerificationRequired($report, $confirm, $comment){
		$parse = $report->validateSchema();
		
		if (count($parse)){
			$this->set("wrong_changes", true);
			$this->set("parse_error", $parse);
			$this->set("wrong_document_content", $report->content);
			$this->set("error", "The document was not saved.");
			return true;
		}
		
		// Check annotations
		list($annotations_new, $wrong_annotations) = HtmlParser::readInlineAnnotationsWithOverlapping($report->content);

		$changes = array();

		if (count($wrong_annotations)){
			$this->set("wrong_changes", true);
			foreach ($wrong_annotations as $id=>&$a){
				$an = new TableReportAnnotation($id);
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
		
		$annotations = $this->getDb()->fetch_rows("SELECT a.*, u.screename, t.group_id
				 FROM reports_annotations_optimized a
				 LEFT JOIN annotation_types t ON (a.type_id=t.annotation_type_id)
				 LEFT JOIN users u USING (user_id)
				 WHERE a.report_id=$report->id
				 ORDER BY `from`");

        foreach ($annotations as $a) {
			if (!isset($annotations_new[$a['id']])) {
				$an = new TableReportAnnotation($a['id']);
				$this->annotations_to_delete[] = $an;
				$changes[] = array("action"=>"removed", "data1"=>$an, "data2"=>null); 
			} else {
				list($from, $to, $type, $type_id, $id, $text) = $annotations_new[$a['id']];
				if ($from > $to){
					$an = new TableReportAnnotation($a['id']);
					$this->annotations_to_delete[] = $an;
					$changes[] = array("action"=>"removed", "data1"=>$an, "data2"=>null, 'annotation_type_name' => $type);
				}
				elseif ($a['text'] != $text || $a['from'] != $from || $a['to'] != $to )
				{
					$anb = new TableReportAnnotation($id);
					$anb->setText(trim($anb->text));

					$an = new TableReportAnnotation($id);
					$an->setFrom($from);
					$an->setTo($to);
					$an->setTypeId($type_id);
					$an->setText(trim($text));
					
					$this->annotations_to_update[] = $an;

					if ($a['text'] != $text && $a['from'] == $from && $a['to'] == $to && $an->text == $anb->text){
						$anb->text = $a['text'];
						$changes[] = array("action"=>"remove_whitespaces", "data1"=>$anb, "data2"=>$an, 'annotation_type_name' => $type);
					}
					else{
						$changes[] = array("action"=>"changed", "data1"=>$anb, "data2"=>$an, 'annotation_type_name' => $type);
					}
				}
			}
		}

		if (!$confirm && count($changes)>0)
		{							
			$this->set("confirm", true);
			$this->set("confirm_content", $report->content);
			$this->set("confirm_changed", $changes);
			$this->set("confirm_comment", $comment);
			return true;
		}
		else
			return false;		
	}
	
} 

?>
