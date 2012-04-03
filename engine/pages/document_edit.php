<?php
class Page_document_edit extends CPage{

	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("add_documents") || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){

		global $corpus;
				
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();

		if (!$this->get('date'))
			$this->set('date', date("Y-m-d"));
			
		$this->set('features', $features);
		$this->set('subcorpora', $subcorpora);
		$this->set('statuses', $statuses);
	}
}


?>
