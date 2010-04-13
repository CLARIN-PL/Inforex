<?php

class Action_document_save extends CAction{
	
	function execute(){
		global $user, $mdb2, $corpus;
		$report_id = intval($_POST['report_id']);
		$content = strval($_POST['content']);
		$title = strval($_POST['title']);
		$date = strval($_POST['date']);
		
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
		if (!strval($title)) $missing_fields[] = "<b>tytuł</b>";
		
		if (count($missing_fields)){
			$this->set("content", stripslashes($content));
			$this->set("title", $title);
			$this->set("date", $date);
			$this->set("error", "Uzupełnij brakujące pola: ".implode(", ", $missing_fields));
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
			if ($report->id){
				$report->save();
				$this->set("info", "Dokument został zapisany.");
			}else{			
				$report->save();
				$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$report->corpora}&amp;id={$report->id}";
				$this->set("info", "Dokument został dodany. <b><a href='$link'>Przejdź do edycji dodanego dokumentu</a> &raquo;</b>");
			}
		}else{
			$this->set("error", "Danie <b>nie</b> zostały zapisane");
		}
				
		return "";
	}
	
} 

?>
