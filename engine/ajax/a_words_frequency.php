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

		$sortName		= $_POST['sortname'];
		$sortOrder		= $_POST['sortorder'];
		$pageElements	= intval($_POST['rp']);  // Liczba elementów na stronę
		$page			= intval($_POST['page']); // Numer strony
		
		$ctag = $_POST['ctag'];
		$subcorpus_id = $_POST['subcorpus_id'];		
		$corpus_id = $_POST['corpus'];
		$isdisamb = true;
		
		$rows = DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus_id, $ctag, $isdisamb, ($page-1)*$pageElements, $pageElements);
		$total = DbCorpusStats::getUniqueBaseCount($corpus_id, $subcorpus_id, $ctag, $isdisamb);
				
		// UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $rows, 'post' => $_POST));
		die;
	}
}