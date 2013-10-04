<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_words_frequency extends CPage{

	public function execute(){
		global $corpus;
		
		$ctag = $_POST['ctag'];
		$subcorpus = $_POST['subcorpus'];		
		$corpus_id = $_POST['corpus'];
		
		return DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true, $set_filters);
	}
}