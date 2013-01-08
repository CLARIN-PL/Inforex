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
		
		$ext_filters = array();
		foreach ($_GET as $k=>$v){
			if ( $v && preg_match("/^filter_(.*)/", $k, $m) ){
				$ext_filters[$m[1]] = $v;
			}
		}
				
		$filters = array();
		$table_name = DbCorpus::getCorpusExtTable($corpus_id);
		$columns = DbCorpus::getCorpusExtColumns($table_name);
		foreach ($columns as $c){
			if ( preg_match("/enum\((.*)\)/", $c["type"], $m) ){
				$name = $c["field"];
				$filters[] = array(
					"name"=>$name,
					"values"=>explode(",", str_replace("'", "", $m[1])),
					"selected"=>isset($ext_filters[$name])?$ext_filters[$name]:null 
				);												
			}
		}		
												
		$this->set("classes", Tagset::getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true, $ext_filters));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));
		$this->set("filters", $filters);		
	}		

}
 
?>