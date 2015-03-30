<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
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
		DbReport::updateReportExt($report_id, $metadata_ext);

		$r = new CReport($report_id);
		$r->title = strval($_POST['title']);
		$r->author = strval($_POST['author']);
		$r->date = date("Y-m-d", strtotime(strval($_POST['date'])));
		$r->source = strval($_POST['source']);
		$r->subcorpus_id = intval($_POST['subcorpus_id'])>0 ? intval($_POST['subcorpus_id']) : null;
		$r->status = intval($_POST['status']);
		$r->format_id = intval($_POST['format']);
		$r->save();
		$this->set("info", "The metadata were saved.");

		return "";
	}
	
} 

?>
