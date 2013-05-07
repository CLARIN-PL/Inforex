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
		$r->save();
		
		foreach ($_POST as $k=>$v){
			if ( substr($k, 0, 4) == "ext_" )
				$metadata_ext[substr($k, 4)] = $v=='(NULL)' ? null : $v;
		}
		DbReport::insertEmptyReportExt($r->id);
		DbReport::updateReportExt($r->id, $metadata_ext);
		fb($metadata_ext);
		
		$df = new DiffFormatter();
		$diff = $df->diff("", $r->content, true);
		if ( trim($diff) != "" ){
			$deflated = gzdeflate($diff);
			$data = array("datetime"=>date("Y-m-d H:i:s"), "user_id"=>$user['user_id'] , "report_id"=>$r->id, "diff"=>$deflated);		
			DbReport::insertReportDiffs($data);
		}
		
		$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$r->corpora}&amp;id={$r->id}";
		$this->set("info", "The document was added. <a href='$link' style='color: blue; font-weight: bold;'>Edit the document content</a> or add another one.");
		
		$row = array();
		$row['subcorpus_id'] = $r->subcorpus_id;
		$row['status'] = $r->status;

		$this->set('row', $row);
		
		return "";
	}
		
} 

?>
