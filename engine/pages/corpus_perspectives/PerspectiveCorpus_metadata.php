<?php

class PerspectiveCorpus_metadata extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$corpus_elements = DbCorpus::getCorpusById($corpus['id']);
		$this->page->set("extList", DbCorpus::getCorpusExtColumns($corpus_elements['ext']));
	}
}
?>
