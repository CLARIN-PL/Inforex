<?php

class PerspectiveSubcorpora extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$sql = "SELECT subcorpus_id AS id, name, description FROM corpus_subcorpora WHERE corpus_id=?";
		$this->page->set('subcorpusList', $db->fetch_rows($sql, array($corpus['id'])));
	}
}
?>
