<?php
/**
 * Dodaje nową adnotację do bazy, generuje identyfikator adnotacji, 
 * wstawia go do raportu i zapisuje zaktualizowany raport do bazy.
 * 
 */
class Ajax_report_update_content extends CPage {
	
	function checkPermission(){
		global $user, $corpus;
		$report = array(intval($_POST['report_id']));
		if ( hasAccessToReport($user, $report, $corpus) && hasCorpusRole('edit_documents') )
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus;
	
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));
		
		if (!intval($corpus['id'])){
			$this->set("error", "Brakuje identyfikatora korpusu!");
			return "";
		}

		if (!intval($user['user_id'])){
			$this->set("error", "Brakuje identyfikatora użytkownika!");
			return "";
		}
				
		$report = new CReport($report_id);			
		$content_before  = $report->content;
		$report->content = $content;
		$report->save();
		
		$df = new DiffFormatter();
		$diff = $df->diff($content_before, $report->content, true);
		if ( trim($diff) != "" ){
			$deflated = gzdeflate($diff);
			$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$report->id, "diff"=>$deflated);		
			db_insert("reports_diffs", $data);
		}
				
		$json = array( "success"=>1 );		
		echo json_encode($json);
	}
	
}
?>
