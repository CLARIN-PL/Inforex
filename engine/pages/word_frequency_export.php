<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_word_frequency_export extends CPage{
	
	var $isSecure = false;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_READ) || $corpus['public'];
	}
		
	function execute(){
		global $db, $user, $corpus;

		$ctag = $_GET['ctag'];
		$subcorpus_id = $_GET['subcorpus_id'];
		$corpus_id = $corpus['id'];
		$set_filters = array();
		
		$rows = DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus_id, $ctag, true, $set_filters);
		$this->set("rows", $rows);
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="words_frequency.csv"');		
	}
		
	function cleanText($text){
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = str_replace("\t", " ", $text);
		return $text;
	}
}


?>
