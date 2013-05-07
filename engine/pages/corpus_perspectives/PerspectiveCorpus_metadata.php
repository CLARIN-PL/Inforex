<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveCorpus_metadata extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$corpus_elements = DbCorpus::getCorpusById($corpus['id']);
		$this->page->set("extList", DbCorpus::getCorpusExtColumns($corpus_elements['ext']));
	}
}
?>
