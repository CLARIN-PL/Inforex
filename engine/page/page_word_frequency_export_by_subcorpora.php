<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_word_frequency_export_by_subcorpora extends CPage{
	
	var $isSecure = false;

	function checkPermission(){
		global $corpus;
		return hasCorpusRole(CORPUS_ROLE_READ) || $corpus['public'];
	}
		
	function execute(){
		global $db, $user, $corpus;

		$ctag = $_GET['ctag'];
		$corpus_id = $corpus['id'];
		$set_filters = array();
		
		$rows = DbCorpusStats::getWordsFrequencesPerSubcorpus($corpus_id, $ctag, true, null);		
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
		
		$counts = array();
		$base_id_order = array();
		
		foreach ( $rows as $row ){
			$base_id = $row['base_id'];
			$base = $row['base'];
			$pos = $row['pos'];
			$subcorpus_id = $row['subcorpus_id'];
			$count_words = $row['c'];
			$count_docs = $row['docs'];
			if ( !isset($counts[$base_id]) ){
				$r = array();
				foreach ($subcorpora as $sub){
					$r[$sub['subcorpus_id']] = array("words"=>0, "docs"=>0);
				}
				$counts[$base_id] = $r;
			}
			$counts[$base_id][$subcorpus_id]["words"] = $count_words;
			$counts[$base_id][$subcorpus_id]["docs"] = $count_docs;
		}
		unset($rows);
		
		$rows = DbCorpusStats::getWordsFrequnces($corpus_id);
		foreach ($rows as $row){
			$base_id = $row['id'];
			$pos = $row['pos'];
			$counts[$base_id]['base'] = $row['base'];
			$counts[$base_id]['pos'] = $row['pos'];
			$counts[$base_id]['total_words'] = $row['c'];
			$counts[$base_id]['total_docs'] = $row['docs'];
			$base_id_order[] = $base_id;
		}
		unset($rows);		

		$this->set("subcorpora", $subcorpora);
		$this->set("base_id_order", $base_id_order);
		$this->set("counts", $counts);
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="words_frequency_by_subcorpora.csv"');		
	}
		
	function cleanText($text){
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = str_replace("\t", " ", $text);
		return $text;
	}
}


?>
