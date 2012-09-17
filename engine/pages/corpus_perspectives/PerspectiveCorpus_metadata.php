<?php

class PerspectiveCorpus_metadata extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$sql = "SELECT corpora_flag_id AS id, name, short, sort FROM corpora_flags WHERE corpora_id=?";
		$corpus_elements = DbCorpus::getCorpusById($corpus['id']);
		$this->page->set("extList", DbCorpus::getCorpusExtColumns($corpus_elements['ext']));
	}
}
?>
