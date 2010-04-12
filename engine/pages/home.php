<?php
class Page_home extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2;
		
		$tbl = $mdb2->tableBrowserFactory('corpora', 'id');
		$corpora = $tbl->getRows()->fetchAll(MDB2_FETCHMODE_ASSOC);
		$this->set('corpus_set', $corpora);
	}
}


?>
