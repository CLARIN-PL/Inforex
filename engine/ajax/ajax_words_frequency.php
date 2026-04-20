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
        $this->usedOnPages[] = "page_corpus_word_frequency";
    }

    public function execute(){
        try {
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

		    $phrases = array_map('trim', explode(",", $phrases));
		    $phrases = array_filter($phrases);
		    if ( count($phrases) == 0 ) $phrases = null;
		
		    $result = DbCorpusStats::getWordsFrequncesWithTotal($corpus_id, $subcorpus_id, $ctag, $isdisamb, $phrases, ($page-1)*$pageElements, $pageElements);
				
		    // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		    echo json_encode(array('page' => $page, 'total' => $result['total'], 'rows' => $result['rows'], 'post' => $_POST));
		    die;
        } catch (Throwable $e) {
            error_log('Ajax_words_frequency failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(array(
                'error' => true,
                'message' => $e->getMessage(),
                'page' => 1,
                'total' => 0,
                'rows' => array(),
            ));
            die;
        }
	}
}
