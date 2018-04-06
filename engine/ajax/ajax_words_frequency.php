<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_words_frequency extends CPageCorpus{

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    public function execute(){
		global $corpus;

		$sortName		= $_POST['sortname'];
		$sortOrder		= $_POST['sortorder'];
		$pageElements	= intval($_POST['rp']);  // Liczba elementów na stronę
		$page			= intval($_POST['page']); // Numer strony
		$phrases		= $_POST['phrase'];
		
		$ctag = $_POST['ctag'];
		$subcorpus_id = $_POST['subcorpus_id'];		
		$corpus_id = $_POST['corpus'];
		$isdisamb = true;

		$phrases = explode(",", $phrases);
		array_walk($phrases, trim);
		$phrases = array_filter($phrases);
		if ( count($phrases) == 0 ) $phrases = null;
		
		$rows = DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus_id, $ctag, $isdisamb, $phrases, ($page-1)*$pageElements, $pageElements);
		$total = DbCorpusStats::getUniqueBaseCount($corpus_id, $subcorpus_id, $ctag, $isdisamb, $phrases);
				
		// UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $rows, 'post' => $_POST));
		die;
	}
}