<?php
class Page_home extends CPage{
	
	var $isSecure = false;
	
	function execute(){
		global $mdb2;
		
		$sql = "SELECT c.*, COUNT(r.id) AS `reports` FROM corpora c JOIN reports r ON (c.id = r.corpora) GROUP BY c.id";
		$corpora = db_fetch_rows($sql);
		$this->set('corpus_set', $corpora);
	}
}


?>
