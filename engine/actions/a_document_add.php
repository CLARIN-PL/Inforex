<?php

class Action_document_add extends CAction{
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("add_documents") || isCorpusOwner())
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
		$r->source = strval($_POST['source']);
		$r->corpora = intval($corpus['id']);
		$r->subcorpus_id = intval($_POST['subcorpus_id']);
		$r->user_id = $user['user_id'];
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
		
		$link = "index.php?page=report&amp;subpage=edit&amp;corpus={$r->corpora}&amp;id={$r->id}";
		$this->set("info", "The document was added. <a href='$link' style='color: blue; font-weight: bold;'>Edit the document content</a> or add another one.");
		
		return "";
	}
		
} 

?>
