<?php

class Action_metadata_save extends CAction{
	
	var $annotations_to_update = array();
	var $annotations_to_delete = array();
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("edit_documents") || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){

		global $db;
		$report_id = intval($_POST['report_id']);
		$report = null;
		$metadata_ext = array();

		$report = new CReport($report_id);	
		$corpus = DbCorpus::getCorpusById($report->corpora);	
		
		if (!$corpus){
			$this->set("error", "Corpus not found");
			return "";
		}

		foreach ($_POST as $k=>$v){
			if ( substr($k, 0, 4) == "ext_" )
				$metadata_ext[substr($k, 4)] = $v;
		}

		$args = array();
		$columns = array();
		foreach ($metadata_ext as $k=>$v){
			$columns[] = "`$k` = ?";
			$args[] = $v;
		}
		$args[] = $report_id;

		$sql = "UPDATE {$corpus['ext']} SET " . implode(", ", $columns) . " WHERE id = ?";
		$db->execute($sql, $args);

		$this->set("info", "The metadata were saved.");

		return "";
	}
	
} 

?>
