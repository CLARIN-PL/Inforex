<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_document_add extends CAction{
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_ADD_DOCUMENTS) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $user, $mdb2, $corpus;
		$metadata_ext = array();
				
		if ( !$user ){
			$this->set("error", "INTERNAL ERROR: User id not found.");
			return "";
		}
		
		$r = new CReport();
		$r->title = strval($_POST['title']);
		$r->date = date("Y-m-d", strtotime(strval($_POST['date'])));
		$r->author = strval($_POST['author']);		
		$r->source = strval($_POST['source']);
		$r->corpora = intval($corpus['id']);
		$r->subcorpus_id = intval($_POST['subcorpus_id']);
		$r->user_id = $user['user_id'];
		$r->content = stripslashes(strval($_POST['content']));
		$r->status = intval($_POST['status']);
		$r->type = 1;  // nieokreślony
		$r->format_id = intval($_POST['format']);
		
		if ( $r->subcorpus_id == 0 ){
			$r->subcorpus_id = null;
		}
		
		foreach ($_POST as $k=>$v){
			if ( substr($k, 0, 4) == "ext_" )
				$metadata_ext[substr($k, 4)] = $v=='(NULL)' ? null : $v;
		}
				
		$parse = $r->validateSchema();
		
		if (count($parse)){
			$this->set("wrong_changes", true);
			$this->set("parse_error", $parse);
			$this->set("wrong_document_content", $r->content);
			$this->set("error", "The document was not saved.");
			
			$row = array(
					"title" => $r->title,
					"author" => $r->author,
					"source" => $r->source,
					"subcorpus_id" => $r->subcorpus_id,
					"content" => $r->content,
					"status" => $r->status,
					"date" => $_POST['date'],
					"format" => $r->format_id
			);
			$this->set("row", $row);
			$this->set("metadata_values", $metadata_ext);
			return "";
		}
						
		$r->save();
		
		DbReport::insertEmptyReportExt($r->id);
		DbReport::updateReportExt($r->id, $metadata_ext);
		
		$df = new DiffFormatter();
		$diff = $df->diff("", $r->content, true);
		if ( trim($diff) != "" ){
			$deflated = gzdeflate($diff);
			$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$r->id, "diff"=>$deflated);		
			DbReport::insertReportDiffs($data);
		}
		
		$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$r->corpora}&amp;id={$r->id}";
		$this->set("info", "The document was added. <a href='$link' style='color: blue; font-weight: bold;'>Edit the content</a> or add another one.");
		
		$row = array();
		$row['subcorpus_id'] = $r->subcorpus_id;
		$row['status'] = $r->status;

		$this->set('row', $row);
		
		return "";
	}
		
} 

?>
