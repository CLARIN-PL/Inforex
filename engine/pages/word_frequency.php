<?php
class Page_word_frequency extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}	
	
	function execute(){
		global $corpus;
		
		$ctag = $_GET['ctag'];
		$subcorpus = $_GET['subcorpus'];		
		$corpus_id = $corpus['id'];
				
		$this->set("classes", Tagset::getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));		
	}		

}
 
?>