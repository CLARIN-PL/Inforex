<?php
 
 /**
  * Strona z testami spójności
  */
 
 class Page_tests extends CPage{
 	
 	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute()
	{
		global $corpus;
		
		$documents_in_corpus = DbReport::getReportsByCorpusId($corpus['id'],' count(*) AS count ');
	
		$this->set('corpus_id',$corpus['id']);
		$this->set('documents_in_corpus',$documents_in_corpus[0]['count']);
	}	
 } 
?>
