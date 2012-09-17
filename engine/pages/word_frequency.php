<?php
class Page_word_frequency extends CPage{
	
	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}	
	
	function execute(){
		
		$ctag = $_GET['ctag'];
		$subcorpus = $_GET['subcorpus'];
		
		$corpus_id = 3;
				
		$this->set("classes", Tagset::getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));		
	}		

}
 
?>